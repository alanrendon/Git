<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/cheques.inc.php';
include './includes/pcl.inc.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die("MODIFICANDO LA PANTALLA... GOMEN ^_^|");

$numfilas = 20;

if (isset($_POST['num_cia'])) {
	$sql = "";
	for ($i = 0; $i < $numfilas; $i++)
		if (get_val($_POST['importe'][$i]) > 0) {
			$data['num_cia'] = $_POST['num_cia'];
			$data['num_proveedor'] = $_POST['num_pro'];
			$data['fecha'] = $_POST['fecha'];
			$data['modelo'] = strtoupper(trim($_POST['modelo'][$i]));
			$data['color'] = strtoupper(trim($_POST['color'][$i]));
			$data['talla'] = get_val($_POST['talla'][$i]);
			$data['piezas'] = get_val($_POST['piezas'][$i]);
			$data['precio'] = get_val($_POST['precio'][$i]);
			$data['importe'] = get_val($_POST['importe'][$i]);
			$data['obs'] = strtoupper(trim($_POST['obs'][$i]));
			$data['imp'] = 'FALSE';
			$sql .= $db->preparar_insert("devoluciones_zap", $data) . ";\n";
		}
	if ($sql != '') $db->query($sql);
	header("location: ./zap_dev_cap.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower("./plantillas/header.tpl");

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/zap/zap_dev_cap.tpl");
$tpl->prepare();

// Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->newBlock("captura");
$tpl->assign("fecha", date("d/m/Y", mktime(0, 0, 0, date("n"), date("d") - 1, date("Y"))));

for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila_cap");
	$tpl->assign("i", $i);
	$tpl->assign("back", $i > 0 ? $i - 1 : $numfilas - 1);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
}

$cias = $db->query("SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias WHERE num_cia BETWEEN 900 AND 950 ORDER BY num_cia");
foreach ($cias as $cia) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia['num_cia']);
	$tpl->assign("nombre", $cia['nombre']);
}

$pros = $db->query("SELECT num_proveedor AS num_pro, nombre FROM catalogo_proveedores WHERE num_proveedor BETWEEN 9000 AND 9999 ORDER BY num_pro");
foreach ($pros as $pro) {
	$tpl->newBlock("pro");
	$tpl->assign("num_pro", $pro['num_pro']);
	$tpl->assign("nombre", $pro['nombre']);
}

$tpl->printToScreen();
?>