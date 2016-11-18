# Remove some config from cfg_configurations and put them in site/config/config.php

ALTER TABLE `cfg_configurations` DROP `b_logout_to_site`;
ALTER TABLE `cfg_configurations` DROP `b_query_urls`;
