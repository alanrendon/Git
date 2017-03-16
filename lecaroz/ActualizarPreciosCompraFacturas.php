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
		case 'mp':
			$sql = '
				SELECT
					nombre
				FROM
					catalogo_mat_primas
				WHERE
						codmp = ' . $_REQUEST['codmp'] . '
					AND
						codmp
							IN
								(
									SELECT
										codmp
									FROM
										precios_guerra
									WHERE
										precio_compra > 0
									GROUP BY
										codmp
								)
			';
			$result = $db->query($sql);
			
			echo $result[0]['nombre'];
		break;
		
		case 'buscar':
			$cias = array();
			$omitir = array();
			$pros = array();
			
			$conditions[] = 'codmp = ' . $_REQUEST['codmp'];
			
			// Periodo de búsqueda
			if (isset($_REQUEST['fecha2'])) {
				$conditions[] = 'fecha_mov BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
			}
			else {
				$conditions[] = 'fecha_mov = \'' . $_REQUEST['fecha1'] . '\'';
			}
			
			// Intervalo de compañías
			if (isset($_REQUEST['cias'])) {
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
					$conditions[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			// Intervalo de compañías omitidas
			if (isset($_REQUEST['omitir'])) {
				$pieces = explode(',', $_REQUEST['omitir']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir[] = $piece;
					}
				}
				
				if (count($omitir) > 0) {
					$conditions[] = 'num_cia NOT IN (' . implode(', ', $omitir) . ')';
				}
			}
			
			// Intervalo de proveedores
			if (isset($_REQUEST['pros'])) {
				$pieces = explode(',', $_REQUEST['pros']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$pros[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$pros[] = $piece;
					}
				}
				
				if (count($pros) > 0) {
					$conditions[] = 'fr.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}
			
			$conditions[] = '
				(
					fr.num_cia,
					fr.num_proveedor,
					fr.num_fac
				)
					NOT IN
						(
							SELECT
								num_cia,
								num_proveedor,
								num_fact
							FROM
								facturas_pagadas
							WHERE
									num_proveedor
										IN
											(
												SELECT
													num_proveedor
												FROM
													precios_guerra
												WHERE
													precio_compra > 0
												GROUP BY
													num_proveedor
											)
								AND
									fecha_mov ' . (isset($_REQUEST['fecha2']) ? 'BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'' : '= \'' . $_REQUEST['fecha1'] . '\'') . '
						)
			';
			
			$sql = '
				SELECT
					idfact_rosticeria
						AS
							id,
					fr.num_proveedor
						AS
							num_pro,
					cp.nombre
						AS
							nombre_pro,
					num_fac
						AS
							num_fact,
					fecha_mov
						AS
							fecha,
					num_cia,
					cc.nombre
						AS
							nombre_cia,
					codmp,
					cmp.nombre
						AS
							nombre_mp,
					kilos,
					precio,
					total
						AS
							importe
				FROM
						fact_rosticeria
							fr
					LEFT JOIN
						catalogo_proveedores
							cp
								USING
									(
										num_proveedor
									)
					LEFT JOIN
						catalogo_companias
							cc
								USING
									(
										num_cia
									)
					LEFT JOIN
						catalogo_mat_primas
							cmp
								USING
									(
										codmp
									)
				WHERE
					' . implode(' AND ', $conditions) . '
				ORDER BY
					fr.num_proveedor,
					num_cia,
					num_fact
			';
			$result = $db->query($sql);
			
			if ($result) {
				$code = '<table class="tabla_captura"><tr><th scope="col"><input name="checkall" type="checkbox" id="checkall" checked="checked" /></th><th scope="col">Proveedor</th></th><th scope="col">Factura</th><th scope="col">Compa&ntilde;&iacute;a</th><th scope="col">Fecha</th><th scope="col">Producto</th><th scope="col">Kilos</th><th scope="col">Precio</th><th scope="col">Importe</th></tr>';
				
				foreach ($result as $i => $r) {
					$code .= '<tr class="linea_' . (($i + 1) % 2 == 0 ? 'on' : 'off') . '"><td align="center"><input name="id[]" type="checkbox" id="id" value="' . $r['id'] . '" checked="checked" /></td><td>' . $r['num_pro'] . ' ' . $r['nombre_pro'] . '</td><td align="right">' . $r['num_fact'] . '</td><td>' . $r['num_cia'] . ' ' . utf8_encode($r['nombre_cia']) . '</td><td align="center">' . $r['fecha'] . '</td><td>' . $r['codmp'] . ' ' . utf8_encode($r['nombre_mp']) . '</td><td align="right">' . number_format($r['kilos'], 2, '.', ',') . '</td><td align="right">' . number_format($r['precio'], 2, '.', ',') . '</td><td align="right">' . number_format($r['importe'], 2, '.', ',') . '</td></tr>';
				}
				$code .= '</table><p><input name="nuevo" type="button" class="boton" id="actualizar" value="Actualizar" /><img src="imagenes/_loading.gif" name="loading_upd" width="16" height="16" id="loading_upd" />';
				
				echo $code;
			}
		break;
		
		case 'actualizar':
			$ids = implode(', ', $_REQUEST['id']);
			$precio_compra = get_val($_REQUEST['precio_compra']);
			
			$sql = '
				UPDATE
					fact_rosticeria
				SET
					precio = ' . $precio_compra . ',
					total = kilos * ' . $precio_compra . ',
					precio_unidad = kilos * ' . $precio_compra . ' / cantidad
				WHERE
					idfact_rosticeria
						IN
							(
								' . $ids . '
							)
			' . ";\n";
			
			$sql .= '
				DELETE FROM
					mov_inv_real
				WHERE
					(
						num_cia,
						codmp,
						fecha,
						tipo_mov,
						descripcion
					)
						IN
							(
								SELECT
									num_cia,
									codmp,
									fecha_mov,
									\'FALSE\'::boolean,
									\'COMPRA F. NO. \' || num_fac
								FROM
									fact_rosticeria
								WHERE
									idfact_rosticeria
										IN
											(
												' . $ids . '
											)
							)
			' . ";\n";
			
			$sql .= '
				INSERT INTO
					mov_inv_real
						(
							num_cia,
							codmp,
							fecha,
							cod_turno,
							tipo_mov,
							cantidad,
							precio,
							total_mov,
							precio_unidad,
							descripcion,
							num_proveedor
						)
				SELECT
					num_cia,
					codmp,
					fecha_mov,
					11,
					\'FALSE\'::boolean,
					cantidad,
					precio,
					total,
					precio_unidad,
					\'COMPRA F. NO. \' || num_fac,
					num_proveedor
				FROM
					fact_rosticeria
				WHERE
					idfact_rosticeria
						IN
							(
								' . $ids . '
							)
			' . ";\n";
			
			$sql .= '
				DELETE FROM
					total_fac_ros
				WHERE
					(
						num_cia,
						num_proveedor,
						num_fac,
						fecha
					)
						IN
							(
								SELECT
									num_cia,
									num_proveedor,
									num_fac,
									fecha_mov
								FROM
									fact_rosticeria
								WHERE
									idfact_rosticeria
										IN
											(
												' . $ids . '
											)
							)
			' . ";\n";
			
			$sql .= '
				INSERT INTO
					total_fac_ros
						(
							num_cia,
							num_proveedor,
							num_fac,
							fecha,
							total_fac,
							contado,
							credito,
							porc795,
							porc13,
							pagado
						)
				SELECT
					num_cia,
					num_proveedor,
					num_fac,
					fecha_mov,
					sum(total),
					round((sum(total) * porcentaje_13 / 100)::numeric, 2),
					sum(total) - round((sum(total) * porcentaje_13 / 100)::numeric, 2),
					porcentaje_795,
					porcentaje_13,
					\'FALSE\'::boolean
				FROM
						fact_rosticeria
					LEFT JOIN
						porcentajes_facturas
							USING
								(
									num_cia
								)
				WHERE
					(
						num_cia,
						num_proveedor,
						num_fac,
						fecha_mov
					)
						IN
							(
								SELECT
									num_cia,
									num_proveedor,
									num_fac,
									fecha_mov
								FROM
									fact_rosticeria
								WHERE
									idfact_rosticeria
										IN
											(
												' . $ids . '
											)
							)
				GROUP BY
					num_cia,
					num_proveedor,
					num_fac,
					fecha_mov,
					porcentaje_795,
					porcentaje_13
				ORDER BY
					num_proveedor,
					num_fac,
					num_cia
			' . ";\n";
			
			$sql .= '
				DELETE FROM
					pasivo_proveedores
				WHERE
					(
						num_cia,
						num_proveedor,
						num_fact,
						fecha
					)
						IN
							(
								SELECT
									num_cia,
									num_proveedor,
									num_fac,
									fecha_mov
								FROM
									fact_rosticeria
								WHERE
									idfact_rosticeria
										IN
											(
												' . $ids . '
											)
							)
			' . ";\n";
			
			$sql .= '
				INSERT INTO
					pasivo_proveedores
						(
							num_cia,
							num_proveedor,
							num_fact,
							fecha,
							descripcion,
							codgastos,
							total
						)
				SELECT
					num_cia,
					num_proveedor,
					num_fac,
					fecha,
					\'COMPRA F. NO. \' || num_fac,
					33,
					credito
				FROM
					total_fac_ros
				WHERE
					(
						num_cia,
						num_proveedor,
						num_fac,
						fecha
					)
						IN
							(
								SELECT
									num_cia,
									num_proveedor,
									num_fac,
									fecha_mov
								FROM
									fact_rosticeria
								WHERE
									idfact_rosticeria
										IN
											(
												' . $ids . '
											)
							)
			' . ";\n";
			
			$sql .= '
				DELETE FROM
					movimiento_gastos
				WHERE
					(
						num_cia,
						fecha,
						codgastos,
						concepto
					)
						IN
							(
								SELECT
									num_cia,
									fecha,
									33,
									\'COMPRA F. NO. \' || num_fac
								FROM
									fact_rosticeria
								WHERE
									idfact_rosticeria
										IN
											(
												' . $ids . '
											)
							)
			' . ";\n";
			
			$sql .= '
				INSERT INTO
					movimiento_gastos
						(
							num_cia,
							fecha,
							codgastos,
							concepto,
							captura,
							importe
						)
				SELECT
					num_cia,
					fecha,
					33,
					\'COMPRA F. NO. \' || num_fac,
					\'TRUE\',
					contado
				FROM
					total_fac_ros
				WHERE
					(
						num_cia,
						num_proveedor,
						num_fac,
						fecha
					)
						IN
							(
								SELECT
									num_cia,
									num_proveedor,
									num_fac,
									fecha_mov
								FROM
									fact_rosticeria
								WHERE
									idfact_rosticeria
										IN
											(
												' . $ids . '
											)
							)
			' . ";\n";
			
			//echo $sql;
			$db->query($sql);
			
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ros/ActualizarPreciosCompraFacturas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
