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
		), 0) AS saldo_2
	FROM
		catalogo_companias cc
	WHERE
		num_cia = {$num_cia}");

	$cia = $result[0];

	if ($cia['con_cuenta_1'] == 'f')
	{
		$result = $db->query("SELECT
			num_cia,
			nombre_corto AS nombre_cia,
			clabe_cuenta AS cuenta
		FROM
			catalogo_companias
		WHERE
			rfc = '{$cia['rfc_cia']}'
			AND LENGTH(TRIM(clabe_cuenta)) = 11
		ORDER BY
			num_cia");

		if ($result)
		{
			$cia['cias_pago_1'] = $result;
		}
	}

	if ($cia['con_cuenta_2'] == 'f')
	{
		$result = $db->query("SELECT
			num_cia,
			nombre_corto AS nombre_cia,
			clabe_cuenta2 AS cuenta
		FROM
			catalogo_companias
		WHERE
			rfc = '{$cia['rfc_cia']}'
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
			SELECT
				num_cia
			FROM
				catalogo_companias
			WHERE
				rfc = cc.rfc
			ORDER BY
				num_cia
			LIMIT 1
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
			$tpl = new TemplatePower('plantillas/ban/ConciliacionDepositosInicio.tpl');
			$tpl->prepare();

			$tpl->assign('fecha', date('d/m/Y'));

			echo $tpl->getOutputContent();

			break;

		case 'consulta':
			$condiciones = array();

			$condiciones[] = "mov.fecha_con IS NULL";

			$condiciones[] = "mov.tipo_mov = FALSE";

			$condiciones[] = "mov.num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');

			$condiciones_string = implode(' AND ', $condiciones);

			$result = $db->query("SELECT
				mov.id,
				mov.num_cia,
				cc.nombre AS nombre_cia,
				cc.clabe_cuenta AS cuenta_1,
				cc.clabe_cuenta2 AS cuenta_2,
				(mov.fecha - INTERVAL '1 DAY')::DATE AS fecha,
				mov.fecha AS fecha_con,
				mov.concepto,
				mov.importe
			FROM
				mov_" . ($_REQUEST['banco'] == 1 ? 'banorte' : 'santander') . " mov
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				{$condiciones_string}
			ORDER BY
				mov.num_cia,
				mov.fecha,
				mov.importe DESC");

			if ($result)
			{
				$tpl = new TemplatePower('plantillas/ban/ConciliacionDepositosConsulta.tpl');
				$tpl->prepare();

				$tpl->assign('banco', $_REQUEST['banco']);

				$tpl->assign('nombre_banco', $_REQUEST['banco'] == 1 ? 'BANORTE' : 'SANTANDER');

				$num_cia = NULL;
				$index = 0;

				$codigos = $db->query("SELECT
					cod_mov AS value,
					cod_mov || ' ' || descripcion AS text
				FROM
					catalogo_mov_" . ($_REQUEST['banco'] == 1 ? 'bancos' : 'santander') . "
				WHERE
					tipo_mov = FALSE
				GROUP BY
					value,
					text
				ORDER BY
					value");

				foreach ($result as $index => $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$cia = obtener_datos_cia($num_cia);

						$tpl->newBlock('cia');

						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
						$tpl->assign('cuenta', $row["cuenta_{$_REQUEST['banco']}"]);

						$total = 0;
					}

					$tpl->newBlock('deposito');

					$data = array(
						'index'		=> intval($index),
						'id'		=> intval($row['id']),
						'num_cia'	=> intval($row['num_cia']),
						'fecha'		=> $row['fecha'],
						'fecha_con'	=> $row['fecha_con'],
						'concepto'	=> $row['concepto'],
						'importe'	=> floatval($row['importe'])
					);

					$tpl->assign('deposito_data', json_encode($data));

					$tpl->assign('index', $index);
					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('fecha', $row['fecha']);
					$tpl->assign('concepto', $row['concepto']);
					$tpl->assign('data_importe', $row['importe']);
					$tpl->assign('importe', number_format($row['importe'], 2));

					$total += $row['importe'];

					$tpl->assign('cia.total_depositos', number_format($total, 2));

					foreach ($codigos as $codigo)
					{
						$tpl->newBlock('cod_mov');

						$tpl->assign('value', $codigo['value']);
						$tpl->assign('text', $codigo['text']);

						if (in_array($codigo['value'], array(1, 2, 7, 13, 16, 29, 99)))
						{
							$tpl->assign('class', 'bold blue');
						}
						else if (in_array($codigo['value'], array(44)))
						{
							$tpl->assign('class', 'bold green');
						}
					}
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'conciliar':
			if ( ! isset($_REQUEST['deposito']))
			{
				return false;
			}

			if ( ! class_exists('PHPMailer'))
			{
				include_once(dirname(__FILE__) . '/includes/phpmailer/class.phpmailer.php');
			}

			$ts = date('d/m/Y H:i:s');

			foreach ($_REQUEST['deposito'] as $i => $deposito_string)
			{
				$deposito = json_decode($deposito_string);
				$tarjeta = json_decode($_REQUEST['tarjeta'][$i]);

				$sql = "UPDATE mov_" . ($_REQUEST['banco'] == 1 ? 'banorte' : 'santander') . "
				SET
					concepto = '" . strtoupper(trim($_REQUEST['concepto'][$i])) . "',
					cod_mov = {$_REQUEST['cod_mov'][$i]},
					fecha_con = NOW()::DATE,
					imprimir = TRUE
				WHERE
					id = {$deposito->id};\n";

				$sql .= "INSERT INTO estado_cuenta (
					num_cia,
					fecha,
					fecha_con,
					tipo_mov,
					importe,
					cod_mov,
					concepto,
					cuenta,
					iduser,
					timestamp,
					tipo_con,
					tsins,
					idins
				)
				SELECT
					num_cia,
					'{$_REQUEST['fecha'][$i]}',
					fecha,
					tipo_mov,
					importe,
					cod_mov,
					concepto,
					{$_REQUEST['banco']},
					{$_SESSION['iduser']},
					'{$ts}',
					4,
					'{$ts}',
					{$_SESSION['iduser']}
				FROM
					mov_" . ($_REQUEST['banco'] == 1 ? 'banorte' : 'santander') . "
				WHERE
					id = {$deposito->id};\n";

				$sql .= "UPDATE saldos
				SET
					saldo_bancos = saldo_bancos + {$deposito->importe},
					saldo_libros = saldo_libros + {$deposito->importe}
				WHERE
					num_cia = {$deposito->num_cia}
					AND cuenta = {$_REQUEST['banco']};\n";

				$db->query($sql);

				$result = $db->query("SELECT MAX(id) AS id FROM estado_cuenta WHERE idins = {$_SESSION['iduser']}");

				$id = $result[0]['id'];

				if ($tarjeta)
				{
					$sql = "INSERT INTO estado_cuenta (
						num_cia,
						fecha,
						fecha_con,
						tipo_mov,
						importe,
						cod_mov,
						concepto,
						cuenta,
						iduser,
						timestamp,
						tipo_con,
						tsins,
						idins
					)
					SELECT
						num_cia,
						'{$_REQUEST['fecha'][$i]}',
						fecha,
						FALSE,
						{$tarjeta->tarjeta},
						44,
						concepto,
						{$_REQUEST['banco']},
						{$_SESSION['iduser']},
						'{$ts}',
						4,
						'{$ts}',
						{$_SESSION['iduser']}
					FROM
						mov_" . ($_REQUEST['banco'] == 1 ? 'banorte' : 'santander') . "
					WHERE
						id = {$deposito->id};\n";

					$sql .= "UPDATE saldos
					SET
						saldo_bancos = saldo_bancos + {$tarjeta->tarjeta},
						saldo_libros = saldo_libros + {$tarjeta->tarjeta}
					WHERE
						num_cia = {$deposito->num_cia}
						AND cuenta = {$_REQUEST['banco']};\n";

					$sql .= "INSERT INTO estado_cuenta (
						num_cia,
						fecha,
						fecha_con,
						tipo_mov,
						importe,
						cod_mov,
						concepto,
						cuenta,
						iduser,
						timestamp,
						tipo_con,
						tsins,
						idins
					)
					SELECT
						num_cia,
						'{$_REQUEST['fecha'][$i]}',
						fecha,
						TRUE,
						{$tarjeta->comision},
						46,
						'COMISION TARJETA DE CREDITO',
						{$_REQUEST['banco']},
						{$_SESSION['iduser']},
						'{$ts}',
						4,
						'{$ts}',
						{$_SESSION['iduser']}
					FROM
						mov_" . ($_REQUEST['banco'] == 1 ? 'banorte' : 'santander') . "
					WHERE
						id = {$deposito->id};\n";

					$sql .= "UPDATE saldos
					SET
						saldo_bancos = saldo_bancos - {$tarjeta->comision},
						saldo_libros = saldo_libros - {$tarjeta->comision}
					WHERE
						num_cia = {$deposito->num_cia}
						AND cuenta = {$_REQUEST['banco']};\n";

					$sql .= "INSERT INTO estado_cuenta (
						num_cia,
						fecha,
						fecha_con,
						tipo_mov,
						importe,
						cod_mov,
						concepto,
						cuenta,
						iduser,
						timestamp,
						tipo_con,
						tsins,
						idins
					)
					SELECT
						num_cia,
						'{$_REQUEST['fecha'][$i]}',
						fecha,
						TRUE,
						{$tarjeta->iva},
						10,
						'IVA POR COMISIONES',
						{$_REQUEST['banco']},
						{$_SESSION['iduser']},
						'{$ts}',
						4,
						'{$ts}',
						{$_SESSION['iduser']}
					FROM
						mov_" . ($_REQUEST['banco'] == 1 ? 'banorte' : 'santander') . "
					WHERE
						id = {$deposito->id};\n";

					$sql .= "UPDATE saldos
					SET
						saldo_bancos = saldo_bancos - {$tarjeta->iva},
						saldo_libros = saldo_libros - {$tarjeta->iva}
					WHERE
						num_cia = {$deposito->num_cia}
						AND cuenta = {$_REQUEST['banco']};\n";

					$db->query($sql);
				}

				if ($_REQUEST['cod_mov'][$i] == 1 && strtoupper(trim($deposito->concepto)) != strtoupper(trim($_REQUEST['concepto'][$i])))
				{
					$result = $db->query("SELECT
						cc.num_cia,
						cc.nombre_corto AS nombre_cia,
						cc.email AS email_cia,
						ca.email AS email_admin,
						COALESCE((
							SELECT
								TRUE
							FROM
								catalogo_expendios
							WHERE
								num_cia = {$deposito->num_cia}
								AND idagven > 0
							LIMIT
								1
						), FALSE) AS agente_ventas,
						idadministrador AS admin
					FROM
						catalogo_companias cc
						LEFT JOIN catalogo_administradores ca
							USING (idadministrador)
					WHERE
						num_cia = {$deposito->num_cia}");

					$cia = $result[0];

					$mail = new PHPMailer();

					$mail->IsSMTP();
					$mail->Host = 'mail.lecaroz.com';
					$mail->Port = 587;
					$mail->SMTPAuth = true;
					$mail->Username = 'mollendo@lecaroz.com';
					$mail->Password = 'L3c4r0z*';

					$mail->From = 'mollendo@lecaroz.com';
					$mail->FromName = utf8_decode('Oficinas Administrativas Mollendo, S. de R.L. de C.V.');

					$mail->AddBCC('miguelrebuelta@lecaroz.com');

					if ($cia['email_cia'] != '')
					{
						$mail->AddAddress($cia['email_cia']);
					}

					if ($cia['email_admin'] != '')
					{
						$mail->AddCC($cia['email_admin']);
					}

					if ($cia['agente_ventas'] == 't')
					{
						$mail->AddCC('liliabalcazar@hotmail.com');
						$mail->AddCC('lilia.balcazar@lecaroz.com');
					}

					if ($cia['admin'] == 13)
					{
						$mail->AddBCC('ilarracheai@hotmail.com');
						$mail->AddBCC('jmjuan68@hotmail.com');
					}

					$mail->Subject = 'Deposito de pago de cliente';

					$tpl = new TemplatePower('plantillas/ban/email_deposito_cliente.tpl');
					$tpl->prepare();

					$tpl->assign('num_cia', $cia['num_cia']);
					$tpl->assign('nombre_cia', $cia['nombre_cia']);

					$tpl->assign('cliente', strtoupper($_REQUEST['concepto'][$i]));
					$tpl->assign('importe', number_format($deposito->importe, 2));
					$tpl->assign('fecha', $_REQUEST['fecha'][$i]);

					$mail->Body = $tpl->getOutputContent();

					$mail->IsHTML(true);

					@$mail->Send();
				}
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/ConciliacionDepositos.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
