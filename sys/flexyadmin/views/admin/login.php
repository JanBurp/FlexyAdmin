<!DOCTYPE html>
<html lang="<?=$lang?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />

	<title>Login</title>
	<base href="<?=base_url()?>" />

  <link rel="shortcut icon" href="about:blank">
  <link rel="stylesheet" href="<?=mix_asset('flexyadmin.css',true)?>" type="text/css" media="screen">
</head>

<body class="login-body bg-primary">

<div id="main">
  <div class="card" id="login-form">
		<h1 v-cloak v-if="message!==''" class="card-header bg-danger" style="padding-left:1.25rem;">{{message}}</h1>
    <div class="card-body">
  		<form v-if="!forgottenPasswordDialog" action="<?=site_url("_admin/login/check")?>" method="post" class="">
        <div class="form-group row">
          <div class="col-10"><input type="text" class="form-control" id="user" name="user" placeholder="<?=lang('login_username')?>" value=""></div>
          <label for="user" class="col-2 col-form-label"><span class="fa fa-user"></span></label>
        </div>
        <div class="form-group row">
          <div class="col-10"><input type="password" class="form-control" id="password" name="password" placeholder="<?=lang('login_password')?>" value=""></div>
          <label for="password" class="col-2 col-form-label"><span class="fa fa-lock"></span></label>
        </div>
        <button type="submit" class="btn btn-warning col-12"><?=lang('login_submit')?></button>
        <a v-cloak @click="showForgottenPasswordDialog(true)" id="forgotten-password-link"><?=lang('login_forgot')?></a>
  		</form>

  		<form v-cloak v-if="forgottenPasswordDialog" action="<?=site_url("_admin/login/forgot")?>" method="post">
        <div class="form-group row">
          <div class="col-10"><input type="text" class="form-control" id="email" name="email" placeholder="<?=lang('login_email')?>" value=""></div>
          <label for="email" class="col-2 col-form-label"><span class="fa fa-envelope"></span></label>
        </div>
        <button type="submit" class="btn btn-warning col-12"><?=lang('login_send_new_password')?></button>
        <a @click="showForgottenPasswordDialog(false)" id="forgotten-password-link"><?=lang('login_login')?></a>
  		</form>

    </div>
  </div>
</div>

<script type="text/javascript" charset="utf-8">
var _flexy = {
  'language'      : '<?=$lang?>',
  'message'       : '<?=isset($message)?strip_tags($message):''?>',
};
</script>
<script src="<?=mix_asset('login.build.js',true)?>" type="text/javascript" charset="utf-8"></script>

</body>
</html>
