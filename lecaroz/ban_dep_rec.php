<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_dep_rec.tpl");
$tpl->prepare();

if (isset($_POST['dia'])) {
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_SESSION['efe']['fecha1'], $fecha);
	
	$sql = "";
	foreach ($_POST['dia'] as $dia)
		$sql .= "UPDATE estado_cuenta SET fecha = fecha $_POST[dir] interval '1 day' WHERE num_cia = " . $_SESSION['efe']['num_cia' . $_SESSION['efe']['next']] . " AND fecha = '$dia/$fecha[2]/$fecha[3]' AND cod_mov IN (1,16);\n";//echo $sql;die;
	$db->query($sql);
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("question");

$tpl->assign('dir', $_GET['dir']);
$tpl->assign('direccion', $_GET['dir'] == '+' ? 'arriba' : 'abajo');

foreach ($_GET['dia'] as $dia) {
	$tpl->newBlock("dia");
	$tpl->assign("dia", $dia);
}

$tpl->printToScreen();
?>