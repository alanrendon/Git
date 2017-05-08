<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$result = $db->query("SELECT * FROM estado_cuenta WHERE cod_mov = 35 AND cuenta = 2 AND fecha = '31/03/2006' ORDER BY fecha");
$cargos = $db->query("SELECT * FROM estado_cuenta WHERE cod_mov IN (10, 17, 38, 42) AND tipo_mov = 'TRUE' AND cuenta = 2 AND fecha IN ('30/12/2005', '31/01/2006', '28/02/2006') ORDER BY fecha");

foreach ($result as $reg) {
	
}
?>