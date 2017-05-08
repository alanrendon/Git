<?php
include('/var/www/lecaroz/includes/class.db.inc.php');
include('/var/www/lecaroz/includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes,mostrar_errores=no,en_error_desconectar=no');

$rep_path = '/home/lecaroz/facturas/';
$bak_path = '/home/lecaroz/facturas/backup/';
$dup_path = '/home/lecaroz/facturas/duplicate/';
$err_path = '/home/lecaroz/facturas/error/';
$log_path = '/home/lecaroz/facturas/log/';

$url_zap = 'http://localhost/lecaroz/FacturasElectronicasAutomatico.php';
$url_pan = 'http://localhost/lecaroz/FacturasElectronicasAutomaticoPanaderias.php';

function filter($file) {
	return strpos($file, '.sql');
}

$files = array_filter(scandir($rep_path), 'filter');

if (count($files) > 0) {
	echo '--- ESCANEO DE REPOSITORIO: ' . date('d/m/Y H:i:s') . ' ---';
	
	foreach ($files as $file) {
		$content = file_get_contents($rep_path . $file);
		
		if ($db->query($content) < 0) {
			echo "\n@@ " . $file . ': El archivo contiene errores y no pudo ser procesado: ' . utf8_decode($db->ultimo_error);
			
			rename($rep_path . $file, $err_path . $file);
			
			continue;
		}
		else {
			echo "\n@@ " . $file . ': Archivo insertado';
			
			rename($rep_path . $file, $bak_path . $file);
		}
	}
}

if ($db->query('SELECT * FROM facturas_panaderias_tmp WHERE tsreg IS NULL AND num_cia < 900 LIMIT 1')) {
	echo "\n\n";
	
	$result = @file_get_contents($url_pan);
	
	if ($result !== FALSE) {
		echo $result;
	}
	else {
		echo "@@ [" . date('d/m/Y H:i:s') . "] No se puede acceder al proceso automático de facturación [PANADERIAS]\n\n";
	}
}

// if ($db->query('SELECT * FROM facturas_panaderias_tmp WHERE tsreg IS NULL AND num_cia >= 900 LIMIT 1')) {
// 	echo "\n\n";
	
// 	$result = @file_get_contents($url_zap);
	
// 	if ($result !== FALSE) {
// 		echo $result;
// 	}
// 	else {
// 		echo "@@ [" . date('d/m/Y H:i:s') . "] No se puede acceder al proceso automático de facturación [ZAPATERIAS]\n\n";
// 	}
// }

?>
