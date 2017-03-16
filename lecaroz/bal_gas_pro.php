<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn);

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/bal/bal_gas_pro.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['anio'])) {
	$tpl->newBlock("datos");
	$tpl->assign("anio", date("Y"));

	$result = $db->query('SELECT idadministrador AS id, nombre_administrador AS nombre FROM catalogo_administradores ORDER BY nombre');
	foreach ($result as $reg) {
		$tpl->newBlock('admin');
		$tpl->assign('id', $reg['id']);
		$tpl->assign('nombre', $reg['nombre']);
	}

	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
	}

	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}

	$tpl->printToScreen();
	die();
}

$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias WHERE " . ($_GET['num_cia'] > 0 ? "num_cia = $_GET[num_cia]" : "num_cia < 300") . ($_GET['idadmin'] > 0 ? " AND idadministrador = $_GET[idadmin]" : '') . " ORDER BY num_cia";
$cia = $db->query($sql);

$tpl->newBlock("listado");
$tpl->assign("anio", $_GET['anio']);

$fecha1 = "01/01/$_GET[anio]";
$fecha2 = "31/12/$_GET[anio]";

$num_meses = $_GET['anio'] < date("Y") ? 12 : date("n", mktime(0, 0, 0, date("n"), 0, $_GET['anio']));

function buscarPro($mes) {
	global $produccion;

	if (!$produccion)
		return 0;

	for ($i = 0; $i < count($produccion); $i++)
		if ($mes == $produccion[$i]['mes'])
			return $produccion[$i]['produccion_total'];

	return 0;
}

function buscarGas($mes) {
	global $consumo_gas;

	if (!$consumo_gas)
		return 0;

	for ($i = 0; $i < count($consumo_gas); $i++)
		if ($mes == $consumo_gas[$i]['mes'])
			return $consumo_gas[$i]['consumo'];

	return 0;
}

function buscarDes($mes) {
	global $descuento;

	if (!$descuento)
		return 0;

	for ($i = 0; $i < count($descuento); $i++)
		if ($mes == $descuento[$i]['mes'])
			return $descuento[$i]['des'];

	return 0;
}

$total_gas = array_fill(1, $num_meses, 0);
$total_nat = array_fill(1, $num_meses, 0);
$total_gen = array_fill(1, $num_meses, 0);

$cias_gas = array_fill(1, $num_meses, 0);
$cias_nat = array_fill(1, $num_meses, 0);
$cias_gen = array_fill(1, $num_meses, 0);

for ($i = 0; $i < count($cia); $i++) {
	$sql = "SELECT produccion_total, mes FROM balances_pan WHERE num_cia = {$cia[$i]['num_cia']} AND anio = $_GET[anio]";
	$produccion = $db->query($sql);

	$sql = "SELECT sum(" . (isset($_GET['litros']) ? "cantidad" : "total_mov") . ") AS consumo, extract(month FROM fecha) AS mes FROM mov_inv_real";
	$sql .= " WHERE num_cia = {$cia[$i]['num_cia']} AND codmp = 90 AND tipo_mov = 'TRUE' AND fecha BETWEEN '$fecha1' AND '$fecha2'";
	$sql .= " GROUP BY extract(month FROM fecha) ORDER BY extract(month FROM fecha)";
	$consumo_gas = $db->query($sql);

	$natural = FALSE;

	if (!$consumo_gas && empty($_GET['litros'])) {
		$sql = "SELECT sum(importe) AS consumo, extract(month FROM fecha) AS mes FROM movimiento_gastos";
		$sql .= " WHERE num_cia = {$cia[$i]['num_cia']} AND codgastos = 128 AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$sql .= " GROUP BY extract(month FROM fecha) ORDER BY extract(month FROM fecha)";
		$consumo_gas = $db->query($sql);

		$natural = TRUE;
	}
	else if ($cia[$i]['num_cia'] == 76)
	{
		$natural = TRUE;
	}

	$descuento = FALSE;
	if (empty($_GET['litros'])) {
		// Obtener descuento de gas
		$sql = "SELECT sum(importe) AS des, extract(month FROM fecha) AS mes FROM gastos_caja";
		$sql .= " WHERE num_cia = {$cia[$i]['num_cia']} AND cod_gastos = 92 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'TRUE'";
		$sql .= " GROUP BY extract(month FROM fecha) ORDER BY extract(month FROM fecha)";
		$descuento = $db->query($sql);
	}

	if (!$consumo_gas) {
		continue;
	}

	//echo '<pre>CIA ' . $cia[$i]['num_cia'] . '<br />' . print_r($consumo_gas, TRUE) . '</pre>';

	$tpl->newBlock("fila");
	$tpl->assign("num_cia", $cia[$i]['num_cia']);
	$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);

	$tpl->assign('color', $natural ? '#306' : '#09F');
	$tpl->assign('natural', $natural ? ' style="border-top:solid 3px #000;border-bottom:solid 3px #000;"' : '');
	$tpl->assign('natural2', $natural ? 'border-top:solid 3px #000;border-bottom:solid 3px #000;' : '');

	for ($j = 1; $j <= $num_meses; $j++) {
		//$fecha1 = "1/$j/$_GET[anio]";
		//$fecha2 = date("d/m/Y", mktime(0, 0, 0, $j + 1, 0, $_GET['anio']));

		//$sql = "SELECT produccion_total FROM balances_pan WHERE num_cia = {$cia[$i]['num_cia']} AND mes = $j AND anio = $_GET[anio]";
		//$temp = $db->query($sql);
		//$pro = $temp ? $temp[0]['produccion_total'] : 0;

		// Obtener consumos de gas
		//$sql = "SELECT sum(" . (isset($_GET['litros']) ? "cantidad" : "total_mov") . ") FROM mov_inv_real WHERE num_cia = {$cia[$i]['num_cia']} AND codmp = 90 AND tipo_mov = 'TRUE' AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		//$gas = $db->query($sql);

		// Si la compañía maneja Gas Natural
		/*if ($gas[0]['sum'] == "" && empty($_GET['litros'])) {
			$sql = "SELECT sum(importe) FROM movimiento_gastos WHERE num_cia = {$cia[$i]['num_cia']} AND codgastos = 128 AND fecha BETWEEN '$fecha1' AND '$fecha2'";
			$temp = $db->query($sql);
			$gas = $temp;
		}*/

		/*$des = 0;
		f (empty($_GET['litros'])) {
			// Obtener descuento de gas
			$sql = "SELECT sum(importe) FROM gastos_caja WHERE num_cia = {$cia[$i]['num_cia']} AND cod_gastos = 92 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'TRUE'";
			$temp = $db->query($sql);
			$des = $temp[0]['sum'];
		}*/

		$pro = buscarPro($j);
		$gas = buscarGas($j);
		$des = buscarDes($j);

		$por = $pro > 0 ? ($gas - $des) / $pro : 0;

		if (empty($_GET['litros']))
		{
			$tpl->assign($j, round($por, 3) != 0 ? "<span style=\"color:#" . ($por <= 0.035 ? "00C" : "C00") . "\">" . number_format($por * 100, 3, ".", ",") . "</span>" : "&nbsp;");

			if ($natural)
			{
				$total_nat[$j] += $por;
				$cias_nat[$j] += round($por, 3) != 0 ? 1 : 0;
			}
			else
			{
				$total_gas[$j] += $por;
				$cias_gas[$j] += round($por, 3) != 0 ? 1 : 0;
			}

			$total_gen[$j] += $por;
			$cias_gen[$j] += round($por, 3) != 0 ? 1 : 0;
		}
		else
		{
			$tpl->assign($j, round($por, 3) != 0 ? number_format($por, 4, ".", ",") : "&nbsp;");
			$total_gen[$j] += $por;
		}
	}
}

$tpl->gotoBlock('listado');

foreach ($total_gas AS $i => $t)
{
	$tpl->assign('pg' . $i, round($t, 4) != 0 ? number_format(empty($_REQUEST['litros']) ? $t / $cias_gas[$i] * 100 : $t, 3) : '&nbsp;');
}

foreach ($total_nat AS $i => $t)
{
	$tpl->assign('pn' . $i, round($t, 4) != 0 ? number_format(empty($_REQUEST['litros']) ? $t / $cias_gas[$i] * 100 : $t, 3) : '&nbsp;');
}

foreach ($total_gen AS $i => $t)
{
	$tpl->assign('p' . $i, round($t, 4) != 0 ? number_format(empty($_REQUEST['litros']) ? $t / $cias_gas[$i] * 100 : $t, 3) : '&nbsp;');
}

$tpl->printToScreen();
$db->desconectar();
?>
