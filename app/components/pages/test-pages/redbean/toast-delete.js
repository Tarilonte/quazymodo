(function () {
  var payload = window.__redbeanDeletePayload;

  if (!payload) {
    return;
  }

  window.__redbeanDeletePayload = null;

  var message = (payload.message || '').toString();
  var toastType = (payload.type || 'info').toString();

  if (message !== '' && typeof ToastComponent !== 'undefined') {
    ToastComponent.newToast(message, 5000, toastType);
  }

  if (payload.success !== true) {
    return;
  }

  var deletedId = parseInt(payload.deletedId, 10);
  if (Number.isNaN(deletedId)) {
    return;
  }

  var $row = $("input[name='delete_id'][value='" + deletedId + "']")
    .first()
    .closest('tr');

  if ($row.length === 0) {
    return;
  }

  var fadeDuration = 500;
  var slideDuration = 200;

  $row.find('td').each(function () {
    var $cell = $(this);
    if ($cell.children('.row-delete-anim').length === 0) {
      $cell.wrapInner('<div class="row-delete-anim"></div>');
    }
  });

  var $cellContent = $row.find('.row-delete-anim');

  $cellContent
    .stop(true, true)
    .animate({ opacity: 0 }, fadeDuration)
    .slideUp(slideDuration);

  $cellContent.promise().done(function () {
    $row.remove();
  });
})();
