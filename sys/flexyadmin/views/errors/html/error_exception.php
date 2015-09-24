<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div style="border:1px solid #696;margin:4px;padding:10px;color:#000;background-color:#FFF;opacity:.8;">
<?php if (ENVIRONMENT=='development' or ENVIRONMENT=='testing'): ?>
	<h4 style="font-weight:bold;font-size: 14px;color: #696;">An uncaught Exception was encountered</h4>
  Type: <?php echo get_class($exception); ?><br>
  Message: <strong><?php echo $message; ?></strong><br>
  Filename: <?php echo $exception->getFile(); ?><br>
  Line Number: <?php echo $exception->getLine(); ?><br>
  <?php if (function_exists('backtrace_')) { backtrace_(); } ?>
<?php else : ?>
  <?php
	  $to="error@flexyadmin.com";
    if (defined("ERROR_EMAIL")) $to=ERROR_EMAIL;
  ?>
  <h4 style="font-weight:bold;font-size: 14px;color: #696;">Sorry, an uncaught Exception was encountered</h4>
  <p>A mail is sent to "<?=$to?>" with all necessary error information.</p>
  <?php
    $subject="FlexyAdmin ERROR: ".$_SERVER['HTTP_HOST'];
    $body="FLEXYADMIN ERROR FROM: '".$_SERVER['HTTP_HOST']."' \n\n";
    $body.="Type:\t".get_class($exception)."\n";
    $body.="Message:\t".$message."\n";
    $body.="Filename:\t".$exception->getFile()."\n";
    $body.="Line Number:\t".$exception->getLine()."\n";
    if (function_exists('backtrace_')) $body.="\n\nBACKTRACE:\n".print_r(backtrace_(3,10,false),true);
    $body.="\n\nSERVER:\n".print_r($_SERVER,true);
    mail($to,$subject,$body);
  ?>
<?php endif ?>
</div>