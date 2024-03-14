// Exibe a senha para o usuário
$(".pw-viewer").on("click", function(e) {
  $(this).toggleClass("swap-active");
  var input = $(".pw-input");
  input.attr('type') === 'password' ? input.attr('type','text') : input.attr('type','password');
});

// Coloca o foco inicial no campo de Nome
$("input[name='nome']").focus();

// Envia o formulário
$("#submit-btn").on("click", function(e) {
  e.preventDefault();
  let fields = $("form#register-form").serialize();
  $.ajax({
    type: "POST",
    url: "/User/processRegisterForm",
    data: fields
    })
    .done(function(data) {
      btnStopLoading();
      handle_response(data);
    });
});

// Lida com a resposta
function handle_response(data) {
  let responseCode = data.substring(0,2);
  switch (responseCode) {
    case "99":
      window.location.href = "/User/welcome";
      break;
    case "00":
      user_already_exists();
      break;
  
    default:
      modal_addClass("bg-warning text-warning-content");
      Modal_header = '<span class="mdi mdi-alert-circle text-4xl"></span>';
      Modal_message = 'Desculpe, algo deu errado..'
      Modal_message += `<br>${data}`;
      break;
  }
}

function user_already_exists() {
  Modal_header = '<span class="mdi mdi-badge-account-alert-outline text-5xl mr-2"></span>Usuário já existe';
  Modal_message = 'Parece que já existe um usuário cadastrado com esse email.';
  Modal_message += '<br><a href="login" class="btn btn-outline border-2 mt-6">Ir para o Login</a>';
  modal_show(Modal_header, Modal_message);
}