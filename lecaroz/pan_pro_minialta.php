<?php
// ALTA RÁPIDA DE UN PRODUCTO EN CONTROL DE PRODUCCION
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
$tpl->assignInclude("body","./plantillas/pan/pan_pro_minialta.tpl");
$tpl->prepare();

if (isset($_GET['alta'])) {
	$num_cia = $_POST['num_cia'];
	$cod_turno = $_POST['cod_turno'];
	$cod_producto = $_POST['cod_producto'];
	$num_orden = ($_POST['num_orden'] != "")?$_POST['num_orden']:"NULL";
	$precio_raya = ($_POST['precio_raya'] != "")?$_POST['precio_raya']:"NULL";
	$porc_raya = ($_POST['porc_raya'] != "")?$_POST['porc_raya']:"NULL";
	$precio_venta = ($_POST['precio_venta'] != "")?$_POST['precio_venta']:"NULL";
	
	ejecutar_script("INSERT INTO control_produccion (cod_turno,cod_producto,num_cia,precio_raya,porc_raya,precio_venta,num_orden) VALUES ($cod_turno,$cod_producto,$num_cia,$precio_raya,$porc_raya,$precio_venta,$num_orden)",$dsn);
	
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
$tpl->newBlock("alta");

$tpl->assign("num_cia",$_GET['num_cia']);
$tpl->assign("cod_turno",$_GET['cod_turno']);
$cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia=$_GET[num_cia]",$dsn);
$turno = ejecutar_script("SELECT descripcion FROM catalogo_turnos WHERE cod_turno=$_GET[cod_turno]",$dsn);
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
$tpl->assign("nombre_turno",$turno[0]['descripcion']);

// Generar listado de compañías
$pro = ejecutar_script("SELECT * FROM catalogo_productos ORDER BY cod_producto ASC",$dsn);
for ($i=0; $i<count($pro); $i++) {
	$tpl->newBlock("nombre_pro");
	$tpl->assign("num_pro",$pro[$i]['cod_producto']);
	$tpl->assign("nombre_pro",$pro[$i]['nombre']);
}

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