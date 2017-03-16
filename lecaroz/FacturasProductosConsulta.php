<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value) {
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
			$tpl = new TemplatePower('plantillas/fac/FacturasProductosConsultaInicio.tpl');
			$tpl->prepare();

			$fecha1 = date('j') <= 5 ? date('01/m/Y', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('01/m/Y');
			$fecha2 = date('j') <= 5 ? date('d/m/Y', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('d/m/Y');
			
			$tpl->assign('fecha1', $fecha1);
			$tpl->assign('fecha2', $fecha2);


			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'consultar':
			$condiciones = array();

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
					$condiciones[] = 'emp.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
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
					$condiciones[] = 'emp.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			if (isset($_REQUEST['mps']) && trim($_REQUEST['mps']) != '') {
				$mps = array();
				
				$pieces = explode(',', $_REQUEST['mps']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$mps[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$mps[] = $piece;
					}
				}
				
				if (count($mps) > 0) {
					$condiciones[] = 'emp.codmp IN (' . implode(', ', $mps) . ')';
				}
			}

			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
				|| (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
					&& (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
					$condiciones[] = 'emp.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				} else if (isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') {
					$condiciones[] = 'emp.fecha = \'' . $_REQUEST['fecha1'] . '\'';
				} else if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '') {
					$condiciones[] = 'emp.fecha <= \'' . $_REQUEST['fecha2'] . '\'';
				}
			}

			if (isset($_REQUEST['facturas']) && trim($_REQUEST['facturas']) != '') {
				$facturas = array();
				$facturas_between = array();
				
				$pieces = explode(',', $_REQUEST['facturas']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$facturas_between[] =  "emp.num_fact BETWEEN '" . $exp[0] . "' AND '" . $exp[1] . "'";
					}
					else {
						$facturas[] = $piece;
					}
				}
				
				$partes = array();
				
				if (count($facturas) > 0) {
					$partes[] = "emp.num_fact IN ('" . implode("', '", $facturas) . "')";
				}
				
				if (count($facturas_between) > 0) {
					$partes[] = implode(' OR ', $facturas_between);
				}
				
				if (count($partes) > 0) {
					$condiciones[] = '(' . implode(' OR ', $partes) . ')';
				}
			}
			
			if (isset($_REQUEST['usuario'])) {
				$condiciones[] = 'f.iduser = ' . $_SESSION['iduser'];
			}
			
			$sql = "
				SELECT
					emp.id,
					emp.codmp,
					cmp.nombre
						AS producto,
					emp.num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					emp.num_fact,
					emp.fecha,
					emp.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					emp.cantidad,
					emp.contenido,
					tuc.descripcion
						AS unidad,
					emp.precio,
					emp.cantidad * emp.precio
						AS importe,
					emp.costales * emp.precio_costal
						AS costales,
					emp.desc1,
					emp.desc2,
					emp.desc3,
					emp.iva,
					emp.ieps,
					emp.importe + emp.iva + emp.ieps
						AS total,
					fp.folio_cheque
						AS folio,
					fecha_cheque
						AS fecha_pago,
					fecha_con
						AS fecha_cobro,
					ch.cuenta
						AS banco,
					ch.cod_mov,
					ch.fecha_cancelacion
				FROM
					entrada_mp emp
					LEFT JOIN catalogo_mat_primas cmp
						USING (codmp)
					LEFT JOIN tipo_unidad_consumo tuc
							ON (idunidad = unidadconsumo)
					LEFT JOIN facturas f
						USING (num_proveedor, num_fact)
					LEFT JOIN facturas_pagadas fp
						ON (fp.num_proveedor = f.num_proveedor AND fp.num_fact = f.num_fact AND fp.fecha = f.fecha)
					LEFT JOIN cheques ch
						ON (ch.num_cia = fp.num_cia AND ch.folio = fp.folio_cheque AND ch.fecha = fp.fecha_cheque)
					LEFT JOIN estado_cuenta ec
						ON (ec.num_cia = fp.num_cia AND ec.folio = fp.folio_cheque AND ec.fecha = fp.fecha_cheque)
					LEFT JOIN catalogo_proveedores cp
						ON (cp.num_proveedor = emp.num_proveedor)
					LEFT JOIN catalogo_companias cc
						ON (cc.num_cia = emp.num_cia)
				WHERE
					" . implode(' AND ', $condiciones) . "
				ORDER BY
					emp.codmp,
					num_pro,
					emp.num_cia,
					emp.fecha,
					emp.num_fact
			";
			
			$query = $db->query($sql);
			
			if ($query) {
				$tpl = new TemplatePower('plantillas/fac/FacturasProductosConsultaResultado.tpl');
				$tpl->prepare();
				
				$codmp = NULL;
				
				foreach ($query as $row) {
					if ($codmp != $row['codmp']) {
						$codmp = $row['codmp'];
						
						$tpl->newBlock('mp');
						
						$tpl->assign('codmp', $row['codmp']);
						$tpl->assign('producto', utf8_encode($row['producto']));
						
						$totales = array(
							'cantidad'	=> 0,
							'costales'	=> 0,
							'importe'	=> 0,
							'desc1'		=> 0,
							'desc2'		=> 0,
							'desc3'		=> 0,
							'iva'		=> 0,
							'ieps'		=> 0,
							'total'		=> 0
						);
					}
					
					$tpl->newBlock('row');
					
					$tpl->assign('num_pro', $row['num_pro']);
					$tpl->assign('nombre_pro', utf8_encode($row['nombre_pro']));
					$tpl->assign('num_fact', utf8_encode($row['num_fact']));
					$tpl->assign('fecha', $row['fecha']);
					$tpl->assign('num_cia', $row['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));

					if ($codmp == 1 && $row['cantidad'] == 1)
					{
						$row['cantidad'] = $row['contenido'] / 44;
						$row['contenido'] = 44;
						$row['precio'] = $row['importe'] / $row['cantidad'];
					}

					$tpl->assign('cantidad', $row['cantidad'] > 0 ? number_format($row['cantidad'], 2) : '&nbsp;');
					$tpl->assign('contenido', $row['contenido'] > 0 ? number_format($row['contenido'], 2) : '&nbsp;');
					$tpl->assign('precio', $row['precio'] > 0 ? number_format($row['precio'], 2) : '&nbsp;');
					$tpl->assign('unidad', trim($row['unidad']) != '' ? utf8_encode($row['unidad']) : '&nbsp;');
					$tpl->assign('costales', $row['costales'] > 0 ? number_format($row['costales'], 2) : '&nbsp;');
					$tpl->assign('importe', $row['importe'] > 0 ? number_format($row['importe'], 2) : '&nbsp;');
					$tpl->assign('desc1', $row['desc1'] > 0 ? number_format($row['desc1'], 2) : '&nbsp;');
					$tpl->assign('desc2', $row['desc2'] > 0 ? number_format($row['desc2'], 2) : '&nbsp;');
					$tpl->assign('desc3', $row['desc3'] > 0 ? number_format($row['desc3'], 2) : '&nbsp;');
					$tpl->assign('iva', $row['iva'] > 0 ? number_format($row['iva'], 2) : '&nbsp;');
					$tpl->assign('ieps', $row['ieps'] > 0 ? number_format($row['ieps'], 2) : '&nbsp;');
					$tpl->assign('total', $row['total'] > 0 ? number_format($row['total'], 2) : '&nbsp;');
					$tpl->assign('fecha_pago', $row['fecha_pago'] != '' ? $row['fecha_pago'] : '&nbsp;');
					$tpl->assign('banco', $row['banco'] > 0 ? ('<img src="/lecaroz/imagenes/' . ($row['banco'] == 1 ? 'Banorte' : 'Santander') . '16x16.png" width="16" height="16" />') : '&nbsp;');
					$tpl->assign('folio', $row['folio'] > 0 ? '<span style="color:' . ($row['fecha_cancelacion'] == '' ? ($row['cod_mov'] == 41 ? '#063' : '#00C') : '#C00') . '">' . $row['folio'] . '</span>' : '&nbsp;');
					$tpl->assign('fecha_cobro', $row['fecha_cobro'] != '' ? $row['fecha_cobro'] : '&nbsp;');

					$totales['cantidad'] += $row['cantidad'];
					$totales['costales'] += $row['costales'];
					$totales['importe'] += $row['importe'];
					$totales['desc1'] += $row['desc1'];
					$totales['desc2'] += $row['desc2'];
					$totales['desc3'] += $row['desc3'];
					$totales['iva'] += $row['iva'];
					$totales['ieps'] += $row['ieps'];
					$totales['total'] += $row['total'];

					foreach ($totales as $columna => $importe)
					{
						$tpl->assign('mp.' . $columna, $importe > 0 ? number_format($importe, 2) : '&nbsp;');
					}
				}
				
				echo $tpl->getOutputContent();
			}
			
			break;

		case 'detalle':
			$tpl = new TemplatePower('plantillas/fac/FacturasConsultaDetalle.tpl');
			$tpl->prepare();

			$sql = "
				SELECT
					f.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					f.num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					f.num_fact,
					f.fecha
				FROM
					facturas f
					LEFT JOIN catalogo_proveedores cp
						ON (cp.num_proveedor = f.num_proveedor)
					LEFT JOIN catalogo_companias cc
						ON (cc.num_cia = f.num_cia)
				WHERE
					f.id = {$_REQUEST['id']}
			";

			$result = $db->query($sql);

			$info_fac = $result[0];

			$tpl->assign('num_cia', $info_fac['num_cia']);
			$tpl->assign('nombre_cia', utf8_encode($info_fac['nombre_cia']));
			$tpl->assign('num_pro', $info_fac['num_pro']);
			$tpl->assign('nombre_pro', utf8_encode($info_fac['nombre_pro']));
			$tpl->assign('num_fact', utf8_encode($info_fac['num_fact']));
			$tpl->assign('fecha', $info_fac['fecha']);

			if ($_REQUEST['tipo'] == 1)
			{
				$sql = "
					SELECT
						emp.cantidad,
						emp.codmp,
						cmp.nombre
							AS nombre_mp,
						emp.contenido,
						tuc.descripcion
							AS unidad,
						emp.precio,
						emp.cantidad * emp.precio
							AS importe,
						emp.desc1,
						emp.desc2,
						emp.desc3,
						emp.iva,
						emp.ieps,
						emp.importe + emp.iva + emp.ieps
							AS total
					FROM
						entrada_mp emp
						LEFT JOIN catalogo_mat_primas cmp
							USING (codmp)
						LEFT JOIN tipo_unidad_consumo tuc
							ON (idunidad = unidadconsumo)
					WHERE
						(emp.num_proveedor, emp.num_fact) IN (
							SELECT
								num_proveedor,
								num_fact
							FROM
								facturas
							WHERE
								id = {$_REQUEST['id']}
						)
					ORDER BY
						emp.id
				";

				$result = $db->query($sql);

				$total = 0;

				$tpl->newBlock('mp');

				foreach ($result as $row)
				{
					$tpl->newBlock('row_mp');

					$tpl->assign('cantidad', number_format($row['cantidad'], 2));
					$tpl->assign('codmp', $row['codmp']);
					$tpl->assign('nombre_mp', utf8_encode($row['nombre_mp']));
					$tpl->assign('contenido', number_format($row['contenido'], 2));
					$tpl->assign('unidad', utf8_encode($row['unidad']));
					$tpl->assign('precio', number_format($row['precio'], 2));
					$tpl->assign('importe', $row['importe'] > 0 ? number_format($row['importe'], 2) : '&nbsp;');
					$tpl->assign('desc1', $row['desc1'] > 0 ? number_format($row['desc1'], 2) : '&nbsp;');
					$tpl->assign('desc2', $row['desc2'] > 0 ? number_format($row['desc2'], 2) : '&nbsp;');
					$tpl->assign('desc3', $row['desc3'] > 0 ? number_format($row['desc3'], 2) : '&nbsp;');
					$tpl->assign('iva', $row['iva'] > 0 ? number_format($row['iva'], 2) : '&nbsp;');
					$tpl->assign('ieps', $row['ieps'] > 0 ? number_format($row['ieps'], 2) : '&nbsp;');
					$tpl->assign('total', $row['total'] > 0 ? number_format($row['total'], 2) : '&nbsp;');

					$total += $row['total'];

					$tpl->assign('mp.total', number_format($total, 2));
				}
			}
			else if ($_REQUEST['tipo'] == 2)
			{
				$sql = "
					SELECT
						litros,
						precio_unit
							AS precio,
						litros * precio_unit
							AS importe,
						total - litros * precio_unit
							AS iva,
						total
					FROM
						factura_gas
					WHERE
						(num_proveedor, num_fact) IN (
							SELECT
								num_proveedor,
								num_fact
							FROM
								facturas
							WHERE
								id = {$_REQUEST['id']}
						)
					ORDER BY
						id
				";

				$result = $db->query($sql);

				$total = 0;

				$tpl->newBlock('gas');

				foreach ($result as $row)
				{
					$tpl->newBlock('row_gas');

					$tpl->assign('litros', number_format($row['litros'], 2));
					$tpl->assign('precio', number_format($row['precio'], 2));
					$tpl->assign('importe', $row['importe'] > 0 ? number_format($row['importe'], 2) : '&nbsp;');
					$tpl->assign('iva', $row['iva'] > 0 ? number_format($row['iva'], 2) : '&nbsp;');
					$tpl->assign('total', $row['total'] > 0 ? number_format($row['total'], 2) : '&nbsp;');

					$total += $row['total'];

					$tpl->assign('gas.total', number_format($total, 2));
				}
			}

			echo $tpl->getOutputContent();

			break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturasProductosConsulta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
