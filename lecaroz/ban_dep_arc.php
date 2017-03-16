<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_FILES['archivo'])) {
	$fp = fopen($_FILES['archivo']['tmp_name'], "r");
	$cuenta = $_POST['cuenta'];
	
	$sql = '';
	while (!feof($fp)) {
		$buffer = fgets($fp);
		
		if (trim($buffer) != '') {
			$data = split(',', $buffer);
			
			$num_cia = get_val($data[0]);
			if ($num_cia > 0) {
				$importe = get_val($data[1]);
				$fecha = trim($data[2]);
				$comprobante = isset($data[3]) && intval(trim($data[3]), 10) > 0 ? intval(trim($data[3]), 10) : 'NULL';
				$cod_mov = $num_cia <= 300 ? 1 : 16;
				$concepto = $num_cia <= 300 ? 'DEPOSITO' . ($comprobante > 0 ? ' ' . $comprobante : '') : 'DEPOSITO (POLLO)' . ($comprobante > 0 ? ' ' . $comprobante : '');
				$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, comprobante) VALUES ($num_cia, '$fecha', 'FALSE', $importe, $cod_mov, '$concepto', $cuenta, $_SESSION[iduser], now(), $comprobante);\n";
			}
		}
	}
	
	fclose($fp);
	
	if (trim($sql) != '') $db->query($sql);
	
	die(header('location: ./ban_dep_arc.php'));
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_dep_arc.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>