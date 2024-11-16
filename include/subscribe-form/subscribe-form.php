<?php
/**
 * "Website Under Construction" PHP script from HTMLPIE.COM :)
 * © HTMLPIE.COM . All rights reserved.
 *
 * @file
 * Subscribe form.
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

 // Loading prerequisites for AJAX output.
if (isset($_POST['ajax'])) {
  require_once('../wuc_configuration.php');
  require_once('../wuc_language.php');
}

// This is for checking whether the form should remember the data.
$show_data = FALSE;
// Generating a name for the hidden CAPTCHA field.
$hfs = md5(((isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']) . __FILE__ . date('m') . 'subscribe');
// Just a bunch of empty arrays.
$problematic = $results = array();

if (isset($_POST['email_subscribe'])) {

  // Data sanitization.
  $name = str_replace(array('"',','), '', trim(strip_tags(stripslashes($_POST['name_subscribe']))));
  $email = str_replace(array('"',','), '', filter_var(trim(strip_tags(stripslashes($_POST['email_subscribe']))), FILTER_SANITIZE_EMAIL));

  if (empty($name)) {
    $results[] = WUC_L_ENTER_REQUIRED_FIELDS;
    $problematic[] = 'name_subscribe';
    $show_data = TRUE;
  }

  if (empty($email)) {
    $results[] = WUC_L_ENTER_REQUIRED_FIELDS;
    $problematic[] = 'email_subscribe';
    $show_data = TRUE;
  }
  // Validating the e-mail address.
  else {
    list($user, $host) = explode("@", $email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || (function_exists('checkdnsrr') && !checkdnsrr($host, "MX") && !checkdnsrr($host, "A"))) {
      $results[] = WUC_L_EMAIL_INCORRECT;
      $problematic[] = 'email_subscribe';
      $show_data = TRUE;
    }
  }
  // We will check if the hidden field of the hidden CAPTCHA has been filled.
  if (isset($_POST[$hfs]) && !empty($_POST[$hfs])) {
    $results[] = WUC_L_SOMETHING_WRONG;
    $show_data = TRUE;
  }

  // Flood protection.
  if (isset($_SESSION['antiflood_subscribe'])) {
    // User has to wait for 2 minutes before sending another email,
    // and insisting on sending email will increase the delay to an hour.
    $latest_message = $_SESSION['antiflood_subscribe'][0];
    $num_fail = $_SESSION['antiflood_subscribe'][1];
    $num_success = $_SESSION['antiflood_subscribe'][2];
    $time_limit = ($num_fail > 5) ? (30 * 60) : (2 * 60);
    if ($num_success > 10) {
      $time_limit = 120 * 60;
    }
    $time_passed = time() - $latest_message;
    if ($time_passed < $time_limit) {
      $_SESSION['antiflood_subscribe'][1]++;
      switch ($time_limit) {
        case (2 * 60):
        $results[] = WUC_L_WAIT_TWO_MINUTES;
        break;
        case (30 * 60):
        $results[] = WUC_L_WAIT_THIRTY_MINUTES;
        break;
        case (120 * 60):
        $results[] = WUC_L_WAIT_TWO_HOURS;
        break;
      }
      $show_data = TRUE;
    }
  }
  // Removing duplicates, if any.
  $results = array_unique($results);

  // Everything right? Cool, preparing the CSV.
  if (empty($results)) {
    // Path to the CSV file.
    $csv_file = dirname(__FILE__) .'/../../csv/'. WUC_C_CSV_FILENAME .'.csv';
    // We will check whether the email exists in the CSV file.
    if (file_exists($csv_file)) {
      // This is sometimes required.
      ini_set('auto_detect_line_endings', TRUE);
      // Opening the CSV file.
      $handle = fopen($csv_file, 'rb');
      // Creating an array out of the CSV.
      $data = explode('"', fread($handle, filesize($csv_file)));
      fclose($handle);
      // Looking for the email address in the array.
      if (in_array($email, $data)) {
        $results[] = WUC_L_ALREADY_SUBSCRIBED;
        $show_data = TRUE;
      }
    }
    // If the $results was empty still.
    if (empty($results)) {
      // Creating the new line.
      $csv = '"'. $email .'","'. $name .'","'. date('Y-m-d H:i:s') ."\"\n";
      // Another way is to use fputcsv() here but this way (using fwrite) is a
      // little bit more flexible.
      $handle = fopen($csv_file, 'a');
      if (fwrite($handle, $csv)) {
        // Creating a session for anti-flood protection or if one already exists
        // this resets it.
        $num_sent = (isset($_SESSION['antiflood_subscribe'])) ? (int)$_SESSION['antiflood_subscribe'][2] + 1 : 1;
        $_SESSION['antiflood_subscribe'] = array(time(), 1, $num_sent);
        // Crystal clear.
        $results[] = WUC_L_SUBSCRIBE_THANKS;
        $show_data = FALSE;
        // Sending the automatic response.
        if (WUC_C_SUBSCRIBE_AUTORESPONSE) {
          // Whether SwiftMailer library should be used.
          if (strpos(WUC_C_MAILER, 'swiftmailer') !== FALSE) {
            // Loading SwiftMailer.
            require_once(dirname(__FILE__) .'/../swiftmailer/swift_required.php');
            if (WUC_C_MAILER == 'swiftmailer') {
              $transport = Swift_MailTransport::newInstance();
            }
            if (WUC_C_MAILER == 'swiftmailer_smtp') {
              $transport = Swift_SmtpTransport::newInstance()
                ->setHost(WUC_C_SMTP_SERVER)
                ->setPort(WUC_C_SMTP_PORT)
                ->setUsername(WUC_C_SMTP_USERNAME)
                ->setPassword(WUC_C_SMTP_PASSWORD);
              if (WUC_C_SMTP_SSL) {
                $transport->setEncryption('ssl');
              }
            }
            // Creating the e-mail.
            $mailer = Swift_Mailer::newInstance($transport);
            $site_owner = WUC_C_SITE_OWNER_EMAIL;
            $new_email = Swift_Message::newInstance()
              ->setContentType('text/html')
              ->setSubject(WUC_L_SUBSCRIBE_AUTORESPONSE_SUBJECT)
              ->setFrom($site_owner)
              ->setTo($email)
              ->setBody(WUC_L_SUBSCRIBE_AUTORESPONSE_BODY);
            // Sending it.
            $mailer->send($new_email);
          }
          // Or the plain PHP mail() function should be used.
          else {
            $site_owner = WUC_C_SITE_OWNER_EMAIL;
            // Creating the header.
            $header = "From: ". $site_owner ."\r\n";
            $header .= "Reply-To: ". $site_owner ."\r\n";
            $header .= "Return-Path: ". $site_owner ."\r\n";
            $header .= "MIME-Version: 1.0\r\n";
            $header .= "Content-Type: text/html; charset=UTF-8\r\n";
            $header .= "Content-Transfer-Encoding: quoted-printable\r\n";
            // These are sometimes required, depending on server configuration.
            date_default_timezone_set('UTC');
            ini_set('sendmail_from', $site_owner);
            // Sending the email.
            mail($email, $subject, $body, $headers);
          }
        }
      } else {
        $results[] = WUC_L_SOMETHING_WRONG;
        $show_data = TRUE;
      }
      fclose($handle);
    }
  }

  // Output for AJAX.
  if (isset($_POST['ajax'])) {
    if (!empty($results)) {
      if ($show_data) {
        $messages = array();
        foreach ($results as $r) {
          $messages[] = '- '. $r .".\r\n";
        }
      }
      else {
        $messages[] = implode($results);
      }
    }
    echo json_encode(array(
      'status' => ($show_data) ? FALSE : TRUE,
      'result' => implode("", $messages),
    ));
  }
}

if (!isset($_POST['ajax'])) {
  if (count($results) > 0) {
  ?>
  <div class="results clearfix">
    <ul>
    <?php foreach ($results as $r) { ?>
      <li><?php echo $r; ?></li>
    <?php } ?>
    </ul>
  </div>
  <?php } ?>
  <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="wuc-subscribe-form" class="clearfix">
    <div class="row name clearfix">
      <label for="name_subscribe"><?php echo WUC_L_YOUR_NAME; ?>: <span class="asterisk">(<?php echo WUC_L_REQUIRED; ?>)</span></label>
      <input type="text" name="name_subscribe" id="name_subscribe" size="35" value="<?php echo (($show_data == TRUE) ? $_POST['name_subscribe'] : ''); ?>"<?php echo ((WUC_C_WAI_ARIA) ? ' role="textbox" aria-required="true"' : '') . ((in_array('name_subscribe', $problematic)) ? ' class="field_error"' : ''); ?> />
    </div>
    <div class="row email clearfix">
      <label for="email_subscribe"><?php echo WUC_L_YOUR_EMAIL; ?>: <span class="asterisk">(<?php echo WUC_L_REQUIRED; ?>)</span></label>
      <input type="text" name="email_subscribe" id="email_subscribe" size="35" value="<?php echo (($show_data == TRUE) ? $_POST['email_subscribe'] : ''); ?>"<?php echo ((WUC_C_WAI_ARIA) ? ' role="textbox" aria-required="true"' : '') . ((in_array('email_subscribe', $problematic)) ? ' class="field_error"' : ''); ?> />
    </div>
    <?php // Please do not touch this part :) ?>
    <div style="border:0 none !important;clip:rect(1px 1px 1px 1px);clip:rect(1px,1px,1px,1px);height:1px !important;margin:0 !important;overflow:hidden !important;padding:0 !important;position:absolute !important;width:1px !important;">
      <label for="<?php echo $hfs; ?>">Subject: *</label>
      <input type="text" name="<?php echo $hfs; ?>" id="<?php echo $hfs; ?>" />
    </div>
    <?php // Please do not touch this part :) ?>
    <div class="row submit clearfix">
      <input type="submit" name="subscribe" class="button" value="<?php echo WUC_L_SUBSCRIBE; ?>" />
    </div>
  </form>
<?php } ?>