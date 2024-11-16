<?php
/**
 * "Website Under Construction" PHP script from HTMLPIE.COM :)
 * © HTMLPIE.COM . All rights reserved.
 *
 * @file
 * The configuration file validation.
 *
 * @version 2.1
 *
 */

  if (!defined('WUC_C_PASSWORD') ||
     !defined('WUC_C_STYLE') ||
     !defined('WUC_C_LAUNCH_YEAR') ||
     !defined('WUC_C_LAUNCH_MONTH') ||
     !defined('WUC_C_LAUNCH_DAY') ||
     !defined('WUC_C_PROGRESSBAR') ||
     !defined('WUC_C_PROGRESSBAR_SPEED') ||
     !defined('WUC_C_PROGRESSBAR_EFFECT') ||
     !defined('WUC_C_TWITTER_HANDLE') ||
     !defined('WUC_C_TWEETS_NUMBER') ||
     !defined('WUC_C_TWCONSUMER_KEY') ||
     !defined('WUC_C_TWCONSUMER_SECRET') ||
     !defined('WUC_C_TWACCESS_TOKEN') ||
     !defined('WUC_C_TWACCESS_SECRET') ||
     !defined('WUC_C_SITE_OWNER_EMAIL') ||
     !defined('WUC_C_MAILER') ||
     !defined('WUC_C_CONTACT_AUTORESPONSE') ||
     !defined('WUC_C_CSV_FILENAME') ||
     !defined('WUC_C_SUBSCRIBE_AUTORESPONSE') ||
     !defined('WUC_C_WAI_ARIA') ||
     !defined('WUC_C_LANGUAGE_DEFAULT') ||
     !defined('WUC_C_LANGUAGE_DETECT') ||
     !defined('WUC_C_LANGUAGE_MENU') ||
     !defined('WUC_C_ENABLE_SLIDER') ||
     !defined('WUC_C_ENABLE_COUNTDOWN') ||
     !defined('WUC_C_ENABLE_PROGRESSBAR') ||
     !defined('WUC_C_ENABLE_SUBSCRIBE_FORM') ||
     !defined('WUC_C_ENABLE_CONTACT_FORM')) {
    echo 'Caution: Incomplete configuration file.';
    die();
  }

  if (trim(WUC_C_PASSWORD) == '' || WUC_C_PASSWORD == '123456789') {
    echo 'Caution: You must change the WUC_C_PASSWORD in the configuration file.';
    die();
  } elseif (strength_check(WUC_C_PASSWORD) < 5) {
    echo 'Caution: You must change the WUC_C_PASSWORD in the configuration file as it is not a strong enough password.';
    die();
  }

  if (trim(WUC_C_STYLE) == '' || !file_exists(dirname(__FILE__) .'/../css/'. WUC_C_STYLE .'.css')) {
    echo 'Caution: You must change the WUC_C_STYLE in the configuration file. The CSS file does not exist.';
    die();
  }

  if (trim(WUC_C_LAUNCH_YEAR) == '' || !is_numeric(WUC_C_LAUNCH_YEAR) || date('Y') > WUC_C_LAUNCH_YEAR) {
    echo 'Caution: You must change the WUC_C_LAUNCH_YEAR in the configuration file.';
    die();
  }

  if (trim(WUC_C_LAUNCH_MONTH) == '' || !is_numeric(WUC_C_LAUNCH_MONTH) ||
      (WUC_C_LAUNCH_MONTH < 1 || WUC_C_LAUNCH_MONTH > 12) ||
      (date('Y') == WUC_C_LAUNCH_YEAR && date('n') > WUC_C_LAUNCH_MONTH)) {
    echo 'Caution: You must change the WUC_C_LAUNCH_MONTH in the configuration file.';
    die();
  }

  if (trim(WUC_C_LAUNCH_DAY) == '' || !is_numeric(WUC_C_LAUNCH_DAY) ||
      (WUC_C_LAUNCH_DAY < 1 || WUC_C_LAUNCH_DAY > 31) ||
      (date('Y') == WUC_C_LAUNCH_YEAR && date('n') == WUC_C_LAUNCH_MONTH &&  date('j') > WUC_C_LAUNCH_DAY) ||
      !checkdate(WUC_C_LAUNCH_MONTH, WUC_C_LAUNCH_DAY, WUC_C_LAUNCH_YEAR)) {
    echo 'Caution: You must change your launch date in the configuration file.';
    die();
  }

  if (trim(WUC_C_PROGRESSBAR) == '' || !is_numeric(WUC_C_PROGRESSBAR) ||
      (WUC_C_PROGRESSBAR <= 0 || WUC_C_PROGRESSBAR > 100)) {
    echo 'Caution: You must change the WUC_C_PROGRESSBAR in the configuration file.';
    die();
  }

  if (trim(WUC_C_PROGRESSBAR_SPEED) == '' || !is_numeric(WUC_C_PROGRESSBAR_SPEED) || WUC_C_PROGRESSBAR_SPEED <= 0) {
    echo 'Caution: You must change the WUC_C_PROGRESSBAR_SPEED in the configuration file.';
    die();
  }

  if (trim(WUC_C_PROGRESSBAR_EFFECT) == '') {
    echo 'Caution: You must change the WUC_C_PROGRESSBAR_EFFECT in the configuration file.';
    die();
  }

  if (trim(WUC_C_TWEETS_NUMBER) != '' && !is_numeric(WUC_C_TWEETS_NUMBER) || WUC_C_TWEETS_NUMBER <= 0) {
    echo 'Caution: You must change the WUC_C_TWEETS_NUMBER in the configuration file.';
    die();
  }

  if (trim(WUC_C_SITE_OWNER_EMAIL) == 'mail@example.com' || !filter_var(WUC_C_SITE_OWNER_EMAIL, FILTER_VALIDATE_EMAIL)) {
    echo 'Caution: You must change the WUC_C_SITE_OWNER_EMAIL in the configuration file.';
    die();
  }

  if (WUC_C_MAILER == '' || (WUC_C_MAILER != 'mail' && WUC_C_MAILER != 'swiftmailer' && WUC_C_MAILER != 'swiftmailer_smtp')) {
    echo 'Caution: You must change the WUC_C_MAILER in the configuration file.';
    die();
  }

  if (WUC_C_MAILER == 'swiftmailer_smtp') {
    if (!defined('WUC_C_SMTP_SERVER') || trim(WUC_C_SMTP_SERVER) == '' || WUC_C_SMTP_SERVER == 'smtp.example.com' ||
        !defined('WUC_C_SMTP_PORT') || !is_numeric(WUC_C_SMTP_PORT) || WUC_C_SMTP_PORT <= 0 ||
        !defined('WUC_C_SMTP_USERNAME') || trim(WUC_C_SMTP_USERNAME) == '' || WUC_C_SMTP_USERNAME == 'username' ||
        !defined('WUC_C_SMTP_PASSWORD') || trim(WUC_C_SMTP_PASSWORD) == '' || WUC_C_SMTP_PASSWORD == 'password' ||
        !defined('WUC_C_SMTP_SSL') || !is_bool(WUC_C_SMTP_SSL)) {
      echo 'Caution: You must add your SMTP settings in the configuration file.';
      die();
    }
  }

  if (!is_bool(WUC_C_CONTACT_AUTORESPONSE)) {
    echo 'Caution: You must change the WUC_C_CONTACT_AUTORESPONSE in the configuration file.';
    die();
  }

  if (trim(WUC_C_CSV_FILENAME) == '' || WUC_C_CSV_FILENAME == '123456789') {
    echo 'Caution: You must change the WUC_C_CSV_FILENAME in the configuration file.';
    die();
  } else {
    preg_match_all ("/[^A-Z^a-z^0-9^_^.]/", trim(WUC_C_CSV_FILENAME), $matches);
    if (!empty($matches[0])) {
      echo 'Caution: You must change the WUC_C_CSV_FILENAME in the configuration file. The file name can only have letters, numbers, and underscores.';
      die();
    }
  }

  if (!is_bool(WUC_C_SUBSCRIBE_AUTORESPONSE)) {
    echo 'Caution: You must change the WUC_C_SUBSCRIBE_AUTORESPONSE in the configuration file.';
    die();
  }

  if (!is_bool(WUC_C_WAI_ARIA)) {
    echo 'Caution: You must change the WUC_C_WAI_ARIA in the configuration file.';
    die();
  }

  if (is_numeric(WUC_C_LANGUAGE_DEFAULT) || strpos(WUC_C_LANGUAGE_DEFAULT, ' ') !== FALSE) {
    echo 'Caution: You must change the WUC_C_LANGUAGE_DEFAULT in the configuration file.';
    die();
  }

  if (!is_bool(WUC_C_LANGUAGE_DETECT)) {
    echo 'Caution: You must change the WUC_C_LANGUAGE_DETECT in the configuration file.';
    die();
  }

  if (!is_bool(WUC_C_LANGUAGE_MENU)) {
    echo 'Caution: You must change the WUC_C_LANGUAGE_MENU in the configuration file.';
    die();
  }

  if (!is_bool(WUC_C_ENABLE_SLIDER)) {
    echo 'Caution: You must change the WUC_C_ENABLE_SLIDER in the configuration file.';
    die();
  }

  if (!is_bool(WUC_C_ENABLE_COUNTDOWN)) {
    echo 'Caution: You must change the WUC_C_ENABLE_COUNTDOWN in the configuration file.';
    die();
  }

  if (!is_bool(WUC_C_ENABLE_PROGRESSBAR)) {
    echo 'Caution: You must change the WUC_C_ENABLE_PROGRESSBAR in the configuration file.';
    die();
  }

  if (!is_bool(WUC_C_ENABLE_TWITTER_FEED)) {
    echo 'Caution: You must change the WUC_C_ENABLE_TWITTER_FEED in the configuration file.';
    die();
  }

  if (!is_bool(WUC_C_ENABLE_SUBSCRIBE_FORM)) {
    echo 'Caution: You must change the WUC_C_ENABLE_SUBSCRIBE_FORM in the configuration file.';
    die();
  }

  if (!is_bool(WUC_C_ENABLE_CONTACT_FORM)) {
    echo 'Caution: You must change the WUC_C_ENABLE_CONTACT_FORM in the configuration file.';
    die();
  }

  /**
   * Some rather "novice" users may prefer to remove! folders in order
   * to make some parts disappear and so on, therefore we will do all these in
   * order to make sure everything is fail-safe.
   */
  $current_directory = dirname(__FILE__);
  if (file_exists($current_directory . '/../javascript/jquery.unslider/jquery.event.move.min.js') && file_exists($current_directory . '/../javascript/jquery.unslider/jquery.event.swipe.min.js') && file_exists($current_directory . '/../javascript/jquery.unslider/unslider.js')) {
    define('WUC_C_EXISTS_SLIDER',         TRUE);
  }
  else {
    define('WUC_C_EXISTS_SLIDER',         FALSE);
  }

  if (file_exists($current_directory . '/../javascript/jquery.countdown/jquery.plugin.min.js') && file_exists($current_directory . '/../javascript/jquery.countdown/jquery.countdown.min.js')) {
    define('WUC_C_EXISTS_COUNTDOWN',      TRUE);
  }
  else {
    define('WUC_C_EXISTS_COUNTDOWN',      FALSE);
  }

  if (file_exists($current_directory . '/../javascript/jquery.progressbar/jquery.progressbar.min.js')) {
    define('WUC_C_EXISTS_PROGRESSBAR',    TRUE);
  }
  else {
    define('WUC_C_EXISTS_PROGRESSBAR',    FALSE);
  }

  if (file_exists($current_directory . '/twitter-feed/twitter-feed.php')) {
    define('WUC_C_EXISTS_TWITTER_FEED',   TRUE);
  }
  else {
    define('WUC_C_EXISTS_TWITTER_FEED',   FALSE);
  }

  if (file_exists($current_directory . '/subscribe-form/subscribe-form.php') && file_exists($current_directory . '/subscribe-form/subscribe-form.js.php')) {
    define('WUC_C_EXISTS_SUBSCRIBE_FORM', TRUE);
  }
  else {
    define('WUC_C_EXISTS_SUBSCRIBE_FORM', FALSE);
  }

  if (file_exists($current_directory . '/contact-form/contact-form.php') && file_exists($current_directory . '/contact-form/contact-form.js.php')) {
    define('WUC_C_EXISTS_CONTACT_FORM',   TRUE);
  }
  else {
    define('WUC_C_EXISTS_CONTACT_FORM',   FALSE);
  }

  /**
   * Tests password strength.
   * @param string $password The password!
   * @return int
   */
  function strength_check($password) {
    if (strlen($password) < 7 || (strtolower($password) == $password || strtoupper($password) == $password)) {
      return 1;
    }
    $strength = 0;
    if (strtolower($password) != $password || strtoupper($password) != $password) {
      $strength += 1;
    }
    if (strlen($password) >= 10 && strlen($password) <= 20) {
      $strength += 2;
    }
    preg_match_all('/[0-9]/', $password, $numbers);
    $strength += count($numbers[0]);
    preg_match_all('/[|!@#$%&*\/=?,;.:\-_+~^\\\]/', $password, $specialcharacters);
    $strength += sizeof($specialcharacters[0]);
    $strength += sizeof(array_unique(str_split($password))) * 3;
    $strength = floor(($strength > 99 ? 99 : $strength) / 10 + 1);
    return $strength;
  }
