<?php

include('includes/class.db.inc.php');
include('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');

$sql = '
	SELECT
		id
	FROM
		catalogo_trabajadores
	WHERE
		num_cia >= 900
	ORDER BY
		num_cia,
		ap_paterno,
		ap_materno,
		nombre
';

$result = $db->query($sql);

$sql = '';

if ($result) {
	$num_emp = 1;
	
	foreach ($result as $rec) {
		$sql .= 'UPDATE catalogo_trabajadores SET num_emp = ' . $num_emp . ' WHERE id = ' . $rec['id'] . ";\n";
		
		$num_emp++;
	}
}

echo "<pre>$sql</pre>";
