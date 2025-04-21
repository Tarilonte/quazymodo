class ToastComponent {

  static container = $('#toasts_container');
  static template = this.container.find('.toast_message.hidden');
 

  static newToast(message, duration = 5000, toastType = null) {
    // Declara os toastTypes para refletir no tailwind
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
        if (currentValue > 5) {
          $progressBar.attr("value", currentValue - 5);
        } else {
          clearInterval(this);
        }
      }, duration / 20);
    
    // Remove após a duração
    setTimeout(() => {
      $toast
        .removeClass("animate__animated animate__backInUp")
        .addClass("animate__animated animate__backOutRight")
        .on("animationend webkitAnimationEnd oAnimationEnd", function() {
          $(this).remove();
        });

      // Fallback: Remove após 6s se a animação falhar
      setTimeout(() => {
        if ($toast.is(":visible")) $toast.remove();
      }, (duration + 1000));
    }, duration);
  }
}

// Exemplo de uso
//const toastManager = new ToastComponent();

setInterval(() => {
  const messages = [
    [ 'Warning Message' , 'warning'],
    [ 'Error Message' , 'error'],
    [ 'Success Message' , 'success'],
    [ 'Info Message' , 'info'],
    [ 'Default Message' , null],
  ]
  const rand = Math.floor(Math.random() * messages.length);
  ToastComponent.newToast(messages[rand][0], 8000, messages[rand][1]);
}, 3000); 