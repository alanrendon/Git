<?php
// ACTUALIZAR CONSUMOS PROMEDIO MENSUAL POR COMPAÑÍA
// Tablas 'folios_cheque, cheques, facturas, facturas_pagadas, estado_cuenta'
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

$descripcion_error[1] = "No hay consumos para el mes especificado";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_act_con_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['mes'])) {
	$tpl->newBlock("datos");
	$tpl->assign(date("n",mktime(0,0,0,date("m")-1,1,date("Y"))),"selected");
	$tpl->assign("anio",date("Y"));
	
	$result = $db->query('SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores ORDER BY admin');
	foreach ($result as $r) {
		$tpl->newBlock('admin');
		$tpl->assign('id', $r['id']);
		$tpl->assign('admin', $r['admin']);
	}
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign("message",$descripcion_error[$_GET['codigo_error']]);	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	$tpl->printToScreen();
	die;
}

$mes = $_GET['mes'];
$anio = $_GET['anio'];
$fecha1 = "01/$mes/$anio";
$fecha2 = date("d/m/Y", mktime(0, 0, 0, $mes + 1, 0, $anio));

$sql = "SELECT num_cia, codmp, sum(CASE WHEN tipo_mov = 'TRUE' THEN cantidad ELSE -cantidad END) AS consumo FROM mov_inv_real LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha BETWEEN '$fecha1' AND '$fecha2' AND (tipo_mov = 'TRUE' OR (tipo_mov = 'FALSE' AND descripcion LIKE 'DIFERENCIA%')) AND cantidad > 0";
$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : " AND num_cia <= 300";
$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '';
$sql .= " GROUP BY num_cia, codmp ORDER BY num_cia, codmp";
$result = $db->query($sql);

if (!$result) {
	header("location: ./bal_act_con_v2.php?codigo_error=1");
	die;
}

$sql = "DELETE FROM consumos_mensuales WHERE mes = $mes AND anio = $anio" . ($_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "") . ";\n";
foreach ($result as $reg)
	$sql .= "INSERT INTO consumos_mensuales (num_cia, codmp, mes, anio, consumo) VALUES ($reg[num_cia], $reg[codmp], $mes, $anio, $reg[consumo]);\n";

//echo "<pre>$sql</pre>";die;
$db->query($sql);

header("location: ./bal_act_con_v2.php?mensaje=Se+actualizaron+los+consumos+al+mes+de+".mes_escrito($mes)."+del+$anio");
?>