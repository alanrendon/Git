<?php

include('includes/class.db.inc.php');
include('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');

$sql = "SELECT
	fe.*
FROM
	facturas_electronicas fe
LEFT JOIN facturas_electronicas_series fes ON (
	fes.num_cia = fe.num_cia
	AND fes.tipo_serie = fe.tipo_serie
	AND fe.consecutivo BETWEEN fes.folio_inicial
	AND fes.folio_inicial
)
WHERE
	fe.tipo_serie = 3
	AND TRIM(fe.observaciones) != ''
	AND fe_id IS NULL
ORDER BY
	id";

$result = $db->query($sql);

if ( ! $result)
{
	echo "<br>No hay resultados";
	die;
}

foreach ($result as $row)
{
	echo "<br>" . $row['observaciones'];

	$folio = substr($row['observaciones'], 41, strpos($row['observaciones'], ' CON FECHA') - 41);
	$fecha = substr($row['observaciones'], strpos($row['observaciones'], 'FECHA') + 6, 10);

	if (strpos($row['observaciones'], ', COMPLEMENTO') !== FALSE)
	{
		$importe = preg_replace('/[^0-9\.]/', '', substr($row['observaciones'], strpos($row['observaciones'], '$') + 1, strpos($row['observaciones'], ', COMPLEMENTO') - strpos($row['observaciones'], '$') - 1));
	}
	else
	{
		$importe = preg_replace('/[^0-9\.]/', '', substr($row['observaciones'], strpos($row['observaciones'], '$') + 1));
	}

	$fac = $db->query("SELECT id FROM facturas_electronicas WHERE consecutivo = {$folio} AND fecha = '{$fecha}' AND total = {$importe} AND tscan IS NULL");

	if ($fac)
	{
		echo "<br><strong style=\"color:" . (count($fac) == 1 ? 'blue' : 'green') . ";\">Encontradas " . count($fac) . " coincidencias</strong>";

		if (count($fac) == 1)
		{
			$db->query("UPDATE facturas_electronicas SET fe_id = {$fac[0]['id']} WHERE id = {$row['id']}");
		}
	}
	else
	{
		echo "<br><strong style=\"color:red;\">No se encontraron coincidencias</strong>";
	}

	echo "<br>";
}
