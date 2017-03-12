
CREATE TABLE IF NOT EXISTS  `llx_contab_polizas_docto` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `fk_poliza` int(11) NOT NULL,
  `nom_carpeta` varchar(30) NOT NULL,
  `archivo` varchar(50) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET FOREIGN_KEY_CHECKS=1;
