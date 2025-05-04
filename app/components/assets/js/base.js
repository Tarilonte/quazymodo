$('document').ready(function() {
  $("html").attr("data-theme", getCookie("css-theme"));
  $("body").fadeTo(700, 1);
});

function getCookie(name) {
  var exp = new RegExp('[; ]'+name+'=([^\\s;]*)');
  var matchs = (' '+document.cookie).match(exp);
  if (matchs) {
    return matchs[1];
  }
  //console.log('cookie: ' + name +' not found.');
  return false;
}