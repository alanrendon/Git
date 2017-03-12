
CREATE TABLE IF NOT EXISTS  `llx_contab_dol_login` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `dol_login` varchar(40) NOT NULL,
  `login_key` varchar(250) NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `user_contab` (`dol_login`),
  UNIQUE KEY `login_key_contab` (`login_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET FOREIGN_KEY_CHECKS=1;
