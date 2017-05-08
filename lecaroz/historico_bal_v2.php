<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

$condiciones = array();

$condiciones[] = "bal.anio = '{$_REQUEST['anio']}'";

if (isset($_REQUEST['num_cia']) && count(array_filter($_REQUEST['num_cia'])) > 0)
{
	$condiciones[] = 'bal.num_cia IN (' . implode(', ', array_filter($_REQUEST['num_cia'])) . ')';
}

if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
{
	$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
}

$condiciones_string = implode(' AND ', $condiciones);

if (isset($_REQUEST['agrupar_totales'])) {
	/*$sql = "SELECT
		10000 AS num_cia,
		'TOTALES GENERALES DE PANADERIAS' AS nombre,
		anio,
		mes AS titulo,
		SUM(venta_puerta) AS venta_puerta,
		SUM(bases) AS bases,
		SUM(barredura) AS barredura,
		SUM(pastillaje) AS pastillaje,
		SUM(abono_emp) AS abono_emp,
		SUM(otros) AS otros,
		SUM(total_otros) AS total_otros,
		SUM(abono_reparto) AS abono_reparto,
		SUM(errores) AS errores,
		SUM(ventas_netas) AS ventas_netas,
		NULL AS blank1,
		SUM(inv_ant) AS inv_ant,
		SUM(compras) AS compras,
		SUM(mercancias) AS mercancias,
		SUM(inv_act) AS inv_act,
		SUM(mat_prima_utilizada) AS mat_prima_utilizada,
		SUM(mano_obra) AS mano_obra,
		SUM(panaderos) AS panaderos,
		SUM(gastos_fab) AS gastos_fab,
		SUM(costo_produccion) AS costo_produccion,
		NULL AS blank2,
		SUM(utilidad_bruta) AS utilidad_bruta,
		NULL AS blank3,
		SUM(pan_comprado) AS pan_comprado,
		SUM(gastos_generales) AS gastos_generales,
		SUM(gastos_caja) AS gastos_caja,
		SUM(comisiones) AS comisiones,
		SUM(reserva_aguinaldos) AS reserva_aguinaldos,
		SUM(pagos_anticipados) AS pagos_anticipados,
		SUM(gastos_otras_cias) AS gastos_otras_cias,
		SUM(total_gastos) AS total_gastos,
		NULL AS blank4,
		SUM(ingresos_ext) AS ingresos_ext,
		NULL AS blank5,
		SUM(utilidad_neta) AS utilidad_neta,
		NULL AS blank6,
		NULL AS mp_vtas,
		NULL AS utilidad_pro,
		NULL AS mp_pro,
		NULL AS gas_pro,
		NULL AS blank7,
		SUM(produccion_total) AS produccion_total,
		SUM(ganancia) AS ganancia,
		NULL AS porc_ganancia,
		SUM(faltante_pan) AS faltante_pan,
		SUM(devoluciones) AS devoluciones,
		SUM(rezago_ini) AS rezago_ini,
		SUM(rezago_fin) AS rezago_fin,
		NULL AS var_rezago,
		SUM(efectivo) AS efectivo,
		NULL AS blank8,
		COALESCE((
			SELECT
				SUM(importe)
			FROM
				gastos_caja
			WHERE
				fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND tipo_mov = TRUE
		), 0) AS ingresos,
		COALESCE((
			SELECT
				SUM(importe)
			FROM
				gastos_caja
			WHERE
				fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND tipo_mov = FALSE
		), 0) AS egresos,
		COALESCE((
			SELECT
				SUM(CASE
					WHEN tipo_mov = TRUE THEN
						importe
					ELSE
						-importe
				END)
			FROM
				gastos_caja
			WHERE
				fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
		), 0) AS total_gastos_caja,
		COALESCE((
			SELECT
				SUM(importe)
			FROM
				estado_cuenta
			WHERE
				fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND cod_mov = 1
		), 0) AS depositos,
		COALESCE((
			SELECT
				SUM(importe)
			FROM
				otros_depositos
			WHERE
				fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
		), 0) AS otros_depositos,
		NULL AS blank9,
		COALESCE((
			SELECT
				SUM(saldo_libros) + COALESCE((
					SELECT
						SUM(CASE
							WHEN tipo_mov = TRUE THEN
								importe
							ELSE
								-importe
						END)
					FROM
						estado_cuenta
					WHERE
						fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE - INTERVAL '1 DAY' AND NOW()::DATE
				), 0)
			FROM
				saldos
		), 0) AS saldo_ini,
		COALESCE((
			SELECT
				SUM(saldo_libros) + COALESCE((
					SELECT
						SUM(CASE
							WHEN tipo_mov = TRUE THEN
								importe
							ELSE
								-importe
						END)
					FROM
						estado_cuenta
					WHERE
						fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY' AND NOW()::DATE
				), 0)
			FROM
				saldos
		), 0) AS saldo_fin,
		COALESCE((
			SELECT
				SUM(total)
			FROM
				historico_proveedores
			WHERE
				fecha_arc = ('01' || '-' || bal.mes || '-' || bal.anio)::DATE - INTERVAL '1 DAY'
		), 0) AS saldo_pro_ini,
		COALESCE((
			SELECT
				SUM(total)
			FROM
				historico_proveedores
			WHERE
				fecha_arc = ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
		), 0) AS saldo_pro_fin,
		COALESCE((
			SELECT
				SUM(importe) AS importe
			FROM
				movimiento_gastos g
				LEFT JOIN catalogo_gastos cg USING (codgastos)
			WHERE
				fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND codigo_edo_resultados = 0
				AND codgastos NOT IN (33, 134)
		), 0) AS no_inc,
		SUM(inv_act) - SUM(inv_ant) AS dif_inventario,
		SUM(ABS(reserva_aguinaldos)) AS dif_reservas,
		-1 * SUM(ABS(pagos_anticipados)) AS pagos_anticipados_negativo,
		COALESCE(-1 * ABS((
			SELECT
				SUM(importe * (EXTRACT(MONTHS FROM AGE(fecha_fin, ('01/' || mes || '/' || anio)::DATE)) + 1))
			FROM
				pagos_anticipados
			WHERE
				('01/' || mes || '/' || anio)::DATE BETWEEN fecha_ini AND fecha_fin
		)), 0) AS pagos_anticipados_acumulados
	FROM
		balances_pan bal
	WHERE
		{$condiciones_string}
	GROUP BY
		anio,
		mes
	ORDER BY
		mes";*/

	$sql = "SELECT
		10000 AS num_cia,
		'TOTALES GENERALES DE PANADERIAS' AS nombre,
		anio,
		mes AS titulo,
		SUM(venta_puerta) AS venta_puerta,
		SUM(bases) AS bases,
		SUM(barredura) AS barredura,
		SUM(pastillaje) AS pastillaje,
		SUM(abono_emp) AS abono_emp,
		SUM(otros) AS otros,
		SUM(total_otros) AS total_otros,
		SUM(abono_reparto) AS abono_reparto,
		SUM(errores) AS errores,
		SUM(ventas_netas) AS ventas_netas,
		NULL AS blank1,
		SUM(inv_ant) AS inv_ant,
		SUM(compras) AS compras,
		SUM(mercancias) AS mercancias,
		SUM(inv_act) AS inv_act,
		SUM(mat_prima_utilizada) AS mat_prima_utilizada,
		SUM(mano_obra) AS mano_obra,
		SUM(panaderos) AS panaderos,
		SUM(gastos_fab) AS gastos_fab,
		SUM(costo_produccion) AS costo_produccion,
		NULL AS blank2,
		SUM(utilidad_bruta) AS utilidad_bruta,
		NULL AS blank3,
		SUM(pan_comprado) AS pan_comprado,
		SUM(gastos_generales) AS gastos_generales,
		SUM(gastos_caja) AS gastos_caja,
		SUM(comisiones) AS comisiones,
		SUM(reserva_aguinaldos) AS reserva_aguinaldos,
		SUM(pagos_anticipados) AS pagos_anticipados,
		SUM(gastos_otras_cias) AS gastos_otras_cias,
		SUM(total_gastos) AS total_gastos,
		NULL AS blank4,
		SUM(ingresos_ext) AS ingresos_ext,
		NULL AS blank5,
		SUM(utilidad_neta) AS utilidad_neta,
		NULL AS blank6,
		NULL AS mp_vtas,
		NULL AS utilidad_pro,
		NULL AS mp_pro,
		NULL AS gas_pro,
		NULL AS blank7,
		SUM(produccion_total) AS produccion_total,
		SUM(ganancia) AS ganancia,
		NULL AS porc_ganancia,
		SUM(faltante_pan) AS faltante_pan,
		SUM(devoluciones) AS devoluciones,
		SUM(rezago_ini) AS rezago_ini,
		SUM(rezago_fin) AS rezago_fin,
		NULL AS var_rezago,
		SUM(efectivo) AS efectivo,
		SUM(ingresos) AS ingresos,
		SUM(egresos) AS egresos,
		SUM(total_gastos_caja) AS total_gastos_caja,
		SUM(depositos) AS depositos,
		SUM(otros_depositos) AS otros_depositos,
		NULL AS blank9,
		SUM(saldo_ini) AS saldo_ini,
		SUM(saldo_fin) AS saldo_fin,
		SUM(saldo_pro_ini) AS saldo_pro_ini,
		SUM(saldo_pro_fin) AS saldo_pro_fin,
		SUM(no_inc) AS no_inc,
		SUM(dif_inventario) AS dif_inventario,
		SUM(dif_inventario) AS dif_reservas,
		SUM(pagos_anticipados_acumulados) AS pagos_anticipados_acumulados
	FROM
		(
			SELECT
				num_cia,
				nombre,
				anio,
				mes,
				venta_puerta,
				bases,
				barredura,
				pastillaje,
				abono_emp,
				otros,
				total_otros,
				abono_reparto,
				errores,
				ventas_netas,
				inv_ant,
				compras,
				mercancias,
				inv_act,
				mat_prima_utilizada,
				mano_obra,
				panaderos,
				gastos_fab,
				costo_produccion,
				utilidad_bruta,
				pan_comprado,
				gastos_generales,
				gastos_caja,
				comisiones,
				reserva_aguinaldos,
				pagos_anticipados,
				gastos_otras_cias,
				total_gastos,
				ingresos_ext,
				utilidad_neta + COALESCE((
					SELECT
						ROUND(SUM(importe * (CASE WHEN ccec.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
					FROM
						estado_cuenta
						LEFT JOIN catalogo_companias ccec
							USING (num_cia)
					WHERE
						((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2))
						AND fecha BETWEEN ('01/' || bal.mes || '/' || bal.anio)::DATE AND ('01/' || bal.mes || '/' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND cod_mov IN (1, 16)
				), 0) AS utilidad_neta,
				mp_vtas,
				utilidad_pro,
				mp_pro,
				gas_pro,
				produccion_total,
				ganancia,
				porc_ganancia,
				faltante_pan,
				devoluciones,
				rezago_ini,
				rezago_fin,
				var_rezago,
				efectivo,
				COALESCE((
					SELECT
						SUM(importe)
					FROM
						gastos_caja
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND tipo_mov = TRUE
				), 0) AS ingresos,
				COALESCE((
					SELECT
						SUM(importe)
					FROM
						gastos_caja
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND tipo_mov = FALSE
				), 0) AS egresos,
				COALESCE((
					SELECT
						SUM(CASE
							WHEN tipo_mov = TRUE THEN
								importe
							ELSE
								-importe
						END)
					FROM
						gastos_caja
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				), 0) AS total_gastos_caja,
				COALESCE((
					SELECT
						SUM(importe)
					FROM
						estado_cuenta
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND cod_mov = 1
				), 0) AS depositos,
				COALESCE((
					SELECT
						SUM(importe)
					FROM
						otros_depositos
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				), 0) AS otros_depositos,
				COALESCE((
					SELECT
						SUM(saldo_libros) + COALESCE((
							SELECT
								SUM(CASE
									WHEN tipo_mov = TRUE THEN
										importe
									ELSE
										-importe
								END)
							FROM
								estado_cuenta
							WHERE
								num_cia = bal.num_cia
								AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE - INTERVAL '1 DAY' AND NOW()::DATE
						), 0)
					FROM
						saldos
					WHERE
						num_cia = bal.num_cia
				), 0) AS saldo_ini,
				COALESCE((
					SELECT
						SUM(saldo_libros) + COALESCE((
							SELECT
								SUM(CASE
									WHEN tipo_mov = TRUE THEN
										importe
									ELSE
										-importe
								END)
							FROM
								estado_cuenta
							WHERE
								num_cia = bal.num_cia
								AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY' AND NOW()::DATE
						), 0)
					FROM
						saldos
					WHERE
						num_cia = bal.num_cia
				), 0) AS saldo_fin,
				COALESCE((
					SELECT
						SUM(total)
					FROM
						historico_proveedores
					WHERE
						num_cia = bal.num_cia
						AND fecha_arc = ('01' || '-' || bal.mes || '-' || bal.anio)::DATE - INTERVAL '1 DAY'
				), 0) AS saldo_pro_ini,
				COALESCE((
					SELECT
						SUM(total)
					FROM
						historico_proveedores
					WHERE
						num_cia = bal.num_cia
						AND fecha_arc = ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				), 0) AS saldo_pro_fin,
				COALESCE((
					SELECT
						SUM(importe) AS importe
					FROM
						movimiento_gastos g
						LEFT JOIN catalogo_gastos cg USING (codgastos)
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND codigo_edo_resultados = 0
						AND codgastos NOT IN (33, 134)
				), 0) AS no_inc,
				inv_act - inv_ant AS dif_inventario,
				ABS(reserva_aguinaldos) AS dif_reservas,
				-1 * ABS(pagos_anticipados) AS pagos_anticipados_negativo,
				COALESCE(-1 * ABS((
					SELECT
						SUM(importe * (EXTRACT(MONTHS FROM AGE(fecha_fin, ('01/' || mes || '/' || anio)::DATE)) + 1))
					FROM
						pagos_anticipados
					WHERE
						num_cia = bal.num_cia
						AND ('01/' || mes || '/' || anio)::DATE BETWEEN fecha_ini AND fecha_fin
				)), 0) AS pagos_anticipados_acumulados
			FROM
				balances_pan bal
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				{$condiciones_string}
		) AS result
	GROUP BY
		anio,
		mes
	ORDER BY
		mes";
}
else
{
	$sql = "SELECT
		num_cia,
		nombre,
		anio,
		mes AS titulo,
		venta_puerta,
		bases,
		barredura,
		pastillaje,
		abono_emp,
		otros,
		total_otros,
		abono_reparto,
		errores,
		ventas_netas,
		NULL AS blank1,
		inv_ant,
		compras,
		mercancias,
		inv_act,
		mat_prima_utilizada,
		mano_obra,
		panaderos,
		gastos_fab,
		costo_produccion,
		NULL AS blank2,
		utilidad_bruta,
		NULL AS blank3,
		pan_comprado,
		gastos_generales,
		gastos_caja,
		comisiones,
		reserva_aguinaldos,
		pagos_anticipados,
		gastos_otras_cias,
		total_gastos,
		NULL AS blank4,
		ingresos_ext,
		NULL AS blank5,
		utilidad_neta + COALESCE((
			SELECT
				ROUND(SUM(importe * (CASE WHEN ccec.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
			FROM
				estado_cuenta
				LEFT JOIN catalogo_companias ccec
					USING (num_cia)
			WHERE
				((num_cia IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2) AND num_cia_sec IS NULL) OR num_cia_sec IN (SELECT num_cia FROM catalogo_companias WHERE num_cia_primaria = bal.num_cia AND tipo_cia = 2))
				AND fecha BETWEEN ('01/' || bal.mes || '/' || bal.anio)::DATE AND ('01/' || bal.mes || '/' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND cod_mov IN (1, 16)
		), 0) AS utilidad_neta,
		NULL AS blank6,
		mp_vtas,
		utilidad_pro,
		mp_pro,
		gas_pro,
		NULL AS blank7,
		produccion_total,
		ganancia,
		porc_ganancia,
		faltante_pan,
		devoluciones,
		rezago_ini,
		rezago_fin,
		var_rezago,
		efectivo,
		NULL AS blank8,
		COALESCE((
			SELECT
				SUM(importe)
			FROM
				gastos_caja
			WHERE
				num_cia = bal.num_cia
				AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND tipo_mov = TRUE
		), 0) AS ingresos,
		COALESCE((
			SELECT
				SUM(importe)
			FROM
				gastos_caja
			WHERE
				num_cia = bal.num_cia
				AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND tipo_mov = FALSE
		), 0) AS egresos,
		COALESCE((
			SELECT
				SUM(CASE
					WHEN tipo_mov = TRUE THEN
						importe
					ELSE
						-importe
				END)
			FROM
				gastos_caja
			WHERE
				num_cia = bal.num_cia
				AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
		), 0) AS total_gastos_caja,
		COALESCE((
			SELECT
				SUM(importe)
			FROM
				estado_cuenta
			WHERE
				num_cia = bal.num_cia
				AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND cod_mov = 1
		), 0) AS depositos,
		COALESCE((
			SELECT
				SUM(importe)
			FROM
				otros_depositos
			WHERE
				num_cia = bal.num_cia
				AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
		), 0) AS otros_depositos,
		NULL AS blank9,
		COALESCE((
			SELECT
				SUM(saldo_libros) + COALESCE((
					SELECT
						SUM(CASE
							WHEN tipo_mov = TRUE THEN
								importe
							ELSE
								-importe
						END)
					FROM
						estado_cuenta
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE - INTERVAL '1 DAY' AND NOW()::DATE
				), 0)
			FROM
				saldos
			WHERE
				num_cia = bal.num_cia
		), 0) AS saldo_ini,
		COALESCE((
			SELECT
				SUM(saldo_libros) + COALESCE((
					SELECT
						SUM(CASE
							WHEN tipo_mov = TRUE THEN
								importe
							ELSE
								-importe
						END)
					FROM
						estado_cuenta
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY' AND NOW()::DATE
				), 0)
			FROM
				saldos
			WHERE
				num_cia = bal.num_cia
		), 0) AS saldo_fin,
		COALESCE((
			SELECT
				SUM(total)
			FROM
				historico_proveedores
			WHERE
				num_cia = bal.num_cia
				AND fecha_arc = ('01' || '-' || bal.mes || '-' || bal.anio)::DATE - INTERVAL '1 DAY'
		), 0) AS saldo_pro_ini,
		COALESCE((
			SELECT
				SUM(total)
			FROM
				historico_proveedores
			WHERE
				num_cia = bal.num_cia
				AND fecha_arc = ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
		), 0) AS saldo_pro_fin,
		COALESCE((
			SELECT
				SUM(importe) AS importe
			FROM
				movimiento_gastos g
				LEFT JOIN catalogo_gastos cg USING (codgastos)
			WHERE
				num_cia = bal.num_cia
				AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND codigo_edo_resultados = 0
				AND codgastos NOT IN (33, 134)
		), 0) AS no_inc,
		inv_act - inv_ant AS dif_inventario,
		ABS(reserva_aguinaldos) AS dif_reservas,
		-1 * ABS(pagos_anticipados) AS pagos_anticipados_negativo,
		COALESCE(-1 * ABS((
			SELECT
				SUM(importe * (EXTRACT(MONTHS FROM AGE(fecha_fin, ('01/' || mes || '/' || anio)::DATE)) + 1))
			FROM
				pagos_anticipados
			WHERE
				num_cia = bal.num_cia
				AND ('01/' || mes || '/' || anio)::DATE BETWEEN fecha_ini AND fecha_fin
		)), 0) AS pagos_anticipados_acumulados
	FROM
		balances_pan bal
		LEFT JOIN catalogo_companias cc USING (num_cia)
	WHERE
		{$condiciones_string}
	ORDER BY
		num_cia,
		mes";
}

$panaderias = $db->query($sql);

if (isset($_REQUEST['agrupar_totales'])) {
	/*$sql = "SELECT
		10000 AS num_cia,
		'TOTALES GENERALES DE ROSTICERIAS' AS nombre,
		anio,
		mes AS titulo,
		SUM(venta) AS venta,
		SUM(otros) AS otros,
		SUM(ventas_netas) AS ventas_netas,
		NULL AS blank1,
		SUM(inv_ant) AS inv_ant,
		SUM(compras) AS compras,
		SUM(mercancias) AS mercancias,
		SUM(inv_act) AS inv_act,
		SUM(mat_prima_utilizada) AS mat_prima_utilizada,
		SUM(gastos_fab) AS gastos_fab,
		SUM(costo_produccion) AS costo_produccion,
		NULL AS blank2,
		SUM(utilidad_bruta) AS utilidad_bruta,
		NULL AS blank3,
		SUM(gastos_generales) AS gastos_generales,
		SUM(gastos_caja) AS gastos_caja,
		SUM(comisiones) AS comisiones,
		SUM(reserva_aguinaldos) AS reserva_aguinaldos,
		SUM(gastos_otras_cias) AS gastos_otras_cias,
		SUM(total_gastos) AS total_gastos,
		NULL AS blank4,
		SUM(ingresos_ext) AS ingresos_ext,
		NULL AS blank5,
		SUM(utilidad_neta) AS utilidad_neta,
		NULL AS blank6,
		NULL AS mp_vtas,
		SUM(efectivo) AS efectivo,
		SUM(pollos_vendidos) AS pollos_vendidos,
		SUM(p_pavo) AS p_pavo,
		SUM(pescuezos) AS pescuezos,
		NULL AS blank7,
		COALESCE((
			SELECT
				SUM(importe)
			FROM
				gastos_caja
			WHERE
				fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND tipo_mov = TRUE
		), 0) AS ingresos,
		COALESCE((
			SELECT
				SUM(importe)
			FROM
				gastos_caja
			WHERE
				fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND tipo_mov = FALSE
		), 0) AS egresos,
		COALESCE((
			SELECT
				SUM(CASE
					WHEN tipo_mov = TRUE THEN
						importe
					ELSE
						-importe
				END)
			FROM
				gastos_caja
			WHERE
				fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
		), 0) AS total_gastos_caja,
		COALESCE((
			SELECT
				SUM(importe)
			FROM
				estado_cuenta
			WHERE
				fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND cod_mov = 1
		), 0) AS depositos,
		COALESCE((
			SELECT
				SUM(importe)
			FROM
				otros_depositos
			WHERE
				fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
		), 0) AS otros_depositos,
		NULL AS blank8,
		COALESCE((
			SELECT
				SUM(saldo_libros) + COALESCE((
					SELECT
						SUM(CASE
							WHEN tipo_mov = TRUE THEN
								importe
							ELSE
								-importe
						END)
					FROM
						estado_cuenta
					WHERE
						fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE - INTERVAL '1 DAY' AND NOW()::DATE
				), 0)
			FROM
				saldos
		), 0) AS saldo_ini,
		COALESCE((
			SELECT
				SUM(saldo_libros) + COALESCE((
					SELECT
						SUM(CASE
							WHEN tipo_mov = TRUE THEN
								importe
							ELSE
								-importe
						END)
					FROM
						estado_cuenta
					WHERE
						fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY' AND NOW()::DATE
				), 0)
			FROM
				saldos
		), 0) AS saldo_fin,
		COALESCE((
			SELECT
				SUM(total)
			FROM
				historico_proveedores
			WHERE
				fecha_arc = ('01' || '-' || bal.mes || '-' || bal.anio)::DATE - INTERVAL '1 DAY'
		), 0) AS saldo_pro_ini,
		COALESCE((
			SELECT
				SUM(total)
			FROM
				historico_proveedores
			WHERE
				fecha_arc = ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
		), 0) AS saldo_pro_fin,
		COALESCE((
			SELECT
				SUM(importe) AS importe
			FROM
				movimiento_gastos g
				LEFT JOIN catalogo_gastos cg USING (codgastos)
			WHERE
				fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND codigo_edo_resultados = 0
				AND codgastos NOT IN (33, 134)
		), 0) AS no_inc,
		SUM(inv_act) - SUM(inv_ant) AS dif_inventario,
		SUM(ABS(reserva_aguinaldos)) AS dif_reservas
		--0 AS pagos_anticipados_negativo,
		--0 AS pagos_anticipados_acumulados
	FROM
		balances_ros bal
	WHERE
		{$condiciones_string}
	GROUP BY
		anio,
		mes
	ORDER BY
		mes";*/

	$sql = "SELECT
		10000 AS num_cia,
		'TOTALES GENERALES DE ROSTICERIAS' AS nombre,
		anio,
		mes AS titulo,
		SUM(venta) AS venta,
		SUM(otros) AS otros,
		SUM(ventas_netas) AS ventas_netas,
		NULL AS blank1,
		SUM(inv_ant) AS inv_ant,
		SUM(compras) AS compras,
		SUM(mercancias) AS mercancias,
		SUM(inv_act) AS inv_act,
		SUM(mat_prima_utilizada) AS mat_prima_utilizada,
		SUM(gastos_fab) AS gastos_fab,
		SUM(costo_produccion) AS costo_produccion,
		NULL AS blank2,
		SUM(utilidad_bruta) AS utilidad_bruta,
		NULL AS blank3,
		SUM(gastos_generales) AS gastos_generales,
		SUM(gastos_caja) AS gastos_caja,
		SUM(comisiones) AS comisiones,
		SUM(reserva_aguinaldos) AS reserva_aguinaldos,
		SUM(gastos_otras_cias) AS gastos_otras_cias,
		SUM(total_gastos) AS total_gastos,
		NULL AS blank4,
		SUM(ingresos_ext) AS ingresos_ext,
		NULL AS blank5,
		SUM(iva) AS iva,
		SUM(utilidad_neta) AS utilidad_neta,
		NULL AS blank6,
		NULL AS mp_vtas,
		SUM(efectivo) AS efectivo,
		SUM(pollos_vendidos) AS pollos_vendidos,
		SUM(p_pavo) AS p_pavo,
		SUM(pescuezos) AS pescuezos,
		NULL AS blank7,
		SUM(ingresos) AS ingresos,
		SUM(egresos) AS egresos,
		SUM(total_gastos_caja) AS total_gastos_caja,
		SUM(depositos) AS depositos,
		SUM(otros_depositos) AS otros_depositos,
		NULL AS blank8,
		SUM(saldo_ini) AS saldo_ini,
		SUM(saldo_fin) AS saldo_fin,
		SUM(saldo_pro_ini) AS saldo_pro_ini,
		SUM(saldo_pro_fin) AS saldo_pro_fin,
		SUM(no_inc) AS no_inc,
		SUM(dif_inventario) AS dif_inventario,
		SUM(dif_reservas) AS dif_reservas,
		AVG(precio_kilo) AS precio_kilo
	FROM
		(
			SELECT
				num_cia,
				nombre,
				anio,
				mes,
				venta,
				otros,
				ventas_netas,
				inv_ant,
				compras,
				mercancias,
				inv_act,
				mat_prima_utilizada,
				gastos_fab,
				costo_produccion,
				utilidad_bruta,
				gastos_generales,
				gastos_caja,
				comisiones,
				reserva_aguinaldos,
				gastos_otras_cias,
				total_gastos,
				ingresos_ext,
				COALESCE((
					SELECT
						ROUND(SUM(importe * (CASE WHEN cc.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2) * -1
					FROM
						estado_cuenta
					WHERE
						((num_cia = bal.num_cia AND num_cia_sec IS NULL) OR num_cia_sec = bal.num_cia)
						AND fecha BETWEEN ('01/' || bal.mes || '/' || bal.anio)::DATE AND ('01/' || bal.mes || '/' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND cod_mov IN (1, 16)
				), 0) AS iva,
				utilidad_neta - COALESCE((
					SELECT
						ROUND(SUM(importe * (CASE WHEN cc.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
					FROM
						estado_cuenta
					WHERE
						((num_cia = bal.num_cia AND num_cia_sec IS NULL) OR num_cia_sec = bal.num_cia)
						AND fecha BETWEEN ('01/' || bal.mes || '/' || bal.anio)::DATE AND ('01/' || bal.mes || '/' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND cod_mov IN (1, 16)
				), 0) AS utilidad_neta,
				mp_vtas,
				efectivo,
				pollos_vendidos,
				p_pavo,
				pescuezos,
				COALESCE((
					SELECT
						SUM(importe)
					FROM
						gastos_caja
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND tipo_mov = TRUE
				), 0) AS ingresos,
				COALESCE((
					SELECT
						SUM(importe)
					FROM
						gastos_caja
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND tipo_mov = FALSE
				), 0) AS egresos,
				COALESCE((
					SELECT
						SUM(CASE
							WHEN tipo_mov = TRUE THEN
								importe
							ELSE
								-importe
						END)
					FROM
						gastos_caja
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				), 0) AS total_gastos_caja,
				COALESCE((
					SELECT
						SUM(importe)
					FROM
						estado_cuenta
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND cod_mov = 1
				), 0) AS depositos,
				COALESCE((
					SELECT
						SUM(importe)
					FROM
						otros_depositos
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				), 0) AS otros_depositos,
				COALESCE((
					SELECT
						SUM(saldo_libros) + COALESCE((
							SELECT
								SUM(CASE
									WHEN tipo_mov = TRUE THEN
										importe
									ELSE
										-importe
								END)
							FROM
								estado_cuenta
							WHERE
								num_cia = bal.num_cia
								AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE - INTERVAL '1 DAY' AND NOW()::DATE
						), 0)
					FROM
						saldos
					WHERE
						num_cia = bal.num_cia
				), 0) AS saldo_ini,
				COALESCE((
					SELECT
						SUM(saldo_libros) + COALESCE((
							SELECT
								SUM(CASE
									WHEN tipo_mov = TRUE THEN
										importe
									ELSE
										-importe
								END)
							FROM
								estado_cuenta
							WHERE
								num_cia = bal.num_cia
								AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY' AND NOW()::DATE
						), 0)
					FROM
						saldos
					WHERE
						num_cia = bal.num_cia
				), 0) AS saldo_fin,
				COALESCE((
					SELECT
						SUM(total)
					FROM
						historico_proveedores
					WHERE
						num_cia = bal.num_cia
						AND fecha_arc = ('01' || '-' || bal.mes || '-' || bal.anio)::DATE - INTERVAL '1 DAY'
				), 0) AS saldo_pro_ini,
				COALESCE((
					SELECT
						SUM(total)
					FROM
						historico_proveedores
					WHERE
						num_cia = bal.num_cia
						AND fecha_arc = ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				), 0) AS saldo_pro_fin,
				COALESCE((
					SELECT
						SUM(importe) AS importe
					FROM
						movimiento_gastos g
						LEFT JOIN catalogo_gastos cg USING (codgastos)
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
						AND codigo_edo_resultados = 0
						AND codgastos NOT IN (33, 134)
				), 0) AS no_inc,
				inv_act - inv_ant AS dif_inventario,
				ABS(reserva_aguinaldos) AS dif_reservas,
				COALESCE((SELECT precio_pollo FROM historico WHERE num_cia = bal.num_cia AND mes = bal.mes AND anio = bal.anio), 0) AS precio_kilo
			FROM
				balances_ros bal
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				{$condiciones_string}
		) AS result
	GROUP BY
		anio,
		mes
	ORDER BY
		mes";
}
else {
	$sql = "SELECT
		num_cia,
		nombre,
		anio,
		mes AS titulo,
		venta,
		otros,
		ventas_netas,
		NULL AS blank1,
		inv_ant,
		compras,
		mercancias,
		inv_act,
		mat_prima_utilizada,
		gastos_fab,
		costo_produccion,
		NULL AS blank2,
		utilidad_bruta,
		NULL AS blank3,
		gastos_generales,
		gastos_caja,
		comisiones,
		reserva_aguinaldos,
		gastos_otras_cias,
		total_gastos,
		NULL AS blank4,
		ingresos_ext,
		NULL AS blank5,
		COALESCE((
			SELECT
				ROUND(SUM(importe * (CASE WHEN cc.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2) * -1
			FROM
				estado_cuenta
			WHERE
				((num_cia = bal.num_cia AND num_cia_sec IS NULL) OR num_cia_sec = bal.num_cia)
				AND fecha BETWEEN ('01/' || bal.mes || '/' || bal.anio)::DATE AND ('01/' || bal.mes || '/' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND cod_mov IN (1, 16)
		), 0) AS iva,
		utilidad_neta - COALESCE((
			SELECT
				ROUND(SUM(importe * (CASE WHEN cc.persona_fis_moral = FALSE THEN 0.16 ELSE 0 END))::NUMERIC, 2)
			FROM
				estado_cuenta
			WHERE
				((num_cia = bal.num_cia AND num_cia_sec IS NULL) OR num_cia_sec = bal.num_cia)
				AND fecha BETWEEN ('01/' || bal.mes || '/' || bal.anio)::DATE AND ('01/' || bal.mes || '/' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND cod_mov IN (1, 16)
		), 0) AS utilidad_neta,
		NULL AS blank6,
		mp_vtas,
		efectivo,
		pollos_vendidos,
		p_pavo,
		pescuezos,
		NULL AS blank7,
		COALESCE((
			SELECT
				SUM(importe)
			FROM
				gastos_caja
			WHERE
				num_cia = bal.num_cia
				AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND tipo_mov = TRUE
		), 0) AS ingresos,
		COALESCE((
			SELECT
				SUM(importe)
			FROM
				gastos_caja
			WHERE
				num_cia = bal.num_cia
				AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND tipo_mov = FALSE
		), 0) AS egresos,
		COALESCE((
			SELECT
				SUM(CASE
					WHEN tipo_mov = TRUE THEN
						importe
					ELSE
						-importe
				END)
			FROM
				gastos_caja
			WHERE
				num_cia = bal.num_cia
				AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
		), 0) AS total_gastos_caja,
		COALESCE((
			SELECT
				SUM(importe)
			FROM
				estado_cuenta
			WHERE
				num_cia = bal.num_cia
				AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND cod_mov = 1
		), 0) AS depositos,
		COALESCE((
			SELECT
				SUM(importe)
			FROM
				otros_depositos
			WHERE
				num_cia = bal.num_cia
				AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
		), 0) AS otros_depositos,
		NULL AS blank8,
		COALESCE((
			SELECT
				SUM(saldo_libros) + COALESCE((
					SELECT
						SUM(CASE
							WHEN tipo_mov = TRUE THEN
								importe
							ELSE
								-importe
						END)
					FROM
						estado_cuenta
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE - INTERVAL '1 DAY' AND NOW()::DATE
				), 0)
			FROM
				saldos
			WHERE
				num_cia = bal.num_cia
		), 0) AS saldo_ini,
		COALESCE((
			SELECT
				SUM(saldo_libros) + COALESCE((
					SELECT
						SUM(CASE
							WHEN tipo_mov = TRUE THEN
								importe
							ELSE
								-importe
						END)
					FROM
						estado_cuenta
					WHERE
						num_cia = bal.num_cia
						AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY' AND NOW()::DATE
				), 0)
			FROM
				saldos
			WHERE
				num_cia = bal.num_cia
		), 0) AS saldo_fin,
		COALESCE((
			SELECT
				SUM(total)
			FROM
				historico_proveedores
			WHERE
				num_cia = bal.num_cia
				AND fecha_arc = ('01' || '-' || bal.mes || '-' || bal.anio)::DATE - INTERVAL '1 DAY'
		), 0) AS saldo_pro_ini,
		COALESCE((
			SELECT
				SUM(total)
			FROM
				historico_proveedores
			WHERE
				num_cia = bal.num_cia
				AND fecha_arc = ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
		), 0) AS saldo_pro_fin,
		COALESCE((
			SELECT
				SUM(importe) AS importe
			FROM
				movimiento_gastos g
				LEFT JOIN catalogo_gastos cg USING (codgastos)
			WHERE
				num_cia = bal.num_cia
				AND fecha BETWEEN ('01' || '-' || bal.mes || '-' || bal.anio)::DATE AND ('01' || '-' || bal.mes || '-' || bal.anio)::DATE + INTERVAL '1 MONTH' - INTERVAL '1 DAY'
				AND codigo_edo_resultados = 0
				AND codgastos NOT IN (33, 134)
		), 0) AS no_inc,
		inv_act - inv_ant AS dif_inventario,
		ABS(reserva_aguinaldos) AS dif_reservas,
		COALESCE((SELECT precio_pollo FROM historico WHERE num_cia = bal.num_cia AND mes = bal.mes AND anio = bal.anio), 0) AS precio_kilo
		--0 AS pagos_anticipados_negativo,
		--0 AS pagos_anticipados_acumulados
	FROM
		balances_ros bal
		LEFT JOIN catalogo_companias cc USING (num_cia)
	WHERE
		{$condiciones_string}
	ORDER BY
		num_cia,
		mes";
}

$rosticerias = $db->query($sql);

if ( ! $panaderias && ! $rosticerias)
	die;

$not = array('num_cia', 'nombre', 'anio', 'bloque');
$esp = array('titulo');
$dec3 = array('mp_vtas', 'utilidad_pro', 'mp_pro');
$dec5 = array('gas_pro');

$tpl = new TemplatePower('plantillas/bal/historico_bal_v2.tpl');
$tpl->prepare();

if ($panaderias) {
	$num_cia = NULL;
	foreach ($panaderias as $reg) {
		if ($num_cia != $reg['num_cia']) {
			if ($num_cia != NULL) {
				if ($columnas == 7) {
					$tpl->newBlock('hoja_pan');
					$tpl->assign('num_cia', $num_cia < 10000 ? $num_cia : '');
					$tpl->assign('nombre', $reg['nombre']);
					$tpl->assign('anio', $_GET['anio']);
					$tpl->assign('salto', '<br style="page-break-after:always;" />');

					$columnas = 0;
				}

				// Crear columna de totales
				$tpl->newBlock('titulo_pan');
				$tpl->assign('titulo_pan', 'TOTALES');
				foreach ($totales as $k => $v) {
					$tpl->newBlock($k . '_pan');

					if (in_array($k . '_pan', $dec3))
						$tpl->assign($k . '_pan', $v != 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v, 3) . '</span>' : '&nbsp;');
					else if (in_array($k . '_pan', $dec5))
						$tpl->assign($k . '_pan', $v != 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v, 5) . '</span>' : '&nbsp;');
					else
						$tpl->assign($k . '_pan', $v != 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v, 2, '.', ',') . '</span>' : '&nbsp;');
				}

				$columnas++;

				if ($columnas == 7) {
					$tpl->newBlock('hoja_pan');
					$tpl->assign('num_cia', $num_cia < 10000 ? $num_cia : '');
					$tpl->assign('nombre', $reg['nombre']);
					$tpl->assign('anio', $_GET['anio']);
					$tpl->assign('salto', '<br style="page-break-after:always;" />');

					$columnas = 0;
				}

				// Crear columna de promedios
				$tpl->newBlock('titulo_pan');
				$tpl->assign('titulo_pan', 'PROMEDIOS');
				foreach ($totales as $k => $v) {
					$tpl->newBlock($k . '_pan');

					if (in_array($k . '_pan', $dec3))
						$tpl->assign($k . '_pan', $v != 0 && $meses > 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v / $meses, 3) . '</span>' : '&nbsp;');
					else if (in_array($k . '_pan', $dec5))
						$tpl->assign($k . '_pan', $v != 0 && $meses > 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v / $meses, 5) . '</span>' : '&nbsp;');
					else
						$tpl->assign($k . '_pan', $v != 0 && $meses > 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v / $meses, 2, '.', ',') . '</span>' : '&nbsp;');
				}
			}

			$num_cia = $reg['num_cia'];

			$tpl->newBlock('hoja_pan');
			$tpl->assign('num_cia', $num_cia < 10000 ? $num_cia : '');
			$tpl->assign('nombre', $reg['nombre']);
			$tpl->assign('anio', $_GET['anio']);
			$tpl->assign('salto', '<br style="page-break-after:always;" />');

			// Crear arreglo de totales
			$totales = array();
			foreach ($reg as $k => $v)
				if (!in_array($k, $not) && $k != 'titulo')
					$totales[$k] = 0;
			$totales['general'] = 0;
			$totales['diferencia'] = 0;

			// Contador de meses
			$meses = 0;

			// Columnas
			$columnas = 0;
		}

		if ($columnas == 7) {
			$tpl->newBlock('hoja_pan');
			$tpl->assign('num_cia', $num_cia < 10000 ? $num_cia : '');
			$tpl->assign('nombre', $reg['nombre']);
			$tpl->assign('anio', $_GET['anio']);
			$tpl->assign('salto', '<br style="page-break-after:always;" />');

			$columnas = 0;
		}

		$reg['general'] = $reg['otros_depositos'] + $reg['total_gastos_caja'];
		$reg['diferencia'] = $reg['general'] - $reg['utilidad_neta'];

		// Datos generales de balance
		foreach ($reg as $k => $v) {
			if (in_array($k, $not))
				continue;

			$tpl->newBlock($k . '_pan');

			if (in_array($k, $esp)) {
				$tpl->assign('titulo_pan', substr(mes_escrito($v, TRUE), 0, 3));
				$meses++;
				continue;
			}
			else if (in_array($k, $dec3))
				$tpl->assign($k . '_pan', $v != 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v, 3) . '</span>' : '&nbsp;');
			else if (in_array($k, $dec5))
				$tpl->assign($k . '_pan', $v != 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v, 5) . '</span>' : '&nbsp;');
			else
				$tpl->assign($k . '_pan', $v != 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v, 2, '.', ',') . '</span>' : '&nbsp;');

			$totales[$k] += $v;
		}

		$columnas++;

		// Datos extra

	}
	if ($num_cia != NULL) {
		if ($columnas == 7) {
			$tpl->newBlock('hoja_pan');
			$tpl->assign('num_cia', $num_cia < 10000 ? $num_cia : '');
			$tpl->assign('nombre', $reg['nombre']);
			$tpl->assign('anio', $_GET['anio']);
			$tpl->assign('salto', '<br style="page-break-after:always;" />');

			$columnas = 0;
		}

		// Crear columna de totales
		$tpl->newBlock('titulo_pan');
		$tpl->assign('titulo_pan', 'TOTALES');
		foreach ($totales as $k => $v) {
			$tpl->newBlock($k . '_pan');

			if (in_array($k, $dec3))
				$tpl->assign($k . '_pan', $v != 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v, 3) . '</span>' : '&nbsp;');
			else if (in_array($k, $dec5))
				$tpl->assign($k . '_pan', $v != 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v, 5) . '</span>' : '&nbsp;');
			else
				$tpl->assign($k . '_pan', $v != 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v, 2, '.', ',') . '</span>' : '&nbsp;');
		}

		$columnas++;

		if ($columnas == 7) {
			$tpl->newBlock('hoja_pan');
			$tpl->assign('num_cia', $num_cia < 10000 ? $num_cia : '');
			$tpl->assign('nombre', $reg['nombre']);
			$tpl->assign('anio', $_GET['anio']);
			$tpl->assign('salto', '<br style="page-break-after:always;" />');

			$columnas = 0;
		}

		// Crear columna de promedios
		$tpl->newBlock('titulo_pan');
		$tpl->assign('titulo_pan', 'PROMEDIOS');
		foreach ($totales as $k => $v) {
			$tpl->newBlock($k . '_pan');

			if (in_array($k, $dec3))
				$tpl->assign($k . '_pan', $v != 0 && $meses > 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v / $meses, 3) . '</span>' : '&nbsp;');
			else if (in_array($k, $dec5))
				$tpl->assign($k . '_pan', $v != 0 && $meses > 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v / $meses, 5) . '</span>' : '&nbsp;');
			else
				$tpl->assign($k . '_pan', $v != 0 && $meses > 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v / $meses, 2, '.', ',') . '</span>' : '&nbsp;');
		}
	}
}

if ($rosticerias) {
	$num_cia = NULL;
	foreach ($rosticerias as $reg) {
		if ($num_cia != $reg['num_cia']) {
			if ($num_cia != NULL) {
				if ($columnas == 7) {
					$tpl->newBlock('hoja_ros');
					$tpl->assign('num_cia', $num_cia < 10000 ? $num_cia : '');
					$tpl->assign('nombre', $reg['nombre']);
					$tpl->assign('anio', $_GET['anio']);
					$tpl->assign('salto', '<br style="page-break-after:always;" />');

					$columnas = 0;
				}

				// Crear columna de totales
				$tpl->newBlock('titulo_ros');
				$tpl->assign('titulo_ros', 'TOTALES');
				foreach ($totales as $k => $v) {
					$tpl->newBlock($k . '_ros');

					if (in_array($k . '_ros', $dec3))
						$tpl->assign($k . '_ros', $v != 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v, 3) . '</span>' : '&nbsp;');
					else if (in_array($k . '_ros', $dec5))
						$tpl->assign($k . '_ros', $v != 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v, 5) . '</span>' : '&nbsp;');
					else
						$tpl->assign($k . '_ros', $v != 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v, 2, '.', ',') . '</span>' : '&nbsp;');
				}

				$columnas++;

				if ($columnas == 7) {
					$tpl->newBlock('hoja_ros');
					$tpl->assign('num_cia', $num_cia < 10000 ? $num_cia : '');
					$tpl->assign('nombre', $reg['nombre']);
					$tpl->assign('anio', $_GET['anio']);
					$tpl->assign('salto', '<br style="page-break-after:always;" />');

					$columnas = 0;
				}

				// Crear columna de promedios
				$tpl->newBlock('titulo_ros');
				$tpl->assign('titulo_ros', 'PROMEDIOS');
				foreach ($totales as $k => $v) {
					$tpl->newBlock($k . '_ros');

					if (in_array($k . '_ros', $dec3))
						$tpl->assign($k . '_ros', $v != 0 && $meses > 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v / $meses, 3) . '</span>' : '&nbsp;');
					else if (in_array($k . '_ros', $dec5))
						$tpl->assign($k . '_ros', $v != 0 && $meses > 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v / $meses, 5) . '</span>' : '&nbsp;');
					else
						$tpl->assign($k . '_ros', $v != 0 && $meses > 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v / $meses, 2, '.', ',') . '</span>' : '&nbsp;');
				}
			}

			$num_cia = $reg['num_cia'];

			$tpl->newBlock('hoja_ros');
			$tpl->assign('num_cia', $num_cia < 10000 ? $num_cia : '');
			$tpl->assign('nombre', $reg['nombre']);
			$tpl->assign('anio', $_GET['anio']);
			$tpl->assign('salto', '<br style="page-break-after:always;" />');

			// Crear arreglo de totales
			$totales = array();
			foreach ($reg as $k => $v)
				if (!in_array($k, $not) && $k != 'titulo')
					$totales[$k] = 0;
			$totales['general'] = 0;
			$totales['diferencia'] = 0;

			// Contador de meses
			$meses = 0;

			// Columnas
			$columnas = 0;
		}

		if ($columnas == 7) {
			$tpl->newBlock('hoja_ros');
			$tpl->assign('num_cia', $num_cia < 10000 ? $num_cia : '');
			$tpl->assign('nombre', $reg['nombre']);
			$tpl->assign('anio', $_GET['anio']);
			$tpl->assign('salto', '<br style="page-break-after:always;" />');

			$columnas = 0;
		}

		$reg['general'] = $reg['otros_depositos'] + $reg['total_gastos_caja'];
		$reg['diferencia'] = $reg['general'] - $reg['utilidad_neta'];

		// Datos generales de balance
		foreach ($reg as $k => $v) {
			if (in_array($k, $not))
				continue;

			$tpl->newBlock($k . '_ros');

			if (in_array($k, $esp)) {
				$tpl->assign('titulo_ros', substr(mes_escrito($v, TRUE), 0, 3));
				$meses++;
				continue;
			}
			else if (in_array($k, $dec3))
				$tpl->assign($k . '_ros', $v != 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v, 3) . '</span>' : '&nbsp;');
			else if (in_array($k, $dec5))
				$tpl->assign($k . '_ros', $v != 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v, 5) . '</span>' : '&nbsp;');
			else
				$tpl->assign($k . '_ros', $v != 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v, 2, '.', ',') . '</span>' : '&nbsp;');

			$totales[$k] += $v;
		}

		$columnas++;

		// Datos extra

	}
	if ($num_cia != NULL) {
		if ($columnas == 7) {
			$tpl->newBlock('hoja_ros');
			$tpl->assign('num_cia', $num_cia < 10000 ? $num_cia : '');
			$tpl->assign('nombre', $reg['nombre']);
			$tpl->assign('anio', $_GET['anio']);
			$tpl->assign('salto', '<br style="page-break-after:always;" />');

			$columnas = 0;
		}

		// Crear columna de totales
		$tpl->newBlock('titulo_ros');
		$tpl->assign('titulo_ros', 'TOTALES');
		foreach ($totales as $k => $v) {
			$tpl->newBlock($k . '_ros');

			if (in_array($k, $dec3))
				$tpl->assign($k . '_ros', $v != 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v, 3) . '</span>' : '&nbsp;');
			else if (in_array($k, $dec5))
				$tpl->assign($k . '_ros', $v != 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v, 5) . '</span>' : '&nbsp;');
			else
				$tpl->assign($k . '_ros', $v != 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v, 2, '.', ',') . '</span>' : '&nbsp;');
		}

		$columnas++;

		if ($columnas == 7) {
			$tpl->newBlock('hoja_ros');
			$tpl->assign('num_cia', $num_cia < 10000 ? $num_cia : '');
			$tpl->assign('nombre', $reg['nombre']);
			$tpl->assign('anio', $_GET['anio']);
			$tpl->assign('salto', '<br style="page-break-after:always;" />');

			$columnas = 0;
		}

		// Crear columna de promedios
		$tpl->newBlock('titulo_ros');
		$tpl->assign('titulo_ros', 'PROMEDIOS');
		foreach ($totales as $k => $v) {
			$tpl->newBlock($k . '_ros');

			if (in_array($k, $dec3))
				$tpl->assign($k . '_ros', $v != 0 && $meses > 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v / $meses, 3) . '</span>' : '&nbsp;');
			else if (in_array($k, $dec5))
				$tpl->assign($k . '_ros', $v != 0 && $meses > 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v / $meses, 5) . '</span>' : '&nbsp;');
			else
				$tpl->assign($k . '_ros', $v != 0 && $meses > 0 ? '<span style="color:#' . ($v > 0 ? '00C' : 'C00') . '">' . number_format($v / $meses, 2, '.', ',') . '</span>' : '&nbsp;');
		}
	}
}

$tpl->printToScreen();
?>
