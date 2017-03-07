# Gebruikersrechten

Er kunnen meerdere gebruikers met FlexyAdmin werken. Ze kunnen zelfs verschillende rechten hebben.

## Rechten en user_groups

Rechten van gebruikers moeten worden ingesteld in een _user_group_ bij **User Groups**. Standaard zijn de volgende user_groups bekend:

str_name    |str_description      |rights                   |b_all_users  |b_backup |b_tools  |b_delete |b_add  |b_edit |b_show
------------|---------------------|-------------------------|-------------|---------|---------|---------|-------|-------|------
super_admin |Super Administrator  |*                        |1            |1        |1        |1        |1      |1      |1
admin       |Administrator        |tbl_*|media_*|cfg_users  |0            |1        |1        |1        |1      |1      |1
user        |User                 |tbl_*|media_*            |0            |0        |0        |1        |1      |1      |1
visitor     |Visitor              |tbl_*|media_*            |0            |0        |0        |0        |0      |0      |0

Hieronder een uitleg wat elke veld betekent:

- str_name - Naam van de user group
- str_description - Omschrijving van de user group (lange naam)
- rights - Voor welke tabellen en media mappen de rechten gelden ( * - alle | tbl_* - alle tabellen met prefix tbl_ etc.)
- b_all_users - gebruiker kan ook rijen van andere gebruikers aanpassen als rijen een eigenaar hebben (zie verderop)
- b_backup - gebruiker kan een backup van de database maken| en ook een backup database importeren (restore)
- b_tools - gebruiker kan andere tools gebruiken| zoals zoeken/vervangen in de database
- b_delete - gebruiker kan velden van de ingestelde tabellen verwijderen
- b_add - gebruiker kan velden aan de ingestelde tabellen toevoegen
- b_edit - gebruiker kan velden van de ingestelde tabellen aanpassen
- b_show - gebruiker kan de ingestelde tabellen bekijken

## Users

Gebruikers worden aangemaakt bij **Users** en elke gebruiker hoort bij een _user_group_.
Standaard zijn de volgende users bekend:

str_username  |id_user_group  |email_email  |b_active|str_language 
--------------|---------------|-------------|--------|-------------
admin         |super_admin    |             |        |nl           
user          |user           |             |        |nl           

Uitleg van de (zichtbare) velden:

- str_username - Naam van de gebruiker
- id_user_group - User group waar de gebruiker bij hoort, dit bepaald de rechten van de gebruiker
- email_email - Email adres van gebruiker. Kan (in de toekomst) gebruikt worden als de gebruiker zijn/haar wachtwoord is vergeten
- b_active - Of de user al eens heeft ingelogd of niet
- str_language - De taal die de gebruiker graag heeft in het admin deel van FlexyAdmin (op dit moment alleen nl en en)

## Users koppelen aan rijen in een tabel of media

Als bovenstaande rechten nog niet genoeg zijn kan elke rij van een tabel aan een user worden gekoppeld.
Alleen die user (en users waar `b_all_users=1`) kan die rij zien, verwijderen en aanpassen.

Je kunt dit doen door het veld 'user' (INT) aan een tabel toe te voegen.
Als je hetzelfde wilt bereiken voor bestanden kun je het 'user' veld toevoegen aan res_assets. En de optie 'restricted' aanzetten in `site/config/assets.php`


# Flexy_Auth

Wil je in je code (plugins/modules) iets met de rechten van gebruikers doen, gebruik dan de standaard library Flexy_auth.

## Laden van Flexy_auth

`$this->load->library('flexy_auth');`

## Gebruik van Flexy_auth

Zie voor de mogelijkheden de inline documentatie: `sys/flexyadmin/libraries/Flexy_auth.php`

## Ion_auth

Flexy_auth is gebaseerd op Ion_auth.
Je kunt dus alles wat Ion_auth kan ook met Flexy_auth.
