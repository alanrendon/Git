<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/class.auxinv.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'generar':
			$condiciones1[] = 'idadministrador IS NOT NULL';
			$condiciones2[] = 'idadministrador IS NOT NULL';
			
			$condiciones1[] = 'estatus = 1';
			$condiciones2[] = 'estatus = 1';
			
			if (isset($_REQUEST['color']) && $_REQUEST['color'] > 0) {
				$condiciones1[] = 'color_placa(placas) = ' . $_REQUEST['color'];
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones1[] = 'idadministrador = ' . $_REQUEST['admin'];
				$condiciones2[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
			/*
			@ Intervalo de compañías
			*/
			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
				$cias = array();
				
				$pieces = explode(',', $_REQUEST['cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}
				
				if (count($cias) > 0) {
					$condiciones1[] = 'num_cia IN (' . implode(', ', $cias) . ')';
					$condiciones2[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			/*
			@ Intervalo de vehículos
			*/
			if (isset($_REQUEST['vehiculos']) && trim($_REQUEST['vehiculos']) != '') {
				$cias = array();
				
				$pieces = explode(',', $_REQUEST['vehiculos']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$vehiculos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$vehiculos[] = $piece;
					}
				}
				
				if (count($vehiculos) > 0) {
					$condiciones1[] = 'idcamioneta IN (' . implode(', ', $vehiculos) . ')';
					$condiciones2[] = 'idcamioneta IN (' . implode(', ', $vehiculos) . ')';
				}
			}
			
			$sql = '
				(
					SELECT
						*
					FROM
						(
							SELECT
								1
									AS
										tipo,
								administrador,
								num_cia,
								nombre_cia,
								numero,
								placas,
								color,
								modelo,
								CASE
									WHEN ver1_ok IS NULL THEN
										\'FALSE\'
									ELSE
										ver1_ok
								END
									AS
										ver1_ok,
								CASE
									WHEN ver1_rec IS NULL THEN
										\'FALSE\'
									ELSE
										ver1_rec
								END
									AS
										ver1_rec,
								CASE
									WHEN ver2_ok IS NULL THEN
										\'FALSE\'
									ELSE
										ver2_ok
								END
									AS
										ver2_ok,
								CASE
									WHEN ver2_rec IS NULL THEN
										\'FALSE\'
									ELSE
										ver2_rec
								END
									AS
										ver2_rec
							FROM
								(
									SELECT
										nombre_administrador
											AS
												administrador,
										num_cia,
										cc.nombre_corto
											AS
												nombre_cia,
										idcamioneta
											AS
										numero,
										placas,
										color_placa(placas)
											AS
												color,
										modelo,
										(
											SELECT
												ver1_ok
											FROM
												revisiones_vehiculares
											WHERE
													idcamioneta = cv.idcamioneta
												AND
													anio = ' . $_REQUEST['anio'] . '
										)
											AS
												ver1_ok,
										(
											SELECT
												ver1_rec
											FROM
												revisiones_vehiculares
											WHERE
													idcamioneta = cv.idcamioneta
												AND
													anio = ' . $_REQUEST['anio'] . '
										)
											AS
												ver1_rec,
										(
											SELECT
												ver2_ok
											FROM
												revisiones_vehiculares
											WHERE
													idcamioneta = cv.idcamioneta
												AND
													anio = ' . ($_REQUEST['periodo'] == 1 ? $_REQUEST['anio'] - 1 : $_REQUEST['anio']) . '
										)
											AS
												ver2_ok,
										(
											SELECT
												ver2_rec
											FROM
												revisiones_vehiculares
											WHERE
													idcamioneta = cv.idcamioneta
												AND
													anio = ' . ($_REQUEST['periodo'] == 1 ? $_REQUEST['anio'] - 1 : $_REQUEST['anio']) . '
										)
											AS
												ver2_rec
									FROM
											catalogo_camionetas
												cv
										LEFT JOIN
											catalogo_companias
												cc
													USING
														(
															num_cia
														)
										LEFT JOIN
											catalogo_administradores
												ca
													USING
														(
															idadministrador
														)
									WHERE
										' . implode(' AND ', $condiciones1) . '
								)
									result
						)
							vehiculos
					WHERE
						ver' . $_REQUEST['periodo'] . '_ok = \'FALSE\'
				)
				
				UNION
				
				(
					SELECT
						*
					FROM
						(
							SELECT
								2
									AS
										tipo,
								administrador,
								num_cia,
								nombre_cia,
								numero,
								placas,
								color,
								modelo,
								CASE
									WHEN ver1_ok IS NULL THEN
										\'FALSE\'
									ELSE
										ver1_ok
								END
									AS
										ver1_ok,
								CASE
									WHEN ver1_rec IS NULL THEN
										\'FALSE\'
									ELSE
										ver1_rec
								END
									AS
										ver1_rec,
								CASE
									WHEN ver2_ok IS NULL THEN
										\'FALSE\'
									ELSE
										ver2_ok
								END
									AS
										ver2_ok,
								CASE
									WHEN ver2_rec IS NULL THEN
										\'FALSE\'
									ELSE
										ver2_rec
								END
									AS
										ver2_rec
							FROM
								(
									SELECT
										nombre_administrador
											AS
												administrador,
										num_cia,
										cc.nombre_corto
											AS
												nombre_cia,
										idcamioneta
											AS
										numero,
										placas,
										color_placa(placas)
											AS
												color,
										modelo,
										(
											SELECT
												ver1_ok
											FROM
												revisiones_vehiculares
											WHERE
													idcamioneta = cv.idcamioneta
												AND
													anio = ' . $_REQUEST['anio'] . '
										)
											AS
												ver1_ok,
										(
											SELECT
												ver1_rec
											FROM
												revisiones_vehiculares
											WHERE
													idcamioneta = cv.idcamioneta
												AND
													anio = ' . $_REQUEST['anio'] . '
										)
											AS
												ver1_rec,
										(
											SELECT
												ver2_ok
											FROM
												revisiones_vehiculares
											WHERE
													idcamioneta = cv.idcamioneta
												AND
													anio = ' . ($_REQUEST['periodo'] == 1 ? $_REQUEST['anio'] - 1 : $_REQUEST['anio']) . '
										)
											AS
												ver2_ok,
										(
											SELECT
												ver2_rec
											FROM
												revisiones_vehiculares
											WHERE
													idcamioneta = cv.idcamioneta
												AND
													anio = ' . ($_REQUEST['periodo'] == 1 ? $_REQUEST['anio'] - 1 : $_REQUEST['anio']) . '
										)
											AS
												ver2_rec
									FROM
											catalogo_camionetas
												cv
										LEFT JOIN
											catalogo_companias
												cc
													USING
														(
															num_cia
														)
										LEFT JOIN
											catalogo_administradores
												ca
													USING
														(
															idadministrador
														)
									WHERE
										' . implode(' AND ', $condiciones2) . '
								)
									result
						)
							vehiculos
					WHERE
						ver' . ($_REQUEST['periodo'] == 1 ? 2 : 1) . '_rec = \'FALSE\'
				)
				ORDER BY
					administrador,
					num_cia,
					numero
			';
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/doc/CartaCertificadosVerificación.tpl');
			$tpl->prepare();
			
			if ($result) {
				$admin = NULL;
				
				foreach ($result as $r) {
					if ($admin != $r['administrador']) {
						if ($admin != NULL) {
							$tpl->assign('carta.salto', '<br style="page-break-after:always;" />');
						}
						
						$admin = $r['administrador'];
						
						$tpl->newBlock('carta');
						$tpl->assign('dia', date('j'));
						$tpl->assign('mes', mes_escrito(date('n')));
						$tpl->assign('anio', date('Y'));
						$tpl->assign('admin', utf8_encode($r['administrador']));
						$tpl->assign('num', $_REQUEST['periodo']);
					}
					if ($r['tipo'] == 1) {
						$tpl->newBlock('revision');
						$tpl->assign('num_camioneta', $r['numero']);
						$tpl->assign('nombre_cia', utf8_encode($r['nombre_cia']));
						$tpl->assign('placas', utf8_encode($r['placas']));
						$tpl->assign('modelo', utf8_encode($r['modelo']));
						$tpl->assign('recibo', $r['ver' . ($_REQUEST['periodo'] == 1 ? 2 : 1) . '_rec'] == 'f' ? 'N/RECIBO' : '&nbsp;');
					}
					if ($r['tipo'] == 2) {
						$tpl->newBlock('pendiente');
						$tpl->assign('num_camioneta', $r['numero']);
						$tpl->assign('nombre_cia', utf8_encode($r['nombre_cia']));
						$tpl->assign('placas', utf8_encode($r['placas']));
						$tpl->assign('modelo', utf8_encode($r['modelo']));
						$tpl->assign('recibo', $r['ver' . ($_REQUEST['periodo'] == 1 ? 2 : 1) . '_rec'] == 'f' ? 'N/RECIBO' : '&nbsp;');
					}
				}
			}
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/doc/GenerarCartasCertificadosVerificaciones.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y'));

$sql = '
	SELECT
		idadministrador
			AS
				id,
		nombre_administrador
			AS
				nombre
	FROM
		catalogo_administradores
	ORDER BY
		nombre
';
$admins = $db->query($sql);

foreach ($admins as $a) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $a['id']);
	$tpl->assign('nombre', $a['nombre']);
}

$tpl->printToScreen();
?>
