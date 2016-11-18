#
# FlexyAdmin DB-Export 2015-02-07
#
# DATA TABLES: cfg_email
#


#
# TABLE STRUCTURE FOR: cfg_email
#

DROP TABLE IF EXISTS cfg_email;

CREATE TABLE `cfg_email` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `str_subject_nl` varchar(255) CHARACTER SET utf8 NOT NULL,
  `txt_email_nl` text CHARACTER SET utf8 NOT NULL,
  `str_subject_en` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `txt_email_en` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

INSERT INTO cfg_email (`id`, `key`, `str_subject_nl`, `txt_email_nl`, `str_subject_en`, `txt_email_en`) VALUES (1, 'test', 'Een test email van {site_title}', 0x3c703e4469742069732065656e20746573746d61696c2c207665727a6f6e64656e2076616e207b736974655f7469746c657d206f70207b736974655f75726c7d3c2f703e0a3c703e266e6273703b3c2f703e0a3c703e266e6273703b3c2f703e0a3c703e4e61616d207b6e616d657d3c2f703e0a3c703e266e6273703b3c2f703e0a3c703e42657374616174206e696574207b626573746161745f6e6965747d3c2f703e0a3c703e266e6273703b3c2f703e, 'test', 0x3c703e544553543c2f703e);
INSERT INTO cfg_email (`id`, `key`, `str_subject_nl`, `txt_email_nl`, `str_subject_en`, `txt_email_en`) VALUES (2, 'login_admin_new_register', 'Nieuw account aangevraagd voor {site_title}', 0x3c68313e45656e206e69657577206163636f756e742069732061616e6765767261616720646f6f72207b6964656e746974797d203c2f68313e0a3c703e4c6f6720696e206f6d2064652061616e76726161672074652062656f6f7264656c656e2e3c2f703e0a, 'New account asked for {site_title}', 0x3c68313e41206e6577206163636f756e74206973206265696e672061736b656420666f72206279207b6964656e746974797d203c2f68313e0a3c703e4c6f6720696e20746f2064656e79206f72206163636570742074686520726567697374726174696f6e2e3c2f703e);
INSERT INTO cfg_email (`id`, `key`, `str_subject_nl`, `txt_email_nl`, `str_subject_en`, `txt_email_en`) VALUES (3, 'login_accepted', 'Account voor {site_title} geaccepteerd', 0x3c68313e4163636f756e742061616e767261616720766f6f72207b6964656e746974797d206973206765616363657074656572642e3c2f68313e0a3c703e55206b756e74206e7520696e6c6f6767656e2e3c2f703e0a, 'Account for {site_title} accepted', 0x3c68313e4163636f756e7420726567697374726174696f6e20666f72207b6964656e746974797d2069732061636365707465642e3c2f68313e0a3c703e596f752063616e206c6f67696e206e6f772e3c2f703e);
INSERT INTO cfg_email (`id`, `key`, `str_subject_nl`, `txt_email_nl`, `str_subject_en`, `txt_email_en`) VALUES (4, 'login_activate', 'Activeer account voor {site_title}', 0x3c68313e41637469766565722064652061616e6d656c64696e6720766f6f72207b6964656e746974797d3c2f68313e0a3c703e4b6c696b206f70203c6120687265663d227b736974655f75726c7d2f7b61637469766174655f7572697d3f69643d7b757365725f69647d26616d703b61637469766174696f6e3d7b61637469766174696f6e7d223e64657a65206c696e6b3c2f613e206f6d206a65206163636f756e742074652061637469766572656e2e3c2f703e, 'Activate your account for {site_title}', 0x3c68313e4163746976617465206163636f756e7420666f72207b6964656e746974797d3c2f68313e0a3c703e506c6561736520636c69636b203c6120687265663d227b736974655f75726c7d2f7b61637469766174655f7572697d3f69643d7b757365725f69647d26616d703b61637469766174696f6e3d7b61637469766174696f6e7d223e74686973206c696e6b3c2f613e20746f20616374697661746520796f7572206163636f756e742e3c2f703e);
INSERT INTO cfg_email (`id`, `key`, `str_subject_nl`, `txt_email_nl`, `str_subject_en`, `txt_email_en`) VALUES (5, 'login_deny', 'Account aanvraag voor {site_title} afgewezen', 0x3c68313e4166676577657a656e206163636f756e7420766f6f72207b6964656e746974797d3c2f68313e0a3c703e55772061616e767261616720766f6f722065656e206163636f756e74206973206166676577657a656e2e3c2f703e, 'Account for {site_title} denied', 0x3c68313e44656e696564206163636f756e7420666f72207b6964656e746974797d3c2f68313e0a3c703e596f7572206163636f756e742069732064656e6965642e3c2f703e);
INSERT INTO cfg_email (`id`, `key`, `str_subject_nl`, `txt_email_nl`, `str_subject_en`, `txt_email_en`) VALUES (6, 'login_forgot_password', 'Nieuw wachtwoord voor {site_title}', 0x3c68313e4e69657577207761636874776f6f72642061616e76726167656e20766f6f72207b6964656e746974797d3c2f68313e0a3c703e4b6c696b2068696572206f6d203c6120687265663d227b736974655f75726c7d7b666f72676f7474656e5f70617373776f72645f7572697d3f636f64653d7b666f72676f7474656e5f70617373776f72645f636f64657d223e7761636874776f6f726420746520726573657474656e3c2f613e2e3c2f703e, 'New password for {site_title}', 0x3c68313e4e65772070617373776f7264207265717565737420666f72207b6964656e746974797d3c2f68313e0a3c703e436c69636b206f6e203c6120687265663d227b736974655f75726c7d7b666f72676f7474656e5f70617373776f72645f7572697d3f636f64653d7b666f72676f7474656e5f70617373776f72645f636f64657d223e746f2072657374657420796f75722070617373776f72643c2f613e2e3c2f703e);
INSERT INTO cfg_email (`id`, `key`, `str_subject_nl`, `txt_email_nl`, `str_subject_en`, `txt_email_en`) VALUES (7, 'login_new_password', 'Nieuwe inloggegevens voor {site_title}', 0x3c68313e4a65206e696575776520696e6c6f6767676576656e7320766f6f72207b736974655f7469746c657d3a3c2f68313e0a3c703e4765627275696b65723a207b6964656e746974797d3c6272202f3e205761636874776f6f72643a7b70617373776f72647d3c2f703e, 'New login for {site_title}', 0x3c68333e596f7520676f7420616e206163636f756e742e3c2f68333e0a3c703e4c6f67696e20776974682074686573652073657474696e67733a3c2f703e0a3c703e557365726e616d65203a207b6964656e746974797d3c6272202f3e50617373776f7264203a207b70617373776f72647d3c2f703e);
INSERT INTO cfg_email (`id`, `key`, `str_subject_nl`, `txt_email_nl`, `str_subject_en`, `txt_email_en`) VALUES (8, 'login_new_account', 'Welkom en inloggegevens voor {site_title}', 0x3c68313e57656c6b6f6d2062696a207b736974655f7469746c657d3c2f68313e0a3c703e486965726f6e64657220737461616e206a6520696e6c6f676765676576656e732e3c2f703e0a3c703e4765627275696b65723a207b6964656e746974797d3c6272202f3e205761636874776f6f72643a7b70617373776f72647d3c2f703e, 'New login for {site_title}', 0x3c68313e57656c636f6d65206174207b736974655f7469746c657d3c2f68313e0a3c703e4c6f67696e20776974682074686573652073657474696e67733a3c2f703e0a3c703e557365726e616d65203a207b6964656e746974797d3c6272202f3e50617373776f7264203a207b70617373776f72647d3c2f703e);


