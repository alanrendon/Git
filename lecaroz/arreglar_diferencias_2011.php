<?php

include('includes/class.db.inc.php');
include('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');

$sql = '
	SELECT
		num_cia,
		fecha,
		codmp,
		existencia,
		(
			SELECT
				inventario
			FROM
				inventario_fin_mes
			WHERE
				num_cia = h.num_cia
				AND fecha = h.fecha
				AND codmp = h.codmp
		)
			AS inventario,
		ROUND(existencia::numeric - COALESCE((SELECT inventario FROM inventario_fin_mes WHERE num_cia = h.num_cia AND fecha = h.fecha AND codmp = h.codmp)::numeric, 0), 2)
			AS diferencia
	FROM
		historico_inventario h
	WHERE
		num_cia < 300
		AND fecha = \'31/08/2011\'
		AND existencia < 0
	ORDER BY
		num_cia,
		fecha,
		codmp
';

$result = $db->query($sql);echo '<pre>' . print_r($result, TRUE) . '</pre>';

$sql = '';

$cont = 0;

foreach ($result as $rec) {
	if ($id = $db->query('
		SELECT
			id
		FROM
			mov_inv_real
		WHERE
			num_cia = ' . $rec['num_cia'] . '
			AND fecha = \'' . $rec['fecha'] . '\'
			AND codmp = ' . $rec['codmp'] . '
			AND descripcion = \'DIFERENCIA INVENTARIO\'
	')) {
		if ($rec['diferencia'] != 0) {
			$sql .= 'UPDATE mov_inv_real SET cantidad = ROUND(' . round($rec['diferencia'], 2) . ', 2) WHERE id = ' . $id[0]['id'] . ";\n";
			
			$cont++;
		}
		else {
			$sql .= 'DELETE FROM mov_inv_real WHERE id = ' . $id[0]['id'] . ";\n";
			
			$cont++;
		}
	}
	else if ($rec['diferencia'] != 0) {
		$sql .= 'INSERT INTO mov_inv_real (num_cia, fecha, codmp, tipo_mov, descripcion, cantidad) VALUES (' . $rec['num_cia'] . ', \'' . $rec['fecha'] . '\', ' . $rec['codmp'] . ', TRUE, \'DIFERENCIA INVENTARIO\', ROUND(' . round($rec['diferencia'], 2) . ', 2))' . ";\n";
		
		$cont++;
	}
}

echo "<pre>$sql</pre>";

echo $cont;

?>