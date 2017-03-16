<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/ban/reporte_asunto.tpl" );
$tpl->prepare();

$sql = 'SELECT num_cia, nombre_corto AS nombre, folio, fecha, upper(trim(atencion)) AS atencion, upper(trim(cf.referencia)) AS referencia FROM cartas_foleadas cf LEFT JOIN catalogo_companias cc USING (num_cia) WHERE id = ' . $_GET['id'];
$carta = $db->query($sql);

$sql = 'SELECT fecha_respuesta, dependencia, responsable, observaciones FROM cartas_foleadas_seguimiento WHERE id_carta = ' . $_GET['id'];
$seguimiento = $db->query($sql);

$tpl->assign('num_cia', $carta[0]['num_cia']);
$tpl->assign('nombre', $carta[0]['nombre']);
$tpl->assign('folio', $carta[0]['folio']);
$tpl->assign('fecha', $carta[0]['fecha']);
$tpl->assign('atencion', $carta[0]['atencion']);
$tpl->assign('referencia', $carta[0]['referencia']);

if ($seguimiento) {
	$tpl->newBlock('detalle');
	foreach ($seguimiento as $reg) {
		$tpl->newBlock('fila');
		$tpl->assign('fecha_respuesta', $reg['fecha_respuesta']);
		$tpl->assign('dependencia', $reg['dependencia']);
		$tpl->assign('responsable', $reg['responsable']);
		$tpl->assign('observaciones', $reg['observaciones']);
	}
}

$tpl->printToScreen();
?>