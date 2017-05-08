<?php
//define ('IDSCREEN',2211); //ID de pantalla
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos.";
$descripcion_error[2] = "El código del producto no existe en la Base de Datos.";

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
$tpl->assignInclude("body","./plantillas/ros/ros_precios_cap.tpl");//$session->ruta/$session->plantilla");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
$tpl->assign("tabla","precios_guerra");

if (!isset($_GET['num_cia'])) 
{
	$tpl->newBlock("obtener_datos");
	$cia = obtener_registro("catalogo_companias",array(),array(),"num_cia","ASC",$dsn);
	for ($i=0; $i<count($cia); $i++) {
		if ($cia[$i]['num_cia'] > 300 && $cia[$i]['num_cia'] < 600 || $cia[$i]['num_cia'] == 702 || $cia[$i]['num_cia']=='704' || $cia[$i]['num_cia']=='708' || $cia[$i]['num_cia']=='709' && $cia[$i]['status']=='t') {
			$tpl->newBlock("nombre_cia");
			$tpl->assign("num_cia",$cia[$i]['num_cia']);
			$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
		}
	}

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
		
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}

	
	$tpl->printToScreen();
	die();
}


$tpl->newBlock("captura");
$tpl->assign("num_cia",$_GET['num_cia']);
$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
$tpl->assign("num_pro",$_GET['num_proveedor']);
switch ($_GET['num_proveedor']) {
	case 13:
		$nombre_pro = "POLLOS GUERRA";
	break;
	
	case 204:
		$nombre_pro = "GONZALEZ AYALA JOSE REGINO";
	break;
	
	case 482:
		$nombre_pro = "CENTRAL DE POLLOS Y CARNES S.A. DE C.V.";
	break;
	
	case 1225:
		$nombre_pro = "EL RANCHERITO S.A. DE C.V.";
	break;
}
$tpl->assign("nombre_pro", $nombre_pro);


$tpl->assign("num_cia",$_GET['num_cia']);






for($i=0;$i<20;$i++)
{
	$tpl->newBlock("rows");
	$tpl->assign("i",$i);
	$tpl->assign("num_cia",$_GET['num_cia']);
	$tpl->assign("num_proveedor", $_GET['num_proveedor']);
	
	$tpl->assign("next",$i+1);
	//$tpl->gotoBlock("_ROOT");
}


$m="SELECT codmp, nombre FROM catalogo_mat_primas where (tipo_cia=false OR codmp=170) order by codmp";
$mp=ejecutar_script($m,$dsn);
for ($i=0; $i<count($mp); $i++) {
		$tpl->newBlock("nombre_mp");
		$tpl->assign("codmp",$mp[$i]['codmp']);
		$tpl->assign("nombre_mp",$mp[$i]['nombre']);
}

//tipo_cia=FALSE


// Si viene de una página que genero error


// Imprimir el resultado
$tpl->printToScreen();
?>

