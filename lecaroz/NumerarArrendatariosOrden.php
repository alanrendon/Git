<?php

include('includes/class.db.inc.php');
include('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');

$sql = '
	SELECT
		idarrendatario,
		idarrendador,
		bloque
	FROM
		rentas_arrendatarios
	WHERE
		tsbaja IS NOT NULL
	ORDER BY
		idarrendador,
		bloque,
		idarrendatario
';

$result = $db->query($sql);

if ($result) {
	$sql = '';
	
	$idarrendador = NULL;
	foreach ($result as $rec) {
		if ($idarrendador != $rec['idarrendador']) {
			$idarrendador = $rec['idarrendador'];
			
			$bloque = NULL;
		}
		
		if ($bloque != $rec['bloque']) {
			$bloque = $rec['bloque'];
			
			$orden = 1;
		}
		
		$sql .= 'UPDATE rentas_arrendatarios SET orden = ' . $orden . ' WHERE idarrendatario = ' . $rec['idarrendatario'] . ";\n";
		
		$orden++;
	}
	
	echo '<pre>' . $sql . '</pre>';
}

?>