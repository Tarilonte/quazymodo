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
      top: 0,
      height: 0,
      lastTransform: '',
    }))
    .filter((item) => item.image);

  if (items.length === 0) {
    return;
  }

  const minScale = 1;
  const maxScale = 1.4;
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
      const scale = minScale + ((maxScale - minScale) * progress);

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

  scheduleEffects();
})();
