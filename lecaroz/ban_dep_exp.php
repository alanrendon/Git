<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Formato de archivo no valido";
$descripcion_error[2] = "Una de las compañías en el archivo no existe en el catalogo";

$db = new DBclass($dsn, "autocommit=yes");

// Función de comparacion para ordenar los datos
function cmp($a, $b) {
	// Descomponer fecha
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{1,2})", $a['fecha_mov'], $fecha_a);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{1,2})", $b['fecha_mov'], $fecha_b);
	
	// Timestamp para comparacion
	$ts_a = mktime(0, 0, 0, $fecha_a[2], $fecha_a[1], $fecha_a[3]);
	$ts_b = mktime(0, 0, 0, $fecha_b[2], $fecha_b[1], $fecha_b[3]);
	
	// Si las compañías son iguales
	if ($a['num_cia'] == $b['num_cia']) {
		if ($ts_a == $ts_b) {
			if ($a['importe'] == $b['importe'])
				return 0;
			else
				return $a['importe'] < $b['importe'] ? -1 : 1;
		}
		else
			return $ts_a < $ts_b ? -1 : 1;
	}
	else
		return $a['num_cia'] < $b['num_cia'] ? -1 : 1;
}

function buscar_cia($array, $num_cia) {
	$num_elementos = count($array);
	if ($num_elementos < 1)
		return FALSE;
	
	$count = 0;
	for ($i = 0; $i < $num_elementos; $i++)
		if ($array[$i]['num_cia'] == $num_cia)
			return $i;
	
	return FALSE;
}

if (isset($_POST['MAX_FILE_SIZE'])) {
	$sql = "SELECT num_cia, nombre, clabe_cuenta FROM catalogo_companias ORDER BY num_cia";
	$cias = $db->query($sql);
	
	// Datos del archivo
	$nombre_archivo = $_FILES['userfile']['name'];
	$tipo_archivo   = $_FILES['userfile']['type'];
	$tamano_archivo = $_FILES['userfile']['size'];
	$archivo_temp   = $_FILES['userfile']['tmp_name'];

	// Comprobar características del fichero
	if (!(stristr($tipo_archivo, "text/plain") && $tamano_archivo < 1048576)) {
		header("location: ./ban_dep_exp.php?codigo_error=1");
		die;
	}
	else {
		// Cargar depositos a un arreglo temporal
		$fd = fopen($_FILES['userfile']['tmp_name'], "rb");
		$count = 0;
		while (!feof($fd)) {
			// Obtener cadena del archivo y almacenarlo en el buffer
			$buffer = fgets($fd, 46);

			// Dividir cadena en secciones y almacenarlas en variables
			if ($buffer != "") {
				$temp_cia = (int)substr($buffer, 0, 3);
				// Si la compañía es 140 o 146, cambiar a 147, si es 171, cambiar a 170, de lo contrario sera la compañía obtenida del archivo
				$datos[$count]['num_cia']   = $temp_cia == 140 || $temp_cia == 146 ? 147 : ($temp_cia == 171 ? 170 : $temp_cia);
				$datos[$count]['fecha_mov'] = substr($buffer, 9, 2) . "/" . substr($buffer, 7, 2) . "/" . substr($buffer, 3, 4);
				$temp_cod = number_format(substr($buffer, 11, 2), 0, "", "");
				$datos[$count]['cod_mov']   = ($datos[$count]['num_cia'] > 100 && $datos[$count]['num_cia'] < 200) || ($datos[$count]['num_cia'] > 701 && $datos[$count]['num_cia'] < 750) ? 16 : $temp_cod;
				$datos[$count]['importe']   = (float)(substr($buffer, 13, 18) . "." . substr($buffer, 31, 2));
				$datos[$count]['ficha']     = substr($buffer, 33, 10);
				$count++;
			}
		}
		fclose($fd);
		
		// Ordenar datos
		//usort($datos, "cmp");
		
		// Revisar si existen todas las compañías contenidas en el archivo
		for ($i = 0; $i < $count; $i++)
			if (buscar_cia($cias, $datos[$i]['num_cia']) === FALSE) {
				header("location: ./ban_dep_exp.php?codigo_error=2");
				die;
			}
		
		// Revisar y descartar repetidos
		$index = 0;
		for ($i = 0; $i < $count; $i++) {
			$rep = 0; // Variable contador para registros repetidos
			for ($j = $i; $j < $count; $j++)
				if ($datos[$i]['num_cia'] == $datos[$j]['num_cia'] && $datos[$i]['fecha_mov'] == $datos[$j]['fecha_mov'] && $datos[$i]['cod_mov'] == $datos[$j]['cod_mov'] && $datos[$i]['importe'] == $datos[$j]['importe'])
					$rep++;
			if ($rep == 1) {
				$dep[$index]['num_cia']    = $datos[$i]['num_cia'];
				$cia = buscar_cia($cias, $dep[$index]['num_cia']);
				$dep[$index]['nombre_cia'] = $cias[$cia]['nombre'];
				$dep[$index]['cuenta']     = $cias[$cia]['clabe_cuenta'];
				$dep[$index]['fecha_mov']  = $datos[$i]['fecha_mov'];
				$dep[$index]['cod_mov']    = $datos[$i]['cod_mov'];
				$dep[$index]['importe']    = $datos[$i]['importe'];
				$dep[$index]['ficha']      = $datos[$i]['ficha'];

				$index++;
			}
		}
		
		header("Content-Type: application/download");
		header("Content-Disposition: attachment; filename=depositos.csv");
		
		echo '"CIA.","NOMBRE","CUENTA","FECHA","CODIGO","FICHA","IMPORTE"' . "\n";
		$ficha = NULL;
		$count = 0;
		for ($i = 0; $i < count($dep); $i++) {
			if ($ficha != $dep[$i]['ficha']) {
				$ficha = $dep[$i]['ficha'];
				$count++;
			}
			
			echo "\"{$dep[$i]['num_cia']}\",";
			echo "\"{$dep[$i]['nombre_cia']}\",";
			echo "\"{$dep[$i]['cuenta']}\",";
			echo "\"{$dep[$i]['fecha_mov']}\",";
			echo "\"{$dep[$i]['cod_mov']}\",";
			echo "\"{$dep[$i]['ficha']}\",";
			echo "\"{$dep[$i]['importe']}\"\n";
		}
		echo "\"$count\"";
	}
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_dep_exp.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");


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
$db->desconectar();
?>