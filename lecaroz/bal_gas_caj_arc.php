<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_FILES['archivo'])) {
	$fp = fopen($_FILES['archivo']['tmp_name'], "r");
	
	$tipo_mov = $_POST['tipo_mov'];
	$cod_gastos = $_POST['cod_gastos'];
	$clave_balance = isset($_POST['clave_balance']) ? 'TRUE' : 'FALSE';
	$fecha = $_POST['fecha'];
	$comentario = isset($_REQUEST['comentario']) ? trim(strtoupper($_REQUEST['comentario'])) : '';
	
	$sql = '';
	while (!feof($fp)) {
		$buffer = fgets($fp);
		
		if (trim($buffer) != '') {
			$data = split(',', $buffer);
			
			foreach ($data as $i => $d)
				if ($i == 0 && get_val($d) > 0)
					$num_cia = get_val($d);
				else if ($i > 0 && get_val($d) > 0) {
					$importe = get_val($d);
					$sql .= "INSERT INTO gastos_caja (num_cia, cod_gastos, importe, tipo_mov, clave_balance, fecha, fecha_captura, imp_inf, iduser, comentario) VALUES ($num_cia, $cod_gastos, $importe, '$tipo_mov', '$clave_balance', '$fecha', CURRENT_DATE, 'FALSE', $_SESSION[iduser], '$comentario');\n";
				}
		}
	}
	
	fclose($fp);
	
	if (trim($sql) != '') $db->query($sql);
	
	die(header('location: ./bal_gas_caj_arc.php'));
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/bal/bal_gas_caj_arc.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign('fecha', date('d/m/Y', date('d') < 5 ? mktime(0, 0, 0, date('n'), 0, date('Y')) : mktime(0, 0, 0, date('n'), date('d'), date('Y'))));

$sql = "SELECT id, descripcion FROM catalogo_gastos_caja ORDER BY descripcion";
$result = $db->query($sql);
foreach ($result as $r) {
	$tpl->newBlock('cod');
	$tpl->assign('cod', $r['id']);
	$tpl->assign('desc', $r['descripcion']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>