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
$tpl->assignInclude("body","./plantillas/bal/bal_res_pago1.tpl");
$tpl->prepare();
//Seleccionar el script para menu
;$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla	
///$tpl->assign("tabla",$session->tabla);


// Si viene de una página que genero error
//------------------------------------------------Obtener Datos------------------------------------------------------------

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
	$tpl->printToScreen();
	die();
}
//------------------------------------------------***Reservas***------------------------------------------------------------

$var=0;
$total=0;
//print_r($_POST);
//echo count($_POST);

$tpl->assign("anio_ac",date("Y")-1);
$res1 = obtener_registro("catalogo_reservas",array("tipo_res"),array($_POST['reserva']),"","",$dsn);
$tpl->assign("reserva",$res1[0]['descripcion']);
$tpl->assign("res",$_POST['reserva']);
$contador=0;
for ($i=0;$i<$_POST['cont'];$i++){
	
	if ($_POST['pagado'.$i] > 0){
		$tpl->newBlock("rows");
		$tpl->assign("i",$i);
		$tpl->assign("num_cia",$_POST['num_cia'.$i]);
		$cia_r = obtener_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia'.$i]),"","",$dsn);
		$tpl->assign("nom_cia",$cia_r[0]['nombre_corto']);
		$tpl->assign("pago",$_POST['pagado'.$i]);
		$tpl->assign("pago1",number_format(($_POST['pagado'.$i]),2,'.',','));
		$total+=$_POST['pagado'.$i];
		$contador=$i;
	}
	
}
$tpl->gotoBlock("_ROOT");
$contador++;
$tpl->assign("contador",$contador);

$tpl->newBlock("total");
$tpl->assign("total",number_format($total,2,'.',','));


$tpl->printToScreen();
?>