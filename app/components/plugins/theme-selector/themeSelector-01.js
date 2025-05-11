// Instancia o seletor de temas
themeSelector01 = $("label#btn-themeSelector");

// Define as cores do seletor de temas
var lightTheme = "silk";
var darkTheme = "sunset";

// Captura o tema ativo
if(!getCookie("css-theme")){
  toggle_cssTheme(lightTheme, themeSelector01);
  //console.log("Cookie inexistente");
}else{
  $("html").attr("data-theme", getCookie("css-theme"));
  swap_btn(themeSelector01);
  //console.log("Cookie existente: " + getCookie("css-theme"));
}

// Captura o clique no seletor de temas
$(themeSelector01).click(function() {  
  activeTheme = $("html").attr("data-theme");
  newTheme = (activeTheme === lightTheme)? darkTheme : lightTheme;  
  toggle_cssTheme(newTheme, themeSelector01);  
});

function toggle_cssTheme(newTheme, element) {
  document.cookie = "css-theme=" + newTheme + "; path=/";
  element.toggleClass("swap-active");
  $("html").attr("data-theme", newTheme);
}


function swap_btn(element) {
  if ($("html").attr("data-theme") === darkTheme) {
    element.toggleClass("swap-active");
  }
}