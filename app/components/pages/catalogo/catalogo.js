/*
 * Catalogo snap state controller.
 *
 * Intencao:
 * - detectar quando uma secao fica ativa no snap
 * - animar imagem de 110% para 100% na secao ativa
 * - adicionar transicao elegante no bloco de descricao
 * - reverter estados quando a secao perde o estado ativo
 */
$(document).ready(function () {
  const $container = $('[catalogo-container]').first();
  const $sections = $container.find('[produto-secao]');

  if ($container.length === 0 || $sections.length === 0) {
    return;
  }

  /*
   * Aplica estado visual de ativo/inativo na secao.
   */
  const setSectionState = (sectionElement, isActive) => {
    const $section = $(sectionElement);
    const $image = $section.find('[produto-imagem]').first();
    const $description = $section.find('[produto-descricao]').first();

    if ($image.length === 0) {
      return;
    }

    $section.toggleClass('is-snap-active', isActive);

    $image.css({
      transition: 'background-size 1300ms cubic-bezier(0.22, 1, 0.36, 1), filter 1300ms ease',
      backgroundSize: isActive ? '100%' : '110%',
      filter: isActive ? 'brightness(1) saturate(1)' : 'brightness(0.9) saturate(0.92)',
    });

    if ($description.length > 0) {
      $description.css({
        transition: 'transform 900ms cubic-bezier(0.22, 1, 0.36, 1), opacity 900ms ease',
        transform: isActive ? 'translateY(0px)' : 'translateY(18px)',
        opacity: isActive ? '1' : '0.72',
      });
    }
  };

  /*
   * Estado inicial: todas as secoes inativas (110%).
   */
  $sections.each(function () {
    setSectionState(this, false);
  });

  let activeSection = null;

  if (!('IntersectionObserver' in window)) {
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      let bestCandidate = null;
      let bestRatio = 0;

      entries.forEach((entry) => {
        const sectionElement = entry.target;

        if (entry.isIntersecting && entry.intersectionRatio >= 0.85 && entry.intersectionRatio > bestRatio) {
          bestRatio = entry.intersectionRatio;
          bestCandidate = sectionElement;
        }
      });

      if (bestCandidate && bestCandidate !== activeSection) {
        if (activeSection) {
          setSectionState(activeSection, false);
        }

        setSectionState(bestCandidate, true);
        activeSection = bestCandidate;

        $container.trigger('catalogo:snap', {
          index: $sections.index(bestCandidate),
          section: bestCandidate,
        });
      }
    },
    {
      root: $container.get(0),
      threshold: [0.95, 1],
    }
  );

  $sections.each(function () {
    observer.observe(this);
  });
});
