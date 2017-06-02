CREATE TABLE IF NOT EXISTS  `llx_contab_cat_iva` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `id_cuenta` int(11) DEFAULT NULL,
  `porcentaje` decimal(10,2) DEFAULT NULL,
  `id_user_create` int(11) DEFAULT NULL,
  `id_user_update` int(11) DEFAULT NULL,
  `date_create` datetime DEFAULT NULL,
  `date_update` datetime DEFAULT NULL,
  PRIMARY KEY (`rowid`)
);
