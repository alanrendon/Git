CREATE TABLE  IF NOT EXISTS `llx_eventos_consultas` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_paciente` int(11) DEFAULT NULL,
  `fk_evento` int(11) DEFAULT NULL,
  `fk_consulta` int(11) DEFAULT NULL,
  `entity` int(11) DEFAULT '1',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
