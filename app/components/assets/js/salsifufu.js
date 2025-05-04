let confetti = new Confetti('htmx_test_button');
$('#htmx_test_button')
  .removeAttr('hx-get hx-target hx-swap hx-trigger')
  .text('Now click me to see confetti!');