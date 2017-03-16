<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

if (!in_array($_SESSION['iduser'], array(1, 4, 62, 102))) die;

$fecha = date('d/m/Y 06:00:00', mktime(0, 0, 0, date('n'), date('d'), date('Y')));

$sql = "SELECT co.nombre AS operadora, num_cia, cc.nombre_corto AS nombre_cia, fecha, codmp, cmp.nombre AS producto, ct.nombre_corto AS turno, consumo, promedio * 1.20 AS promedio, diferencia FROM his_aut_con_avio his LEFT JOIN catalogo_companias cc USING (num_cia) LEFT JOIN catalogo_operadoras co USING (idoperadora) LEFT JOIN catalogo_mat_primas cmp USING (codmp) LEFT JOIN catalogo_turnos ct USING (cod_turno) WHERE his.status = 1 AND tsmod >= '$fecha' ORDER BY operadora, num_cia, codmp, cod_turno";
$result = $db->query($sql);

if (!$result) die;

$tpl = new TemplatePower( "./plantillas/verConAut.tpl" );
$tpl->prepare();

$operadora = NULL;
foreach ($result as $reg) {
	if ($operadora != $reg['operadora']) {
		if ($operadora != NULL)
			$tpl->assign('operadora.salto', '<br style="page-break-after:always;">');

		$operadora = $reg['operadora'];

		$tpl->newBlock('operadora');
		$tpl->assign('operadora', $operadora);

		$num_cia =  NULL;
	}
	if ($num_cia != $reg['num_cia']) {
		$num_cia = $reg['num_cia'];

		$tpl->newBlock('cia');
		$tpl->assign('num_cia', $num_cia);
		$tpl->assign('nombre_cia', $reg['nombre_cia']);

		$fecha = NULL;
	}
	if ($fecha != $reg['fecha']) {
		$fecha = $reg['fecha'];

		$tpl->newBlock('fecha');
		$tpl->assign('fecha', $fecha);
	}
	$tpl->newBlock('producto');
	$tpl->assign('style', $reg['diferencia'] >= 100 ? ' style="background-color:#FFCC00;"' : '');
	$tpl->assign('codmp', $reg['codmp']);
	$tpl->assign('producto', $reg['producto']);
	$tpl->assign('turno', $reg['turno']);
	$tpl->assign('consumo', number_format($reg['consumo'], 2, '.', ','));
	$tpl->assign('promedio', $reg['promedio'] != 0 ? number_format($reg['promedio'], 2, '.', ',') : '-');
	$tpl->assign('diferencia', $reg['diferencia'] != 0 ? number_format($reg['diferencia'], 2) . '%' : '-');
}

die($tpl->getOutputContent());
?>
