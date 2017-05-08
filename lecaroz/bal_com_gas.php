<?php
// COMPARATIVO DE GAS MENSUAL
// Menu 'No definido'

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

// Conectarse a la base de datos
$db = new DBclass($dsn);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_com_gas.tpl");
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
		$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	$tpl->printToScreen();
	$db->desconectar();
	die;
}

$db = new DBclass($dsn);

$sql = "SELECT * FROM balances_pan WHERE".($_GET['num_cia'] > 0 ? "num_cia = $_GET[num_cia] AND" : "")." mes = $_GET[mes] AND anio = $_GET[anio] ORDER BY num_cia";
$result = $db->query($sql);

if (!$result) {
	header("location: ./bal_com_gas.php?codigo_error=1");
	die;
}

$fecha1 = "1/$_GET[mes]/$_GET[anio]";
$fecha2 = date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']));

$tpl->newBlock("listado");
$tpl->assign("mes",mes_escrito($_GET['mes']));
$tpl->assign("anio",$_GET['anio']);

$numreg = count($result);

for ($i=0; $i<$numreg; $i++) {
	$tpl->newBlock("fila");
	
	$tpl->assign("num_cia",$result[$i]['num_cia']);
	$nombre = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = {$result[$i]['num_cia']}");
	$tpl->assign("nombre",$nombre[0]['nombre_corto']);
	
	$sql = "SELECT sum(total_mov) AS pesos,sum(cantidad) AS litros FROM mov_inv_real WHERE num_cia = {$result[$i]['num_cia']} AND codmp = 90 AND tipo_mov = 'TRUE' AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$gas = $db->query($sql);
	
	$tpl->assign("gas_pesos",$gas[0]['pesos'] != 0 ? number_format($gas[0]['pesos'],2,".",",") : "&nbsp;");
	$tpl->assign("faltante_pan",$result[$i]['faltante_pan'] != 0 ? number_format($result[$i]['faltante_pan'],2,".",",") : "&nbsp;");
	$tpl->assign("fp_pro",$result[$i]['produccion_total'] != 0 ? number_format(abs($result[$i]['faltante_pan'] / $result[$i]['produccion_total'] * 100),3,".",",") : "&nbsp;");
	$tpl->assign("rezago",$result[$i]['rezago_fin'] != 0 ? number_format($result[$i]['rezago_fin'],2,".",",") : "&nbsp;");
	$tpl->assign("pro",$result[$i]['produccion_total'] != 0 ? number_format($result[$i]['produccion_total'],2,".",",") : "&nbsp;");
	$tpl->assign("gas_litros",$gas[0]['litros'] != 0 ? number_format($gas[0]['litros'],2,".",",") : "&nbsp;");
	$tpl->assign("gas_pro",$result[$i]['produccion_total'] != 0 ? number_format($gas[0]['pesos'] / $result[$i]['produccion_total'] * 100,3,".",",") : "&nbsp;");
	
	$sql = "SELECT sum(importe) FROM movimiento_gastos WHERE num_cia = {$result[$i]['num_cia']} AND codgastos = 1 AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$emp = $db->query($sql);
	
	$tpl->assign("sueldo_emp",$emp[0]['sum'] != 0 ? number_format($emp[0]['sum'],2,".",",") : "&nbsp;");
	$tpl->assign("sdo_pro",$result[$i]['produccion_total'] != 0 ? number_format($emp[0]['sum'] / $result[$i]['produccion_total'] * 100,3,".",",") : "&nbsp;");
	
	$sql = "SELECT sum(importe) FROM movimiento_gastos WHERE num_cia = {$result[$i]['num_cia']} AND codgastos = 2 AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$enc = $db->query($sql);
	
	$tpl->assign("sueldo_enc",$enc[0]['sum'] != 0 ? number_format($enc[0]['sum'],2,".",",") : "&nbsp;");
	$tpl->assign("mp_utilizada",$result[$i]['mat_prima_utilizada'] != 0 ? number_format($result[$i]['mat_prima_utilizada'],2,".",",") : "&nbsp;");
	$tpl->assign("mp_pro",$result[$i]['mp_pro'] != 0 ? number_format($result[$i]['mp_pro'] * 100,3,".",",") : "&nbsp;");
	$tpl->assign("mano_obra",$result[$i]['mano_obra'] != 0 ? number_format($result[$i]['mano_obra'],2,".",",") : "&nbsp;");
	$tpl->assign("mo_pro",$result[$i]['produccion_total'] != 0 ? number_format($result[$i]['mano_obra'] / $result[$i]['produccion_total'] * 100,3,".",",") : "&nbsp;");
	$tpl->assign("panaderos",$result[$i]['panaderos'] != 0 ? number_format($result[$i]['panaderos'],2,".",",") : "&nbsp;");
	$tpl->assign("gastos_fab",$result[$i]['gastos_fab'] != 0 ? number_format($result[$i]['gastos_fab'],2,".",",") : "&nbsp;");
	$tpl->assign("total_gastos",$result[$i]['total_gastos'] != 0 ? number_format($result[$i]['total_gastos'],2,".",",") : "&nbsp;");
	$tpl->assign("ventas",$result[$i]['ventas_netas'] != 0 ? number_format($result[$i]['ventas_netas'],2,".",",") : "&nbsp;");
	$tpl->assign("efectivo",$result[$i]['efectivo'] != 0 ? number_format($result[$i]['efectivo'],2,".",",") : "&nbsp;");
	
	$mp_vtas = $result[$i]['mp_vtas'] * 100;
	$ut_vtas = $result[$i]['ventas_netas'] > 0 ? $result[$i]['utilidad_neta'] / $result[$i]['ventas_netas'] * 100 : 0;
	$gastos_vtas = $result[$i]['ventas_netas'] > 0 ? abs($result[$i]['total_gastos'] / $result[$i]['ventas_netas'] * 100) : 0;
	$por_total = $mp_vtas + $ut_vtas + $gastos_vtas;
	
	$tpl->assign("mp_vtas",round($mp_vtas,2) != 100 ? number_format($mp_vtas,2,".",",") : "&nbsp;");
	$tpl->assign("ut_vtas",round($ut_vtas,2) != 0 ? number_format($ut_vtas,2,".",",") : "&nbsp;");
	$tpl->assign("gastos_vtas",round($gastos_vtas,2) != 0 ? number_format($gastos_vtas,2,".",",") : "&nbsp;");
	$tpl->assign("total",round($por_total,2) != 100 ? number_format($por_total,2,".",",") : "&nbsp;");
}

$tpl->printToScreen();
$db->desconectar();
?>