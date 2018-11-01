# Faq

## Frontend

### Subpagina's zijn niet te bereiken

Meestal betekend dit dat de server niet goed ingesteld. Kijk bij [Installatie]({Installatie}).

### Scherm wit na uploaden bestand

Meestal is het bestand te groot voor de server instellingen.
Als het bij een klein bestand ook gebeurt, zorg er dan voor dat PHP alle foutmeldingen laat zien (in de server-instellingen op het web is hier genoeg over te vinden) en kijk wat voor fout er komt.

### Afbeeldingen hebben niet de ingestelde omvang

Standaard worden de afbeeldingen die in tekstvelden (txt_) komen zo opgeschoond dat de `heigth` en `width` attributen worden verwijderd.
Dit kun je aanpassen in _site/config/config.php_ bij `$config['parse_content']`. Daar vind je ook meer uitleg.


## CMS

### Ik heb de database en/of een config/data/...php bestand aangepast maar er gebeurt niets.

Probeer de cache folder leeg te halen: `site/cache`.
Hierin komen onder andere caches van automatisch gegenereerde data settings.


