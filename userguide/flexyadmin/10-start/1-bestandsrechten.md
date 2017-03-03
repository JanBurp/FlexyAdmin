# Bestandsrechten

Onderstaande bestanden en mappen moeten schrijfrechten hebben om FlexyAdmin
goed te laten werken:

- sitemap.xml - moet schrijfbaar zijn zodat aanpassen ook hierin terecht komen.
- robots.txt - moet eenmalig het absolute pad naar sitemap.xml kunnen aanpassen. Dat gebeurd als bij Site de url van de site wordt ingevoerd. Daarna hoeft robots.txt geen schrijfrechten meer te hebben.
- site/stats - voor het opslaan van xml bestanden voor site statistieken
- site/cache - voor het opslaan van cache bestanden
- site/assets/ - alle mappen hieronder

## Bestanden en mappen in de root

Onderstaande mappen en bestanden zie je als FlexyAdmin in een lege map heb
geinstalleerd:

bestand/map                              |uitleg
-----------------------------------------|-------------------------------------------
**bower.json**                           |Alleen aanpassen als je bower wilt gebruiken
**changelog.txt**                        |Globale informatie over laatste wijzigingen van FlexyAdmin
**gulpfile.js**                          |Voor het build proces met gulp, alleen aanpassen als je hier gebruik van maakt
**package.json**                         |Wordt gebruikt om gulp en bower te installeren. Niet aankomen.
readme.md                                |Zeer globale info
**todo.txt**                             |Dit bestand kun je gebruiken als kladblok.
**update.txt**                           |Hierin staat sumier hoe je eventueel kunt updaten naar nieuwere versies van FlexyAdmin
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;**db**          |Map met demo databases en database updates voor FlexyAdmin. Gebruik deze map ook om je database backups in te bewaren.
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;public          |Deze map is zichtbaar voor de buitenwereld. Zie hieronder.
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;site            |Dit is de map waar je als bouwer veel mee te maken hebt. Zie hieronder.
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;sys             |De map met FlexyAdmin. LET OP: Hier niets aanpassen!!
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;**userguide**   |Handleidingen van FlexyAdmin & CodeIgniter

De mappen en bestanden in **vet** zijn niet nodig voor de online site.

### /public

bestand/map                              |uitleg
-----------------------------------------|-------------------------------------------
.htaccess                                |Niet aanpassen
htaccess.htaccess                        |Copy van *.htaccess*
index_temp.html                          |Dit kun je gebruiken als een tijdelijke startpagina. Eventueel zelf aan te passen naar wens.
index.php                                |De start van alles. Niet aankomen.
readme.txt                               |Zeer globale info
robots.txt                               |Zorgt ervoor dat zoekrobots niet in alle mappen kijkt en heeft een verwijzing naar *sitemap.xml*
sitemap.xml                              |Hierin staat de menustructuur van de site. Wordt automatisch aangepast. Zorg ervoor dat dit bestand beschrijfbaar is.
temp.htaccess                            |Hernoem deze tijdelijk naar *.htaccess* en de tijdelijke pagina *index_temp.html* is actief. Handig tijdens grote updates.
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;assets    |In deze map komen alle bestanden die bereikbaar moeten zijn voor de buitenwereld: css,js etc.

## /public/assets

bestand/map                          |uitleg
-------------------------------------|-------------------------------------------
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;css         |Plaats hier je stylesheets, en zorg voor een goede link vanuit de view *site.php*.<br />Standaard staan hier: *text.css*, *layout.css*, *ie*.css* en *admin.css*. Zie de comments van elke stylesheets.
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;fonts       |Hier kun je eigen fonts in plaatsen. Standaard staat hier het glyphicon font van bootstrap in.
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;less-default|Hier vind je de standaard LESS bestanden
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;less-bootstrap|Hier vind je de LESS bestanden voor het gebruik van Bootstrap
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;img         |Plaats hier de afbeeldingen die nodig zijn voor de styling. Dus geen afbeeldingen die de gebruiker moet kunnen uploaden.

## /site

Hier zie je de bestanden en mappen in de map Site:

bestand/map                        |uitleg
-----------------------------------|-------------------------------------------
Controller.php                     |Hierin zul je als gevorderde webbouwer veel kunnen aanpassen. Hier wordt bepaald wat er gebeurt bij welke URL en worden eventuele modules en views geladen en aangeroepen.
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;assets    |In deze map komen alle bestanden die de site verder nodig heeft en die de browser moet kunnen benaderen. Zoals foto's, downloads, etc.
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;cache     |Map met cachebestanden als je caching gebruikt voor je site.
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;config    |Hierin vind je config bestanden, bijvoorbeeld voor de database.<br />Enkelen hebben een *_local* suffix. Deze worden gebruikt voor lokale instellingen. Zo kun je eenvoudig een scheiding maken tussen je lokale en online instellingen.<br />Je vindt hier ook eventuele config bestanden voor modules en noodzakelijke config bestanden voor plugins.
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;helpers   |Hier kun je eigen helpers in plaatsen voor ondersteuning van je eigen modules of plugins. <a href="http://codeigniter.com/user_guide/general/helpers.html" target="_blank">Hier lees je meer over CodeIgniter helpers</a>.
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;languages |Hierin vind je language bestanden gegroepeerd per taal. Standaard vind je hier de talen *nl* (Nederlands) en *en*(Engels). Je kunt zelf talen en taalbestanden toevoegen voor je modules. Of de bestaande taalbestanden aanpassen.
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;libraries |Map met eigen libraries (classes). Hierin vind je modules en plugins. Deze kun je zelf aanpassen of nieuwe toevoegen. Er zitten al een aantal voorbeeld modules in.
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;models    |Map met CodeIgniter models voor je site. Lees hier meer over <a href="http://codeigniter.com/user_guide/general/models.html" target="_blank">models</a>.
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;stats     |Map met xml bestanden voor de statistieken van de site.
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;tests     |Hier kun je eventueel eigen unittests inzetten.
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;views     |De map met de views: de eigenlijke HTML bestanden met PHP code waar de data komt. Zelf aan te maken en aan te passen.<br />De belangrijkste is *site.php*, dit is de view waarin alles samen komt. Verder zijn standaard *page.php* (een pagina), *links.php* (voor de links module) en *error.php* (foutmelding voor als een pagina niet gevonden kon worden)


## /site/assets

Hier zie je de bestanden en mappen in de map Assets:

bestand/map                          |uitleg
-------------------------------------|-------------------------------------------
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;_thumbcache |Deze map gebruikt FlexyAdmin voor het opslaan van thumbnails van alle afbeeldingen. Hoef je niets zelf aan te doen.
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;downloads   |Dit is de standaard map waar gebruikers hun downloadbare bestanden in kunnen uploaden.
<span class="glyphicon glyphicon-folder-open"></span>&nbsp;pictures    |Dit is de standaard map waar gebruikers hun Foto's in kunnen uploaden. Je kunt zelf meerdere mappen aanmaken voor diverse soorten afbeeldingen.


   [1]: http://codeigniter.com/user_guide/general/helpers.html
   [2]: http://codeigniter.com/user_guide/general/models.html
