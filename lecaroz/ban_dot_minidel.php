<?php
// BORRADO DE UN MOVIMIENTO NO CONCILIADO
// Tablas 'estado_cuenta,mov_banorte'
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
$tpl->assignInclude("body","./plantillas/ban/ban_dot_minidel.tpl");
$tpl->prepare();

// Si ya se modificaron los datos, actualizar la base de datos
if (isset($_POST['id'])) {
	// Borrar registro
	ejecutar_script("DELETE FROM otros_depositos WHERE id = $_POST[id]",$dsn);
	// Cerrar ventana y regresar
	$tpl->newBlock("cerrar");
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("question");
$tpl->assign("id",$_GET['id']);

$tpl->printToScreen();
?>