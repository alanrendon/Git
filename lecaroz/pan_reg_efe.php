<?php
// REGISTRO DEL ULTIMO EFECTIVO CAPTURADO
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

// --------------------------------- Descripcion de errores --------------------------------------------------
//$descripcion_error[]

if (isset($_GET['num_cia'])) {
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_GET['fecha'],$temp);
	$fecha1 = "1/$temp[2]/$temp[3]";
	$fecha2 = $_GET['fecha'];
	
	$sql = "UPDATE total_panaderias SET efe='TRUE',exp='TRUE',gas='TRUE',pro='TRUE',pas='TRUE' WHERE num_cia=$_GET[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	ejecutar_script($sql,$dsn);
	header("location: ./pan_reg_efe.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_reg_efe.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Obtener compañías por capturista
if ($_SESSION['iduser'] != 1 && $_SESSION['iduser'] != 4)
	$sql = "SELECT num_cia,nombre_corto FROM catalogo_operadoras JOIN catalogo_companias USING (idoperadora) WHERE iduser = $_SESSION[iduser] AND (num_cia <= 300 OR num_cia IN (702,703)) ORDER BY num_cia";
else
	$sql = "SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia <= 300 OR num_cia IN (702, 703) ORDER BY num_cia";
$num_cia = ejecutar_script($sql,$dsn);

$fecha1 = date("d/m/Y",mktime(0,0,0,date("m"),1,date("Y")));
$fecha2 = date("d/m/Y",mktime(0,0,0,date("m"),date("d")-1,date("Y")));
$tpl->assign("fecha",$fecha2);
for ($i=0; $i<count($num_cia); $i++) {
	$sql = "SELECT id FROM total_panaderias WHERE num_cia = {$num_cia[$i]['num_cia']} AND fecha <= '$fecha2' AND (efe = 'FALSE' OR exp = 'FALSE' OR gas = 'FALSE' OR pro = 'FALSE' OR pas = 'FALSE') ORDER BY fecha DESC LIMIT 1";
	$ok = ejecutar_script($sql,$dsn);
	if ($ok) {
		$tpl->newBlock("num_cia");
		$tpl->assign("num_cia",$num_cia[$i]['num_cia']);
		$tpl->assign("nombre_cia",$num_cia[$i]['nombre_corto']);
	}
}

$tpl->printToScreen();
?>