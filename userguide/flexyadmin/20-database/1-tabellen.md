# Tabellen

FlexyAdmin kent vier soorten tabellen. Hieronder het overzicht met hun prefixen.

**Prefix**|**Voorbeeld**      |**Uitleg**
----------|-------------------|---------------
tbl_      |tbl_menu tbl_links |Een standaard tabel.
rel_      |rel_menu__links    |Relatietabel. Zorgt voor een *many_to_many* koppelling tussen twee tabellen. De naam van een relatietabel is alsvolgt opgebouwd: *rel_naam1__naam2*. Kijk bij '<a href="handleiding_voor_webbouwers/database/relaties_met_ander_tabellen">Relaties met andere tabellen</a>' voor meer informatie.
cfg_      |cfg_users          |Configuratietabellen die nodig zijn om FlexyAdmin te laten werken.
log_      |log_stats          |Log tabellen. Voor bijvoorbeeld het bijhouden van statistieken
res_      |res_assets         |Resultaat tabellen. Dit zijn standaard tabellen die  niet aangepast kunnen worden door de gebruiker, maar die worden gegenereerd door FlexyAdmin of een plugin.
      
*/