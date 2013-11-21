Hieronder in vogelvlucht alles wat je moet weten over FlexyAdmin.

## Snelle installatie

- Maak een map voor je site.
- Pak FlexyAdmin uit in die map.
- Hernoem het bestand _htaccess.htaccess_ naar _.htaccess_
- Maak een nieuwe MySql database aan.
- Vul de juiste gegevens van je database in: _site/config/database(_local).php_.
- Ga met je browser naar de site. Als alles goed is ingesteld zal FlexyAdmin nu starten met de demo database.
- Klaar om in te loggen!

## Inloggen / nieuwe gebruikers

Standaard bestaan er twee gebruikers:

- user (wachtwoord: user) - deze kan alleen de inhoud van de site aanpassen.
- admin (admin) - deze gebruiker kan bij alle instellingen van FlexyAdmin

## HTML aanpassen

Views zijn HTML templates. Ze staan in de map: _site/views._

De belangrijkste view is _site.php_, hier komt alle inhoud samen. Je vind hier ook het HEAD deel met verwijzingen naar javascripts en stylesheets.

Lees meer over [Views in de CodeIgniter handleiding][1]. Ook goed om te lezen is de globale werking van het [MVC model][2].

## CSS Stylesheets aanpassen

Stylesheets vind je in de map: _site/assets/css._ Ze worden geladen vanuit _site/views/site.php._

Standaard stylesheets:

- _text.css_ - Deze wordt ook geladen in het admin deel en bepaald mede het uiterlijk van de tekst in de HTML editor. Hier bepaal je dan ook de standaard tekstinstellingen en een aantal standaard styles.
- _layout.css _- Hier verzorg je de rest van de styling van je site. 
- _ie9.css ... ie6.css_ - Hier kun je per versie van Internet Explorer je stylesheet overrulen om je site ook in Microsoft Internet Explorer goed te krijgen.
- _admin.css _- Hier kun je extra styling toevoegen aan het admin deel.
- _*.htc_ - Dit zijn styling helper bestanden om ouder versies van Internet Explorer mee te laten doen. Oa :hover voor ie6.

Stop eventuele [afbeeldingen][3] voor de [styling][4] in de map: _site/assets/img_

## JavaScript & jQuery gebruiken

JavaScript bestanden vind je in _site/assets/js._ Standaard vind je daar al _site.js_ wat je kunt aanpassen naar wens.

FlexyAdmin wordt standaard geïnstalleerd met jQuery en enkele jQuery plugins. 
jQuery is een bekende javascript library waarmee je browser-onafhankelijke scripts kunt maken. Voor effecten, DOM-manipulatie, AJAX etc. [Lees hier meer over jQuery][5]

NB Zorg ervoor dat in _site/views/site.php_ de javascript bestanden worden
geladen.

   [1]: http://codeigniter.com/user_guide/general/views.html

   [2]: http://codeigniter.com/user_guide/overview/mvc.html

   [3]: {Afbeeldingen-en-bestanden}

   [4]: {Frontend-controller}

   [5]: http://jquery.com/

