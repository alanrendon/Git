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
						cia_secundaria
					FROM
						cuentas_mancomunadas
					WHERE
						tsbaja IS NULL
						AND cia_secundaria = {$_REQUEST['num_cia']}
				)");

			if ($result)
			{
				echo utf8_encode($result[0]['nombre']);
			}

			break;

		case 'inicio':
			$tpl = new TemplatePower('plantillas/ban/CuentasMancomunadasInicio.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();

			break;

		case 'consultar':
			$condiciones = array();

			$condiciones[] = "cm.tsbaja IS NULL";

			if (isset($_REQUEST['principales']) && trim($_REQUEST['principales']) != '')
			{
				$principales = array();

				$pieces = explode(',', $_REQUEST['principales']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$principales[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else
					{
						$principales[] = $piece;
					}
				}

				if (count($principales) > 0)
				{
					$condiciones[] = 'cm.cia_principal IN (' . implode(', ', $principales) . ')';
				}
			}

			if (isset($_REQUEST['secundarias']) && trim($_REQUEST['secundarias']) != '')
			{
				$secundarias = array();

				$pieces = explode(',', $_REQUEST['secundarias']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$secundarias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else
					{
						$secundarias[] = $piece;
					}
				}

				if (count($secundarias) > 0)
				{
					$condiciones[] = 'cm.cia_principal IN (SELECT cia_principal FROM cuentas_mancomunadas WHERE cia_secundaria IN (' . implode(', ', $secundarias) . '))';
				}
			}

			$sql = "SELECT
				cm.id,
				cm.cia_principal AS principal,
				cp.nombre AS nombre_principal,
				cp.rfc AS rfc_principal,
				cm.cia_secundaria AS secundaria,
				cs.nombre AS nombre_secundaria,
				cs.rfc AS rfc_secundaria
			FROM
				cuentas_mancomunadas cm
				LEFT JOIN catalogo_companias cp ON (cp.num_cia = cm.cia_principal)
				LEFT JOIN catalogo_companias cs ON (cs.num_cia = cm.cia_secundaria)
			" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
			ORDER BY
				principal,
				CASE
					WHEN cm.cia_principal = cm.cia_secundaria THEN
						1
					ELSE
						2
				END,
				secundaria";

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ban/CuentasMancomunadasConsulta.tpl');
			$tpl->prepare();

			if ($result)
			{
				$principal = NULL;

				foreach ($result as $row)
				{
					if ($principal != $row['principal'])
					{
						$principal = $row['principal'];

						$id_principal = $row['id'];

						$tpl->newBlock('principal');

						$tpl->assign('principal', $principal);
						$tpl->assign('datos_principal', json_encode(array(
							'id'				=> intval($id_principal),
							'principal'			=> intval($principal),
							'nombre_principal'	=> utf8_encode($row['nombre_principal']),
							'rfc_principal'		=> utf8_encode($row['rfc_principal'])
						)));
						$tpl->assign('nombre_principal', utf8_encode($row['nombre_principal']));
						$tpl->assign('rfc_principal', utf8_encode($row['rfc_principal']));
					}

					if ($row['secundaria'] == $principal)
					{
						continue;
					}

					$tpl->newBlock('row');

					$tpl->assign('secundaria', $row['secundaria']);
					$tpl->assign('datos_secundaria', json_encode(array(
						'id_secundaria'			=> intval($row['id']),
						'id_principal'			=> intval($id_principal),
						'secundaria'			=> intval($row['secundaria']),
						'nombre_secundaria'		=> utf8_encode($row['nombre_secundaria']),
						'rfc_secundaria'		=> utf8_encode($row['rfc_secundaria'])
					)));
					$tpl->assign('nombre_secundaria', utf8_encode($row['nombre_secundaria']));
					$tpl->assign('rfc_secundaria', utf8_encode($row['rfc_secundaria']));

					$tpl->assign('dif', $row['rfc_secundaria'] != $row['rfc_principal'] ? ' style="background-color:#FF7979;"' : '');
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'do_alta_principal':
			$db->query("INSERT INTO cuentas_mancomunadas (cia_principal, cia_secundaria) VALUES ({$_REQUEST['num_cia']}, {$_REQUEST['num_cia']})");

			break;

		case 'do_alta_secundaria':
			$db->query("INSERT INTO cuentas_mancomunadas (cia_principal, cia_secundaria) VALUES ({$_REQUEST['principal']}, {$_REQUEST['secundaria']})");

			break;

		case 'do_baja_principal':
			$db->query("UPDATE cuentas_mancomunadas SET tsbaja = NOW(), idbaja = {$_SESSION['iduser']} WHERE cia_principal = {$_REQUEST['principal']}");

			break;

		case 'do_baja_secundaria':
			$db->query("UPDATE cuentas_mancomunadas SET tsbaja = NOW(), idbaja = {$_SESSION['iduser']} WHERE id = {$_REQUEST['id_secundaria']}");

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/CuentasMancomunadas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
