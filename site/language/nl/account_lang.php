<?php

// REGISTER
$lang['register_caption']				= 'Aanmelden';
$lang['register_intro']					= 'Vertel iets over jezelf.';
$lang['register_already']				= 'Je bent al aangemeld, %s! <a href="nl/you/logout">Log uit</a> als je je wil aanmelden met een ander email adres.'; //  $content = sprintf(lang('welcome'), $this->CI->session->userdata['str_username']);
$lang['register_already_title']	= 'Aanmelden niet nodig';
//$lang['username']							= 'Naam'; // see login
$lang['username_used']					= 'Deze naam wordt al gebruikt.';
//$lang['password']							= 'Wachtwoord'; // lee login
$lang['password2']							= 'Wachtwoord, nogmaals ter controle.';
$lang['password_mismatch']			= 'Het wachtwoord is anders dan het controle-wachtwoord. Vul 2 keer hetzelfde in.';
$lang['email']									= 'Email adres';
$lang['email_used']							= 'Dit email adres is al in gebruik.';
$lang['register_submit']				= 'Aanmelden!';
$lang['register_completed']			= 'Welkom bij de club!';

// LOGIN
$lang['username']								= 'Gebruikersnaam';
$lang['password']								= 'Wachtwoord <a class="forgot_password_link" href="nl/you/forgot_password">(vergeten?)</a>';
$lang['forgot_password']				= 'Wachtwoord vergeten.';
$lang['remember']								= 'Onthou mij.';
$lang['login_submit']						= 'Inloggen';
$lang['login_error_title']			= 'Inloggen niet gelukt.';
$lang['login_error']						= 'Verkeerde gebruikersnaam/wachtwoord combinatie.';
$lang['login_already']					= 'Je bent al ingelogd, %s!'; //  $content = sprintf(lang('welcome'), $this->CI->session->userdata['str_username']);
$lang['login_already_title']		= 'Inloggen niet nodig';
$lang['login_completed_title']	= 'Ingelogd';
$lang['login_completed']				= 'Welkom %s!'; //  $content = sprintf(lang('welcome'), $this->CI->session->userdata['str_username']);

// LOGOUT
$lang['logout_done']						= 'Je bent uitgelogd.';

// FORGOT PASSWORD
$lang['forgot_password_intro']			= 'Je bent je wachtwoord vergeten. Kan gebeuren. Vul je email in. Dan sturen wij je een mail waarmee je je wachtwoord kunt resetten.';
$lang['forgot_password_submit']			= 'Stuur email';
$lang['forgot_password_completed']	= 'Check je email om je wachtwoord te resetten.';

// RESET PASSWORD
$lang['reset_password']						= 'Wachtwoord vergeten.';
$lang['reset_password_submit']		= 'Reset wachtwoord';
$lang['reset_password_completed'] = 'Je wachtwoord is gereset. Check je mail en <a href="nl/login">login</a>';

?>