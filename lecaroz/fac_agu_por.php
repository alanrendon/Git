<?php
// CATALOGO DE PORCENTAJES DE AGUINALDOS
// Tablas varias ''
// Menu 'Panaderías->Producción'

define ('IDSCREEN',1222); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();
// --------------------------------- Capturar datos ----------------------------------------------------------
if (isset($_GET['porcentaje_nuevo'])) {
	if ($_GET['porcentaje_nuevo'] != $_GET['porcentaje_ant']) {
		$fecha = date("d/m/Y");
		$sql = "INSERT INTO porcentaje_aguinaldo (porcentaje,fecha) VALUES ($_GET[porcentaje_nuevo],'$fecha')";
		ejecutar_script($sql,$dsn);
	}
	
	header("location: ./fac_agu_por.php");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_agu_por.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Obtener ultimo porcentaje
$sql = "SELECT * FROM porcentaje_aguinaldo ORDER BY id DESC LIMIT 1";
$result = ejecutar_script($sql,$dsn);

$tpl->assign("porcentaje",$result ? number_format($result[0]['porcentaje'],2,".",",") : "NO HAY PORCENTAJES");
$tpl->assign("porcentaje_ant",$result ? number_format($result[0]['porcentaje'],2,".","") : "");

$tpl->printToScreen();
?>