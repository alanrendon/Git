<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value)
{
	return intval($value, 10);
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

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{

		case 'inicio':
			$tpl = new TemplatePower('plantillas/ban/ChequesConsultaInicio.tpl');
			$tpl->prepare();

			$fecha1 = date('j') <= 5 ? date('01/m/Y', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('01/m/Y');
			$fecha2 = date('j') <= 5 ? date('d/m/Y', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('d/m/Y');

			$tpl->assign('fecha1', $fecha1);
			$tpl->assign('fecha2', $fecha2);

			echo $tpl->getOutputContent();

			break;

		case 'consultar':
			$condiciones = array();

			if ( ! in_array($_SESSION['iduser'], array(1, 4, 2)))
			{
				if ($_SESSION['tipo_usuario'] == 2)
				{
					$condiciones[] = 'c.num_cia BETWEEN 900 AND 998';
				}
				else
				{
					$condiciones[] = 'c.num_cia BETWEEN 1 AND 899';
				}
			}

			/*
			@ Intervalo de compañías
			*/
			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '')
			{
				$cias = array();

				$pieces = explode(',', $_REQUEST['cias']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else
					{
						$cias[] = $piece;
					}
				}

				if (count($cias) > 0)
				{
					$condiciones[] = 'c.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			/*
			@ Intervalo de proveedores
			*/
			if (isset($_REQUEST['pros']) && trim($_REQUEST['pros']) != '')
			{
				$pros = array();

				$pieces = explode(',', $_REQUEST['pros']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$pros[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else
					{
						$pros[] = $piece;
					}
				}

				if (count($pros) > 0)
				{
					$condiciones[] = 'c.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			/*
			@ Intervalo de folios
			*/
			if (isset($_REQUEST['folios']) && trim($_REQUEST['folios']) != '')
			{
				$folios = array();

				$pieces = explode(',', $_REQUEST['folios']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$folios[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else
					{
						$folios[] = $piece;
					}
				}

				if (count($folios) > 0)
				{
					$condiciones[] = 'c.folio IN (' . implode(', ', $folios) . ')';
				}
			}

			/*
			@ Intervalo de gastos
			*/
			if (isset($_REQUEST['gastos']) && trim($_REQUEST['gastos']) != '')
			{
				$gastos = array();

				$pieces = explode(',', $_REQUEST['gastos']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$gastos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else
					{
						$gastos[] = $piece;
					}
				}

				if (count($gastos) > 0)
				{
					$condiciones[] = 'c.codgastos IN (' . implode(', ', $gastos) . ')';
				}
			}

			/*
			@ Intervalo de gastos a omitir
			*/
			if (isset($_REQUEST['omitir_gastos']) && trim($_REQUEST['omitir_gastos']) != '')
			{
				$gastos = array();

				$pieces = explode(',', $_REQUEST['omitir_gastos']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$gastos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else
					{
						$gastos[] = $piece;
					}
				}

				if (count($gastos) > 0)
				{
					$condiciones[] = 'c.codgastos NOT IN (' . implode(', ', $gastos) . ')';
				}
			}

			if (isset($_REQUEST['importes']) && trim($_REQUEST['importes']) != '')
			{
				$importes = array();
				$rangos = array();

				$pieces = explode(',', $_REQUEST['importes']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$rangos[] =  'c.importe BETWEEN ' . $exp[0] . ' AND ' . $exp[1];
					}
					else
					{
						$importes[] = $piece;
					}
				}

				$filtros = array();

				if ($importes)
				{
					$filtros[] = 'c.importe IN (' . implode(', ', $importes) . ')';
				}

				if ($rangos)
				{
					$filtros[] = implode(' OR ', $rangos);
				}

				if ($filtros)
				{
					$condiciones[] = '(' . implode(' OR ', $filtros) . ')';
				}
			}

			/*
			@ Banco
			*/
			if (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0)
			{
				$condiciones[] = 'c.cuenta = ' . $_REQUEST['banco'];
			}

			/*
			@ Periodo
			*/
			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') || (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != ''))
			{
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') && (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != ''))
				{
					$condiciones[] = 'c.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				}
				else if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != ''))
				{
					$condiciones[] = 'c.fecha >= \'' . $_REQUEST['fecha1'] . '\'';
				}
				else if ((isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != ''))
				{
					$condiciones[] = 'c.fecha = \'' . $_REQUEST['fecha2'] . '\'';
				}
			}

			/*
			@ Cobrado
			*/
			if ((isset($_REQUEST['cobrado1']) && $_REQUEST['cobrado1'] != '') || (isset($_REQUEST['cobrado2']) && $_REQUEST['cobrado2'] != ''))
			{
				if ((isset($_REQUEST['cobrado1']) && $_REQUEST['cobrado1'] != '') && (isset($_REQUEST['cobrado2']) && $_REQUEST['cobrado2'] != ''))
				{
					$condiciones[] = 'ec.fecha_con BETWEEN \'' . $_REQUEST['cobrado1'] . '\' AND \'' . $_REQUEST['cobrado2'] . '\'';
				}
				else if ((isset($_REQUEST['cobrado1']) && $_REQUEST['cobrado1'] != ''))
				{
					$condiciones[] = 'ec.fecha_con >= \'' . $_REQUEST['cobrado1'] . '\'';
				}
				else if ((isset($_REQUEST['cobrado2']) && $_REQUEST['cobrado2'] != ''))
				{
					$condiciones[] = 'ec.fecha_con = \'' . $_REQUEST['cobrado2'] . '\'';
				}
			}

			/*
			@ Status
			*/
			if ( ! isset($_REQUEST['pendientes']))
			{
				$condiciones[] = '(ec.fecha_con IS NOT NULL OR c.fecha_cancelacion IS NOT NULL)';
			}

			if ( ! isset($_REQUEST['cobrados']))
			{
				$condiciones[] = '(ec.fecha_con IS NULL OR c.fecha_cancelacion IS NOT NULL)';
			}

			if ( ! isset($_REQUEST['cancelados']))
			{
				$condiciones[] = 'c.fecha_cancelacion IS NULL';
			}

			/*
			@ Tipo
			*/
			if ( ! isset($_REQUEST['otros']))
			{
				$condiciones[] = 'c.cod_mov IN (5, 41)';
			}

			if ( ! isset($_REQUEST['cheques']))
			{
				$condiciones[] = 'c.cod_mov <> 5';
			}

			if ( ! isset($_REQUEST['transferencias']))
			{
				$condiciones[] = 'c.cod_mov <> 41';
			}

			/*
			@ Concepto
			*/
			if (isset($_REQUEST['concepto']) && $_REQUEST['concepto'] != '')
			{
				$condiciones[] = 'c.concepto LIKE \'%' . $_REQUEST['concepto'] . '%\'';
			}

			/*
			@ Solo importes mayores a cero
			*/
			$condiciones[] = 'c.importe >= 0';

			$sql = "SELECT
				c.id AS id_cheque,
				ec.id AS id_ec,
				c.num_cia,
				cc.nombre_corto AS nombre_cia,
				c.num_proveedor AS num_pro,
				cp.nombre AS nombre_pro,
				a_nombre AS beneficiario,
				c.cuenta AS banco,
				CASE
					WHEN c.cuenta = 1 THEN
						cc.clabe_cuenta
					WHEN c.cuenta = 2 THEN
						cc.clabe_cuenta2
					ELSE
						NULL
				END AS cuenta,
				c.cod_mov,
				c.folio,
				c.fecha,
				ec.fecha_con AS cobrado,
				c.fecha_cancelacion AS cancelado,
				COALESCE((
					SELECT
						descripcion
					FROM
						facturas_pagadas
					WHERE
						num_cia = c.num_cia
						AND cuenta = c.cuenta
						AND folio_cheque = c.folio
					LIMIT 1
				), c.concepto) AS concepto,
				c.codgastos AS gasto,
				cg.descripcion AS nombre_gasto,
				c.importe,
				c.tscan,
				CONCAT_WS(' ', auth.nombre, auth.apellido) AS user_can,
				CASE
					WHEN c.fecha_cancelacion IS NULL AND ec.fecha_con IS NOT NULL THEN
						(
							SELECT
								id
							FROM
								estado_cuenta
							WHERE
								num_cia = c.num_cia
								AND importe = c.importe
								AND fecha_con >= ec.fecha_con
								AND tipo_mov = FALSE
								AND cod_mov IN (24, 25, 49)
							ORDER BY
								id ASC
							LIMIT 1
						)
					ELSE
						NULL
				END AS id_deposito_ec,
				CASE
					WHEN c.fecha_cancelacion IS NULL AND ec.fecha_con IS NOT NULL THEN
						(
							CASE
								WHEN c.cuenta = 1 THEN
									(
										SELECT
											id
										FROM
											mov_banorte
										WHERE
											num_cia = c.num_cia
											AND importe = c.importe
											AND fecha >= ec.fecha_con
											AND tipo_mov = FALSE
											AND fecha_con IS NULL
										ORDER BY
											id
										LIMIT
											1
									)
								WHEN c.cuenta = 2 THEN
									(
										SELECT
											id
										FROM
											mov_santander
										WHERE
											num_cia = c.num_cia
											AND importe = c.importe
											AND fecha >= ec.fecha_con
											AND tipo_mov = FALSE
											AND fecha_con IS NULL
										ORDER BY
											id
										LIMIT
											1
									)
								ELSE
									NULL
							END
						)
					ELSE
						NULL
				END AS id_mov_banco,
				CASE
					WHEN c.fecha_cancelacion IS NULL THEN
						FALSE
					WHEN c.fecha_cancelacion IS NOT NULL AND (
						SELECT
							id
						FROM
							facturas_pagadas
						WHERE
							num_cia = c.num_cia
							AND cuenta = c.cuenta
							AND folio_cheque = c.folio
							AND fecha_cheque = c.fecha
						LIMIT 1
					) IS NOT NULL THEN
						TRUE
					ELSE
						FALSE
				END AS status_pasivo
			FROM
				cheques c
				LEFT JOIN estado_cuenta ec USING (num_cia, cuenta, folio, fecha)
				LEFT JOIN catalogo_proveedores cp ON (cp.num_proveedor = c.num_proveedor)
				LEFT JOIN catalogo_companias cc ON (cc.num_cia = c.num_cia)
				LEFT JOIN catalogo_gastos cg USING (codgastos)
				LEFT JOIN auth ON (auth.iduser = c.iduser_can)
			WHERE
				" . implode(' AND ', $condiciones) . "
			ORDER BY
				c.num_cia,
				c.cuenta,
				c.folio";

			$query = $db->query($sql);

			if ($query)
			{
				$tpl = new TemplatePower('plantillas/ban/ChequesConsultaResultado.tpl');
				$tpl->prepare();

				$total = 0;

				$num_cia = NULL;

				foreach ($query as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$tpl->newBlock('cia');

						$tpl->assign('num_cia', $row['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));

						$banco = NULL;

						$total_cia = 0;
					}

					if ($banco != $row['banco'])
					{
						$banco = $row['banco'];

						$tpl->newBlock('banco');

						$tpl->assign('logo_banco', $row['banco'] > 0 ? ('<img src="/lecaroz/imagenes/' . ($row['banco'] == 1 ? 'Banorte' : 'Santander') . '16x16.png" width="16" height="16" />') : '&nbsp;');
						$tpl->assign('nombre_banco', $row['banco'] > 0 ? ($row['banco'] == 1 ? 'BANORTE' : 'SANTANDER') : '&nbsp;');
						$tpl->assign('num_cia', $row['num_cia']);
						$tpl->assign('banco', $row['banco']);

						$tpl->assign('cuenta', $row['cuenta']);

						$total_banco = 0;
					}

					$tpl->newBlock('row');

					$data = json_encode(array(
						'id_cheque'			=> intval($row['id_cheque']),
						'id_ec'				=> intval($row['id_ec']),
						'id_deposito_ec'	=> $row['id_deposito_ec'] > 0 ? intval($row['id_deposito_ec']) : NULL,
						'id_mov_banco'		=> $row['id_mov_banco'] > 0 ? intval($row['id_mov_banco']) : NULL,
						'cobrado'			=> $row['cobrado'] != '' ? TRUE : FALSE
					));

					$tpl->assign('id', $row['id_cheque']);
					$tpl->assign('data', $data);
					$tpl->assign('num_cia', $row['num_cia']);
					$tpl->assign('banco', $row['banco']);
					$tpl->assign('folio', $row['folio'] > 0 ? '<span style="color:' . ($row['cancelado'] == '' ? ($row['cod_mov'] == 41 ? '#063' : '#00C') : '#C00') . '">' . $row['folio'] . '</span>' : '&nbsp;');
					$tpl->assign('fecha', $row['fecha'] != '' ? $row['fecha'] : '&nbsp;');
					$tpl->assign('cobrado', $row['cobrado'] != '' ? $row['cobrado'] : '&nbsp;');
					$tpl->assign('cancelado', $row['cancelado'] != '' ? $row['cancelado'] : '&nbsp;');
					$tpl->assign('num_pro', $row['num_pro']);
					$tpl->assign('nombre_pro', utf8_encode($row['nombre_pro']));
					$tpl->assign('concepto', trim($row['concepto']) != '' ? trim(utf8_encode($row['concepto'])) : '&nbsp;');
					$tpl->assign('gasto', $row['gasto']);
					$tpl->assign('nombre_gasto', $row['nombre_gasto']);
					$tpl->assign('importe', $row['importe'] != 0 ? number_format($row['importe'], 2) : '&nbsp;');

					$tpl->assign('class_icono_print', ' class="icono"');

					if ($row['cancelado'] != '' || ($row['cobrado'] != '' && ! ($row['id_deposito_ec'] > 0 || $row['id_mov_banco'] > 0)))
					{
						$tpl->assign('checkbox_disabled', ' disabled="disabled"');
						$tpl->assign('cancel_disabled', '_gray');
						// $tpl->assign('print_disabled', '_gray');
						$tpl->assign('class_icono_cancel', ' class="icono_disabled"');
						// $tpl->assign('class_icono_print', ' class="icono_disabled"');
					}
					else if ($row['cobrado'] != '' && ($row['id_deposito_ec'] > 0 || $row['id_mov_banco'] > 0))
					{
						// $tpl->assign('print_disabled', '_gray');
						$tpl->assign('class_icono_cancel', ' class="icono"');
						// $tpl->assign('class_icono_print', ' class="icono_disabled"');
					}
					else
					{
						$tpl->assign('class_icono_cancel', ' class="icono"');
						// $tpl->assign('class_icono_print', ' class="icono"');
					}

					if ($row['cancelado'] != '' && $row['status_pasivo'] == 't')
					{
						$tpl->assign('class_icono_pasivo', ' class="icono"');
					}
					else
					{
						$tpl->assign('class_icono_pasivo', ' class="icono_disabled"');
						$tpl->assign('pasivo_disabled', '_gray"');
					}

					$tpl->assign('class_cancelado', $row['cancelado'] != '' ? ' class="cancelado"' : '');

					$total += $row['cancelado'] == '' || isset($_REQUEST['sumar_cancelados']) ? $row['importe'] : 0;
					$total_cia += $row['cancelado'] == '' || isset($_REQUEST['sumar_cancelados']) ? $row['importe'] : 0;
					$total_banco += $row['cancelado'] == '' || isset($_REQUEST['sumar_cancelados']) ? $row['importe'] : 0;

					$tpl->assign('_ROOT.total', number_format($total, 2));
					$tpl->assign('cia.total_cia', number_format($total_cia, 2));
					$tpl->assign('banco.total_banco', number_format($total_banco, 2));
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'cancelar':
			$data = json_decode($_REQUEST['data']);

			// Obtener datos del cheque
			$result = $db->query("SELECT * FROM cheques WHERE id = {$data->id_cheque}");
			$cheque = $result[0];

			list($cheque_dia, $cheque_mes, $cheque_anio) = array_map('toInt', explode('/', $cheque['fecha']));
			list($cancelacion_dia, $cancelacion_mes, $cancelacion_anio) = array_map('toInt', explode('/', $_REQUEST['fecha_cancelacion']));

			$sql = '';

			// Si el cheque esta cobrado generar movimiento inverso para poder liberar el pago y poder cancelarlo
			if ($data->cobrado)
			{
				$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, cuenta, tipo_mov, cod_mov, concepto, importe, iduser, timestamp, tipo_con, idins, tsins, idmod, tsmod) SELECT num_cia, fecha, fecha_con, cuenta, TRUE, 21, 'CANCELACION CARGO (FOLIO ' || folio || ')', importe, {$_SESSION['iduser']}, NOW(), 7, {$_SESSION['iduser']}, NOW(), {$_SESSION['iduser']}, NOW() FROM estado_cuenta WHERE id = {$data->id_ec};\n";
				$sql .= "UPDATE estado_cuenta SET fecha_con = NULL, tipo_con = 0 WHERE id = {$data->id_ec};\n";
				$sql .= "UPDATE saldos SET saldo_libros = saldo_libros - {$cheque['importe']} WHERE num_cia = {$cheque['num_cia']} AND cuenta = {$cheque['cuenta']};\n";

				if ($data->id_mov_banco > 0)
				{
					$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, cuenta, tipo_mov, cod_mov, concepto, importe, iduser, timestamp, tipo_con, idins, tsins, idmod, tsmod) SELECT num_cia, fecha, fecha, {$cheque['cuenta']}, FALSE, " . ($cheque['cod_mov'] == 41 ? 49 : 25) . ", concepto || ' (FOLIO ' || {$cheque['folio']} || ')', importe, {$_SESSION['iduser']}, NOW(), 7, {$_SESSION['iduser']}, NOW(), {$_SESSION['iduser']}, NOW() FROM " . ($cheque['cuenta'] == 1 ? 'mov_banorte' : 'mov_santander') . " WHERE id = {$data->id_mov_banco};\n";
					$sql .= "UPDATE saldos SET saldo_libros = saldo_libros + {$cheque['importe']}, saldo_bancos = saldo_bancos + {$cheque['importe']} WHERE num_cia = {$cheque['num_cia']} AND cuenta = {$cheque['cuenta']};\n";
					$sql .= "UPDATE " . ($cheque['cuenta'] == 1 ? 'mov_banorte' : 'mov_santander') . " SET fecha_con = NOW()::DATE, cod_mov = " . ($cheque['cod_mov'] == 41 ? 49 : 25) . ", iduser = {$_SESSION['iduser']}, timestamp = NOW() WHERE id = {$data->id_mov_banco};\n";
				}
				else if ($data->id_deposito_ec > 0)
				{
					$sql .= "UPDATE estado_cuenta SET concepto = concepto || ' (FOLIO ' || {$cheque['folio']} || ')' WHERE id = {$data->id_deposito_ec};\n";
				}
			}

			if ( ! $data->cobrado || ($data->cobrado && $_REQUEST['inversa'] == 0))
			{
				if (mktime(0, 0, 0, $cheque_mes, $cheque_dia, $cheque_anio) < mktime(0, 0, 0, $cancelacion_mes, 1, $cancelacion_anio))
				{
					$sql .= "INSERT INTO movimiento_gastos (codgastos, num_cia, fecha, importe, concepto, captura, folio, cuenta) SELECT codgastos, num_cia, '{$_REQUEST['fecha_cancelacion']}', importe * -1, concepto, TRUE, folio, cuenta FROM cheques WHERE id = {$data->id_cheque};\n";
					$sql .= "INSERT INTO cheques (cod_mov, num_proveedor, num_cia, fecha, folio, importe, iduser, a_nombre, imp, concepto, facturas, codgastos, cuenta) SELECT cod_mov, num_proveedor, num_cia, '{$_REQUEST['fecha_cancelacion']}', folio, importe * -1, iduser, a_nombre, imp, concepto, facturas, codgastos, cuenta FROM cheques WHERE id = {$data->id_cheque};\n";
					$sql .= "INSERT INTO pagos_otras_cias (num_cia, cuenta, folio, fecha, num_cia_aplica) SELECT num_cia, cuenta, folio, '{$_REQUEST['fecha_cancelacion']}', num_cia_aplica FROM cheques c LEFT JOIN pagos_otras_cias poc USING (num_cia, cuenta, folio, fecha) WHERE c.id = {$data->id_cheque} AND num_cia_aplica IS NOT NULL;\n";
				}
				else
				{
					$sql .= "DELETE FROM movimiento_gastos WHERE num_cia = {$cheque['num_cia']} AND folio = {$cheque['folio']} AND fecha = '{$cheque['fecha']}';\n";
				}

				$sql .= "DELETE FROM estado_cuenta WHERE id = {$data->id_ec};\n";
				$sql .= "UPDATE cheques SET fecha_cancelacion = '{$_REQUEST['fecha_cancelacion']}', tscan = NOW(), iduser_can = {$_SESSION['iduser']}, site = TRUE, tssite = NULL WHERE id = {$data->id_cheque};\n";
				$sql .= "UPDATE transferencias_electronicas SET status = 2 WHERE num_cia = {$cheque['num_cia']} AND folio = {$cheque['folio']} AND cuenta = {$cheque['cuenta']} AND fecha_gen = '{$cheque['fecha']}';\n";
				$sql .= "UPDATE saldos SET saldo_libros = saldo_libros + {$cheque['importe']} WHERE num_cia = {$cheque['num_cia']} AND cuenta = {$cheque['cuenta']};\n";

				if ($_REQUEST['devolver_facturas'] == 1)
				{
					if ($cheque['num_cia'] < 900)
					{
						$sql .= "INSERT INTO pasivo_proveedores (
							num_cia,
							num_fact,
							total,
							descripcion,
							fecha,
							num_proveedor,
							codgastos,
							copia_fac
						)
						SELECT
							COALESCE(poc.num_cia_aplica, fp.num_cia),
							fp.num_fact,
							fp.total,
							fp.descripcion,
							fp.fecha,
							fp.num_proveedor,
							fp.codgastos,
							TRUE
						FROM
							facturas_pagadas fp
							LEFT JOIN pagos_otras_cias poc ON (
								poc.num_cia = fp.num_cia
								AND poc.cuenta = fp.cuenta
								AND poc.folio = fp.folio_cheque
								AND poc.fecha = fp.fecha_cheque
							)
						WHERE
							fp.num_cia = {$cheque['num_cia']}
							AND fp.folio_cheque = {$cheque['folio']}
							AND fp.cuenta = {$cheque['cuenta']}
							AND fp.fecha_cheque = '{$cheque['fecha']}';\n";

						$sql .= "DELETE
						FROM
							facturas_pagadas
						WHERE
							num_cia = {$cheque['num_cia']}
							AND folio_cheque = {$cheque['folio']}
							AND cuenta = {$cheque['cuenta']}
							AND fecha_cheque = '{$cheque['fecha']}';\n";
					}
					else
					{
						$sql .= "UPDATE facturas_zap SET folio = NULL, cuenta = NULL, tspago = NULL WHERE num_cia = {$cheque['num_cia']} AND folio = {$cheque['folio']} AND cuenta = {$cheque['cuenta']};\n";
						$sql .= "UPDATE devoluciones_zap SET folio_cheque = NULL, cuenta = NULL, imp = 'FALSE', folio = NULL, num_cia_cheque = NULL, num_fact = NULL WHERE num_cia_cheque = {$cheque['num_cia']} AND folio_cheque = {$cheque['folio']} AND cuenta = {$cheque['cuenta']};\n";
						$sql .= "UPDATE notas_credito_zap SET status = 1, num_cia_apl = NULL, folio_cheque = NULL, cuenta = NULL, num_fact = NULL WHERE num_cia = {$cheque['num_cia']} AND folio_cheque = {$cheque['folio']} AND cuenta = {$cheque['cuenta']};\n";
					}
				}
			}

			$db->query($sql);

			break;

		case 'cancelar_seleccion':
			list($cancelacion_dia, $cancelacion_mes, $cancelacion_anio) = array_map('toInt', explode('/', $_REQUEST['fecha_cancelacion']));

			foreach ($_REQUEST['data'] as $json_data)
			{
				$data = json_decode($json_data);

				// Obtener datos del cheque
				$result = $db->query("SELECT * FROM cheques WHERE id = {$data->id_cheque}");
				$cheque = $result[0];

				list($cheque_dia, $cheque_mes, $cheque_anio) = array_map('toInt', explode('/', $cheque['fecha']));

				$sql = '';

				// Si el cheque esta cobrado generar movimiento inverso para poder liberar el pago y poder cancelarlo
				if ($data->cobrado)
				{
					$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, cuenta, tipo_mov, cod_mov, concepto, importe, iduser, timestamp, tipo_con, idins, tsins, idmod, tsmod) SELECT num_cia, fecha, fecha_con, cuenta, TRUE, 21, 'CANCELACION CARGO (FOLIO ' || folio || ')', importe, {$_SESSION['iduser']}, NOW(), 7, {$_SESSION['iduser']}, NOW(), {$_SESSION['iduser']}, NOW() FROM estado_cuenta WHERE id = {$data->id_ec};\n";
					$sql .= "UPDATE estado_cuenta SET fecha_con = NULL, tipo_con = 0 WHERE id = {$data->id_ec};\n";
					$sql .= "UPDATE saldos SET saldo_libros = saldo_libros - {$cheque['importe']} WHERE num_cia = {$cheque['num_cia']} AND cuenta = {$cheque['cuenta']};\n";

					if ($data->id_mov_banco > 0)
					{
						$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, cuenta, tipo_mov, cod_mov, concepto, importe, iduser, timestamp, tipo_con, idins, tsins, idmod, tsmod) SELECT num_cia, fecha, fecha, {$cheque['cuenta']}, FALSE, " . ($cheque['cod_mov'] == 41 ? 49 : 25) . ", concepto || ' (FOLIO ' || {$cheque['folio']} || ')', importe, {$_SESSION['iduser']}, NOW(), 7, {$_SESSION['iduser']}, NOW(), {$_SESSION['iduser']}, NOW() FROM " . ($cheque['cuenta'] == 1 ? 'mov_banorte' : 'mov_santander') . " WHERE id = {$data->id_mov_banco};\n";
						$sql .= "UPDATE saldos SET saldo_libros = saldo_libros + {$cheque['importe']}, saldo_bancos = saldo_bancos + {$cheque['importe']} WHERE num_cia = {$cheque['num_cia']} AND cuenta = {$cheque['cuenta']};\n";
						$sql .= "UPDATE " . ($cheque['cuenta'] == 1 ? 'mov_banorte' : 'mov_santander') . " SET fecha_con = NOW()::DATE, cod_mov = " . ($cheque['cod_mov'] == 41 ? 49 : 25) . ", iduser = {$_SESSION['iduser']}, timestamp = NOW() WHERE id = {$data->id_mov_banco};\n";
					}
					else if ($data->id_deposito_ec > 0)
					{
						$sql .= "UPDATE estado_cuenta SET concepto = concepto || ' (FOLIO ' || {$cheque['folio']} || ')' WHERE id = {$data->id_deposito_ec};\n";
					}
				}

				if ( ! $data->cobrado || ($data->cobrado && $_REQUEST['inversa'] == 0))
				{
					if (mktime(0, 0, 0, $cheque_mes, $cheque_dia, $cheque_anio) < mktime(0, 0, 0, $cancelacion_mes, 1, $cancelacion_anio))
					{
						$sql .= "INSERT INTO movimiento_gastos (codgastos, num_cia, fecha, importe, concepto, captura, folio, cuenta) SELECT codgastos, num_cia, '{$_REQUEST['fecha_cancelacion']}', importe * -1, concepto, TRUE, folio, cuenta FROM cheques WHERE id = {$data->id_cheque};\n";
						$sql .= "INSERT INTO cheques (cod_mov, num_proveedor, num_cia, fecha, folio, importe, iduser, a_nombre, imp, concepto, facturas, codgastos, cuenta) SELECT cod_mov, num_proveedor, num_cia, '{$_REQUEST['fecha_cancelacion']}', folio, importe * -1, iduser, a_nombre, imp, concepto, facturas, codgastos, cuenta FROM cheques WHERE id = {$data->id_cheque};\n";
						$sql .= "INSERT INTO pagos_otras_cias (num_cia, cuenta, folio, fecha, num_cia_aplica) SELECT num_cia, cuenta, folio, '{$_REQUEST['fecha_cancelacion']}', num_cia_aplica FROM cheques c LEFT JOIN pagos_otras_cias poc USING (num_cia, cuenta, folio, fecha) WHERE c.id = {$data->id_cheque} AND num_cia_aplica IS NOT NULL;\n";
					}
					else
					{
						$sql .= "DELETE FROM movimiento_gastos WHERE num_cia = {$cheque['num_cia']} AND folio = {$cheque['folio']} AND fecha = '{$cheque['fecha']}';\n";
					}

					$sql .= "DELETE FROM estado_cuenta WHERE id = {$data->id_ec};\n";
					$sql .= "UPDATE cheques SET fecha_cancelacion = '{$_REQUEST['fecha_cancelacion']}', tscan = NOW(), iduser_can = {$_SESSION['iduser']} WHERE id = {$data->id_cheque};\n";
					$sql .= "UPDATE transferencias_electronicas SET status = 2 WHERE num_cia = {$cheque['num_cia']} AND folio = {$cheque['folio']} AND cuenta = {$cheque['cuenta']} AND fecha_gen = '{$cheque['fecha']}';\n";
					$sql .= "UPDATE saldos SET saldo_libros = saldo_libros + {$cheque['importe']} WHERE num_cia = {$cheque['num_cia']} AND cuenta = {$cheque['cuenta']};\n";

					if ($_REQUEST['devolver_facturas'] == 1)
					{
						if ($cheque['num_cia'] < 900)
						{
							$sql .= "INSERT INTO pasivo_proveedores (
								num_cia,
								num_fact,
								total,
								descripcion,
								fecha,
								num_proveedor,
								codgastos,
								copia_fac
							)
							SELECT
								COALESCE(poc.num_cia_aplica, fp.num_cia),
								fp.num_fact,
								fp.total,
								fp.descripcion,
								fp.fecha,
								fp.num_proveedor,
								fp.codgastos,
								TRUE
							FROM
								facturas_pagadas fp
								LEFT JOIN pagos_otras_cias poc ON (
									poc.num_cia = fp.num_cia
									AND poc.cuenta = fp.cuenta
									AND poc.folio = fp.folio_cheque
									AND poc.fecha = fp.fecha_cheque
								)
							WHERE
								fp.num_cia = {$cheque['num_cia']}
								AND fp.folio_cheque = {$cheque['folio']}
								AND fp.cuenta = {$cheque['cuenta']}
								AND fp.fecha_cheque = '{$cheque['fecha']}';\n";

							$sql .= "DELETE
							FROM
								facturas_pagadas
							WHERE
								num_cia = {$cheque['num_cia']}
								AND folio_cheque = {$cheque['folio']}
								AND cuenta = {$cheque['cuenta']}
								AND fecha_cheque = '{$cheque['fecha']}';\n";
						}
						else
						{
							$sql .= "UPDATE facturas_zap SET folio = NULL, cuenta = NULL, tspago = NULL WHERE num_cia = {$cheque['num_cia']} AND folio = {$cheque['folio']} AND cuenta = {$cheque['cuenta']};\n";
							$sql .= "UPDATE devoluciones_zap SET folio_cheque = NULL, cuenta = NULL, imp = 'FALSE', folio = NULL, num_cia_cheque = NULL, num_fact = NULL WHERE num_cia_cheque = {$cheque['num_cia']} AND folio_cheque = {$cheque['folio']} AND cuenta = {$cheque['cuenta']};\n";
							$sql .= "UPDATE notas_credito_zap SET status = 1, num_cia_apl = NULL, folio_cheque = NULL, cuenta = NULL, num_fact = NULL WHERE num_cia = {$cheque['num_cia']} AND folio_cheque = {$cheque['folio']} AND cuenta = {$cheque['cuenta']};\n";
						}
					}
				}

				$db->query($sql);
			}

			break;

		case 'regresar_pasivo':
			$pago = $db->query("SELECT num_cia, cuenta, folio, fecha FROM cheques WHERE id = {$_REQUEST['id']}");

			$sql = "INSERT INTO pasivo_proveedores (
					num_cia,
					num_fact,
					total,
					descripcion,
					fecha,
					num_proveedor,
					codgastos,
					copia_fac
				)
				SELECT
					COALESCE(poc.num_cia_aplica, fp.num_cia),
					fp.num_fact,
					fp.total,
					fp.descripcion,
					fp.fecha,
					fp.num_proveedor,
					fp.codgastos,
					TRUE
				FROM
					facturas_pagadas fp
					LEFT JOIN pagos_otras_cias poc ON (
						poc.num_cia = fp.num_cia
						AND poc.cuenta = fp.cuenta
						AND poc.folio = fp.folio_cheque
						AND poc.fecha = fp.fecha_cheque
					)
				WHERE
					fp.num_cia = {$pago[0]['num_cia']}
					AND fp.cuenta = {$pago[0]['cuenta']}
					AND fp.folio_cheque = {$pago[0]['folio']}
					AND fp.fecha_cheque = '{$pago[0]['fecha']}';\n";

			$sql .= "DELETE
			FROM
				facturas_pagadas
			WHERE
				num_cia = {$pago[0]['num_cia']}
				AND cuenta = {$pago[0]['cuenta']}
				AND folio_cheque = {$pago[0]['folio']}
				AND fecha_cheque = '{$pago[0]['fecha']}';";

			$db->query($sql);

			break;

		case 'imprimir':
			$db->query("UPDATE cheques SET imp = FALSE WHERE id = {$_REQUEST['id']}");

			break;

		case 'imprimir_seleccion':
			$ids = array();

			foreach ($_REQUEST['data'] as $json_data)
			{
				$data = json_decode($json_data);

				if ( ! $data->cobrado)
				{
					$ids[] = $data->id_cheque;
				}
			}

			$db->query("UPDATE cheques SET imp = FALSE, poliza = TRUE WHERE id IN (" . implode(', ', $ids) . ")");

			break;

		case 'cambiar_fecha_seleccion':
			foreach ($_REQUEST['data'] as $json_data)
			{
				$data = json_decode($json_data);

				$sql = "UPDATE estado_cuenta
				SET fecha = '{$_REQUEST['nueva_fecha']}'
				WHERE
					(num_cia, cuenta, folio, fecha) IN (
						SELECT
							num_cia,
							cuenta,
							folio,
							fecha
						FROM
							cheques
						WHERE
							id = {$data->id_cheque}
					);\n";

				$sql .= "UPDATE transferencias_electronicas
				SET fecha_gen = '{$_REQUEST['nueva_fecha']}'
				WHERE
					(num_cia, cuenta, folio, fecha_gen) IN (
						SELECT
							num_cia,
							cuenta,
							folio,
							fecha
						FROM
							cheques
						WHERE
							id = {$data->id_cheque}
					);\n";

				$sql .= "UPDATE folios_cheque
				SET fecha = '{$_REQUEST['nueva_fecha']}'
				WHERE
					(num_cia, cuenta, folio, fecha) IN (
						SELECT
							num_cia,
							cuenta,
							folio,
							fecha
						FROM
							cheques
						WHERE
							id = {$data->id_cheque}
					);\n";

				$sql .= "UPDATE pagos_otras_cias
				SET fecha = '{$_REQUEST['nueva_fecha']}'
				WHERE
					(num_cia, cuenta, folio, fecha) IN (
						SELECT
							num_cia,
							cuenta,
							folio,
							fecha
						FROM
							cheques
						WHERE
							id = {$data->id_cheque}
					);\n";

				$sql .= "UPDATE facturas_pagadas
				SET fecha_cheque = '{$_REQUEST['nueva_fecha']}'
				WHERE
					(num_cia, cuenta, folio_cheque, fecha_cheque) IN (
						SELECT
							num_cia,
							cuenta,
							folio,
							fecha
						FROM
							cheques
						WHERE
							id = {$data->id_cheque}
					);\n";

				$sql .= "UPDATE movimiento_gastos
				SET fecha = '{$_REQUEST['nueva_fecha']}'
				WHERE
					(num_cia, fecha, folio, importe) IN (
						SELECT
							num_cia,
							fecha,
							folio,
							importe
						FROM
							cheques
						WHERE
							id = {$data->id_cheque}
					);\n";

				$sql .= "UPDATE cheques
				SET fecha = '{$_REQUEST['nueva_fecha']}'
				WHERE
					id = {$data->id_cheque};\n";

				$db->query($sql);
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/ChequesConsulta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha_cancelacion', date('d/m/Y'));

$tpl->printToScreen();
