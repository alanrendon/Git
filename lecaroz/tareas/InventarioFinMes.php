<?php

include('class.db.inc.php');
include('dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');

$fecha_consulta = date('d/m/Y', mktime(0, 0, 0, date('n') + (date('d') < 8 ? 0 : 1), 1, date('Y')));
$fecha_inventario = date('Y-m-d', mktime(0, 0, 0, date('n') + (date('d') < 8 ? 0 : 1), 1, date('Y')));

$sql = "
	SELECT
		num_cia
	FROM
		inventario_real inv
	WHERE
		num_cia <= 300
		AND (
			(num_cia, codmp) IN (
				SELECT
					num_cia,
					codmp
				FROM
					mov_inv_real
				WHERE
					fecha >= '{$fecha_consulta}'::DATE - INTERVAL '2 MONTHS'
					AND num_cia <= 300
				GROUP BY
					num_cia,
					codmp
			)
			OR existencia != 0
		)
	GROUP BY
		num_cia
	ORDER BY
		num_cia
";

$result = $db->query($sql);

if ( ! $result)
{
	echo 'No hay resultados';
	
	die;
}

$sql = '';

foreach ($result as $row)
{
	$sql .= "INSERT INTO actualizacion_panas (num_cia, metodo, parametros, iduser) VALUES ({$row['num_cia']}, 'actualizar_inventario_fin_mes', 'num_cia={$row['num_cia']}&fecha_consulta={$fecha_consulta}&fecha_inventario={$fecha_inventario}', 0);\n";
}

$db->query($sql);
