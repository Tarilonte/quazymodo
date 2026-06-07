const Modal = $("dialog#modal");
var Modal_header = '';
var Modal_message = '';
var Modal_action = '';

/*
 * Sequenced modal animations: blur/fade the backdrop first, then reveal
 * the modal box with a jQuery slide.
 */
const Modal_animation = {
  backdropDuration: 300,
  revealDuration: 400
};

function modal_setHeader(header) {
  Modal.find(".modal-header").html(header);
}

function modal_setMessage(message) {
  Modal.find(".modal-message").html(message);
}

function modal_prepareBoxForReveal() {
  Modal
    .find("#modal-box")
    .stop(true, true)
    .hide();
}

function modal_revealBox() {
  const $box = Modal.find("#modal-box");

  $box
    .slideDown(Modal_animation.revealDuration, function() {
      Modal
        .focus()
        .css('outline', 'none');
    });
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
  addClass ? modal_addClass(addClass) : false;
  
  if (Modal.hasClass("modal-open")) {
    modal_refresh(header, message);
  } else {
    modal_open(header, message, addClass);
  }
}

function modal_open(header='', message='') {
  const $backdrop = Modal.find(".modal-backdrop");

  modal_setHeader(header);
  modal_setMessage(message);

  modal_prepareBoxForReveal();

  Modal
    .addClass("modal-open");

  $backdrop
    .stop(true, true)
    .css('opacity', '0')
    .animate({opacity: "1"}, Modal_animation.backdropDuration, function() {
      modal_revealBox();
    });
}

function modal_close() {
  const $box = Modal.find("#modal-box");
  const $backdrop = Modal.find(".modal-backdrop");

  $box
    .stop(true, true)
    .slideUp(Modal_animation.revealDuration, function() {
      Modal
        .removeClass("modal-open")
        .find(".modal-box").removeClass('bg-warning text-warning-content');

      $backdrop
        .stop(true, true)
        .css('opacity', '0');

      modal_prepareBoxForReveal();
    });
}

function modal_refresh(header, message) {
  const animationDuration = 400;
  const $box = Modal.find('#modal-box');

  /*
   * Hide the whole modal box before swapping content so any size change
   * happens off-screen, then bring it back with a single slide.
   */
  $box
    .stop(true, true)
    .slideUp(animationDuration)
    .promise()
    .done(function() {
      modal_setHeader(header);
      modal_setMessage(message);

      $box.slideDown(animationDuration);
    });
}

function modal_addClass(class_name) {
  Modal.find(".modal-box").addClass(class_name);
}

function modal_lock() {
  Modal.addClass("modal-locked");
  $(".modal-lock").removeClass("hidden");
  $(".modal-close").css({opacity: "0"});
  $("#modal-close-btn").hide();
}

function modal_unlock() {
  Modal.removeClass("modal-locked");
  $(".modal-lock").addClass("hidden");
  $(".modal-close").fadeTo(1000,1);
  $("#modal-close-btn").show();
}

$("div.modal-backdrop, button.modal-close").click(function() {
  modal_close();
});

Modal.keyup(function(e) {
  if (e.key === "Escape" && !Modal.hasClass("modal-locked")) {
    modal_close()
  }
});
