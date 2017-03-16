<?php
// LISTADO DE COMPRAS ANUALES POR PROVEEDOR
// Tablas 'folios_cheque, cheques, facturas, facturas_pagadas, estado_cuenta'
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// VARIABLES GLOBALES
$numfilas = 20;

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_pro_anu.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_proveedor'])) {
	$tpl->newBlock("datos");
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

$num_meses = 12;
$numfilas_x_hoja = 46;

// Obtener todas las panaderias
$sql = "SELECT num_cia,nombre,nombre_corto FROM catalogo_companias WHERE num_cia < 100 ORDER BY num_cia";
$cia = ejecutar_script($sql,$dsn);

$inicio = microtime_float();

$numfilas = $numfilas_x_hoja;
$gran_total = 0;
for ($c=0; $c<count($cia); $c++) {
	if ($numfilas == $numfilas_x_hoja) {
		$tpl->newBlock("listado");
		$nombre_pro = ejecutar_script("SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = $_GET[num_proveedor]",$dsn);
		$tpl->assign("nombre_pro",$nombre_pro[0]['nombre']);
		$tpl->assign("anio",$_GET['anio']);
		
		$numfilas = 0;
	}
	
	$num_cia = $cia[$c]['num_cia'];
	
	$tpl->newBlock("fila");
	$tpl->assign("num_cia",$num_cia);
	$tpl->assign("nombre_cia",$cia[$c]['nombre_corto']);
	
	$total = 0;
	for ($m=1; $m<=$num_meses; $m++) {
		$sql = "SELECT SUM(importe_total) FROM facturas WHERE num_cia = $num_cia AND num_proveedor = $_GET[num_proveedor] AND fecha_mov BETWEEN '1/$m/$_GET[anio]' AND '$diasxmes[$m]/$m/$_GET[anio]'";
		$result = ejecutar_script($sql,$dsn);
		$tpl->assign($m,$result[0]['sum'] > 0 ? number_format($result[0]['sum'],2,".",",") : "&nbsp;");
		
		$total += $result[0]['sum'];
		$gran_total += $result[0]['sum'];
	}
	$tpl->assign("total",$total > 0 ? number_format($total,2,".",",") : "&nbsp;");
	$numfilas++;
}
$tpl->newBlock("total");
$tpl->assign("gran_total",number_format($gran_total,2,".",","));

$fin = microtime_float();
$tiempo = $fin - $inicio;
//echo "Tiempo de ejecución: ".round($tiempo,3)." segundos";

$tpl->printToScreen();
die;
?>