# Velden

De velden die gebruikt worden door FlexyAdmin hebben prefixen in hun namen. Om twee redenen:

- Aan de naam is meteen te zien wat voor gegevens er in het veld zit.
- FlexyAdmin gebruikt de prefix om te bepalen hoe het veld in het CMS deel wordt getoond en werkt.

In de tabel hieronder staan alle prefixen op een rij, wat ze doen in de CMS en hoe ze in MySQL moeten worden aangemaakt.
Als een veld geen prefix heeft wordt het als een *str_* gezien.

Prefixen voor database velden
-----------------------------


**Prefix**      |**Voorbeeld**          |**MySQL**    |**CMS veld**   
----------------|-----------------------|-------------|---------------
id_             |id_link                |INT          |Foreign key. Is een verwijzing naar een rij in een andere tabel. Komt in de CMS tevoorschijn als keuzelijst. Voorbeeld: *id_links* verwijst naar een rij in *tbl_links*. Zie bij <a href="index.html#section_relaties">Relaties met andere tabellen</a>.
self_           |self_parent            |INT          |Een verwijzing naar een rij in dezelfde tabel. Zie bijvoorbeeld <a href="index.html#section_speciale_velden">*self_parent*</a>
dat_, date_     |dat_date               |DATE         |Datumveld. Komt in CMS tevoorschijn als datum widget. Vult standaard huidige datum in bij nieuw item.
tme_, datetime_ |tme_time               |DATETIME     |Zie *dat_*, maar nu ook velden voor tijd.
time_           |time_time              |TIME_        |Idem maar dan alleen maar tijd
int_            |int_number             |INT          |Een getal, komt als normaal veld tevoorschijn in CMS
dec_            |dec_price              |DECIMAL      |Voor getallen met decimalen zoals een prijs.
b_, is_         |b_visible              |TINYINT      |Boolean. Komt in CMS tevoorschijn als vink (aan/uit).
str_            |str_title              |VARCHAR      |Een stringveld, komt tevoorschijn als lang veld in CMS
url_            |url_link               |VARCHAR      |Een veld met URL.
email_          |email_email            |VARCHAR      |Een veld met een emailadres
txt_            |txt_text               |TEXT         |Een groot tekst veld. In CMS als HTML editor te zien.
stx_            |stx_description        |TEXT         |Een groot teksveld (geen HTML editor).
pwd_            |pwd_password           |VARCHAR      |Paswoord veld. Geen zichtbare tekens.
gpw_            |gpw_password           |VARCHAR      |Idem, maar met een knop ernaast om automatisch paswoord te genereren.
media_          |media_foto             |VARCHAR      |Een bestandsnaam. Via **Media Info** gekoppeld aan een assets map.FlexyAdmin toont dit als een keuzeveld, met als keuze alle bestanden in de assets map.Als het om afbeeldingen gaat kan het ook getoond worden als thumbnails.Zie bij <a href="index.html#section_afbeeldingen_en_bestanden">Afbeeldingen en bestanden</a>.
medias_         |medias_fotos           |VARCHAR      |idem, maar nu kunnen er meerdere gekozen worden
rgb_            |rgb_color              |VARCHAR(7)   |Een HTML kleurcode. FlexyAdmin toont een kleurkiezer.
list_           |list_link, list_links  |VARCHAR      |Dit veld verwijst naar een keuze uit uit &eacute;&eacute;n van de javascript list bestanden die FlexyAdmin genereerd (*img_list.js, link_list.js, media_list.js en embed_list.js*). Als je dit veld *list_link* noemt toont FlexyAdmin een keuzeveld met alle links uit *link_list.js*. Als je een keuzeveld wilt waar meerdere links gekozen kunnen worden voeg je een 's' toe aan het eind van de naam: *list_links*. Zie bij <a href="index.html#section_links_en_downloads">Links</a>.
ip_             |ip_ip                  |VARCHAR(15)  |Bevat een ip adres

Voor alle velden geld dat de lengte en in sommige gevallen het type van het MySQL data type zelf gekozenkan worden. Dus ipv INT kan ook SMALLINT of BIGINT worden gebruikt. Of ipv VARCHAR kan ook TEXT worden gebruikt.