<?php
// ACTUALIZACION DE INVENTARIOS (VER. 3)
// Menu 'No definido'

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn, 'autocommit=yes');

// Conectarse a la base de datos
$db = new DBclass($dsn, 'autocommit=yes');

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_act_inv_v4.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Actualizar inventarios
if (isset($_GET['accion']) && $_GET['accion'] == 1) {
	$fecha_ini = date("d/m/Y", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
	$fecha = date("d/m/Y", mktime(0, 0, 0, date("m"), 0, date("Y")));
	
	// Actualizar nuevamente existencias y diferencias, por si hubo algun cambio
	$sql = "UPDATE inventario_fin_mes SET existencia = inv.existencia, precio_unidad = inv.precio_unidad, diferencia = inv.existencia - inventario FROM (SELECT num_cia, codmp, existencia, precio_unidad FROM inventario_real) inv WHERE fecha = '2009/03/31' AND inventario_fin_mes.num_cia = inv.num_cia AND inventario_fin_mes.codmp = inv.codmp;\n";
	
	// Generar diferencias en contra
	$sql .= "INSERT INTO dif_inv_tmp (num_cia, codmp, fecha, cod_turno, tipo_mov, cantidad, existencia, precio, total_mov, precio_unidad, descripcion)";
	$sql .= " SELECT num_cia, codmp, '$fecha' AS fecha, NULL AS cod_turno, 'TRUE' AS tipo_mov, abs(diferencia) AS cantidad, 0 AS existencia, precio_unidad AS precio,";
	$sql .= " abs(precio_unidad * diferencia) AS total_mov, precio_unidad, 'DIFERENCIA INVENTARIO' AS descripcion FROM inventario_fin_mes WHERE diferencia > 0 AND fecha = '$fecha';\n";
	// Generar diferencias a favor
	// [02/Jul/2008] Las diferencias a favor ahora se guardaran como salidas negativas
	$sql .= "INSERT INTO dif_inv_tmp (num_cia, codmp, fecha, cod_turno, tipo_mov, cantidad, existencia, precio, total_mov, precio_unidad, descripcion)";
	$sql .= " SELECT num_cia, codmp, '$fecha' AS fecha, NULL AS cod_turno, 'TRUE' AS tipo_mov, -abs(diferencia) AS cantidad, 0 AS existencia, precio_unidad AS precio,";
	$sql .= " -abs(precio_unidad * diferencia) AS total_mov, precio_unidad, 'DIFERENCIA INVENTARIO' AS descripcion FROM inventario_fin_mes WHERE diferencia < 0 AND fecha = '$fecha';\n";
	// Generar movimientos
	$sql .= "INSERT INTO mov_inv (num_cia, codmp, fecha, cod_turno, tipo_mov, cantidad, existencia, precio, total_mov, precio_unidad, descripcion) SELECT num_cia, codmp, fecha,";
	$sql .= " cod_turno, tipo_mov, cantidad, existencia, precio, total_mov, precio_unidad, descripcion FROM dif_inv_tmp WHERE fecha = '$fecha';\n";
	// Generar gastos
	$sql .= "INSERT INTO mov_gastos_tmp (codgastos, num_cia, fecha, importe, concepto) SELECT 90 AS codgastos, num_cia, fecha, total_mov AS importe, descripcion AS concepto";
	$sql .= " FROM mov_inv WHERE codmp = 90 AND tipo_mov = 'TRUE' AND fecha = '$fecha';\n";
	
	$db->query($sql);
	
	header("location: ./bal_act_inv_v4.php");
	die;
}

if (isset($_GET['accion']) && $_GET['accion'] == 2) {
	unset($_SESSION['act_inv']);
	header("location: ./bal_act_inv_v4.php");
	die;
}

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	
	$fecha = date("d/m/Y", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
	$mes = mes_escrito(date("n", mktime(0, 0, 0, date("m") - 1, 1, date("Y"))));
	$anio = date("Y", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
	
	$tpl->assign("mes", $mes);
	$tpl->assign("anio", $anio);
	
	if (empty($_SESSION['act_inv'])) {
		// Obtener primera compañía con diferencias
		$sql = "SELECT num_cia FROM inventario_fin_mes WHERE fecha >= '$fecha' ORDER BY num_cia LIMIT 1";
		$cia = $db->query($sql);
		if ($cia)
			$tpl->assign("num_cia", $cia[0]['num_cia']);
		else
			$tpl->assign("num_cia", 1);
	}
	else
		$tpl->assign("num_cia", $_SESSION['act_inv']['num_cia']);
	
	if (isset($_GET['alerta']))
		$tpl->newBlock("alerta");
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	}
	
	$tpl->printToScreen();
	die;
}

$fecha = date("d/m/Y", mktime(0, 0, 0, date("n") - 1, 1, date("Y")));
$fecha_fin = date('d/m/Y', mktime(0, 0, 0, date('n'), 0, date('Y')));
$mes = date("n", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
$anio = date("Y", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
$fecha0 = date('d/m/Y', mktime(0, 0, 0, $mes, 0, $anio));
$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes - 1, 0, $anio));

// Actualizar existencia en inventario_fin_mes
$sql = "UPDATE inventario_fin_mes SET existencia = inventario_real.existencia, diferencia = inventario_real.existencia - inventario,precio_unidad = inventario_real.precio_unidad WHERE";
$sql .= " num_cia = $_GET[num_cia] AND fecha >= '$fecha' AND num_cia = inventario_real.num_cia AND codmp = inventario_real.codmp";
$db->query($sql);

// Obtener diferencias
$sql = "SELECT id, codmp, nombre, existencia, inventario, diferencia, precio_unidad, controlada, (SELECT diferencia * precio_unidad FROM inventario_fin_mes WHERE num_cia = ifm.num_cia AND codmp = ifm.codmp AND fecha = '$fecha0' LIMIT 1) AS dif0, (SELECT diferencia * precio_unidad FROM inventario_fin_mes WHERE num_cia = ifm.num_cia AND codmp = ifm.codmp AND fecha = '$fecha1' LIMIT 1) AS dif1 FROM inventario_fin_mes ifm JOIN catalogo_mat_primas USING (codmp) WHERE";
$sql .= " num_cia = $_GET[num_cia] AND fecha >= '$fecha'";
// [02-Sep-2008] Solo mostrar productos controlados que hayan tenido consumo los ultimos 2 meses
if ($_GET['tipo'] == 'TRUE') {
	$sql .= " AND (codmp IN (SELECT codmp FROM mov_inv_real LEFT JOIN catalogo_mat_primas USING (codmp) WHERE controlada = 'TRUE' AND num_cia = $_GET[num_cia] AND fecha BETWEEN '$fecha_fin'::date - interval '2 months' AND '$fecha_fin' AND tipo_mov = 'TRUE' GROUP BY codmp) OR diferencia <> 0)";
}
$sql .= $_GET['tipo'] != "" ? " AND controlada = '$_GET[tipo]'" : "";
$sql .= " ORDER BY num_cia, controlada DESC, codmp ASC";
$result = $db->query($sql);//if ($_SESSION['iduser'] == 1)echo '<pre>' . $sql . "\n" . print_r($result, true) . '</pre>';

$tpl->newBlock("listado");
$tpl->assign("tipo", $_GET['tipo']);
$nombre = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
$tpl->assign("num_cia", $_GET['num_cia']);
$tpl->assign("nombre", $nombre[0]['nombre_corto']);
$tpl->assign("mes", mes_escrito($mes,TRUE));
$tpl->assign("anio", $anio);

// Obtener siguiente compañía en el listado de diferencias
$sql = "SELECT num_cia, nombre_corto FROM inventario_fin_mes JOIN catalogo_companias USING (num_cia) WHERE num_cia > $_GET[num_cia] AND fecha >= '$fecha' ORDER BY num_cia LIMIT 1";
$cia = $db->query($sql);
if ($cia) {
	$tpl->assign("num_cia_next", $cia[0]['num_cia']);
	$tpl->assign("nombre_next", $cia[0]['nombre_corto']);
	$_SESSION['act_inv']['num_cia'] = $cia[0]['num_cia'];
}
else {
	// Obtener primera compañía con diferencias
	$sql = "SELECT num_cia, nombre_corto FROM inventario_fin_mes JOIN catalogo_companias USING (num_cia) WHERE fecha >= '$fecha' ORDER BY num_cia LIMIT 1";
	$cia = $db->query($sql);
	if ($cia) {
		$tpl->assign("num_cia_next", $cia[0]['num_cia']);
		$tpl->assign("nombre_next", $cia[0]['nombre_corto']);
		$_SESSION['act_inv']['num_cia'] = $cia[0]['num_cia'];
	}
}

// Listado de compañías
$sql = "SELECT num_cia, nombre_corto FROM inventario_fin_mes LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha >= '$fecha' GROUP BY num_cia, nombre_corto ORDER BY num_cia";
$cias = $db->query($sql);
foreach ($cias as $cia) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia['num_cia']);
	$tpl->assign("nombre", $cia['nombre_corto']);
}

$cia = NULL;
$total = 0;
$favor = 0;
$contra = 0;

// [02-Julio-2008] Almacena el consumo total de materia prima no controlada
$nc = 0;

$i = 0;
foreach ($result as $reg)
	if (round($reg['inventario'] - $reg['existencia'], 2) != 0) {
		$tpl->newBlock("fila");
		$tpl->assign("i", $i);
		$tpl->assign("id", $reg['id']);
		$tpl->assign("num_cia", $_GET['num_cia']);
		$tpl->assign("mes", date("n", mktime(0, 0, 0, date("n"), 0, date("Y"))));
		$tpl->assign("anio", date("Y", mktime(0, 0, 0, date("n"), 0, date("Y"))));
		$tpl->assign("codmp", $reg['codmp']);
		$tpl->assign("nombre", $reg['nombre']);
		$tpl->assign("color_mp", $reg['controlada'] == "TRUE" ? "0000CC" : "993300");
		$tpl->assign("color_exi", round($reg['existencia'], 2) >= 0 ? "000000" : "CC0000");
		$tpl->assign("existencia", round($reg['existencia'], 2) != 0 ? number_format($reg['existencia'], 2, ".", ",") : "");
		$tpl->assign("inventario", round($reg['inventario'], 2) != 0 ? number_format($reg['inventario'], 2, ".", ",") : "");
		$dif = $reg['inventario'] - $reg['existencia'];
		$tpl->assign("falta", $dif < 0 ? number_format(abs($dif), 2, ".", ",") : "");
		$tpl->assign("sobra", $dif > 0 ? number_format(abs($dif), 2, ".", ",") : "");
		$tpl->assign("costo", number_format($reg['precio_unidad'], 4, ".", ","));
		$tpl->assign("color_t", $dif < 0 ? "CC0000" : "0000CC");
		$tpl->assign("total", number_format(abs($dif * $reg['precio_unidad']), 2, ".", ","));
		$tpl->assign('dif0', $reg['dif0'] != 0 ? ('<span style="color:#' . ($reg['dif0'] < 0 ? '00C' : 'C00') . ';">' . number_format(abs($reg['dif0']), 2, ".", ",") . '</span>') : '&nbsp;');
		$tpl->assign('dif1', $reg['dif1'] != 0 ? ('<span style="color:#' . ($reg['dif1'] < 0 ? '00C' : 'C00') . ';">' . number_format(abs($reg['dif1']), 2, ".", ",") . '</span>') : '&nbsp;');
		
		$i++;
		
		$total += $dif * round($reg['precio_unidad'], 4);
		if ($dif < 0)
			$contra += $dif * round($reg['precio_unidad'], 4);
		else
			$favor += $dif * round($reg['precio_unidad'], 4);
		
		if ($reg['controlada'] != 'TRUE' && !in_array($reg['codmp'], array(90)))
			$nc += $dif * round($reg['precio_unidad'], 4);
	}
$tpl->assign("listado.contra", $contra != 0 ? number_format(abs($contra), 2, ".", ",") : "");
$tpl->assign("listado.favor", $favor != 0 ? number_format($favor, 2, ".", ",") : "");
$tpl->assign("listado.color_gt", round($total, 2) >= 0 ? "0000CC" : "CC0000");
$tpl->assign("listado.total", number_format(abs($total), 2, ".", ","));

$tpl->printToScreen();
?>