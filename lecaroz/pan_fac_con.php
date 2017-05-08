<?php
// LISTADO DE CONSUMOS ANUALES POR COMPAÑIA
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

$descripcion_error[1] = "No hay resultados";

// FUNCIONES
function buscar($array, $num_cia, $dia, $nombre) {
	$num_elementos = count($array);	// Contar número de elementos en el arreglo
	if ($num_elementos < 1)
		return 0;
	
	// Recorrer array
	for ($i=0; $i<$num_elementos; $i++)
		if ($array[$i]['num_cia'] == $num_cia && $array[$i]['dia'] == $dia)
			return $array[$i][$nombre];
	
	// Se llego al final del array y no se encontro registro
	return 0;
}

function buscar_dep($array, $num_cia, $dia) {
	$num_elementos = count($array);	// Contar número de elementos en el arreglo
	if ($num_elementos < 1)
		return 0;
	
	// Recorrer array
	for ($i=0; $i<$num_elementos; $i++)
		if ((($array[$i]['num_cia'] == $num_cia && $array[$i]['num_cia_sec'] == '') || $array[$i]['num_cia_sec'] == $num_cia) && $array[$i]['dia'] == $dia)
			return $array[$i]['importe'];
	
	// Se llego al final del array y no se encontro registro
	return 0;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_fac_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['mes'])) {
	$tpl->newBlock("datos");
	$tpl->assign(date("n"),"selected");
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

$mes = (int)$_GET['mes'];
$anio = (int)$_GET['anio'];

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

$fecha1 = "1/$mes/$anio";
$fecha2 = "$diasxmes[$mes]/$mes/$anio";

$numdias = $diasxmes[$mes];

// Obtener las facturas del mes de los clientes
$sql = "SELECT num_cia,extract(day from fecha) AS dia,sum(importe_total) FROM facturas_clientes WHERE";
if ($_GET['num_cia'] > 0)
	$sql .= " num_cia = $_GET[num_cia] AND";
$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY num_cia,fecha ORDER BY num_cia,fecha";
$fac = ejecutar_script($sql,$dsn);//echo '<p>fac</p><pre>' . print_r($fac, true) . '</pre>';

// Obtener las facturas impresas del mes
$sql = "SELECT num_cia, extract(day from fecha) AS dia, sum(importe) AS importe FROM facturas_diarias WHERE";
$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia] AND" : "";
$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY num_cia, fecha ORDER BY num_cia, fecha";
$fac_dia = ejecutar_script($sql, $dsn);//echo '<p>fac_dia</p><pre>' . print_r($fac_dia, true) . '</pre>';

$sql = 'SELECT num_cia, dia, sum(importe) FROM (SELECT CASE WHEN num_cia_sec IS NULL THEN num_cia WHEN num_cia_sec IS NOT NULL THEN num_cia_sec END AS num_cia, extract(day from fecha) AS dia, importe FROM estado_cuenta WHERE ';
$sql .= $_GET['num_cia'] > 0 ? " ((num_cia = $_GET[num_cia] AND num_cia_sec IS NULL) OR num_cia_sec = $_GET[num_cia]) AND" : '';
$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_mov IN (1, 16, 44, 99) ORDER BY num_cia, fecha) result GROUP BY num_cia, dia ORDER BY num_cia, dia";
$dep = ejecutar_script($sql, $dsn);

// Obtener los depositos del mes
/*$sql = "SELECT num_cia, num_cia_sec, extract(day from fecha) AS dia, sum(importe) FROM estado_cuenta WHERE";
if ($_GET['num_cia'] > 0)
	$sql .= " num_cia = $_GET[num_cia] AND";
$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_mov IN (1, 16, 44, 99) GROUP BY num_cia, fecha ORDER BY num_cia,fecha";
$dep = ejecutar_script($sql,$dsn);//echo '<p>dep</p><pre>' . print_r($dep, true) . '</pre>';

if (!$dep) {
	header("location: ./pan_fac_con.php?codigo_error=1");
	die;
}*/

$tpl->newBlock("listado");
$tpl->assign("mes",mes_escrito($mes));
$tpl->assign("anio",$anio);

$num_cia = NULL;
$dia = 1;
for ($i=0; $i<count($dep); $i++) {
	if ($num_cia != $dep[$i]['num_cia']) {
		if ($num_cia != NULL) {
			$tpl->assign("cia.depositos",$total_depositos != 0 ? number_format($total_depositos,2,".",",") : "&nbsp;");
			$tpl->assign("cia.facturas",$total_facturas != 0 ? number_format($total_facturas,2,".",",") : "&nbsp;");
			$tpl->assign("cia.diferencia",$total_diferencia != 0 ? number_format($total_diferencia,2,".",",") : "&nbsp;");
		}
		
		$num_cia = $dep[$i]['num_cia'];
		
		$tpl->newBlock("cia");
		$tpl->assign("num_cia",$num_cia);
		$nombre_cia = ejecutar_script("SELECT nombre FROM catalogo_companias WHERE num_cia = $num_cia",$dsn);
		$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);
		
		$diferencia = 0;
		
		$total_depositos = 0;
		$total_facturas = 0;
		$total_fac = 0;
		$total_diferencia = 0;
	}
	
	if ($dia < $dep[$i]['dia']) {
		do {
			$tpl->newBlock('fila');
			$tpl->assign('dia', $dia);
			$tpl->assign("depositos", "<span style=\"color:red; font-weight:bold;\">0.00</span>");
			$tpl->assign("facturas", "&nbsp;");
			$tpl->assign("fac", "&nbsp;");
			$tpl->assign("diferencia", "&nbsp;");
			$dia++;
		} while ($dia < $dep[$i]['dia']);
	}
	
	$tpl->newBlock("fila");
	$tpl->assign("dia",$dep[$i]['dia']);
	$facturas = buscar($fac,$num_cia,$dep[$i]['dia'],"sum");
	$fac_d = buscar($fac_dia, $num_cia, $dep[$i]['dia'], "importe");
	$diferencia = $dep[$i]['sum'] - $facturas - $fac_d;
	$tpl->assign("depositos",round($dep[$i]['sum'],2) != 0 ? number_format($dep[$i]['sum'],2,".",",") : "&nbsp;");
	$tpl->assign("facturas",round($facturas,2) != 0 ? number_format($facturas,2,".",",") : "&nbsp;");
	$tpl->assign("fac", round($fac_d, 2) != 0 ? number_format($fac_d, 2, ".", ",") : "&nbsp;");
	$tpl->assign("diferencia",round($diferencia,2) != 0 ? "<font color=\"#".($diferencia > 0 ? "0000FF" : "FF0000")."\">".number_format($diferencia,2,".",",")."</font>" : "&nbsp;");
	
	$total_depositos += $dep[$i]['sum'];
	$total_facturas += $facturas;
	$total_fac += $fac_d;
	$total_diferencia += $diferencia;
	$dia++;
}
if ($num_cia != NULL) {
	$tpl->assign("cia.depositos",$total_depositos != 0 ? number_format($total_depositos,2,".",",") : "&nbsp;");
	$tpl->assign("cia.facturas",$total_facturas != 0 ? number_format($total_facturas,2,".",",") : "&nbsp;");
	$tpl->assign("cia.fac", $total_fac != 0 ? number_format($total_fac, 2, ".", ",") : "&nbsp;");
	$tpl->assign("cia.diferencia",$total_diferencia != 0 ? number_format($total_diferencia,2,".",",") : "&nbsp;");
}

$tpl->printToScreen();
?>