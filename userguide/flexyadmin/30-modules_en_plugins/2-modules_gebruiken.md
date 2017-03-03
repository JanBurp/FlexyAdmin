# Modules gebruiken

Modules worden geladen door de controller. De controller beslist op twee manieren welke modules worden geladen:

- Aan de hand van instellingen in _site/config/config.php_
- Aan de hand van door de gebruiker ingevuld veld in het menu (standaard _str_module_)

## Modules laden aan de hand van instellingen

Hieronder zie je een overzicht van de instellingen in _site/config/config.php_ die invloed hebben op het laden van modules:

- Als je wilt dat een module op elke pagina wordt geladen:

        $config['autoload_modules'] = array('submenu');

- Modules laden voor bijna elke pagina:
  Stel je wilt de _submenu_ module alleen laden op pagina's waar het veld _b_submenu_ van _tbl_menu_ waar is.
  Dan kun je dat zo instellen:

        $config['autoload_modules_if'] = array('submenu'=>array('b_submenu'=>true));

## Modules laden bij bepaalde pagina's

Standaard heeft _tbl_menu_ het veld _str_module_. In dit veld kan de gebruiker een modulenaam kiezen.
Bijvoorbeeld de standaard module _links_. Dan weet de controller dat deze module geladen moet worden.
De controller kijkt standaard naar het veld _str_module_. Je kunt de controller ook naar een ander veld laten kijken,
bijvoorbeeld _uri_, of zelfs een foreign key verwijzing naar een tabel met modules (_id_modules_). Dit kun je dan aanpassen met:

    $config['module_field']='str_module';


## Fallback module

Als de controller onverhoopt een module probeert te laden die niet bestaat dan zal de module niets doen. In plaats daarvan kan
de controller een fallback module laden. Standaard staat dit ingesteld op 'fallback'. Je kunt die [module aanpassen][8] zoals je wilt.

    $config['fallback_module']='fallback';

## Modules met meerdere methods

In bovenstaande voorbeelden worden modules geladen met hun eigen naam. De controller roept dan de index method aan van de module.
Soms is het handig om een module meerdere aanroepbare methods te geven. 
In zo'n geval kun je dat aangeven met de module naam, een punt, en de naam van
de method. Als je bijvoorbeeld van de module _search_ het method _form_ wilt aanroepen dan doe je dat zo:

	search.form
    
Dit kan op elke bovenstaande manieren van laden en uitvoeren van modules.

   [8]: {Modules-maken}