<?php
/**
 * "Website Under Construction" PHP script from HTMLPIE.COM :)
 * © HTMLPIE.COM . All rights reserved.
 *
 * @file
 * Administration back-end.
 *
 * @version 2.1
 *
 */

  // Prevents direct access.
  if (count(get_included_files()) == 1) {
    exit('Direct access is not allowed!');
  }

  /**
   * Simple sanitization function.
   * @param string $input The input.
   * @return string The sanitized data.
   */
  function wuc_sanitize($input) {
    return htmlentities(str_replace("'", "\\'", trim(strip_tags(stripslashes($input)))), ENT_NOQUOTES, "UTF-8");
  }

  // Just some empty arrays.
  $problematic = $result = array();

  // Log out.
  if (isset($_GET['logout']) && $_GET['logout'] == substr(md5(__FILE__ . date('Y-m-d')), 0, 10) && isset($_SESSION['wuc_login']) && $_SESSION['wuc_login'] == md5(__FILE__ . date('Y-m-d'))) {
    unset($_SESSION['wuc_login']);
  }

  // Log in.
  if (!isset($_SESSION['wuc_login']) || $_SESSION['wuc_login'] != md5(__FILE__ . date('Y-m-d'))) {
    if (isset($_POST['password'])){
      if (empty($_POST['password'])){
        $result[] = 'Enter your password please.';
        $problematic[] = 'password';
        $show_data = true;
      } elseif (isset($_SESSION['wuc_login_attempt'])) {

        $latest_attempt = $_SESSION['wuc_login_attempt'][0];
        $num_fail = $_SESSION['wuc_login_attempt'][1];
        $time_limit = ($num_fail > 7) ? (30 * 60) : (2 * 60);
        $time_passed = time() - $latest_attempt;
        if ($num_fail > 4 && $time_passed < $time_limit) {
          switch ($time_limit) {
            case (2 * 60):
              $result[] = 'Please wait for 2 minutes before trying again.';
              break;
            case (30 * 60):
              $result[] = 'Please wait for 30 minutes before trying again.';
              break;
          }
        }
      }

      if (empty($result)){
        if ($_POST['password'] != WUC_C_PASSWORD || !empty($_POST['username'])) {
          $result[] = 'Incorrect password.';
          $problematic[] = 'password';
          $show_data = true;
          $attempt = (isset($_SESSION['wuc_login_attempt'])) ? (int)$_SESSION['wuc_login_attempt'][1] + 1 : 1;
          $_SESSION['wuc_login_attempt'] = array(time(), $attempt);
        } else {
          $_SESSION['wuc_login'] = md5(__FILE__ . date('Y-m-d'));
          if (isset($_SESSION['wuc_login_attempt'])){
            unset($_SESSION['wuc_login_attempt']);
          }
        }
      }
    }
  }

  // Loading the configuration.
  if (isset($_SESSION['wuc_login']) && $_SESSION['wuc_login'] == md5(__FILE__ . date('Y-m-d'))) {
    // Copying each constant as a string in order to be able to override it later on.
    $wuc_c_style = WUC_C_STYLE;
    $wuc_c_launch_year = WUC_C_LAUNCH_YEAR;
    $wuc_c_launch_month = WUC_C_LAUNCH_MONTH;
    $wuc_c_launch_day = WUC_C_LAUNCH_DAY;
    $wuc_c_progressbar = WUC_C_PROGRESSBAR;
    $wuc_c_progressbar_speed = WUC_C_PROGRESSBAR_SPEED;
    $wuc_c_slider_speed = WUC_C_SLIDER_SPEED;
    $wuc_c_progressbar_effect = WUC_C_PROGRESSBAR_EFFECT;
    $wuc_c_slider_effect = WUC_C_SLIDER_EFFECT;
    $wuc_c_twitter_handle = WUC_C_TWITTER_HANDLE;
    $wuc_c_tweets_number = WUC_C_TWEETS_NUMBER;
    $wuc_c_twconsumer_key = WUC_C_TWCONSUMER_KEY;
    $wuc_c_twconsumer_secret = WUC_C_TWCONSUMER_SECRET;
    $wuc_c_twaccess_token = WUC_C_TWACCESS_TOKEN;
    $wuc_c_twaccess_secret = WUC_C_TWACCESS_SECRET;
    $wuc_c_site_owner_email = WUC_C_SITE_OWNER_EMAIL;
    $wuc_c_mailer = WUC_C_MAILER;
    $wuc_c_smtp_server = WUC_C_SMTP_SERVER;
    $wuc_c_smtp_port = WUC_C_SMTP_PORT;
    $wuc_c_smtp_username = WUC_C_SMTP_USERNAME;
    $wuc_c_smtp_password = WUC_C_SMTP_PASSWORD;
    $wuc_c_smtp_ssl = WUC_C_SMTP_SSL;
    $wuc_c_contact_autoresponse = WUC_C_CONTACT_AUTORESPONSE;
    $wuc_c_csv_filename = WUC_C_CSV_FILENAME;
    $wuc_c_subscribe_autoresponse = WUC_C_SUBSCRIBE_AUTORESPONSE;
    $wuc_c_wai_aria = WUC_C_WAI_ARIA;
    $wuc_c_language_default = WUC_C_LANGUAGE_DEFAULT;
    $wuc_c_language_detect = WUC_C_LANGUAGE_DETECT;
    $wuc_c_language_menu = WUC_C_LANGUAGE_MENU;
    $wuc_c_enable_slider = WUC_C_ENABLE_SLIDER;
    $wuc_c_enable_countdown = WUC_C_ENABLE_COUNTDOWN;
    $wuc_c_enable_progressbar = WUC_C_ENABLE_PROGRESSBAR;
    $wuc_c_enable_twitter_feed = WUC_C_ENABLE_TWITTER_FEED;
    $wuc_c_enable_subscribe_form = WUC_C_ENABLE_SUBSCRIBE_FORM;
    $wuc_c_enable_contact_form = WUC_C_ENABLE_CONTACT_FORM;

    // Just a list of available easing effects.
    $effects = array('swing','easeInQuad','easeOutQuad','easeInOutQuad','easeInCubic','easeOutCubic','easeInOutCubic','easeInQuart','easeOutQuart','easeInOutQuart','easeInQuint','easeOutQuint','easeInOutQuint','easeInSine','easeOutSine','easeInOutSine','easeInExpo','easeOutExpo','easeInOutExpo','easeInCirc','easeOutCirc','easeInOutCirc','easeInElastic','easeOutElastic','easeInOutElastic','easeInBack','easeOutBack','easeInOutBack','easeInBounce','easeOutBounce','easeInOutBounce');

    // Tells the form whether submitted data should be remembered.
    $show_data = FALSE;

    // Form submission.
    if (isset($_POST['config_submit'])) {

      // Validation.
      if (
      (trim($_POST['launch_year']) == '' || !is_numeric($_POST['launch_year']) || date('Y') > $_POST['launch_year']) ||
      (trim($_POST['launch_month']) == '' || !is_numeric($_POST['launch_month']) || ($_POST['launch_month'] < 1 || $_POST['launch_month'] > 12) || (date('Y') == $_POST['launch_year'] && date('n') > $_POST['launch_month'])) ||
      (trim($_POST['launch_day']) ==  '' || !is_numeric($_POST['launch_day']) || ($_POST['launch_day'] < 1 || $_POST['launch_day'] > 31) || (date('Y') == $_POST['launch_year'] && date('n') == $_POST['launch_month'] &&  date('j') > $_POST['launch_day'])) ||
      !checkdate($_POST['launch_month'], $_POST['launch_day'], $_POST['launch_year'])
      ) {
        $result[] = 'Please provide an appropriate launch date.';
        $problematic[] = 'launch_date';
      }

      if (trim($_POST['progressbar']) == '' || !is_numeric($_POST['progressbar']) || ($_POST['progressbar'] < 0 || $_POST['progressbar'] > 100)) {
        $result[] = 'Incorrect progress percentage.';
        $problematic[] = 'progressbar';
      }

      if (trim($_POST['progressbar_speed']) == '' || !is_numeric($_POST['progressbar_speed'])) {
        $result[] = 'Incorrect progress bar animation speed.';
        $problematic[] = 'progressbar_speed';
      }

      if (trim($_POST['progressbar_effect']) == '' || !in_array($_POST['progressbar_effect'], $effects)) {
        $result[] = 'Incorrect progress bar animation effect.';
        $problematic[] = 'progressbar_effect';
      }

      if (trim($_POST['slider_speed']) == '' || !is_numeric($_POST['slider_speed'])) {
        $result[] = 'Incorrect slideshow animation speed.';
        $problematic[] = 'slider_speed';
      }

      if (trim($_POST['slider_effect']) == '' || !in_array($_POST['slider_effect'], $effects)) {
        $result[] = 'Incorrect slideshow animation effect.';
        $problematic[] = 'slider_effect';
      }

      if (isset($_POST['enable_twitter_feed']) && trim($_POST['twitter_handle']) != '' && $_POST['twitter_handle'] != 'twitter' && (trim($_POST['twconsumer_key']) == '' || trim($_POST['twconsumer_secret']) == '' || trim($_POST['twaccess_token']) == '' || trim($_POST['twaccess_secret']) == '')) {
        $result[] = 'All Twiter API keys are required. You can get them from <a href="https://dev.twitter.com/apps">https://dev.twitter.com/apps</a>.';
        $problematic[] = 'twitter_keys';
      }

      if (!is_numeric($_POST['tweets_number'])) {
        $result[] = 'Incorrect number of tweets.';
        $problematic[] = 'tweets_number';
      }

      if (!filter_var($_POST['site_owner_email'], FILTER_VALIDATE_EMAIL) || trim($_POST['site_owner_email']) == '') {
        $result[] = 'Incorrect e-mail address.';
        $problematic[] = 'site_owner_email';
      }

      if (empty($_POST['mailer']) || ($_POST['mailer'] != 'mail' && $_POST['mailer'] != 'swiftmailer' && $_POST['mailer'] != 'swiftmailer_smtp')) {
        $result[] = 'Wrong mailer function.';
        $problematic[] = 'mailer';
      }

      if ($_POST['mailer'] == 'swiftmailer_smtp') {
        if (trim($_POST['smtp_server']) == '' || $_POST['smtp_server'] == 'smtp.example.com' ||
            !is_numeric($_POST['smtp_port']) || trim($_POST['smtp_port']) == '' ||
            trim($_POST['smtp_username']) == '' || $_POST['smtp_username'] == 'username' ||
            trim($_POST['smtp_password']) == '' || $_POST['smtp_password'] == 'password') {
          $result[] = 'All SMTP connection settings are required.';
          $problematic[] = 'smtp_settings';
        }
      }

      if (trim($_POST['csv_filename']) == '' || $_POST['csv_filename'] == '123456789') {
        $result[] = 'Please provide a name for the CSV file.';
        $problematic[] = 'csv_filename';
      } else {
        // Only letters, numbers, and underscores are allowed.
        preg_match_all ("/[^A-Z^a-z^0-9^_^.]/", trim($_POST['csv_filename']), $matches);
        if (!empty($matches[0])) {
          $result[] = 'CSV file name can only have letters, numbers, and underscores.';
          $problematic[] = 'csv_filename';
        }
      }

      if (!file_exists(dirname(__FILE__) .'/../css/'. $_POST['style'] .'.css')) {
        $result[] = 'File does not exist: /HPWUC/css/'. $_POST['style'] .'.css';
        $problematic[] = 'style';
      }

      // Turn show_data on if there is an error.
      $show_data = (!empty($problematic)) ? TRUE : FALSE;

      // Otherwise update the configuration file.
      if (empty($result)) {

        // Creating a new configuration file using a huge string!
        $new_configuration = "<?php
/**
* \"Website Under Construction\" PHP script from HTMLPIE.COM :)
* © HTMLPIE.COM . All rights reserved.
*
* @file
* Configuration file.
*
* @saved ". date('Y-m-d') ."
*
* @version 2.1
*
*/

/**
 * Control panel password.
 * A strong password contains letters, numbers, and special characters (~!@#...)
 */
 define('WUC_C_PASSWORD',                '". WUC_C_PASSWORD ."');

/**
 * Contact form.
 */
define('WUC_C_SITE_OWNER_EMAIL',         '". (isset($_POST['site_owner_email']) ? wuc_sanitize($_POST['site_owner_email']) : 'mail@example.com') ."');

/**
 * You MUST choose a hard-to-guess word for the CSV file, otherwise your
 * visitors might be able to DOWNLOAD THE CSV FILE!
 * Example: a2Bo7QlRrZ0q1fc5
 * Please note this is supposed to be a file name hence only letters and numbers
 * should be used.
 */
define('WUC_C_CSV_FILENAME',             '". (isset($_POST['csv_filename']) ? wuc_sanitize($_POST['csv_filename']) : substr(md5(date('Y-m-d')), 0, 10)) ."');

/**
 * Style name.
 * You can choose between any of the 5 styles this script has or use them to
 * create your own style.
 * Styles should be uploaded in /HPWUC/css
 * Example: style5
 * Default: style1
 */
 define('WUC_C_STYLE',                   '". (isset($_POST['style']) ? wuc_sanitize($_POST['style']) : 'style1') ."');

/**
 * Countdown timer.
 */
define('WUC_C_LAUNCH_YEAR',              ". (isset($_POST['launch_year']) ? wuc_sanitize($_POST['launch_year']) : 2017) ."); /* 2017 */
define('WUC_C_LAUNCH_MONTH',             ". (isset($_POST['launch_month']) ? wuc_sanitize($_POST['launch_month']) : 12) .");   /* 1 to 12 */
define('WUC_C_LAUNCH_DAY',               ". (isset($_POST['launch_day']) ? wuc_sanitize($_POST['launch_day']) : 1) .");   /* 1 to 31 */

/**
 * Progress bar size.
 */
define('WUC_C_PROGRESSBAR',              ". (isset($_POST['progressbar']) ? wuc_sanitize($_POST['progressbar']) : 90)."); /* 0 to 100 */

/**
 * Animation speed for the progress bar and the header slider.
 * in milliseconds (i.e. 4000 = 4 seconds)
 *
 */
define('WUC_C_PROGRESSBAR_SPEED',        ". (isset($_POST['progressbar_speed']) ? wuc_sanitize($_POST['progressbar_speed']) : 4000) ."); /* Default: 4000 */
define('WUC_C_SLIDER_SPEED',             ". (isset($_POST['slider_speed']) ? wuc_sanitize($_POST['slider_speed']) : 500) .");  /* Default: 500 */

/**
 * Animation effects for the progress bar and the header slider.
 * Available effects:
 *   swing, easeInQuad, easeOutQuad,
 *   easeInOutQuad, easeInCubic, easeOutCubic, easeInOutCubic, easeInQuart,
 *   easeOutQuart, easeInOutQuart, easeInQuint, easeOutQuint, easeInOutQuint,
 *   easeInSine, easeOutSine, easeInOutSine, easeInExpo, easeOutExpo,
 *   easeInOutExpo, easeInCirc, easeOutCirc, easeInOutCirc, easeInElastic,
 *   easeOutElastic, easeInOutElastic, easeInBack, easeOutBack, easeInOutBack,
 *   easeInBounce, easeOutBounce, easeInOutBounce
 *
 */
define('WUC_C_PROGRESSBAR_EFFECT',       '". (isset($_POST['progressbar_effect']) ? wuc_sanitize($_POST['progressbar_effect']) : 'easeOutBounce') ."'); /* Default: easeOutBounce */
define('WUC_C_SLIDER_EFFECT',            '". (isset($_POST['slider_effect']) ? wuc_sanitize($_POST['slider_effect']) : 'swing') ."'); /* Default: swing */

/**
 * Twitter feed settings.
 */
define('WUC_C_TWITTER_HANDLE',           '". (isset($_POST['twitter_handle']) ? wuc_sanitize($_POST['twitter_handle']) : 'twitter') ."');
define('WUC_C_TWEETS_NUMBER',            ". (isset($_POST['tweets_number']) ? wuc_sanitize($_POST['tweets_number']) : 10) .");
define('WUC_C_TWCONSUMER_KEY',           '". $_POST['twconsumer_key'] ."');
define('WUC_C_TWCONSUMER_SECRET',        '". $_POST['twconsumer_secret'] ."');
define('WUC_C_TWACCESS_TOKEN',           '". $_POST['twaccess_token'] ."');
define('WUC_C_TWACCESS_SECRET',          '". $_POST['twaccess_secret'] ."');

/**
 * Mail function.
 * Options: mail | swiftmailer | swiftmailer_smtp
 * Default: swiftmailer_smtp
 */
define('WUC_C_MAILER',                    '". (isset($_POST['mailer']) ? wuc_sanitize($_POST['mailer']) : 'swiftmailer') ."');

/**
 * SMTP settings.
 */
define('WUC_C_SMTP_SERVER',               '". (isset($_POST['smtp_server']) ? wuc_sanitize( $_POST['smtp_server']) : 'smtp.example.com') ."');
define('WUC_C_SMTP_PORT',                 ". (isset($_POST['smtp_port']) ? wuc_sanitize($_POST['smtp_port']) : 25) .");
define('WUC_C_SMTP_USERNAME',             '". (isset($_POST['smtp_username']) ? wuc_sanitize($_POST['smtp_username']) : 'username') ."');
define('WUC_C_SMTP_PASSWORD',             '". (isset($_POST['smtp_password']) ? wuc_sanitize($_POST['smtp_password']) : 'password') ."');
define('WUC_C_SMTP_SSL',                  ". (isset($_POST['smtp_ssl']) ? 'TRUE' : 'FALSE') .");

/**
 * Should the sender receive an automatic response?
 * Please refer to the language file if you need to change the automatic response
 * email subject and body.
 *
 * Options: TRUE FALSE
 * Default: FALSE
 */
define('WUC_C_CONTACT_AUTORESPONSE',      ". (isset($_POST['contact_autoresponse']) ? 'TRUE' : 'FALSE') .");

/**
 * Should the newsletter subscribers receive an automatic response?
 * Please refer to the language file if you need to change the automatic response
 * email subject and body.
 *
 * Options: TRUE FALSE
 * Default: FALSE
 */
define('WUC_C_SUBSCRIBE_AUTORESPONSE',    ". (isset($_POST['subscribe_autoresponse']) ? 'TRUE' : 'FALSE') .");

/**
 * Adds WAI-ARIA roles to the form fields.
 * Please note that not every DTD is suitable for WAI-ARIA, the best being
 * probably <!doctype html>
 * more information: http://www.w3.org/TR/wai-aria/appendices#xhtml_dtd
 */
define('WUC_C_WAI_ARIA',                  ". (isset($_POST['waiaria']) ? 'TRUE' : 'FALSE') .");

/**
 * UI language.
 * You can create your own language file and put it at HPWUC/language
 * Please also look at the HPWUC/javascript/jquery.countdown/ directory for
 * available translations.
 */
define('WUC_C_LANGUAGE_DEFAULT',         '". (isset($_POST['language_default']) ? wuc_sanitize($_POST['language_default']) : '') ."');
define('WUC_C_LANGUAGE_DETECT',          ". (isset($_POST['language_detect']) ? 'TRUE' : 'FALSE') .");
define('WUC_C_LANGUAGE_MENU',            ". (isset($_POST['language_menu']) ? 'TRUE' : 'FALSE') .");

/**
 * Do NOT change unless you are sure what you are doing.
 */
define('WUC_C_ENABLE_SLIDER',            ". (isset($_POST['enable_slider']) ? 'TRUE' : 'FALSE') .");
define('WUC_C_ENABLE_COUNTDOWN',         ". (isset($_POST['enable_countdown']) ? 'TRUE' : 'FALSE') .");
define('WUC_C_ENABLE_PROGRESSBAR',       ". (isset($_POST['enable_progressbar']) ? 'TRUE' : 'FALSE') .");
define('WUC_C_ENABLE_TWITTER_FEED',      ". (isset($_POST['enable_twitter_feed']) ? 'TRUE' : 'FALSE') .");
define('WUC_C_ENABLE_SUBSCRIBE_FORM',    ". (isset($_POST['enable_subscribe_form']) ? 'TRUE' : 'FALSE') .");
define('WUC_C_ENABLE_CONTACT_FORM',      ". (isset($_POST['enable_contact_form']) ? 'TRUE' : 'FALSE') .");";

        // Saving the new configuration file.
        $open = fopen('HPWUC/include/wuc_configuration.php', 'w');
        $write = fwrite($open, $new_configuration);
        fclose($open);

        // Saved successfully?
        if ($write) {
          // Updating the strings, so the form would know the new configuration
          // without the user being redirected to the same page, or refreshing the page.
          $wuc_c_launch_year = wuc_sanitize($_POST['launch_year']);
          $wuc_c_launch_month = wuc_sanitize($_POST['launch_month']);
          $wuc_c_launch_day = wuc_sanitize($_POST['launch_day']);
          $wuc_c_progressbar = wuc_sanitize($_POST['progressbar']);
          $wuc_c_progressbar_speed = wuc_sanitize($_POST['progressbar_speed']);
          $wuc_c_slider_speed = wuc_sanitize($_POST['slider_speed']);
          $wuc_c_progressbar_effect = wuc_sanitize($_POST['progressbar_effect']);
          $wuc_c_slider_effect = wuc_sanitize($_POST['slider_effect']);
          $wuc_c_twitter_handle = wuc_sanitize($_POST['twitter_handle']);
          $wuc_c_tweets_number = wuc_sanitize($_POST['tweets_number']);
          $wuc_c_twconsumer_key = wuc_sanitize($_POST['twconsumer_key']);
          $wuc_c_twconsumer_secret = wuc_sanitize($_POST['twconsumer_secret']);
          $wuc_c_twaccess_token = wuc_sanitize($_POST['twaccess_token']);
          $wuc_c_twaccess_secret = wuc_sanitize($_POST['twaccess_secret']);
          $wuc_c_site_owner_email = filter_var(trim(strip_tags(stripslashes($_POST['site_owner_email']))), FILTER_SANITIZE_EMAIL);
          $wuc_c_mailer = wuc_sanitize($_POST['mailer']);
          $wuc_c_smtp_server = wuc_sanitize($_POST['smtp_server']);
          $wuc_c_smtp_port = wuc_sanitize($_POST['smtp_port']);
          $wuc_c_smtp_username = wuc_sanitize($_POST['smtp_username']);
          $wuc_c_smtp_password = wuc_sanitize($_POST['smtp_password']);
          $wuc_c_smtp_ssl = isset($_POST['smtp_ssl']) ? TRUE : FALSE;
          $wuc_c_contact_autoresponse = isset($_POST['contact_autoresponse']) ? TRUE : FALSE;
          $wuc_c_csv_filename = wuc_sanitize($_POST['csv_filename']);
          $wuc_c_subscribe_autoresponse = isset($_POST['subscribe_autoresponse']) ? TRUE : FALSE;
          $wuc_c_wai_aria = isset($_POST['waiaria']) ? TRUE : FALSE;
          $wuc_c_language_default = wuc_sanitize($_POST['language_default']);
          $wuc_c_language_detect = isset($_POST['language_detect']) ? TRUE : FALSE;
          $wuc_c_language_menu = isset($_POST['language_menu']) ? TRUE : FALSE;
          $wuc_c_enable_slider = isset($_POST['enable_slider']) ? TRUE : FALSE;
          $wuc_c_enable_countdown = isset($_POST['enable_countdown']) ? TRUE : FALSE;
          $wuc_c_enable_progressbar = isset($_POST['enable_progressbar']) ? TRUE : FALSE;
          $wuc_c_enable_twitter_feed = isset($_POST['enable_twitter_feed']) ? TRUE : FALSE;
          $wuc_c_enable_subscribe_form = isset($_POST['enable_subscribe_form']) ? TRUE : FALSE;
          $wuc_c_enable_contact_form = isset($_POST['enable_contact_form']) ? TRUE : FALSE;
          $wuc_c_style = wuc_sanitize($_POST['style']);

          $result[] = 'Configuration saved successfully.';
        }  else {
          $result[] = 'Configuration file cannot be updated.';
          $show_data = true;
        }
      }
    }
  }

?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="ie6 no-js" lang="en"><![endif]-->
<!--[if IE 7 ]>    <html class="ie7 no-js" lang="en"><![endif]-->
<!--[if IE 8 ]>    <html class="ie8 no-js" lang="en"><![endif]-->
<!--[if IE 9 ]>    <html class="ie9 no-js" lang="en"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
  <head>
    <title>Website Under Construction | Administration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link type="image/x-icon" rel="shortcut icon" href="HPWUC/favicon.ico">
    <script type="text/javascript" src="HPWUC/javascript/jquery-1.12.3.min.js"></script>
    <script type="text/javascript" src="HPWUC/javascript/modernizr.custom-2.7.1.min.js"></script>
    <style type="text/css">
      <!--
      /*! normalize.css v4.1.1 | MIT License | git.io/normalize */html{font-family:sans-serif;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}body{margin:0}article,aside,details,figcaption,figure,footer,header,main,menu,nav,section,summary{display:block}audio,canvas,progress,video{display:inline-block}audio:not([controls]){display:none;height:0}progress{vertical-align:baseline}template,[hidden]{display:none}a{background-color:transparent;-webkit-text-decoration-skip:objects}a:active,a:hover{outline-width:0}abbr[title]{border-bottom:none;text-decoration:underline;text-decoration:underline dotted}b,strong{font-weight:inherit;font-weight:bolder}dfn{font-style:italic}h1{font-size:2em;margin:.67em 0}mark{background-color:#ff0;color:#000}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sub{bottom:-.25em}sup{top:-.5em}img{border-style:none}svg:not(:root){overflow:hidden}code,kbd,pre,samp{font-family:monospace,monospace;font-size:1em}figure{margin:1em 40px}hr{box-sizing:content-box;height:0;overflow:visible}button,input,select,textarea{font:inherit;margin:0}optgroup{font-weight:700}button,input{overflow:visible}button,select{text-transform:none}button,html [type="button"],[type="reset"],[type="submit"]{-webkit-appearance:button}button::-moz-focus-inner,[type="button"]::-moz-focus-inner,[type="reset"]::-moz-focus-inner,[type="submit"]::-moz-focus-inner{border-style:none;padding:0}button:-moz-focusring,[type="button"]:-moz-focusring,[type="reset"]:-moz-focusring,[type="submit"]:-moz-focusring{outline:1px dotted ButtonText}fieldset{border:1px solid silver;margin:0 2px;padding:.35em .625em .75em}legend{box-sizing:border-box;color:inherit;display:table;max-width:100%;padding:0;white-space:normal}textarea{overflow:auto}[type="checkbox"],[type="radio"]{box-sizing:border-box;padding:0}[type="number"]::-webkit-inner-spin-button,[type="number"]::-webkit-outer-spin-button{height:auto}[type="search"]{-webkit-appearance:textfield;outline-offset:-2px}[type="search"]::-webkit-search-cancel-button,[type="search"]::-webkit-search-decoration{-webkit-appearance:none}::-webkit-input-placeholder{color:inherit;opacity:.54}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}
      /*! end of normalize.css */
      *, *:before, *:after{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box:}
      .clearfix:after{content:".";display:block;height:0;clear:both;visibility:hidden}
      * html .clearfix{height:1%}
      *:first-child + html .clearfix{min-height:1%}
      html{height:100%;-webkit-font-smoothing:antialiased}
      body{background-attachment:fixed;background-color:#005a7e;background-position:top center;background-repeat:no-repeat;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;color:#fff;cursor:default;font:normal 14px Arial, Helvetica, Sans-serif;margin:0;padding:0}
      @media only screen and (min-width:1920px){body{background-image:url('../image/background_teal.png')}}
      @media only screen and (min-width:1500px) and (max-width:1920px){body{background-image:url('../image/background_teal_1920.png')}      }
      @media only screen and (min-width:1080px) and (max-width:1500px){body{background-image:url('../image/background_teal_1500.png')}      }
      @media only screen and (min-width:720px) and (max-width:1080px){body{background-image:url('../image/background_teal_1080.png')}      }
      @media only screen and (max-width:720px){body{background-image:url('../image/background_teal_720.png')}}
      #wrapper{height:100%;margin:0 auto;text-shadow:0 1px 0 rgba(0,0,0,0.35);width:95%}
      @media (min-width:767px){#wrapper{width:700px}body.login #wrapper{width:430px}}
      #header{color:#fff;float:left;padding:10px 0 10px 25px;text-shadow:0 2px 0 rgba(0,0,0,0.5);width:100%}
      #header h1{float:left}
      #header .button{background:transparent;border:0 none;border-bottom-width:1px;border-bottom-style:dotted;float:right;margin:30px 10px 0 10px;padding:0 0 1px}
      @media (max-width:767px){
      body{font-size:1.142em}
      #header{margin:0 0 20px;padding:10px 0;text-align:center}
      #header h1,#header .button{width:100%}
      label{margin-left:0;margin-right:0}
      label,input,select,textarea{width:100%}
      input[type="checkbox"]{width:auto}
      }
      h1{font-size:1em;text-transform:uppercase}
      h1 span{display:block;font-size:2.143em}
      h1 a{color:#fff;text-decoration:none}
      .result{background:rgba(255,255,255,0.2);border:1px solid #fff;border:1px solid rgba(255,255,255,0.5);float:left;font-weight:bold;width:100%}
      .result ul{list-style:square;margin:0 0 0 10px;padding:20px}
      .result ul li{margin:0 0 5px}
      .result ul li:last-child{margin-bottom:0}
      .result a,.result a:visited{border-bottom:1px dotted #fff;color:#fff;text-decoration:none}
      .result.error,form .field_error{border:1px solid #ffe000}
      fieldset{background:transparent;background:rgba(255,255,255,0.03);border:1px solid #fff;border:1px solid rgba(255,255,255,0.5);float:left;margin:10px 0;padding:10px;display:inline;width:100%}
      fieldset:last-child{margin-bottom:0}
      fieldset legend{background:rgba(255,255,255,0.2);border:0 none;color:#fff;font-weight:bold;margin:0;padding:4px 10px;text-transform:uppercase}
      input,textarea,a.button{background:transparent;border:1px solid #fff;border:1px solid rgba(255,255,255,0.5);text-decoration:none;text-shadow:0 1px 0 rgba(0,0,0,0.35)}
      input,textarea,select,a.button{color:rgba(255,255,255,0.9);padding:1px 2px;-webkit-transition:border-color .2s, color .2s;-moz-transition:border-color .2s, color .2s;-ms-transition:border-color .2s, color .2s;-o-transition:border-color .2s, color .2s;transition:border-color .2s, color .2s}
      select option{background-color:#005a7e}
      input,select,textarea,input:focus,select:focus,textarea:focus{outline:0 !important}
      input:focus,input:hover,input:active,textarea:focus,textarea:hover,textarea:active,select:focus,select:hover,select:active,a.button:focus,a.button:hover,a.button:active{border:1px solid rgba(255,255,255,0.8);color:#fff}
      form .row{clear:both;float:left;margin:7px 0;width:100%}
      label{cursor:pointer;float:left;font-weight:bold;padding:1px 10px}
      select{background:transparent;border:1px solid #fff;outline:0 none}
      @media (max-width:767px){input[type="text"],textarea{display:block}}
      input.button,a.button{background:rgba(255,255,255,0.2);color:#fff;color:rgba(255,255,255,0.9);font-weight:bold;-webkit-transition:background .2s;-moz-transition:background .2s;-ms-transition:background .2s;-o-transition:background .2s;transition:background .2s}
      input.button:focus,input.button:hover,input.button:active,a.button:focus,a.button:hover,a.button:active{background:rgba(255,255,255,0.3);color:#fff;}
      #wuc_administration input.button{display:block;margin:40px 0;padding:10px 20px}
      #wuc_login fieldset{padding-bottom:100px;padding-top:70px}
      #wuc_login label{display:block;margin:0 15%;padding:0;width:70%}
      #wuc_login input{margin:10px 15% 0;padding:5px;width:70%}
      @media (max-width:767px){#wuc_login label,#wuc_login input{margin-left:0;margin-right:0;width:100%}}
      /-->
    </style>
  </head>
  <body<?php if (!isset($_SESSION['wuc_login']) || $_SESSION['wuc_login'] != md5(__FILE__ . date('Y-m-d'))) { echo ' class="login"'; } ?>>
    <div id="wrapper">
      <div id="header" class="clearfix">
        <h1><a href="<?php echo $_SERVER['PHP_SELF']; ?>?cp=1">Website Under Construction<span>Control Panel</span></a></h1>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" target="_blank" class="button">View Website</a>
        <?php if (isset($_SESSION['wuc_login']) && $_SESSION['wuc_login'] == md5(__FILE__ . date('Y-m-d'))) { ?><a href="<?php echo $_SERVER['PHP_SELF'] .'?cp=1&amp;logout='. substr(md5(__FILE__ . date('Y-m-d')), 0, 10); ?>" class="button">Logout</a><?php } ?>
      </div>
      <?php if (!empty($result)) { ?>
      <div class="result<?php echo ($show_data) ? ' error' : ''; ?> clearfix">
        <ul>
        <?php foreach ($result as $result) { ?>
          <li><?php echo $result; ?></li>
        <?php } ?>
        </ul>
      </div>
      <?php } ?>
      <?php if (isset($_SESSION['wuc_login']) && $_SESSION['wuc_login'] == md5(__FILE__ . date('Y-m-d'))) { ?>
      <form id="wuc_administration" action="<?php echo $_SERVER['PHP_SELF']; ?>?cp=1" method="post">

        <fieldset>
          <legend>Countdown</legend>
          <div class="row">
            <label for="enable_countdown">
              <input type="checkbox" name="enable_countdown" id="enable_countdown" value="true"<?php if ($show_data && isset($_POST['enable_countdown'])) { echo ' checked="checked"'; } elseif ($wuc_c_enable_countdown) { echo ' checked="checked"'; } ?>>
              Enable jQuery Countdown plugin
            </label>
          </div>
          <div class="row">
            <label for="launch_year">Launch date</label>
            <?php $year = ($show_data) ? $_POST['launch_year'] : $wuc_c_launch_year; ?>
            <select name="launch_year" id="launch_year"<?php if (in_array('launch_date', $problematic)) { echo ' class="field_error"'; } ?>>
            <?php
              $current_year = date('Y');
              for ($y = 0; $y < 5; $y++) { ?>
              <option value="<?php echo $current_year + $y; ?>"<?php if ($year == $current_year + $y) { echo ' selected="selected"'; } ?>><?php echo $current_year + $y; ?></option>
            <?php } ?>
            </select>

            <?php $month = ($show_data) ? $_POST['launch_month']: $wuc_c_launch_month; ?>
            <select name="launch_month" id="launch_month"<?php if (in_array('launch_date', $problematic)) { echo ' class="field_error"'; } ?>>
              <option value="1"<?php if ($month ==  1) { echo ' selected="selected"'; } ?>>January</option>
              <option value="2"<?php if ($month ==  2) { echo ' selected="selected"'; } ?>>February</option>
              <option value="3"<?php if ($month ==  3) { echo ' selected="selected"'; } ?>>March</option>
              <option value="4"<?php if ($month ==  4) { echo ' selected="selected"'; } ?>>April</option>
              <option value="5"<?php if ($month ==  5) { echo ' selected="selected"'; } ?>>May</option>
              <option value="6"<?php if ($month ==  6) { echo ' selected="selected"'; } ?>>June</option>
              <option value="7"<?php if ($month ==  7) { echo ' selected="selected"'; } ?>>July</option>
              <option value="8"<?php if ($month ==  8) { echo ' selected="selected"'; } ?>>August</option>
              <option value="9"<?php if ($month ==  9) { echo ' selected="selected"'; } ?>>September</option>
              <option value="10"<?php if ($month == 10) { echo ' selected="selected"'; } ?>>October</option>
              <option value="11"<?php if ($month == 11) { echo ' selected="selected"'; } ?>>November</option>
              <option value="12"<?php if ($month == 12) { echo ' selected="selected"'; } ?>>December</option>
            </select>

            <?php $day = ($show_data) ? $_POST['launch_day'] : $wuc_c_launch_day; ?>
            <select name="launch_day" id="launch_day"<?php if (in_array('launch_date', $problematic)) { echo ' class="field_error"'; } ?>>
            <?php for ($i = 1; $i <= 31; $i++) { ?>
                <option value="<?php echo $i; ?>"<?php if ($day == $i) { echo ' selected="selected"'; } ?>><?php echo $i; ?></option>
            <?php } ?>
            </select>
          </div>
        </fieldset>

        <fieldset>
          <legend>Progress bar</legend>
          <div class="row">
            <label for="enable_progressbar">
              <input type="checkbox" name="enable_progressbar" id="enable_progressbar" value="true"
              <?php if ($show_data && isset($_POST['enable_progressbar'])) { echo ' checked="checked"'; } elseif ($wuc_c_enable_progressbar) { echo ' checked="checked"'; } ?>>
              Enable jQuery Progress Bar plugin
            </label>
          </div>
          <div class="row">
            <label for="progressbar">Progress (Default: 90)</label>
            <input type="text" name="progressbar" id="progressbar"<?php if (in_array('progressbar', $problematic)) { echo ' class="field_error"'; } ?> size="2" maxlength="3" value="<?php if ($show_data) { echo $_POST['progressbar']; } else { echo $wuc_c_progressbar; } ?>"> <span class="hint">%</span>
          </div>
          <div class="row">
            <label for="progressbar_speed">Progress bar animation speed (Default: 4000)</label>
            <input type="text" name="progressbar_speed" id="progressbar_speed"<?php if (in_array('progressbar_speed', $problematic)) { echo ' class="field_error"'; } ?> size="2" maxlength="7" value="<?php if ($show_data) { echo $_POST['progressbar_speed']; } else { echo $wuc_c_progressbar_speed; } ?>"> <span class="hint">milliseconds</span>
          </div>
          <div class="row">
            <label for="progressbar_effect">Progress bar animation effect (Default: easeOutBounce)</label>
            <select name="progressbar_effect" id="progressbar_effect"<?php if (in_array('progressbar_effect', $problematic)) { echo 'class="field_error"'; } ?>>
            <?php $effect = ($show_data) ? $_POST['progressbar_effect'] : $wuc_c_progressbar_effect; ?>
            <?php foreach ($effects as $e) { ?>
              <option<?php if ($effect == $e) { echo ' selected="selected"'; } ?>><?php echo $e; ?></option>
            <?php }?>
            </select>
          </div>
        </fieldset>

        <fieldset>
          <legend>Slideshow</legend>
          <div class="row">
            <label for="enable_slider">
              <input type="checkbox" name="enable_slider" id="enable_slider" value="true"
              <?php if ($show_data && isset($_POST['enable_slider'])) { echo ' checked="checked"'; } elseif ($wuc_c_enable_slider) { echo ' checked="checked"'; } ?>>
              Enable jQuery UnSlider plugin
            </label>
          </div>
          <div class="row">
            <label for="slider_speed">Slideshow speed (Default: 500)</label>
            <input type="text" name="slider_speed" id="slider_speed"<?php if (in_array('slider_speed', $problematic)) { echo 'class="field_error"'; } ?> size="2" maxlength="7" value="<?php echo ($show_data) ? $_POST['slider_speed'] : $wuc_c_slider_speed; ?>"> <span class="hint">milliseconds</span>
          </div>
          <div class="row">
            <label for="slider_effect">Slideshow animation effect (Default: swing)</label>
            <select name="slider_effect" id="slider_effect"<?php if (in_array('slider_effect', $problematic)) { echo 'class="field_error"'; } ?>>
            <?php $effect = ($show_data) ? $_POST['slider_effect'] : $wuc_c_slider_effect; ?>
            <?php foreach ($effects as $e) { ?>
              <option<?php if ($effect == $e) { echo ' selected="selected"'; } ?>><?php echo $e; ?></option>
            <?php }?>
            </select>
          </div>
        </fieldset>

        <fieldset>
          <legend>Twitter feed</legend>
          <div class="row">
            <label for="enable_twitter_feed">
              <input type="checkbox" name="enable_twitter_feed" id="enable_twitter_feed" value="true"<?php if ($show_data && isset($_POST['enable_twitter_feed'])) { echo ' checked="checked"'; } elseif ($wuc_c_enable_twitter_feed) { echo ' checked="checked"'; } ?>>
              Enable Twitter feed
            </label>
          </div>
          <div class="row">
            <label for="twitter_handle">Twitter handle</label>
            <input type="text" name="twitter_handle" id="twitter_handle"<?php if (in_array('twitter_handle', $problematic)) { echo ' class="field_error"'; } ?> value="<?php echo ($show_data) ? $_POST['twitter_handle'] : $wuc_c_twitter_handle; ?>">
          </div>
          <div class="row">
            <label for="tweets_number">Number of tweets (Default: 10)</label>
            <input type="text" name="tweets_number" id="tweets_number"<?php if (in_array('tweets_number', $problematic)) { echo ' class="field_error"'; } ?> size="2" value="<?php echo ($show_data) ? $_POST['tweets_number'] : $wuc_c_tweets_number; ?>">
          </div>
          <div class="row">
            <label for="twconsumer_key">Twitter consumer key</label>
            <input type="text" name="twconsumer_key" id="twconsumer_key"<?php if (in_array('twitter_keys', $problematic)) { echo ' class="field_error"'; } ?> size="50" value="<?php echo ($show_data) ? $_POST['twconsumer_key'] : $wuc_c_twconsumer_key; ?>">
          </div>
          <div class="row">
            <label for="twconsumer_secret">Twitter consumer secret</label>
            <input type="text" name="twconsumer_secret" id="twconsumer_secret"<?php if (in_array('twitter_keys', $problematic)) { echo ' class="field_error"'; } ?> size="50" value="<?php echo ($show_data) ? $_POST['twconsumer_secret'] : $wuc_c_twconsumer_secret; ?>">
          </div>
          <div class="row">
            <label for="twaccess_token">Twitter access token</label>
            <input type="text" name="twaccess_token" id="twaccess_token"<?php if (in_array('twitter_keys', $problematic)) { echo ' class="field_error"'; } ?> size="50" value="<?php echo ($show_data) ? $_POST['twaccess_token'] : $wuc_c_twaccess_token; ?>">
          </div>
          <div class="row">
            <label for="twaccess_secret">Twitter access secret</label>
            <input type="text" name="twaccess_secret" id="twaccess_secret"<?php if (in_array('twitter_keys', $problematic)) { echo ' class="field_error"'; } ?> size="50" value="<?php echo ($show_data) ? $_POST['twaccess_secret'] : $wuc_c_twaccess_secret; ?>">
          </div>
        </fieldset>

        <fieldset>
          <legend>Contact form</legend>
          <div class="row">
            <label for="enable_contact_form">
              <input type="checkbox" name="enable_contact_form" id="enable_contact_form" value="true"<?php if ($show_data && isset($_POST['enable_contact_form'])) { echo ' checked="checked"'; } elseif ($wuc_c_enable_contact_form) { echo ' checked="checked"'; } ?>>
              Enable contact form
            </label>
          </div>
          <div class="row">
            <label for="site_owner_email">E-mail address</label>
            <input type="text" name="site_owner_email" id="site_owner_email"<?php if (in_array('site_owner_email', $problematic)) { echo 'class="field_error"'; } ?> value="<?php echo ($show_data) ? $_POST['site_owner_email'] : $wuc_c_site_owner_email; ?>">
          </div>
          <div class="row">
            <label for="contact_autoresponse">
              <input type="checkbox" name="contact_autoresponse" id="contact_autoresponse" value="true"<?php if ($show_data && isset($_POST['contact_autoresponse'])) { echo ' checked="checked"'; } elseif ($wuc_c_contact_autoresponse) { echo ' checked="checked"'; } ?>>
              Enable automatic response for contact form
            </label>
          </div>
        </fieldset>

        <fieldset>
          <legend>Subscribe form</legend>
          <div class="row">
            <label for="enable_subscribe_form">
              <input type="checkbox" name="enable_subscribe_form" id="enable_subscribe_form" value="true"<?php if ($show_data && isset($_POST['enable_subscribe_form'])) { echo ' checked="checked"'; } elseif ($wuc_c_enable_subscribe_form) { echo ' checked="checked"'; } ?>>
              Enable subscribe form
            </label>
          </div>
          <div class="row">
            <label for="csv_filename">CSV filename</label>
            <input type="text" name="csv_filename" id="csv_filename"<?php if (in_array('csv_filename', $problematic)) { echo 'class="field_error"'; } ?> value="<?php echo ($show_data) ? $_POST['csv_filename'] : $wuc_c_csv_filename; ?>">
          </div>
          <div class="row">
            <label for="subscribe_autoresponse">
              <input type="checkbox" name="subscribe_autoresponse" id="subscribe_autoresponse" value="true"<?php if ($show_data && isset($_POST['subscribe_autoresponse'])) { echo ' checked="checked"'; } elseif ($wuc_c_subscribe_autoresponse) { echo ' checked="checked"'; } ?>>
              Enable automatic response for subscription
            </label>
          </div>
        </fieldset>

        <fieldset>
          <legend>Mail</legend>
          <div class="row">
            <label>Mailer (Default: Swiftmailer)</label>
            <?php $wuc_c_mailer = ($show_data) ? $_POST['mailer'] : $wuc_c_mailer; ?><br /><br />
            <label for="mail"><input type="radio" name="mailer" id="mail" <?php if ($wuc_c_mailer == 'mail') { echo ' checked="checked"'; } ?>value="mail" />PHP Mail()</label><br /><br />
            <label for="swiftmailer"><input type="radio" name="mailer" id="swiftmailer"<?php if ($wuc_c_mailer == 'swiftmailer') { echo ' checked="checked"'; } ?> value="swiftmailer" />SwiftMailer</label><br /><br />
            <label for="swiftmailer_smtp"><input type="radio" name="mailer" id="swiftmailer_smtp"<?php if ($wuc_c_mailer == 'swiftmailer_smtp') { echo ' checked="checked"'; } ?> value="swiftmailer_smtp" />SwiftMailer SMTP</label>
          </div>
          <fieldset>
            <legend>SMTP settings</legend>
            <div class="row">
              <label for="smtp_ssl">
                <input type="checkbox" name="smtp_ssl" id="smtp_ssl" value="true"<?php if ($show_data && isset($_POST['smtp_ssl'])) { echo ' checked="checked"'; } elseif ($wuc_c_smtp_ssl) { echo ' checked="checked"'; } ?>>
                Enable SSL
              </label>
            </div>
            <div class="row">
              <label for="smtp_server">Server</label>
              <input type="text" name="smtp_server" id="smtp_server"<?php if (in_array('smtp_settings', $problematic)) { echo 'class="field_error"'; } ?> value="<?php echo ($show_data) ? $_POST['smtp_server'] : $wuc_c_smtp_server; ?>">
            </div>
            <div class="row">
              <label for="smtp_port">Port</label>
              <input type="text" name="smtp_port" id="smtp_port"<?php if (in_array('smtp_settings', $problematic)) { echo 'class="field_error"'; } ?> value="<?php echo ($show_data) ? $_POST['smtp_port'] : $wuc_c_smtp_port; ?>">
            </div>
            <div class="row">
              <label for="smtp_username">Username</label>
              <input type="text" name="smtp_username" id="smtp_username"<?php if (in_array('smtp_settings', $problematic)) { echo 'class="field_error"'; } ?> value="<?php echo ($show_data) ? $_POST['smtp_username'] : $wuc_c_smtp_username; ?>">
            </div>
            <div class="row">
              <label for="smtp_password">Password</label>
              <input type="text" name="smtp_password" id="smtp_password"<?php if (in_array('smtp_settings', $problematic)) { echo 'class="field_error"'; } ?> value="<?php echo ($show_data) ? $_POST['smtp_password'] : $wuc_c_smtp_password; ?>">
            </div>
          </fieldset>
        </fieldset>

        <fieldset>
          <legend>UI Language</legend>
          <div class="row">
            <label for="language_menu">
              <input type="checkbox" name="language_menu" id="language_menu" value="true"<?php if ($show_data && isset($_POST['language_menu'])) { echo ' checked="checked"'; } elseif ($wuc_c_language_menu) { echo ' checked="checked"'; } ?>>
              Enable language menu
            </label>
          </div>
          <div class="row">
            <label for="language_default">Default language</label>
            <?php
              $languages = wuc_language_scan('files');
              $language = ($show_data) ? $_POST['language_default'] : $wuc_c_language_default;
            ?>
            <select name="language_default" id="language_default"<?php if (in_array('language_default', $problematic)) { echo 'class="field_error"'; } ?>>
            <?php foreach ($languages as $l) { ?>
              <option value="<?php echo $l['code']; ?>"<?php if ($l['code'] == $language) { echo ' selected="selected"'; } ?>><?php echo $l['name'] . ' (/HPWUC/language/wuc_language.'. $l['code'] .'.php)'; ?></option>
            <?php } ?>
            </select>
          </div>
          <div class="row">
            <label for="language_detect">
              <input type="checkbox" name="language_detect" id="language_detect" value="true"<?php if ($show_data && isset($_POST['language_detect'])) { echo ' checked="checked"'; } elseif ($wuc_c_language_detect) { echo ' checked="checked"'; } ?>>
              Enable automatic language detection
            </label>
          </div>
        </fieldset>

        <fieldset>
          <legend>Accessibility</legend>
          <label for="waiaria">
            <input type="checkbox" name="waiaria" id="waiaria" value="true"<?php if ($show_data && isset($_POST['wai_aria'])) { echo ' checked="checked"'; } elseif ($wuc_c_wai_aria) { echo ' checked="checked"'; } ?>>
            Add WAI-ARIA landmark roles
          </label>
        </fieldset>

        <fieldset>
          <legend>Style</legend>
          <div class="row">
            <label for="style">Style:</label>
            <?php
              $styles = array();
              $css_directory = dirname(__FILE__) .'/../css/';
              if (!file_exists($css_directory)) {
                echo 'Caution: Cannot find the CSS folder (/HPWUC/css).';
                die();
              } elseif ($handle = opendir($css_directory)) {
                while (($file = readdir($handle)) !== false) {
                  $ext = strtolower(substr($file, strrpos($file, '.') + 1));
                  if ($file != "." && $file != ".." && $file != 'main.css' && $file != 'control_panel.css' && $ext == 'css') {
                    $styles[] = str_replace('.css', '', $file);
                  }
                }
                closedir($handle);
                asort($styles);
              }
              if (empty($styles)) {
                echo '<p>The CSS folder (/HPWUC/css) does not contain any style.</p>';
              } else {
            ?>
            <select name="style" id="style"<?php echo (in_array('style', $problematic)) ? ' class="field_error"' : ''; ?>>
            <?php $style = ($show_data) ? $_POST['style'] : $wuc_c_style; ?>
            <?php foreach ($styles as $s) { ?>
              <option value="<?php echo $s; ?>"<?php if ($style == $s) { echo ' selected="selected"'; } ?>><?php echo $s; ?> (/HPWUC/css/<?php echo $s; ?>.css)</option>
            <?php
                }
              }
            ?>
            </select>
          </div>
        </fieldset>

        <div class="row">
          <input type="submit" name="config_submit" id="config_submit" class="button" value="Save Configuration">
        </div>

      </form>

      <?php } else { // If not logged in. ?>

      <form id="wuc_login" action="<?php echo $_SERVER['PHP_SELF']; ?>?cp=1" method="post">
        <fieldset>
          <legend>Login</legend>
          <label for="username" style="position:absolute;top:-9999px;">Username:</label>
          <input type="text" name="username" id="username" value="" style="position:absolute;top:-9999px;" />
          <label for="password">Password:</label>
          <input type="password" name="password" id="password"<?php if (in_array('password', $problematic)) { echo ' class="field_error"'; } ?> value="" />
          <input type="submit" name="login_submit" id="login_submit" class="button" value="Login" />
        </fieldset>
      </form>

      <?php } ?>

    </div>
  </body>
</html>