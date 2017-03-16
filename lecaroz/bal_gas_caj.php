<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die('Modificando');

if (isset($_GET['list'])) {
	$sql = '
		SELECT
			num_cia,
			nombre_corto
				AS
					nombre,
			fecha,
			cod_gastos,
			descripcion,
			comentario,
			clave_balance
				AS
					bal,
			tipo_mov,
			importe
		FROM
				gastos_caja
					gc
			LEFT JOIN
				catalogo_companias
					cc
						USING
							(
								num_cia
							)
			LEFT JOIN
				catalogo_gastos_caja
					cgc
						ON
							(
								cgc.id = gc.cod_gastos
							)
		WHERE
			imp = \'TRUE\'
		ORDER BY
			num_cia,
			descripcion
	';
	
	$result = $db->query($sql);
	
	if (!$result)
		die;
	
	$db->query('UPDATE gastos_caja SET imp = \'FALSE\' WHERE imp = \'TRUE\'');
	
	$tpl = new TemplatePower('plantillas/bal/listado_gastos_caja.tpl');
	$tpl->prepare();
	
	$tpl->assign('oficina', $_SESSION['tipo_usuario'] == 2 ? 'Zapaterias Elite' : 'Oficinas Administrativas Mollendo S. de R.L. y C.V. ');
	ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $result[0]['fecha'], $tmp);
	$tpl->assign('dia', $tmp[1]);
	$tpl->assign('mes', mes_escrito($tmp[2]));
	$tpl->assign('anio', $tmp[3]);
	
	$num_cia = NULL;
	$total_egresos = 0;
	$total_ingresos = 0;
	foreach ($result as $r) {
		if ($num_cia != $r['num_cia']) {
			$num_cia = $r['num_cia'];
			
			$tpl->newBlock('cia');
			$tpl->assign('num_cia', $num_cia);
			$tpl->assign('nombre', $r['nombre']);
			
			$egresos = 0;
			$ingresos = 0;
		}
		$tpl->newBlock('gasto');
		$tpl->assign('cod', $r['cod_gastos']);
		$tpl->assign('desc', $r['descripcion']);
		$tpl->assign('comentario', trim($r['comentario']) != '' ? trim($r['comentario']) : '&nbsp;');
		$tpl->assign('bal', $r['bal'] == 't' ? 'SI' : '&nbsp;');
		$tpl->assign('egreso', $r['tipo_mov'] == 'f' ? number_format($r['importe'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('ingreso', $r['tipo_mov'] == 't' ? number_format($r['importe'], 2, '.', ',') : '&nbsp;');
		
		$egresos += $r['tipo_mov'] == 'f' ? $r['importe'] : 0;
		$ingresos += $r['tipo_mov'] == 't' ? $r['importe'] : 0;
		$total = $ingresos - $egresos;
		
		$total_egresos += $r['tipo_mov'] == 'f' ? $r['importe'] : 0;
		$total_ingresos += $r['tipo_mov'] == 't' ? $r['importe'] : 0;
		
		$tpl->assign('cia.egresos', $egresos > 0 ? number_format($egresos, 2, '.', ',') : '&nbsp;');
		$tpl->assign('cia.ingresos', $ingresos > 0 ? number_format($ingresos, 2, '.', ',') : '&nbsp;');
		$tpl->assign('cia.total', $total != 0 ? '<span style="color:#' . ($total >= 0 ? '00C' : 'C00') . ';">' . number_format($total, 2, '.', ',') . '</span>' : '&nbsp;');
	}
	
	$tpl->assign('_ROOT.egresos', $total_egresos > 0 ? number_format($total_egresos) : '&nbsp;');
	$tpl->assign('_ROOT.ingresos', $total_ingresos > 0 ? number_format($total_ingresos) : '&nbsp;');
	$total_general = $total_ingresos - $total_egresos;
	$tpl->assign('_ROOT.total', $total_general != 0 ? '<span style="color:#' . ($total_general >= 0 ? '00C' : 'C00') . ';">' . number_format($total_general, 2, '.', ',') . '</span>' : '&nbsp;');
	
	die($tpl->getOutputContent());
}

// Validar factura
if (isset($_GET['f'])) {
	// Datos de la remisión
	$sql = '
		SELECT
			num_cia,
			fecha,
			total,
			tspago
		FROM
			facturas_zap
		WHERE
				num_proveedor = ' . $_GET['p'] . '
			AND
				num_fact = ' . $_GET['f'] . '
	';
	$result = $db->query($sql);
	
	if (!$result) {
		echo '-1';
		die;
	}
	else if ($result[0]['tspago'] != '') {
		echo '-2';
		die;
	}
	else if ($result[0]['num_cia'] != $_GET['c']) {
		echo '-3';
		die;
	}
	
	// Datos del proveedor
	$sql = '
		SELECT
			cn.id,
			clave_seguridad
		FROM
				catalogo_proveedores
					cp
			LEFT JOIN
				catalogo_nombres
					cn
						ON
							(
								cn.num = cp.clave_seguridad
							)
		WHERE
			num_proveedor = ' . $_GET['p'] . '
	';
	$tmp = $db->query($sql);
	
	if (!$tmp)
		die;
	
	$id = $tmp[0]['id'];
	
	// Buscar pagos parciales de la remisión en otros depositos
	$sql = '
		SELECT
			sum
				(
					importe
				)
					AS
						importe
		FROM
			(
					SELECT
						sum
							(
								pag1
							)
								AS
									importe
					FROM
						otros_depositos
					WHERE
							fecha >= \'' . $result[0]['fecha'] . '\'
						AND
							idnombre = ' . $id . '
						AND
							num_fact1 = ' . $_GET['f'] . '
						AND pag1 > 0
				UNION
					SELECT
						sum
							(
								pag2
							)
								AS
									importe
					FROM
						otros_depositos
					WHERE
							fecha >= \'' . $result[0]['fecha'] . '\'
						AND
							idnombre = ' . $id . '
						AND
							num_fact2 = ' . $_GET['f'] . '
						AND
							pag2 > 0
				UNION
					SELECT
						sum
							(
								pag3
							)
								AS
									importe
					FROM
						otros_depositos
					WHERE
							fecha >= \'' . $result[0]['fecha'] . '\'
						AND
							idnombre = ' . $id . '
						AND
							num_fact3 = ' . $_GET['f'] . '
						AND
							pag3 > 0
				UNION
					SELECT
						sum
							(
								pag4
							)
								AS
									importe
					FROM
						otros_depositos
					WHERE
							fecha >= \'' . $result[0]['fecha'] . '\'
						AND
							idnombre = ' . $id . '
						AND
							num_fact4 = ' . $_GET['f'] . '
						AND
							pag4 > 0
		)
			result
	';
	$otros = $db->query($sql);
	
	// Buscar pagos parciales de la remisión en gastos de caja
	$sql = '
		SELECT
			sum
				(
					pagado
				)
					AS
						importe
		FROM
			fac_zap_gas_caj
		WHERE
				num_cia = ' . $_GET['c'] . '
			AND
				num_proveedor = ' . $_GET['p'] . '
			AND
				num_fact = ' . $_GET['f'] . '
			AND
				fecha >= \'' . $result[0]['fecha'] . '\'
	';
	$caja = $db->query($sql);
	
	$pagado = $otros[0]['importe'] + $caja[0]['importe'];
	
	echo $result[0]['total'] . '|' . $pagado;
	
	die;
}

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
				num_cia
					BETWEEN
						' . ($_SESSION['iduser'] != 1 ? ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') : '1 AND 998') . '
			AND
				num_cia = ' . $_GET['c'];
	$result = $db->query($sql);
	
	if ($result)
		echo $result[0]['nombre'];
	
	die;
}

// Obtener proveedor
if (isset($_GET['p'])) {
	$sql = '
		SELECT
			nombre,
			clave_seguridad
		FROM
			catalogo_proveedores
		WHERE
			num_proveedor = ' . $_GET['p'];
	$result = $db->query($sql);
	
	if ($result) {
		if ($result[0]['clave_seguridad'] > 0)
			echo $result[0]['nombre'];
		else
			echo -1;
	}
	
	die;
}

if (isset($_POST['fecha'])) {
	$sql = '';
	
	foreach ($_POST['num_cia'] as $i => $c)
		if ($c > 0 && get_val($_POST['importe'][$i]) > 0) {
			$sql .= '
				INSERT INTO
					gastos_caja
						(
							num_cia,
							cod_gastos,
							importe,
							tipo_mov,
							clave_balance,
							fecha,
							fecha_captura,
							comentario,
							iduser,
							imp
						)
					VALUES
						(
							' . $c . ',
							' . $_POST['cod_gastos'][$i] . ',
							' . get_val($_POST['importe'][$i]) . ',
							\'' . $_POST['tipo_mov'][$i] . '\',
							\'' . $_POST['clave_balance'][$i] . '\',
							\'' . $_POST['fecha'] . '\',
							now()::date,
							\'' . trim($_POST['comentario'][$i]) . '\',
							' . $_SESSION['iduser'] . ',
							\'TRUE\'
						)
			' . ";\n";
			
			if ($_POST['cod_gastos'][$i] == 147)
				for ($j = 1; $j <= 4; $j++)
					if ($_POST['num_cia' . $j][$i] > 0 && $_POST['num_pro' . $j][$i] > 0 && $_POST['num_fact' . $j][$i] > 0 && get_val($_POST['importe'][$i]) > 0) {
						$sql .= '
							INSERT INTO
								fac_zap_gas_caj
									(
										idgasto,
										num_cia,
										num_proveedor,
										num_fact,
										fecha,
										pagado,
										iduser,
										tsmod
									)
							VALUES
									(
										(
											SELECT
												last_value
											FROM
												gastos_caja_id_seq
										),
										' . $_POST['num_cia' . $j][$i] . ',
										' . $_POST['num_pro' . $j][$i] . ',
										' . $_POST['num_fact' . $j][$i] . ',
										\'' . $_POST['fecha'] . '\',
										' . get_val($_POST['importe' . $j][$i]) . ',
										' . $_SESSION['iduser'] . ',
										now()
									)
						' . ";\n";
						
						if (get_val($_POST['total' . $j][$i]) == get_val($_POST['pagado' . $j][$i]) + get_val($_POST['importe' . $j][$i]))
							$sql .= '
								UPDATE
									facturas_zap
								SET
									folio = 0,
									cuenta = 0,
									tspago = now()
								WHERE
										num_cia = ' . $_POST['num_cia' . $j][$i] . '
									AND
										num_proveedor = ' . $_POST['num_pro' . $j][$i] . '
									AND
										num_fact = ' . $_POST['num_fact' . $j][$i] . '
							' . ";\n";
					}
		}
	if ($sql != '') $db->query($sql);
	
	die(header('location: ./bal_gas_caj.php'));
}



$tpl = new TemplatePower('plantillas/bal/bal_gas_caj.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha', date('d') < 5 ? date('d/m/Y', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('d/m/Y'));

$sql = '
	SELECT
		id,
		descripcion
			AS
				gasto,
		CASE
			WHEN id = 147 THEN
				1
			ELSE
				0
		END
			AS
				orden
	FROM
		catalogo_gastos_caja
	ORDER BY
		orden,
		descripcion
';
$gastos = $db->query($sql);
foreach ($gastos as $g) {
	$tpl->newBlock('gasto');
	$tpl->assign('id', $g['id']);
	$tpl->assign('gasto', $g['gasto']);
	if ($g['id'] == 147)
		$tpl->assign('style', ' style="color:#00C;font-weight:bold;"');
}

$tpl->printToScreen();
?>