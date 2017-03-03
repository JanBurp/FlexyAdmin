# Plugins maken

Wat modules zijn voor de site zelf, zo zijn plugins uitbreidingen voor het CMS deel.

Als je plugins gaat maken is het handig om de volgende pagina's te hebben gelezen:

- [CodeIgniter must read]({codeigniter})
- [Modules maken]({Modules-maken})

## Naamgeving

Plugins vindt je in de map *site/libraries/plugins*. Een plugin kun je onderscheiden van modules en andere libraries doordat ze altijd beginnen met *'plugin_'*.

## Template

Je vind in die map een [template][1] plugin die je kunt gebruiken als basis voor je eigen plugins (['Plugin_template'][1]).

## Onderdelen van een plugin

Een module bestaat uit de volgende onderdelen:

- De module zelf, in de map _site/libraries/plugins_ beginnend met _'plugin_'
- Een config bestand, in de map _site/config/plugins_ met dezelfde naam als de plugin

Eventueel de volgende onderdelen:

- Eén of meerdere views in de map _site/views_
- Eén of meerdere language bestanden in de map _site/language_ (een language bestand met dezelfde naam als de plugin word standaard geladen)
- Een sql bestand in de map _db_

## Wat en wanneer wordt een plugin actief?

Een plugin van FlexyAdmin kan op een aantal plekken inhaken in het admin deel:

- Als eigen menu commando met een eigen url: `_admin/plugin/....`
- Tijdens Logout
- Als een (bepaald) veld van een (bepaalde) tabel wordt aangepast (of aangemaakt)
- Als een rij van een (bepaalde) tabel wordt verwijderd

Het admin deel van FlexyAdmin laad als eerste alle config bestanden van alle bekende plugins.
In die config bestanden staat ingesteld waar een plugin wil inhaken (met welke method van die plugin).
Zodat FlexyAdmin weet welke plugins wanneer moeten worden geladen en aangeroepen.


## De Class Plugin

Een plugin is gebaseerd op de [Class Plugin]({Plugin}). Wat doet deze:

- Laden van eventuele taal bestanden met dezelfde naam
- Laden van het config bestand. (Je kunt de functie `lang()` gebruiken in de config voor taal-variaties)
- De naam van de Module instellen in de variabele `$this->name`
- Een korte naam van de Module instellen in de variabele `$this->shortname`
- Een verwijzing naar het CodeIgniter super-object bekendmaken als `$this->CI`.
  (Omdat een Plugin niet meer is dan een [CodeIgniter library][2] is het CodeIgniter super-object niet bekend.)

De Class module voorziet ook in een aantal standaard methods, de meeste zul je weinig nodig hebben. Ze worden beschreven bij [Plugin]({Plugin}).

## Eenvoudig voorbeeld

Stel je wilt een plugin dat de complete [link_lijst]({Links-en-downloads}) laat zien met de naam: _plugin_list_.

### Het config bestand

Als eerste moet er een config bestand komen onder dezelfde naam (_plugin_list.php_) maar dan in de map _site/config/plugin_.
We baseren deze op _plugin_template.php_ en passen aan wat voor ons nodig is:

    /*
    |--------------------------------------------------------------------------
    | Plugin methods:
    |
    | Hier stellen we in dat we een menu-item maken die verwijst naar de method '_admin_api'
    |--------------------------------------------------------------------------
    |
    */
    
    $config['admin_api_method'] = '_admin_api';
    
    
    /*
    |--------------------------------------------------------------------------
    | Plugins specific config settings
    |
    | Hier kun je eigen instellingen maken, wij geven hier als instelling de naam van de lijst
    |--------------------------------------------------------------------------
    |
    */
    
    $config['list'] = 'link_list.js'


Er gebeuren hier twee dingen:

- We zeggen tegen FlexyAdmin dat deze plugin alleen actief wordt als eigen menu naam (met de url '../_admin/plugin/list')
- We creeëren een eigen instelling: de naam van de lijst die we willen tonen

### Overrulen van standaard config

Met name voor de standaard meegeleverde plugins (die niet in de map site/libraries staan) is soms nodig om de werking aan te passen.
Dat kan door het config bestand van die plugin te overrulen met een eigen config bestand. Dit is de werkwijze om dat te doen:

- Maak een kopie van de originele config:
- Je vindt de originelen in de map *sys/flexyadmin/config/plugin*. 
- Plaats de kopie in de map *site/config/plugin*.
- Verwijder in die kopie alles wat je niet wilt aanpassen.
- Pas de items aan die je wilt aanpassen.

### De plugin

Weer gebruiken we de template als uitgangspunt en verwijderen wat niet nodig is en passen aan wat we wel nodig hebben.
Het resultaat, met commentaar:

    class Plugin_template extends Plugin {
    
      /**
        * _admin_api
        *
        * Dit is de method van de plugin die aangeroepen wordt vanuit het menu of met de url '../admin/plugins/list'
        *
        * @param $args  Is een lijst met eventuele argumenten die meegegeven worden aan de plugin
        *               Argumenten zijn alle uri-delen na de standaard url.
        * @return string Geef de HTML output terug die de plugin wil laten zien
        **/

      public function _admin_api($args=NULL) {
        $titel='De complete lijst: ' . $this->config('list');
        return $titel;
      }
      
    }
    
Op dit moment doet de plugin niet meer dan een tekst tonen:

    De complete lijst: link_list.js
    
Laten we de lijst laden en tonen:

      public function _admin_api($args=NULL) {
        $listFile = $this->config('list');
        $list = read_file( 'site/assets/lists/'.$listFile );   // Laad de lijst in
        
        $HTML = '<h1>De complete lijst: '.$listFile. '</h1>';   // Maak HTML output aan, begin met een titel
        $HTML.= $list;                                          // Voeg de lijst aan de HTML toe
        
        return $HTML;                                           // Geef de HTML terug, klaar!
      }



Het is natuurlijk mooier om niet de $HTML variabele te gebruiken maar aan array aan te maken en die door te sturen naar een view:

      public function _admin_api($args=NULL) {
        $listFile = $this->config('list');
        $list = read_file( 'site/assets/lists/'.$listFile );
        return $this->CI->load->view('plugin_list',array('file'=>$listFile, 'list'=>$list), true);   // Laad de view en geef het resultaat terug
      }

Hoe je een view maakt leggen we hier niet uit, dat kun je [hier][2] lezen.

Als output is de variabel $list nu natuurlijk nog niet zo interessant, het is een letterlijke weergave van het JSON object.
Met hulp van [MY_Array_helper]({MY_array_helper}) kun je er vast iets mooiers van maken en in je view daarmee dat mooier weergeven.
We gaan daar hier verder niet op in en focussen op de mogelijkheden van plugins.

### Argumenten meegeven aan een plugin

Een plugin zoals hierboven kun je argumenten meegeven door ze aan de URL toe te voegen.

      ../_admin/plugins/arg1/.../arg2/...
      
Zo lezen we het argument in in onze plugin:

      public function _admin_api($args=NULL) {
        if (isset($args[1])) {
          // Hé, er is een argument meegegeven!
          // $args is een array van de meegegeven argumenten (onderdelen van de uri)
        }
        else {
          // Mmm, geen argument meegegeven, dan de standaard maar gebruiken
        }
      }


## De inhaakplekken van een plugin

Plugins kunnen op verschillende plekken inhaken. Elke manier verwijst naar een eigen method in de plugin.
Alleen de methods die in de config ingesteld zijn worden aangeroepen. Hieronder een overzicht van de mogelijkheden:

Config                | Standaard Method  | Uitleg
----------------------|-------------------|-------
admin_api_method      |_admin_api         |Deze method wordt gebruikt als de plugin door een URL wordt aangeroepen (kan dus een eigen menu-item worden)
logout_method         |_admin_logout      |Deze method wordt aangeroepen bij het uitloggen
ajax_api_method       |_ajax_api          |-gereserveerd-
after_update_method   |_after_update      |Deze method wordt aangeroepen nadat een gebruiker iets bepaalde tabellen en/of velden in FlexyAdmin heeft aangepast
after_delete_method   |_after_delete      |Deze method wordt aangeroepen nadat een gebruiker iets uit een bepaalde tabel heeft verwijderd

Voor meer uitleg per method zie [Plugin_template][1].

## Triggers

De *after_delete_method* en de *after_update_method* worden actief als aan bepaalde voorwaarden worden voldaan.
Die voorwaarden zijn de triggers en worden ingesteld in de config bij `$config['trigger']`:
 
- tables - geef hier in een array aan bij welke tabellen de trigger actief wordt
- field_types - geef hier in een array aan bij welke veld typen (prefixen) de trigger actief wordt, als er bij tables niets bekend is geld het voor alle tabellen.
- fields - geef hier in een array aan bij welke velden (volledige veldnamen) de trigger actief wordt, als er bij tables niets bekend is geld het voor alle tabellen.

### Dynamische triggers

Het kan zijn dat de triggers afhankelijk zijn van de inhoud van de database bijvoorbeeld. Dus dat niet van te voren bekend is welke triggers wanneer actief moeten worden. 
In zo'n geval kun je zelf een dynamische trigger aanmaken:

- Zet in de config: `$config['trigger_method'] = '_trigger'`;
- Maak in je plugin de method `_trigger` die de als return waarde een trigger array geeft zoals je normaal in de config zet.
- De output van de trigger method wordt samengevoegd met de trigger array in de config.


  [1]: {Plugin_template}
  
  [2]: http://codeigniter.com/user_guide/general/views.html
  
*/