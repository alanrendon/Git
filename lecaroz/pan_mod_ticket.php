<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("la estoy modificando");

$descripcion_error[1] = "";
$numfilas = 25;


// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/pan/pan_mod_ticket.tpl");
$tpl->prepare();

if (isset($_POST['num_cia'])) {
	$sql = "DELETE FROM corte_tmp WHERE num_cia = $_POST[num_cia] AND fecha = '$_POST[fecha]';\n";
	foreach ($_POST['pan'] as $ticket)
		if ($ticket > 0)
			$sql .= "INSERT INTO corte_tmp (num_cia, fecha, ticket, tipo) VALUES ($_POST[num_cia], '$_POST[fecha]', $ticket, 1);\n";
	foreach ($_POST['pastel'] as $ticket)
		if ($ticket > 0)
			$sql .= "INSERT INTO corte_tmp (num_cia, fecha, ticket, tipo) VALUES ($_POST[num_cia], '$_POST[fecha]', $ticket, 2);\n";
	
	$db->query($sql);
	$tpl->newBlock('cerrar');
	$tpl->printToScreen();
	die;
}

$pan = $db->query("SELECT ticket FROM corte_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND tipo = 1 ORDER BY ticket");
$pastel = $db->query("SELECT ticket FROM corte_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND tipo = 2 ORDER BY ticket");

$tpl->newBlock('datos');
$tpl->assign('num_cia', $_GET['num_cia']);
$tpl->assign('fecha', $_GET['fecha']);

if ($pan)
	foreach ($pan as $i => $reg)
		$tpl->assign('pan' . $i, $reg['ticket']);

if ($pastel)
	foreach ($pastel as $i => $reg)
		$tpl->assign('pastel' . $i, $reg['ticket']);

$tpl->printToScreen();
?>