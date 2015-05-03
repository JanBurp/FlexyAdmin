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
</head>

<body style='background-color:#EEFFBB'>

<div id="wrapper">

	<div id="dialog">
		<form action="<?=site_url("admin/login/forgot_password")?>" method="post" class="login">
			<h2>FlexyAdmin login</h2>
			<p>Type you're email address and you will get an email to reset you're password</p>
			<?php if (isset($message) and !empty($message)): ?>
				<div class="error message"><br/><?=$message?></div>
			<?php endif ?>
			<p class="str"><label for="email">Email</label><input type="text" name="email" value="" id="email" class="email" /></p>
			<input type="submit" name="" value="Send" submit="submit" class="button submit"  />
		</form>
	</div>

</div>

</body>
</html>
