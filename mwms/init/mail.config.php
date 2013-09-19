<?php
/* Mailing */
define("_USINGSMTP_", false); // use SMTP (true) or NOT (false)
define("_SYSTEMMAIL_","noreply@domain.tld"); // reply email
define("_SMTPSERVER_","smtp.gmail.com"); // SMTP server
define("_SMTPPORT_","587"); // SMTP port 25 form SMTP, 587 gmail/tls, 465 gmail/ssl
define("_SMTPTIMEOUT_", 60); // deprecated, compat
define("_SMTPAUTH_", true); // Authenticated SMTP true/false
define("_SMTPUSER_","noreply@domain.tld"); // SMTP user
define("_SMTPPASSWORD_","some.password"); // SMTP password
define("_SMTPDELAY_", 1); // sleep delay in seconds (used with more then 1 recipient)
define("_SMTPDELAYEDSEND_", true); // used with more then 1 recipient
define("_SMTPSECURE_", 'tls'); // null, 'ssl', 'tls'
?>
