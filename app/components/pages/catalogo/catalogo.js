/*
 * Catalogo visual behavior.
 *
 * Intencao:
 * - manter parallax na imagem quando a secao sai por cima
 * - manter zoom no hero-content quando a secao entra/sai por baixo
 * - separar clipping (frame) de transform (imagem)
 * - alinhar arestas entre secoes (base atual segue topo da proxima)
 */
(() => {
  const container = document.querySelector('[data-catalogo-container]');
  const sections = Array.from(document.querySelectorAll('[data-catalogo-section]'));

  if (!container || sections.length === 0) {
    return;
  }

  const finalScale = 1;
  const startScale = 0.6;
  const topParallaxFactor = 0.8;
  const topEdgeFinalPercentDefault = 80;
  let rafId = 0;

  /*
   * Mantem progress em intervalo valido.
   */
  const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

  /*
   * Cria frame para separar clipping (frame) de transform (imagem).
   */
  const ensureImageFrame = (image) => {
    if (image.parentElement?.hasAttribute('data-catalogo-section-image-frame')) {
      return image.parentElement;
    }

    const frame = document.createElement('div');
    frame.setAttribute('data-catalogo-section-image-frame', '');
    frame.className = 'pointer-events-none absolute overflow-hidden';

    image.parentNode.insertBefore(frame, image);
    frame.appendChild(image);

    image.style.position = 'absolute';
    image.style.left = '0';
    image.style.top = '0';
    image.style.width = '100%';
    image.style.height = '100%';

    return frame;
  };

  /*
   * Gera clip-path com arestas superior e inferior em percentual.
   */
  const buildRightEdgeClipPathByEdgePercents = (topPercent, bottomPercent) => {
    const normalizedTopPercent = clamp(topPercent, 1, 100);
    const normalizedBottomPercent = clamp(bottomPercent, 1, 100);
    return `polygon(0 0, ${normalizedTopPercent.toFixed(2)}% 0, ${normalizedBottomPercent.toFixed(2)}% 100%, 0 100%)`;
  };

  const items = sections
    .map((section) => {
      const image = section.querySelector('[data-catalogo-section-image]');
      const frame = image ? ensureImageFrame(image) : null;

      return {
        section,
        frame,
        image,
        content: section.querySelector('[data-catalogo-section-content]'),
        contentBody: section.querySelector('[data-catalogo-section-content] > div'),
        top: 0,
        height: 0,
        lastImageTransform: '',
        lastContentTransform: '',
        lastClipPath: '',
        lastLayout: '',
      };
    })
    .filter((item) => item.image && item.frame && item.content && item.contentBody);

  if (items.length === 0) {
    return;
  }

  /*
   * Atualiza metrica de layout para evitar leituras repetidas por frame.
   */
  const refreshMetrics = () => {
    items.forEach((item) => {
      item.top = item.section.offsetTop;
      item.height = item.section.offsetHeight;

      const isPortrait = item.section.offsetHeight > item.section.offsetWidth;
      item.lastLayout = isPortrait ? 'portrait' : 'landscape';

      if (isPortrait) {
        const sectionHeight = item.section.offsetHeight;

        item.frame.style.left = '0';
        item.frame.style.top = '0';
        item.frame.style.width = '100%';
        item.frame.style.height = 'auto';

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

        item.frame.style.height = `${imageHeight}px`;
        item.content.style.height = `${contentHeight}px`;
        item.frame.style.clipPath = 'none';
        item.frame.style.webkitClipPath = 'none';
        return;
      }

      item.frame.style.left = '0';
      item.frame.style.top = '0';
      item.frame.style.width = '50%';
      item.frame.style.height = '100%';

      const clipPath = buildRightEdgeClipPathByEdgePercents(100, 100);
      item.frame.style.clipPath = clipPath;
      item.frame.style.webkitClipPath = clipPath;

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
      item.frame.style.clipPath = clipPath;
      item.frame.style.webkitClipPath = clipPath;
      item.lastClipPath = clipPath;
    }
  };

  const applyEffects = () => {
    rafId = 0;

    const scrollTop = container.scrollTop;
    const viewportHeight = container.clientHeight;
    const topEdgeFinalPercent = clamp(topEdgeFinalPercentDefault, 1, 100);

    const states = items.map((item) => {
      const top = item.top - scrollTop;
      const bottom = top + item.height;
      const isFullyVisible = top >= 0 && bottom <= viewportHeight;
      const isFar = bottom < -item.height || top > viewportHeight + item.height;
      const isPortrait = item.lastLayout === 'portrait';

      let clipProgress = 0;
      if (top < 0) {
        clipProgress = clamp((-top) / item.height, 0, 1);
      } else if (bottom > viewportHeight) {
        clipProgress = clamp((bottom - viewportHeight) / item.height, 0, 1);
      }

      const invertedProgress = 1 - clipProgress;
      const animatedTopEdgePercent = 100 - ((100 - topEdgeFinalPercent) * invertedProgress);

      return {
        item,
        top,
        bottom,
        isFullyVisible,
        isFar,
        isPortrait,
        topEdgePercent: isPortrait ? 100 : animatedTopEdgePercent,
      };
    });

    states.forEach((state, index) => {
      const { item, top, bottom, isFullyVisible, isFar, isPortrait, topEdgePercent } = state;
      const nextState = states[index + 1];
      const nextPercent = !nextState || nextState.isPortrait ? 100 : nextState.topEdgePercent;

      const clipPath = isPortrait
        ? 'none'
        : buildRightEdgeClipPathByEdgePercents(topEdgePercent, nextPercent);

      if (isFar || isFullyVisible) {
        writeVisualState(item, 'translateY(0px) scale(1)', 'scale(1)', clipPath);
        return;
      }

      /*
       * Interacao por cima: parallax na imagem.
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
      const scale = finalScale + ((startScale - finalScale) * progress);

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
