/* A a bit animation for the contact form textarea. */
;(function() {
  jQuery(document).ready(function($){
    var timer;
    $('#wuc-contact-form textarea').on('focus mouseenter', function(){
      $(this).css({height:'130px'});
    })
    .on('blur mouseleave', function(){
      if(!$(this).is(":focus")){
        var $this = $(this);
        clearTimeout(timer);
        /* Small delay, for a better UX. */
        timer = setTimeout(function() {
          $this.css('height','');
        }, 700);
      }
    });
  });
})(jQuery);