<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay cheques pendientes";
$descripcion_error[2] = "No se pudo crear el archivo";
$mensaje[1] = "Se creo el archivo correctamente, favor de revisar la carpeta de archivos";

$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

function rellenaCeros($numCeros, $posicion = 0, $cadena = "") {
	$longitud = strlen((string)$cadena);
	
	$relleno = $numCeros - $longitud;
	$nueva_cadena = (string)$cadena;
	
	if ($relleno > 0)
		for ($i = 0; $i < $relleno; $i++) {
			// Concatenar ceros a la izquierda
			if ($posicion == 0)
				$nueva_cadena = "0" . $nueva_cadena;
			// Concatenar ceros a la derecha
			else
				$nueva_cadena .= "0";
		}
	
	return $nueva_cadena;
}

function buscarCia($num_cia) {
	for ($i = 0; $i < count($GLOBALS['cia']); $i++)
		if ($num_cia == $GLOBALS['cia'][$i]['num_cia'])
			return $i;
	
	return FALSE;
}

function buscarImporte($num_cia) {
	for ($i = 0; $i < count($GLOBALS['importe_total']); $i++)
		if ($num_cia == $GLOBALS['importe_total'][$i]['num_cia'])
			return $i;
	
	return FALSE;
}

function buscarNumReg($num_cia) {
	for ($i = 0; $i < count($GLOBALS['importe_total']); $i++)
		if ($num_cia == $GLOBALS['importe_total'][$i]['num_cia'])
			return $i;
	
	return FALSE;
}

if (isset($_GET['generar'])) {
	// Obtener datos de las compañías
	$cia = $db->query("SELECT num_cia, emisora, clabe_cuenta FROM catalogo_companias ORDER BY num_cia");
	
	// Importes totales
	$sql = "SELECT num_cia, sum(importe) AS importe_total, count(importe) AS num_reg FROM estado_cuenta WHERE num_cia = 1 AND cod_mov = 5 AND fecha_con IS NULL GROUP BY num_cia ORDER BY num_cia";
	$importe_total = $db->query($sql);
	
	// Obtener los cheques que no se han conciliado
	$sql = "SELECT num_cia, fecha, importe, clabe_cuenta, folio FROM estado_cuenta LEFT JOIN catalogo_companias USING (num_cia) WHERE num_cia = 1 AND cod_mov = 5 AND fecha_con IS NULL AND folio > 0 ORDER BY num_cia, folio";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ban_pro_che.php?codigo_error=1");
		die;
	}
	
	// Crear archivo en el servidor
	$filename = "/samba/cheprobanorte/60" . (int)date("y") . date("md") . "1.dis";
	$fp = fopen($filename, "w");
	
	// Si ocurrio un error al crear el archivo
	if (!$fp) {
		header("location: ./ban_pro_che.php?codigo_error=2");
		die;
	}
	
	$fecha_actual = date("dmY");
	
	$relleno1 = "";
	for ($i = 0; $i < 173; $i++)
		$relleno1 .= " ";
	
	$relleno2 = "";
	for ($i = 0; $i < 93; $i++)
		$relleno2 .= " ";
	
	$num_cia = NULL;
	for ($i = 0; $i < count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			$num_cia = $result[$i]['num_cia'];
			
			$index_cia = buscarCia($num_cia);
			
			// Construir encabezado
			$header = "0";
			$header .= rellenaCeros(5, 0, $cia[$index_cia]['emisora']);
			$header .= "60";
			$header .= $fecha_actual;
			$header .= "01";
			$header .= rellenaCeros(6, 0, $importe_total[buscarNumReg($num_cia)]['num_reg']);
			$header .= rellenaCeros(18, 0, number_format($importe_total[buscarImporte($num_cia)]['importe_total'], 2, "", ""));
			$header .= "$relleno1\r\n";
			
			// Poner encabezado en el archivo archivo
			fputs($fp, $header);
			
			$secuencia = 1;
		}
		$detalle = "1";
		$detalle .= rellenaCeros(6, 0, $secuencia);
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $result[$i]['fecha'], $temp);
		$detalle .= $temp[1] . $temp[2] . $temp[3];
		$detalle .= "60";
		$detalle .= "00072";
		$detalle .= rellenaCeros(15, 0, number_format($result[$i]['importe'], 2, "", ""));
		$detalle .= rellenaCeros(11, 0, $result[$i]['clabe_cuenta']);
		$detalle .= rellenaCeros(20);
		$detalle .= rellenaCeros(15, 0, $result[$i]['folio']);
		$detalle .= rellenaCeros(15);
		$detalle .= $relleno2;
		$detalle .= rellenaCeros(8);
		$detalle .= "                \r\n";
		
		fputs($fp, $detalle);
		
		$secuencia++;
	}
	fclose($fp);
	
	header("location: ./ban_pro_che.php?mensaje=1");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_pro_che.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");


// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $mensaje[$_GET['mensaje']]);
}

// Imprimir el resultado
$tpl->printToScreen();
?>