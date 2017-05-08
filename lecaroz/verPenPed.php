<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

if (!in_array($_SESSION['iduser'], array(21))) die;

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

$sql = "SELECT num_cia, nombre_corto AS nombre FROM pedidos_tmp AS p LEFT JOIN catalogo_companias USING (num_cia) WHERE p.status = 0 AND importe > 0 GROUP BY num_cia, nombre_corto ORDER BY num_cia";
$result = $db->query($sql);

if (!$result) die;

$sql = "SELECT tsins::date FROM pedidos_tmp WHERE status = 0 AND importe > 0 AND tsins::date < CURRENT_DATE - interval '2 days' LIMIT 1";
$ret = $db->query($sql);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/verPenPed.tpl" );
$tpl->prepare();

$tpl->assign('alert', $ret ? ' <p style="font-size:14pt; color:#F00">Hay pedidos de hasta 2 días de retraso</p>' : '');

foreach ($result as $reg) {
	$tpl->newBlock('fila');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre']);
}

die($tpl->getOutputContent());
?>