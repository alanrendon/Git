<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

$search = array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ');
$replace = array('&Aacute;', '&Eacute;', '&Iacute;', '&Oacute;', '&Uacute;', '&Ntilde;');

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
			clave_balance,
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
			fecha,
			importe
	';
	$result = $db->query($sql);
	
	if (!$result)
		die;
	
	$db->query('UPDATE gastos_caja SET imp = \'FALSE\' WHERE imp = \'TRUE\'');
	
	$tpl = new TemplatePower('plantillas/bal/listado_dispersion_gastos_caja.tpl');
	$tpl->prepare();
	
	$tpl->assign('oficina', $_SESSION['iduser'] >= 28 ? 'Zapaterias Elite' : 'Oficinas Administrativas Mollendo S. de R.L. y C.V. ');
	$tpl->assign('fecha', $result[0]['fecha']);
	$tpl->assign('descripcion', $result[0]['descripcion']);
	$tpl->assign('tipo', $result[0]['tipo_mov'] == 'f' ? '<span style="color:#C00;">EGRESOS</span>' : '<span style="color:#00C;">INGRESOS</span>');
	$tpl->assign('bal', $result[0]['clave_balance'] == 't' ? ', APLICA A BALANCE' : '');
	
	$total = 0;
	foreach ($result as $r) {
		$tpl->newBlock('fila');
		$tpl->assign('num_cia', $r['num_cia']);
		$tpl->assign('nombre', str_replace($search, $replace, $r['nombre']));
		$tpl->assign('importe', number_format($r['importe'], 2, '.', ','));
		$total += $r['importe'];
	}
	$tpl->assign('_ROOT.total', number_format($total, 2, '.', ','));
	
	die($tpl->getOutputContent());
}

if (isset($_GET['f'])) {
	ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_GET['f'], $tmp);
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $tmp[2], 1, $tmp[3]));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $tmp[2], $tmp[1], $tmp[3]));
	
	if ($_SESSION['iduser'] >= 28)
		$sql .= '
			SELECT
				num_cia,
				nombre_corto
					AS
						nombre,
				sum
					(
						efectivo
					)
						AS
							efectivo
			FROM
					total_zapaterias
				LEFT JOIN
					catalogo_companias
						USING
							(
								num_cia
							)
			WHERE
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			GROUP BY
				num_cia,
				nombre_corto
			ORDER BY
				num_cia
		';
	else
		$sql = '
			SELECT
				num_cia,
				nombre_corto
					AS
						nombre,
				sum
					(
						efectivo
					)
						AS
							efectivo
			FROM
					total_panaderias
				LEFT JOIN
					catalogo_companias
						USING
							(
								num_cia
							)
			WHERE
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			GROUP BY
				num_cia,
				nombre_corto
			
			UNION
			
			SELECT
				num_cia,
				nombre_corto
					AS
						nombre,
				sum
					(
						efectivo
					)
						AS
							efectivo
			FROM
					total_companias
				LEFT JOIN
					catalogo_companias
						USING
							(
								num_cia
							)
			WHERE
				fecha
					BETWEEN
							\'' . $fecha1 . '\'
						AND
							\'' . $fecha2 . '\'
			GROUP BY
				num_cia,
				nombre_corto
			
			ORDER BY
				num_cia
		';
	$result = $db->query($sql);
	
	// No hay efectivos para el periodo dado
	if (!$result) {
		echo -1;
		die;
	}
	
	// Obtener el total de la suma de efectivos
	$total_efectivo = 0;
	foreach ($result as $r)
		$total_efectivo += $r['efectivo'];
	
	// Obtener porcentaje por cada efectivo, aplicarlo al importe y generar tabla de datos
	$data = '<table class="tabla_captura">
          <tr>
            <th scope="col">Compa&ntilde;&iacute;a</th>
            <th scope="col">Efectivo</th>
            <th scope="col">%</th>
            <th scope="col">Importe</th>
          </tr>';
	$total = 0;
	$array_size = count($result);
	foreach ($result as $i => $r) {
		// Calcular porcentaje
		$porc = $r['efectivo'] * 100 / $total_efectivo;
		// Calcular importe para la compañía
		$importe = round($_GET['i'] * $porc / 100, 2);
		$total += $importe;
		
		if ($i == $array_size - 1 && $total != $_GET['i']) {
			$importe += round($_GET['i'] - $total, 2);
			$total += round($_GET['i'] - $total, 2);
		}
		
		if ($importe <= 0)
			continue;
		
		// Añadir a la tabla de datos
		$data .= '<tr class="linea_' . ($i % 2 == 0 ? 'off' : 'on') . '">
            <td><input name="num_cia[]" type="hidden" id="num_cia" value="' . $r['num_cia'] . '" />' . $r['num_cia'] . ' ' . str_replace($search, $replace, $r['nombre']) . ' </td>
            <td align="right" style="color:#00C;">' . number_format($r['efectivo'], 2, '.', ',') . '</td>
            <td align="right" style="color:#060;">' . number_format($porc, 4, '.', ',') . '</td>
            <td align="right" style="color:#C00;"><input name="importe[]" type="hidden" id="importe" value="' . $importe . '" />' . number_format($importe, 2, '.', ',') . '</td>
          </tr>';
	}
	$data .= '<tr>
            <th align="right">Totales</th>
            <th align="right" style="color:#00C;">' . number_format($total_efectivo, 2, '.', ',') . '</th>
            <th>&nbsp;</th>
            <th align="right" style="color:#C00;">' . number_format($total, 2, '.', ',') . '</th>
          </tr>
        </table>';
	
	echo $data;
	
	die;
}

if (isset($_POST['fecha'])) {
	$sql = '';
	foreach ($_POST['num_cia'] as $i => $c)
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
						imp,
						iduser
					)
			VALUES
					(
						' . $c . ',
						' . $_POST['cod_gastos'] . ',
						' . $_POST['importe'][$i] . ',
						\'' . $_POST['tipo_mov']  . '\',
						\'' . $_POST['clave_balance'] . '\',
						\'' . $_POST['fecha'] . '\',
						now()::date,
						\'TRUE\',
						' . $_SESSION['iduser'] . '
					)
		' . ";\n";
	
	$db->query($sql);
	
	die(header('location: DispersionGastosCaja.php'));
}

$tpl = new TemplatePower('plantillas/bal/DispersionGastosCaja.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), date('d') - 1, date('Y'))));

$sql = '
	SELECT
		id,
		descripcion
	FROM
		catalogo_gastos_caja
	ORDER BY
		descripcion
';
$gastos = $db->query($sql);
foreach ($gastos as $g) {
	$tpl->newBlock('cod');
	$tpl->assign('id', $g['id']);
	$tpl->assign('desc', $g['descripcion']);
}

$tpl->printToScreen();
?>