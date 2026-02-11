class ToastComponent {

  // Gerencia o template de toast e aplica estilo/icone por tipo.
  static container = $('#toasts_container');
  static template = this.container.find('.toast_message.hidden');
  static iconMap = {
    info: 'mdi-information-outline',
    success: 'mdi-check-circle-outline',
    warning: 'mdi-alert-outline',
    error: 'mdi-alert-circle-outline',
  };

  /**
   * Creates and displays a new toast notification.
   *
   * @param {string} message The message to be displayed in the toast.
   * @param {number} [duration=5000] The duration in milliseconds for which the toast will be visible.
   * @param {string|null} [toastType=null] The type of the toast, which determines its styling.
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
    let timeoutId = null;
    let progressIntervalId = null;
    let segmentStartedAt = 0;
    let segmentDuration = totalDuration;
    let remainingMs = totalDuration;
    let isPaused = false;
    let isClosed = false;

    const clearTimers = () => {
      if (timeoutId !== null) {
        clearTimeout(timeoutId);
        timeoutId = null;
      }

      if (progressIntervalId !== null) {
        clearInterval(progressIntervalId);
        progressIntervalId = null;
      }
    };

    const updateProgress = () => {
      const elapsed = Date.now() - segmentStartedAt;
      const currentRemaining = Math.max(0, segmentDuration - elapsed);
      const progress = Math.max(0, Math.min(100, (currentRemaining / totalDuration) * 100));
      $progressBar.attr('value', Math.round(progress));
    };

    const finalizeToast = () => {
      if (isClosed) {
        return;
      }

      isClosed = true;
      clearTimers();
      this.removeToast($toast);
    };

    const startSegment = () => {
      if (isClosed) {
        return;
      }

      segmentDuration = remainingMs;
      segmentStartedAt = Date.now();
      updateProgress();

      progressIntervalId = setInterval(() => {
        updateProgress();
      }, 40);

      timeoutId = setTimeout(() => {
        remainingMs = 0;
        finalizeToast();
      }, segmentDuration);
    };

    const pauseTimers = () => {
      if (isPaused || isClosed) {
        return;
      }

      const elapsed = Date.now() - segmentStartedAt;
      remainingMs = Math.max(0, segmentDuration - elapsed);
      isPaused = true;
      clearTimers();
      updateProgress();
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

    $toast.on('mouseenter', pauseTimers);
    $toast.on('mouseleave', resumeTimers);
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
    toast
        .addClass("animate__animated animate__backOutRight")
        .on("animationend webkitAnimationEnd oAnimationEnd", function() {
          $(this).remove();
        });
  }
}

$('#toasts_container').on('click', ".btn-close-toast", function() {
  const $toast = $(this).parent('.toast_message');
  const finalizeToast = $toast.data('toastFinalize');

  if (typeof finalizeToast === 'function') {
    finalizeToast();
    return;
  }

  ToastComponent.removeToast($toast);
});
