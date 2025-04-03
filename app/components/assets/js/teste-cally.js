$(document).ready(function() {
  // Verifica se o elemento calendar-date existe
  if ($('calendar-date.cally').length) {
    // Adiciona o event listener para o evento change
    $('calendar-date.cally').on('change', function() {
      // Atualiza o texto do botão com o valor selecionado
      $('#cally1').text($(this).val());
    });
  }
});