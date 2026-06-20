class ToastComponent {

  // Gerencia o template de toast e aplica estilo/icone por tipo.
  static container = $('#toasts_container');
  static template = this.container.find('.toast_message.hidden');
  static activeToastControllers = new Set();
  static iconMap = {
    info: 'mdi-information-variant-circle',
    success: 'mdi-check-bold',
    warning: 'mdi-alert',
    error: 'mdi-close-circle',
  };

  // Registra o controller do toast para comandos globais de hover.
  static registerToastController({ controller }) {
    this.activeToastControllers.add(controller);
  }

  static unregisterToastController({ controller }) {
    this.activeToastControllers.delete(controller);
  }

  static pauseAllVisibleToasts() {
    this.activeToastControllers.forEach((controller) => {
      controller.pauseToast({ pauseSource: 'hover' });
    });
  }

  static resumeAllVisibleToasts() {
    this.activeToastControllers.forEach((controller) => {
      controller.resumeToast({ pauseSource: 'hover' });
    });
  }

  static captureToastPositions() {
    return new Map(
      this.container
        .find('.toast_message:not(.hidden)')
        .toArray()
        .map((element) => [element, element.getBoundingClientRect()])
    );
  }

  // Aplica FLIP simples para suavizar o reposicionamento da pilha.
  static animateToastStackShift({ previousPositions }) {
    this.container
      .find('.toast_message:not(.hidden)')
      .toArray()
      .forEach((element) => {
        const previousRect = previousPositions.get(element);

        if (!previousRect) {
          return;
        }

        const nextRect = element.getBoundingClientRect();
        const deltaY = previousRect.top - nextRect.top;

        if (deltaY === 0) {
          return;
        }

        // Forca o estado invertido sem transicao antes de animar de volta.
        element.style.transition = 'none';
        element.style.setProperty('--toast-shift-y', `${deltaY}px`);
        element.getBoundingClientRect();

        requestAnimationFrame(() => {
          element.style.transition = '';
          element.style.setProperty('--toast-shift-y', '0px');

          const clearShiftStyles = () => {
            element.style.removeProperty('transition');
            element.style.removeProperty('--toast-shift-y');
          };

          element.addEventListener('transitionend', clearShiftStyles, { once: true });
        });
      });
  }

  /**
   * Creates and displays a new toast notification.
   *
   * @param {string} message The message to be displayed in the toast.
   * @param {number} [duration=5000] The duration in milliseconds for which the toast will be visible.
   * @param {string|null} [toastType=null] The type   of the toast, which determines its styling.
   *                                      Accepted values can be 'info', 'success', 'warning', 'error'.
   *                                      If null, no specific type class is added.
   */
  static newToast(message, duration = 5000, toastType = null) {
    // Clona o template
    const previousPositions = this.captureToastPositions();
    const $toast = this.template.clone();
    const $toastPanel = $toast.find('.toast_panel');
    const normalizedType = this.normalizeToastType(toastType);
    const totalDuration = Math.max(1, Number(duration) || 5000);
    const entryAnimationClasses = 'animate__animated animate__slideInUp';

    // Estiliza o toast

    $toast
      .removeClass('hidden')
      .addClass('block')

    // Anima apenas o painel interno para nao conflitar com o transform do toast.
    $toastPanel.addClass(entryAnimationClasses);

    if (normalizedType) {
      // Adiciona a classe de toastType
      $toastPanel.addClass('alert-' + normalizedType);
    }

    // Aplica o icone no lado esquerdo, alinhado ao centro.
    const iconClass = this.iconMap[normalizedType] || this.iconMap.info;
    $toast.find('.toast_icon')
      .removeClass(Object.values(this.iconMap).join(' '))
      .addClass(iconClass);
    
    // Define a mensagem
    $toast.find('.toast_text').text(message);
    
    // Adiciona ao container
    this.container.append($toast);

    // Limpa as classes de entrada para evitar interferencia com estados futuros.
    $toastPanel.on('animationend webkitAnimationEnd oAnimationEnd', function() {
      $(this).removeClass(entryAnimationClasses);
    });

    this.animateToastStackShift({ previousPositions });

    // Controle de vida do toast (pausa no hover e retomada precisa).
    const $progressBar = $toast.find('progress');
    const progressBarElement = $progressBar.get(0);
    let animationFrameId = null;
    let segmentStartedAt = 0;
    let segmentDuration = totalDuration;
    let remainingMs = totalDuration;
    let isPaused = false;
    let isHoverPaused = false;
    let isClosed = false;

    // Cancela a animacao ativa para evitar loops concorrentes.
    const clearAnimationFrame = () => {
      if (animationFrameId !== null) {
        cancelAnimationFrame(animationFrameId);
        animationFrameId = null;
      }
    };

    const updateProgress = (now) => {
      const elapsed = now - segmentStartedAt;
      const currentRemaining = Math.max(0, segmentDuration - elapsed);
      const progress = Math.max(0, Math.min(100, (currentRemaining / totalDuration) * 100));

      // Mantem valores fracionarios para reduzir o efeito de degraus no native progress.
      progressBarElement.value = progress;

      return currentRemaining;
    };

    const finalizeToast = () => {
      if (isClosed) {
        return;
      }

      isClosed = true;
      clearAnimationFrame();
      ToastComponent.unregisterToastController({ controller: toastController });
      this.removeToast($toast);
    };

    // Usa um unico loop RAF para manter barra e expiracao sincronizadas.
    const tickProgress = (now) => {
      if (isClosed || isPaused) {
        animationFrameId = null;
        return;
      }

      remainingMs = updateProgress(now);

      if (remainingMs <= 0) {
        finalizeToast();
        return;
      }

      animationFrameId = requestAnimationFrame(tickProgress);
    };

    const startSegment = () => {
      if (isClosed) {
        return;
      }

      segmentDuration = remainingMs;
      segmentStartedAt = performance.now();
      updateProgress(segmentStartedAt);
      clearAnimationFrame();
      animationFrameId = requestAnimationFrame(tickProgress);
    };

    const pauseTimers = () => {
      if (isPaused || isClosed) {
        return;
      }

      const now = performance.now();
      const elapsed = now - segmentStartedAt;
      remainingMs = Math.max(0, segmentDuration - elapsed);
      isPaused = true;
      clearAnimationFrame();
      updateProgress(now);
    };

    const pauseToast = ({ pauseSource }) => {
      if (pauseSource === 'hover') {
        isHoverPaused = true;
      }

      if (isPaused || isClosed) {
        return;
      }

      pauseTimers();
    };

    const resumeTimers = () => {
      if (!isPaused || isClosed) {
        return;
      }

      isPaused = false;

      if (remainingMs <= 0) {
        finalizeToast();
        return;
      }

      startSegment();
    };

    const resumeToast = ({ pauseSource }) => {
      if (pauseSource === 'hover') {
        isHoverPaused = false;
      }

      if (isClosed || isHoverPaused) {
        return;
      }

      resumeTimers();
    };

    const toastController = {
      pauseToast,
      resumeToast,
    };

    ToastComponent.registerToastController({ controller: toastController });
    $toast.data('toastFinalize', finalizeToast);

    startSegment();
  }

  static normalizeToastType(toastType) {
    // Evita classes invalidas e garante fallback previsivel.
    if (!toastType || typeof toastType !== 'string') {
      return 'info';
    }

    const normalized = toastType.trim().toLowerCase();
    return this.iconMap[normalized] ? normalized : 'info';
  }

  static removeToast(toast) {
    const $toastPanel = toast.find('.toast_panel');

    // Reaproveita o Animate.css so no painel interno para preservar o FLIP do wrapper.
    $toastPanel
        .removeClass('animate__slideInUp animate__backOutRight')
        .addClass("animate__animated animate__backOutRight")
        .on("animationend webkitAnimationEnd oAnimationEnd", function() {
          toast.remove();
        });
  }
}

$('#toasts_container').on('click', ".btn-close-toast", function() {
  const $toast = $(this).closest('.toast_message');
  const finalizeToast = $toast.data('toastFinalize');

  if (typeof finalizeToast === 'function') {
    finalizeToast();
    return;
  }

  ToastComponent.removeToast($toast);
});

$('#toasts_container').on('mouseenter', function() {
  ToastComponent.pauseAllVisibleToasts();
});

$('#toasts_container').on('mouseleave', function() {
  ToastComponent.resumeAllVisibleToasts();
});
