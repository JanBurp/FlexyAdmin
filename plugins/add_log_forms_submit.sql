# This adds a log table wich can be used with forms to give a period with wich the form can't be filled again for the same ip address:
# Set these fields in config/forms.php for your fields:
# 
#  'restrict_this_ip_days' => 90,                                                           // days
#  'restrict_message'      => "Je hebt dit formulier al eerder ingevuld. Daarvoor dank.",   // message


CREATE TABLE `log_forms_submit` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `str_form` varchar(100) NOT NULL,
  `ip` varchar(45) NOT NULL DEFAULT '',
  `dat_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

