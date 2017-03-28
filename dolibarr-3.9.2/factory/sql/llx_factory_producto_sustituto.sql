CREATE TABLE `llx_factory_producto_sustituto` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_product` int(11) NOT NULL DEFAULT '0',
  `fk_sustituto` int(11) NOT NULL DEFAULT '0',
  `entity` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rowid`)
);
