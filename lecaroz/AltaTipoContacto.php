<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

if (isset($_POST['Tipo'])) {
	$sql = 'INSERT INTO "CatalogoTiposContacto" ("Tipo", "Status") VALUES';
	$sql .= " ('$_POST[Tipo]', 1);\n";
	
	$db->query($sql);
	die(header('location: AltaTipoContacto.php'));
}

$tpl = new TemplatePower('smarty/templates/AltaTipoContacto.tpl');
$tpl->prepare();

// Seleccionar script para menu
$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>