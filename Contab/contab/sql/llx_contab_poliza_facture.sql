CREATE TABLE IF NOT EXISTS  `llx_contab_poliza_facture` (
`rowid`  int NOT NULL AUTO_INCREMENT ,
`id_facture`  int NOT NULL ,
`id_poliza`  int NOT NULL ,
PRIMARY KEY (`rowid`),
INDEX `facture_foreing_poliza` (`id_facture`) ,
INDEX `poliza_foreing_facture` (`id_poliza`)
)
;
