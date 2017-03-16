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
$tpl->assignInclude("body","./plantillas/ban/ban_admin_con.tpl");
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
$tpl->assign("tabla","catalogo_administradores");

$sql="SELECT * FROM catalogo_administradores order by idadministrador";

$reg=ejecutar_script($sql,$dsn);
//print_r ($reg);
$tpl->assign("count",count($reg));
if($reg)
{
	for($i=0;$i<count($reg);$i++)
	{
		$tpl->newBlock("rows");
		$tpl->assign("i",$i);
		$tpl->assign('idadministrador',$reg[$i]['idadministrador']);
		$cia = obtener_registro("catalogo_administradores",array("idadministrador"),array($reg[$i]['idadministrador']),"","",$dsn);
		$tpl->assign('nombre_administrador',$cia[0]['nombre_administrador']);
	}
}
else
{
	header("location: ./ban_admin_con.php?codigo_error=1");
	die;
}
// Imprimir el resultado
$tpl->printToScreen();

?>