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
      lastTransform: '',
      lastLayout: '',
    }))
    .filter((item) => item.image && item.content && item.contentBody);

  if (items.length === 0) {
    return;
  }

  const finalScale = 1;
  const StartScale = 0.8;
  const topParallaxFactor = 0.5;
  let rafId = 0;

  /*
   * Mantem progress em intervalo valido.
   */
  const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

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
        return;
      }

      item.image.style.left = '0';
      item.image.style.top = '0';
      item.image.style.width = '50%';
      item.image.style.height = '100%';

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
  const writeVisualState = (item, transform) => {
    if (item.lastTransform !== transform) {
      item.image.style.transform = transform;
      item.lastTransform = transform;
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
        writeVisualState(item, 'translateY(0px) scale(1)');
        return;
      }

      if (isFullyVisible) {
        writeVisualState(item, 'translateY(0px) scale(1)');
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
          `translateY(${translateY.toFixed(2)}px) scale(1)`
        );
        return;
      }

      const bottomOverflow = bottom - viewportHeight;
      const progress = clamp(bottomOverflow / item.height, 0, 1);
      const scale = finalScale + ((StartScale - finalScale) * progress);

      writeVisualState(
        item,
        `translateY(0px) scale(${scale.toFixed(3)})`
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
