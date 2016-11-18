# Pas cfg_session aan (voor update naar CodeIgniter 3.0)
DROP TABLE `cfg_sessions`;
CREATE TABLE IF NOT EXISTS `cfg_sessions` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
  `data` blob NOT NULL,
  PRIMARY KEY (id),
  KEY `cfg_sessions_timestamp` (`timestamp`)
);
ALTER TABLE cfg_sessions ADD CONSTRAINT cfg_sessions_id_ip UNIQUE (id, ip_address);

# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '3067';




