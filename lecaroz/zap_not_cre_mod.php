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

	echo !$result ? 1 : -1;
	die;
}

// [AJAX] Obtener nombre de compañía
if (isset($_GET['c'])) {
	$sql = "SELECT nombre_corto AS nombre FROM catalogo_companias WHERE num_cia = $_GET[c]";
	$result = $db->query($sql);

	die(trim($result[0]['nombre']));
}

// [AJAX] Obtener nombre del proveedor
if (isset($_GET['p'])) {
	$sql = "SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = $_GET[p]";
	$result = $db->query($sql);

	die(trim($result[0]['nombre']));
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/zap/zap_not_cre_mod.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	$num_cia = $_POST['num_cia'];
	$num_pro = $_POST['num_pro'];
	$fecha = $_POST['fecha'];
	$folio = $_POST['folio'];
	$concepto = strtoupper(trim(str_replace(array('    ', '   ', '  '), ' ', $_POST['concepto'])));
	$importe = get_val($_POST['importe']);
	$impuestos = $_POST['impuestos'];

	$sql = "UPDATE notas_credito_zap SET num_cia = {$num_cia}, num_proveedor = {$num_pro}, fecha = '{$fecha}', folio = {$folio}, concepto = '{$concepto}', importe = {$importe}, impuestos = '{$impuestos}', iduser = $_SESSION[iduser], lastmod = now() WHERE id = $_POST[id]";

	$db->query($sql);

	$tpl->newBlock('cerrar');
	die($tpl->printToScreen());
}

$sql = "SELECT nc.num_cia, cc.nombre_corto AS nombre_cia, nc.num_proveedor AS num_pro, cp.nombre AS nombre_pro, nc.fecha, nc.folio, nc.concepto, nc.importe, nc.impuestos FROM notas_credito_zap nc LEFT JOIN catalogo_proveedores cp USING (num_proveedor) LEFT JOIN catalogo_companias cc USING (num_cia) WHERE nc.id = $_GET[id]";
$result = $db->query($sql);

$tpl->newBlock('mod');
$tpl->assign('id', $_GET['id']);
$tpl->assign('num_cia', $result[0]['num_cia']);
$tpl->assign('nombre_cia', $result[0]['nombre_cia']);
$tpl->assign('num_pro', $result[0]['num_pro']);
$tpl->assign('nombre_pro', $result[0]['nombre_pro']);
$tpl->assign('fecha', $result[0]['fecha']);
$tpl->assign('folio', $result[0]['folio']);
$tpl->assign('concepto', $result[0]['concepto']);
$tpl->assign('importe', number_format($result[0]['importe'], 2, '.', ','));

switch ($result[0]['impuestos'])
{
	case 'IVA 0':
		$tpl->assign('0', ' selected=""');
		break;

	case 'IVA':
		$tpl->assign('1', ' selected=""');
		break;

	case 'IVA + RET 4%':
		$tpl->assign('2', ' selected=""');
		break;

	case 'HONORARIOS/ARRENDAMIENTOS':
		$tpl->assign('3', ' selected=""');
		break;

	case 'ARRENDAMIENTO HABITACION':
		$tpl->assign('4', ' selected=""');
		break;

	case 'R35%':
		$tpl->assign('5', ' selected=""');
		break;
}

$tpl->printToScreen();
?>
