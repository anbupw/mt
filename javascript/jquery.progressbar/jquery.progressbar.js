/**
 * A very simple progress bar plugin for jQuery.
 * Made by HTMLPIE.COM for the "Website Under Construction" PHP script :)
 *
 * Free to use under the MIT license.
 * http://opensource.org/licenses/mit-license.php
 *
 * @version 1.0
 *
 */

;(function($){
  $.fn.progressbar = function(options){
    options = $.extend({
      progress: '90%',
      speed: 5000,
      easing: 'swing'
    }, options);
    for (var a = 0; a < this.length; a++){
      var $this = $(this).eq(a),
      timer;
      $this.find('.progress').each(function(){
        $(this).css({width:0}).animate({width: options.progress},{
          queue: false,
          duration: options.speed,
          easing: options.easing,
          step: function(now, fx){
            if (fx.prop == 'width'){
              var progress = parseInt(Math.round(now * 100) / 100) + '%',
              $digit = $(this).children('.percent');
              progressClass = 'percent-' + progress.replace('%','');
              $digit.text(progress).attr('class', 'percent ' + progressClass);
              $digit.parent().attr('class', 'progress ' + progressClass);
            }
          }
        });
      });
    }
  }
})(jQuery);