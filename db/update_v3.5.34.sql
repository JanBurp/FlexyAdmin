# Homepage text changed
UPDATE `tbl_menu` SET `txt_text` = '<p>Als je dit ziet is het je gelukt om FlexyAdmin te installeren en werkend te krijgen. <br />Je hebt nu een standaard-installatie van een zeer eenvoudige basis-site.</p>' WHERE `id` = '1';

# Change db revision
UPDATE `cfg_version` SET `str_version` = '3.5.34';
