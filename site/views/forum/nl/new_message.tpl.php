<html>
<body>
	<h1>Een nieuw forum bericht.</h1>
	<p>In de discussie <a href="<?=$uri?>"><i>'<?=$thread?>'</i></a> is een nieuw bericht geplaatst door '<?=$user?>'.</p>
	<? if (!empty($message)): ?>
	<p><?=$message?></p>
	<? endif ?>
	<p><a href="<?=$uri?>">Ga naar het laatste bericht.</a></p>
</body>
</html>