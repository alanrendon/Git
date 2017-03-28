CREATE TABLE `llx_factory_object_object` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_order_origen` int(11) NOT NULL DEFAULT '0',
  `fk_order_clone` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rowid`)
);
