<?php
/**
 * "Website Under Construction" PHP script from HTMLPIE.COM :)
 * © HTMLPIE.COM . All rights reserved.
 *
 * @file
 * Form validation and AJAX submission.
 *
 * @version 2.1
 *
 */

// Starting a new session.
if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }
} elseif(session_id() === '') {
  session_start();
}

// Loading prerequisites.
require_once(dirname(__FILE__) . '/../wuc_configuration.php');
require_once(dirname(__FILE__) . '/../wuc_configuration.validate.php');
require_once(dirname(__FILE__) . '/../wuc_language.php');
header('Content-type: application/javascript');
?>
;(function() {

  function isValidEmailAddress(email) {
    var emailReg = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i),
    valid = emailReg.test(email);
    return ((valid) ? true : false);
  }

  jQuery(document).ready(function() {

    $('#wuc-contact-form').submit(function(e) {

      var $this = $(this),
      error = new Array(),
      $email = $('#email_contact'),
      emailValue = $.trim($email.val()),
      $message = $('#message'),
      messageValue = $.trim($message.val());

      if (emailValue == '' || messageValue == '') {
        error.push("- <?php echo WUC_L_ENTER_REQUIRED_FIELDS; ?>");

        if (emailValue == '') {
          $email.addClass('field_error');
        }
        if (messageValue == '') {
          $message.addClass('field_error');
        }

      }
      if (emailValue != '' && !isValidEmailAddress(emailValue)) {
        $email.addClass('field_error');
        error.push("- <?php echo WUC_L_EMAIL_INCORRECT; ?>");
      }

      if (error.length > 0) {
        e.preventDefault();
        alert(error.join("\r\n"));
      }
      else {
        $('.field_error', $this).removeClass('field_error');
        var $load = $('<div class="please-wait clearfix" style="display:none"><div class="loading-wrapper"><div class="loading"><div class="loading1"></div><div class="loading2"></div><div class="loading3"></div></div></div><span>' + "<?php echo WUC_L_PLEASE_WAIT; ?>" + '</span></div>');
        $this.prev('.results').fadeOut().end().fadeOut('slow', function(){
          $(this).before($load).prev($load).fadeIn('slow', function(){
            $.post('HPWUC/include/contact-form/contact-form.php', $this.serialize() + '&ajax=1', function(data) {
              var d = $.parseJSON($.trim(data));
              $load.fadeOut('slow', function(){
                if (d.status) {
                  $load.before('<div class="success" style="display:none"><p>' + d.result + '</p></div>').prev('.success').fadeIn('slow');
                }
                else {
                  $this.fadeIn('fast', function() {
                    alert(d.result);
                  });
                }
              });
            });
          });
        });
        e.preventDefault();
      }
    });
  });
})(jQuery);