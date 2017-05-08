<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No tenemos productos registrados para esta compañía";
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
$tpl->assignInclude("body","./plantillas/ros/ros_porcentaje_con.tpl");
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
	$tpl->printToScreen();
	die();

}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}


$tpl->newBlock('listado');
$tpl->assign("tabla","porcentajes_facturas");

$sql="SELECT * FROM porcentajes_facturas order by num_cia";

$reg=ejecutar_script($sql,$dsn);
//print_r ($reg);
$tpl->assign("count",count($reg));
if($reg)
{
	for($i=0;$i<count($reg);$i++)
	{
		$tpl->newBlock("rows");
		$tpl->assign("i",$i);
		$tpl->assign('num_cia',$reg[$i]['num_cia']);
		$tpl->assign('id',$reg[$i]['idporcentajes_facturas']);
		$cia = obtener_registro("catalogo_companias",array("num_cia"),array($reg[$i]['num_cia']),"","",$dsn);
		$tpl->assign('nom_cia',$cia[0]['nombre_corto']);
		$tpl->assign('porcentaje1',$reg[$i]['porcentaje_795']);
		$tpl->assign('porcentaje2',$reg[$i]['porcentaje_13']);
	}
}
else
{
	header("location: ./ros_pesos_con.php?codigo_error=1");
	die;
}
// Imprimir el resultado
$tpl->printToScreen();

?>