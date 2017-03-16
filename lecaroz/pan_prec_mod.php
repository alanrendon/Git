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
$tpl->assignInclude("body","./plantillas/pan/pan_prec_mod.tpl");
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

$cias = obtener_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia']),"","",$dsn);
$tpl->assign("num_cia",$cias[0]['num_cia']);
$tpl->assign("nom_cia",$cias[0]['nombre_corto']);

$tpl->assign("tabla","catalogo_produccion");
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
			$tpl->assign('cod_producto',$_POST['cod_producto'.$i]);
			$tpl->assign('id',$_POST['id'.$i]);
			$prod = obtener_registro("catalogo_productos",array("cod_producto"),array($_POST['cod_producto'.$i]),"","",$dsn);
			$tpl->assign('nom_producto',$prod[0]['nombre']);
			$turno = obtener_registro("catalogo_turnos",array("cod_turno"),array($_POST['cod_turno'.$i]),"","",$dsn);
			$tpl->assign('turno',$turno[0]['descripcion']);
			$tpl->assign('precio_raya',$_POST['precio_raya'.$i]);
			$tpl->assign('porc_raya',$_POST['porcentaje'.$i]);
			$tpl->assign('precio_venta',$_POST['precio_venta'.$i]);
			$tpl->assign('orden',$_POST['orden'.$i]);
		}
	}
	$tpl->newBlock("contador");
	$tpl->assign("cont",$var);
	
}
else
{
	header("location: ./pan_prec_mod.php");
	die;
}
// Imprimir el resultado
$tpl->printToScreen();

?>