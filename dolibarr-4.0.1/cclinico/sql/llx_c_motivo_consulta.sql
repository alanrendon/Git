CREATE TABLE IF NOT EXISTS `llx_c_motivo_consulta` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL,
  `description` text,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `llx_c_motivo_consulta` ADD `entity` int(11) DEFAULT '1';