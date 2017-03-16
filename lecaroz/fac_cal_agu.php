<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/fac/fac_cal_agu.tpl" );
$tpl->prepare();

$puesto = $db->query("SELECT * FROM catalogo_puestos ORDER BY cod_puestos");
for ($i = 0; $i < count($puesto); $i++) {
	$tpl->newBlock("puesto");
	$tpl->assign("cod_puestos", $puesto[$i]['cod_puestos']);
	$tpl->assign("descripcion", $puesto[$i]['descripcion']);
	$tpl->assign("sueldo", $puesto[$i]['sueldo']);
}

$tpl->printToScreen();
?>