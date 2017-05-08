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

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_imp_fed.tpl");
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

// Comprobar características del fichero
if (!(stristr($_FILES['file']['type'], "text/plain") && $_FILES['file']['size'] < 5242880)) {
	header("location: ./ban_imp_fed.php?codigo_error=1");
	die;
}

// Cargar depositos a la  base de datos
$fd = fopen($_FILES['file']['tmp_name'], "r");
$count = 0;
while (!feof($fd)) {
	// Obtener cadena del archivo y almacenarlo en el buffer
	$buffer = fgets($fd);

	// Dividir cadena en secciones y almacenarlas en variables
	if ($buffer != "") {
		$tipo_reg = substr($buffer, 0, 2);
		switch ($tipo_reg) {
			// Registro de cabecera de cuenta
			case 11:
				$cuenta = substr($buffer, 25, 10);
				$result = $db->query("SELECT num_cia,nombre FROM catalogo_companias WHERE clabe_cuenta = '0$cuenta'");
				if ($result) {
					$cia    = $result[0]['num_cia'];
					$nombre = $result[0]['nombre'];
					
					// Obtener ultimo folio
					$temp = $db->query("SELECT folio FROM folios_cheque WHERE num_cia = $cia AND cuenta = 1 ORDER BY folio DESC LIMIT 1");
					$folio_cheque = $temp ? $temp[0]['folio'] + 1 : 1;
				}
				else {
					$cia    = "0";
					$nombre = substr($buffer,66,26);
				}
			break;
			// Registro principal de movimientos
			case 22:
				$temp = substr($buffer, 52, 12) . substr($buffer, 64, 16);
				
				if ($cia > 0 && trim($temp) == "PAGO DE IMPUESTOS FEDERALES") {
					$mov[$count]['num_cia']       = $cia;
					$mov[$count]['nombre_cia']    = $nombre;
					$mov[$count]['cuenta']        = $cuenta;
					$mov[$count]['fecha']         = substr($buffer, 14, 2) . "/" . substr($buffer, 12, 2) . "/20" . substr($buffer, 10, 2);
					$mov[$count]['tipo_mov']      = "TRUE";
					$mov[$count]['importe']       = number_format(substr($buffer, 28, 12) . "." . substr($buffer, 40, 2), 2, ".", "");
					$mov[$count]['concepto']      = trim($temp);
					$mov[$count]['cod_mov']       = 33;
					$mov[$count]['folio']         = $folio_cheque;
					$mov[$count]['cuenta']        = 1;
					
					$cheque[$count]['num_cia']       = $cia;
					$cheque[$count]['num_proveedor'] = 237;
					$cheque[$count]['a_nombre']      = "TESORERIA DE LA FEDERACION";
					$cheque[$count]['fecha']         = $mov[$count]['fecha'];
					$cheque[$count]['importe']       = $mov[$count]['importe'];
					$cheque[$count]['iduser']        = $_SESSION['iduser'];
					$cheque[$count]['imp']           = "FALSE";
					$cheque[$count]['concepto']      = $mov[$count]['concepto'];
					$cheque[$count]['cod_mov']       = 33;
					$cheque[$count]['codgastos']     = 140;
					$cheque[$count]['folio']         = $folio_cheque;
					$cheque[$count]['cuenta']        = 1;
					
					$gasto[$count]['num_cia']       = $cia;
					$gasto[$count]['fecha']         = $mov[$count]['fecha'];
					$gasto[$count]['importe']       = $mov[$count]['importe'];
					$gasto[$count]['concepto']      = $mov[$count]['concepto'];
					$gasto[$count]['codgastos']     = 140;
					$gasto[$count]['folio']         = $folio_cheque;
					$gasto[$count]['captura']       = "TRUE";
					
					$folio[$count]['num_cia']   = $cia;
					$folio[$count]['folio']     = $folio_cheque++;
					$folio[$count]['reservado'] = "FALSE";
					$folio[$count]['utilizado'] = "TRUE";
					$folio[$count]['fecha']     = $mov[$count]['fecha'];
					$folio[$count]['cuenta']    = 1;
					
					$count++;
				}
			break;
		}
	}
}
fclose($fd);

if ($count == 0) {
	header("location: ./ban_imp_fed.php?codigo_error=1");
	die;
}

$sql  = $db->multiple_insert("estado_cuenta", $mov);
$sql .= $db->multiple_insert("cheques", $cheque);
$sql .= $db->multiple_insert("movimiento_gastos", $gasto);
$sql .= $db->multiple_insert("folios_cheque", $folio);
$db->query($sql);
$db->desconectar();

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
usort($mov, "cmp");


for ($i = 0; $i < $count; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("num_cia", $mov[$i]['num_cia']);
	$tpl->assign("nombre_cia", $mov[$i]['nombre_cia']);
	$tpl->assign("cuenta", $mov[$i]['cuenta']);
	$tpl->assign("fecha", $mov[$i]['fecha']);
	$tpl->assign("folio", $mov[$i]['folio']);
	$tpl->assign("importe", number_format($mov[$i]['importe'], 2, ".", ","));
}

$tpl->printToScreen();
?>