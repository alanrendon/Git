<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value) {
	return intval($value, 10);
}

$_meses = array(
	1  => 'ENERO',
	2  => 'FEBRERO',
	3  => 'MARZO',
	4  => 'ABRIL',
	5  => 'MAYO',
	6  => 'JUNIO',
	7  => 'JULIO',
	8  => 'AGOSTO',
	9  => 'SEPTIEMBRE',
	10 => 'OCTUBRE',
	11 => 'NOVIEMBRE',
	12 => 'DICIEMBRE'
);

$_dias = array(
	0 => 'D',
	1 => 'L',
	2 => 'M',
	3 => 'M',
	4 => 'J',
	5 => 'V',
	6 => 'S'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die('EN PROCESO DE ACTUALIZACION');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {

		case 'reporte':
			$condiciones = array();

			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
				$cias = array();

				$pieces = explode(',', $_REQUEST['cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}

				if (count($cias) > 0) {
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') && (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
				$condiciones[] = 'fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
			}

			if (isset($_REQUEST['turno'])) {
				$condiciones[] = 'cod_turnos IN (' . implode(', ', $_REQUEST['turno']) . ')';
			}

			if (!in_array($_SESSION['iduser'], array(1, 4, 6, 18, 19, 20, 24, 37, 42, 48, 50))) {
				$condiciones[] = 'co.iduser = ' . $_SESSION['iduser'];
			}

			$sql = "
				SELECT
					num_cia,
					cc.nombre_corto
						AS nombre_cia,
					cod_turnos
						AS turno,
					SUM(imp_produccion)
						AS produccion
				FROM
					produccion p
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_administradores ca
						USING (idadministrador)
					LEFT JOIN catalogo_operadoras co
						USING (idoperadora)
				WHERE
					" . implode(' AND ', $condiciones) . "
				GROUP BY
					num_cia,
					nombre_cia,
					turno
				ORDER BY
					num_cia,
					turno
			";

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/pan/ProduccionTurnosReporteImpreso.tpl');
			$tpl->prepare();

			$tpl->assign('fecha1', $_REQUEST['fecha1']);
			$tpl->assign('fecha2', $_REQUEST['fecha2']);

			if ($result) {
				$total_turnos = array_fill_keys($_REQUEST['turno'], 0);

				$num_cia = NULL;

				foreach ($result as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$datos[$num_cia] = array(
							'nombre_cia'		=> utf8_encode($row['nombre_cia']),
							'produccion_turnos'	=> array_fill_keys($_REQUEST['turno'], 0),
							'total_cia'			=> 0
						);
					}

					$datos[$num_cia]['produccion_turnos'][$row['turno']] = $row['produccion'];

					$total_turnos[$row['turno']] += $row['produccion'];

					$datos[$num_cia]['total_cia'] += $row['produccion'];
				}

				$turnos = array(
					1	=> 'FD',
					2	=> 'FN',
					3	=> 'BIZ',
					4	=> 'REP',
					8	=> 'PIC',
					9	=> 'GEL'
				);

				foreach ($_REQUEST['turno'] as $turno)
				{
					$tpl->newBlock('turno_titulo');
					$tpl->assign('turno', $turnos[$turno]);
				}

				foreach ($datos as $num_cia => $d)
				{
					$tpl->newBlock('cia');

					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', $d['nombre_cia']);

					$tpl->assign('total_cia', number_format($d['total_cia'], 2));

					foreach ($d['produccion_turnos'] as $turno => $produccion)
					{
						$tpl->newBlock('produccion_turno');

						if ($produccion > 0)
						{
							$por = $produccion * 100 / $d['total_cia'];

							$tpl->assign('produccion_turno', '<span class="orange" style="float:left;">(' . number_format($por, 2) . '%) </span> ' . number_format($produccion, 2));
						}
						else
						{
							$tpl->assign('produccion_turno', '&nbsp;');
						}
					}
				}

				foreach ($total_turnos as $turno => $total)
				{
					$tpl->newBlock('total_turno');

					$tpl->assign('total_turno', $total > 0 ? number_format($total, 2) : '&nbsp;');
				}

				$tpl->assign('_ROOT.total', number_format(array_sum($total_turnos), 2));
			}

			$tpl->printToScreen();

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/pan/ProduccionTurnosReporte.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha1', date('d/m/Y', mktime(0, 0, 0, date('n'), 1, date('Y'))));
$tpl->assign('fecha2', date('d/m/Y', mktime(0, 0, 0, date('n') + 1, 0, date('Y'))));

$sql = '
	SELECT
		idadministrador
			AS value,
		nombre_administrador
			AS text
	FROM
		catalogo_administradores
	ORDER BY
		text
';

$result = $db->query($sql);

if ($result) {
	foreach ($result as $r) {
		$tpl->newBlock('admin');
		$tpl->assign('value', $r['value']);
		$tpl->assign('text', utf8_encode($r['text']));
	}
}

$tpl->printToScreen();
?>
