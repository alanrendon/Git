<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No tenemos tanques de tas registrados para esta compañía";

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
$tpl->assignInclude("body","./plantillas/fac/fac_gas_mod.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");


	
	// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia']),"","",$dsn);
$tpl->assign("num_cia",$cia[0]['num_cia']);
$tpl->assign("nom_cia",$cia[0]['nombre_corto']);

$tpl->assign("tabla",$_GET['tabla']);


//print_r ($_POST);
//echo count($_POST['cont']);
$var=0;
if($_POST['cont'] >0)
{
	for($i=0;$i<$_POST['cont'];$i++)
	{
		if($_POST['modificar'.$i]==1)
		{
			$tpl->newBlock("rows");
			$tpl->assign("i",$var);
			$var++;
			$tpl->assign('id',$_POST['id'.$i]);
			$tpl->assign('num_tanque',$_POST['num_tanque'.$i]);
			$tpl->assign('nombre',$_POST['nombre'.$i]);
			$tpl->assign('capacidad',$_POST['capacidad'.$i]);
		}
	}
	$tpl->newBlock("contador");
	$tpl->assign("cont",$var);
	
}
else
{
	header("location: ./fac_gas_con.php");
	die;
}
// Imprimir el resultado
$tpl->printToScreen();

?>