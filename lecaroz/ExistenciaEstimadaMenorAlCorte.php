<?php
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';
include 'includes/dbstatus.php';

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

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'reporte':
			$condiciones = array();

			$condiciones[] = 'num_cia <= 300';

			if (isset($_REQUEST['mps']) && trim($_REQUEST['mps']) != '') {
				$mps = array();

				$pieces = explode(',', $_REQUEST['mps']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$mps[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$mps[] = $piece;
					}
				}

				if (count($mps) > 0) {
					$condiciones[] = 'codmp IN (' . implode(', ', $mps) . ')';
				}
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
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}

			$sql = '
				SELECT
					*
				FROM
					(
						SELECT
							*,

							CASE
								WHEN consumo > 0 THEN
									consumo / EXTRACT(day FROM fecha)
								ELSE
									NULL
							END
								promedio,
							CASE
								WHEN consumo > 0 THEN
									FLOOR(existencia / (consumo / EXTRACT(day FROM fecha)))
								ELSE
									NULL
							END
								dias,
							(SELECT num_proveedor FROM entrada_mp WHERE id = result.compra_id LIMIT 1) AS num_pro,
							(SELECT nombre FROM entrada_mp LEFT JOIN catalogo_proveedores USING (num_proveedor) WHERE id = result.compra_id LIMIT 1) AS nombre_pro,
							(SELECT fecha FROM entrada_mp WHERE id = result.compra_id LIMIT 1) AS ultima_compra,
							compra_id
						FROM
							(
								SELECT
									codmp,
									cmp.nombre
										AS nombre_mp,
									num_cia,
									cc.nombre_corto
										AS nombre_cia,
									existencia,
									COALESCE((
										SELECT
											SUM(cantidad)
										FROM
											mov_inv_virtual movs
										WHERE
											num_cia = inv.num_cia
											AND codmp = inv.codmp
											AND tipo_mov = TRUE
											AND fecha BETWEEN (
												SELECT
													MAX(fecha) + INTERVAL \'1 day\'
												FROM
													historico_inventario
												WHERE
													num_cia = inv.num_cia
													AND codmp = inv.codmp
											) AND now()::date
									), 0)
										AS consumo,
									COALESCE((
										SELECT
											MAX(fecha)
										FROM
											mov_inv_real movs
										WHERE
											num_cia = inv.num_cia
											AND codmp = inv.codmp
											AND tipo_mov = TRUE
											AND fecha BETWEEN (
												SELECT
													MAX(fecha) + INTERVAL \'1 day\'
												FROM
													historico_inventario
												WHERE
													num_cia = inv.num_cia
													AND codmp = inv.codmp
											) AND now()::date
									), NULL)
										AS fecha,
									(SELECT id FROM entrada_mp WHERE num_cia = inv.num_cia AND codmp = inv.codmp AND fecha IS NOT NULL ORDER BY fecha DESC LIMIT 1) AS compra_id
								FROM
									inventario_virtual inv
									LEFT JOIN catalogo_companias cc
										USING (num_cia)
									LEFT JOIN catalogo_mat_primas cmp
										USING (codmp)
								WHERE
									' . implode(' AND ', $condiciones) . '
							) result
					) result
				WHERE
					dias <= ' . $_REQUEST['dias'] . '
					OR (
						existencia > 0
						AND consumo = 0
					)
				ORDER BY
					codmp,
					dias
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ped/ExistenciaEstimadaMenorAlCorteReporte.tpl');
			$tpl->prepare();

			if ($result) {
				$codmp = NULL;

				foreach ($result as $rec) {
					if ($codmp != $rec['codmp']) {
						if ($codmp != NULL) {
							$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');
						}

						$codmp = $rec['codmp'];

						$tpl->newBlock('reporte');
						$tpl->assign('codmp', $codmp);
						$tpl->assign('nombre_mp', $rec['nombre_mp']);
						$tpl->assign('dias', $_REQUEST['dias'] . ' d&iacute;a' . ($_REQUEST['dias'] > 1 ? 's' : ''));
					}

					$tpl->newBlock('row');

					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('nombre_cia', $rec['nombre_cia']);
					$tpl->assign('existencia', $rec['existencia'] != 0 ? number_format($rec['existencia'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('consumo', $rec['consumo'] != 0 ? number_format($rec['consumo'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('fecha', $rec['fecha'] != '' ? $rec['fecha'] : '&nbsp;');
					$tpl->assign('promedio', $rec['promedio'] != 0 ? number_format($rec['promedio'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('dias', $rec['dias'] != '' ? $rec['dias'] : '<span class="red">SIN CONSUMO</span>');
					$tpl->assign('num_pro', $rec['num_pro'] > 0 ? $rec['num_pro'] : '&nbsp;');
					$tpl->assign('nombre_pro', $rec['num_pro'] > 0 ? $rec['nombre_pro'] : '&nbsp;');
					$tpl->assign('ultima_compra', $rec['ultima_compra'] != '' ? $rec['ultima_compra'] : '&nbsp;');
				}
			}

			$tpl->printToScreen();
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ped/ExistenciaEstimadaMenorAlCorte.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

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
	foreach ($admins as $admin) {
		$tpl->newBlock('admin');
		$tpl->assign('value', $admin['value']);
		$tpl->assign('text', $admin['text']);
	}
}

$tpl->printToScreen();
?>
