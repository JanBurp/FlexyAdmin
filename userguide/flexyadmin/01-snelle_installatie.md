# Snelle Installatie

- Maak een nieuwe map.
- Pak zipbestand met FlexyAdmin uit in die map.
- Maak een kopie van _public/htaccess.htaccess_ en noem deze _public/.htaccess_ (let op, dit is een onzichtbaar bestand)
- Maak een nieuwe -lege- MySQL database aan.
- Vul de database gegevens in (in _site/config/database.php_ of lokaal in _site/config/database_local.php_):

~~~{.php}
$db['local']['hostname'] = "localhost";
$db['local']['username'] = "root";
$db['local']['password'] = "root";
$db['local']['database'] = "flexyadmin_demo";
~~~

- Ga nu met je browser naar je site.
- Als alles goed is ingesteld zal FlexyAdmin zichzelf installeren en starten met de demo database.
- Mochten er database foutmeldingen komen, importeer de demo database dan handmatig ( _db/flexyadmin_demo_xxxx.sql_ )

## Inloggen / nieuwe gebruikers

- Type achter de URL `_admin` om in te loggen.
- Na installatie bestaan er twee gebruikers:
  - **admin** (admin) - Administrator, mag alles.
  - **user** (wachtwoord: user) - Gebruiker, kan alleen de inhoud aanpassen.
  
## Alternatieve installatie

In de map _scripts_ vind je scripts om de installatie in andere mappen te verdelen. Zie de help aldaar.
