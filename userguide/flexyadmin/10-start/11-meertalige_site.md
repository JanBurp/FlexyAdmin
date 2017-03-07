# Meertalige site

TODO: dit is verouderd...

Meertalige sites zijn op verschillende manieren mogelijk. Hier wordt de standaard manier uitgelegd waarbij de taal het eerste uri-deel van de url wordt.
Dus bijvoorbeeld: *nl/home* en *en/home* bij een site met de talen nederlands (nl) en engels (en).

Snelstart meertalige site
=========================

- Stel in [config.php]({Frontend-controller}) je talen en standaard-taal in
- Zorg dat de site met [samengestelde menu's]({Samengesteld-menu}) werkt
- Zorg in de tabellen die meertalig moeten zijn dat je velden meertalig zijn, dus: *str_title* wordt *str_title_nl* en *str_title_en* en *txt_text* wordt *txt_text_nl* en *txt_text_en* etc.
- In *res_menu_result* moeten die velden zonder(!) taal suffix komen: *str_title* blijft gewoon zo en dus geen *str_title_nl*. Meestal hoef je dus niets te veranderen aan *res_menu_result*
- Voeg bij **Auto Menu** iig de taalkeuze toe, aan het eind, en geef als parameters je talen, bijvoorbeeld: **nl|en**

Als je dit allemaal hebt gedaan komt in *res_menu_result* een menu met voor elke taal een eigen submenu.

Hoe wordt de taal gekozen?
============================

De frontend controller kiest de taal op de volgende manieren:

- Zit er aan het begin van de uri een taal? *nl/pagina* geeft dan **nl** en _en/blog/post_one_ geeft dan **en**
- Als er geen taal gekozen is die je hebt ingesteld als mogelijke talen: pak dan de standaardtaal van de browser
- Als er nog geen taal gekozen is die je hebt ingesteld als mogelijke talen: Pak dan de standaardtaal die je hebt ingesteld

Als je de taal (ook nog) op andere manieren wilt kiezen (bijvoorbeeld met een cookie) pas dan de method `_set_language()` in de frontend controller aan naar wens.


Waar pas ik taalteksten aan?
============================

In de map *site/language* vind je mappen van de beschikbare talen (standaard _nl_ en _en_). Hierin vindt je diverse taalbestanden voor de meegeleverde modules.
Je kunt daarin zelf aanpassingen maken en voor eigen modules ook eigen taalbestanden maken. Zie [Modules maken]({Modules-maken}).

Database tabel voor taalbestanden
=================================

Je kunt ook een tabel in de database gebruiken. Als eerste wordt dan in deze tabel gezocht, als het gezochte woord of taal niet bestaat, wordt gezocht in de taalbestanden:

- Voeg de taaltabel toe: _db/add_language_table.sql_
- Zorg dat in _site/config/config.php_ de instellingen `$config['language_table']  = "cfg_lang";` staat.
- Als je naast de standaard talen meer talen nodig hebt voeg dan nieuwe velden toe: _'lang_XX'_ waar _XX_ de taal is.

Nieuwe talen toevoegen
======================

Standaard zijn de talen 'nl' en 'en' mogelijk. Wil je zelf meer talen toevoegen? Volg dan de volgende stappen (in de stappen wordt 'de'-duits als taal toegevoegd):

- In *site/languages* de map en alle bestanden van *nl* kopieëren en hernoemen naar *de* zodat er nu drie mappen van drie talen bestaan met daarin precies dezelfde bestanden.
- Hetzelfde moet je doen in *sys/flexyadmin/languages*
- Je kunt dan eventueel zelf alle teksten in de taalbestanden in de nieuwe mappen vertalen naar duits
- Als je een database tabel voor je talen gebruikt, voeg dan de juiste velden toe (zie hierboven).


Voor meer informatie lees de [CodeIgniter Language Class][1] en de [CodeIgniter Language Helper][2]

   [1]: http://ellislab.com/codeigniter/user-guide/libraries/language.html
   
   [2]: http://ellislab.com/codeigniter/user-guide/helpers/language_helper.html
   
*/