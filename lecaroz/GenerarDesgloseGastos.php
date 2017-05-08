<?php

include('includes/class.db.inc.php');
include('includes/dbstatus.php');

if ( ! isset($_REQUEST['anio']) || ! isset($_REQUEST['mes']))
{
	die(-1);
}

$db = new DBclass($dsn, 'autocommit=yes');

$anio = $_REQUEST['anio'];
$mes = $_REQUEST['mes'];

$fecha1 = date('d-m-Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
$fecha2 = date('d-m-Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

$fecha = date('Y-m-d', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

$condiciones = array();

$condiciones[] = "fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

$condiciones[] = "codgastos IN (SELECT codgastos FROM gastos_porcentajes_distribucion GROUP BY codgastos)";

$condiciones[] = "concepto LIKE 'DISTRIBUCION POR GASTO%'";

if (isset($_REQUEST['gasto']) && $_REQUEST['gasto'] > 0)
{
	$condiciones[] = "codgastos = {$_REQUEST['gasto']}";
}

// Validar que no se hayan generado antes las distribuciones de gas
$sql = "SELECT
	*
FROM
	movimiento_gastos
WHERE
	" . implode(' AND ', $condiciones) . "
LIMIT 1";

if (isset($_REQUEST['no_validar']) || ! $db->query($sql)) {
	$condiciones1 = array();
	$condiciones2 = array();

	$condiciones1[] = "g.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";
	$condiciones2[] = "anio  = {$anio}";
	$condiciones2[] = "mes  = {$mes}";

	$condiciones1[] = "g.concepto NOT LIKE 'DISTRIBUCION POR GASTO%'";

	$condiciones1[] = "dg.ros IS NOT NULL";

	if (isset($_REQUEST['gasto']) && $_REQUEST['gasto'] > 0)
	{
		$condiciones1[] = "g.codgastos = {$_REQUEST['gasto']}";
		$condiciones2[] = "codgastos = {$_REQUEST['gasto']}";
	}

	$condiciones1[] = "(g.num_cia, g.codgastos) IN (
		SELECT
			num_cia,
			codgastos
		FROM
			gastos_porcentajes_distribucion
	)";

	$condiciones1_string = implode(' AND ', $condiciones1);
	$condiciones2_string = implode(' AND ', $condiciones2);

	// $sql = "SELECT
	// 	num_cia,
	// 	codgastos,
	// 	SUM(importe)
	// 		AS total,
	// 	ros,
	// 	porc
	// FROM
	// 	gastos_porcentajes_distribucion dg
	// 	LEFT JOIN movimiento_gastos g
	// 		USING (num_cia, codgastos)
	// WHERE
	// 	" . implode(' AND ', $condiciones1) . "
	// GROUP BY
	// 	num_cia,
	// 	codgastos,
	// 	ros,
	// 	porc
	// HAVING
	// 	SUM(importe) > 0
	// ORDER BY
	// 	codgastos,
	// 	num_cia,
	// 	porc,
	// 	ros";
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
			{$condiciones1_string}
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
			{$condiciones2_string}
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
		ros";

	$result = $db->query($sql);

	if ($result) {
		$sql = '';

		$cont = 0;

		foreach ($result as $r) {
			$importe = round($r['total'] * $r['porc'] / 100, 2);

			// Ingresar gasto negativo en panaderia
			$sql .= "INSERT INTO movimiento_gastos (num_cia, codgastos, fecha, importe, captura, concepto ) VALUES ({$r['num_cia']}, {$r['codgastos']}, '{$fecha}', -{$importe}, TRUE, 'DISTRIBUCION POR GASTO FORZADO EN EL BALANCE {$r['ros']}');\n";

			$cont++;

			// Ingresar gasto positivo en rosticeria
			$sql .= "INSERT INTO movimiento_gastos (num_cia, codgastos, fecha, importe, captura, concepto ) VALUES ({$r['ros']}, {$r['codgastos']}, '{$fecha}', {$importe}, TRUE, 'DISTRIBUCION POR GASTO FORZADO EN EL BALANCE {$r['num_cia']}');\n";

			$cont++;
		}

		echo "<pre>-- #Registros: {$cont}\n\n{$sql}</pre>";

		// $db->query($sql);
	}
	else
	{
		echo "No hay gastos para desglosar.";

		die(-3);
	}
}
else
{
	echo "Ya han sido generado los gastos.";

	die(-2);
}

