
CREATE TABLE IF NOT EXISTS `llx_union_bt` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_tercero` int(11) DEFAULT NULL,
  `fk_cuenta` int(11) DEFAULT NULL,
  `date_c` datetime DEFAULT NULL,
  `fk_user_create` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
