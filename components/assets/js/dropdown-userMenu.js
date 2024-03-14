if ($('#navbar-userMenuButton').attr('user-email'))  {
  $("#button-user-login").remove();
} else {
  $('div[component-name="dropdown-userMenu"]').remove();
  $("div#btn-navbarInbox").remove();
}

$(document).ready(function() {
  testImage($('#user-picture').attr('src'));
});

function testImage(URL) {
  var tester=new Image();
  tester.onerror=imgReplacement;
  tester.src=URL;
}

function imgReplacement(){
  let match = $('#user-name').text().match(/\s+(\S)/);
  let firstLetter = match ? match[1].toUpperCase() : '';
  $('#user-picture')
    .after(`<div class="flex justify-center items-center bg-primary text-primary-content h-full">
            <span class="font-bold">${firstLetter}</span>
          </div>`)
    .remove();
}