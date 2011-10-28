<html>
<body>
	<h1>Reset Password for <?=$user?></h1>
	<p>Please click this link to <?php echo anchor('auth/reset_password?code='. $forgotten_password_code, 'Reset Your Password');?>.</p>
</body>
</html>