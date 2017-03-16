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
			$tpl = new TemplatePower('plantillas/ban/ImpuestosFilialesInicio.tpl');
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
					$condiciones[] = 'f.num_cia_primaria IN (' . implode(', ', $matrices) . ')';
				}
			}

			if (isset($_REQUEST['filiales']) && trim($_REQUEST['filiales']) != '')
			{
				$filiales = array();

				$pieces = explode(',', $_REQUEST['filiales']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$filiales[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else
					{
						$filiales[] = $piece;
					}
				}

				if (count($filiales) > 0)
				{
					$condiciones[] = 'f.num_cia_primaria IN (SELECT num_cia_primaria FROM catalogo_filiales WHERE num_cia IN (' . implode(', ', $filiales) . '))';
				}
			}

			$sql = "SELECT
				f.id,
				f.num_cia_primaria AS matriz,
				cm.nombre AS nombre_matriz,
				cm.rfc AS rfc_matriz,
				f.num_cia AS filial,
				cf.nombre AS nombre_filial,
				cf.rfc AS rfc_filial
			FROM
				catalogo_filiales f
				LEFT JOIN catalogo_companias cm ON (cm.num_cia = f.num_cia_primaria)
				LEFT JOIN catalogo_companias cf ON (cf.num_cia = f.num_cia)
			" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
			ORDER BY
				matriz,
				CASE
					WHEN f.num_cia_primaria = f.num_cia THEN
						1
					ELSE
						2
				END,
				filial";

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ban/ImpuestosFilialesConsulta.tpl');
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

						$tpl->newBlock('matriz');

						$tpl->assign('matriz', $matriz);
						$tpl->assign('datos_matriz', json_encode(array(
							'id'				=> intval($id_matriz),
							'matriz'			=> intval($matriz),
							'nombre_matriz'	=> utf8_encode($row['nombre_matriz']),
							'rfc_matriz'		=> utf8_encode($row['rfc_matriz'])
						)));
						$tpl->assign('nombre_matriz', utf8_encode($row['nombre_matriz']));
						$tpl->assign('rfc_matriz', utf8_encode($row['rfc_matriz']));
					}

					if ($row['filial'] == $matriz)
					{
						continue;
					}

					$tpl->newBlock('row');

					$tpl->assign('filial', $row['filial']);
					$tpl->assign('datos_filial', json_encode(array(
						'id_filial'			=> intval($row['id']),
						'id_matriz'			=> intval($id_matriz),
						'filial'			=> intval($row['filial']),
						'nombre_filial'		=> utf8_encode($row['nombre_filial']),
						'rfc_filial'		=> utf8_encode($row['rfc_filial'])
					)));
					$tpl->assign('nombre_filial', utf8_encode($row['nombre_filial']));
					$tpl->assign('rfc_filial', utf8_encode($row['rfc_filial']));

					$tpl->assign('dif', $row['rfc_filial'] != $row['rfc_matriz'] ? ' style="background-color:#FF7979;"' : '');
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'do_alta_matriz':
			$db->query("INSERT INTO catalogo_filiales (num_cia_primaria, num_cia, first) VALUES ({$_REQUEST['num_cia']}, {$_REQUEST['num_cia']}, TRUE)");

			break;

		case 'do_alta_filial':
			$db->query("INSERT INTO catalogo_filiales (num_cia_primaria, num_cia, first) VALUES ({$_REQUEST['matriz']}, {$_REQUEST['filial']}, FALSE)");

			break;

		case 'do_baja_matriz':
			$db->query("DELETE FROM catalogo_filiales WHERE num_cia_primaria = {$_REQUEST['matriz']}");

			break;

		case 'do_baja_filial':
			$db->query("DELETE FROM catalogo_filiales WHERE id = {$_REQUEST['id_filial']}");

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/ImpuestosFiliales.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
