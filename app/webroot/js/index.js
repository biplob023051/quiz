jQuery(window).bind("load", function() {
  if (click_video) {
      $( "a#play_video" ).trigger( "click" );
  } else {
    // do nothing
  }
});

(function ($) {
  $(document).on('click', 'a#play_video', function (e) {
    var src = 'https://www.youtube.com/embed/' + url_src + '?autoplay=1';
    $('#video-modal iframe').attr('src', src);
    $('#video-modal').modal('show');
  });

  $(document).on('click', 'button#close', function (e) {
    $('#video-modal iframe').removeAttr('src');
    $('#video-modal').modal('hide');
  });

  $('#video-modal').on('hidden.bs.modal', function (e) {
    $('#video-modal iframe').removeAttr('src');
  });
})(jQuery);