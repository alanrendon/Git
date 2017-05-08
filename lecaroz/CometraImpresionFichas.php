<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');
include('includes/phpmailer/class.phpmailer.php');

if (!function_exists('json_encode')) {
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
	1  => 'Enero',
	2  => 'Febrero',
	3  => 'Marzo',
	4  => 'Abril',
	5  => 'Mayo',
	6  => 'Junio',
	7  => 'Julio',
	8  => 'Agosto',
	9  => 'Septiembre',
	10 => 'Octubre',
	11 => 'Noviembre',
	12 => 'Diciembre'
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

//if ($_SESSION['iduser'] != 1) die('EN PROCESO DE ACTUALIZACION');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'imprimir':
			$condiciones = array();
			
			$condiciones[] = 'num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
			
			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
				$cias = array();
				
				$pieces = explode(',', $_REQUEST['cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}
				
				if (count($cias) > 0) {
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			$sql = '
				SELECT
					num_cia,
					nombre
						AS nombre_cia,
					cliente_cometra,
					TRIM(regexp_replace(direccion, \'\s+\', \' \', \'g\'))
						AS domicilio
				FROM
					catalogo_companias
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$string = '';
				
				foreach ($result as $rec) {
					foreach (range(1, $_REQUEST['cantidad']) as $i) {
						//$string .= str_pad('', 2, "\n");
						$string .= str_pad('', 12, ' ') . 'PAQUETES_________';
						$string .= str_pad('', 5, "\n");
						$string .= str_pad('', 52, ' ') . 'X';
						$string .= str_pad('', 2, "\n");
						$string .= str_pad('', 3, ' ') . str_pad($rec['cliente_cometra'], 8, '0') . str_pad('', 27, ' ') . 'X';
						$string .= str_pad('', 2, "\n");
						$string .= str_pad('', 6, ' ') . substr($rec['num_cia'] . '-' . $rec['nombre_cia'], 0, 80);
						$string .= str_pad('', 2, "\n");
						$string .= str_pad('', 4, ' ') . substr($rec['domicilio'], 0, 82);
						$string .= str_pad('', 11, "\n");
						$string .= str_pad('', 5, ' ') . 'CAJA GENERAL COMETRA';
						$string .= str_pad('', 2, "\n");
						$string .= str_pad('', 5, ' ') . 'CAJA GENERAL';
						$string .= str_pad('', 4, "\n");
						$string .= str_pad('', 6, ' ') . 'CHEQ';
						$string .= str_pad('', 1, "\n");
						$string .= str_pad('', 6, ' ') . 'PAN';
						$string .= str_pad('', 1, "\n");
						$string .= str_pad('', 6, ' ') . 'PAN';
						$string .= str_pad('', 1, "\n");
						$string .= str_pad('', 6, ' ') . 'PAN';
						$string .= str_pad('', 1, "\n");
						$string .= str_pad('', 6, ' ') . 'POLL';
						$string .= str_pad('', 1, "\n");
						$string .= str_pad('', 6, ' ') . 'POLL';
						$string .= str_pad('', 1, "\n");
						$string .= str_pad('', 6, ' ') . 'POLL';
						$string .= str_pad('', 1, "\n");
						$string .= str_pad('', 6, ' ') . 'P FAL';
						$string .= str_pad('', 16, "\n");
					}
				}
				
				shell_exec("chmod ugo=rwx pcl");
				
				$fp = fopen('pcl/ComprobantesCometra.txt', 'w');
				
				fwrite($fp, $string);
				
				fclose($fp);
				
				shell_exec('lpr -l -P cometra pcl/ComprobantesCometra.txt');
				
				shell_exec("chmod ugo=r pcl");
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ban/CometraImpresionFichas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
