<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

if (isset($_GET['accion'])) {
	if ($_GET['accion'] == 'eliminar') {
		$sql = 'UPDATE "CatalogoTiposContacto" SET "Status" = 0 WHERE "IdTipo" = ' . $_GET['id'];
		$db->query($sql);
		die;
	}
	
	die;
}

$tpl = new TemplatePower('smarty/templates/ConsultaTipoContacto.tpl');
$tpl->prepare();

// Seleccionar script para menu
$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$sql = 'SELECT "IdTipo", "Tipo" FROM "CatalogoTiposContacto" WHERE "Status" = 1 ORDER BY "Tipo"';
$result = $db->query($sql);

if ($result)
	foreach ($result as $i => $reg) {
		$tpl->newBlock('tipo');
		$tpl->assign('color_row', ($i + 1) % 2 == 0 ? 'on' : 'off');
		
		$tpl->assign('id', $reg['IdTipo']);
		$tpl->assign('Tipo', $reg['Tipo']);
	}

$tpl->printToScreen();
?>