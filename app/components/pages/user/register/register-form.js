$(document).ready(function () {
    const $registerForm = $('#register-form');

    // Show/Hide password
    $(".pw-viewer").on("click", function() {
      $(".pw-viewer").toggleClass("swap-active");
      var $passwordInputs = $(".pw-input");
      $passwordInputs.attr('type', $passwordInputs.attr('type') === 'password' ? 'text' : 'password');
    });

    // Submit form
    $registerForm.on('submit', function (event) {
        if (passwordMatch() == false) {
          event.preventDefault();
          modal_show('Senhas não Coincidem', 'As senhas digitadas não são iguais. Por favor, verifique e tente novamente.');
          return false;
        }
    });

    // Adiciona um listener para o evento htmx:beforeRequest
    // para impedir a submissão do HTMX se a validação do lado do cliente falhar.
    $registerForm.on('htmx:beforeRequest', function (event) {
      console.log('htmx:beforeRequest');
      if (passwordMatch() == false) {
        event.preventDefault(); // Impede a requisição HTMX
      }
    });
});

function passwordMatch() {
    $passwordMatch = $('input[name="password"]').val() === $('input[name="password_confirmation"]').val();
    if ($passwordMatch == false) {
      return false;
    }
}