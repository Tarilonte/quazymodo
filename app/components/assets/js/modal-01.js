const Modal = $("dialog#modal");
var Modal_header = '';
var Modal_message = '';
var Modal_action = '';

function modal_setHeader(header) {
  Modal.find(".modal-header").html(header);
}

function modal_setMessage(message) {
  Modal.find(".modal-message").html(message);
}

/**
 * Shows a modal with the specified header and message.
 * @param {string} header - The header text for the modal.
 * @param {string} message - The message text for the modal.
 * @param {boolean} addClass - Whether to add a custom CSS class to the modal.
 * @param {boolean} locked - Whether the modal should be locked (not closable).
 */
function modal_show(header='', message='', addClass=false, locked=false) {
  locked ? modal_lock() : modal_unlock();
  addClass ? modal_addClass(addClass) : modal_addClass('bg-primary');
  
  if (Modal.hasClass("modal-open")) {
    modal_refresh(header, message);
  } else {
    modal_open(header, message, addClass);
  }
}

function modal_open(header='', message='') {
  modal_setHeader(header);
  modal_setMessage(message);
  Modal
    .addClass("modal-open")
    .show()
    .focus()
    .find(".modal-backdrop")
      .css('opacity', '0')
      .animate({opacity: "1"},800);
}

function modal_close() {
  modal_setHeader('');
  modal_setMessage('');
  Modal
  // .fadeOut()
  .addClass('animate__animated animate__hinge')
  .removeClass("modal-open")
  .removeClass('animate__animated animate__hinge')
  .find(".modal-box").removeClass('bg-warning text-warning-content');
}

function modal_refresh(header, message) {
  Modal
    .find(".modal-header, .modal-message")
    .slideUp(200,function() {
      modal_setHeader(header);
      modal_setMessage(message);
      $(this).slideDown(200);
    });
}

function modal_addClass(class_name) {
  Modal.find(".modal-box").addClass(class_name);
}

function modal_lock() {
  Modal.addClass("modal-locked");
  $(".modal-lock").removeClass("hidden");
  $(".modal-close").css({opacity: "0"});
}

function modal_unlock() {
  Modal.removeClass("modal-locked");
  $(".modal-lock").addClass("hidden");
  $(".modal-close").fadeTo(1000,1);
}

$("div.modal-backdrop, button.modal-close").click(function() {
  modal_close();
});

Modal.keyup(function(e) {
  if (e.key === "Escape" && !Modal.hasClass("modal-locked")) {
    modal_close()
  }
});