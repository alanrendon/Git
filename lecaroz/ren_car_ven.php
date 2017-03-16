<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ren/ren_car_ven.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$sql = 'SELECT id, num_local, nombre_arrendatario, fecha_final, renta_con_recibo AS renta, por_incremento FROM catalogo_arrendatarios WHERE status = 1 AND bloque = 2 AND fecha_final <= now()::date + interval \'1 month 15 days\' AND renta_con_recibo > 0 ORDER BY fecha_final';
$result = $db->query($sql);

if (!$result)
	$tpl->newBlock('no_result');
else {
	foreach ($result as $reg) {
		$tpl->newBlock('fila');
		$tpl->assign('num_local', $reg['num_local']);
		$tpl->assign('arr', $reg['nombre_arrendatario']);
		$tpl->assign('fecha_final', $reg['fecha_final']);
		$tpl->assign('renta', number_format($reg['renta'], 2, '.', ','));
		$tpl->assign('por_incremento', $reg['por_incremento'] > 0 ? number_format($reg['por_incremento'], 2, '.', ',') : '&nbsp;');
	}
}

$tpl->printToScreen();
?>