<?php
// MODIFICACIÓN RÁPIDA DE PRECIOS PROMEDIO DE INVENTARIO FIN MES
// Tablas 'inventario_fin_mes'
// Menu 'No definido'

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

function toInt($value) {
	return intval($value, 10);
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_ifm_minimod_v3.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	$tmp = $db->query("SELECT * FROM inventario_fin_mes WHERE id = $_POST[id]");
	$dif = $tmp[0];
	$inv = str_replace(",", "", $_POST['inventario']) != 0 ? str_replace(",", "", $_POST['inventario']) : 0;

	if ($inv != $dif['inventario'])
	{
		$db->query("UPDATE inventario_fin_mes SET inventario = $inv, diferencia = existencia - $inv WHERE id = $_POST[id]");

		$db->query("INSERT INTO bitacora_modificacion_diferencias (num_cia, fecha, codmp, cantidad_anterior, cantidad_nueva, idmod) VALUES ({$dif['num_cia']}, '{$dif['fecha']}', {$dif['codmp']}, {$dif['inventario']}, {$inv}, {$_SESSION['iduser']})");
	}

	list($dia, $mes, $anio) = array_map('toInt', explode('/', $dif['fecha']));

	$tpl->newBlock("cerrar");
	$tpl->assign("i", $_POST['i']);
	$tpl->assign("inv", $inv);
	$tpl->assign('anio', $anio);
	$tpl->assign('mes', $mes);
	$tpl->assign('num_cia', $dif['num_cia']);
	$tpl->printToScreen();
	die;
}

// Generar pantalla de captura
$result = $db->query("SELECT id, num_cia, codmp, nombre, existencia, inventario FROM inventario_fin_mes LEFT JOIN catalogo_mat_primas USING(codmp) WHERE id = $_GET[id]");

$tpl->newBlock("modificar");
$tpl->assign("i", $_GET['i']);
$tpl->assign("id", $_GET['id']);
$tpl->assign("num_cia", $result[0]['num_cia']);
$nombre = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = {$result[0]['num_cia']}");
$tpl->assign("nombre_cia", $nombre[0]['nombre_corto']);
$tpl->assign("codmp", $result[0]['codmp']);
$tpl->assign("nombre", $result[0]['nombre']);
$tpl->assign("existencia", number_format($result[0]['existencia'], 2, ".", ","));
$tpl->assign("inventario", number_format($result[0]['inventario'], 2, ".", ","));

$tpl->printToScreen();
?>