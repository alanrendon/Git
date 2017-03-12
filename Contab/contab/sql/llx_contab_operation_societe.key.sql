ALTER TABLE `llx_contab_operation_societe` ADD CONSTRAINT `fk_key_type_operation` FOREIGN KEY (`key_type_operation`) REFERENCES `llx_contab_type_operation` (`rowid`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `llx_contab_operation_societe` ADD CONSTRAINT `fk_key_type_societe` FOREIGN KEY (`key_type_societe`) REFERENCES `llx_contab_type_societe` (`rowid`) ON DELETE CASCADE ON UPDATE CASCADE;

