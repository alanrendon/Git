<?php
include 'class.db.inc.php';
include 'dbstatus.php';

function scandir($dir, $sort = 1) {
	$dh = opendir($dir);
	$files = array();
	while (false !== ($filename = readdir($dh)))
		$files[] = $filename;
	
	if (count($files) == 0) return FALSE;
	
	if ($sort == 1) sort($files);
	else rsort($files);
	
	return $files;
}

// Directorio contenedor
$dir = '/var/ftp/pub/lecaroz/envio';
$dir_zap = '/var/ftp/pub/lecaroz/zapatas';

// Timestamp del dia actual
$week_ago = mktime(0, 0, 0, date('n'), date('d') - 8, date('Y'));
$today = mktime(0, 0, 0);

// Obtener directorio
$files = scandir($dir);
// Eliminar directorio padre y raiz del listado
array_shift($files);
array_shift($files);

echo "---------[ " . date('d/m/Y H:i') . " ]---------\n";

//if (!$files)
	//die("-- No hay archivos --\n---------[     END LOG      ]---------\n\n");

$db = new DBclass($dsn, 'autocommit=yes,mostrar_errores=no,en_error_desconectar=no');

// Explorar y validar archivos
echo "-- Adquiriendo archivos --\n";
$cont = 0;
if ($files)
	foreach ($files as $file) {
		// Desglozar el nombre del archivo y validarlo
		if (!ereg("([0-9]{1,3})-([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2}).sql", $file, $tmp)) {
			echo "\t$file --> Nombre de archivo no valido.\n";
			continue;
		}
		
		// Timestamp de los datos
		$ts_file = mktime(0, 0, 0, $tmp[3], $tmp[4], $tmp[2]);
		
		// Validar que el archivo no sea d hoy o dias posteriores [29-Mar-2007] Se omitio a petición de Mario
		if ($ts_file >= $today && time() < mktime(21)) {
			echo "\t$file --> El archivo no puede ser del día [antes de las 21:00 horas] de hoy o días posteriores.\n";
			continue;
		}
		
		// Validar que el archivo no tenga una semana de antigüedad
		if ($ts_file < $week_ago) {
			echo "\t$file --> El archivo no puede tener una semana de antigüedad.\n";
			continue;
		}
		
		// Obtener datos principales del archivo
		$num_cia = $tmp[1];
		$fecha = "$tmp[4]/$tmp[3]/$tmp[2]";
		
		// Validar que el archivo no haya sido ya insertado en las tablas temporales
		if ($ts_ins = $db->query("SELECT ts_ins FROM efectivos_tmp WHERE num_cia = $num_cia AND fecha = '$fecha'")) {
			echo "\t$file --> El archivo ya ha sido insertado en la base de datos [{$ts_ins[0]['ts_ins']}].\n";
			continue;
		}
		
		// Obtener contenido del archivo
		$content = file_get_contents($dir . '/' . $file);
		
		// Cadena principal de inserciones
		$sql = $content;
		
		// Ejecutar script de datos
		if ($db->query($sql) < 0) {
			echo "\t$file --> El archivo contiene errores y no pudo ser procesado:\n";
			echo "\t\tERROR: $db->ultimo_error\n";
			continue;
		}
		
		// El archivo se proceso correctamente
		echo "\t$file --> >> OK <<\n";
		// Eliminar el archivo del directorio contenedor
		unlink($dir . '/' . $file);
		
		$cont++;
	}

/***** PROCESO DE ZAPATERIAS *****/
// Obtener directorio
$files = scandir($dir_zap);
// Eliminar directorio padre y raiz del listado
array_shift($files);
array_shift($files);

if (!$files)
	die("-- No hay archivos --\n---------[     END LOG      ]---------\n\n");

foreach ($files as $file) {
	// Desglozar el nombre del archivo y validarlo
	if (!ereg("([0-9]{1,3})-([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2}).sql", $file, $tmp)) {
		echo "\t$file --> Nombre de archivo no valido.\n";
		continue;
	}
	
	// Timestamp de los datos
	$ts_file = mktime(0, 0, 0, $tmp[3], $tmp[4], $tmp[2]);
	
	// Validar que el archivo no sea d hoy o dias posteriores [29-Mar-2007] Se omitio a petición de Mario
	if ($ts_file >= $today && time() < mktime(21)) {
		echo "\t$file --> El archivo no puede ser del día [antes de las 21:00 horas] de hoy o días posteriores.\n";
		continue;
	}
	
	// Validar que el archivo no tenga una semana de antigüedad
	if ($ts_file < $week_ago) {
		echo "\t$file --> El archivo no puede tener una semana de antigüedad.\n";
		continue;
	}
	
	// Obtener datos principales del archivo
	$num_cia = $tmp[1];
	$fecha = "$tmp[4]/$tmp[3]/$tmp[2]";
	
	// Validar que el archivo no haya sido ya insertado en las tablas temporales
	if ($ts_ins = $db->query("SELECT ts_ins FROM ventadia_tmp WHERE num_cia = $num_cia AND fecha = '$fecha'")) {
		echo "\t$file --> El archivo ya ha sido insertado en la base de datos [{$ts_ins[0]['ts_ins']}].\n";
		continue;
	}
	
	// Obtener contenido del archivo
	$content = file_get_contents($dir_zap . '/' . $file);
	
	// Cadena principal de inserciones
	$sql = $content;
	
	// Ejecutar script de datos
	if ($db->query($sql) < 0) {
		echo "\t$file --> El archivo contiene errores y no pudo ser procesado:\n";
		echo "\t\tERROR: $db->ultimo_error\n";
		continue;
	}
	
	// El archivo se proceso correctamente
	echo "\t$file --> >> OK <<\n";
	// Eliminar el archivo del directorio contenedor
	unlink($dir_zap . '/' . $file);
	
	$cont++;
}

if ($cont > 0)
	echo "-- Archivos adquiridos: $cont --\n";
else
	echo "-- No se adquirieron archivos --\n";

echo "---------[     END LOG      ]---------\n\n";
?>
