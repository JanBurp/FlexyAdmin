# Modules & Plugins installeren

Je kunt eenvoudige modules en plugins van anderen installeren:

- Ga naar de plugin_install_plugin door in de URL dit te typen: ../_admin/plugin/install_plugin
- Kies het .zip bestand met de plugin die je nodig hebt.
- Daarna volgt vanzelf de installatie
- Het kan zijn dat je nog andere dingen moet instellen voor de plugin of module, dit lees je in de documentatie van de module/plugin.

## Maak een .zip van je eigen module/plugin

Als je zelf modules of plugins hebt gemaakt (zie bij Uitbreiden) kun je er eenvoudig een zip bestand van maken zodat anderen je module/plugin kunnen instaleren:

- Ga naar de plugin_create_plugin door in de URL dit te typen: ../_admin/plugin/create_plugin
- Kies je module/plugin
- Controleer in het volgende scherm of alle bestanden worden toegevoegd (zo niet, kijk hieronder)
- Geef eventueel nog een andere naam: begin met je eigen initialen, en daarna de naam van de module/plugin

### Meer bestanden toevoegen aan je module/plugin

Standaard worden deze bestanden toegevoegd:

- Het config bestand met dezelfde naam in 'site/config'
- Alle taalbestanden met dezelfde naam (en de suffix _lang) in 'site/language/xx/'
- De module/plugin zelf in 'site/libraries'
- Een view met dezelfde naam 'site/views'

Als je meer bestanden wilt toevoegen:

- Zorg ervoor dat je plugin/module een bijbehorden config bestand heeft in de map 'site/config'
- Voeg deze regels toe aan de config (en pas het aan):

        $config['_files']=array(
          'db/example.sql'
        );
