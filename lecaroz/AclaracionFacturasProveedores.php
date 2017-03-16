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
		case 'buscar':
			// Actualizar todas las solicitudes nuevas y no aclaradas
			$sql = '
				UPDATE
					aclaraciones_facturas
				SET
					estatus = result.estatus,
					num_cia = result.num_cia,
					fecha_factura = result.fecha_factura,
					concepto = result.concepto,
					importe = result.importe,
					num_cia_pago = result.num_cia_pago,
					fecha_pago = result.fecha_pago,
					cuenta = result.cuenta,
					folio = result.folio,
					tipo_pago = result.tipo_pago,
					fecha_cobro = result.fecha_cobro,
					tsupd = now()
					FROM
						(
							SELECT
								af.id,
								CASE
									/* Factura registrada pero sin copia */
									WHEN pp.id IS NOT NULL AND pp.copia_fac = FALSE THEN
										1
									/* Factura registrada y pendiente de pago */
									WHEN pp.id IS NOT NULL AND pp.copia_fac = TRUE THEN
										2
									/* Factura registrada y pagada */
									WHEN fp.id IS NOT NULL THEN
										3
									/* Error de sistema, la factura esta registrada pero no se encuentra en pendientes o pagadas */
									WHEN f.id IS NOT NULL AND pp.id IS NULL AND fp.id IS NULL THEN
										-1
									/* Factura no se encuentra registrada en el sistema */
									ELSE
										0
								END
									AS
										estatus,
								f.num_cia,
								f.fecha
									AS
										fecha_factura,
								CASE
									WHEN f.id IS NOT NULL THEN
										f.concepto
									WHEN fg.id IS NOT NULL THEN
										\'GAS\'
									WHEN fr.id IS NOT NULL THEN
										\'ROSTICERIA\'
									ELSE
										\'\'
								END
									AS
										concepto,
								CASE
									WHEN f.id IS NOT NULL THEN
										f.total
									WHEN fg.id IS NOT NULL THEN
										fg.total
									WHEN fr.id IS NOT NULL THEN
										fr.credito
									ELSE
										0
								END
									AS
										importe,
								fp.num_cia
									AS
										num_cia_pago,
								fp.fecha_cheque
									AS
										fecha_pago,
								fp.cuenta,
								fp.folio_cheque
									AS
										folio,
								CASE
									WHEN cod_mov = 5 THEN
										1
									WHEN cod_mov = 41 THEN
										2
									ELSE
										NULL
								END
									AS
										tipo_pago,
								ec.fecha_con
									AS
										fecha_cobro
							FROM
									aclaraciones_facturas
										af
								LEFT JOIN
									facturas
										f
											USING
												(
													num_proveedor,
													num_fact
												)
								LEFT JOIN
									factura_gas
										fg
											USING
												(
													num_proveedor,
													num_fact
												)
								LEFT JOIN
									total_fac_ros
										fr
											ON
												(
														fr.num_proveedor = af.num_proveedor
													AND
														fr.num_fac = af.num_fact
												)
								LEFT JOIN
									pasivo_proveedores
										pp
											ON
												(
														pp.num_proveedor = af.num_proveedor
													AND
														pp.num_fact = af.num_fact
												)
								LEFT JOIN
									facturas_pagadas
										fp
											ON
												(
														fp.num_proveedor = af.num_proveedor
													AND
														fp.num_fact = af.num_fact
												)
								LEFT JOIN
									estado_cuenta
										ec
											ON
												(
														ec.num_cia = fp.num_cia
													AND
														ec.cuenta = fp.cuenta
													AND
														ec.folio = fp.folio_cheque
												)
							WHERE
									af.estatus IS NULL
								OR
									af.tsacl IS NULL
						)
							AS
								result
				WHERE
					aclaraciones_facturas.id = result.id
			';
			$db->query($sql);
			
			$conditions = array();
			
			// No incluir aclaradas
			if (!isset($_REQUEST['aclaradas'])) {
				$conditions[] = 'tsacl IS NULL';
			}
			 // No incluir pendientes
			 if (!isset($_REQUEST['pendientes'])) {
			 	$conditions[] = 'tsacl IS NOT NULL';
			 }
			
			// Periodo de búsqueda
			if (isset($_REQUEST['fecha1'])) {
				if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '') {
					$conditions[] = 'tsins::date BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				}
				else if ($_REQUEST['fecha1'] != '') {
					$conditions[] = 'tsins::date = \'' . $_REQUEST['fecha1'] . '\'';
				}
			}
			
			// Intervalo de compañías
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
					$conditions[] = 'f.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			// Intervalo de proveedores
			if (isset($_REQUEST['pros']) && trim($_REQUEST['pros']) != '') {
				$pros = array();
				
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
					$conditions[] = 'af.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}
			
			$sql = '
				SELECT
					af.id,
					af.num_proveedor
						AS
							num_pro,
					af.num_cia,
					cc.nombre_corto
						AS
							nombre_cia,
					af.num_proveedor,
					cp.nombre
						AS
							nombre_pro,
					num_fact,
					fecha_factura,
					concepto,
					importe,
					CASE
						/* Factura no se encuentra registrada en el sistema */
						WHEN estatus = 0 THEN
							\'<span style="font-weight:bold;color:#C00;">NO REGISTRADA</span>\'
						/* Factura registrada pero sin copia */
						WHEN estatus = 1 THEN
							\'<span style="font-weight:bold;color:#CC0;">REGISTRADA, SIN COPIA</span>\'
						/* Factura registrada y pendiente de pago */
						WHEN estatus = 2 THEN
							\'<span style="font-weight:bold;color:#0C0;">REGISTRADA, PENDIENTE DE PAGO</span>\'
						/* Factura registrada y pagada */
						WHEN estatus = 3 THEN
							\'<span style="font-weight:bold;color:#00C;">PAGADA</span>\' || (
								CASE
									WHEN fecha_cobro IS NULL AND now()::date - fecha_pago > 7 THEN
										\' <span style="color:#C00;">(\' || now()::date - fecha_pago || \' D&Iacute;AS SIN COBRAR)</span>\'
									WHEN fecha_cobro IS NULL AND now()::date - fecha_pago <= 7 THEN
										\' <span style="color:#00E;">(PENDIENTE DE COBRO)</span>\'
									ELSE
										\'\'
								END
							)
						WHEN estatus = -1 THEN
							\'<span style="font-weight:bold;color:#F00;">ERROR DE SISTEMA</span>. LA FACTURA ESTA REGISTRADA PERO NO SE ENCUENTRA EN PENDIENTES O PAGADAS\'
					END
						AS
							estatus,
					af.observaciones,
					fecha_pago,
					CASE
						WHEN af.cuenta = 1 THEN
							\'BANORTE\'
						WHEN af.cuenta = 2 THEN
							\'SANTANDER\'
						ELSE
							NULL
					END
						AS
							banco,
					folio,
					CASE
						WHEN tipo_pago = 1 THEN
							\'Cheque\'
						WHEN tipo_pago = 2 THEN
							\'Transferencia\'
						ELSE
							NULL
					END
						AS
							tipo,
					fecha_cobro,
					comentarios,
					tsins,
					CASE
						WHEN tsacl IS NOT NULL THEN
							\'t\'
						ELSE
							\'f\'
					END
						AS
							aclarado
				FROM
						aclaraciones_facturas
							af
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
			';
			if (count($conditions) > 0) {
				$sql .= '
					WHERE
						' . implode(' AND ', $conditions) . '
				';
			}
			$sql .= '
				ORDER BY
					num_proveedor,
					num_fact
			';
			$result = $db->query($sql);
			
			if ($result) {
				$html = '';
				
				$search = array("\n", '[', ']');
				$replace = array('<br />', '<strong>[', ']</strong>');
				
				$num_pro = NULL;
				foreach ($result as $r) {
					if ($num_pro != $r['num_pro']) {
						if ($num_pro != NULL) {
							$html .= '</div></div>';
						}
						
						$num_pro = $r['num_pro'];
						
						$html .= '<div id="proveedor">
  <div id="nombre_pro">' . $r['num_pro'] . ' ' . $r['nombre_pro'] . '<span style="float:right;"><img src="imagenes/arrow_down.png" id="show_pro" width="16" height="16" /></span>
  </div>
  <div id="facturas">';
					}
					$html .= '<div id="factura">
      <table width="100%">
        <tr>
          <th width="10%">Factura</th>
          <th width="15%">Fecha de Solicitud </th>
          <th width="25%">Estatus</th>
          <th>Observaciones<span style="float:right;"><img src="imagenes/arrow_down.png" id="show_fac" width="16" height="16" /></span></th>
        </tr>
        <tr>
          <td align="center">' . $r['num_fact'] . '</td>
          <td align="center">' . $r['tsins'] . '</td>
          <td align="center">' . $r['estatus'] . '</td>
          <td>' . $r['observaciones'] . '</td>
        </tr>
      </table>
	  <div id="detalle">
      <table width="100%" id="t1">
        <tr>
		  <th width="26%" scope="col">Compa&ntilde;&iacute;a</th>
          <th width="8%" scope="col">Fecha</th>
          <th width="26%" scope="col">Concepto</th>
          <th width="8%" scope="col">Importe</th>
          <th width="8%" scope="col">Fecha de pago</th>
          <th width="8%" scope="col">Banco</th>
          <th width="8%" scope="col">' . $r['tipo'] . '</th>
		  <th width="8%" scope="col">Fecha de cobro</th>
        </tr>
        <tr id="desglose">
		  <td align="center"><input name="num_pro" type="hidden" id="num_pro" value="' . $r['num_pro'] . '" /><input name="num_fact" type="hidden" id="num_fact" value="' . $r['num_fact'] . '" />' . ($r['num_cia'] > 0 ? $r['num_cia'] . ' ' . $r['nombre_cia'] : '&nbsp;') . '</td>
          <td align="center">' . $r['fecha_factura'] . '</td>
          <td align="center">' . $r['concepto'] . '</td>
          <td align="center">' . ($r['importe'] > 0 ? number_format($r['importe'], 2, '.', ',') : '') . '</td>
          <td align="center">' . $r['fecha_pago'] . '</td>
          <td align="center">' . $r['banco'] . '</td>
          <td align="center"' . ($r['tipo'] == 'Transferencia' ? ' style="color:#0C0;"' : '') . '>' . $r['folio'] . '</td>
		  <td align="center">' . $r['fecha_cobro'] . '</td>
        </tr>
    </table>
      ';
		if ($r['aclarado'] == 't') {
			$html .= '<table width="100%" id="t2">
        <tr>
          <th scope="col">Comentarios</th>
        </tr>
		<tr>
          <td align="center"><div id="bloque_comentarios">' . ($r['comentarios'] != '' ? '<ul>' . $r['comentarios'] . '</ul>' : '&nbsp;') . '</div></td>
        </tr>
    </table>';
		}
		else {
			$html .= '<table width="100%" id="t2">
        <tr>
          <th scope="col">Comentarios</th>
          <th width="10%" scope="col"><img src="imagenes/tool16x16.png" width="16" height="16" /></th>
        </tr>
		<tr>
          <td align="center"><div id="bloque_comentarios">' . ($r['comentarios'] != '' ? '<ul>' . $r['comentarios'] . '</ul>' : '&nbsp;') . '</div><input name="id" type="hidden" id="id" value="' . $r['id'] . '" /><textarea name="comentarios" id="comentarios" style="width:98%;"></textarea></td>
          <td align="center"><input name="actualizar" type="button" id="actualizar" style="width:70%;" value="Actualizar" />
          <br />
          <input name="aclarar" type="button" id="aclarar" style="width:70%;" value="Aclarar" /></td>
        </tr>
    </table>';
	}
	$html .= '</div>
  </div>';
				}
				if ($num_pro != NULL) {
					$html .= '</div></div>';
				}
				
				echo $html;
			}
		break;
		
		case 'actualizar':
			$comentarios = '<li><strong>[' . date('Y-m-d G:i:s]') . ']</strong> ';
			
			if (!isset($_REQUEST['comentarios']) || trim($_REQUEST['comentarios']) == '') {
				$sql = '
					SELECT
						CASE
							/* Factura no se encuentra registrada en el sistema */
							WHEN estatus = 0 THEN
								\'LA FACTURA NO SE ENCUENTRA REGISTRADA EN NUESTRO SISTEMA\'
							/* Factura registrada pero sin copia */
							WHEN estatus = 1 THEN
								\'NO TENEMOS LA FACTURA ORIGINAL Y/O NO HA SIDO VALIDADA EN SISTEMA\'
							/* Factura registrada y pendiente de pago */
							WHEN estatus = 2 THEN
								\'FACTURA PENDIENTE PARA PAGO\'
							/* Factura registrada y pagada */
							WHEN estatus = 3 THEN
								\'FACTURA PAGADA EL \' || fecha_pago || \' \' || (
									CASE
										WHEN fecha_cobro IS NULL AND now()::date - fecha_pago > 7 THEN
											\' (\' || now()::date - fecha_pago || \' D&Iacute;AS SIN COBRAR, GENERALMENTE POR PROBLEMAS CON EL SISTEMA DEL BANCO, ESTAMOS REVISANDO EL CASO)\'
										WHEN fecha_cobro IS NULL AND now()::date - fecha_pago <= 7 THEN
											\' (PENDIENTE DE COBRO)\'
										ELSE
											\' (COBRADA EL \' || fecha_cobro || \')\'
									END
								)
							WHEN estatus = -1 THEN
								\'ERROR DE SISTEMA. ESTAMOS REVISANDO EL CASO\'
						END
							AS
								comentarios
					FROM
						aclaraciones_facturas
					WHERE
						id = ' . $_REQUEST['id'] . '
				';
				$result = $db->query($sql);
				
				$comentarios .= $result[0]['comentarios'];
			}
			else {
				$comentarios .= $_REQUEST['comentarios'];
			}
			
			$comentarios .= '</li>';
			
			$sql = '
				UPDATE
					aclaraciones_facturas
				SET
					comentarios = \'' . $comentarios . '\' || CASE WHEN comentarios IS NOT NULL THEN comentarios ELSE \'\' END,
					iduser = ' . $_SESSION['iduser'] . ',
					tsmod = now(),
					send = \'TRUE\'
				WHERE
					id = ' . $_REQUEST['id'] . '
			';
			$db->query($sql);
			
			$sql = '
				SELECT
					comentarios
				FROM
					aclaraciones_facturas
				WHERE
					id = ' . $_REQUEST['id'] . '
			';
			$result = $db->query($sql);
			
			$search = array("\n", '[', ']');
			$replace = array('<br />', '<strong>[', ']</strong>');
			
			echo str_replace($search, $replace, $result[0]['comentarios']);
		break;
		
		case 'aclarar':
			$comentarios = '<li><strong>[' . date('[Y-m-d G:i:s]') . '</strong> ';
			
			if (!isset($_REQUEST['comentarios']) || trim($_REQUEST['comentarios']) == '') {
				$sql = '
					SELECT
						CASE
							/* Factura no se encuentra registrada en el sistema */
							WHEN estatus = 0 THEN
								\'LA FACTURA NO SE ENCUENTRA REGISTRADA EN NUESTRO SISTEMA\'
							/* Factura registrada pero sin copia */
							WHEN estatus = 1 THEN
								\'NO TENEMOS LA FACTURA ORIGINAL Y/O NO HA SIDO VALIDADA EN SISTEMA\'
							/* Factura registrada y pendiente de pago */
							WHEN estatus = 2 THEN
								\'FACTURA PENDIENTE PARA PAGO\'
							/* Factura registrada y pagada */
							WHEN estatus = 3 THEN
								\'FACTURA PAGADA EL \' || fecha_pago || \' \' || (
									CASE
										WHEN fecha_cobro IS NULL AND now()::date - fecha_pago > 7 THEN
											\' (\' || now()::date - fecha_pago || \' D&Iacute;AS SIN COBRAR, GENERALMENTE POR PROBLEMAS CON EL SISTEMA DEL BANCO, ESTAMOS REVISANDO EL CASO)\'
										WHEN fecha_cobro IS NULL AND now()::date - fecha_pago <= 7 THEN
											\' (PENDIENTE DE COBRO)\'
										ELSE
											\' (COBRADA EL \' || fecha_cobro || \')\'
									END
								)
							WHEN estatus = -1 THEN
								\'ERROR DE SISTEMA. ESTAMOS REVISANDO EL CASO\'
						END
							AS
								comentarios
					FROM
						aclaraciones_facturas
					WHERE
						id = ' . $_REQUEST['id'] . '
				';
				$result = $db->query($sql);
				
				$comentarios .= $result[0]['comentarios'];
			}
			else {
				$comentarios .= $_REQUEST['comentarios'];
			}
			
			$comentarios .= '</li>';
			
			$sql = '
				UPDATE
					aclaraciones_facturas
				SET
					comentarios = \'' . $comentarios . '\' ||
						CASE
							WHEN comentarios IS NOT NULL THEN
								comentarios
							ELSE \'\'
						END,
					iduser = ' . $_SESSION['iduser'] . ',
					tsmod = now(),
					tsacl = now(),
					send = \'TRUE\'
				WHERE
					id = ' . $_REQUEST['id'] . '
			';
			$db->query($sql);
		break;
		
		case 'desglose':
			$tipo = 1;
			
			/*
			@ Buscar en materias primas
			*/
			$sql = '
				SELECT
					id
				FROM
					entrada_mp
				WHERE
						num_proveedor = ' . $_REQUEST['num_pro'] . '
					AND
						num_documento = ' . $_REQUEST['num_fact'] . '
			';
			$result = $db->query($sql);
			
			/*
			@ Buscar en gas
			*/
			if (!$result) {
				$tipo = 2;
				
				$sql = '
					SELECT
						id
					FROM
						factura_gas
					WHERE
							num_proveedor = ' . $_REQUEST['num_pro'] . '
						AND
							num_fact = ' . $_REQUEST['num_fact'] . '
				';
				$result = $db->query($sql);
			}
			
			if (!$result) {
				echo -1;
			}
			else {
				$data = array();
				foreach ($result as $r) {
					$data[] = 'id[]=' . $r['id'];
				}
				
				echo 'tipo=' . $tipo . '&' . implode('&', $data);
			}
		break;
		
		case 'showDesglose':
			$tpl = new TemplatePower('plantillas/ban/DesgloseFactura.tpl');
			$tpl->prepare();
			
			switch ($_REQUEST['tipo']) {
				case 1:
					$sql = '
						SELECT
							cantidad,
							codmp,
							nombre,
							contenido,
							descripcion
								AS
									unidad,
							precio,
							porciento_desc_normal
								AS
									desc1,
							porciento_desc_adicional2
								AS
									desc2,
							porciento_desc_adicional3
								AS
									desc3,
							porciento_impuesto
								AS
									iva,
							ieps,
							costo_unitario
								AS
									importe
							FROM
									entrada_mp
								LEFT JOIN
									catalogo_mat_primas
										USING
											(
												codmp
											)
								LEFT JOIN
									tipo_unidad_consumo
										ON
											(
												idunidad = unidadconsumo
											)
						WHERE
							id
								IN
									(
										' . implode(', ', $_REQUEST['id']) . '
									)
					';
					$result = $db->query($sql);
					
					$tpl->newBlock('materia_prima');
					$total_fac = 0;
					foreach ($result as $r) {
						$tpl->newBlock('mp');
						$tpl->assign('cantidad', number_format($r['cantidad'], 2, '.', ','));
						$tpl->assign('codmp', $r['codmp']);
						$tpl->assign('nombre', $r['nombre']);
						$tpl->assign('contenido', number_format($r['contenido'], 2, '.', ','));
						$tpl->assign('unidad', $r['unidad']);
						$tpl->assign('precio', number_format($r['precio'], 2, '.', ','));
						$tpl->assign('importe', number_format($r['importe'], 2, '.', ','));
						
						$desc1 = $r['importe'] * $r['desc1'] / 100;
						$desc2 = ($r['importe'] - $desc1) * $r['desc2'] / 100;
						$desc3 = ($r['importe'] - $desc1 - $desc2) * $r['desc3'] / 100;
						$descuentos = $desc1 + $desc2 + $desc3;
						$subtotal = $r['importe'] - $descuentos;
						$iva = $subtotal * $r['iva'] / 100;
						$total = $subtotal + $iva;
						$total_fac += $total;
						
						$tpl->assign('descuentos', $descuentos != 0 ? number_format($descuentos, 2, '.', ',') : '&nbsp;');
						$tpl->assign('iva', $iva != 0 ? number_format($iva, 2, '.', ',') : '&nbsp;');
						$tpl->assign('total', number_format($total, 2, '.', ','));
					}
					$tpl->assign('materia_prima.total', number_format($total_fac, 2, '.', ','));
				break;
				
				case 2:
					$sql = '
						SELECT
							litros,
							precio_unit
								AS
									precio,
							total
						FROM
							factura_gas
						WHERE
							id
								IN
									(
										' . implode(', ', $_REQUEST['id']) . '
									)
					';
					$result = $db->query($sql);
					
					$tpl->newBlock('gas');
					$total_fac = 0;
					foreach ($result as $r) {
						$tpl->newBlock('tanque');
						$tpl->assign('litros', number_format($r['litros'], 2, '.', ','));
						$tpl->assign('precio', number_format($r['precio'], 2, '.', ','));
						$tpl->assign('importe', number_format($r['total'], 2, '.', ','));
						
						$total_fac += $r['total'];
					}
					$tpl->assign('gas.total', number_format($total_fac, 2, '.', ','));
				break;
			}
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ban/AclaracionFacturasProveedores.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
