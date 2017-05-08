<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// Cambiar estatus
if (isset($_GET['ok'])) {
	$sql = "UPDATE status_cheques SET ok = 'TRUE'";
	$db->query($sql);
	$db->desconectar();
	header("location: ./ban_che_shift.php");
	die;
}

// Recorrer folios
if (isset($_POST['num_cheque'])) {
	// Incrementar los numeros de cheques +1 a partir del folio dado
	$sql = "UPDATE cheques SET num_cheque = num_cheque + 1 WHERE num_cheque >= $_POST[num_cheque];\n";
	// Insertar un cheque en blanco con el folio cancelado
	$sql .= "INSRET INTO cheques (num_cheque, fecha_cancelacion) VALUES ($_POST[num_cheque], CURRENT_DATE);\n";
	// Insertar registro del folio cancelado
	$sql .= "INSERT INTO num_cheque_cancelados (num_cheque, fecha_cancelacion) VALUES ($_POST[num_cheque], CURRENT_DATE);\n";
	$db->query($sql);
	$db->desconectar();
	header("location: ./ban_che_shift.php");
	die;
}

// --------------------------------- Delaracion de variables -------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/ban/ban_che_shift.tpl" );
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['shift'])) {
	$tpl->newBlock("folio");
	$tpl->printToScreen();
	$db->desconectar();
	die;
}

// Obtener ultimo estatus de la impresión de los cheques
$sql = "SELECT * FROM status_cheques";
$status = $db->query($sql);

if ($status[0]['ok'] == "f") {
	$tpl->newBlock("pregunta");
}
else {
	header("location: ./ban_che_imp.php");
	die;
}

$tpl->printToScreen();
$db->desconectar();
?>