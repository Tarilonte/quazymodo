/*
 * Catalogo v2 snap controller.
 *
 * Intencao:
 * - manter uma secao ativa por vez durante o scroll snap
 * - animar as camadas do texto sem alterar o conteudo original
 * - aplicar zoom progressivo na imagem conforme distancia do centro
 */
$(document).ready(function () {
  const $container = $('[catalogo-v2-container]').first();
  const $sections = $container.find('[catalogo-v2-secao]');

  if ($container.length === 0 || $sections.length === 0) {
    return;
  }

  const containerElement = $container.get(0);

  /*
   * Aplica estado visual de ativo/inativo para a secao e suas camadas.
   */
  const setSectionState = (sectionElement, isActive) => {
    $(sectionElement).toggleClass('is-snap-active', isActive);
  };

  const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

  const updatePointerGlow = (activeSectionRect, viewportRect) => {
    const centerX = activeSectionRect.left + (activeSectionRect.width / 2);
    const focusY = activeSectionRect.top + (activeSectionRect.height * 0.3);
    const xPercent = clamp(((centerX - viewportRect.left) / viewportRect.width) * 100, 0, 100);
    const yPercent = clamp(((focusY - viewportRect.top) / viewportRect.height) * 100, 0, 100);

    containerElement.style.setProperty('--catalogo-v2-pointer-x', `${xPercent.toFixed(2)}%`);
    containerElement.style.setProperty('--catalogo-v2-pointer-y', `${yPercent.toFixed(2)}%`);
  };

  const $images = $sections.map(function () {
    return $(this).find('[produto-imagem-fundo]').first().get(0);
  });
  const lastImageTransformByElement = new WeakMap();
  let activeSection = null;
  let rafId = 0;

  /*
   * Atualiza secao ativa e escala da imagem conforme progressao do scroll.
   */
  const updateVisualState = () => {
    rafId = 0;

    const viewportRect = containerElement.getBoundingClientRect();
    const viewportCenterY = viewportRect.top + (viewportRect.height / 2);

    let nextActiveSection = null;
    let nearestDistance = Number.POSITIVE_INFINITY;

    $sections.each(function (index) {
      const sectionElement = this;
      const imageElement = $images.get(index);

      if (!imageElement) {
        return;
      }

      const sectionRect = sectionElement.getBoundingClientRect();
      const sectionCenterY = sectionRect.top + (sectionRect.height / 2);
      const distanceFromCenter = Math.abs(sectionCenterY - viewportCenterY);

      if (distanceFromCenter < nearestDistance) {
        nearestDistance = distanceFromCenter;
        nextActiveSection = sectionElement;
      }

      const maxDistance = (viewportRect.height / 2) + (sectionRect.height / 2);
      const progress = 1 - clamp(distanceFromCenter / maxDistance, 0, 1);
      const scaleValue = 1.12 - (0.12 * progress);
      const nextTransform = `scale(${scaleValue.toFixed(4)})`;
      const previousTransform = lastImageTransformByElement.get(imageElement);

      if (previousTransform !== nextTransform) {
        imageElement.style.transform = nextTransform;
        lastImageTransformByElement.set(imageElement, nextTransform);
      }
    });

    if (nextActiveSection) {
      updatePointerGlow(nextActiveSection.getBoundingClientRect(), viewportRect);
    }

    if (nextActiveSection && nextActiveSection !== activeSection) {
      if (activeSection) {
        setSectionState(activeSection, false);
      }

      setSectionState(nextActiveSection, true);
      activeSection = nextActiveSection;
    }
  };

  const scheduleUpdate = () => {
    if (rafId !== 0) {
      return;
    }

    rafId = window.requestAnimationFrame(updateVisualState);
  };

  $sections.each(function () {
    setSectionState(this, false);
  });

  $container.on('scroll', scheduleUpdate);
  $(window).on('resize orientationchange', scheduleUpdate);
  scheduleUpdate();
});
