<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");
$db_scans = new DBclass("pgsql://mollendo:pobgnj@127.0.0.1:5432/scans", "autocommit=yes");	// Coneccion a la base de datos de las imagenes

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_ord_ser_det.tpl");
$tpl->prepare();

$sql = "SELECT os.id, os.folio, os.fecha, m.num_cia, cc.nombre_corto AS nombre_cia, tipo_orden, estatus, autorizo, concepto, observaciones, idmaq FROM orden_servicio os LEFT JOIN maquinaria m ON (m.id = idmaq) LEFT JOIN catalogo_companias cc USING (num_cia) WHERE os.id = $_GET[id]";
$result = $db->query($sql);

foreach ($result[0] as $field => $value) {
	if (in_array($field, array('id', 'folio', 'fecha', 'num_cia', 'nombre_cia', 'autorizo', 'concepto', 'observaciones')))
		$tpl->assign($field, $value);
	else if (in_array($field, array('tipo_orden')))
		$tpl->assign($field, $value == 1 ? 'REPARACION' : 'MANTENIMIENTO');
	else if (in_array($field, array('estatus')))
		$tpl->assign($field, $value == 1 ? 'TERMINADO' : '&nbsp;');
	else if (in_array($field, array('idmaq'))) {
		$sql = "SELECT id, num_maquina, descripcion, marca FROM maquinaria WHERE id = $value ORDER BY num_maquina";
		$m = $db->query($sql);
		$tpl->assign('maq', "{$m[0]['num_maquina']}-" . (trim($m[0]['descripcion']) != '' ? trim($m[0]['descripcion']) : '') .  (trim($m[0]['marca']) != '' ? ' (' . trim($m[0]['marca']) . ')' : ''));
	}
}

// Obtener facturas
$sql = "SELECT id AS id_fac, num_proveedor AS num_pro, nombre AS nombre_pro, num_fact, fecha, concepto, importe FROM orden_servicio_facs LEFT JOIN catalogo_proveedores USING (num_proveedor) WHERE folio = {$result[0]['folio']} ORDER BY fecha";
$facs = $db->query($sql);

$total = 0;
if ($facs) {
	foreach ($facs as $i => $fac) {
		$tpl->newBlock('fac');
		foreach ($fac as $field => $value)
			if (in_array($field, array('num_fact', 'num_pro', 'nombre_pro', 'fecha', 'concepto')))
				$tpl->assign($field, $value != '' ? $value : '&nbsp;');
			else if (in_array($field, array('importe'))) {
				$tpl->assign($field, number_format($value, 2, '.', ','));
				$total += $value;
			}
	}
}
$tpl->assign('_ROOT.total', number_format($total, 2, '.', ','));

$tpl->printToScreen();
?>