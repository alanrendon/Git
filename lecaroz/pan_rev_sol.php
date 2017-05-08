<?php
// CONTROL DE BLOCKS
// Tabla 'BLOCKS'
// Menu

//define ('IDSCREEN',1620); //ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No existen facturas a modificar";
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
$tpl->assignInclude("body","./plantillas/pan/pan_rev_sol.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
// Generar listado de turnos
// Si viene de una página que genero error
if(!isset($_GET['capturistas']))
{
	$tpl->newBlock("obtener_datos");
	$sql="select modificacion_pastel.num_cia, idoperadora, catalogo_operadoras.nombre from modificacion_pastel join catalogo_companias using(num_cia) join catalogo_operadoras using (idoperadora) where fecha_autorizacion is null group by idoperadora,catalogo_operadoras.nombre,num_cia order by idoperadora";
	$usuarios=ejecutar_script($sql,$dsn);
/*	
	$sql="select idoperadora, nombre from catalogo_operadoras where idoperadora not in (11,8) order by nombre";
	$usuarios=ejecutar_script($sql,$dsn);
*/	
	$aux=0;
	if($usuarios){
		for($i=0;$i<count($usuarios);$i++)
		{
			if($aux!=$usuarios[$i]['idoperadora']){
				$tpl->newBlock("capturistas");
				$tpl->assign("num_cap",$usuarios[$i]['idoperadora']);
				$tpl->assign("nom_cap",$usuarios[$i]['nombre']);
			}
			$aux=$usuarios[$i]['idoperadora'];
		}
	}
	else $tpl->assign("disabled","disabled");
	
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



$sql="select * from modificacion_pastel where num_cia in (select num_cia from catalogo_companias where idoperadora=".$_GET['capturistas']." and num_cia < 101 or num_cia=999 order by num_cia) and estado = false order by num_cia";
$facturas=ejecutar_script($sql,$dsn);
if(!$facturas)
{
	header("location: ./pan_rev_sol.php?codigo_error=1");
	die();
}

$tpl->newBlock("pasteles");
$cap=obtener_registro("catalogo_operadoras", array("idoperadora"),array($_GET['capturistas']),"","",$dsn);
$tpl->assign("nom_usuario",$cap[0]['nombre']);
$tpl->assign("cont",count($facturas));

for($i=0;$i<count($facturas);$i++)
{
	$tpl->newBlock("renglones");
	$tpl->assign("i",$i);
	$tpl->assign("id",$facturas[$i]['id']);
	$tpl->assign("num_cia",$facturas[$i]['num_cia']);

	if($facturas[$i]['let_folio']=='X')
		$tpl->assign("let_folio","");
	else
		$tpl->assign("let_folio",$facturas[$i]['let_folio']);
	
	$tpl->assign("num_fact",$facturas[$i]['num_remi']);
	$tpl->assign("descripcion",strtoupper($facturas[$i]['descripcion']));
	$cia=obtener_registro("catalogo_companias", array("num_cia"),array($facturas[$i]['num_cia']),"","",$dsn);
	$tpl->assign("nom_cia",$cia[0]['nombre_corto']);
	if($facturas[$i]['kilos_mas']=='t' and $facturas[$i]['kilos_menos']=='f')
		$tpl->assign("kilos","KILOS DE MAS");
	else if($facturas[$i]['kilos_menos']=='t' and $facturas[$i]['kilos_mas']=='f')
		$tpl->assign("kilos","KILOS DE MENOS");
	else if ($facturas[$i]['kilos_menos']=='f' and $facturas[$i]['kilos_mas']=='f')
		$tpl->assign("kilos"," ");
	
	if($facturas[$i]['base']=="t")
		$tpl->assign("base","Modificar");
	
	if($facturas[$i]['otros']=="t")
		$tpl->assign("otros","Modificar");
	
	if($facturas[$i]['precio_unidad']=="t")
		$tpl->assign("precio_unidad","Modificar");

	if($facturas[$i]['cancelar']=="t")
		$tpl->assign("cancelar","Cancelar");

	if($facturas[$i]['perdida']=="t")
		$tpl->assign("perdida","Modificar");

	if($facturas[$i]['cambio_fecha']=="t")
		$tpl->assign("cambio_fecha","Modificar");
	
	if($facturas[$i]['fecha_nueva']=="t")
		$tpl->assign("fecha_nueva","Modificar");
}


$tpl->printToScreen();
?>