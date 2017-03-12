CREATE TABLE IF NOT EXISTS  `llx_contab_operation_societe` (
`rowid`  int NOT NULL AUTO_INCREMENT ,
`key_type_operation`  int NOT NULL ,
`key_type_societe`  int NOT NULL ,
PRIMARY KEY (`rowid`),
INDEX `key_type_operation` (`key_type_operation`) ,
INDEX `key_type_societe` (`key_type_societe`) 
)
;

