<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

if (isset($_POST['cod'])) {
	$concepto = strtoupper(trim($_POST['concepto']));
	
	if (strlen($concepto) > 0)
		$sql = "INSERT INTO cat_conceptos_descuentos (cod, concepto, tipo) VALUES ($_POST[cod], '$concepto', $_POST[tipo])";
	
	if (isset($sql)) $db->query($sql);
	
	die(header('location: ./zap_con_des_alta.php'));
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/zap/zap_con_des_alta.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$result = $db->query('SELECT cod FROM cat_conceptos_descuentos ORDER BY cod');

$lastcod = 0;
if (!$result) $lastcod = 1;
else {
	foreach ($result as $i => $reg)
		if ($i + 1 != $reg['cod']) {
			$lastcod = $i + 1;
			break;
		}
		else
			$lastcod = $i + 2;
}

$tpl->assign('cod', $lastcod);

$tpl->printToScreen();
?>