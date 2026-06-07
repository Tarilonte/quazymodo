/*
 * Examples page interactions.
 *
 * Intencao: demonstrar modal e toast com a API real dos plugins.
 */
$(document).ready(function () {
  // Abre o item correto do accordion quando a URL contem ancora de exemplo.
  const openAccordionFromHash = function (hash) {
    if (!hash) {
      return;
    }

    const $target = $(hash).first();

    if ($target.length === 0) {
      return;
    }

    const $toggle = $target.find('input[data-accordion-toggle]').first();

    if ($toggle.length > 0) {
      $toggle.prop('checked', true);
    }
  };

  // Helper centraliza fallback quando plugin nao estiver carregado.
  const notifyMissingPlugin = function (pluginName) {
    const message = `Plugin ${pluginName} nao esta disponivel nesta pagina.`;

    if (typeof ToastComponent !== 'undefined') {
      ToastComponent.newToast(message, 4200, 'error');
      return;
    }

    window.alert(message);
  };

  // Modal de exemplo com conteudo explicativo.
  $('#btn-exemplo-modal').on('click', function () {
    if (typeof window.modal_show !== 'function') {
      notifyMissingPlugin('modalComponent');
      return;
    }

    window.modal_show(
      'Confirmacao orientada',
      'Use modal quando a acao exigir foco total e contexto curto. Para feedback simples, prefira toast.',
      false,
      false,
    );
  });

  // Modal travado para demonstrar estado de bloqueio temporario.
  $('#btn-exemplo-modal-locked').on('click', function () {
    if (typeof window.modal_show !== 'function') {
      notifyMissingPlugin('modalComponent');
      return;
    }

    window.modal_show(
      'Processando',
      'Este exemplo abre o modal no modo locked para impedir fechamento acidental durante um fluxo critico.',
      'bg-primary text-primary-content',
      true,
    );

    setTimeout(function () {
      if (typeof window.modal_unlock === 'function') {
        window.modal_unlock();
      }

      if (typeof window.modal_show === 'function') {
        window.modal_show(
          'Concluido',
          'Fluxo finalizado. O modal voltou ao estado normal para permitir fechamento.',
          false,
          false,
        );
      }
    }, 1800);
  });

  // Toasts de exemplo para cada tipo sem bloquear navegacao.
  $('[data-toast-type]').on('click', function () {
    if (typeof ToastComponent === 'undefined') {
      notifyMissingPlugin('toastComponent');
      return;
    }

    const toastType = $(this).data('toast-type');
    const toastMessage = `Exemplo de toast ${toastType}: feedback rapido e nao bloqueante.`;

    ToastComponent.newToast(toastMessage, 8000, toastType);
  });

  // Sincroniza links de ancora com a abertura do accordion.
  $('[data-accordion-link]').on('click', function () {
    const hash = $(this).attr('href') || '';
    openAccordionFromHash(hash);
  });

  openAccordionFromHash(window.location.hash);
});
