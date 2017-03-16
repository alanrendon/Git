<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

if (!in_array($_SESSION['iduser'], array(19))) die;

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

//$sql = "SELECT num_cia, nombre_corto, 2 AS cuenta FROM mov_santander LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha_con IS NULL AND fecha < now()::date - interval '3 days' AND num_cia BETWEEN 1 AND 800 GROUP BY num_cia, nombre_corto, cuenta UNION SELECT num_cia, nombre_corto, 1 AS cuenta FROM mov_banorte LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha_con IS NULL AND fecha < now()::date - interval '3 days' AND num_cia BETWEEN 1 AND 800 GROUP BY num_cia, nombre_corto, cuenta";
$sql = "SELECT num_cia, nombre_corto, cuenta, CASE WHEN cuenta = 1 THEN clabe_cuenta ELSE clabe_cuenta2 END AS cuenta_cia FROM estado_cuenta LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha_con IS NULL AND fecha < now()::date - interval '6 days' AND num_cia BETWEEN 1 AND 800 AND tipo_mov = 'FALSE' GROUP BY num_cia, nombre_corto, cuenta, cuenta_cia ORDER BY num_cia, cuenta";
$result = $db->query($sql);

if (!$result) die;

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/verMovPen.tpl" );
$tpl->prepare();

foreach ($result as $reg) {
	$tpl->newBlock('fila');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre_corto']);
	$tpl->assign('banco', $reg['cuenta'] == 1 ? 'BANORTE' : 'SANTANDER');
	$tpl->assign('cuenta', $reg['cuenta_cia']);
}

die($tpl->getOutputContent());
?>