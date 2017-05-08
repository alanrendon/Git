<?php
//define ('IDSCREEN',6213); //ID de pantalla
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);
// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Lo siento pero no hay registros de inventarios para esta compañía";
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ros/ros_invfm_cap.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla	
$tpl->assign("tabla","inventario_fin_mes");

// Si viene de una página que genero error
//------------------------------------------------Obtener Datos------------------------------------------------------------
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio",date("Y"));

	$cia = obtener_registro("catalogo_companias",array(),array(),"num_cia","ASC",$dsn);
	for ($i=0; $i<count($cia); $i++) 
	{

			$tpl->newBlock("nom_cia");
			$tpl->assign("num_cia",$cia[$i]['num_cia']);
			$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);

	}

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
//------------------------------------------------***Reservas***------------------------------------------------------------

$tpl->newBlock("inventario");
//$sql="SELECT * FROM inventario_real WHERE num_cia='".$_GET['num_cia']."' order by codmp";
//$sql="select num_cia, codmp, existencia, catalogo_mat_primas.nombre from inventario_real join catalogo_mat_primas using(codmp) where num_cia='".$_GET['num_cia']."' order by catalogo_mat_primas.nombre";
$sql="SELECT num_cia, codmp, existencia, nombre, precio_unidad FROM inventario_real JOIN catalogo_mat_primas USING(codmp) WHERE num_cia='$_GET[num_cia]' ORDER BY nombre";

$inv=ejecutar_script($sql,$dsn);

$cia1 = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
$tpl->assign("nombre_cia",$cia1[0]['nombre_corto']);
if(!$inv)
{
	header("location: ./ros_invfm_cap.php?codigo_error=1");
	die;
}


else{
$tpl->assign("numfilas",count($inv));
	for($i=0;$i<count($inv);$i++)
	{
		$tpl->newBlock("rows");
		$tpl->assign("i",$i);
		$tpl->assign("back",($i > 0)?$i-1:count($inv)-1);
		$tpl->assign("next",($i < count($inv)-1)?$i+1:0);
		
		$tpl->assign("fecha",date("d/m/Y", mktime(0,0,0,date("m"),0,date("Y"))));
		$tpl->assign("num_cia",$inv[$i]['num_cia']);
		$tpl->assign("codmp",$inv[$i]['codmp']);
		$tpl->assign("existencia",$inv[$i]['existencia']);
		$tpl->assign("fexistencia",number_format($inv[$i]['existencia'],2,".",","));
		//$mp = obtener_registro("catalogo_mat_primas",array("codmp"),array($inv[$i]['codmp']),"","",$dsn);
		$tpl->assign("nombre_mp",$inv[$i]['nombre']);
		//$pu = obtener_registro("inventario_real",array("num_cia","codmp"),array($inv[$i]['num_cia'], $inv[$i]['codmp']),"","",$dsn);
		$tpl->assign("precio_unidad",$inv[$i]['precio_unidad']);
	}
	
	
	
	$tpl->printToScreen();
}
?>