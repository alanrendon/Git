<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

// --------------------------------- Descripción de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

if (isset($_GET['c'])) {
	$sql = "SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[c]";
	$cia = $db->query($sql);
	
	if ($cia)
		echo $cia[0]['nombre_corto'];
	
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_dot_mod_date.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	$sql = "UPDATE otros_depositos SET num_cia = $_POST[num_cia], fecha = '$_POST[fecha]', tsmod = now(), iduser = $_SESSION[iduser] WHERE id = $_POST[id]";
	$db->query($sql);
	
	$tpl->newBlock('close');
	die($tpl->printToScreen());
}

$sql = "SELECT num_cia, nombre_corto, fecha, importe FROM otros_depositos LEFT JOIN catalogo_companias USING (num_cia) WHERE id = $_GET[id]";
$result = $db->query($sql);

$tpl->newBlock('mod');
$tpl->assign('id', $_GET['id']);
$tpl->assign('num_cia', $result[0]['num_cia']);
$tpl->assign('nombre', $result[0]['nombre_corto']);
$tpl->assign('fecha', $result[0]['fecha']);
$tpl->assign('importe', number_format($result[0]['importe'], 2, '.', ','));

$tpl->printToScreen();
?>