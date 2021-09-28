<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/*
 * -------------------------------------------------------------------------
 * Local Email settings for testing
 *
 * Set you're email configuration here, it will override the standard settings
 *
 * See http://codeigniter.com/user_guide/libraries/email.html
 *
 * -------------------------------------------------------------------------
 */

// Mailhog http://0.0.0.0:8025/ start `mailhog`
$config['mailtype']  = 'html';
$config['protocol']  = 'smtp';
$config['smtp_host'] = 'localhost';
$config['smtp_port'] = 1025;
$config['newline']   = "\r\n";
$config['crlf']      = "\r\n";

// SMTP catcher
// $config['mailtype'] = 'html';
// $config['newline']  = "\r\n";
// $config['protocol'] = 'sendmail';
// $config['mailpath'] = 'sudo -u jan /Users/jan/Sites/smtp_out/smtp_catcher.php';


?>
