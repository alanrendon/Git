<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "";
$numfilas = 25;

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_imss_pagos_cap.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$meses = 12;

if (isset($_GET['num_cia'])) {
	$sql = "SELECT mes, importe FROM pagos_imss WHERE num_cia = $_GET[num_cia] AND anio = $_GET[anio] ORDER BY mes";
	$result = $db->query($sql);
	
	$str = "";
	if ($result)
		foreach ($result as $reg)
			$str .= "$reg[mes]|$reg[importe]\n";
	
	echo $str;
	die;
}

if (isset($_POST['num_cia'])) {
	// Obtener meses capturados
	$mes = array();
	for ($i = 0; $i < $meses; $i++)
		if (get_val($_POST['importe'][$i]) > 0)
			$mes[$_POST['mes'][$i]] = get_val($_POST['importe'][$i]);
	
	// Validar que no haya huecos entre meses
	$keys = array_keys($mes);
	$ini = $keys[0];
	$fin = $keys[count($keys) - 1];
	for ($i = $ini; $i <= $fin; $i++)
		if (!isset($mes[$i])) {
			$tpl->newBlock("valid");
			$tpl->assign("mensaje", "No puede dejar huecos entre los meses");
			$tpl->assign("campo", "importe[" . ($i - 1) . "]");
			$tpl->printToScreen();
			die;
		}
	
	$sql = "DELETE FROM pagos_imss WHERE num_cia = $_POST[num_cia] AND anio = $_POST[anio];\n";
	$data['num_cia'] = $_POST['num_cia'];
	$data['anio'] = $_POST['anio'];
	for ($i = 0; $i < count($_POST['importe']); $i++)
		if (get_val($_POST['importe'][$i]) > 0) {
			$data['mes'] = $_POST['mes'][$i];
			$data['importe'] = get_val($_POST['importe'][$i]);
			$sql .= $db->preparar_insert("pagos_imss", $data) . ";\n";
		}
	$db->query($sql);
	
	$tpl->newBlock("redir");
	$tpl->printToScreen();
	die($sql);
}

$tpl->newBlock("captura");
$tpl->assign("anio", date("Y"));
$tpl->assign("total", "0.00");

for ($i = 0; $i < $meses; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("mes", $i + 1);
	$tpl->assign("next", $i < $meses - 1 ? $i + 1 : 0);
	$tpl->assign("back", $i > 0 ? $i - 1 : $meses - 1);
	$tpl->assign("nombre", mes_escrito($i + 1));
}

// Catálogo de Compañías
$cias = $db->query("SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias/* WHERE num_cia BETWEEN " . ($_SESSION['iduser'] >= 28 ? '900 AND 998' : '1 AND 899') . "*/ ORDER BY num_cia");
foreach ($cias as $cia) {
	$tpl->newBlock("cia");
	foreach ($cia as $tag => $value)
		$tpl->assign($tag, $value);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>