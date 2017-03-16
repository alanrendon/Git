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
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

			$condiciones = array();

			$condiciones[] = 'num_cia <= 300';

			$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

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

			if (isset($_REQUEST['turno'])) {
				$condiciones[] = 'cod_turnos IN (' . implode(', ', $_REQUEST['turno']) . ')';
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

			$tpl = new TemplatePower('plantillas/pan/ProduccionTurnosEfectivoReporteImpreso.tpl');
			$tpl->prepare();

			$tpl->assign('mes', ucfirst(strtolower($_meses[$_REQUEST['mes']])));
			$tpl->assign('anio', $_REQUEST['anio']);

			if ($result) {
				$total_turnos = array_fill_keys($_REQUEST['turno'], 0);

				$total_ieps = 0;

				$num_cia = NULL;

				foreach ($result as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$datos[$num_cia] = array(
							'nombre_cia'			=> utf8_encode($row['nombre_cia']),
							'produccion_turnos'		=> array_fill_keys($_REQUEST['turno'], 0),
							'porcentaje_turnos'		=> array_fill_keys($_REQUEST['turno'], 0),
							'efectivo_turnos'		=> array_fill_keys($_REQUEST['turno'], 0),
							'total_produccion'		=> 0,
							'efectivo'				=> 0,
							'efectivo_pan_dulce'	=> 0,
							'faltante_pan'			=> 0,
							'total_general'			=> 0,
							'porcentaje'			=> 0
						);
					}

					$datos[$num_cia]['produccion_turnos'][$row['turno']] = $row['produccion'];

					$datos[$num_cia]['total_produccion'] += $row['produccion'];
				}

				// Calcular porcentajes de produccion
				foreach ($datos as $num_cia => $d)
				{
					foreach ($d['produccion_turnos'] as $turno => $produccion)
					{
						if ($produccion > 0)
						{
							$por = $produccion * 100 / $d['total_produccion'];

							$datos[$num_cia]['porcentaje_turnos'][$turno] = $por;
						}
					}
				}

				// Efectivos
				$condiciones = array();

				$condiciones[] = '((ec.num_cia <= 300 AND ec.num_cia_sec IS NULL) OR ec.num_cia_sec <= 300)';

				$condiciones[] = 'ec.fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

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
						$condiciones[] = '((ec.num_cia IN (' . implode(', ', $cias) . ') AND ec.num_cia_sec IS NULL) OR ec.num_cia_sec IN (' . implode(', ', $cias) . '))';
					}
				}

				if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
					$condiciones[] = '((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE idadministrador = ' . $_REQUEST['admin'] . ' AND num_cia <= 300) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE idadministrador = ' . $_REQUEST['admin'] . ' AND num_cia <= 300))';
				}

				$condiciones[] = 'ec.cod_mov IN (1, 16, 44, 99)';

				$sql = "
					SELECT
						COALESCE(ec.num_cia_sec, ec.num_cia)
							AS num_cia,
						SUM(ec.importe)
							AS efectivo
					FROM
						estado_cuenta ec
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						" . implode(' AND ', $condiciones) . "
					GROUP BY
						COALESCE(ec.num_cia_sec, ec.num_cia)
					ORDER BY
						COALESCE(ec.num_cia_sec, ec.num_cia)
				";

				$result = $db->query($sql);

				if ($result)
				{
					foreach ($result as $row)
					{
						if (isset($datos[$row['num_cia']]))
						{
							$datos[$row['num_cia']]['efectivo'] = $row['efectivo'];

							foreach ($datos[$row['num_cia']]['porcentaje_turnos'] as $turno => $porcentaje)
							{
								$datos[$row['num_cia']]['efectivo_turnos'][$turno] = $row['efectivo'] * $porcentaje / 100;

								$total_turnos[$turno] += $datos[$row['num_cia']]['efectivo_turnos'][$turno];

								if (in_array($turno, array(3, 4)))
								{
									$datos[$row['num_cia']]['efectivo_pan_dulce'] += $row['efectivo'] * $porcentaje / 100;
								}
							}
						}
					}
				}

				// Faltante de pan
				$condiciones = array();

				$condiciones[] = 'num_cia <= 300';

				$condiciones[] = 'fecha = \'' . $fecha1 . '\'';

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

				$sql = "
					SELECT
						num_cia,
						faltante_pan,
						porcentaje_reporte_produccion
							AS porcentaje
					FROM
						balances_pan bal
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						" . implode(' AND ', $condiciones) . "
					ORDER BY
						num_cia
				";

				$result = $db->query($sql);

				if ($result)
				{
					foreach ($result as $row)
					{
						if (isset($datos[$row['num_cia']]))
						{
							$datos[$row['num_cia']]['faltante_pan'] = $row['faltante_pan'];

							$datos[$row['num_cia']]['total_general'] = $datos[$row['num_cia']]['faltante_pan'] < 0 ? $datos[$row['num_cia']]['efectivo'] + abs($datos[$row['num_cia']]['faltante_pan']) : 0;
						}
					}
				}

				// Porcentajes
				$condiciones = array();

				$condiciones[] = 'num_cia <= 300';

				$condiciones[] = 'anio = ' . $_REQUEST['anio'];

				$condiciones[] = 'mes = ' . $_REQUEST['mes'];

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

				$sql = "
					SELECT
						num_cia,
						porcentaje
					FROM
						porcentajes_ieps por
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						" . implode(' AND ', $condiciones) . "
					ORDER BY
						num_cia
				";

				$result = $db->query($sql);

				if ($result)
				{
					foreach ($result as $row)
					{
						if (isset($datos[$row['num_cia']]))
						{
							$datos[$row['num_cia']]['porcentaje'] = $row['porcentaje'];
						}
					}
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

					$tpl->assign('anio', $_REQUEST['anio']);
					$tpl->assign('mes', $_REQUEST['mes']);

					$tpl->assign('efectivo', number_format($d['efectivo'], 2));
					$tpl->assign('faltante_pan', '<span class="' . ($d['faltante_pan'] < 0 ? 'red' : 'blue') . '">' . number_format($d['faltante_pan'], 2) . '</span>');
					$tpl->assign('total_general', $d['total_general'] > 0 ? number_format($d['total_general'], 2) : '&nbsp;');

					$tpl->assign('efectivo_pan_dulce', $d['efectivo_pan_dulce']);

					$tpl->assign('porcentaje', $d['porcentaje'] > 0 ? $d['porcentaje'] : '');

					$tpl->assign('ieps', $d['porcentaje'] > 0 && $d['efectivo_pan_dulce'] > 0 ? number_format(($d['efectivo_pan_dulce'] * $d['porcentaje'] / 100) * 0.08, 2) : '');

					$total_ieps += ($d['efectivo_pan_dulce'] * $d['porcentaje'] / 100) * 0.08;

					foreach ($d['efectivo_turnos'] as $turno => $efectivo)
					{
						$tpl->newBlock('efectivo_turno');

						if ($efectivo > 0)
						{
							$tpl->assign('efectivo_turno', '<span class="orange" style="float:left;">(' . number_format($datos[$num_cia]['porcentaje_turnos'][$turno], 2) . '%) </span> ' . number_format($efectivo, 2));
						}
						else
						{
							$tpl->assign('efectivo_turno', '&nbsp;');
						}
					}
				}

				foreach ($total_turnos as $turno => $total)
				{
					$tpl->newBlock('total_turno');

					$tpl->assign('total_turno', $total > 0 ? number_format($total, 2) : '&nbsp;');
				}

				$tpl->assign('_ROOT.total_efectivo', number_format(array_sum($total_turnos), 2));

				$tpl->assign('_ROOT.total_ieps', number_format($total_ieps, 2));
			}

			$tpl->printToScreen();

			break;

		case 'actualizar_porcentaje':
			if ($id = $db->query("
				SELECT
					id
				FROM
					porcentajes_ieps
				WHERE
					num_cia = {$_REQUEST['num_cia']}
					AND anio = {$_REQUEST['anio']}
					AND mes = {$_REQUEST['mes']}
			"))
			{
				$sql = "
					UPDATE
						porcentajes_ieps
					SET
						porcentaje = {$_REQUEST['porcentaje']},
						tsmod = NOW()
					WHERE
						id = {$id[0]['id']}
				";
			}
			else
			{
				$sql = "
					INSERT INTO
						porcentajes_ieps (
							num_cia,
							anio,
							mes,
							porcentaje
						)
						VALUES (
							{$_REQUEST['num_cia']},
							{$_REQUEST['anio']},
							{$_REQUEST['mes']},
							{$_REQUEST['porcentaje']}
						)
				";
			}

			$db->query($sql);
			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/pan/ProduccionTurnosEfectivoReporte.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y'));

foreach ($_meses as $value => $text)
{
	$tpl->newBlock('mes');
	$tpl->assign('value', $value);
	$tpl->assign('text', $text);

	if ($value == date('n'))
	{
		$tpl->assign('selected', ' selected="selected"');
	}
}

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
