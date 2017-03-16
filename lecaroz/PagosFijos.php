<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value) {
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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {

		case 'inicio':
			$tpl = new TemplatePower('plantillas/ban/PagosFijosInicio.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();

			break;

		case 'obtener_cia':
			$condiciones = array();

			$condiciones[] = "cc.num_cia = {$_REQUEST['num_cia']}";

			if ($_SESSION['iduser'] != 1)
			{
				$condiciones[] = 'num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
			}

			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias cc
				WHERE
					' . implode(' AND ', $condiciones) . '
			';

			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode($result[0]['nombre_corto']);
			}

			break;

		case 'obtener_pro':
			$condiciones = array();

			$condiciones[] = "cp.num_proveedor = {$_REQUEST['num_pro']}";

			$sql = '
				SELECT
					nombre
				FROM
					catalogo_proveedores cp
				WHERE
					' . implode(' AND ', $condiciones) . '
			';

			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode($result[0]['nombre']);
			}

			break;

		case 'obtener_gasto':
			$condiciones = array();

			$condiciones[] = "cg.codgastos = {$_REQUEST['cod']}";

			$sql = '
				SELECT
					descripcion
				FROM
					catalogo_gastos cg
				WHERE
					' . implode(' AND ', $condiciones) . '
			';

			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode($result[0]['descripcion']);
			}

			break;

		case 'consultar':
			$condiciones = array();

			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '')
			{
				$cias = array();

				$pieces = explode(',', $_REQUEST['cias']);

				foreach ($pieces as $piece) {
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
					$condiciones[] = 'pc.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['pros']) && trim($_REQUEST['pros']) != '')
			{
				$pros = array();

				$pieces = explode(',', $_REQUEST['pros']);

				foreach ($pieces as $piece) {
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
					$condiciones[] = 'pc.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			if (isset($_REQUEST['gastos']) && trim($_REQUEST['gastos']) != '')
			{
				$gastos = array();

				$pieces = explode(',', $_REQUEST['gastos']);

				foreach ($pieces as $piece) {
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
					$condiciones[] = 'pc.codgastos IN (' . implode(', ', $gastos) . ')';
				}
			}

			if ($_SESSION['iduser'] != 1)
			{
				$condiciones[] = 'pc.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
			}

			$sql = "
				SELECT
					pc.id,
					pc.num_cia,
					cc.nombre
						AS nombre_cia,
					pc.num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					pc.codgastos
						AS cod,
					cg.descripcion
						AS gasto,
					pc.concepto,
					pc.importe,
					pc.iva,
					pc.ret_iva,
					pc.isr,
					pc.cedular,
					pc.total,
					CASE
						WHEN tipo_renta = 1 THEN
							'INTERNA'
						WHEN tipo_renta = 2 THEN
							'EXTERNA'
					END
						AS tipo_renta
				FROM
					pre_cheques pc
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_proveedores cp
						ON (pc.num_proveedor = cp.num_proveedor)
					LEFT JOIN catalogo_gastos cg
						USING (codgastos)
					" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
				ORDER BY
					pc.num_cia,
					pc.codgastos
			";

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ban/PagosFijosConsulta.tpl');
			$tpl->prepare();

			if ($result)
			{
				$num_cia = NULL;

				foreach ($result as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $row['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
					}

					$tpl->newBlock('row');

					$tpl->assign('id', $row['id']);
					$tpl->assign('num_pro', $row['num_pro']);
					$tpl->assign('nombre_pro', utf8_encode($row['nombre_pro']));
					$tpl->assign('cod', $row['cod']);
					$tpl->assign('gasto', utf8_encode($row['gasto']));
					$tpl->assign('concepto', utf8_encode($row['concepto']));
					$tpl->assign('importe', $row['importe'] != 0 ? number_format($row['importe'], 2) : '&nbsp;');
					$tpl->assign('iva', $row['iva'] != 0 ? number_format($row['iva'], 2) : '&nbsp;');
					$tpl->assign('ret_iva', $row['ret_iva'] != 0 ? number_format($row['ret_iva'], 2) : '&nbsp;');
					$tpl->assign('isr', $row['isr'] != 0 ? number_format($row['isr'], 2) : '&nbsp;');
					$tpl->assign('cedular', $row['cedular'] != 0 ? number_format($row['cedular'], 2) : '&nbsp;');
					$tpl->assign('total', $row['total'] != 0 ? number_format($row['total'], 2) : '&nbsp;');
					$tpl->assign('tipo_renta', '<span class="' . ($row['tipo_renta'] == 'INTERNA' ? 'blue' : 'red') . '">' . utf8_encode($row['tipo_renta']) . '</span>');
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'alta':
			$tpl = new TemplatePower('plantillas/ban/PagosFijosAlta.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();

			break;

		case 'do_alta':
			$sql = '';

			foreach ($_REQUEST['num_cia'] as $i => $num_cia)
			{
				if ($num_cia > 0 && $_REQUEST['num_pro'][$i] > 0 && $_REQUEST['cod'][$i] > 0 && get_val($_REQUEST['total'][$i]) > 0)
				{
					$sql .= "INSERT INTO pre_cheques (
						num_cia,
						num_proveedor,
						codgastos,
						concepto,
						importe,
						iva,
						ret_iva,
						isr,
						cedular,
						total,
						tipo_renta
					) VALUES (
						{$num_cia},
						{$_REQUEST['num_pro'][$i]},
						{$_REQUEST['cod'][$i]},
						'{$_REQUEST['concepto'][$i]}',
						" . get_val($_REQUEST['importe'][$i]) . ",
						" . get_val($_REQUEST['iva'][$i]) . ",
						" . get_val($_REQUEST['ret_iva'][$i]) . ",
						" . get_val($_REQUEST['isr'][$i]) . ",
						" . get_val($_REQUEST['cedular'][$i]) . ",
						" . get_val($_REQUEST['total'][$i]) . ",
						{$_REQUEST['tipo_renta'][$i]}
					);\n";
				}
			}

			if ($sql != '')
			{
				$db->query($sql);
			}

			break;

		case 'modificar':
			$sql = "
				SELECT
					pc.id,
					pc.num_cia,
					cc.nombre
						AS nombre_cia,
					pc.num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					pc.codgastos
						AS cod,
					cg.descripcion
						AS gasto,
					pc.concepto,
					pc.importe,
					pc.iva,
					pc.ret_iva,
					pc.isr,
					pc.cedular,
					pc.total,
					pc.tipo_renta
				FROM
					pre_cheques pc
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_proveedores cp
						ON (pc.num_proveedor = cp.num_proveedor)
					LEFT JOIN catalogo_gastos cg
						USING (codgastos)
				WHERE
					pc.id = {$_REQUEST['id']}
				ORDER BY
					pc.num_cia,
					pc.codgastos
			";

			$result = $db->query($sql);

			$row = $result[0];

			$tpl = new TemplatePower('plantillas/ban/PagosFijosModificar.tpl');
			$tpl->prepare();

			$tpl->assign('id', $row['id']);
			$tpl->assign('num_cia', $row['num_cia']);
			$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
			$tpl->assign('num_pro', $row['num_pro']);
			$tpl->assign('nombre_pro', utf8_encode($row['nombre_pro']));
			$tpl->assign('cod', $row['cod']);
			$tpl->assign('gasto', utf8_encode($row['gasto']));
			$tpl->assign('concepto', utf8_encode($row['concepto']));
			$tpl->assign('importe', $row['importe'] != 0 ? number_format($row['importe'], 2) : '');
			$tpl->assign('iva', $row['iva'] != 0 ? number_format($row['iva'], 2) : '');
			$tpl->assign('ret_iva', $row['ret_iva'] != 0 ? number_format($row['ret_iva'], 2) : '');
			$tpl->assign('isr', $row['isr'] != 0 ? number_format($row['isr'], 2) : '');
			$tpl->assign('cedular', $row['cedular'] != 0 ? number_format($row['cedular'], 2) : '');
			$tpl->assign('total', $row['total'] != 0 ? number_format($row['total'], 2) : '');
			$tpl->assign('tipo_renta_' . $row['tipo_renta'], ' selected="selected"');

			echo $tpl->getOutputContent();

			break;

		case 'do_modificar':
			$sql = "
				UPDATE
					pre_cheques
				SET
					num_cia = {$_REQUEST['num_cia']},
					num_proveedor = {$_REQUEST['num_pro']},
					codgastos = {$_REQUEST['cod']},
					concepto = '{$_REQUEST['concepto']}',
					importe = " . get_val($_REQUEST['importe']) . ",
					iva = " . get_val($_REQUEST['iva']) . ",
					ret_iva = " . get_val($_REQUEST['ret_iva']) . ",
					isr = " . get_val($_REQUEST['isr']) . ",
					cedular = " . get_val($_REQUEST['cedular']) . ",
					total = " . get_val($_REQUEST['total']) . ",
					tipo_renta = {$_REQUEST['tipo_renta']}
				WHERE
					id = {$_REQUEST['id']}
			";

			$db->query($sql);

			break;

		case 'do_baja':
			$sql = "DELETE FROM pre_cheques WHERE id = {$_REQUEST['id']}";

			$db->query($sql);

			break;

		case 'generar_pagos':
			$condiciones = array();

			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '')
			{
				$cias = array();

				$pieces = explode(',', $_REQUEST['cias']);

				foreach ($pieces as $piece) {
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
					$condiciones[] = 'pc.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['pros']) && trim($_REQUEST['pros']) != '')
			{
				$pros = array();

				$pieces = explode(',', $_REQUEST['pros']);

				foreach ($pieces as $piece) {
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
					$condiciones[] = 'pc.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			if (isset($_REQUEST['gastos']) && trim($_REQUEST['gastos']) != '')
			{
				$gastos = array();

				$pieces = explode(',', $_REQUEST['gastos']);

				foreach ($pieces as $piece) {
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
					$condiciones[] = 'pc.codgastos IN (' . implode(', ', $gastos) . ')';
				}
			}

			if ($_SESSION['iduser'] != 1)
			{
				$condiciones[] = 'pc.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
			}

			$sql = "SELECT
				pc.id,
				pc.num_cia,
				cc.tipo_cia,
				cc.nombre AS nombre_cia,
				/*CASE
					WHEN cc.clabe_cuenta IS NULL OR TRIM(cc.clabe_cuenta) = '' OR LENGTH(TRIM(cc.clabe_cuenta)) < 11 THEN
						FALSE
					ELSE
						TRUE
				END AS con_cuenta,*/
				COALESCE((
					SELECT
						FALSE
					FROM
						cuentas_mancomunadas
					WHERE
						tsbaja IS NULL
						AND cia_secundaria = cc.num_cia
				), TRUE) AS con_cuenta,
				/*(SELECT MIN(num_cia) FROM catalogo_companias WHERE rfc = cc.rfc AND LENGTH(clabe_cuenta) = 11) AS cia_pago,*/
				COALESCE((
					SELECT
						cia_principal
					FROM
						cuentas_mancomunadas
					WHERE
						tsbaja IS NULL
						AND cia_secundaria = cc.num_cia
				), cc.num_cia) AS cia_pago,
				/*(SELECT nombre FROM catalogo_companias WHERE rfc = cc.rfc AND LENGTH(clabe_cuenta) = 11 ORDER BY num_cia LIMIT 1) AS nombre_cia_pago,*/
				COALESCE((
					SELECT
						nombre
					FROM
						cuentas_mancomunadas
						LEFT JOIN catalogo_companias ON (num_cia = cia_principal)
					WHERE
						tsbaja IS NULL
						AND cia_secundaria = cc.num_cia
				), cc.nombre) AS nombre_cia_pago,
				pc.num_proveedor AS num_pro,
				cp.nombre AS nombre_pro,
				pc.codgastos AS cod,
				cg.descripcion AS gasto,
				pc.concepto,
				pc.importe,
				pc.iva,
				pc.ret_iva,
				pc.isr,
				pc.cedular,
				pc.total,
				pc.tipo_renta,
				cp.trans
			FROM
				pre_cheques pc
				LEFT JOIN catalogo_companias cc USING (num_cia)
				LEFT JOIN catalogo_proveedores cp ON (pc.num_proveedor = cp.num_proveedor)
				LEFT JOIN catalogo_gastos cg USING (codgastos)
				" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
			ORDER BY
				pc.num_cia,
				pc.codgastos";

			$result = $db->query($sql);

			if ($result)
			{
				$sql = '';

				$num_cia = NULL;

				$folio = array();

				foreach ($result as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$cia_pago = $row['con_cuenta'] == 'f' ? $row['cia_pago'] : $row['num_cia'];

						if ( ! isset($folio[$cia_pago]))
						{
							$query = $db->query("SELECT COALESCE((SELECT MAX(folio) FROM folios_cheque WHERE num_cia = {$cia_pago} AND cuenta = {$_REQUEST['banco']}), 50) + 1 AS folio");

							$folio[$cia_pago] = $query[0]['folio'];
						}

						$no_pagar = $row['con_cuenta'] == 't' || ($row['con_cuenta'] == 'f' && $cia_pago > 0) ? FALSE : TRUE;
					}

					if ($no_pagar)
					{
						continue;
					}

					$sql .= "INSERT INTO cheques (cod_mov, num_proveedor, num_cia, fecha, folio, importe, iduser, a_nombre, imp, concepto, codgastos, cuenta, poliza, tipo_renta) VALUES (" . ($row['trans'] == 't' ? 41 : 5) . ", {$row['num_pro']}, {$cia_pago}, '{$_REQUEST['fecha']}', {$folio[$cia_pago]}, {$row['total']}, {$_SESSION['iduser']}, '{$row['nombre_pro']}', FALSE, '" . (trim($row['concepto']) != '' ? $row['concepto'] : $row['gasto']) . "', {$row['cod']}, {$_REQUEST['banco']}, " . ($row['trans'] == 't' ? 'TRUE' : 'FALSE') . ", {$row['tipo_renta']});\n";
					$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, folio, concepto, cuenta, iduser) VALUES ({$cia_pago}, '{$_REQUEST['fecha']}', TRUE, {$row['total']}, " . ($row['trans'] == 't' ? 41 : 5) . ", {$folio[$cia_pago]}, '" . (trim($row['concepto']) != '' ? $row['concepto'] : $row['gasto']) . "', {$_REQUEST['banco']}, {$_SESSION['iduser']});\n";
					$sql .= "INSERT INTO movimiento_gastos (codgastos, num_cia, fecha, importe, captura, folio, cuenta, concepto) VALUES ({$row['cod']}, {$cia_pago}, '{$_REQUEST['fecha']}', {$row['total']}, TRUE, {$folio[$cia_pago]}, {$_REQUEST['banco']}, '" . (trim($row['concepto']) != '' ? $row['concepto'] : $row['gasto']) . "');\n";
					$sql .= "INSERT INTO folios_cheque (folio, num_cia, reservado, utilizado, fecha, cuenta) VALUES ({$folio[$cia_pago]}, {$cia_pago}, FALSE, TRUE, '{$_REQUEST['fecha']}', {$_REQUEST['banco']});\n";

					if ($row['trans'] == 't')
					{
						$sql .= "INSERT INTO transferencias_electronicas (num_cia, num_proveedor, folio, importe, fecha_gen, status, cuenta) VALUES ({$cia_pago}, {$row['num_pro']}, {$folio[$cia_pago]}, {$row['total']}, '{$_REQUEST['fecha']}', 0, {$_REQUEST['banco']});\n";
					}

					if ($cia_pago != $num_cia)
					{
						$sql .= "INSERT INTO pagos_otras_cias (num_cia, folio, cuenta, num_cia_aplica, fecha) VALUES ({$cia_pago}, {$folio[$cia_pago]}, {$_REQUEST['banco']}, {$row['num_cia']}, '{$_REQUEST['fecha']}');\n";
					}

					if ($row['tipo_cia'] == 4)
					{
						$num_fact = $row['num_cia'] . date('dm') . rand(0, 9);
						$prisr = $row['isr'] * 100 / $row['importe'];
						$priva = $row['ret_iva'] * 100 / $row['importe'];
						$pcedular = $row['cedular'] * 100 / $row['importe'];

						$sql .= "INSERT INTO facturas_zap (
							num_cia,
							num_proveedor,
							num_fact,
							fecha,
							concepto,
							codgastos,
							importe,
							iva,
							pisr,
							isr,
							pivaret,
							ivaret,
							pcedular,
							cedular,
							total,
							iduser,
							por_aut,
							copia_fac,
							folio,
							cuenta,
							tspago
						) VALUES (
							{$row['num_cia']},
							{$row['num_pro']},
							'{$num_fact}',
							'{$_REQUEST['fecha']}',
							'" . (trim($row['concepto']) != '' ? $row['concepto'] : $row['gasto']) . "',
							{$row['cod']},
							{$row['importe']},
							ABS({$row['iva']}),
							ABS({$prisr}),
							ABS({$row['isr']}),
							ABS({$priva}),
							ABS({$row['ret_iva']}),
							ABS({$pcedular}),
							ABS({$row['cedular']}),
							{$row['total']},
							{$_SESSION['iduser']},
							TRUE,
							TRUE,
							{$folio[$cia_pago]},
							{$_REQUEST['banco']},
							NOW()
						);\n";
					}
					else
					{
						$num_fact = $row['num_cia'] . date('dm') . rand(0, 9);
						$piva = $row['iva'] * 100 / $row['importe'];
						$prisr = $row['isr'] * 100 / $row['importe'];
						$priva = $row['ret_iva'] * 100 / $row['importe'];
						$pcedular = $row['cedular'] * 100 / $row['importe'];
						$tipo_fac = stristr($row['gasto'], 'HONORARIO') !== FALSE ? 1 : (stristr($row['gasto'], 'RENTA') ? 2 : 3);

						$sql .= "INSERT INTO facturas (
							num_proveedor,
							num_cia,
							num_fact,
							fecha,
							importe,
							piva,
							iva,
							pretencion_isr,
							retencion_isr,
							pretencion_iva,
							retencion_iva,
							pcedular,
							cedular,
							codgastos,
							total,
							tipo_factura,
							fecha_captura,
							iduser,
							concepto
						) VALUES (
							{$row['num_pro']},
							{$row['num_cia']},
							'{$num_fact}',
							'{$_REQUEST['fecha']}',
							{$row['importe']},
							ABS({$piva}),
							ABS({$row['iva']}),
							ABS({$prisr}),
							ABS({$row['isr']}),
							ABS({$priva}),
							ABS({$row['ret_iva']}),
							ABS({$pcedular}),
							ABS({$row['cedular']}),
							{$row['cod']},
							{$row['total']},
							{$tipo_fac},
							NOW()::DATE,
							{$_SESSION['iduser']},
							'" . (trim($row['concepto']) != '' ? $row['concepto'] : $row['gasto']) . "'
						);\n";

						$sql .= "INSERT INTO facturas_pagadas (
							num_cia,
							num_proveedor,
							num_fact,
							total,
							descripcion,
							fecha,
							fecha_cheque,
							folio_cheque,
							codgastos,
							proceso,
							imp,
							cuenta
						) VALUES (
							{$cia_pago},
							{$row['num_pro']},
							'{$num_fact}',
							{$row['total']},
							'" . (trim($row['concepto']) != '' ? $row['concepto'] : $row['gasto']) . "',
							'{$_REQUEST['fecha']}',
							'{$_REQUEST['fecha']}',
							{$folio[$cia_pago]},
							{$row['cod']},
							TRUE,
							TRUE,
							{$_REQUEST['banco']}
						);\n";
					}

					$folio[$cia_pago]++;
				}

				$db->query($sql);
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/PagosFijos.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha', date('d/m/Y'));

$tpl->printToScreen();
