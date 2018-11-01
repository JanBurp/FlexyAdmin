# Relaties

FlexyAdmin ondersteund relaties tussen tabellen. Hier volgt de uitleg van twee
relatievormen en hoe FlexyAdmin ze behandeld.

## Veel op één relaties (many_to_one)

Dit kun je zien als het maken van een keuzetabel.

Stel je hebt een tabel met allemaal links erin: _tbl_links_ en je wilt deze
gebruiken als een keuzetabel vanuit een tabel met alle pagina's van je site:
_tbl_menu_.

Je maakt dan in _tbl_menu_ een foreign key aan: _id_links_. Vanuit een rij in
_tbl_menu _kan nu verwezen worden naar een rij in _tbl_links_.

FlexyAdmin zal in het admin deel bij het tonen van een rij uit _tbl_menu_ op
de plaats van het veld _id_links_ een keuzeveld laten zien waarin een keuze
gemaakt kan worden uit _tbl_links_.

## Veel op veel relaties (many_to_many)

Dit kun je zien als een keuzetabel waar het mogelijk is om meerdere keuzes te
selecteren.

Stel je wilt in plaats van bij bovenstaand voorbeeld niet een maar meerdere
links kunnen koppelen aan een pagina. Dan heb je een tussentabel nodig om
meerdere relaties te kunnen leggen. Deze relatie tabel zou in ons voorbeeld zo
gaan heten: _rel_menu__links_. In deze tabel bestaan naast een primary key
(_id_) twee foreign keys: _id_menu_ en _id_links_.

FlexyAdmin zal nu bij het tonen van een rij uit _tbl_menu_ een keuzeveld laten
zien waar het mogelijk is om meerdere keuzes te maken uit _tbl_links_.

## Naamgeving foreign keys en relatietabellen

FlexyAdmin kan tabellen automatisch koppelen als de foreign keys voldoen aan
de volgende naamgeving: _id_tabel_. Waar _tabel_ staat voor de naam van tabel
waarnaar verwezen wordt zonder de eigen prefix van die tabel. Dus een foreign
key die moet verwijzen naar _tbl_links_ heet _id_links_. En een foreign key
die verwijst naar _tbl_menu_ heet _id_menu_.

FlexyAdmin kan de koppeling van veel op veel relaties ook automatisch leggen
als de relatietabellen die daarvoor nodig zijn op de volgende manier worden
genoemd: _rel_tabel1__tabel2_. Waar _tabel1_ en _tabel2_ de namen van de
tabellen zijn waar de relatie tussen wordt gelegd, zonder hun eigen prefixen.

Zoals hierboven al is genoemd bevat een relatietabel drie velden: een primary
key en twee foreign keys.