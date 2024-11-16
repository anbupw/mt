<?php
/**
 * "Website Under Construction" PHP script from HTMLPIE.COM :)
 * © HTMLPIE.COM . All rights reserved.
 *
 * @file
 * Packing all the JS file to reduce HTTP requests.
 *
 * @version 2.1
 */

if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }
} elseif(session_id() === '') {
  session_start();
}

// Loading prerequisites.
require_once('../include/wuc_configuration.php');
require_once('../include/wuc_configuration.validate.php');
require_once('../include/wuc_language.php');
header('Content-type: application/javascript');

ob_start();

require_once('jquery.easing-1.3.min.js');

/*
 * The "=== TRUE" below are very much unnecessary! just to keep everything
 * as clear as possible for everybody.
 */
if (WUC_C_ENABLE_SLIDER === TRUE && WUC_C_EXISTS_SLIDER === TRUE) {
  require_once('jquery.unslider/jquery.event.move.min.js');
  require_once('jquery.unslider/jquery.event.swipe.min.js');
  require_once('jquery.unslider/unslider.min.js');
}

if (WUC_C_ENABLE_COUNTDOWN === TRUE && WUC_C_EXISTS_COUNTDOWN === TRUE) {
  require_once('jquery.countdown/jquery.plugin.min.js');
  require_once('jquery.countdown/jquery.countdown.min.js');

  $localised = 'jquery.countdown/jquery.countdown-'. wuc_language() .'.js';
  if (wuc_language() != 'en' && file_exists($localised)) {
    require_once($localised);
  }
}

if (WUC_C_ENABLE_PROGRESSBAR === TRUE && WUC_C_EXISTS_PROGRESSBAR === TRUE) {
  require_once('jquery.progressbar/jquery.progressbar.min.js');
}

if (WUC_C_ENABLE_SUBSCRIBE_FORM === TRUE && WUC_C_EXISTS_SUBSCRIBE_FORM === TRUE) {
  require_once('../include/subscribe-form/subscribe-form.js.php');
}

if (WUC_C_ENABLE_CONTACT_FORM === TRUE && WUC_C_EXISTS_CONTACT_FORM === TRUE) {
  require_once('../include/contact-form/contact-form.js.php');
}

if (WUC_C_ENABLE_TWITTER_FEED === TRUE && WUC_C_EXISTS_TWITTER_FEED === TRUE) {
  require_once('tweets-slider.js');
}

if (WUC_C_ENABLE_CONTACT_FORM === TRUE && WUC_C_EXISTS_CONTACT_FORM === TRUE) {
  require_once('contact-form-animation.js');
}
?>
;(function($){
<?php if (WUC_C_ENABLE_SLIDER === TRUE && WUC_C_EXISTS_SLIDER === TRUE) { ?>
  var $slider = $('#header .unslider'), $items = $('ul li', $slider), timer;
  $slider.unslider({speed:<?php echo WUC_C_SLIDER_SPEED; ?>, autoplay:true, keys:false, arrows:false, nav:false, fluid:true, easing:'<?php echo WUC_C_SLIDER_EFFECT; ?>'}).unslider('animate');
  /* Bit of tweaking that makes unslider more responsive. */
  $(window).resize(function(){
    clearTimeout(timer);
    timer = setTimeout(function(){
      $items.each(function(){
        $(this).css({width:(100 / $items.length) + '%'});
      });
    }, 50);
  });
<?php } ?>
<?php if (WUC_C_ENABLE_COUNTDOWN === TRUE && WUC_C_EXISTS_COUNTDOWN === TRUE) { ?>
$('.countdown').countdown({until: new Date(<?php echo WUC_C_LAUNCH_YEAR; ?>, <?php echo WUC_C_LAUNCH_MONTH; ?>-1, <?php echo WUC_C_LAUNCH_DAY; ?>)});
<?php } ?>
<?php if (WUC_C_ENABLE_PROGRESSBAR === TRUE && WUC_C_EXISTS_PROGRESSBAR === TRUE) { ?>
$('.progressbar').progressbar({progress: '<?php echo WUC_C_PROGRESSBAR; ?>%', speed: <?php echo WUC_C_PROGRESSBAR_SPEED; ?>, easing: '<?php echo WUC_C_PROGRESSBAR_EFFECT; ?>'});
<?php } ?>
})(jQuery);
<?php
  /**
   * Note, this file meant to save some HTTP requests not more, as most of the
   * JavaScript files we have are minimized versions already.
   */
  $js = ob_get_contents();
  ob_end_clean();
  // Removing blank lines
  $js = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", trim($js));
  // Clearing indentation.
  $trimmed = array();
  $js = explode("\n", $js);
  foreach ($js as $js) {
    $trimmed[] = trim($js);
  }
  echo implode("\n", $trimmed);