<?php
include 'includes/class.db.inc.php';
include 'includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');

$sql = '
	SELECT
		rfc,
		num_cia,
		razon_social
	FROM
		catalogo_companias
	WHERE
		num_cia <= 800
		AND rfc IS NOT NULL
		AND TRIM(rfc) != \'\'
	ORDER BY
		rfc,
		num_cia
';

$result = $db->query($sql);

if ($result) {
	$rfc = '';
	
	$data = array();
	
	foreach ($result as $rec) {
		if ($rfc != $rec['rfc']) {
			$rfc = $rec['rfc'];
			
			$data[$rfc] = array(
				'nombre' => $rec['razon_social'],
				'cias'   => array()
			);
		}
		
		$data[$rfc]['cias'][] = $rec['num_cia'];
	}
	
	$string = '"NOMBRE","RFC","COMPAÑIAS"' . "\r\n";
	
	foreach ($data as $rfc => $d) {
		$string .= '"' . $d['nombre'] . '","' . $rfc . '","' . implode('","', $d['cias']) . '"' . "\r\n";
	}
	
	header('Content-Type: application/download');
	header('Content-Disposition: attachment; filename="cias.csv"');
	
	echo $string;
}