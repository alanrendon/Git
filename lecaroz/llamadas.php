<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
include './includes/pcl.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_POST['id'])) {
	$sql = "";
	for ($i = 0; $i < count($_POST['id']); $i++)
		$sql .= "UPDATE llamadas SET status = 1 WHERE id = {$_POST['id'][$i]};\n";
	$db->query($sql);
	
	header("location: ./llamadas.php?status=$_POST[status]&fecha=$_POST[fecha]");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/lla/llamadas.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['status'])) {
	$sql = "SELECT * FROM llamadas WHERE " . ($_SESSION['iduser'] != 1 ? "iduser = $_SESSION[iduser] AND" : "") . " fecha >= '$_GET[fecha]' " . ($_GET['status'] != "" ? " AND status = $_GET[status]" : "") . " ORDER BY fecha, hora";
	$result = $db->query($sql);
	
	$tpl->newBlock("listado");
	$user = $db->query("SELECT * FROM auth WHERE iduser = $_SESSION[iduser]");
	$tpl->assign("usuario", trim($user[0]['nombre'] . " " . $user[0]['apellido']));
	$tpl->assign("status", $_GET['status']);
	$tpl->assign("fecha", $_GET['fecha']);
	
	if (!$result) {
		$tpl->newBlock("no_result");
	}
	else {
		$tpl->newBlock("result");
		
		for ($i = 0; $i < count($result); $i++) {
			$tpl->newBlock("fila");
			$tpl->assign("id", $result[$i]['id']);
			$tpl->assign("de", $result[$i]['de']);
			$tpl->assign("fecha", $result[$i]['fecha']);
			$tpl->assign("hora", $result[$i]['hora']);
			$tpl->assign("recado", $result[$i]['recado']);
			$tpl->assign("color", $result[$i]['status'] == 1 ? "0000FF" : "FF0000");
			$tpl->assign("status", $result[$i]['status'] == 1 ? "CONTESTADA" : "NO CONTESTADA");
			$tpl->assign("disabled", $result[$i]['status'] == 1 ? "disabled" : "");
		}
	}
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");
$tpl->assign("fecha", date("d/m/Y"));

$tpl->printToScreen();
?>