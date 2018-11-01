#assets_actions

 \ingroup models
API: assets

 

#auth

 \ingroup models
API auth. Hiermee kan worden ingelogd of uitgelogd.

Bij login wordt een auth_token terugegeven die gebruikt moet worden in cross-domain apis in de authentication header.
Deze auth_token is een dag geldig.


###_api/auth

Parameters: GEEN

Response data:

 - `HTTP/1.1 401 Unauthorized` header als niet is ingelogd.
 - Een array met een aantal gegevens van de gebruiker (zie hieronder).

Voorbeeld:

 - `_api/auth`


###_api/auth/login

POST Parameters:

 - username  - De gebruikersnaam van het profiel
 - password  - Het wachtwoord van het profiel

Response data:

 - `HTTP/1.1 401 Unauthorized` header als niet is ingelogd
 - Een array met een aantal gegevens van de gebruiker (zie hieronder).

Voorbeeld:

 - `_api/auth/login`
 - Waarbij de POST data er zo uitziet: `username=profielnaam&password=profielwachtwoord`


###_api/auth/logout

Parameters: GEEN

Response data:

 - `HTTP/1.1 401 Unauthorized` header.

Voorbeeld:

 - `_api/auth/logout`

###Voorbeeld response (dump) met uitleg:

     [success] => TRUE
     [api] => 'auth'
     [args] => (
       [type] => 'GET'
      )
     [user] => (
       [username] => 'admin'                    // Gebruikersnaam
       [group_name] => 'Administrator'          // Groepsnaam
       [group_id] => '1'                        // Groeps id
     )
     [data] => (
       [username] => 'admin'                     // Gebruikersnaam
       [email] => 'info@flexyadmin.com'          // Emailadres van gebruiker
       [language] => 'nl'                        // Taal van de gebruiker
       [auth_token] => 'xxxx'                    // Auth token die gebruikt moet worden in de Authorization header bij volgende api aanroepen. Is een dag geldig. Daarna moet een nieuwe worden opgevraagd door in te loggen.
      )

 

#get_admin_nav


API: Geeft het admin menu terug voor in het backend deel van FlexyAdmin.

 

#get_help


API: Geeft help tekst

 

#get_image_list


API: Geeft een lijst van links voor de TinyMCE editor

 

#get_link_list


API: Geeft een lijst van links voor de TinyMCE editor

 

#get_plugin


API: Geeft plugin pagina, voor backend van FlexyAdmin

 

#link_checker

 \ingroup models
API user

   
 

#media

 \ingroup models
API: media. Geeft een lijst, bewerkt of upload bestanden toe aan een map.
De specifieke functie wordt bepaald door de (soort) parameters. Zie hieronder per functie.

##GET files

Hiermee wordt een lijst opgevraagd van een map

###Parameters (GET):

 - `path`                     // De map is assets waarvan de bestanden worden opgevraagd.
 - `[offset=0]`               // Sla de eerste bestanden in de lijst over
 - `[limit=0]`                // Geef een maximaal aantal bestanden terug (bij 0 worden alle bestanden teruggegeven)
 - `[settings=FALSE]`         // Instellingen van de gevraagde map 

###Voorbeelden:

 - `_api/media?path=pictures`
 - `_api/media?path=pictures&settings=true`

###Response:

De `info` response key geeft extra informatie over het resultaat, met de volgende keys:

 - `files`        // Het aantal bestanden in `data`.
 - `total_files`  // Het totaal aantal bestanden van de map die opgevraagd is. (op dit moment nog hetzelfde)

Voorbeeld response (dump) van `_api/media?path=pictures`:

    [success] => TRUE
     [api] => 'media'
     [args] => (
       [path] => 'pictures'
       [type] => 'GET'
      )
     [data] => (
       [2] => (
         [id] => '2'
         [file] => 'test_03.jpg'
         [path] => 'pictures'
         [full_path] => '_media/pictures/test_03.jpg'
         [str_type] => 'jpg'
         [str_title] => 'wbCYmaFZ'
         [dat_date] => '2014-09-16'
         [int_size] => '114'
         [int_img_width] => '960'
         [int_img_height] => '720'
        )
      )
     [info] => (
       [files] => 1
       [total_files] => 1
      )
    )


##UPLOAD FILE

Hiermee kan een bestand worden geupload

###Parameters (POST):

 - `path`                     // De map waar het bestand naartoe moet.
 - `file`                     // De bestandsnaam dat geupload moet worden. NB Zoals het resultaat van een HTML FORM: `<input type="file" name="file" />`. Dus ook in FILES['file'].
 - `[settings=FALSE]`         // Instellingen van de gevraagde map 

###Voorbeeld:

 - `_api/media` met POST data: `path=pictures&file=test_03.jpg` en de corresponderende FILES data.

###Response:

Als het uploaden is gelukt komt in `data` de informatie van het bestand (NB de naam kan veranderd zijn na het uploaden!).
Als het uploaden om wat voor reden niet is gelukt zal `success` FALSE zijn en komt er in `error` een foutmelding.

    [success] => TRUE
     [test] => TRUE
     [api] => 'media'
     [args] => (
       [path] => 'pictures'
       [file] => 'test_03.jpg'
       [type] => 'POST'
      )
     [data] => (
       [id] => '27'
       [b_exists] => '1'
       [file] => 'test_03.jpg'
       [path] => 'pictures'
       [str_type] => 'jpg'
       [str_title] => 'test_03'
       [dat_date] => '2015-03-29'
       [int_size] => '18'
       [int_img_width] => '300'
       [int_img_height] => '225'
      )


##UPDATE FILE

Hiermee wordt informatie van een bestand aangepast

###Parameters (POST):

 - `path`                     // De map waar het bestand in staat
 - `where`                    // Bepaal hiermee welk bestand moet worden aangepast
 - `data`                     // De aangepaste data (hoeft niet compleet, alleen de aan te passen velden meegeven is genoeg).
 - `[settings=FALSE]`         // Instellingen van de gevraagde map 


###Voorbeeld:

 - `_api/media` met POST data: `path=pictures&where=test_03.jpg&data[str_title]=Nieuwe titel`

###Response:

Als response wordt in `data` TRUE gegeven als het aanpassen is gelukt:

    [success] => TRUE
     [test] => TRUE
     [format] => 'dump'
     [api] => 'media'
     [args] => (
       [path] => 'pictures'
       [where] => 'test_03.jpg'
       [data] => (
         [str_title] => 'TestTitel'
        )
       [type] => 'POST'
      )
     [data] => TRUE

##DELETE FILE

Hiermee wordt een bestand uit een map verwijderd.

 - `path`                     // De map waar het bestand in staat
 - `where`                    // Bepaal hiermee welk bestand moet worden verwijderd
 - `[settings=FALSE]`         // Instellingen van de gevraagde map 


###Voorbeeld:

 - `_api/media` met POST data: `path=pictures&where=test_03.jpg

###Response:

Als response wordt in `data` TRUE gegeven als het verwijderen is gelukt:

    [success] => TRUE
     [test] => TRUE
     [format] => 'dump'
     [api] => 'media'
     [args] => (
       [path] => 'pictures'
       [where] => 'test_03.jpg'
       [type] => 'POST'
      )
     [data] => TRUE


 

#row

 \ingroup models
API row. Geeft, bewerkt of voegt een record toe aan een tabel.
De specifieke functie wordt bepaald door de (soort) parameters. Zie hieronder per functie.

##GET ROW

Hiermee wordt een record uit een tabel opgevraagd.

###Parameters (GET):

 - `table`                    // De tabel waar de record van wordt opgevraagd.
 - `where`                    // Hiermee wordt bepaald welk record wordt opgevraagd.
 - `[as_form=FALSE]`          // Als `TRUE`, dan wordt de data als specifiek form formaat teruggegeven zoals het de backend van de CMS wordt getoond.
 - `[settings=FALSE]`         // Instellingen van de gevraagde tabel

###Voorbeelden:

 - `_api/row?table=tbl_menu&where=3`
 - `_api/row?table=tbl_menu&where=10`

###Response:

Voorbeeld response (dump) van `_api/table?row=tbl_menu&where=3`:

    [success] => TRUE
    [test] => TRUE
    [args] => (
      [table] => 'tbl_menu'
      [where] => '3'
      [type] => 'GET'
     )
    [data] => (
      [id] => '3'
      [order] => '0'
      [self_parent] => '2'
      [uri] => 'subpagina'
      [str_title] => 'Subpagina'
      [txt_text] => '<p>Een subpagina</p> ...'
     )


##INSERT ROW

Hiermee wordt een record uit een tabel toegevoegd
De data wordt altijd eerst gevalideerd.

###Parameters (POST):

 - `table`                    // De tabel waar de record aan wordt toegevoegd.
 - `data`                     // Het nieuwe record
 - `[settings=FALSE]`         // Instellingen van de gevraagde tabel


###Voorbeeld:

 - `_api/row` met POST data: `table=tbl_links&data[str_title]=Test&data[url_url]=www.test.nl`


###Response:

Als response wordt in `data` het `id` gegeven van het nieuw aangemaakte record.
Of `FALSE` bij een validatiefout, dan komen de volgende keys in `info`:

 - `validation`         // Of validatie is gelukt (TRUE|FALSE)
 - `validation_errors`  // Als validatie niet is gelukt komt hier een array van strings: ['veldnaam'=>'Error..']

Voorbeeld response (dump) van bovenstaand voorbeeld (als validatie is gelukt):

    [success] => TRUE
    [args] => (
      [table] => 'tbl_links'
      [data] => (
       [str_title] => 'Test'
       [url_url] => 'www.burp.nl'
      )
      [type] => 'POST'
    )
    [data] => (
     [id] => 12
    )



##UPDATE ROW

Hiermee wordt een record uit een tabel aangepast.
De data wordt altijd eerst gevalideerd.

###Parameters (POST):

 - `table`                    // De tabel waar de record aan wordt toegevoegd.
 - `where`                    // Bepaal hiermee welk record moet worden aangepast
 - `data`                     // De aangepaste data (hoeft niet compleet, alleen de aan te passen velden meegeven is genoeg).
 - `[settings=FALSE]`         // Instellingen van de gevraagde tabel

###Voorbeeld:

 - `_api/row` met POST data: `table=tbl_links&where=3&data[str_title]=Test&data[url_url]=www.test.nl`


###Response:

Als response wordt in `data` het `id` gegeven van het aangepaste record.
Of `FALSE` bij een validatiefout, dan komen de volgende keys in `info`:

 - `validation`         // Of validatie is gelukt (TRUE|FALSE)
 - `validation_errors`  // Als validatie niet is gelukt komt hier een array van strings: ['veldnaam'=>'Error..']

Voorbeeld response (dump) van bovenstaand voorbeeld:

    [success] => TRUE
    [args] => (
      [table] => 'tbl_links'
      [where] => 3
      [data] => (
       [str_title] => 'Test'
       [url_url] => 'www.burp.nl'
      )
      [type] => 'POST'
    )
    [data] => (
     [id] => 3
    )


##DELETE ROW

Hiermee wordt een record uit een tabel verwijderd.

###Parameters (POST):

 - `table`                    // De tabel waar de record van wordt verwijderd.
 - `where`                    // Bepaal hierme welk record wordt verwijderd, als where een array is worden er meerdere tegelijk verwijderd
 - `[settings=FALSE]`         // Instellingen van de gevraagde tabel

###Voorbeeld:

 - `_api/row` met POST data: `table=tbl_links&where=3

###Response:

Als response wordt `data` = TRUE als het verwijderen is gelukt.
Voorbeeld response (dump) van bovenstaand voorbeeld:

    [success] => TRUE
    [args] => (
      [table] => 'tbl_links'
      [where] => 3
      [type] => 'POST'
    )
    [data] => TRUE


 

#table

 \ingroup models
API table. Geeft de data van een tabel uit de database.

###Parameters:

 - `table`                    // De gevraagde tabel
 - `[path]`                   // Eventueel op te vragen map voor media/assets (bij table='res_assets')
 - `[limit=0]`                // Aantal rijen dat het resultaat moet bevatten. Als `0` dan worden alle rijen teruggegeven.
 - `[offset=0]`               // Hoeveel rijen vanaf de start worden overgeslagen.
 - `[order='']`               // De volgorde van het resultaat, geef een veld, bijvoorbeeld `str_title` of `_str_title` voor DESC
 - `[filter='']`              // Eventuele string waarop alle data wordt gefilterd
 - `[as_grid=FALSE]`          // Als `TRUE`, dan wordt de data als specifieke grid formaat teruggegeven zoals het de backend van de CMS wordt getoond. NB Kan onderstaande opties overrulen!
 - `[txt_abstract=0]`         // Als `TRUE`, dan bevatten velden met de `txt_` prefix een ingekorte tekst zonder HTML tags. Of een integer waarde voor de lengte.

 - `[as_options=FALSE]`       // Als `TRUE`, dan wordt de data als opties teruggegeven die gebruikt kunnen worden in een dropdown field bijvoorbeeld. (`limit` en `offset` werken dan niet)
 - `[options=FALSE]`          // Als `TRUE`, dan worden de mogelijke waarden van velden meegegeven.
 - `[settings=FALSE]`         // Instellingen van de gevraagde tabel


###Voorbeelden:

 - `_api/table?table=tbl_menu`
 - `_api/table?table=tbl_menu&offset=9&limit=10`
 - `_api/table?table=tbl_menu&txt_abstract=TRUE`
 - `_api/table?table=tbl_menu&settings=true`

###Response:

De `info` response key geeft extra informatie over het resultaat, met de volgende keys:

 - `num_rows`   // Het aantal items in `data`.
 - `total_rows` // Het totaal aantal items zonder `limit`

###Voorbeeld response (dump) van `_api/table?table=tbl_menu`:

    [success] => TRUE
    [test] => TRUE
    [args] => (
      [table] => 'tbl_menu'
      [limit] => 0
      [offset] => 0
      [type] => 'GET'
     )
    [data] => (
      [1] => (
        [id] => '1'
        [order] => '0'
        [self_parent] => '0'
        [uri] => 'gelukt'
        [str_title] => 'Gelukt!'
        [txt_text] => 'Als je dit ziet is het je gelukt om FlexyAdmin te installeren en werkend te krijgen.'
       )
      [2] => (
        [id] => '2'
        [order] => '1'
        [self_parent] => '0'
        [uri] => 'een_pagina'
        [str_title] => 'Een pagina'
        [txt_text] => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat.'
       )
      [3] => (
        [id] => '3'
        [order] => '0'
        [self_parent] => '2'
        [uri] => 'subpagina'
        [str_title] => 'Subpagina'
        [txt_text] => 'Een subpagina...'
       )
      [5] => (
        [id] => '5'
        [order] => '1'
        [self_parent] => '2'
        [uri] => 'nog_een_subpagina'
        [str_title] => 'Nog een subpagina'
        [txt_text] => ''
       )
      [4] => (
        [id] => '4'
        [order] => '2'
        [self_parent] => '0'
        [uri] => 'contact'
        [str_title] => 'Contact'
        [txt_text] => 'Hier een voorbeeld van een eenvoudig contactformulier.'
       )
     )
    [info] => (
        [num_rows] => 5
        [total_rows] => 5
       )

   
 

#table_order

 \ingroup models
API order. Past volgorde van items aan in een tabel

##UPDATE VOLGORDE

Hiermee wordt van items (id's) in de meegegeven tabel een nieuwe volgorde gemaakt (vanaf een bepaalde waarde)
 - Als er meerdere ids worden meegegeven dan wordt klakkeloos die volgorde overgenomen en verder niets (geen control op kinderen en tussenliggenden).
 - Als er één id wordt meegegeven dan wordt gekeken of er kinderen meemoeten en worden tussenliggende items opgeschoven.

###Parameters (POST):

 - `table`                    // De tabel waar de record aan wordt toegevoegd.
 - `id[]`                     // id's van de items die moeten worden aangepast
 - `[from=0]`                 // Startwaarde van de nieuwe volgorde van de items

###Response:

Als response worden de nieuwe volgordes meegegeven.
Of in het geval van één meegegeven id, dan wordt alleen de nieuwe volgorde teruggegeven

###Voorbeeld 1:

 - `_api/table_order` met POST data: `table=tbl_menu&id[]=3&id[]=5&id[]=7&from=3`

Voorbeeld response (dump) van bovenstaand voorbeeld:

    [success] => TRUE
    [args] => (
      [table] => 'tbl_links'
      [id] => (
               3,
               5,
               7
               )
      [from] => 3
      [type] => 'POST'
    )
    [data] => (
               0 => array( 'id'=>3, 'order'=>3 ),
               1 => array( 'id'=>5, 'order'=>4 ),
               2 => array( 'id'=>7, 'order'=>5 ),
             )

 ###Voorbeeld 2:

 - `_api/table_order` met POST data: `table=tbl_menu&id[]=3&from=3`

Voorbeeld response (dump) van bovenstaand voorbeeld:

    [success] => TRUE
    [args] => (
      [table] => 'tbl_links'
      [id] => 3
      [from] => 3
      [type] => 'POST'
    )
    [data] => 3

 

#tools

 \ingroup models
API user

   
 

#user

 \ingroup models
API user

   
 

