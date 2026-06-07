const Modal = $("dialog#modal");
const Modal_element = Modal.get(0);

/*
 * Sequenced modal animations: blur/fade the backdrop first, then reveal
 * the modal box with a jQuery slide.
 */
const Modal_animation = {
  backdropDuration: 300,
  revealDuration: 400
};

function modal_isOpen() {
  return Modal_element?.open === true;
}

function modal_isLocked() {
  return Modal.hasClass("modal-locked");
}

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

function modal_resetState() {
  /*
   * Centraliza a limpeza visual para manter o estado consistente tanto no
   * fechamento animado quanto em fechamentos nativos disparados pelo dialog.
   */
  Modal
    .find(".modal-box")
    .removeClass('bg-warning text-warning-content');

  Modal
    .find(".modal-backdrop")
    .stop(true, true)
    .css('opacity', '0');

  modal_prepareBoxForReveal();
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
  
  if (modal_isOpen()) {
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

  $backdrop
    .stop(true, true)
    .css('opacity', '0');

  Modal_element.showModal();
  $("html").css("scrollbar-gutter", "stable");

  $backdrop
    .stop(true, true)
    .css('opacity', '0')
    .animate({opacity: "1"}, Modal_animation.backdropDuration, function() {
      modal_revealBox();
    });
}

function modal_close() {
  const $box = Modal.find("#modal-box");

  if (!modal_isOpen()) {
    return;
  }

  $box
    .stop(true, true)
    .slideUp(Modal_animation.revealDuration, function() {
      modal_resetState();
      Modal_element.close();
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

$("div.modal-backdrop, button.modal-close").click(function(event) {
  event.preventDefault();

  if (!modal_isLocked()) {
    modal_close();
  }
});

Modal.on('cancel', function(event) {
  /*
   * Intercepta o fechamento nativo por Escape para preservar a animacao atual
   * e respeitar o estado locked do componente.
   */
  event.preventDefault();

  if (!modal_isLocked()) {
    modal_close();
  }
});

Modal.on('close', function() {
  $("html").css("scrollbar-gutter", "");
  modal_unlock();
  modal_resetState();
});
