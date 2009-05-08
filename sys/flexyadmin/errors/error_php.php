<div style="border:1px solid #990000;margin:4px;padding:4px;color:#000;">

<h4>A PHP Error was encountered</h4>

<pre>
Severity:	<?php echo $severity; ?>

Message:	<?php echo $message; ?>

Filename:	<a style="color:#000;text-decoration:underline;" href="txmt://open?url=file:///<?=str_replace('errors/error_php.php','',__FILE__).$filepath ?>&amp;line=<?=$line?>"><?php echo $filepath; ?></a>
Line:		<?php echo $line; ?>

<?php if ($severity!="Warning") echo trace_(NULL,true,4); ?>
</pre>


</div>