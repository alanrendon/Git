CREATE TABLE IF NOT EXISTS `llx_mc_complements` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(255) DEFAULT NULL,
  `fk_member` int(11) DEFAULT NULL,
  `id_provider` int(11) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `home_phone` varchar(255) DEFAULT NULL,
  `m_phone1` varchar(255) DEFAULT NULL,
  `q_phone1` tinyint(4) DEFAULT NULL,
  `m_phone2` varchar(255) DEFAULT NULL,
  `q_phone2` tinyint(4) DEFAULT NULL,
  `primary_phone` int(11) DEFAULT NULL,
  `temperature` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
