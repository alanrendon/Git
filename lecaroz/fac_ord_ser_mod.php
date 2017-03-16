<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");
$db_scans = new DBclass("pgsql://mollendo:pobgnj@127.0.0.1:5432/scans", "autocommit=yes");	// Coneccion a la base de datos de las imagenes

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$descripcion_error[1] = "No hay resultados";

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

	die($_GET['i'] . '|' . trim($result[0]['nombre']));
}

// [AJAX] Obtener maquinaria asociada a la compañía
if (isset($_GET['ce'])) {
	$sql = 'SELECT id, num_maquina, descripcion, marca FROM maquinaria';
	$sql .= $_GET['ce'] > 0 ? " WHERE num_cia = $_GET[ce] AND status = 1" : '';
	$sql .= ' ORDER BY num_maquina';
	$result = $db->query($sql);

	if (!$result) die("-1");

	$data = "";
	foreach ($result as $i => $reg)
		$data .= "$reg[id]/$reg[num_maquina]-" . (trim($reg['descripcion']) != '' ? trim($reg['descripcion']) : '') .  (trim($reg['marca']) != '' ? ' (' . trim($reg['marca']) . ')' : '') . ($i < count($result) - 1 ? '|' : '');

	die($data);
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_ord_ser_mod.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	$sql = '';
	$sql_scan = '';

	$data['folio'] = $_POST['folio'];
	$data['fecha'] = $_POST['fecha'];
	$data['idmaq'] = $_POST['idmaq'];
	$data['tipo_orden'] = $_POST['tipo_orden'];
	$data['estatus'] = isset($_POST['estatus']) ? 1 : 0;
	$data['autorizo'] = strtoupper(trim(str_replace(array('    ', '   ', '  '), ' ', $_POST['autorizo'])));
	$data['concepto'] = strtoupper(trim(str_replace(array('    ', '   ', '  '), ' ', $_POST['concepto'])));
	$data['observaciones'] = strtoupper(trim(str_replace(array('    ', '   ', '  '), ' ', $_POST['observaciones'])));
	$data['iduser'] = $_SESSION['iduser'];
	$data['lastmod'] = date('d/m/Y H:i:s');

	$sql = "UPDATE orden_servicio SET fecha = '$data[fecha]', idmaq = $data[idmaq], tipo_orden = $data[tipo_orden], estatus = $data[estatus], autorizo = '$data[autorizo]', concepto = '$data[concepto]', observaciones = '$data[observaciones]', iduser = $_SESSION[iduser], lastmod = now() WHERE id = $_POST[id];\n";

	for ($i = 0; $i < count($_POST['num_fact']); $i++)
		if ($_POST['id_fac'][$i] > 0 && $_POST['fecha_fac'][$i] != '' && get_val($_POST['importe'][$i]) > 0) {
			$importe = get_val($_POST['importe'][$i]);
			$sql .= "UPDATE orden_servicio_facs SET fecha = '{$_POST['fecha_fac'][$i]}', concepto = '{$_POST['concepto_fac'][$i]}', importe = $importe, iduser = $_SESSION[iduser], lastmod = now() WHERE id = {$_POST['id_fac'][$i]};\n";
		}
		else if ($_POST['ok'][$i] > 0 && $_POST['num_fact'][$i] != '' && $_POST['num_pro'][$i] > 0 && $_POST['fecha_fac'][$i] != '' && trim($_POST['concepto_fac'][$i]) != '' && get_val($_POST['importe'][$i]) > 0) {
			$fac['folio'] = $_POST['folio'];
			$fac['num_fact'] = $_POST['num_fact'][$i];
			$fac['num_proveedor'] = $_POST['num_pro'][$i];
			$fac['fecha'] = $_POST['fecha_fac'][$i];
			$fac['concepto'] = strtoupper(trim(str_replace(array('    ', '   ', '  '), ' ', $_POST['concepto_fac'][$i])));
			$fac['importe'] = get_val($_POST['importe'][$i]);
			$fac['iduser'] = $_SESSION['iduser'];
			$fac['lastmod'] = date('d/m/Y H:i:s');

			$sql .= $db->preparar_insert('orden_servicio_facs', $fac) . ";\n";

			$sql_scan .= "INSERT INTO img_fac_ord_ser (folio, num_proveedor, num_fact, imagen) SELECT folio, num_proveedor, num_fact, imagen FROM img_tmp_fac WHERE folio = $_POST[folio] AND num_proveedor = {$_POST['num_pro'][$i]} AND num_fact = '{$_POST['num_fact'][$i]}';\n";
		}
	$sql_scan .= "DELETE FROM img_tmp_fac WHERE folio = $_POST[folio];\n";

	$db->query($sql);
	$db_scans->query($sql_scan);

	$tpl->newBlock('cerrar');
	die($tpl->printToScreen());
}

if (isset($_GET['id'])) {
	$sql = "SELECT os.id, os.folio, os.fecha, m.num_cia, cc.nombre_corto AS nombre_cia, tipo_orden, estatus, autorizo, concepto, observaciones, idmaq FROM orden_servicio os LEFT JOIN maquinaria m ON (m.id = idmaq) LEFT JOIN catalogo_companias cc USING (num_cia) WHERE os.id = $_GET[id]";
	$result = $db->query($sql);

	$db_scans->query("DELETE FROM img_tmp_fac WHERE folio = {$result[0]['folio']}");

	$tpl->newBlock('mod');
	foreach ($result[0] as $field => $value) {
		if (in_array($field, array('id', 'folio', 'fecha', 'num_cia', 'nombre_cia', 'autorizo', 'concepto', 'observaciones')))
			$tpl->assign($field, $value);
		else if (in_array($field, array('tipo_orden')))
			$tpl->assign($field . '_' . $value, ' checked');
		else if (in_array($field, array('estatus')))
			$tpl->assign('estatus', $value == 1 ? ' checked' : '');
		else if (in_array($field, array('idmaq'))) {
			$sql = "SELECT id, num_maquina, descripcion, marca FROM maquinaria WHERE num_cia = {$result[0]['num_cia']} AND status = 1 ORDER BY num_maquina";
			$maq = $db->query($sql);

			foreach ($maq as $m) {
				$tpl->newBlock('idmaq');
				$tpl->assign('id', $m['id']);
				$tpl->assign('desc', "$m[num_maquina]-" . (trim($m['descripcion']) != '' ? trim($m['descripcion']) : '') .  (trim($m['marca']) != '' ? ' (' . trim($m['marca']) . ')' : ''));
				$tpl->assign('selected', $value == $m['id'] ? ' selected' : '');
			}
		}
	}

	// Obtener facturas
	$sql = "SELECT id AS id_fac, num_proveedor AS num_pro, nombre AS nombre_pro, num_fact, fecha, concepto, importe FROM orden_servicio_facs LEFT JOIN catalogo_proveedores USING (num_proveedor) WHERE folio = {$result[0]['folio']} ORDER BY fecha";
	$facs = $db->query($sql);

	if (!$facs) {
		$tpl->assign('mod.cont_row', 0);
		$tpl->newBlock('fac');
		$tpl->assign('ok', 0);
	}
	else {
		$tpl->assign('mod.cont_row', count($facs) - 1);
		foreach ($facs as $i => $fac) {
			$tpl->newBlock($i == 0 ? 'fac' : 'fac_ext');
			$tpl->assign('i', $i);
			$tpl->assign('ok', 1);
			$tpl->assign('ro', ' readonly');
			$tpl->assign('dis', ' disabled');
			foreach ($fac as $field => $value)
				if (in_array($field, array('id_fac', 'num_fact', 'num_pro', 'nombre_pro', 'fecha', 'concepto')))
					$tpl->assign($field, $value);
				else if (in_array($field, array('importe')))
					$tpl->assign($field, number_format($value, 2, '.', ','));
		}
	}

	die($tpl->printToScreen());
}

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$sql = 'SELECT os.id, folio, os.fecha, idmaq, tipo_orden, estatus, autorizo, concepto, observaciones, num_maquina, descripcion, marca, num_cia, cc.nombre_corto AS nombre_cia, (SELECT sum(importe) FROM orden_servicio_facs WHERE folio = os.folio) AS costo_reparacion FROM orden_servicio os LEFT JOIN maquinaria m ON (m.id = idmaq) LEFT JOIN catalogo_companias cc USING (num_cia)';
	if ($_GET['num_cia'] > 0 || $_GET['idmaq'] > 0 || $_GET['folio'] > 0 || $_GET['tipo_orden'] > 0 || $_GET['estatus'] >= 0 || $_GET['fecha1'] != '') {
		$sql .= ' WHERE';
		$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia]" : '';
		$sql .= $_GET['idmaq'] > 0 ? ($_GET['num_cia'] > 0 ? ' AND' : '') . " idmaq = $_GET[idmaq]" : '';
		$sql .= $_GET['folio'] > 0 ? ($_GET['num_cia'] > 0 || $_GET['idmaq'] > 0 ? ' AND' : '') . " folio = $_GET[folio]" : '';
		$sql .= $_GET['tipo_orden'] > 0 ? ($_GET['num_cia'] > 0 || $_GET['idmaq'] > 0 || $_GET['folio'] > 0 ? ' AND' : '') . " tipo_orden = $_GET[tipo_orden]" : '';
		$sql .= $_GET['estatus'] >= 0 ? ($_GET['num_cia'] > 0 || $_GET['idmaq'] > 0 || $_GET['folio'] > 0 || $_GET['tipo_orden'] > 0 ? ' AND' : '') . " estatus = $_GET[estatus]" : '';
		$sql .= $_GET['fecha1'] > 0 ? ($_GET['num_cia'] > 0 || $_GET['idmaq'] > 0 || $_GET['folio'] > 0 || $_GET['tipo_orden'] > 0 || $_GET['estatus'] >= 0 ? ' AND' : '') . ' os.fecha ' . ($_GET['fecha2'] != '' ? "BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'" : "= '$_GET[fecha1]'") : '';
	}
	$sql .= ' ORDER BY num_cia, num_maquina';
	$result = $db->query($sql);

	if (!$result) die(header('location: ./fac_ord_ser_mod.php?codigo_error=1'));

	$total = 0;

	$tpl->newBlock('result');
	foreach ($result as $reg) {
		$tpl->newBlock('row');
		$tpl->assign('id', $reg['id']);
		$tpl->assign('folio', $reg['folio']);
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre_cia', $reg['nombre_cia']);
		$tpl->assign('maquina', "$reg[num_maquina]-" . (trim($reg['descripcion']) != '' ? trim($reg['descripcion']) : '') .  (trim($reg['marca']) != '' ? ' (' . trim($reg['marca']) . ')' : ''));
		$tpl->assign('fecha', $reg['fecha']);
		$tpl->assign('tipo', $reg['tipo_orden'] == 1 ? 'REPARACION' : 'MANTENIMIENTO');
		$tpl->assign('concepto', $reg['concepto'] != '' ? $reg['concepto'] : '&nbsp;');
		$tpl->assign('observaciones', $reg['observaciones'] != '' ? $reg['observaciones'] : '&nbsp;');
		$tpl->assign('costo_reparacion', $reg['costo_reparacion'] > 0 ? number_format($reg['costo_reparacion'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('estatus', $reg['estatus'] == 1 ? 'TERMINADO' : '&nbsp;');

		$total += $reg['costo_reparacion'];
	}

	$tpl->assign('result.total', number_format($total, 2, '.', ','));

	die($tpl->printToScreen());
}

$tpl->newBlock('datos');

$sql = 'SELECT id, num_maquina, descripcion, marca FROM maquinaria ORDER BY num_maquina';
$result = $db->query($sql);

foreach ($result as $i => $reg) {
	$tpl->newBlock('idm');
	$tpl->assign('id', $reg['id']);
	$tpl->assign('desc', "$reg[num_maquina]-" . (trim($reg['descripcion']) != '' ? trim($reg['descripcion']) : '') .  (trim($reg['marca']) != '' ? ' (' . trim($reg['marca']) . ')' : ''));
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>
