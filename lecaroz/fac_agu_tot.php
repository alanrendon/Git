<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$tpl = new TemplatePower( "./plantillas/fac/listado_totales_aguinaldos.tpl" );
$tpl->prepare();

$last_porc = $db->query("SELECT * FROM porcentaje_aguinaldo ORDER BY id DESC LIMIT 1");

$num_cia = isset($_GET['mancomunadas']) ? "catalogo_companias.cia_aguinaldos" : "num_cia_emp";
$orden = isset($_GET['mancomunadas']) ? "cia_aguinaldos" : "num_cia_emp";

$cias = array();
foreach ($_GET['cia'] as $cia)
	if ($cia > 0)
		$cias[] = $cia;

$sql = "
	SELECT
		$num_cia
			AS num_cia,
		count(aguinaldos.id)
			AS num_empleados,
		sum(aguinaldos.importe)
			AS total_aguinaldos
	FROM
		aguinaldos
		LEFT JOIN catalogo_trabajadores
			ON (catalogo_trabajadores.id=aguinaldos.id_empleado)
		LEFT JOIN catalogo_companias
			ON (catalogo_companias.num_cia=catalogo_trabajadores.num_cia)
		WHERE
			solo_aguinaldo = 'TRUE'
			AND aguinaldos.fecha = '{$last_porc[0]['fecha_aguinaldo']}'
			AND aguinaldos.importe >= 20
			AND (
				catalogo_trabajadores.fecha_baja IS NULL
				OR catalogo_trabajadores.fecha_baja > '{$last_porc[0]['fecha_aguinaldo']}'
			)
			AND catalogo_trabajadores.num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
if (count($cias) > 0) {
	$sql .= " AND /*$num_cia*/catalogo_trabajadores.num_cia NOT IN (";
	foreach ($cias as $i => $cia)
		$sql .= $cia . ($i < count($cias) - 1 ? ", " : ")");
}
$sql .= " GROUP BY $num_cia ORDER BY $orden";
$result = $db->query($sql);

$numfilas_x_hoja = 50;
$numfilas = $numfilas_x_hoja;

$total_aguinaldos  = 0;
$num_empleados = 0;
for ($i = 0; $i < count($result); $i++) {
	if ($numfilas == $numfilas_x_hoja) {
		$tpl->newBlock("listado");
		$numfilas = 0;
	}
	$tpl->newBlock("fila");
	$tpl->assign("num_cia", $result[$i]['num_cia']);
	$nombre_cia = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = {$result[$i]['num_cia']}");
	$tpl->assign("nombre_cia", $nombre_cia[0]['nombre']);
	$tpl->assign("num_empleados", $result[$i]['num_empleados']);
	$tpl->assign("total_aguinaldos", number_format($result[$i]['total_aguinaldos'], 2, ".", ","));
	
	$total_aguinaldos += $result[$i]['total_aguinaldos'];
	$num_empleados += $result[$i]['num_empleados'];
	$numfilas++;
	
	if ($numfilas == $numfilas_x_hoja) $tpl->newBlock("salto");
}
$tpl->newBlock("totales");
$tpl->assign("totales.num_empleados", $num_empleados);
$tpl->assign("totales.total_aguinaldos", number_format($total_aguinaldos, 2, ".", ","));

$bill = array();
if ($last_porc[0]['b1000'] == "t") $bill[] = 1000;
if ($last_porc[0]['b500'] == "t") $bill[] = 500;
if ($last_porc[0]['b200'] == "t") $bill[] = 200;
if ($last_porc[0]['b100'] == "t") $bill[] = 100;
if ($last_porc[0]['b50'] == "t") $bill[] = 50;
if ($last_porc[0]['b20'] == "t") $bill[] = 20;

$desglose = array(1000 => 0, 500 => 0, 200 => 0, 100 => 0, 50 => 0, 20 => 0);

$sql = "SELECT importe FROM aguinaldos LEFT JOIN catalogo_trabajadores ON (aguinaldos.id_empleado=catalogo_trabajadores.id) LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha = '{$last_porc[0]['fecha_aguinaldo']}' AND solo_aguinaldo = TRUE AND importe > 20 AND (fecha_baja IS NULL OR fecha_baja > '{$last_porc[0]['fecha_aguinaldo']}') AND catalogo_trabajadores.num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
if (count($cias) > 0) {
	$sql .= " AND /*catalogo_trabajadores*/$num_cia NOT IN (";
	foreach ($cias as $i => $cia)
		$sql .= $cia . ($i < count($cias) - 1 ? ", " : ")");
}
$result = $db->query($sql);

for ($i = 0; $i < count($result); $i++) {
	$residuo = $result[$i]['importe'];
	for ($j = 0; $j < count($bill); $j++) {
		if (floor($residuo / $bill[$j]) > 0)
			$desglose[$bill[$j]] += floor($residuo / $bill[$j]);
		$residuo = $residuo % $bill[$j];
	}
}
$tpl->newBlock("desglose");
$total = 0;
foreach ($desglose as $key => $value)
	if ($value > 0) {
		$tpl->newBlock("den");
		$tpl->assign("cantidad", $value);
		$tpl->assign("denominacion", $key);
		$tpl->assign("importe", number_format($key * $value), 2, ".", ",");
		$total += $key * $value;
	}
$tpl->assign("desglose.total", number_format($total, 2, ".", ","));


$tpl->printToScreen();

?>