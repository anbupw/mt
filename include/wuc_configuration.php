<?php
/**
* "Website Under Construction" PHP script from HTMLPIE.COM :)
* © HTMLPIE.COM . All rights reserved.
*
* @file
* Configuration file.
*
* @version 2.1
*
*/

/**
 * Control panel password.
 * A strong password contains letters, numbers, and special characters (~!@#...)
 */
define('WUC_C_PASSWORD',                '123456789');

/**
 * Contact form.
 */
define('WUC_C_SITE_OWNER_EMAIL',         'mail@example.com');

/**
 * You MUST choose a hard-to-guess word for the CSV file, otherwise your
 * visitors might be able to DOWNLOAD THE CSV FILE!
 * Example: a2Bo7QlRrZ0q1fc5
 * Please note this is supposed to be a file name hence only letters and numbers
 * should be used.
 */
define('WUC_C_CSV_FILENAME',             '123456789');

/**
 * Style name.
 * You can choose between any of the 5 styles this script has or use them to
 * create your own style.
 * Styles should be uploaded in /HPWUC/css
 * Example: style5
 * Default: style1
 */
 define('WUC_C_STYLE',                   'style1');

/**
 * Countdown timer.
 */
define('WUC_C_LAUNCH_YEAR',              2017); /* 2017 */
define('WUC_C_LAUNCH_MONTH',             12);   /* 1 to 12 */
define('WUC_C_LAUNCH_DAY',               31);   /* 1 to 31 */

/**
 * Progress bar size.
 */
define('WUC_C_PROGRESSBAR',              90); /* 0 to 100 */

/**
 * Animation speed for the progress bar and the header slider.
 * in milliseconds (i.e. 4000 = 4 seconds)
 *
 */
define('WUC_C_PROGRESSBAR_SPEED',        4000); /* Default: 4000 */
define('WUC_C_SLIDER_SPEED',             500);  /* Default: 500 */

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
define('WUC_C_PROGRESSBAR_EFFECT',       'easeOutBounce'); /* Default: easeOutBounce */
define('WUC_C_SLIDER_EFFECT',            'swing'); /* Default: swing */

/**
 * Twitter feed settings.
 */
define('WUC_C_TWITTER_HANDLE',           'twitter');
define('WUC_C_TWEETS_NUMBER',            10);
define('WUC_C_TWCONSUMER_KEY',           '');
define('WUC_C_TWCONSUMER_SECRET',        '');
define('WUC_C_TWACCESS_TOKEN',           '');
define('WUC_C_TWACCESS_SECRET',          '');

/**
 * Mail function.
 * Options: mail | swiftmailer | swiftmailer_smtp
 * Default: swiftmailer_smtp
 */
define('WUC_C_MAILER',                    'swiftmailer');

/**
 * SMTP settings.
 */
define('WUC_C_SMTP_SERVER',               'smtp.example.com');
define('WUC_C_SMTP_PORT',                 25);
define('WUC_C_SMTP_USERNAME',             'username');
define('WUC_C_SMTP_PASSWORD',             'password');
define('WUC_C_SMTP_SSL',                  FALSE);

/**
 * Should the sender receive an automatic response?
 * Please refer to the language file if you need to change the automatic response
 * email subject and body.
 *
 * Options: TRUE FALSE
 * Default: FALSE
 */
define('WUC_C_CONTACT_AUTORESPONSE',      FALSE);

/**
 * Should the newsletter subscribers receive an automatic response?
 * Please refer to the language file if you need to change the automatic response
 * email subject and body.
 *
 * Options: TRUE FALSE
 * Default: FALSE
 */
define('WUC_C_SUBSCRIBE_AUTORESPONSE',    FALSE);

/**
 * Adds WAI-ARIA roles to the form fields.
 * Please note that not every DTD is suitable for WAI-ARIA, the best being
 * probably <!doctype html>
 * more information: http://www.w3.org/TR/wai-aria/appendices#xhtml_dtd
 */
define('WUC_C_WAI_ARIA',                  FALSE);

/**
 * UI language.
 * You can create your own language file and put it at HPWUC/language
 * Please also look at the HPWUC/javascript/jquery.countdown/ directory for
 * available translations.
 */
define('WUC_C_LANGUAGE_DEFAULT',         '');
define('WUC_C_LANGUAGE_DETECT',          TRUE);
define('WUC_C_LANGUAGE_MENU',            TRUE);

/**
 * Do NOT change unless you are sure what you are doing.
 */
define('WUC_C_ENABLE_SLIDER',            TRUE);
define('WUC_C_ENABLE_COUNTDOWN',         TRUE);
define('WUC_C_ENABLE_PROGRESSBAR',       TRUE);
define('WUC_C_ENABLE_TWITTER_FEED',      TRUE);
define('WUC_C_ENABLE_SUBSCRIBE_FORM',    TRUE);
define('WUC_C_ENABLE_CONTACT_FORM',      TRUE);