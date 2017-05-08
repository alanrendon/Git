<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No tenemos productos registrados para este proveedor";
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
$tpl->assignInclude("body","./plantillas/pan/pan_prec_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_datos");
	
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

	$tpl->printToScreen();
	die();
}


$tpl->newBlock('listado');
$tpl->assign("tabla","control_produccion");

$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);

$tpl->assign("num_cia",$cia[0]['num_cia']);
$tpl->assign("nom_cia",$cia[0]['nombre_corto']);


$sql="SELECT * FROM control_produccion where num_cia='".$_GET['num_cia']."' ORDER BY num_cia, cod_turno, num_orden";

$reg=ejecutar_script($sql,$dsn);
//print_r ($reg);
$tpl->assign("count",count($reg));
$tmp=0;
if($reg)
{
	for($i=0;$i<count($reg);$i++)
	{
		if($reg[$i]['cod_turno']!=$tmp)
		{
			$tpl->newBlock("turnos");
			$turno = obtener_registro("catalogo_turnos",array("cod_turno"),array($reg[$i]['cod_turno']),"","",$dsn);			
			$tpl->assign("turno",$turno[0]['descripcion']);
			$tmp=0;
		}
		$tmp=$reg[$i]['cod_turno'];
		
		$tpl->newBlock("rows");
		$tpl->assign("i",$i);
		$tpl->assign('cod_producto',$reg[$i]['cod_producto']);
		$tpl->assign('id',$reg[$i]['idcontrol_produccion']);
		$prod = obtener_registro("catalogo_productos",array("cod_producto"),array($reg[$i]['cod_producto']),"","",$dsn);
		$tpl->assign('nom_prod',$prod[0]['nombre']);
		$tpl->assign('precio_raya1',number_format($reg[$i]['precio_raya'],2,'.',','));
		$tpl->assign('porcentaje1',number_format($reg[$i]['porc_raya'],2,'.',','));
		$tpl->assign('precio_venta1',number_format($reg[$i]['precio_venta'],2,'.',','));
		$tpl->assign('orden',$reg[$i]['num_orden']);
		$tpl->assign('turno',$reg[$i]['cod_turno']);

		$tpl->assign('precio_raya',$reg[$i]['precio_raya']);
		$tpl->assign('porcentaje',$reg[$i]['porc_raya']);
		$tpl->assign('precio_venta',$reg[$i]['precio_venta']);
		$tpl->gotoBlock("turnos");
		
	}
}
else
{
	header("location: ./pan_prec_con.php?codigo_error=1");
	die;
}
	
		


$tpl->gotoBlock("listado");
// Imprimir el resultado
$tpl->printToScreen();

?>