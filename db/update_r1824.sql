# Adding res_media_files for keeping all media info in a table and some extra title field
CREATE TABLE `res_media_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL,
  `str_type` varchar(10) NOT NULL DEFAULT '',
  `str_title` varchar(255) NOT NULL,
  `dat_date` date NOT NULL,
  `int_size` int(11) NOT NULL,
  `int_img_width` int(11) NOT NULL,
  `int_img_height` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


# Change db revision
UPDATE `cfg_configurations` SET `str_revision` = '1824';

