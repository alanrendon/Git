<?php
// LISTADO DE CARPINTERIAS
// Tablas ''
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
$tpl->assignInclude("body","./plantillas/bal/bal_lis_mad.tpl");
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

$fecha1 = "1/$mes/$anio";
$fecha2 = "$diasxmes[$mes]/$mes/$anio";

$sql = "SELECT num_cia,nombre_corto FROM catalogo_companias ORDER BY num_cia";
$cia = ejecutar_script($sql,$dsn);

$numfilas_x_hoja = 58;
$numfilas = $numfilas_x_hoja;
$gran_total = 0;
for ($i=0; $i<count($cia); $i++) {
	$num_cia = $cia[$i]['num_cia'];
	
	if ($numfilas == $numfilas_x_hoja) {
		$tpl->newBlock("listado");
		$tpl->assign("mes",mes_escrito($mes));
		$tpl->assign("anio",$anio);
		
		$numfilas = 0;
	}
	// Buscar movimientos de gastos con código 95 (CARPINTERO)
	$sql = "SELECT sum(importe) FROM movimiento_gastos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 95";
	$madera = ejecutar_script($sql,$dsn);
	// Buscar bases
	$sql = "SELECT sum(costo_total) FROM entrada_mp WHERE num_cia = $num_cia AND fecha_pago BETWEEN '$fecha1' AND '$fecha2' AND codmp = 310";
	$bases = ejecutar_script($sql,$dsn);
	$total = $madera[0]['sum'] + $bases[0]['sum'];
	if ($total != 0) {
		$tpl->newBlock("fila");
		$tpl->assign("num_cia",$num_cia);
		$tpl->assign("nombre",$cia[$i]['nombre_corto']);
		$tpl->assign("madera",$total != 0 ? number_format($total,2,".",",") : "&nbsp;");
		
		$gran_total += $total;
	}
}
$tpl->assign("listado.total",number_format($gran_total,2,".",","));

$tpl->printToScreen();
die;
?>