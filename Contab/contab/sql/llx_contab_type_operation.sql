CREATE TABLE IF NOT EXISTS  `llx_contab_type_operation` (
`rowid`  int NOT NULL AUTO_INCREMENT ,
`key`  varchar(10) NOT NULL ,
`name`  varchar(100) NOT NULL ,
PRIMARY KEY (`rowid`),
UNIQUE INDEX `llave_tipo_operation` (`key`) 
)
;
