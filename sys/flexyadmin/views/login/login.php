<!-- <h1><?=$title?></h1> -->

<p>Voor deze pagina moet u ingelogd zijn.</p>

<div class="loginErrors"><?=$errors?></div>
<div class="loginForm"><?=$form?></div>
<br />
<p>
<? if (isset($forgotten_password)): ?>
	<a href="<?=$forgotten_password_uri?>"><?=$forgotten_password?></a><br />
<? endif ?>
<? if (isset($register)): ?>
	<a href="<?=$register_uri?>"><?=$register?></a>
<? endif ?>
</p>
