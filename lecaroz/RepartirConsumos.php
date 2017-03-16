<?php
include 'includes/class.db.inc.php';
include 'includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');

$condiciones = array();

$condiciones[] = 'codmp = 521';

if (isset($_REQUEST['num_cia']) && $_REQUEST['num_cia'] > 0) {
	$condiciones[] = 'num_cia = ' . $_REQUEST['num_cia'];
}

$sql = '
	SELECT
		num_cia,
		fecha,
		existencia,
		precio_unidad
	FROM
		historico_inventario
	WHERE
		codmp = 521
	ORDER BY
		num_cia,
		fecha
';

$result = $db->query($sql);

if ($result) {
	
	
	echo "<pre>$sql</pre>";
}