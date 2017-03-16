<?php

include substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), '/')) . '/includes/class.db.inc.php';
include substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), '/')) . '/includes/dbstatus.php';
include substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), '/')) . '/includes/class.auxinv.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');

echo "\n--------------------" . date('d/m/Y H:i:s') . "--------------------\n";
echo "\nEjecutando...\n";

$opt = array();

if (isset($_REQUEST['num_cia']) && $_REQUEST['num_cia'] > 0)
	$opt[] = 'num_cia = ' . $_REQUEST['num_cia'];

if (isset($_REQUEST['codmp']) && $_REQUEST['codmp'] > 0)
	$opt[] = 'codmp = ' . $_REQUEST['codmp'];

if (isset($_REQUEST['pan']))
	$opt[] = 'num_cia BETWEEN 1 AND 300';

if (isset($_REQUEST['pollo']))
	$opt[] = '(num_cia BETWEEN 301 AND 599 OR num_cia IN (702, 704, 705, 707))';

$codmp = isset($_REQUEST['codmp']) && $_REQUEST['codmp'] > 0 ? $_REQUEST['codmp'] : NULL;

$flags = $db->query('SELECT actualizar_historico FROM flags');

$his = $flags[0]['actualizar_historico'] == 't' || isset($_REQUEST['his']) ? TRUE : FALSE;
$dif = isset($_REQUEST['dif']) ? TRUE : FALSE;
$real = isset($_REQUEST['real']) || !(isset($_REQUEST['his']) || isset($_REQUEST['dif']) || isset($_REQUEST['virtual'])) ? TRUE : FALSE;
$virtual = isset($_REQUEST['virtual']) || !(isset($_REQUEST['his']) || isset($_REQUEST['dif']) || isset($_REQUEST['real'])) ? TRUE : FALSE;

echo "\nOpciones...\n";
echo 'Compañia: ' . (isset($_REQUEST['num_cia']) && $_REQUEST['num_cia'] > 0 ? $_REQUEST['num_cia'] : 'Todas') . "\n";
echo 'Producto: ' . ($codmp > 0 ? $codmp : 'Todos') . "\n";
echo 'Actualizar Historico: ' . ($his ? 'Si' : 'No') . "\n";
echo 'Actualizar Real: ' . ($real ? 'Si' : 'No') . "\n";
echo 'Actualizar Virtual: ' . ($virtual ? 'Si' : 'No') . "\n";
echo 'Actualizar Diferencias: ' . ($dif ? 'Si' : 'No') . "\n";

echo "\nObteniendo periodos...\n";

if (isset($_REQUEST['anio']) && isset($_REQUEST['mes'])) {
	$anio = $_REQUEST['anio'];
	$mes = $_REQUEST['mes'];

	$fecha = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 0, $_REQUEST['anio']));
}
else {
	/*
	@ Obtener última fecha del histórico de inventarios
	*/
	$sql = '
		SELECT
			max(fecha)
				AS
					fecha
		FROM
			historico_inventario
	';
	if (count($opt) > 0)
		$sql .= '
			WHERE ' . implode(' AND ', $opt) . '
		';
	$result = $db->query($sql);

	if (!$result) {
		echo "\nNo hay fecha de histórico\n";

		die(-2);
	}

	ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{1,4})', $result[0]['fecha'], $tmp);

	if ($flags[0]['actualizar_historico'] == 't') {
		$anio = date('Y', mktime(0, 0, 0, $tmp[2], 1, $tmp[3]));
		$mes = date('n', mktime(0, 0, 0, $tmp[2], 1, $tmp[3]));
	}
	else {
		$anio = date('Y', mktime(0, 0, 0, $tmp[2] + 1, 1, $tmp[3]));
		$mes = date('n', mktime(0, 0, 0, $tmp[2] + 1, 1, $tmp[3]));
	}

	$fecha = $result[0]['fecha'];
}
echo "\nAño = " . $anio . "</pre>";
echo "\nMes = " . mes_escrito($mes) . "\n";

echo "\nObteniendo compañías...\n";

$sql = '
	SELECT
		num_cia
	FROM
		historico_inventario
	WHERE
		fecha = \'' . $fecha . '\'
';
if (count($opt) > 0)
	$sql .= '
		AND
			' . implode(' AND ', $opt) . '
	';
$sql .= '
	GROUP BY
		num_cia
	ORDER BY
		num_cia
';
$cias = $db->query($sql);

if (!$cias) {
	echo "\nNo hay compañías\n";

	die(-1);
}

echo "\nRealizando cálculo de costos...\n";

$sql = '';
foreach ($cias as $c) {
	/*
	@
	@@ Actualizar Inventario Real
	@
	*/

	if ($his || $dif || $real) {
		$aux = new AuxInvClass($c['num_cia'], $anio, $mes, $codmp, 'real');

		if ($real)
			foreach ($aux->mps as $cod => $mp) {
				$sql .= '
					UPDATE
						inventario_real
					SET
						existencia = ROUND(' . $mp['existencia'] . ', 2),
						precio_unidad = ' . $mp['precio'] . '
					WHERE
							num_cia = ' . $c['num_cia'] . '
						AND
							codmp = ' . $cod . '
				' . ";\n";
			}

		if ($his)
			foreach ($aux->mps as $cod => $mp) {
				$sql .= '
					UPDATE
						historico_inventario
					SET
						existencia = ROUND(' . $mp['existencia'] . ', 2),
						precio_unidad = ' . $mp['precio'] . '
					WHERE
							num_cia = ' . $c['num_cia'] . '
						AND
							codmp = ' . $cod . '
						AND
							fecha = \'' . $aux->fecha2 . '\'
				' . ";\n";
			}

		if ($dif)
			foreach ($aux->mps as $cod => $mp) {
				$sql .= '
					UPDATE
						inventario_fin_mes
					SET
						existencia = ROUND(' . $mp['existencia'] . ', 2),
						diferencia = ROUND(' . $mp['existencia'] . ', 2) - inventario,
						precio_unidad = ' . $mp['precio'] . '
					WHERE
							num_cia = ' . $c['num_cia'] . '
						AND
							codmp = ' . $cod . '
						AND
							fecha = \'' . $aux->fecha2 . '\'
				' . ";\n";
			}
	}

	/*
	@
	@@ Actualizar Inventario Virtual
	@
	*/

	if ($virtual) {
		$aux = new AuxInvClass($c['num_cia'], $anio, $mes, $codmp, 'virtual');

		foreach ($aux->mps as $cod => $mp) {
			$sql .= '
				UPDATE
					inventario_virtual
				SET
					existencia = ROUND(' . $mp['existencia'] . ', 2),
					precio_unidad = ' . $mp['precio'] . '
				WHERE
						num_cia = ' . $c['num_cia'] . '
					AND
						codmp = ' . $cod . '
			' . ";\n";
		}
	}
}

if ($his) {
	$sql .= '
		UPDATE
			flags
		SET
			actualizar_historico = FALSE
	' . ";\n";
}

echo "\nEjecutando querys...\n";

if ($sql != '')
	$db->query($sql);

echo "\nProceso terminado!\n";
?>
