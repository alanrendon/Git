<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, 'autocommit=yes');

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Delaracion de variables -------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/pan/hoja_diaria.tpl" );
$tpl->prepare();

$importes = array();
for ($i = 0; $i < count($_GET['importe']); $i++)
	if (get_val($_GET['importe'][$i]) > 0 && ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_GET['fecha'][$i]))
		$importes[] = array('fecha' => $_GET['fecha'][$i], 'importe' => get_val($_GET['importe'][$i]));

$excluir = array();

do {
	$ok = TRUE;
	$result = array();
	$cont = 0;
	$num_cia = FALSE;
	foreach ($importes as $imp) {
		if (!$num_cia) {
			$sql = "SELECT num_cia, round(min(abs($imp[importe] - efectivo))::numeric, 2) AS diferencia FROM efectivos_tmp WHERE fecha = '$imp[fecha]'" . (count($excluir) > 0 ? ' AND num_cia NOT IN (' . implode(', ', $excluir) . ')' : '') . " GROUP BY num_cia ORDER BY diferencia LIMIT 10";
			$min = $db->query($sql);
			
			if (!$min)
				continue;
			
			$num_cia = $min[0]['num_cia'];
		}
		
		$sql = "SELECT * FROM efectivos_tmp WHERE num_cia = $num_cia AND fecha = '$imp[fecha]'";
		$efe = $db->query($sql);
		
		if (!$efe)
			continue;
		
		$dif = $efe[0]['efectivo'] - $imp['importe'];
		
		$efe[0]['cajaam'] = $efe[0]['cajaam'] - $dif;
		$efe[0]['efectivo'] = $efe[0]['efectivo'] - $dif;
		
		if ($efe[0]['cajaam'] < 0 && $ok) {
			$ok = FALSE;
			$excluir[] = $num_cia;
			break;
		}
		
		$result[$cont++] = $efe[0];
	}
} while (!$ok);

if (count($result) == 0) {
	$tpl->newBlock('cerrar');
	die($tpl->printToScreen());
}

foreach ($result as $reg) {
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$nombre_cia = $tmp ? $tmp[0]['nombre_corto'] : 'SIN NOMBRE';
	
	$tpl->newBlock('hoja');
	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre_cia', $nombre_cia);
	$tpl->assign('fecha', $reg['fecha']);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $reg['fecha'], $fecha);
	$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
	
	// Efectivos
	$tpl->assign('am', $reg['cajaam'] != 0 ? number_format($reg['cajaam'], 2, '.', ',') : '');
	$tpl->assign('clientes_am', $reg['clientesam'] != 0 ? number_format($reg['clientesam']) : '');
	$tpl->assign('error_am', $reg['erroramcaja'] != 0 ? number_format($reg['erroramcaja'], 2, '.', ',') : '');
	$tpl->assign('error_clientes_am', $reg['erroramclientes'] != 0 ? number_format($reg['erroramclientes']) : '');
	$tpl->assign('pm', $reg['cajapm'] != 0 ? number_format($reg['cajapm'], 2, '.', ',') : '');
	$tpl->assign('clientes_pm', $reg['clientespm'] != 0 ? number_format($reg['clientespm']) : '');
	$tpl->assign('error_pm', $reg['errorpmcaja'] != 0 ? number_format($reg['errorpmcaja'], 2, '.', ',') : '');
	$tpl->assign('error_clientes_pm', $reg['errorpmclientes'] != 0 ? number_format($reg['errorpmclientes']) : '');
	$tpl->assign('pastel_am', $reg['pastelam'] != 0 ? number_format($reg['pastelam'], 2, '.', ',') : '');
	$tpl->assign('clientes_am_pastel', $reg['clientespastelam'] != 0 ? number_format($reg['clientespastelam']) : '');
	$tpl->assign('pastel_pm', $reg['pastelpm'] != 0 ? number_format($reg['pastelpm'], 2, '.', ',') : '');
	$tpl->assign('clientes_pm_pastel', $reg['clientespastelpm'] != 0 ? number_format($reg['clientespastelpm']) : '');
	
	$total_caja = $reg['cajaam'] - $reg['erroramcaja'] + $reg['cajapm'] - $reg['errorpmcaja'] + $reg['pastelam'] + $reg['pastelpm'];
	$total_clientes = $reg['clientesam'] - $reg['erroramclientes'] + $reg['clientespm'] - $reg['errorpmclientes'] + $reg['clientespastelam'] + $reg['clientespastelpm'];
	
	$tpl->assign('total_caja', $total_caja != 0 ? number_format($total_caja, 2, '.', ',') : '');
	$tpl->assign('total_clientes', $total_clientes != 0 ? number_format($total_clientes) : '');
	
	// Cortes
	$corte_pan = $db->query("SELECT ticket FROM corte_tmp WHERE num_cia = $reg[num_cia] AND fecha = '$reg[fecha]' AND tipo = 1");
	$corte_pastel = $db->query("SELECT ticket FROM corte_tmp WHERE num_cia = $reg[num_cia] AND fecha = '$reg[fecha]' AND tipo = 2");
	if ($corte_pan || $corte_pastel) {
		if ($corte_pan)
			foreach ($corte_pan as $i => $corte)
				$tpl->assign('corte_pan_' . ($i + 1), $corte['ticket'] != 0 ? $corte['ticket'] : '');
		if ($corte_pastel)
			foreach ($corte_pastel as $i => $corte)
				$tpl->assign('corte_pastel_' . ($i + 1), $corte['ticket'] != 0 ? $corte['ticket'] : '');
	}
	
	// Producción -- En lugar de raya_pagada se utiliza raya_ganada porque los valores de los campos estan invertidos
	$sql = "SELECT codturno, /*raya_pagada*/raya_ganada AS raya_pagada, total_produccion FROM total_produccion_tmp WHERE num_cia = $reg[num_cia]";
	$sql .= " AND fecha_total = '$reg[fecha]'";
	$prod = $db->query($sql);
	$pro = 0;
	$raya = 0;
	$pro_array = array();
	if ($prod) {
		foreach ($prod as $p) {
			$tpl->assign('pro' . $p['codturno'], $p['total_produccion'] != 0 ? number_format($p['total_produccion'], 2, '.', ',') : '');
			$tpl->assign('raya' . $p['codturno'], $p['raya_pagada'] != 0 ? number_format($p['raya_pagada'], 2, '.', ',') : '');
			$pro += $p['total_produccion'];
			$raya += $p['raya_pagada'];
		}
		$tpl->assign('pro', $pro != 0 ? number_format($pro, 2, '.', ',') : '');
		$tpl->assign('raya', $raya != 0 ? number_format($raya, 2, '.', ',') : '');
		
		foreach ($prod as $p)
			$pro_array[$p['codturno']] = $p['total_produccion'];
	}
	
	// Rendimientos
	$sql = "SELECT cod_turno, sum(cantidad) AS bultos FROM mov_inv_tmp WHERE num_cia = $reg[num_cia] AND fecha = '$reg[fecha]' AND codmp = 1 AND tipomov = 'TRUE' AND cod_turno < 10";
	$sql .= " GROUP BY cod_turno ORDER BY cod_turno";
	$con = $db->query($sql);
	if ($con) {
		foreach ($con as $c) {
			$tpl->assign('bultos' . $c['cod_turno'], number_format($c['bultos'], 2, '.', ','));
			$tpl->assign('ren' . $c['cod_turno'], number_format($pro_array[$c['cod_turno']] / $c['bultos'], 2, '.', ','));
		}
	}
	
	// Agua
	$med = $db->query("SELECT * FROM mediciones_tmp WHERE num_cia = $reg[num_cia] AND fecha = '$reg[fecha]'");
	if ($med) {
		$tpl->assign('med1', $med[0]['toma1'] != 0 ? number_format($med[0]['toma1'], 2, '.', ',') : '');
		$tpl->assign('hora1', $med[0]['toma1'] != 0 ? substr($med[0]['horatoma1'], 0, 5) : '');
		$tpl->assign('med2', $med[0]['toma2'] != 0 ? number_format($med[0]['toma2'], 2, '.', ',') : '');
		$tpl->assign('hora2', $med[0]['toma2'] != 0 ? substr($med[0]['horatoma2'], 0, 5) : '');
		$tpl->assign('med3', $med[0]['toma3'] != 0 ? number_format($med[0]['toma3'], 2, '.', ',') : '');
		$tpl->assign('hora3', $med[0]['toma3'] != 0 ? substr($med[0]['horatoma3'], 0, 5) : '');
	}
	
	// Camionetas
	$cam = $db->query("SELECT * FROM camionetas_tmp WHERE num_cia = $reg[num_cia] AND fecha = '$reg[fecha]'");
	if ($cam) {
		$tpl->assign('km1', $cam[0]['medunidad1'] != 0 ? number_format($cam[0]['medunidad1'], 2, '.', ',') : '');
		$tpl->assign('din1', $cam[0]['dinunidad1'] != 0 ? number_format($cam[0]['dinunidad1'], 2, '.', ',') : '');
		$tpl->assign('km2', $cam[0]['medunidad2'] != 0 ? number_format($cam[0]['medunidad2'], 2, '.', ',') : '');
		$tpl->assign('din2', $cam[0]['dinunidad2'] != 0 ? number_format($cam[0]['dinunidad2'], 2, '.', ',') : '');
		$tpl->assign('km3', $cam[0]['medunidad3'] != 0 ? number_format($cam[0]['medunidad3'], 2, '.', ',') : '');
		$tpl->assign('din3', $cam[0]['dinunidad3'] != 0 ? number_format($cam[0]['dinunidad3'], 2, '.', ',') : '');
		$tpl->assign('km4', $cam[0]['medunidad4'] != 0 ? number_format($cam[0]['medunidad4'], 2, '.', ',') : '');
		$tpl->assign('din4', $cam[0]['dinunidad4'] != 0 ? number_format($cam[0]['dinunidad4'], 2, '.', ',') : '');
		$tpl->assign('km5', $cam[0]['medunidad5'] != 0 ? number_format($cam[0]['medunidad5'], 2, '.', ',') : '');
		$tpl->assign('din5', $cam[0]['dinunidad5'] != 0 ? number_format($cam[0]['dinunidad5'], 2, '.', ',') : '');
	}
	
	// Avio recibido
	$facs = $db->query("SELECT * FROM facturas_tmp WHERE num_cia = $reg[num_cia] AND fecha = '$reg[fecha]' LIMIT 12");
	if ($facs) {
		foreach ($facs as $fac) {
			$tpl->newBlock('avio_rec');
			$tpl->assign('id', $fac['id']);
			$tpl->assign('checked', $fac['valid'] == 't' ? ' checked' : '');
			$tpl->assign('prov', $fac['proveedor']);
			$tpl->assign('fac', $fac['factura']);
			$tpl->assign('obs', trim($fac['observaciones']) != '' ? strtoupper(trim($fac['observaciones'])) : '&nbsp;');
		}
	}
	
	// Desglose de gastos
	$sql = "SELECT concepto, importe FROM gastos_tmp WHERE num_cia = $reg[num_cia] AND fecha = '$reg[fecha]'";
	$gastos = $db->query($sql);
	$pan_comprado = 0;
	$total_gastos = 0;
	if ($gastos)
		foreach ($gastos as $gasto) {
			$tpl->newBlock('gasto_hoja');
			$tpl->assign('concepto', trim($gasto['concepto']) != '' ? trim($gasto['concepto']) : '&nbsp;');
			$tpl->assign('importe', $gasto['importe'] != 0 ? number_format($gasto['importe'], 2, '.', ',') : '&nbsp;');
			$total_gastos += $gasto['importe'];
			if (trim($gasto['concepto']) == 'PAN COMPRADO') $pan_comprado = $gasto['importe'];
		}
	$pres = $db->query("SELECT 'PRESTAMO ' || nombre AS concepto, importe FROM prestamos_tmp WHERE num_cia = $reg[num_cia] AND fecha = '$reg[fecha]' AND tipo_mov = 'FALSE'");
	if ($pres)
		foreach ($pres as $pre) {
			$tpl->newBlock('gasto_hoja');
			$tpl->assign('concepto', trim($pre['concepto']) != '' ? trim($pre['concepto']) : '&nbsp;');
			$tpl->assign('importe', $pre['importe'] != 0 ? number_format($pre['importe'], 2, '.', ',') : '&nbsp;');
			$total_gastos += $pre['importe'];
		}
	$tpl->gotoBlock('hoja');
	$tpl->assign('total_gastos', $total_gastos != 0 ? number_format($total_gastos, 2, '.', ',') : '&nbsp;');
	
	// Prueba de Efectivo
	$tpl->assign('cambio_ayer', $reg['cambioayer'] != 0 ? number_format($reg['cambioayer'], 2, '.', ',') : '');
	$tpl->assign('barredura', $reg['barredura'] != 0 ? number_format($reg['barredura'], 2, '.', ',') : '');
	$tpl->assign('pasteles', $reg['pasteles'] != 0 ? number_format($reg['pasteles'], 2, '.', ',') : '');
	$tpl->assign('bases', $reg['bases'] != 0 ? number_format($reg['bases'], 2, '.', ',') : '');
	$tpl->assign('esquilmos', $reg['esquilmos'] != 0 ? number_format($reg['esquilmos'], 2, '.', ',') : '');
	$tpl->assign('botes', $reg['botes'] != 0 ? number_format($reg['botes'], 2, '.', ',') : '');
	$tpl->assign('pastillaje', $reg['pastillaje'] != 0 ? number_format($reg['pastillaje'], 2, '.', ',') : '');
	$tpl->assign('costales', $reg['costales'] != 0 ? number_format($reg['costales'], 2, '.', ',') : '');
	$tpl->assign('efectivo', $reg['efectivo'] != 0 ? number_format($reg['efectivo'], 2, '.', ',') : '');
	$suma1 = /*$reg['cambioayer'] + */$reg['barredura'] + $reg['pasteles'] + $reg['bases'] + $reg['esquilmos'] + $reg['botes'] + $reg['pastillaje'] + $reg['costales'];
	
	// Pastillaje
	$past = $db->query("SELECT * FROM pastillaje_tmp WHERE num_cia = $reg[num_cia] AND fecha = '$reg[fecha]'");
	if ($past) {
		$tpl->assign('existencia_inicial', $past[0]['existenciainicial'] != 0 ? number_format($past[0]['existenciainicial'], 2, '.', ',') : '');
		$tpl->assign('venta_pastillaje', $past[0]['venta'] != 0 ? number_format($past[0]['venta'], 2, '.', ',') : '');
		$tpl->assign('compra_pastillaje', $past[0]['compra'] != 0 ? number_format($past[0]['compra'], 2, '.', ',') : '');
		$tpl->assign('existencia_final', $past[0]['existenciafinal'] != 0 ? number_format($past[0]['existenciafinal'], 2, '.', ',') : '');
	}
	
	// Prueba de Pan
	$vpuerta = $reg['efectivo'] + $reg['pasteles'];
	$tmp = $db->query("SELECT sum(pan_p_venta) AS pan_venta, sum(abono) AS abono FROM mov_exp_tmp WHERE num_cia = $reg[num_cia] AND fecha = '$reg[fecha]'");
	$vreparto = $tmp[0]['pan_venta'] != 0 ? $tmp[0]['pan_venta'] : 0;
	$abono_exp = $tmp[0]['abono'] != 0 ? $tmp[0]['abono'] : 0;
	$prueba = $db->query("SELECT descuentos, pan_contado, sobranteayer FROM prueba_pan_tmp WHERE num_cia = $reg[num_cia] AND fecha = '$reg[fecha]'");
	$tpl->assign('sobrante_ayer', $prueba[0]['sobranteayer'] != 0 ? number_format($prueba[0]['sobranteayer'], 2, '.', ',') : '');
	$tpl->assign('pan_comprado', $pan_comprado != 0 ? number_format($pan_comprado, 2, '.', ',') : '');
	$total_dia = $prueba[0]['sobranteayer'] + $pro + $pan_comprado;
	$tpl->assign('total_dia', $total_dia != 0 ? number_format($total_dia, 2, '.', ',') : '');
	$tpl->assign('venta_puerta', /*$vpuerta*/$total_caja + $reg['pasteles'] != 0 ? number_format(/*$vpuerta*/$total_caja + $reg['pasteles'], 2, '.', ',') : '');
	$tpl->assign('reparto', $vreparto != 0 ? number_format($vreparto, 2, '.', ',') : '');
	$tpl->assign('desc', $prueba[0]['descuentos'] != 0 ? number_format($prueba[0]['descuentos'], 2, '.', ',') : '');
	$sobrante = $total_dia - /*$vpuerta*/$total_caja - $reg['pasteles'] - $vreparto - $prueba[0]['descuentos'];
	$tpl->assign('sobrante_manana', $sobrante != 0 ? number_format($sobrante, 2, '.', ',') : '');
	$tpl->assign('pan_contado', $prueba[0]['pan_contado'] != 0 ? number_format($prueba[0]['pan_contado'], 2, '.', ',') : '');
	$faltante = $sobrante - $prueba[0]['pan_contado'];
	$tpl->assign('faltante', $faltante != 0 ? number_format($faltante, 2, '.', ',') : '');
	
	// Prestamos a plazo
	$pres = $db->query("SELECT * FROM prestamos_tmp WHERE num_cia = $reg[num_cia] AND fecha = '$reg[fecha]'");
	$saldo_ant = 0;
	$cargos = 0;
	$abonos = 0;
	$saldo_act = 0;
	if ($pres) {
		foreach ($pres as $pre) {
			$tpl->newBlock('prestamo');
			$tpl->assign('nombre', $pre['nombre']);
			$tpl->assign('saldo_ant', $pre['saldo'] != 0 ? number_format($pre['saldo'], 2, '.', ',') : '');
			if ($pre['tipo_mov'] != '') $tpl->assign($pre['tipo_mov'] == 'f' ? 'cargo' : 'abono', $pre['importe'] != 0 ? number_format($pre['importe'], 2, '.', ',') : '');
			$tmp = $pre['saldo'] + ($pre['tipo_mov'] == 'f' ? $pre['importe'] : -$pre['importe']);
			$tpl->assign('saldo_act', $tmp != 0 ? number_format($tmp, 2, '.', ',') : '');
			$cargos += $pre['tipo_mov'] == 'f' ? $pre['importe'] : 0;
			$abonos += $pre['tipo_mov'] == 't' ? $pre['importe'] : 0;
			$saldo_ant += $pre['saldo'];
			$saldo_act += $tmp;
		}
		$tpl->assign('hoja.saldo_ant', number_format($saldo_ant, 2, '.', ','));
		$tpl->assign('hoja.cargo', number_format($cargos, 2, '.', ','));
		$tpl->assign('hoja.abono_obreros', number_format($abonos, 2, '.', ','));
		$tpl->assign('hoja.saldo_act', number_format($saldo_act, 2, '.', ','));
	}
	
	$suma1 += $abonos + $abono_exp + $total_caja;
	$tpl->assign('hoja.abonos', number_format($abono_exp, 2, '.', ','));
	$tpl->assign('hoja.suma_prueba1', number_format($suma1, 2, '.', ','));
	
	$suma2 = $reg['efectivo'] - $cargos + $total_gastos + $raya;
	$tpl->assign('hoja.suma_prueba2', number_format($suma2, 2, '.', ','));
	$tpl->assign('hoja.efectivo', number_format($reg['efectivo'] - $cargos, 2, '.', ','));
	
	// [19-Sep-2007] Hoja de Expendios
	$sql = "SELECT tmp.num_expendio AS num_tmp, num_referencia AS num_cat, cat.num_expendio AS num_exp, nombre_expendio AS nombre_tmp, nombre AS nombre_cat, porc_ganancia AS por_tmp,";
	$sql .= " porciento_ganancia AS por_cat, importe_fijo AS fijo, rezago_anterior, pan_p_venta, pan_p_expendio, abono, devolucion, rezago FROM mov_exp_tmp AS tmp LEFT JOIN";
	$sql .= " catalogo_expendios AS cat ON (cat.num_cia = tmp.num_cia AND num_referencia = tmp.num_expendio) WHERE tmp.num_cia = $reg[num_cia] AND fecha = '$reg[fecha]' ORDER BY num_tmp";
	$exps = $db->query($sql);
	
	if ($exps) {
		$tpl->newBlock('exp');
		$tpl->assign('num_cia', $_GET['num_cia']);
		$tpl->assign('nombre_cia', $nombre_cia);
		$tpl->assign('fecha', $reg['fecha']);
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $reg['fecha'], $fecha);
		$tpl->assign('_fecha', "$fecha[1] DE " . mes_escrito($fecha[2], TRUE) . " DE $fecha[3]");
		
		$pan_p_venta = 0;
		$pan_p_exp = 0;
		$abono = 0;
		$devuelto = 0;
		$rezago = 0;
		$rezago_ant_total = 0;
		foreach ($exps as $exp) {
			$tpl->newBlock('mov_exp');
			$tpl->assign('num', $exp['num_cat']);
			$tpl->assign('nombre', $exp['nombre_tmp']);
			
			// Rezago anterior
			$tpl->assign('rezago_ant', $exp['rezago_anterior'] != 0 ? number_format($exp['rezago_anterior'], 2, '.', ',') : '&nbsp;');
			
			// Pan para venta
			$tpl->assign('pan_p_venta', $exp['pan_p_venta'] != 0 ? number_format($exp['pan_p_venta'], 2, '.', ',') : '&nbsp;');
			
			// Devolución
			$tpl->assign('dev', $exp['devolucion'] != 0 ? number_format($exp['devolucion'], 2, '.', ',') : '&nbsp;');
			
			// Pan para expendio
			$tpl->assign('por', $exp['por_tmp'] != 0 ? '% ' . number_format($exp['por_tmp']) : '&nbsp;');
			$tpl->assign('pan_p_exp', $exp['pan_p_expendio'] != 0 ? number_format($exp['pan_p_expendio'], 2, '.', ',') : '&nbsp;');
			
			$tpl->assign('abono', $exp['abono'] != 0 ? number_format($exp['abono'], 2, '.', ',') : '&nbsp;');
			$tpl->assign('rezago', $exp['rezago'] != 0 ? number_format($exp['rezago'], 2, '.', ',') : '&nbsp;');
			if ($exp['rezago'] < 0) $tpl->assign('color_rezago', ' bgcolor="#FFFF66"');
			
			$pan_p_venta += $exp['pan_p_venta'];
			$pan_p_exp += $exp['pan_p_expendio'];
			$abono += $exp['abono'];
			$devuelto += $exp['devolucion'];
			$rezago_ant_total += $exp['rezago_anterior'];
			$rezago += $exp['rezago'];
		}
		$tpl->assign('exp.rezago_ant', number_format($rezago_ant_total, 2, '.', ','));
		$tpl->assign('exp.pan_p_venta', number_format($pan_p_venta, 2, '.', ','));
		$tpl->assign('exp.dev', number_format($devuelto, 2, '.', ','));
		$tpl->assign('exp.pan_p_exp', number_format($pan_p_exp, 2, '.', ','));
		$tpl->assign('exp.abono', number_format($abono, 2, '.', ','));
		$tpl->assign('exp.rezago', number_format($rezago, 2, '.', ','));
	}
	else
		$tpl->assign('hoja.salto2', '<br style="page-break-after:always;">');
	
	if ((count($result) > 1 && count($exps) < 61) || count($exps) > 62) $tpl->assign('hoja.salto', '<br style="page-break-after:always;">');
	if (count($exps) > 62) $tpl->assign('hoja.salto2', '<br style="page-break-after:always;">');
}
$tpl->printToScreen();
?>