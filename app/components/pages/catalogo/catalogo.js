/*
 * Catalogo snap state controller.
 *
 * Intencao:
 * - detectar secao ativa pelo centro mais proximo
 * - animar imagem continuamente de 110% para 100% durante a transicao
 * - manter evento catalogo:snap na troca real de secao ativa
 */
$(document).ready(function () {
  const $container = $('[catalogo-container]').first();
  const $sections = $container.find('[produto-secao]');
  const layerInactiveClasses = 'opacity-0 translate-y-4';
  const layerActiveClasses = 'opacity-100 translate-y-0';

  if ($container.length === 0 || $sections.length === 0) {
    return;
  }

  /*
   * Aplica estado visual de ativo/inativo na secao.
   */
  const setSectionState = (sectionElement, isActive) => {
    const $section = $(sectionElement);
    const $layers = $section.find('[produto-camada]');

    $section.toggleClass('is-snap-active', isActive);

    if ($layers.length > 0) {
      $layers
        .toggleClass(layerActiveClasses, isActive)
        .toggleClass(layerInactiveClasses, !isActive);
    }
  };

  /*
   * Atualiza zoom continuo da imagem e secao ativa.
   */
  const clamp = (value, min, max) => Math.min(max, Math.max(min, value));
  const $images = $sections.map(function () {
    return $(this).find('[produto-imagem-fundo]').first().get(0);
  });
  const lastImageTransformByElement = new WeakMap();
  let activeSection = null;
  let rafId = 0;

  const updateVisualState = () => {
    rafId = 0;

    const containerElement = $container.get(0);
    const containerRect = containerElement.getBoundingClientRect();
    const containerCenterY = containerRect.top + (containerRect.height / 2);

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
      const distanceFromCenter = Math.abs(sectionCenterY - containerCenterY);

      if (distanceFromCenter < nearestDistance) {
        nearestDistance = distanceFromCenter;
        nextActiveSection = sectionElement;
      }

      const maxDistance = (containerRect.height / 2) + (sectionRect.height / 2);
      const progress = 1 - clamp(distanceFromCenter / maxDistance, 0, 1);
      const scaleValue = 1.10 - (0.10 * progress);
      const nextTransform = `scale(${scaleValue.toFixed(4)})`;
      const previousTransform = lastImageTransformByElement.get(imageElement);

      if (previousTransform !== nextTransform) {
        imageElement.style.transform = nextTransform;
        lastImageTransformByElement.set(imageElement, nextTransform);
      }
    });

    if (nextActiveSection && nextActiveSection !== activeSection) {
      if (activeSection) {
        setSectionState(activeSection, false);
      }

      setSectionState(nextActiveSection, true);
      activeSection = nextActiveSection;

      $container.trigger('catalogo:snap', {
        index: $sections.index(nextActiveSection),
        section: nextActiveSection,
      });
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
