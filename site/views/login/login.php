<h1><?=$title?></h1>

<p>Voor deze pagina moet u ingelogd zijn.<br />
Als u geen account hebt en u hebt kinderen op de Bussumse Montessorischool, neem dan even contact op met de school.</p>

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
