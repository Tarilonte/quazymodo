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
    
    // Animação da barra de progresso
    let $progressBar = $toast.find("progress");
      // Mantem a barra com a mesma cor do texto do toast via text-current.
      setInterval(() => {
        const currentValue = parseInt($progressBar.attr("value"));
        if (currentValue > 0) {
          $progressBar.attr("value", currentValue - 5);
        } else {
          clearInterval(this);
        }
      }, duration / 25);
    
    // Remove após a duração
    setTimeout(() => {
      this.removeToast($toast);      
      // Fallback: Remove após 6s se a animação falhar
      setTimeout(() => {
        if ($toast.is(":visible")) $toast.remove();
      }, (duration + 1000));
    }, duration);
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
  ToastComponent.removeToast($toast);
});
