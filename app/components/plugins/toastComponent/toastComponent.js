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
    const $toast = this.template.clone();
    const normalizedType = this.normalizeToastType(toastType);
    const totalDuration = Math.max(1, Number(duration) || 5000);

    // Estiliza o toast

    $toast
      .removeClass('hidden')
      .addClass('block')

    if (normalizedType) {
      // Adiciona a classe de toastType
      $toast.addClass('alert-' + normalizedType);
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

    // Controle de vida do toast (pausa no hover e retomada precisa).
    const $progressBar = $toast.find('progress');
    const progressBarElement = $progressBar.get(0);
    let animationFrameId = null;
    let segmentStartedAt = 0;
    let segmentDuration = totalDuration;
    let remainingMs = totalDuration;
    let isPaused = false;
    let isHoverPaused = false;
    let isButtonPaused = false;
    let isClosed = false;
    const $pauseButton = $toast.find('.btn-toggle-toast-pause');

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

    // Reflete no botao se o pause atual veio do clique manual.
    const syncPauseButton = () => {
      $pauseButton
        .toggleClass('ti-player-pause', !isButtonPaused)
        .toggleClass('ti-player-play', isButtonPaused)
        .attr('aria-label', isButtonPaused ? 'Retomar toast' : 'Pausar toast');
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

      if (pauseSource === 'button') {
        isButtonPaused = true;
        syncPauseButton();
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

      if (pauseSource === 'button') {
        isButtonPaused = false;
        syncPauseButton();
      }

      if (isClosed || isHoverPaused || isButtonPaused) {
        return;
      }

      resumeTimers();
    };

    const toggleButtonPause = () => {
      if (isClosed) {
        return;
      }

      if (isButtonPaused) {
        resumeToast({ pauseSource: 'button' });
        return;
      }

      pauseToast({ pauseSource: 'button' });
    };

    const toastController = {
      pauseToast,
      resumeToast,
    };

    ToastComponent.registerToastController({ controller: toastController });
    $toast.data('toastFinalize', finalizeToast);
    $toast.data('toastTogglePause', toggleButtonPause);
    syncPauseButton();

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
    toast
        .addClass("animate__animated animate__backOutRight")
        .on("animationend webkitAnimationEnd oAnimationEnd", function() {
          $(this).remove();
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

$('#toasts_container').on('click', '.btn-toggle-toast-pause', function() {
  const $toast = $(this).closest('.toast_message');
  const togglePause = $toast.data('toastTogglePause');

  if (typeof togglePause === 'function') {
    togglePause();
  }
});

$('#toasts_container').on('mouseenter', function() {
  ToastComponent.pauseAllVisibleToasts();
});

$('#toasts_container').on('mouseleave', function() {
  ToastComponent.resumeAllVisibleToasts();
});
