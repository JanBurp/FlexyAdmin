<div style="border:1px solid #696;margin:4px;padding:10px;color:#000;">
<?php if (IS_LOCALHOST): ?>
	<h4 style="font-weight:bold;font-size: 14px;color: #696;">A PHP Error was encountered</h4>
	<pre>
	Severity:	<?php echo $severity; ?>
	Message:	<?php echo $message; ?>
	Filename:	<?php echo $filepath; ?>
	Line:		<?php echo $line; ?>
	</pre>

	<?php if ($severity!="Warning") { backtrace_(3); } ?>
<?php else : ?>
  <h4 style="font-weight:bold;font-size: 14px;color: #696;">Sorry, an error (<?php echo $severity?>) has been encoutered</h4>
  <?php if ($severity!="Warning") : ?>
		<p>A mail is sent to flexyadmin.com with all necessary error information.</p>
    <?php
		$to="error@flexyadmin.com";
		$subject="FlexyAdmin ERROR: ".$_SERVER['HTTP_HOST'];
		$body="FLEXYADMIN ERROR FROM: '".$_SERVER['HTTP_HOST']."' \n\n";
		$body.="Severity:\t".$severity."\n";
		$body.="Message:\t".$message."\n";
		$body.="File:\t\t".$filepath."\n";
		$body.="Line:\t\t".$line."\n";
		$body.="\n\nBACKTRACE:\n".print_r(backtrace_(3,false),true);
		$body.="\n\nSERVER:\n".print_r($_SERVER,true);
		mail($to,$subject,$body);
    ?>
  <?php else : ?>
	  <p>Contact your webmaster.</p>
  <?php endif ?>

<?php endif ?>
</div>


