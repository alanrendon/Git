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
$descripcion_error[1] = "Lo siento pero no ha capturado datos para esta reserva";
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_res_pago.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla	
///$tpl->assign("tabla",$session->tabla);


// Si viene de una página que genero error
//------------------------------------------------Obtener Datos------------------------------------------------------------
if (!isset($_GET['cod_reserva'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio",date("Y")-1);

	$res = obtener_registro("catalogo_reservas",array(),array(),"tipo_res","ASC",$dsn);
	//print_r ($res);
	for ($i=0; $i<count($res); $i++) 
	{
			$tpl->newBlock("nombre_reserva");
			$tpl->assign("tipo_res",$res[$i]['tipo_res']);
			$tpl->assign("descripcion",$res[$i]['descripcion']);
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

//$verifica = obtener_registro("reservas_cias",array("num_cia","cod_reserva"),array($_GET['num_cia'], $_GET['cod_reserva']),"","", $dsn);
//$sql="SELECT distinct(num_cia), pagado  FROM reservas_cias WHERE cod_reserva='".$_GET['cod_reserva']."' and anio='".(date("Y")-1)."' order by num_cia";
$sql = "SELECT num_cia FROM reservas_cias WHERE cod_reserva=$_GET[cod_reserva] AND anio=".(date("Y")-1)." AND pagado IS NULL GROUP BY num_cia ORDER BY num_cia";
$reserva = ejecutar_script($sql,$dsn);

if ($reserva==false)
{
	header("location: ./bal_res_pago.php?codigo_error=1");
	die;
}

$tpl->assign("tabla","reservas_cias");
$tpl->newBlock("reservas");
$tpl->assign("anio_ac",date("Y")-1);
$res1 = obtener_registro("catalogo_reservas",array("tipo_res"),array($_GET['cod_reserva']),"tipo_res","ASC",$dsn);
$tpl->assign("reserva",$res1[0]['descripcion']);
$tpl->assign("res1",$_GET['cod_reserva']);
$var=0;
for ($i=0;$i<count($reserva);$i++){
	
	//if ($reserva[$i]['pagado']<=0){
		$tpl->newBlock("rows");
		$tpl->assign("i",/*$var*/$i);
		$tpl->assign("next",$i < count($reserva)-1 ? $i + 1 : 0);
		$tpl->assign("num_cia",$reserva[$i]['num_cia']);
		$cia_r = obtener_registro("catalogo_companias",array("num_cia"),array($reserva[$i]['num_cia']),"","",$dsn);
		$tpl->assign("nom_cia",$cia_r[0]['nombre_corto']);
		$var++;
	//}
	
}
$tpl->gotoBlock("reservas");
$tpl->assign("cont",$var);
$tpl->printToScreen();
?>