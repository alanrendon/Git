<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_gas_minimod_v2.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	$fac = $db->query("SELECT num_cia, num_proveedor AS num_pro, num_fact FROM facturas WHERE id = $_POST[id]");
	
	$sql = "UPDATE facturas SET codgastos = $_POST[codgastos] WHERE id = $_POST[id];\n";
	$sql .= "UPDATE pasivo_proveedores SET codgastos = $_POST[codgastos] WHERE num_cia = {$fac[0]['num_cia']} AND num_proveedor = {$fac[0]['num_pro']} AND num_fact = '{$fac[0]['num_fact']}';\n";
	$db->query($sql);
	
	$tpl->newBlock('cerrar');
	$tpl->printToScreen();
	die;
}

$fac = $db->query("SELECT codgastos AS cod, descripcion AS desc FROM facturas LEFT JOIN catalogo_gastos USING (codgastos) WHERE id = $_GET[id]");

$tpl->newBlock('datos');
$tpl->assign('id', $_GET['id']);
$tpl->assign('cod', $fac[0]['cod']);
$tpl->assign('desc', $fac[0]['desc']);

$gastos = $db->query('SELECT codgastos AS cod, descripcion AS desc FROM catalogo_gastos ORDER BY cod');
foreach ($gastos as $gasto) {
	$tpl->newBlock('gasto');
	$tpl->assign('cod', $gasto['cod']);
	$tpl->assign('desc', $gasto['desc']);
}

$tpl->printToScreen();
?>