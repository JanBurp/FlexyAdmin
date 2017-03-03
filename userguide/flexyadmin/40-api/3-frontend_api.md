# Frontend API

`_api/page`
-----------

Eenvoudig voorbeeld van een frontend API.
Geeft de tekst van een pagina van de site.

Parameters:

 - `uri`  - Geef hier de uri van de opgevraagde pagina

Response data:

 - `FALSE`  - als de pagina niet is gevonden
 - `array`  - van de gevonden pagina zoals die uit de menu tabel komt (meestal `tbl_menu` of `res_menu_result`)

Voorbeeld:

 - `/_api/page?uri=contact`
