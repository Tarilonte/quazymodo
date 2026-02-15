/*
 * Home sections zoom behavior.
 *
 * Intencao: combinar dois comportamentos de scroll:
 * - por cima: imagem rola na metade da velocidade da secao
 * - por baixo: zoom varia ate 20%
 * - secao inteira visivel: tamanho original
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
    }))
    .filter((item) => item.image);

  if (items.length === 0) {
    return;
  }

  const minScale = 1;
  const maxScale = 1.3;
  const topParallaxFactor = 0.5;
  const maxBlurPx = 8;
  let rafId = 0;

  /*
   * Mantem progress em intervalo valido.
   */
  const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

  const applyZoom = () => {
    rafId = 0;

    const containerRect = container.getBoundingClientRect();

    items.forEach(({ section, image }) => {
      const sectionRect = section.getBoundingClientRect();
      const isFullyVisible = sectionRect.top >= containerRect.top && sectionRect.bottom <= containerRect.bottom;

      if (isFullyVisible) {
        image.style.transform = 'translateY(0px) scale(1)';
        image.style.filter = 'blur(0px)';
        return;
      }

      /*
       * Interacao por cima: parallax 50% (metade da velocidade).
       */
      if (sectionRect.top < containerRect.top) {
        const topOverflow = containerRect.top - sectionRect.top;
        const topProgress = clamp(topOverflow / sectionRect.height, 0, 1);
        const translateY = topOverflow * topParallaxFactor;
        const blur = maxBlurPx * topProgress;
        image.style.transform = `translateY(${translateY.toFixed(2)}px) scale(1)`;
        image.style.filter = `blur(${blur.toFixed(2)}px)`;
        return;
      }

      const bottomOverflow = sectionRect.bottom - containerRect.bottom;
      const progress = clamp(bottomOverflow / sectionRect.height, 0, 1);
      const scale = minScale + ((maxScale - minScale) * progress);
      const blur = maxBlurPx * progress;

      image.style.transform = `translateY(0px) scale(${scale.toFixed(3)})`;
      image.style.filter = `blur(${blur.toFixed(2)}px)`;
    });
  };

  const scheduleZoom = () => {
    if (rafId !== 0) {
      return;
    }

    rafId = window.requestAnimationFrame(applyZoom);
  };

  container.addEventListener('scroll', scheduleZoom, { passive: true });
  window.addEventListener('resize', scheduleZoom);

  scheduleZoom();
})();
