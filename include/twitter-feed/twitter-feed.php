<?php

require_once('twitteroauth/autoload.php'); // Path to twitteroauth library
use Abraham\TwitterOAuth\TwitterOAuth;

  /**
   * Gets connection with user Twitter account
   * @param  String $cons_key     Consumer Key
   * @param  String $cons_secret  Consumer Secret Key
   * @param  String $oauth_token  Access Token
   * @param  String $oauth_secret Access Secrete Token
   * @return Object               Twitter Session
   */
  function getConnectionWithToken($cons_key, $cons_secret, $oauth_token, $oauth_secret) {
    $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_secret);

    return $connection;
  }

  function wuc_twitter_oauth($number) {
    $output = array();
    // Check if keys are in place
    if (trim(WUC_C_TWCONSUMER_KEY) != '' && trim(WUC_C_TWCONSUMER_SECRET) != '' && trim(WUC_C_TWACCESS_TOKEN) != '' && trim(WUC_C_TWACCESS_SECRET) != ''){
      // Connect
      $connection = getConnectionWithToken(WUC_C_TWCONSUMER_KEY, WUC_C_TWCONSUMER_SECRET, WUC_C_TWACCESS_TOKEN, WUC_C_TWACCESS_SECRET);

      $content = $connection->get("statuses/home_timeline", ["count" => $number, "exclude_replies" => true]);

      if ($content) {
        if (empty($content->errors)) {
          foreach ($content as $c) {
            $output[] = array($c->text, twitter_time($c->created_at));
          }
        } else {
          $errors = $content->errors;
          foreach ($errors as $e) {
            $output[] = array($e->message, '');
          }
        }
      } else {
        $output[] = array('Cannot connect to Twitter.', '');
      }
    } else {
      $output[] = array('You need Twitter API keys. Get yours from <a href="https://dev.twitter.com/apps">dev.twitter.com/apps</a>','');
    }
    return $output;
  }

  /**
   * Converts Twitter time stamps into meaningful sentences.
   * @param  String $time  Twitter time stamp
   * @return String        The time past.
   */
  function twitter_time($time) {
    // Get difference.
    $d = strtotime("now") - strtotime($time);
    // Calculate different time values.
    $minute = 60;
    $hour = $minute * 60;
    $day = $hour * 24;
    $week = $day * 7;
    if (is_numeric($d) && $d > 0) {
      // Less than 3 seconds.
      if ($d < 3) return WUC_L_RIGHT_NOW;
      // Less than minute.
      if ($d < $minute) return sprintf(WUC_L_SECONDS_AGO, floor($d));
      // Less than 2 minutes.
      if ($d < $minute * 2) return WUC_L_ABOUT_ONE_MINUTE_AGO;
      // Less than hour.
      if ($d < $hour) return sprintf(WUC_L_MINUTES_AGO, floor($d / $minute));
      // Less than 2 hours.
      if ($d < $hour * 2) return WUC_L_ABOUT_ONE_HOUR_AGO;
      // Less than day.
      if ($d < $day) return sprintf(WUC_L_HOURS_AGO, floor($d / $hour));
      // More than day, but less than 2 days.
      if ($d > $day && $d < $day * 2) return WUC_L_YESTERDAY;
      // Mess than year.
      if ($d < $day * 365) return sprintf(WUC_L_DAYS_AGO, floor($d / $day));
      // Else return more than a year.
      return WUC_L_OVER_A_YEAR_AGO;
    }
  }