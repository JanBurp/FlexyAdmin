## Technische voorwaarden

Om FlexyAdmin te kunnen installeren zijn de volgende technische randvoorwaarden nodig:

- PHP 5.5 of hoger (vanaf 5.2.4 werkt FlexyAdmin in principe ook)
- MySQL 5.1 of hoger
- Bij voorkeur een Apache server met mod_rewrite (andere servers kunnen ook, maar mogelijkerwijs met minder mooie URL's)

## Installatie aanpassen

Na de standaard installatie zijn er een aantal dingen die aangepast kunnen
worden. Zeker bij een afwijkende server kan dat nodig zijn.
Bekijk ook de map _scripts_ hiervoor.

### Localhost

FlexyAdmin kan herkennen of het zich in een lokale testomgeving bevindt of op
de productieserver. In het geval van de lokale testomgeving zal FlexyAdmin de
volgende config bestanden extra inladen. De instellingen zullen de normale
instellingen overschrijven.

- _site/config/database_local.php_ - Stel hier je lokale database gegevens in.
- _site/config/config_local.php_ - Als de server instellingen van de productiesite afwijken van de lokale testomgeving kun je hier de lokale instellingen instellen.

Voor de mogelijke instellingen zie bij [CodeIginters database instellingen][1]
en [CodeIgniter troubleshooting][2] over _config.php_.

### Afwijkende localhost

Als jouw lokale testomgeving niet wordt herkend (kun je zien als in FlexyAdmin
helemaal onderaan naast User:... geen [LOCAL] staat) dan kan het zijn dat je
je localhost moet instellen. Dat doe je in _index.php_ met de regel:

    define("LOCALHOSTS", "localhost, localhost:8888, 10.37.129.2");

Voeg je eigen localhost toe aan bovenstaande.

## Server problemen

Mocht je site niet goed reageren op de standaard uri's. Bijvoorbeeld dat welke
pagina je ook kiest hij altijd met de startpagina begint ook al zie je in de
url meer dan alleen de root. Probeer dan in _site/config.php_ of
_site/config_local.php_ enkele alternatieve instellingen uit.

Lees bij de [CodeIgniter troubleshooting][2] voor meer info. Het komt neer op
de volgende stappen:

- Probeer alternatieven voor `$config['uri_protocol']`
- Verander `$config['index_page']` in: `$config['index_page'] = "index.php";`
- Of verander `$config['index_page']` in: `$config['index_page'] = "index.php?";`

## base_url

FlexyAdmin probeert automatisch de 'base_url' in te stellen. In een enkel geval zal dat niet lukken en komt FlexyAdmin met een melding.
In dat geval moet je de volledige url in _site/config/config(_local).php_ ingeven op de
volgende regel:

    $config['base_url'] = "http://www.mijndomein.nl/test";

   [1]: http://codeigniter.com/user_guide/database/configuration.html

   [2]: http://codeigniter.com/user_guide/installation/troubleshooting.html
