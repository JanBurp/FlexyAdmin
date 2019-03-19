# Better new password emails
UPDATE `cfg_email` SET `txt_email_nl` = '<h1>Nieuw wachtwoord aanvragen voor {identity}</h1>\n<p>&nbsp;</p>\n<p>Klik hier om <a href=\"{site_url}{forgotten_password_uri}?code={forgotten_password_code}\">wachtwoord te resetten</a>.</p>\n<p>Je krijgt na het klikken op de link een nieuwe email met daarin je nieuwe wachtwoord.</p>' WHERE `key`='login_forgot_password';
UPDATE `cfg_email` SET `txt_email_nl` = '<h1>Je nieuwe inloggevens voor {site_title}:</h1>\n<p>Gebruiker: {identity}<br />Wachtwoord: {password}</p>\n<p>Let op dat je bij het wachtwoord alle tekens meeneemt, ook eventuele punten aan het einde.</p>' WHERE `key`='login_new_password';
UPDATE `cfg_email` SET `txt_email_nl` = '<h1>Welkom bij {site_title}</h1>\n<p>Hieronder staan je inloggegevens.</p>\n<p>Gebruiker: {identity}<br />Wachtwoord: {password}</p>\n<p>Let op dat je bij het wachtwoord alle tekens meeneemt, ook eventuele punten aan het einde.</p>' WHERE `key`='login_new_account';

# Change db revision
UPDATE `cfg_version` SET `str_version` = '3.5.20';
