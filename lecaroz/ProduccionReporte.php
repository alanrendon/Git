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

			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') || (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') && (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')
					&& $_REQUEST['fecha1'] != $_REQUEST['fecha2']) {
					$condiciones[] = 'fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';

					$tipo = 'acumulado';
				} else if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') && (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')
					&& $_REQUEST['fecha1'] == $_REQUEST['fecha2']) {
					$condiciones[] = 'fecha = \'' . $_REQUEST['fecha1'] . '\'';

					$tipo = 'diario';
				}  else if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') || (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
					$condiciones[] = 'fecha = \'' . ($_REQUEST['fecha1'] ? $_REQUEST['fecha1'] : $_REQUEST['fecha2']) . '\'';

					$tipo = 'diario';
				}
			}

			if (isset($_REQUEST['turno'])) {
				$condiciones[] = 'cod_turnos IN (' . implode(', ', $_REQUEST['turno']) . ')';
			}

			if (isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '') {
				$productos = array();

				$pieces = explode(',', $_REQUEST['productos']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$productos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$productos[] = $piece;
					}
				}

				if (count($productos) > 0) {
					$condiciones[] = 'cod_producto IN (' . implode(', ', $productos) . ')';
				}
			}

			// if (!in_array($_SESSION['iduser'], array(1, 4, 6, 18, 19, 20, 24, 37, 42))) {
			// 	$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
			// }

			if (!in_array($_SESSION['iduser'], array(1, 4, 6, 18, 19, 20, 24, 37, 42, 48, 50))) {
				$condiciones[] = 'co.iduser = ' . $_SESSION['iduser'];
			}

			if ($tipo == 'diario') {
				$sql = '
					SELECT
						num_cia,
						cc.nombre_corto
							AS nombre_cia,
						fecha,
						cod_producto
							AS cod,
						cp.nombre
							AS producto,
						cod_turnos
							AS turno,
						piezas,
						CASE
							WHEN precio_raya > 0 THEN
								precio_raya::VARCHAR
							WHEN porc_raya > 0 THEN
								porc_raya || \'%\'
							ELSE
								\'\'
						END
							AS precio_raya,
						imp_raya,
						precio_venta,
						imp_produccion,
						COALESCE((
							SELECT
								SUM(piezas)
							FROM
								produccion
							WHERE
								num_cia = p.num_cia
								AND fecha = p.fecha
								AND cod_turnos = p.cod_turnos
						), 0)
							AS total_piezas,
						COALESCE((
							SELECT
								SUM(cantidad)
							FROM
								mov_inv_real
							WHERE
								num_cia = p.num_cia
								AND fecha = p.fecha
								AND codmp = 1
								AND tipo_mov = TRUE
								AND cod_turno = p.cod_turnos
								AND descripcion != \'DIFERENCIA INVENTARIO\'
						) / 44, 0)
							AS total_bultos
					FROM
						produccion p
						LEFT JOIN catalogo_productos cp
							USING (cod_producto)
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
						LEFT JOIN catalogo_operadoras co
							USING (idoperadora)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia,
						turno,
						cod
				';
			} else if ($tipo == 'acumulado') {
				$sql = '
					SELECT
						num_cia,
						cc.nombre_corto
							AS nombre_cia,
						cod_producto
							AS cod,
						cp.nombre
							AS producto,
						cod_turnos
							AS turno,
						SUM(piezas)
							AS piezas,
						SUM(imp_raya)
							AS imp_raya,
						SUM(imp_produccion)
							AS imp_produccion,
						COALESCE((
							SELECT
								SUM(piezas)
							FROM
								produccion
							WHERE
								num_cia = p.num_cia
								AND fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'
								AND cod_turnos = p.cod_turnos
						), 0)
							AS total_piezas,
						COALESCE((
							SELECT
								SUM(cantidad)
							FROM
								mov_inv_real
							WHERE
								num_cia = p.num_cia
								AND fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'
								AND codmp = 1
								AND tipo_mov = TRUE
								AND cod_turno = p.cod_turnos
								AND descripcion != \'DIFERENCIA INVENTARIO\'
						) / 44, 0)
							AS total_bultos
					FROM
						produccion p
						LEFT JOIN catalogo_productos cp
							USING (cod_producto)
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
						LEFT JOIN catalogo_operadoras co
							USING (idoperadora)
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						num_cia,
						nombre_cia,
						cod,
						producto,
						turno
					ORDER BY
						num_cia,
						turno,
						cod
				';
			}

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/pan/ProduccionReporteImpreso.tpl');
			$tpl->prepare();

			if ($result) {
				if ($tipo == 'diario') {
					$tpl->newBlock('reporte_diario');

					$tpl->assign('fecha', $_REQUEST['fecha1'] ? $_REQUEST['fecha1'] : $_REQUEST['fecha2']);

					$num_cia = NULL;

					foreach ($result as $row) {
						if ($num_cia != $row['num_cia']) {
							$num_cia = $row['num_cia'];

							$tpl->newBlock('d_cia');
							$tpl->assign('num_cia', $row['num_cia']);
							$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));

							$total_raya_ganada = 0;
							$total_raya_pagada = 0;
							$total_produccion = 0;

							$turno = NULL;

							if (!(isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '')) {
								$sql = '
									SELECT
										codturno
											AS turno,
										raya_ganada,
										raya_pagada,
										total_produccion
									FROM
										total_produccion tp
									WHERE
										numcia = ' . $num_cia . '
										AND fecha_total = \'' . $row['fecha'] . '\'
										' . (isset($_REQUEST['turno']) ? ' AND codturno IN (' . implode(', ', $_REQUEST['turno']) . ')' : '') . '
								';

								$tmp = $db->query($sql);

								$totales = array();

								if ($tmp) {
									foreach ($tmp as $t) {
										$totales[$t['turno']] = array(
											'raya_ganada' => $t['raya_ganada'],
											'raya_pagada' => $t['raya_pagada'],
											'produccion' => $t['total_produccion']
										);

										$total_raya_pagada += $t['raya_pagada'];
									}
								}

								$tpl->newBlock('d_totales');
								$tpl->assign('raya_pagada', number_format($total_raya_pagada, 2));
							} else {
								$tpl->newBlock('d_totales_small');
							}
						}

						if ($turno != $row['turno']) {
							$turno = $row['turno'];

							$tpl->newBlock('d_turno');

							switch ($turno) {

								case 1:
									$nombre_turno = 'FRANCES DE DIA';
									break;

								case 2:
									$nombre_turno = 'FRANCES DE NOCHE';
									break;

								case 3:
									$nombre_turno = 'BIZCOCHERO';
									break;

								case 4:
									$nombre_turno = 'REPOSTERO';
									break;

								case 8:
									$nombre_turno = 'PICONERO';
									break;

								case 9:
									$nombre_turno = 'GELATINERO';
									break;

							}

							$tpl->assign('turno', $nombre_turno);

							$bultos = 0;
							$piezas = 0;
							$imp_raya = 0;
							$imp_produccion = 0;

							if (!(isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '')) {
								$tpl->newBlock('d_raya_pagada');
								$tpl->assign('raya_pagada', number_format($totales[$turno]['raya_pagada'], 2));
							}
						}

						$tpl->newBlock('d_row');
						$tpl->assign('cod', $row['cod']);
						$tpl->assign('producto', utf8_encode($row['producto']));
						$tpl->assign('bultos', number_format($row['piezas'] * $row['total_bultos'] / $row['total_piezas'], 2));
						$tpl->assign('piezas', number_format($row['piezas'], 2));
						$tpl->assign('precio_raya', $row['precio_raya'] != '' ? $row['precio_raya'] : '&nbsp;');
						$tpl->assign('imp_raya', $row['imp_raya'] > 0 ? number_format($row['imp_raya'], 2) : '&nbsp;');
						$tpl->assign('precio_venta', $row['precio_venta'] > 0 ? number_format($row['precio_venta'], 2) : '&nbsp;');
						$tpl->assign('imp_produccion', $row['imp_produccion'] > 0 ? number_format($row['imp_produccion'], 2) : '&nbsp;');

						$bultos += round($row['piezas'] * $row['total_bultos'] / $row['total_piezas'], 2);
						$piezas += $row['piezas'];
						$imp_raya += $row['imp_raya'];
						$imp_produccion += $row['imp_produccion'];

						$total_raya_ganada += $row['imp_raya'];
						$total_produccion += $row['imp_produccion'];

						$tpl->assign('d_turno.bultos', number_format($bultos, 2));
						$tpl->assign('d_turno.piezas', number_format($piezas, 2));
						$tpl->assign('d_turno.raya_ganada', number_format($imp_raya, 2));
						$tpl->assign('d_turno.produccion', number_format($imp_produccion, 2));

						$tpl->assign('d_totales' . (isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '' ? '_small' : '') . '.raya_ganada', number_format($total_raya_ganada, 2));
						$tpl->assign('d_totales' . (isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '' ? '_small' : '') . '.produccion', number_format($total_produccion, 2));
					}
				} else if ($tipo == 'acumulado') {
					$tpl->newBlock('reporte_acumulado');

					$tpl->assign('fecha1', $_REQUEST['fecha1']);
					$tpl->assign('fecha2', $_REQUEST['fecha2']);

					$num_cia = NULL;

					foreach ($result as $row) {
						if ($num_cia != $row['num_cia']) {
							$num_cia = $row['num_cia'];

							$tpl->newBlock('a_cia');
							$tpl->assign('num_cia', $row['num_cia']);
							$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));

							$total_raya_ganada = 0;
							$total_raya_pagada = 0;
							$total_produccion = 0;

							$turno = NULL;

							if (!(isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '')) {
								$sql = '
									SELECT
										codturno
											AS turno,
										SUM(raya_ganada)
											AS raya_ganada,
										SUM(raya_pagada)
											AS raya_pagada,
										SUM(total_produccion)
											AS total_produccion
									FROM
										total_produccion
									WHERE
										numcia = ' . $num_cia . '
										AND fecha_total BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'
										' . (isset($_REQUEST['turno']) ? 'AND codturno IN (' . implode(', ', $_REQUEST['turno']) . ')' : '') . '
									GROUP BY
										turno
								';

								$tmp = $db->query($sql);

								$totales = array();

								if ($tmp) {
									foreach ($tmp as $t) {
										$totales[$t['turno']] = array(
											'raya_ganada' => $t['raya_ganada'],
											'raya_pagada' => $t['raya_pagada'],
											'produccion' => $t['total_produccion']
										);

										$total_raya_pagada += $t['raya_pagada'];
									}
								}

								$tpl->newBlock('a_totales');
								$tpl->assign('raya_pagada', number_format($total_raya_pagada, 2));
							} else {
								$tpl->newBlock('d_totales_small');
							}
						}

						if ($turno != $row['turno']) {
							$turno = $row['turno'];

							$tpl->newBlock('a_turno');

							switch ($turno) {

								case 1:
									$nombre_turno = 'FRANCES DE DIA';
									break;

								case 2:
									$nombre_turno = 'FRANCES DE NOCHE';
									break;

								case 3:
									$nombre_turno = 'BIZCOCHERO';
									break;

								case 4:
									$nombre_turno = 'REPOSTERO';
									break;

								case 8:
									$nombre_turno = 'PICONERO';
									break;

								case 9:
									$nombre_turno = 'GELATINERO';
									break;

							}

							$tpl->assign('turno', $nombre_turno);

							$bultos = 0;
							$piezas = 0;
							$imp_raya = 0;
							$imp_produccion = 0;

							if (!(isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '')) {
								$tpl->newBlock('a_raya_pagada');
								$tpl->assign('raya_pagada', number_format($totales[$turno]['raya_pagada'], 2));
							}
						}

						$tpl->newBlock('a_row');
						$tpl->assign('cod', $row['cod']);
						$tpl->assign('producto', utf8_encode($row['producto']));
						$tpl->assign('bultos', number_format($row['piezas'] * $row['total_bultos'] / $row['total_piezas'], 2));
						$tpl->assign('piezas', number_format($row['piezas'], 2));
						$tpl->assign('imp_raya', $row['imp_raya'] > 0 ? number_format($row['imp_raya'], 2) : '&nbsp;');
						$tpl->assign('imp_produccion', $row['imp_produccion'] > 0 ? number_format($row['imp_produccion'], 2) : '&nbsp;');

						$bultos += round($row['piezas'] * $row['total_bultos'] / $row['total_piezas'], 2);
						$piezas += $row['piezas'];
						$imp_raya += $row['imp_raya'];
						$imp_produccion += $row['imp_produccion'];

						$total_raya_ganada += $row['imp_raya'];
						$total_produccion += $row['imp_produccion'];

						$tpl->assign('a_turno.bultos', number_format($bultos, 2));
						$tpl->assign('a_turno.piezas', number_format($piezas, 2));
						$tpl->assign('a_turno.raya_ganada', number_format($imp_raya, 2));
						$tpl->assign('a_turno.produccion', number_format($imp_produccion, 2));

						$tpl->assign('a_totales' . (isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '' ? '_small' : '') . '.raya_ganada', number_format($total_raya_ganada, 2));
						$tpl->assign('a_totales' . (isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '' ? '_small' : '') . '.produccion', number_format($total_produccion, 2));
					}
				}
			}

			$tpl->printToScreen();

			break;

		case 'reporte_totales':
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

			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')) {
				list($dia, $mes, $anio) = array_map('toInt', explode('/', $_REQUEST['fecha1']));

				$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, $anio));
				$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anio));

				$condiciones[] = "fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";
			}

			if (isset($_REQUEST['turno'])) {
				$condiciones[] = 'cod_turnos IN (' . implode(', ', $_REQUEST['turno']) . ')';
			}

			if (isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '') {
				$productos = array();

				$pieces = explode(',', $_REQUEST['productos']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$productos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$productos[] = $piece;
					}
				}

				if (count($productos) > 0) {
					$condiciones[] = 'cod_producto IN (' . implode(', ', $productos) . ')';
				}
			}

			// if (!in_array($_SESSION['iduser'], array(1, 4, 6, 18, 19, 20, 24, 37, 42))) {
			// 	$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
			// }

			if (!in_array($_SESSION['iduser'], array(1, 4, 6, 18, 19, 20, 24, 37, 42, 48, 50))) {
				$condiciones[] = 'co.iduser = ' . $_SESSION['iduser'];
			}

			$sql = "
				SELECT
					num_cia,
					cc.nombre_corto
						AS nombre_cia,
					cod_producto
						AS cod,
					cp.nombre
						AS producto,
					/*cod_turnos
						AS turno,*/
					EXTRACT(MONTH FROM fecha)
						AS mes,
					SUM(imp_produccion)
						AS produccion
				FROM
					produccion p
					LEFT JOIN catalogo_productos cp
						USING (cod_producto)
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
					cod,
					mes,
					producto/*,
					turno*/
				ORDER BY
					num_cia,
					/*turno,*/
					cod,
					mes
			";

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/pan/ProduccionTotalesReporteImpreso.tpl');
			$tpl->prepare();

			$tpl->assign('anio', $anio);

			if ($result) {
				$num_cia = NULL;

				foreach ($result as $r)
				{
					if ($num_cia != $r['num_cia'])
					{
						if ($num_cia != NULL)
						{
							$tpl->assign('cia.total', number_format(array_sum($totales), 2));
						}

						$num_cia = $r['num_cia'];

						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', utf8_decode($r['nombre_cia']));

						$totales = array(
							1 => 0,
							2 => 0,
							3 => 0,
							4 => 0,
							5 => 0,
							6 => 0,
							7 => 0,
							8 => 0,
							9 => 0,
							10 => 0,
							11 => 0,
							12 => 0
						);

						$cod = NULL;
					}

					if ($cod != $r['cod'])
					{
						$cod = $r['cod'];

						$tpl->newBlock('row');
						$tpl->assign('cod', $cod);
						$tpl->assign('producto', utf8_decode($r['producto']));

						$total = 0;
					}

					$tpl->assign('pro_' . $r['mes'], number_format($r['produccion'], 2));

					$total += $r['produccion'];

					$tpl->assign('total', number_format($total, 2));

					$totales[$r['mes']] += $r['produccion'];

					$tpl->assign('cia.tot_' . $r['mes'], $totales[$r['mes']] > 0 ? number_format($totales[$r['mes']], 2) : '&nbsp;');
				}

				if ($num_cia != NULL)
				{
					$tpl->assign('cia.total', number_format(array_sum($totales), 2));
				}
			}

			$tpl->printToScreen();

			break;

		case 'exportar':
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

			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') || (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') && (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')
					&& $_REQUEST['fecha1'] != $_REQUEST['fecha2']) {
					$condiciones[] = 'fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';

					$tipo = 'acumulado';
				} else if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') && (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')
					&& $_REQUEST['fecha1'] == $_REQUEST['fecha2']) {
					$condiciones[] = 'fecha = \'' . $_REQUEST['fecha1'] . '\'';

					$tipo = 'diario';
				}  else if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') || (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
					$condiciones[] = 'fecha = \'' . ($_REQUEST['fecha1'] ? $_REQUEST['fecha1'] : $_REQUEST['fecha2']) . '\'';

					$tipo = 'diario';
				}
			}

			if (isset($_REQUEST['turno'])) {
				$condiciones[] = 'cod_turnos IN (' . implode(', ', $_REQUEST['turno']) . ')';
			}

			if (isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '') {
				$productos = array();

				$pieces = explode(',', $_REQUEST['productos']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$productos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$productos[] = $piece;
					}
				}

				if (count($productos) > 0) {
					$condiciones[] = 'cod_producto IN (' . implode(', ', $productos) . ')';
				}
			}

			if (!in_array($_SESSION['iduser'], array(1, 4, 6, 18, 19, 20, 24, 37, 42, 48))) {
				$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
			}

			if (!in_array($_SESSION['iduser'], array(1, 4, 6, 18, 19, 20, 24, 37, 42, 48))) {
				$condiciones[] = 'co.iduser = ' . $_SESSION['iduser'];
			}

			if ($tipo == 'diario') {
				$sql = '
					SELECT
						num_cia,
						cc.nombre_corto
							AS nombre_cia,
						fecha,
						cod_producto
							AS cod,
						cp.nombre
							AS producto,
						cod_turnos
							AS turno,
						piezas,
						CASE
							WHEN precio_raya > 0 THEN
								precio_raya::VARCHAR
							WHEN porc_raya > 0 THEN
								porc_raya || \'%\'
							ELSE
								\'\'
						END
							AS precio_raya,
						imp_raya,
						precio_venta,
						imp_produccion,
						COALESCE((
							SELECT
								SUM(piezas)
							FROM
								produccion
							WHERE
								num_cia = p.num_cia
								AND fecha = p.fecha
								AND cod_turnos = p.cod_turnos
						), 0)
							AS total_piezas,
						COALESCE((
							SELECT
								SUM(cantidad)
							FROM
								mov_inv_real
							WHERE
								num_cia = p.num_cia
								AND fecha = p.fecha
								AND codmp = 1
								AND tipo_mov = TRUE
								AND cod_turno = p.cod_turnos
								AND descripcion != \'DIFERENCIA INVENTARIO\'
						) / 44, 0)
							AS total_bultos
					FROM
						produccion p
						LEFT JOIN catalogo_productos cp
							USING (cod_producto)
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
						LEFT JOIN catalogo_operadoras co
							USING (idoperadora)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia,
						turno,
						cod
				';
			} else if ($tipo == 'acumulado') {
				$sql = '
					SELECT
						num_cia,
						cc.nombre_corto
							AS nombre_cia,
						cod_producto
							AS cod,
						cp.nombre
							AS producto,
						cod_turnos
							AS turno,
						SUM(piezas)
							AS piezas,
						SUM(imp_raya)
							AS imp_raya,
						SUM(imp_produccion)
							AS imp_produccion,
						COALESCE((
							SELECT
								SUM(piezas)
							FROM
								produccion
							WHERE
								num_cia = p.num_cia
								AND fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'
								AND cod_turnos = p.cod_turnos
						), 0)
							AS total_piezas,
						COALESCE((
							SELECT
								SUM(cantidad)
							FROM
								mov_inv_real
							WHERE
								num_cia = p.num_cia
								AND fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'
								AND codmp = 1
								AND tipo_mov = TRUE
								AND cod_turno = p.cod_turnos
								AND descripcion != \'DIFERENCIA INVENTARIO\'
						) / 44, 0)
							AS total_bultos
					FROM
						produccion p
						LEFT JOIN catalogo_productos cp
							USING (cod_producto)
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
						LEFT JOIN catalogo_operadoras co
							USING (idoperadora)
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						num_cia,
						nombre_cia,
						cod,
						producto,
						turno
					ORDER BY
						num_cia,
						turno,
						cod
				';
			}

			$result = $db->query($sql);

			$data = '';

			if ($result) {
				if ($tipo == 'diario') {
					$data .= '"Reporte de produccion del dia ' . ($_REQUEST['fecha1'] ? $_REQUEST['fecha1'] : $_REQUEST['fecha2']) . '"' . "\r\n";

					$num_cia = NULL;

					foreach ($result as $row) {
						if ($num_cia != $row['num_cia']) {
							if ($num_cia != NULL) {
								if ($turno != NULL) {
									$data .= "\r\n" . '"","","Raya ganada","' . $imp_raya . '","Produccion","' . $imp_produccion . '"';

									if (!(isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '')) {
										$data .= "\r\n" . '"","","Raya pagada","' . number_format($totales[$turno]['raya_pagada'], 2) . '"';
									}

									$data .= "\r\n";
								}
								if (!(isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '')) {
									$data .= "\r\n" . '"","","Raya ganada","Raya pagada","Produccion"';
									$data .= "\r\n" . '"","","' . number_format($total_raya_ganada, 2) . '","' . number_format($total_raya_pagada, 2) .  '","' . number_format($total_produccion, 2) . '"' . "\r\n";
								} else {
									$data .= "\r\n" . '"","","Raya ganada","Produccion"';
									$data .= "\r\n" . '"","","' . number_format($total_raya_ganada, 2) . '","' . number_format($total_produccion, 2) . '"' . "\r\n";
								}
							}

							$num_cia = $row['num_cia'];

							$data .= "\r\n" . '"' . $num_cia . ' ' . $row['nombre_cia'] . '"';

							$total_raya_ganada = 0;
							$total_raya_pagada = 0;
							$total_produccion = 0;

							$turno = NULL;

							if (!(isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '')) {
								$sql = '
									SELECT
										codturno
											AS turno,
										raya_ganada,
										raya_pagada,
										total_produccion
									FROM
										total_produccion
									WHERE
										numcia = ' . $num_cia . '
										AND fecha_total = \'' . $row['fecha'] . '\'
										' . (isset($_REQUEST['turno']) ? 'AND codturno IN (' . implode(', ', $_REQUEST['turno']) . ')' : '') . '
								';

								$tmp = $db->query($sql);

								$totales = array();

								if ($tmp) {
									foreach ($tmp as $t) {
										$totales[$t['turno']] = array(
											'raya_ganada' => $t['raya_ganada'],
											'raya_pagada' => $t['raya_pagada'],
											'produccion' => $t['total_produccion']
										);

										$total_raya_pagada += $t['raya_pagada'];
									}
								}
							}
						}

						if ($turno != $row['turno']) {
							if ($turno != NULL) {
								$data .= "\r\n" . '"","","Raya ganada","' . number_format($imp_raya, 2) . '","Produccion","' . number_format($imp_produccion, 2) . '"';

								if (!(isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '')) {
									$data .= "\r\n" . '"","","Raya pagada","' . number_format($totales[$turno]['raya_pagada'], 2) . '"';
								}
							}

							$turno = $row['turno'];

							switch ($turno) {

								case 1:
									$data .= "\r\n" . '"FRANCES DE DIA"';
									break;

								case 2:
									$data .= "\r\n" . '"FRANCES DE NOCHE"';
									break;

								case 3:
									$data .= "\r\n" . '"BIZCOCHERO"';
									break;

								case 4:
									$data .= "\r\n" . '"REPOSTERO"';
									break;

								case 8:
									$data .= "\r\n" . '"PICONERO"';
									break;

								case 9:
									$data .= "\r\n" . '"GELATINERO"';
									break;

							}

							$data .= "\r\n" . '"Producto","Piezas","Precio raya","Importe raya","Precio venta","Importe produccion"';

							$imp_raya = 0;
							$imp_produccion = 0;

						}

						$data .= "\r\n" . '"' . $row['cod'] . ' ' . $row['producto'] . '","' . $row['piezas'] . '","' . $row['precio_raya'] . '","' . number_format($row['imp_raya'], 2) . '","' . number_format($row['precio_venta'], 2) . '","' . number_format($row['imp_produccion'], 2) . '"';

						$imp_raya += $row['imp_raya'];
						$imp_produccion += $row['imp_produccion'];

						$total_raya_ganada += $row['imp_raya'];
						$total_produccion += $row['imp_produccion'];
					}

					if ($num_cia != NULL) {
						if ($turno != NULL) {
							$data .= "\r\n" . '"","","Raya ganada","' . number_format($imp_raya, 2) . '","Produccion","' . number_format($imp_produccion, 2) . '"';

							if (!(isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '')) {
								$data .= "\r\n" . '"","","Raya pagada","' . number_format($totales[$turno]['raya_pagada'], 2) . '"';
							}

							$data .= "\r\n";
						}
						if (!(isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '')) {
							$data .= "\r\n" . '"","","Raya ganada","Raya pagada","Produccion"';
							$data .= "\r\n" . '"","","' . number_format($total_raya_ganada, 2) . '","' . number_format($total_raya_pagada, 2) .  '","' . number_format($total_produccion, 2) . '"' . "\r\n";
						} else {
							$data .= "\r\n" . '"","","Raya ganada","Produccion"';
							$data .= "\r\n" . '"","","' . number_format($total_raya_ganada, 2) . '","' . number_format($total_produccion, 2) . '"' . "\r\n";
						}
					}
				} else if ($tipo == 'acumulado') {
					$data .= '"Reporte de produccion del periodo ' . $_REQUEST['fecha1'] . ' al ' . $_REQUEST['fecha2'] . '"' . "\r\n";

					$num_cia = NULL;

					foreach ($result as $row) {
						if ($num_cia != $row['num_cia']) {
							if ($num_cia != NULL) {
								if ($turno != NULL) {
									$data .= "\r\n" . '"","Totales","' . $imp_raya . '","' . $imp_produccion . '"';

									if (!(isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '')) {
										$data .= "\r\n" . '"","Raya pagada","' . number_format($totales[$turno]['raya_pagada'], 2) . '"';
									}

									$data .= "\r\n";
								}
								if (!(isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '')) {
									$data .= "\r\n" . '"","Raya ganada","Raya pagada","Produccion"';
									$data .= "\r\n" . '"","' . number_format($total_raya_ganada, 2) . '","' . number_format($total_raya_pagada, 2) .  '","' . number_format($total_produccion, 2) . '"' . "\r\n";
								} else {
									$data .= "\r\n" . '"","Raya ganada","Produccion"';
									$data .= "\r\n" . '"","' . number_format($total_raya_ganada, 2) . '","' . number_format($total_produccion, 2) . '"' . "\r\n";
								}
							}

							$num_cia = $row['num_cia'];

							$data .= "\r\n" . '"' . $num_cia . ' ' . $row['nombre_cia'] . '"';

							$total_raya_ganada = 0;
							$total_raya_pagada = 0;
							$total_produccion = 0;

							$turno = NULL;

							if (!(isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '')) {
								$sql = '
									SELECT
										codturno
											AS turno,
										SUM(raya_ganada)
											AS raya_ganada,
										SUM(raya_pagada)
											AS raya_pagada,
										SUM(total_produccion)
											AS total_produccion
									FROM
										total_produccion
									WHERE
										numcia = ' . $num_cia . '
										AND fecha_total BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'
										' . (isset($_REQUEST['turno']) ? 'AND codturno IN (' . implode(', ', $_REQUEST['turno']) . ')' : '') . '
									GROUP BY
										turno
								';

								$tmp = $db->query($sql);

								$totales = array();

								if ($tmp) {
									foreach ($tmp as $t) {
										$totales[$t['turno']] = array(
											'raya_ganada' => $t['raya_ganada'],
											'raya_pagada' => $t['raya_pagada'],
											'produccion' => $t['total_produccion']
										);

										$total_raya_pagada += $t['raya_pagada'];
									}
								}
							}
						}

						if ($turno != $row['turno']) {
							if ($turno != NULL) {
								$data .= "\r\n" . '"","Totales","' . number_format($imp_raya, 2) . '","' . number_format($imp_produccion, 2) . '"';

								if (!(isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '')) {
									$data .= "\r\n" . '"","Raya pagada","' . number_format($totales[$turno]['raya_pagada'], 2) . '"';
								}
							}

							$turno = $row['turno'];

							switch ($turno) {

								case 1:
									$data .= "\r\n" . '"FRANCES DE DIA"';
									break;

								case 2:
									$data .= "\r\n" . '"FRANCES DE NOCHE"';
									break;

								case 3:
									$data .= "\r\n" . '"BIZCOCHERO"';
									break;

								case 4:
									$data .= "\r\n" . '"REPOSTERO"';
									break;

								case 8:
									$data .= "\r\n" . '"PICONERO"';
									break;

								case 9:
									$data .= "\r\n" . '"GELATINERO"';
									break;

							}

							$data .= "\r\n" . '"Producto","Piezas","Importe raya","Importe produccion"';

							$imp_raya = 0;
							$imp_produccion = 0;

						}

						$data .= "\r\n" . '"' . $row['cod'] . ' ' . $row['producto'] . '","' . $row['piezas'] . '","' . number_format($row['imp_raya'], 2) . '","' . number_format($row['imp_produccion'], 2) . '"';

						$imp_raya += $row['imp_raya'];
						$imp_produccion += $row['imp_produccion'];

						$total_raya_ganada += $row['imp_raya'];
						$total_produccion += $row['imp_produccion'];
					}

					if ($num_cia != NULL) {
						if ($turno != NULL) {
							$data .= "\r\n" . '"","Totales","' . number_format($imp_raya, 2) . '","' . number_format($imp_produccion, 2) . '"';

							if (!(isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '')) {
								$data .= "\r\n" . '"","Raya pagada","' . number_format($totales[$turno]['raya_pagada'], 2) . '"';
							}

							$data .= "\r\n";
						}
						if (!(isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '')) {
							$data .= "\r\n" . '"","Raya ganada","Raya pagada","Produccion"';
							$data .= "\r\n" . '"","' . number_format($total_raya_ganada, 2) . '","' . number_format($total_raya_pagada, 2) .  '","' . number_format($total_produccion, 2) . '"' . "\r\n";
						} else {
							$data .= "\r\n" . '"","Raya ganada","Produccion"';
							$data .= "\r\n" . '"","' . number_format($total_raya_ganada, 2) . '","' . number_format($total_produccion, 2) . '"' . "\r\n";
						}
					}
				}
			}

			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename=produccion.csv');

			echo $data;

			break;

		case 'exportar_totales':
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

			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')) {
				list($dia, $mes, $anio) = array_map('toInt', explode('/', $_REQUEST['fecha1']));

				$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, $anio));
				$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anio));

				$condiciones[] = "fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";
			}

			if (isset($_REQUEST['turno'])) {
				$condiciones[] = 'cod_turnos IN (' . implode(', ', $_REQUEST['turno']) . ')';
			}

			if (isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '') {
				$productos = array();

				$pieces = explode(',', $_REQUEST['productos']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$productos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$productos[] = $piece;
					}
				}

				if (count($productos) > 0) {
					$condiciones[] = 'cod_producto IN (' . implode(', ', $productos) . ')';
				}
			}

			// if (!in_array($_SESSION['iduser'], array(1, 4, 6, 18, 19, 20, 24, 37, 42))) {
			// 	$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
			// }

			if (!in_array($_SESSION['iduser'], array(1, 4, 6, 18, 19, 20, 24, 37, 42))) {
				$condiciones[] = 'co.iduser = ' . $_SESSION['iduser'];
			}

			$sql = "
				SELECT
					num_cia,
					cc.nombre_corto
						AS nombre_cia,
					cod_producto
						AS cod,
					cp.nombre
						AS producto,
					/*cod_turnos
						AS turno,*/
					EXTRACT(MONTH FROM fecha)
						AS mes,
					SUM(imp_produccion)
						AS produccion
				FROM
					produccion p
					LEFT JOIN catalogo_productos cp
						USING (cod_producto)
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
					cod,
					mes,
					producto/*,
					turno*/
				ORDER BY
					num_cia,
					/*turno,*/
					cod,
					mes
			";

			$result = $db->query($sql);

			$data = '"REPORTE DE PRODUCCION DEL AÑO ' . $anio . '"' . "\r\n";

			if ($result)
			{
				$num_cia = NULL;

				foreach ($result as $r)
				{
					if ($num_cia != $r['num_cia'])
					{
						if ($num_cia != NULL)
						{
							$data .= "\r\n" . implode(',', $row);

							$data .= "\r\n" . '"","TOTALES","' . implode('","', $totales) . '","' . array_sum($totales) . '"' . "\r\n";
						}

						$num_cia = $r['num_cia'];

						$data .= "\r\n" . '"' . $num_cia . ' ' . $r['nombre_cia'] . '"';

						$data .= "\r\n" . '"COD.","PRODUCTO","ENE","FEB","MARZO","ABR","MAY","JUN","JUL","AGO","SEP","OCT","NOV","DIC","TOTAL"';

						$totales = array_fill(1, 12, 0);

						$cod = NULL;
					}

					if ($cod != $r['cod'])
					{
						if ($cod != NULL)
						{
							$data .= "\r\n" . implode(',', $row);
						}

						$cod = $r['cod'];

						$row = array();

						$row['cod'] = '"' . $cod . '"';
						$row['producto'] = '"' . utf8_decode($r['producto']) . '"';

						$row = array_merge($row, array_fill(1, 12, '"' . 0 . '"'), array('total' => '"0"'));

						$total = 0;
					}

					$row[$r['mes'] - 1] = '"' . $r['produccion'] . '"';

					$total += $r['produccion'];

					$row['total'] = '"' . $total . '"';

					$totales[$r['mes']] += $r['produccion'];
				}

				if ($num_cia != NULL)
				{
					$data .= "\r\n" . implode(',', $row);

					$data .= "\r\n" . '"","TOTALES","' . implode('","', $totales) . '","' . array_sum($totales) . '"' . "\r\n";
				}
			}

			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename=produccion_anual_' . $anio . '.csv');

			echo $data;

			break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/pan/ProduccionReporte.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$isIpad = /*(bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')*/FALSE;

$tpl->newBlock($isIpad ? 'ipad' : 'normal');

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
		$tpl->newBlock(($isIpad ? 'i' : 'n') . '_admin');
		$tpl->assign('value', $r['value']);
		$tpl->assign('text', utf8_encode($r['text']));
	}
}

if ($isIpad) {
	$condiciones[] = 'cc.num_cia <= 300';

	if (!in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 24, 37, 48))) {
		$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
	}

	$sql = '
		SELECT
			cc.num_cia
				AS value,
			cc.num_cia || \' \' || cc.nombre_corto
				AS text
		FROM
			catalogo_companias cc
			LEFT JOIN catalogo_administradores ca
				USING (idadministrador)
		WHERE
			' . implode(' AND ', $condiciones) . '
		ORDER BY
			cc.num_cia
	';

	$cias = $db->query($sql);

	if ($cias) {
		foreach ($cias as $c) {
			$tpl->newBlock('i_cia');
			$tpl->assign('value', $c['value']);
			$tpl->assign('text', utf8_encode($c['text']));
		}
	}

	$sql = '
		SELECT
			cod_producto
				AS value,
			cod_producto || \' \' || nombre
				AS text
		FROM
			catalogo_productos
		ORDER BY
			nombre
	';

	$productos = $db->query($sql);

	if ($productos) {
		foreach ($productos as $p) {
			$tpl->newBlock('i_pro');
			$tpl->assign('value', $p['value']);
			$tpl->assign('text', utf8_encode($p['text']));
		}
	}
}

$tpl->printToScreen();
?>
