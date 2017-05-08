<?php
//define ('IDSCREEN',1241); // ID de pantalla
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
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No se encontraron registros";
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower("./plantillas/header.tpl");
// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/bal/bal_goc_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['mes'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio",date("Y"));

	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign("message",$descripcion_error[$_GET['codigo_error']]);	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message",$_GET['mensaje']);
	}

	$tpl->printToScreen();
	die();
}

// -------------------------------- Mostrar listado ---------------------------------------------------------

$sql="SELECT * FROM gastos_otras_cia where fecha= '".$_GET['anio']."/".$_GET['mes']."/1'";
$gastos = ejecutar_script($sql,$dsn);

if(!$gastos)
{
	header("location: ./bal_goc_con.php?codigo_error=1");
	die();
}

$tpl->newBlock("gastos");
for($i=0;$i<count($gastos);$i++)
{
	$tpl->newBlock("fila");
	$cia = obtener_registro("catalogo_companias",array("num_cia"),array($gastos[$i]['num_cia_egreso']),"","",$dsn);
	$tpl->assign("nom_cia",$cia[0]['nombre_corto']);
	$tpl->assign("concepto",$gastos[$i]['concepto']);
	$cia1 = obtener_registro("catalogo_companias",array("num_cia"),array($gastos[$i]['num_cia_ingreso']),"","",$dsn);
	$tpl->assign("nom_cia2",$cia1[0]['nombre_corto']);
	$tpl->assign("importe",$gastos[$i]['monto']);
}


$tpl->printToScreen();

?>