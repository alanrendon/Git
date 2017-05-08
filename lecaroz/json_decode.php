<?php

include('includes/dbstatus.php');
include('includes/class.db.inc.php');

$db = new DBclass($dsn, 'autocommit=yes');

$result = $db->query("SELECT num_cia, tipo_serie, consecutivo, ob_response FROM facturas_electronicas WHERE ob_response != '' AND TRIM(uuid) = '' ORDER BY num_cia, tipo_serie, consecutivo");

foreach ($result as $row) {
	echo '<pre>' . print_r(json_decode(utf8_encode($row['ob_response'])), TRUE) . '</pre>';
}

