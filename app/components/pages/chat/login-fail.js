message = 'O apelido <b id="changeModal" class="text-accent">' + nickname + '</b> já está sendo utilizado =/'
message += '<br>Escolha outro por favor.'
modal_show('Poxa,', message );

$("#modal").on("click", "#changeModal", function() {
  modal_show('Funciona!', 'Muito bom' );
});