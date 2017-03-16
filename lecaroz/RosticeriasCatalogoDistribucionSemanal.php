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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {

		case 'inicio':
			$tpl = new TemplatePower('plantillas/ros/RosticeriasCatalogoDistribucionSemanalInicio.tpl');
			$tpl->prepare();

			$result = $db->query("SELECT num_proveedor AS value, nombre AS text FROM precios_guerra LEFT JOIN catalogo_proveedores USING (num_proveedor) GROUP BY value, text ORDER BY value");

			if ($result)
			{
				foreach ($result as $row) {
					$tpl->newBlock('pro');

					$tpl->assign('value', $row['value']);
					$tpl->assign('text', utf8_encode("{$row['value']} {$row['text']}"));
				}
			}

			for ($i = 0; $i < 7; $i++)
			{
				$tpl->newBlock('semana');
				$tpl->assign('fecha1', date('d/m/Y', strtotime("last sunday - 21 days + 1 day + " . ($i * 7) . " days")));
				$tpl->assign('fecha2', date('d/m/Y', strtotime("last sunday - 21 days + 1 day + " . ($i * 7) . " days + 6 days")));
			}

			echo $tpl->getOutputContent();

			break;

		case 'consultar':
			$fechas = array();

			for ($i = 0; $i < 7; $i++)
			{
				$fechas[] = date('d/m/Y', strtotime("last sunday - 1 week - 6 days + {$i} days"));
			}

			$condiciones = array();

			$condiciones[] = "tipo_cia = 2";

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
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			$sql = "SELECT
				num_cia,
				nombre_corto AS nombre_cia,
				(
					SELECT
						SUM(total_fac)
					FROM
						total_fac_ros
					WHERE
						num_cia = cc.num_cia
						AND num_proveedor = {$_REQUEST['num_pro']}
						AND fecha BETWEEN '{$fechas[0]}' AND '{$fechas[6]}'
				) AS total_semana
			FROM
				catalogo_companias cc
			WHERE
				" . implode(' AND ', $condiciones) . "
			ORDER BY
				num_cia";

			$result = $db->query($sql);

			if ($result)
			{
				list($dia, $mes, $anio) = array_map('toInt', explode('/', $_REQUEST['fecha_inicio_semana']));

				$fecha1 = $_REQUEST['fecha_inicio_semana'];
				$fecha2 = date('d/m/Y', strtotime("{$anio}/{$mes}/{$dia} + 6 days"));

				$data = array();

				foreach ($result as $row)
				{
					$data[$row['num_cia']] = array(
						'nombre_cia'	=> $row['nombre_cia'],
						'dias'			=> array_fill(0, 7, NULL),
						'total_semana'	=> floatval($row['total_semana']),
						'total_dias'	=> 0,
						'porc'			=> 0
					);
				}

				$condiciones[0] = "cat.tsbaja IS NULL";

				$condiciones[] = "cat.num_proveedor = {$_REQUEST['num_pro']}";

				$condiciones[] = "cat.fecha_inicio_semana = '{$_REQUEST['fecha_inicio_semana']}'";

				$sql = "SELECT
					cat.id,
					cat.num_cia,
					cc.nombre_corto AS nombre_cia,
					cat.dia_semana,
					cat.fecha_inicio_semana,
					cat.fecha_inicio_semana + INTERVAL '6 DAYS' AS fecha_fin_semana
				FROM
					catalogo_rosticerias_distribucion_semanal cat
					LEFT JOIN catalogo_companias cc USING (num_cia)
				" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
				ORDER BY
					cat.num_cia,
					cat.dia_semana";

				$result = $db->query($sql);

				if ($result)
				{
					foreach ($result as $row)
					{
						$data[$row['num_cia']]['dias'][$row['dia_semana']] = $row['id'];
					}

					foreach ($data as $num_cia => $data_cia)
					{
						if (array_filter($data_cia['dias']))
						{
							$fechas_cia = array();

							foreach (array_filter($data_cia['dias']) as $key => $val)
							{
								$fechas_cia[] = $fechas[$key];
							}

							$total_dias = $db->query("SELECT SUM(total_fac) AS total_dias FROM total_fac_ros WHERE num_cia = {$num_cia} AND num_proveedor = {$_REQUEST['num_pro']} AND fecha IN ('" . implode("', '", $fechas_cia) . "')");

							$data[$num_cia]['total_dias'] = floatval($total_dias[0]['total_dias']);

							if ($data[$num_cia]['total_semana'] > 0 && $data[$num_cia]['total_dias'] > 0)
							{
								$porc = $data[$num_cia]['total_dias'] * 100 / $data[$num_cia]['total_semana'];

								$data[$num_cia]['porc'] = $porc;
							}
						}
					}
				}

				$tpl = new TemplatePower('plantillas/ros/RosticeriasCatalogoDistribucionSemanalConsulta.tpl');
				$tpl->prepare();

				$nombre_pro = $db->query("SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = {$_REQUEST['num_pro']}");

				$tpl->assign('num_pro', $_REQUEST['num_pro']);
				$tpl->assign('nombre_pro', utf8_encode($nombre_pro[0]['nombre']));

				$tpl->assign('fecha1', $fecha1);
				$tpl->assign('fecha2', $fecha2);

				$tpl->assign('fecha_referencia1', $fechas[0]);
				$tpl->assign('fecha_referencia2', $fechas[6]);

				foreach ($data as $num_cia => $data_cia)
				{
					$tpl->newBlock('row');

					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', utf8_encode($data_cia['nombre_cia']));

					$tpl->assign('num_pro', $_REQUEST['num_pro']);

					foreach ($data_cia['dias'] as $dia => $id)
					{
						$tpl->assign('dia_' . $dia, $id > 0 ? '' : '_blank');
						$tpl->assign('id_' . $dia, $id > 0 ? $id : '');
					}

					$tpl->assign('porc', $data_cia['porc'] > 0 ? number_format($data_cia['porc'], 2) : '');
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'cambiar_status':
			$fechas = array();

			for ($i = 0; $i < 7; $i++)
			{
				$fechas[] = date('d/m/Y', strtotime('last sunday - 1 week - 6 days + ' . $i . ' days'));
			}

			if ($_REQUEST['id'] > 0)
			{
				$db->query("UPDATE catalogo_rosticerias_distribucion_semanal
					SET
						tsbaja = NOW(),
						idbaja = {$_SESSION['iduser']}
					WHERE
						id = {$_REQUEST['id']}");

				$total_semana = $db->query("SELECT SUM(total_fac) AS total FROM total_fac_ros WHERE num_cia = {$_REQUEST['num_cia']} AND num_proveedor = {$_REQUEST['num_pro']} AND fecha BETWEEN '{$fechas[0]}' AND '{$fechas[6]}'");

				$dias = $db->query("SELECT dia_semana FROM catalogo_rosticerias_distribucion_semanal WHERE num_cia = {$_REQUEST['num_cia']} AND num_proveedor = {$_REQUEST['num_pro']} AND fecha_inicio_semana = '{$_REQUEST['fecha']}' AND tsbaja IS NULL ORDER BY dia_semana");

				if ($dias)
				{
					$fechas_cia = array();

					foreach ($dias as $dia)
					{
						$fechas_cia[] = $fechas[$dia['dia_semana']];
					}

					$total_dias = $db->query("SELECT SUM(total_fac) AS total FROM total_fac_ros WHERE num_cia = {$_REQUEST['num_cia']} AND num_proveedor = {$_REQUEST['num_pro']} AND fecha IN ('" . implode("', '", $fechas_cia) . "')");

					if ($total_semana[0]['total'] > 0 && $total_dias[0]['total'] > 0)
					{
						$porc = $total_dias[0]['total'] * 100 / $total_semana[0]['total'];
					}
					else
					{
						$porc = 0;
					}
				}
				else
				{
					$porc = 0;
				}

				echo json_encode(array(
					'status'	=> 0,
					'porc'		=> $porc
				));
			}
			else
			{
				$db->query("INSERT INTO catalogo_rosticerias_distribucion_semanal (num_proveedor, num_cia, dia_semana, idalta, fecha_inicio_semana) VALUES ({$_REQUEST['num_pro']}, {$_REQUEST['num_cia']}, {$_REQUEST['dia']}, {$_SESSION['iduser']}, '{$_REQUEST['fecha']}')");

				$id = $db->query("SELECT MAX(id) AS id FROM catalogo_rosticerias_distribucion_semanal");

				$total_semana = $db->query("SELECT SUM(total_fac) AS total FROM total_fac_ros WHERE num_cia = {$_REQUEST['num_cia']} AND num_proveedor = {$_REQUEST['num_pro']} AND fecha BETWEEN '{$fechas[0]}' AND '{$fechas[6]}'");

				$dias = $db->query("SELECT dia_semana FROM catalogo_rosticerias_distribucion_semanal WHERE num_cia = {$_REQUEST['num_cia']} AND num_proveedor = {$_REQUEST['num_pro']} AND fecha_inicio_semana = '{$_REQUEST['fecha']}' AND tsbaja IS NULL ORDER BY dia_semana");

				if ($dias)
				{
					$fechas_cia = array();

					foreach ($dias as $dia)
					{
						$fechas_cia[] = $fechas[$dia['dia_semana']];
					}

					$total_dias = $db->query("SELECT SUM(total_fac) AS total FROM total_fac_ros WHERE num_cia = {$_REQUEST['num_cia']} AND num_proveedor = {$_REQUEST['num_pro']} AND fecha IN ('" . implode("', '", $fechas_cia) . "')");

					if ($total_semana[0]['total'] > 0 && $total_dias[0]['total'] > 0)
					{
						$porc = $total_dias[0]['total'] * 100 / $total_semana[0]['total'];
					}
					else
					{
						$porc = 0;
					}
				}
				else
				{
					$porc = 0;
				}

				echo json_encode(array(
					'status'	=> 1,
					'id'		=> intval($id[0]['id']),
					'porc'		=> $porc
				));
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ros/RosticeriasCatalogoDistribucionSemanal.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
