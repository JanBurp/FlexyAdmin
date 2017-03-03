# Speciale velden

Enkele veldnamen hebben een speciale functie. Hieronder staan ze op een rij met hun uitleg. Een aantal zijn (vooralsnog) gereserveerd voor intern gebruik.

Algemene velden
---------------

**Veld**     |**MySQL**                        |**Uitleg**
-------------|-------------------------------- |----------
id           |INT, primary_key, auto-increment |Primary key. Noodzakelijk in elke tabel voor een goede werking.
uri          |VARCHAR                          |Dit veld wordt gebruikt in menu tabellen en kan een onderdeel vormen van de url van een pagina. Het _uri_ veld wordt automatisch gecreeërd door FlexyAdmin na het aanmaken of aanpassen van een rij. Het eerstvolgende _str__, _dat__ of _int__ veld wordt daarvoor als basis gebruikt. Als je wilt voorkomen dat een uri telkens wordt aangepast kun je **Table Info -> Freeze Uri** aanzetten.
order        |INT (6)                          |Dit veld kan gebruikt worden om de volgorde van rijen in een tabel te bepalen. In het admin deel van FlexyAdmin kun je de rijen eenvoudig op volgorde slepen. Deze velden worden dan aangepast in de tabel.Als _order_ wordt gebruikt in combinatie met _self_parent_ zal bij elke unieke _self_parent_ een eigen volgorde worden gemaakt. Dus dan kunnen er meerdere rijen zijn met dezelfde waarde in _order_, het onderscheid zit dan in _self_parent_.
self_parent  |INT                              |Dit veld verwijst naar een rij in de eigen tabel. _self_parent_ wordt gebruikt in menutabellen om een menu van meerder nivo's te kunnen maken. _self_parent_ verwijst dan naar de bovenliggende pagina. Op het hoogste nivo is _self_parent_ 0.In combinatie met _order_ kan zo een menu worden samengesteld met meerder nivo's en met een zelf te bepalen volgorde.
user         |                                 |gereserveerd
user_changed |INT                              |In dit veld wordt automatisch de user ingesteld die de laatste aanpassing heeft gedaan in de betreffende rij.
tme_last_changed|TIMESTAMP, on update CURRENT_TIMESTAMP|De tijd/datum van de laatste aanpassing.

Velden in een menu tabel
------------------------

**Veld**     |**MySQL**                        |**Uitleg**
-------------|-------------------------------- |----------
b_visible    |                                 | Zichtbaarheid van de pagina in het menu
b_redirect   |                                 | Of de pagina altijd geredirect moet worden, standaard naar de eerstvolgende onderliggende pagina
list_redirect|                                 | Een link waarnaar de pagina geredirect moet worden (als b_redirect = TRUE)
str_anchor   |                                 | Het anker deel van een link (#anker). Wordt bij een redirect achter de link geplaatst
