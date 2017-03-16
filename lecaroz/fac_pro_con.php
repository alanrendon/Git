<?php
// CONSULTA DE PROMEDIOS DE CONSUMOS
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
$tpl->assignInclude("body","./plantillas/fac/fac_pro_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['anio'])) {
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

$anio_actual = date("Y");
$anio_act = $_GET['anio'];
$anio_ant = $_GET['anio'] - 1;
$num_meses_act = $_GET['anio'] < $anio_actual ? 12 : date("n") - 1;
$num_meses_ant = 12;

$tpl->newBlock("listado");
$tpl->assign("num_cia",$_GET['num_cia']);
$nombre_cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]",$dsn);
$tpl->assign("nombre_cia",$nombre_cia[0]['nombre_corto']);
$tpl->assign("codmp",$_GET['codmp']);
$nombre_mp = ejecutar_script("SELECT * FROM catalogo_mat_primas WHERE codmp = $_GET[codmp]",$dsn);
$tpl->assign("nombre_mp",$nombre_mp[0]['nombre']);
$tpl->assign("anio",$anio_act);

$unidad = ejecutar_script("SELECT descripcion FROM tipo_unidad_consumo WHERE idunidad = ".$nombre_mp[0]['unidadconsumo'],$dsn);
$tpl->assign("unidad",strtoupper($unidad[0]['descripcion']));
$tpl->assign("tipo",$nombre_mp[0]['tipo'] == 1 ? "MATERIA PRIMA" : "MATERIAL DE EMPAQUE");
$presentacion = ejecutar_script("SELECT descripcion FROM tipo_presentacion WHERE idpresentacion = ".$nombre_mp[0]['presentacion'],$dsn);
$tpl->assign("presentacion",strtoupper($presentacion[0]['descripcion']));
$existencia = ejecutar_script("SELECT existencia FROM inventario_real WHERE num_cia = $_GET[num_cia] AND codmp = $_GET[codmp]",$dsn);
$tpl->assign("existencia",number_format($existencia[0]['existencia'],2,".",","));
$tpl->assign("ped_aut",$nombre_mp[0]['procpedautomat'] == "t" ? "SI" : "NO");
$tpl->assign("por_ped",number_format($nombre_mp[0]['porcientoincremento'],2,".",","));
$tpl->assign("num_entregas",$nombre_mp[0]['entregasfinmes']);

$inicio = microtime_float();

$tpl->assign("anio_ant",$anio_ant);
$total = 0;
for ($m=1; $m<=$num_meses_ant; $m++) {
	$sql = "SELECT SUM(cantidad) FROM mov_inv_real WHERE num_cia = $_GET[num_cia] AND codmp = $_GET[codmp] AND fecha BETWEEN '1/$m/$anio_ant' AND '$diasxmes[$m]/$m/$anio_ant' AND tipo_mov = 'TRUE'";
	$result = ejecutar_script($sql,$dsn);
	$tpl->assign($m."_ant",$result[0]['sum'] != 0 ? number_format($result[0]['sum'],2,".",",") : "&nbsp;");
	
	$total += $result[0]['sum'];
}
$tpl->assign("total_ant",$total > 0 ? number_format($total,2,".",",") : "&nbsp;");
$tpl->assign("prom_ant",$total > 0 ? number_format($total / $num_meses_ant,2,".",",") : "&nbsp;");

$tpl->assign("anio_act",$anio_act);
$total = 0;
for ($m=1; $m<=$num_meses_act; $m++) {
	$sql = "SELECT SUM(cantidad) FROM mov_inv_real WHERE num_cia = $_GET[num_cia] AND codmp = $_GET[codmp] AND fecha BETWEEN '1/$m/$anio_act' AND '$diasxmes[$m]/$m/$anio_act' AND tipo_mov = 'TRUE'";
	$result = ejecutar_script($sql,$dsn);
	$tpl->assign($m."_act",$result[0]['sum'] != 0 ? number_format($result[0]['sum'],2,".",",") : "&nbsp;");
	
	$total += $result[0]['sum'];
}
$tpl->assign("total_act",$total > 0 ? number_format($total,2,".",",") : "&nbsp;");
$tpl->assign("prom_act",$total > 0 ? number_format($total / $num_meses_act,2,".",",") : "&nbsp;");

$fin = microtime_float();
$tiempo = $fin - $inicio;
//echo "Tiempo de ejecución: ".round($tiempo,3)." segundos";

$tpl->printToScreen();
die;
?>