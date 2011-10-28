<html>
<body>
	<h1>Wachtwoord is gereset voor <?=$user?></h1>
	<p>Klik hier om <?php echo anchor('nl/reset_password?code='. $forgotten_password_code, 'wachtwoord te resetten');?>.</p>
</body>
</html>