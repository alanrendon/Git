ALTER TABLE `llx_contab_operation_societe_fourn` ADD CONSTRAINT `id_societe_operation` FOREIGN KEY (`id_societe`) REFERENCES `llx_societe` (`rowid`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `llx_contab_operation_societe_fourn` ADD CONSTRAINT `id_operation_to_societe` FOREIGN KEY (`id_operation_societe`) REFERENCES `llx_contab_operation_societe` (`rowid`) ON DELETE CASCADE ON UPDATE CASCADE;

