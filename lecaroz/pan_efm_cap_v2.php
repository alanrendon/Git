<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

$descripcion_error[1] = "No hay resultados";

$numfilas = 15;

if (isset($_GET['action']) && $_GET['action'] == "cancel") {
	unset($_SESSION['efm']);
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/pan/pan_efm_cap_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['action']) && $_GET['action'] == "cap") {
	$sql = "";
	for ($i = 0; $i < $numfilas; $i++) {
		$reg['num_cia'] = $_SESSION['efm']['num_cia'][$i];
		$reg['venta_pta'] = get_val($_SESSION['efm']['venta_pta'][$i]);
		if ($reg['num_cia'] > 0 && $reg['venta_pta'] > 0) {
			// Datos de captura
			$reg['pastillaje'] = get_val($_SESSION['efm']['pastillaje'][$i]);
			$reg['otros'] = get_val($_SESSION['efm']['otros'][$i]);
			$reg['ctes'] = get_val($_SESSION['efm']['ctes'][$i]);
			$reg['corte1'] = get_val($_SESSION['efm']['corte1'][$i]);
			$reg['corte2'] = get_val($_SESSION['efm']['corte2'][$i]);
			$reg['fecha'] = $_SESSION['efm']['fecha'];
			$reg['desc_pastel'] = get_val($_SESSION['efm']['desc_pastel'][$i]);
			$reg['am'] = get_val($_SESSION['efm']['am'][$i]);
			$reg['am_error'] = get_val($_SESSION['efm']['am_error'][$i]);
			$reg['pm'] = get_val($_SESSION['efm']['pm'][$i]);
			$reg['pm_error'] = get_val($_SESSION['efm']['pm_error'][$i]);
			$reg['pastel'] = get_val($_SESSION['efm']['pastel'][$i]);
			
			$sql .= $db->preparar_insert("captura_efectivos", $reg) . ";\n";
			
			// Datos de efectivos
			if ($id = $db->query("SELECT id FROM total_panaderias WHERE num_cia = $reg[num_cia] AND fecha = '$reg[fecha]'")) {
				$sql .= "UPDATE total_panaderias SET venta_puerta = venta_puerta + $reg[venta_pta], pastillaje = pastillaje + $reg[pastillaje], otros = otros + $reg[otros],";
				$sql .= " efectivo = efectivo + $reg[venta_pta] + $reg[pastillaje] + $reg[otros], efe = 'TRUE' WHERE num_cia = $reg[num_cia] AND fecha = '$reg[fecha]';\n";
			}
			else {
				$efe['num_cia'] = $reg['num_cia'];
				$efe['fecha'] = $reg['fecha'];
				$efe['venta_puerta'] = $reg['venta_pta'];
				$efe['pastillaje'] = $reg['pastillaje'];
				$efe['otros'] = $reg['otros'];
				$efe['abono'] = 0;
				$efe['gastos'] = 0;
				$efe['raya_pagada'] = 0;
				$efe['venta_pastel'] = 0;
				$efe['abono_pastel'] = 0;
				$efe['efectivo'] = $reg['venta_pta'] + $reg['pastillaje'] + $reg['otros'];
				$efe['efe'] = "TRUE";
				$efe['exp'] = "FALSE";
				$efe['gas'] = "FALSE";
				$efe['pro'] = "FALSE";
				$efe['pas'] = "FALSE";
				
				$sql .= $db->preparar_insert("total_panaderias", $efe) . ";\n";
			}
		}
	}
	// Liberar datos temporales
	unset($_SESSION['efm']);
	
	// Ejecutar querys
	$db->query($sql);
	
	// Redireccionar página
	$tpl->newBlock("redir");
	$tpl->printToScreen();
	die;
}

if (isset($_POST['fecha'])) {
	$dia = date("d");
	$mes = date("m");
	$anio = date("Y");
	
	// Descomponer fecha de captura
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_POST['fecha'], $fecha);
	$ts = mktime(0, 0, 0, $fecha[2], $fecha[1], $fecha[3]);
	$tsmin = $dia <= 7 ? mktime(0, 0, 0, $mes - 1, 1, $anio) : mktime(0, 0, 0, $mes, 1, $anio);
	$tsmax = mktime(0, 0, 0, $mes, $dia - 1, $anio);
	
	$fecha1 = $fecha[1] <= 7 ? date("d/m/Y", mktime(0, 0, 0, $fecha[2] - 1, 1, $fecha[3])) : date("d/m/Y", mktime(0, 0, 0, $fecha[2], 1, $fecha[3]));
	$fecha2 = $fecha[1] <= 7 ? date("d/m/Y", mktime(0, 0, 0, $fecha[2], 0, $fecha[3])) : date("d/m/Y", mktime(0, 0, 0, $fecha[2], $fecha[1] - 1, $fecha[3]));
	
	// Rangos para clientes
	if (($fecha[1] == 10 && $fecha[2] == 5) || 
	($fecha[1] == 30 && $fecha[2] == 4) || 
	($fecha[1] == 12 && $fecha[2] == 12) || 
	($fecha[1] == 24 && $fecha[2] == 12) || 
	($fecha[1] == 26 && $fecha[2] == 12) || 
	($fecha[1] == 31 && $fecha[2] == 12) ||
	($fecha[1] == 2 && $fecha[2] == 1) || 
	($fecha[1] == 5 && $fecha[2] == 1) || 
	($fecha[1] == 6 && $fecha[2] == 1)) {
		$pro1 = 0.75;
		$pro2 = 3.00;
	}
	else if (($fecha[1] == 25 && $fecha[2] == 12) || ($fecha[1] == 1 && $fecha[2] == 1)) {
		$pro1 = 0;
		$pro2 = 10.00;
	}
	else {
		$pro1 = 0.75;
		$pro2 = 1.25;
	}
	
	// No puede capturar datos para dias posteriores
	if ($ts < $tsmin || $ts > $tsmax) {
		$tpl->newBlock("validar");
		$tpl->assign("mensaje", "Error en la fecha de captura");
		$tpl->assign("campo", "fecha");
		$tpl->printToScreen();
		die;
	}
	
	// Validar que el registro no este ya en la tabla
	for ($i = 0; $i < $numfilas; $i++) {
		$num_cia = $_POST['num_cia'][$i];
		$venta = get_val($_POST['venta_pta'][$i]);
		if ($num_cia > 0 && $venta > 0 && $db->query("SELECT idcaptura_efectivos FROM captura_efectivos WHERE num_cia = $num_cia AND fecha = '$_POST[fecha]'")) {
			$tpl->newBlock("validar");
			$tpl->assign("mensaje", "El registro para la compañia $num_cia ya esta en la base de datos");
			$tpl->assign("campo", "num_cia[$i]");
			$tpl->printToScreen();
			die;
		}
	}
	
	// Validar clientes
	for ($i = 0; $i < $numfilas; $i++) {
		$num_cia = $_POST['num_cia'][$i];
		$venta = get_val($_POST['venta_pta'][$i]);
		$clientes = get_val($_POST['ctes'][$i]);
		if ($num_cia > 0 && $venta > 0) {
			// Obtener el promedio de clientes
			$prom = $db->query("SELECT avg(ctes) AS prom FROM captura_efectivos WHERE num_cia = $num_cia AND fecha BETWEEN '$fecha1' AND '$fecha2'");
			
			if (!in_array($num_cia, array(702, 703)) && $prom[0]['prom'] > 0 && ($clientes < round($prom[0]['prom'] * $pro1) || $clientes > round($prom[0]['prom'] * $pro2))) {
				$tpl->newBlock("validar");
				$tpl->assign("mensaje", "El número de clientes capturado debe estar entre " . round($prom[0]['prom'] * $pro1) . " y " . round($prom[0]['prom'] * $pro2));
				$tpl->assign("campo", "ctes[$i]");
				$tpl->printToScreen();
				die;
			}
		}
	}
	
	// Si no hubo ningun error, almacenar los datos en una variable de sesion y preguntar se se capturan ya los datos
	$_SESSION['efm'] = $_POST;
	$tpl->newBlock("confirmar");
	$tpl->printToScreen();
	die();
}

// Armar captura
$tpl->newBlock("captura");
for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("back", $i > 0 ? $i - 1 : $numfilas - 1);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
}

if (in_array($_SESSION['iduser'], array(1, 4)))
	$sql = "SELECT num_cia, cortes_caja FROM catalogo_companias WHERE num_cia <= 300 ORDER BY num_cia";
else
	$sql = "SELECT num_cia, cortes_caja FROM catalogo_companias LEFT JOIN catalogo_operadoras USING (idoperadora) WHERE iduser = $_SESSION[iduser] ORDER BY num_cia";
$cias = $db->query($sql);

foreach ($cias as $cia) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia['num_cia']);
	$tpl->assign("cortes", $cia['cortes_caja'] > 0 ? $cia['cortes_caja'] : 0);
	
	// Buscar ultimo corte
	$ult_corte1 = $db->query("SELECT corte1 FROM captura_efectivos WHERE num_cia = $cia[num_cia] AND corte1 > 0 ORDER BY fecha DESC LIMIT 1");
	$ult_corte2 = $db->query("SELECT corte2 FROM captura_efectivos WHERE num_cia = $cia[num_cia] AND corte2 > 0 ORDER BY fecha DESC LIMIT 1");
	$tpl->assign("ult_corte1", $ult_corte1 ? $ult_corte1[0]['corte1'] : 0);
	$tpl->assign("ult_corte2", $ult_corte2 ? $ult_corte2[0]['corte2'] : 0);
}

$tpl->printToScreen();
?>