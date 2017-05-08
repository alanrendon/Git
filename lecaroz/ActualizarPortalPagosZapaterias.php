<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes,datestyle=YMD');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'actualizar':
			$mysql = new DBclass('mysql://lecar866_carlos:kilian88@www.lecaroz.com/lecar866_zapaterias', 'autocommit=yes');
			
			if (isset($_REQUEST['catalogo_proveedores'])) {
				$sql = '
					SELECT
						num_proveedor
							AS
								"IdPro",
						nombre
							AS
								"NombrePro",
						md5(pass_site)
							AS
								"Password"
					FROM
						catalogo_proveedores
					WHERE
						trim(pass_site) <> \'\'
					ORDER BY
						num_proveedor
				';
				$result = $db->query($sql);
				
				if ($result) {
					$data = array();
					foreach ($result as $r) {
						$data[] = '
							(
								\'' . $r['IdPro'] . '\',
								\'' . $r['NombrePro'] . '\',
								\'' . $r['Password'] . '\'
							)
						';
					}
					
					$sql = '
						TRUNCATE
							`Usuarios`
					';
					$mysql->query($sql);
					
					$sql = '
						INSERT INTO
							`Usuarios`
								(
									`IdPro`,
									`NombrePro`,
									`Password`
								)
						VALUES
							' . implode(",\n", $data) . '
					';
					$mysql->query($sql);
				}
			}
			
			if (isset($_REQUEST['catalogo_zapaterias'])) {
				$sql = '
					SELECT
						num_cia
							AS
								"NumCia",
						nombre
							AS
								"NombreCia"
					FROM
						catalogo_companias
					WHERE
						num_cia
							BETWEEN
									900
								AND
									998
					ORDER BY
						num_cia
				';
				$result = $db->query($sql);
				
				if ($result) {
					$data = array();
					foreach ($result as $r) {
						$data[] = '
							(
								\'' . $r['NumCia'] . '\',
								\'' . $r['NombreCia'] . '\'
							)
						';
					}
					
					$sql = '
						TRUNCATE
							`CatalogoZapaterias`
					';
					$mysql->query($sql);
					
					$sql = '
						INSERT INTO
							`CatalogoZapaterias`
								(
									`NumCia`,
									`NombreCia`
								)
						VALUES
							' . implode(",\n", $data) . '
					';
					$mysql->query($sql);
				}
			}
			
			if (isset($_REQUEST['pagos'])) {
				$sql = '
					SELECT
						c.num_proveedor
							AS
								"IdPro",
						num_cia
							AS
								"NumCia",
						nombre_corto
							AS
								"NombreCia",
						cuenta
							AS
								"IdBanco",
						folio
							AS
								"Folio",
						fecha
							AS
								"Fecha",
						facturas
							AS
								"Facturas",
						importe
							AS
								"Importe"
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
										900
									AND
										998
						AND
							site = \'TRUE\'
						AND
							fecha_cancelacion IS NULL
					ORDER BY
						c.num_proveedor,
						num_cia,
						cuenta,
						folio
				';
				$result = $db->query($sql);
				
				if ($result) {
					$sql = '
						UPDATE
							cheques
						SET
							site = \'FALSE\'
						WHERE
							site = \'TRUE\'
					';
					$db->query($sql);
					
					
					$data = array();
					foreach ($result as $r) {
						$data[] = '
							(
								\'' . $r['IdPro'] . '\',
								\'' . $r['NumCia'] . '\',
								\'' . $r['NombreCia'] . '\',
								\'' . $r['IdBanco'] . '\',
								\'' . $r['Folio'] . '\',
								\'' . $r['Fecha'] . '\',
								\'' . $r['Facturas'] . '\',
								\'' . $r['Importe'] . '\'
							)
						';
					}
					
					$sql = '
						DELETE FROM
							`Pagos`
						WHERE
							`TsIns` < SUBDATE(CURDATE(), INTERVAL 1 MONTH)
					';
					$mysql->query($sql);
					
					$sql = '
						INSERT INTO
							`Pagos`
								(
									`IdPro`,
									`NumCia`,
									`NombreCia`,
									`IdBanco`,
									`Folio`,
									`Fecha`,
									`Facturas`,
									`Importe`
								)
						VALUES
							' . implode(",\n", $data) . '
					';
					$mysql->query($sql);
				}
			}
			
			if (isset($_REQUEST['pendientes'])) {
				$sql = '
					SELECT
						fz.num_proveedor
							AS
								"IdPro",
						num_fact
							AS
								"NumFac",
						fecha
							AS
								"Fecha",
						num_cia
							AS
								"NumCia",
						nombre_corto
							AS
								"NombreCia",
						total
							AS
								"Importe"
					FROM
							facturas_zap
								fz
						LEFT JOIN
							catalogo_companias
								cc
									USING
										(
											num_cia
										)
					WHERE
						tspago IS NULL
					ORDER BY
						fz.num_proveedor,
						num_cia,
						num_fact
				';
				$result = $db->query($sql);
				
				if ($result) {
					$data = array();
					foreach ($result as $r) {
						$data[] = '
							(
								\'' . $r['IdPro'] . '\',
								\'' . $r['NumFac'] . '\',
								\'' . $r['Fecha'] . '\',
								\'' . $r['NumCia'] . '\',
								\'' . $r['NombreCia'] . '\',
								\'' . $r['Importe'] . '\'
							)
						';
					}
					
					$sql = '
						TRUNCATE
							`Pendientes`
					';
					$mysql->query($sql);
					
					$sql = '
						INSERT INTO
							`Pendientes`
								(
									`IdPro`,
									`NumFac`,
									`Fecha`,
									`NumCia`,
									`NombreCia`,
									`Importe`
								)
						VALUES
							' . implode(",\n", $data) . '
					';
					$mysql->query($sql);
				}
			}
			
			$aclaraciones = 0;
			if (isset($_REQUEST['aclaraciones'])) {
				$sql = '
					SELECT
						`IdAclaracion`,
						`IdPro`,
						`NumFac`,
						`Observaciones`,
						`TsIns`
					FROM
						`Aclaraciones`
					WHERE
						1
					ORDER BY
						`IdPro`,
						`NumFac`
				';
				$result = $mysql->query($sql);
				
				if ($result) {
					$sql = '';
					foreach ($result as $r) {
						if (!$db->query('SELECT id FROM aclaraciones_facturas WHERE idaclaracion = ' . $r['IdAclaracion'])) {
							$sql .= '
								INSERT INTO
									aclaraciones_facturas
										(
											idaclaracion,
											num_proveedor,
											num_fact,
											observaciones,
											tsins
										)
								VALUES
									(
										' . $r['IdAclaracion'] . ',
										' . $r['IdPro'] . ',
										' . $r['NumFac'] . ',
										\'' . $r['Observaciones'] . '\',
										\'' . $r['TsIns'] . '\'
									)
							' . ";\n";
							
							$aclaraciones++;
						}
					}
					
					if ($sql != '') {
						$db->query($sql);
					}
				}
			}
			
			echo '<p style="font-weight:bold;font-size:12pt;">&iexcl;&iexcl;&iexcl;Actualizaci&oacute;n del portal completa!!!</p>';
			
			if ($aclaraciones > 0) {
				echo '<p style="font-weight:bold;font-size:12pt;color:#00C;>Hay ' . $aclaraciones . ' nuevas solicitudes para aclaraci&oacute;n de facturas</p>';
				echo '<p></p>';
			}
			
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/zap/ActualizarPortalPagosZapaterias.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>