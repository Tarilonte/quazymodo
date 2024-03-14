$("input[name='email']").focus();
// Exibe a senha para o usuário
$(".pw-viewer").on("click", function(e) {
  $(this).toggleClass("swap-active");
  var input = $(".pw-input");
  input.attr('type') === 'password' ? input.attr('type','text') : input.attr('type','password');
});

// Validção e envio do formulário
$("#submit-btn").on("click", function(e) {
  e.preventDefault();
  let fields = $("form#login-form").serialize();
  $.ajax({
    type: "POST",
    url: "/User/processLoginForm",
    data: fields
    })
    .done(function(data) {
      setTimeout(function () {
        btnStopLoading();
        handle_response(data);
      }, 500)
    });
});

// Lida com a resposta do servidor
function handle_response(data) {
  let responseCode = data.substring(0,2);
  switch (responseCode) {
    case "99":
      // login bem sucedido
      history.back();
      break;
    case "00":
      login_denied();
      break;
    case "98":
      // e-mail de redefinição enviado
      finish_reset();
      break;  
    default:
      defaultResponse(data);
      break;
  }  
}

let isInvalidTriggered = false;
document.getElementById("login-form").addEventListener('invalid', function(){
  if (!isInvalidTriggered) {
    btnStopLoading();
    isInvalidTriggered = true;
    // Resetar isInvalidTriggered após um curto delay para garantir que funcione para tentativas subsequentes de submissão
    setTimeout(() => isInvalidTriggered = false, 100);
  }
}, true);

$(document).on("click", ".startReset", function(e){
  start_reset(e);
});

function defaultResponse(data) {
  modal_addClass("bg-warning text-warning-content");
  Modal_header = '<span class="mdi mdi-alert-circle text-5xl mr-2"></span>';
  Modal_header += 'Desculpe, algo deu errado..'
  Modal_message = data;
  modal_show(Modal_header, Modal_message);
}

function login_denied(){
  Modal_header = '<span class="mdi mdi-badge-account-alert-outline text-5xl mr-2"></span>';
  Modal_header += 'Credenciais inválidas.';
  Modal_message = ' <a class="link startReset" href="">Esqueceu a senha?</a>';
  modal_show(Modal_header, Modal_message);
}

function start_reset(e) {
  e.preventDefault();
  let email = $("input[name='email']").val();
  if (email === "") {
    askForEmail();
    return;
  }

  Modal_header = "<span class='mdi mdi-shield-account-outline text-5xl mr-2'></span>";
  Modal_header += "Redefinição de senha";
  Modal_message = "Seu e-email, informado abaixo, está correto?";
  Modal_message += `<br><span class="inline-block font-semibold mt-4 mb-5 text-xl">${email}</span>`;
  Modal_message += `<br><a id="btn-reset" class="btn btn-outline border-2">Está correto</a>`;
  Modal_message += ` <a id="btn-nevermind" class="btn btn-ghost">Está errado</button>`;
  modal_show(Modal_header, Modal_message);
}

$("#modal").on("click", "#btn-nevermind", function() {
  document.getElementById("login-form").reset();
  $("input[name='email']").focus();
  modal_close()
});

$("#modal").on("click", "#btn-reset", function() {

  Modal_header = '<span class="loading loading-dots loading-lg mr-2"></span>';
  Modal_header += 'Aguarde, por favor.';
  Modal_message = 'Estamos enviando um e-mail para você.';
  modal_show(Modal_header, Modal_message);

  let fields = $("form#login-form").serialize();
  $.ajax({
    type: "POST",
    url: "/User/generatePwdResetToken",
    data: fields
    })
    .done(function(data) {
      btnStopLoading();
      handle_response(data);
    });
});

function askForEmail() {
  Modal_header = "<span class='mdi mdi-shield-account-outline text-5xl mr-2'></span>";
  Modal_header += "Redefinição de senha";
  Modal_message = "Informe seu e-mail para reefinir sua senha.";
  modal_show(Modal_header, Modal_message);
}

function finish_reset(){
  Modal_header = '<span class="mdi mdi-email-outline text-5xl mr-2"></span>';
  Modal_header += 'Verifique seu e-mail.';
  Modal_message = 'Você receberá um e-mail para redefinir sua senha.';
  modal_show(Modal_header, Modal_message);
  return false;
}