<?php
// MODIFICACION RAPIDA DE DEPOSITO
// Tabla 'estado_cuenta'
// Menu 'Panaderías->Producción'

//define ('IDSCREEN',1241); // ID de pantalla

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
$descripcion_error[1] = "No hay resultados";
$descripcion_error[2] = "Contraseña incorrecta";
$descripcion_error[3] = "Ha cambiado de usuario";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_esc_minidel.tpl");
$tpl->prepare();

if (!(isset($_SESSION['esc_mod']) && $_SESSION['esc_mod'] == $_SESSION['iduser'])) {
	$tpl->newBlock("cerrar_error");
	$tpl->printToScreen();
}

// Modificar datos
if (isset($_POST['id'])) {
	// Obtener datos anteriores del movimiento
	$sql = "SELECT * FROM estado_cuenta WHERE id = $_POST[id]";
	$mov = ejecutar_script($sql,$dsn);

	$sql = "DELETE FROM estado_cuenta WHERE id = $_POST[id]";
	ejecutar_script($sql,$dsn);
	
	if (isset($_POST['saldo_libros'])) {
		// Actualizar saldo en libros
		$sql = "UPDATE saldos SET saldo_libros = saldo_libros ".($mov[0]['tipo_mov'] == "f" ? "-" : "+")." ".$mov[0]['importe']." WHERE id = $_POST[id]";
		ejecutar_script($sql,$dsn);
	}
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

// Mostrar pantalla de modificación
$tpl->newBlock("borrar");
$tpl->assign("id",$_GET['id']);
$tpl->printToScreen();
?>