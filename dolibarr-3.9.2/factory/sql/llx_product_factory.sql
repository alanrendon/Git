
CREATE TABLE IF NOT EXISTS  `llx_product_factory` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_product_father` int(11) NOT NULL DEFAULT '0',
  `fk_product_children` int(11) NOT NULL DEFAULT '0',
  `pmp` double(24,8) DEFAULT '0.00000000',
  `price` double(24,8) DEFAULT '0.00000000',
  `qty` double DEFAULT NULL,
  `globalqty` int(11) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `treatment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `uk_product_factory` (`fk_product_father`,`fk_product_children`),
  KEY `idx_product_factory_fils` (`fk_product_children`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=latin1;
