/*
 * Home sections zoom behavior.
 *
 * Intencao: combinar dois comportamentos de scroll:
 * - por cima: imagem rola na metade da velocidade da secao
 * - por baixo: zoom varia ate 20%
 * - secao inteira visivel: tamanho original (sem blur)
 */
(() => {
  const container = document.querySelector('[data-home-sections-container]');
  const sections = Array.from(document.querySelectorAll('[data-home-section]'));

  if (!container || sections.length === 0) {
    return;
  }

  const items = sections
    .map((section) => ({
      section,
      image: section.querySelector('[data-home-section-image]'),
      content: section.querySelector('[data-home-section-content]'),
      contentBody: section.querySelector('[data-home-section-content] > div'),
      top: 0,
      height: 0,
      lastImageTransform: '',
      lastContentTransform: '',
      lastClipPath: '',
      lastLayout: '',
    }))
    .filter((item) => item.image && item.content && item.contentBody);

  if (items.length === 0) {
    return;
  }

  const finalScale = 1;
  const StartScale = 0.6;
  const topParallaxFactor = 0.8;
  const topEdgeFinalPercentDefault = 80;
  let rafId = 0;

  /*
   * Mantem progress em intervalo valido.
   */
  const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

  /*
   * Gera clip-path com aresta superior controlada em percentual.
   */
  const buildRightEdgeClipPathByTopPercent = (topPercent) => {
    const normalizedTopPercent = clamp(topPercent, 1, 100);
    return `polygon(0 0, ${normalizedTopPercent.toFixed(2)}% 0, 100% 100%, 0 100%)`;
  };

  /*
   * Atualiza metrica de layout para evitar leituras repetidas por frame.
   */
  const refreshMetrics = () => {
    items.forEach((item) => {
      item.top = item.section.offsetTop;
      item.height = item.section.offsetHeight;

      const isPortrait = item.section.offsetHeight > item.section.offsetWidth;
      const nextLayout = isPortrait ? 'portrait' : 'landscape';
      item.lastLayout = nextLayout;

      if (isPortrait) {
        const sectionHeight = item.section.offsetHeight;

        item.image.style.left = '0';
        item.image.style.top = '0';
        item.image.style.width = '100%';
        item.image.style.height = 'auto';

        item.content.style.position = 'absolute';
        item.content.style.left = '0';
        item.content.style.right = '0';
        item.content.style.bottom = '0';
        item.content.style.marginLeft = '0';
        item.content.style.marginTop = '0';
        item.content.style.width = '100%';
        item.content.style.height = 'auto';
        item.contentBody.style.height = 'auto';

        const contentHeight = Math.min(item.content.scrollHeight, sectionHeight);
        const imageHeight = Math.max(sectionHeight - contentHeight, 0);

        item.image.style.height = `${imageHeight}px`;
        item.content.style.height = `${contentHeight}px`;
        item.image.style.clipPath = 'none';
        item.image.style.webkitClipPath = 'none';
        return;
      }

      item.image.style.left = '0';
      item.image.style.top = '0';
      item.image.style.width = '50%';
      item.image.style.height = '100%';

      const clipPath = buildRightEdgeClipPathByTopPercent(100);
      item.image.style.clipPath = clipPath;
      item.image.style.webkitClipPath = clipPath;

      item.content.style.position = 'relative';
      item.content.style.left = '';
      item.content.style.right = '';
      item.content.style.bottom = '';
      item.content.style.marginLeft = 'auto';
      item.content.style.marginTop = '0';
      item.content.style.width = '50%';
      item.content.style.height = '100%';
      item.contentBody.style.height = '100%';
    });
  };

  /*
   * Aplica estilo somente quando houver mudanca real.
   */
  const writeVisualState = (item, imageTransform, contentTransform, clipPath) => {
    if (item.lastImageTransform !== imageTransform) {
      item.image.style.transform = imageTransform;
      item.lastImageTransform = imageTransform;
    }

    if (item.lastContentTransform !== contentTransform) {
      item.content.style.transform = contentTransform;
      item.lastContentTransform = contentTransform;
    }

    if (item.lastClipPath !== clipPath) {
      item.image.style.clipPath = clipPath;
      item.image.style.webkitClipPath = clipPath;
      item.lastClipPath = clipPath;
    }
  };

  const applyEffects = () => {
    rafId = 0;

    const scrollTop = container.scrollTop;
    const viewportHeight = container.clientHeight;

    items.forEach((item) => {
      const top = item.top - scrollTop;
      const bottom = top + item.height;
      const isFullyVisible = top >= 0 && bottom <= viewportHeight;

      // Ignora secoes distantes para reduzir custo por frame.
      if (bottom < -item.height || top > viewportHeight + item.height) {
        const clipPath = item.lastLayout === 'portrait'
          ? 'none'
          : buildRightEdgeClipPathByTopPercent(100);
        writeVisualState(item, 'translateY(0px) scale(1)', 'scale(1)', clipPath);
        return;
      }

      let clipProgress = 0;
      if (top < 0) {
        clipProgress = clamp((-top) / item.height, 0, 1);
      } else if (bottom > viewportHeight) {
        clipProgress = clamp((bottom - viewportHeight) / item.height, 0, 1);
      }

      const invertedProgress = 1 - clipProgress;
      const topEdgeFinalPercent = clamp(topEdgeFinalPercentDefault, 1, 100);
      const animatedTopEdgePercent = 100 - ((100 - topEdgeFinalPercent) * invertedProgress);
      const clipPath = item.lastLayout === 'portrait'
        ? 'none'
        : buildRightEdgeClipPathByTopPercent(animatedTopEdgePercent);

      if (isFullyVisible) {
        writeVisualState(item, 'translateY(0px) scale(1)', 'scale(1)', clipPath);
        return;
      }

      /*
       * Interacao por cima: parallax 50% (metade da velocidade).
       */
      if (top < 0) {
        const topOverflow = -top;
        const translateY = topOverflow * topParallaxFactor;

        writeVisualState(
          item,
          `translateY(${translateY.toFixed(2)}px) scale(1)`,
          'scale(1)',
          clipPath
        );
        return;
      }

      const bottomOverflow = bottom - viewportHeight;
      const progress = clamp(bottomOverflow / item.height, 0, 1);
      const scale = finalScale + ((StartScale - finalScale) * progress);

      writeVisualState(
        item,
        'translateY(0px) scale(1)',
        `scale(${scale.toFixed(3)})`,
        clipPath
      );
    });
  };

  const scheduleEffects = () => {
    if (rafId !== 0) {
      return;
    }

    rafId = window.requestAnimationFrame(applyEffects);
  };

  refreshMetrics();
  container.addEventListener('scroll', scheduleEffects, { passive: true });
  window.addEventListener('resize', () => {
    refreshMetrics();
    scheduleEffects();
  });
  window.addEventListener('orientationchange', () => {
    refreshMetrics();
    scheduleEffects();
  });

  scheduleEffects();
})();
