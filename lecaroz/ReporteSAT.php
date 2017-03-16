<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

if(!function_exists('json_encode')) {
	include_once('includes/JSON.php');
	
	$GLOBALS['JSON_OBJECT'] = new Services_JSON();
	
	function json_encode($value) {
		return $GLOBALS['JSON_OBJECT']->encode($value); 
	}
	
	function json_decode($value) {
		return $GLOBALS['JSON_OBJECT']->decode($value); 
	}
}

$_meses = array(
	1  => 'ENERO',
	2  => 'FEBRERO',
	3  => 'MARZO',
	4  => 'ABRIL',
	5  => 'MAYO',
	6  => 'JUNIO',
	7  => 'JULIO',
	8  => 'AGOSTO',
	9  => 'SEPTIEMBRE',
	10 => 'OCTUBRE',
	11 => 'NOVIEMBRE',
	12 => 'DICIMEBRE'
);

$_dias = array(
	0 => 'D',
	1 => 'L',
	2 => 'M',
	3 => 'M',
	4 => 'J',
	5 => 'V',
	6 => 'S'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die('MODIFICANDO');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'reporte':
			$sql = '
				
			';
			
			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename="ConsumosDeMas.csv"');
			
			echo $data;
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/ReporteSAT.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');



$tpl->printToScreen();
?>
