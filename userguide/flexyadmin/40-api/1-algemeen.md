# Algemeen

FlexyAdmin heeft een standaard API. Daarmee is het mogelijk voor andere sites of bijvoorbeeld mobiele apps om informatie informatie op te vragen of zelfs aan te passen.

## Aanroepen API

Het aanroepen van de API gaan met een standaard URL die alsvolgt is opgebouwd:

- `http://www.flexyadmin.com`         - *De URL van je site*
- `/_api`                             - *De specifieke API uri waarmee je aangeeft een API aanroep te doen*
- `/page`                             - *Daarna volgt de API aanroep die je nodig hebt*
- `/subapi`                           - *Eventueel volgt daarna een specifieke onderdeel van de API aanroep*
- `?parameter1=test&parameter2=3`     - *Eventueel volgen daarna nog GET paramaters in de url. Het hangt van de API aanroep of en wat daar nodig is*

Enkele voorbeelden:

- `http://www.flexyadmin.com/_api/page?uri=start`
- `http://www.flexyadmin.com/_api/auth/check`

## Parameters meegeven aan de API

- Parameters kunnen op twee manieren aan de API aanroep worden meegegeven, als GET of als POST data.
- Het is afhankelijk van de API aanroep welke gevraagd wordt. Zie de documentatie van de aanroep.
- Een API kan ook zijn actie bepalen afhankelijk van of er GET of POST data beschikbaar is. Bijvoorbeeld `/_api/row` die bij GET informatie opvraagt, en bij POST informatie toevoegt of aanpast.

### GET

- Gebruikt voor het opvragen van informatie
- Direct in de url
- Beginnend met `?`
- Meerdere parameters gescheiden door `&`
- Eenvoudige te testen met een browser

Enkele voorbeelden:

- `.../_api/page?uri=start`
- `.../_api/page?uri=contact`
- `.../_api/table?table=tbl_menu`
- `.../_api/table?table=tbl_links&limit=3&offset=2`
- `.../_api/row?table=tbl_links&where=3`


### POST

- Gebruikt voor het sturen van informatie naar de API
- Bijvoorbeeld om in te loggen
- Of om informatie aan te passen of toe te voegen
- POST data wordt bijvoorbeeld door een webformulier gegenereerd
- Of door een JavaScript bestand met een AJAX aanroep, jQuery doet dat bijvoorbeeldmet `$.post()`
- Je kunt in een browser testen met de browser plugin [http://restclient.net](http://restclient.net)  (Let op dat je de header aanpast met: `Content-Type:application/x-www-form-urlencoded` )

Enkele voorbeelden:

- `.../_api/auth/login`
- `.../_api/row`

## Response data

Een API aanroep geeft altijd resultaat.
Behalve als er niet is ingelogd, dan komt er een `HTTP/1.1 401 Unauthorized` header terug. Hoe je moet inloggen wordt uitgelegd bij de API aanroep `auth`.

De response komt standaard als JSON.

### Standaard response keys

De response bevat een aantal standaard keys:

- `success` - *true/false, geeft aan of de aanroep gelukt is*
- `error`   - *als success=false, dan komt hier een foutmelding in te staan*
- `message` - *sommige api's kunnen hier extra informatie in geven*
- `data`    - *hierin komen de gegevens die de API aanroep teruggeeft.*

Mogelijke extra keys:

- `info`    - *kan extra informatie bevatten over het resultaat*
- `config`  - *bevat de config informatie die gevraagd is, zie hieronder*
- `options` - *bevat mogelijke opties voor velden, als gevraagd, zie hieronder*
- `status`  - *bij een dump zichtbaar, bijvoorbeeld `status=401` als er niet is ingelogd terwijl dat wel nodig is*
- `format`  - *welk formaat is teruggegeven*
- `api`     - *de naam van de api aanroep*
- `test`    - *true bij een dump*


## Admin API - Veelgebruikte parameters

De standaard admin API van FlexyAdmin heeft een aantal parameters die bij de diverse aanroepen veel gebruikt worden.

### table

De `table` parameter is in de meeste API aanroepen nodig. Hiermee geef je de database tabel aan waar de data van wordt opgevraagd.

### path

De `path` parameter is vergelijkbaar met `table`, behalve dat het nu om een map gaat waar bestanden in staan.


### where

De `where` parameter is in een aantal API aanroepen nodig. Hiermee selecteer je bijvoorbeeld de rij(en) die je wilt opvragen.
Er zijn diverse manieren om `where` te gebruiken, hieronder volgt voor elke manier voorbeelden en uitleg:

- `where=10`          // Hiermee wordt direct verwezen naar een uniek id nummer. In SQL zou dit er zo uitzien: `WHERE id=10`
- `where=3`           // Idem: `WHERE id=3`
- NB op dit moment zijn er nog geen andere manieren hoe `where` kan worden gebruikt.

In het geval van acties op bestanden bevat `where` de naam van het bestand:

- `where=IMG_908.JPG`


### data

Als er data naar de database moet worden gestuurd (aanpassen van huidige gegevens of toevoegen van nieuwe gegevens) gebeurt dat altijd met de POST parameter `data`.
Deze bevat een array met field/value paren van de nieuwe data. Bijvoorbeeld:

`data[str_title]=Subpagina&data[txt_text]=Lorem ipsum dolor sit amet, consetetur sadipscing elitr.`