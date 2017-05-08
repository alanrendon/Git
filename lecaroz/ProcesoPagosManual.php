<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value)
{
	return intval($value, 10);
}

function toNumberFormat($value)
{
	return number_format($value, 2);
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

function siguiente_proveedor($params)
{
	global $db;

	$condiciones = array();

	$condiciones[] = "pp.fecha <= '{$params['fecha_corte']}'";

	$condiciones[] = "pp.total != 0";

	if ( ! isset($params['sin_cuenta']))
	{
		$condiciones[] = "LENGTH(TRIM(" . ($params['banco'] == 1 ? 'cc.clabe_cuenta' : 'cc.clabe_cuenta2') . ")) = 11";
	}

	if (isset($params['cias']) && trim($params['cias']) != '')
	{
		$cias = array();

		$pieces = explode(',', $params['cias']);
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
			$condiciones[] = 'pp.num_cia IN (' . implode(', ', $cias) . ')';
		}
	}

	if (isset($params['omitir_cias']) && trim($params['omitir_cias']) != '')
	{
		$omitir_cias = array();

		$pieces = explode(',', $params['omitir_cias']);
		foreach ($pieces as $piece)
		{
			if (count($exp = explode('-', $piece)) > 1)
			{
				$omitir_cias[] =  implode(', ', range($exp[0], $exp[1]));
			}
			else
			{
				$omitir_cias[] = $piece;
			}
		}

		if (count($omitir_cias) > 0)
		{
			$condiciones[] = 'pp.num_cia NOT IN (' . implode(', ', $omitir_cias) . ')';
		}
	}

	if (isset($params['pros']) && trim($params['pros']) != '')
	{
		$pros = array();

		$pieces = explode(',', $params['pros']);
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
			$condiciones[] = 'pp.num_proveedor IN (' . implode(', ', $pros) . ')';
		}
	}

	if (isset($params['omitir_pros']) && trim($params['omitir_pros']) != '')
	{
		$omitir_pros = array();

		$pieces = explode(',', $params['omitir_pros']);
		foreach ($pieces as $piece)
		{
			if (count($exp = explode('-', $piece)) > 1)
			{
				$omitir_pros[] =  implode(', ', range($exp[0], $exp[1]));
			}
			else
			{
				$omitir_pros[] = $piece;
			}
		}

		if (count($omitir_pros) > 0)
		{
			$condiciones[] = 'pp.num_proveedor NOT IN (' . implode(', ', $omitir_pros) . ')';
		}
	}

	if ( ! isset($params['current']) && isset($params['num_pro']) && $params['num_pro'] > 0)
	{
		$condiciones[] = "pp.num_proveedor = {$params['num_pro']}";
	}
	else if (isset($params['current']) && $params['current'] > 0)
	{
		if (isset($params['next']) && $params['next'] > 0)
		{
			$condiciones[] = "pp.num_proveedor >= {$params['next']}";
		}
		else
		{
			$condiciones[] = "pp.num_proveedor > {$params['current']}";
		}
	}

	$condiciones_string = implode(' AND ', $condiciones);

	$result = $db->query("SELECT
		pp.num_proveedor AS num_pro,
		cp.nombre AS nombre_pro,
		CASE
			WHEN cp.trans = TRUE THEN
				'TRANSFERENCIA'
			ELSE
				'CHEQUE'
		END AS tipo_pago
	FROM
		pasivo_proveedores pp
		LEFT JOIN catalogo_proveedores cp USING (num_proveedor)
		LEFT JOIN catalogo_companias cc USING (num_cia)
	WHERE
		{$condiciones_string}
	GROUP BY
		pp.num_proveedor,
		cp.nombre,
		cp.trans
	ORDER BY
		pp.num_proveedor");

	if ($result)
	{
		return $result[0];
	}

	return NULL;
}

function obtener_datos_cia($num_cia)
{
	global $db;

	$result = $db->query("SELECT
		num_cia,
		nombre_corto AS nombre_cia,
		rfc AS rfc_cia,
		clabe_cuenta AS cuenta_1,
		clabe_cuenta2 AS cuenta_2,
		CASE
			WHEN LENGTH(TRIM(clabe_cuenta)) = 11 THEN
				TRUE
			ELSE
				FALSE
		END AS con_cuenta_1,
		CASE
			WHEN LENGTH(TRIM(clabe_cuenta2)) = 11 THEN
				TRUE
			ELSE
				FALSE
		END AS con_cuenta_2,
		COALESCE((
			SELECT
				saldo_libros
			FROM
				saldos
			WHERE
				num_cia = cc.num_cia
				AND cuenta = 1
		), 0) - COALESCE((
			SELECT
				SUM(importe)
			FROM
				estado_cuenta
			WHERE
				num_cia = cc.num_cia
				AND cuenta = 1
				AND tipo_mov = FALSE
				AND fecha_con IS NULL
		), 0) AS saldo_1,
		COALESCE((
			SELECT
				saldo_libros
			FROM
				saldos
			WHERE
				num_cia = cc.num_cia
				AND cuenta = 2
		), 0) - COALESCE((
			SELECT
				SUM(importe)
			FROM
				estado_cuenta
			WHERE
				num_cia = cc.num_cia
				AND cuenta = 2
				AND tipo_mov = FALSE
				AND fecha_con IS NULL
		), 0) AS saldo_2,
		COALESCE((
			SELECT
				cia_principal
			FROM
				cuentas_mancomunadas
			WHERE
				tsbaja IS NULL
				AND cia_secundaria = cc.num_cia
		), cc.num_cia) AS cia_principal
	FROM
		catalogo_companias cc
	WHERE
		num_cia = {$num_cia}");

	$cia = $result[0];

	if ($cia['con_cuenta_1'] == 'f' || $cia['num_cia'] != $cia['cia_principal'])
	{
		$result = $db->query("SELECT
			num_cia,
			nombre_corto AS nombre_cia,
			clabe_cuenta AS cuenta
		FROM
			catalogo_companias
		WHERE
			-- rfc = '{$cia['rfc_cia']}'
			num_cia = {$cia['cia_principal']}
			AND LENGTH(TRIM(clabe_cuenta)) = 11
		ORDER BY
			num_cia");

		if ($result)
		{
			$cia['cias_pago_1'] = $result;
		}
	}

	if ($cia['con_cuenta_2'] == 'f' || $cia['num_cia'] != $cia['cia_principal'])
	{
		$result = $db->query("SELECT
			num_cia,
			nombre_corto AS nombre_cia,
			clabe_cuenta2 AS cuenta
		FROM
			catalogo_companias
		WHERE
			-- rfc = '{$cia['rfc_cia']}'
			num_cia = {$cia['cia_principal']}
			AND LENGTH(TRIM(clabe_cuenta2)) = 11
		ORDER BY
			num_cia");

		if ($result)
		{
			$cia['cias_pago_2'] = $result;
		}
	}

	return $cia;
}

function obtener_facturas($params, $current)
{
	global $db;

	$condiciones = array();

	$condiciones[] = "pp.fecha <= '{$params['fecha_corte']}'";

	$condiciones[] = "pp.num_proveedor = {$current}";

	$condiciones[] = "pp.total != 0";

	if ( ! isset($params['sin_cuenta']))
	{
		$condiciones[] = "LENGTH(TRIM(" . ($params['banco'] == 1 ? 'cc.clabe_cuenta' : 'cc.clabe_cuenta2') . ")) = 11";
	}

	if (isset($params['cias']) && trim($params['cias']) != '')
	{
		$cias = array();

		$pieces = explode(',', $params['cias']);
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
			$condiciones[] = 'pp.num_cia IN (' . implode(', ', $cias) . ')';
		}
	}

	if (isset($params['omitir_cias']) && trim($params['omitir_cias']) != '')
	{
		$omitir_cias = array();

		$pieces = explode(',', $params['omitir_cias']);
		foreach ($pieces as $piece)
		{
			if (count($exp = explode('-', $piece)) > 1)
			{
				$omitir_cias[] =  implode(', ', range($exp[0], $exp[1]));
			}
			else
			{
				$omitir_cias[] = $piece;
			}
		}

		if (count($omitir_cias) > 0)
		{
			$condiciones[] = 'pp.num_cia NOT IN (' . implode(', ', $omitir_cias) . ')';
		}
	}

	$condiciones_string = implode(' AND ', $condiciones);

	$result = $db->query("SELECT
		pp.id,
		pp.num_proveedor AS num_pro,
		cp.nombre AS nombre_pro,
		COALESCE((
			/*SELECT
				num_cia
			FROM
				catalogo_companias
			WHERE
				rfc = cc.rfc
			ORDER BY
				num_cia
			LIMIT 1*/
			SELECT
				cia_principal
			FROM
				cuentas_mancomunadas
			WHERE
				cia_secundaria = pp.num_cia
				AND tsbaja IS NULL
		), pp.num_cia) AS num_cia_padre,
		pp.num_cia,
		pp.num_fact,
		pp.fecha,
		COALESCE(f.concepto, pp.descripcion) AS concepto,
		pp.total AS importe,
		CASE
			WHEN cp.verfac = TRUE AND pp.copia_fac = FALSE THEN
				FALSE
			ELSE
				TRUE
		END AS con_copia,
		COALESCE((
			SELECT
				TRUE
			FROM
				facturas_pendientes
			WHERE
				num_proveedor = pp.num_proveedor
				AND num_fact = pp.num_fact
				AND fecha_aclaracion IS NULL
		), FALSE) AS por_aclarar
	FROM
		pasivo_proveedores pp
		LEFT JOIN facturas f USING (num_cia, num_proveedor, num_fact, fecha)
		LEFT JOIN catalogo_proveedores cp USING (num_proveedor)
		LEFT JOIN catalogo_companias cc USING (num_cia)
	WHERE
		{$condiciones_string}
	ORDER BY
		num_cia_padre,
		pp.num_cia,
		pp.fecha,
		pp.num_fact");

	if ($result)
	{
		return $result;
	}

	return NULL;
}

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{

		case 'obtener_pro':
			$result = $db->query("SELECT
				nombre
			FROM
				catalogo_proveedores
			WHERE
				num_proveedor = {$_REQUEST['num_pro']}");

			if ($result)
			{
				echo $result[0]['nombre'];
			}

			break;

		case 'inicio':
			$tpl = new TemplatePower('plantillas/ban/ProcesoPagosManualInicio.tpl');
			$tpl->prepare();

			$tpl->assign('fecha', date('d/m/Y'));

			echo $tpl->getOutputContent();

			break;

		case 'consulta':
			$pro = siguiente_proveedor($_REQUEST);

			if ( ! $pro)
			{
				die;
			}

			if ($result = obtener_facturas($_REQUEST, $pro['num_pro']))
			{
				$tpl = new TemplatePower('plantillas/ban/ProcesoPagosManualProveedores.tpl');
				$tpl->prepare();

				$tpl->assign('num_pro', $pro['num_pro']);
				$tpl->assign('nombre_pro', $pro['nombre_pro']);
				$tpl->assign('tipo_pago', $pro['tipo_pago']);
				$tpl->assign('banco', $_REQUEST['banco']);
				$tpl->assign('nombre_banco', $_REQUEST['banco'] == 1 ? 'BANORTE' : 'SANTANDER');
				$tpl->assign('fecha_pago', $_REQUEST['fecha_pago']);

				$num_cia = NULL;
				$index = 0;

				foreach ($result as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$cia = obtener_datos_cia($num_cia);

						$tpl->newBlock('cia');

						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $cia['nombre_cia']);
						$tpl->assign('cuenta', $cia["con_cuenta_{$_REQUEST['banco']}"] == 't' ? $cia["cuenta_{$_REQUEST['banco']}"] : 'SIN CUENTA');

						if ($cia["con_cuenta_{$_REQUEST['banco']}"] == 't' && $cia['num_cia'] == $cia['cia_principal'])
						{
							$tpl->newBlock('saldo_inicio');

							$tpl->assign('num_cia', $num_cia);

							$tpl->assign('saldo', $cia["saldo_{$_REQUEST['banco']}"]);
							$tpl->assign('saldo_inicio', '<span class="' . ($cia["saldo_{$_REQUEST['banco']}"] > 0 ? 'blue' : 'red') . '">' . number_format($cia["saldo_{$_REQUEST['banco']}"], 2) . '</span>');

							$tpl->newBlock('saldo_final');

							$tpl->assign('num_cia', $num_cia);

							$tpl->assign('saldo_final', '<span class="' . ($cia["saldo_{$_REQUEST['banco']}"] > 0 ? 'blue' : 'red') . '">' . number_format($cia["saldo_{$_REQUEST['banco']}"], 2) . '</span>');
						}
						else
						{
							$tpl->newBlock('cuentas_pago');

							$tpl->assign('num_cia', $num_cia);

							if (isset($cia["cias_pago_{$_REQUEST['banco']}"]))
							{
								foreach ($cia["cias_pago_{$_REQUEST['banco']}"] as $cia_pago)
								{
									$tpl->newBlock('cia_pago');

									$tpl->assign('num_cia', $cia_pago['num_cia']);
									$tpl->assign('nombre_cia', $cia_pago['nombre_cia']);
									$tpl->assign('cuenta', $cia_pago['cuenta']);
								}
							}
						}
					}

					$tpl->newBlock('factura');

					if ($row['con_copia'] == 'f' || $row['por_aclarar'] == 't')
					{
						if ($row['con_copia'] == 'f')
						{
							$row['concepto'] = '<span class="bold red">[SIN COPIA]</span> ' . $row['concepto'];
						}

						if ($row['por_aclarar'] == 't')
						{
							$row['concepto'] = '<span class="bold red">[POR ACLARAR]</span> ' . $row['concepto'];
						}
					}
					else
					{
						$data = json_encode(array(
							'id'			=> intval($row['id']),
							'num_cia'		=> intval($num_cia),
							'num_cia_pago'	=> isset($cia["cias_pago_{$_REQUEST['banco']}"]) ? intval($cia["cias_pago_{$_REQUEST['banco']}"][0]['num_cia']) : intval($num_cia)
						));

						$tpl->assign('factura_checkbox', '<input name="factura[]" type="checkbox" id="factura_' . $index . '" data-index="' . $index . '" data-cia="' . $num_cia . '" data-cia-pago="' . (isset($cia["cias_pago_{$_REQUEST['banco']}"]) ? $cia["cias_pago_{$_REQUEST['banco']}"][0]['num_cia'] : $num_cia) . '" data-importe="' . $row['importe'] . '" value=\'' . $data . '\'' . ($cia["con_cuenta_{$_REQUEST['banco']}"] == 'f' && ! isset($cia["cias_pago_{$_REQUEST['banco']}"]) ? ' disabled=""' : '') . '>');

						$index++;
					}

					$tpl->assign('index', $index);
					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('num_fact', $row['num_fact']);
					$tpl->assign('fecha', $row['fecha']);
					$tpl->assign('concepto', $row['concepto']);
					$tpl->assign('importe', number_format($row['importe'], 2));
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'pagar':
			$ids = array();
			$cia_pago = array();

			foreach ($_REQUEST['factura'] as $factura)
			{
				$data = json_decode($factura);

				$ids[] = $data->id;

				$cia_pago[$data->num_cia] = $data->num_cia_pago;
			}

			$sql = "SELECT
				pp.id,
				pp.num_cia,
				pp.num_fact,
				pp.total AS importe,
				pp.descripcion AS descripcion,
				pp.fecha,
				EXTRACT(YEAR FROM pp.fecha) AS anio,
				pp.num_proveedor AS num_pro,
				cp.nombre AS nombre_pro,
				pp.codgastos AS gasto,
				cg.descripcion AS nombre_gasto,
				cp.trans AS tipo_pago,
				cp.facturas_por_pago
			FROM
				pasivo_proveedores pp
				LEFT JOIN catalogo_gastos cg USING (codgastos)
				LEFT JOIN facturas f USING (num_cia, num_proveedor, num_fact, fecha)
				LEFT JOIN catalogo_proveedores cp USING (num_proveedor)
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				pp.id IN (" . implode(', ', $ids) . ")
			ORDER BY
				pp.num_cia,
				num_pro,
				pp.fecha,
				importe DESC";

			$result = $db->query($sql);

			if ($result)
			{
				$datos = array();

				$num_cia = NULL;
				$num_pro = NULL;
				$anio = NULL;
				$gasto = NULL;
				$num_facts = 0;

				$ts = date('d/m/Y H:i:s');

				foreach ($result as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$datos[$num_cia] = array(
							'num_cia'		=> $row['num_cia'],
							'num_cia_pago'	=> $cia_pago[$row['num_cia']],
							'pagos'			=> array()
						);

						$num_pro = NULL;
						$anio = NULL;
						$gasto = NULL;
						$num_facturas = 0;

						$pagos_cont = 0;
					}

					if ($num_pro != $row['num_pro'])
					{
						$facturas_por_pago = in_array($num_cia, array(700, 800)) ? 1 : $row['facturas_por_pago'];
					}

					if ($num_pro != $row['num_pro'] || $anio != $row['anio'] || $gasto != $row['gasto'] || $num_facturas == $facturas_por_pago)
					{
						if ($num_pro != NULL || $anio != NULL || $gasto != NULL || $num_facturas <= $facturas_por_pago)
						{
							$pagos_cont++;
						}

						$num_pro = $row['num_pro'];
						$anio = $row['anio'];
						$gasto = $row['gasto'];
						$num_facturas = 0;

						$datos[$num_cia]['pagos'][$pagos_cont] = array();
					}

					$datos[$num_cia]['pagos'][$pagos_cont][] = $row['id'];

					$num_facturas++;
				}//echo '<pre>' . print_r($datos, TRUE) . '</pre>';die;

				foreach ($datos as $num_cia => $cia)
				{
					foreach ($cia['pagos'] as $pagos)
					{
						$sql = "INSERT INTO cheques (
							num_cia,
							cuenta,
							folio,
							fecha,
							cod_mov,
							num_proveedor,
							a_nombre,
							proceso,
							codgastos,
							concepto,
							importe,
							iduser,
							poliza,
							tsmod,
							facturas
						)
						SELECT
							{$cia_pago[$num_cia]},
							{$_REQUEST['banco']},
							COALESCE((
								SELECT
									folio + 1
								FROM
									folios_cheque
								WHERE
									num_cia = {$cia_pago[$num_cia]}
									AND cuenta = {$_REQUEST['banco']}
								ORDER BY
									id DESC
								LIMIT 1
							), 51),
							'{$_REQUEST['fecha_pago']}',
							CASE
								WHEN cp.trans = TRUE THEN
									41
								ELSE
									5
							END,
							pp.num_proveedor,
							cp.nombre,
							FALSE,
							pp.codgastos,
							cg.descripcion,
							SUM(pp.total),
							{$_SESSION['iduser']},
							CASE
								WHEN cp.trans = TRUE THEN
									TRUE
								ELSE
									FALSE
							END,
							'{$ts}',
							array_to_string(array(
								SELECT
									num_fact
								FROM
									pasivo_proveedores
								WHERE
									id IN (" . implode(', ', $pagos) . ")
								ORDER BY
									id
							), ' ')
						FROM
							pasivo_proveedores pp
							LEFT JOIN catalogo_gastos cg USING (codgastos)
							LEFT JOIN facturas f USING (num_cia, num_proveedor, num_fact, fecha)
							LEFT JOIN catalogo_proveedores cp USING (num_proveedor)
							LEFT JOIN catalogo_companias cc USING (num_cia)
						WHERE
							pp.id IN (" . implode(', ', $pagos) . ")
						GROUP BY
							pp.num_proveedor,
							cp.nombre,
							pp.codgastos,
							cg.descripcion,
							cp.trans;\n";

						$sql .= "INSERT INTO estado_cuenta (
							num_cia,
							cuenta,
							folio,
							fecha,
							tipo_mov,
							cod_mov,
							concepto,
							importe,
							timestamp,
							iduser,
							tsins,
							idins
						)
						SELECT
							{$cia_pago[$num_cia]},
							{$_REQUEST['banco']},
							COALESCE((
								SELECT
									folio + 1
								FROM
									folios_cheque
								WHERE
									num_cia = {$cia_pago[$num_cia]}
									AND cuenta = {$_REQUEST['banco']}
								ORDER BY
									id DESC
								LIMIT 1
							), 51),
							'{$_REQUEST['fecha_pago']}',
							TRUE,
							CASE
								WHEN cp.trans = TRUE THEN
									41
								ELSE
									5
							END,
							array_to_string(array(
								SELECT
									num_fact
								FROM
									pasivo_proveedores
								WHERE
									id IN (" . implode(', ', $pagos) . ")
								ORDER BY
									id
							), ' '),
							SUM(pp.total),
							'{$ts}',
							{$_SESSION['iduser']},
							'{$ts}',
							{$_SESSION['iduser']}
						FROM
							pasivo_proveedores pp
							LEFT JOIN catalogo_gastos cg USING (codgastos)
							LEFT JOIN facturas f USING (num_cia, num_proveedor, num_fact, fecha)
							LEFT JOIN catalogo_proveedores cp USING (num_proveedor)
							LEFT JOIN catalogo_companias cc USING (num_cia)
						WHERE
							pp.id IN (" . implode(', ', $pagos) . ")
						GROUP BY
							pp.num_proveedor,
							cp.nombre,
							pp.codgastos,
							cg.descripcion,
							cp.trans;\n";

						$sql .= "INSERT INTO transferencias_electronicas (
							num_cia,
							cuenta,
							folio,
							fecha_gen,
							num_proveedor,
							importe,
							status,
							iduser
						)
						SELECT
							{$cia_pago[$num_cia]},
							{$_REQUEST['banco']},
							COALESCE((
								SELECT
									folio + 1
								FROM
									folios_cheque
								WHERE
									num_cia = {$cia_pago[$num_cia]}
									AND cuenta = {$_REQUEST['banco']}
								ORDER BY
									id DESC
								LIMIT 1
							), 51),
							'{$_REQUEST['fecha_pago']}',
							pp.num_proveedor,
							SUM(pp.total),
							0,
							{$_SESSION['iduser']}
						FROM
							pasivo_proveedores pp
							LEFT JOIN catalogo_gastos cg USING (codgastos)
							LEFT JOIN facturas f USING (num_cia, num_proveedor, num_fact, fecha)
							LEFT JOIN catalogo_proveedores cp USING (num_proveedor)
							LEFT JOIN catalogo_companias cc USING (num_cia)
						WHERE
							pp.id IN (" . implode(', ', $pagos) . ")
							AND cp.trans = TRUE
						GROUP BY
							pp.num_proveedor,
							cp.nombre,
							pp.codgastos,
							cg.descripcion,
							cp.trans;\n";

						$sql .= "UPDATE saldos
						SET saldo_libros = saldo_libros - COALESCE((
								SELECT
									SUM(pp.total)
								FROM
									pasivo_proveedores pp
								WHERE
									pp.id IN (" . implode(', ', $pagos) . ")
							), 0)
						WHERE
							num_cia = {$cia_pago[$num_cia]}
							AND cuenta = {$_REQUEST['banco']};\n";

						$sql .= "INSERT INTO movimiento_gastos (
							num_cia,
							cuenta,
							folio,
							fecha,
							codgastos,
							concepto,
							importe,
							captura
						)
						SELECT
							{$cia_pago[$num_cia]},
							{$_REQUEST['banco']},
							COALESCE((
								SELECT
									folio + 1
								FROM
									folios_cheque
								WHERE
									num_cia = {$cia_pago[$num_cia]}
									AND cuenta = {$_REQUEST['banco']}
								ORDER BY
									id DESC
								LIMIT 1
							), 51),
							'{$_REQUEST['fecha_pago']}',
							pp.codgastos,
							'PAGO PROVEEDOR: ' || pp.num_proveedor || 'FAC.: ' || array_to_string(array(
								SELECT
									num_fact
								FROM
									pasivo_proveedores
								WHERE
									id IN (" . implode(', ', $pagos) . ")
								ORDER BY
									id
							), ' '),
							SUM(pp.total),
							TRUE
						FROM
							pasivo_proveedores pp
							LEFT JOIN catalogo_gastos cg USING (codgastos)
							LEFT JOIN facturas f USING (num_cia, num_proveedor, num_fact, fecha)
							LEFT JOIN catalogo_proveedores cp USING (num_proveedor)
							LEFT JOIN catalogo_companias cc USING (num_cia)
						WHERE
							pp.id IN (" . implode(', ', $pagos) . ")
						GROUP BY
							pp.num_proveedor,
							cp.nombre,
							pp.codgastos,
							cg.descripcion,
							cp.trans;\n";

						if ($cia['num_cia'] != $cia['num_cia_pago'])
						{
							$sql .= "INSERT INTO pagos_otras_cias (
								num_cia,
								cuenta,
								folio,
								fecha,
								num_cia_aplica
							) VALUES (
								{$cia_pago[$num_cia]},
								{$_REQUEST['banco']},
								COALESCE((
									SELECT
										folio + 1
									FROM
										folios_cheque
									WHERE
										num_cia = {$cia_pago[$num_cia]}
										AND cuenta = {$_REQUEST['banco']}
									ORDER BY
										id DESC
									LIMIT 1
								), 51),
								'{$_REQUEST['fecha_pago']}',
								{$num_cia}
							);\n";
						}

						$sql .= "INSERT INTO facturas_pagadas (
							num_cia,
							num_proveedor,
							num_fact,
							fecha,
							codgastos,
							descripcion,
							total,
							cuenta,
							folio_cheque,
							fecha_cheque,
							proceso,
							imp
						)
						SELECT
							{$cia_pago[$num_cia]},
							pp.num_proveedor,
							pp.num_fact,
							pp.fecha,
							pp.codgastos,
							pp.descripcion,
							pp.total,
							{$_REQUEST['banco']},
							COALESCE((
								SELECT
									folio + 1
								FROM
									folios_cheque
								WHERE
									num_cia = {$cia_pago[$num_cia]}
									AND cuenta = {$_REQUEST['banco']}
								ORDER BY
									id DESC
								LIMIT 1
							), 51),
							'{$_REQUEST['fecha_pago']}',
							FALSE,
							TRUE
						FROM
							pasivo_proveedores pp
						WHERE
							pp.id IN (" . implode(', ', $pagos) . ")
						ORDER BY
							pp.id;\n";

						$sql .= "DELETE FROM pasivo_proveedores WHERE id IN (" . implode(', ', $pagos) . ");\n";

						$sql .= "INSERT INTO folios_cheque (
							num_cia,
							cuenta,
							folio,
							fecha,
							reservado,
							utilizado
						) VALUES (
							{$cia_pago[$num_cia]},
							{$_REQUEST['banco']},
							COALESCE((
								SELECT
									folio + 1
								FROM
									folios_cheque
								WHERE
									num_cia = {$cia_pago[$num_cia]}
									AND cuenta = {$_REQUEST['banco']}
								ORDER BY
									id DESC
								LIMIT 1
							), 51),
							'{$_REQUEST['fecha_pago']}',
							FALSE,
							FALSE
						);\n";

						$db->query($sql);
					}
				}
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/ProcesoPagosManual.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('random', time());

$tpl->printToScreen();
