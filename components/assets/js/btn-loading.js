$(".btn-loading").click(function() {
  btnStartLoading($(this));
});

function btnStopLoading() {
    let btn = $(".btn-loading.is-loading");
    btn
    .removeClass("is-loading")
    .html(btn.attr("value"))
    .prop('disabled', false);

}

$("#modal").on("click", ".btn-loading", function() {
  btnStartLoading($(this));
});

function btnStartLoading(btn) {
  let w = btn.width();
  let value = btn.html();
  btn
    .width(w)
    .addClass("is-loading")
    .attr("value", value)
    .html('<span class="loading loading-dots loading-lg"></span>')
    .prop('disabled', true);
}