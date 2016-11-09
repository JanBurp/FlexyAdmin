<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />

	<title>FlexyAdmin</title>
	<base href="<?=base_url()?>" />

  <link rel="stylesheet" href="<?=admin_assets()?>css/font-awesome.min.css" type="text/css" media="screen">
  <link rel="stylesheet" href="<?=admin_assets()?>dist/flexyadmin.css" type="text/css" media="screen">
</head>

<body class="login-body bg-primary">

<div id="main">
  <div class="card" id="login-form">
		<?php if (isset($message) and !empty($message)): ?><h1 class="card-header bg-danger" style="padding-left:1.25rem;"><?=$message?></h1><?php endif ?>
    <div class="card-block">
  		<form action="<?=site_url("admin/login/check")?>" method="post" class="">
        <div class="form-group row">
          <div class="col-xs-10"><input type="text" class="form-control" id="user" name="user" placeholder="<?=lang('login_username')?>" value=""></div>
          <label for="user" class="col-xs-2 col-form-label"><span class="fa fa-user"></span></label>
        </div>
        <div class="form-group row">
          <div class="col-xs-10"><input type="password" class="form-control" id="password" name="password" placeholder="<?=lang('login_password')?>" value=""></div>
          <label for="password" class="col-xs-2 col-form-label"><span class="fa fa-lock"></span></label>
        </div>
        <button type="submit" class="btn btn-warning col-xs-12"><?=lang('login_submit')?></button>
  		</form>
    </div>
  </div>
</div>

</body>
</html>
