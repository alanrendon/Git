<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/excel.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

$descripcion_error[1] = "No existen las compañias especificadas";

function buscar_depositos($dep, $dia) {
	if (!is_array($dep))
		return FALSE;
	
	$index = array();
	$count = 0;
	for ($i = 0; $i < count($dep); $i++)
		if ($dep[$i]['dia'] == $dia) {
			$index[] = $i;
			$count++;
		}
	
	return $count > 0 ? $index : FALSE;
}

function buscar_otros_depositos($dep, $dia) {
	if (!is_array($dep))
		return FALSE;
	
	foreach ($dep as $d)
		if ($d['dia'] == $dia)
			return $d['importe'];
	
	return FALSE;
}

if (isset($_GET['fecha'])) {
	$num_cia = NULL;
	foreach ($_GET['num_cia'] as $value)
		if ($value > 0)
			$num_cia[] = $value;
	
	// Obtener compañias
	$sql = "SELECT num_cia, nombre, clabe_cuenta FROM catalogo_companias WHERE num_cia";
	if (is_array($num_cia)) {
		$sql .= " IN (";
		for ($i = 0; $i < count($num_cia); $i++)
			$sql .= $num_cia[$i] . ($i < count($num_cia) - 1 ? ", " : ")");
		
		if (in_array($_SESSION['iduser'], $users))
			$sql .= " AND num_cia BETWEEN 900 AND 950";
	}
	else if (in_array($_SESSION['iduser'], $users))
		$sql .= " AND num_cia BETWEEN 900 AND 950";
	else
		$sql .= " <= 300 OR num_cia BETWEEN 301 AND 599 OR num_cia BETWEEN 702 AND 720";
	$sql .= " ORDER BY num_cia";
	
	$cia = $db->query($sql);
	
	if (!$cia) {
		header("location: ./ban_efe_xls.php?codigo_error=1");
		die;
	}
	
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $temp);
	$fecha1 = "01/$temp[2]/$temp[3]";
	$fecha2 = $_GET['fecha'];
	
	$data = array(array("Dia" => "", "Efectivo" => "", "Deposito 1" => "", "Deposito 2" => "", "Deposito 3" => "", "Deposito 4" => "", "Deposito 5" => "", "Otros Depositos" => ""));
	$count = 1;
	
	for ($c = 0; $c < count($cia); $c++) {
		// Obtener efectivos
		if ($cia[$c]['num_cia'] > 300 && $cia[$c]['num_cia'] < 600 || $cia[$c]['num_cia'] == 704)
			$sql = "SELECT efectivo, extract(day FROM fecha) AS dia, fecha FROM total_companias WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha";
		else if ($cia[$c]['num_cia'] >= 900 && $cia[$c]['num_cia'] <= 950)
			$sql = "SELECT efectivo, extract(day FROM fecha) AS dia, fecha FROM total_zapaterias WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha";
		else
			$sql = "SELECT efectivo, extract(day FROM fecha) AS dia, fecha FROM total_panaderias WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha";
		$efectivo = $db->query($sql);
		
		// Obtener depósitos
		$sql = "SELECT importe, extract(day FROM fecha) AS dia FROM estado_cuenta WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_mov IN (1, 16, 44, 99) ORDER BY fecha";
		$deposito = $db->query($sql);
		
		// [12-Mar-2008] Obtener otros depositos
		$sql = "SELECT sum(importe) AS importe, extract(day FROM fecha) AS dia FROM otros_depositos WHERE num_cia = {$cia[$c]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY dia ORDER BY dia";
		$otros_depositos = $db->query($sql);
		
		if ($efectivo) {
			// [08-Mar-2007] Obtener efectivos directos posteriores al último día de efectivos
			$last_date = $efectivo[count($efectivo) - 1]['fecha'];
			$sql = "SELECT importe AS efectivo, extract(day from fecha) AS dia FROM importe_efectivos WHERE num_cia = {$cia[$c]['num_cia']} AND fecha > '$last_date' AND fecha <= '$fecha2' ORDER BY fecha";
			$direct = $db->query($sql);
			if ($direct)
				foreach ($direct as $reg)
					$efectivo[] = $reg;
			
			$data[$count++]["Dia"] = "{$cia[$c]['num_cia']} {$cia[$c]['nombre']} {$cia[$c]['clabe_cuenta']}";
			$data[$count++]["Dia"] = "";
			
			$total = array("Efectivo" => 0, "Deposito 1" => 0, "Deposito 2" => 0, "Deposito 3" => 0, "Deposito 4" => 0, "Deposito 5" => 0, "Deposito 6" => 0, "Deposito 7" => 0, "Deposito 8" => 0, "Deposito 9" => 0, "Deposito 10" => 0, "Otros Depositos" => 0);
			for ($i = 0; $i < count($efectivo); $i++) {
				// Día del mes
				$data[$count]["Dia"] = intval($efectivo[$i]['dia']);
				// Efectivo del día
				$data[$count]["Efectivo"] = floatval($efectivo[$i]['efectivo']);
				$total["Efectivo"] += floatval($efectivo[$i]['efectivo']);
				
				// Depósitos del día
				$dep = buscar_depositos($deposito, $efectivo[$i]['dia']);
				if ($dep)
					for ($j = 0; $j < count($dep); $j++) {
						$data[$count]["Deposito " . ($j + 1)] = floatval($deposito[$dep[$j]]['importe']);
						$total["Deposito " . ($j + 1)] += floatval($deposito[$dep[$j]]['importe']);
					}
				
				$otro = buscar_otros_depositos($otros_depositos, $efectivo[$i]['dia']);
				if ($otro) {
					$data[$count]["Otros Depositos"] = floatval($otro);
					$total["Otros Depositos"] += floatval($otro);
				}
				
				$count++;
			}
			$data[$count]["Dia"] = "Total";
			foreach ($total as $key => $value)
				$data[$count][$key] = $value != 0 ? $value : "";
			$count++;
				
			$data[$count++]["Dia"] = "";
		}
	}
	
	// Exportar datos a Excel
	$export_file = "xlsfile://tmp/efectivos.xls";
	$fp = fopen($export_file, "wb");
	if (!is_resource($fp))
		die("Cannot open $export_file");
	
	fwrite($fp, serialize($data));
	fclose($fp);
	
	// Descargar archivo
	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	header ("Content-type: application/x-msexcel");
	header ("Content-Disposition: attachment; filename=\"" . basename($export_file) . "\"" );
	header ("Content-Description: PHP/INTERBASE Generated Data" );
	readfile($export_file);
	exit;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body","./plantillas/ban/ban_efe_xls.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign("fecha", date("d/m/Y"));

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
?>