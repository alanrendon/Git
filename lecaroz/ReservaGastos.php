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

$_meses_abr = array(
	1  => 'Ene',
	2  => 'Feb',
	3  => 'Mar',
	4  => 'Abr',
	5  => 'May',
	6  => 'Jun',
	7  => 'Jul',
	8  => 'Ago',
	9  => 'Sep',
	10 => 'Oct',
	11 => 'Nov',
	12 => 'Dic'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{

		case 'obtener_cia':
			$condiciones = array();

			$result = $db->query("SELECT
				nombre
			FROM
				catalogo_companias
			WHERE
				num_cia = {$_REQUEST['num_cia']}
				AND num_cia NOT IN (
					SELECT
						num_cia
					FROM
						catalogo_filiales
					WHERE
						num_cia = {$_REQUEST['num_cia']}
				)");

			if ($result)
			{
				echo utf8_encode($result[0]['nombre']);
			}

			break;

		case 'inicio':
			$tpl = new TemplatePower('plantillas/bal/ReservaGastosInicio.tpl');
			$tpl->prepare();

			$tpl->assign('anio', date('Y'));

			echo $tpl->getOutputContent();

			break;

		case 'consultar':
			$condiciones = array();

			$condiciones[] = "rg.codgastos = {$_REQUEST['gasto']}";

			$condiciones[] = "rg.anio = {$_REQUEST['anio']}";

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
					$condiciones[] = 'cc.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			$sql = "SELECT
				rg.num_cia,
				cc.nombre_corto AS nombre_cia,
				rg.mes,
				rg.importe
			FROM
				reserva_gastos rg
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				" . implode(' AND ', $condiciones) . "
			ORDER BY
				num_cia,
				mes";

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/bal/ReservaGastosConsulta.tpl');
			$tpl->prepare();

			$gasto = $db->query("SELECT descripcion FROM catalogo_gastos WHERE codgastos = {$_REQUEST['gasto']}");

			$tpl->assign('gasto', $_REQUEST['gasto']);
			$tpl->assign('nombre_gasto', $gasto[0]['descripcion']);
			$tpl->assign('anio', $_REQUEST['anio']);

			if ($result)
			{
				$datos = array();

				$totales = array_fill(0, 12, 0);

				$num_cia = NULL;

				foreach ($result as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = intval($row['num_cia']);

						$datos[$num_cia] = array(
							'num_cia'		=> intval($row['num_cia']),
							'nombre_cia'	=> $row['nombre_cia'],
							'acumulado'		=> 0,
							'importes'		=> array_fill(1, 12, 0)
						);

						$acumulado = $db->query("SELECT
							SUM(importe) AS importe
						FROM
							reserva_gastos
						WHERE
							num_cia = {$num_cia}
							AND codgastos = {$_REQUEST['gasto']}
							AND anio < {$_REQUEST['anio']}");

						$datos[$num_cia]['acumulado'] = floatval($acumulado[0]['importe']);

						$totales[0] += floatval($acumulado[0]['importe']);
					}

					$datos[$num_cia]['importes'][intval($row['mes'])] = floatval($row['importe']);

					$totales[intval($row['mes'])] += floatval($row['importe']);
				}

				foreach ($_meses_abr as $mes => $nombre)
				{
					$tpl->newBlock('th_mes');

					$tpl->assign('mes', $nombre);
				}

				foreach ($datos as $num_cia => $row)
				{
					if (count(array_filter($row['importes'])) == 0)
					{
						continue;
					}

					$tpl->newBlock('row');

					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', $row['nombre_cia']);

					$tpl->assign('acumulado', '<span class="' . ($row['acumulado'] < 0 ? 'red' : '') . '">' . number_format($row['acumulado'], 2) . '</span>');

					$total = array_sum($row['importes']) + $row['acumulado'];

					$tpl->assign('total', '<span class="' . ($total < 0 ? 'red' : '') . '">' . number_format($total, 2) . '</span>');

					foreach ($row['importes'] as $mes => $importe)
					{
						$tpl->newBlock('td_mes');

						$tpl->assign('importe', $importe != 0 ? '<span class="' . ($importe < 0 ? 'red' : '') . '">' . number_format($importe, 2) . '</span>' : '&nbsp;');
					}
				}

				foreach ($totales as $mes => $total)
				{
					$tpl->newBlock('total_mes');

					$tpl->assign('total', $total != 0 ? number_format($total, 2) : '&nbsp;');
				}

				$tpl->assign('_ROOT.total', number_format(array_sum($totales), 2));
			}

			echo $tpl->getOutputContent();

			break;

		case 'modificar':
			$condiciones = array();

			$condiciones[] = "tipo_cia IN (1, 2)";

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
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			$sql = "SELECT
				num_cia,
				nombre_corto AS nombre_cia
			FROM
				catalogo_companias
			WHERE
				" . implode(' AND ', $condiciones) . "
			ORDER BY
				num_cia";

			$cias = $db->query($sql);

			$tpl = new TemplatePower('plantillas/bal/ReservaGastosModificar.tpl');
			$tpl->prepare();

			$gasto = $db->query("SELECT descripcion FROM catalogo_gastos WHERE codgastos = {$_REQUEST['gasto']}");

			$tpl->assign('gasto', $_REQUEST['gasto']);
			$tpl->assign('nombre_gasto', $gasto[0]['descripcion']);
			$tpl->assign('anio', $_REQUEST['anio']);

			if ($cias)
			{
				$datos = array();

				$num_cia = NULL;

				foreach ($cias as $row)
				{
					$datos[intval($row['num_cia'])] = array(
						'num_cia'		=> intval($row['num_cia']),
						'nombre_cia'	=> $row['nombre_cia'],
						'acumulado'		=> 0,
						'importes'		=> array_fill(1, 12, 0)
					);

					$acumulado = $db->query("SELECT
						SUM(importe) AS importe
					FROM
						reserva_gastos
					WHERE
						num_cia = {$row['num_cia']}
						AND codgastos = {$_REQUEST['gasto']}
						AND anio < {$_REQUEST['anio']}");

					$datos[intval($row['num_cia'])]['acumulado'] = floatval($acumulado[0]['importe']);
				}

				$condiciones = array();

				$condiciones[] = "rg.codgastos = {$_REQUEST['gasto']}";

				$condiciones[] = "rg.anio = {$_REQUEST['anio']}";

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
						$condiciones[] = 'cc.num_cia IN (' . implode(', ', $cias) . ')';
					}
				}

				$sql = "SELECT
					rg.num_cia,
					cc.nombre_corto AS nombre_cia,
					rg.mes,
					rg.importe
				FROM
					reserva_gastos rg
					LEFT JOIN catalogo_companias cc USING (num_cia)
				WHERE
					" . implode(' AND ', $condiciones) . "
				ORDER BY
					num_cia,
					mes";

				$result = $db->query($sql);

				if ($result)
				{
					foreach ($result as $row)
					{
						$datos[intval($row['num_cia'])]['importes'][intval($row['mes'])] = floatval($row['importe']);
					}
				}

				foreach ($_meses_abr as $mes => $nombre)
				{
					$tpl->newBlock('th_mes');

					$tpl->assign('mes', $nombre);
				}

				$index = 0;
				$row_number = 0;

				foreach ($datos as $num_cia => $row)
				{
					$tpl->newBlock('row');

					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', $row['nombre_cia']);
					$tpl->assign('row', $row_number);

					$total = array_sum($row['importes']) + $row['acumulado'];

					$tpl->assign('acumulado', number_format($row['acumulado'], 2));
					$tpl->assign('total', number_format($total, 2));

					foreach ($row['importes'] as $mes => $importe)
					{
						$tpl->newBlock('td_mes');

						$tpl->assign('mes', $mes);
						$tpl->assign('row', $row_number);
						$tpl->assign('index', $index);
						$tpl->assign('importe', $importe != 0 ? number_format($importe, 2) : '');

						$index++;
					}

					$row_number++;
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'do_modificar':
			$gasto = $_REQUEST['gasto'];
			$anio = $_REQUEST['anio'];

			$sql = '';

			foreach ($_REQUEST['num_cia'] as $i => $num_cia)
			{
				foreach ($_REQUEST['importe_' . $i] as $j => $importe)
				{
					$importe = get_val($importe);

					if ($id = $db->query("SELECT id FROM reserva_gastos WHERE num_cia = {$num_cia} AND codgastos = {$gasto} AND anio = {$anio} AND mes = {$j} + 1"))
					{
						$sql .= "UPDATE reserva_gastos
						SET
							importe = {$importe},
							tsmod = NOW(),
							idmod = {$_SESSION['iduser']}
						WHERE
							id = {$id[0]['id']}
							AND importe != {$importe};\n";
					}
					else if ($importe != 0)
					{
						$sql .= "INSERT INTO reserva_gastos (
							codgastos,
							anio,
							num_cia,
							mes,
							importe,
							idalta
						) VALUES (
							{$gasto},
							{$anio},
							{$num_cia},
							{$j} + 1,
							{$importe},
							{$_SESSION['iduser']}
						);\n";
					}
				}
			}

			$db->query($sql);

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/bal/ReservaGastos.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
