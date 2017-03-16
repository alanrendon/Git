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

$sql = 'SELECT num_cia, nombre_corto, clabe_cuenta AS cuenta, 1 AS banco, saldo_bancos, saldo, (SELECT sum(CASE WHEN tipo_mov = \'FALSE\' THEN importe ELSE -importe END) FROM mov_banorte WHERE num_cia = sb.num_cia AND fecha_con IS NULL) AS pendientes, CASE WHEN tsdif IS NOT NULL THEN now()::date - tsdif::date ELSE 0 END AS dias FROM saldos sb LEFT JOIN saldo_banorte USING (num_cia) LEFT JOIN catalogo_companias cc USING (num_cia) WHERE num_cia < 900 AND cuenta = 1
UNION
SELECT num_cia, nombre_corto, clabe_cuenta2 AS cuenta, 2 AS banco, saldo_bancos, saldo, (SELECT sum(CASE WHEN tipo_mov = \'FALSE\' THEN importe ELSE -importe END) FROM mov_santander WHERE num_cia = ss.num_cia AND fecha_con IS NULL) AS pendientes, CASE WHEN tsdif IS NOT NULL THEN now()::date - tsdif::date ELSE 0 END AS dias FROM saldos ss LEFT JOIN saldo_santander USING (num_cia) LEFT JOIN catalogo_companias cc USING (num_cia) WHERE num_cia < 900 AND cuenta = 2';

$result = $db->query($sql);

if (!$result) die;

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/verDifSal.tpl" );
$tpl->prepare();

$cont = 0;
foreach ($result as $reg) {
	$dif = $reg['saldo_bancos'] + $reg['pendientes'] - $reg['saldo'];
	if (round($dif, 2) != 0 && $reg['saldo'] != '') {
		$tpl->newBlock('fila');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre_corto']);
		$tpl->assign('nombre_banco', $reg['banco'] == 1 ? 'BANORTE' : 'SANTANDER');
		$tpl->assign('cuenta', $reg['cuenta']);
		$tpl->assign('oficina', round($reg['saldo_bancos'], 2) != 0 ? number_format($reg['saldo_bancos'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('pendientes', round($reg['pendientes'], 2) != 0 ? number_format($reg['pendientes'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('banco', round($reg['saldo'], 2) != 0 ? number_format($reg['saldo'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('dif', round($dif, 2) ? number_format($dif, 2, '.', ',') : '&nbsp;');
		$tpl->assign('dias', $reg['dias'] != 0 ? $reg['dias'] : '&nbsp;');
		$cont++;
	}
}

if ($cont > 0) die($tpl->getOutputContent());
//$tpl->printToScreen();
?>