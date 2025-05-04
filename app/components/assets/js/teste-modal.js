const body_a = $(".body-a");
const body_b = $(".body-b");

$('body').on('click', '.callBodyA', function() {
  modal_show('Vamos mudar o modal', body_a, false, false);
});

$('body').on('click', '.callBodyB', function() {
  modal_show('Funcionou!', body_b, false, false);
});