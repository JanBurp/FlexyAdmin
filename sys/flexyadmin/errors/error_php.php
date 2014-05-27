<div style="border:1px solid #696;margin:4px;padding:10px;color:#000;">
<?php if (IS_LOCALHOST): ?>
	<h4 style="font-weight:bold;font-size: 14px;color: #696;">A PHP Error was encountered</h4>
	Severity:	<?php echo $severity; ?><br>
	Message:	<?php echo $message; ?><br>
	Filename:	<?php echo $filepath; ?><br>
	Line:		<?php echo $line; ?><br>
	<?php if ($severity!="Warning") { backtrace_(3); } ?>
<?php else : ?>
  <?php
	  $to="error@flexyadmin.com";
    if (defined("ERROR_EMAIL")) $to=ERROR_EMAIL;
  ?>
  <h4 style="font-weight:bold;font-size: 14px;color: #696;">Sorry, an error (<?php echo $severity?>) has been encoutered</h4>
  <p>A mail is sent to "<?=$to?>" with all necessary error information.</p>
  <?php
    $subject="FlexyAdmin ERROR: ".$_SERVER['HTTP_HOST'];
    $body="FLEXYADMIN ERROR FROM: '".$_SERVER['HTTP_HOST']."' \n\n";
    $body.="Severity:\t".$severity."\n";
    $body.="Message:\t".$message."\n";
    $body.="File:\t\t".$filepath."\n";
    $body.="Line:\t\t".$line."\n";
    $body.="\n\nBACKTRACE:\n".print_r(backtrace_(3,10,false),true);
    $body.="\n\nSERVER:\n".print_r($_SERVER,true);
    mail($to,$subject,$body);
  ?>
<?php endif ?>
</div>


