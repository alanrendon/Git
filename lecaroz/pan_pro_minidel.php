<?php
// BORRAR RAPIDO DE UN PRODUCTO EN CONTROL DE PRODUCCION
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
$tpl->assignInclude("body","./plantillas/pan/pan_pro_minidel.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	ejecutar_script("DELETE FROM control_produccion WHERE idcontrol_produccion=$_POST[id]",$dsn);
	
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
$tpl->newBlock("question");
$tpl->assign("id",$_GET['id']);

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message","El empleado no. $_GET[codigo_error] ya tiene un prestamo");	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
?>