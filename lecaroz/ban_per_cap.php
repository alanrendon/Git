<?php
// CONCILIACION DE EFECTIVOS
// Tablas 'estado_cuenta'
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

if (isset($_POST['num_cia'])) {
	$sql = "";
	for ($i = 0; $i < count($_POST['num_cia']); $i++)
		if ($id = $db->query("SELECT id FROM perdidas WHERE num_cia = {$_POST['num_cia'][$i]}"))
			$sql .= "UPDATE perdidas SET monto = " . ($_POST['monto'][$i] != 0 ? $_POST['monto'][$i] : "NULL") . " WHERE id = {$id[0]['id']};\n";
		else
			$sql .= "INSERT INTO perdidas (num_cia, monto) VALUES ({$_POST['num_cia'][$i]}, " . ($_POST['monto'][$i] != 0 ? $_POST['monto'][$i] : "NULL") . ");\n";
	
	$db->query($sql);
	$db->desconectar();
	
	header("location: ./ban_per_cap.php");
	die;
}

// --------------------------------- Descripcion de errores --------------------------------------------------
//$descripcion_error[]

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body","./plantillas/ban/ban_per_cap.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$sql = "SELECT num_cia, nombre_corto, monto FROM catalogo_companias LEFT JOIN perdidas USING (num_cia) WHERE num_cia NOT IN (999) ORDER BY num_cia";
$result = $db->query($sql);

for ($i = 0; $i < count($result); $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("next", $i < count($result) - 1 ? $i + 1 : 0);
	$tpl->assign("num_cia", $result[$i]['num_cia']);
	$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
	$tpl->assign("monto", number_format($result[$i]['monto'], 2, ".", ""));
}

$tpl->printToScreen();
$db->desconectar();
?>