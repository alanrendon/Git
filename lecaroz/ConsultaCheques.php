<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/class.auxinv.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'generar':
			$condiciones = array();

			if (!in_array($_SESSION['iduser'], array(1, 4, 2))) {
				if ($_SESSION['tipo_usuario'] == 2) {
					$condiciones[] = 'c.num_cia BETWEEN 900 AND 998';
				}
				else {
					$condiciones[] = 'c.num_cia BETWEEN 1 AND 899';
				}
			}

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
					$condiciones[] = 'c.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			/*
			@ Intervalo de proveedores
			*/
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
					$condiciones[] = 'c.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			/*
			@ Intervalo de folios
			*/
			if (isset($_REQUEST['folios']) && trim($_REQUEST['folios']) != '') {
				$folios = array();

				$pieces = explode(',', $_REQUEST['folios']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$folios[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$folios[] = $piece;
					}
				}

				if (count($folios) > 0) {
					$condiciones[] = 'c.folio IN (' . implode(', ', $folios) . ')';
				}
			}

			/*
			@ Intervalo de gastos
			*/
			if (isset($_REQUEST['gastos']) && trim($_REQUEST['gastos']) != '') {
				$gastos = array();

				$pieces = explode(',', $_REQUEST['gastos']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$gastos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$gastos[] = $piece;
					}
				}

				if (count($gastos) > 0) {
					$condiciones[] = 'c.codgastos IN (' . implode(', ', $gastos) . ')';
				}
			}

			if (isset($_REQUEST['importe']) && $_REQUEST['importe'] > 0) {
				$condiciones[] = 'c.importe = ' . get_val($_REQUEST['importe']);
			}

			/*
			@ Banco
			*/
			if (isset($_REQUEST['cuenta']) && $_REQUEST['cuenta'] > 0) {
				$condiciones[] = 'c.cuenta = ' . $_REQUEST['cuenta'];
			}

			/*
			@ Periodo
			*/
			if (isset($_REQUEST['fecha1']) && isset($_REQUEST['fecha2'])) {
				$condiciones[] = 'c.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
			}

			/*
			@ Cobrado
			*/
			if (isset($_REQUEST['fecha_con1']) || isset($_REQUEST['fecha_con2'])) {
				if (isset($_REQUEST['fecha_con1']) && isset($_REQUEST['fecha_con2'])) {
					$condiciones[] = 'ec.fecha_con BETWEEN \'' . $_REQUEST['fecha_con1'] . '\' AND \'' . $_REQUEST['fecha_con2'] . '\'';
				}
				else if (isset($_REQUEST['fecha_con1'])) {
					$condiciones[] = 'ec.fecha_con >= \'' . $_REQUEST['fecha_con1'] . '\'';
				}
				else if (isset($_REQUEST['fecha_con2'])) {
					$condiciones[] = 'ec.fecha_con = \'' . $_REQUEST['fecha_con2'] . '\'';
				}
			}

			/*
			@ Status
			*/
			if (!isset($_REQUEST['pendientes'])) {
				$condiciones[] = '(ec.fecha_con IS NOT NULL OR c.fecha_cancelacion IS NOT NULL)';
			}

			if (!isset($_REQUEST['cobrados'])) {
				$condiciones[] = '(ec.fecha_con IS NULL OR c.fecha_cancelacion IS NOT NULL)';
			}

			if (!isset($_REQUEST['cancelados'])) {
				$condiciones[] = 'c.fecha_cancelacion IS NULL';
			}

			/*
			@ Tipo
			*/
			if (!isset($_REQUEST['otros'])) {
				$condiciones[] = 'c.cod_mov IN (5, 41)';
			}

			if (!isset($_REQUEST['cheques'])) {
				$condiciones[] = 'c.cod_mov <> 5';
			}

			if (!isset($_REQUEST['transferencias'])) {
				$condiciones[] = 'c.cod_mov <> 41';
			}

			/*
			@ Solo importes mayores a cero
			*/
			$condiciones[] = 'c.importe >= 0';

			$sql = '
				SELECT
					c.num_cia,
					cc.nombre_corto
						AS
							nombre_cia,
					c.num_proveedor
						AS
							num_pro,
					cp.nombre
						AS
							nombre_pro,
					a_nombre
						AS
							beneficiario,
					CASE
						WHEN c.cuenta = 1 THEN
							\'BANORTE\'
						WHEN c.cuenta = 2 THEN
							\'SANTANDER\'
						ELSE
							NULL
					END
						AS
							banco,
					CASE
						WHEN c.cuenta = 1 THEN
							cc.clabe_cuenta
						WHEN c.cuenta = 2 THEN
							cc.clabe_cuenta2
						ELSE
							NULL
					END
						AS
							cuenta,
					CASE
						/*
						@ Cheque
						*/
						WHEN ec.cod_mov = 5 THEN
						/*
						@ Transferencia
						*/
							1
						WHEN ec.cod_mov = 41 THEN
							2
						/*
						@ Otro
						*/
						ELSE
							-1
					END
						AS
							tipo,
					c.folio,
					c.fecha,
					ec.fecha_con
						AS
							cobrado,
					c.fecha_cancelacion
						AS
							cancelado,
					COALESCE((
						SELECT
							descripcion
						FROM
							facturas_pagadas
						WHERE
							num_cia = c.num_cia
							AND cuenta = c.cuenta
							AND folio_cheque = c.folio
						LIMIT
							1
					), c.concepto)
						AS concepto,
					c.codgastos
						AS
							cod,
					cg.descripcion
						AS
							gasto,
					c.importe,
					c.tscan,
					CONCAT_WS(\' \', auth.nombre, auth.apellido)
						AS
							user_can
				FROM
						cheques c
					LEFT JOIN
						estado_cuenta
							ec
								USING
									(
										num_cia,
										cuenta,
										folio,
										fecha
									)
					LEFT JOIN
						catalogo_proveedores
							cp
								ON
									(
										cp.num_proveedor = c.num_proveedor
									)
					LEFT JOIN
						catalogo_companias
							cc
								ON
									(
										cc.num_cia = c.num_cia
									)
					LEFT JOIN
						catalogo_gastos
							cg
								USING
									(
										codgastos
									)
					LEFT JOIN
						auth
							ON
								(
									auth.iduser = c.iduser_can
								)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					c.num_cia,
					c.cuenta,
					c.folio
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ban/ReporteCheques.tpl');
			$tpl->prepare();

			if ($result) {
				$tpl->newBlock('reporte');

				if ($_SESSION['tipo_usuario'] == 2) {
					$tpl->assign('empresa', 'Elite');
				}
				else {
					$tpl->assign('empresa', 'Oficinas Administrativas Mollendo');
				}

				$num_cia = NULL;
				$total_general = 0;
				foreach ($result as $i => $r) {
					if ($num_cia != $r['num_cia']) {
						$num_cia = $r['num_cia'];

						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $r['nombre_cia']);

						$total_cia = 0;

						$banco = NULL;
					}
					if ($banco != $r['banco']) {
						$banco = $r['banco'];

						$tpl->newBlock('banco');
						$tpl->assign('banco', $banco);
						$tpl->assign('cuenta', $r['cuenta']);

						$total_banco = 0;
					}
					$tpl->newBlock('row');
					$tpl->assign('folio', $r['folio']);

					switch ($r['tipo']) {
						case 1:
							$tpl->assign('color', ' blue');
						break;

						case 2:
							$tpl->assign('color', ' green');
						break;

						case -1:
							$tpl->assign('color', ' red');
						break;
					}

					$tpl->assign('fecha', $r['fecha']);
					$tpl->assign('num_pro', $r['num_pro']);
					$tpl->assign('beneficiario', $r['beneficiario']);
					$tpl->assign('concepto', $r['concepto']);
					$tpl->assign('cod', $r['cod']);
					$tpl->assign('gasto', $r['gasto']);
					$tpl->assign('cobrado', $r['cobrado'] != '' ? $r['cobrado'] : '&nbsp;');
					$tpl->assign('cancelado', $r['cancelado'] != '' ? '<a class="info" title="' . htmlentities("Fecha y hora: {$r['tscan']}<br />Usuario: {$r['user_can']}") . '" style="text-decoration:none;color:#c00;">' . $r['cancelado'] . '</a>' : '&nbsp;');
					$tpl->assign('color_importe', $r['cancelado'] != '' ? ' red' : '');
					$tpl->assign('importe', number_format($r['importe'], 2, '.', ','));

					if ($r['cancelado'] == '' || isset($_REQUEST['sumar_cancelados'])) {
						$total_banco += $r['importe'];
						$total_cia += $r['importe'];
						$total_general += $r['importe'];
					}

					$tpl->assign('banco.total', number_format($total_banco, 2, '.', ','));
					$tpl->assign('cia.total', number_format($total_cia, 2, '.', ','));
					$tpl->assign('reporte.total', number_format($total_general, 2, '.', ','));
				}
			}

			$tpl->printToScreen();

		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/ConsultaCheques.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha1', date('d/m/Y', mktime(0, 0, 0, date('n'), 1, date('Y'))));
$tpl->assign('fecha2', date('d/m/Y'));

$tpl->printToScreen();
?>
