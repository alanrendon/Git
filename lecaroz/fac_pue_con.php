<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No se encontraron registros";
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
$tpl->assignInclude("body","./plantillas/fac/fac_pue_con.tpl");
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
$tpl->assign("tabla","catalogo_aseguradoras");

$sql="SELECT * FROM catalogo_puestos WHERE giro = $_SESSION[tipo_usuario] order by cod_puestos";

$reg=ejecutar_script($sql,$dsn);
//print_r ($reg);
$tpl->assign("count",count($reg));
if($reg)
{
	for($i=0;$i<count($reg);$i++)
	{
		$tpl->newBlock("rows");
		$tpl->assign("i",$i);
		$tpl->assign('cod_puestos',$reg[$i]['cod_puestos']);
		$tpl->assign('descripcion',$reg[$i]['descripcion']);
		$tpl->assign('sueldo1',number_format($reg[$i]['sueldo'],2,'.',','));
		$tpl->assign("sueldo",$reg[$i]['sueldo']);
	}
	$tpl->gotoBlock("_ROOT");
	$tpl->assign("count",$i);
}
else
{
	header("location: ./fac_pue_con.php?codigo_error=1");
	die;
}
// Imprimir el resultado
$tpl->printToScreen();

?>