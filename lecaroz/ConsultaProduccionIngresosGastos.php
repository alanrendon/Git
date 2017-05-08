<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

if(!function_exists('json_encode')) {
	include_once('includes/JSON.php');

	$GLOBALS['JSON_OBJECT'] = new Services_JSON();

	function json_encode($value) {
		return $GLOBALS['JSON_OBJECT']->encode($value);
	}

	function json_decode($value) {
		return $GLOBALS['JSON_OBJECT']->decode($value);
	}
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
	12 => 'DICIMEBRE'
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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'reporte':
			$fecha_pieces = explode('/', $_REQUEST['fecha']);

			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $fecha_pieces[1], 1, $fecha_pieces[2]));
			$fecha2 = $_REQUEST['fecha'];

			$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			$condiciones[] = 'efe = \'TRUE\' AND exp = \'TRUE\' AND gas = \'TRUE\' AND pro = \'TRUE\' AND pas = \'TRUE\'';

			/*
			@ Intervalo de compañías
			*/
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

			/*
			@ Administrador
			*/
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}

			/*
			@ Usuario
			*/
			if (!in_array($_SESSION['iduser'], array(1, 4, 14, 18, 19, 20, 24, 37, 62, 48))) {
				$condiciones[] = '(co.iduser = ' . $_SESSION['iduser'] . ' OR ca.iduser = ' . $_SESSION['iduser'] . ')';
			}

			$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS
							nombre_cia,
					fecha,
					EXTRACT(day FROM fecha)
						AS
							dia,
					EXTRACT(month FROM fecha)
						AS
							mes,
					EXTRACT(year FROM fecha)
						AS
							anio,
					venta_puerta,
					abono,
					otros + pastillaje
						AS
							otros,
					venta_puerta + abono + otros + pastillaje
						AS
							ingresos,
					raya_pagada
						AS
							raya,
					gastos,
					efectivo
				FROM
						total_panaderias tp
					LEFT JOIN
						catalogo_companias cc
							USING
								(
									num_cia
								)
					LEFT JOIN
						catalogo_administradores ca
							USING
								(
									idadministrador
								)
					LEFT JOIN
						catalogo_operadoras co
							USING
								(
									idoperadora
								)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia,
					fecha
			';

			$result = $db->query($sql);

			if ($result) {
				$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

				$tpl = new TemplatePower('plantillas/pan/' . ($isIpad ? 'ReporteProduccionIngresosGastosIpad.tpl' : 'ReporteProduccionIngresosGastos.tpl'));
				$tpl->prepare();

				$num_cia = NULL;
				$hojas = 0;
				foreach ($result as $index => $rec) {
					if ($num_cia != $rec['num_cia']) {
						if ($num_cia != NULL) {
							foreach ($totales as $key => $value) {
								$tpl->assign('reporte.' . $key, number_format($value, $key == 'clientes' ? 0 : 2, '.', ','));
							}

							foreach ($totales as $key => $value) {
								$tpl->assign('reporte.p_' . $key, number_format($value / $fecha_pieces[0], $key == 'clientes' ? 0 : 2, '.', ','));
							}

							if ($hojas % 2 == 0) {
								$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');
							}
						}

						$num_cia = $rec['num_cia'];

						$hojas++;

						/*
						@
						@@ Obtener todos los datos del reporte para la compañía dada
						@
						*/

						/*
						@ Producción
						*/
						$sql = '
							SELECT
								fecha_total
									AS
										fecha,
								SUM(total_produccion)
									AS
										importe
							FROM
								total_produccion
							WHERE
									numcia = ' . $num_cia . '
								AND
									fecha_total BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
							GROUP BY
								fecha
							ORDER BY
								fecha
						';
						$tmp = $db->query($sql);

						$produccion = array();
						if ($tmp) {
							foreach ($tmp as $t) {
								$produccion[$t['fecha']] = $t['importe'];
							}
						}

						/*
						@ Sueldo empleados
						*/
						$sql = '
							SELECT
								fecha,
								SUM(importe)
									AS
										importe
							FROM
								movimiento_gastos
							WHERE
									num_cia = ' . $num_cia . '
								AND
									fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
								AND
									codgastos = 1
								AND
									captura = \'FALSE\'
							GROUP BY
								fecha
							ORDER BY
								fecha
						';
						$tmp = $db->query($sql);

						$sueldo_empleados = array();
						if ($tmp) {
							foreach ($tmp as $t) {
								$sueldo_empleados[$t['fecha']] = $t['importe'];
							}
						}

						/*
						@ Sueldo encargado
						*/
						$sql = '
							SELECT
								fecha,
								SUM(importe)
									AS
										importe
							FROM
								movimiento_gastos
							WHERE
									num_cia = ' . $num_cia . '
								AND
									fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
								AND
									codgastos = 2
								AND
									captura = \'FALSE\'
							GROUP BY
								fecha
							ORDER BY
								fecha
						';
						$tmp = $db->query($sql);

						$sueldo_encargado = array();
						if ($tmp) {
							foreach ($tmp as $t) {
								$sueldo_encargado[$t['fecha']] = $t['importe'];
							}
						}

						/*
						@ Panaderos
						*/
						$sql = '
							SELECT
								fecha,
								SUM(importe)
									AS
										importe
							FROM
								movimiento_gastos
							WHERE
									num_cia = ' . $num_cia . '
								AND
									fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
								AND
									codgastos = 3
								AND
									captura = \'FALSE\'
							GROUP BY
								fecha
							ORDER BY
								fecha
						';
						$tmp = $db->query($sql);

						$panaderos = array();
						if ($tmp) {
							foreach ($tmp as $t) {
								$panaderos[$t['fecha']] = $t['importe'];
							}
						}

						/*
						@ Clientes
						*/
						$sql = '
							SELECT
								fecha,
								SUM(ctes)
									AS
										clientes
							FROM
								captura_efectivos
							WHERE
									num_cia = ' . $num_cia . '
								AND
									fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
							GROUP BY
								fecha
							ORDER BY
								fecha
						';
						$tmp = $db->query($sql);

						$clientes = array();
						if ($tmp) {
							foreach ($tmp as $t) {
								$clientes[$t['fecha']] = $t['clientes'];
							}
						}

						$tpl->newBlock('reporte');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $rec['nombre_cia']);
						$tpl->assign('dia', $fecha_pieces[0]);
						$tpl->assign('mes', $_meses[intval($fecha_pieces[1], 10)]);
						$tpl->assign('anio', $fecha_pieces[2]);

						$sobrante = 0;
						$dias = 0;

						$totales = array(
							'produccion'       => 0,
							'venta_puerta'     => 0,
							'abono'            => 0,
							'otros'            => 0,
							'ingresos'         => 0,
							'raya'             => 0,
							'sueldo_empleados' => 0,
							'sueldo_encargado' => 0,
							'panaderos'        => 0,
							'otros_gastos'     => 0,
							'gastos'           => 0,
							'efectivo'         => 0,
							'clientes'         => 0
						);

						$bgcolor = FALSE;
					}

					$tpl->newBlock('row');

					if ($isIpad) {
						$tpl->assign('bgcolor', $bgcolor ? 'bgGray' : 'bgWhite');
						$bgcolor = !$bgcolor;
					}

					$tpl->assign('dial', $_dias[date('w', mktime(0, 0, 0, $rec['mes'], $rec['dia'], $rec['anio']))]);
					$tpl->assign('dian', str_pad($rec['dia'], 2, '0', STR_PAD_LEFT));

					$tpl->assign('produccion', isset($produccion[$rec['fecha']]) ? number_format($produccion[$rec['fecha']], 2, '.', ',') : '&nbsp;');
					$tpl->assign('venta_puerta', $rec['venta_puerta'] != 0 ? number_format($rec['venta_puerta'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('abono', $rec['abono'] != 0 ? number_format($rec['abono'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('otros', $rec['otros'] != 0 ? number_format($rec['otros'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('ingresos', $rec['ingresos'] != 0 ? number_format($rec['ingresos'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('raya', $rec['raya'] != 0 ? number_format($rec['raya'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('sueldo_empleados', isset($sueldo_empleados[$rec['fecha']]) ? number_format($sueldo_empleados[$rec['fecha']], 2, '.', ',') : '&nbsp;');
					$tpl->assign('sueldo_encargado', isset($sueldo_encargado[$rec['fecha']]) ? number_format($sueldo_encargado[$rec['fecha']], 2, '.', ',') : '&nbsp;');
					$tpl->assign('panaderos', isset($panaderos[$rec['fecha']]) ? number_format($panaderos[$rec['fecha']], 2, '.', ',') : '&nbsp;');

					$otros_gastos = $rec['gastos']
					                - (isset($sueldo_empleados[$rec['fecha']]) ? $sueldo_empleados[$rec['fecha']] : 0)
									- (isset($sueldo_encargado[$rec['fecha']]) ? $sueldo_encargado[$rec['fecha']] : 0)
									- (isset($panaderos[$rec['fecha']]) ? $panaderos[$rec['fecha']] : 0);

					$tpl->assign('otros_gastos', $otros_gastos != 0 ? number_format($otros_gastos, 2, '.', ',') : '&nbsp;');
					$tpl->assign('gastos', $rec['gastos'] != 0 ? number_format($rec['gastos'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('efectivo', $rec['efectivo'] != 0 ? number_format($rec['efectivo'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('clientes', isset($clientes[$rec['fecha']]) ? number_format($clientes[$rec['fecha']]) : '&nbsp;');

					$totales['produccion'] += isset($produccion[$rec['fecha']]) ? $produccion[$rec['fecha']] : 0;
					$totales['venta_puerta'] += $rec['venta_puerta'];
					$totales['abono'] += $rec['abono'];
					$totales['otros'] += $rec['otros'];
					$totales['ingresos'] += $rec['ingresos'];
					$totales['raya'] += $rec['raya'];
					$totales['sueldo_empleados'] += isset($sueldo_empleados[$rec['fecha']]) ? $sueldo_empleados[$rec['fecha']] : 0;
					$totales['sueldo_encargado'] += isset($sueldo_encargado[$rec['fecha']]) ? $sueldo_encargado[$rec['fecha']] : 0;
					$totales['panaderos'] += isset($panaderos[$rec['fecha']]) ? $panaderos[$rec['fecha']] : 0;
					$totales['otros_gastos'] += $otros_gastos;
					$totales['gastos'] += $rec['gastos'];
					$totales['efectivo'] += $rec['efectivo'];
					$totales['clientes'] += isset($clientes[$rec['fecha']]) ? $clientes[$rec['fecha']] : 0;
				}
				if ($num_cia != NULL) {
					foreach ($totales as $key => $value) {
						$tpl->assign('reporte.' . $key, number_format($value, $key == 'clientes' ? 0 : 2, '.', ','));
					}

					foreach ($totales as $key => $value) {
						$tpl->assign('reporte.p_' . $key, number_format($value / $fecha_pieces[0], $key == 'clientes' ? 0 : 2, '.', ','));
					}
				}

				$tpl->printToScreen();
			}
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/pan/ConsultaProduccionIngresosGastos.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

$tpl->newBlock($isIpad ? 'ipad' : 'normal');

if ($isIpad) {
	$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 1, date('Y'))));

	$condiciones[] = 'num_cia <= 300';

	if (!in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 24, 37, 62))) {
		$condiciones[] = '(co.iduser = ' . $_SESSION['iduser'] . ' OR ca.iduser = ' . $_SESSION['iduser'] . ')';
	}

	$sql = '
		SELECT
			num_cia,
			nombre_corto
				AS
					nombre_cia
		FROM
				catalogo_companias cc
			LEFT JOIN
				catalogo_administradores ca
					USING
						(
							idadministrador
						)
			LEFT JOIN
				catalogo_operadoras co
					USING
						(
							idoperadora
						)
		WHERE
			' . implode(' AND ', $condiciones) . '
		ORDER BY
			num_cia
	';
	$cias = $db->query($sql);

	foreach ($cias as $c) {
		$tpl->newBlock('cia');
		$tpl->assign('num_cia', $c['num_cia']);
		$tpl->assign('nombre_cia', $c['nombre_cia']);
	}
}
else {
	$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 1, date('Y'))));

	$sql = '
		SELECT
			idadministrador
				AS
					id,
			nombre_administrador
				AS
					nombre
		FROM
			catalogo_administradores
		ORDER BY
			nombre
	';
	$admins = $db->query($sql);

	foreach ($admins as $a) {
		$tpl->newBlock('admin');
		$tpl->assign('id', $a['id']);
		$tpl->assign('nombre', $a['nombre']);
	}
}

$tpl->printToScreen();
?>
