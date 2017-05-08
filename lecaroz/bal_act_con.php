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

$descripcion_error[1] = "No existe la compañía";
$descripcion_error[2] = "No hay consumos para el mes especificado";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_act_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['mes'])) {
	$tpl->newBlock("datos");
	$tpl->assign(date("n",mktime(0,0,0,date("m")-1,1,date("Y"))),"selected");
	$tpl->assign("anio",date("Y"));
	
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

// Obtener listado de panaderias
$sql = "SELECT num_cia FROM catalogo_companias WHERE";
if ($_GET['num_cia'] > 0)
	$sql .= " num_cia = $_GET[num_cia]";
else
	$sql .= " num_cia < 100";
$sql .= " ORDER BY num_cia";
$cia = $db->query($sql);

if (!$cia) {
	header("location: ./bal_act_con.php?codigo_error=1");
	die;
}

$diasxmes[1] = 31;
$diasxmes[2] = $_GET['anio'] % 4 == 0 ? 29 : 28;
$diasxmes[3] = 31;
$diasxmes[4] = 30;
$diasxmes[5] = 31;
$diasxmes[6] = 30;
$diasxmes[7] = 31;
$diasxmes[8] = 31;
$diasxmes[9] = 30;
$diasxmes[10] = 31;
$diasxmes[11] = 30;
$diasxmes[12] = 31;

$mes = $_GET['mes'];
$anio = $_GET['anio'];

// Recorrer compañías
$sql = "";
for ($i=0; $i<count($cia); $i++) {
	$num_cia = $cia[$i]['num_cia'];
	
	// Obtener todas las materias primas
	$mp = $db->query("SELECT codmp FROM mov_inv_real WHERE num_cia = $num_cia AND fecha BETWEEN '1/$mes/$anio' AND '$diasxmes[$mes]/$mes/$anio' AND tipo_mov = 'TRUE' GROUP BY codmp ORDER BY codmp");
	
	for ($j=0; $j<count($mp); $j++) {
		$codmp = $mp[$j]['codmp'];
		
		$result = $db->query("SELECT SUM(cantidad) FROM mov_inv_real WHERE num_cia = $num_cia AND codmp = $codmp AND fecha BETWEEN '1/$mes/$anio' AND '$diasxmes[$mes]/$mes/$anio' AND tipo_mov = 'TRUE'");
		
		$consumo = $result[0]['sum'] > 0 ? round($result[0]['sum'],2) : 0;
		
		if ($id = $db->query("SELECT id FROM consumos_mensuales WHERE num_cia = $num_cia AND codmp = $codmp AND mes = $mes AND anio = $anio"))
			$sql .= "UPDATE consumos_mensuales SET consumo = $consumo WHERE id = {$id[0]['id']};\n";
		else
			$sql .= "INSERT INTO consumos_mensuales (num_cia,codmp,mes,anio,consumo) VALUES ($num_cia,$codmp,$mes,$anio,$consumo);\n";
	}
}

$db->query($sql);
$db->desconectar();

header("location: ./bal_act_con.php?mensaje=Se+actualizaron+los+consumos+al+mes+de+".mes_escrito($mes)."+del+$anio");
?>