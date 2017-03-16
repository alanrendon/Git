<?php

include('includes/class.db.inc.php');
include('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');

$sql = '
	SELECT
		idlocal,
		idarrendador
	FROM
		rentas_locales
	WHERE
		tsbaja IS NULL
	ORDER BY
		idarrendador,
		idlocal
';

$result = $db->query($sql);

if ($result) {
	$sql = '';
	
	$idarrendador = NULL;
	foreach ($result as $rec) {
		if ($idarrendador != $rec['idarrendador']) {
			$idarrendador = $rec['idarrendador'];
			
			$local = 1;
		}
		
		$sql .= 'UPDATE rentas_locales SET local = ' . $local . ', alias_local = \'LOCAL ' . $local . '\' WHERE idlocal = ' . $rec['idlocal'] . ";\n";
		
		$local++;
	}
	
	echo '<pre>' . $sql . '</pre>';
}

$sql = '
	SELECT
		idlocal,
		idarrendador
	FROM
		rentas_locales
	WHERE
		tsbaja IS NOT NULL
	ORDER BY
		idarrendador,
		idlocal
';

$result = $db->query($sql);

if ($result) {
	$sql = '';
	
	$idarrendador = NULL;
	foreach ($result as $rec) {
		if ($idarrendador != $rec['idarrendador']) {
			$idarrendador = $rec['idarrendador'];
			
			$local = 1;
		}
		
		$sql .= 'UPDATE rentas_locales SET local = ' . $local . ', alias_local = \'LOCAL ' . $local . '\' WHERE idlocal = ' . $rec['idlocal'] . ";\n";
		
		$local++;
	}
	
	echo '<pre>' . $sql . '</pre>';
}

?>