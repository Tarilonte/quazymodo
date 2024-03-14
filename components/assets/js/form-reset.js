$("input[name='password1']").focus();
// Exibe a senha para o usuário
$(".pw-viewer").on("click", function(e) {
  $(".pw-viewer").toggleClass("swap-active");
  var input = $(".pw-input");
  input.attr('type') === 'password' ? input.attr('type','text') : input.attr('type','password');
});

// Validção e envio do formulário
$("form#resetpwd-form").on("submit", function(e) {
  e.preventDefault();

  let password1 = $("input[name='password1']").val();
  let password2 = $("input[name='password2']").val();
  if (password1!== password2) {
    passwordDontMatch();
    return false;
  }

  startProcessing();

  let fields = $(this).serialize();
  $.ajax({
    type: "POST",
    url: "/User/processResetPassword",
    data: fields
    })
    .done(function(data) {
      setTimeout(function () {
        handle_response(data);
      }, 500)
    });
});

// Lida com a resposta do servidor
function handle_response(data) {
  let responseCode = data.substring(0,2);
  switch (responseCode) {
    case "99":
      resetSuccessful();
      break;
    case "00":
      resetFail();
      break;
    default:
      unmatchedResponse(data);
      break;
  }  
}

function unmatchedResponse(data) {
  modal_addClass("bg-warning text-warning-content");
  Modal_header = '<span class="mdi mdi-alert-circle text-5xl mr-2"></span>';
  Modal_header += 'Desculpe, algo deu errado..'
  Modal_message = data;
  modal_show(Modal_header, Modal_message);
}

function passwordDontMatch(){
  Modal_header = '<span class="mdi mdi-form-textbox-password text-5xl mr-2"></span>';
  Modal_header += 'As senhas não coincidem.';
  Modal_message = 'As senhas precisam ser iguais, por favor verifique.';
  modal_show(Modal_header, Modal_message);
}

function resetSuccessful(){
  Modal_header = '<span class="mdi mdi-lock-check text-5xl mr-2"></span>';
  Modal_header += 'Senha redefinida com sucesso!';
  Modal_message = 'Você já pode efetuar o login com sua nova senha.';
  Modal_message += '<br><a href="/" class="btn btn-outline border-2 mt-4">Ir para o Início</a>';
  modal_show(Modal_header, Modal_message);
}

function resetFail(){
  Modal_header = '<span class="mdi mdi-lock-remove text-5xl mr-2"></span>';
  Modal_header += 'Falha na redefinição de senha';
  Modal_message = 'Por favor, vá para o login e solicite novamente a recuperação da senha.';
  Modal_message += '<br><a href="/User/login" class="btn mt-4">Ir para o login</a>';
  modal_show(Modal_header, Modal_message, 'bg-warning text-warning-content');
}

function startProcessing(){
  Modal_header = '<span class="loading loading-dots loading-lg mr-2"></span>';
  Modal_header += 'Redefinindo senha';
  Modal_message = 'Aguarde um pouco, estamos redefinindo sua senha.';
  modal_show(Modal_header, Modal_message, false, true);
}