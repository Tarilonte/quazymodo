class ToastComponent {

  static container = $('#toasts_container');
  static template = this.container.find('.toast_message.hidden');

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
    const $stoastTypes = [
      'alert-info', 'alert-success', 'alert-warning', 'alert-error', 
      'progress-info', 'progress-success', 'progress-warning', 'progress-error'
    ];

    // Clona o template
    const $toast = this.template.clone();

    // Estiliza o toast

    $toast
      .removeClass('hidden')
      .addClass('block')

    if (toastType) {
      // Adiciona a classe de toastType
      $toast.addClass('alert-' + toastType);
    }
    
    // Define a mensagem
    $toast.find('.toast_message').text(message);
    
    // Adiciona ao container
    this.container.append($toast);
    
    // Animação da barra de progresso
    let $progressBar = $toast.find("progress");
      $progressBar.addClass('progress-' + toastType);
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

  static removeToast(toast) {
    toast
        .removeClass("animate__animated animate__backInUp")
        .addClass("animate__animated animate__backOutRight")
        .on("animationend webkitAnimationEnd oAnimationEnd", function() {
          $(this).remove();
        });
  }
}

$('#toasts_container').on('click', ".btn-close-toast", function() {
  console.log('close toast');
  const $toast = $(this).parent('.toast_message');
  ToastComponent.removeToast($toast);
});