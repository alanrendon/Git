ALTER TABLE `llx_user_extrafields` ADD `med001` tinyint(4);
ALTER TABLE `llx_user_extrafields` ADD `cedula_prof` varchar(255);
ALTER TABLE `llx_pacientes_extrafields` ADD `sang_con` varchar(255);
ALTER TABLE `llx_pacientes_extrafields` ADD `nom_seg_con` varchar(255);
ALTER TABLE `llx_pacientes_extrafields` ADD `num_seg_con` varchar(255);
ALTER TABLE `llx_pacientes_extrafields` ADD `est_civ` text;

--INSERT INTO `llx_extrafields` VALUES ('med001', '1', 'user', '2016-10-27 17:10:10', 'Médico', 'boolean', '', '0', '1', null, '1', '1', 'a:1:{s:7:\"options\";a:1:{s:0:\"\";N;}}', '0');
INSERT INTO `llx_extrafields` (
	`name`,
	`entity`,
	`elementtype`,
	`tms`,
	`label`,
	`type`,
	`size`,
	`fieldunique`,
	`fieldrequired`,
	`perms`,
	`pos`,
	`alwayseditable`,
	`param`,
	`list`
)
VALUES
	(
		'med001',
		'0',
		'user',
		'2016-10-27 17:10:10',
		'Médico',
		'boolean',
		'',
		'0',
		'0',
		NULL,
		'1',
		'1',
		'a:1',
		'0'
	);

INSERT INTO `llx_extrafields` (
	`name`,
	`entity`,
	`elementtype`,
	`tms`,
	`label`,
	`type`,
	`size`,
	`fieldunique`,
	`fieldrequired`,
	`perms`,
	`pos`,
	`alwayseditable`,
	`param`,
	`list`
)
VALUES
	(
		'cedula_prof',
		'0',
		'user',
		'2016-10-27 17:10:10',
		'Cédula',
		'varchar',
		'255',
		'0',
		'0',
		NULL,
		'2',
		'1',
		'a:1',
		'0'
	);
INSERT INTO `llx_extrafields` (
	`name`,
	`entity`,
	`elementtype`,
	`tms`,
	`label`,
	`type`,
	`size`,
	`fieldunique`,
	`fieldrequired`,
	`perms`,
	`pos`,
	`alwayseditable`,
	`param`,
	`list`
)
VALUES
	(
		'sang_con',
		'0',
		'pacientes',
		'2016-10-27 17:10:10',
		'Tipo de Sangre',
		'varchar',
		'255',
		'0',
		'0',
		NULL,
		'0',
		'1',
		'a:1',
		'0'
	);
INSERT INTO `llx_extrafields` (
	`name`,
	`entity`,
	`elementtype`,
	`tms`,
	`label`,
	`type`,
	`size`,
	`fieldunique`,
	`fieldrequired`,
	`perms`,
	`pos`,
	`alwayseditable`,
	`param`,
	`list`
)
VALUES
	(
		'nom_seg_con',
		'0',
		'pacientes',
		'2016-10-27 17:10:10',
		'Nombre del Seguro',
		'varchar',
		'255',
		'0',
		'0',
		NULL,
		'0',
		'1',
		'a:1',
		'0'
	);
INSERT INTO `llx_extrafields` (
	`name`,
	`entity`,
	`elementtype`,
	`tms`,
	`label`,
	`type`,
	`size`,
	`fieldunique`,
	`fieldrequired`,
	`perms`,
	`pos`,
	`alwayseditable`,
	`param`,
	`list`
)
VALUES
	(
		'num_seg_con',
		'0',
		'pacientes',
		'2016-10-27 17:10:10',
		'Número de Seguro',
		'varchar',
		'255',
		'0',
		'0',
		NULL,
		'0',
		'1',
		'a:1',
		'0'
	);
INSERT INTO `llx_const`
	(
		`name`,
		`entity`,
		`value`,
		`type`,
		`visible`,
		`note`,
		`tms`
	)
VALUES
	(
		'SOCIETE_CODECONSULTA_ADDON',
		'0',
		'mod_codeconsultas_monkey',
		'chaine',
		'0',
		'',
		'2016-11-13 18:05:20'
	);
INSERT INTO `llx_extrafields`
 (
	`name`,
	`entity`,
	`elementtype`,
	`tms`,
	`label`,
	`type`,
	`size`,
	`fieldunique`,
	`fieldrequired`,
	`perms`,
	`pos`,
	`alwayseditable`,
	`param`,
	`list`
)
VALUES
(
	'est_civ',
	'0',
	'pacientes',
	'2016-12-14 18:05:09',
	'Estado Civil',
	'select',
	'',
	'0',
	'0',
	NULL,
	'0',
	'0',
	'',
	'0'
);
