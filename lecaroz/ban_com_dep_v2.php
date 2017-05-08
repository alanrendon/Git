<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

$descripcion_error[-1] = "El tipo o tamaño del archivo no es correcto.<br>Se permiten archivos .txt y de tamaño no mayor a 1 MB";
$descripcion_error[-2] = "El archivo ya fue cargado en el sistema";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/ban/ban_com_dep_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_POST['MAX_FILE_SIZE'])) {
	$tpl->newBlock("datos");
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		if ($_GET['codigo_error'] < 0)
			$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
		else
			$tpl->assign( "message", "La compañía $_GET[codigo_error] no existe en el catalogo");
	}
	
	$tpl->printToScreen();
	die;
}

$current_date = date("d/m/Y");

$file_type = $_FILES['userfile']['type'];
$file_size = $_FILES['userfile']['size'];
$file_name = $_FILES['userfile']['tmp_name'];

if ($db->query("SELECT hash FROM upload_files WHERE hash = '" . md5_file($file_name) . "'")) {
	header("location: ./ban_com_dep_v2.php?codigo_error=-2");
	die;
}

// Cargar depositos a la  base de datos
$fd = fopen($file_name, "rb");

$today = mktime(0, 0, 0, date("n"), date("d"), date("Y"));
$one_month_ago = mktime(0, 0, 0, date("n") - 1, date("d"), date("Y"));

$cont = 0;
while (!feof($fd)) {
	$buffer = fgets($fd, 96);

	// Dividir cadena en secciones y almacenarlas en variables
	if ($buffer != "") {
		$cia = intval(substr($buffer, 0, 3));
		
		// [12-Septiembre-2008] Si la compañía esta en el rango 101 a 200, cambiarlo al rango 301 a 599
		//if ($cia > 100 && $cia <= 200)
			//$cia = $cia + 200;
		
		//if ($cia < 900) {
			// Si la compañía es 140 o 146, cambiar a 147, si es 171, cambiar a 170, de lo contrario sera la compañía obtenida del archivo
			$line[$cont]['num_cia']  = $cia/* == 140 || $cia == 146 ? 147 : ($cia == 171 ? 170 : $cia)*/;
			$date = mktime(0, 0, 0, intval(substr($buffer, 7, 2)), intval(substr($buffer, 9, 2)), intval(substr($buffer, 3, 4)));
			
			$fecha = $date >= $one_month_ago && $date <= $today ? intval(substr($buffer, 9, 2)) . "/" . intval(substr($buffer, 7, 2)) . "/" . intval(substr($buffer, 3, 4)) : date("d/m/Y");
			
			$line[$cont]['fecha']    = $fecha;
			$line[$cont]['cod_mov']  = intval(substr($buffer, 11, 2))/*intval(substr($buffer, 11, 2)) != 99 ? (intval(substr($buffer, 11, 2)) == 16 && $cia > 900 ? 1 : intval(substr($buffer, 11, 2))) : 1*/;
			$line[$cont]['importe']  = floatval(str_replace("-", "", substr($buffer, 13, 18)) . ".". substr($buffer, 31, 2));
			$line[$cont]['tipo_mov'] = in_array($line[$cont]['cod_mov'], array(19, 48)) ? "TRUE" : "FALSE";
			$line[$cont]['concepto'] = trim(substr($buffer, 43, 50)) != "" ? trim(substr($buffer, 43, 50)) : ($line[$cont]['cod_mov'] != 19 && $line[$cont]['cod_mov'] != 48 && $line[$cont]['cod_mov'] != 13 ? "DEPOSITO COMETRA" : ($line[$cont]['cod_mov'] == 13 ? "SOBRANTE CAJA GENERAL" : ($line[$cont]['cod_mov'] == 19 ? "FAL REP CAJA" : ($line[$cont]['cod_mov'] == 99 ? "CHEQUE" : "FALTANTE (FALSO)"))));
			$line[$cont]['ficha']    = intval(substr($buffer, 33, 10));
			$cont++;
		//}
	}
}
fclose($fd);//echo "<pre>";print_r($line);echo "</pre>";die;

// Función de comparacion para ordenar los datos
function cmp($a, $b) {
	// Descomponer fecha
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{1,2})", $a['fecha'], $fecha_a);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{1,2})", $b['fecha'], $fecha_b);
	
	// Timestamp para comparacion
	$ts_a = mktime(0, 0, 0, $fecha_a[2], $fecha_a[1], $fecha_a[3]);
	$ts_b = mktime(0, 0, 0, $fecha_b[2], $fecha_b[1], $fecha_b[3]);
	
	// Si las compañías son iguales
	if ($a['ficha'] == $b['ficha']) {
		if ($a['num_cia'] == $b['num_cia']) {
			if ($ts_a == $ts_b)
				return 0;
			else
				return $ts_a < $ts_b ? -1 : 1;
		}
		else
			return $a['num_cia'] < $b['num_cia'] ? -1 : 1;
	}
	else
		return $a['ficha'] < $b['ficha'] ? -1 : 1;
}

// Reacomodar datos
usort($line, "cmp");

// Obtener catalogo de compañías
$clabe_cuenta = $_POST['cuenta'] == 1 ? "clabe_cuenta" : "clabe_cuenta2";
$cias = $db->query("SELECT num_cia, nombre_corto, $clabe_cuenta FROM catalogo_companias WHERE num_cia NOT IN (999) ORDER BY num_cia_primaria");

function buscarCia($num_cia) {
	global $cias;
	
	foreach ($cias as $cia)
		if ($num_cia == $cia['num_cia'])
			return $cia;
	
	return FALSE;
}

// Revisar si existen todas las compañías contenidas en el archivo
foreach ($line as $mov)
	if (!buscarCia($mov['num_cia'])) {
		header("location: ./ban_com_dep_v2.php?codigo_error=$mov[num_cia]");
		die;
	}

// Descartar movimientos repetidos
foreach ($line as $i => $mov) {
	$r = 0;
	foreach ($line as $cop)
		if ($mov['num_cia'] == $cop['num_cia'] && $mov['fecha'] == $cop['fecha'] && $mov['cod_mov'] == $cop['cod_mov'] && $mov['importe'] == $cop['importe'] && $mov['ficha'] == $cop['ficha'])
			$r++;
	
	if ($r > 1)
		unset($line[$i]);
}

$sql = "";

// Evaluar movimientos
$total = array();

$iesc = 0;
$iabo = 0;
$icar = 0;

$timestamp = date('d/m/Y H:i:s');

$ficha = NULL;
foreach ($line as $mov)
	if ($mov['importe'] > 0) {
		if ($ficha != $mov['ficha']) {
			$ficha = $mov['ficha'];
			
			$total[$ficha] = 0;
		}
		
		// Estado de Cuenta
		$esc[$iesc]['num_cia']  = $mov['num_cia'];
		$esc[$iesc]['fecha']    = $mov['fecha'];
		$esc[$iesc]['tipo_mov'] = $mov['tipo_mov'];
		$esc[$iesc]['importe']  = $mov['importe'];
		$esc[$iesc]['cod_mov']  = $mov['cod_mov'];
		$esc[$iesc]['concepto'] = $mov['concepto'] . ' ' . $mov['ficha'];
		$esc[$iesc]['cuenta']   = $_POST['cuenta'];
		$esc[$iesc]['ficha']    = $ficha;
		$esc[$iesc]['iduser']   = $_SESSION['iduser'];
		$esc[$iesc]['timestamp'] = $timestamp;
		$esc[$iesc]['tipo_con'] = '0';
		$iesc++;
		
		if ($id = $db->query("SELECT id FROM saldos WHERE num_cia = $mov[num_cia] AND cuenta = $_POST[cuenta]"))
			$sql .= "UPDATE saldos SET saldo_libros = saldo_libros " . ($mov['tipo_mov'] == "FALSE" ? "+" : "-") . " $mov[importe] WHERE num_cia = $mov[num_cia] AND cuenta = $_POST[cuenta];\n";
		
		$total[$ficha] += $mov['cod_mov'] != 13 && $mov['cod_mov'] != 19 && $mov['cod_mov'] != 48 ? $mov['importe'] : 0;
		
		if ($mov['tipo_mov'] == "FALSE") {
			$abo[$iabo]['num_cia'] = $mov['num_cia'];
			$abo[$iabo]['cod_mov'] = $mov['cod_mov'];
			$abo[$iabo]['fecha_mov'] = $mov['fecha'];
			$abo[$iabo]['importe'] = $mov['importe'];
			$abo[$iabo]['concepto'] = $mov['concepto'] . ' ' . $mov['ficha'];
			$abo[$iabo]['fecha_cap'] = $current_date;
			$abo[$iabo]['manual'] = "FALSE";
			$abo[$iabo]['imprimir'] = "TRUE";
			$abo[$iabo]['ficha'] = "FALSE";
			$abo[$iabo]['cuenta'] = $_POST['cuenta'];
			$iabo++;
		}
		else {
			$car[$icar]['num_cia'] = $mov['num_cia'];
			$car[$icar]['cod_mov'] = $mov['cod_mov'];
			$car[$icar]['fecha_mov'] = $mov['fecha'];
			$car[$icar]['importe'] = $mov['importe'];
			$car[$icar]['concepto'] = $mov['concepto'] . ' ' . $mov['ficha'];
			$car[$icar]['fecha_cap'] = $current_date;
			$car[$icar]['manual'] = "FALSE";
			$car[$icar]['imprimir'] = "TRUE";
			$car[$icar]['cuenta'] = $_POST['cuenta'];
			$icar++;
		}
	}

$sql .= $db->multiple_insert("estado_cuenta", $esc);
$sql .= $db->multiple_insert("depositos", $abo);
if ($icar > 0) $sql .= $db->multiple_insert("retiros", $car);

function buscarTot($ficha) {
	global $total;
	
	if (array_key_exists($ficha, $total))
		return $total[$ficha];
	
	return 0;
}

// Obtener faltantes
$fal = array();
$ifal = 0;
foreach ($esc as $mov)
	if ($mov['cod_mov'] == 13 || $mov['cod_mov'] == 19 || $mov['cod_mov'] == 48) {
		$fal[$ifal]['num_cia'] = $mov['num_cia'];
		$fal[$ifal]['fecha'] = $mov['fecha'];
		$fal[$ifal]['importe'] = $mov['importe'];
		$fal[$ifal]['deposito'] = buscarTot($mov['ficha']);
		$fal[$ifal]['tipo'] = $mov['cod_mov'] == 13 ? "TRUE" : "FALSE";
		$fal[$ifal]['descripcion'] = $mov['concepto'];
		$fal[$ifal]['imp'] = "FALSE";
		$fal[$ifal]['implis'] = "TRUE";
		$ifal++;
	}

$sql .= $db->multiple_insert("faltantes_cometra", $fal);

// Almacenar entrada de archivo
$sql .= "INSERT INTO upload_files (hash) VALUES ('" . md5_file($file_name) . "');\n";

// Ejecutar querys
$db->query($sql);

// Desplegar listado de depositos
$tpl->newBlock("listado");

$sql = "SELECT num_cia, cod_mov, fecha_mov, importe, 'f' AS tipo_mov FROM depositos WHERE fecha_cap = '$current_date' AND manual = 'FALSE' AND imprimir = 'TRUE'";
$sql .= " UNION SELECT num_cia, cod_mov, fecha_mov, importe, 't' AS tipo_mov FROM retiros WHERE fecha_cap = '$current_date' AND manual = 'FALSE' AND imprimir = 'TRUE' ORDER BY num_cia, fecha_mov";
$result = $db->query($sql);

$sql = "UPDATE depositos SET imprimir = 'FALSE' WHERE fecha_cap = '$current_date' AND manual = 'FALSE' AND imprimir = 'TRUE';\n";
$sql .= "UPDATE retiros SET imprimir = 'FALSE' WHERE fecha_cap = '$current_date' AND manual = 'FALSE' AND imprimir = 'TRUE';\n";
$db->query($sql);

$tpl->assign("dia", date("d"));
$tpl->assign("anio", date("Y"));
$tpl->assign("mes", mes_escrito(date("n")));

$cat_mov = $_POST['cuenta'] == 1 ? "catalogo_mov_bancos" : "catalogo_mov_santander";
// Catalogo de movimientos
$sql = "SELECT cod_mov, descripcion FROM $cat_mov GROUP BY cod_mov, descripcion ORDER BY cod_mov";
$cat_cod = $db->query($sql);

function buscarCod($cod_mov) {
	global $cat_cod;
	
	foreach ($cat_cod as $cod)
		if ($cod_mov == $cod['cod_mov'])
			return $cod['descripcion'];
	
	return FALSE;
}

$total = 0;
foreach ($result as $mov) {
	$tpl->newBlock("fila");
	
	$cia = buscarCia($mov['num_cia']);
	$tpl->assign("num_cia", $cia['num_cia']);
	$tpl->assign("cuenta", $cia[$clabe_cuenta]);
	$tpl->assign("nombre", $cia['nombre_corto']);
	$tpl->assign("cod_mov", $mov['cod_mov']);
	$tpl->assign("descripcion", buscarCod($mov['cod_mov']));
	$tpl->assign("importe", number_format($mov['importe'], 2, ".", ","));
	$tpl->assign("fecha", $mov['fecha_mov']);
	$total += $mov['tipo_mov'] == "f" ? $mov['importe'] : -$mov['importe'];
}
$tpl->assign("listado.total", number_format($total, 2, ".", ","));

// Desplegar listado de faltantes
$sql = "SELECT num_cia, nombre_corto, fecha, importe, tipo, descripcion, deposito FROM faltantes_cometra LEFT JOIN catalogo_companias USING (num_cia) WHERE implis = 'TRUE' ORDER BY num_cia, fecha, tipo";
$result = $db->query($sql);

if ($result) {
	$db->query("UPDATE faltantes_cometra set implis = 'FALSE' WHERE implis = 'TRUE'");
	
	$tpl->newBlock("faltantes");
	$tpl->assign("dia", date("d"));
	$tpl->assign("mes", mes_escrito(date("n")));
	$tpl->assign("anio", date("Y"));
	
	$num_cia = NULL;
	$faltantes = 0;
	$sobrantes = 0;
	for ($i = 0; $i < count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			if ($num_cia != NULL && $count > 1) {
				$tpl->newBlock("totales");
				$tpl->assign("faltante", number_format($faltante, 2, ".", ","));
				$tpl->assign("sobrante", number_format($sobrante, 2, ".", ","));
				$tpl->assign("diferencia", number_format($faltante - $sobrante, 2, ".", ","));
			}
			
			$num_cia = $result[$i]['num_cia'];
			
			$tpl->newBlock("cia_fal");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
			
			$faltante = 0;
			$sobrante = 0;
			
			$count = 0;
		}
		$tpl->newBlock("fila_fal");
		$tpl->assign("fecha", $result[$i]['fecha']);
		$tpl->assign("deposito", number_format($result[$i]['deposito'], 2, ".", ","));
		$tpl->assign("descripcion", $result[$i]['descripcion']);
		$tpl->assign("faltante", $result[$i]['tipo'] == "f" ? number_format($result[$i]['importe'], 2, ".", ",") : "");
		$tpl->assign("sobrante", $result[$i]['tipo'] == "t" ? number_format($result[$i]['importe'], 2, ".", ",") : "");
		
		$faltante += $result[$i]['tipo'] == "f" ? $result[$i]['importe'] : 0;
		$sobrante += $result[$i]['tipo'] == "t" ? $result[$i]['importe'] : 0;
		
		$faltantes += $result[$i]['tipo'] == "f" ? $result[$i]['importe'] : 0;
		$sobrantes += $result[$i]['tipo'] == "t" ? $result[$i]['importe'] : 0;
		
		$count++;
	}
	if ($num_cia != NULL && $count > 1) {
		$tpl->newBlock("totales");
		$tpl->assign("faltante", number_format($faltante, 2, ".", ","));
		$tpl->assign("sobrante", number_format($sobrante, 2, ".", ","));
		$tpl->assign("diferencia", number_format($faltante - $sobrante, 2, ".", ","));
	}
	$tpl->assign("faltantes.faltantes", number_format($faltantes, 2, ".", ","));
	$tpl->assign("faltantes.sobrantes", number_format($sobrantes, 2, ".", ","));
	$tpl->assign("faltantes.diferencia", number_format($faltantes - $sobrantes, 2, ".", ","));
}

$tpl->printToScreen();
?>