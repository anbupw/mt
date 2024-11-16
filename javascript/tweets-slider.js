/* A tiny simple slideshow for the Twitter feed items. */
;(function() {
  jQuery(document).ready(function($){
    if ($('ul.tweets li').length > 1) {
      $('ul.tweets li:gt(0)').hide();
      setInterval(function(){
        $('ul.tweets li:first-child').fadeOut('fast', function(){
          jQuery(this).next('li').fadeIn().end().appendTo('ul.tweets');
        });
      }, 4000);
    }
  });
})(jQuery);