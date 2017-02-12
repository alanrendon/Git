ALTER TABLE `llx_bank` ADD `inv` tinyint(4) DEFAULT 0;
ALTER TABLE `llx_societe_extrafields` ADD `inv001` tinyint(4);
ALTER TABLE `llx_societe_extrafields` ADD `inv002` double(24,8);
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
	'inv001',
	'1',
	'societe',
	'2016-11-17 17:10:10',
	'Inversionista',
	'boolean',
	'',
	'0',
	'0',
	NULL,
	'0',
	'1',
	'a:1',
	'0'
);

INSERT INTO `llx_extrafields`(
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
VALUES(
	'inv002',
	'1',
	'societe',
	'2016-11-17 17:23:03',
	'Porcentaje Inverción',
	'double',
	'24,8',
	'0',
	'0',
	NULL,
	'1',
	'0',
	'a:1',
	'0'
);

