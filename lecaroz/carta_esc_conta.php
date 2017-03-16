<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/ban/carta_esc_conta.tpl" );
$tpl->prepare();

$sql = "SELECT num_cia, cuenta, nombre, clabe_cuenta, clabe_cuenta2, mes, anio, nombre_contador FROM estados_cuenta_recibidos LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN catalogo_contadores USING (idcontador) WHERE imp = 'TRUE' ORDER BY nombre_contador, num_cia, cuenta, anio, mes";
$result = $db->query($sql);

if (!$result) {
	$tpl->newBlock('cerrar');
	die($tpl->printToScreen());
}

$sql = 'UPDATE estados_cuenta_recibidos SET imp = \'FALSE\' WHERE imp = \'TRUE\'';
$db->query($sql);

$contador = NULL;
foreach ($result as $r) {
	if ($contador != $r['nombre_contador']) {
		$contador = $r['nombre_contador'];
		
		$tpl->newBlock('carta');
		$tpl->assign('contador', $contador);
		$tpl->assign('dia', date('d'));
		$tpl->assign('mes', mes_escrito(date('n'), TRUE));
		$tpl->assign('anio', date('Y'));
		$tpl->assign('salto', '<br style="page-break-after:always;">');
	}
	$tpl->newBlock('fila');
	$tpl->assign('nombre', $r['nombre']);
	$tpl->assign('banco', $r['cuenta'] == 1 ? 'BANORTE' : 'SANTANDER');
	$tpl->assign('cuenta', $r['cuenta'] == 1 ? $r['clabe_cuenta'] : $r['clabe_cuenta2']);
	$tpl->assign('mes', mes_escrito($r['mes']));
	$tpl->assign('anio', $r['anio']);
}

$tpl->printToScreen();
?>