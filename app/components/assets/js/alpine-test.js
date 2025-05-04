$('.btn-show-aside').on('click', function() {
  $('aside').toggleClass('translate-x-full');
  $(this).toggleClass('swap-active');
});