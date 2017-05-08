<?php
// ACTUALIZACION DE INVENTARIOS (VER. 3)
// Menu 'No definido'

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/auxinv.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn, "autocommit=yes");

// Conectarse a la base de datos
$db = new DBclass($dsn, 'autocommit=yes');

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_act_inv_v3.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_REQUEST['actualizar_observaciones'])) {
	if ($id = $db->query("SELECT id FROM observaciones_diferencias_inventario WHERE num_cia = {$_REQUEST['num_cia']} AND anio = {$_REQUEST['anio']} AND mes = {$_REQUEST['mes']}")) {
		$sql = "
			UPDATE
				observaciones_diferencias_inventario
			SET
				observaciones = '" . utf8_decode(strtoupper(trim($_REQUEST['obs']))) . "'
			WHERE
				id = {$id[0]['id']}
		";
	} else {
		$sql = "
			INSERT INTO
				observaciones_diferencias_inventario (
					num_cia,
					anio,
					mes,
					observaciones
				) VALUES (
					{$_REQUEST['num_cia']},
					{$_REQUEST['anio']},
					{$_REQUEST['mes']},
					'" . utf8_decode(strtoupper(trim($_REQUEST['obs']))) . "'
				)
		";
	}

	$db->query($sql);

	die;
}

if (isset($_REQUEST['obtener_observaciones_extra']))
{
	$fecha = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

	$result = $db->query("SELECT log.codmp, cmp.nombre, log.cantidad_anterior, log.cantidad_nueva, log.tsmod::DATE AS fecha_mod, DATE_TRUNC('SECOND', log.tsmod::TIME) AS hora_mod, auth.nombre AS user FROM bitacora_modificacion_diferencias log LEFT JOIN catalogo_mat_primas cmp USING (codmp) LEFT JOIN auth ON (auth.iduser = log.idmod) WHERE log.num_cia = {$_REQUEST['num_cia']} AND log.fecha = '{$fecha}' ORDER BY log.id");

	if ($result)
	{
		$html = '<table class="tabla" align="center"><tr><th class="tabla">Fecha y hora</th><th class="tabla">Usuario</th><th class="tabla">Producto</th><th class="tabla">Anterior</th><th class="tabla">Nuevo</th></tr>';

		foreach ($result as $row) {
			$html .= '<tr><td class="tabla">' . $row['fecha_mod'] . ' ' . $row['hora_mod'] . '</td><td class="vtabla">' . $row['user'] . '</td><td class="vtabla">' . $row['codmp'] . ' ' . $row['nombre'] . '</td><td class="rtabla">' . number_format($row['cantidad_anterior'], 2) . '</td><td class="rtabla">' . number_format($row['cantidad_nueva'], 2) . '</td></tr>';
		}

		$html .= '</table>';

		echo $html;
	}

	die;
}

// Actualizar inventarios
if (isset($_GET['accion']) && $_GET['accion'] == 1) {
	$fecha_ini = date("d/m/Y", mktime(0, 0, 0, date("n") - 1, 1, date("Y")));
	$fecha = date("d/m/Y", mktime(0, 0, 0, date("n"), 0, date("Y")));

	$anio = date('Y', mktime(0, 0, 0, date('n') - 1, 1, date('Y')));
	$mes = date('n', mktime(0, 0, 0, date('n') - 1, 1, date('Y')));

	// Validar inclusión de gastos

	$sql = "
		SELECT
			num_cia,
			nombre_corto,
			codgastos,
			descripcion
		FROM
			gastos_obligados
			LEFT JOIN catalogo_companias
				USING (num_cia)
			LEFT JOIN catalogo_gastos
				USING (codgastos)
		WHERE
			(num_cia, codgastos) NOT IN (
				SELECT
					num_cia,
					codgastos
				FROM
					cheques
				WHERE
					(
						num_cia <= 300
						OR num_cia IN (702, 703)
					)
					AND fecha BETWEEN '$fecha_ini' AND '$fecha'
					AND (
						fecha_cancelacion IS NULL
						OR fecha_cancelacion > '$fecha'
					)

				UNION

				SELECT
					num_cia,
					codgastos
				FROM
					reserva_gastos
				WHERE
					(
						num_cia <= 300
						OR num_cia IN (702, 703)
					)
					AND anio = {$anio}
					AND mes = {$mes}
			)
			AND num_cia NOT IN (6, 38, 55, 11)
		ORDER BY
			num_cia,
			codgastos
	";

	$result = $db->query($sql);
	if ($result) {
		$tpl->newBlock("bloqueo");
		foreach ($result as $reg) {
			$tpl->newBlock("gasto");
			$tpl->assign("num_cia", $reg['num_cia']);
			$tpl->assign("nombre", $reg['nombre_corto']);
			$tpl->assign("codgastos", $reg['codgastos']);
			$tpl->assign("nombre_gasto", $reg['descripcion']);
		}
		$tpl->printToScreen();
		die;
	}

	// [2-Nov-2013] Validar que el gas capturado en el inventario sea menor al 90% de la capacidad total de los tanques de gas en la compañía

	$sql = "
		SELECT
			num_cia,
			nombre_corto,
			SUM(capacidad)
				AS capacidad,
			inventario
		FROM
			inventario_fin_mes ifm
			LEFT JOIN catalogo_tanques ct
				USING (num_cia)
			LEFT JOIN catalogo_companias cc
				USING (num_cia)
		WHERE
			codmp = 90
			AND fecha = '{$fecha}'
		GROUP BY
			num_cia,
			nombre_corto,
			inventario
		HAVING
			SUM(capacidad) * 0.90 < inventario
		ORDER BY
			num_cia
	";

	$result = $db->query($sql);

	if ($result)
	{
		$tpl->newBlock("bloqueo_capacidad_gas");

		foreach ($result as $row) {
			$tpl->newBlock("cia_gas");

			$tpl->assign("num_cia", $row['num_cia']);
			$tpl->assign("nombre", $row['nombre_corto']);
			$tpl->assign("capacidad", number_format($row['capacidad']));
			$tpl->assign("inventario", number_format($row['inventario']));
		}

		$tpl->printToScreen();

		die;
	}

	/*
	[02-Jun-2013] Desglosar automáticamente el gas
	*/

	// Validar que no se hayan generado antes las distribuciones de gas
	$sql = '
		SELECT
			id
		FROM
			mov_inv_real
		WHERE
			fecha BETWEEN \'' . $fecha_ini . '\' AND \'' . $fecha . '\'
			AND codmp = 90
			AND tipo_mov = FALSE
			AND descripcion LIKE \'TRASPASO GAS%\'
		LIMIT
			1
	';

	if ( ! $db->query($sql)) {
		/*$sql = '
			SELECT
				num_cia,
				fecha,
				cantidad,
				precio_unidad,
				ros,
				porc
			FROM
				distribucion_gas dg
				LEFT JOIN mov_inv_real mov
					USING (num_cia)
			WHERE
				fecha BETWEEN \'' . $fecha_ini . '\' AND \'' . $fecha . '\'
				AND codmp = 90
				AND tipo_mov = FALSE
				AND descripcion NOT LIKE \'TRASPASO GAS%\'
			ORDER BY
				num_cia,
				fecha
		';

		$result = $db->query($sql);

		if ($result) {
			$sql = '';

			foreach ($result as $r) {
				// Claúsula Especial. Si la panaderia es la 29, 30 o 59 solo tomar las entradas con más de 500 litros
				if (in_array($r['num_cia'], array(29, 30, 59)) && $r['cantidad'] < 500)
					continue;

				$cantidad = round($r['cantidad'] * $r['porc'] / 100, 2);
				$costo = round($cantidad * $r['precio_unidad'], 2);

				// Ingresar entrada negativa en panaderia
				$sql .= '
					INSERT INTO
						mov_inv_real (
							num_cia,
							codmp,
							fecha,
							tipo_mov,
							cantidad,
							precio,
							total_mov,
							precio_unidad,
							descripcion
						) VALUES (
							' . $r['num_cia'] . ',
							90,
							\'' . $r['fecha'] . '\',
							\'FALSE\',
							' . -$cantidad . ',
							' . $r['precio_unidad'] . ',
							' . -$costo . ',
							' . $r['precio_unidad'] . ',
							\'TRASPASO GAS (CIA ' . $r['ros'] . ')\'
						)
				' . ";\n";

				// Ingresar entrada positiva en rosticeria
				$sql .= '
					INSERT INTO
						mov_inv_real (
							num_cia,
							codmp,
							fecha,
							tipo_mov,
							cantidad,
							precio,
							total_mov,
							precio_unidad,
							descripcion
						) VALUES (
							' . $r['ros'] . ',
							90,
							\'' . $r['fecha'] . '\',
							\'FALSE\',
							' . $cantidad . ',
							' . $r['precio_unidad'] . ',
							' . $costo . ',
							' . $r['precio_unidad'] . ',
							\'TRASPASO GAS (CIA ' . $r['num_cia'] . ')\'
						)
				' . ";\n";

				// Actualizar inventario de panaderia
				$sql .= '
					UPDATE
						inventario_real
					SET
						existencia = existencia - ' . $cantidad . ',
						precio_unidad = ' . $r['precio_unidad'] . '
					WHERE
						num_cia = ' . $r['num_cia'] . '
						AND codmp = 90
				' . ";\n";

				// Actualizar inventario de rosticeria
				$sql .= '
					UPDATE
						inventario_real
					SET
						existencia = existencia + ' . $cantidad . ',
						precio_unidad = ' . $r['precio_unidad'] . '
					WHERE
						num_cia = ' . $r['ros'] . '
						AND codmp = 90
				' . ";\n";
			}

			$db->query($sql);
		}*/

		// [06-Mar-2015] A partir de febrero de 2015 ya no se desglosa por porcentajes,
		// se calcula el consumo de gas de las rosticerias a partir del volumen de ventas
		// de pollo y la siguiente ecuacion para calcular el precio por pollo vendido:
		//
		//           -3 * Cantidad de pollos vendidos + 4200
		// Precio = ----------------------------------------- + 4
		//                            11600
		//
		// Operacion obtenida a partir de la ecuacion de la recta:
		//
		// m = (y2 - y1) / (x2 - x1)
		//
		// y - y1 = m(x - x1)
		//
		// COOL!!! :D

		// [08-Jun-2015] Nada de lo anterior funciono ò_Ó

		// $sql = "SELECT
		// 	dg.ros,
		// 	dg.num_cia,
		// 	SUM(mvr.cantidad)
		// 		AS cantidad,
		// 	COALESCE((
		// 		SELECT
		// 			precio_unidad
		// 		FROM
		// 			mov_inv_real
		// 		WHERE
		// 			num_cia = dg.num_cia
		// 			AND codmp = 90
		// 			AND fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
		// 			AND tipo_mov = FALSE
		// 		ORDER BY
		// 			fecha DESC
		// 		LIMIT
		// 			1
		// 	), (
		// 		SELECT
		// 			precio_unidad
		// 		FROM
		// 			historico_inventario
		// 		WHERE
		// 			num_cia = dg.num_cia
		// 			AND codmp = 90
		// 			AND fecha = '{$fecha_ini}'::DATE - INTERVAL '1 DAY'
		// 	), 0)
		// 		AS precio_gas
		// FROM
		// 	mov_inv_real mvr
		// 	LEFT JOIN distribucion_gas dg
		// 		ON (mvr.num_cia = dg.ros)
		// WHERE
		// 	mvr.fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
		// 	AND mvr.codmp IN (160, 334, 700, 600, 573)
		// 	AND mvr.tipo_mov = TRUE
		// 	AND mvr.descripcion NOT LIKE 'TRASPASO GAS%'
		// 	AND dg.ros IS NOT NULL
		// 	AND dg.ros NOT IN (325, 305, 310, 324, 419, 417, 344, 333, 331, 329, 436, 439, 321)
		// GROUP BY
		// 	dg.ros,
		// 	dg.num_cia
		// ORDER BY
		// 	dg.ros";

		// $result = $db->query($sql);

		// if ($result) {
		// 	$sql = '';

		// 	foreach ($result as $r) {
		// 		$precio_consumo = ((-3 * $r['cantidad'] + 4200) / 11600) + 4;
		// 		$costo_consumo = round($r['cantidad'] * $precio_consumo, 2);
		// 		$cantidad = round($costo_consumo / $r['precio_gas'], 2);
		// 		$precio_unidad = $r['precio_gas'];

		// 		// Ingresar entrada negativa en panaderia
		// 		$sql .= "INSERT INTO
		// 			mov_inv_real (
		// 				num_cia,
		// 				codmp,
		// 				fecha,
		// 				tipo_mov,
		// 				cantidad,
		// 				precio,
		// 				total_mov,
		// 				precio_unidad,
		// 				descripcion
		// 			) VALUES (
		// 				{$r['num_cia']},
		// 				90,
		// 				'{$fecha}',
		// 				FALSE,
		// 				-{$cantidad},
		// 				{$precio_unidad},
		// 				-{$costo_consumo},
		// 				{$precio_unidad},
		// 				'TRASPASO GAS (CIA {$r['ros']})'
		// 			);";

		// 		// Ingresar entrada positiva en rosticeria
		// 		$sql .= "INSERT INTO
		// 			mov_inv_real (
		// 				num_cia,
		// 				codmp,
		// 				fecha,
		// 				tipo_mov,
		// 				cantidad,
		// 				precio,
		// 				total_mov,
		// 				precio_unidad,
		// 				descripcion
		// 			) VALUES (
		// 				{$r['ros']},
		// 				90,
		// 				'{$fecha}',
		// 				FALSE,
		// 				{$cantidad},
		// 				{$precio_unidad},
		// 				{$costo_consumo},
		// 				{$precio_unidad},
		// 				'TRASPASO GAS (CIA {$r['num_cia']})'
		// 			);";

		// 		// Actualizar inventario de panaderia
		// 		$sql .= "UPDATE
		// 			inventario_real
		// 		SET
		// 			existencia = existencia - {$cantidad},
		// 			precio_unidad = {$precio_unidad}
		// 		WHERE
		// 			num_cia = {$r['num_cia']}
		// 			AND codmp = 90;";

		// 		// Actualizar inventario de rosticeria
		// 		$sql .= "UPDATE
		// 			inventario_real
		// 		SET
		// 			existencia = existencia + {$cantidad},
		// 			precio_unidad = {$precio_unidad}
		// 		WHERE
		// 			num_cia = {$r['ros']}
		// 			AND codmp = 90;";
		// 	}

		// 	$db->query($sql);
		// }

		// [07-may-2015] Agregado caso para cuando el traspaso se hace de rosticería
		// a panadería

		// $sql = "SELECT
		// 	dg.ros,
		// 	dg.num_cia,
		// 	SUM(mvr.cantidad)
		// 		AS cantidad,
		// 	COALESCE((
		// 		SELECT
		// 			precio_unidad
		// 		FROM
		// 			mov_inv_real
		// 		WHERE
		// 			num_cia = dg.num_cia
		// 			AND codmp = 90
		// 			AND fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
		// 			AND tipo_mov = FALSE
		// 		ORDER BY
		// 			fecha DESC
		// 		LIMIT
		// 			1
		// 	), (
		// 		SELECT
		// 			precio_unidad
		// 		FROM
		// 			historico_inventario
		// 		WHERE
		// 			num_cia = dg.num_cia
		// 			AND codmp = 90
		// 			AND fecha = '{$fecha_ini}'::DATE - INTERVAL '1 DAY'
		// 	), 0)
		// 		AS precio_gas,
		// 	COALESCE((
		// 		SELECT
		// 			SUM(cantidad)
		// 		FROM
		// 			mov_inv_real
		// 		WHERE
		// 			num_cia = dg.num_cia
		// 			AND codmp = 90
		// 			AND fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
		// 			AND tipo_mov = FALSE
		// 	), 0)
		// 		AS compras_gas
		// FROM
		// 	mov_inv_real mvr
		// 	LEFT JOIN distribucion_gas dg
		// 		ON (mvr.num_cia = dg.num_cia)
		// WHERE
		// 	mvr.fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
		// 	AND mvr.codmp IN (160, 334, 700, 600, 573)
		// 	AND mvr.tipo_mov = TRUE
		// 	AND mvr.descripcion NOT LIKE 'TRASPASO GAS%'
		// 	AND dg.ros IS NOT NULL
		// 	AND dg.ros NOT IN (325, 305, 310, 324, 419, 417, 344, 333, 331, 329, 436, 439, 321)
		// GROUP BY
		// 	dg.ros,
		// 	dg.num_cia
		// ORDER BY
		// 	dg.ros";

		// $result = $db->query($sql);

		// if ($result) {
		// 	$sql = '';

		// 	foreach ($result as $r) {
		// 		$precio_consumo = ((-3 * $r['cantidad'] + 4200) / 11600) + 4;
		// 		$costo_consumo = round($r['cantidad'] * $precio_consumo, 2);
		// 		$cantidad = round($costo_consumo / $r['precio_gas'], 2);
		// 		$precio_unidad = $r['precio_gas'];

		// 		// Ingresar entrada negativa en rosticería
		// 		$sql .= "INSERT INTO
		// 			mov_inv_real (
		// 				num_cia,
		// 				codmp,
		// 				fecha,
		// 				tipo_mov,
		// 				cantidad,
		// 				precio,
		// 				total_mov,
		// 				precio_unidad,
		// 				descripcion
		// 			) VALUES (
		// 				{$r['num_cia']},
		// 				90,
		// 				'{$fecha}',
		// 				FALSE,
		// 				-{$cantidad},
		// 				{$precio_unidad},
		// 				-{$costo_consumo},
		// 				{$precio_unidad},
		// 				'TRASPASO GAS (CIA {$r['ros']})'
		// 			);";

		// 		// Ingresar entrada positiva en panadería
		// 		$sql .= "INSERT INTO
		// 			mov_inv_real (
		// 				num_cia,
		// 				codmp,
		// 				fecha,
		// 				tipo_mov,
		// 				cantidad,
		// 				precio,
		// 				total_mov,
		// 				precio_unidad,
		// 				descripcion
		// 			) VALUES (
		// 				{$r['ros']},
		// 				90,
		// 				'{$fecha}',
		// 				FALSE,
		// 				{$cantidad},
		// 				{$precio_unidad},
		// 				{$costo_consumo},
		// 				{$precio_unidad},
		// 				'TRASPASO GAS (CIA {$r['num_cia']})'
		// 			);";

		// 		// Actualizar inventario de panaderia
		// 		$sql .= "UPDATE
		// 			inventario_real
		// 		SET
		// 			existencia = existencia - {$cantidad},
		// 			precio_unidad = {$precio_unidad}
		// 		WHERE
		// 			num_cia = {$r['num_cia']}
		// 			AND codmp = 90;";

		// 		// Actualizar inventario de rosticeria
		// 		$sql .= "UPDATE
		// 			inventario_real
		// 		SET
		// 			existencia = existencia + {$cantidad},
		// 			precio_unidad = {$precio_unidad}
		// 		WHERE
		// 			num_cia = {$r['ros']}
		// 			AND codmp = 90;";
		// 	}

		// 	$db->query($sql);
		// }

		// Compañias que traspasan

		$result = $db->query("SELECT
			dg.num_cia,
			cc1.tipo_cia,
			dg.ros,
			cc2.tipo_cia AS tipo_ros
		FROM
			distribucion_gas dg
			LEFT JOIN catalogo_companias cc1
				ON (cc1.num_cia = dg.num_cia)
			LEFT JOIN catalogo_companias cc2
				ON (cc2.num_cia = dg.ros)
		ORDER BY
			dg.num_cia,
			dg.ros");

		$sql = '';

		if ($result)
		{
			foreach ($result as $row) {
				if (($row['tipo_cia'] == 1 || $row['tipo_cia'] == 2) && $row['tipo_ros'] == 2)
				{
					$traspaso = $db->query("SELECT
						num_cia,
						pollos_vendidos * costo_gas_aprox / pollos_vendidos_aprox
							AS costo_gas,
						pollos_vendidos * costo_gas_aprox / pollos_vendidos_aprox / precio_gas
							AS cantidad_gas,
						precio_gas
					FROM
						(SELECT
							num_cia,
							pollos_vendidos,
							(
								SELECT
									num_cia
								FROM
									(
										SELECT
											mvr.num_cia,
											SUM(cantidad)
												AS pollos_vendidos,
											COALESCE((
												SELECT
													SUM(importe)
												FROM
													movimiento_gastos
												WHERE
													num_cia = mvr.num_cia
													AND fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
													AND codgastos = 90
											), (
												SELECT
													SUM(diferencia * precio_unidad)
												FROM
													inventario_fin_mes
												WHERE
													num_cia = mvr.num_cia
													AND fecha = '{$fecha}'
													AND codmp = 90
											), 0)
											AS consumo_gas
										FROM
											mov_inv_real mvr
										WHERE
											mvr.fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
											AND mvr.codmp IN (160, 334, 700, 600, 573)
											AND mvr.tipo_mov = TRUE
											AND num_cia NOT IN (
												SELECT
													ros
												FROM
													distribucion_gas
											)
											AND num_cia NOT IN (
												SELECT
													num_cia
												FROM
													distribucion_gas
											)
											AND mvr.num_cia NOT IN (312, 336, 347, 359, 378, 319, 346, 350, 421, 320, 463, 416, 345, 450, 453, 426, 355, 380, 460, 444, 318, 443, 415, 473, 470, 476, 412, 352, 348, 447)
										GROUP BY
											num_cia
										ORDER BY
											pollos_vendidos DESC
									) AS tabuladores
								WHERE
									consumo_gas > 0
									AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
								LIMIT 1
							)
								AS num_cia_aprox,
							(
								SELECT
									pollos_vendidos
								FROM
									(
										SELECT
											SUM(cantidad)
												AS pollos_vendidos,
											COALESCE((
												SELECT
													SUM(importe)
												FROM
													movimiento_gastos
												WHERE
													num_cia = mvr.num_cia
													AND fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
													AND codgastos = 90
											), (
												SELECT
													SUM(diferencia * precio_unidad)
												FROM
													inventario_fin_mes
												WHERE
													num_cia = mvr.num_cia
													AND fecha = '{$fecha}'
													AND codmp = 90
											), 0)
											AS consumo_gas
										FROM
											mov_inv_real mvr
										WHERE
											mvr.fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
											AND mvr.codmp IN (160, 334, 700, 600, 573)
											AND mvr.tipo_mov = TRUE
											AND num_cia NOT IN (
												SELECT
													ros
												FROM
													distribucion_gas
											)
											AND num_cia NOT IN (
												SELECT
													num_cia
												FROM
													distribucion_gas
											)
											AND mvr.num_cia NOT IN (312, 336, 347, 359, 378, 319, 346, 350, 421, 320, 463, 416, 345, 450, 453, 426, 355, 380, 460, 444, 318, 443, 415, 473, 470, 476, 412, 352, 348, 447)
										GROUP BY
											num_cia
										ORDER BY
											pollos_vendidos DESC) AS tabuladores
								WHERE
									consumo_gas > 0
									AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
								LIMIT 1
							)
								AS pollos_vendidos_aprox,
							(
								SELECT
									consumo_gas
								FROM
									(
										SELECT
											SUM(cantidad)
												AS pollos_vendidos,
											COALESCE((
												SELECT
													SUM(importe)
												FROM
													movimiento_gastos
												WHERE
													num_cia = mvr.num_cia
													AND fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
													AND codgastos = 90
											), (
												SELECT
													SUM(diferencia * precio_unidad)
												FROM
													inventario_fin_mes
												WHERE
													num_cia = mvr.num_cia
													AND fecha = '{$fecha}'
													AND codmp = 90
											), 0)
											AS consumo_gas
										FROM
											mov_inv_real mvr
										WHERE
											mvr.fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
											AND mvr.codmp IN (160, 334, 700, 600, 573)
											AND mvr.tipo_mov = TRUE
											AND num_cia NOT IN (
												SELECT
													ros
												FROM
													distribucion_gas
											)
											AND num_cia NOT IN (
												SELECT
													num_cia
												FROM
													distribucion_gas
											)
											AND mvr.num_cia NOT IN (312, 336, 347, 359, 378, 319, 346, 350, 421, 320, 463, 416, 345, 450, 453, 426, 355, 380, 460, 444, 318, 443, 415, 473, 470, 476, 412, 352, 348, 447)
										GROUP BY
											num_cia
										ORDER BY
											pollos_vendidos DESC
									) AS tabuladores
								WHERE
									consumo_gas > 0
									AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
								LIMIT 1
							)
								AS costo_gas_aprox,
							precio_gas
						FROM
							(
								SELECT
									dg.ros
										AS num_cia,
									ROUND(SUM(cantidad)::NUMERIC, 2)
										AS pollos_vendidos,
									COALESCE((
										SELECT
											precio_unidad
										FROM
											mov_inv_real
										WHERE
											num_cia = dg.num_cia
											AND codmp = 90
											AND fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
											AND tipo_mov = FALSE
										ORDER BY
											fecha DESC
										LIMIT
											1
									), (
										SELECT
											precio_unidad
										FROM
											historico_inventario
										WHERE
											num_cia = dg.num_cia
											AND codmp = 90
											AND fecha = '{$fecha_ini}'::DATE - INTERVAL '1 DAY'
											AND precio_unidad > 0
									), NULL)
										AS precio_gas
								FROM
									mov_inv_real mvr
									LEFT JOIN distribucion_gas dg
										ON (mvr.num_cia = dg.ros)
								WHERE
									dg.num_cia = {$row['num_cia']}
									AND dg.ros = {$row['ros']}
									AND mvr.fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
									AND mvr.codmp IN (160, 334, 700, 600, 573)
									AND mvr.tipo_mov = TRUE
									AND dg.ros NOT IN (325, 305, 310, 324, 419, 417, 344, 333, 331, 329, 436, 439, 321)
								GROUP BY
									dg.ros,
									dg.num_cia
								HAVING
									SUM(cantidad) > 0
							) AS result_pollos_vendidos) AS result_general");
				}
				else if ($row['tipo_cia'] == 1 && $row['tipo_ros'] == 1)
				{
					$traspaso = $db->query("SELECT
						dg.ros
							AS num_cia,
						diferencia * precio_unidad
							AS costo_gas,
						diferencia
							AS cantidad_gas,
						precio_unidad
							AS precio_gas
					FROM
						inventario_fin_mes ifm
						LEFT JOIN distribucion_gas dg
							ON (dg.num_cia = ifm.num_cia)
					WHERE
						dg.num_cia = {$row['num_cia']}
						AND dg.ros = {$row['ros']}
						AND ifm.fecha = '{$fecha}'
						AND ifm.codmp = 90
						AND diferencia > 0
					LIMIT
						1");
				}
				else if ($row['tipo_cia'] == 2 && $row['tipo_ros'] == 1)
				{
					$traspaso = $db->query("SELECT
						num_cia,
						COALESCE((
							SELECT
								existencia * precio_unidad
							FROM
								historico_inventario
							WHERE
								num_cia = result_general.num_cia_traspasa
								AND codmp = 90
								AND fecha = '{$fecha_ini}'::DATE - INTERVAL '1 DAY'
						), 0) + COALESCE((
							SELECT
								SUM(total_mov)
							FROM
								mov_inv_real
							WHERE
								num_cia = result_general.num_cia_traspasa
								AND codmp = 90
								AND fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
								AND tipo_mov = FALSE
						), 0) - COALESCE((
							SELECT
								inventario * precio_unidad
							FROM
								inventario_fin_mes
							WHERE
								num_cia = result_general.num_cia_traspasa
								AND codmp = 90
								AND fecha = '{$fecha}'
							LIMIT
								1
						), 0) - (pollos_vendidos * costo_gas_aprox / pollos_vendidos_aprox)
							AS costo_gas,
						COALESCE((
							SELECT
								existencia
							FROM
								historico_inventario
							WHERE
								num_cia = result_general.num_cia_traspasa
								AND codmp = 90
								AND fecha = '{$fecha_ini}'::DATE - INTERVAL '1 DAY'
						), 0) + COALESCE((
							SELECT
								SUM(cantidad)
							FROM
								mov_inv_real
							WHERE
								num_cia = result_general.num_cia_traspasa
								AND codmp = 90
								AND fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
								AND tipo_mov = FALSE
						), 0) - COALESCE((
							SELECT
								inventario
							FROM
								inventario_fin_mes
							WHERE
								num_cia = result_general.num_cia_traspasa
								AND codmp = 90
								AND fecha = '{$fecha}'
							LIMIT
								1
						), 0) - (pollos_vendidos * costo_gas_aprox / pollos_vendidos_aprox / precio_gas)
							AS cantidad_gas,
						precio_gas
					FROM
						(SELECT
							num_cia,
							num_cia_traspasa,
							pollos_vendidos,
							(
								SELECT
									num_cia
								FROM
									(
										SELECT
											mvr.num_cia,
											SUM(cantidad)
												AS pollos_vendidos,
											COALESCE((
												SELECT
													SUM(importe)
												FROM
													movimiento_gastos
												WHERE
													num_cia = mvr.num_cia
													AND fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
													AND codgastos = 90
											), (
												SELECT
													SUM(diferencia * precio_unidad)
												FROM
													inventario_fin_mes
												WHERE
													num_cia = mvr.num_cia
													AND fecha = '{$fecha}'
													AND codmp = 90
											), 0)
											AS consumo_gas
										FROM
											mov_inv_real mvr
										WHERE
											mvr.fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
											AND mvr.codmp IN (160, 334, 700, 600, 573)
											AND mvr.tipo_mov = TRUE
											AND num_cia NOT IN (
												SELECT
													ros
												FROM
													distribucion_gas
											)
											AND num_cia NOT IN (
												SELECT
													num_cia
												FROM
													distribucion_gas
											)
											AND mvr.num_cia NOT IN (312, 336, 347, 359, 378, 319, 346, 350, 421, 320, 463, 416, 345, 450, 453, 426, 355, 380, 460, 444, 318, 443, 415, 473, 470, 476, 412, 352, 348, 447)
										GROUP BY
											num_cia
										ORDER BY
											pollos_vendidos DESC
									) AS tabuladores
								WHERE
									consumo_gas > 0
									AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
								LIMIT 1
							)
								AS num_cia_aprox,
							(
								SELECT
									pollos_vendidos
								FROM
									(
										SELECT
											SUM(cantidad)
												AS pollos_vendidos,
											COALESCE((
												SELECT
													SUM(importe)
												FROM
													movimiento_gastos
												WHERE
													num_cia = mvr.num_cia
													AND fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
													AND codgastos = 90
											), (
												SELECT
													SUM(diferencia * precio_unidad)
												FROM
													inventario_fin_mes
												WHERE
													num_cia = mvr.num_cia
													AND fecha = '{$fecha}'
													AND codmp = 90
											), 0)
											AS consumo_gas
										FROM
											mov_inv_real mvr
										WHERE
											mvr.fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
											AND mvr.codmp IN (160, 334, 700, 600, 573)
											AND mvr.tipo_mov = TRUE
											AND num_cia NOT IN (
												SELECT
													ros
												FROM
													distribucion_gas
											)
											AND num_cia NOT IN (
												SELECT
													num_cia
												FROM
													distribucion_gas
											)
											AND mvr.num_cia NOT IN (312, 336, 347, 359, 378, 319, 346, 350, 421, 320, 463, 416, 345, 450, 453, 426, 355, 380, 460, 444, 318, 443, 415, 473, 470, 476, 412, 352, 348, 447)
										GROUP BY
											num_cia
										ORDER BY
											pollos_vendidos DESC) AS tabuladores
								WHERE
									consumo_gas > 0
									AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
								LIMIT 1
							)
								AS pollos_vendidos_aprox,
							(
								SELECT
									consumo_gas
								FROM
									(
										SELECT
											SUM(cantidad)
												AS pollos_vendidos,
											COALESCE((
												SELECT
													SUM(importe)
												FROM
													movimiento_gastos
												WHERE
													num_cia = mvr.num_cia
													AND fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
													AND codgastos = 90
											), (
												SELECT
													SUM(diferencia * precio_unidad)
												FROM
													inventario_fin_mes
												WHERE
													num_cia = mvr.num_cia
													AND fecha = '{$fecha}'
													AND codmp = 90
											), 0)
											AS consumo_gas
										FROM
											mov_inv_real mvr
										WHERE
											mvr.fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
											AND mvr.codmp IN (160, 334, 700, 600, 573)
											AND mvr.tipo_mov = TRUE
											AND num_cia NOT IN (
												SELECT
													ros
												FROM
													distribucion_gas
											)
											AND num_cia NOT IN (
												SELECT
													num_cia
												FROM
													distribucion_gas
											)
											AND mvr.num_cia NOT IN (312, 336, 347, 359, 378, 319, 346, 350, 421, 320, 463, 416, 345, 450, 453, 426, 355, 380, 460, 444, 318, 443, 415, 473, 470, 476, 412, 352, 348, 447)
										GROUP BY
											num_cia
										ORDER BY
											pollos_vendidos DESC
									) AS tabuladores
								WHERE
									consumo_gas > 0
									AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
								LIMIT 1
							)
								AS costo_gas_aprox,
							precio_gas
						FROM
							(
								SELECT
									dg.ros
										AS num_cia,
									mvr.num_cia
										AS num_cia_traspasa,
									ROUND(SUM(cantidad)::NUMERIC, 2)
										AS pollos_vendidos,
									COALESCE((
										SELECT
											precio_unidad
										FROM
											mov_inv_real
										WHERE
											num_cia = mvr.num_cia
											AND codmp = 90
											AND fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
											AND tipo_mov = FALSE
										ORDER BY
											fecha DESC
										LIMIT
											1
									), (
										SELECT
											precio_unidad
										FROM
											historico_inventario
										WHERE
											num_cia = mvr.num_cia
											AND codmp = 90
											AND fecha = '{$fecha_ini}'::DATE - INTERVAL '1 DAY'
											AND precio_unidad > 0
									), NULL)
										AS precio_gas
								FROM
									mov_inv_real mvr
									LEFT JOIN distribucion_gas dg
										ON (mvr.num_cia = dg.num_cia)
								WHERE
									mvr.num_cia = {$row['num_cia']}
									AND mvr.fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
									AND mvr.codmp IN (160, 334, 700, 600, 573)
									AND mvr.tipo_mov = TRUE
								GROUP BY
									mvr.num_cia,
									dg.ros
								HAVING
									SUM(cantidad) > 0
							) AS result_pollos_vendidos) AS result_general");
				}

				if ($traspaso)
				{
					// No traspasar gas si la cantidad de gas es menor o igual a cero
					if ($traspaso[0]['cantidad_gas'] <= 0)
					{
						continue;
					}

					// Ingresar entrada negativa en compañia que traspasa
					$sql .= "INSERT INTO
						mov_inv_real (
							num_cia,
							codmp,
							fecha,
							tipo_mov,
							cantidad,
							precio,
							total_mov,
							precio_unidad,
							descripcion
						) VALUES (
							{$row['num_cia']},
							90,
							'{$fecha}',
							FALSE,
							-{$traspaso[0]['cantidad_gas']},
							{$traspaso[0]['precio_gas']},
							-{$traspaso[0]['costo_gas']},
							{$traspaso[0]['precio_gas']},
							'TRASPASO GAS (CIA {$row['ros']})'
						);";

					// Ingresar entrada positiva en rosticeria
					$sql .= "INSERT INTO
						mov_inv_real (
							num_cia,
							codmp,
							fecha,
							tipo_mov,
							cantidad,
							precio,
							total_mov,
							precio_unidad,
							descripcion
						) VALUES (
							{$row['ros']},
							90,
							'{$fecha}',
							FALSE,
							{$traspaso[0]['cantidad_gas']},
							{$traspaso[0]['precio_gas']},
							{$traspaso[0]['costo_gas']},
							{$traspaso[0]['precio_gas']},
							'TRASPASO GAS (CIA {$row['num_cia']})'
						);";

					// Actualizar inventario de panaderia
					$sql .= "UPDATE
						inventario_real
					SET
						existencia = existencia - {$traspaso[0]['cantidad_gas']},
						precio_unidad = {$traspaso[0]['precio_gas']}
					WHERE
						num_cia = {$row['num_cia']}
						AND codmp = 90;";

					// Actualizar inventario de rosticeria
					$sql .= "UPDATE
						inventario_real
					SET
						existencia = existencia + {$traspaso[0]['cantidad_gas']},
						precio_unidad = {$traspaso[0]['precio_gas']}
					WHERE
						num_cia = {$row['ros']}
						AND codmp = 90;";
				}
			}
		}

		if ($sql != '')
		{
			$db->query($sql);
		}
	}

	/*
	[06-Mar-2015] Desglosar automáticamente el gas natural
	*/

	// Validar que no se hayan generado antes las distribuciones de gas natural
	$sql = "
		SELECT
			idmovimiento_gastos
		FROM
			movimiento_gastos
		WHERE
			fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
			AND codgastos = 128
			AND concepto LIKE 'TRASPASO GAS NATURAL%'
		LIMIT
			1
	";

	if ( ! $db->query($sql))
	{
		$cias_gas_natural = array(
			// 325	=> 75,
			305	=> 19,
			310	=> 27,
			324	=> 71,
			419	=> 19,
			// 417	=> 121,
			344	=> 44,
			334	=> 21,
			333	=> 73,
			331	=> 40,
			329	=> 31,
			436	=> 148,
			439	=> 143,
			321	=> 72
		);

		$sql = "
			SELECT
				num_cia,
				SUM (cantidad)
					AS cantidad
			FROM
				mov_inv_real
			WHERE
				num_cia IN (" . implode(', ', array_keys($cias_gas_natural)) . ")
				AND codmp IN (160, 334, 700, 600, 573)
				AND tipo_mov = TRUE
				AND fecha BETWEEN '{$fecha_ini}'
				AND '{$fecha}'
			GROUP BY
				num_cia
			ORDER BY
				num_cia
		";

		$result = $db->query($sql);

		if ($result)
		{
			$sql = '';

			foreach ($result as $r) {
				// Ingresar gasto negativo en panaderia
				$sql .= "
					INSERT INTO
						movimiento_gastos (
							num_cia,
							codgastos,
							fecha,
							importe,
							captura,
							concepto
						) VALUES (
							{$cias_gas_natural[$r['num_cia']]},
							128,
							'{$fecha}',
							-{$r['cantidad']} * 1.20,
							FALSE,
							'TRASPASO GAS NATURAL {$r['num_cia']}'
						);
				";

				// Ingresar gasto positivo en rosticeria
				$sql .= "
					INSERT INTO
						movimiento_gastos (
							num_cia,
							codgastos,
							fecha,
							importe,
							captura,
							concepto
						) VALUES (
							{$r['num_cia']},
							128,
							'{$fecha}',
							{$r['cantidad']} * 1.20,
							FALSE,
							'TRASPASO GAS NATURAL {$cias_gas_natural[$r['num_cia']]}'
						);
				";
			}

			$db->query($sql);
		}
	}

	// [03-Sep-2009] Actualizar existencias y costos
	$sql = '
		SELECT
			num_cia
		FROM
			inventario_fin_mes
		WHERE
			fecha = \'' . $fecha . '\'
			' . ($_GET['generar'] > 0 ? 'AND ' . ($_GET['generar'] == 1 ? 'num_cia <= 300' : '(num_cia BETWEEN 301 AND 599 OR num_cia IN (702, 704, 705, 707))') : '') . '
		GROUP BY
			num_cia
		ORDER BY
			num_cia
	';

	$cias = $db->query($sql);
	if ($cias/* && $_SESSION['iduser'] != 1*/) {
		$sql = '';

		foreach ($cias as $c)
			$sql .= ActualizarInventario($c['num_cia'], $anio, $mes, NULL, TRUE, FALSE, FALSE, TRUE);

		if ($sql != '')
			$db->query($sql);
	}

	/*$sql = '
		UPDATE
			inventario_fin_mes
		SET
			existencia = result.existencia,
			diferencia = inventario_fin_mes.existencia - inventario
		FROM (
			SELECT
				num_cia,
				codmp,
				existencia
			FROM
				inventario_real
		) result
		WHERE
			fecha = \'' . $fecha . '\'
			' . ($_GET['generar'] > 0 ? 'AND ' . ($_GET['generar'] == 1 ? 'inventario_fin_mes.num_cia <= 300' : '(inventario_fin_mes.num_cia BETWEEN 301 AND 599 OR inventario_fin_mes.num_cia IN (702, 704, 705, 707))') : '') . '
			AND inventario_fin_mes.num_cia = result.num_cia
			AND inventario_fin_mes.codmp = result.codmp
	' . ";\n";

	$db->query($sql);*/

	// [2007/01/04] Guardar historico de proveedores
	$sql = "
		INSERT INTO
			historico_proveedores (
				num_cia,
				num_fact,
				total,
				descripcion,
				fecha_mov,
				fecha_pago,
				num_proveedor,
				codgastos,
				fecha_arc
			)
			SELECT
				num_cia,
				num_fact,
				total,
				descripcion,
				fecha,
				fecha,
				num_proveedor,
				codgastos,
				'$fecha'
			FROM
				pasivo_proveedores
			" . ($_GET['generar'] > 0 ? 'WHERE ' . ($_GET['generar'] == 1 ? 'num_cia <= 300' : '(num_cia BETWEEN 301 AND 599 OR num_cia IN (702, 704, 705, 707))') : '') . "
		;\n";

	// Generar diferencias en contra
	$sql .= "
		INSERT INTO
			diferencias_inventario (
				num_cia,
				codmp,
				fecha,
				cod_turno,
				tipo_mov,
				cantidad,
				existencia,
				precio,
				total_mov,
				precio_unidad,
				descripcion
			)
			SELECT
				num_cia,
				codmp,
				'$fecha'
					AS fecha,
				NULL
					AS cod_turno,
				TRUE
					AS tipo_mov,
				ABS(diferencia)
					AS cantidad,
				0
					AS existencia,
				precio_unidad
					AS precio,
				ABS(precio_unidad * diferencia)
					AS total_mov,
				precio_unidad,
				'DIFERENCIA INVENTARIO'
					AS descripcion
			FROM
				inventario_fin_mes
			WHERE
				diferencia > 0
				AND fecha = '$fecha'
				" . ($_GET['generar'] > 0 ? 'AND ' . ($_GET['generar'] == 1 ? 'num_cia <= 300' : '(num_cia BETWEEN 301 AND 599 OR num_cia IN (702, 704, 705, 707))') : '') . "
	;\n";

	// Generar diferencias a favor
	// [02/Jul/2008] Las diferencias a favor ahora se guardaran como salidas negativas
	$sql .= "
		INSERT INTO
			diferencias_inventario (
				num_cia,
				codmp,
				fecha,
				cod_turno,
				tipo_mov,
				cantidad,
				existencia,
				precio,
				total_mov,
				precio_unidad,
				descripcion
			)
			SELECT
				num_cia,
				codmp,
				'$fecha'
					AS fecha,
				NULL
					AS cod_turno,
				TRUE
					AS tipo_mov,
				-ABS(diferencia)
					AS cantidad,
				0
					AS existencia,
				precio_unidad
					AS precio,
				-ABS(precio_unidad * diferencia)
					AS total_mov,
				precio_unidad,
				'DIFERENCIA INVENTARIO'
					AS descripcion
			FROM
				inventario_fin_mes
			WHERE
				diferencia < 0
				AND fecha = '$fecha'
				" . ($_GET['generar'] > 0 ? 'AND ' . ($_GET['generar'] == 1 ? 'num_cia <= 300' : '(num_cia BETWEEN 301 AND 599 OR num_cia IN (702, 704, 705, 707))') : '') . "
	;\n";

	// Generar movimientos de diferencia
	$sql .= "
		INSERT INTO
			mov_inv_real (
				num_cia,
				codmp,
				fecha,
				cod_turno,
				tipo_mov,
				cantidad,
				existencia,
				precio,
				total_mov,
				precio_unidad,
				descripcion
			)
			SELECT
				num_cia,
				codmp,
				fecha,
				cod_turno,
				tipo_mov,
				cantidad,
				existencia,
				precio,
				total_mov,
				precio_unidad,
				descripcion
			FROM
				diferencias_inventario
			WHERE
				fecha = '$fecha'
				" . ($_GET['generar'] > 0 ? 'AND ' . ($_GET['generar'] == 1 ? 'num_cia <= 300' : '(num_cia BETWEEN 301 AND 599 OR num_cia IN (702, 704, 705, 707))') : '') . "
	;\n";

	// Generar gastos de gas
	$sql .= "
		INSERT INTO
			movimiento_gastos (
				codgastos,
				num_cia,
				fecha,
				importe,
				concepto
			)
			SELECT
				90
					AS codgastos,
				num_cia,
				fecha,
				total_mov
					AS importe,
				descripcion
					AS concepto
			FROM
				mov_inv_real
			WHERE
				codmp = 90
				AND tipo_mov = TRUE
				AND fecha BETWEEN '$fecha_ini' AND '$fecha'
				" . ($_GET['generar'] > 0 ? '
				AND ' . ($_GET['generar'] == 1 ? 'num_cia <= 300' : '(num_cia BETWEEN 301 AND 599 OR num_cia IN (702, 704, 705, 707))') : '') . "
	;\n";

	// Actualizar inventarios (sumar cantidades a favor)
	// [02/Jul/2008] Debido a que las diferencias a favor ahora son negativas ya no es necesario correr el query de abajo ya que se actualiza en el siguiente
	//$sql .= "UPDATE inventario_real SET existencia = existencia + diferencias_inventario.cantidad WHERE num_cia = diferencias_inventario.num_cia AND";
	//$sql .= " codmp = diferencias_inventario.codmp AND diferencias_inventario.tipo_mov = 'FALSE' AND diferencias_inventario.fecha = '$fecha';\n";
	// Actualizar inventarios (restar cantidades en contra)
	// [02/Jul/2008] Ahora este query tambien actualiza con las diferencias a favor
//	$sql .= "UPDATE inventario_real SET existencia = existencia - diferencias_inventario.cantidad WHERE num_cia = diferencias_inventario.num_cia AND";
//	$sql .= " codmp = diferencias_inventario.codmp AND diferencias_inventario.tipo_mov = 'TRUE' AND diferencias_inventario.fecha = '$fecha'" . ($_GET['generar'] > 0 ? ' AND ' . ($_GET['generar'] == 1 ? 'num_cia <= 300' : '(num_cia BETWEEN 301 AND 599 OR num_cia IN (702, 704, 705, 707))') : '') . ";\n";
	$sql .= "
		UPDATE
			inventario_real
		SET
			existencia = existencia - result.cantidad
		FROM (
			SELECT
				num_cia,
				codmp,
				cantidad
			FROM
				diferencias_inventario
			WHERE
				fecha = '$fecha'
				AND tipo_mov = TRUE
		) result
		WHERE
			inventario_real.num_cia = result.num_cia
			AND inventario_real.codmp = result.codmp
			" . ($_GET['generar'] > 0 ? '
			AND ' . ($_GET['generar'] == 1 ? 'inventario_real.num_cia <= 300' : '(inventario_real.num_cia BETWEEN 301 AND 599 OR inventario_real.num_cia IN (702, 704, 705, 707))') : '') . "
	;\n";

	// Copiar inventario real al virtual
	$sql .= "
		DELETE FROM
			inventario_virtual
		" . ($_GET['generar'] > 0 ? 'WHERE ' . ($_GET['generar'] == 1 ? 'num_cia <= 300' : '(num_cia BETWEEN 301 AND 599 OR num_cia IN (702, 704, 705, 707))') : '') . "
	;\n";

	$sql .= "
		INSERT INTO
			inventario_virtual (
				num_cia,
				codmp,
				existencia,
				precio_unidad
			)
			SELECT
				num_cia,
				codmp,
				existencia,
				precio_unidad
			FROM
				inventario_real
			" . ($_GET['generar'] > 0 ? 'WHERE ' . ($_GET['generar'] == 1 ? 'num_cia <= 300' : '(num_cia BETWEEN 301 AND 599 OR num_cia IN (702, 704, 705, 707))') : '') . "
	;\n";

	// Actualizar historico de inventario
	$sql .= "
		INSERT INTO
			historico_inventario (
				num_cia,
				codmp,
				fecha,
				existencia,
				precio_unidad
			)
			SELECT
				num_cia,
				codmp,
				'$fecha'
					AS fecha,
				existencia,
				precio_unidad
			FROM
				inventario_real
			" . ($_GET['generar'] > 0 ? 'WHERE ' . ($_GET['generar'] == 1 ? 'num_cia <= 300' : '(num_cia BETWEEN 301 AND 599 OR num_cia IN (702, 704, 705, 707))') : '') . "
	;\n";

	// Borrar todos los gastos y volver a insertarlos
	$sql .= "
		DELETE FROM
			movimiento_gastos
		WHERE
			fecha BETWEEN '$fecha_ini' AND '$fecha'
			AND captura = TRUE
			AND codgastos NOT IN (179, 180, 181, 183, 187, 189, 190)
			" . ($_GET['generar'] > 0 ? 'AND ' . ($_GET['generar'] == 1 ? 'num_cia <= 300' : '(num_cia BETWEEN 301 AND 599 OR num_cia IN (702, 704, 705, 707))') : '') . "
	;\n";

	$sql .= "
		INSERT INTO
			movimiento_gastos (
				codgastos,
				num_cia,
				fecha,
				importe,
				concepto,
				captura,
				folio
			)
			SELECT
				codgastos,
				num_cia,
				fecha,
				importe,
				concepto,
				TRUE,
				folio
			FROM
				cheques
			WHERE
				fecha BETWEEN '$fecha_ini' AND '$fecha'
				AND fecha_cancelacion IS NULL
				" . ($_GET['generar'] > 0 ? 'AND ' . ($_GET['generar'] == 1 ? 'num_cia <= 300' : '(num_cia BETWEEN 301 AND 599 OR num_cia IN (702, 704, 705, 707))') : '') . "
	;\n";

	// Pasar todos los movimientos del mes a una tabla temporal
	$sql .= "
		TRUNCATE TABLE mov_inv_real_temp
	;\n";

	$sql .= "
		INSERT INTO
			mov_inv_real_temp (
				num_cia,
				codmp,
				fecha,
				cod_turno,
				tipo_mov,
				cantidad,
				precio,
				total_mov,
				precio_unidad,
				descripcion
			)
			SELECT
				num_cia,
				codmp,
				fecha,
				cod_turno,
				tipo_mov,
				cantidad,
				precio,
				total_mov,
				precio_unidad,
				descripcion
			FROM
				mov_inv_real
			WHERE
				fecha BETWEEN '$fecha_ini' AND '$fecha'
				AND num_cia <= 300
	;\n";

	// Poner bandera en tareas cron para actualizar costos en historico
	$sql .= "
		UPDATE flags SET actualizar_historico = TRUE
	;\n";

	if (/*$_SESSION['iduser'] == 1*/FALSE) {
		echo "<pre>$sql</pre>";
		die;
	}
	// Ejecutar scripts

	$db->comenzar_transaccion();
	$db->query($sql);
	$db->terminar_transaccion();

	/*
	[02-Sep-2014] Desglosar automáticamente los gastos
	[07-Ene-2015] Movido el código a la parte despues de borrar gastos y volverlos a insertar
	*/

	// Validar que no se hayan generado antes las distribuciones de gastos
	$sql = "
		SELECT
			*
		FROM
			movimiento_gastos
		WHERE
			fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
			AND codgastos IN (
				SELECT
					codgastos
				FROM
					gastos_porcentajes_distribucion
				GROUP BY
					codgastos
			)
			AND concepto LIKE 'DISTRIBUCION POR GASTO%'
		LIMIT
			1
	";

	if ( ! $db->query($sql)) {
		// $sql = "
		// 	SELECT
		// 		num_cia,
		// 		codgastos,
		// 		SUM(importe)
		// 			AS total,
		// 		ros,
		// 		porc
		// 	FROM
		// 		gastos_porcentajes_distribucion dg
		// 		LEFT JOIN movimiento_gastos g
		// 			USING (num_cia, codgastos)
		// 	WHERE
		// 		fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
		// 		AND concepto NOT LIKE 'DISTRIBUCION POR GASTO%'
		// 	GROUP BY
		// 		num_cia,
		// 		codgastos,
		// 		ros,
		// 		porc
		// 	HAVING
		// 		SUM(importe) > 0
		// 	ORDER BY
		// 		codgastos,
		// 		num_cia,
		// 		porc,
		// 		ros
		// ";
		// [05-Oct-2016] Tomar en cuenta los gastos en reserva para desglose de gastos
		/*$sql = "SELECT
			num_cia,
			codgastos,
			SUM(total) AS total,
			ros,
			porc
		FROM
		(
			SELECT
				num_cia,
				codgastos,
				SUM(importe)
					AS total,
				ros,
				porc
			FROM
				gastos_porcentajes_distribucion dg
				LEFT JOIN movimiento_gastos g
					USING (num_cia, codgastos)
			WHERE
				fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
				AND concepto NOT LIKE 'DISTRIBUCION POR GASTO%'
			GROUP BY
				num_cia,
				codgastos,
				ros,
				porc
			HAVING
				SUM(importe) != 0

			UNION ALL

			SELECT
				num_cia,
				codgastos,
				importe,
				ros,
				porc
			FROM
				gastos_porcentajes_distribucion dg
				LEFT JOIN reserva_gastos rg
					USING (num_cia, codgastos)
			WHERE
				anio = {$anio}
				AND mes = {$mes}
		) AS gastos

		GROUP BY
			num_cia,
			codgastos,
			ros,
			porc

		HAVING SUM(total) > 0

		ORDER BY
			codgastos,
			num_cia,
			porc,
			ros";*/

		// [3-Nov-2016] Tomar en cuenta pagos hechos por otras compañías
		$sql = "SELECT
			num_cia,
			codgastos,
			SUM(total) AS total,
			ros,
			porc
		FROM
		(
			SELECT
				COALESCE(poc.num_cia_aplica, g.num_cia) AS num_cia,
				g.codgastos,
				SUM(g.importe) AS total,
				dg.ros,
				dg.porc
			FROM
				movimiento_gastos g
				LEFT JOIN pagos_otras_cias poc ON (
					poc.num_cia = g.num_cia
					AND poc.fecha = g.fecha
					AND poc.folio = g.folio
				)
				LEFT JOIN cheques c ON (
					c.num_cia = poc.num_cia
					AND c.cuenta = poc.cuenta
					AND c.folio = poc.folio
					AND c.fecha = poc.fecha
					AND c.fecha_cancelacion IS NULL
				)
				LEFT JOIN gastos_porcentajes_distribucion dg ON (
					dg.num_cia = COALESCE(poc.num_cia_aplica, g.num_cia)
					AND dg.codgastos = g.codgastos
				)
			WHERE
				g.fecha BETWEEN '{$fecha_ini}' AND '{$fecha}'
				AND g.concepto NOT LIKE 'DISTRIBUCION POR GASTO%'
				AND dg.ros IS NOT NULL
				AND (g.num_cia, g.codgastos) IN (
					SELECT
						num_cia,
						codgastos
					FROM
						gastos_porcentajes_distribucion
				)
			GROUP BY
				COALESCE(poc.num_cia_aplica, g.num_cia),
				g.codgastos,
				ros,
				porc
			HAVING
				SUM(g.importe) != 0

			UNION ALL

			SELECT
				num_cia,
				codgastos,
				importe,
				ros,
				porc
			FROM
				gastos_porcentajes_distribucion dg
				LEFT JOIN reserva_gastos rg USING (num_cia, codgastos)
			WHERE
				anio = {$anio}
				AND mes = {$anio}
		) AS gastos

		GROUP BY
			num_cia,
			codgastos,
			ros,
			porc

		HAVING
			SUM(total) > 0

		ORDER BY
			codgastos,
			num_cia,
			porc,
			ros";

		$result = $db->query($sql);

		if ($result) {
			$sql = '';

			foreach ($result as $r) {
				$importe = round($r['total'] * $r['porc'] / 100, 2);

				// Ingresar gasto negativo en panaderia
				$sql .= "
					INSERT INTO
						movimiento_gastos (
							num_cia,
							codgastos,
							fecha,
							importe,
							captura,
							concepto
						) VALUES (
							{$r['num_cia']},
							{$r['codgastos']},
							'{$fecha}',
							-{$importe},
							TRUE,
							'DISTRIBUCION POR GASTO FORZADO EN EL BALANCE {$r['ros']}'
						);
				";

				// Ingresar gasto positivo en rosticeria
				$sql .= "
					INSERT INTO
						movimiento_gastos (
							num_cia,
							codgastos,
							fecha,
							importe,
							captura,
							concepto
						) VALUES (
							{$r['ros']},
							{$r['codgastos']},
							'{$fecha}',
							{$importe},
							TRUE,
							'DISTRIBUCION POR GASTO FORZADO EN EL BALANCE {$r['num_cia']}'
						);
				";
			}

			$db->query($sql);
		}
	}

	$sql = "
		INSERT INTO
			actualizacion_panas (
				num_cia,
				metodo,
				parametros,
				iduser
			)
			SELECT
				num_cia,
				'actualizar_inventario_inicio_mes',
				'num_cia=' || num_cia,
				{$_SESSION['iduser']}
			FROM
				historico_inventario
			WHERE
				num_cia <= 300
				AND fecha = '{$fecha}'
			GROUP BY
				num_cia
			ORDER BY
				num_cia
	";

	$db->query($sql);

	header("location: ./bal_act_inv_v3.php?alerta=1");
	die;
}

if (isset($_GET['accion']) && $_GET['accion'] == 2) {
	unset($_SESSION['act_inv']);
	header("location: ./bal_act_inv_v3.php");
	die;
}

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");

	$fecha = date("d/m/Y", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
	$mes = mes_escrito(date("n", mktime(0, 0, 0, date("m") - 1, 1, date("Y"))));
	$anio = date("Y", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));

	$tpl->assign("mes", $mes);
	$tpl->assign("anio", $anio);

	if (!in_array($_SESSION['iduser'], array(1, 4, 19, 25, 18)))
		$tpl->assign('disabled', ' disabled');

	if (empty($_SESSION['act_inv'])) {
		// Obtener primera compañía con diferencias
		$sql = "SELECT num_cia FROM inventario_fin_mes WHERE fecha >= '$fecha'" . ($_SESSION['iduser'] == 17 ? " AND (num_cia BETWEEN 301 AND 599 OR num_cia IN (702, 703, 704))" : '') . " ORDER BY num_cia LIMIT 1";
		$cia = $db->query($sql);
		if ($cia)
			$tpl->assign("num_cia", $cia[0]['num_cia']);
		else
			$tpl->assign("num_cia", 1);
	}
	else
		$tpl->assign("num_cia", $_SESSION['act_inv']['num_cia']);

	if (isset($_GET['alerta']))
		$tpl->newBlock("alerta");

	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
	}

	$tpl->printToScreen();
	die;
}

$fecha = date("d/m/Y", mktime(0, 0, 0, date("n") - 1, 1, date("Y")));
$fecha_fin = date('d/m/Y', mktime(0, 0, 0, date('n'), 0, date('Y')));
$mes = date("n", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
$anio = date("Y", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
$fecha0 = date('d/m/Y', mktime(0, 0, 0, $mes, 0, $anio));
$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes - 1, 0, $anio));

// [Obsoleto] Actualizar existencia en inventario_fin_mes
//$sql = "UPDATE inventario_fin_mes SET existencia = inventario_real.existencia, diferencia = inventario_real.existencia - inventario,precio_unidad = inventario_real.precio_unidad WHERE";
//$sql .= " num_cia = $_GET[num_cia] AND fecha >= '$fecha' AND num_cia = inventario_real.num_cia AND codmp = inventario_real.codmp";
//$db->query($sql);

// [03-Sep-2009] Actualizar existencias y costos
if (($sql = ActualizarInventario($_GET['num_cia'], $anio, $mes, NULL, TRUE, FALSE, FALSE, TRUE)) != '')
	$db->query($sql);

// Obtener diferencias
$sql = "SELECT
	ifm.id,
	ifm.codmp,
	cmp.nombre,
	ifm.existencia,
	ifm.inventario,
	ifm.diferencia,
	ifm.precio_unidad,
	cmp.controlada,
	(
		/*SELECT
			total_mov
		FROM
			diferencias_inventario
		WHERE
			num_cia = ifm.num_cia
			AND codmp = ifm.codmp
			AND fecha = '{$fecha0}'
		LIMIT
			1*/
		SELECT
			total_mov
		FROM
			mov_inv_real
		WHERE
			num_cia = ifm.num_cia
			AND codmp = ifm.codmp
			AND fecha = '{$fecha0}'
			AND descripcion = 'DIFERENCIA INVENTARIO'
	)
		AS dif0,
	(
		SELECT
			gas_pro
		FROM
			balances_pan
		WHERE
			num_cia = ifm.num_cia
			AND mes = EXTRACT(MONTH FROM '{$fecha0}'::DATE)
			AND anio = EXTRACT(YEAR FROM '{$fecha0}'::DATE)
	)
		AS gas_pro0,
	(
		/*SELECT
			total_mov
		FROM
			diferencias_inventario
		WHERE
			num_cia = ifm.num_cia
			AND codmp = ifm.codmp
			AND fecha = '{$fecha1}'
		LIMIT
			1*/
		SELECT
			total_mov
		FROM
			mov_inv_real
		WHERE
			num_cia = ifm.num_cia
			AND codmp = ifm.codmp
			AND fecha = '{$fecha1}'
			AND descripcion = 'DIFERENCIA INVENTARIO'
	)
		AS dif1,
	(
		SELECT
			gas_pro
		FROM
			balances_pan
		WHERE
			num_cia = ifm.num_cia
			AND mes = EXTRACT(MONTH FROM '{$fecha1}'::DATE)
			AND anio = EXTRACT(YEAR FROM '{$fecha1}'::DATE)
	)
		AS gas_pro1,
	CASE
		WHEN codmp = 90 AND COALESCE((
			SELECT
				TRUE
			FROM
				distribucion_gas
			WHERE
				num_cia = {$_GET['num_cia']}
				OR ros = {$_GET['num_cia']}
			LIMIT
				1
		), FALSE) = TRUE THEN
			'f90'
		ELSE
			'fff'
		END
			AS bgcolor,
	/*
	* [05-Dic-2013] Obtener el porcentaje de desglose de gas para la compañia
	*/
	(
		SELECT
			SUM(porc)
		FROM
			distribucion_gas
		WHERE
			num_cia = {$_REQUEST['num_cia']}
			OR ros = {$_REQUEST['num_cia']}
	)
		AS por_dis_gas,
	(
		SELECT
			CASE
				WHEN num_cia = {$_REQUEST['num_cia']} THEN
					'traspasa'
				WHEN ros = {$_REQUEST['num_cia']} THEN
					'recibe'
				ELSE
					NULL
			END
		FROM
			distribucion_gas
		WHERE
			num_cia = {$_REQUEST['num_cia']}
			OR ros = {$_REQUEST['num_cia']}
		LIMIT
			1
	)
		AS gas_tipo,
	COALESCE((
		SELECT
			TRUE
		FROM
			mov_inv_real
		WHERE
			num_cia = {$_REQUEST['num_cia']}
			AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
			AND codmp = 90
			AND tipo_mov = FALSE
			AND descripcion LIKE 'TRASPASO GAS%'
		LIMIT
			1
	), FALSE)
		AS gas_status,
	(
		SELECT
			SUM(total_produccion)
		FROM
			total_produccion
		WHERE
			numcia = {$_REQUEST['num_cia']}
			AND fecha_total BETWEEN '{$fecha}' AND '{$fecha_fin}'
	)
		AS produccion,
	(
		SELECT
			SUM(cantidad)
		FROM
			mov_inv_real
		WHERE
			num_cia = {$_REQUEST['num_cia']}
			AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
			AND codmp IN (160, 573)
			AND tipo_mov = TRUE
			AND descripcion != 'DIFERENCIA INVENTARIO'
	)
		AS pollos_vendidos,
	(
		SELECT
			SUM(cantidad)
		FROM
			mov_inv_real
		WHERE
			num_cia = {$_REQUEST['num_cia']}
			AND fecha BETWEEN '" . date('d/m/Y', mktime(0, 0, 0, $mes - 2, 1, $anio)) . "' AND '" . date('d/m/Y', mktime(0, 0, 0, $mes - 1, 0, $anio)) . "'
			AND codmp IN (160, 573)
			AND tipo_mov = TRUE
			AND descripcion != 'DIFERENCIA INVENTARIO'
	)
		AS pollos_vendidos0,
	(
		SELECT
			SUM(cantidad)
		FROM
			mov_inv_real
		WHERE
			num_cia = {$_REQUEST['num_cia']}
			AND fecha BETWEEN '" . date('d/m/Y', mktime(0, 0, 0, $mes - 3, 1, $anio)) . "' AND '" . date('d/m/Y', mktime(0, 0, 0, $mes - 2, 0, $anio)) . "'
			AND codmp IN (160, 573)
			AND tipo_mov = TRUE
			AND descripcion != 'DIFERENCIA INVENTARIO'
	)
		AS pollos_vendidos1,
	cc.tipo_cia,
	(SELECT tipo_cia FROM catalogo_companias WHERE num_cia IN (SELECT ros FROM distribucion_gas WHERE num_cia = ifm.num_cia LIMIT 1))
		AS tipo_cia_rec
FROM
	inventario_fin_mes ifm
	LEFT JOIN catalogo_mat_primas cmp
		USING (codmp)
	LEFT JOIN catalogo_companias cc
		USING (num_cia)
WHERE
	num_cia = {$_GET['num_cia']}
	AND fecha >= '{$fecha}'
	AND no_exi = FALSE";
// [02-Sep-2008] Solo mostrar productos controlados que hayan tenido consumo los ultimos 2 meses
if ($_GET['tipo'] == 'TRUE') {
	$sql .= " AND (codmp IN (SELECT codmp FROM mov_inv_real LEFT JOIN catalogo_mat_primas USING (codmp) WHERE controlada = 'TRUE' AND num_cia = $_GET[num_cia] AND fecha BETWEEN '$fecha_fin'::date - interval '2 months' AND '$fecha_fin' AND tipo_mov = 'TRUE' GROUP BY codmp) OR diferencia <> 0)";
}
$sql .= $_GET['tipo'] != "" ? " AND controlada = '$_GET[tipo]'" : "";
$sql .= " ORDER BY num_cia, CASE WHEN codmp = 90 THEN 9999 WHEN codmp IN (1, 3, 4, 44, 45, 47, 49, 148) THEN 1 WHEN codmp IN (112, 1020, 77, 1039, 67, 1060, 1061) THEN 2 WHEN codmp IN (187, 38, 742, 1072, 1073, 1087, 1088) THEN 3 WHEN codmp IN (561, 51, 398, 234, 1019, 973) THEN 4 WHEN codmp IN (856, 426, 486, 279, 150, 744, 756, 760, 765, 769, 774, 206, 121, 779, 806, 811, 407, 60, 68, 1027) THEN 5 WHEN codmp IN (433, 242, 28, 229) THEN 6 WHEN codmp IN (156, 310, 179) THEN 93 WHEN codmp = 311 THEN 95 WHEN codmp = 170 THEN 94 ELSE 90 END ASC, controlada DESC, CASE WHEN codmp IN (1, 3, 4, 44, 45, 47, 49, 148, 156, 310, 179, 311, 170) THEN codmp::numeric ELSE ((inventario - existencia) * precio_unidad)::numeric END";
$result = $db->query($sql);//if ($_SESSION['iduser'] == 1)echo '<pre>' . $sql . "\n" . print_r($result, true) . '</pre>';

// [03-Junio-2008] Obtener consumos de los últimos 3 meses
//$sql = "SELECT extract(year from fecha) AS anio, extract(month from fecha) AS mes, sum(cantidad * inv.precio_unidad) AS consumo FROM mov_inv_real mov LEFT JOIN inventario_real inv USING (num_cia, codmp) LEFT JOIN catalogo_mat_primas cmp USING (codmp) WHERE num_cia = $_GET[num_cia] AND fecha BETWEEN '$fecha'::date - interval '5 months' AND '$fecha_fin' AND tipo_mov = 'TRUE' AND codmp NOT IN (90, 128)";
//$sql .= $_GET['tipo'] != '' ? " AND controlada = '$_GET[tipo]'" : '';
//$sql .= ' GROUP BY anio, mes ORDER BY anio ASC, mes ASC';
// [03-Abr-2009] Obtener consumos de los datos de balance
$consumos = FALSE;
if ($_GET['num_cia'] <= 300) {
	$sql = "SELECT anio, mes, mat_prima_utilizada AS consumo, mercancias, CASE WHEN produccion_total > 0 THEN mat_prima_utilizada / produccion_total ELSE 0 END AS mp_pro, produccion_total FROM balances_pan WHERE num_cia = $_GET[num_cia] AND fecha BETWEEN '$fecha'::date - interval '5 months' AND '$fecha_fin' ORDER BY anio, mes";
	$consumos = $db->query($sql);
}
//$consumos = FALSE;

$tpl->newBlock("listado");
$tpl->assign("tipo", $_GET['tipo']);
$nombre = $db->query("SELECT nombre_corto, nombre_operadora FROM catalogo_companias LEFT JOIN catalogo_operadoras USING (idoperadora) WHERE num_cia = $_GET[num_cia]");
$tpl->assign("num_cia", $_GET['num_cia']);
$tpl->assign("nombre", $nombre[0]['nombre_corto']);
$tpl->assign("mes", mes_escrito($mes,TRUE));
$tpl->assign("anio", $anio);
$tpl->assign('operadora', $nombre[0]['nombre_operadora']);

$tpl->assign("mes_obs", $mes);
$tpl->assign("anio_obs", $anio);

if ($obs = $db->query("SELECT observaciones FROM observaciones_diferencias_inventario WHERE num_cia = {$_REQUEST['num_cia']} AND anio = {$anio} AND mes = {$mes}")) {
	$tpl->assign('observaciones', $obs[0]['observaciones']);
}

// Obtener siguiente compañía en el listado de diferencias
$sql = "SELECT num_cia, nombre_corto FROM inventario_fin_mes JOIN catalogo_companias USING (num_cia) WHERE num_cia > $_GET[num_cia]" . ($_SESSION['iduser'] == 17 ? " AND (num_cia BETWEEN 301 AND 599 OR num_cia IN (702, 703, 704))" : '') . " AND fecha >= '$fecha' ORDER BY num_cia LIMIT 1";
$cia = $db->query($sql);
if ($cia) {
	$tpl->assign("num_cia_next", $cia[0]['num_cia']);
	$tpl->assign("nombre_next", $cia[0]['nombre_corto']);
	$_SESSION['act_inv']['num_cia'] = $cia[0]['num_cia'];
}
else {
	// Obtener primera compañía con diferencias
	$sql = "SELECT num_cia, nombre_corto FROM inventario_fin_mes JOIN catalogo_companias USING (num_cia) WHERE fecha >= '$fecha'" . ($_SESSION['iduser'] == 17 ? " AND (num_cia BETWEEN 301 AND 599 OR num_cia IN (702, 703, 704))" : '') . " ORDER BY num_cia LIMIT 1";
	$cia = $db->query($sql);
	if ($cia) {
		$tpl->assign("num_cia_next", $cia[0]['num_cia']);
		$tpl->assign("nombre_next", $cia[0]['nombre_corto']);
		$_SESSION['act_inv']['num_cia'] = $cia[0]['num_cia'];
	}
}

// Listado de compañías
$sql = "SELECT num_cia, nombre_corto FROM inventario_fin_mes LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha >= '$fecha'" . ($_SESSION['iduser'] == 17 ? " AND (num_cia BETWEEN 301 AND 599 OR num_cia IN (702, 703, 704))" : '') . " GROUP BY num_cia, nombre_corto ORDER BY num_cia";
$cias = $db->query($sql);
foreach ($cias as $cia) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia['num_cia']);
	$tpl->assign("nombre", $cia['nombre_corto']);
}

$cia = NULL;
$total = 0;
$favor = 0;
$contra = 0;

// [02-Julio-2008] Almacena el consumo total de materia prima no controlada
$nc = 0;
$gas = 0;
$i = 0;
foreach ($result as $reg) {
	if ($reg['codmp'] == 90 && $reg['gas_status'] == 'f')
	{
		$distribuciones = $db->query("SELECT CASE WHEN num_cia <= 300 THEN 1 ELSE 2 END AS traspasa, CASE WHEN ros <= 300 THEN 1 ELSE 2 END AS recibe, CASE WHEN num_cia = {$_REQUEST['num_cia']} THEN 'traspasa' WHEN ros = {$_REQUEST['num_cia']} THEN 'recibe' END AS gas_tipo FROM distribucion_gas WHERE num_cia = {$_REQUEST['num_cia']} OR ros = {$_REQUEST['num_cia']} GROUP BY gas_tipo, traspasa, recibe ORDER BY gas_tipo, traspasa, recibe");

		if ($distribuciones)
		{//print_r($distribuciones);
			$costo_inicial = $reg['existencia'] * $reg['precio_unidad'];

			$cantidad_gas_traspasa = 0;
			$costo_gas_traspasa = 0;

			$cantidad_gas_recibe = 0;
			$costo_gas_recibe = 0;

			$info_distribucion = '<table align="center" id="info_tip"><tr><th colspan="2">Existencia sin traspaso</th></tr><tr><th>Cantidad</th><th>Costo</th></tr>';
			$info_distribucion .= '<tr><td align="right">' . number_format($reg['existencia'], 2) . '</td><td align="right">' . number_format($costo_inicial, 2) . '</td></tr></table><br />';

			foreach ($distribuciones as $dist)
			{//print_r($dist);
				if ($dist['gas_tipo'] == 'traspasa')
				{
					if (($reg['tipo_cia'] == 1 || $reg['tipo_cia'] == 2) && $dist['recibe'] == 2)
					{//echo "TRASPASA: {$reg['tipo_cia']}-{$dist['recibe']}<br>";
						$sql = "SELECT
							num_cia,
							(
								SELECT
									nombre_corto
								FROM
									catalogo_companias
								WHERE
									num_cia = result_general.num_cia
							)
								AS nombre_cia,
							pollos_vendidos,
							pollos_vendidos * costo_gas_aprox / pollos_vendidos_aprox
								AS costo_gas,
							pollos_vendidos * costo_gas_aprox / pollos_vendidos_aprox / precio_gas
								AS cantidad_gas,
							num_cia_aprox,
							(
								SELECT
									nombre_corto
								FROM
									catalogo_companias
								WHERE
									num_cia = result_general.num_cia_aprox
							)
								AS nombre_cia_aprox,
							pollos_vendidos_aprox,
							costo_gas_aprox,
							precio_gas
						FROM
							(SELECT
								num_cia,
								pollos_vendidos,
								(
									SELECT
										num_cia
									FROM
										(
											SELECT
												mvr.num_cia,
												SUM(cantidad)
													AS pollos_vendidos,
												COALESCE((
													SELECT
														SUM(importe)
													FROM
														movimiento_gastos
													WHERE
														num_cia = mvr.num_cia
														AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
														AND codgastos = 90
												), (
													SELECT
														SUM(diferencia * precio_unidad)
													FROM
														inventario_fin_mes
													WHERE
														num_cia = mvr.num_cia
														AND fecha = '{$fecha_fin}'
														AND codmp = 90
												), 0)
												AS consumo_gas
											FROM
												mov_inv_real mvr
											WHERE
												mvr.fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
												AND mvr.codmp IN (160, 334, 700, 600, 573)
												AND mvr.tipo_mov = TRUE
												AND num_cia NOT IN (
													SELECT
														ros
													FROM
														distribucion_gas
												)
												AND num_cia NOT IN (
													SELECT
														num_cia
													FROM
														distribucion_gas
												)
												AND mvr.num_cia NOT IN (312, 336, 347, 359, 378, 319, 346, 350, 421, 320, 463, 416, 345, 450, 453, 426, 355, 380, 460, 444, 318, 443, 415, 473, 470, 476, 412, 352, 348, 447)
											GROUP BY
												num_cia
											ORDER BY
												pollos_vendidos DESC
										) AS tabuladores
									WHERE
										consumo_gas > 0
										AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
									LIMIT 1
								)
									AS num_cia_aprox,
								(
									SELECT
										pollos_vendidos
									FROM
										(
											SELECT
												SUM(cantidad)
													AS pollos_vendidos,
												COALESCE((
													SELECT
														SUM(importe)
													FROM
														movimiento_gastos
													WHERE
														num_cia = mvr.num_cia
														AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
														AND codgastos = 90
												), (
													SELECT
														SUM(diferencia * precio_unidad)
													FROM
														inventario_fin_mes
													WHERE
														num_cia = mvr.num_cia
														AND fecha = '{$fecha_fin}'
														AND codmp = 90
												), 0)
												AS consumo_gas
											FROM
												mov_inv_real mvr
											WHERE
												mvr.fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
												AND mvr.codmp IN (160, 334, 700, 600, 573)
												AND mvr.tipo_mov = TRUE
												AND num_cia NOT IN (
													SELECT
														ros
													FROM
														distribucion_gas
												)
												AND num_cia NOT IN (
													SELECT
														num_cia
													FROM
														distribucion_gas
												)
												AND mvr.num_cia NOT IN (312, 336, 347, 359, 378, 319, 346, 350, 421, 320, 463, 416, 345, 450, 453, 426, 355, 380, 460, 444, 318, 443, 415, 473, 470, 476, 412, 352, 348, 447)
											GROUP BY
												num_cia
											ORDER BY
												pollos_vendidos DESC) AS tabuladores
									WHERE
										consumo_gas > 0
										AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
									LIMIT 1
								)
									AS pollos_vendidos_aprox,
								(
									SELECT
										consumo_gas
									FROM
										(
											SELECT
												SUM(cantidad)
													AS pollos_vendidos,
												COALESCE((
													SELECT
														SUM(importe)
													FROM
														movimiento_gastos
													WHERE
														num_cia = mvr.num_cia
														AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
														AND codgastos = 90
												), (
													SELECT
														SUM(diferencia * precio_unidad)
													FROM
														inventario_fin_mes
													WHERE
														num_cia = mvr.num_cia
														AND fecha = '{$fecha_fin}'
														AND codmp = 90
												), 0)
												AS consumo_gas
											FROM
												mov_inv_real mvr
											WHERE
												mvr.fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
												AND mvr.codmp IN (160, 334, 700, 600, 573)
												AND mvr.tipo_mov = TRUE
												AND num_cia NOT IN (
													SELECT
														ros
													FROM
														distribucion_gas
												)
												AND num_cia NOT IN (
													SELECT
														num_cia
													FROM
														distribucion_gas
												)
												AND mvr.num_cia NOT IN (312, 336, 347, 359, 378, 319, 346, 350, 421, 320, 463, 416, 345, 450, 453, 426, 355, 380, 460, 444, 318, 443, 415, 473, 470, 476, 412, 352, 348, 447)
											GROUP BY
												num_cia
											ORDER BY
												pollos_vendidos DESC
										) AS tabuladores
									WHERE
										consumo_gas > 0
										AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
									LIMIT 1
								)
									AS costo_gas_aprox,
								/*COALESCE(precio_gas, (
									SELECT
										precio_gas
									FROM
										(
											SELECT
												SUM(cantidad)
													AS pollos_vendidos,
												COALESCE((
													SELECT
														SUM(importe)
													FROM
														movimiento_gastos
													WHERE
														num_cia = mvr.num_cia
														AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
														AND codgastos = 90
												), (
													SELECT
														SUM(diferencia * precio_unidad)
													FROM
														inventario_fin_mes
													WHERE
														num_cia = mvr.num_cia
														AND fecha = '{$fecha_fin}'
														AND codmp = 90
												), 0)
												AS consumo_gas,
												COALESCE(precio_gas, (
													SELECT
														precio_unidad
													FROM
														inventario_fin_mes
													WHERE
														num_cia = mvr.num_cia
														AND fecha = '{$fecha_fin}'
														AND codmp = 90
														AND precio_unidad > 0
													LIMIT
														1
												), (
													SELECT
														precio_unidad
													FROM
														mov_inv_real
													WHERE
														num_cia = mvr.num_cia
														AND codmp = 90
														AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
														AND tipo_mov = FALSE
														AND precio_unidad > 0
													ORDER BY
														fecha DESC
													LIMIT
														1
												), (
													SELECT
														precio_unidad
													FROM
														historico_inventario
													WHERE
														num_cia = mvr.num_cia
														AND codmp = 90
														AND fecha = '{$fecha}'::DATE - INTERVAL '1 DAY'
														AND precio_unidad > 0
													LIMIT
														1
												), NULL)
													AS precio_gas
											FROM
												mov_inv_real mvr
											WHERE
												mvr.fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
												AND mvr.codmp IN (160, 334, 700, 600, 573)
												AND mvr.tipo_mov = TRUE
												AND num_cia NOT IN (
													SELECT
														ros
													FROM
														distribucion_gas
												)
												AND num_cia NOT IN (
													SELECT
														num_cia
													FROM
														distribucion_gas
												)
											GROUP BY
												num_cia
											ORDER BY
												pollos_vendidos DESC
										) AS tabuladores
									WHERE
										consumo_gas > 0
										AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
									LIMIT 1
								))
									AS */precio_gas
							FROM
								(
									SELECT
										dg.ros
											AS num_cia,
										ROUND(SUM(cantidad)::NUMERIC, 2)
											AS pollos_vendidos,
										COALESCE((
											SELECT
												precio_unidad
											FROM
												mov_inv_real
											WHERE
												num_cia = dg.num_cia
												AND codmp = 90
												AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
												AND tipo_mov = FALSE
											ORDER BY
												fecha DESC
											LIMIT
												1
										), (
											SELECT
												precio_unidad
											FROM
												historico_inventario
											WHERE
												num_cia = dg.num_cia
												AND codmp = 90
												AND fecha = '{$fecha}'::DATE - INTERVAL '1 DAY'
												AND precio_unidad > 0
										), NULL)
											AS precio_gas
									FROM
										mov_inv_real mvr
										LEFT JOIN distribucion_gas dg
											ON (mvr.num_cia = dg.ros)
									WHERE
										dg.num_cia = {$_REQUEST['num_cia']}
										AND mvr.fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
										AND mvr.codmp IN (160, 334, 700, 600, 573)
										AND mvr.tipo_mov = TRUE
										AND dg.ros > 300
										AND dg.ros NOT IN (325, 305, 310, 324, 419, 417, 344, 333, 331, 329, 436, 439, 321)
									GROUP BY
										dg.ros,
										dg.num_cia
									HAVING
										SUM(cantidad) > 0
								) AS result_pollos_vendidos) AS result_general";
					}
					else if ($reg['tipo_cia'] == 1 && $dist['recibe'] == 1)
					{//echo "TRASPASA: {$reg['tipo_cia']}-{$dist['recibe']}<br>";
						$sql = "SELECT
							dg.ros
								AS num_cia,
							(
								SELECT
									nombre_corto
								FROM
									catalogo_companias
								WHERE
									num_cia = dg.ros
							)
								AS nombre_cia,
							NULL
								AS pollos_vendidos,
							diferencia * precio_unidad
								AS costo_gas,
							diferencia
								AS cantidad_gas,
							NULL
								num_cia_aprox,
							NULL
								AS nombre_cia_aprox,
							0
								AS pollos_vendidos_aprox,
							0
								AS costo_gas_aprox,
							precio_unidad
								AS precio_gas
						FROM
							inventario_fin_mes ifm
							LEFT JOIN distribucion_gas dg
								ON (dg.num_cia = ifm.num_cia)
						WHERE
							dg.num_cia = {$_REQUEST['num_cia']}
							AND dg.ros <= 300
							AND ifm.fecha = '{$fecha_fin}'
							AND ifm.codmp = 90
							AND diferencia > 0
						LIMIT
							1";
					}
					else if ($reg['tipo_cia'] == 2 && $dist['recibe'] == 1)
					{//echo "TRASPASA: {$reg['tipo_cia']}-{$dist['recibe']}<br>";
						$sql = "SELECT
							num_cia,
							(
								SELECT
									nombre_corto
								FROM
									catalogo_companias
								WHERE
									num_cia = result_general.num_cia
							)
								AS nombre_cia,
							pollos_vendidos,
							COALESCE((
								SELECT
									existencia * precio_unidad
								FROM
									historico_inventario
								WHERE
									num_cia = result_general.num_cia_traspasa
									AND codmp = 90
									AND fecha = '{$fecha}'::DATE - INTERVAL '1 DAY'
							), 0) + COALESCE((
								SELECT
									SUM(total_mov)
								FROM
									mov_inv_real
								WHERE
									num_cia = result_general.num_cia_traspasa
									AND codmp = 90
									AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
									AND tipo_mov = FALSE
							), 0) - COALESCE((
								SELECT
									inventario * precio_unidad
								FROM
									inventario_fin_mes
								WHERE
									num_cia = result_general.num_cia_traspasa
									AND codmp = 90
									AND fecha = '{$fecha_fin}'
								LIMIT
									1
							), 0) - (pollos_vendidos * costo_gas_aprox / pollos_vendidos_aprox)
								AS costo_gas,
							COALESCE((
								SELECT
									existencia
								FROM
									historico_inventario
								WHERE
									num_cia = result_general.num_cia_traspasa
									AND codmp = 90
									AND fecha = '{$fecha}'::DATE - INTERVAL '1 DAY'
							), 0) + COALESCE((
								SELECT
									SUM(cantidad)
								FROM
									mov_inv_real
								WHERE
									num_cia = result_general.num_cia_traspasa
									AND codmp = 90
									AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
									AND tipo_mov = FALSE
							), 0) - COALESCE((
								SELECT
									inventario
								FROM
									inventario_fin_mes
								WHERE
									num_cia = result_general.num_cia_traspasa
									AND codmp = 90
									AND fecha = '{$fecha_fin}'
								LIMIT
									1
							), 0) - (pollos_vendidos * costo_gas_aprox / pollos_vendidos_aprox / precio_gas)
								AS cantidad_gas,
							num_cia_aprox,
							(
								SELECT
									nombre_corto
								FROM
									catalogo_companias
								WHERE
									num_cia = result_general.num_cia_aprox
							)
								AS nombre_cia_aprox,
							pollos_vendidos_aprox,
							costo_gas_aprox,
							precio_gas
						FROM
							(SELECT
								num_cia,
								num_cia_traspasa,
								pollos_vendidos,
								(
									SELECT
										num_cia
									FROM
										(
											SELECT
												mvr.num_cia,
												SUM(cantidad)
													AS pollos_vendidos,
												COALESCE((
													SELECT
														SUM(importe)
													FROM
														movimiento_gastos
													WHERE
														num_cia = mvr.num_cia
														AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
														AND codgastos = 90
												), (
													SELECT
														SUM(diferencia * precio_unidad)
													FROM
														inventario_fin_mes
													WHERE
														num_cia = mvr.num_cia
														AND fecha = '{$fecha_fin}'
														AND codmp = 90
												), 0)
												AS consumo_gas
											FROM
												mov_inv_real mvr
											WHERE
												mvr.fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
												AND mvr.codmp IN (160, 334, 700, 600, 573)
												AND mvr.tipo_mov = TRUE
												AND num_cia NOT IN (
													SELECT
														ros
													FROM
														distribucion_gas
												)
												AND num_cia NOT IN (
													SELECT
														num_cia
													FROM
														distribucion_gas
												)
												AND mvr.num_cia NOT IN (312, 336, 347, 359, 378, 319, 346, 350, 421, 320, 463, 416, 345, 450, 453, 426, 355, 380, 460, 444, 318, 443, 415, 473, 470, 476, 412, 352, 348, 447)
											GROUP BY
												num_cia
											ORDER BY
												pollos_vendidos DESC
										) AS tabuladores
									WHERE
										consumo_gas > 0
										AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
									LIMIT 1
								)
									AS num_cia_aprox,
								(
									SELECT
										pollos_vendidos
									FROM
										(
											SELECT
												SUM(cantidad)
													AS pollos_vendidos,
												COALESCE((
													SELECT
														SUM(importe)
													FROM
														movimiento_gastos
													WHERE
														num_cia = mvr.num_cia
														AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
														AND codgastos = 90
												), (
													SELECT
														SUM(diferencia * precio_unidad)
													FROM
														inventario_fin_mes
													WHERE
														num_cia = mvr.num_cia
														AND fecha = '{$fecha_fin}'
														AND codmp = 90
												), 0)
												AS consumo_gas
											FROM
												mov_inv_real mvr
											WHERE
												mvr.fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
												AND mvr.codmp IN (160, 334, 700, 600, 573)
												AND mvr.tipo_mov = TRUE
												AND num_cia NOT IN (
													SELECT
														ros
													FROM
														distribucion_gas
												)
												AND num_cia NOT IN (
													SELECT
														num_cia
													FROM
														distribucion_gas
												)
												AND mvr.num_cia NOT IN (312, 336, 347, 359, 378, 319, 346, 350, 421, 320, 463, 416, 345, 450, 453, 426, 355, 380, 460, 444, 318, 443, 415, 473, 470, 476, 412, 352, 348, 447)
											GROUP BY
												num_cia
											ORDER BY
												pollos_vendidos DESC) AS tabuladores
									WHERE
										consumo_gas > 0
										AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
									LIMIT 1
								)
									AS pollos_vendidos_aprox,
								(
									SELECT
										consumo_gas
									FROM
										(
											SELECT
												SUM(cantidad)
													AS pollos_vendidos,
												COALESCE((
													SELECT
														SUM(importe)
													FROM
														movimiento_gastos
													WHERE
														num_cia = mvr.num_cia
														AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
														AND codgastos = 90
												), (
													SELECT
														SUM(diferencia * precio_unidad)
													FROM
														inventario_fin_mes
													WHERE
														num_cia = mvr.num_cia
														AND fecha = '{$fecha_fin}'
														AND codmp = 90
												), 0)
												AS consumo_gas
											FROM
												mov_inv_real mvr
											WHERE
												mvr.fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
												AND mvr.codmp IN (160, 334, 700, 600, 573)
												AND mvr.tipo_mov = TRUE
												AND num_cia NOT IN (
													SELECT
														ros
													FROM
														distribucion_gas
												)
												AND num_cia NOT IN (
													SELECT
														num_cia
													FROM
														distribucion_gas
												)
												AND mvr.num_cia NOT IN (312, 336, 347, 359, 378, 319, 346, 350, 421, 320, 463, 416, 345, 450, 453, 426, 355, 380, 460, 444, 318, 443, 415, 473, 470, 476, 412, 352, 348, 447)
											GROUP BY
												num_cia
											ORDER BY
												pollos_vendidos DESC
										) AS tabuladores
									WHERE
										consumo_gas > 0
										AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
									LIMIT 1
								)
									AS costo_gas_aprox,
								precio_gas
							FROM
								(
									SELECT
										dg.ros
											AS num_cia,
										mvr.num_cia
											AS num_cia_traspasa,
										ROUND(SUM(cantidad)::NUMERIC, 2)
											AS pollos_vendidos,
										COALESCE((
											SELECT
												precio_unidad
											FROM
												mov_inv_real
											WHERE
												num_cia = mvr.num_cia
												AND codmp = 90
												AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
												AND tipo_mov = FALSE
											ORDER BY
												fecha DESC
											LIMIT
												1
										), (
											SELECT
												precio_unidad
											FROM
												historico_inventario
											WHERE
												num_cia = mvr.num_cia
												AND codmp = 90
												AND fecha = '{$fecha}'::DATE - INTERVAL '1 DAY'
												AND precio_unidad > 0
										), NULL)
											AS precio_gas
									FROM
										mov_inv_real mvr
										LEFT JOIN distribucion_gas dg
											ON (mvr.num_cia = dg.num_cia)
									WHERE
										mvr.num_cia = {$_REQUEST['num_cia']}
										AND mvr.fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
										AND mvr.codmp IN (160, 334, 700, 600, 573)
										AND mvr.tipo_mov = TRUE
									GROUP BY
										mvr.num_cia,
										dg.ros
									HAVING
										SUM(cantidad) > 0
								) AS result_pollos_vendidos) AS result_general";
					}

					$costo_gas = $db->query($sql);

					if ($costo_gas)
					{
						$info_distribucion .= '<table align="center" id="info_tip"><tr><th colspan="4">Compa&ntilde;&iacute;a en revisi&oacute;n</th><th colspan="3">Tabulador</th></tr><tr><th>Traspasa a</th><th>Cantidad de gas</th><th>Costo</th><th>Pollos vendidos</th><th>Compa&ntilde;&iacute;a</th><th>Pollos vendidos</th><th>Costo de gas</th></tr>';

						$pollos_vendidos = 0;

						foreach ($costo_gas as $c) {
							// echo "TRASPASA {$reg['existencia']}, {$c['cantidad_gas']}, {$c['precio_gas']}";
							// $reg['existencia'] -= $c['cantidad_gas'];

							$cantidad_gas_traspasa += $c['cantidad_gas'] > 0 ? $c['cantidad_gas'] : 0;
							$costo_gas_traspasa += $c['cantidad_gas'] > 0 ? $c['costo_gas'] : 0;
							$pollos_vendidos += $c['pollos_vendidos'];

							if ($c['cantidad_gas'] > 0)
							{
								$info_distribucion .= "<tr><td>{$c['num_cia']} {$c['nombre_cia']}</td><td align=\"right\" style=\"color:#C00;\">" . number_format($c['cantidad_gas'], 2) . "</td><td align=\"right\" style=\"color:#C00;\">" . number_format($c['costo_gas'], 2) . "</td><td align=\"right\" style=\"color:#C00;\">" . number_format($c['pollos_vendidos']) . "</td><td>{$c['num_cia_aprox']} {$c['nombre_cia_aprox']}</td><td align=\"right\" style=\"color:#0C0;\">" . number_format($c['pollos_vendidos_aprox']) . "</td><td align=\"right\" style=\"color:#0C0;\">" . number_format($c['costo_gas_aprox'], 2) . "</td></tr>";
							}
							else
							{
								$info_distribucion .= "<tr><td>{$c['num_cia']} {$c['nombre_cia']}</td><td colspan=\"2\" style=\"color:#C00;\">No traspaso gas</td><td align=\"right\" style=\"color:#C00;\">" . number_format($c['pollos_vendidos']) . "</td><td>{$c['num_cia_aprox']} {$c['nombre_cia_aprox']}</td><td align=\"right\" style=\"color:#0C0;\">" . number_format($c['pollos_vendidos_aprox']) . "</td><td align=\"right\" style=\"color:#0C0;\">" . number_format($c['costo_gas_aprox'], 2) . "</td></tr>";
							}
						}

						$info_distribucion .= '<tr><th align="right">Traspasado</th><th align="right">' . number_format($cantidad_gas_traspasa, 2) . '</th><th align="right">' . number_format($costo_gas_traspasa, 2) . '</th><th align="right">' . number_format($pollos_vendidos) . '</th><th colspan="3"></th></tr></tr></table><br />';
					}
				}
				else if ($dist['gas_tipo'] == 'recibe')
				{
					if ($reg['tipo_cia'] == 2 && ($dist['traspasa'] == 1 || $dist['traspasa'] == 2))
					{//echo "RECIBE: {$reg['tipo_cia']}-{$dist['traspasa']}<br>";
						$sql = "SELECT
							num_cia,
							(
								SELECT
									nombre_corto
								FROM
									catalogo_companias
								WHERE
									num_cia = result_general.num_cia
							)
								AS nombre_cia,
							pollos_vendidos,
							pollos_vendidos * costo_gas_aprox / pollos_vendidos_aprox
								AS costo_gas,
							pollos_vendidos * costo_gas_aprox / pollos_vendidos_aprox / precio_gas
								AS cantidad_gas,
							num_cia_aprox,
							(
								SELECT
									nombre_corto
								FROM
									catalogo_companias
								WHERE
									num_cia = result_general.num_cia_aprox
							)
								AS nombre_cia_aprox,
							pollos_vendidos_aprox,
							costo_gas_aprox,
							precio_gas
						FROM
							(SELECT
								num_cia,
								pollos_vendidos,
								(
									SELECT
										num_cia
									FROM
										(
											SELECT
												mvr.num_cia,
												SUM(cantidad)
													AS pollos_vendidos,
												COALESCE((
													SELECT
														SUM(importe)
													FROM
														movimiento_gastos
													WHERE
														num_cia = mvr.num_cia
														AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
														AND codgastos = 90
												), (
													SELECT
														SUM(diferencia * precio_unidad)
													FROM
														inventario_fin_mes
													WHERE
														num_cia = mvr.num_cia
														AND fecha = '{$fecha_fin}'
														AND codmp = 90
												), 0)
												AS consumo_gas
											FROM
												mov_inv_real mvr
											WHERE
												mvr.fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
												AND mvr.codmp IN (160, 334, 700, 600, 573)
												AND mvr.tipo_mov = TRUE
												AND num_cia NOT IN (
													SELECT
														ros
													FROM
														distribucion_gas
												)
												AND num_cia NOT IN (
													SELECT
														num_cia
													FROM
														distribucion_gas
												)
												AND mvr.num_cia NOT IN (312, 336, 347, 359, 378, 319, 346, 350, 421, 320, 463, 416, 345, 450, 453, 426, 355, 380, 460, 444, 318, 443, 415, 473, 470, 476, 412, 352, 348, 447)
											GROUP BY
												num_cia
											ORDER BY
												pollos_vendidos DESC
										) AS tabuladores
									WHERE
										consumo_gas > 0
										AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
									LIMIT 1
								)
									AS num_cia_aprox,
								(
									SELECT
										pollos_vendidos
									FROM
										(
											SELECT
												SUM(cantidad)
													AS pollos_vendidos,
												COALESCE((
													SELECT
														SUM(importe)
													FROM
														movimiento_gastos
													WHERE
														num_cia = mvr.num_cia
														AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
														AND codgastos = 90
												), (
													SELECT
														SUM(diferencia * precio_unidad)
													FROM
														inventario_fin_mes
													WHERE
														num_cia = mvr.num_cia
														AND fecha = '{$fecha_fin}'
														AND codmp = 90
												), 0)
												AS consumo_gas
											FROM
												mov_inv_real mvr
											WHERE
												mvr.fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
												AND mvr.codmp IN (160, 334, 700, 600, 573)
												AND mvr.tipo_mov = TRUE
												AND num_cia NOT IN (
													SELECT
														ros
													FROM
														distribucion_gas
												)
												AND num_cia NOT IN (
													SELECT
														num_cia
													FROM
														distribucion_gas
												)
												AND mvr.num_cia NOT IN (312, 336, 347, 359, 378, 319, 346, 350, 421, 320, 463, 416, 345, 450, 453, 426, 355, 380, 460, 444, 318, 443, 415, 473, 470, 476, 412, 352, 348, 447)
											GROUP BY
												num_cia
											ORDER BY
												pollos_vendidos DESC) AS tabuladores
									WHERE
										consumo_gas > 0
										AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
									LIMIT 1
								)
									AS pollos_vendidos_aprox,
								(
									SELECT
										consumo_gas
									FROM
										(
											SELECT
												SUM(cantidad)
													AS pollos_vendidos,
												COALESCE((
													SELECT
														SUM(importe)
													FROM
														movimiento_gastos
													WHERE
														num_cia = mvr.num_cia
														AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
														AND codgastos = 90
												), (
													SELECT
														SUM(diferencia * precio_unidad)
													FROM
														inventario_fin_mes
													WHERE
														num_cia = mvr.num_cia
														AND fecha = '{$fecha_fin}'
														AND codmp = 90
												), 0)
												AS consumo_gas
											FROM
												mov_inv_real mvr
											WHERE
												mvr.fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
												AND mvr.codmp IN (160, 334, 700, 600, 573)
												AND mvr.tipo_mov = TRUE
												AND num_cia NOT IN (
													SELECT
														ros
													FROM
														distribucion_gas
												)
												AND num_cia NOT IN (
													SELECT
														num_cia
													FROM
														distribucion_gas
												)
												AND mvr.num_cia NOT IN (312, 336, 347, 359, 378, 319, 346, 350, 421, 320, 463, 416, 345, 450, 453, 426, 355, 380, 460, 444, 318, 443, 415, 473, 470, 476, 412, 352, 348, 447)
											GROUP BY
												num_cia
											ORDER BY
												pollos_vendidos DESC
										) AS tabuladores
									WHERE
										consumo_gas > 0
										AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
									LIMIT 1
								)
									AS costo_gas_aprox,
								/*COALESCE(precio_gas, (
									SELECT
										precio_gas
									FROM
										(
											SELECT
												SUM(cantidad)
													AS pollos_vendidos,
												COALESCE((
													SELECT
														SUM(importe)
													FROM
														movimiento_gastos
													WHERE
														num_cia = mvr.num_cia
														AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
														AND codgastos = 90
												), (
													SELECT
														SUM(diferencia * precio_unidad)
													FROM
														inventario_fin_mes
													WHERE
														num_cia = mvr.num_cia
														AND fecha = '{$fecha_fin}'
														AND codmp = 90
												), 0)
												AS consumo_gas,
												COALESCE(precio_gas, (
													SELECT
														precio_unidad
													FROM
														inventario_fin_mes
													WHERE
														num_cia = mvr.num_cia
														AND fecha = '{$fecha_fin}'
														AND codmp = 90
														AND precio_unidad > 0
													LIMIT
														1
												), (
													SELECT
														precio_unidad
													FROM
														mov_inv_real
													WHERE
														num_cia = mvr.num_cia
														AND codmp = 90
														AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
														AND tipo_mov = FALSE
														AND precio_unidad > 0
													ORDER BY
														fecha DESC
													LIMIT
														1
												), (
													SELECT
														precio_unidad
													FROM
														historico_inventario
													WHERE
														num_cia = mvr.num_cia
														AND codmp = 90
														AND fecha = '{$fecha}'::DATE - INTERVAL '1 DAY'
														AND precio_unidad > 0
													LIMIT
														1
												), NULL)
													AS precio_gas
											FROM
												mov_inv_real mvr
											WHERE
												mvr.fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
												AND mvr.codmp IN (160, 334, 700, 600, 573)
												AND mvr.tipo_mov = TRUE
												AND num_cia NOT IN (
													SELECT
														ros
													FROM
														distribucion_gas
												)
												AND num_cia NOT IN (
													SELECT
														num_cia
													FROM
														distribucion_gas
												)
											GROUP BY
												num_cia
											ORDER BY
												pollos_vendidos DESC
										) AS tabuladores
									WHERE
										consumo_gas > 0
										AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
									LIMIT 1
								))
									AS */precio_gas
							FROM
								(
									SELECT
										dg.num_cia,
										ROUND(SUM(cantidad)::NUMERIC, 2)
											AS pollos_vendidos,
										COALESCE((
											SELECT
												precio_unidad
											FROM
												mov_inv_real
											WHERE
												num_cia = dg.num_cia
												AND codmp = 90
												AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
												AND tipo_mov = FALSE
											ORDER BY
												fecha DESC
											LIMIT
												1
										), (
											SELECT
												precio_unidad
											FROM
												historico_inventario
											WHERE
												num_cia = dg.num_cia
												AND codmp = 90
												AND fecha = '{$fecha}'::DATE - INTERVAL '1 DAY'
												AND precio_unidad > 0
										), NULL)
											AS precio_gas
									FROM
										mov_inv_real mvr
										LEFT JOIN distribucion_gas dg
											ON (mvr.num_cia = dg.ros)
									WHERE
										dg.ros = {$_REQUEST['num_cia']}
										AND dg.num_cia <= 300
										AND mvr.fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
										AND mvr.codmp IN (160, 334, 700, 600, 573)
										AND mvr.tipo_mov = TRUE
										AND dg.ros NOT IN (325, 305, 310, 324, 419, 417, 344, 333, 331, 329, 436, 439, 321)
									GROUP BY
										dg.ros,
										dg.num_cia
									HAVING
										SUM(cantidad) > 0
								) AS result_pollos_vendidos) AS result_general";
					}
					else if ($reg['tipo_cia'] == 1 && $dist['traspasa'] == 1)
					{//echo "RECIBE: {$reg['tipo_cia']}-{$dist['traspasa']}<br>";
						$sql = "SELECT
							dg.num_cia,
							(
								SELECT
									nombre_corto
								FROM
									catalogo_companias
								WHERE
									num_cia = dg.num_cia
							)
								AS nombre_cia,
							NULL
								AS pollos_vendidos,
							diferencia * precio_unidad
								AS costo_gas,
							diferencia
								AS cantidad_gas,
							NULL
								num_cia_aprox,
							NULL
								AS nombre_cia_aprox,
							0
								AS pollos_vendidos_aprox,
							0
								AS costo_gas_aprox,
							precio_unidad
								AS precio_gas
						FROM
							inventario_fin_mes ifm
							LEFT JOIN distribucion_gas dg
								ON (dg.num_cia = ifm.num_cia)
						WHERE
							dg.ros = {$_REQUEST['num_cia']}
							AND dg.num_cia <= 300
							AND ifm.fecha = '{$fecha_fin}'
							AND ifm.codmp = 90
							AND diferencia > 0
						LIMIT
							1";
					}
					else if ($reg['tipo_cia'] == 1 || $dist['traspasa'] == 2)
					{//echo "RECIBE: {$reg['tipo_cia']}-{$dist['traspasa']}<br>";
						$sql = "SELECT
							num_cia,
							(
								SELECT
									nombre_corto
								FROM
									catalogo_companias
								WHERE
									num_cia = result_general.num_cia
							)
								AS nombre_cia,
							pollos_vendidos,
							COALESCE((
								SELECT
									existencia * precio_unidad
								FROM
									historico_inventario
								WHERE
									num_cia = result_general.num_cia
									AND codmp = 90
									AND fecha = '{$fecha}'::DATE - INTERVAL '1 DAY'
							), 0) + COALESCE((
								SELECT
									SUM(total_mov)
								FROM
									mov_inv_real
								WHERE
									num_cia = result_general.num_cia
									AND codmp = 90
									AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
									AND tipo_mov = FALSE
							), 0) - COALESCE((
								SELECT
									inventario * precio_unidad
								FROM
									inventario_fin_mes
								WHERE
									num_cia = result_general.num_cia
									AND codmp = 90
									AND fecha = '{$fecha_fin}'
								LIMIT
									1
							), 0) - (pollos_vendidos * costo_gas_aprox / pollos_vendidos_aprox)
								AS costo_gas,
							COALESCE((
								SELECT
									existencia
								FROM
									historico_inventario
								WHERE
									num_cia = result_general.num_cia
									AND codmp = 90
									AND fecha = '{$fecha}'::DATE - INTERVAL '1 DAY'
							), 0) + COALESCE((
								SELECT
									SUM(cantidad)
								FROM
									mov_inv_real
								WHERE
									num_cia = result_general.num_cia
									AND codmp = 90
									AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
									AND tipo_mov = FALSE
							), 0) - COALESCE((
								SELECT
									inventario
								FROM
									inventario_fin_mes
								WHERE
									num_cia = result_general.num_cia
									AND codmp = 90
									AND fecha = '{$fecha_fin}'
								LIMIT
									1
							), 0) - (pollos_vendidos * costo_gas_aprox / pollos_vendidos_aprox / precio_gas)
								AS cantidad_gas,
							num_cia_aprox,
							(
								SELECT
									nombre_corto
								FROM
									catalogo_companias
								WHERE
									num_cia = result_general.num_cia_aprox
							)
								AS nombre_cia_aprox,
							pollos_vendidos_aprox,
							costo_gas_aprox,
							precio_gas
						FROM
							(SELECT
								num_cia,
								pollos_vendidos,
								(
									SELECT
										num_cia
									FROM
										(
											SELECT
												mvr.num_cia,
												SUM(cantidad)
													AS pollos_vendidos,
												COALESCE((
													SELECT
														SUM(importe)
													FROM
														movimiento_gastos
													WHERE
														num_cia = mvr.num_cia
														AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
														AND codgastos = 90
												), (
													SELECT
														SUM(diferencia * precio_unidad)
													FROM
														inventario_fin_mes
													WHERE
														num_cia = mvr.num_cia
														AND fecha = '{$fecha_fin}'
														AND codmp = 90
												), 0)
												AS consumo_gas
											FROM
												mov_inv_real mvr
											WHERE
												mvr.fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
												AND mvr.codmp IN (160, 334, 700, 600, 573)
												AND mvr.tipo_mov = TRUE
												AND num_cia NOT IN (
													SELECT
														ros
													FROM
														distribucion_gas
												)
												AND num_cia NOT IN (
													SELECT
														num_cia
													FROM
														distribucion_gas
												)
												AND mvr.num_cia NOT IN (312, 336, 347, 359, 378, 319, 346, 350, 421, 320, 463, 416, 345, 450, 453, 426, 355, 380, 460, 444, 318, 443, 415, 473, 470, 476, 412, 352, 348, 447)
											GROUP BY
												num_cia
											ORDER BY
												pollos_vendidos DESC
										) AS tabuladores
									WHERE
										consumo_gas > 0
										AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
									LIMIT 1
								)
									AS num_cia_aprox,
								(
									SELECT
										pollos_vendidos
									FROM
										(
											SELECT
												SUM(cantidad)
													AS pollos_vendidos,
												COALESCE((
													SELECT
														SUM(importe)
													FROM
														movimiento_gastos
													WHERE
														num_cia = mvr.num_cia
														AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
														AND codgastos = 90
												), (
													SELECT
														SUM(diferencia * precio_unidad)
													FROM
														inventario_fin_mes
													WHERE
														num_cia = mvr.num_cia
														AND fecha = '{$fecha_fin}'
														AND codmp = 90
												), 0)
												AS consumo_gas
											FROM
												mov_inv_real mvr
											WHERE
												mvr.fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
												AND mvr.codmp IN (160, 334, 700, 600, 573)
												AND mvr.tipo_mov = TRUE
												AND num_cia NOT IN (
													SELECT
														ros
													FROM
														distribucion_gas
												)
												AND num_cia NOT IN (
													SELECT
														num_cia
													FROM
														distribucion_gas
												)
												AND mvr.num_cia NOT IN (312, 336, 347, 359, 378, 319, 346, 350, 421, 320, 463, 416, 345, 450, 453, 426, 355, 380, 460, 444, 318, 443, 415, 473, 470, 476, 412, 352, 348, 447)
											GROUP BY
												num_cia
											ORDER BY
												pollos_vendidos DESC) AS tabuladores
									WHERE
										consumo_gas > 0
										AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
									LIMIT 1
								)
									AS pollos_vendidos_aprox,
								(
									SELECT
										consumo_gas
									FROM
										(
											SELECT
												SUM(cantidad)
													AS pollos_vendidos,
												COALESCE((
													SELECT
														SUM(importe)
													FROM
														movimiento_gastos
													WHERE
														num_cia = mvr.num_cia
														AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
														AND codgastos = 90
												), (
													SELECT
														SUM(diferencia * precio_unidad)
													FROM
														inventario_fin_mes
													WHERE
														num_cia = mvr.num_cia
														AND fecha = '{$fecha_fin}'
														AND codmp = 90
												), 0)
												AS consumo_gas
											FROM
												mov_inv_real mvr
											WHERE
												mvr.fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
												AND mvr.codmp IN (160, 334, 700, 600, 573)
												AND mvr.tipo_mov = TRUE
												AND num_cia NOT IN (
													SELECT
														ros
													FROM
														distribucion_gas
												)
												AND num_cia NOT IN (
													SELECT
														num_cia
													FROM
														distribucion_gas
												)
												AND mvr.num_cia NOT IN (312, 336, 347, 359, 378, 319, 346, 350, 421, 320, 463, 416, 345, 450, 453, 426, 355, 380, 460, 444, 318, 443, 415, 473, 470, 476, 412, 352, 348, 447)
											GROUP BY
												num_cia
											ORDER BY
												pollos_vendidos DESC
										) AS tabuladores
									WHERE
										consumo_gas > 0
										AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
									LIMIT 1
								)
									AS costo_gas_aprox,
								/*COALESCE(precio_gas, (
									SELECT
										precio_gas
									FROM
										(
											SELECT
												SUM(cantidad)
													AS pollos_vendidos,
												COALESCE((
													SELECT
														SUM(importe)
													FROM
														movimiento_gastos
													WHERE
														num_cia = mvr.num_cia
														AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
														AND codgastos = 90
												), (
													SELECT
														SUM(diferencia * precio_unidad)
													FROM
														inventario_fin_mes
													WHERE
														num_cia = mvr.num_cia
														AND fecha = '{$fecha_fin}'
														AND codmp = 90
												), 0)
												AS consumo_gas,
												COALESCE(precio_gas, (
													SELECT
														precio_unidad
													FROM
														inventario_fin_mes
													WHERE
														num_cia = mvr.num_cia
														AND fecha = '{$fecha_fin}'
														AND codmp = 90
														AND precio_unidad > 0
													LIMIT
														1
												), (
													SELECT
														precio_unidad
													FROM
														mov_inv_real
													WHERE
														num_cia = mvr.num_cia
														AND codmp = 90
														AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
														AND tipo_mov = FALSE
														AND precio_unidad > 0
													ORDER BY
														fecha DESC
													LIMIT
														1
												), (
													SELECT
														precio_unidad
													FROM
														historico_inventario
													WHERE
														num_cia = mvr.num_cia
														AND codmp = 90
														AND fecha = '{$fecha}'::DATE - INTERVAL '1 DAY'
														AND precio_unidad > 0
													LIMIT
														1
												), NULL)
													AS precio_gas
											FROM
												mov_inv_real mvr
											WHERE
												mvr.fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
												AND mvr.codmp IN (160, 334, 700, 600, 573)
												AND mvr.tipo_mov = TRUE
												AND num_cia NOT IN (
													SELECT
														ros
													FROM
														distribucion_gas
												)
												AND num_cia NOT IN (
													SELECT
														num_cia
													FROM
														distribucion_gas
												)
											GROUP BY
												num_cia
											ORDER BY
												pollos_vendidos DESC
										) AS tabuladores
									WHERE
										consumo_gas > 0
										AND result_pollos_vendidos.pollos_vendidos >= pollos_vendidos
									LIMIT 1
								))
									AS */precio_gas
							FROM
								(
									SELECT
										dg.num_cia,
										ROUND(SUM(cantidad)::NUMERIC, 2)
											AS pollos_vendidos,
										COALESCE((
											SELECT
												precio_unidad
											FROM
												mov_inv_real
											WHERE
												num_cia = dg.num_cia
												AND codmp = 90
												AND fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
												AND tipo_mov = FALSE
											ORDER BY
												fecha DESC
											LIMIT
												1
										), (
											SELECT
												precio_unidad
											FROM
												historico_inventario
											WHERE
												num_cia = dg.num_cia
												AND codmp = 90
												AND fecha = '{$fecha}'::DATE - INTERVAL '1 DAY'
												AND precio_unidad > 0
										), NULL)
											AS precio_gas
									FROM
										mov_inv_real mvr
										LEFT JOIN distribucion_gas dg
											ON (mvr.num_cia = dg.num_cia)
									WHERE
										dg.ros = {$_REQUEST['num_cia']}
										AND mvr.fecha BETWEEN '{$fecha}' AND '{$fecha_fin}'
										AND mvr.codmp IN (160, 334, 700, 600, 573)
										AND mvr.tipo_mov = TRUE
										AND dg.ros NOT IN (325, 305, 310, 324, 419, 417, 344, 333, 331, 329, 436, 439, 321)
									GROUP BY
										dg.ros,
										dg.num_cia
									HAVING
										SUM(cantidad) > 0
								) AS result_pollos_vendidos) AS result_general";
					}

					$costo_gas = $db->query($sql);

					if ($costo_gas)
					{
						$info_distribucion .= '<table align="center" id="info_tip"><tr><th colspan="4">Compa&ntilde;&iacute;a en revisi&oacute;n</th><th colspan="3">Tabulador</th></tr><tr><th>Recibe de</th><th>Cantidad de gas</th><th>Costo</th><th>Pollos vendidos</th><th>Compa&ntilde;&iacute;a</th><th>Pollos vendidos</th><th>Costo de gas</th></tr>';

						$pollos_vendidos = 0;

						foreach ($costo_gas as $c) {
							// echo "RECIBE {$reg['existencia']}, {$c['cantidad_gas']}, {$c['precio_gas']}";
							// $reg['existencia'] += $c['cantidad_gas'];

							$cantidad_gas_recibe += $c['cantidad_gas'] > 0 ? $c['cantidad_gas'] : 0;
							$costo_gas_recibe += $c['cantidad_gas'] > 0 ? $c['costo_gas'] : 0;
							$pollos_vendidos += $c['pollos_vendidos'];

							if ($c['cantidad_gas'] > 0)
							{
								$info_distribucion .= "<tr><td>{$c['num_cia']} {$c['nombre_cia']}</td><td align=\"right\" style=\"color:#00C;\">" . number_format($c['cantidad_gas'], 2) . "</td><td align=\"right\" style=\"color:#00C;\">" . number_format($c['costo_gas'], 2) . "</td><td align=\"right\" style=\"color:#00C;\">" . number_format($c['pollos_vendidos']) . "</td><td>{$c['num_cia_aprox']} {$c['nombre_cia_aprox']}</td><td align=\"right\" style=\"color:#0C0;\">" . number_format($c['pollos_vendidos_aprox']) . "</td><td align=\"right\" style=\"color:#0C0;\">" . number_format($c['costo_gas_aprox'], 2) . "</td></tr>";
							}
							else
							{
								$info_distribucion .= "<tr><td>{$c['num_cia']} {$c['nombre_cia']}</td><td colspan=\"2\" style=\"color:#C00;\">No recibio gas</td><td align=\"right\" style=\"color:#00C;\">" . number_format($c['pollos_vendidos']) . "</td><td>{$c['num_cia_aprox']} {$c['nombre_cia_aprox']}</td><td align=\"right\" style=\"color:#0C0;\">" . number_format($c['pollos_vendidos_aprox']) . "</td><td align=\"right\" style=\"color:#0C0;\">" . number_format($c['costo_gas_aprox'], 2) . "</td></tr>";
							}
						}

						$info_distribucion .= '<tr><th align="right">Recibido</th><th align="right">' . number_format($cantidad_gas_recibe, 2) . '</th><th align="right">' . number_format($costo_gas_recibe, 2) . '</th><th align="right">' . number_format($pollos_vendidos) . '</th><th colspan="3"></th></tr></table><br />';
					}
				}
			}

			$reg['existencia'] += $cantidad_gas_recibe - $cantidad_gas_traspasa;
			$reg['precio_unidad'] = $reg['existencia'] > 0 ? ($costo_inicial + $costo_gas_recibe - $costo_gas_traspasa) / $reg['existencia'] : 0;

			$info_distribucion .= '<table align="center" id="info_tip"><tr><th colspan="2">Existencia con traspaso</th></tr><tr><th>Cantidad</th><th>Costo</th></tr>';
			$info_distribucion .= "<tr><td align=\"right\">" . number_format($reg['existencia'], 2) . "</td><td align=\"right\">" . number_format($reg['existencia'] * $reg['precio_unidad'], 2) . "</td></tr>";
		}
	}

	if (round($reg['inventario'] - $reg['existencia'], 2) != 0 || ($reg['codmp'] == 90/* && $reg['gas_status'] == 'f' && $reg['por_dis_gas'] > 0*/)) {
		$tpl->newBlock("fila");
		$tpl->assign("i", $i);
		$tpl->assign('bgcolor', $reg['bgcolor']);
		$tpl->assign("id", $reg['id']);
		$tpl->assign("num_cia", $_GET['num_cia']);
		$tpl->assign("mes", date("n", mktime(0, 0, 0, date("n"), 0, date("Y"))));
		$tpl->assign("anio", date("Y", mktime(0, 0, 0, date("n"), 0, date("Y"))));
		$tpl->assign("codmp", $reg['codmp']);
		$tpl->assign("nombre", $reg['nombre']);
		$tpl->assign("color_mp", $reg['controlada'] == "TRUE" ? "0000CC" : "993300");
		$tpl->assign("color_exi", round($reg['existencia'], 2) >= 0 ? "000000" : "CC0000");

		$tpl->assign("existencia", round($reg['existencia'], 2) != 0 ? number_format($reg['existencia'], 2, ".", ",") : "");
		$tpl->assign("inventario", round($reg['inventario'], 2) != 0 ? number_format($reg['inventario'], 2, ".", ",") : "");
		$dif = $reg['inventario'] - $reg['existencia'];
		$tpl->assign("falta", $dif < 0 ? number_format(abs($dif), 2, ".", ",") : "");
		$tpl->assign("sobra", $dif > 0 ? number_format(abs($dif), 2, ".", ",") : "");
		$tpl->assign("costo", number_format($reg['precio_unidad'], 4, ".", ","));
		$tpl->assign("color_t", $dif < 0 ? "CC0000" : "0000CC");
		$tpl->assign("total", number_format(abs($dif * $reg['precio_unidad']), 2, ".", ","));
		$tpl->assign('dif0', $reg['dif0'] != 0 ? ('<span style="color:#' . ($reg['dif0'] < 0 ? '00C' : 'C00') . ';">' . number_format(abs($reg['dif0']), 2, ".", ",") . '</span>') : '&nbsp;');
		$tpl->assign('dif1', $reg['dif1'] != 0 ? ('<span style="color:#' . ($reg['dif1'] < 0 ? '00C' : 'C00') . ';">' . number_format(abs($reg['dif1']), 2, ".", ",") . '</span>') : '&nbsp;');

		if ($reg['codmp'] == 90 && $_REQUEST['num_cia'] <= 300)
		{
			$tpl->assign('gas_pro', $reg['produccion'] > 0 ? '<span style="float:left;font-size:8pt;font-weight:bold;">(' . (number_format(abs($dif) * $reg['precio_unidad'] / $reg['produccion'], 5)) . ')</span>&nbsp;&nbsp;' : '');
			$tpl->assign('gas_pro_0', $reg['gas_pro0'] > 0 ? '<span style="float:left;font-size:8pt;font-weight:bold;">(' . (number_format($reg['gas_pro0'], 5)) . ')</span>&nbsp;&nbsp;' : '');
			$tpl->assign('gas_pro_1', $reg['gas_pro1'] > 0 ? '<span style="float:left;font-size:8pt;font-weight:bold;">(' . (number_format($reg['gas_pro1'], 5)) . ')</span>&nbsp;&nbsp;' : '');
		}
		else if ($reg['codmp'] == 90 && ($_REQUEST['num_cia'] > 300 && $_REQUEST['num_cia'] < 600))
		{
			$tpl->assign('gas_pro', $reg['pollos_vendidos'] > 0 ? '<span style="float:left;font-size:8pt;font-weight:bold;">($' . (number_format(abs($dif) * $reg['precio_unidad'] / $reg['pollos_vendidos'], 2)) . ' - ' . number_format($reg['pollos_vendidos']) . ')</span>&nbsp;&nbsp;' : '');
			$tpl->assign('gas_pro_0', $reg['pollos_vendidos0'] > 0 ? '<span style="float:left;font-size:8pt;font-weight:bold;">($' . (number_format(abs($reg['dif0']) / $reg['pollos_vendidos0'], 2)) . ' - ' . number_format($reg['pollos_vendidos0']) . ')</span>&nbsp;&nbsp;' : '');
			$tpl->assign('gas_pro_1', $reg['pollos_vendidos1'] > 0 ? '<span style="float:left;font-size:8pt;font-weight:bold;">($' . (number_format(abs($reg['dif1']) / $reg['pollos_vendidos1'], 2)) . ' - ' . number_format($reg['pollos_vendidos1']) . ')</span>&nbsp;&nbsp;' : '');
		}

		if (in_array($reg['codmp'], array(1, 3, 4, 148))) {
			$tpl->assign('dif_bultos', floor($dif / ($reg['codmp'] == 1 ? 44 : ($reg['codmp'] == 148 ? 360 : 50))) != 0 ? ('<span style="color:#' . ($dif < 0 ? 'C00' : '00C') . ';">' . ($reg['codmp'] == 1 ? number_format(floor($dif / 44), 0, '.', ',') : ($reg['codmp'] == 148 ? number_format(floor($dif / 360), 0, '.', ',') : number_format(floor($dif / 50), 0, '.', ','))) . ' ' . ($reg['codmp'] == 148 ? 'CAJAS' : 'BULTOS') . '</span>') : '');
		}
		else {
			$tpl->assign('dif_bultos', '');
		}

		$i++;

		if (in_array($reg['codmp'], array(90, 128)))
			$gas += $dif * round($reg['precio_unidad'], 4);

		$total += $dif * round($reg['precio_unidad'], 4);
		if ($dif < 0)
			$contra += $dif * round($reg['precio_unidad'], 4);
		else
			$favor += $dif * round($reg['precio_unidad'], 4);

		if ($reg['controlada'] != 'TRUE' && !in_array($reg['codmp'], array(90, 128)))
			$nc += $dif * round($reg['precio_unidad'], 4);

		if ($reg['codmp'] == 90)
		{
			if (isset($info_distribucion) && $info_distribucion != '')
			{
				$tpl->assign('info_distribucion', ' <img src="iconos/info.png" width="16" height="16" id="info" data-info="' . htmlentities($info_distribucion) . '" />');
			}

			$desglose_gas = $db->query("
				SELECT
					ct.num_tanque
						AS num,
					ct.nombre,
					COALESCE((
						SELECT
							cantidad
						FROM
							tanques_gas_lecturas_fin_mes_tmp
						WHERE
							idtanque = ct.id
							AND fecha = '{$fecha_fin}'
						LIMIT
							1
					), -1)
						AS lectura
				FROM
					catalogo_tanques ct
				WHERE
					ct.num_cia = {$_REQUEST['num_cia']}
				ORDER BY
					ct.num_tanque
			");

			if ($desglose_gas)
			{
				$info_desglose = '<table id="info_tip"><tr><th>Tanque</th><th>Lectura</th></tr>';

				$total_gas = 0;

				foreach ($desglose_gas as $des) {
					$info_desglose .= "<tr><td>{$des['num']} {$des['nombre']}</td><td align=\"right\">" . ($des['lectura'] < 0 ? '<span style="color:#C00;">NO TOMARON LECTURA</span>' : number_format($des['lectura'])) . "</td></tr>";

					$total_gas += $des['lectura'] > 0 ? $des['lectura'] : 0;
				}

				$info_desglose .= '<tr><th align="right">Total</th><th align="right">' . number_format($total_gas) . '</th></tr>';
				$info_desglose .= '</table>';

				$tpl->assign('info_tanques', ' <img src="iconos/info.png" width="16" height="16" id="info" data-info="' . htmlentities($info_desglose) . '" />');
			}

		}
	}
}

$tpl->assign("listado.contra", $contra != 0 ? number_format(abs($contra), 2, ".", ",") : "");
$tpl->assign("listado.favor", $favor != 0 ? number_format($favor, 2, ".", ",") : "");
$tpl->assign("listado.color_gt", round($total, 2) >= 0 ? "0000CC" : "CC0000");
$tpl->assign("listado.total", number_format(abs($total), 2, ".", ","));

if ($consumos) {
	foreach ($consumos as $con) {
		$tpl->newBlock('consumo_ant');
		$tpl->assign('mes', mes_escrito($con['mes']));
		$tpl->assign('anio', $con['anio']);
		$tpl->assign('pro', number_format($con['produccion_total'], 2, '.', ','));
		$tpl->assign('mer', number_format($con['mercancias'], 2, '.', ','));
		$tpl->assign('consumo', number_format($con['consumo'], 2, '.', ','));
		$tpl->assign('con_pro', number_format($con['mp_pro'], 4));
	}

	$sql = "SELECT sum(cantidad * inv.precio_unidad) AS consumo FROM mov_inv_real mov LEFT JOIN inventario_real inv USING (num_cia, codmp) LEFT JOIN catalogo_mat_primas cmp USING (codmp) WHERE num_cia = $_GET[num_cia] AND fecha BETWEEN '$fecha' AND '$fecha_fin' AND tipo_mov = 'TRUE' AND codmp NOT IN (90, 128)";
	$consumo_mes = $db->query($sql);

	$sql = "SELECT SUM(mercancias) AS mercancias FROM (SELECT sum(importe) AS mercancias FROM movimiento_gastos WHERE num_cia = $_GET[num_cia] AND fecha BETWEEN '$fecha' AND '$fecha_fin' AND codgastos = 23 UNION SELECT SUM(importe) FROM gastos_caja WHERE num_cia = $_GET[num_cia] AND fecha BETWEEN '$fecha' AND '$fecha_fin' AND cod_gastos = 154) result";
	$mercancias = $db->query($sql);

	$sql = "SELECT sum(total_produccion) AS produccion FROM total_produccion WHERE numcia = $_GET[num_cia] AND fecha_total BETWEEN '$fecha' AND '$fecha_fin'";
	$pro = $db->query($sql);

	$tpl->assign('listado.dif_pro', $pro[0]['produccion'] != 0 ? '%' . number_format(abs($total) * 100 / $pro[0]['produccion'], 4) : '&nbsp;');

	$tpl->newBlock('consumo_ant');
	$tpl->assign('mes', mes_escrito($mes));
	$tpl->assign('anio', $anio);
	$tpl->assign('pro', number_format($pro[0]['produccion'], 2, '.', ','));
	$tpl->assign('mer', number_format($mercancias[0]['mercancias'], 2, '.', ','));
	if ($consumo_mes[0]['consumo'] != 0) {
		$tpl->assign('consumo', number_format($consumo_mes[0]['consumo'] + $mercancias[0]['mercancias'] + ($total < 0 ? abs($total) : 0) - ($gas < 0 ? abs($gas) : 0), 2, '.', ','));
		$tpl->assign('con_pro', number_format(($consumo_mes[0]['consumo'] + $mercancias[0]['mercancias'] + ($total < 0 ? abs($total) : 0) - ($gas < 0 ? abs($gas) : 0)) / $pro[0]['produccion'], 4));
	}
	else {
		$tpl->assign('consumo', $nc < 0 ? number_format(abs($nc), 2, '.', ',') : '&nbsp;');
		$tpl->assign('con_pro', $nc < 0 && $pro[0]['produccion'] > 0 ? number_format(abs($nc / $pro[0]['produccion']), 2, '.', ',') : '&nbsp;');
	}
}

$sql = "
	SELECT
		num_tanque
			AS num,
		nombre,
		capacidad,
		capacidad * 0.90
			AS capacidad_90
	FROM
		catalogo_tanques
	WHERE
		num_cia = {$_REQUEST['num_cia']}
	ORDER BY
		num_tanque
";

$tanques = $db->query($sql);

if ($tanques)
{
	foreach ($tanques as $tanque) {
		$tpl->newBlock('tanque_gas');
		$tpl->assign('num', $tanque['num']);
		$tpl->assign('nombre', $tanque['nombre']);
		$tpl->assign('capacidad', number_format($tanque['capacidad']));
		$tpl->assign('capacidad_90', number_format($tanque['capacidad_90']));
	}
}

$tpl->printToScreen();
?>
