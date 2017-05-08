<?php

include('includes/class.db.inc.php');
include('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');

$sql = '
	SELECT
		idarrendatario,
		idarrendador
	FROM
		rentas_arrendatarios
	WHERE
		tsbaja IS NULL
	ORDER BY
		idarrendador,
		idarrendatario
';

$result = $db->query($sql);

if ($result) {
	$sql = '';
	
	$idarrendador = NULL;
	foreach ($result as $rec) {
		if ($idarrendador != $rec['idarrendador']) {
			$idarrendador = $rec['idarrendador'];
			
			$arrendatario = 1;
		}
		
		$sql .= 'UPDATE rentas_arrendatarios SET arrendatario = ' . $arrendatario . ' WHERE idarrendatario = ' . $rec['idarrendatario'] . ";\n";
		
		$arrendatario++;
	}
	
	echo '<pre>' . $sql . '</pre>';
}

$sql = '
	SELECT
		idarrendatario,
		idarrendador
	FROM
		rentas_arrendatarios
	WHERE
		tsbaja IS NOT NULL
	ORDER BY
		idarrendador,
		idarrendatario
';

$result = $db->query($sql);

if ($result) {
	$sql = '';
	
	$idarrendador = NULL;
	foreach ($result as $rec) {
		if ($idarrendador != $rec['idarrendador']) {
			$idarrendador = $rec['idarrendador'];
			
			$arrendatario = 1;
		}
		
		$sql .= 'UPDATE rentas_arrendatarios SET arrendatario = ' . $arrendatario . ' WHERE idarrendatario = ' . $rec['idarrendatario'] . ";\n";
		
		$arrendatario++;
	}
	
	echo '<pre>' . $sql . '</pre>';
}

?>