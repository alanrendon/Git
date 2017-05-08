<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

$users = array(28, 29, 30, 31, 32);

//if ($_SESSION['iduser'] != 1) die("MODIFICANDO LA PANTALLA... GOMEN ^_^|");

if (isset($_GET['dias'])) {
	$sql = "SELECT clabe_cuenta2 AS cuenta, folio, a_nombre, importe, CURRENT_DATE AS fecha, CURRENT_DATE + interval '$_GET[dias] days' AS fecha_limite FROM cheques LEFT JOIN catalogo_companias USING (num_cia)";
	$sql .= " WHERE cuenta = 2 AND archivo = 'TRUE' AND num_cheque > 0 AND fecha_cancelacion IS NULL AND num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? '900 AND 998' : '1 AND 899') . " ORDER BY clabe_cuenta2, folio;";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ban_che_seg_san.php?codigo_error=1");
		die;
	}
	
	$db->query("UPDATE cheques SET archivo = 'FALSE' WHERE cuenta = 2 AND archivo = 'TRUE' AND num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? '900 AND 998' : '1 AND 899'));
	
	function fillString($string, $maxlength, $char, $side) {
		$length = strlen($string);
		$filler = "";
		
		for ($i = 0; $i < $maxlength - $length; $i++)
			$filler .= $char;
		
		return $side == -1 ? $filler . $string : $string . $filler;
	}
	
	$row = array();
	foreach ($result as $i => $reg) {
		$row[$i] = fillString($reg['cuenta'], 16, " ", 1) . fillString($reg['folio'], 7, "0", -1) . fillString("", 13, " ", 1) . fillString(substr(trim($reg['a_nombre']), 0, 60), 60, " ", 1);
		$row[$i] .= fillString(number_format($reg['importe'], 2, "", ""), 16, "0", -1) . $reg['fecha'] . str_replace(" 00:00:00", "", $reg['fecha_limite']) . "\r\n";
	}
	
	$nombre_archivo = "CHESEG" . (in_array($_SESSION['iduser'], $users) ? '_ELITE' : '') . date("Ymd") . ".TXT";
	
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=$nombre_archivo");
	
	foreach ($row as $string)
		echo $string;
	
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower("./plantillas/header.tpl");

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_che_seg_san.tpl");
$tpl->prepare();

// Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message",$descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>