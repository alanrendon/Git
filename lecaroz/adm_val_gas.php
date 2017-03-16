<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "";

$db = new DBclass($dsn, "autocommit=yes");

if (isset($_POST['tmp'])) {
	$sql = '';
	foreach ($_POST as $k => $v)
		if (strpos($k, 'val') !== FALSE)
			$sql .= "UPDATE gastos_tmp SET aut = 2 WHERE id = $v;\n";
		else if (strpos($k, 'del') !== FALSE) {
			$tmp = $db->query("SELECT num_cia, fecha, codgastos, importe FROM gastos_tmp WHERE id = $v");
			$sql .= "UPDATE gastos_tmp SET aut = 3 WHERE id = $v;\n";
			$sql .= "UPDATE total_panaderias SET efectivo = efectivo + {$tmp[0]['importe']}, gastos = gastos - {$tmp[0]['importe']} WHERE";
			$sql .= " num_cia = {$tmp[0]['num_cia']} AND fecha = '{$tmp[0]['fecha']}';\n";
			$sql .= "DELETE FROM movimiento_gastos WHERE num_cia = {$tmp[0]['num_cia']} AND fecha = '{$tmp[0]['fecha']}' AND codgastos = {$tmp[0]['codgastos']}";
			$sql .= " AND importe = {$tmp[0]['importe']} AND captura = 'FALSE';\n";
		}
	
	if ($sql != '') $db->query($sql);
	die(header('location: ./adm_val_gas.php'));
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/adm/adm_val_gas.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$sql = "SELECT id, num_cia, nombre_corto, fecha, codgastos, descripcion AS desc, concepto, importe FROM gastos_tmp LEFT JOIN catalogo_companias USING (num_cia)";
$sql .= " LEFT JOIN catalogo_gastos USING (codgastos) WHERE aut = 1 ORDER BY num_cia, fecha";
$result = $db->query($sql);

if ($result) {
	$num_cia = NULL;
	foreach ($result as $i => $reg) {
		if ($num_cia != $reg['num_cia']) {
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock('cia');
			$tpl->assign('num_cia', $reg['num_cia']);
			$tpl->assign('nombre', $reg['nombre_corto']);
			$total = 0;
		}
		$tpl->newBlock('fila');
		$tpl->assign('i', $i);
		$tpl->assign('id', $reg['id']);
		$tpl->assign('fecha', $reg['fecha']);
		$tpl->assign('cod', $reg['codgastos']);
		$tpl->assign('desc', $reg['desc']);
		$tpl->assign('concepto', $reg['concepto']);
		$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
		$total += $reg['importe'];
		$tpl->assign('cia.total', number_format($total, 2, '.', ','));
	}
}

$tpl->printToScreen();
?>