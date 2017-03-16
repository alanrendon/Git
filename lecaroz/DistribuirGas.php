<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

// Obtener compañía
if (isset($_GET['c'])) {
	$sql = '
		SELECT
			nombre_corto
				AS
					nombre
		FROM
			catalogo_companias
		WHERE
				(
						num_cia
							BETWEEN
									1
								AND
									300
					OR
						num_cia
							IN
								(
									702,
									704,
									705
								)
				)
			AND
				num_cia = ' . $_GET['c'];
	$result = $db->query($sql);
	
	if ($result)
		echo trim(str_replace('ROSTICERIA', '', $result[0]['nombre']));
	
	die;
}

if (isset($_GET['anio'])) {
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_GET['mes'], 1, $_GET['anio']));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
	
	$sql = '
		SELECT
			num_cia,
			fecha,
			cantidad,
			precio_unidad,
			ros,
			porc
		FROM
				distribucion_gas
					dg
			LEFT JOIN
				mov_inv_real
					mov
						USING
							(
								num_cia
							)
		WHERE
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'';
	if ($_GET['num_cia'] > 0)
		$sql .= '
			AND
				num_cia = ' . $_GET['num_cia'] . '
		';
	$sql .= '
			AND
				codmp = 90
			AND
				tipo_mov = \'FALSE\'
			 AND
			 	descripcion
					NOT LIKE
						\'TRASPASO GAS%\'
		ORDER BY
		num_cia,
		fecha
	';
	$result = $db->query($sql);
	
	if (!$result)
		die(header('location: DistribuirGas.php?error=1'));
	
	$sql = '';
	foreach ($result as $r) {
		// [04-Jun-2009] Claúsula Especial. Si la panaderia es la 29, 30 o 59 solo tomar las entradas con más de 500 litros
		if (in_array($r['num_cia'], array(29, 30, 59)) && $r['cantidad'] < 500)
			continue;
		
		$cantidad = round($r['cantidad'] * $r['porc'] / 100, 2);
		$costo = round($cantidad * $r['precio_unidad'], 2);
		
		// Ingresar entrada negativa en panaderia
		$sql .= '
			INSERT INTO
				mov_inv_real
					(
						num_cia,
						codmp,
						fecha,
						tipo_mov,
						cantidad,
						precio,
						total_mov,
						precio_unidad,
						descripcion
					)
				VALUES
					(
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
				mov_inv_real
					(
						num_cia,
						codmp,
						fecha,
						tipo_mov,
						cantidad,
						precio,
						total_mov,
						precio_unidad,
						descripcion
					)
				VALUES
					(
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
				AND
					codmp = 90
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
				AND
					codmp = 90
		' . ";\n";
	}
	
	$db->query($sql);
	
	die(header('location: DistribuirGas.php'));
}

$tpl = new TemplatePower('plantillas/bal/DistribuirGas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));
$tpl->assign(date('n', mktime(0, 0, 0, date('n'), 0, date('Y'))), ' selected');

$tpl->printToScreen();
?>