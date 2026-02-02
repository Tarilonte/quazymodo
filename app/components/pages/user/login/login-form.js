$(document).ready(function () {
    const $registerForm = $('#register-form');

    // Show/Hide password
    $(".pw-viewer").on("click", function() {
      $(".pw-viewer").toggleClass("swap-active");
      var $passwordInputs = $(".pw-input");
      $passwordInputs.attr('type', $passwordInputs.attr('type') === 'password' ? 'text' : 'password');
    });
});