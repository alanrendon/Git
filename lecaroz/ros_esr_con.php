<?php
// ESTADO DE RESULTADOS
// Tablas 'compra_directa', 'hoja_dia_rost', 'movimiento_gastos', 'total_companias'
// Menu 'No definido'

define ('IDSCREEN',2); // ID de pantalla

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

// --------------------------------- Generar pantalla --------------------------------------------------------

if (!isset($_GET['compania'])) {
	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower( "./plantillas/header.tpl" );
	
	// Incluir el cuerpo del documento
	$tpl->assignInclude("body","./plantillas/ros/ros_esr_con.tpl");
	$tpl->prepare();
	
	// Seleccionar script para menu
	$tpl->newBlock("menu");
	if (isset($_SESSION['menu']))
		$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
	$tpl->gotoBlock("_ROOT");
	
	$tpl->printToScreen();
	die;
}

$cia = $_GET['compania'];
$dia = date("d");
$mes = $_GET['mes'];
$anio = date("Y");

// Construir fecha inicial y fecha final
$fecha1 = "1/$mes/$anio";
$fecha2 = "$dia/$mes/$anio";

// Ventas
$ventas = ejecutar_script("SELECT sum(precio_total) FROM hoja_diaria_rost WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'",$dsn);
// Ventas Netas
$ventas_netas = ejecutar_script("SELECT sum(venta) FROM total_companias WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'",$dsn);
// Otros
$otros = $ventas_netas[0]['sum'] - $ventas[0]['sum'];
// Inventario Anterior
$inv_ant = ejecutar_script("SELECT sum(cost_inv) FROM total_cost_inv WHERE num_cia = $cia",$dsn);
// Compras
$compras = ejecutar_script("SELECT sum(total_fac) FROM total_fac_ros WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2'",$dsn);
// Mercancias
$mercancias = ejecutar_script("SELECT sum(total) FROM compra_directa WHERE num_cia = $cia AND fecha_mov >= '$fecha1' AND fecha_mov <= '$fecha2'",$dsn);
// Inventario Actual
$costentradas = ejecutar_script("SELECT sum(total_mov) FROM mov_inv_real WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND tipo_mov='FALSE'",$dsn);
$costsalidas  = ejecutar_script("SELECT sum(total_mov) FROM mov_inv_real WHERE num_cia = $cia AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND tipo_mov='TRUE'",$dsn);
$inv_act = $inv_ant[0]['sum'] + $costentradas[0]['sum'] - $costsalidas[0]['sum'];
// Materia Prima Utilizada
$mat_prima_utilizada = $compras[0]['sum'] + $mercancias[0]['sum'] - $inv_act;
// Gastos de Fabricación
$result = ejecutar_script("SELECT DISTINCT movimiento_gastos.importe WHERE movimiento_gastos.num_cia=$cia AND movimiento_gastos.fecha >= '$fecha1' AND movimiento_gastos.fecha <= '$fecha2' AND catalogo_gastos.codigo_edo_resultados=1",$dsn);
$gastos_fab = 0;
for ($i=0; $i<count($result); $i++) {
	$gastos_fab += $result[$i]['importe'];
}
// Costos de Elaboración
$costo_elaboracion = $mat_prima_utilizada + $gastos_fab;

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ros/ros_esr_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->printToScreen();
die;

?>