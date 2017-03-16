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
			$condiciones = array();

			$condiciones[] = 'copia_fac = FALSE';

			if (isset($_REQUEST['fecha_corte']) && $_REQUEST['fecha_corte'] != '') {
				$condiciones[] = 'pp.fecha <= \'' . $_REQUEST['fecha_corte'] . '\'';
			}

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
					$condiciones[] = 'pp.num_cia IN (' . implode(', ', $cias) . ')';
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
					$condiciones[] = 'pp.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}

			$sql = '
				SELECT
					pp.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					pp.num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					pp.num_fact,
					pp.fecha,
					pp.total
						AS importe
				FROM
					pasivo_proveedores pp
					LEFT JOIN facturas f
						USING (num_proveedor, num_fact)
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
					LEFT JOIN catalogo_companias cc
						ON (cc.num_cia = pp.num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_pro,
					num_cia,
					num_fact
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/fac/FacturasPendientesReporte.tpl');
			$tpl->prepare();

			if ($result) {
				$num_pro = NULL;

				foreach ($result as $rec) {
					if ($num_pro != $rec['num_pro']) {
						if ($num_pro != NULL) {
							$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');
						}

						$num_pro = $rec['num_pro'];

						$tpl->newBlock('reporte');

						$tpl->assign('num_pro', $num_pro);
						$tpl->assign('nombre_pro', utf8_encode($rec['nombre_pro']));

						$total = 0;
					}

					$tpl->newBlock('row');
					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
					$tpl->assign('num_fact', $rec['num_fact']);
					$tpl->assign('fecha', $rec['fecha']);
					$tpl->assign('importe', number_format($rec['importe'], 2));

					$total += $rec['importe'];

					$tpl->assign('reporte.total', number_format($total, 2));
				}
			}

			$tpl->printToScreen();
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturasPendientes.tpl');
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
