$('document').ready(function() {
  $("html").attr("data-theme", getCookie("css-theme"));
  $("body").fadeTo(1000, 1);
});

function getCookie(name) {
  var exp = new RegExp('[; ]'+name+'=([^\\s;]*)');
  var matchs = (' '+document.cookie).match(exp);
  if (matchs) return matchs[1];
  return false;
}

/**
 * ---------------------------------------------
 * Tooltips
 * ---------------------------------------------
 * 
 * https://atomiks.github.io/tippyjs/
 * Aplica o tooltip aos elementos com o atributo 'title'
 * Exibe tooltips apenas em telas maiores que 768px
 * 
 */

tippy('[tooltip-primary]', {
  touch: false,
  arrow: tippy.roundArrow,
  content: (reference) => reference.getAttribute('tooltip-primary'),
  theme: 'primary',
});

tippy('[tooltip-primary-content]', {
  touch: false,
  arrow: tippy.roundArrow,
  content: (reference) => reference.getAttribute('tooltip-primary-content'),
  theme: 'primary-content',
});
