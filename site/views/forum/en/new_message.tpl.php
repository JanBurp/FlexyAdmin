<html>
<body>
	<h1>A new forum message.</h1>
	<p>In the thread <a href="<?=$uri?>"><i>'<?=$thread?>'</i></a> a new message was posted by '<?=$user?>'.</p>
	<? if (!empty($message)): ?>
	<p><?=$message?></p>
	<? endif ?>
	<p><a href="<?=$uri?>">Go to last message.</a></p>
</body>
</html>