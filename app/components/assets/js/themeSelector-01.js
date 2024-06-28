// Instancia o seletor de temas
themeSelector01 = $("label.swap[component-name='themeSelector-01']");

// Captura o tema ativo
if(!getCookie("css-theme")){
  toggle_cssTheme("light", themeSelector01);
  //console.log("Cookie inexistente");
}else{
  $("html").attr("data-theme", getCookie("css-theme"));
  swap_btn(themeSelector01);
  //console.log("Cookie existente");
}

// Captura o clique no seletor de temas
$(themeSelector01).click(function() {  
  activeTheme = $("html").attr("data-theme");
  newTheme = (activeTheme === "light")? "dark" : "light";  
  toggle_cssTheme(newTheme, themeSelector01);  
});

function toggle_cssTheme(newTheme, element) {
  document.cookie = "css-theme=" + newTheme + "; path=/";
  element.toggleClass("swap-active");
  $("html").attr("data-theme", newTheme);
}


function swap_btn(element) {
  if ($("html").attr("data-theme") === "dark") {
    element.toggleClass("swap-active");
  }
}