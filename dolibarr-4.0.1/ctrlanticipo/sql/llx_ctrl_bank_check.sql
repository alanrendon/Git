
CREATE TABLE IF NOT EXISTS `llx_ctrl_bank_check` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(11) DEFAULT NULL,
  `fk_paiment` int(11) DEFAULT NULL COMMENT 'LLave del pago',
  `date_asign` date DEFAULT NULL COMMENT 'Fecha del cheque',
  `receptor` varchar(255) DEFAULT NULL COMMENT 'Receptor del cheque',
  `concept` text COMMENT 'Concepto del pago',
  `account_number` varchar(50) DEFAULT NULL COMMENT 'Numero de la cuenta',
  `number_check` varchar(50) DEFAULT NULL COMMENT 'Numero del cheque',
  `fk_user_create` int(11) DEFAULT NULL,
  `date_create` datetime DEFAULT NULL,
  `fk_user_modify` int(11) DEFAULT NULL,
  `date_modify` datetime DEFAULT NULL,
  `mode_print` tinyint(4) DEFAULT NULL,
  `statut` tinyint(4) DEFAULT NULL COMMENT '1: No impreso\r\n2: Ya fue impreso',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
