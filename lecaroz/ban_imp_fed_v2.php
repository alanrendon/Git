<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$descripcion_error[1] = "ERROR AL LEER EL ARCHIVO";
$descripcion_error[2] = "NO HAY RESULTADOS";

function buscar_cia($catCias, $cuenta) {
	for ($i = 0; $i < count($catCias); $i++)
		if ($cuenta == $catCias[$i]['clabe_cuenta2'])
			return $catCias[$i];
	
	return FALSE;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_imp_fed_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_FILES['file'])) {
	$tpl->newBlock("datos");
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
	}
	
	$tpl->printToScreen();
	die;
}

// Cargar catálogo de compañías
$catCias = $db->query("SELECT num_cia, nombre, clabe_cuenta2 FROM catalogo_companias WHERE clabe_cuenta2 IS NOT NULL ORDER BY clabe_cuenta2");

$j = 0;
// Abrir el archivo
$file_name = $_FILES['file']['name'];
$lines = gzfile($_FILES['file']['tmp_name']);

// Recorrer el archivo
$num_cia = NULL;
$cont = 0;
foreach ($lines as $line) {
	$cia = buscar_cia($catCias, trim(substr($line, 0, 11)));
	$cod = substr($line, 32, 4);
	
	if ($cia['num_cia'] > 0 && ($cod == 990 || $cod == 740 || $cod == 741)) {
		if ($num_cia != $cia['num_cia']) {
			$num_cia = $cia['num_cia'];
			
			// Obtener ultimo folio
			$temp = $db->query("SELECT folio FROM folios_cheque WHERE num_cia = $num_cia AND cuenta = 2 ORDER BY folio DESC LIMIT 1");
			$folio_cheque = $temp ? $temp[0]['folio'] + 1 : 1;
		}
		
		$mov[$cont]['num_cia'] = $num_cia;
		$mov[$cont]['nombre_cia'] = $cia['nombre'];
		$mov[$cont]['clabe_cuenta'] = trim(substr($line, 0, 11));
		$mov[$cont]['cuenta'] = 2;
		$mov[$cont]['fecha'] = substr($line, 18, 2) . "/" . substr($line, 16, 2) . "/" . substr($line, 20, 4);
		$mov[$cont]['tipo_mov'] = "TRUE";
		$mov[$cont]['importe'] = floatval(substr($line, 77, 12) . "." . substr($line, 89, 2));
		$mov[$cont]['concepto'] = $cod == 990 ? "PAGO DE IMPUESTOS FEDERALES" : ($cod == 740 ? "PAGO IMSS" : "PAGO INFONAVIT");
		$mov[$cont]['cod_mov'] = $cod == 990 ? 33 : 43;
		$mov[$cont]['folio'] = $folio_cheque;
		
		$cheque[$cont]['num_cia']       = $num_cia;
		$cheque[$cont]['num_proveedor'] = $cod == 990 ? 237 : 235;
		$cheque[$cont]['a_nombre']      = $cod == 990 ? "TESORERIA DE LA FEDERACION" : "INSTITUTO MEXICANO DEL SEGURO SOCIAL";
		$cheque[$cont]['fecha']         = $mov[$cont]['fecha'];
		$cheque[$cont]['importe']       = $mov[$cont]['importe'];
		$cheque[$cont]['iduser']        = $_SESSION['iduser'];
		$cheque[$cont]['imp']           = "FALSE";
		$cheque[$cont]['concepto']      = $mov[$cont]['concepto'];
		$cheque[$cont]['cod_mov']       = $cod == 990 ? 33 : 43;
		$cheque[$cont]['codgastos']     = $cod == 990 ? 140 : 141;
		$cheque[$cont]['folio']         = $folio_cheque;
		$cheque[$cont]['cuenta']        = 2;
		$cheque[$cont]['poliza']        = "FALSE";
		
		$gasto[$cont]['num_cia']       = $num_cia;
		$gasto[$cont]['fecha']         = $mov[$cont]['fecha'];
		$gasto[$cont]['importe']       = $mov[$cont]['importe'];
		$gasto[$cont]['concepto']      = $mov[$cont]['concepto'];
		$gasto[$cont]['codgastos']     = $cod == 990 ? 140 : 141;
		$gasto[$cont]['folio']         = $folio_cheque;
		$gasto[$cont]['captura']       = "TRUE";
		
		$folio[$cont]['num_cia']   = $num_cia;
		$folio[$cont]['folio']     = $folio_cheque++;
		$folio[$cont]['reservado'] = "FALSE";
		$folio[$cont]['utilizado'] = "TRUE";
		$folio[$cont]['fecha']     = $mov[$cont]['fecha'];
		$folio[$cont]['cuenta']    = 2;
		
		$cont++;
	}
}

if ($cont == 0) {
	header("location: ./ban_imp_fed_v2.php?codigo_error=1");
	die;
}

$sql  = $db->multiple_insert("estado_cuenta", $mov);
$sql .= $db->multiple_insert("cheques", $cheque);
$sql .= $db->multiple_insert("movimiento_gastos", $gasto);
$sql .= $db->multiple_insert("folios_cheque", $folio);
$db->query($sql);
$db->desconectar();//echo "<pre>$sql</pre>";die;

$tpl->newBlock("listado");
$tpl->assign("dia", date("d"));
$tpl->assign("mes", mes_escrito(date("n")));
$tpl->assign("anio", date("Y"));

// Función de comparacion para ordenar los datos
function cmp($a, $b) {
	// Descomponer fecha
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{1,2})", $a['fecha'], $fecha_a);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{1,2})", $b['fecha'], $fecha_b);
	
	// Timestamp para comparacion
	$ts_a = mktime(0, 0, 0, $fecha_a[2], $fecha_a[1], $fecha_a[3]);
	$ts_b = mktime(0, 0, 0, $fecha_b[2], $fecha_b[1], $fecha_b[3]);
	
	// Si las compañías son iguales
	if ($a['num_cia'] == $b['num_cia']) {
		if ($ts_a == $ts_b) {
			if ($a['folio'] == $b['folio'])
				return 0;
			else
				return $a['folio'] < $b['folio'] ? -1 : 1;
		}
		else
			return $ts_a < $ts_b ? -1 : 1;
	}
	else
		return $a['num_cia'] < $b['num_cia'] ? -1 : 1;
}
usort($mov, "cmp");


for ($i = 0; $i < $cont; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("num_cia", $mov[$i]['num_cia']);
	$tpl->assign("nombre_cia", $mov[$i]['nombre_cia']);
	$tpl->assign("cuenta", $mov[$i]['clabe_cuenta']);
	$tpl->assign("fecha", $mov[$i]['fecha']);
	$tpl->assign("concepto", $mov[$i]['concepto']);
	$tpl->assign("folio", $mov[$i]['folio']);
	$tpl->assign("importe", number_format($mov[$i]['importe'], 2, ".", ","));
}

$tpl->printToScreen();
?>