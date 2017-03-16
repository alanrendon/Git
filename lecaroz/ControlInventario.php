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
			$tpl = new TemplatePower('plantillas/pan/ControlInventarioInicio.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();

			break;

		case 'obtener_cia':
			$condiciones = array();

			$condiciones[] = 'tipo_cia IN (1, 2)';

			$condiciones[] = "cc.num_cia = {$_REQUEST['num_cia']}";

			if (!in_array($_SESSION['iduser'], array(1, 4))) {
				$condiciones[] = "co.iduser = {$_SESSION['iduser']}";
			}

			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias cc
					LEFT JOIN catalogo_operadoras co
						USING (idoperadora)
				WHERE
					' . implode(' AND ', $condiciones) . '
			';

			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode($result[0]['nombre_corto']);
			}

			break;

		case 'obtener_mp':
			$tipo_cia = $db->query("SELECT CASE WHEN tipo_cia = 1 THEN 'TRUE' ELSE 'FALSE' END AS tipo_cia FROM catalogo_companias WHERE num_cia = {$_REQUEST['num_cia']}");

			$sql = "
				SELECT
					nombre,
					COALESCE((
						SELECT
							-1
						FROM
							historico_inventario
						WHERE
							num_cia = {$_REQUEST['num_cia']}
							AND fecha = '{$_REQUEST['fecha']}'
							AND codmp = {$_REQUEST['codmp']}
					), 1)
						AS status
				FROM
					catalogo_mat_primas cmp
				WHERE
					(tipo_cia = {$tipo_cia[0]['tipo_cia']} OR codmp = 90)
					AND codmp = {$_REQUEST['codmp']}
			";

			$result = $db->query($sql);

			if ($result) {
				$row = $result[0];

				$data = array(
					'status'	=> get_val($row['status']),
					'num_cia'	=> $_REQUEST['num_cia'],
					'fecha'		=> $_REQUEST['fecha'],
					'codmp'		=> $_REQUEST['codmp'],
					'nombre'	=> utf8_encode($row['nombre'])
				);
			}
			else
			{
				$data = array(
					'status'	=> -2
				);
			}

			header('Content-Type: application/json');
			echo json_encode($data);

			break;

		case 'consultar':
			$sql = "
				SELECT
					num_cia,
					nombre_corto
						AS nombre_cia,
					COALESCE((
						SELECT
							MAX(fecha)
						FROM
							historico_inventario
						WHERE
							num_cia = cc.num_cia
					), (
						SELECT
							MAX(fecha)
						FROM
							historico_inventario
						WHERE
							num_cia <= 300
					))
						AS fecha
				FROM
					catalogo_companias cc
				WHERE
					num_cia = {$_REQUEST['num_cia']}
					AND tipo_cia IN (1, 2)
			";

			$result = $db->query($sql);

			if ($result)
			{
				$cia = $result[0];

				$tpl = new TemplatePower('plantillas/pan/ControlInventarioConsulta.tpl');
				$tpl->prepare();

				$tpl->assign('num_cia', $cia['num_cia']);
				$tpl->assign('nombre_cia', utf8_encode($cia['nombre_cia']));

				list($dia_inv, $mes_inv, $anio_inv) = array_map('toInt', explode('/', $cia['fecha']));

				$tpl->assign('fecha', $cia['fecha']);
				$tpl->assign('mes', mb_strtoupper($_meses[date('n', mktime(0, 0, 0, $mes_inv + 1, 1, $anio_inv))]));
				$tpl->assign('anio', date('Y', mktime(0, 0, 0, $mes_inv + 1, 1, $anio_inv)));

				if ($result) {
					$condiciones = array();

					$condiciones[] = "hi.num_cia = {$_REQUEST['num_cia']}";

					$condiciones[] = "hi.fecha = (
						SELECT
							MAX(fecha)
						FROM
							historico_inventario
						WHERE
							num_cia = hi.num_cia
					)";

					if (!in_array($_SESSION['iduser'], array(1, 4))) {
						$condiciones[] = "co.iduser = {$_SESSION['iduser']}";
					}

					$sql = "
						SELECT
							hi.idinv
								AS id_hi,
							ir.idinv
								AS id_ir,
							iv.idinv
								AS id_iv,
							hi.num_cia,
							cc.nombre_corto
								AS nombre_cia,
							hi.codmp
								AS cod,
							cmp.nombre
								AS nombre_mp,
							hi.fecha,
							hi.existencia
								AS existencia_inicio,
							ir.existencia
								AS existencia_real,
							iv.existencia
								AS existencia_virtual
						FROM
							historico_inventario hi
							LEFT JOIN catalogo_companias cc
								USING (num_cia)
							LEFT JOIN catalogo_operadoras co
								USING (idoperadora)
							LEFT JOIN catalogo_mat_primas cmp
								USING (codmp)
							LEFT JOIN inventario_real ir
								USING (num_cia, codmp)
							LEFT JOIN inventario_virtual iv
								USING (num_cia, codmp)
						WHERE
							" . implode(' AND ', $condiciones) . "
						ORDER BY
							hi.num_cia,
							hi.codmp
					";

					$result = $db->query($sql);

					if ($result)
					{
						foreach ($result as $row)
						{
							$tpl->newBlock('row');

							$tpl->assign('cod', $row['cod']);
							$tpl->assign('nombre_mp', utf8_encode($row['nombre_mp']));
							$tpl->assign('existencia_inicio', $row['existencia_inicio'] != 0 ? '<span class="' . ($row['existencia_inicio'] < 0 ? 'red' : 'blue') . '">' . number_format($row['existencia_inicio'], 2) . '</span>' : '-');
							$tpl->assign('existencia_real', $row['existencia_real'] != 0 ? '<span class="' . ($row['existencia_real'] < 0 ? 'red' : 'blue') . '">' . number_format($row['existencia_real'], 2) . '</span>' : '-');
							$tpl->assign('existencia_virtual', $row['existencia_virtual'] != 0 ? '<span class="' . ($row['existencia_virtual'] < 0 ? 'red' : 'blue') . '">' . number_format($row['existencia_virtual'], 2) . '</span>' : '-');

							$diferencia = $row['existencia_real'] - $row['existencia_virtual'];

							$tpl->assign('existencia_diferencia', $diferencia != 0 ? '<span class="' . ($diferencia < 0 ? 'red' : 'blue') . '">' . number_format($diferencia, 2) . '</span>' : '-');
						}
					}

				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'alta':
			$tpl = new TemplatePower('plantillas/pan/ControlInventarioAlta.tpl');
			$tpl->prepare();

			$tpl->assign('num_cia', $_REQUEST['num_cia']);
			$tpl->assign('fecha', $_REQUEST['fecha']);

			$sql = "
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					num_cia = {$_REQUEST['num_cia']}
			";

			$result = $db->query($sql);

			$tpl->assign('nombre_cia', utf8_encode($result[0]['nombre_corto']));

			list($dia_inv, $mes_inv, $anio_inv) = array_map('toInt', explode('/', $_REQUEST['fecha']));

			$tpl->assign('fecha', $_REQUEST['fecha']);
			$tpl->assign('mes', mb_strtoupper($_meses[date('n', mktime(0, 0, 0, $mes_inv + 1, 1, $anio_inv))]));
			$tpl->assign('anio', date('Y', mktime(0, 0, 0, $mes_inv + 1, 1, $anio_inv)));

			echo $tpl->getOutputContent();

			break;

		case 'do_alta':
			$sql = '';

			foreach ($_REQUEST['data'] as $data_row)
			{
				if ($data_row != '')
				{
					$data = json_decode($data_row);

					if ($data->status == 1)
					{
						$sql .= "
							INSERT INTO
								historico_inventario (
									num_cia,
									fecha,
									codmp,
									existencia,
									precio_unidad
								)
								VALUES (
									{$data->num_cia},
									'{$data->fecha}'::DATE,
									{$data->codmp},
									0,
									0
								);
						";

						$sql .= "
							INSERT INTO
								inventario_real (
									num_cia,
									codmp,
									existencia,
									precio_unidad
								)
								VALUES (
									{$data->num_cia},
									{$data->codmp},
									0,
									0
								);
						";

						$sql .= "
							INSERT INTO
								inventario_virtual (
									num_cia,
									codmp,
									existencia,
									precio_unidad
								)
								VALUES (
									{$data->num_cia},
									{$data->codmp},
									0,
									0
								);
						";
					}
				}
			}

			if ($sql != '')
			{
				$db->query($sql);
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/pan/ControlInventario.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
