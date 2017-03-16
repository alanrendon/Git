<?php

include('includes/class.db.inc.php');
include('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');

$sql = '
	SELECT
		tablename
	FROM
		pg_tables
	WHERE
		schemaname = \'public\'
	ORDER BY
		tablename
';
$tablas = $db->query($sql);

if ($tablas) {
	$querys = '';
	
	foreach ($tablas as $tabla) {
		$sql = '
			SELECT
				column_name
			FROM
				information_schema.key_column_usage
			WHERE
				table_name = \'' . $tabla['tablename'] . '\'
		';
		$columna = $db->query($sql);
		
		if ($columna) {
			$sql = '
				SELECT
					pg_get_serial_sequence(\'"' . $tabla['tablename'] . '"\', \'' . $columna[0]['column_name'] . '\')
			';
			$secuencia = $db->query($sql);
			
			if ($secuencia/* && $secuencia[0]['pg_get_serial_sequence'] != ''*/) {
				$querys .= 'SELECT setval(\'' . $secuencia[0]['pg_get_serial_sequence'] . '\', (SELECT MAX("' . $columna[0]['column_name'] . '") FROM "' . $tabla['tablename'] . '"));' . "\n";
			}
		}
	}
	
	echo "<pre>$querys</pre>";
}

?>
