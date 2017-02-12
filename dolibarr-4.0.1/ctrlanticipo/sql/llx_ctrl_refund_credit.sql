
CREATE TABLE IF NOT EXISTS `llx_ctrl_refund_credit` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `ref` varchar(55) DEFAULT NULL,
  `fk_credit` int(11) DEFAULT NULL,
  `fk_soc` int(11) DEFAULT NULL COMMENT 'Proveedor',
  `date_apply` date DEFAULT NULL,
  `fk_paymen` int(11) DEFAULT NULL COMMENT 'Forma de pago',
  `fk_bank_account` int(11) DEFAULT NULL COMMENT 'Cuenta de banco (debito)',
  `num_paiment` varchar(50) DEFAULT NULL COMMENT 'Numero (cheque / transferencia)',
  `transfer` varchar(100) DEFAULT NULL COMMENT 'Emisor (Transmisor cheque/Transferencia)',
  `bank` varchar(100) DEFAULT NULL COMMENT 'Banco (Banco del cheque)',
  `note` text COMMENT 'Comentarios',
  `date_c` datetime DEFAULT NULL,
  `fk_user` int(11) DEFAULT NULL,
  `fk_bank_line` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
