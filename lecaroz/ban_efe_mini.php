<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$tpl = new TemplatePower( "./plantillas/ban/ban_efe_mini.tpl" );
$tpl->prepare();

$fecha = $_POST['fecha'];
ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $fecha, $tmp);
$fecha1 = date('d/m/Y', mktime(0, 0, 0, $tmp[2], 1, $tmp[3]));
$fecha2 = $fecha;

$cias = array();
for ($i = 0; $i < 30; $i++)
	if (isset($_POST['cia' . $i]) && $_POST['cia' . $i] > 0)
		$cias[] = $_POST['cia' . $i];

$sql = 'SELECT num_cia, num_cia_primaria AS num_cia_p, nombre, nombre_corto, idadministrador AS idadmin, tipo_cia FROM catalogo_companias cc WHERE';
$sql .= ' num_cia BETWEEN ' . ($_SESSION['iduser'] >= 28 ? '900 AND 998' : '1 AND 899');
$sql .= count($cias) > 0 ? ' AND num_cia IN (' . implode(', ', $cias) . ')' : '';
$sql .= $_POST['a_partir'] > 0 ? ' AND num_cia >= ' . $_POST['a_partir'] : '';
$sql .= $_POST['idadmin'] > 0 ? ' AND idadministrador = ' . $_POST['idadmin'] : '';
$sql .= ' ORDER BY num_cia_primaria, num_cia';
$cias = $db->query($sql);

if (!$cias) {
	die;
}

// Compañías de las cuales no se tomaran en cuenta sus depósitos reales
for ($i = 1; $i <= 10; $i++)
	if (isset($_POST['num_cia' . ($i - 1)]))
		$no_deps[$i] = $_POST['num_cia' . ($i - 1)];

$num_cia = NULL;
$idadmin = NULL;
$hoja = NULL;
$bloque = NULL;
$reporte = NULL;
foreach ($cias as $c) {
	// Validar rangos de compañías, solo deben estar contenidos entre:
	// Panaderias  1 - 300
	// Rosticerias 301 - 599 y 702, 704, 705
	// Zapaterias  900 - 998
	/*if (!(($c['num_cia'] <= 300)
		|| (($c['num_cia'] > 300 && $c['num_cia'] < 600) || in_array($c['num_cia'], array(702, 704, 705)))
		|| ($c['num_cia'] >= 900 && $c['num_cia'] < 999)))
		continue;*/

	if ( ! ($c['tipo_cia'] == 1 || $c['tipo_cia'] == 2 || $c['tipo_cia'] == 4))
		continue;

	// Obtener efectivos de la compañía en consulta
	$sql = 'SELECT efectivo + (CASE WHEN num_cia IN (31, 32, 33, 34, 73, 121) AND EXTRACT(year FROM fecha) = 2012 AND EXTRACT(month FROM fecha) = 9 THEN COALESCE((SELECT importe FROM cometra WHERE comprobante IN (41355658, 40759126) AND num_cia = tc.num_cia AND fecha = tc.fecha), 0) ELSE 0 END) AS efectivo, extract(day FROM fecha) AS dia, fecha, ';
	// if ($c['num_cia'] <= 300)
	if ($c['tipo_cia'] == 1)
		$sql .= 'CASE WHEN efe = \'TRUE\' AND exp = \'TRUE\' AND gas = \'TRUE\' AND pro = \'TRUE\' AND pas = \'TRUE\' THEN \'t\' ELSE \'f\' END AS status';
	// else if (($c['num_cia'] > 300 && $c['num_cia'] < 600) || in_array($c['num_cia'], array(702, 704, 705)))
	else if ($c['tipo_cia'] == 2)
		$sql .= '\'t\' AS status';
	// else if ($c['num_cia'] >= 900 && $c['num_cia'] < 999)
	else if ($c['tipo_cia'] == 4)
		$sql .= 'CASE WHEN venta > 0 THEN \'t\' ELSE \'f\' END AS status';
	$sql .= ' FROM ';
	// if ($c['num_cia'] <= 300)
	if ($c['tipo_cia'] == 1)
		$sql .= 'total_panaderias';
	// else if (($c['num_cia'] > 300 && $c['num_cia'] < 600) || in_array($c['num_cia'], array(702, 704, 705)))
	else if ($c['tipo_cia'] == 2)
		$sql .= 'total_companias';
	// else if ($c['num_cia'] >= 900 && $c['num_cia'] < 999)
	else if ($c['tipo_cia'] == 4)
		$sql .= 'total_zapaterias';
	$sql .= ' tc WHERE num_cia = ' . $c['num_cia'] . ' AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\' ORDER BY fecha';
	$efes = $db->query($sql);

	if (!$efes && $db->query('
		SELECT
			id
		FROM
			gastos_caja
		WHERE
			num_cia = ' . $c['num_cia'] . '
			AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
	')) {
		$efes = array();

		for ($dia = 1; $dia <= $fecha[1]; $dia++) {
			$efes[] = array(
				'efectivo' => 0,
				'dia' => $dia,
				'fecha' => date('d/m/Y', mktime(0, 0, 0, $tmp[2], $dia, $tmp[3])),
				'status' => 'f'
			);
		}
	}

	// Si no hay efectivos para la compañía en consulta saltar el proceso de reporte
	if (!$efes)
		continue;

	if ($idadmin != $c['idadmin']) {
		if ($reporte != NULL) {
			$vacios = 0;
			if ($reporte % 3 != 0)
				$vacios = (floor($reporte / 3) + 1) * 3 - $reporte;

			for ($v = 0; $v < $vacios; $v++)
				$tpl->newBlock('vacio');

//			if ($hoja % 2 != 0)
//				$tpl->assign('hoja.blanco', '<br style="page-break-after:always;" />');
		}

		$idadmin = $c['idadmin'];

		$tpl->newBlock('hoja');
		$tpl->assign('salto', '<br style="page-break-after:always;" />');

		$hoja = 1;
		$bloque = 1;
		$reporte = 0;
	}

	if ($num_cia != $c['num_cia']) {
		$num_cia = $c['num_cia'];

		$reporte++;

		if ($bloque > 2) {
			$tpl->newBlock('hoja');
			$tpl->assign('salto', '<br style="page-break-after:always;" />');
			$hoja++;
			$bloque = 1;
		}

		if ($reporte % 3 == 1) {
			$tpl->newBlock('bloque');
			$bloque++;
		}
	}

	// Acomodar efectivos para consulta rápida
	$efectivos = array();
	foreach ($efes as $e)
		$efectivos[$e['dia']] = $e;

	// Obtener efectivos directos
	$sql = 'SELECT importe AS efectivo, extract(day from fecha) AS dia, fecha, \'f\' AS status FROM importe_efectivos WHERE num_cia = ' . $c['num_cia'] . ' AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\' ORDER BY fecha';
	$directos = $db->query($sql);

	// Completar el arreglo de efectivos con todos aquellos directos que no esten listados o que no esten completos
	if ($directos)
		foreach ($directos as $d)
			if (!isset($efectivos[$d['dia']]) || $efectivos[$d['dia']]['status'] == 'f')
				$efectivos[$d['dia']] = $d;

	/*
	@ [12-Sep-2012] Sumar al efectivo los siguientes importes para el mes de agosto de 2012 (solo del dia 1 al 30)
	@
	@ 21 - 10,000.00
	@ 31 -  4,000.00
	@ 32 -  3,000.00
	@ 34 -  5,000.00
	@ 49 -  3,000.00
	@ 73 -  3,000.00
	@ 79 -  2,000.00
	@ 121 - 5,000.00
	*/

	if (in_array($c['num_cia'], array(
		21,
		31,
		32,
		34,
		49,
		73,
		79,
		121
		))
		&& intval($tmp[3], 10) == 2012
		&& intval($tmp[2], 10) == 8) {
		$importes_agosto_2012 = array(
			21  => 10000,
			31  => 4000,
			32  => 3000,
			34  => 5000,
			49  => 3000,
			73  => 3000,
			79  => 2000,
			121 => 5000
		);

		foreach ($efectivos as $dia => $efectivo) {
			if ($dia < 31) {
				$efectivos[$dia]['efectivo'] += $importes_agosto_2012[$c['num_cia']];
			}
		}
	}

	/*
	@ [13-Nov-2012] Sumar al efectivo los siguientes importes para el mes de octubre de 2012
	@
	*/

	if (in_array($c['num_cia'], array(33))
		&& intval($tmp[3], 10) == 2012
		&& intval($tmp[2], 10) == 10) {
		foreach ($efectivos as $dia => $efectivo) {
			$efectivos[$dia]['efectivo'] += 10000;
		}
	}

	/*
	@ [12-Dic-2012] Sumar al efectivo los siguientes importes para el mes de noviembre de 2012
	@
	*/

	if (in_array($c['num_cia'], array(33))
		&& intval($tmp[3], 10) == 2012
		&& intval($tmp[2], 10) == 11) {
		foreach ($efectivos as $dia => $efectivo) {
			$efectivos[$dia]['efectivo'] += 10000;
		}
	}

	/*
	@ [13-Nov-2013] Sumar al efectivo los siguientes importes para el mes de octubre de 2013
	@
	*/

	if (in_array($c['num_cia'], array(49, 57, 67, 34))
		&& intval($tmp[3], 10) == 2013
		&& intval($tmp[2], 10) == 10) {
		foreach ($efectivos as $dia => $efectivo) {
			$efectivos[$dia]['efectivo'] += 10000;
		}
	}

	/*
	@ [13-Nov-2013] Sumar al efectivo los siguientes importes para el mes de octubre de 2013
	@
	*/

	if (in_array($c['num_cia'], array(32))
		&& intval($tmp[3], 10) == 2013
		&& intval($tmp[2], 10) == 10) {
		foreach ($efectivos as $dia => $efectivo) {
			if ($dia <= 11)
			{
				$efectivos[$dia]['efectivo'] += 10000;
			}
		}
	}

	/*
        @ [13-Nov-2013] Sumar al efectivo los siguientes importes para el mes de octubre de 2013
        @
        */

        if (in_array($c['num_cia'], array(20, 50))
                && intval($tmp[3], 10) == 2013
                && intval($tmp[2], 10) == 10) {
                foreach ($efectivos as $dia => $efectivo) {
                        if ($dia <= 21)
                        {
                                $efectivos[$dia]['efectivo'] += 10000;
                        }
                }
        }

	// Crear reporte
	$tpl->newBlock('reporte');
	$tpl->assign('num_cia', $c['num_cia']);
	$tpl->assign('nombre', $c['nombre_corto']);

	// Obtener depósitos con código (1, 16, 44, 99)
	if (mktime(0, 0, 0, $tmp[2], $tmp[1], $tmp[3]) <= mktime(0, 0, 0, 5, 31, 2011) && in_array($c['num_cia'], array(11, 303, 353, 355))) {
		$cias = array(
			11  => '11, 810',
			303 => '303, 811',
			353 => '353, 812',
			355 => '355, 813'
		);

		$sql = '
			SELECT
				importe,
				extract(day from fecha)
					AS dia
			FROM
				estado_cuenta
			WHERE
				(
					(
						num_cia IN (' . $cias[$c['num_cia']] . ')
						AND num_cia_sec IS NULL
					)
					OR num_cia_sec IN (' . $cias[$c['num_cia']] . ')
				)
				AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
				AND cod_mov IN (1, 16, 44, 99)
			ORDER BY
				fecha,
				importe DESC
		';
	}
	else {
		$sql = '
			SELECT
				importe,
				extract(day from fecha)
					AS dia
			FROM
				estado_cuenta
			WHERE
				(
					(
						num_cia = ' . $c['num_cia'] . '
						AND num_cia_sec IS NULL
					)
					OR num_cia_sec = ' . $c['num_cia'] . '
				)
				AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
				AND cod_mov IN (1, 16, 44, 99)
			ORDER BY
				fecha,
				importe DESC
		';
	}
	$deps = $db->query($sql);

	// Obtener depósitos alternativos
	$sql = 'SELECT dep1, dep2, extract(day from fecha) AS dia, fecha FROM depositos_alternativos WHERE num_cia = ' . $c['num_cia'] . ' AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\' ORDER BY fecha';
	$alts = $db->query($sql);

	// Acomodar depósitos en arreglo para consulta rápida
	$depositos = array();
	if ($deps && !in_array($c['num_cia'], $no_deps))
		foreach ($deps as $d)
			$depositos[$d['dia']][] = $d['importe'];

	if ($alts)
		foreach ($alts as $a) {
			if ($a['dep1'] > 0)
				$depositos[$a['dia']][] = $a['dep1'];
			if ($a['dep2'] > 0)
				$depositos[$a['dia']][] = $a['dep2'];
		}

	// Obtener otros depósitos
	$sql = 'SELECT sum(importe) AS importe, extract(day from fecha) AS dia FROM otros_depositos WHERE num_cia = ' . $c['num_cia'] . ' AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\' GROUP BY dia ORDER BY dia';
	$otros_deps = $db->query($sql);

	// Acomodar otros depósitos en arreglo para consulta rápida
	$otros = array();
	if ($otros_deps)
		foreach ($otros_deps as $o)
			$otros[$o['dia']] = $o['importe'];

	// Variables para almacenar totales
	$total_efectivo = 0;
	$total_deposito = 0;
	$total_mayoreo = 0;
	$total_oficina = 0;
	$total_diferencia = 0;
	$total_dia = array();

	foreach ($efectivos as $dia => $e) {
		$tpl->newBlock('fila');
		$tpl->assign('dia', $dia);
		$tpl->assign('efectivo', round($e['efectivo'], 2) != 0 ? number_format($e['efectivo'], 2, '.', ',') : '&nbsp;');
		$total_efectivo += $e['efectivo'];
		$total_dia[$dia] = 0;

		if (isset($depositos[$dia])) {
			$mayoreo = 0;
			foreach ($depositos[$dia] as $i => $d) {
				if ($i == 0) {
					$tpl->assign('deposito', number_format($d, 2, '.', ','));
					$total_deposito += $d;
					$total_dia[$dia] += $d;
				}
				else {
					$mayoreo += $d;
					$total_mayoreo += $d;
					$total_dia[$dia] += $d;
				}
			}
			$tpl->assign('mayoreo', $mayoreo != 0 ? number_format($mayoreo, 2, '.', ',') : '&nbsp;');
		}

		$tpl->assign('oficina', isset($otros[$dia]) ? number_format($otros[$dia], 2, '.', ',') : '&nbsp;');
		$total_oficina += isset($otros[$dia]) ? $otros[$dia] : 0;
		$total_dia[$dia] += isset($otros[$dia]) ? $otros[$dia] : 0;

		$diferencia = $e['efectivo'] - $total_dia[$dia];
		$total_diferencia += $diferencia;

		$tpl->assign('diferencia', $diferencia != 0 ? '<span style="color:#' . ($diferencia > 0 ? 'F00' : '00F') . '">' . number_format($diferencia, 2, '.', ',') . '</span>' : '&nbsp;');

//		$tpl->assign('total', $total_dia[$dia] != 0 ? number_format($total_dia[$dia], 2, '.', ',') : '&nbsp;');
	}

	$tpl->assign('reporte.efectivo', /*number_format($total_efectivo, 2, '.', ',')*/'&nbsp;');
	$tpl->assign('reporte.deposito', /*number_format($total_deposito, 2, '.', ',')*/'&nbsp;');
	$tpl->assign('reporte.mayoreo', /*number_format($total_mayoreo, 2, '.', ',')*/'&nbsp;');
	$tpl->assign('reporte.oficina', /*number_format($total_oficina, 2, '.', ',')*/'&nbsp;');
	$tpl->assign('reporte.diferencia', number_format($total_diferencia, 2, '.', ','));
//	$tpl->assign('reporte.total', number_format(array_sum($total_dia), 2, '.', ','));

//	$dias = count($efectivos);
//	$tpl->assign('reporte.pefectivo', number_format($total_efectivo / $dias, 2, '.', ','));
//	$tpl->assign('reporte.pdeposito', number_format($total_deposito / $dias, 2, '.', ','));
//	$tpl->assign('reporte.pmayoreo', number_format($total_mayoreo / $dias, 2, '.', ','));
//	$tpl->assign('reporte.poficina', number_format($total_oficina / $dias, 2, '.', ','));
//	$tpl->assign('reporte.ptotal', number_format(array_sum($total_dia) / $dias, 2, '.', ','));
}
if ($reporte != NULL) {
	$vacios = 0;
	if ($reporte % 3 != 0)
		$vacios = (floor($reporte / 3) + 1) * 3 - $reporte;

	for ($v = 0; $v < $vacios; $v++)
		$tpl->newBlock('vacio');
}

$tpl->printToScreen();
?>
