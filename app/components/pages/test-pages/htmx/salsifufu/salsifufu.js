$('#htmx_test_button').text('Espere...');

(async () => {
  const start = Date.now();
  while (typeof window.Confetti !== 'function') {
    if (Date.now() - start > 5000) {
      $('#htmx_test_button').text('Confetti não carregou :(');
      return;
    }
    await new Promise(r => setTimeout(r, 50));
  }
  new window.Confetti('htmx_test_button');
  $('#htmx_test_button').text('Vamos jogar confete!');
})();