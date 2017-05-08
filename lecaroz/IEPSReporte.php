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

			if (isset($_REQUEST['conta']) && $_REQUEST['conta'] > 0) {
				$condiciones[] = 'cc.idcontador = ' . $_REQUEST['conta'];
			}

			if ( ! in_array($_SESSION['iduser'], array(1, 4, 67, 79, 46, 76, 77, 92, 87, 88))) {
				$condiciones[] = 'con.iduser = ' . $_SESSION['iduser'];
			}

			$condiciones[] = 'cod_turnos IN (1, 2, 3, 4, 8, 9)';

			$sql = "
				SELECT
					num_cia,
					cc.nombre
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
					LEFT JOIN catalogo_contadores con
						USING (idcontador)
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

			$tpl = new TemplatePower('plantillas/pan/IEPSReporteImpreso.tpl');
			$tpl->prepare();

			$tpl->assign('mes', ucfirst(strtolower($_meses[$_REQUEST['mes']])));
			$tpl->assign('anio', $_REQUEST['anio']);

			if ($result) {
				$turnos = array(
					1	=> 'FD',
					2	=> 'FN',
					3	=> 'BIZ',
					4	=> 'REP',
					8	=> 'PIC',
					9	=> 'GEL'
				);

				$total_turnos = array_fill_keys(array_keys($turnos), 0);
				$total_ieps = 0;

				$num_cia = NULL;

				foreach ($result as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$datos[$num_cia] = array(
							'nombre_cia'				=> utf8_encode($row['nombre_cia']),
							'produccion_turnos'			=> array_fill_keys(array_keys($turnos), 0),
							'porcentaje_turnos'			=> array_fill_keys(array_keys($turnos), 0),
							'porcentaje_turnos_excento'	=> array_fill_keys(array_keys($turnos), 0),
							'total_produccion'			=> 0,
							'total_produccion_excento'	=> 0,
							'efectivo_turnos'			=> array_fill_keys(array_keys($turnos), 0),
							'efectivo_porcentajes'		=> array_fill_keys(array_keys($turnos), 0),
							'efectivo'					=> 0,
							'efectivo_pan_dulce'		=> 0,
							'efectivo_gravado_3'		=> 0,
							'efectivo_excento_3'		=> 0,
							'efectivo_gravado_4'		=> 0,
							'efectivo_excento_4'		=> 0,
							'faltante_pan'				=> 0,
							'total_general'				=> 0,
							'porcentaje'				=> 0,
							'ieps'						=> 0
						);
					}

					$datos[$num_cia]['produccion_turnos'][$row['turno']] = $row['produccion'];

					$datos[$num_cia]['total_produccion'] += $row['produccion'];

					if ( ! in_array($row['turno'], array(3, 4)))
					{
						$datos[$num_cia]['total_produccion_excento'] += $row['produccion'];
					}
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

							if ( ! in_array($row['turno'], array(3, 4)))
							{
								$por = $produccion * 100 / $d['total_produccion_excento'];

								$datos[$num_cia]['porcentaje_turnos_excento'][$turno] = $por;
							}
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

				if (isset($_REQUEST['conta']) && $_REQUEST['conta'] > 0) {
					$condiciones[] = '((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE idcontador = ' . $_REQUEST['conta'] . ' AND num_cia <= 300) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE idcontador = ' . $_REQUEST['conta'] . ' AND num_cia <= 300))';
				}

				if ( ! in_array($_SESSION['iduser'], array(1, 4, 67, 79, 46, 76, 77, 92, 87, 88))) {
					$condiciones[] = '((num_cia IN (SELECT num_cia FROM catalogo_companias LEFT JOIN catalogo_contadores USING (idcontador) WHERE iduser = ' . $_SESSION['iduser'] . ' AND num_cia <= 300) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias LEFT JOIN catalogo_contadores USING (idcontador) WHERE iduser = ' . $_SESSION['iduser'] . ' AND num_cia <= 300))';
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

								if (in_array($turno, array(3, 4)))
								{
									$datos[$row['num_cia']]['efectivo_pan_dulce'] += $row['efectivo'] * $porcentaje / 100;
								}
							}
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

				if (isset($_REQUEST['conta']) && $_REQUEST['conta'] > 0) {
					$condiciones[] = 'cc.idcontador = ' . $_REQUEST['conta'];
				}

				if ( ! in_array($_SESSION['iduser'], array(1, 4, 67, 79, 46, 76, 77, 92, 87, 88))) {
					$condiciones[] = 'con.iduser = ' . $_SESSION['iduser'];
				}

				$sql = "
					SELECT
						num_cia,
						porcentaje
					FROM
						porcentajes_ieps por
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
						LEFT JOIN catalogo_contadores con
							USING (idcontador)
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
						if ( ! isset($datos[$row['num_cia']]))
						{
							continue;
						}

						$datos[$row['num_cia']]['porcentaje'] = $row['porcentaje'];

						if ($datos[$row['num_cia']]['porcentaje'] > 0)
						{
							if ($datos[$row['num_cia']]['efectivo_turnos'][3] > 0)
							{
								$datos[$row['num_cia']]['efectivo_gravado_3'] = $datos[$row['num_cia']]['efectivo_turnos'][3] * $datos[$row['num_cia']]['porcentaje'] / 100;
								$datos[$row['num_cia']]['efectivo_excento_3'] = $datos[$row['num_cia']]['efectivo_turnos'][3] - $datos[$row['num_cia']]['efectivo_gravado_3'];

								$datos[$row['num_cia']]['efectivo_turnos'][3] = $datos[$row['num_cia']]['efectivo_gravado_3'];
							}

							if ($datos[$row['num_cia']]['efectivo_turnos'][4] > 0)
							{
								$datos[$row['num_cia']]['efectivo_gravado_4'] = $datos[$row['num_cia']]['efectivo_turnos'][4] * $datos[$row['num_cia']]['porcentaje'] / 100;
								$datos[$row['num_cia']]['efectivo_excento_4'] = $datos[$row['num_cia']]['efectivo_turnos'][4] - $datos[$row['num_cia']]['efectivo_gravado_4'];

								$datos[$row['num_cia']]['efectivo_turnos'][4] = $datos[$row['num_cia']]['efectivo_gravado_4'];
							}

							$importe_gravado = $datos[$row['num_cia']]['efectivo_gravado_3'] + $datos[$row['num_cia']]['efectivo_gravado_4'];

							$importe_excento = $datos[$row['num_cia']]['efectivo_excento_3'] + $datos[$row['num_cia']]['efectivo_excento_4'];

							$datos[$row['num_cia']]['ieps'] = $importe_gravado * 0.08;

							$total_ieps += $datos[$row['num_cia']]['ieps'];

							// Distribuir importe excento entre los turnos exceptuando 3 y 4
							foreach ($datos[$row['num_cia']]['porcentaje_turnos_excento'] as $turno => $porcentaje)
							{
								if ( ! in_array($turno, array(3, 4)))
								{
									$importe_porcentaje = $importe_excento * $porcentaje / 100;

									$datos[$row['num_cia']]['efectivo_turnos'][$turno] += $importe_porcentaje;
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

				if (isset($_REQUEST['conta']) && $_REQUEST['conta'] > 0) {
					$condiciones[] = 'cc.idcontador = ' . $_REQUEST['conta'];
				}

				if ( ! in_array($_SESSION['iduser'], array(1, 4, 67, 79, 46, 76, 77, 92, 87, 88))) {
					$condiciones[] = 'con.iduser = ' . $_SESSION['iduser'];
				}

				$sql = "
					SELECT
						num_cia,
						faltante_pan
					FROM
						balances_pan bal
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
						LEFT JOIN catalogo_contadores con
							USING (idcontador)
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
						if ( ! isset($datos[$row['num_cia']]))
						{
							continue;
						}

						$datos[$row['num_cia']]['faltante_pan'] = $row['faltante_pan'];

						$datos[$row['num_cia']]['total_general'] = $datos[$row['num_cia']]['faltante_pan'] < 0 ? $datos[$row['num_cia']]['efectivo'] + abs($datos[$row['num_cia']]['faltante_pan']) : 0;
					}
				}

				// Re-calcular porcentaje de los turnos
				foreach ($datos as $num_cia => $datos_cia)
				{
					foreach ($datos_cia['efectivo_turnos'] as $turno => $efectivo)
					{
						$datos[$num_cia]['efectivo_porcentajes'][$turno] = $efectivo * 100 / $datos_cia['efectivo'];

						$total_turnos[$turno] += $efectivo;
					}
				}

				foreach (array_keys($turnos) as $turno)
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

					$tpl->assign('ieps', $d['ieps'] > 0 ? number_format(ceil($d['ieps']), 2) : '');

					foreach ($d['efectivo_turnos'] as $turno => $efectivo)
					{
						$tpl->newBlock('efectivo_turno');

						if ($efectivo > 0)
						{
							$tpl->assign('efectivo_turno', '<span class="orange" style="float:left;">(' . number_format($datos[$num_cia]['efectivo_porcentajes'][$turno], 2) . '%) </span> ' . number_format($efectivo, 2));
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

	}

	die;
}

$tpl = new TemplatePower('plantillas/pan/IEPSReporte.tpl');
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

$sql = '
	SELECT
		idcontador
			AS value,
		nombre_contador
			AS text
	FROM
		catalogo_contadores
	ORDER BY
		text
';

$result = $db->query($sql);

if ($result) {
	foreach ($result as $r) {
		$tpl->newBlock('conta');
		$tpl->assign('value', $r['value']);
		$tpl->assign('text', utf8_encode($r['text']));
	}
}

$tpl->printToScreen();
?>
