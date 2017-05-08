<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

/**********************************************************************************/
/*** FUNCIONES SUPLEMENTARIAS                                                   ***/
/**********************************************************************************/
function microtime_float() {
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

function buscar_fal($array, $dia) {
	if (!$array)
		return 0;
	
	foreach ($array as $dato)
		if ($dato['dia'] == $dia || ($dia == 0 && $dato['dia'] == "0"))
			return $dato['dato'];
	
	return 0;
}

function mes_abreviado($mes) {
	switch ($mes) {
		case 1: $mes_abr = "Ene"; break;
		case 2: $mes_abr = "Feb"; break;
		case 3: $mes_abr = "Mar"; break;
		case 4: $mes_abr = "Abr"; break;
		case 5: $mes_abr = "May"; break;
		case 6: $mes_abr = "Jun"; break;
		case 7: $mes_abr = "Jul"; break;
		case 8: $mes_abr = "Ago"; break;
		case 9: $mes_abr = "Sep"; break;
		case 10: $mes_abr = "Oct"; break;
		case 11: $mes_abr = "Nov"; break;
		case 12: $mes_abr = "Dic"; break;
		default: $mes_abr = ""; break;
	}
	
	return $mes_abr;
}
/**********************************************************************************/
// Construir fecha inicial y final
$mes    = $_GET['mes'];
$anio   = $_GET['anio'];
$_mes_ant = date("n", mktime(0, 0, 0, $_GET['mes'], 0, $_GET['anio']));
$_anio_ant = date("Y", mktime(0, 0, 0, $_GET['mes'], 0, $_GET['anio']));
$fecha1 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'], 1, $_GET['anio']));
$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1 ,0, $_GET['anio']));
$dias   = date("d", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));

$ultima_fecha_mov = $db->query("SELECT fecha FROM mov_inv_real_temp GROUP BY fecha ORDER BY fecha LIMIT 1");
ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $ultima_fecha_mov[0]['fecha'], $tmp);

// Obtener compañias
$sql = "SELECT num_cia, nombre, nombre_corto FROM catalogo_companias WHERE ";
$sql .= isset($_GET['compania']) && $_GET['compania'] > 0 ? "num_cia = $_GET[compania]" : "num_cia BETWEEN 901 AND 998";
$sql .= " ORDER BY num_cia";
$cia = $db->query($sql);

if (!$cia)
	die("No hay resultados");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/bal/bal_esr_zap.tpl" );
$tpl->prepare();

for ($f = 0; $f < count($cia); $f++) {
	$num_cia = $cia[$f]['num_cia'];
	
	// ************* HOJA 1, SECCION 1 *************
	
	/**** VENTAS ZAPATERIA ****/
	$tmp = $db->query("SELECT sum(venta) FROM total_zapaterias WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'");
	$venta_zap = $tmp[0]['sum'] > 0 ? $tmp[0]['sum'] : 0;
	
	/**** ABONO EMPLEADOS ****/
	$tmp = $db->query("SELECT sum(importe) FROM prestamos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'TRUE'");
	$abono_empleados = $tmp[0]['sum'] > 0 ? $tmp[0]['sum'] : 0;
	
	/**** OTROS ****/
	$tmp = $db->query("SELECT sum(otros) FROM total_zapaterias WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'");
	$otros = $tmp[0]['sum'] > 0 ? $tmp[0]['sum'] : 0;
	
	/**** TOTAL OTROS ****/
	$total_otros = $otros + $abono_empleados;
	
	/**** ERRORES ****/
	$tmp = $db->query("SELECT sum(errores) FROM efectivos_zap WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'");
	$errores = $tmp[0]['sum'] > 0 ? $tmp[0]['sum'] : 0;
	
	/**** VENTAS NETAS ****/
	$ventas_netas = $venta_zap + $total_otros - $errores[0]['sum'];
	
	// ************* HOJA 1, SECCION 2 *************
	
	/**** INVENTARIO ANTERIOR ****/
	$tmp = $db->query("SELECT importe FROM inventario_zap WHERE num_cia = $num_cia AND mes = $_mes_ant AND anio = $_anio_ant");
	$inv_ant = $tmp ? $tmp[0]['importe'] : 0;
	
	/**** COMPRAS ****/
	if ($tmp = $db->query("SELECT importe FROM compras_zap WHERE num_cia = $num_cia AND mes = $mes AND anio = $anio")) {
		$compras = $tmp[0]['importe'];
		$desc = 0;
		$desc_compras = 0;
		$desc_pagos = 0;
		$dev = 0;
	}
	else {
		$tmp = $db->query("SELECT importe, faltantes, dif_precio, CASE WHEN dev > 0 THEN dev WHEN (SELECT sum(importe) FROM devoluciones_zap WHERE num_proveedor = fz.num_proveedor AND num_fact = fz.num_fact) IS NOT NULL THEN (SELECT sum(importe) FROM devoluciones_zap WHERE num_proveedor = fz.num_proveedor AND num_fact = fz.num_fact) ELSE 0 END AS dev, pdesc1, pdesc2, pdesc3, pdesc4, desc1, desc2, desc3, desc4, (SELECT tipo FROM cat_conceptos_descuentos WHERE cod = fz.cod_desc1) AS tipo_desc1, (SELECT tipo FROM cat_conceptos_descuentos WHERE cod = fz.cod_desc2) AS tipo_desc2, (SELECT tipo FROM cat_conceptos_descuentos WHERE cod = fz.cod_desc3) AS tipo_desc3, (SELECT tipo FROM cat_conceptos_descuentos WHERE cod = fz.cod_desc4) AS tipo_desc4, iva, total FROM facturas_zap AS fz WHERE num_cia = $num_cia AND fecha_inv BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 33");
		
		$compras = 0;
		$desc = 0;
		$desc_compras = 0;
		$desc_pagos = 0;
		if ($tmp)
			foreach ($tmp as $i => $reg) {
				$subimporte = $reg['importe'] - $reg['faltantes'] - $reg['dif_precio'] - $reg['dev'];
				$desc1 = $reg['pdesc1'] > 0 ? round($subimporte * $reg['pdesc1'] / 100, 2) : ($reg['desc1'] > 0 ? $reg['desc1'] : 0);
				$desc_compras += $reg['tipo_desc1'] == 1 && $desc1 > 0 ? $desc1 : 0;
				$desc_pagos += $reg['tipo_desc1'] == 2 && $desc1 > 0 ? $desc1 : 0;
				$desc2 = $reg['pdesc2'] > 0 ? round(($subimporte - $desc1) * $reg['pdesc2'] / 100, 2) : ($reg['desc2'] > 0 ? $reg['desc2'] : 0);
				$desc_compras += $reg['tipo_desc2'] == 1 && $desc2 > 0 ? $desc2 : 0;
				$desc_pagos += $reg['tipo_desc2'] == 2 && $desc2 > 0 ? $desc2 : 0;
				$desc3 = $reg['pdesc3'] > 0 ? round(($subimporte - $desc1 - $desc2) * $reg['pdesc3'] / 100, 2) : ($reg['desc3'] > 0 ? $reg['desc3'] : 0);
				$desc_compras += $reg['tipo_desc3'] == 1 && $desc3 > 0 ? $desc3 : 0;
				$desc_pagos += $reg['tipo_desc3'] == 2 && $desc3 > 0 ? $desc3 : 0;
				$desc4 = $reg['pdesc4'] > 0 ? round(($subimporte - $desc1 - $desc2 - $desc3) * $reg['pdesc4'] / 100, 2) : ($reg['desc4'] > 0 ? $reg['desc4'] : 0);
				$desc_compras += $reg['tipo_desc4'] == 1 && $desc4 > 0 ? $desc4 : 0;
				$desc_pagos += $reg['tipo_desc4'] == 2 && $desc4 > 0 ? $desc4 : 0;
				$subtotal = $subimporte - $desc1 - $desc2 - $desc3 - $desc4;
				$iva = $reg['iva'] > 0 ? $subtotal * 0.15 : 0;
				
				// [24-Abr-2008] Quitar el IVA de las compras
				$compras += $reg['importe'] + $reg['faltantes'] + $reg['dif_precio'] + $reg['dev']/* + $iva*/;
				$desc += $desc1 + $desc2 + $desc3 + $desc4;
			}
	}
	
	/**** [09-Oct-2007] Devoluciones para la misma tienda pero que no han sido aplicadas a ninguna factura ****/
	$sql = "SELECT sum(d.importe) AS importe FROM devoluciones_zap AS d LEFT JOIN cheques AS c ON (c.num_cia = d.num_cia_cheque AND c.folio = folio_cheque AND c.cuenta = d.cuenta) WHERE d.num_cia = $num_cia AND d.fecha BETWEEN '$fecha1' AND '$fecha2' AND (folio_cheque IS NULL OR (c.fecha > '$fecha2' AND d.num_cia_cheque = $num_cia))";
	$dev = $db->query($sql);
	
	/**** [09-Oct-2007] Devoluciones que son de la misma tienda pero de meses anteriores ****/
	$sql = "SELECT sum(d.importe) AS importe FROM devoluciones_zap AS d LEFT JOIN cheques AS c ON (c.num_cia = d.num_cia_cheque AND c.folio = folio_cheque AND c.cuenta = d.cuenta) WHERE d.num_cia = $num_cia AND d.fecha < '$fecha1' AND (folio_cheque IS NULL OR (c.fecha > '$fecha2' AND d.num_cia_cheque = $num_cia))";
	$dev_ant = $db->query($sql);
	
	/**** [09-Oct-2007] Devoluciones aplicadas en el mes que son de otras tiendas ****/
	$sql = "SELECT sum(d.importe) AS importe FROM devoluciones_zap AS d LEFT JOIN cheques AS c ON (c.num_cia = d.num_cia_cheque AND c.folio = folio_cheque AND c.cuenta = d.cuenta) WHERE d.num_cia = $num_cia AND d.num_cia_cheque != $num_cia AND d.fecha BETWEEN '$fecha1' AND '$fecha2' AND (folio_cheque IS NULL OR (c.fecha > '$fecha2' AND d.num_cia_cheque != $num_cia))";
	$dev_otras = $db->query($sql);
	
	/**** [09-Oct-2007] Devoluciones aplicadas en el mes que son de la tienda pero aplicadas en otras ****/
	$sql = "SELECT sum(d.importe) AS importe FROM devoluciones_zap AS d LEFT JOIN cheques AS c ON (c.num_cia = d.num_cia_cheque AND c.folio = folio_cheque AND c.cuenta = d.cuenta) WHERE d.num_cia_cheque = $num_cia AND d.num_cia != $num_cia AND d.fecha BETWEEN '$fecha1' AND '$fecha2' AND (folio_cheque IS NULL OR c.fecha > '$fecha2' AND d.num_cia != $num_cia)";
	$dev_pag_otras = $db->query($sql);
	
	/**** [28-Mar-2007] Traspasos de pares entre zapaterias ****/
	$tmp = $db->query("SELECT importe FROM traspaso_pares WHERE num_cia = $num_cia AND mes = $mes AND anio = $anio");
	$traspaso = $tmp ? $tmp[0]['importe'] : 0;
	
	/**** INVENTARIO ACTUAL ****/
	$tmp = $db->query("SELECT importe FROM inventario_zap WHERE num_cia = $num_cia AND mes = $mes AND anio = $anio");
	$inv_act = $tmp ? $tmp[0]['importe'] : 0;
	
	/**** MATERIA PRIMA UTILIZADA ****/
	$mat_prima_utilizada = $inv_ant + ($compras + $desc) - $desc_compras - $inv_act + $traspaso - $dev[0]['importe'];
	
	/**** COSTO DE VENTA ****/
	$costo_venta = $mat_prima_utilizada - $desc_pagos - $dev_ant[0]['importe'] - $dev_otras[0]['importe'] - $dev_pag_otras[0]['importe'];
	
	/**** UTILIDAD BRUTA ****/
	$utilidad_bruta = $ventas_netas - $costo_venta;
	
	// ************* HOJA 1, SECCION 3 *************
	
	/**** GASTOS OPERACION ****/
	$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 1 AND codgastos NOT IN (9, 76, 140, 141) GROUP BY num_cia";
	$gastos_oper = $db->query($sql);
	@$gastos_oper[0]['sum'] *= -1;
	
	/**** GASTOS GENERALES ****/
	$sql = "SELECT sum(importe) FROM catalogo_gastos JOIN movimiento_gastos USING (codgastos) WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados = 2 AND codgastos NOT IN (9, 76, 140, 141) GROUP BY num_cia";
	$gastos_gral = $db->query($sql);
	@$gastos_gral[0]['sum'] *= -1;
	
	/**** [27-Mar-2007] Comisiones bancarias ****/
	$comisiones = 0;
	/*$sql = "(SELECT ec.tipo_mov, sum(importe) AS importe FROM estado_cuenta AS ec LEFT JOIN catalogo_mov_bancos AS cm USING (cod_mov) WHERE fecha BETWEEN '$fecha1' AND '$fecha2'";
	$sql .= " AND entra_bal = 'TRUE' AND cuenta = 1 AND num_cia = $num_cia GROUP BY ec.tipo_mov) UNION (SELECT ec.tipo_mov, sum(importe) FROM estado_cuenta AS ec LEFT";
	$sql .= " JOIN catalogo_mov_santander AS cm USING (cod_mov) WHERE fecha BETWEEN '$fecha1' AND '$fecha2' AND entra_bal = 'TRUE' AND cuenta = 2 AND num_cia = $num_cia GROUP BY ec.tipo_mov)";*/
	$sql = "(SELECT tipo_mov, sum(importe) AS importe FROM estado_cuenta AS ec WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cuenta = 1 AND cod_mov IN (SELECT cod_mov FROM";
	$sql .= " catalogo_mov_bancos WHERE entra_bal = 'TRUE' GROUP BY cod_mov) GROUP BY tipo_mov) UNION (SELECT tipo_mov, sum(importe) AS importe FROM estado_cuenta AS ec WHERE num_cia = $num_cia";
	$sql .= " AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cuenta = 2 AND cod_mov IN (SELECT cod_mov FROM catalogo_mov_santander WHERE entra_bal = 'TRUE' GROUP BY cod_mov) GROUP BY";
	$sql .= " tipo_mov)";
	$result = $db->query($sql);
	
	if ($result)
		foreach ($result as $reg)
			$comisiones += $reg['tipo_mov'] == 'f' ? $reg['importe'] : -$reg['importe'];
	
	/**** GASTOS DE CAJA ****/
	$egresos = $db->query("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'FALSE' AND clave_balance = 'TRUE'");
	$ingresos = $db->query("SELECT sum(importe) FROM gastos_caja WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'TRUE' AND clave_balance = 'TRUE'");
	$gastos_caja = $ingresos[0]['sum'] - $egresos[0]['sum'];
	
	/**** RESERVAS ****/
	$sql = "SELECT sum(importe) FROM reservas_cias WHERE num_cia = $num_cia AND fecha = '$fecha1'";
	$reservas = $db->query($sql);
	$reservas[0]['sum'] *= -1;
	
	/**** PAGOS HECHOS POR ANTICIPADO ****/
	$sql = "SELECT sum(importe) FROM pagos_anticipados WHERE num_cia = $num_cia AND (fecha_ini, fecha_fin) OVERLAPS (DATE '$fecha1', DATE '$fecha2')";
	$pagos_anticipados = $db->query($sql);
	$pagos_anticipados[0]['sum'] *= -1;
	
	/**** GASTOS PAGADOS POR OTRAS COMPAÑIAS ****/
	$cia_gasto_egreso = $db->query("SELECT sum(monto) FROM gastos_otras_cia WHERE num_cia_egreso = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'");
	$cia_gasto_ingreso = $db->query("SELECT sum(monto) FROM gastos_otras_cia WHERE num_cia_ingreso = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'");
	$gastos_otros = $cia_gasto_egreso[0]['sum'] - $cia_gasto_ingreso[0]['sum'];
	
	/**** TOTAL DE GASTOS ****/
	$gastos_totales = $gastos_oper[0]['sum'] + $gastos_gral[0]['sum'] + $gastos_caja + $reservas[0]['sum'] + $pagos_anticipados[0]['sum'] + $gastos_otros + $comisiones;
	
	/**** INGRESOS EXTRAORDINARIOS ****/
	if (empty($_GET['no_gastos'])) {
		$tmp = $db->query("SELECT sum(importe) FROM estado_cuenta WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_mov = 18");
		$ingresos_ext = $tmp[0]['sum'] > 0 ? $tmp[0]['sum'] : 0;
	}
	else
		$ingresos_ext = 0;
	
	/**** UTILIDAD NETA ****/
	$utilidad_neta = $gastos_totales + $ingresos_ext + $utilidad_bruta;
	
	/**** DATO HISTORICO DE CLIENTES ****/
	// Insertar o actualizar historico
//	$tmp = $db->query("SELECT sum(clientes) FROM efectivos_zap WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'");
//	$clientes = $tmp[0]['sum'] > 0 ? $tmp[0]['sum'] : 0;
//	$ing_ext = $ingresos_ext > 0 ? "TRUE" : "FALSE";
//	$prom = $clientes > 0 ? $venta_zap / $clientes : 0;
//	if ($id = $db->query("SELECT id FROM historico WHERE num_cia = $num_cia AND mes = $mes AND anio = $anio"))
//		$sql = "UPDATE historico SET utilidad = $utilidad_neta, venta = $venta_zap, gasto_ext = '$ing_ext', ingresos = $ingresos_ext, clientes = $clientes WHERE id = {$id[0]['id']}";
//	else
//		$sql = "INSERT INTO historico (num_cia, mes, anio, utilidad, venta, gasto_ext, ingresos, clientes) VALUES ($num_cia, $mes, $anio, $utilidad_neta, $venta_zap, '$ing_ext', $ingresos_ext, $clientes)";
//	$db->query($sql);
	
	// ************* HOJA 1, SECCION 4 *************
	
	/**** ENCARGADOS ****/
	$sql = "SELECT * FROM encargados WHERE num_cia = $num_cia AND mes = $mes AND anio = $anio";
	$encargado = $db->query($sql);
	
	/**** EMPLEADOS AFILIADOS AL IMSS (AGREGADO EL 23 DE NOVIEMBRE DE 2005) ****/
	/*if ($mes == date("n", mktime(0, 0, 0, date("n"), 0, date("Y"))) && $anio == date("Y", mktime(0, 0, 0, date("n"), 0, date("Y"))) && date("d") < 6) {
		$temp = $db->query("SELECT count(id) FROM catalogo_trabajadores WHERE num_cia = $num_cia AND num_afiliacion IS NOT NULL AND fecha_baja IS NULL");
		$emp_afi = $temp[0]['count'];
	}
	else {
		$temp = $db->query("SELECT emp_afi FROM balances_pan WHERE num_cia = $num_cia AND mes = $mes AND anio = $anio");
		$emp_afi = $temp ? $temp[0]['emp_afi'] : 0;
	}*/
	
	/**** HISTORICO DE BALANCES ANTERIORES ****/
	$historico_actual = $db->query("SELECT * FROM historico WHERE num_cia = $num_cia AND mes <= $mes AND anio = $anio ORDER BY mes");
	$historico_anterior = $db->query("SELECT * FROM historico WHERE num_cia = $num_cia AND anio = " . ($anio - 1) . " ORDER BY mes");
	
	$historico = $db->query("SELECT utilidad, ingresos FROM historico WHERE num_cia = $num_cia AND anio = " . ($anio - 1) . " AND mes = $mes");
	$utilidad_anterior = $historico ? $historico[0]['utilidad'] - $historico[0]['ingresos'] : 0;
	
	/**** ALMACENAR DATOS EN EL HISTORICO DE BALANCES ****/
	$bal['num_cia'] = $num_cia;
	$bal['mes'] = $mes;
	$bal['anio'] = $anio;
	$bal['venta_zap'] = $venta_zap;
	$bal['abono_emp'] = $abono_empleados;
	$bal['otros'] = $otros;
	//$bal['total_otros'] = $total_otros;
	//$bal['errores'] = $errores[0]['sum'];
	$bal['ventas_netas'] = $ventas_netas;
	$bal['inv_ant'] = $inv_ant;
	$bal['compras'] = $compras;
	$bal['inv_act'] = $inv_act;
	$bal['mat_prima_utilizada'] = $mat_prima_utilizada;
	$bal['costo_venta'] = $costo_venta;
	$bal['utilidad_bruta'] = $utilidad_bruta;
	$bal['gastos_generales'] = $gastos_gral[0]['sum'] != 0 ? $gastos_gral[0]['sum'] : "0";
	$bal['gastos_caja'] = $gastos_caja != 0 ? $gastos_caja : "0";
	$bal['reservas'] = $reservas[0]['sum'] != 0 ? $reservas[0]['sum'] : "0";
	$bal['gastos_otras_cias'] = $gastos_otros != 0 ? $gastos_otros : "0";
	$bal['total_gastos'] = $gastos_totales != 0 ? $gastos_totales : "0";
	$bal['ingresos_ext'] = $ingresos_ext != 0 ? $ingresos_ext : "0";
	$bal['utilidad_neta'] = $utilidad_neta != 0 ? $utilidad_neta : "0";
	$bal['pagos_anticipados'] = $pagos_anticipados[0]['sum'] != 0 ? $pagos_anticipados[0]['sum'] : "0";
	
//	if (empty($_GET['no_gastos'])) {
//		if ($id = $db->query("SELECT id FROM balances_zap WHERE num_cia = $num_cia AND mes = $mes AND anio = $anio"))
//			$db->query($db->preparar_update("balances_zap", $bal, "id = {$id[0]['id']}"));
//		else
//			$db->query($db->preparar_insert("balances_zap", $bal));
//	}
	
	// ************* MOSTRAR EN PANTALLA LA HOJA 1 *************
	$tpl->newBlock("reporte");
	
	/**** ENCABEZADO ****/
	$tpl->assign("num_cia", $num_cia);
	$tpl->assign("nombre_cia", $cia[$f]['nombre']);
	$tpl->assign("nombre_corto", $cia[$f]['nombre_corto']);
	$tpl->assign("anio", $anio);
	$tpl->assign("mes", mes_escrito($mes));
	
	/**** PRIMERA SECCION ****/
	$tpl->assign("venta_zap", $venta_zap != 0 ? "<font color=\"#" . ($venta_zap > 0 ? "0000FF" : "FF0000") . "\">" . number_format($venta_zap, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("p_venta_puerta", $venta_zap != 0 ? "<font color=\"#FF9900\">(" . number_format($venta_zap * 100 / $ventas_netas, 2, ".", ",") . "%)</font>" : "");
	$tpl->assign("abono_emp", $abono_empleados != 0 ? "<font color=\"#" . ($abono_empleados > 0 ? "0000FF" : "FF0000") . "\">" . number_format($abono_empleados, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("otros", $otros != 0 ? "<font color=\"#" . ($otros > 0 ? "0000FF" : "FF0000") . "\">" . number_format($otros, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("total_otros", $total_otros != 0 ? "<font color=\"#" . ($total_otros > 0 ? "0000FF" : "FF0000") . "\">" . number_format($total_otros, 2, ".", ",") . "</font>" : "&nbsp;");
	//$tpl->assign("errores", $errores[0]['sum'] != 0 ? "<font color=\"#" . ($errores[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($errores[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("ventas_netas", $ventas_netas != 0 ? "<font color=\"#" . ($ventas_netas > 0 ? "0000FF" : "FF0000") . "\">" . number_format($ventas_netas, 2, ".", ",") . "</font>" : "&nbsp;");
	
	/**** SEGUNDA SECCION ****/
	$tpl->assign("inventario_anterior", $inv_ant != 0 ? "<font color=\"#" . ($inv_ant > 0 ? "0000FF" : "FF0000") . "\">" . number_format($inv_ant, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("compras", $compras + $desc != 0 ? "<font color=\"#" . ($compras + $desc > 0 ? "0000FF" : "FF0000") . "\">" . number_format($compras + $desc, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("desc_compras", $desc_compras != 0 ? "<font color=\"#" . ($desc_compras > 0 ? "0000FF" : "FF0000") . "\">" . number_format($desc_compras, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("desc_pagos", $desc_pagos != 0 ? "<font color=\"#" . ($desc_pagos > 0 ? "0000FF" : "FF0000") . "\">" . number_format($desc_pagos, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("devoluciones", $dev[0]['importe'] != 0 ? "<font color=\"#" . ($dev[0]['importe'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($dev[0]['importe'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("inventario_actual", $inv_act != 0 ? "<font color=\"#" . ($inv_act > 0 ? "0000FF" : "FF0000") . "\">" . number_format($inv_act, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("traspaso", $traspaso != 0 ? "<font color=\"#" . ($traspaso > 0 ? "0000FF" : "FF0000") . "\">" . number_format($traspaso, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("mat_prima_utilizada", $mat_prima_utilizada != 0 ? "<font color=\"#" . ($mat_prima_utilizada > 0 ? "0000FF" : "FF0000") . "\">" . number_format($mat_prima_utilizada, 2, ".", ",") . "</font>" : "&nbsp;");
	
	$tpl->assign("dev_otros_meses", $dev_ant[0]['importe'] != 0 ? "<font color=\"#" . ($dev_ant[0]['importe'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($dev_ant[0]['importe'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("dev_otras_tiendas", $dev_otras[0]['importe'] != 0 ? "<font color=\"#" . ($dev_otras[0]['importe'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($dev_otras[0]['importe'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("dev_por_otras_tiendas", $dev_pag_otras[0]['importe'] != 0 ? "<font color=\"#" . ($dev_pag_otras[0]['importe'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($dev_pag_otras[0]['importe'], 2, ".", ",") . "</font>" : "&nbsp;");
	
	@$por_cos = $costo_venta * 100 / $venta_zap;
	$tpl->assign("costo_venta", $costo_venta != 0 ? "<font color=\"#" . ($costo_venta > 0 ? "0000FF" : "FF0000") . "\">" . number_format($costo_venta, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign('por_cos', $por_cos != 0 ? number_format($por_cos, 2, '.', ',') . '%' : '');
	@$por_uti_bru = $utilidad_bruta * 100 / $venta_zap;
	$tpl->assign("utilidad_bruta", $utilidad_bruta != 0 ? "<font color=\"#" . ($utilidad_bruta > 0 ? "0000FF" : "FF0000") . "\">" . number_format($utilidad_bruta, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign('por_uti_bru', $por_uti_bru != 0 ? number_format($por_uti_bru, 2, '.', ',') . '%' : '');
	
	/**** TERCERA SECCION ****/
	$tpl->assign("gastos_operacion", $gastos_oper[0]['sum'] != 0 ? "<font color=\"#" . ($gastos_oper[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($gastos_oper[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("gastos_generales", $gastos_gral[0]['sum'] != 0 ? "<font color=\"#" . ($gastos_gral[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($gastos_gral[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("comisiones", $comisiones != 0 ? "<font color=\"#" . ($comisiones > 0 ? "0000FF" : "FF0000") . "\">" . number_format($comisiones, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("gastos_caja", $gastos_caja != 0 ? "<font color=\"#" . ($gastos_caja > 0 ? "0000FF" : "FF0000") . "\">" . number_format($gastos_caja, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("reserva_aguinaldos", $reservas[0]['sum'] != 0 ? "<font color=\"#" . ($reservas[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($reservas[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("pagos_anticipados", $pagos_anticipados[0]['sum'] != 0 ? "<font color=\"#" . ($pagos_anticipados[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($pagos_anticipados[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign("gastos_otras_cias", $gastos_otros != 0 ? "<font color=\"#" . ($gastos_otros > 0 ? "0000FF" : "FF0000") . "\">" . number_format($gastos_otros, 2, ".", ",") . "</font>" : "&nbsp;");
	@$por_gas = $gastos_totales * 100 / $utilidad_bruta;
	$tpl->assign("total_gastos", $gastos_totales != 0 ? "<font color=\"#" . ($gastos_totales > 0 ? "0000FF" : "FF0000") . "\">" . number_format($gastos_totales, 2, ".", ",") . "</font>" : "&nbsp;");
	$tpl->assign('por_gas', $por_gas != 0 ? number_format($por_gas, 2, '.', ',') . '%' : '');
	$tpl->assign("ingresos_ext", $ingresos_ext[0]['sum'] != 0 ? "<font color=\"#" . ($ingresos_ext[0]['sum'] > 0 ? "0000FF" : "FF0000") . "\">" . number_format($ingresos_ext[0]['sum'], 2, ".", ",") . "</font>" : "&nbsp;");
	@$por_uti = $utilidad_neta * 100 / $venta_zap;
	$tpl->assign("utilidad_mes", $utilidad_neta != 0 ? "<font color=\"#" . ($utilidad_neta > 0 ? "0000FF" : "FF0000") . "\">" . number_format($utilidad_neta, 2, ".", ",") . "</font>" . ($utilidad_neta - $utilidad_anterior - $ingresos_ext[0]['sum'] != 0 && $utilidad_anterior != 0 ? $utilidad_neta - $utilidad_anterior - $ingresos_ext[0]['sum'] > 0 ? "&nbsp;&nbsp;MEJORO" : "&nbsp;&nbsp;EMPEORO" : "") : "&nbsp;");
	$tpl->assign('por_uti', $por_uti != 0 ? number_format($por_uti, 2, '.', ',') . '%' : '');
	
	/**** CUARTO SECCION ****/
	$tpl->assign("inicio", $encargado[0]['nombre_inicio']);
	$tpl->assign("termino", $encargado[0]['nombre_fin']);
	
	/**** HISTORICO SALDOS BANCOS ****/
	$sql = "SELECT extract(month from fecha) AS mes, tipo_mov, sum(importe) AS importe FROM estado_cuenta WHERE num_cia = $num_cia AND fecha BETWEEN '01/01/$anio' AND CURRENT_DATE GROUP BY mes, tipo_mov ORDER BY mes DESC, tipo_mov";
	$mov_esc = $db->query($sql);
	$tmp = $db->query("SELECT sum(saldo_libros) FROM saldos WHERE num_cia = $num_cia");
	$saldo_act = $tmp[0]['sum'] != 0 ? $tmp[0]['sum'] : 0;
	
	$saldos = array(1 => NULL, 2 => NULL, 3 => NULL, 4 => NULL, 5 => NULL, 6 => NULL, 7 => NULL, 8 => NULL, 9 => NULL, 10 => NULL, 11 => NULL, 12 => NULL);
	if ($mov_esc)
		foreach ($mov_esc as $reg) {
			if ($saldos[$reg['mes']] == NULL) $saldos[$reg['mes']] = $saldo_act;
			$saldo_act += $reg['tipo_mov'] == 'f' ? -$reg['importe'] : $reg['importe'];
		}
	
	foreach ($saldos as $m => $v)
		if ($v != NULL && $m <= $mes)
			$tpl->assign("sal$m", $v != 0 ? "<span style=\"color=#" . ($v > 0 ? "0000CC" : "CC0000") . ";\">" . number_format($v, 0, ".", ",") . "</span>" : "");
	
	/**** HISTORICO DE SALDO A PROVEEDORES ****/
	$salPro = array();
	for ($m = 1; $m <= $mes; $m++) {
		$fecha_tmp = date('d/m/Y', mktime(0, 0, 0, $m + 1, 0, $anio));
		$tmp = $db->query("SELECT sum(total) AS saldo FROM facturas_zap WHERE num_cia = $num_cia AND fecha <= '$fecha_tmp' AND (tspago IS NULL OR tspago > cast('$fecha_tmp' as date))");
		$tpl->assign('salpro' . $m, $tmp[0]['saldo'] > 0 ? number_format($tmp[0]['saldo'], 2, '.', ',') : '');
	}
	
	/**** HISTORICO DE PARES VENDIDOS ****/
	$sql = "SELECT extract(month from fecha) AS mes, sum(pares) AS pares FROM efectivos_zap WHERE num_cia = $num_cia AND fecha BETWEEN '01/01/$anio' AND '$fecha2' GROUP BY mes ORDER BY mes";
	$pares = $db->query($sql);
	
	if ($pares)
		foreach ($pares as $reg)
			$tpl->assign("parven" . $reg['mes'], $reg['mes'] <= $mes && $reg['pares'] > 0 ? number_format($reg['pares'], 0, ".", ",") : "");
	
	/**** HISTORICO INVENTARIO ****/
	$his_inv = $db->query("SELECT mes, importe FROM inventario_zap WHERE num_cia = $num_cia AND anio = $anio ORDER BY mes");
	if ($his_inv)
		foreach ($his_inv as $reg)
			$tpl->assign("inv" . $reg['mes'], $reg['mes'] <= $mes && $reg['importe'] != 0 ? number_format($reg['importe'], 0, ".", ",") : "");
	
	/**** HISTORICO EFECTIVOS ****/
	$sql = "SELECT extract(month from fecha) AS mes, sum(efectivo) AS efectivo FROM total_zapaterias WHERE num_cia = $num_cia AND fecha BETWEEN '01/01/$anio' AND '$fecha2' GROUP BY mes ORDER BY mes";
	$his_efe = $db->query($sql);
	if ($his_efe)
		foreach ($his_efe as $reg)
			$tpl->assign("efe" . $reg['mes'], $reg['mes'] <= $mes && $reg['efectivo'] != 0 ? number_format($reg['efectivo'], 0, ".", ",") : "");
	
	/**** SUB-SECCION RESERVAS ****/
	$sql = "SELECT cod_reserva, descripcion, importe, codgastos FROM reservas_cias LEFT JOIN catalogo_reservas ON (tipo_res = cod_reserva) WHERE num_cia = $num_cia AND fecha = '$fecha1' ORDER BY cod_reserva";
	$result = $db->query($sql);
	
	if ($result) {
		// Crear títulos
		$tpl->assign("titulo_reserva", "Reserva");
		$tpl->assign("titulo_importe", "Importe");
		// Si el año es 2005, crear titulos de pagado y diferencia
		if ($anio == 2005) {
			$tpl->assign("titulo_pagado", "Pagado");
			$tpl->assign("titulo_diferencia", "Diferencia");
		}
		
		$total_result = 0;
		$total_pagado = 0;
		$total_diferencia = 0;
		$diferencia = 0;
		
		$imss_infonavit = FALSE;
		for ($r = 0; $r < count($result); $r++) {
			$tpl->assign("nombre_reserva" . ($r + 1), $result[$r]['descripcion']);
			$tpl->assign("importe_reserva" . ($r + 1), number_format($result[$r]['importe'], 2, ".", ","));
			$total_result += $result[$r]['importe'];
			
			if ($result[$r]['cod_reserva'] == 4) $imss_infonavit = TRUE;
		}
		$tpl->assign("nombre_reserva" . ($r + 1), "<strong>Total</strong>");
		$tpl->assign("importe_reserva" . ($r + 1), "<strong>" . number_format($total_result, 2, ".", ",") . "</strong>");
	}
	
	/*****************************/
	
	/**** QUINTA SECCION ****/
	$tpl->assign("utilidad_anio_ant", "<font color='#" . (($utilidad_anterior > 0) ? "0000FF" : "FF0000") . "'>" . number_format($utilidad_anterior, 2, ".", ",") . "</font>");
	
	/**** UTILIDAD AÑO ANTERIOR ****/
	$vta = 0;
	$abono = 0;
	$clientes = 0;
	for ($h = 0; $h < count($historico_anterior); $h++) {
		$tpl->assign("tant_" . $historico_anterior[$h]['mes'], mes_abreviado($historico_anterior[$h]['mes']));
		$tpl->assign("ant_" . $historico_anterior[$h]['mes'], "<font color='#0000FF'>" . number_format($historico_anterior[$h]['utilidad'] - (isset($_GET['no_gastos']) ? $historico_anterior[$h]['ingresos'] : 0) - ($anio - 1 == 2004 ? ($num_cia == 44 ? 25000 : ($num_cia == 144 ? 8000 : ($num_cia == 40 ? 5000 : ($num_cia == 131 ? 2000 : ($num_cia == 16 ? 15000 : ($num_cia == 154 ? 10000 : ($num_cia == 41 ? 40000 : ($num_cia == 173 ? 15000 : 0)))))))) : 0), 2, ".", ",") . "</font>" . (($historico_anterior[$h]['ingresos'] != 0 && empty($_GET['no_gastos'])) ? " <font color='#FF0000'>(" . number_format($historico_anterior[$h]['ingresos'], 2, ".", ",") . ")</font>" : ""));
		$tpl->assign("vta_ant_" . $historico_anterior[$h]['mes'], number_format($historico_anterior[$h]['venta'], 2, ".", ","));
		$tpl->assign("clientes_ant_" . $historico_anterior[$h]['mes'], number_format($historico_anterior[$h]['clientes'], 2, ".", ","));
		$tpl->assign("prom_ant_" . $historico_anterior[$h]['mes'], $historico_anterior[$h]['clientes'] != 0 ? number_format($historico_anterior[$h]['venta'] / $historico_anterior[$h]['clientes'], 2, ".", ",") : "&nbsp;");
		
		$vta += $historico_anterior[$h]['venta'];
		$clientes += $historico_anterior[$h]['clientes'];
	}
	$tpl->assign("tot_vta_ant", $vta > 0 ? number_format($vta, 2, ".", ",") : "");
	$tpl->assign("tot_abono_ant", $abono > 0 ? number_format($abono, 2, ".", ",") : "");
	$tpl->assign("tot_clientes_ant", $clientes > 0 ? number_format($clientes, 2, ".", ",") : "");
	
	/**** UTILIDAD AÑO ACTUAL ****/
	$vta = 0;
	$abono = 0;
	$clientes = 0;	
	for ($h=0; $h<count($historico_actual); $h++) {
		$tpl->assign("tact_" . $historico_actual[$h]['mes'], mes_abreviado($historico_actual[$h]['mes']));
		$tpl->assign("act_" . $historico_actual[$h]['mes'], "<font color='#0000FF'>" . number_format($historico_actual[$h]['utilidad'] - (isset($_GET['no_gastos']) ? $historico_actual[$h]['ingresos'] : 0), 2, ".", ",") . "</font>" . (($historico_actual[$h]['ingresos'] != 0 && empty($_GET['no_gastos'])) ? " <font color='#FF0000'>(" . number_format($historico_actual[$h]['ingresos'], 2, ".", ",").")</font>" : ""));
		$tpl->assign("vta_" . $historico_actual[$h]['mes'], number_format($historico_actual[$h]['venta'], 2, ".", ","));
		$tpl->assign("por_efe_" . $historico_actual[$h]['mes'], number_format($historico_actual[$h]['por_efe'], 2, ".", ","));
		$tpl->assign("clientes_" . $historico_actual[$h]['mes'], number_format($historico_actual[$h]['clientes'], 0, ".", ","));
		$tpl->assign("prom_" . $historico_actual[$h]['mes'], $historico_actual[$h]['clientes'] != 0 ? number_format($historico_actual[$h]['venta'] / $historico_actual[$h]['clientes'], 2, ".", ",") : "&nbsp;");
		
		$vta += $historico_actual[$h]['venta'];
		$clientes += $historico_actual[$h]['clientes'];
	}
	$tpl->assign("tot_vta", $vta > 0 ? number_format($vta, 2, ".", ",") : "");
	$tpl->assign("tot_clientes", $clientes > 0 ? number_format($clientes, 2, ".", ",") : "");
	
	// *********************************************************
	
	// ************* HOJA 2 RELACION DE GASTOS EXTRAS *************
	
	$tpl->newBlock("gastos_extras");
	$tpl->assign("num_cia", $num_cia);
	$tpl->assign("nombre_cia", $cia[$f]['nombre']);
	$tpl->assign("dia", $dias);
	$tpl->assign("anio", $anio);
	$tpl->assign("mes", mes_escrito($mes));
	
	// Fechas del mes anterior
	$fecha1_ant = date("d/m/Y", mktime(0, 0, 0, $mes - 1, 1, $anio));
	$fecha2_ant = date("d/m/Y", mktime(0, 0, 0, $mes, 0, $anio));
	$fecha1_anio_ant = date("d/m/Y", mktime(0, 0, 0, $mes, 1, $anio - 1));
	$fecha2_anio_ant = date("d/m/Y", mktime(0, 0, 0, $mes + 1, 0, $anio - 1));
	
	$mes_ant = date("n", mktime(0, 0, 0, $mes, 0, $anio));
	$anio_ant = date("Y", mktime(0, 0, 0, $mes, 0, $anio));
	$anio_anio_ant = $anio - 1;
	
	$sql = "SELECT codgastos, descripcion, codigo_edo_resultados AS tipo, extract(month FROM fecha) AS mes, extract(year FROM fecha) AS anio, sum(importe) AS importe FROM movimiento_gastos";
	$sql .= " LEFT JOIN catalogo_gastos USING (codgastos) WHERE num_cia = $num_cia AND (fecha BETWEEN '$fecha1_ant' AND '$fecha2' OR fecha BETWEEN '$fecha1_anio_ant' AND '$fecha2_anio_ant')";
	if ($db->query("SELECT id FROM reservas_cias WHERE num_cia = $num_cia AND fecha = '$fecha1' AND cod_reserva = 4"))
		$sql .= " AND codigo_edo_resultados IN (1, 2) AND codgastos NOT IN (141, 140)";
	else
		$sql .= " AND codigo_edo_resultados IN (1, 2) AND codgastos NOT IN (140)";
	$sql .= " AND (importe >= 0.01 OR importe <= -0.01) GROUP BY codgastos, descripcion, codigo_edo_resultados, extract(month FROM fecha), extract(year FROM fecha)";
	$sql .= " ORDER BY codigo_edo_resultados, codgastos, extract(year FROM fecha) DESC, extract(month FROM fecha) DESC";
	$result = $db->query($sql);
	
	$total_importe = 0;
	$total_mes_ant = 0;
	$total_anio_ant = 0;
	
	$tipo = NULL;
	$codtmp = NULL;
	if ($result) {
		foreach ($result as $cod) {
			if ($tipo != $cod['tipo']) {
				if ($tipo != NULL) {
					$tpl->assign("tipo_gasto.importe", number_format($imp, 2, ".", ","));
					$tpl->assign("tipo_gasto.mes_ant", number_format($mes_par, 2, ".", ","));
					$tpl->assign("tipo_gasto.anio_ant", number_format($anio_par, 2, ".", ","));
				}
				
				$tipo = $cod['tipo'];
				$nombre_tipo = $tipo == 1 ? "GASTOS DE OPERACI&Oacute;N" : "GASTOS GENERALES";
				
				$tpl->newBlock("tipo_gasto");
				$tpl->assign("tipo_gasto", $nombre_tipo);
				$tpl->assign("title_mes", mes_escrito($mes));
				$tpl->assign("title_anio", $anio);
				$tpl->assign("title_mes_ant", mes_escrito($mes_ant));
				$tpl->assign("title_anio_ant", $anio_ant);
				$tpl->assign("title_mes_anio_ant", mes_escrito($mes));
				$tpl->assign("title_anio_anio_ant", $anio_anio_ant);
				
				$imp = 0;
				$mes_par = 0;
				$anio_par = 0;
			}
			if ($codtmp != $cod['codgastos']) {
				$codtmp = $cod['codgastos'];
				$tpl->newBlock("fila_gasto");
				$tpl->assign("codgastos", $cod['codgastos']);
				$tpl->assign("concepto", $cod['descripcion']);
			}
			$tpl->assign($cod['mes'] == $mes && $cod['anio'] == $anio ? "importe" : ($cod['mes'] == $mes_ant ? "mes_ant" : "anio_ant"), $cod['importe'] != 0 ? number_format($cod['importe'], 2, ".", ",") : "&nbsp;");
			$tpl->assign('num_cia', $num_cia);
			$tpl->assign($cod['mes'] == $mes ? "mes" : "_mes_ant", $cod['mes']);
			$tpl->assign($cod['anio'] == $anio && $cod['mes'] == $mes ? "anio" : ($cod['anio'] == $anio_ant && $cod['mes'] == $mes_ant ? "_anio_mes_ant" : "_anio_ant"), $cod['anio']);
			
			$imp += $cod['mes'] == $mes && $cod['anio'] == $anio ? $cod['importe'] : 0;
			$mes_par += $cod['mes'] == $mes_ant ? $cod['importe'] : 0;
			$anio_par += $cod['mes'] == $mes && $cod['anio'] == $anio_anio_ant ? $cod['importe'] : 0;
			
			$total_importe += $cod['mes'] == $mes && $cod['anio'] == $anio ? $cod['importe'] : 0;
			$total_mes_ant += $cod['mes'] == $mes_ant ? $cod['importe'] : 0;
			$total_anio_ant += $cod['mes'] == $mes && $cod['anio'] == $anio_anio_ant ? $cod['importe'] : 0;
		}
		if ($tipo != NULL) {
			$tpl->assign("tipo_gasto.importe", number_format($imp, 2, ".", ","));
			$tpl->assign("tipo_gasto.mes_ant", number_format($mes_par, 2, ".", ","));
			$tpl->assign("tipo_gasto.anio_ant", number_format($anio_par, 2, ".", ","));
		}
		$tpl->assign("gastos_extras.total_importe", number_format($total_importe, 2, ".", ","));
		$tpl->assign("gastos_extras.total_mes_ant", number_format($total_mes_ant, 2, ".", ","));
		$tpl->assign("gastos_extras.total_anio_ant", number_format($total_anio_ant, 2, ".", ","));
	}
	
	// 3er bloque de gastos extras (GASTOS POR CAJA)
	$sql = "SELECT cod_gastos, descripcion, tipo_mov, extract(month FROM fecha) AS mes, extract(year FROM fecha) AS anio, sum(importe) AS importe FROM gastos_caja";
	$sql .= " LEFT JOIN catalogo_gastos_caja ON (catalogo_gastos_caja.id = cod_gastos) WHERE num_cia = $num_cia AND (fecha BETWEEN '$fecha1_ant' AND '$fecha2' OR";
	$sql .= " fecha BETWEEN '$fecha1_anio_ant' AND '$fecha2_anio_ant') AND clave_balance = 'TRUE' GROUP BY cod_gastos, descripcion, extract(month FROM fecha), extract(year FROM fecha), tipo_mov ORDER BY";
	$sql .= " cod_gastos, extract(year FROM fecha) DESC, extract(month FROM fecha) DESC, tipo_mov";
	$result = $db->query($sql);
	
	if ($result) {
		$total_importe = 0;
		$total_mes_ant = 0;
		$total_anio_ant = 0;
		
		$tpl->newBlock("gastos_caja");
		$tpl->assign("title_mes", mes_escrito($mes));
		$tpl->assign("title_anio", $anio);
		$tpl->assign("title_mes_ant", mes_escrito($mes_ant));
		$tpl->assign("title_anio_ant", $anio_ant);
		$tpl->assign("title_mes_anio_ant", mes_escrito($mes));
		$tpl->assign("title_anio_anio_ant", $anio_anio_ant);
		
		$codtmp = NULL;
		foreach ($result as $cod) {
			if ($codtmp != $cod['cod_gastos']) {
				$codtmp = $cod['cod_gastos'];
				$tpl->newBlock("fila_caja");
				$tpl->assign("concepto", $cod['descripcion']);
				
				$tmp = array("importe" => 0, "mes_ant" => 0, "anio_ant" => 0);
			}
			$etiqueta = $cod['mes'] == $mes && $cod['anio'] == $anio ? "importe" : ($cod['mes'] == $mes_ant ? "mes_ant" : "anio_ant");
			$tmp[$etiqueta] += $cod['tipo_mov'] == "t" ? $cod['importe'] : -$cod['importe'];
			$tpl->assign($etiqueta, number_format($tmp[$etiqueta], 2, ".", ","));
			
			$total_importe += $cod['mes'] == $mes && $cod['anio'] == $anio ? ($cod['tipo_mov'] == "t" ? $cod['importe'] : -$cod['importe']) : 0;
			$total_mes_ant += $cod['mes'] == $mes_ant ? ($cod['tipo_mov'] == "t" ? $cod['importe'] : -$cod['importe']) : 0;
			$total_anio_ant += $cod['mes'] == $mes && $cod['anio'] == $anio_anio_ant ? ($cod['tipo_mov'] == "t" ? $cod['importe'] : -$cod['importe']) : 0;
		}
		$tpl->assign("gastos_caja.total_importe", number_format($total_importe, 2, ".", ","));
		$tpl->assign("gastos_caja.total_mes_ant", number_format($total_mes_ant, 2, ".", ","));
		$tpl->assign("gastos_caja.total_anio_ant", number_format($total_anio_ant, 2, ".", ","));
	}
	
	/**************************************** ESTADO DE RESULTADOS COMPARATIVO *************************************************/
	$tpl->assign("reporte.mes_anio_ant", mes_escrito($mes));
	$tpl->assign("reporte.anio_anio_ant", $anio - 1);
	$tpl->assign("reporte.mes_ant" , mes_escrito($mes - 1 > 0 ? $mes - 1 : 12));
	$tpl->assign("reporte.anio_ant", $mes - 1 > 0 ? $anio : $anio - 1);
	$tpl->assign("reporte.mes_act" , mes_escrito($mes));
	$tpl->assign("reporte.anio_act", $anio);
	
	$tpl->gotoBlock("reporte");
	
	// Obtener datos del balance anterior
	/*$sql = "SELECT * FROM balances_zap WHERE num_cia = $num_cia AND mes = " . ($mes - 1 > 0 ? $mes - 1 : 12)." AND anio = " . ($mes - 1 > 0 ? $anio : $anio - 1);
	$bal_ant = $db->query($sql);
	
	$sql = "SELECT * FROM balances_zap WHERE num_cia = $num_cia AND mes = $mes AND anio = " . ($anio - 1);
	$bal_anio_ant = $db->query($sql);
	
	if ($bal_ant) {
		// Balance anterior
		$tpl->assign("vta_pta_ant",($bal_ant[0]['venta_puerta'] != 0)?"<font color='#".(($bal_ant[0]['venta_puerta'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['venta_puerta'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("abono_emp_ant",($bal_ant[0]['abono_emp'] != 0)?"<font color='#".(($bal_ant[0]['abono_emp'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['abono_emp'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("otros_ant",($bal_ant[0]['otros'] != 0)?"<font color='#".(($bal_ant[0]['otros'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['otros'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("total_otros_ant",($bal_ant[0]['total_otros'] != 0)?"<font color='#".(($bal_ant[0]['total_otros'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['total_otros'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("errores_ant",($bal_ant[0]['errores'] != 0)?"<font color='#".(($bal_ant[0]['errores'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['errores'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("ventas_netas_ant",($bal_ant[0]['ventas_netas'] != 0)?"<font color='#".(($bal_ant[0]['ventas_netas'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['ventas_netas'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("inventario_anterior_ant",($bal_ant[0]['inv_ant'] != 0)?"<font color='#".(($bal_ant[0]['inv_ant'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['inv_ant'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("compras_ant",($bal_ant[0]['compras'] != 0)?"<font color='#".(($bal_ant[0]['compras'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['compras'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("inventario_actual_ant",($bal_ant[0]['inv_act'] != 0)?"<font color='#".(($bal_ant[0]['inv_act'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['inv_act'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("mat_prima_utilizada_ant",($bal_ant[0]['mat_prima_utilizada'] != 0)?"<font color='#".(($bal_ant[0]['mat_prima_utilizada'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['mat_prima_utilizada'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("costo_produccion_ant",($bal_ant[0]['costo_produccion'] != 0)?"<font color='#".(($bal_ant[0]['costo_produccion'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['costo_produccion'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("utilidad_bruta_ant",($bal_ant[0]['utilidad_bruta'] != 0)?"<font color='#".(($bal_ant[0]['utilidad_bruta'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['utilidad_bruta'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("gastos_generales_ant",($bal_ant[0]['gastos_generales'] != 0)?"<font color='#".(($bal_ant[0]['gastos_generales'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['gastos_generales'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("gastos_caja_ant",($bal_ant[0]['gastos_caja'] != 0)?"<font color='#".(($bal_ant[0]['gastos_caja'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['gastos_caja'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("reserva_aguinaldos_ant",($bal_ant[0]['reserva_aguinaldos'] != 0)?"<font color='#".(($bal_ant[0]['reserva_aguinaldos'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['reserva_aguinaldos'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("pagos_anticipados_ant",($bal_ant[0]['pagos_anticipados'] != 0)?"<font color='#".(($bal_ant[0]['pagos_anticipados'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['pagos_anticipados'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("gastos_otras_cias_ant",($bal_ant[0]['gastos_otras_cias'] != 0)?"<font color='#".(($bal_ant[0]['gastos_otras_cias'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['gastos_otras_cias'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("total_gastos_ant",($bal_ant[0]['total_gastos'] != 0)?"<font color='#".(($bal_ant[0]['total_gastos'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['total_gastos'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("ingresos_ext_ant",($bal_ant[0]['ingresos_ext'] != 0)?"<font color='#".(($bal_ant[0]['ingresos_ext'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['ingresos_ext'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("utilidad_mes_ant",($bal_ant[0]['utilidad_neta'] != 0)?"<font color='#".(($bal_ant[0]['utilidad_neta'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_ant[0]['utilidad_neta'],2,".",",")."</font>":"&nbsp;");
		
		// Año anterior
		$tpl->assign("venta_puerta_aa",($bal_anio_ant[0]['venta_puerta'] != 0)?"<font color='#".(($bal_anio_ant[0]['venta_puerta'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['venta_puerta'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("abono_emp_aa",($bal_anio_ant[0]['abono_emp'] != 0)?"<font color='#".(($bal_anio_ant[0]['abono_emp'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['abono_emp'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("otros_aa",($bal_anio_ant[0]['otros'] != 0)?"<font color='#".(($bal_anio_ant[0]['otros'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['otros'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("total_otros_aa",($bal_anio_ant[0]['total_otros'] != 0)?"<font color='#".(($bal_anio_ant[0]['total_otros'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['total_otros'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("errores_aa",($bal_anio_ant[0]['errores'] != 0)?"<font color='#".(($bal_anio_ant[0]['errores'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['errores'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("ventas_netas_aa",($bal_anio_ant[0]['ventas_netas'] != 0)?"<font color='#".(($bal_anio_ant[0]['ventas_netas'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['ventas_netas'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("inventario_anterior_aa",($bal_anio_ant[0]['inv_ant'] != 0)?"<font color='#".(($bal_anio_ant[0]['inv_ant'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['inv_ant'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("compras_aa",($bal_anio_ant[0]['compras'] != 0)?"<font color='#".(($bal_anio_ant[0]['compras'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['compras'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("inventario_actual_aa",($bal_anio_ant[0]['inv_act'] != 0)?"<font color='#".(($bal_anio_ant[0]['inv_act'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['inv_act'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("mat_prima_utilizada_aa",($bal_anio_ant[0]['mat_prima_utilizada'] != 0)?"<font color='#".(($bal_anio_ant[0]['mat_prima_utilizada'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['mat_prima_utilizada'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("costo_produccion_aa",($bal_anio_ant[0]['costo_produccion'] != 0)?"<font color='#".(($bal_anio_ant[0]['costo_produccion'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['costo_produccion'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("utilidad_bruta_aa",($bal_anio_ant[0]['utilidad_bruta'] != 0)?"<font color='#".(($bal_anio_ant[0]['utilidad_bruta'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['utilidad_bruta'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("gastos_generales_aa",($bal_anio_ant[0]['gastos_generales'] != 0)?"<font color='#".(($bal_anio_ant[0]['gastos_generales'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['gastos_generales'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("gastos_caja_aa",($bal_anio_ant[0]['gastos_caja'] != 0)?"<font color='#".(($bal_anio_ant[0]['gastos_caja'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['gastos_caja'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("reserva_aguinaldos_aa",($bal_anio_ant[0]['reserva_aguinaldos'] != 0)?"<font color='#".(($bal_anio_ant[0]['reserva_aguinaldos'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['reserva_aguinaldos'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("pagos_anticipados_aa",($bal_anio_ant[0]['pagos_anticipados'] != 0)?"<font color='#".(($bal_anio_ant[0]['pagos_anticipados'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['pagos_anticipados'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("gastos_otras_cias_aa",($bal_anio_ant[0]['gastos_otras_cias'] != 0)?"<font color='#".(($bal_anio_ant[0]['gastos_otras_cias'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['gastos_otras_cias'],2,".",",")."</font>":"&nbsp;");
		$tpl->assign("total_gastos_aa",($bal_anio_ant[0]['total_gastos'] != 0)?"<font color='#".(($bal_anio_ant[0]['total_gastos'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['total_gastos'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("ingresos_ext_aa",($bal_anio_ant[0]['ingresos_ext'] != 0)?"<font color='#".(($bal_anio_ant[0]['ingresos_ext'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['ingresos_ext'],2,".",",")."</font>":"&nbsp;");
		
		$tpl->assign("utilidad_mes_aa",($bal_anio_ant[0]['utilidad_neta'] != 0)?"<font color='#".(($bal_anio_ant[0]['utilidad_neta'] > 0)?"0000FF":"FF0000")."'>".number_format($bal_anio_ant[0]['utilidad_neta'],2,".",",")."</font>":"&nbsp;");
	}*/
	
	/******************************************* LISTADO DE GASTOS PAGADOS A OFICINAS **********************************************/
	$sql = "SELECT fecha, codgastos, descripcion, facturas, concepto, importe, folio, a_nombre,(SELECT descripcion FROM facturas_pagadas WHERE num_cia = cheques.num_cia AND";
	$sql .= " folio_cheque = cheques.folio LIMIT 1) AS desc FROM cheques JOIN catalogo_gastos USING(codgastos) WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$sql .= " AND codgastos NOT IN (33, 134, 154, 999, 140) AND (fecha_cancelacion IS NULL OR fecha_cancelacion > '$fecha2') ORDER BY codgastos, fecha";
	$result = $db->query($sql);
	
	if ($result){
		$tpl->newBlock("listado_gastos");
		
		$tpl->assign("num_cia",$num_cia);
		$tpl->assign("nombre_cia",$cia[$f]['nombre']);
		$tpl->assign("dia",$dias);
		$tpl->assign("mes",mes_escrito($mes));
		$tpl->assign("anio",$anio);
		
		$codgastos = NULL;
		$count = 0;
		$total = 0;
		for ($i=0; $i<count($result); $i++) {
			if ($codgastos != $result[$i]['codgastos']) {
				if ($codgastos != NULL && $count > 1) {
					$tpl->newBlock("total");
					$tpl->assign("total_gasto",number_format($subtotal,2,".",","));
				}
				
				$codgastos = $result[$i]['codgastos'];
				
				$tpl->newBlock("gasto");
				
				$subtotal = 0;
				$count = 0;
			}
			$tpl->newBlock("fila");
			$tpl->assign("fecha",$result[$i]['fecha']);
			$tpl->assign("codgastos",$result[$i]['codgastos']);
			$tpl->assign("descripcion",$result[$i]['descripcion']);
			$tpl->assign("a_nombre",$result[$i]['a_nombre']);
			$tpl->assign("facturas",$result[$i]['facturas']);
			$tpl->assign("concepto", (($result[$i]['facturas'] > 0) ? "<br>" : "") . ($result[$i]['desc'] != "" ? $result[$i]['desc'] : $result[$i]['concepto']));
			$tpl->assign("importe",number_format($result[$i]['importe'],2,".",","));
			$tpl->assign("folio",$result[$i]['folio']);
			
			$subtotal += $result[$i]['importe'];
			$total += $result[$i]['importe'];
			
			$count++;
		}
		if ($codgastos != NULL && $count > 1) {
			$tpl->newBlock("total");
			$tpl->assign("total_gasto",number_format($subtotal,2,".",","));
		}
		$tpl->assign("listado_gastos.total_gastos",number_format($total,2,".",","));
	}
	
	if (count($cia) > 1)
		$tpl->newBlock("salto");
}

$tpl->printToScreen();
?>