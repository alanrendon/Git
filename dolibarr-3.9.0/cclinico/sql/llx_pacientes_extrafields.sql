CREATE TABLE IF NOT EXISTS `llx_pacientes_extrafields` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fk_object` int(11) NOT NULL,
  `import_key` varchar(14) DEFAULT NULL,
  `sang_con` varchar(255) DEFAULT NULL,
  `nom_seg_con` varchar(255) DEFAULT NULL,
  `num_seg_con` varchar(255) DEFAULT NULL,
  `entity` int(11) DEFAULT '1',
  PRIMARY KEY (`rowid`),
  KEY `idx_pacientes_extrafields` (`fk_object`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;