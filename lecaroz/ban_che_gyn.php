<?php
include 'includes/dbstatus.php';
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';

function toInt($value) {
	return intval($value, 10);
}

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

$descripcion_error[1] = 'No hay resultados';

if (isset($_GET['generar'])) {
	$fecha = date('j') <= 4 ? date('d/m/Y', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('d/m/Y');

	$sql = '
		INSERT INTO
			gastos_caja (
				num_cia,
				cod_gastos,
				comentario,
				importe,
				tipo_mov,
				clave_balance,
				fecha,
				fecha_captura,
				iduser
			)
		SELECT
			num_cia,
			' . ($_SESSION['tipo_usuario'] == 2 ? '133' : '4') . ',
			concepto || \' (FOLIO \' || folio || \')\',
			importe,
			TRUE,
			FALSE,
			\'' . $fecha . '\',
			now()::date,
			' . $_SESSION['iduser'] . '
		FROM
			cheques c
			LEFT JOIN catalogo_companias cc
				USING (num_cia)
		WHERE
			num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
			/*
			*
			** [06-Abr-2010] Incluir oficinas y talleres
			*
			AND num_cia NOT IN (700, 800)
			*/
			AND fecha BETWEEN \'' . $_GET['f1'] . '\' AND \'' . $_GET['f2'] . '\'
			AND codgastos IN (134)
			AND importe > 0
			AND fecha_cancelacion IS NULL
	';
	$sql .= $_GET['c'] > 0 ? '
			AND cuenta = ' . $_GET['c'] . '
	' : '';
	$sql .= '
		ORDER BY
			c.num_cia,
			c.num_proveedor,
			c.fecha
	';
	$db->query($sql);

	die(header('location: ban_che_gyn.php?fecha1=' . $_GET['f1'] . '&fecha2=' . $_GET['f2'] . '&cuenta=' . $_GET['c'] . ($_GET['o'] == 1 ? '&opt=1' : '')));
}

if (isset($_REQUEST['exportar'])) {
	list($dia1, $mes1, $anio1) = array_map('toInt', explode('/', $_REQUEST['f1']));
	list($dia2, $mes2, $anio2) = array_map('toInt', explode('/', $_REQUEST['f2']));


	if ($_REQUEST['c'] == 2)
	{
		$sql = '
			SELECT
				num_cia
					AS "#",
				nombre
					AS "COMPAÑIA",
				CASE
					WHEN cuenta = 1 THEN
						\'BANORTE\'
					WHEN cuenta = 2 THEN
						\'SANTANDER\'
				END
					AS "BANCO",
				CASE
					WHEN cuenta = 1 THEN
						clabe_cuenta
					WHEN cuenta = 2 THEN
						clabe_cuenta2
				END
					AS "CUENTA",
				folio
					AS "FOLIO",
				\'SEMANA DEL ' . $dia1 . ' DE ' . mes_escrito($mes1, TRUE) . ' DE ' . $anio1 . ' AL ' . $dia2 . ' DE ' . mes_escrito($mes2, TRUE) . ' DE ' . $anio2 . '\'
					AS "CONCEPTO",
				importe
					AS "IMPORTE",
				CASE
					WHEN fecha_cancelacion IS NULL THEN
						\'\'
					ELSE
						\'CANDELADO\'
				END
					AS "ESTATUS"
			FROM
				cheques c
				LEFT JOIN catalogo_companias cc
					USING (num_cia)
			WHERE
				num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
				/*
				*
				** [06-Abr-2010] Incluir oficinas y talleres
				*
				AND num_cia NOT IN (700, 800)
				*/
				AND fecha BETWEEN \'' . $_GET['f1'] . '\' AND \'' . $_GET['f2'] . '\'
				AND codgastos IN (134)
				AND importe > 0
				AND fecha_cancelacion IS NULL
		';

		$sql .= $_GET['c'] > 0 ? '
				AND cuenta = ' . $_GET['c'] . '
		' : '';

		$sql .= '
			ORDER BY
				c.num_cia,
				c.num_proveedor,
				c.fecha
		';
	}
	else if ($_REQUEST['c'] == 1)
	{
		$sql = "
			SELECT
				CASE
					WHEN cuenta = 1 THEN
						clabe_cuenta
					WHEN cuenta = 2 THEN
						clabe_cuenta2
				END
					AS \"A\",
				cc.razon_social
					AS \"B\",
				importe
					AS \"C\",
				'MXP'
					AS \"D\",
				'C'
					AS \"E\",
				folio
					AS \"F\",
				SUBSTR('SEMANA DEL {$dia1} DE " . mes_escrito($mes1, TRUE) . " DE {$anio1} AL {$dia2} DE " . mes_escrito($mes2, TRUE) . " DE {$anio2}', 1, 12)
					AS \"G\"
			FROM
				cheques c
				LEFT JOIN catalogo_companias cc
					USING (num_cia)
			WHERE
				num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . "
				/*
				*
				** [06-Abr-2010] Incluir oficinas y talleres
				*
				AND num_cia NOT IN (700, 800)
				*/
				AND fecha BETWEEN '{$_GET['f1']}' AND '{$_GET['f2']}'
				AND codgastos IN (134)
				AND importe > 0
				AND fecha_cancelacion IS NULL
		";
	}

	$result = $db->query($sql);

	$data = '';

	if ($result) {
		if ($_REQUEST['c'] == 2)
		{
			$data .= '"CHEQUES EMITIDOS DEL ' . $dia1 . ' DE ' . mes_escrito($mes1, TRUE) . ' DE ' . $anio1 . ' AL ' . $dia2 . ' DE ' . mes_escrito($mes2, TRUE) . ' DE ' . $anio2 . '"' . "\r\n\r\n";

			$data .= '"' . utf8_decode(implode('","', array_keys($result[0]))) . '"' . "\r\n";

			$total = 0;

			foreach ($result as $rec) {
				$data .= utf8_decode(utf8_encode('"' . implode('","', $rec) . '"')) . "\r\n";

				$total += $rec['IMPORTE'];
			}

			$data .= ',,,,,"TOTAL","' . $total . '"' . "\r\n";

			$data .= ',,,,,"CANTIDAD DE CHEQUES","' . count($result) . '"' . "\r\n";
		}
		else if ($_REQUEST['c'] == 1)
		{
			foreach ($result as $i => $rec) {
				$data .= utf8_decode(utf8_encode('"' . implode('","', $rec) . '"')) . ',"' . ($i + 1) . '"' . "\r\n";
			}
		}
	}

	header('Content-Type: application/download');
	header('Content-Disposition: attachment; filename="gastos_y_nominas.csv"');

	echo $data;

	die;
}

$tpl = new TemplatePower('./plantillas/header.tpl');

$tpl->assignInclude('body', './plantillas/ban/ban_che_gyn.tpl');
$tpl->prepare();

$tpl->newBlock('menu');
$tpl->assign('menucnt', '$_SESSION[menu]_cnt.js');
$tpl->gotoBlock('_ROOT');

if (isset($_GET['cuenta'])) {
	$sql = '
		SELECT
			num_cia,
			nombre_corto
				AS
					nombre,
			folio,
			importe,
			CASE
				WHEN fecha_cancelacion IS NULL THEN
					1
				ELSE
					0
			END
				AS
					status
		FROM
				cheques
					c
			LEFT JOIN
				catalogo_companias
					cc
						USING
							(
								num_cia
							)
		WHERE
				num_cia
					BETWEEN
						' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
			/*
			*
			** [06-Abr-2010] Incluir oficinas y talleres
			*
			AND
				num_cia
					NOT IN
						(
							700,
							800
						)
			*/
			AND
				fecha
					BETWEEN
							\'' . $_GET['fecha1'] . '\'
						AND
							\'' . $_GET['fecha2'] . '\'
			AND
				codgastos
					IN
						(
							134
						)
			AND
				importe > 0
	';
	$sql .= $_GET['cuenta'] > 0 ? '
			AND
				cuenta = ' . $_GET['cuenta'] . '
	' : '';
	$sql .= !isset($_GET['opt']) ? '
			AND
				fecha_cancelacion IS NULL
	' : '';
	$sql .= '
		ORDER BY
			c.num_cia,
			c.num_proveedor,
			c.fecha
	';
	$result = $db->query($sql);

	if (!$result)
		die(header('location: ban_che_gyn.php?codigo_error=1'));

	$tpl->newBlock('listado');
	ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_GET['fecha1'], $fecha1);
	ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_GET['fecha2'], $fecha2);
	$tpl->assign('fecha1', $fecha1[1] . ' de ' . mes_escrito($fecha1[2]) . ' de ' . $fecha1[3]);
	$tpl->assign('fecha2', $fecha2[1] . ' de ' . mes_escrito($fecha2[2]) . ' de ' . $fecha2[3]);

	$tpl->assign('f1', $_GET['fecha1']);
	$tpl->assign('f2', $_GET['fecha2']);
	$tpl->assign('c', $_GET['cuenta']);
	$tpl->assign('o', isset($_GET['opt']) ? 1 : 0);

	$tpl->assign('disabled', !in_array($_SESSION['iduser'], array(1, 4, 28)) ? ' disabled' : '');

	$total = 0;
	$cheques = 0;
	$col = 0;
	foreach ($result as $reg) {
		if ($col == 0)
			$tpl->newBlock('fila');

		$tpl->assign('num_cia' . $col, $reg['num_cia']);
		$tpl->assign('nombre' . $col, $reg['nombre']);
		$tpl->assign('folio' . $col, $reg['folio']);
		$tpl->assign('importe' . $col, $reg['status'] == 1 ? number_format($reg['importe'], 2, '.', ',') : '<span style="color:#C00;">CANCELADO</span>');
		$total += $reg['status'] == 1 ? $reg['importe'] : 0;
		$col = $col == 1 ? 0 : 1;
	}

	$tpl->assign('listado.total', number_format($total, 2, '.', ','));
	$tpl->assign('listado.cheques', number_format(count($result)));
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');

if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
	$tpl->printToScreen();
	die();
}

$tpl->printToScreen();
?>
