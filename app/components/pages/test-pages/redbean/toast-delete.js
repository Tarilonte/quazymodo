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

  $row.stop(true, true).slideUp(220, function () {
    $(this).remove();
  });
})();
