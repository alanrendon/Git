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
$tpl->assignInclude("body","./plantillas/ros/ros_precios_con.tpl");
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
$tpl->assign("tabla","precios_guerra");
$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);

$tpl->assign("num_cia",$cia[0]['num_cia']);
$tpl->assign("nom_cia",$cia[0]['nombre_corto']);


$sql="SELECT * FROM precios_guerra where num_cia='".$_GET['num_cia']."' order by codmp";

$reg=ejecutar_script($sql,$dsn);
//print_r ($reg);
$tpl->assign("count",count($reg));
if($reg)
{
	for($i=0;$i<count($reg);$i++)
	{
		$tpl->newBlock("rows");
		$tpl->assign("i",$i);
		$tpl->assign('codmp',$reg[$i]['codmp']);
		$tpl->assign('id',$reg[$i]['id']);
		$mp = obtener_registro("catalogo_mat_primas",array("codmp"),array($reg[$i]['codmp']),"","",$dsn);
		$tpl->assign('nom_mp',$mp[0]['nombre']);
		$tpl->assign('nombre_alt', $reg[$i]['nombre_alt']);
//		nombre formateado
		$tpl->assign('precio_compra1',number_format($reg[$i]['precio_compra'],2,'.',','));
		$tpl->assign('precio_venta1',number_format($reg[$i]['precio_venta'],2,'.',','));

		$tpl->assign('precio_compra',$reg[$i]['precio_compra']);
		$tpl->assign('precio_venta',$reg[$i]['precio_venta']);
		
		$tpl->assign('num_pro', $reg[$i]['num_proveedor']);
		
		switch ($reg[$i]['num_proveedor']) {
			case 13:
				$tpl->assign('nombre_pro', 'POLLOS GUERRA');
			break;
			
			case 482:
				$tpl->assign('nombre_pro', 'CENTRAL DE POLLOS');
			break;
			
			case 1225:
				$tpl->assign('nombre_pro', 'EL RANCHERITO');
			break;
		}
	}
}
else
{
	header("location: ./ros_precios_con.php?codigo_error=1");
	die;
}
// Imprimir el resultado
$tpl->printToScreen();

?>