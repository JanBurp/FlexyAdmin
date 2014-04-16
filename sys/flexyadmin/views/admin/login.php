<!DOCTYPE html>
<html>
<head>
	<title>FlexyAdmin - Login <?=$title?></title>
	<base href="<?=base_url()?>" />
	<link rel="shortcut icon" href="<?=admin_assets()?>img/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="<?=admin_assets()?>css/admin_main.css" type="text/css" />
	<!--[if lte IE 6]><style type="text/css" media="screen">@import url(<?=admin_assets()?>css/ie6.css);</style><![endif]-->
	<!--[if IE 7]><style type="text/css" media="screen">@import url(<?=admin_assets()?>css/ie7.css);</style><![endif]-->
	<!--[if IE 8]><style type="text/css" media="screen">@import url(<?=admin_assets()?>css/ie8.css);</style><![endif]-->
	<!--[if IE 9]><style type="text/css" media="screen">@import url(<?=admin_assets()?>/css/ie9.css);</style><![endif]-->
	
	<script src="sys/jquery/jquery-1.6.2.min.js" type="text/javascript" charset="utf-8"></script>
	<script>
	$(document).ready(function(){
	if ( $.browser.msie && $.browser.version<=6 ) {
		alert('LET OP: U gebruikt Internet Explorer 6 of ouder. Dit wordt niet ondersteund in FlexyAdmin. Gebruik een modernere browser zoals Firefox.\n\nWARNING: You are using Internet Explorer 6 or older. FlexyAdmin does not support them, use a modern browser such as Firefox.');
	}
	});
	</script>
	
	
	
</head>

<body style='background-color:#EEFFBB'>

<div id="wrapper">

	<div id="dialog">
		<form action="<?=site_url("admin/login/check")?>" method="post" class="login">
			<h2>FlexyAdmin login</h2>
			<?php if (isset($message) and !empty($message)): ?>
				<div class="error message"><br/><?=$message?></div>
			<?php endif ?>
			<p class="str"><label for="user">User</label><input type="text" name="user" value="" id="user" class="user" /></p>
			<p class="pwd"><label for="pass">Password</label><input type="password" name="password" value="" id="password" class="password"  /></p>
			<input type="submit" name="" value="Login" submit="submit" class="button submit"  />
		</form>
	</div>

</div> <!-- wrapper -->

</body>
</html>
