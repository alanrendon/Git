<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

$search = array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ');
$replace = array('&Aacute;', '&Eacute;', '&Iacute;', '&Oacute;', '&Uacute;', '&Ntilde;');

if (isset($_GET['c'])) {
	$sql = '
		SELECT
			nombre_corto
		FROM
			catalogo_companias
		WHERE
				num_cia
					BETWEEN
							900
						AND
							998
			AND
				num_cia = ' . $_GET['c'] . '
	';
	$result = $db->query($sql);
	
	die($result[0]['nombre_corto']);
}

if (isset($_GET['num_cia'])) {
	$sql = '
		SELECT
			num_cia,
			nombre_corto
				AS
					nombre,
			CASE
				WHEN clave > 0 THEN
					1
				ELSE
					0
			END
				AS
					tipo,
			sum
				(
					total
				)
					AS
						importe
		FROM
				facturas_zap
			LEFT JOIN
				catalogo_companias
					USING
						(
							num_cia
						)
		WHERE
				tspago ISNULL';
	if ($_GET['fecha1'] != '') {
		$sql .= '
			AND
				fecha
					BETWEEN
							\'' . $_GET['fecha1'] . '\'
						AND
							\'' . ($_GET['fecha2'] != '' ? $_GET['fecha2'] : $_GET['fecha1']) . '\'
		';
	}
	$sql .= '
		GROUP BY
			num_cia,
			nombre_corto,
			tipo
		ORDER BY
			num_cia
	';
	
	$result = $db->query($sql);
	
	$tpl = new TemplatePower('plantillas/zap/listado_facturas_pendientes_totales.tpl');
	$tpl->prepare();
	
	if (!$result) {
		$tpl->newBlock('cerrar');
		die($tpl->printToScreen());
	}
	
	$tpl->newBlock('listado');
	if ($_GET['fecha1'] != '')
		$tpl->assign('periodo', $_GET['fecha2'] != '' ? '<br />Periodo del ' . $_GET['fecha1'] . ' al ' . $_GET['fecha2'] : '<br />' . $_GET['fecha1']);
	
	$num_cia = NULL;
	$total_fac = 0;
	$total_rem = 0;
	$total_gen = 0;
	foreach ($result as $r) {
		if ($num_cia != $r['num_cia']) {
			$num_cia = $r['num_cia'];
			
			$tpl->newBlock('fila');
			$tpl->assign('num_cia', $num_cia);
			$tpl->assign('nombre', $r['nombre']);
			
			$total_cia = 0;
		}
		$tpl->assign($r['tipo'] == 0 ? 'facturas' : 'remisiones', number_format($r['importe'], 2, '.', ','));
		$total_cia += $r['importe'];
		$tpl->assign('total', number_format($total_cia, 2, '.', ','));
		
		$total_gen += $r['importe'];
		
		if ($r['tipo'] == 0)
			$total_fac += $r['importe'];
		else
			$total_rem += $r['importe'];
		
		$tpl->assign('listado.facturas', number_format($total_fac, 2, '.', ','));
		$tpl->assign('listado.remisiones', number_format($total_rem, 2, '.', ','));
		$tpl->assign('listado.total', number_format($total_gen, 2, '.', ','));
	}
	
	die($tpl->printToScreen());
}

$tpl = new TemplatePower('plantillas/zap/ListadoFacturasPendientesZap.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha1', date('d/m/Y', mktime(0, 0, 0, date('n'), 1, date('Y'))));
$tpl->assign('fecha2', date('d/m/Y', mktime(0, 0, 0, date('n') + 1, 0, date('Y'))));

$tpl->printToScreen();
?>