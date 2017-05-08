<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_act_bal.tpl");
$tpl->prepare();

$tabla_bal = $_GET['num_cia'] > 0 ? ($_GET['num_cia'] < 100 ? "balances_pan" : "balances_ros") : "balances_pan";

if (!isset($_GET['next'])) {
	$sql = "SELECT id FROM $tabla_bal WHERE mes = $_GET[mes] AND anio = $_GET[anio]";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
	$sql .= " LIMIT 1";
	$result = $db->query($sql);
	
	if ($result) {
		$tpl->newBlock("question");
		$tpl->assign("num_cia", $_GET['num_cia']);
		$tpl->assign("mes", $_GET['mes']);
		$tpl->assign("anio", $_GET['anio']);
		$tpl->printToScreen();
		die;
	}
}

$tpl->newBlock("wait");
$tpl->printToScreen();

// ------------------------------- Comenzar actualización de los datos de balance -------------------------------
function balance_pan($num_cia, $mes, $anio) {
	
}


die();
?>