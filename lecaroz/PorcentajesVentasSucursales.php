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

		case 'obtener_cia':
			$condiciones = array();

			$result = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = {$_REQUEST['num_cia']} AND num_cia NOT IN (SELECT matriz FROM porcentajes_puntos_calientes WHERE sucursal = {$_REQUEST['num_cia']})");

			if ($result)
			{
				echo utf8_encode($result[0]['nombre_corto']);
			}

			break;

		case 'inicio':
			$tpl = new TemplatePower('plantillas/ban/PorcentajesVentasSucursalesInicio.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();

			break;

		case 'consultar':
			$condiciones = array();

			if (isset($_REQUEST['matrices']) && trim($_REQUEST['matrices']) != '')
			{
				$matrices = array();

				$pieces = explode(',', $_REQUEST['matrices']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$matrices[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else
					{
						$matrices[] = $piece;
					}
				}

				if (count($matrices) > 0)
				{
					$condiciones[] = 'p.matriz IN (' . implode(', ', $matrices) . ')';
				}
			}

			if (isset($_REQUEST['sucursales']) && trim($_REQUEST['sucursales']) != '')
			{
				$sucursales = array();

				$pieces = explode(',', $_REQUEST['sucursales']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$sucursales[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else
					{
						$sucursales[] = $piece;
					}
				}

				if (count($sucursales) > 0)
				{
					$condiciones[] = 'p.matriz IN (SELECT matriz FROM porcentajes_puntos_calientes WHERE sucursal IN (' . implode(', ', $sucursales) . '))';
				}
			}


			$sql = '
				SELECT
					p.id,
					p.matriz,
					cm.nombre_corto
						AS nombre_matriz,
					p.sucursal,
					cs.nombre_corto
						AS nombre_sucursal,
					porcentaje
				FROM
					porcentajes_puntos_calientes p
					LEFT JOIN catalogo_companias cm
						ON (cm.num_cia = p.matriz)
					LEFT JOIN catalogo_companias cs
						ON (cs.num_cia = p.sucursal)
				' . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . '
				ORDER BY
					p.matriz,
					CASE
						WHEN p.matriz = p.sucursal THEN
							1
						ELSE
							2
					END,
					p.sucursal
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ban/PorcentajesVentasSucursalesConsulta.tpl');
			$tpl->prepare();

			if ($result)
			{
				$matriz = NULL;

				foreach ($result as $row)
				{
					if ($matriz != $row['matriz'])
					{
						$matriz = $row['matriz'];

						$id_matriz = $row['id'];
						$porcentaje = $row['porcentaje'];

						$tpl->newBlock('matriz');

						$tpl->assign('matriz', $matriz);
						$tpl->assign('datos_matriz', json_encode(array(
							'id'			=> intval($id_matriz),
							'matriz'		=> intval($matriz),
							'nombre_matriz'	=> utf8_encode($row['nombre_matriz']),
							'porcentaje'	=> floatval($porcentaje)
						)));
						$tpl->assign('nombre_matriz', utf8_encode($row['nombre_matriz']));
						$tpl->assign('porcentaje', number_format($row['porcentaje'], 2));
					}

					if ($row['sucursal'] == $matriz)
					{
						continue;
					}

					$tpl->newBlock('row');

					$tpl->assign('sucursal', $row['sucursal']);
					$tpl->assign('datos_sucursal', json_encode(array(
						'id_sucursal'			=> intval($row['id']),
						'id_matriz'				=> intval($id_matriz),
						'porcentaje_matriz' 	=> floatval($porcentaje),
						'sucursal'				=> intval($row['sucursal']),
						'nombre_sucursal'		=> utf8_encode($row['nombre_sucursal']),
						'porcentaje_sucursal'	=> floatval($row['porcentaje'])
					)));
					$tpl->assign('nombre_sucursal', utf8_encode($row['nombre_sucursal']));
					$tpl->assign('porcentaje', number_format($row['porcentaje'], 2));
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'do_alta_matriz':
			$db->query("INSERT INTO porcentajes_puntos_calientes (matriz, sucursal, porcentaje) VALUES ({$_REQUEST['num_cia']}, {$_REQUEST['num_cia']}, 100)");

			break;

		case 'do_alta_sucursal':
			$db->query("INSERT INTO porcentajes_puntos_calientes (matriz, sucursal, porcentaje) VALUES ({$_REQUEST['matriz']}, {$_REQUEST['sucursal']}, {$_REQUEST['porcentaje']})");

			$db->query("UPDATE porcentajes_puntos_calientes SET porcentaje = porcentaje - {$_REQUEST['porcentaje']} WHERE id = {$_REQUEST['id_matriz']}");

			break;

		case 'do_modificar_sucursal':
			$db->query("UPDATE porcentajes_puntos_calientes SET porcentaje = {$_REQUEST['porcentaje_matriz']} WHERE id = {$_REQUEST['id_matriz']}");

			$db->query("UPDATE porcentajes_puntos_calientes SET porcentaje = {$_REQUEST['porcentaje_sucursal']} WHERE id = {$_REQUEST['id_sucursal']}");

			break;

		case 'do_baja_matriz':
			$db->query("DELETE FROM porcentajes_puntos_calientes WHERE matriz = {$_REQUEST['matriz']}");

			break;

		case 'do_baja_sucursal':
			$db->query("DELETE FROM porcentajes_puntos_calientes WHERE id = {$_REQUEST['id_sucursal']}");

			$db->query("UPDATE porcentajes_puntos_calientes SET porcentaje = porcentaje + {$_REQUEST['porcentaje']} WHERE id = {$_REQUEST['id_matriz']}");

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/PorcentajesVentasSucursales.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
