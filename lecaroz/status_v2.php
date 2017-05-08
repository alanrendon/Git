<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/status_v2.tpl" );
$tpl->prepare();

$tpl->assign('user', $_SESSION['username']);
//$tpl->assign('fecha_efe', date('d/m/Y', date('d') < 5 ? mktime(0, 0, 0, date('n'), 0, date('Y')) : mktime(0, 0, 0, date('n'), date('d'), date('Y'))));

$sql = "SELECT path, descripcion FROM menus_permisos LEFT JOIN menus USING (idmenu) WHERE iduser = $_SESSION[iduser] AND permiso = TRUE";
$result = $db->query($sql);

if ($result)
	foreach ($result as $reg) {
		$tpl->newBlock('menu');
		$tpl->assign('menupath', $reg['path']);
		$tpl->assign('menu', $reg['descripcion']);
	}
$tpl->printToScreen();
?>