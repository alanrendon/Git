<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{
		case 'actualizar':
			// $mysql = new DBclass('mysql://722709_lecar866:HVDQxwn4n3Wv@72.3.204.211/722709_lecar866_prov', 'autocommit=yes');
			$mysql = new DBclass('mysql://722709_lecar866:HVDQxwn4n3Wv@172.99.97.60/722709_lecar866_prov', 'autocommit=yes');

			if (isset($_REQUEST['catalogo_proveedores']))
			{
				$result = $db->query('SELECT
					num_proveedor AS "IdPro",
					nombre AS "NombrePro",
					md5(pass_site) AS "Password"
				FROM
					catalogo_proveedores
				WHERE
					TRIM(pass_site) <> \'\'
				ORDER BY
					num_proveedor');

				if ($result)
				{
					$data = array();

					foreach ($result as $r)
					{
						$data[] = "('{$r['IdPro']}', '{$r['NombrePro']}', '{$r['Password']}')";
					}

					$mysql->query('TRUNCATE `Usuarios`');

					$mysql->query('INSERT INTO `Usuarios` (`IdPro`, `NombrePro`, `Password`) VALUES ' . implode(', ', $data));
				}
			}

			if (isset($_REQUEST['catalogo_panaderias']))
			{
				$result = $db->query('SELECT
					num_cia AS "NumCia",
					nombre AS "NombreCia"
				FROM
					catalogo_companias
				WHERE
					num_cia < 599
				ORDER BY
					num_cia');

				if ($result)
				{
					$data = array();

					foreach ($result as $r)
					{
						$data[] = "('{$r['NumCia']}', '{$r['NombreCia']}')";
					}

					$mysql->query('TRUNCATE `CatalogoPanaderias`');

					$mysql->query('INSERT INTO `CatalogoPanaderias` (`NumCia`, `NombreCia`) VALUES ' . implode(', ', $data));
				}
			}

			if (isset($_REQUEST['pagos']))
			{
				$result = $db->query('SELECT
					c.num_proveedor AS "IdPro",
					num_cia AS "NumCia",
					nombre_corto AS "NombreCia",
					cuenta AS "IdBanco",
					folio AS "Folio",
					fecha AS "Fecha",
					facturas AS "Facturas",
					importe AS "Importe",
					fecha_cancelacion AS "Cancelado"
				FROM
					cheques c
					LEFT JOIN catalogo_companias cc USING (num_cia)
				WHERE
					num_cia < 900
					AND site = TRUE
					AND importe > 0
				ORDER BY
					c.num_proveedor,
					num_cia,
					cuenta,
					folio');

				if ($result)
				{
					$db->query('UPDATE cheques SET site = FALSE, tssite = NOW() WHERE site = TRUE');

					$data = array();

					foreach ($result as $r)
					{
						$pieces = explode('/', $r['Fecha']);
						$r['Fecha'] = $pieces[2] . '-' . $pieces[1] . '-' . $pieces[0];

						$data[] = "('{$r['IdPro']}', '{$r['NumCia']}', '{$r['NombreCia']}', '{$r['IdBanco']}', '{$r['Folio']}', '{$r['Fecha']}', '{$r['Facturas']}', '{$r['Importe']}', " . ($r['Cancelado'] != '' ? "'{$r['Cancelado']}'" : 'NULL') . ")";
					}

					$mysql->query('DELETE FROM `Pagos` WHERE `TsIns` < SUBDATE(CURDATE(), INTERVAL 1 MONTH)');

					$mysql->query('INSERT INTO `Pagos` (`IdPro`, `NumCia`, `NombreCia`, `IdBanco`, `Folio`, `Fecha`, `Facturas`, `Importe`, `Cancelado`) VALUES ' . implode(', ', $data));
				}
			}

			if (isset($_REQUEST['pendientes']))
			{
				$result = $db->query('SELECT
					pp.num_proveedor AS "IdPro",
					num_fact AS "NumFac",
					fecha AS "Fecha",
					num_cia AS "NumCia",
					nombre_corto AS "NombreCia",
					total AS "Importe"
				FROM
					pasivo_proveedores pp
					LEFT JOIN catalogo_companias cc USING (num_cia)
				ORDER BY
					pp.num_proveedor,
					num_cia,
					num_fact');

				if ($result)
				{
					$data = array();

					foreach ($result as $r)
					{
						$pieces = explode('/', $r['Fecha']);
						$r['Fecha'] = $pieces[2] . '-' . $pieces[1] . '-' . $pieces[0];

						$data[] = "('{$r['IdPro']}', '{$r['NumFac']}', '{$r['Fecha']}', '{$r['NumCia']}', '{$r['NombreCia']}', '{$r['Importe']}')";
					}

					$mysql->query('TRUNCATE `Pendientes`');

					$mysql->query('INSERT INTO `Pendientes` (`IdPro`, `NumFac`, `Fecha`, `NumCia`, `NombreCia`, `Importe`) VALUES ' . implode(', ', $data));
				}
			}

			$aclaraciones = 0;

			if (isset($_REQUEST['aclaraciones']))
			{
				$result = $mysql->query('SELECT
					`IdAclaracion`,
					`IdPro`,
					`NumFac`,
					`NumCia`,
					`Observaciones`,
					`TsIns`
				FROM
					`Aclaraciones`
				WHERE
					1
				ORDER BY
					`IdPro`,
					`NumFac`');

				if ($result)
				{
					$sql = '';

					foreach ($result as $r)
					{
						if (!$db->query('SELECT id FROM aclaraciones_facturas WHERE idaclaracion = ' . $r['IdAclaracion']))
						{
							$sql .= "INSERT INTO aclaraciones_facturas (idaclaracion, num_proveedor, num_fact, num_cia, observaciones, tsins ) VALUES ({$r['IdAclaracion']}, {$r['IdPro']}, {$r['NumFac']}, {$r['NumCia']}, '{$r['Observaciones']}', '{$r['TsIns']}');\n";

							$aclaraciones++;
						}
					}

					if ($sql != '')
					{
						$db->query($sql);
					}
				}

				$result = $db->query('SELECT idaclaracion, comentarios, tsmod, tsacl FROM aclaraciones_facturas WHERE send = TRUE');

				if ($result)
				{
					$db->query('UPDATE aclaraciones_facturas SET send = FALSE WHERE num_cia < 900 AND send = FALSE');

					foreach ($result as $r)
					{
						$mysql->query("UPDATE `Aclaraciones`
						SET
							`Comentarios` = '{$r['comentarios']}',
							`TsUpd` = " . ($r['tsmod'] != '' ? "'{$r['tsmod']}'" : 'NULL') . ",
							`TsAcl` = " . ($r['tsacl'] != '' ? "'{$r['tsacl']}'" : 'NULL') . "
						WHERE
							`IdAclaracion` = {$r['idaclaracion']}");
					}
				}
			}

			echo '<p style="font-weight:bold;font-size:12pt;">&iexcl;&iexcl;&iexcl;Actualizaci&oacute;n del portal completa!!!</p>';

			if ($aclaraciones > 0)
			{
				echo '<p style="font-weight:bold;font-size:12pt;color:#00C;>Hay ' . $aclaraciones . ' nuevas solicitudes para aclaraci&oacute;n de facturas</p>';
				echo '<p></p>';
			}

		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/ActualizarPortalPagos.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
