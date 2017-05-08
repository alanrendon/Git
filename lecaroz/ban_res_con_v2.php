<?php
// DIFERENCIAS DE SALDOS
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_res_con_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['id'])) {
	$saldo_fin = str_replace(",", "", $_POST['saldo_fin']);
	
	$num_cia = $db->query("SELECT num_cia FROM estado_cuenta WHERE id = {$_POST['id'][0]}");
	
	$sql = "";
	foreach ($_POST['id'] as $i => $id) {
		$sql .= "UPDATE estado_cuenta SET fecha_con = '{$_POST['fecha_con'][$i]}', iduser = $_SESSION[iduser], timestamp = now(), tipo_con = 2 WHERE id = $id;\n";
		$sql .= "INSERT INTO mov_con_ban (idesc, imp) VALUES ($id, 'TRUE');\n";
	}
	$sql .= "UPDATE saldos SET saldo_bancos = $saldo_fin WHERE num_cia = {$num_cia[0]['num_cia']} AND cuenta = $_POST[cuenta];\n";
	$db->query($sql);
	
	$tpl->newBlock("cerrar");
	$tpl->assign("cuenta", $_POST['cuenta']);
	$tpl->assign("num_cia", $_POST['num_cia']);
	$tpl->assign("fecha", $_POST['fecha']);
	$tpl->assign("accion", $_POST['accion']);
	$tpl->printToScreen();
	
	die;
}

$cuenta = $_POST['cuenta'];
$num_cia_next = $_POST['num_cia' . $cuenta];
$fecha = $_POST['fecha'];
$accion = $_POST['accion'];
$clabe_cuenta = $cuenta == 1 ? "clabe_cuenta" : "clabe_cuenta2";
$banco = $cuenta == 1 ? "BANORTE" : "SANTANDER";

$id = array();
if (isset($_POST['idabo']))
	foreach ($_POST['idabo'] as $idabo)
		$id[] = $idabo;
if (isset($_POST['idcar']))
	foreach ($_POST['idcar'] as $idcar)
		$id[] = $idcar;

$sql = "SELECT id, num_cia, tipo_mov, importe FROM estado_cuenta WHERE id IN (";
foreach ($id as $i => $value)
	$sql .= $value . ($i < count($id) - 1 ? ", " : ")");
$result = $db->query($sql);

$num_cia = $result[0]['num_cia'];

$cia = $db->query("SELECT nombre_corto, $clabe_cuenta, saldo_bancos FROM saldos LEFT JOIN catalogo_companias USING (num_cia) WHERE num_cia = $num_cia AND cuenta = $cuenta");

$tpl->newBlock("datos");
$tpl->assign("num_cia_next", $num_cia_next);
$tpl->assign("num_cia", $num_cia);
$tpl->assign("nombre_cia", $cia[0]['nombre_corto']);
$tpl->assign("banco", $banco);
$tpl->assign("clabe_cuenta", $cia[0][$clabe_cuenta]);
$tpl->assign("cuenta", $cuenta);
$tpl->assign("fecha", $fecha);
$tpl->assign("accion", $accion);

$tpl->assign("saldo_ini", number_format($cia[0]['saldo_bancos'], 2, ".", ","));

$saldo_fin = $cia[0]['saldo_bancos'];
$tipo_mov = NULL;
foreach ($result as $i => $row) {
	if ($tipo_mov != $row['tipo_mov']) {
		$tipo_mov = $row['tipo_mov'];
		
		$tpl->newBlock("mov");
		$tpl->assign("mov", $tipo_mov == "f" ? "Abonos" : "Cargos");
		
		$total = 0;
	}
	$tpl->newBlock("fila");
	$tpl->assign("next", $i < count($result) - 1 ? $i + 1 : 0);
	$tpl->assign("id", $row['id']);
	$tpl->assign("fecha_con", $fecha);
	$tpl->assign("color", $tipo_mov == "f" ? "0000CC" : "CC0000");
	$tpl->assign("importe", number_format($row['importe'], 2, ".", ","));
	
	$total += $row['importe'];
	$tpl->assign("mov.total", number_format($total, 2, ".", ","));
	
	$saldo_fin += $tipo_mov == "f" ? $row['importe'] : -$row['importe'];
}
$tpl->assign("datos.saldo_fin", number_format($saldo_fin, 2, ".", ","));

$tpl->printToScreen();
?>