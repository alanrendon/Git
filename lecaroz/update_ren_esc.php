<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

$sql = "SELECT id, concepto FROM estado_cuenta_tmp WHERE cod_mov = 2 AND local > 0 and fecha_renta IS NULL";
$result = $db->query($sql);

$sql = "";
foreach ($result as $reg) {
	$str = explode(' ', $reg['concepto']);
	switch ($str[0]) {
		case 'ENERO': $mes = 1; break;
		case 'FEBRERO': $mes = 2; break;
		case 'MARZO': $mes = 3; break;
		case 'ABRIL': $mes = 4; break;
		case 'MAYO': $mes = 5; break;
		case 'JUNIO': $mes = 6; break;
		case 'JULIO': $mes = 7; break;
		case 'AGOSTO': $mes = 8; break;
		case 'SEPTIEMBRE': $mes = 9; break;
		case 'OCTUBRE': $mes = 10; break;
		case 'NOVIEMBRE': $mes = 11; break;
		case 'DICIEMBRE': $mes = 12; break;
	}
	$fecha = "01/$mes/$str[1]";
	$sql .= "UPDATE estado_cuenta_tmp SET fecha_renta = '$fecha' WHERE id = $reg[id];\n";
}
//echo "<pre>$sql</pre>";
$db->query($sql);
?>