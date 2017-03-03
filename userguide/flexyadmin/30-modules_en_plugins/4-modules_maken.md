# Modules maken

Als je modules of plugins gaat maken is het handig om de volgende pagina's te hebben gelezen:

- [CodeIgniter must read]({codeigniter})
- [Frontend Controller]({Frontend-controller})
- [Modules laden en aanroepen]({Modules-laden-en-aanroepen})

## Onderdelen van een module

Een module bestaat uit de volgende onderdelen:

- De module zelf, in de map _site/libraries_

Eventueel de volgende onderdelen:

- Een config bestand, in de map _site/config_
- Eén of meerdere views in de map _site/views_
- Eén of meerdere language bestanden in de map _site/language_
- Een sql bestand in de map _db_

## De Module zelf

Een module is een PHP Class die overerft van de Class Module. Het is in feite
een standaard [CodeIgniter Library][2] met wat extra's. Zo ziet een zeer
eenvoudige module eruit:

  class Example extends Module {

    public function __construct() {
      parent::__construct();
    }

    // index is the standard method
    public function index($page) {
      $content='<h1>Example Module</h1>';
      return $content;
    }

  }
    
Je ziet dat er minstens één method nodig is: function index().

Een method van een module krijgt altijd één variabele mee: `$page`. Dit is de huidige pagina met al z'n standaard geladen velden.
`$page` is een array en ziet er in een standaard instelling bijvoorbeeld zo uit:

    [id] => '2'
    [order] => '1'
    [self_parent] => '0'
    [uri] => 'een_pagina'
    [str_title] => 'Een pagina'
    [txt_text] => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivam ...'
    [str_module] => ''
    [stx_description] => ''
    [str_keywords] => ''
    

## Meer methods

Standaard wordt dus de `index()` method aangeroepen. Maar je kunt een module ook meerdere aanroepbare methods geven.
Bijvoorbeeld zoals hieronder met als extra de method other die als `example.other` wordt aangeroepen:

  class Example extends Module {
      
    public function __construct() {
      parent::__construct();
    }

    // index is the standard method
    public function index($page) {
      $content='<h1>Example Module</h1>';
      return $content;
    }

    // example.other
    public function other($page) {
      // Do something...
      $page['module_content']='<h1>Example Module.Other</h1>';
      return $page;
    }

  }


## Config

Als er in _site/config_ een bestand staat met dezelfde naam als de module, dan
worden de instellingen in dat bestand automatisch geladen in de module en zijn in de module bekend als:

	$this->config('item_naam');

In het config bestand correspondeerd dit met:

	$config['item_naam'] = 'test';

Als je globale instellingen wilt ophalen doe je dat met de standaard [CodeIgniter config class][4]:

	$this->CI->config->item( 'language' );
  
  
## Output van de module

Standaard wordt de output (een string met HTML) van de module aan de pagina toegevoegd in de variabel `module_content` die op zijn beurt gebruikt wordt in de view `page.php`.
Er zijn echter meer mogelijke bestemmingen van de module output:

  1. Standaard: een string variabele met daarin HTML wordt aan de pagina toegevoegd.
  2. Als de output een array is wordt het gezien als een aangepaste array `$page` die later wordt gebruikt in de view `page.php`. Je kunt hiermee dus je pagina op allerlei manieren aanpassen. Bijvoorbeeld het veld `str_title`.
  3. Met een instelling kun je ook bepalen dat de output niet aan de pagina (`$page`) wordt gegeven maar aan site (`$this->site`, zie bij [Frontend Controller]({Frontend-controller}) ). Zo heb je dus veel meer invloed op de inhoud van de site. Denk aan een achtergrond-afbeelding, of content in een kolom die in de view $site staat.
  
De instelling ziet er zo uit:

    $config['__return']='';
    
Dit zijn de mogelijk waarden:

  - Als de instelling niet bestaat wordt de output aan de pagina gegeven zoals hierboven beschreven.
  - '' of 'page - idem
  - 'site' - geeft de output aan `$this->site[module_naam.method]` (of als method index is: `$this->site[module_naam]`)
  - Een combinatie is ook mogelijk, gescheiden door een pipe: 'page|site'


## Views

Het is goed gebruik om de PHP code te scheiden van je HTML. Dat kun je doen met een [view][3].
Je kunt binnen je module op twee manieren met views omgaan:

  1. De output (zie hierboven) door een view laten genereren. Het eind van je module ziet er dan bijvoorbeeld zo uit:
  
        return $this->CI->view( 'links', array( 'links'=>$links ), true );
      
  2. Standaard wordt een pagina getoond door de view _site/views/page.php_. Je kunt in je module aangeven dat de output van de pagina door een andere view getoond wordt. Bijvoorbeeld:
  
        // Dit is een module voor de startpagina van je site. Deze genereerd wat extra's en heeft een andere view nodig.
        // Hier wordt een andere view ingesteld:
        $this->CI->set_page_view('homepage');
      
        // Ga nu gewoon verder door aan $page meerdere variabelen toe te kennen die worden gebruikt in de view 'homepage'
        $page['blog'] = $blog_items;
        $page['twitter'] = $this->CI->view('twitter',array(),true);
        return $page;


Mocht je module meerdere views nodig hebben dan kun je ze in een submap van site/views plaatsen.

  
## De parent class Module

Zoals hierboven al eerder gemeld zijn modules gebaseerd op de [Class Module]({Module}). Wat doet deze nou?

- Laden van een config bestand.
- De naam van de Module instellen in de variabele `$this->name`. 
- Een verwijzing naar het CodeIgniter super-object bekendmaken als `$this->CI`.
  (Omdat een Module niet meer is dan een [CodeIgniter library][2] is de het CodeIgniter super-object niet bekend.)
  Als je bijvoorbeeld de database class wilt aanroepen zul je dat zo moeten doen: `$this->CI->db->...`

De Class module voorziet ook in een aantal standaard methods, de meeste zul je
weinig nodig hebben. Ze worden beschreven bij [Module]({Module}).


   [1]: http://codeigniter.com/user_guide/toc.html

   [2]: http://codeigniter.com/user_guide/general/creating_libraries.html

   [3]: http://codeigniter.com/user_guide/general/views.html

   [4]: http://codeigniter.com/user_guide/libraries/config.html

*/