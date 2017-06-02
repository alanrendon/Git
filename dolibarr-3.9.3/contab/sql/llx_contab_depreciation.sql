
CREATE TABLE IF NOT EXISTS `llx_contab_depreciation` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `clave` varchar(255) DEFAULT NULL,
  `entity` int(11) DEFAULT NULL,
  `descripcion` text,
  `date_purchase` datetime DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `market_value` decimal(10,2) DEFAULT NULL,
  `type_active` int(11) DEFAULT NULL,
  `localitation` text,
  `department` text,
  `serial_number` int(11) DEFAULT NULL,
  `date_init_purchase` datetime DEFAULT NULL,
  `depreciation_rate` decimal(10,2) DEFAULT NULL,
  `depreciation_accumulated` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
