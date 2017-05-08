<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

if (!in_array($_SESSION['iduser'], array(18))) die;

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

$fecha1 = date('d') > 6 ? date('1/m/Y') : date('d/m/Y', mktime(0, 0, 0, date('n') - 1, 1, date('Y')));
$fecha2 = date('d') > 6 ? date('d/m/Y') : date('d/m/Y', mktime(0, 0, 0, date('n'), 0, date('Y')));

$sql = "SELECT num_cia, nombre_corto, saldo_libros, (SELECT sum(total) FROM pasivo_proveedores WHERE num_cia = saldos.num_cia) AS saldo_pro, avg(efectivo) AS prom_efectivo FROM saldos LEFT JOIN total_panaderias USING (num_cia) LEFT JOIN catalogo_companias USING (num_cia) WHERE num_cia < 100 AND cuenta = 2 AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY num_cia, nombre_corto, saldo_libros ORDER BY num_cia";
$result = $db->query($sql);

if (!$result) die;

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/verDepErr.tpl" );
$tpl->prepare();

$ok = FALSE;
foreach ($result as $reg) {
	$dias = $reg['prom_efectivo'] > 0 ? floor(($reg['saldo_pro'] - $reg['saldo_libros']) / $reg['prom_efectivo']) : 0;
	
	if ($dias < -5 || $dias > 5) {
		$ok  = TRUE;
		$tpl->newBlock('fila');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre_corto']);
		$tpl->assign('dias', $dias);
		$tpl->assign('color', $dias > 0 ? '00C' : 'C00');
	}
}

if ($ok) die($tpl->getOutputContent());
?>