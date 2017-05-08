<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$users = array(28, 29, 30, 31);

//if (!in_array($_SESSION['iduser'], array(1, 28))) die("Modificando pantalla");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_efe_zap.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Validar fecha de captura
if (isset($_GET['fecha'])) {
	$sql = "SELECT num_cia, ez.venta, ez.otros, errores, pares, clientes, nota1, nota2, nota3, nota4, COALESCE(gastos, 0) AS gastos, COALESCE(efectivo, 0) AS efectivo FROM efectivos_zap AS ez LEFT JOIN total_zapaterias AS tz USING";
	$sql .= " (num_cia, fecha) WHERE fecha = '$_GET[fecha]' ORDER BY num_cia";
	$result = $db->query($sql);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $fecha);
	$mes = $fecha[2];
	$anio = $fecha[3];
	$bal = $db->query("SELECT num_cia FROM balances_zap WHERE mes = $mes AND anio = $anio LIMIT 1");
	
	$mod = /*in_array($_SESSION['iduser'], array(1, 28)) ? 1 : (!$bal ? 1 : 0)*/1;
	
	$data = /*"$mod\n"*/"1\n";
	if ($result)
		foreach ($result as $reg) {
			// Buscar notas anteriores
			$nota1 = $db->query("SELECT nota1 FROM efectivos_zap WHERE num_cia = $reg[num_cia] AND fecha < '$_GET[fecha]' AND nota1 > 0 ORDER BY fecha DESC LIMIT 1");
			$nota2 = $db->query("SELECT nota2 FROM efectivos_zap WHERE num_cia = $reg[num_cia] AND fecha < '$_GET[fecha]' AND nota2 > 0 ORDER BY fecha DESC LIMIT 1");
			$nota3 = $db->query("SELECT nota3 FROM efectivos_zap WHERE num_cia = $reg[num_cia] AND fecha < '$_GET[fecha]' AND nota3 > 0 ORDER BY fecha DESC LIMIT 1");
			$nota4 = $db->query("SELECT nota4 FROM efectivos_zap WHERE num_cia = $reg[num_cia] AND fecha < '$_GET[fecha]' AND nota4 > 0 ORDER BY fecha DESC LIMIT 1");
			$nota1_ant = $nota1 ? $nota1[0]['nota1'] : 0;
			$nota2_ant = $nota2 ? $nota2[0]['nota2'] : 0;
			$nota3_ant = $nota3 ? $nota3[0]['nota3'] : 0;
			$nota4_ant = $nota4 ? $nota4[0]['nota4'] : 0;
			// Datos a retornar
			$data .= "$reg[num_cia]|$reg[venta]|$reg[errores]|$reg[otros]|$reg[clientes]|$reg[pares]|$reg[nota1]|$reg[nota2]|$reg[nota3]|$reg[nota4]|$nota1_ant|$nota2_ant|$nota3_ant|$nota4_ant|$reg[gastos]|$reg[efectivo]||";
		}
	else {
		$result = $db->query("SELECT num_cia FROM catalogo_companias WHERE num_cia BETWEEN 900 AND 998 ORDER BY num_cia");
		foreach ($result as $reg) {
			// Buscar notas anteriores
			$nota1 = $db->query("SELECT nota1 FROM efectivos_zap WHERE num_cia = $reg[num_cia] AND fecha < '$_GET[fecha]' AND nota1 > 0 ORDER BY fecha DESC LIMIT 1");
			$nota2 = $db->query("SELECT nota2 FROM efectivos_zap WHERE num_cia = $reg[num_cia] AND fecha < '$_GET[fecha]' AND nota2 > 0 ORDER BY fecha DESC LIMIT 1");
			$nota3 = $db->query("SELECT nota3 FROM efectivos_zap WHERE num_cia = $reg[num_cia] AND fecha < '$_GET[fecha]' AND nota3 > 0 ORDER BY fecha DESC LIMIT 1");
			$nota4 = $db->query("SELECT nota4 FROM efectivos_zap WHERE num_cia = $reg[num_cia] AND fecha < '$_GET[fecha]' AND nota4 > 0 ORDER BY fecha DESC LIMIT 1");
			$nota1_ant = $nota1 ? $nota1[0]['nota1'] : 0;
			$nota2_ant = $nota2 ? $nota2[0]['nota2'] : 0;
			$nota3_ant = $nota3 ? $nota3[0]['nota3'] : 0;
			$nota4_ant = $nota4 ? $nota4[0]['nota4'] : 0;
			// Buscar efectivo generado al día
			$gastos = 0;
			$efectivo = 0;
			if ($efe = $db->query("SELECT gastos, efectivo FROM total_zapaterias WHERE num_cia = $reg[num_cia] AND fecha = '$_GET[fecha]'")) {
				$gastos = $efe[0]['gastos'];
				$efectivo = $efe[0]['efectivo'];
			}
			$data .= "$reg[num_cia]|0|0|0|0|0|0|0|0|0|$nota1_ant|$nota2_ant|$nota3_ant|$nota4_ant|$gastos|$efectivo||";
		}
	}
	
	echo $data;
	die;
}

// Insertar datos
if (isset($_POST['fecha'])) {
	$sql = "";
	
	// Validar nota inicial
	for ($i = 0; $i < count($_POST['num_cia']); $i++) {
		// Obtener ultima nota capturada (1)
		$tmp = $db->query("SELECT nota1 FROM efectivos_zap WHERE num_cia = {$_POST['num_cia'][$i]} AND fecha < '$_POST[fecha]' AND nota1 > 0 ORDER BY fecha DESC LIMIT 1");
		$nota1 = $tmp && $tmp[0]['nota1'] > 0 ? $tmp[0]['nota1'] : 0;
//		if (get_val($_POST['nota1'][$i]) > 0 && get_val($_POST['nota1'][$i]) <= $nota1) {
//			$tpl->newBlock("valid");
//			$tpl->assign("mensaje", "La nota capturada para el turno 1 no es el consecutivo de la anterior");
//			$tpl->assign("campo", "nota1[$i]");
//			$tpl->printToScreen();
//			die;
//		}
		// Obtener ultima nota capturada (2)
		$tmp = $db->query("SELECT nota2 FROM efectivos_zap WHERE num_cia = {$_POST['num_cia'][$i]} AND fecha < '$_POST[fecha]' AND nota2 > 0 ORDER BY fecha DESC LIMIT 1");
		$nota2 = $tmp && $tmp[0]['nota2'] > 0 ? $tmp[0]['nota2'] : 0;
//		if (get_val($_POST['nota2'][$i]) > 0 && get_val($_POST['nota2'][$i]) <= $nota2) {
//			$tpl->newBlock("valid");
//			$tpl->assign("mensaje", "La nota capturada para el turno 2 no es el consecutivo de la anterior");
//			$tpl->assign("campo", "nota2[$i]");
//			$tpl->printToScreen();
//			die;
//		}
		// Obtener ultima nota capturada (3)
		$tmp = $db->query("SELECT nota3 FROM efectivos_zap WHERE num_cia = {$_POST['num_cia'][$i]} AND fecha < '$_POST[fecha]' AND nota3 > 0 ORDER BY fecha DESC LIMIT 1");
		$nota3 = $tmp && $tmp[0]['nota3'] > 0 ? $tmp[0]['nota3'] : 0;
//		if (get_val($_POST['nota3'][$i]) > 0 && get_val($_POST['nota3'][$i]) <= $nota3) {
//			$tpl->newBlock("valid");
//			$tpl->assign("mensaje", "La nota capturada para el turno 3 no es el consecutivo de la anterior");
//			$tpl->assign("campo", "nota3[$i]");
//			$tpl->printToScreen();
//			die;
//		}
		// Obtener ultima nota capturada (4)
		$tmp = $db->query("SELECT nota4 FROM efectivos_zap WHERE num_cia = {$_POST['num_cia'][$i]} AND fecha < '$_POST[fecha]' AND nota4 > 0 ORDER BY fecha DESC LIMIT 1");
		$nota4 = $tmp && $tmp[0]['nota4'] > 0 ? $tmp[0]['nota4'] : 0;
//		if (get_val($_POST['nota4'][$i]) > 0 && get_val($_POST['nota4'][$i]) <= $nota4) {
//			$tpl->newBlock("valid");
//			$tpl->assign("mensaje", "La nota capturada para el turno 4 no es el consecutivo de la anterior");
//			$tpl->assign("campo", "nota4[$i]");
//			$tpl->printToScreen();
//			die;
//		}
	}
	
	for ($i = 0; $i < count($_POST['num_cia']); $i++) {
		$num_cia = $_POST['num_cia'][$i];
		$fecha = $_POST['fecha'];
		$venta = get_val($_POST['venta'][$i]);
		$venta_total = get_val($_POST['venta_total'][$i]);
		$errores = get_val($_POST['errores'][$i]);
		$otros = get_val($_POST['otros'][$i]);
		$pares = get_val($_POST['pares'][$i]);
		$clientes = get_val($_POST['clientes'][$i]);
		$nota1 = get_val($_POST['nota1'][$i]);
		$nota2 = get_val($_POST['nota2'][$i]);
		$nota3 = get_val($_POST['nota3'][$i]);
		$nota4 = get_val($_POST['nota4'][$i]);
		
		// TABLA: total_zapaterias
		if ($id = $db->query("SELECT id FROM total_zapaterias WHERE num_cia = $num_cia AND fecha = '$fecha'"))
			$sql .= "UPDATE total_zapaterias SET efectivo = $venta_total + $otros - gastos, venta = $venta_total, otros = $otros WHERE id = {$id[0]['id']};\n";
		else
			$sql .= "INSERT INTO total_zapaterias (num_cia, fecha, venta, otros, gastos, efectivo) VALUES ($num_cia, '$fecha', $venta_total, $otros, 0, $venta_total + $otros);\n";
		
		// TABLA: efectivos_zap
		if ($id = $db->query("SELECT id FROM efectivos_zap WHERE num_cia = $num_cia AND fecha = '$fecha'"))
			$sql .= "UPDATE efectivos_zap SET venta = $venta, errores = $errores, otros = $otros, pares = $pares, clientes = $clientes, nota1 = $nota1, nota2 = $nota2, nota3 = $nota3, nota4 = $nota4 WHERE id = {$id[0]['id']};\n";
		else
			$sql .= "INSERT INTO efectivos_zap (num_cia, fecha, venta, errores, otros, pares, clientes, nota1, nota2, nota3, nota4) VALUES ($num_cia, '$fecha', $venta, $errores, $otros, $pares, $clientes, $nota1, $nota2, $nota3, $nota4);\n";
	}
	if ($sql != "") $db->query($sql);
	
	$tpl->newBlock("redir");
	$tpl->printToScreen();
	die;
}

$mes = date("n");
$anio = date("Y");

$tpl->newBlock("captura");
$tpl->assign("fecha", "");

$sql = "SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias WHERE num_cia BETWEEN 900 AND 998 ORDER BY num_cia";
$result = $db->query($sql);

foreach ($result as $i => $reg) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("next", $i < count($result) - 1 ? $i + 1 : 0);
	$tpl->assign("back", $i > 0 ? $i - 1 : count($result) - 1);
	$tpl->assign("num_cia", $reg['num_cia']);
	$tpl->assign("nombre", $reg['nombre']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message",$descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
die;

?>