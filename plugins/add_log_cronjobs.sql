# This adds log_cronjobs, for simulating and calling cronjobs

CREATE TABLE `log_cronjobs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `str_job` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `tme_last_run` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;