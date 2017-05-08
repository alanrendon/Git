<?php
// MODIFICACIÓN RÁPIDA DE UN PRODUCTO EN CONTROL DE PRODUCCION
// Tablas 'control_produccion'
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
//$descripcion_error[]

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_pro_minimod.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	$num_orden = ($_POST['num_orden'] != "")?$_POST['num_orden']:"NULL";
	$precio_raya = ($_POST['precio_raya'] != "")?$_POST['precio_raya']:"0";
	$porc_raya = ($_POST['porc_raya'] != "")?$_POST['porc_raya']:"0";
	$precio_venta = ($_POST['precio_venta'] != "")?$_POST['precio_venta']:"0";
	
	ejecutar_script("UPDATE control_produccion SET num_orden=$num_orden,precio_raya=$precio_raya,porc_raya=$porc_raya,precio_venta=$precio_venta WHERE idcontrol_produccion=$_POST[id]",$dsn);
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

// Almacenar valores temporalmente
if (isset($_SESSION['pro'])) unset($_SESSION['pro']);
$result = ejecutar_script("SELECT count(cod_producto) FROM control_produccion WHERE num_cia=".$_POST['num_cia0'],$dsn);
$num_reg = $result[0]['count'];
for ($i=0; $i<$num_reg; $i++)
	if (isset($_POST['id'.$i]))
		$_SESSION['pro'][$_POST['id'.$i]] = $_POST['piezas'.$i];

// Generar pantalla de captura
$tpl->newBlock("modificar");

// Obtener datos del producto
$pro = ejecutar_script("SELECT * FROM control_produccion JOIN catalogo_productos USING(cod_producto) WHERE idcontrol_produccion=$_GET[id]",$dsn);

$tpl->assign("id",$_GET['id']);
$tpl->assign("cod_producto",$pro[0]['cod_producto']);
$tpl->assign("nombre",$pro[0]['nombre']);
$tpl->assign("num_orden",$pro[0]['num_orden']);
$tpl->assign("precio_raya",($pro[0]['precio_raya'] > 0)?number_format($pro[0]['precio_raya'],4,".",""):"0");
$tpl->assign("porc_raya",($pro[0]['porc_raya'] > 0)?number_format($pro[0]['porc_raya'],2,".",""):"0");
$tpl->assign("precio_venta",($pro[0]['precio_venta'] > 0)?number_format($pro[0]['precio_venta'],2,".",""):"0");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
?>