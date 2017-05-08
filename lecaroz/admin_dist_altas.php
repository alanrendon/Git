<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No tenemos registrados accionistas";
$descripcion_error[2] = "NO EXISTE LA COMPAÑÍA";
//$descripcion_error[2] = "Número de Gasto no existe en la Base de Datos, revisa bien codigo del gasto";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/adm/admin_dist_altas.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

	
// Si viene de una página que genero error

if (!isset($_GET['cia'])) {
	$tpl->newBlock("obtener_datos");

	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}

	$tpl->printToScreen();
	die();
}




$tpl->newBlock('distribuciones');

if(!(existe_registro("catalogo_companias",array('num_cia'),array($_GET['cia']),$dsn)))
{
	header("location: ./admin_dist_altas.php?codigo_error=2");
	die();
}



$tpl->assign("i",$_GET['numero']);
$tpl->assign("conta",$_GET['numero']);
$cia=obtener_registro("catalogo_companias",array('num_cia'),array($_GET['cia']),"","",$dsn);
$tpl->assign("num_cia",$cia[0]['num_cia']);
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);


$sql="SELECT * FROM catalogo_accionistas order by num";
$reg=ejecutar_script($sql,$dsn);
if($reg)
{
	for($i=0;$i<count($reg);$i++)
	{
		$tpl->newBlock("nombre_acc");
		$tpl->assign("num_accionista",$reg[$i]['num']);
		$tpl->assign('nombre_corto',$reg[$i]['nombre_corto']);
	}
}
else
{
	header("location: ./admin_dist_altas.php?codigo_error=1");
	die;
}

for($i=0;$i<$_GET['numero'];$i++)
{
	$tpl->newBlock("rows");
	$tpl->assign("i",$i);
	$tpl->assign("next",$i+1);
}

// Imprimir el resultado
$tpl->printToScreen();


?>