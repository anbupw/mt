<?php
/**
 * "Website Under Construction" PHP script from HTMLPIE.COM :)
 * © HTMLPIE.COM . All rights reserved.
 *
 * @file
 * The main file.
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

// Loading perquisites.
require_once('HPWUC/include/wuc_configuration.php');
require_once('HPWUC/include/wuc_configuration.validate.php');
require_once('HPWUC/include/wuc_language.php');

// Redirects to control panel.
if (isset($_GET['cp']) && $_GET['cp'] == '1') {
  require_once('HPWUC/include/wuc_administration.php');
  exit;
}
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="ie6 no-js" lang="<?php echo wuc_language(); ?>"><![endif]-->
<!--[if IE 7 ]>    <html class="ie7 no-js" lang="<?php echo wuc_language(); ?>"><![endif]-->
<!--[if IE 8 ]>    <html class="ie8 no-js" lang="<?php echo wuc_language(); ?>"><![endif]-->
<!--[if IE 9 ]>    <html class="ie9 no-js" lang="<?php echo wuc_language(); ?>"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html class="no-js" lang="<?php echo wuc_language(); ?>"> <!--<![endif]-->
  <head>
    <title><?php echo WUC_L_SITE_TITLE; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="Description" content="<?php echo substr(WUC_L_META_DESCRIPTION, 0, 70) .'...'; ?>">
    <link type="image/x-icon" rel="shortcut icon" href="HPWUC/favicon.ico">
    <!--[if IE 8 ]>
    <script type="text/javascript" src="HPWUC/javascript/modernizr-custom.min.js"></script>
    <![endif]-->
    <link type="text/css" rel="stylesheet" href="HPWUC/css/main.css" media="all">
    <link type="text/css" rel="stylesheet" href="HPWUC/css/<?php echo WUC_C_STYLE; ?>.css" media="all">
  </head>
  <body>

<?php
      $language_list = wuc_language_list('code');
      if (!empty($language_list) && WUC_C_LANGUAGE_MENU === TRUE) {
?>
    <div id="language_switch" class="clearfix">
      <?php echo $language_list; ?>
    </div>
<?php } ?>

    <div id="wrapper">
      <header id="header" class="clearfix">
        <div class="middle clearfix">
<?php if (WUC_C_ENABLE_SLIDER === TRUE && WUC_C_EXISTS_SLIDER === TRUE) { ?>
          <div class="unslider clearfix">
            <ul>
              <li><a href="<?php echo $_SERVER['REQUEST_URI']; ?>"><?php echo WUC_L_SLIDE_1; ?></a></li>
              <li><a href="<?php echo $_SERVER['REQUEST_URI']; ?>"><?php echo WUC_L_SLIDE_2; ?></a></li>
              <li><a href="<?php echo $_SERVER['REQUEST_URI']; ?>"><?php echo WUC_L_SLIDE_3; ?></a></li>
            </ul>
          </div>
<?php } else { ?>
          <h1><a href="<?php echo $_SERVER['REQUEST_URI']; ?>"><?php echo WUC_L_SLIDE_1; ?></a></h1>
<?php } ?>
        </div>
      </header>

      <article id="main" class="clearfix">
        <h2 class="element_hidden"><?php echo WUC_L_SITE_TITLE; ?></h2>

        <div class="middle clearfix">

<?php if (WUC_C_ENABLE_COUNTDOWN === TRUE && WUC_C_EXISTS_COUNTDOWN === TRUE) { ?>
          <section id="countdown" class="clearfix">
            <h2><?php echo WUC_L_COMING_SOON; ?></h2>
            <div class="countdown"></div>
<?php
            // Adding a static <noscript> version.
            $launch = strtotime(date("jS F, Y", strtotime(WUC_C_LAUNCH_DAY .'.'. WUC_C_LAUNCH_MONTH .'.'. WUC_C_LAUNCH_YEAR)));
            $days = floor(($launch - time()) / 86400);
?>
            <noscript>
              <div class="countdown is-countdown"><span class="countdown-row countdown-show4"><span class="countdown-section"><span class="countdown-amount"><?php echo $days; ?></span><span class="countdown-period"><?php echo WUC_L_DAYS; ?></span></span></span></div>
            </noscript>
          </section>
<?php } ?>

<?php if (WUC_C_ENABLE_PROGRESSBAR === TRUE && WUC_C_EXISTS_PROGRESSBAR === TRUE) { ?>
          <section id="progressbar" class="clearfix">
            <h2 class="element_hidden"><?php echo WUC_L_PROGRESS; ?></h2>
            <div class="progressbar">
              <?php $progress = (strpos(WUC_C_PROGRESSBAR, '%') !== FALSE) ? WUC_C_PROGRESSBAR : WUC_C_PROGRESSBAR . '%'; ?>
              <div class="progress" style="width:<?php echo $progress; ?>;"><span class="percent"><?php echo $progress; ?></span></div>
            </div>
          </section>
<?php } ?>

        </div>
      </article>

<?php if ((WUC_C_ENABLE_TWITTER_FEED === TRUE && WUC_C_EXISTS_TWITTER_FEED === TRUE) ||
          (WUC_C_ENABLE_SUBSCRIBE_FORM === TRUE && WUC_C_EXISTS_SUBSCRIBE_FORM === TRUE) ||
          (WUC_C_ENABLE_CONTACT_FORM === TRUE && WUC_C_EXISTS_CONTACT_FORM == TRUE)) {
?>
      <aside id="bottom" class="clearfix">
        <div class="middle clearfix">
<?php
          /**
           * Yes! "=== TRUE" is very highly unnecessary but it makes the code
           * more "legible" for novice users.
           */
          if (WUC_C_ENABLE_TWITTER_FEED === TRUE && WUC_C_EXISTS_TWITTER_FEED === TRUE) { ?>
          <section class="block twitter clearfix">
            <h2><?php echo WUC_L_TWITTER; ?></h2>
<?php
              require_once('HPWUC/include/twitter-feed/twitter-feed.php');
              $feed = wuc_twitter_oauth(10);
?>
            <ul class="tweets clearfix">
<?php
              if (is_array($feed)) {
                foreach ($feed as $f) {
?>
              <li><?php echo $f[0]; ?><span class="date"><?php echo $f[1]; ?></span></li>
<?php } } ?>
            </ul>
            <a href="http://twitter.com/<?php echo WUC_C_TWITTER_HANDLE; ?>" class="button"><?php echo WUC_L_FOLLOW; ?> @<?php echo WUC_C_TWITTER_HANDLE; ?></a>
          </section>
<?php } ?>

<?php if (WUC_C_ENABLE_SUBSCRIBE_FORM === TRUE && WUC_C_EXISTS_SUBSCRIBE_FORM === TRUE) { ?>
          <section class="block subscribe clearfix">
            <h2><?php echo WUC_L_SUBSCRIBE; ?></h2>
            <?php require_once('HPWUC/include/subscribe-form/subscribe-form.php'); ?>
          </section>
<?php } ?>

<?php if (WUC_C_ENABLE_CONTACT_FORM === TRUE && WUC_C_EXISTS_CONTACT_FORM === TRUE) { ?>
          <section class="block contact clearfix">
            <h2><?php echo WUC_L_CONTACT_US; ?></h2>
            <?php require_once('HPWUC/include/contact-form/contact-form.php'); ?>
          </section>
<?php } ?>

        </div>
      </aside>
<?php } ?>

      <footer id="footer" class="clearfix">
        <div class="middle clearfix">
          <p><?php echo WUC_L_FOOTER_MESSAGE; ?></p>
        </div>
      </footer>

    </div>

    <script type="text/javascript" src="HPWUC/javascript/jquery.min.js"></script>
    <script type="text/javascript" src="HPWUC/javascript/modernizr-custom.min.js"></script>
    <!--[if IE 8 ]>
    <script type="text/javascript" src="HPWUC/javascript/selectivizr-min.js"></script>
    <![endif]-->
    <script type="text/javascript" src="HPWUC/javascript/package.js.php"></script>
  </body>
</html>
