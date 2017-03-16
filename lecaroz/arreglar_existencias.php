<?php

include(dirname(__FILE__) . '/includes/class.db.inc.php');
include(dirname(__FILE__) . '/includes/dbstatus.php');
include(dirname(__FILE__) . '/includes/auxinv.inc.php');

if ( ! isset($_REQUEST['anio']) || ! isset($_REQUEST['mes']))
{
	echo "<pre>No hay parámetros de búsqueda</pre>";
	die;
}

$db = new DBclass($dsn, 'autocommit=yes');

$fecha_historico = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));
$fecha_historico_ant = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 0, $_REQUEST['anio']));

$condiciones = array();

$condiciones[] = "fecha = '{$fecha_historico}'";

$condiciones[] = "num_cia < 600";

$condiciones[] = "h.existencia != hb.existencia";

$result = $db->query("SELECT
	num_cia,
	fecha,
	codmp,
	h.existencia,
	h.precio_unidad,
	hb.existencia AS existencia_backup,
	hb.precio_unidad AS precio_unidad_backup
FROM
	historico_inventario h
LEFT JOIN historico_inventario_backup hb USING (num_cia, fecha, codmp)
WHERE
	" . implode(' AND ', $condiciones) . "
ORDER BY
	num_cia,
	fecha,
	codmp");

if ( ! $result)
{
	echo "<pre>No hay resultados</pre>";
	die;
}

$sql = '';

foreach ($result as $row)
{
	// Obtener historico de inicio
	$his_ini = $db->query("SELECT existencia, precio_unidad FROM historico_inventario WHERE num_cia = {$row['num_cia']} AND codmp = {$row['codmp']} AND fecha = '{$fecha_historico_ant}'");

	if ( ! $his_ini)
	{
		$db->query("INSERT INTO historico_inventario (num_cia, fecha, codmp, existencia, precio_unidad) VALUES ({$row['num_cia']}, '{$fecha_historico_ant}', {$row['codmp']}, 0, 0)");
		$db->query("INSERT INTO historico_inventario_backup (num_cia, fecha, codmp, existencia, precio_unidad) VALUES ({$row['num_cia']}, '{$fecha_historico_ant}', {$row['codmp']}, 0, 0)");
	}

	$aux = new AuxInvClass($row['num_cia'], $_REQUEST['anio'], $_REQUEST['mes'], $row['codmp'], 'real', NULL, NULL, NULL, FALSE);

	$dif_cantidad = round($aux->mps[$row['codmp']]['existencia'] - $row['existencia_backup'], 2);
	$dif_precio = $aux->mps[$row['codmp']]['precio'];
	$dif_total = $dif_cantidad * $dif_precio;

	$sql .= "-- CIA={$row['num_cia']}, COD={$row['codmp']}, PAN={$row['existencia_backup']}, SIS={$aux->mps[$row['codmp']]['existencia']}, DIF={$dif_cantidad}\n";

	$sql .= "DELETE FROM mov_inv_real WHERE num_cia = {$row['num_cia']} AND fecha = '{$fecha_historico}' AND codmp = {$row['codmp']} AND tipo_mov = TRUE AND descripcion = 'DIFERENCIA INVENTARIO';\n";

	if ($row['codmp'] == 90)
	{
		$sql .= "DELETE FROM movimiento_gastos WHERE num_cia = {$row['num_cia']} AND fecha = '{$fecha_historico}' AND codgastos = 90 AND concepto = 'DIFERENCIA INVENTARIO';\n";
	}

	if ($dif_cantidad != 0)
	{
		$sql .= "INSERT INTO mov_inv_real (num_cia, fecha, codmp, tipo_mov, cantidad, precio_unidad, total_mov, descripcion) VALUES ({$row['num_cia']}, '{$fecha_historico}', {$row['codmp']}, TRUE, {$dif_cantidad}, {$dif_precio}, {$dif_total}, 'DIFERENCIA INVENTARIO');\n";

		if ($row['codmp'] == 90 && $dif_cantidad > 0)
		{
			$sql .= "INSERT INTO movimiento_gastos (num_cia, fecha, codgastos, concepto, captura, importe) VALUES ({$row['num_cia']}, '{$fecha_historico}', 90, 'DIFERENCIA INVENTARIO', TRUE, {$dif_total});\n";
		}
	}

	$sql .= "UPDATE historico_inventario SET existencia = {$row['existencia_backup']}, precio_unidad = {$dif_precio} WHERE num_cia = {$row['num_cia']} AND fecha = '{$fecha_historico}' AND codmp = {$row['codmp']};\n";

}

echo "<pre>{$sql}</pre>";
