<?php

function AuxiliarInventario($num_cia, $anyo, $mes, $codmp = NULL) {
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anyo));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anyo));
	$fecha_his = date('d/m/Y', mktime(0, 0, ));
	
	/*
	@ Obtener historico de inicio de mes y reordenarlo en un arreglo de códigos de materia prima
	*/
	$sql = '
		SELECT
			codmp,
			existencia,
			precio_unidad
		FROM
				historico_inventario
			LEFT JOIN
				catalogo_mat_primas
					USING
						(
							codmp
						)
		WHERE
				num_cia = ' . 301 . '
			AND
				fecha = \'' . $fecha_his . '\'
			AND
				codmp
					NOT IN
						(
							90
						)
		ORDER BY
			controlada
				DESC,
			tipo,
			codmp
	';
	$tmp = $db->query($sql);
}

?>
