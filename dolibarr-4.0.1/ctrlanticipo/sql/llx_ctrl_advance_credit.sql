CREATE TABLE IF NOT EXISTS `llx_ctrl_advance_credit` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_advance` int(11) DEFAULT NULL COMMENT 'Llave foranea de los anticipos',
  `fk_soc` int(11) DEFAULT NULL,
  `date_c` datetime DEFAULT NULL COMMENT 'fecha de creación',
  `date_split` datetime DEFAULT NULL COMMENT 'Fecha de division',
  `date_asign` datetime DEFAULT NULL COMMENT 'fecha de asignacion a diferente proveedor',
  `import` decimal(10,2) DEFAULT NULL COMMENT 'importe sin iva',
  `fk_tva` int(11) DEFAULT NULL COMMENT 'llave foranea IVA',
  `total_import` decimal(10,2) DEFAULT NULL COMMENT 'Importe total con IVA',
  `fk_user_agree` int(11) DEFAULT NULL COMMENT 'Usuario que creo o dividio el credito',
  `statut` tinyint(4) DEFAULT NULL COMMENT '1: pendiente de pago,  \r\n2: consumido, \r\n3: Reembolsado\r\n4: Desactivado',
  `fk_brother_credit` int(11) DEFAULT NULL,
  `fk_parent` int(11) DEFAULT NULL COMMENT '1: Credito padre\r\n0: Credito hijo',
  `fk_user_asign` int(11) DEFAULT NULL COMMENT 'Usuario que asigno el credito a otro proveedor',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


