<?php
// LISTADO DE CONSUMOS ANUALES POR COMPA�IA
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

// --------------------------------- Obtener informaci�n de la pantalla --------------------------------------
//$session->info_pantalla();

// VARIABLES GLOBALES
$numfilas = 20;

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_con_cia.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	$tpl->assign("anio",date("Y"));
	
	// Si viene de una p�gina que genero error
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
$num_meses = $_GET['anio'] < $anio_actual ? 12 : date("n") - 1;
$numfilas_x_hoja = 48;

// Obtener todas las materias primas
$sql = "(SELECT codmp,nombre FROM mov_inv_real 
LEFT JOIN catalogo_mat_primas USING(codmp) 
LEFT JOIN control_avio USING(num_cia,codmp) 
WHERE num_cia = $_GET[num_cia] AND fecha BETWEEN '1/1/$_GET[anio]' AND '$diasxmes[$num_meses]/$num_meses/$_GET[anio]' AND tipo_mov = 'TRUE' AND controlada = 'TRUE' 
GROUP BY codmp,nombre,num_orden ORDER BY num_orden ASC) 
UNION ALL 
(SELECT codmp,nombre FROM mov_inv_real 
LEFT JOIN catalogo_mat_primas USING(codmp) 
WHERE num_cia = $_GET[num_cia] AND fecha BETWEEN '1/1/$_GET[anio]' AND '$diasxmes[$num_meses]/$num_meses/$_GET[anio]' AND tipo_mov = 'TRUE' AND controlada = 'FALSE' 
GROUP BY codmp,nombre,controlada ORDER BY nombre)";
$mp = ejecutar_script($sql,$dsn);

$nombre_cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]",$dsn);

$inicio = microtime_float();

$numfilas = $numfilas_x_hoja;
$gran_total = 0;
for ($c=0; $c<count($mp); $c++) {
	if ($numfilas == $numfilas_x_hoja) {
		$tpl->newBlock("listado");
		$tpl->assign("num_cia",$_GET['num_cia']);
		$tpl->assign("nombre_cia",$nombre_cia[0]['nombre_corto']);
		$tpl->assign("anio",$_GET['anio']);
		
		$numfilas = 0;
	}
	
	$codmp = $mp[$c]['codmp'];
	
	$tpl->newBlock("fila");
	$tpl->assign("codmp",$codmp);
	$tpl->assign("nombre_mp",recorta_cadena($mp[$c]['nombre'],20));
	
	$meses = 0;
	$total = 0;
	for ($m=1; $m<=$num_meses; $m++) {
		$sql = "SELECT SUM(cantidad) FROM mov_inv_real WHERE num_cia = $_GET[num_cia] AND codmp = $codmp AND fecha BETWEEN '1/$m/$_GET[anio]' AND '$diasxmes[$m]/$m/$_GET[anio]' AND tipo_mov = 'TRUE'";
		$result = ejecutar_script($sql,$dsn);
		$tpl->assign($m,$result[0]['sum'] > 0 ? number_format($codmp == 1 ? $result[0]['sum'] / 44 : ($codmp == 3 || $codmp == 4 ? $result[0]['sum'] / 50 : $result[0]['sum']),2,".",",") : "&nbsp;");
		
		$total += $codmp == 1 ? $result[0]['sum'] / 44 : ($codmp == 3 || $codmp == 4 ? $result[0]['sum'] / 50 : $result[0]['sum']);
		$gran_total += $result[0]['sum'];
		$meses += $result[0]['sum'] > 0 ? 1 : 0;
	}
	$tpl->assign("total",$total > 0 ? number_format($total,2,".",",") : "&nbsp;");
	$tpl->assign("promedio",$total > 0 ? number_format($total / $meses,2,".",",") : "&nbsp;");
	$numfilas++;
}
$tpl->newBlock("total");
$tpl->assign("gran_total",number_format($gran_total,2,".",","));

$fin = microtime_float();
$tiempo = $fin - $inicio;
//echo "Tiempo de ejecuci�n: ".round($tiempo,3)." segundos";

$tpl->printToScreen();
die;
?>