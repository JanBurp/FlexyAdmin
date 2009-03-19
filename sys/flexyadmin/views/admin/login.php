<html>
<head>
	<title>FlexyAdmin V1 - Login</title>
	<link rel="stylesheet" href="<?=admin_assets()?>css/admin_main.css" type="text/css" />
	<base href="<?=base_url()?>" />
</head>

<body style='background-color:#EEFFBB'>

<div id="wrapper">

	<div id="dialog">
		<form action="admin/user/check_login" method="post" class="login">
			<h2>FlexyAdmin login</h2>
			<p class="str"><label for="user">User</label><input type="text" name="user" value="" id="user" class="user" /></p>
			<p class="pwd"><label for="pass">Password</label><input type="password" name="password" value="" id="password" class="password"  /></p>
			<input type="submit" name="" value="Login" submit="submit" class="button submit"  />
		</form>
	</div>

</div> <!-- wrapper -->

</body>
</html>
