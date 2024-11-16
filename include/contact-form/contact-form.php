<?php
/**
 * "Website Under Construction" PHP script from HTMLPIE.COM :)
 * © HTMLPIE.COM . All rights reserved.
 *
 * @file
 * Contact form.
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
$hfc = md5(((isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']) . __FILE__ . date('m') . 'contact');
// Just a bunch of empty arrays.
$problematic = $results = array();

/**
 * Sends emails.
 * @param  String $from      Sender`s e-mail address.
 * @param  String $to        Recipient(s) e-mail address.
 * @param  String $subject   E-mail subject.
 * @param  String $body      E-mail body.
 * @return Boolean
 */
function send_message($from, $to, $subject, $body) {
  // Whether SwiftMailer library should be used.
  if (strpos(WUC_C_MAILER, 'swiftmailer') !== FALSE) {
    // Loading SwiftMailer.
    require_once(dirname(__FILE__) .'/../swiftmailer/swift_required.php');
    if (WUC_C_MAILER == 'swiftmailer') {
      $transport = Swift_MailTransport::newInstance();
    }
    elseif (WUC_C_MAILER == 'swiftmailer_smtp') {
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
    $new_email = Swift_Message::newInstance()
      ->setContentType('text/html')
      ->setSubject($subject)
      ->setFrom(array($from))
      ->setTo($to)
      ->setBody($body);
    // Sending it.
    $send = $mailer->send($new_email);
  }
  // Or the plain PHP mail() function.
  else {
    // Creating the header.
    $header = "From: $from\r\n";
    $header .= "Reply-To: $from\r\n";
    $header .= "Return-Path: $from\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: text/html; charset=UTF-8\r\n";
    $header .= "Content-Transfer-Encoding: quoted-printable\r\n";
    // These are sometimes required, depending on server configuration.
    date_default_timezone_set('UTC');
    ini_set('sendmail_from', $from);
    // Sending the e-mail.
    $send = mail($to, $subject, $body, $header);
  }
  return $send;
}

if (isset($_POST['email_contact'])) {

  // Sanitisation.
  $email = filter_var(trim(strip_tags(stripslashes($_POST['email_contact']))), FILTER_SANITIZE_EMAIL);
  $message = nl2br(htmlentities(trim(stripslashes($_POST['message'])), ENT_NOQUOTES, "UTF-8"));

  // Doing some validation.
  if (empty($email)) {
    $results[] = WUC_L_ENTER_REQUIRED_FIELDS;
    $problematic[] = 'email_contact';
    $show_data = TRUE;
  }
  else {
    list($user, $host) = explode("@", $email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || (function_exists('checkdnsrr') && !checkdnsrr($host, "MX") && !checkdnsrr($host, "A"))) {
      $results[] = WUC_L_EMAIL_INCORRECT;
      $problematic[] = 'email_contact';
      $show_data = TRUE;
    }
  }

  if (empty($message)) {
    $results[] = WUC_L_ENTER_REQUIRED_FIELDS;
    $problematic[] = 'message';
    $show_data = TRUE;
  }

  // We will check if the hidden field of the hidden CAPTCHA has been filled.
  if (isset($_POST[$hfc]) && !empty($_POST[$hfc])) {
    $results[] = WUC_L_SOMETHING_WRONG;
    $show_data = TRUE;
  }

  // Flood protection.
  if (isset($_SESSION['antiflood_contact'])) {
    // User has to wait for 2 minutes before sending another email,
    // and insisting on sending email will increase the delay.
    $latest_message = $_SESSION['antiflood_contact'][0];
    $num_fail = $_SESSION['antiflood_contact'][1];
    $num_success = $_SESSION['antiflood_contact'][2];
    $time_limit = ($num_fail > 5) ? (30 * 60) : (2 * 60);
    if ($num_success > 10) {
      $time_limit = 120 * 60;
    }
    $time_passed = time() - $latest_message;
    if ($time_passed < $time_limit) {
      $_SESSION['antiflood_contact'][1]++;
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

  // Everything right? Cool, preparing the email.
  if (empty($results)) {

    // Creating the body.
    $body = WUC_L_EMAIL .": ". $email ."<br><br>\n";
    $body .= WUC_L_MESSAGE .":<br><br>\n". $message ."<br><br>\n";

    // Sending.
    $success = send_message($email, WUC_C_SITE_OWNER_EMAIL, WUC_L_SUBJECT_CONTACT_FORM, $body);

    if ($success) {
      // Creating a session for anti-flood protection.
      // If one already exists this resets it.
      $num_sent = (isset($_SESSION['antiflood_contact'])) ? (int)$_SESSION['antiflood_contact'][2] + 1 : 1;
      $_SESSION['antiflood_contact'] = array(time(), 1, $num_sent);
      // Crystal clear.
      $results[] = WUC_L_CONTACT_THANKS;
      $show_data = FALSE;
      // Sending the automatic response.
      if (WUC_C_CONTACT_AUTORESPONSE) {
        send_message(WUC_C_SITE_OWNER_EMAIL, $email, WUC_L_CONTACT_AUTORESPONSE_SUBJECT, WUC_L_CONTACT_AUTORESPONSE_BODY);
      }
    } else {
      $results[] = WUC_L_SOMETHING_WRONG;
      $show_data = TRUE;
    }
  }

  // Output for AJAX.
  if (isset($_POST['ajax'])) {
    if (count($results) > 0) {
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
  if (!empty($results)) {
  ?>
  <div class="results clearfix">
    <ul>
    <?php foreach ($results as $r) { ?>
      <li><?php echo $r; ?></li>
    <?php } ?>
    </ul>
  </div>
  <?php } ?>
  <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="wuc-contact-form" class="clearfix">
    <div class="row email clearfix">
      <label for="email_contact"><?php echo WUC_L_YOUR_EMAIL; ?>: <span class="asterisk">(<?php echo WUC_L_REQUIRED; ?>)</span></label>
      <input type="text" name="email_contact" id="email_contact" size="35" value="<?php echo (($show_data == TRUE) ? $_POST['email_contact'] : ''); ?>"<?php echo ((WUC_C_WAI_ARIA) ? ' role="textbox" aria-required="true"' : '') . ((in_array('email_contact', $problematic)) ? ' class="field_error"' : ''); ?> />
    </div>
    <div class="row message clearfix">
      <label for="message"><?php echo WUC_L_YOUR_MESSAGE; ?>: <span class="asterisk">(<?php echo WUC_L_REQUIRED; ?>)</span></label>
      <textarea name="message" id="message" rows="10" cols="50"<?php echo ((WUC_C_WAI_ARIA) ? ' role="textbox" aria-required="true"' : '') . ((in_array('message', $problematic)) ? ' class="field_error"' : ''); ?>><?php echo (($show_data == TRUE) ? $_POST['message'] : ''); ?></textarea>
    </div>
    <?php // Please do not touch this part :) ?>
    <div style="border:0 none !important;clip:rect(1px 1px 1px 1px);clip:rect(1px,1px,1px,1px);height:1px !important;margin:0 !important;overflow:hidden !important;padding:0 !important;position:absolute !important;width:1px !important;">
      <label for="<?php echo $hfc; ?>">Subject: *</label>
      <input type="text" name="<?php echo $hfc; ?>" id="<?php echo $hfc; ?>" />
    </div>
    <?php // Please do not touch this part :) ?>
    <div class="row submit clearfix">
      <input type="submit" name="contact" class="button" value="<?php echo WUC_L_SEND; ?>" />
    </div>
  </form>
<?php } ?>