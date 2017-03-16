<?php
include './includes/class.db.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');

if (isset($_REQUEST['anyo']) && isset($_REQUEST['mes'])) {
	$mes  = $_REQUEST['mes'];
	$anyo = $_REQUEST['anyo'];
}
else {
	$mes = date('n');
	$anyo = date('Y');
}

$sql = '
	SELECT
		arr.id,
		arr.cod_arrendador,
		inm.nombre
			AS
				inmobiliaria,
		arr.num_local || \' \' || arr.nombre_local
			AS
				"local",
		(COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0))
		+ (CASE WHEN tipo_local = 1 THEN (COALESCE(renta_con_recibo, 0)+ COALESCE(mantenimiento, 0)) * 0.16 ELSE 0 END)
		+ COALESCE(agua, 0)
		- (CASE WHEN retencion_isr = \'t\' THEN (COALESCE(renta_con_recibo, 0)+ COALESCE(mantenimiento, 0)) * 0.10 ELSE 0 END)
		- (CASE WHEN retencion_iva = \'t\' THEN (COALESCE(renta_con_recibo, 0)+ COALESCE(mantenimiento, 0)) * 0.10666666667 ELSE 0 END)
			AS
				"importe"
	FROM
				catalogo_arrendatarios arr
			LEFT JOIN
				catalogo_arrendadores inm
					USING
						(
							cod_arrendador
						)
	WHERE
			arr.cod_arrendador BETWEEN 601 AND 622
		AND
			arr.status = 1
		AND
			arr.bloque = 2
	ORDER BY
		arr.cod_arrendador,
		arr.num_local
';
$locales = $db->query($sql);

if ($locales) {
	$fecha1_renta = date('d/m/Y', mktime(0, 0, 0, $mes - 10, 0, $anyo));
	$fecha2_renta = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo));
	
	$current_year = $anyo;
	$current_month = $mes;
	$current_day = date('d', mktime(0, 0, 0, $mes + 1, 0, $anyo));
	
	$sql = '
		SELECT
			local,
			extract(month from fecha_renta)
				AS
					mes,
			extract(year from fecha_renta)
				AS
					anio,
			CASE
				WHEN fecha_con IS NOT NULL THEN
					\'t\'
				ELSE
					\'f\'
			END
				AS
					status
		FROM
			estado_cuenta
		WHERE
				num_cia BETWEEN 601 AND 622
			AND
				local
					IN
						(
							SELECT
								ca.id
							FROM
									catalogo_arrendatarios ca
							WHERE
									ca.cod_arrendador BETWEEN 601 AND 622
								AND
									ca.status = 1
								AND
									ca.bloque = 2
						)
			AND
				cod_mov = 2
			AND
				fecha_renta BETWEEN \'' . $fecha1_renta . '\' AND \'' . $fecha2_renta . '\'
		ORDER BY
			fecha_renta
	';
	$tmp = $db->query($sql);
	foreach ($tmp as $t)
		$rentas[$t['local']][$t['anio']][$t['mes']] = $t['status'] == 't' ? 1 : 10;
	
	$sql = '
		SELECT
			local,
			anio,
			mes,
			tipo
		FROM
				estatus_locales el
			LEFT JOIN
				catalogo_arrendatarios ca
					ON
						(ca.id = el.local)
		WHERE
				cod_arrendador BETWEEN 601 AND 622
			AND
				tsmod <= \'' . $fecha2_renta . ' 23:59:59\'::timestamp
			AND
				local
					IN
						(
							SELECT
								ca.id
							FROM
									catalogo_arrendatarios ca
							WHERE
									ca.cod_arrendador BETWEEN 601 AND 622
								AND
									ca.status = 1
								AND
									ca.bloque = 2
						)
			AND
				anio IN (' . $anyo . ', ' . $anyo . ' - 1, ' . $anyo . ' - 2)
			AND
				(local, mes, anio)
					NOT IN
						(
							SELECT
								local,
								extract(month from fecha_renta)
									AS
										mes,
								extract(year from fecha_renta)
									AS
										anio
							FROM
								estado_cuenta
							WHERE
									num_cia BETWEEN 601 AND 622
								AND
									local
										IN
											(
												SELECT
													ca.id
												FROM
														catalogo_arrendatarios ca
												WHERE
														cod_arrendador BETWEEN 601 AND 622
													AND
														ca.status = 1
													AND
														ca.bloque = 2
											)
								AND
									cod_mov = 2
								AND
									fecha_renta BETWEEN \'' . $fecha1_renta . '\' AND \'' . $fecha2_renta . '\'
						)
		ORDER BY
			local,
			anio,
			mes
	';
	$tmp = $db->query($sql);
	
	// [22-Oct-2008] Ordenar estados
	$estados = array();
	$ultimo_estado = array();
	
	if ($tmp) {
		foreach ($tmp as $t) {
			switch ($t['tipo']) {
				case 0:
					$estado = 2;
				break;
				case 1:
					$estado = 1;
				break;
				case 2:
					$estado = 0;
				break;
			}
			$estados[$t['local']][$t['anio']][$t['mes']] = $estado;
			$ultimo_estado[$t['local']] = $estado;
		}
	}
	
	$rentas_pendientes = array();
	
	foreach ($locales as $l) {
		$months = array();
		$ok = TRUE;
		$pen = FALSE;
		$last = NULL;
		for ($y = $anyo - 1; $y <= $anyo; $y++) {
			for ($m = ($y == $anyo - 1 ? (12 - $mes) : 1); $m <= ($y == $anyo - 1 ? 12 : $mes); $m++) {
				if (isset($rentas[$l['id']][$y][$m])) {
					$months[$y][$m] = $rentas[$l['id']][$y][$m];
					$ok = TRUE;
					// Poner los demas meses como pendientes
					$last = 2;
				}
				else if (isset($estados[$l['id']][$y][$m])) {
					$months[$y][$m] = $estados[$l['id']][$y][$m];
					$ok = $estados[$l['id']][$y][$m] == 1 ? TRUE : FALSE;
					$last = $l;
				}
				else if ($last == NULL && isset($ultimo_estado[$l['id']])) {
					$months[$y][$m] = $ultimo_estado[$l['id']];
					$ok = $ultimo_estado[$l['id']] == 1 ? TRUE : FALSE;
					$last = $months[$y][$m];
					
					if ($last == 0 && ($y < $anyo || ($y == $anyo && $y < $current_year) || ($y == $current_year && ($m < $current_month || ($m == $current_month && $current_day > 15)))))
						$pen = TRUE;
				}
				else if (!$ok) {
					$months[$y][$m] = $last;
				}
				else {
					$months[$y][$m] = 0;
					if ($y < $anyo || ($y == $anyo && $y < $current_year) || ($y == $current_year && ($m < $current_month || ($m == $current_month && $current_day > 15))))
						$pen = TRUE;
				}
			}
		}
		
		if (!$pen)
			continue;
		
		foreach ($months as $y => $m) {
			foreach ($m as $i => $s) {
				if ($s == 0) {
					$rentas_pendientes[] = array(
						'#'            => $l['cod_arrendador'],
						'INMOBILIARIA' => $l['inmobiliaria'],
						'FECHA'        => $y . '-' . str_pad($i, 2, '0', STR_PAD_LEFT),
						'LOCAL'        => $l['local'],
						'IMPORTE'      => $l['importe']
					);
				}
			}
		}
	}
	
	$data = '"' . implode('","', array_keys($rentas_pendientes[0])) . '"' . "\n";
	
	foreach ($rentas_pendientes as $r) {
		$data .= '"' . implode('","', $r) . '"' . "\n";
	}
	
	header('Content-Type: application/download');
	header('Content-Disposition: attachment; filename=rentas_pendientes.csv');
	
	echo $data;
}

?>
