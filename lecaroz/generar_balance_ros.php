<?php
include './includes/class.db.inc.php';
include './includes/dbstatus.php';
include './includes/class.session2.inc.php';
include './includes/class.auxinv.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

$sql = '
	SELECT
		nivel
	FROM
		balances_aut
	WHERE
		iduser = ' . $_SESSION['iduser'] . '
';
$nivel = $db->query($sql);

if (!$nivel || $nivel[0]['nivel'] == 0 || $nivel[0]['nivel'] == 1) {
	die('NO TIENEN AUTORIZACION PARA GENERAR BALANCES.');
}

if ($_SESSION['iduser'] != 1 && $_REQUEST['anyo'] < 2016)
{
	echo "NO ES POSIBLE GENERAR BALANCES.";
	die;
}

$anyo = $_REQUEST['anyo'];
$mes = $_REQUEST['mes'];

$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anyo));
$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo));
$fecha_his = date('d/m/Y', mktime(0, 0, 0, $mes, 0, $anyo));
$dias = date('j', mktime(0, 0, 0, $mes + 1, 0, $anyo));

$condiciones = array();
$condiciones[] = 'tipo_cia = 2';
$condiciones[] = 'num_cia IN (SELECT num_cia FROM total_companias WHERE fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\' GROUP BY num_cia)';

/*
@ Intervalo de compañías
*/
if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
	$cias = array();

	$pieces = explode(',', $_REQUEST['cias']);
	foreach ($pieces as $piece) {
		if (count($exp = explode('-', $piece)) > 1) {
			$cias[] = implode(', ', range($exp[0], $exp[1]));
		}
		else {
			$cias[] = $piece;
		}
	}

	if (count($cias) > 0) {
		$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
	}
}

if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
	$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
}

$sql = '
	SELECT
		num_cia
	FROM
		catalogo_companias
	WHERE
		' . implode(' AND ', $condiciones) . '
	ORDER BY
		num_cia
';
$cias = $db->query($sql);

if (!$cias) die('NO HAY RESULTADOS');

if ( ! $db->query("SELECT * FROM movimiento_gastos WHERE fecha BETWEEN '{$fecha1}' AND '{$fecha2}' AND codgastos IN (179, 180, 181, 187)")) {
	$error = 'No se pueden generar balances debido a que los contadores no han terminado de capturar los impuestos';

	echo $error;
	die;
}

$balance = '';
foreach ($cias as $c) {
	$balance .= '
		DELETE FROM
			balances_ros
		WHERE
				anio = ' . $anyo . '
			AND
				mes = ' . $mes . '
			AND
				num_cia = ' . $c['num_cia'] . '
	' . ";\n";

	$balance .= '
		DELETE FROM
			historico
		WHERE
				anio = ' . $anyo . '
			AND
				mes = ' . $mes . '
			AND
				num_cia = ' . $c['num_cia'] . '
	' . ";\n";

	$data = array(
		'num_cia' => $c['num_cia'],
		'anio' => $anyo,
		'mes' => $mes,
		'fecha' => '\'' . date('d/m/Y',  mktime(0, 0, 0, $mes, 1, $anyo)) . '\''
	);

	/*
	@
	@@ VENTAS DEL MES
	@
	*/

	/*
	@ Venta del mes
	*/
	$sql = '
		SELECT
			sum
				(
					venta
				)
					AS
						venta
		FROM
			total_companias
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
	';
	$tmp = $db->query($sql);
	$data['venta'] = $tmp[0]['venta'] != 0 ? $tmp[0]['venta'] : 0;

	/*
	@ Otros
	*/
	$data['otros'] = 0;

	/*
	@ Ventas Netas
	*/
	$data['ventas_netas'] = $tmp[0]['venta'] != 0 ? $tmp[0]['venta'] : 0;

	/*
	@ Inventario Anterior
	*/
	// $sql = '
	// 	SELECT
	// 		inv_act
	// 	FROM
	// 		balances_ros
	// 	WHERE
	// 			num_cia = ' . $c['num_cia'] . '
	// 		AND
	// 			anio = ' . date('Y', mktime(0, 0, 0, $mes, 0, $anyo)) . '
	// 		AND
	// 			mes = ' . date('n', mktime(0, 0, 0, $mes, 0, $anyo)) . '
	// ';
	// $tmp = $db->query($sql);
	// $data['inv_ant'] = $tmp ? $tmp[0]['inv_act'] : 0;

	/*
	@
	@@ COSTO DE PRODUCCION
	@
	*/

	/*
	@
	@@ Calcular existencia a final de mes, asi como las compras y los consumos (mini-auxiliar de inventario)
	@
	*/

	/*
	@ Obtener historico de inicio de mes y reordenarlo en un arreglo de códigos de materia prima
	@
	@ [07-Agosto-2009] Excluir [912] CONDIMENTO
	*/
	// $sql = '
	// 	SELECT
	// 		codmp,
	// 		existencia,
	// 		precio_unidad
	// 	FROM
	// 			historico_inventario
	// 		LEFT JOIN
	// 			catalogo_mat_primas
	// 				USING
	// 					(
	// 						codmp
	// 					)
	// 	WHERE
	// 			num_cia = ' . $c['num_cia'] . '
	// 		AND
	// 			fecha = \'' . $fecha_his . '\'
	// 		AND
	// 			codmp
	// 				NOT IN
	// 					(
	// 						90,
	// 						912
	// 					)
	// 		AND
	// 			no_exi = \'FALSE\'
	// 	ORDER BY
	// 		controlada
	// 			DESC,
	// 		tipo,
	// 		codmp
	// ';
	// $tmp = $db->query($sql);

	/*
	@ Reordenar datos
	*/
	// $mps = array();
	// if ($tmp)
	// 	foreach ($tmp as $t)
	// 		$mps[$t['codmp']] = array(
	// 			'existencia_ini' => $t['existencia'],
	// 			'precio_ini' => $t['precio_unidad'],
	// 			'costo_ini' => $t['existencia'] * $t['precio_unidad'],
	// 			'compras' => 0,
	// 			'consumos' => 0,
	// 			'existencia' => $t['existencia'],
	// 			'precio' => $t['precio_unidad'],
	// 			'costo' => $t['existencia'] * $t['precio_unidad']
	// 		);

	/*
	@ Obtener movimientos  y reordenarlo en un arreglo de códigos de materia prima y movimientos
	@
	@ [07-Agosto-2009] Excluir [912] CONDIMENTO
	*/
	// $sql = '
	// 	SELECT
	// 		codmp,
	// 		fecha,
	// 		tipo_mov,
	// 		cantidad,
	// 		precio_unidad,
	// 		total_mov,
	// 		CASE
	// 			WHEN descripcion = \'DIFERENCIA INVENTARIO\' THEN
	// 				2
	// 			ELSE
	// 				1
	// 		END
	// 			AS
	// 				tipo
	// 	FROM
	// 			mov_inv_real
	// 		LEFT JOIN
	// 			catalogo_mat_primas
	// 				USING
	// 					(
	// 						codmp
	// 					)
	// 	WHERE
	// 			num_cia = ' . $c['num_cia'] . '
	// 		AND
	// 			fecha
	// 				BETWEEN
	// 						\'' . $fecha1 . '\'
	// 					AND
	// 						\'' . $fecha2 . '\'
	// 		AND
	// 			codmp
	// 				NOT IN
	// 					(
	// 						90,
	// 						912
	// 					)
	// 		AND
	// 			no_exi = \'FALSE\'
	// 	ORDER BY
	// 		codmp,
	// 		tipo,
	// 		fecha,
	// 		tipo_mov,
	// 		cantidad
	// 			DESC
	// ';
	// $tmp = $db->query($sql);

	/*
	@ Reordenar datos
	*/
	// $movs = array();
	// if ($tmp)
	// 	foreach ($tmp as $t)
	// 		$movs[$t['codmp']][] = array(
	// 			'tipo' => $t['tipo'],
	// 			'fecha' => $t['fecha'],
	// 			'tipo_mov' => $t['tipo_mov'],
	// 			'cantidad' => $t['cantidad'],
	// 			'total' => $t['total_mov'],
	// 			'precio' => $t['precio_unidad'],
	// 			'existencia' => 0,
	// 			'costo' => 0,
	// 			'precio_pro' => $t['precio_unidad']
	// 		);

	/*
	@ Recorrer productos y calcular costos de consumo y compras, asi como costo de inventario final
	*/
	// foreach ($mps as $cod => $mp) {
		/*
		@ Si no hay movimientos para el producto saltar el proceso de calculo de costos
		*/
		// if (!isset($movs[$cod]))
		// 	continue;

		// $uni_in = 0;								// Unidades de entradas
		// $uni_out = 0;								// Unidades de salidas

		// $costo_in = 0;								// Costo de entradas
		// $costo_out = 0;								// Costo de salidas

		// $uni_ant = $mp['existencia_ini'];			// Arrastre la ultima existencia ante cualquier cambio del mismo
		// $precio_ant = $mp['precio_ini'];			// Arrastra el ultimo precio ante cualquier cambio del mismo
		// $costo_ant = $uni_ant * $precio_ant;		// Arrastra el ultimo costo ante cualquier cambio del mismo

		// $fprecio = FALSE;							// Flag Precio - TRUE = Arrastrar precio promedio

		/*
		@ Recorrer movimientos
		*/
		// foreach ($movs[$cod] as $i => $mov) {
			/*
			@ Tipo 1: Movimiento normal de entrada/salida
			*/
			// if ($mov['tipo'] == 1) {
				/*
				@ Proceso para 'Entradas'
				*/
				// if ($mov['tipo_mov'] == 'f') {
				// 	$uni_ant = $mps[$cod]['existencia'];
				// 	$precio_ant = $mps[$cod]['precio'];
				// 	$costo_ant = $mps[$cod]['costo'];

				// 	$mps[$cod]['existencia'] += $mov['cantidad'];
				// 	$mps[$cod]['costo'] += $mov['total'];

					/*
					@ Si la existencia anterior y actual son negativas, no calcular precio promedio y poner bandera de arrastre en TRUE
					*/
					// if ($uni_ant < 0 && $mps[$cod]['existencia'] < 0)
					// 	$fprecio = TRUE;
					/*
					@ Si la existencia anterior es negativa y la actual es positiva, no calcular precio promedio y poner bandera de arrastre en FALSE
					*/
					// else if ($uni_ant < 0 && $mps[$cod]['existencia'] >= 0)
					// 	$fprecio = FALSE;
					/*
					@ Calcular precio promedio
					*/
				// 	else
				// 		$mps[$cod]['precio'] = $mps[$cod]['costo'] / $mps[$cod]['existencia'];

				// 	$mps[$cod]['compras'] += $mov['total'];
				// 	$uni_in += $mov['cantidad'];

				// 	$movs[$cod][$i]['existencia'] = $mps[$cod]['existencia'];
				// 	$movs[$cod][$i]['costo'] = $mps[$cod]['costo'];
				// 	$movs[$cod][$i]['precio_pro'] = $mps[$cod]['precio'];
				// }
				/*
				@ Proceso para 'Salidas'
				*/
				// else if ($mov['tipo_mov'] == 't') {
				// 	$uni_ant = $mps[$cod]['existencia'];
				// 	$precio_ant = $mps[$cod]['precio'];

				// 	$mps[$cod]['existencia'] -= $mov['cantidad'];

					/*
					@ Si la existencia actual es negativa, calcular precio promedio
					*/
					// if ($mps[$cod]['existencia'] < 0) {
						/*
						@ Arrastrar precio
						*/
						// if ($fprecio == TRUE)
						// 	$proximo_precio = $mps[$cod]['precio'];
						/*
						@ Calcular precio si no hay arrastre del mismo
						*/
						// else {
						// 	$cantidad_tmp = 0;
						// 	$total_tmp = 0;
						// 	$existencia_tmp = $mps[$cod]['existencia'];

						// 	$proximo_precio = 0;

							/*
							@ Buscar cantidad de las siguientes entradas que satisfagan la existencia negativa
							*/
							// foreach (array_slice($movs[$cod], $i) as $next)
								/*
								@ Sumar entradas
								*/
								// if ($next['tipo'] == 1 && $next['tipo_mov'] == 'f') {
								// 	$cantidad_tmp += $next['cantidad'];
								// 	$total_tmp += $next['total'];
								// 	$existencia_tmp += $next['cantidad'];

									/*
									@ Si la suma de esta entrada elimina la existencia negativa, calcular precio
									*/
								// 	if ($existencia_tmp >= 0) {
								// 		$proximo_precio = $total_tmp / $cantidad_tmp;
								// 		break;
								// 	}
								// }
								/*
								@ Restar salidas
								*/
						// 		else if ($next['tipo'] == 1 && $next['tipo_mov'] == 't')
						// 			$existencia_tmp -= $next['cantidad'];
						// }

						/*
						@ Dividir valores de salida, el 'valor 1' es el costo del consumo de la parte
						*/
					// 	$val_1 = ($mps[$cod]['existencia'] + $mov['cantidad']) * $precio_ant;
					// 	$val_2 = abs($mps[$cod]['existencia']) * $proximo_precio;
					// 	$val_sal = $val_1 + $val_2;

					// 	$mps[$cod]['precio'] = $proximo_precio;
					// }
					/*
					@ Arrastre de precio promedio
					*/
					// else
					// 	$val_sal = $mov['cantidad'] * $mps[$cod]['precio'];

					// $movs[$cod][$i]['precio_pro'] = $mps[$cod]['precio'];
					// $movs[$cod][$i]['total'] = $val_sal;

					/*
					@ Calcular arrastres de salida
					*/
			// 		$uni_in += $mov['cantidad'];
			// 		$costo_in  += $val_sal;

			// 		$mps[$cod]['costo'] -= $val_sal;
			// 		$mps[$cod]['consumos'] += $val_sal;

			// 		$movs[$cod][$i]['existencia'] = $mps[$cod]['existencia'];
			// 		$movs[$cod][$i]['costo'] = $mps[$cod]['costo'];
			// 		$movs[$cod][$i]['precio_pro'] = $mps[$cod]['precio'];
			// 	}
			// }
			/*
			@ Tipo 2: Diferencia de inventario
			*/
	// 		else if($mov['tipo'] == 2) {
	// 			$mps[$cod]['existencia'] += $mov['tipo_mov'] == 't' ? -$mov['cantidad'] : $mov['cantidad'];
	// 			$mps[$cod]['costo'] += $mov['tipo_mov'] == 't' ? -($mov['cantidad'] * $mps[$cod]['precio']) : $mov['cantidad'] * $mps[$cod]['precio'];

	// 			if ($mov['tipo_mov'] == 't') {
	// 				$uni_out += $mov['cantidad'];
	// 				$costo_out += $mov['cantidad'] * $mps[$cod]['precio'];
	// 			}
	// 			else if ($mov['tipo_mov'] == 'f') {
	// 				$uni_in += $mov['cantidad'];
	// 				$costo_in += $mov['cantidad'] * $mps[$cod]['precio'];
	// 			}
	// 		}
	// 	}
	// }

	/*
	@ Obtener costos y consumos
	*/
	$aux = new AuxInvClass($c['num_cia'], $anyo, $mes, NULL, 'real');

	/*
	@ Sumar las compras y los costos de todos los productos
	*/
	$data['inv_ant'] = 0;
	$data['compras'] = 0;
	$data['inv_act'] = 0;
	foreach ($aux->mps as $cod => $mp) {
		if ($cod != 90) {
			$data['inv_ant'] += $mp['costo_ini'];
			$data['compras'] += $mp['compras'];
			$data['inv_act'] += $mp['costo'];
		}
	}
	// foreach ($mps as $mp) {
	// 	$data['compras'] += $mp['compras'];
	// 	$data['inv_act'] += $mp['costo'];
	// }

	/*
	@ Otras Mercancias, se suman a las compras
	*/
	$sql = '
		SELECT
			sum
				(
					total
				)
					AS
						otras_mercancias
		FROM
			compra_directa
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha_mov
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				aplica_gasto = \'FALSE\'
	';
	$tmp = $db->query($sql);
	$otras_mercancias = $tmp[0]['otras_mercancias'] != 0 ? $tmp[0]['otras_mercancias'] : 0;echo "<br>COMPRAS={$data['compras']}<br>OTRAS MERCANCIAS={$otras_mercancias}";

	/*
	@ Restar otras mercancias a las compras
	*/
	$data['compras'] -= $otras_mercancias;echo "<br>COMPRAS - OTRAS MERCANCIAS={$data['compras']}";

	/*
	@ Mercancias
	*/
	if (mktime(0, 0, 0, $mes, 1, $anyo) < mktime(0, 0, 0, 6, 1, 2016))
	{
		$sql = '
			SELECT
				sum
					(
						importe
					)
						AS
							mercancias
			FROM
				movimiento_gastos
			WHERE
					num_cia = ' . $c['num_cia'] . '
				AND
					fecha
						BETWEEN
								\'' . $fecha1 . '\'
							AND
								\'' . $fecha2 . '\'
				AND
					codgastos = 23
		';
	}
	else
	{
		$sql = "SELECT
			SUM (importe) AS mercancias
		FROM
			(
				SELECT
					SUM (importe) AS importe
				FROM
					movimiento_gastos
				WHERE
					num_cia = {$c['num_cia']}
					AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
					AND codgastos IN (23)
					AND (
						num_cia,
						fecha,
						codgastos,
						importe
					) NOT IN (
						SELECT
							num_cia,
							fecha,
							codgastos,
							importe
						FROM
							pagos_otras_cias
							LEFT JOIN cheques USING (num_cia, folio, cuenta, fecha)
						WHERE
							num_cia = {$c['num_cia']}
							AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
							AND fecha_cancelacion IS NULL
					)

				UNION

				SELECT
					SUM (importe) AS importe
				FROM
					pagos_otras_cias
					LEFT JOIN cheques USING (num_cia, folio, cuenta, fecha)
					LEFT JOIN catalogo_gastos USING (codgastos)
				WHERE
					num_cia_aplica = {$c['num_cia']}
					AND fecha BETWEEN '{$fecha1}' AND '{$fecha2}'
					AND codgastos IN (23)
					AND fecha_cancelacion IS NULL
			) AS mercancias";
	}
	$tmp = $db->query($sql);
	$data['mercancias'] = $tmp[0]['mercancias'] != 0 ? $tmp[0]['mercancias'] : 0;

	/*
	@ Materia Prima Utilizada
	@
	@ = 'Inventario Anterior' + 'Compras' + 'Mercancias' - 'Inventario Actual'
	*/
	$data['mat_prima_utilizada'] = $data['inv_ant'] + $data['compras'] + $data['mercancias'] - $data['inv_act'];

	/*
	@ Gastos de Fabricación
	*/
	$sql = '
		SELECT
			sum
				(
					importe
				)
					AS
						gastos_fab
		FROM
				movimiento_gastos
			LEFT JOIN
				catalogo_gastos
					USING
						(
							codgastos
						)
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				codigo_edo_resultados = 1
	';
	$tmp = $db->query($sql);
	$data['gastos_fab'] = $tmp[0]['gastos_fab'] != 0 ? $tmp[0]['gastos_fab'] : 0;

	// *** [3-Oct-2008] Excluir todos los pagos hechos para otra compañía
	$sql = "SELECT round(sum(importe)::numeric, 2) AS importe FROM pagos_otras_cias LEFT JOIN cheques USING (num_cia, cuenta, folio, fecha) LEFT JOIN catalogo_gastos USING (codgastos) WHERE num_cia = {$c['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados IN (1) AND codgastos NOT IN (141) AND fecha_cancelacion IS NULL";
	$importe = $db->query($sql);
	$data['gastos_fab'] -= isset($importe[0]) ? $importe[0]['importe'] : 0;
	// *** [3-Oct-2008] Incluir todos los pagos hechos por otra compañía
	$sql = "SELECT round(sum(importe)::numeric, 2) AS importe FROM pagos_otras_cias LEFT JOIN cheques USING (num_cia, cuenta, folio, fecha) LEFT JOIN catalogo_gastos USING (codgastos) WHERE num_cia_aplica = {$c['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados IN (1) AND codgastos NOT IN (141) AND fecha_cancelacion IS NULL";
	$importe = $db->query($sql);
	$data['gastos_fab'] += isset($importe[0]) ? $importe[0]['importe'] : 0;

	// [03-Oct-2016] Incluir gastos en reserva
	$sql = "SELECT
		SUM(importe) AS importe
	FROM
		reserva_gastos rg
		LEFT JOIN catalogo_gastos cg USING (codgastos)
	WHERE
		rg.num_cia = {$c['num_cia']}
		AND rg.anio = {$anyo}
		AND rg.mes = {$mes}
		AND cg.codigo_edo_resultados = 1";

	$reserva_gastos = $db->query($sql);
	$data['gastos_fab'] += isset($reserva_gastos[0]) ? $reserva_gastos[0]['importe'] : 0;

	/*
	@ Costo de Producción
	@
	@ = 'Materia Prima Utilizada' + 'Gastos de Fabricación'
	*/
	$data['costo_produccion'] = $data['mat_prima_utilizada'] + $data['gastos_fab'];

	/*
	@ Utilidad Bruta
	@
	@ = 'Ventas Netas' - 'Costo Producción'
	*/
	$data['utilidad_bruta'] = $data['ventas_netas'] - $data['costo_produccion'];

	/*
	@
	@@ GASTOS
	@
	*/

	/*
	@ Gastos Generales
	*/
	$sql = '
		SELECT
			sum
				(
					importe
				)
					AS
						gastos_generales
		FROM
				movimiento_gastos
			LEFT JOIN
				catalogo_gastos
					USING
						(
							codgastos
						)
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				codigo_edo_resultados = 2
			AND
				codgastos
					NOT IN
						(
							141
						)
	';
	/*
	@ Si la fecha de consulta es posterior al Febrero de 2007 no incluir código 140 IMPUESTOS
	*/
	if (mktime(0, 0, 0, $mes, 1, $anyo) >= mktime(0, 0, 0, 2, 1, 2007))
		$sql .= '
			AND
				codgastos
					NOT IN
						(
							140
						)
		';
	/*
	@ Si la fecha de consulta es posterior al Diciembre de 2013 no incluir código 84 SEGUROS
	*/
	if (mktime(0, 0, 0, $mes, 1, $anyo) >= mktime(0, 0, 0, 1, 1, 2014))
		$sql .= '
			AND
				codgastos
					NOT IN
						(
							84
						)
		';
	$tmp = $db->query($sql);
	$data['gastos_generales'] = $tmp[0]['gastos_generales'] != 0 ? -$tmp[0]['gastos_generales'] : 0;

	// *** [3-Oct-2008] Excluir todos los pagos hechos para otra compañía
	$sql = "SELECT round(sum(importe)::numeric, 2) AS importe FROM pagos_otras_cias LEFT JOIN cheques USING (num_cia, cuenta, folio, fecha) LEFT JOIN catalogo_gastos USING (codgastos) WHERE num_cia = {$c['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados IN (2) AND codgastos NOT IN (141) AND fecha_cancelacion IS NULL";
	$importe = $db->query($sql);
	$data['gastos_generales'] += $importe[0]['importe'];

	// *** [3-Oct-2008] Incluir todos los pagos hechos por otra compañía
	$sql = "SELECT round(sum(importe)::numeric, 2) AS importe FROM pagos_otras_cias LEFT JOIN cheques USING (num_cia, cuenta, folio, fecha) LEFT JOIN catalogo_gastos USING (codgastos) WHERE num_cia_aplica = {$c['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codigo_edo_resultados IN (2) AND codgastos NOT IN (141) AND fecha_cancelacion IS NULL";
	$importe = $db->query($sql);
	$data['gastos_generales'] -= $importe[0]['importe'];

	/*
	@ Impuesto IDE
	@
	@ [03-Feb-2010] A partir del año 2010 el porcentaje es del 3% en lugar del 2%
	@ [12-Feb-2016] A partir del año 2016 ya no se calcula IDE
	*/
	if (mktime(0, 0, 0, $mes, 1, $anyo) < mktime(0, 0, 0, 1, 1, 2016))
	{
		$por_ide = $anyo < 2010 ? 0.02 : 0.03;
		$sql = '
			SELECT
				round
					(
						sum(
							importe
						)
							::
								numeric
									- 25000,
						2
					)
						* ' . $por_ide . '
							AS
								impuesto_ide
			FROM
				estado_cuenta
			WHERE
					num_cia = ' . $c['num_cia'] . '
				AND
					fecha_con
						BETWEEN
								\'' . $fecha1 . '\'
							AND
								\'' . $fecha2 . '\'
				AND
					cod_mov
						IN
							(
								1,
								7,
								13,
								16,
								79
							)
		';
		$tmp = $db->query($sql);
		$impuesto_ide = $tmp[0]['impuesto_ide'] != 0 ? -$tmp[0]['impuesto_ide'] : 0;

		/*
		@ Sumar impuesto IDE a gastos generales
		*/
		$data['gastos_generales'] += $impuesto_ide;
	}

	// [03-Oct-2016] Incluir gastos en reserva
	$sql = "SELECT
		SUM(importe) AS importe
	FROM
		reserva_gastos rg
		LEFT JOIN catalogo_gastos cg USING (codgastos)
	WHERE
		rg.num_cia = {$c['num_cia']}
		AND rg.anio = {$anyo}
		AND rg.mes = {$mes}
		AND cg.codigo_edo_resultados = 2";

	$reserva_gastos = $db->query($sql);
	$data['gastos_generales'] -= isset($reserva_gastos[0]) ? $reserva_gastos[0]['importe'] : 0;

	/*
	@ Gastos de Caja
	*/
	$sql = '
		SELECT
			sum
				(
					CASE
						WHEN tipo_mov = \'FALSE\' THEN
							-importe
						ELSE
							importe
						END
				)
					AS
						gastos_caja
		FROM
			gastos_caja
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				clave_balance = \'TRUE\'
	';
	$tmp = $db->query($sql);
	$data['gastos_caja'] = $tmp[0]['gastos_caja'] != 0 ? $tmp[0]['gastos_caja'] : 0;

	/*
	@ Comisiones Bancarias
	@
	@ Válido a partir de Marzo de 2007
	@
	@ No incluir código 78 IMPUESTO IDE
	*/
	$data['comisiones'] = 0;
	if (mktime(0, 0, 0, $mes, 1, $anyo) >= mktime(0, 0, 0, 3, 1, 2007)) {
		$sql = '
			SELECT
				sum
					(
						CASE
							WHEN tipo_mov = \'TRUE\' THEN
								-importe
							ELSE
								importe
						END
					)
						AS
							comisiones
			FROM
				estado_cuenta
			WHERE
					num_cia = ' . $c['num_cia'] . '
				AND
					fecha
						BETWEEN
								\'' . $fecha1 . '\'
							AND \'' . $fecha2 . '\'
				AND
					(
							(
									cuenta = 1
								AND
									cod_mov
										IN
											(
												SELECT
													cod_mov
												FROM
													catalogo_mov_bancos
												WHERE
														entra_bal = \'TRUE\'
													AND
														cod_mov
															NOT IN
																(
																	78
																)
												GROUP BY
													cod_mov
											)
							)
						OR
							(
									cuenta = 2
								AND
									cod_mov
										IN
											(
												SELECT
													cod_mov
												FROM
													catalogo_mov_santander
												WHERE
														entra_bal = \'TRUE\'
													AND
														cod_mov
															NOT IN
																(
																	78
																)
												GROUP BY
													cod_mov
											)
							)
					)
		';
		$tmp = $db->query($sql);
		$data['comisiones'] = $tmp[0]['comisiones'] != 0 ? $tmp[0]['comisiones'] : 0;
	}

	/*
	@ Reservas
	*/
	$sql = '
		SELECT
			sum
				(
					importe
				)
					AS
						reserva
		FROM
			reservas_cias
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha = \'' . $fecha1 . '\'
	';
	$tmp = $db->query($sql);
	$data['reserva_aguinaldos'] = $tmp[0]['reserva'] != 0 ? -$tmp[0]['reserva'] : 0;

	/*
	@ Gastos Pagados por otras Compañías
	*/
	$sql = '
		SELECT
			sum
				(
					CASE
						WHEN num_cia_egreso = ' . $c['num_cia'] . ' THEN
							monto
						WHEN num_cia_ingreso = ' . $c['num_cia'] . ' THEN
							-monto
					END
				)
					AS
						gastos_otras_cias
		FROM
			gastos_otras_cia
		WHERE
				(
						num_cia_egreso = ' . $c['num_cia'] . '
					OR
						num_cia_ingreso = ' . $c['num_cia'] . '
				)
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
	';
	$tmp = $db->query($sql);
	$data['gastos_otras_cias'] = $tmp[0]['gastos_otras_cias'] != 0 ? $tmp[0]['gastos_otras_cias'] : 0;

	/*
	@ Total de Gastos
	*/
	$data['total_gastos'] = $data['gastos_generales'] + $data['gastos_caja'] + $data['comisiones'] + $data['reserva_aguinaldos'] + $data['gastos_otras_cias'];

	/*
	@ Ingresos Extraordinarios
	*/
	$data['ingresos_ext'] = 0;

	/*
	@ Utilidad Neta
	*/
	$data['utilidad_neta'] = $data['utilidad_bruta'] + $data['total_gastos'] + $data['ingresos_ext'];

	/*
	@ Materia Prima Utilizada / Ventas
	*/
	$data['mp_vtas'] = $data['ventas_netas'] > 0 ? $data['mat_prima_utilizada'] / $data['ventas_netas'] : 0;

	/*
	@ Efectivo
	*/
	$sql = '
		SELECT
			sum
				(
					efectivo
				)
					AS
						efectivo
		FROM
			total_companias
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
	';
	$tmp = $db->query($sql);
	$data['efectivo'] = $tmp[0]['efectivo'] != 0 ? $tmp[0]['efectivo'] : 0;

	/*
	@ Pollos, Pescuezos y Piernas de Pavo Vendidas
	*/
	$data['pollos_vendidos'] = 0;
	$data['pescuezos'] = 0;
	$data['p_pavo'] = 0;

	$sql = '
		SELECT
			CASE
				WHEN codmp IN (160, 600, 700, 573) THEN
					\'pollos_vendidos\'
				WHEN codmp = 297 THEN
					\'pescuezos\'
				WHEN codmp = 352 THEN
					\'p_pavo\'
			END
				AS
					campo,
			sum
				(
					unidades
				)
					AS unidades
		FROM
			hoja_diaria_rost
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				codmp
					IN
						(
							160,
							600,
							700,
							297,
							352,
							573
						)
		GROUP BY
			campo
	';
	$tmp = $db->query($sql);

	if ($tmp)
		foreach ($tmp as $t)
			$data[$t['campo']] = $t['unidades'];

	/*
	@ Pesos promedio de pollo
	*/
	$data['peso_normal'] = 0;
	$data['peso_chico'] = 0;
	$data['peso_grande'] = 0;

	$sql = '
		SELECT
			CASE
				WHEN codmp = 160 THEN
					\'peso_normal\'
				WHEN codmp = 600 THEN
					\'peso_chico\'
				WHEN codmp IN (700, 573) THEN
					\'peso_grande\'
			END
				as
					campo,
			sum
				(
					kilos
				)
					/
						sum
							(
								cantidad
							)
								AS
									peso
		FROM
			fact_rosticeria
		WHERE
				num_cia = ' . $c['num_cia'] . '
			AND
				fecha_mov
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			AND
				codmp
					IN
						(
							160,
							600,
							700, 573
						)
		GROUP BY
			campo
	';
	$tmp = $db->query($sql);

	if ($tmp)
		foreach ($tmp as $t)
			$data[$t['campo']] = $t['peso'];

	$sql = '
		SELECT
			SUM(total) / SUM(kilos)
				AS precio_kilo
		FROM
			fact_rosticeria
		WHERE
			num_cia = ' . $c['num_cia'] . '
			AND fecha_mov BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
			AND codmp IN (160, 600, 700, 573)
	';
	$tmp = $db->query($sql);
	$precio_pollo = 0;
	if ($tmp && $tmp[0]['precio_kilo'] > 0) {
		$precio_pollo = $tmp[0]['precio_kilo'];
	}

	/*
	@ Crear querys de inserción
	*/

	/*
	@ Tabla: balances_ros
	*/
	$balance .= 'INSERT INTO balances_ros (' . implode(', ', array_keys($data)) . ') VALUES (' . implode(', ', $data) . ');' . "\n";
	$balance .= 'INSERT INTO historico (num_cia, anio, mes, venta, utilidad, pollos, pescuezos, piernas, precio_pollo) SELECT num_cia, anio, mes, ventas_netas, utilidad_neta, pollos_vendidos, pescuezos, p_pavo, ' . $precio_pollo . ' FROM balances_ros WHERE num_cia = ' . $data['num_cia'] . ' AND anio = ' . $data['anio'] . ' AND mes = ' . $data['mes'] . ";\n";
}

$db->query($balance);
?>
