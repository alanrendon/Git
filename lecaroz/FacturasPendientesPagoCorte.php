<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

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

function filter($value) {
	return $value != 0;
}

$_meses = array(
	1  => 'Enero',
	2  => 'Febrero',
	3  => 'Marzo',
	4  => 'Abril',
	5  => 'Mayo',
	6  => 'Junio',
	7  => 'Julio',
	8  => 'Agosto',
	9  => 'Septiembre',
	10 => 'Octubre',
	11 => 'Noviembre',
	12 => 'Diciembre'
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
			$condiciones1 = array();

			$condiciones1[] = 'pp.fecha <= \'' . $_REQUEST['fecha_corte'] . '\'';

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
					$condiciones1[] = 'pp.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['pros']) && trim($_REQUEST['pros']) != '') {
				$pros = array();

				$pieces = explode(',', $_REQUEST['pros']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$pros[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$pros[] = $piece;
					}
				}

				if (count($pros) > 0) {
					$condiciones1[] = 'pp.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones1[] = 'idadministrador = ' . $_REQUEST['admin'];
			}

			$condiciones2 = array();

			$condiciones2[] = 'fp.fecha <= \'' . $_REQUEST['fecha_corte'] . '\'';

			$condiciones2[] = 'fp.cuenta IS NOT NULL';

			$condiciones2[] = 'fp.fecha_cheque IS NOT NULL';

			$condiciones2[] = 'c.fecha_cancelacion IS NULL';

			$condiciones2[] = 'ec.fecha IS NOT NULL';

			$condiciones2[] = '(ec.fecha_con IS NULL OR ec.fecha_con > \'' . $_REQUEST['fecha_corte'] . '\')';

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
					$condiciones2[] = 'fp.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['pros']) && trim($_REQUEST['pros']) != '') {
				$pros = array();

				$pieces = explode(',', $_REQUEST['pros']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$pros[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$pros[] = $piece;
					}
				}

				if (count($pros) > 0) {
					$condiciones2[] = 'fp.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones2[] = 'idadministrador = ' . $_REQUEST['admin'];
			}

			$condiciones3 = array();

			$condiciones3[] = 'f.fecha <= \'' . $_REQUEST['fecha_corte'] . '\'';

			$condiciones3[] = '(f.tspago IS NULL OR (c.importe > 0 AND ec.fecha_con IS NULL) OR ec.fecha_con > \'' . $_REQUEST['fecha_corte'] . '\')';

			$condiciones3[] = 'c.fecha_cancelacion IS NULL';

			$condiciones3[] = '(f.clave IS NULL OR f.clave = 0)';

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
					$condiciones3[] = 'f.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['pros']) && trim($_REQUEST['pros']) != '') {
				$pros = array();

				$pieces = explode(',', $_REQUEST['pros']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$pros[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$pros[] = $piece;
					}
				}

				if (count($pros) > 0) {
					$condiciones3[] = 'f.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones3[] = 'idadministrador = ' . $_REQUEST['admin'];
			}

			$sql = 'SELECT
				f.num_cia,
				cc.nombre_corto AS nombre_cia,
				f.num_proveedor AS num_pro,
				cp.nombre AS nombre_pro,
				f.num_fact,
				f.fecha,
				f.total AS importe
			FROM
				facturas f
				LEFT JOIN pasivo_proveedores pp USING (num_proveedor, num_fact, fecha)
				LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = f.num_proveedor)
				LEFT JOIN catalogo_companias cc ON (cc.num_cia = f.num_cia)
			WHERE
				' . implode(' AND ', $condiciones1) . '

			UNION

			SELECT
				f.num_cia,
				cc.nombre_corto AS nombre_cia,
				f.num_proveedor AS num_pro,
				cp.nombre AS nombre_pro,
				f.num_fact,
				f.fecha,
				f.total AS importe
			FROM
				facturas f
				LEFT JOIN facturas_pagadas fp USING (num_proveedor, num_fact, fecha)
				LEFT JOIN cheques c ON (
					c.num_cia = fp.num_cia
					AND c.cuenta = fp.cuenta
					AND c.folio = fp.folio_cheque
					AND c.fecha = fp.fecha_cheque
				)
				LEFT JOIN estado_cuenta ec ON (
					ec.num_cia = c.num_cia
					AND ec.cuenta = c.cuenta
					AND ec.folio = c.folio
					AND ec.fecha = c.fecha
				)
				LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = f.num_proveedor)
				LEFT JOIN catalogo_companias cc ON (cc.num_cia = f.num_cia)
			WHERE
				' . implode(' AND ', $condiciones2) . '

			UNION

			SELECT
				f.num_cia,
				cc.nombre_corto AS nombre_cia,
				f.num_proveedor AS num_pro,
				cp.nombre AS nombre_pro,
				f.num_fact::VARCHAR AS num_fact,
				f.fecha,
				f.total AS importe
			FROM
				facturas_zap f
				LEFT JOIN cheques c ON (
					c.num_cia = f.num_cia
					AND c.cuenta = f.cuenta
					AND c.folio = f.folio
				)
				LEFT JOIN estado_cuenta ec ON (
					ec.num_cia = c.num_cia
					AND ec.cuenta = c.cuenta
					AND ec.folio = c.folio
				)
				LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = f.num_proveedor)
				LEFT JOIN catalogo_companias cc ON (cc.num_cia = f.num_cia)
			WHERE
				' . implode(' AND ', $condiciones3) . '

			ORDER BY
				num_cia,
				num_pro,
				num_fact';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/fac/FacturasPendientesPagoCorteReporte.tpl');
			$tpl->prepare();

			$tpl->newBlock('reporte');

			$tpl->assign('fecha', $_REQUEST['fecha_corte']);

			if ($result) {
				$num_cia = NULL;

				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];

						$tpl->newBlock('cia');

						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));

						$total = 0;
					}

					$tpl->newBlock('row');
					$tpl->assign('num_pro', $rec['num_pro']);
					$tpl->assign('nombre_pro', utf8_encode($rec['nombre_pro']));
					$tpl->assign('num_fact', $rec['num_fact']);
					$tpl->assign('fecha', $rec['fecha']);
					$tpl->assign('importe', number_format($rec['importe'], 2));

					$total += $rec['importe'];

					$tpl->assign('cia.total', number_format($total, 2));
				}
			}

			$tpl->printToScreen();
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturasPendientesPagoCorte.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha_corte', date('d/m/Y'));

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

$admins = $db->query($sql);

if ($admins) {
	foreach ($admins as $a) {
		$tpl->newBlock('admin');
		$tpl->assign('value', $a['value']);
		$tpl->assign('text', utf8_encode($a['text']));
	}
}

$tpl->printToScreen();
?>
