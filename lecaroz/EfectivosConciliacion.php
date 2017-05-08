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

$__meses = array(
	1  => 'ENE',
	2  => 'FEB',
	3  => 'MAR',
	4  => 'ABR',
	5  => 'MAY',
	6  => 'JUN',
	7  => 'JUL',
	8  => 'AGO',
	9  => 'SEP',
	10 => 'OCT',
	11 => 'NOV',
	12 => 'DIC'
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

		case 'obtener_cia':
			$sql = '
				SELECT
					nombre_corto
						AS nombre_cia
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
					AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
			';

			$query = $db->query($sql);

			if ($query) {
				echo utf8_encode($query[0]['nombre_cia']);
			}

			break;

		case 'obtener_cia_sec':
			$sql = '
				SELECT
					nombre_corto
						AS nombre_cia
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia_sec'] . '
					AND rfc = (
						SELECT
							rfc
						FROM
							catalogo_companias
						WHERE
							num_cia = ' . $_REQUEST['num_cia'] . '
					)
					AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
			';

			$query = $db->query($sql);

			if ($query) {
				echo utf8_encode($query[0]['nombre_cia']);
			}

			break;

		case 'obtener_cia_destino':
			$sql = '
				SELECT
					nombre_corto
						AS nombre_cia
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia_destino'] . '
					AND num_cia <> ' . $_REQUEST['num_cia'] . '
					AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
			';

			$query = $db->query($sql);

			if ($query) {
				echo utf8_encode($query[0]['nombre_cia']);
			}

			break;

		case 'obtener_cia_oficina':
			$sql = '
				SELECT
					nombre_corto
						AS nombre_cia
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia_oficina'] . '
					AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
			';

			$query = $db->query($sql);

			if ($query) {
				echo utf8_encode($query[0]['nombre_cia']);
			}

			break;

		case 'validar_periodo':
			list($dia, $mes, $anio) = array_map('toInt', explode('/', $_REQUEST['fecha_corte']));

			$fecha_inicio = date('d/m/Y', mktime(0, 0, 0, $mes - 1, 1, $anio));
			$fecha_fin = date('d/m/Y', mktime(0, 0, 0, $mes + 2, 0, $anio));

			$sql = '
				SELECT
					CASE
						WHEN \'' . $_REQUEST['fecha'] . '\'::DATE < \'' . $fecha_inicio . '\'::DATE THEN
							-1
						WHEN \'' . $_REQUEST['fecha'] . '\'::DATE > \'' . $fecha_fin . '\'::DATE THEN
							1
						ELSE
							0
					END
						AS status
			';

			$result = $db->query($sql);

			echo json_encode(array(
				'status'       => intval($result[0]['status']),
				'fecha_inicio' => $fecha_inicio,
				'fecha_fin'    => $fecha_fin
			));

			break;

		case 'inicio':
			$tpl = new TemplatePower('plantillas/ban/EfectivosConciliacionInicio.tpl');
			$tpl->prepare();

			$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 2, date('Y'))));

			$admins = $db->query("SELECT
				idadministrador AS value,
				nombre_administrador AS text
			FROM
				catalogo_administradores
			ORDER BY
				text");

			if ($admins)
			{
				foreach ($admins as $a)
				{
					$tpl->newBlock("admin");

					$tpl->assign("value", $a['value']);
					$tpl->assign("text", $a['text']);
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'consultar':
			$fecha_corte = $_REQUEST['fecha'];

			list($dia_corte, $mes_corte, $anio_corte) = array_map('toInt', explode('/', $fecha_corte));

			$ultimo_dia_mes = intval(date('j', mktime(0, 0, 0, $mes_corte + 1, 0, $anio_corte)));

			$condiciones = array();

			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes_corte, 1, $anio_corte));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes_corte, $dia_corte, $anio_corte));

			if (isset($_REQUEST['next']) && $_REQUEST['next'] > 0) {
				$sql = '
					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						razon_social,
						(
							SELECT
								nombre_fin
							FROM
								encargados
							WHERE
								num_cia = cc.num_cia
							ORDER BY
								anio DESC,
								mes DESC
							LIMIT
								1
						)
							AS encargado,
						(
							SELECT
								nombre_operadora
							FROM
								catalogo_operadoras
							WHERE
								idoperadora = cc.idoperadora
						)
							AS operadora,
						turno_cometra
					FROM
						total_panaderias tp
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						num_cia >= ' . $_REQUEST['next'] . '
						AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
						AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
						' . (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0 ? "AND cc.idadministrador = {$_REQUEST['admin']}" : '') . '

					UNION

					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						razon_social,
						(
							SELECT
								nombre_fin
							FROM
								encargados
							WHERE
								num_cia = cc.num_cia
							ORDER BY
								anio DESC,
								mes DESC
							LIMIT
								1
						)
							AS encargado,
						(
							SELECT
								nombre_operadora
							FROM
								catalogo_operadoras
							WHERE
								idoperadora = cc.idoperadora
						)
							AS operadora,
						turno_cometra
					FROM
						total_companias tr
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						num_cia >= ' . $_REQUEST['next'] . '
						AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
						AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
						' . (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0 ? "AND cc.idadministrador = {$_REQUEST['admin']}" : '') . '

					UNION

					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						razon_social,
						(
							SELECT
								nombre_fin
							FROM
								encargados
							WHERE
								num_cia = cc.num_cia
							ORDER BY
								anio DESC,
								mes DESC
							LIMIT
								1
						)
							AS encargado,
						(
							SELECT
								nombre_operadora
							FROM
								catalogo_operadoras
							WHERE
								idoperadora = cc.idoperadora
						)
							AS operadora,
						turno_cometra
					FROM
						estado_cuenta ec
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						num_cia >= ' . $_REQUEST['next'] . '
						AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
						AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
						' . (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0 ? "AND cc.idadministrador = {$_REQUEST['admin']}" : '') . '
						AND cod_mov IN (1, 16, 44, 99)

					UNION

					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						razon_social,
						(
							SELECT
								nombre_fin
							FROM
								encargados
							WHERE
								num_cia = cc.num_cia
							ORDER BY
								anio DESC,
								mes DESC
							LIMIT
								1
						)
							AS encargado,
						(
							SELECT
								nombre_operadora
							FROM
								catalogo_operadoras
							WHERE
								idoperadora = cc.idoperadora
						)
							AS operadora,
						turno_cometra
					FROM
						otros_depositos od
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						num_cia >= ' . $_REQUEST['next'] . '
						AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
						AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
						' . (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0 ? "AND cc.idadministrador = {$_REQUEST['admin']}" : '') . '

					GROUP BY
						num_cia,
						nombre_cia,
						razon_social,
						encargado,
						operadora,
						turno_cometra

					ORDER BY
						num_cia

					LIMIT
						1
				';

				$query = $db->query($sql);

				if ($query) {
					$num_cia = $query[0]['num_cia'];
					$nombre_cia = $query[0]['nombre_cia'];
					$razon_social = $query[0]['razon_social'];
					$encargado = $query[0]['encargado'];
					$operadora = $query[0]['operadora'];
					$dia_extra = $query[0]['turno_cometra'] == 2 ? TRUE : FALSE;
				} else {
					$sql = '
						SELECT
							num_cia,
							nombre_corto
								AS nombre_cia,
							razon_social,
							(
								SELECT
									nombre_fin
								FROM
									encargados
								WHERE
									num_cia = cc.num_cia
								ORDER BY
									anio DESC,
									mes DESC
								LIMIT
									1
							)
								AS encargado,
							(
								SELECT
									nombre_operadora
								FROM
									catalogo_operadoras
								WHERE
									idoperadora = cc.idoperadora
							)
								AS operadora,
							turno_cometra
						FROM
							catalogo_companias cc
						WHERE
							num_cia = ' . $_REQUEST['num_cia'] . '
					';

					$query = $db->query($sql);

					if ($query) {
						$num_cia = $query[0]['num_cia'];
						$nombre_cia = $query[0]['nombre_cia'];
						$razon_social = $query[0]['razon_social'];
						$encargado = $query[0]['encargado'];
						$operadora = $query[0]['operadora'];
						$dia_extra = $query[0]['turno_cometra'] == 2 ? TRUE : FALSE;
					}
				}
			} else if (isset($_REQUEST['right'])) {
				$sql = '
					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						razon_social,
						LPAD(COALESCE(num_cia_primaria, num_cia)::VARCHAR, 4, \'0\') || \'-\' || LPAD(num_cia::VARCHAR, 4, \'0\')
							AS homoclave,
						(
							SELECT
								nombre_fin
							FROM
								encargados
							WHERE
								num_cia = cc.num_cia
							ORDER BY
								anio DESC,
								mes DESC
							LIMIT
								1
						)
							AS encargado,
						(
							SELECT
								nombre_operadora
							FROM
								catalogo_operadoras
							WHERE
								idoperadora = cc.idoperadora
						)
							AS operadora,
						turno_cometra
					FROM
						total_panaderias tp
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						LPAD(COALESCE(num_cia_primaria, num_cia)::VARCHAR, 4, \'0\') || \'-\' || LPAD(num_cia::VARCHAR, 4, \'0\') > LPAD((
							SELECT
								COALESCE(num_cia_primaria, num_cia)
							FROM
								catalogo_companias
							WHERE
								num_cia = ' . $_REQUEST['num_cia'] . '
						)::VARCHAR, 4, \'0\') || \'-\' || LPAD(' . $_REQUEST['num_cia'] . '::VARCHAR, 4, \'0\')
						AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
						AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
						' . (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0 ? "AND cc.idadministrador = {$_REQUEST['admin']}" : '') . '

					UNION

					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						razon_social,
						LPAD(COALESCE(num_cia_primaria, num_cia)::VARCHAR, 4, \'0\') || \'-\' || LPAD(num_cia::VARCHAR, 4, \'0\')
							AS homoclave,
						(
							SELECT
								nombre_fin
							FROM
								encargados
							WHERE
								num_cia = cc.num_cia
							ORDER BY
								anio DESC,
								mes DESC
							LIMIT
								1
						)
							AS encargado,
						(
							SELECT
								nombre_operadora
							FROM
								catalogo_operadoras
							WHERE
								idoperadora = cc.idoperadora
						)
							AS operadora,
						turno_cometra
					FROM
						total_companias tr
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						LPAD(COALESCE(num_cia_primaria, num_cia)::VARCHAR, 4, \'0\') || \'-\' || LPAD(num_cia::VARCHAR, 4, \'0\') > LPAD((
							SELECT
								COALESCE(num_cia_primaria, num_cia)
							FROM
								catalogo_companias
							WHERE
								num_cia = ' . $_REQUEST['num_cia'] . '
						)::VARCHAR, 4, \'0\') || \'-\' || LPAD(' . $_REQUEST['num_cia'] . '::VARCHAR, 4, \'0\')
						AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
						AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
						' . (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0 ? "AND cc.idadministrador = {$_REQUEST['admin']}" : '') . '

					UNION

					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						razon_social,
						LPAD(COALESCE(num_cia_primaria, num_cia)::VARCHAR, 4, \'0\') || \'-\' || LPAD(num_cia::VARCHAR, 4, \'0\')
							AS homoclave,
						(
							SELECT
								nombre_fin
							FROM
								encargados
							WHERE
								num_cia = cc.num_cia
							ORDER BY
								anio DESC,
								mes DESC
							LIMIT
								1
						)
							AS encargado,
						(
							SELECT
								nombre_operadora
							FROM
								catalogo_operadoras
							WHERE
								idoperadora = cc.idoperadora
						)
							AS operadora,
						turno_cometra
					FROM
						estado_cuenta ec
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						LPAD(COALESCE(num_cia_primaria, num_cia)::VARCHAR, 4, \'0\') || \'-\' || LPAD(num_cia::VARCHAR, 4, \'0\') > LPAD((
							SELECT
								COALESCE(num_cia_primaria, num_cia)
							FROM
								catalogo_companias
							WHERE
								num_cia = ' . $_REQUEST['num_cia'] . '
						)::VARCHAR, 4, \'0\') || \'-\' || LPAD(' . $_REQUEST['num_cia'] . '::VARCHAR, 4, \'0\')
						AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
						AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
						' . (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0 ? "AND cc.idadministrador = {$_REQUEST['admin']}" : '') . '
						AND cod_mov IN (1, 16, 44, 99)

					UNION

					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						razon_social,
						LPAD(COALESCE(num_cia_primaria, num_cia)::VARCHAR, 4, \'0\') || \'-\' || LPAD(num_cia::VARCHAR, 4, \'0\')
							AS homoclave,
						(
							SELECT
								nombre_fin
							FROM
								encargados
							WHERE
								num_cia = cc.num_cia
							ORDER BY
								anio DESC,
								mes DESC
							LIMIT
								1
						)
							AS encargado,
						(
							SELECT
								nombre_operadora
							FROM
								catalogo_operadoras
							WHERE
								idoperadora = cc.idoperadora
						)
							AS operadora,
						turno_cometra
					FROM
						otros_depositos od
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						LPAD(COALESCE(num_cia_primaria, num_cia)::VARCHAR, 4, \'0\') || \'-\' || LPAD(num_cia::VARCHAR, 4, \'0\') > LPAD((
							SELECT
								COALESCE(num_cia_primaria, num_cia)
							FROM
								catalogo_companias
							WHERE
								num_cia = ' . $_REQUEST['num_cia'] . '
						)::VARCHAR, 4, \'0\') || \'-\' || LPAD(' . $_REQUEST['num_cia'] . '::VARCHAR, 4, \'0\')
						AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
						AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
						' . (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0 ? "AND cc.idadministrador = {$_REQUEST['admin']}" : '') . '

					GROUP BY
						num_cia,
						nombre_cia,
						razon_social,
						homoclave,
						encargado,
						operadora,
						turno_cometra

					ORDER BY
						homoclave

					LIMIT
						1
				';

				$query = $db->query($sql);

				if ($query) {
					$num_cia = $query[0]['num_cia'];
					$nombre_cia = $query[0]['nombre_cia'];
					$razon_social = $query[0]['razon_social'];
					$encargado = $query[0]['encargado'];
					$operadora = $query[0]['operadora'];
					$dia_extra = $query[0]['turno_cometra'] == 2 ? TRUE : FALSE;
				} else {
					$sql = '
						SELECT
							num_cia,
							nombre_corto
								AS nombre_cia,
							razon_social,
							(
								SELECT
									nombre_fin
								FROM
									encargados
								WHERE
									num_cia = cc.num_cia
								ORDER BY
									anio DESC,
									mes DESC
								LIMIT
									1
							)
								AS encargado,
							(
								SELECT
									nombre_operadora
								FROM
									catalogo_operadoras
								WHERE
									idoperadora = cc.idoperadora
							)
								AS operadora,
							turno_cometra
						FROM
							catalogo_companias cc
						WHERE
							num_cia = ' . $_REQUEST['num_cia'] . '
					';

					$query = $db->query($sql);

					if ($query) {
						$num_cia = $query[0]['num_cia'];
						$nombre_cia = $query[0]['nombre_cia'];
						$razon_social = $query[0]['razon_social'];
						$encargado = $query[0]['encargado'];
						$operadora = $query[0]['operadora'];
						$dia_extra = $query[0]['turno_cometra'] == 2 ? TRUE : FALSE;
					}
				}
			} else if (isset($_REQUEST['left'])) {
				$sql = '
					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						razon_social,
						LPAD(COALESCE(num_cia_primaria, num_cia)::VARCHAR, 4, \'0\') || \'-\' || LPAD(num_cia::VARCHAR, 4, \'0\')
							AS homoclave,
						(
							SELECT
								nombre_fin
							FROM
								encargados
							WHERE
								num_cia = cc.num_cia
							ORDER BY
								anio DESC,
								mes DESC
							LIMIT
								1
						)
							AS encargado,
						(
							SELECT
								nombre_operadora
							FROM
								catalogo_operadoras
							WHERE
								idoperadora = cc.idoperadora
						)
							AS operadora,
						turno_cometra
					FROM
						total_panaderias tp
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						LPAD(COALESCE(num_cia_primaria, num_cia)::VARCHAR, 4, \'0\') || \'-\' || LPAD(num_cia::VARCHAR, 4, \'0\') < LPAD((
							SELECT
								COALESCE(num_cia_primaria, num_cia)
							FROM
								catalogo_companias
							WHERE
								num_cia = ' . $_REQUEST['num_cia'] . '
						)::VARCHAR, 4, \'0\') || \'-\' || LPAD(' . $_REQUEST['num_cia'] . '::VARCHAR, 4, \'0\')
						AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
						AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
						' . (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0 ? "AND cc.idadministrador = {$_REQUEST['admin']}" : '') . '

					UNION

					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						razon_social,
						LPAD(COALESCE(num_cia_primaria, num_cia)::VARCHAR, 4, \'0\') || \'-\' || LPAD(num_cia::VARCHAR, 4, \'0\')
							AS homoclave,
						(
							SELECT
								nombre_fin
							FROM
								encargados
							WHERE
								num_cia = cc.num_cia
							ORDER BY
								anio DESC,
								mes DESC
							LIMIT
								1
						)
							AS encargado,
						(
							SELECT
								nombre_operadora
							FROM
								catalogo_operadoras
							WHERE
								idoperadora = cc.idoperadora
						)
							AS operadora,
						turno_cometra
					FROM
						total_companias tr
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						LPAD(COALESCE(num_cia_primaria, num_cia)::VARCHAR, 4, \'0\') || \'-\' || LPAD(num_cia::VARCHAR, 4, \'0\') < LPAD((
							SELECT
								COALESCE(num_cia_primaria, num_cia)
							FROM
								catalogo_companias
							WHERE
								num_cia = ' . $_REQUEST['num_cia'] . '
						)::VARCHAR, 4, \'0\') || \'-\' || LPAD(' . $_REQUEST['num_cia'] . '::VARCHAR, 4, \'0\')
						AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
						AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
						' . (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0 ? "AND cc.idadministrador = {$_REQUEST['admin']}" : '') . '

					UNION

					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						razon_social,
						LPAD(COALESCE(num_cia_primaria, num_cia)::VARCHAR, 4, \'0\') || \'-\' || LPAD(num_cia::VARCHAR, 4, \'0\')
							AS homoclave,
						(
							SELECT
								nombre_fin
							FROM
								encargados
							WHERE
								num_cia = cc.num_cia
							ORDER BY
								anio DESC,
								mes DESC
							LIMIT
								1
						)
							AS encargado,
						(
							SELECT
								nombre_operadora
							FROM
								catalogo_operadoras
							WHERE
								idoperadora = cc.idoperadora
						)
							AS operadora,
						turno_cometra
					FROM
						estado_cuenta ec
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						LPAD(COALESCE(num_cia_primaria, num_cia)::VARCHAR, 4, \'0\') || \'-\' || LPAD(num_cia::VARCHAR, 4, \'0\') < LPAD((
							SELECT
								COALESCE(num_cia_primaria, num_cia)
							FROM
								catalogo_companias
							WHERE
								num_cia = ' . $_REQUEST['num_cia'] . '
						)::VARCHAR, 4, \'0\') || \'-\' || LPAD(' . $_REQUEST['num_cia'] . '::VARCHAR, 4, \'0\')
						AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
						AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
						' . (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0 ? "AND cc.idadministrador = {$_REQUEST['admin']}" : '') . '
						AND cod_mov IN (1, 16, 44, 99)

					UNION

					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						razon_social,
						LPAD(COALESCE(num_cia_primaria, num_cia)::VARCHAR, 4, \'0\') || \'-\' || LPAD(num_cia::VARCHAR, 4, \'0\')
							AS homoclave,
						(
							SELECT
								nombre_fin
							FROM
								encargados
							WHERE
								num_cia = cc.num_cia
							ORDER BY
								anio DESC,
								mes DESC
							LIMIT
								1
						)
							AS encargado,
						(
							SELECT
								nombre_operadora
							FROM
								catalogo_operadoras
							WHERE
								idoperadora = cc.idoperadora
						)
							AS operadora,
						turno_cometra
					FROM
						otros_depositos od
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						LPAD(COALESCE(num_cia_primaria, num_cia)::VARCHAR, 4, \'0\') || \'-\' || LPAD(num_cia::VARCHAR, 4, \'0\') < LPAD((
							SELECT
								COALESCE(num_cia_primaria, num_cia)
							FROM
								catalogo_companias
							WHERE
								num_cia = ' . $_REQUEST['num_cia'] . '
						)::VARCHAR, 4, \'0\') || \'-\' || LPAD(' . $_REQUEST['num_cia'] . '::VARCHAR, 4, \'0\')
						AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
						AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
						' . (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0 ? "AND cc.idadministrador = {$_REQUEST['admin']}" : '') . '

					GROUP BY
						num_cia,
						nombre_cia,
						razon_social,
						homoclave,
						encargado,
						operadora,
						turno_cometra

					ORDER BY
						homoclave DESC

					LIMIT
						1
				';

				$query = $db->query($sql);

				if ($query) {
					$num_cia = $query[0]['num_cia'];
					$nombre_cia = $query[0]['nombre_cia'];
					$razon_social = $query[0]['razon_social'];
					$encargado = $query[0]['encargado'];
					$operadora = $query[0]['operadora'];
					$dia_extra = $query[0]['turno_cometra'] == 2 ? TRUE : FALSE;
				} else {
					$sql = '
						SELECT
							num_cia,
							nombre_corto
								AS nombre_cia,
							razon_social,
							(
								SELECT
									nombre_fin
								FROM
									encargados
								WHERE
									num_cia = cc.num_cia
								ORDER BY
									anio DESC,
									mes DESC
								LIMIT
									1
							)
								AS encargado,
							(
								SELECT
									nombre_operadora
								FROM
									catalogo_operadoras
								WHERE
									idoperadora = cc.idoperadora
							)
								AS operadora,
							turno_cometra
						FROM
							catalogo_companias cc
						WHERE
							num_cia = ' . $_REQUEST['num_cia'] . '
					';

					$query = $db->query($sql);

					if ($query) {
						$num_cia = $query[0]['num_cia'];
						$nombre_cia = $query[0]['nombre_cia'];
						$razon_social = $query[0]['razon_social'];
						$encargado = $query[0]['encargado'];
						$operadora = $query[0]['operadora'];
						$dia_extra = $query[0]['turno_cometra'] == 2 ? TRUE : FALSE;
					}
				}
			} else {
				$sql = '
					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						razon_social,
						(
							SELECT
								nombre_fin
							FROM
								encargados
							WHERE
								num_cia = cc.num_cia
							ORDER BY
								anio DESC,
								mes DESC
							LIMIT
								1
						)
							AS encargado,
						(
							SELECT
								nombre_operadora
							FROM
								catalogo_operadoras
							WHERE
								idoperadora = cc.idoperadora
						)
							AS operadora,
						turno_cometra
					FROM
						total_panaderias tp
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
						AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
						' . (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0 ? "AND cc.idadministrador = {$_REQUEST['admin']}" : '') . '

					UNION

					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						razon_social,
						(
							SELECT
								nombre_fin
							FROM
								encargados
							WHERE
								num_cia = cc.num_cia
							ORDER BY
								anio DESC,
								mes DESC
							LIMIT
								1
						)
							AS encargado,
						(
							SELECT
								nombre_operadora
							FROM
								catalogo_operadoras
							WHERE
								idoperadora = cc.idoperadora
						)
							AS operadora,
						turno_cometra
					FROM
						total_companias tr
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
						AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
						' . (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0 ? "AND cc.idadministrador = {$_REQUEST['admin']}" : '') . '

					UNION

					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						razon_social,
						(
							SELECT
								nombre_fin
							FROM
								encargados
							WHERE
								num_cia = cc.num_cia
							ORDER BY
								anio DESC,
								mes DESC
							LIMIT
								1
						)
							AS encargado,
						(
							SELECT
								nombre_operadora
							FROM
								catalogo_operadoras
							WHERE
								idoperadora = cc.idoperadora
						)
							AS operadora,
						turno_cometra
					FROM
						estado_cuenta ec
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
						AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
						' . (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0 ? "AND cc.idadministrador = {$_REQUEST['admin']}" : '') . '
						AND cod_mov IN (1, 16, 44, 99)

					UNION

					SELECT
						num_cia,
						nombre_corto
							AS nombre_cia,
						razon_social,
						(
							SELECT
								nombre_fin
							FROM
								encargados
							WHERE
								num_cia = cc.num_cia
							ORDER BY
								anio DESC,
								mes DESC
							LIMIT
								1
						)
							AS encargado,
						(
							SELECT
								nombre_operadora
							FROM
								catalogo_operadoras
							WHERE
								idoperadora = cc.idoperadora
						)
							AS operadora,
						turno_cometra
					FROM
						otros_depositos od
						LEFT JOIN catalogo_companias cc
							USING (num_cia)
					WHERE
						fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
						AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
						' . (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0 ? "AND cc.idadministrador = {$_REQUEST['admin']}" : '') . '

					GROUP BY
						num_cia,
						nombre_cia,
						razon_social,
						encargado,
						operadora,
						turno_cometra

					ORDER BY
						num_cia

					LIMIT
						1
				';

				$query = $db->query($sql);

				if ($query) {
					$num_cia = $query[0]['num_cia'];
					$nombre_cia = $query[0]['nombre_cia'];
					$razon_social = $query[0]['razon_social'];
					$encargado = $query[0]['encargado'];
					$operadora = $query[0]['operadora'];
					$dia_extra = $query[0]['turno_cometra'] == 2 ? TRUE : FALSE;
				} else {
					$sql = '
						SELECT
							num_cia,
							nombre_corto
								AS nombre_cia,
							razon_social,
							(
								SELECT
									nombre_fin
								FROM
									encargados
								WHERE
									num_cia = cc.num_cia
								ORDER BY
									anio DESC,
									mes DESC
								LIMIT
									1
							)
								AS encargado,
							(
								SELECT
									nombre_operadora
								FROM
									catalogo_operadoras
								WHERE
									idoperadora = cc.idoperadora
							)
								AS operadora,
							turno_cometra
						FROM
							catalogo_companias cc
						WHERE
							num_cia = ' . $_REQUEST['num_cia'] . '
					';

					$query = $db->query($sql);

					if ($query) {
						$num_cia = $query[0]['num_cia'];
						$nombre_cia = $query[0]['nombre_cia'];
						$razon_social = $query[0]['razon_social'];
						$encargado = $query[0]['encargado'];
						$operadora = $query[0]['operadora'];
						$dia_extra = $query[0]['turno_cometra'] == 2 ? TRUE : FALSE;
					}
				}
			}

			$sql = '
				SELECT
					EXTRACT(DAY FROM generate_series)
						AS dia,
					COALESCE((
						SELECT
							efectivo
						FROM
							total_panaderias
						WHERE
							num_cia = ' . $num_cia . '
							AND fecha = generate_series::DATE
							AND efe = TRUE
							AND exp = TRUE
							AND pro = TRUE
							AND gas = TRUE
							AND pas = TRUE

						UNION

						SELECT
							efectivo
						FROM
							total_companias
						WHERE
							num_cia = ' . $num_cia . '
							AND fecha = generate_series::DATE
					), (
						SELECT
							importe
						FROM
							importe_efectivos
						WHERE
							num_cia = ' . $num_cia . '
							AND fecha = generate_series::DATE
					), (
						SELECT
							efectivo
						FROM
							total_panaderias tp
						WHERE
							num_cia = ' . $num_cia . '
							AND fecha = generate_series::DATE

						UNION

						SELECT
							efectivo
						FROM
							total_companias
						WHERE
							num_cia = ' . $num_cia . '
							AND fecha = generate_series::DATE
					), 0)
						AS efectivo,
					CASE
						/*
						@ Efectivo posterior a la fecha de corte
						*/
						WHEN COALESCE((
							SELECT
								fecha
							FROM
								total_panaderias
							WHERE
								num_cia = ' . $num_cia . '
								AND fecha = generate_series::DATE

							UNION

							SELECT
								fecha
							FROM
								total_companias
							WHERE
								num_cia = ' . $num_cia . '
								AND fecha = generate_series::DATE
						), generate_series::DATE) > \'' . $fecha_corte . '\'::DATE THEN
							-4
						/*
						@ Efectivo completo
						*/
						WHEN COALESCE((
							SELECT
								TRUE
							FROM
								total_panaderias
							WHERE
								num_cia = ' . $num_cia . '
								AND fecha = generate_series::DATE
								AND efe = TRUE
								AND exp = TRUE
								AND pro = TRUE
								AND gas = TRUE
								AND pas = TRUE

							UNION

							SELECT
								TRUE
							FROM
								total_companias
							WHERE
								num_cia = ' . $num_cia . '
								AND fecha = generate_series::DATE
						), FALSE) = TRUE THEN
							(
								CASE
									WHEN (
										SELECT
											status
										FROM
											total_panaderias
										WHERE
											num_cia = ' . $num_cia . '
											AND fecha = generate_series::DATE
											AND efe = TRUE
											AND exp = TRUE
											AND pro = TRUE
											AND gas = TRUE
											AND pas = TRUE

										UNION

										SELECT
											1
										FROM
											total_companias
										WHERE
											num_cia = ' . $num_cia . '
											AND fecha = generate_series::DATE
									) < 1 THEN
										-1
									ELSE
										1
								END
							)
						/*
						@ Efectivo directo
						*/
						WHEN COALESCE((
							SELECT
								TRUE
							FROM
								importe_efectivos
							WHERE
								num_cia = ' . $num_cia . '
								AND fecha = generate_series::DATE
						), FALSE) = TRUE THEN
							-2
						/*
						@ Efectivo incompleto
						*/
						WHEN COALESCE((
							SELECT
								TRUE
							FROM
								total_panaderias tp
							WHERE
								num_cia = ' . $num_cia . '
								AND fecha = generate_series::DATE

							UNION

							SELECT
								TRUE
							FROM
								total_companias
							WHERE
								num_cia = ' . $num_cia . '
								AND fecha = generate_series::DATE
						), FALSE) = TRUE THEN
							-3
						ELSE
							0
					END
						AS status,
					COALESCE((
						SELECT
							TRUE
						FROM
							facturas_electronicas
						WHERE
							num_cia = ' . $num_cia . '
							AND fecha = generate_series::DATE
							AND tipo = 1
							AND tscan IS NULL
						LIMIT 1
					), FALSE) AS facturado
				FROM
					generate_series(\'' . $fecha1 . '\'::DATE, \'' . $fecha2 . '\'::DATE' . ($dia_extra && $dia_corte < $ultimo_dia_mes ? ' + INTERVAL \'1 DAY\'' : '') . ', \'1 DAY\')
			';

			$query = $db->query($sql);

			$efectivos = array_fill_keys(range(1, $dia_corte), 0);
			$status_efectivos = array_fill_keys(range(1, $dia_corte + ($dia_extra && $dia_corte < $ultimo_dia_mes ? 1 : 0)), 0);
			$facturado_efectivos = array_fill_keys(range(1, $dia_corte + ($dia_extra && $dia_corte < $ultimo_dia_mes ? 1 : 0)), 'f');

			if ($query) {
				foreach ($query as $row) {
					if ($row['status'] != 0) {
						$efectivos[$row['dia']] = round(floatval($row['efectivo']), 2);
						$status_efectivos[$row['dia']] = $row['status'];
						$facturado_efectivos[$row['dia']] = $row['facturado'];
					}
				}
			}

			$sql = '
				SELECT
					EXTRACT(DAY FROM fecha)
						AS dia,
					id,
					cuenta
						AS banco,
					num_cia,
					(
						SELECT
							nombre_corto
						FROM
							catalogo_companias
						WHERE
							num_cia = ec.num_cia
					)
						AS nombre_cia,
					fecha,
					fecha_con,
					concepto,
					importe,
					cod_mov,
					CASE
						WHEN cuenta = 1 THEN
							(
								SELECT
									descripcion
								FROM
									catalogo_mov_bancos
								WHERE
									cod_mov = ec.cod_mov
								LIMIT
									1
							)
						WHEN cuenta = 2 THEN
							(
								SELECT
									descripcion
								FROM
									catalogo_mov_santander
								WHERE
									cod_mov = ec.cod_mov
								LIMIT
									1
							)
					END
						AS descripcion,
					CASE
						WHEN fecha_con IS NULL THEN
							FALSE
						ELSE
							TRUE
					END
						AS conciliado,
					CASE
						/*
						@ No es depósito de cometra
						*/
						WHEN concepto NOT LIKE \'%DEPOSITO COMETRA%\' THEN
							\'06f\'
						/*
						@ Depósito en otra cuenta
						*/
						WHEN num_cia_sec IS NOT NULL AND num_cia <> num_cia_sec THEN
							\'60f\'
						/*
						@ Depósito normal
						*/
						ELSE
							\'000\'
					END
						AS status
				FROM
					estado_cuenta ec
				WHERE
					COALESCE(num_cia_sec, num_cia) = ' . $num_cia . '
					AND fecha BETWEEN \'' . $fecha1 . '\'::DATE AND \'' . $fecha2 . '\'::DATE' . ($dia_extra && $dia_corte < $ultimo_dia_mes ? ' + INTERVAL \'1 DAY\'' : '') . '
					AND cod_mov IN (1, 16, 44, 99)
				ORDER BY
					dia,
					CASE
						WHEN cod_mov IN (1, 16) THEN
							1
						WHEN cod_mov IN (99) THEN
							2
						WHEN cod_mov IN (44) THEN
							3
					END,
					importe DESC
			';

			$query = $db->query($sql);

			$depositos = array_fill_keys(range(1, $dia_corte + ($dia_extra && $dia_corte < $ultimo_dia_mes ? 1 : 0)), array());
			$depositos_ids = array_fill_keys(range(1, $dia_corte + ($dia_extra && $dia_corte < $ultimo_dia_mes ? 1 : 0)), array());
			$depositos_conciliados = array_fill_keys(range(1, $dia_corte + ($dia_extra && $dia_corte < $ultimo_dia_mes ? 1 : 0)), array());
			$depositos_info = array_fill_keys(range(1, $dia_corte + ($dia_extra && $dia_corte < $ultimo_dia_mes ? 1 : 0)), array());
			$status_depositos = array_fill_keys(range(1, $dia_corte + ($dia_extra && $dia_corte < $ultimo_dia_mes ? 1 : 0)), array());

			$cheques = array_fill_keys(range(1, $dia_corte + ($dia_extra && $dia_corte < $ultimo_dia_mes ? 1 : 0)), array());
			$cheques_ids = array_fill_keys(range(1, $dia_corte + ($dia_extra && $dia_corte < $ultimo_dia_mes ? 1 : 0)), array());
			$cheques_conciliados = array_fill_keys(range(1, $dia_corte + ($dia_extra && $dia_corte < $ultimo_dia_mes ? 1 : 0)), array());
			$cheques_info = array_fill_keys(range(1, $dia_corte + ($dia_extra && $dia_corte < $ultimo_dia_mes ? 1 : 0)), array());
			$status_cheques = array_fill_keys(range(1, $dia_corte + ($dia_extra && $dia_corte < $ultimo_dia_mes ? 1 : 0)), array());

			$tarjetas = array_fill_keys(range(1, $dia_corte + ($dia_extra && $dia_corte < $ultimo_dia_mes ? 1 : 0)), array());
			$tarjetas_ids = array_fill_keys(range(1, $dia_corte + ($dia_extra && $dia_corte < $ultimo_dia_mes ? 1 : 0)), array());
			$tarjetas_conciliadas = array_fill_keys(range(1, $dia_corte + ($dia_extra && $dia_corte < $ultimo_dia_mes ? 1 : 0)), array());
			$tarjetas_info = array_fill_keys(range(1, $dia_corte + ($dia_extra && $dia_corte < $ultimo_dia_mes ? 1 : 0)), array());
			$status_tarjetas = array_fill_keys(range(1, $dia_corte + ($dia_extra && $dia_corte < $ultimo_dia_mes ? 1 : 0)), array());

			$depositos_columnas = 0;
			$cheques_columnas = 0;
			$tarjetas_columnas = 0;

			if ($query) {
				$dia = NULL;
				foreach ($query as $row) {
					if ($dia != $row['dia']) {
						$dia = $row['dia'];

						$depositos_dia = 0;
						$cheques_dia = 0;
						$tarjetas_dia = 0;
					}

					if (in_array($row['cod_mov'], array(1, 16))) {
						$depositos_dia++;

						if ($depositos_dia > $depositos_columnas) {
							$depositos_columnas = $depositos_dia;
						}
					} else if (in_array($row['cod_mov'], array(99))) {
						$cheques_dia++;

						if ($cheques_dia > $cheques_columnas) {
							$cheques_columnas = $cheques_dia;
						}
					} else if (in_array($row['cod_mov'], array(44))) {
						$tarjetas_dia++;

						if ($tarjetas_dia > $tarjetas_columnas) {
							$tarjetas_columnas = $tarjetas_dia;
						}
					}
				}

				if ($depositos_columnas > 0) {
					foreach ($depositos as $dia => $deposito) {
						$depositos[$dia] = array_fill(0, $depositos_columnas, 0);
						$depositos_ids[$dia] = array_fill(0, $depositos_columnas, NULL);
						$depositos_conciliados[$dia] = array_fill(0, $depositos_columnas, TRUE);
						$depositos_info[$dia] = array_fill(0, $depositos_columnas, array());
						$status_depositos[$dia] = array_fill(0, $depositos_columnas, '000');
					}
				}

				if ($cheques_columnas > 0) {
					foreach ($cheques as $dia => $cheque) {
						$cheques[$dia] = array_fill(0, $cheques_columnas, 0);
						$cheques_ids[$dia] = array_fill(0, $cheques_columnas, NULL);
						$cheques_conciliados[$dia] = array_fill(0, $cheques_columnas, TRUE);
						$cheques_info[$dia] = array_fill(0, $cheques_columnas, array());
						$status_cheques[$dia] = array_fill(0, $cheques_columnas, '000');
					}
				}

				if ($tarjetas_columnas > 0) {
					foreach ($tarjetas as $dia => $tarjeta) {
						$tarjetas[$dia] = array_fill(0, $tarjetas_columnas, 0);
						$tarjetas_ids[$dia] = array_fill(0, $tarjetas_columnas, NULL);
						$tarjetas_conciliados[$dia] = array_fill(0, $tarjetas_columnas, TRUE);
						$tarjetas_info[$dia] = array_fill(0, $tarjetas_columnas, array());
						$status_tarjetas[$dia] = array_fill(0, $tarjetas_columnas, '000');
					}
				}

				$dia = NULL;
				foreach ($query as $row) {
					if ($dia != $row['dia']) {
						$dia = $row['dia'];

						$depositos_cont = 0;
						$cheques_cont = 0;
						$tarjetas_cont = 0;
					}

					if (in_array($row['cod_mov'], array(1, 16))) {
						$depositos[$dia][$depositos_cont] = round(floatval($row['importe']), 2);
						$depositos_ids[$dia][$depositos_cont] = $row['id'];
						$depositos_conciliados[$dia][$depositos_cont] = $row['conciliado'] == 't' ? TRUE : FALSE;
						$depositos_info[$dia][$depositos_cont] = array(
							'id' => $row['id'],
							'num_cia' => $row['num_cia'],
							'nombre_cia' => $row['nombre_cia'],
							'banco' => $row['banco'] == 1 ? 'Banorte' : 'Santander',
							'cod' => $row['cod_mov'],
							'descripcion' => utf8_encode($row['descripcion']),
							'concepto' => utf8_encode($row['concepto']),
							'fecha' => $row['fecha'],
							'fecha_con' => $row['fecha_con']
						);
						$status_depositos[$dia][$depositos_cont] = $row['status'];

						$depositos_cont++;
					} else if (in_array($row['cod_mov'], array(99))) {
						$cheques[$dia][$cheques_cont] = round(floatval($row['importe']), 2);
						$cheques_ids[$dia][$cheques_cont] = $row['id'];
						$cheques_conciliados[$dia][$cheques_cont] = $row['conciliado'] == 't' ? TRUE : FALSE;
						$cheques_info[$dia][$cheques_cont] = array(
							'id' => $row['id'],
							'num_cia' => $row['num_cia'],
							'nombre_cia' => $row['nombre_cia'],
							'banco' => $row['banco'] == 1 ? 'Banorte' : 'Santander',
							'cod' => $row['cod_mov'],
							'descripcion' => utf8_encode($row['descripcion']),
							'concepto' => utf8_encode($row['concepto']),
							'fecha' => $row['fecha'],
							'fecha_con' => $row['fecha_con']
						);
						$status_cheques[$dia][$cheques_cont] = $row['status'];

						$cheques_cont++;
					} else if (in_array($row['cod_mov'], array(44))) {
						$tarjetas[$dia][$tarjetas_cont] = round(floatval($row['importe']), 2);
						$tarjetas_ids[$dia][$tarjetas_cont] = $row['id'];
						$tarjetas_conciliados[$dia][$tarjetas_cont] = $row['conciliado'] == 't' ? TRUE : FALSE;
						$tarjetas_info[$dia][$tarjetas_cont] = array(
							'id' => $row['id'],
							'num_cia' => $row['num_cia'],
							'nombre_cia' => $row['nombre_cia'],
							'banco' => $row['banco'] == 1 ? 'Banorte' : 'Santander',
							'cod' => $row['cod_mov'],
							'descripcion' => utf8_encode($row['descripcion']),
							'concepto' => utf8_encode($row['concepto']),
							'fecha' => $row['fecha'],
							'fecha_con' => $row['fecha_con']
						);
						$status_tarjetas[$dia][$tarjetas_cont] = $row['status'];

						$tarjetas_cont++;
					}
				}
			}

			if ($_REQUEST['tipo'] == 1) {
				$sql = '
					SELECT
						id,
						EXTRACT(DAY FROM fecha)
							AS dia,
						concepto,
						importe
					FROM
						otros_depositos
					WHERE
						num_cia = ' . $num_cia . '
						AND fecha BETWEEN \'' . $fecha1 . '\'::DATE AND \'' . $fecha2 . '\'::DATE' . ($dia_extra && $dia_corte < $ultimo_dia_mes ? ' + INTERVAL \'1 DAY\'' : '') . '
					ORDER BY
						dia,
						importe DESC
				';

				$query = $db->query($sql);

				$oficinas = array_fill_keys(range(1, $dia_corte + ($dia_extra && $dia_corte < $ultimo_dia_mes ? 1 : 0)), 0);
				$oficinas_info = array_fill_keys(range(1, $dia_corte + ($dia_extra && $dia_corte < $ultimo_dia_mes ? 1 : 0)), array());

				if ($query) {
					foreach ($query as $row) {
						$oficinas[$row['dia']] += round(floatval($row['importe']), 2);
						$oficinas_info[$row['dia']][] = array(
							'id' => $row['id'],
							'concepto' => utf8_encode($row['concepto']),
							'importe' => round(floatval($row['importe']), 2)
						);
					}
				}
			} else if ($_REQUEST['tipo'] == 2) {
				$sql = '
					SELECT
						SUM(importe)
							AS importe
					FROM
						otros_depositos
					WHERE
						num_cia = ' . $num_cia . '
						AND fecha BETWEEN \'' . $fecha1 . '\'::DATE AND \'' . $fecha2 . '\'::DATE
				';

				$query = $db->query($sql);

				$oficinas = 0;

				if ($query[0]['importe'] != 0) {
					$oficinas = round(floatval($query[0]['importe']), 2);
				}
			}

			if ($anio_corte >= 2015)
			{
				$sql = "
					SELECT
						EXTRACT(DAY FROM fecha)
							AS dia,
						SUM(
							CASE
								WHEN tipo_mov = TRUE THEN
									-importe
								ELSE
									importe
							END
						)
							AS faltante
					FROM
						estado_cuenta
					WHERE
						num_cia = {$num_cia}
						AND cod_mov IN (7, 13, 19, 48)
						AND fecha BETWEEN '{$fecha1}'::DATE AND '{$fecha2}'::DATE" . ($dia_extra && $dia_corte < $ultimo_dia_mes ? " + INTERVAL '1 DAY'" : '') . "
						AND fecha >= '01-01-2015'
					GROUP BY
						dia
					ORDER BY
						dia
				";
			}
			else
			{
				$sql = "
					SELECT
						EXTRACT(DAY FROM fecha)
							AS dia,
						SUM(
							CASE
								WHEN tipo = FALSE THEN
									-importe
								WHEN tipo = TRUE THEN
									importe
							END
						)
							AS faltante
					FROM
						faltantes_cometra
					WHERE
						num_cia = {$num_cia}
						AND fecha BETWEEN '{$fecha1}'::DATE AND '{$fecha2}'::DATE" . ($dia_extra && $dia_corte < $ultimo_dia_mes ? " + INTERVAL '1 DAY'" : '') . "
						AND fecha_con IS NULL
						AND fecha >= '19-11-2014'
					GROUP BY
						dia
					ORDER BY
						dia
				";
			}

			$query = $db->query($sql);

			$faltantes = array_fill_keys(range(1, $dia_corte + ($dia_extra && $dia_corte < $ultimo_dia_mes ? 1 : 0)), 0);

			if ($query) {
				foreach ($query as $row) {
					$faltantes[$row['dia']] += round(floatval($row['faltante']), 2);
				}
			}

			$tpl = new TemplatePower('plantillas/ban/EfectivosConciliacionResultado.tpl');
			$tpl->prepare();

			$tpl->assign('num_cia', $num_cia);
			$tpl->assign('fecha', $fecha_corte);
			$tpl->assign('nombre_cia', utf8_encode($nombre_cia));
			$tpl->assign('razon_social', htmlentities(utf8_encode('<span class="bold font12">' . $razon_social . '</span>')));
			$tpl->assign('mes_corte', strtoupper($_meses[$mes_corte]));
			$tpl->assign('anio_corte', $anio_corte);
			$tpl->assign('dia_corte', $dia_corte);
			$tpl->assign('encargado', utf8_encode($encargado));
			$tpl->assign('operadora', utf8_encode($operadora));

			if ($depositos_columnas > 0) {
				foreach (range(1, $depositos_columnas) as $i) {
					$tpl->newBlock('deposito_titulo');
					$tpl->assign('i', $i);
				}
			}

			if ($cheques_columnas > 0) {
				foreach (range(1, $cheques_columnas) as $i) {
					$tpl->newBlock('cheque_titulo');
					$tpl->assign('i', $i);
				}
			}

			if ($tarjetas_columnas > 0) {
				foreach (range(1, $tarjetas_columnas) as $i) {
					$tpl->newBlock('tarjeta_titulo');
					$tpl->assign('i', $i);
				}
			}

			$totales = array(
				'efectivo'        => 0,
				'depositos'       => $depositos_columnas > 0 ? array_fill(0, $depositos_columnas, 0) : array(),
				'cheques'         => $cheques_columnas > 0 ? array_fill(0, $cheques_columnas, 0) : array(),
				'tarjetas'        => $tarjetas_columnas > 0 ? array_fill(0, $tarjetas_columnas, 0) : array(),
				'oficina'         => 0,
				'faltantes'       => 0,
				'diferencia'      => 0,
				'total_depositos' => 0
			);

			foreach ($efectivos as $dia => $efectivo) {
				$tpl->newBlock('row');

				if ($status_efectivos[$dia] == -4) {
					$tpl->assign('row_style', 'background-color:#69f;');
				}

				$tpl->assign('dia', $dia);
				$tpl->assign('dia_row', '<span class="' . ($facturado_efectivos[$dia] == 't' ? 'bold blue' : '') . '">' . $dia . '</span>');
				$tpl->assign('efectivo', $efectivo != 0 ? number_format($efectivo, 2) : '&nbsp;');

				$totales['efectivo'] += $status_efectivos[$dia] != -4 ? $efectivo : 0;

				switch ($status_efectivos[$dia]) {
					case -1:
						$tpl->assign('status_efectivo', ' style="background-color:#f33;"');
						break;

					case -2:
						$tpl->assign('status_efectivo', ' style="background-color:#fc0;"');
						break;

					case -3:
						$tpl->assign('status_efectivo', ' style="background-color:#6c0;"');
						break;

					/*case -4:
						$tpl->assign('status_efectivo', ' style="background-color:#69f;"');
						break;*/
				}

				foreach ($depositos[$dia] as $i => $deposito) {
					$tooltip_info = $deposito != 0 ? '<table align="center" class="table"><thead><tr><th scope="col">Depositado en</th><th scope="col">Banco</th><th scope="col">C&oacute;digo</th><th scope="col">Concepto</th><th scope="col">Fecha</th><th scope="col">Conciliado</th><th scope="col"><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th></tr></thead><tbody><tr><td>' . $depositos_info[$dia][$i]['num_cia'] . ' ' . $depositos_info[$dia][$i]['nombre_cia'] . '</td><td align="center"><img src="/lecaroz/imagenes/' . $depositos_info[$dia][$i]['banco'] . '16x16.png" width="16" height="16" /></td><td>' . $depositos_info[$dia][$i]['cod'] . ' ' . $depositos_info[$dia][$i]['descripcion'] . '</td><td>' . $depositos_info[$dia][$i]['concepto'] . '</td><td align="center">' . $depositos_info[$dia][$i]['fecha'] . '</td><td align="center">' . $depositos_info[$dia][$i]['fecha_con'] . '</td><td align="center"><img src="/lecaroz/iconos/pencil.png" class="icono" alt="' . $depositos_info[$dia][$i]['id'] . '" name="mod" width="16" height="16" id="mod" />&nbsp;<img src="/lecaroz/iconos/money_cut.png" class="icono" alt="' . $depositos_info[$dia][$i]['id'] . '" name="div" width="16" height="16" id="div" />&nbsp;<img src="/lecaroz/iconos/refresh.png" class="icono" alt="' . $depositos_info[$dia][$i]['id'] . '" name="mov" width="16" height="16" id="mov" />&nbsp;<img src="/lecaroz/iconos/article.png" class="icono" alt="' . $depositos_info[$dia][$i]['id'] . '" name="carta" width="16" height="16" id="carta" />&nbsp;<img src="/lecaroz/iconos/article_text.png" class="icono" alt="' . $depositos_info[$dia][$i]['id'] . '" name="ficha" width="16" height="16" id="ficha" /></td></tr></tbody><tfoot><tr><td colspan="7">&nbsp;</td></tr></tfoot></table>' : '';

					$tpl->newBlock('deposito');
					$tpl->assign('deposito', $deposito != 0 ? '<a id="deposito" class="enlace" style="color:#' . $status_depositos[$dia][$i] . ';" info="' . htmlentities($tooltip_info) . '">' . number_format($deposito, 2) . '</a>' : '&nbsp;');

					if (!$depositos_conciliados[$dia][$i]) {
						$tpl->assign('deposito_no_conciliado', ' style="background-color:#f80;"');
					}

					$totales['depositos'][$i] += $status_efectivos[$dia] != -4 ? $deposito : 0;
				}

				foreach ($cheques[$dia] as $i => $cheque) {
					$tooltip_info = $cheque != 0 ? '<table align="center" class="table"><thead><tr><th scope="col">Depositado en</th><th scope="col">Banco</th><th scope="col">C&oacute;digo</th><th scope="col">Concepto</th><th scope="col">Fecha</th><th scope="col">Conciliado</th><th scope="col"><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th></tr></thead><tbody><tr><td>' . $cheques_info[$dia][$i]['num_cia'] . ' ' . $cheques_info[$dia][$i]['nombre_cia'] . '</td><td align="center"><img src="/lecaroz/imagenes/' . $cheques_info[$dia][$i]['banco'] . '16x16.png" width="16" height="16" /></td><td>' . $cheques_info[$dia][$i]['cod'] . ' ' . $cheques_info[$dia][$i]['descripcion'] . '</td><td>' . $cheques_info[$dia][$i]['concepto'] . '</td><td align="center">' . $cheques_info[$dia][$i]['fecha'] . '</td><td align="center">' . $cheques_info[$dia][$i]['fecha_con'] . '</td><td align="center"><img src="/lecaroz/iconos/pencil.png" class="icono" alt="' . $cheques_info[$dia][$i]['id'] . '" name="mod" width="16" height="16" id="mod" />&nbsp;<img src="/lecaroz/iconos/money_cut.png" class="icono" alt="' . $cheques_info[$dia][$i]['id'] . '" name="div" width="16" height="16" id="div" />&nbsp;<img src="/lecaroz/iconos/refresh.png" class="icono" alt="' . $cheques_info[$dia][$i]['id'] . '" name="mov" width="16" height="16" id="mov" />&nbsp;<img src="/lecaroz/iconos/article.png" class="icono" alt="' . $cheques_info[$dia][$i]['id'] . '" name="carta" width="16" height="16" id="carta" /></td></tr></tbody><tfoot><tr><td colspan="7">&nbsp;</td></tr></tfoot></table>' : '';

					$tpl->newBlock('cheque');
					$tpl->assign('cheque', $cheque != 0 ? '<a id="cheque" class="enlace" info="' . htmlentities($tooltip_info) . '">' . number_format($cheque, 2) . '</a>' : '&nbsp;');

					if (!$cheques_conciliados[$dia][$i]) {
						$tpl->assign('cheque_no_conciliado', ' style="background-color:#f80;"');
					}

					$totales['cheques'][$i] += $status_efectivos[$dia] != -4 ? $cheque : 0;
				}

				foreach ($tarjetas[$dia] as $i => $tarjeta) {
					$tooltip_info = $tarjeta != 0 ? '<table align="center" class="table"><thead><tr><th scope="col">Depositado en</th><th scope="col">Banco</th><th scope="col">C&oacute;digo</th><th scope="col">Concepto</th><th scope="col">Fecha</th><th scope="col">Conciliado</th><th scope="col"><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th></tr></thead><tbody><tr><td>' . $tarjetas_info[$dia][$i]['num_cia'] . ' ' . $tarjetas_info[$dia][$i]['nombre_cia'] . '</td><td align="center"><img src="/lecaroz/imagenes/' . $tarjetas_info[$dia][$i]['banco'] . '16x16.png" width="16" height="16" /></td><td>' . $tarjetas_info[$dia][$i]['cod'] . ' ' . $tarjetas_info[$dia][$i]['descripcion'] . '</td><td>' . $tarjetas_info[$dia][$i]['concepto'] . '</td><td align="center">' . $tarjetas_info[$dia][$i]['fecha'] . '</td><td align="center">' . $tarjetas_info[$dia][$i]['fecha_con'] . '</td><td align="center"><img src="/lecaroz/iconos/pencil.png" class="icono" alt="' . $tarjetas_info[$dia][$i]['id'] . '" name="mod" width="16" height="16" id="mod" />&nbsp;<img src="/lecaroz/iconos/money_cut.png" class="icono" alt="' . $tarjetas_info[$dia][$i]['id'] . '" name="div" width="16" height="16" id="div" />&nbsp;<img src="/lecaroz/iconos/refresh.png" class="icono" alt="' . $tarjetas_info[$dia][$i]['id'] . '" name="mov" width="16" height="16" id="mov" />&nbsp;<img src="/lecaroz/iconos/article.png" class="icono" alt="' . $tarjetas_info[$dia][$i]['id'] . '" name="carta" width="16" height="16" id="carta" /></td></tr></tbody><tfoot><tr><td colspan="7">&nbsp;</td></tr></tfoot></table>' : '';

					$tpl->newBlock('tarjeta');
					$tpl->assign('tarjeta', $tarjeta != 0 ? '<a id="tarjeta" class="enlace" info="' . htmlentities($tooltip_info) . '">' . number_format($tarjeta, 2) . '</a>' : '&nbsp;');

					if (!$tarjetas_conciliados[$dia][$i]) {
						$tpl->assign('tarjeta_no_conciliado', ' style="background-color:#f80;"');
					}

					$totales['tarjetas'][$i] += $status_efectivos[$dia] != -4 ? $tarjeta : 0;
				}

				if ($oficinas[$dia] != 0) {
					$tooltip_info = '<table class="table"><thead><tr><th scope="col">Concepto</th><th scope="col">Importe</th><th scope="col"><img src="/lecaroz/imagenes/tool16x16.png" width="16" height="16" /></th></tr></thead><tbody>';

					foreach ($oficinas_info[$dia] as $row) {
						$tooltip_info .= '<tr><td>' . $row['concepto'] . '</td><td align="right">' . number_format($row['importe'], 2) . '</td><td align="center"><img src="/lecaroz/iconos/pencil.png" alt="' . $row['id'] . '" name="mod_oficina" width="16" height="16" class="icono" id="mod_oficina" />&nbsp;<img src="/lecaroz/iconos/money_cut.png" alt="' . $row['id'] . '" name="div_oficina" width="16" height="16" class="icono" id="div_oficina" /></td></tr>';
					}

					$tooltip_info .= '</tbody><tfoot><tr><td colspan="3">&nbsp;</td></tr></tfoot></table>';
				}

				$tpl->assign('row.oficina', $oficinas[$dia] != 0 ? '<a id="oficina" class="enlace" info="' . htmlentities($tooltip_info) . '">' . number_format($oficinas[$dia], 2) . '</a>' : '&nbsp;');

				$totales['oficina'] += $status_efectivos[$dia] != -4 ? $oficinas[$dia] : 0;

				$tpl->assign('row.faltantes', $faltantes[$dia] != 0 ? '<span id="faltantes" class="' . ($faltantes[$dia] >= 0 ? 'blue' : 'red') . '">' . number_format($faltantes[$dia], 2) . '</span>' : '&nbsp;');

				$totales['faltantes'] += $status_efectivos[$dia] != -4 ? $faltantes[$dia] : 0;

				$total_depositos = $status_efectivos[$dia] != -4 ? array_sum($depositos[$dia]) + array_sum($cheques[$dia]) + array_sum($tarjetas[$dia]) + $oficinas[$dia] + $faltantes[$dia] : 0;

				$diferencia = $efectivo - $total_depositos;

				$totales['diferencia'] += $status_efectivos[$dia] != -4 ? $diferencia : 0;

				$totales['total_depositos'] += $status_efectivos[$dia] != -4 ? $total_depositos : 0;

				$tpl->assign('row.diferencia', $diferencia != 0 ? '<span class="' . ($diferencia >= 0 ? 'blue' : 'red') . '">' . number_format($diferencia, 2) . '</span>' : '&nbsp;');

				$tpl->assign('row.total_depositos', $total_depositos != 0 ? number_format($total_depositos, 2) : '&nbsp;');
			}

			$tpl->assign('_ROOT.total_efectivo', $totales['efectivo'] != 0 ? number_format($totales['efectivo'], 2) : '&nbsp;');

			foreach ($totales['depositos'] as $total_depositos) {
				$tpl->newBlock('total_depositos');
				$tpl->assign('total_depositos', number_format($total_depositos, 2));

				$prom_depositos = $total_depositos / $dia_corte;

				$tpl->newBlock('prom_depositos');
				$tpl->assign('prom_depositos', $prom_depositos != 0 ? number_format($prom_depositos, 2) : '&nbsp;');
			}

			foreach ($totales['cheques'] as $total_cheques) {
				$tpl->newBlock('total_cheques');
				$tpl->assign('total_cheques', number_format($total_cheques, 2));

				$prom_cheques = $total_cheques / $dia_corte;

				$tpl->newBlock('prom_cheques');
				$tpl->assign('prom_cheques', $prom_cheques != 0 ? number_format($prom_cheques, 2) : '&nbsp;');
			}

			foreach ($totales['tarjetas'] as $total_tarjetas) {
				$tpl->newBlock('total_tarjetas');
				$tpl->assign('total_tarjetas', number_format($total_tarjetas, 2));

				$prom_tarjetas = $total_tarjetas / $dia_corte;

				$tpl->newBlock('prom_tarjetas');
				$tpl->assign('prom_tarjetas', $prom_tarjetas != 0 ? number_format($prom_tarjetas, 2) : '&nbsp;');
			}

			$tpl->assign('_ROOT.total_oficina', $totales['oficina'] != 0 ? number_format($totales['oficina'], 2) : '&nbsp;');

			$tpl->assign('_ROOT.total_faltantes', $totales['faltantes'] != 0 ? number_format($totales['faltantes'], 2) : '&nbsp;');

			$tpl->assign('_ROOT.total_diferencia', round(abs($totales['diferencia']), 2) != 0 ? number_format($totales['diferencia'], 2) : '&nbsp;');

			$tpl->assign('_ROOT.total', $totales['total_depositos'] != 0 ? number_format($totales['total_depositos'], 2) : '&nbsp;');

			@$p_depositos = array_sum($totales['depositos']) * 100 / $totales['efectivo'];

			@$p_cheques = array_sum($totales['cheques']) * 100 / $totales['efectivo'];

			@$p_tarjetas = array_sum($totales['tarjetas']) * 100 / $totales['efectivo'];

			@$p_oficina = $totales['oficina'] * 100 / $totales['efectivo'];

			$prom_efectivo = $totales['efectivo'] / $dia_corte;

			$prom_total = $totales['total_depositos'] / $dia_corte;

			if ($p_depositos != 0) {
				$tpl->newBlock('p_depositos');
				$tpl->assign('p_depositos_columnas', $depositos_columnas);
				$tpl->assign('p_depositos', $p_depositos != 0 ? '%' .number_format($p_depositos, 2) : '&nbsp;');
			}

			if ($p_cheques != 0) {
				$tpl->newBlock('p_cheques');
				$tpl->assign('p_cheques_columnas', $cheques_columnas);
				$tpl->assign('p_cheques', $p_cheques != 0 ? '%' .number_format($p_cheques, 2) : '&nbsp;');
			}

			if ($p_tarjetas != 0) {
				$tpl->newBlock('p_tarjetas');
				$tpl->assign('p_tarjetas_columnas', $tarjetas_columnas);
				$tpl->assign('p_tarjetas', $p_tarjetas != 0 ? '%' .number_format($p_tarjetas, 2) : '&nbsp;');
			}

			$tpl->assign('_ROOT.p_oficina', $p_oficina != 0 ? '%' .number_format($p_oficina, 2) : '&nbsp;');

			$tpl->assign('_ROOT.prom_efectivo', $prom_efectivo != 0 ? number_format($prom_efectivo, 2) : '&nbsp;');

			$tpl->assign('_ROOT.prom_total', $prom_total != 0 ? number_format($prom_total, 2) : '&nbsp;');

			echo $tpl->getOutputContent();

			break;

		case 'datos_deposito':
			$sql = '
				SELECT
					id,
					COALESCE(num_cia_sec, num_cia)
						AS num_cia_sec,
					COALESCE((
						SELECT
							nombre_corto
						FROM
							catalogo_companias
						WHERE
							num_cia = ec.num_cia_sec
					), (
						SELECT
							nombre_corto
						FROM
							catalogo_companias
						WHERE
							num_cia = ec.num_cia
					))
						AS nombre_cia_sec,
					fecha,
					fecha_con
						AS conciliado,
					cod_mov
						AS codigo,
					concepto,
					importe,
					cuenta
						AS banco
				FROM
					estado_cuenta ec
				WHERE
					id = ' . $_REQUEST['id'] . '
			';

			$query = $db->query($sql);

			$row = $query[0];

			$row['id'] = intval($row['id']);
			$row['num_cia_sec'] = intval($row['num_cia_sec']);
			$row['nombre_cia_sec'] = utf8_encode($row['nombre_cia_sec']);
			$row['banco'] = intval($row['banco']);
			$row['codigo'] = intval($row['codigo']);
			$row['concepto'] = utf8_encode($row['concepto']);
			$row['importe'] = floatval($row['importe']);

			$sql = '
				SELECT
					cod_mov
						AS value,
					cod_mov || \' \' || descripcion
						AS text
				FROM
					' . ($row['banco'] == 1 ? 'catalogo_mov_bancos' : 'catalogo_mov_santander') . '
				WHERE
					tipo_mov = FALSE
				GROUP BY
					value,
					text
				ORDER BY
					value
			';

			$query = $db->query($sql);

			foreach ($query as $r) {
				$row['codigos'][] = array(
					'value' => intval($r['value']),
					'text'  => utf8_encode($r['text'])
				);
			}

			echo json_encode($row);

			break;

		case 'modificar_deposito':
			$sql = '
				UPDATE
					estado_cuenta
				SET
					fecha = \'' . $_REQUEST['fecha_deposito'] . '\',
					cod_mov = ' . $_REQUEST['codigo_deposito'] . '
				WHERE
					id = ' . $_REQUEST['id_deposito'] . '
			';

			$db->query($sql);

			break;

		case 'dividir_deposito':
			$sql = '';

			foreach ($_REQUEST['importe_deposito_dividir'] as $importe) {
				if (get_val($importe) > 0) {
					$sql .= '
						INSERT INTO
							estado_cuenta (
								num_cia,
								fecha,
								fecha_con,
								tipo_mov,
								importe,
								cod_mov,
								cuenta,
								iduser,
								timestamp,
								tipo_con,
								num_cia_sec,
								num_doc,
								comprobante,
								concepto
							)
							SELECT
								num_cia,
								fecha,
								fecha_con,
								tipo_mov,
								' . get_val($importe) . ',
								cod_mov,
								cuenta,
								iduser,
								timestamp,
								tipo_con,
								num_cia_sec,
								num_doc,
								comprobante,
								concepto
							FROM
								estado_cuenta
							WHERE
								id = ' . $_REQUEST['id_deposito_dividir'] . '
					' . ";\n";
				}
			}

			$sql .= '
				DELETE FROM
					estado_cuenta
				WHERE
					id = ' . $_REQUEST['id_deposito_dividir'] . '
			' . ";\n";

			$db->query($sql);

			break;

		case 'cambiar_deposito':
			$sql = '
				UPDATE
					estado_cuenta
				SET
					num_cia_sec = ' . ($_REQUEST['num_cia_sec'] > 0 && $_REQUEST['num_cia_sec'] != $_REQUEST['num_cia'] ? $_REQUEST['num_cia_sec'] : 'NULL') . '
				WHERE
					id = ' . $_REQUEST['id_deposito_cambiar'] . '
			';

			$db->query($sql);

			break;

		case 'carta_deposito':
			$sql = '
				INSERT INTO
					estado_cuenta (
						num_cia,
						fecha,
						tipo_mov,
						importe,
						cod_mov,
						concepto,
						cuenta,
						iduser,
						timestamp
					)
					SELECT
						num_cia,
						fecha,
						TRUE,
						importe,
						21,
						\'ERROR PERTENECE A LA CIA ' . $_REQUEST['num_cia_destino'] . '\',
						cuenta,
						' . $_SESSION['iduser'] . ',
						NOW()
					FROM
						estado_cuenta
					WHERE
						id = ' . $_REQUEST['id_deposito_carta'] . '
			' . ";\n";

			if (!$db->query('
				SELECT
					id
				FROM
					estado_cuenta
				WHERE
					(num_cia, fecha, tipo_mov, importe, cuenta)	IN (
						SELECT
							' . $_REQUEST['num_cia_destino'] . ',
							fecha,
							tipo_mov,
							importe,
							cuenta
						FROM
							estado_cuenta
						WHERE
							id = ' . $_REQUEST['id_deposito_carta'] . '
					)
			')) {
				$sql .= '
					INSERT INTO
						estado_cuenta (
							num_cia,
							fecha,
							tipo_mov,
							importe,
							cod_mov,
							concepto,
							cuenta,
							comprobante,
							iduser,
							timestamp
						)
						SELECT
							' . $_REQUEST['num_cia_destino'] . ',
							fecha,
							tipo_mov,
							importe,
							CASE
								WHEN ' . $_REQUEST['num_cia_destino'] . ' <= 300 OR ' . $_REQUEST['num_cia_destino'] . ' >= 900 THEN
									1
								ELSE
									16
							END
								AS cod_mov,
							concepto,
							cuenta,
							comprobante,
							' . $_SESSION['iduser'] . ',
							NOW()
						FROM
							estado_cuenta
						WHERE
							id = ' . $_REQUEST['id_deposito_carta'] . '
				' . ";\n";
			}

			$sql .= '
				UPDATE
					estado_cuenta
				SET
					cod_mov = 29,
					concepto = \'ERROR PERTENECE A LA CIA ' . $_REQUEST['num_cia_destino'] . '\'
				WHERE
					id = ' . $_REQUEST['id_deposito_carta'] . '
			' . ";\n";

			$db->query($sql);

			break;

		case 'carta_deposito_documento':
			$sql = '
				SELECT
					CASE
						WHEN cuenta = 1 THEN
							\'BANCO MERCANTIL DEL NORTE S.A.\'
						WHEN cuenta = 2 THEN
							\'SANTANDER\'
					END
						AS banco,
					fecha,
					importe,
					num_cia
						AS num_cia_dep,
					(
						SELECT
							nombre
						FROM
							catalogo_companias
						WHERE
							num_cia = ec.num_cia
					)
						AS nombre_dep,
					(
						SELECT
							CASE
								WHEN ec.cuenta = 1 THEN
									clabe_cuenta
								WHEN ec.cuenta = 2 THEN
									clabe_cuenta2
							END
						FROM
							catalogo_companias
						WHERE
							num_cia = ec.num_cia
					)
						AS cuenta_dep,
					' . $_REQUEST['num_cia_destino'] . '
						AS num_cia_des,
					(
						SELECT
							nombre
						FROM
							catalogo_companias
						WHERE
							num_cia = ' . $_REQUEST['num_cia_destino'] . '
					)
						AS nombre_des,
					(
						SELECT
							CASE
								WHEN ec.cuenta = 1 THEN
									clabe_cuenta
								WHEN ec.cuenta = 2 THEN
									clabe_cuenta2
							END
						FROM
							catalogo_companias
						WHERE
							num_cia = ' . $_REQUEST['num_cia_destino'] . '
					)
						AS cuenta_des
				FROM
					estado_cuenta ec
				WHERE
					id = ' . $_REQUEST['id'] . '
			';

			$result = $db->query($sql);

			$row = $result[0];

			$tpl = new TemplatePower('plantillas/ban/carta_bonificacion.tpl');
			$tpl->prepare();

			$tpl->assign('dia', date('d'));
			$tpl->assign('mes', $_meses[date('n')]);
			$tpl->assign('anio', date('Y'));

			$tpl->assign('banco', $row['banco']);
			$tpl->assign('contacto', strtoupper($_REQUEST['contacto']));

			$tpl->newBlock('fila');

			$tpl->assign('fecha', $row['fecha']);
			$tpl->assign('importe', number_format($row['importe'], 2));
			$tpl->assign('num_cia_dep', $row['num_cia_dep']);
			$tpl->assign('nombre_dep', $row['nombre_dep']);
			$tpl->assign('cuenta_dep', $row['cuenta_dep']);
			$tpl->assign('num_cia_des', $row['num_cia_des']);
			$tpl->assign('nombre_des', $row['nombre_des']);
			$tpl->assign('cuenta_des', $row['cuenta_des']);

			$tpl->printToScreen();

			break;

		case 'cometra_deposito':
			$folio_cometra = date('YmdHis');

			$sql = '
				INSERT INTO
					cometra (
						comprobante,
						num_cia,
						fecha,
						banco,
						cod_mov,
						importe,
						iduser_ins,
						tsins,
						concepto,
						tipo_comprobante,
						separar,
						total,
						reporte
					)
					SELECT
						' . $folio_cometra . ',
						num_cia,
						fecha,
						cuenta,
						21,
						importe,
						' . $_SESSION['iduser'] . ',
						NOW(),
						\'ERROR PERTENECE A LA CIA ' . $_REQUEST['num_cia_destino_cometra'] . '\',
						3,
						0,
						importe,
						TRUE
					FROM
						estado_cuenta
					WHERE
						id = ' . $_REQUEST['id_deposito_cometra'] . '
			' . ";\n";

			$sql .= '
				INSERT INTO
					cometra (
						comprobante,
						num_cia,
						fecha,
						banco,
						cod_mov,
						importe,
						iduser_ins,
						tsins,
						concepto,
						tipo_comprobante,
						separar,
						total,
						reporte,
						no_separar
					)
					SELECT
						' . $folio_cometra . ',
						' . $_REQUEST['num_cia_destino_cometra'] . ',
						fecha,
						cuenta,
						CASE
							WHEN ' . $_REQUEST['num_cia_destino_cometra'] . ' <= 300 OR ' . $_REQUEST['num_cia_destino_cometra'] . ' >= 900 THEN
								1
							ELSE
								16
						END
							AS cod_mov,
						importe,
						' . $_SESSION['iduser'] . ',
						NOW(),
						concepto,
						3,
						0,
						importe,
						TRUE,
						TRUE
					FROM
						estado_cuenta
					WHERE
						id = ' . $_REQUEST['id_deposito_cometra'] . '
			' . ";\n";

			$sql .= '
				UPDATE
					estado_cuenta
				SET
					cod_mov = 29,
					concepto = \'ERROR PERTENECE A LA CIA ' . $_REQUEST['num_cia_destino_cometra'] . '\'
				WHERE
					id = ' . $_REQUEST['id_deposito_cometra'] . '
			' . ";\n";

			$db->query($sql);

			echo $folio_cometra;

			break;

		case 'datos_oficina':
			$sql = '
				SELECT
					id,
					num_cia,
					(
						SELECT
							nombre_corto
						FROM
							catalogo_companias
						WHERE
							num_cia = od.num_cia
					)
						AS nombre_cia,
					fecha,
					importe
				FROM
					otros_depositos od
				WHERE
					id = ' . $_REQUEST['id'] . '
			';

			$query = $db->query($sql);

			$row = $query[0];

			$row['id'] = intval($row['id']);
			$row['num_cia'] = intval($row['num_cia']);
			$row['nombre_cia'] = utf8_encode($row['nombre_cia']);
			$row['importe'] = floatval($row['importe']);

			echo json_encode($row);

			break;

		case 'modificar_oficina':
			$sql = '
				UPDATE
					otros_depositos
				SET
					num_cia = ' . $_REQUEST['num_cia_oficina'] . ',
					fecha = \'' . $_REQUEST['fecha_oficina'] . '\'
				WHERE
					id = ' . $_REQUEST['id_oficina'] . '
			';

			$db->query($sql);

			break;

		case 'dividir_oficina':
			$sql = '';

			foreach ($_REQUEST['importe_oficina_dividir'] as $i => $importe) {
				if (get_val($importe) > 0 && $_REQUEST['fecha_oficina_dividir'][$i] != '') {
					$sql .= '
						INSERT INTO
							otros_depositos (
								num_cia,
								fecha,
								importe,
								fecha_cap,
								acumulado,
								concepto,
								iduser,
								tsins,
								tsmod,
								comprobante
							)
							SELECT
								num_cia,
								\'' . $_REQUEST['fecha_oficina_dividir'][$i] . '\',
								' . get_val($importe) . ',
								fecha_cap,
								acumulado,
								concepto,
								iduser,
								tsins,
								NOW(),
								comprobante
							FROM
								otros_depositos
							WHERE
								id = ' . $_REQUEST['id_oficina_dividir'] . '
					' . ";\n";
				}
			}

			$sql .= '
				DELETE FROM
					otros_depositos
				WHERE
					id = ' . $_REQUEST['id_oficina_dividir'] . '
			' . ";\n";

			$db->query($sql);

			break;

		case 'recorrer_depositos':
			$sql = '';

			foreach ($_REQUEST['dia'] as $dia) {
				$sql .= '
					UPDATE
						estado_cuenta
					SET
						fecha = fecha ' . ($_REQUEST['op'] == 'add' ? '+' : '-') . ' INTERVAL \'1 DAY\'
					WHERE
						num_cia = ' . $_REQUEST['num_cia'] . '
						AND fecha = \'' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], $dia, $_REQUEST['anio'])) . '\'
						AND cod_mov IN (1, 16)
				' . ";\n";
			}

			$db->query($sql);

			break;

		case 'facturas_electronicas':
			list($dia_corte, $mes_corte, $anio_corte) = array_map('toInt', explode('/', $_REQUEST['fecha']));

			/*
			@ Número de días del mes solicitado
			*/
			$dias_del_mes = date('j', mktime(0, 0, 0, $mes_corte + 1, 0, $anio_corte));

			/*
			@ Crear rango con los días del mes
			*/
			$dias = range(1, $dias_del_mes);

			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes_corte, 1, $anio_corte));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes_corte, $dia_corte, $anio_corte));

			/*
			@ Obtener desglose de puntos calientes
			*/
			$sql = '
				SELECT
					matriz,
					sucursal,
					porcentaje,
					CASE
						WHEN sucursal = matriz THEN
							2
						ELSE
							1
					END
						AS tipo
				FROM
					porcentajes_puntos_calientes
				ORDER BY
					matriz,
					tipo,
					sucursal
			';
			$result = $db->query($sql);

			$sucursales = array();

			if ($result) {
				/*
				@ Reordenar porcentajes
				*/
				foreach ($result as $rec) {
					$porcentajes[$rec['matriz']][] = array(
						'sucursal' => $rec['sucursal'],
						'porcentaje' => $rec['porcentaje'],
						'tipo' => $rec['tipo']
					);

					$sucursales[$rec['sucursal']] = $rec['matriz'];
				}
			}

			/*
			@ Condiciones [Estado de Cuenta]
			*/
			$condiciones1 = array();
			$condiciones2 = array();

			$condiciones1[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

			$condiciones1[] = 'cod_mov IN (1, 16, 44, 99)';

			$condiciones1[] = 'num_cia BETWEEN 1 AND 899';

			$condiciones1[] = !in_array($_SESSION['iduser'], array(1, 4)) ? ($_SESSION['tipo_usuario'] == 2 ? 'FALSE' : 'TRUE') : 'TRUE';

			$condiciones1[] = 'COALESCE(num_cia_sec, num_cia) = ' . $_REQUEST['num_cia'];

			$condiciones2[] = 'TRUE';

			if (count($sucursales) > 0) {
				$condiciones2[] = 'num_cia NOT IN (' . implode(', ', array_keys($sucursales)) . ')';
			}

			/*
			@ Obtener depositos del mes [Estado de Cuenta]
			*/
			$sql = '
				SELECT
					num_cia,
					dia,
					SUM(importe)
						AS importe
				FROM
					(
						SELECT
							COALESCE(num_cia_sec, num_cia)
								AS num_cia,
							EXTRACT(DAY FROM fecha)
								AS dia,
							importe
						FROM
							estado_cuenta
							LEFT JOIN catalogo_companias
								USING (num_cia)
						WHERE
							' . implode(' AND ', $condiciones1) . '
					) result
					LEFT JOIN catalogo_companias
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones2) . '
				GROUP BY
					num_cia,
					dia
			';

			/*
			@ Condiciones para desglose de puntos calientes
			*/
			$condiciones = array();

			$condiciones[] = !in_array($_SESSION['iduser'], array(1, 4)) ? ($_SESSION['tipo_usuario'] == 2 ? 'FALSE' : 'TRUE') : 'TRUE';

			$cias = array($_REQUEST['num_cia']);

			/*
			@ En el caso de sucursales, inlcuir matriz y todas sus filiales FORZOZAMENTE
			*/
			if (isset($sucursales[$_REQUEST['num_cia']])) {
				foreach ($porcentajes[$sucursales[$_REQUEST['num_cia']]] as $p) {
					$cias[] = $p['sucursal'];
				}
			}

			$cias = array_unique($cias);

			if (count($cias) > 0) {
				$condiciones[] = 'sucursal IN (' . implode(', ', $cias) . ')';
			}

			/*
			@ Obtener registros para desglose de puntos calientes
			*/
			$sql .= '
				UNION

				SELECT
					sucursal
						AS num_cia,
					EXTRACT(DAY FROM \'' . $fecha1 . '\'::DATE)
						AS dia,
					0
						AS importe
				FROM
					porcentajes_puntos_calientes ppc
					LEFT JOIN catalogo_companias cc
						ON (cc.num_cia = ppc.sucursal)
				WHERE
					' . implode(' AND ', $condiciones) . '
			';

			/*
			@ Condiciones [Ventas Zapaterias]
			*/
			$condiciones = array();

			$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

			$condiciones[] = 'num_cia BETWEEN 900 AND 998';

			$condiciones[] = !in_array($_SESSION['iduser'], array(1, 4)) ? ($_SESSION['tipo_usuario'] == 2 ? 'TRUE' : 'FALSE') : 'TRUE';

			$condiciones[] = 'num_cia = ' . $_REQUEST['num_cia'];

			/*
			@ Obtener depositos del mes [Ventas Zapaterias]
			*/
			$sql .= '
				UNION

				SELECT
					num_cia,
					EXTRACT(DAY FROM fecha)
						AS dia,
					importe
				FROM
					ventas_zapaterias vz
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
			';

			$sql .= '
				ORDER BY
					num_cia,
					dia
			';

			$result = $db->query($sql);

			/*
			@ Reordenar depósitos
			*/
			$lista_cias = array();

			$depositos = array();

			if ($result) {
				$num_cia = NULL;
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];

						$lista_cias[] = $num_cia;
					}

					$depositos[$rec['num_cia']][$rec['dia']] = floatval($rec['importe']);
				}
			}

			/*
			@ Hacer copia de los depositos para calculo de diferencia de efectivos
			*/

			$depositos_copia = $depositos;

			/*
			@ Desglosar ventas de puntos calientes
			*/
			foreach ($sucursales as $sucursal => $matriz) {
				if (isset($depositos[$sucursal])) {
					if (!isset($depositos_matriz[$matriz])) {
						/*
						@ Obtener depósitos de la matriz
						*/
						$sql = '
							SELECT
								dia,
								SUM(importe)
									AS importe
							FROM
								(
									SELECT
										EXTRACT(DAY FROM fecha)
											AS dia,
										importe
									FROM
										estado_cuenta
										LEFT JOIN catalogo_companias
											USING (num_cia)
									WHERE
										(
											(
												num_cia IN (' . $matriz . ')
												AND num_cia_sec IS NULL
											)
											OR num_cia_sec IN (' . $matriz . ')
										)
										AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
										AND cod_mov IN (1, 16, 44, 99)
								) result
							GROUP BY
								dia
							ORDER BY
								dia
						';

						$result = $db->query($sql);

						/*
						@ Reordenar los depósitos de la matriz
						*/
						if ($result) {
							foreach ($result as $rec) {
								$depositos_matriz[$matriz][$rec['dia']] = floatval($rec['importe']);

								$depositos_copia[$matriz][$rec['dia']] = floatval($rec['importe']);
							}
						}

						/*
						@ Obtener depósitos de sucursales
						*/
						$sql = '
							SELECT
								num_cia,
								EXTRACT(DAY FROM fecha)
									AS dia,
								importe
							FROM
								ventas_sucursales
							WHERE
								fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
								AND num_cia IN (
									SELECT
										sucursal
									FROM
										porcentajes_puntos_calientes
									WHERE
										matriz = ' . $matriz . '
								)
							ORDER BY
								num_cia,
								dia
						';

						$result = $db->query($sql);

						/*
						@ Reordenar los depósitos de las sucursales
						*/
						if ($result) {
							foreach ($result as $rec) {
								$depositos_sucursal[$rec['num_cia']][$rec['dia']] = array(
									'importe' => $rec['importe'],
									'status'  => TRUE
								);
							}
						}

						/*
						@ Desglosar depósitos entre todas las sucursales
						*/
						if (isset($depositos_matriz[$matriz])) {
							foreach ($depositos_matriz[$matriz] as $dia => $importe) {
								if ($importe > 0) {
									$total_sucursales = 0;

									foreach ($porcentajes[$matriz] as $por) {
										/*
										@ Es sucursal y no se ha generado la venta del día
										*/
										if ($por['tipo'] == 1 && !isset($depositos_sucursal[$por['sucursal']][$dia])) {
											$porcentaje = $por['porcentaje'] + round(mt_rand(-99, 99) / 100, 2);

											$importe_sucursal = round($importe * $porcentaje / 100, 2);

											$total_sucursales += $importe_sucursal;

											$depositos_sucursal[$por['sucursal']][$dia] = array(
												'importe' => $importe_sucursal,
												'status'  => FALSE
											);
										}
										/*
										@ Es sucursal y ya se generó la venta del día
										*/
										else if ($por['tipo'] == 1 && isset($depositos_sucursal[$por['sucursal']][$dia])) {
											$total_sucursales += $depositos_sucursal[$por['sucursal']][$dia]['importe'];
										}
										/*
										@ Es matriz y no se ha generado la venta del día
										*/
										else if ($por['tipo'] == 2 && !isset($depositos_sucursal[$por['sucursal']][$dia])) {
											$depositos_sucursal[$por['sucursal']][$dia] = array(
												'importe' => $importe - $total_sucursales,
												'status'  => FALSE
											);

											$total_sucursales += $importe - $total_sucursales;
										}
										/*
										@ Es matriz y ya se generó la venta del día
										*/
										else if ($por['tipo'] == 2 && isset($depositos_sucursal[$por['sucursal']][$dia])) {
											//$depositos_sucursal[$por['sucursal']][$dia] = $depositos_sucursal[$por['sucursal']][$dia];

											$total_sucursales += $depositos_sucursal[$por['sucursal']][$dia]['importe'];
										}
									}

									/*
									@ Comparar variación de los depósitos del día y la suma total de las sucursales
									*/
									if ($total_sucursales != $importe) {
										$dif = round($importe - $total_sucursales, 2);

										foreach ($porcentajes[$matriz] as $por) {
											if (!$depositos_sucursal[$por['sucursal']][$dia]['status']) {
												if ($dif > 0 || ($dif < 0 && abs($dif) < $depositos_sucursal[$por['sucursal']][$dia]['importe'])) {
													$depositos_sucursal[$por['sucursal']][$dia]['importe'] += $dif;

													break;
												}
												else if ($dif < 0 && abs($dif) > $depositos_sucursal[$por['sucursal']][$dia]['importe']) {
													$dif += round($depositos_sucursal[$por['sucursal']][$dia]['importe'] / 2, 2);

													$depositos_sucursal[$por['sucursal']][$dia]['importe'] -= round($depositos_sucursal[$por['sucursal']][$dia]['importe'] / 2, 2);
												}
											}
										}

										//$depositos_sucursal[$por['sucursal']][$dia] += $dif;
									}
								}
							}
						}
					}

					if (isset($depositos_sucursal[$sucursal])) {
						$depositos[$sucursal] = array();

						foreach ($depositos_sucursal[$sucursal] as $dia => $dep) {
							$depositos[$sucursal][$dia] = $dep['importe'];
						}

						//$depositos[$sucursal] = $depositos_sucursal[$sucursal];
					}
					else {
						unset($depositos[$sucursal]);
					}
				}
			}

			/*
			@ Condiciones para obtener facturas de clientes
			*/
			$condiciones = array();

			$condiciones[] = 'fecha_pago BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

			$condiciones[] = 'tipo = 2';

			$condiciones[] = 'status = 1';

			$cias = array($_REQUEST['num_cia']);

			/*
			@ En el caso de sucursales, inlcuir matriz y todas sus filiales FORZOZAMENTE
			*/
			if (isset($sucursales[$_REQUEST['num_cia']])) {
				foreach ($porcentajes[$sucursales[$_REQUEST['num_cia']]] as $p) {
					$cias[] = $p['sucursal'];
				}
			}

			$cias = array_unique($cias);

			if (count($cias) > 0) {
				$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
			}

			/*
			@ Obtener facturas de clientes del mes
			*/
			$sql = '
				SELECT
					num_cia,
					EXTRACT(DAY FROM fecha_pago)
						AS dia,
					COUNT(id)
						AS cantidad,
					SUM(total)
						AS importe
				FROM
					facturas_electronicas
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					num_cia,
					dia
				ORDER BY
					num_cia,
					dia
			';
			$result = $db->query($sql);

			$facturas_clientes = array();

			if ($result) {
				/*
				@ Reordenar facturas
				*/
				foreach ($result as $rec) {
					$facturas_clientes[$rec['num_cia']][$rec['dia']] = array(
						'cantidad' => $rec['cantidad'],
						'importe'  => $rec['importe']
					);
				}
			}

			/*
			@ Condiciones para obtener facturas de venta del mes
			*/
			$condiciones = array();

			$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

			$condiciones[] = 'tipo = 1';

			$condiciones[] = 'status = 1';

			$cias = array($_REQUEST['num_cia']);

			/*
			@ En el caso de sucursales, inlcuir matriz y todas sus filiales FORZOZAMENTE
			*/
			if (isset($sucursales[$_REQUEST['num_cia']])) {
				foreach ($porcentajes[$sucursales[$_REQUEST['num_cia']]] as $p) {
					$cias[] = $p['sucursal'];
				}
			}

			$cias = array_unique($cias);

			if (count($cias) > 0) {
				$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
			}

			/*
			@ Obtener facturas de venta mes
			*/
			$sql = '
				SELECT
					num_cia,
					EXTRACT(DAY FROM fecha)
						AS dia,
					iduser_ins
						AS iduser,
					COUNT(id)
						AS cantidad,
					SUM(total)
						AS importe
				FROM
					facturas_electronicas
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					num_cia,
					dia,
					iduser
				ORDER BY
					num_cia,
					dia
			';
			$result = $db->query($sql);

			$facturas_venta = array();

			if ($result) {
				/*
				@ Reordenar facturas
				*/
				foreach ($result as $rec) {
					$facturas_venta[$rec['num_cia']][$rec['dia']] = array(
						'cantidad' => $rec['cantidad'],
						'importe'  => $rec['importe'],
						'iduser'   => $rec['iduser']
					);
				}
			}

			/*
			@ Condiciones para obtener facturas electrónicas del mes [CANCELADAS]
			*/
			$condiciones = array();

			$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

			$condiciones[] = 'tipo = 1';

			$condiciones[] = 'status = 0';

			$cias = array($_REQUEST['num_cia']);

			/*
			@ En el caso de sucursales, inlcuir matriz y todas sus filiales FORZOZAMENTE
			*/
			if (isset($sucursales[$_REQUEST['num_cia']])) {
				foreach ($porcentajes[$sucursales[$_REQUEST['num_cia']]] as $p) {
					$cias[] = $p['sucursal'];
				}
			}

			$cias = array_unique($cias);

			if (count($cias) > 0) {
				$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
			}

			/*
			@ Obtener facturas electrónicas del mes generadas en panaderías [CANCELADAS]
			*/
			$sql = '
				SELECT
					num_cia,
					EXTRACT(DAY FROM fecha)
						AS dia,
					MAX(consecutivo)
						AS folio
				FROM
					facturas_electronicas
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					num_cia,
					dia
				ORDER BY
					num_cia,
					dia
			';
			$result = $db->query($sql);

			$facturas_venta_canceladas = array();

			if ($result) {
				/*
				@ Reordenar facturas
				*/
				foreach ($result as $rec) {
					$facturas_venta_canceladas[$rec['num_cia']][$rec['dia']] = $rec['folio'];
				}
			}

			/*
			@ Condiciones para obtener diferencia iniciales
			*/
			$condiciones = array();

			$condiciones[] = 'num_cia IN (' . implode(', ', $lista_cias) . ')';

			$condiciones[] = 'anio = ' . date('Y', mktime(0, 0, 0, $mes_corte, 0, $anio_corte));

			$condiciones[] = 'mes = ' . date('n', mktime(0, 0, 0, $mes_corte, 0, $anio_corte));

			/*
			@ Obtener diferencias iniciales
			*/
			$sql = '
				SELECT
					num_cia,
					diferencia
				FROM
					diferencia_ventas
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia
			';
			$result = $db->query($sql);

			$diferencias_iniciales = array();

			/*
			@ Reordenar diferencias iniciales
			*/
			if ($result) {
				foreach ($result as $rec) {
					$diferencias_iniciales[$rec['num_cia']] = floatval($rec['diferencia']);
				}
			}

			/*
			@ Condiciones para obtener compañías
			*/
			$condiciones = array();

			$condiciones[] = 'num_cia IN (' . implode(', ', $lista_cias) . ')';

			/*
			@ Obtener compañías
			*/
			$sql = '
				SELECT
					num_cia,
					nombre
						AS
							nombre_cia
				FROM
					catalogo_companias
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia
			';
			$result = $db->query($sql);

			$companias = array();

			foreach ($result as $rec) {
				$companias[$rec['num_cia']] = $rec['nombre_cia'];
			}

			/*
			@ Generar listado
			*/
			$tpl = new TemplatePower('plantillas/ban/EfectivosConciliacionFacturasElectronicas.tpl');
			$tpl->prepare();

			$index = 0;

			$ok = TRUE;

			foreach ($companias as $num_cia => $nombre_cia) {
				$tpl->newBlock('cia');
				$tpl->assign('num_cia', $num_cia);
				$tpl->assign('nombre_cia', utf8_encode($nombre_cia));

				$tpl->assign('diferencia_inicial', isset($diferencias_iniciales[$num_cia]) ? number_format($diferencias_iniciales[$num_cia], 2, '.', ',') : '0.00');

				$arrastre_diferencia = isset($diferencias_iniciales[$num_cia]) ? $diferencias_iniciales[$num_cia] : 0;

				$totales = array(
					'efectivo'  => 0,
					'clientes'   => 0,
					'venta'      => 0,
					'diferencia' => isset($diferencias_iniciales[$num_cia]) ? $diferencias_iniciales[$num_cia] : 0
				);

				$es_sucursal = in_array($num_cia, array_keys($sucursales));

				$periodo1 = '';
				$periodo2 = '';

				$color = FALSE;

				foreach ($dias as $dia) {
					$tpl->newBlock('dia');
					$tpl->assign('dia', $dia);
					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('index', $index);

					$index++;

					$_depositos = isset($depositos[$num_cia][$dia]) ? $depositos[$num_cia][$dia] : 0;

					$_facturas_transito = isset($facturas_transito[$num_cia][$dia]) ? $facturas_transito[$num_cia][$dia]['importe'] : 0;
					$_facturas_transito_cantidad = isset($facturas_transito[$num_cia][$dia]) ? $facturas_transito[$num_cia][$dia]['cantidad'] : 0;

					$_facturas_clientes = isset($facturas_clientes[$num_cia][$dia]) ? $facturas_clientes[$num_cia][$dia]['importe'] : 0;
					$_facturas_clientes_cantidad = isset($facturas_clientes[$num_cia][$dia]) ? $facturas_clientes[$num_cia][$dia]['cantidad'] : 0;

					$json_data = array(
						'num_cia'           => intval($num_cia),
						'anio'              => intval($anio_corte, 10),
						'mes'               => intval($mes_corte, 10),
						'dia'               => intval($dia),
						'depositos'         => round(floatval($_depositos), 2),
						'facturas_clientes' => round(floatval($_facturas_clientes), 2),
						'facturas_venta'    => 0,
						'diferencia'        => 0,
						'arrastre'          => 0,
						'sustituye'         => 0
					);

					if (isset($facturas_venta[$num_cia][$dia])) {
						$_facturas_venta = $facturas_venta[$num_cia][$dia]['importe'];
						$_facturas_venta_cantidad = $facturas_venta[$num_cia][$dia]['cantidad'];

						$_diferencia = $_depositos - $_facturas_transito - $_facturas_clientes - $_facturas_venta;

						$json_data['facturas_venta'] = round(floatval($_facturas_venta), 2);
						$json_data['diferencia'] = round(floatval($_diferencia), 2);

						$arrastre_diferencia += $_diferencia;

						$tpl->assign('disabled', ' disabled="disabled"');
					} else if ($_depositos > 0) {
						$_diferencia = $_depositos - $_facturas_transito - $_facturas_clientes;

						$_facturas_venta = $_diferencia + $arrastre_diferencia;

						/*
						@ La factura de venta es negativa, poner importe en 0 y arrastrar diferencia
						*/
						if ($_facturas_venta < 0) {
							$_diferencia = $_facturas_venta;

							$_facturas_venta = 0;
						} else {
							$_facturas_venta_cantidad = 1;

							$_diferencia = $_depositos - $_facturas_transito - $_facturas_clientes - $_facturas_venta;
						}

						$json_data['facturas_venta'] = round(floatval($_facturas_venta), 2);
						$json_data['diferencia'] = round(floatval($_diferencia), 2);
						$json_data['arrastre'] = round(floatval($arrastre_diferencia), 2);

						/*
						@ La factura sustituye a una cancelada
						*/
						if (isset($facturas_venta_canceladas[$num_cia][$dia])) {
							$json_data['sustituye'] = intval($facturas_venta_canceladas[$num_cia][$dia]);
						}

						if ($_facturas_venta == 0) {
							$arrastre_diferencia = $_diferencia;
						} else {
							$arrastre_diferencia += $_diferencia;
						}

						if ( ! $ok || $_facturas_venta <= 0)
						{
							$tpl->assign('disabled', ' disabled="disabled"');

							$ok = FALSE;
						}
						else
						{
							$tpl->assign('checked', ' checked="checked"');
						}

						if ( ! $ok)
						{
							$tpl->assign('row_color', ' style="background-color:#FFD7D6;"');
						}

						if ($periodo1 == '') {
							$periodo1 = date('j/n/Y', mktime(0, 0, 0, $mes_corte, $dia, $anio_corte));
						}
					} else {
						$tpl->assign('disabled', $dia > $dia_corte ? ' disabled="disabled"' : '');

						$_facturas_venta = 0;
						$_diferencia = 0;
					}

					$totales['efectivo'] += $_depositos;
					$totales['clientes'] += $_facturas_clientes;
					$totales['venta'] += $_facturas_venta;
					$totales['diferencia'] += $_diferencia;

					$periodo2 = date('j/n/Y', mktime(0, 0, 0, $mes_corte, $dia_corte, $mes_corte));

					if ($_depositos != 0) {
						$tpl->assign('efectivo', ($es_sucursal ? '<span style="float:left">*&nbsp;</span>' : '') . number_format($_depositos, 2));
					} else if ($dia <= $dia_corte) {
						$tpl->assign('efectivo', '<a id="efectivo-' . $num_cia . '-' . $dia . '" title="' . $num_cia . '|' . $dia . '" class="enlace blue">----------</a>');
					} else {
						$tpl->assign('efectivo', '&nbsp;');
					}

					$tpl->assign('param', htmlentities(json_encode(array(
						'num_cia' => intval($num_cia),
						'anio'    => intval($anio_corte),
						'mes'     => intval($mes_corte),
						'dia'     => intval($dia)
					))));
					$tpl->assign('clientes', $_facturas_clientes != 0 ? '<span style="float:left;" class="font6">(' . $_facturas_clientes_cantidad . ')</span>&nbsp;' . number_format($_facturas_clientes, 2) : '&nbsp;');
					$tpl->assign('venta', $_facturas_venta != 0 ? '<span style="float:left;" class="font6">(' . $_facturas_venta_cantidad . ')</span>&nbsp;' . number_format($_facturas_venta, 2) : '&nbsp;');
					$tpl->assign('diferencia', round($_diferencia, 2) != 0 ? number_format($_diferencia, 2) : '&nbsp;');
					$tpl->assign('color_diferencia', $_diferencia >= 0 ? 'blue' : 'red');

					$tpl->assign('datos', htmlentities(json_encode($json_data)));
				}

				foreach ($totales as $key => $value) {
					$tpl->assign('cia.' . $key, $value != 0 ? number_format($value, 2) : '&nbsp;');

					if ($key == 'diferencia') {
						$tpl->assign('cia.color', $value >= 0 ? 'blue' : 'red');
					}
				}

				$diferencia_venta = $totales['efectivo'] - $totales['clientes'] - $totales['venta'];

				$tpl->assign('cia.diferencia', $diferencia_venta != 0 ? number_format($diferencia_venta, 2) : '&nbsp;');

				$tpl->assign('cia.arrastre_diferencia', $arrastre_diferencia);

				$tpl->assign('cia.periodo', $periodo1 . '|' . $periodo2);
			}

			echo $tpl->getOutputContent();

		break;

		case 'modificar_facturas_clientes':
			$sql = '
				SELECT
					id,
					fecha
						AS fecha_emision,
					fecha_pago,
					CONCAT_WS(\'-\', (
						SELECT
							serie
						FROM
							facturas_electronicas_series
						WHERE
							num_cia = fe.num_cia
							AND tipo_serie = fe.tipo_serie
							AND fe.consecutivo BETWEEN folio_inicial AND folio_final
					), consecutivo)
						AS factura,
					nombre_cliente
						AS cliente,
					rfc,
					importe,
					iva,
					total
				FROM
					facturas_electronicas fe
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
					AND fecha_pago = \'' . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], $_REQUEST['dia'], $_REQUEST['anio'])) . '\'
					AND tipo = 2
					AND status = 1
				ORDER BY
					consecutivo
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ban/EfectivosConciliacionModificarFacturasClientes.tpl');
			$tpl->prepare();

			if ($result) {
				$total = 0;

				foreach ($result as $rec) {
					$tpl->newBlock('factura');

					$tpl->assign('id', $rec['id']);
					$tpl->assign('fecha_emision', $rec['fecha_emision']);
					$tpl->assign('fecha_pago', $rec['fecha_pago']);
					$tpl->assign('factura', $rec['factura']);
					$tpl->assign('cliente', utf8_encode($rec['cliente']));
					$tpl->assign('rfc', utf8_encode($rec['rfc']));
					$tpl->assign('importe', number_format($rec['importe'], 2));
					$tpl->assign('iva', $rec['iva'] != 0 ? number_format($rec['iva'], 2) : '&nbsp;');
					$tpl->assign('total', number_format($rec['total'], 2));

					$total += $rec['total'];
				}

				$tpl->assign('_ROOT.total', number_format($total, 2));
			}

			echo $tpl->getOutputContent();

			break;

		case 'generar_facturas_electronicas':
			include_once('includes/class.facturas.v3.inc.php');

			// $dbf = new DBclass('pgsql://lecaroz:pobgnj@192.168.1.251:5432/ob_lecaroz', 'autocommit=yes');

			$fac = new FacturasClass();

			if ($fac->ultimoCodigoError() < 0) {
				return -1;
			}

			/*
			@ Obtener desglose de puntos calientes
			*/
			$sql = '
				SELECT
					sucursal
						AS
							num_cia
				FROM
					porcentajes_puntos_calientes
				ORDER BY
					num_cia
			';
			$result = $db->query($sql);

			/*
			@ Reordenar porcentajes
			*/
			$sucursales = array();
			if ($result) {
				foreach ($result as $rec) {
					$sucursales[] = $rec['num_cia'];
				}
			}

			/*
			@ Generar reporte
			*/
			$tpl = new TemplatePower('plantillas/ban/EfectivosConciliacionFacturasElectronicasReporte.tpl');
			$tpl->prepare();

			if (isset($_REQUEST['datos'])) {
				$num_cia = NULL;

				foreach ($_REQUEST['datos'] as $json_string) {
					$data = json_decode($json_string);

					if ($num_cia != $data->num_cia) {
						$num_cia = $data->num_cia;

						$status_emisor = TRUE;

						$sql = '
							SELECT
								nombre
									AS
										nombre_cia,
								aplica_iva
									AS
										aplicar_iva,
								email
							FROM
								catalogo_companias
							WHERE
								num_cia = ' . $num_cia . '
						';
						$cia = $db->query($sql);

						$piva = $cia[0]['aplicar_iva'] == 't' ? 16 : 0;

						$tpl->newBlock('emisor');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $cia[0]['nombre_cia']);

						$total_cia = 0;
					}

					$fecha = date('d/m/Y', mktime(0, 0, 0, $data->mes, $data->dia, $data->anio));
					$hora = '22:00:00';

					$importe = round($data->facturas_venta / (1 + $piva / 100), 2);
					$iva = $data->facturas_venta - $importe;
					$total = $data->facturas_venta;

					$total_cia += $total;

					$tpl->assign('emisor.total', number_format($total_cia, 2, '.', ','));

					if (!$status_emisor) {
						$tpl->newBlock('row');
						$tpl->assign('fecha', $fecha);
						$tpl->assign('importe', number_format($total, 2, '.', ','));

						$tpl->assign('estatus', '<span class="red">Error en d&iacute;as anteriores</span>');
					} else if ($total < 0) {
						$tpl->newBlock('row');
						$tpl->assign('fecha', $fecha);
						$tpl->assign('importe', number_format($total, 2, '.', ','));

						$tpl->assign('estatus', '<span class="red">El importe de la factura para este d&iacute;a no puede ser negativo o cero</span>');

						$status_emisor = FALSE;
					} else if ($total == 0) {
						$tpl->newBlock('row');
						$tpl->assign('fecha', $fecha);
						$tpl->assign('importe', number_format($total, 2, '.', ','));

						$tpl->assign('estatus', 'No se gener&oacute; factura para este d&iacute;a');

						$sql = '
							INSERT INTO
								facturas_electronicas
									(
										num_cia,
										fecha,
										hora,
										tipo_serie,
										consecutivo,
										tipo,
										clave_cliente,
										nombre_cliente,
										rfc,
										calle,
										no_exterior,
										no_interior,
										colonia,
										localidad,
										referencia,
										municipio,
										estado,
										pais,
										codigo_postal,
										importe,
										iva,
										total,
										iduser_ins,
										fecha_pago
									)
								VALUES
									(
										' . $num_cia . ',
										\'' . $fecha . '\',
										\'' . $hora . '\',
										1,
										0,
										1,
										1,
										\'PUBLICO EN GENERAL\',
										\'XAXX010101000\',
										\'\',
										\'\',
										\'\',
										\'\',
										\'\',
										\'\',
										\'\',
										\'\',
										\'\',
										\'\',
										0,
										0,
										0,
										' . $_SESSION['iduser'] . ',
										\'' . $fecha . '\'
									)
						' . ";\n";

						/*
						@ Actualizar diferencia del emisor
						*/
						if ($id = $db->query('
							SELECT
								id
							FROM
								diferencia_ventas
							WHERE
									num_cia = ' . $num_cia . '
								AND
									anio = ' . $data->anio . '
								AND
									mes = ' . $data->mes . '
						')) {
							$sql .= '
								UPDATE
									diferencia_ventas
								SET
									diferencia = ' . $data->diferencia . ',
									iduser_mod = ' . $_SESSION['iduser'] . ',
									tsmod = now()
								WHERE
									id = ' . $id[0]['id'] . '
							' . ";\n";
						} else {
							$sql .= '
								INSERT INTO
									diferencia_ventas
										(
											num_cia,
											anio,
											mes,
											diferencia,
											iduser_ins,
											iduser_mod
										)
									VALUES
										(
											' . $num_cia . ',
											' . $data->anio . ',
											' . $data->mes . ',
											' . $data->diferencia . ',
											' . $_SESSION['iduser'] . ',
											' . $_SESSION['iduser'] . '
										)
							' . ";\n";
						}

						$db->query($sql);
					} else {
						/*
						@ Obtener entidad de la compañía
						*/

						$sql = '
							SELECT
								estado
							FROM
								catalogo_companias
							WHERE
								num_cia = ' . $num_cia . '
						';

						$estado = $db->query($sql);

						$datos = array(
							'cabecera' => array (
								'num_cia'               => $num_cia,
								'clasificacion'         => 1,
								'fecha'                 => $fecha,
								'hora'                  => $hora,
								'clave_cliente'         => 1,
								'nombre_cliente'        => 'PUBLICO EN GENERAL',
								'rfc_cliente'           => 'XAXX010101000',
								'calle'                 => '',
								'no_exterior'           => '',
								'no_interior'           => '',
								'colonia'               => '',
								'localidad'             => '',
								'referencia'            => '',
								'municipio'             => '',
								'estado'                => $estado[0]['estado'],
								'pais'                  => 'MEXICO',
								'codigo_postal'         => '',
								'email'                 => '',
								'observaciones'         => $data->sustituye > 0 ? ' (SUSTITUYE A LA FACTURA ' . $data->sustituye . ')' : '',
								'importe'               => $importe,
								'porcentaje_descuento'  => 0,
								'descuento'             => 0,
								'porcentaje_iva'        => $piva,
								'importe_iva'           => $iva,
								'aplicar_retenciones'   => 'N',
								'importe_retencion_isr' => 0,
								'importe_retencion_iva' => 0,
								'total'                 => $total
							),
							'consignatario' => array (
								'nombre'        => '',
								'rfc'           => '',
								'calle'         => '',
								'no_exterior'   => '',
								'no_interior'   => '',
								'colonia'       => '',
								'localidad'     => '',
								'referencia'    => '',
								'municipio'     => '',
								'estado'        => '',
								'pais'          => '',
								'codigo_postal' => ''
							),
							'detalle' => array(
								array (
									'clave'            => 1,
									'descripcion'      => 'VENTA DEL DIA ' . $fecha,
									'cantidad'         => 1,
									'unidad'           => 'NO APLICA',
									'precio'           => $importe,
									'importe'          => $importe,
									'descuento'        => 0,
									'porcentaje_iva'   => $piva > 0 ? 16 : 0,
									'importe_iva'      => $iva,
									'numero_pedimento' => '',
									'fecha_entrada'    => '',
									'aduana_entrada'   => ''
								)
							)
						);

						$tpl->newBlock('row');
						$tpl->assign('cliente', 'PUBLICO EN GENERAL');
						$tpl->assign('fecha', $fecha);
						$tpl->assign('importe', number_format($total, 2, '.', ','));

						$folio_reservado = $fac->recuperarFolio($num_cia, 1, $fecha);

						if (($status = $fac->generarFactura($_SESSION['iduser'], $num_cia, 1, $datos, $folio_reservado)) < 0) {
							$tpl->assign('estatus', '<span class="red">' . $fac->ultimoError() . '</span>');

							$status_emisor = FALSE;
						} else {
							$pieces = explode('-', $status);
							$folio = $pieces[1];

							$tpl->assign('factura', $folio);
							$tpl->assign('estatus', '<span class="green">OK</span>');

							if ($folio_reservado > 0) {
								$fac->utilizarFolio($_SESSION['iduser'], $num_cia, 1, $folio_reservado);
							}

							if (in_array($num_cia, $sucursales)) {
								$sql = '
									INSERT INTO
										ventas_sucursales
											(
												num_cia,
												fecha,
												importe
											)
										VALUES
											(
												' . $num_cia . ',
												\'' . $fecha . '\',
												' . $data->depositos . '
											)
								' . ";\n";

								$db->query($sql);
							}

							/*
							@ Actualizar diferencia del emisor
							*/
							if ($id = $db->query('
								SELECT
									id
								FROM
									diferencia_ventas
								WHERE
										num_cia = ' . $num_cia . '
									AND
										anio = ' . $data->anio . '
									AND
										mes = ' . $data->mes . '
							')) {
								$sql = '
									UPDATE
										diferencia_ventas
									SET
										diferencia = ' . $data->diferencia . ',
										iduser_mod = ' . $_SESSION['iduser'] . ',
										tsmod = now()
									WHERE
										id = ' . $id[0]['id'] . '
								' . ";\n";
							} else {
								$sql = '
									INSERT INTO
										diferencia_ventas
											(
												num_cia,
												anio,
												mes,
												diferencia,
												iduser_ins,
												iduser_mod
											)
										VALUES
											(
												' . $num_cia . ',
												' . $data->anio . ',
												' . $data->mes . ',
												' . $data->diferencia . ',
												' . $_SESSION['iduser'] . ',
												' . $_SESSION['iduser'] . '
											)
								' . ";\n";
							}

							//$db->query($sql);
						}
					}
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'enviar_email':
			list($dia_corte, $mes_corte, $anio_corte) = array_map('toInt', explode('/', $_REQUEST['fecha']));

			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes_corte, 1, $anio_corte));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes_corte, $dia_corte, $anio_corte));

			$dias_mes = intval(date('j', mktime(0, 0, 0, $mes_corte + 1, 0, $anio_corte)));

			$condiciones = array();

			$condiciones[] = "num_cia = {$_REQUEST['num_cia']}";

			$sql = '
				SELECT
					ca.idadministrador
						AS admin,
					ca.nombre_administrador
						AS nombre_admin,
					ca.email
						AS email_admin,
					cc.num_cia,
					cc.num_cia_primaria,
					cc.email
						AS email_cia,
					cc.nombre,
					cc.nombre_corto,
					cc.turno_cometra
				FROM
					catalogo_companias cc
					LEFT JOIN catalogo_administradores ca
						USING (idadministrador)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					admin,
					num_cia_primaria,
					num_cia
			';

			$result = $db->query($sql);

			if ($result) {
				foreach ($result as $rec) {
					$data = array(
						'num_cia'     => $rec['num_cia'],
						'nombre'      => $rec['nombre'],
						'alias'       => $rec['nombre_corto'],
						'email_admin' => $rec['email_admin'],
						'email_cia'   => $rec['email_cia'],
						'dias'        => range(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0)),
						'status'      => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), FALSE),
						'cometra'     => $rec['turno_cometra'],
						'efectivo'    => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'deposito'    => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'mayoreo'     => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'oficina'     => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'faltante'    => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'diferencia'  => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'total'       => array_fill(1, /*$dias_mes*/$dia_corte + ($rec['turno_cometra'] == 2 && $dia_corte < $dias_mes ? 1 : 0), 0),
						'totales'     => array(
							'efectivo'   => 0,
							'deposito'   => 0,
							'mayoreo'    => 0,
							'oficina'    => 0,
							'faltante'   => 0,
							'diferencia' => 0,
							'total'      => 0
						),
						'promedios'  => array(
							'efectivo'   => 0,
							'deposito'   => 0,
							'mayoreo'    => 0,
							'oficina'    => 0,
							'total'      => 0
						)
					);
				}

				$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

				$sql = '
					SELECT
						idadministrador
							AS admin,
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha)
							AS dia,
						(efe AND exp AND pro AND gas AND pas)
							AS status,
						ROUND(efectivo::NUMERIC, 2)
							AS efectivo
					FROM
						total_panaderias
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
						/*AND (efe AND exp AND pro AND gas AND pas) = TRUE*/

					UNION

					SELECT
						idadministrador
							AS admin,
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha),
						TRUE,
						ROUND(efectivo::NUMERIC, 2)
							AS efectivo
					FROM
						total_companias
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '

					UNION

					SELECT
						idadministrador
							AS admin,
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha),
						(venta > 0),
						ROUND(efectivo::NUMERIC, 2)
							AS efectivo
					FROM
						total_zapaterias
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
						AND venta > 0

					/*UNION

					SELECT
						idadministrador
							AS admin,
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha),
						FALSE,
						ROUND(importe::NUMERIC, 2)
					FROM
						importe_efectivos
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
						AND (num_cia, fecha) NOT IN (
							SELECT
								num_cia,
								fecha
							FROM
								total_panaderias
							WHERE
								' . implode(' AND ', $condiciones) . '
								AND (efe AND exp AND pro AND gas AND pas) = TRUE

							UNION

							SELECT
								num_cia,
								fecha
							FROM
								total_companias
							WHERE
								' . implode(' AND ', $condiciones) . '

							UNION

							SELECT
								num_cia,
								fecha
							FROM
								total_zapaterias
							WHERE
								' . implode(' AND ', $condiciones) . '
								AND venta > 0
						)*/

					ORDER BY
						admin,
						num_cia_primaria,
						num_cia,
						dia
				';

				$result = $db->query($sql);

				if ($result) {
					$importes_agosto_2012 = array(
						21  => 10000,
						31  => 4000,
						32  => 3000,
						34  => 5000,
						49  => 3000,
						73  => 3000,
						79  => 2000,
						121 => 5000
					);

					$sql = '
						SELECT
							idadministrador
								AS admin,
							num_cia,
							EXTRACT(DAY FROM fecha)
								AS dia,
							importe
						FROM
							cometra
							LEFT JOIN catalogo_companias
								USING (num_cia)
						WHERE
							comprobante IN (41355658, 40759126)
						ORDER BY
							num_cia,
							fecha,
							importe
					';

					$tmp = $db->query($sql);

					$importes_septiembre_2012 = array();

					if ($tmp) {
						foreach ($tmp as $t) {
							$importes_septiembre_2012[$t['num_cia']][$t['dia']] = $t['importe'];
						}
					}

					foreach ($result as $rec) {
						$data['efectivo'][$rec['dia']] = floatval($rec['efectivo']);
						$data['status'][$rec['dia']] = $rec['status'] == 't' ? 1 : -1;

						/*
						@ [12-Sep-2012] Sumar al efectivo los siguientes importes para el mes de agosto de 2012 (solo del dia 1 al 30)
						@
						@ 21 - 10,000.00
						@ 31 -  4,000.00
						@ 32 -  3,000.00
						@ 34 -  5,000.00
						@ 49 -  3,000.00
						@ 73 -  3,000.00
						@ 79 -  2,000.00
						@ 121 - 5,000.00
						*/

						if (in_array($rec['num_cia'], array(
							21,
							31,
							32,
							34,
							49,
							73,
							79,
							121
							))
							&& $anio_corte == 2012
							&& $mes_corte == 8
							&& $rec['dia'] < 31) {
							$data['efectivo'][$rec['dia']] += $importes_agosto_2012[$rec['num_cia']];
						}

						/*
						@ [04-Oct-2012] Sumar al efectivo los siguientes importes para el mes de septiembre de 2012 (solo del dia 1 al 30)
						@
						*/

						if (in_array($rec['num_cia'], array(
							31,
							32,
							33,
							34,
							73,
							121
							))
							&& $anio_corte == 2012
							&& $mes_corte == 9
							&& isset($importes_septiembre_2012[$rec['num_cia']][$rec['dia']])) {
							$data['efectivo'][$rec['dia']] += $importes_septiembre_2012[$rec['num_cia']][$rec['dia']];
						}

						/*
						@ [13-Nov-2012] Sumar al efectivo los siguientes importes para el mes de octubre de 2012
						*/

						if (in_array($rec['num_cia'], array(
							33
							))
							&& $anio_corte == 2012
							&& $mes_corte == 10) {
							$data['efectivo'][$rec['dia']] += 10000;
						}

						/*
						@ [12-Dic-2012] Sumar al efectivo los siguientes importes para el mes de noviembre de 2012
						*/

						if (in_array($rec['num_cia'], array(
							33
							))
							&& $anio_corte == 2012
							&& $mes_corte == 11) {
							$data['efectivo'][$rec['dia']] += 10000;
						}

						/*
						@ [13-Nov-2013] Sumar al efectivo los siguientes importes para el mes de octubre de 2013
						*/

						if (in_array($rec['num_cia'], array(
							49,
							57,
							67,
							34
							))
							&& $anio_corte == 2013
							&& $mes_corte == 10) {
							$data['efectivo'][$rec['dia']] += 10000;
						}
					}
				}

				$sql = '
					SELECT
						idadministrador
							AS admin,
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha)
							AS dia,
						ROUND(importe::NUMERIC, 2)
							AS capturado
					FROM
						importe_efectivos
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia_primaria,
						num_cia,
						dia
				';

				$result = $db->query($sql);

				if ($result) {
					foreach ($result as $rec) {
						if ($data['status'][$rec['dia']] <= 0) {
							$data['efectivo'][$rec['dia']] = floatval($rec['capturado']);
							$data['status'][$rec['dia']] = -2;
						}
					}
				}

				$condiciones_otros = $condiciones;

				$condiciones_otros[] = 'comprobante IS NOT NULL';

				$condiciones_otros[] = '(concepto NOT LIKE \'COMPLEMENTO VENTA%\' OR concepto IS NULL)';

				$sql = '
					SELECT
						idadministrador
							AS admin,
						num_cia,
						num_cia_primaria,
						comprobante,
						EXTRACT(DAY FROM fecha)
							AS dia,
						SUM(importe)
							AS importe
					FROM
						otros_depositos
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones_otros) . '
					GROUP BY
						admin,
						num_cia_primaria,
						num_cia,
						dia,
						comprobante
					ORDER BY
						num_cia,
						dia
				';

				$result = $db->query($sql);

				$otros = array();

				if ($result) {
					foreach ($result as $rec) {
						$otros[$rec['dia']][$rec['comprobante']] = array(
							'num_cia_primaria' => $rec['num_cia_primaria'],
							'importe'          => floatval($rec['importe']),
							'status'           => FALSE
						);
					}
				}

				$condiciones_otros = $condiciones;

				$condiciones_otros[] = 'comprobante IS NULL';

				$condiciones_otros[] = '(concepto NOT LIKE \'COMPLEMENTO VENTA%\' OR concepto IS NULL)';

				$sql = '
					SELECT
						idadministrador
							AS admin,
						num_cia,
						num_cia_primaria,
						EXTRACT(DAY FROM fecha)
							AS dia,
						SUM(importe)
							AS importe
					FROM
						otros_depositos
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones_otros) . '
					GROUP BY
						admin,
						num_cia_primaria,
						num_cia,
						dia
					ORDER BY
						num_cia_primaria,
						num_cia,
						dia
				';

				$result = $db->query($sql);

				if ($result) {
					foreach ($result as $rec) {
						$data['oficina'][$rec['dia']] = floatval($rec['importe']);
					}
				}

				// Faltantes y sobrantes

				if ($anio_corte >= 2015)
				{
					$condiciones_faltantes = $condiciones;

					$condiciones_faltantes[] = "cod_mov IN (7, 13, 19, 48)";

					$condiciones_faltantes[] = "fecha >= '01-01-2015'";

					$sql = "
						SELECT
							idadministrador
								AS admin,
							num_cia,
							num_cia_primaria,
							EXTRACT(DAY FROM fecha)
								AS dia,
							SUM(
								CASE
									WHEN tipo_mov = TRUE THEN
										-importe
									WHEN tipo_mov = FALSE THEN
										importe
								END
							)
								AS faltante
						FROM
							estado_cuenta
							LEFT JOIN catalogo_companias
								USING (num_cia)
						WHERE
							" . implode(' AND ', $condiciones_faltantes) . "
						GROUP BY
							admin,
							num_cia_primaria,
							num_cia,
							dia
						ORDER BY
							num_cia,
							dia
					";

					$query = $db->query($sql);
				}
				else
				{
					$condiciones_faltantes = $condiciones;

					$condiciones_faltantes[] = "fecha_con IS NULL";

					$condiciones_faltantes[] = "fecha >= '19-11-2014'";

					$sql = "
						SELECT
							idadministrador
								AS admin,
							num_cia,
							num_cia_primaria,
							EXTRACT(DAY FROM fecha)
								AS dia,
							SUM(
								CASE
									WHEN tipo = FALSE THEN
										-importe
									WHEN tipo = TRUE THEN
										importe
								END
							)
								AS faltante
						FROM
							faltantes_cometra
							LEFT JOIN catalogo_companias
								USING (num_cia)
						WHERE
							" . implode(' AND ', $condiciones_faltantes) . "
						GROUP BY
							admin,
							num_cia_primaria,
							num_cia,
							dia
						ORDER BY
							num_cia,
							dia
					";

					$query = $db->query($sql);
				}

				if ($query) {
					foreach ($query as $row) {
						$data['faltante'][$row['dia']] = floatval($row['faltante']);
					}
				}

				$condiciones = array();

				$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\'::DATE AND \'' . $fecha2 . '\'::DATE' . ($dia_corte < $dias_mes ? ' + INTERVAL \'1 DAY\'' : '');

				$condiciones[] = 'cod_mov IN (1, 16, 44, 99)';

				$condiciones[] = 'concepto NOT LIKE \'COMPLEMENTO VENTA%\'';

				$condiciones[] = '((num_cia = ' . $_REQUEST['num_cia'] . ' AND num_cia_sec IS NULL) OR num_cia_sec = ' . $_REQUEST['num_cia'] . ')';

				$sql = '
					SELECT
						idadministrador
							AS admin,
						COALESCE(num_cia_sec, num_cia)
							AS num_cia,
						CASE
							WHEN num_cia_sec IS NOT NULL THEN
								(
									SELECT
										num_cia_primaria
									FROM
										catalogo_companias
									WHERE
										num_cia = ec.num_cia_sec
								)
							ELSE
								num_cia_primaria
						END
							AS num_cia_primaria,
						comprobante,
						EXTRACT(day FROM fecha)
							AS dia,
						cod_mov,
						/*importe + COALESCE((
							SELECT
								SUM(importe)
							FROM
								otros_depositos
							WHERE
								comprobante = ec.comprobante
								AND num_cia = COALESCE(ec.num_cia_sec, ec.num_cia)
								AND fecha = ec.fecha
						), 0)
							AS */importe
					FROM
						estado_cuenta ec
						LEFT JOIN catalogo_companias
							USING (num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
					ORDER BY
						num_cia_primaria,
						num_cia,
						fecha,
						importe DESC
				';

				$result = $db->query($sql);

				if ($result) {
					foreach ($result as $rec) {
						if (isset($data['deposito'][$rec['dia']])
							&& $data['deposito'][$rec['dia']] == 0) {
							$data['deposito'][$rec['dia']] = $rec['dia'] > $dia_corte && $rec['cod_mov'] == 44 ? 0 : floatval($rec['importe']);

							if (isset($otros[$rec['dia']][$rec['comprobante']]) && !$otros[$rec['dia']][$rec['comprobante']]['status']) {
								$data['deposito'][$rec['dia']] += $otros[$rec['dia']][$rec['comprobante']]['importe'];

								$otros[$rec['dia']][$rec['comprobante']]['status'] = TRUE;
							}
						}
						else if (isset($data['mayoreo'][$rec['dia']])) {
							$data['mayoreo'][$rec['dia']] += $rec['dia'] > $dia_corte && $rec['cod_mov'] == 44 ? 0 : floatval($rec['importe']);

							if (isset($otros[$rec['dia']][$rec['comprobante']]) && !$otros[$rec['dia']][$rec['comprobante']]['status']) {
								$data['mayoreo'][$rec['dia']] += $otros[$rec['dia']][$rec['comprobante']]['importe'];

								$otros[$rec['dia']][$rec['comprobante']]['status'] = TRUE;
							}
						}
					}
				}

				foreach ($otros as $dia => $comprobantes) {
					foreach ($comprobantes as $rec) {
						if (!$rec['status']) {
							$data['oficina'][$dia] += $rec['importe'];
						}
					}
				}

				foreach ($data['efectivo'] as $dia => $efectivo) {
					if ($dia <= $dia_corte) {
						$data['total'][$dia] = $data['deposito'][$dia] + $data['mayoreo'][$dia] + $data['oficina'][$dia] + $data['faltante'][$dia];
						$data['diferencia'][$dia] = $data['efectivo'][$dia] - $data['total'][$dia];
					}
				}

				$data['totales']['efectivo'] = array_sum($data['efectivo']);
				$data['totales']['deposito'] = array_sum($data['deposito']) - ($data['cometra'] == 2 && $dia_corte < $dias_mes ? $data['deposito'][$dia_corte + 1] : 0);
				$data['totales']['mayoreo'] = array_sum($data['mayoreo']);
				$data['totales']['oficina'] = array_sum($data['oficina']);
				$data['totales']['faltante'] = array_sum($data['faltante']);
				$data['totales']['diferencia'] = array_sum($data['diferencia']);
				$data['totales']['total'] = array_sum($data['total']);

				$data['promedios']['efectivo'] = round($data['totales']['efectivo'] / $dia_corte, 2);
				$data['promedios']['deposito'] = round($data['totales']['deposito'] / $dia_corte, 2);
				$data['promedios']['mayoreo'] = round($data['totales']['mayoreo'] / $dia_corte, 2);
				$data['promedios']['oficina'] = round($data['totales']['oficina'] / $dia_corte, 2);
				$data['promedios']['total'] = round($data['totales']['total'] / $dia_corte, 2);

				include_once('includes/phpmailer/class.phpmailer.php');
				require_once('includes/WkHtmlToPdf.php');

				$path = dirname(__FILE__);

				$tpl = new TemplatePower('plantillas/ban/EfectivosReporteMensualEmail.tpl');
				$tpl->prepare();

				$tpl->newBlock('hoja');
				$tpl->newBlock('reporte');

				$tpl->assign('num_cia', $data['num_cia']);
				$tpl->assign('nombre', utf8_encode(strpos($data['nombre'], ' (') !== FALSE ? substr($data['nombre'], 0, strpos($data['nombre'], ' (')) : $data['nombre']));
				$tpl->assign('alias', utf8_encode($data['alias']));
				$tpl->assign('periodo', $__meses[$mes_corte] . ' ' . substr($anio_corte, -2));

				foreach ($data['efectivo'] as $dia => $efectivo) {
					$tpl->newBlock('row');

					$tpl->assign('dia', str_pad($dia, 2, '0', STR_PAD_LEFT));
					$tpl->assign('efectivo', $efectivo != 0 ? number_format($efectivo, 2) : '&nbsp;');
					$tpl->assign('deposito', $data['deposito'][$dia] != 0 ? number_format($data['deposito'][$dia], 2) : '&nbsp;');
					$tpl->assign('mayoreo', $data['mayoreo'][$dia] != 0 ? number_format($data['mayoreo'][$dia], 2) : '&nbsp;');
					$tpl->assign('oficina', $data['oficina'][$dia] != 0 ? number_format($data['oficina'][$dia], 2) : '&nbsp;');
					$tpl->assign('faltante', $data['faltante'][$dia] != 0 ? number_format($data['faltante'][$dia], 2) : '&nbsp;');
					$tpl->assign('diferencia', $data['diferencia'][$dia] != 0 ? number_format($data['diferencia'][$dia], 2) : '&nbsp;');
					$tpl->assign('total', $data['total'][$dia] != 0 ? number_format($data['total'][$dia], 2) : '&nbsp;');

					$tpl->assign('color_faltante', $data['faltante'][$dia] >= 0 ? 'blue' : 'red');
					$tpl->assign('color_diferencia', $data['diferencia'][$dia] >= 0 ? 'blue' : 'red');
				}

				$tpl->assign('bcelda', 'bcelda');

				foreach ($data['totales'] as $key => $value) {
					$tpl->assign('reporte.' . $key, $value != 0 ? number_format($value, 2) : ($key == 'diferencia' ? '0.00' : '&nbsp;'));
				}

				$tpl->assign('reporte.color_faltante', $data['totales']['faltante'] >= 0 ? 'blue' : 'red');
				$tpl->assign('reporte.color_diferencia', $data['totales']['diferencia'] >= 0 ? 'blue' : 'red');

				foreach ($data['promedios'] as $key => $value) {
					$tpl->assign('reporte.p' . $key, $value != 0 ? number_format($value, 2) : '&nbsp;');
				}
				$mail = new PHPMailer();

				$mail->IsSMTP();
				$mail->Host = 'mail.lecaroz.com';
				$mail->Port = 587;
				$mail->SMTPAuth = true;
				$mail->Username = 'miguelrebuelta@lecaroz.com';
				$mail->Password = 'L3c4r0z*';

				$mail->From = 'miguelrebuelta@lecaroz.com';
				$mail->FromName = utf8_decode('Lic. Miguel Angel Rebuelta Diez');

				if ($data['email_cia'] != '')
				{
					$mail->AddAddress($data['email_cia']);
				}

				if ($data['email_admin'] != '')
				{
					$mail->AddAddress($data['email_admin']);
				}

				$mail->AddCC('miguelrebuelta@lecaroz.com');

				// $mail->AddBCC('carlos.candelario@lecaroz.com');
				// $mail->AddAddress('carlos.candelario@lecaroz.com');

				$mail->Subject = utf8_decode('Reportes de efectivos del mes de ' . $_meses[$mes_corte] . ' de ' . $anio_corte);

				$pdf = new WkHtmlToPdf(array(
					'binPath'		=> '/usr/local/bin/wkhtmltopdf',
					// 'no-outline',								// Make Chrome not complain
					'margin-top'	=> 0,
					'margin-right'	=> 0,
					'margin-bottom'	=> 0,
					'margin-left'	=> 0,
					'page-size'		=> 'Letter',
					'orientation'	=> 'Landscape'
				));

				$pdf->setPageOptions(array(
					'disable-smart-shrinking',
					'user-style-sheet' => $path . '/styles/reporte-efectivos-pdf.css',
				));

				$pdf->addPage($tpl->getOutputContent());

				if ( ! $pdf->saveAs($path . '/tmp/reporte_efectivos_' . $anio_corte . '_' . strtolower($_meses[$mes_corte]) . '.pdf'))
				{
					throw new Exception('Could not create PDF: '.$pdf->getError());
				}

				$mail->AddAttachment($path . '/tmp/reporte_efectivos_' . $anio_corte . '_' . strtolower($_meses[$mes_corte]) . '.pdf');

				$mail->Body = 'Favor de descargar y abrir el archivo adjunto con Acrobat Reader o similares.';

				// $mail->IsHTML(true);

				if(!$mail->Send()) {
					echo 'Error: ' . $mail->ErrorInfo;
				}
				else {
					echo 'Correo enviado a todos los destinatarios.';
				}
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/EfectivosConciliacion.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
