<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$descripcion_error[1] = "No hay resultados";

$numfilas = 10;

// [AJAX] Validar nota de credito
if (isset($_GET['f'])) {
	$sql = "SELECT id FROM notas_credito_zap WHERE num_proveedor = $_GET[p] AND folio = $_GET[f]";
	$result = $db->query($sql);

	die("$_GET[i]|" . (!$result ? 1 : -1));
}

// [AJAX] Obtener nombre de compañía
if (isset($_GET['c'])) {
	$sql = "SELECT nombre_corto AS nombre FROM catalogo_companias WHERE num_cia = $_GET[c]";
	$result = $db->query($sql);

	die($_GET['i'] . '|' . trim($result[0]['nombre']));
}

// [AJAX] Obtener nombre del proveedor
if (isset($_GET['p'])) {
	$sql = "SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = $_GET[p]";
	$result = $db->query($sql);

	die($_GET['i'] . '|' . trim($result[0]['nombre']));
}

if (isset($_POST['num_cia'])) {
	$cont = 0;
	for ($i = 0; $i < $numfilas; $i++)
		if ($_POST['num_cia'][$i] > 0 && $_POST['num_pro'][$i] > 0 && $_POST['fecha'][$i] != '' && $_POST['folio'][$i] > 0 && get_val($_POST['importe'][$i]) > 0) {
			$data[$cont]['num_cia'] = $_POST['num_cia'][$i];
			$data[$cont]['num_proveedor'] = $_POST['num_pro'][$i];
			$data[$cont]['fecha'] = $_POST['fecha'][$i];
			$data[$cont]['folio'] = $_POST['folio'][$i];
			$data[$cont]['concepto'] = strtoupper(trim(str_replace(array('    ', '   ', '  '), ' ', $_POST['concepto'][$i])));
			$data[$cont]['importe'] = get_val($_POST['importe'][$i]);
			$data[$cont]['iduser'] = $_SESSION['iduser'];
			$data[$cont]['lastmod'] = date('d/m/Y H:i:s');
			$data[$cont]['status'] = 0;
			$data[$cont]['impuestos'] = $_POST['impuestos'][$i];
			$cont++;
		}

	if ($cont > 0) $db->query($db->multiple_insert('notas_credito_zap', $data));

	die(header('location: ./zap_not_cre.php'));
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/zap/zap_not_cre.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock('fila');
	$tpl->assign('i', $i);
	$tpl->assign('next', $i < $numfilas - 1 ? $i + 1 : 0);
	$tpl->assign('back', $i > 0 ? $i - 1 : $numfilas - 1);
}

$tpl->printToScreen();
?>
