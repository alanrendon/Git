<?php
// LISTADO DE ESTADOS DE CUENTA
// Tabla 'estado_cuenta'
// Menu 'pendiente'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripción de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------

if (!isset($_GET['impresion'])) {
	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower( "./plantillas/header.tpl" );
	
	// Incluir el cuerpo del documento
	$tpl->assignInclude("body","./plantillas/ban/ban_esc_con.tpl");
	$tpl->prepare();
	
	// Seleccionar script para menu
	$tpl->newBlock("menu");
	if (isset($_SESSION['menu']))
		$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
	$tpl->gotoBlock("_ROOT");
}
else {
	$tpl = new TemplatePower( "./plantillas/ban/estado_cuenta.tpl" );
	$tpl->prepare();
}

// -------------------------------- Tipo de listado -------------------------------------------------------
if (!isset($_GET['listado'])) {
	$tpl->newBlock("datos");
	
	$tpl->assign("fecha1",date("d/m/Y",mktime(0,0,0,date("m"),1,date("Y"))));
	$tpl->assign("fecha2",date("d/m/Y"));
	
	$cod_mov = ejecutar_script("SELECT cod_mov, descripcion FROM catalogo_mov_bancos GROUP BY cod_mov, descripcion ORDER BY cod_mov",$dsn);
	for ($i = 0; $i < count($cod_mov); $i++) {
		$tpl->newBlock("cod_mov");
		$tpl->assign("cod_mov", $cod_mov[$i]['cod_mov']);
		$tpl->assign("descripcion", $cod_mov[$i]['descripcion']);
	}
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	
	$tpl->printToScreen();
	die();
}

// Segun las opciones, generar los scripts sql
$sql  = "SELECT estado_cuenta.id AS id,estado_cuenta.num_cia AS num_cia,estado_cuenta.fecha AS fecha,fecha_con,estado_cuenta.importe AS importe,tipo_mov,estado_cuenta.folio AS folio,(SELECT a_nombre FROM cheques WHERE num_cia = estado_cuenta.num_cia AND folio = estado_cuenta.folio AND cuenta = estado_cuenta.cuenta AND importe >= 0) AS a_nombre,estado_cuenta.concepto AS concepto FROM estado_cuenta ";
$sql .= "WHERE estado_cuenta.fecha BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'";
$sql .= " AND estado_cuenta.cuenta = $_GET[cuenta]";
// Si son solo depositos
if ($_GET['tipo'] == "depositos")
	$sql .= " AND tipo_mov = 'FALSE'";
// Si son solo retiros
else if ($_GET['tipo'] == "retiros")
	$sql .= " AND tipo_mov = 'TRUE'";
// Si es por concepto bancario
else if ($_GET['tipo'] == "concepto")
	$sql .= " AND estado_cuenta.cod_mov = $_GET[cod_concepto]";
// Si la consulta es para una sola compañía
if (isset($_GET['num_cia']) && $_GET['num_cia'] > 0)
	$sql .= " AND estado_cuenta.num_cia = $_GET[num_cia]";
$sql .= " ORDER BY estado_cuenta.num_cia,estado_cuenta.fecha,tipo_mov ASC";

// ****** CASO ESPECIAL SOLO PARA LISTADO DE SALDOS. TODOS LOS MOVIMIENTOS DE CODIGO 5
if (isset($_GET['cod_mov'])) {
	// Segun las opciones, generar los scripts sql
	$sql  = "SELECT estado_cuenta.id AS id,estado_cuenta.num_cia AS num_cia,estado_cuenta.fecha AS fecha,fecha_con,estado_cuenta.importe AS importe,tipo_mov,estado_cuenta.folio AS folio,(SELECT a_nombre FROM cheques WHERE num_cia = estado_cuenta.num_cia AND folio = estado_cuenta.folio AND cuenta = estado_cuenta.cuenta) AS a_nombre,estado_cuenta.concepto AS concepto FROM estado_cuenta ";
	$sql .= "WHERE ";
	$sql .= " tipo_mov = 'TRUE'";
	$sql .= "AND estado_cuenta.cuenta = $_GET[cuenta]";
	$sql .= " AND estado_cuenta.cod_mov = $_GET[cod_mov] AND fecha_con IS NULL";
	$sql .= " AND estado_cuenta.num_cia = $_GET[num_cia]";
	$sql .= " ORDER BY estado_cuenta.num_cia,estado_cuenta.fecha,tipo_mov ASC";
}

$result = ejecutar_script($sql,$dsn);

if (!$result) {
	header("location: ./ban_esc_con.php?codigo_error=1");
	die;
}

// Crear bloque para el listado
$tpl->newBlock("listado");
//echo $sql;
if ($_GET['tipo'] == "concepto") {
	$tpl->newBlock("concepto");
	$sql = "SELECT cod_mov, descripcion FROM catalogo_mov_bancos WHERE cod_mov = $_GET[cod_concepto] LIMIT 1";
	$cod_mov = ejecutar_script($sql,$dsn);
	
	$tpl->assign("cod_mov", $cod_mov[0]['cod_mov']);
	$tpl->assign("descripcion", $cod_mov[0]['descripcion']);
	$tpl->gotoBlock("listado");
}

ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_GET['fecha1'],$fecha1);
ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_GET['fecha2'],$fecha2);

function mes($mes) {
	switch ($mes) {
		case 1: $string = "Enero"; break;
		case 2: $string = "Febrero"; break;
		case 3: $string = "Marzo"; break;
		case 4: $string = "Abril"; break;
		case 5: $string = "Mayo"; break;
		case 6: $string = "Junio"; break;
		case 7: $string = "Julio"; break;
		case 8: $string = "Agosto"; break;
		case 9: $string = "Septiembre"; break;
		case 10: $string = "Octubre"; break;
		case 11: $string = "Noviembre"; break;
		case 12: $string = "Diciembre"; break;
		default: $string = ""; break;
	}
	
	return $string;
}

$tpl->assign("dia1",(int)$fecha1[1]);
$tpl->assign("dia2",(int)$fecha2[1]);
$tpl->assign("mes1",mes($fecha1[2]));
$tpl->assign("mes2",mes($fecha2[2]));
$tpl->assign("anio",$fecha1[3]);

// Iniciar ciclo de recorrido de movimientos
$current_cia = NULL;
$total_depositos = 0;
$total_retiros = 0;
$saldo_final = 0;
$gran_total_depositos = 0;
$gran_total_retiros = 0;

for ($i=0; $i<count($result); $i++) {
	if ($current_cia != $result[$i]['num_cia']) {
		if ($current_cia != NULL) {
			if ($_GET['tipo'] != "concepto") {
				$tpl->newBlock("saldo_actual");
				$tpl->assign("saldo_actual",number_format($saldo_final_libros,2,".",","));
				$tpl->assign("saldo_actual_bancos",number_format($saldo_final_bancos,2,".",","));
				$tpl->assign("diferencia",number_format($saldo_final_libros - $saldo_final_bancos,2,".",","));
			}
			
			$total_depositos = 0;
			$total_retiros = 0;
		}
		
		$current_cia = $result[$i]['num_cia'];
		
		// *********************************** CALCULAR SALDO INICIAL Y FINAL DE LIBROS Y BANCOS ***********************************
		$temp_saldo = ejecutar_script("SELECT * FROM saldos WHERE num_cia = ".$result[$i]['num_cia']." AND cuenta = $_GET[cuenta]",$dsn);
		// Saldo inicial de libros
		$temp_ret = ejecutar_script("SELECT SUM(importe) FROM estado_cuenta WHERE num_cia = ".$result[$i]['num_cia']." AND fecha >= '$_GET[fecha1]' AND tipo_mov = 'TRUE' AND cuenta = $_GET[cuenta]",$dsn);
		$temp_dep = ejecutar_script("SELECT SUM(importe) FROM estado_cuenta WHERE num_cia = ".$result[$i]['num_cia']." AND fecha >= '$_GET[fecha1]' AND tipo_mov = 'FALSE' AND cuenta = $_GET[cuenta]",$dsn);
		$saldo_inicial_libros = $temp_saldo[0]['saldo_libros'] + $temp_ret[0]['sum'] - $temp_dep[0]['sum'];
		
		// Saldo final de libros
		$temp_ret = ejecutar_script("SELECT SUM(importe) FROM estado_cuenta WHERE num_cia = ".$result[$i]['num_cia']." AND fecha >= '$_GET[fecha1]' AND fecha <= '$_GET[fecha2]' AND tipo_mov = 'TRUE' AND cuenta = $_GET[cuenta]",$dsn);
		$temp_dep = ejecutar_script("SELECT SUM(importe) FROM estado_cuenta WHERE num_cia = ".$result[$i]['num_cia']." AND fecha >= '$_GET[fecha1]' AND fecha <= '$_GET[fecha2]' AND tipo_mov = 'FALSE' AND cuenta = $_GET[cuenta]",$dsn);
		$saldo_final_libros = $saldo_inicial_libros + $temp_dep[0]['sum'] - $temp_ret[0]['sum'];
		
		// Saldo inicial de bancos
		$temp_ret = ejecutar_script("SELECT SUM(importe) FROM estado_cuenta WHERE num_cia = ".$result[$i]['num_cia']." AND fecha_con >= '$_GET[fecha1]' AND fecha_con IS NOT NULL AND tipo_mov = 'TRUE' AND cuenta = $_GET[cuenta]",$dsn);
		$temp_dep = ejecutar_script("SELECT SUM(importe) FROM estado_cuenta WHERE num_cia = ".$result[$i]['num_cia']." AND fecha_con >= '$_GET[fecha1]' AND fecha_con IS NOT NULL AND tipo_mov = 'FALSE' AND cuenta = $_GET[cuenta]",$dsn);
		$saldo_inicial_bancos = $temp_saldo[0]['saldo_bancos'] + $temp_ret[0]['sum'] - $temp_dep[0]['sum'];
		
		// Saldo final de bancos
		$temp_ret = ejecutar_script("SELECT SUM(importe) FROM estado_cuenta WHERE num_cia = ".$result[$i]['num_cia']." AND fecha_con >= '$_GET[fecha1]' AND fecha_con <= '$_GET[fecha2]' AND fecha_con IS NOT NULL AND tipo_mov = 'TRUE' AND cuenta = $_GET[cuenta]",$dsn);
		$temp_dep = ejecutar_script("SELECT SUM(importe) FROM estado_cuenta WHERE num_cia = ".$result[$i]['num_cia']." AND fecha_con >= '$_GET[fecha1]' AND fecha_con <= '$_GET[fecha2]' AND fecha_con IS NOT NULL AND tipo_mov = 'FALSE' AND cuenta = $_GET[cuenta]",$dsn);
		$saldo_final_bancos = $saldo_inicial_bancos + $temp_dep[0]['sum'] - $temp_ret[0]['sum'];
		// *****************************************************************************************************************
		
		$tpl->newBlock("cia");
		$cia = ejecutar_script("SELECT nombre,nombre_corto,clabe_cuenta FROM catalogo_companias WHERE num_cia=".$result[$i]['num_cia'],$dsn);
		$tpl->assign("num_cia",$result[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cia[0]['nombre']);
		$tpl->assign("nombre_corto",$cia[0]['nombre_corto']);
		$tpl->assign("cuenta",$cia[0]['clabe_cuenta']);
		if ($_GET['tipo'] != "concepto") {
			$tpl->newBlock("saldo_anterior");
			$tpl->assign("saldo_anterior",number_format($saldo_inicial_libros,2,".",","));
			$tpl->assign("saldo_anterior_bancos",number_format($saldo_inicial_bancos,2,".",","));
		}
	}
	// Crear fila de movimiento
	$tpl->newBlock("fila");
	$tpl->assign("fecha",$result[$i]['fecha']);
	$tpl->assign("deposito",($result[$i]['tipo_mov'] == "f")?number_format($result[$i]['importe'],2,".",","):"&nbsp;");
	$tpl->assign("retiro",($result[$i]['tipo_mov'] == "t")?number_format($result[$i]['importe'],2,".",","):"&nbsp;");
	$tpl->assign("folio",($result[$i]['folio'] > 0)?$result[$i]['folio']:"&nbsp;");
	$tpl->assign("beneficiario",($result[$i]['a_nombre'] != "")?$result[$i]['a_nombre']:"&nbsp;");
	$tpl->assign("concepto",$result[$i]['concepto']);
	$tpl->assign("fecha_con",($result[$i]['fecha_con'] != "")?$result[$i]['fecha_con']:"&nbsp;");
	if ($result[$i]['tipo_mov'] == "f") {
		$total_depositos += $result[$i]['importe'];
		$gran_total_depositos += $result[$i]['importe'];
	}
	else {
		$total_retiros += $result[$i]['importe'];
		$gran_total_retiros += $result[$i]['importe'];
	}
	$tpl->assign("cia.total_depositos",number_format($total_depositos,2,".",","));
	$tpl->assign("cia.total_retiros",number_format($total_retiros,2,".",","));
	/*$tpl->assign("cia.saldo_actual",number_format($saldo_final_libros,2,".",","));
	$tpl->assign("cia.saldo_actual_bancos",number_format($saldo_final_bancos,2,".",","));
	$tpl->assign("cia.diferencia",number_format($saldo_final_libros - $saldo_final_bancos,2,".",","));*/
}
if ($_GET['tipo'] != "concepto") {
	$tpl->newBlock("saldo_actual");
	$tpl->assign("saldo_actual", "<font color=\"#" . ($saldo_final_libros > 0 ? "000000" : "FF0000") . "\">" . number_format($saldo_final_libros,2,".",",") . "</font>");
	$tpl->assign("saldo_actual_bancos","<font color=\"#" . ($saldo_final_bancos > 0 ? "000000" : "FF0000") . "\">" . number_format($saldo_final_bancos,2,".",",") . "</font>");
	$tpl->assign("diferencia","<font color=\"#" . ($saldo_final_bancos - $saldo_final_libros > 0 ? "000000" : "FF0000") . "\">" . number_format($saldo_final_bancos - $saldo_final_libros,2,".",",") . "</font>");
}

if (isset($_GET['impresion']) && $_GET['num_cia'] == "") {
	$tpl->newBlock("gran_total");
	$tpl->assign("depositos", number_format($gran_total_depositos, 2, ".", ","));
	$tpl->assign("retiros", number_format($gran_total_retiros, 2, ".", ","));
}

if (isset($_GET['cerrar']) && !isset($_GET['impresion']))
	$tpl->newBlock("cerrar");
else if (!isset($_GET['impresion']))
	$tpl->newBlock("regresar");

$tpl->printToScreen();
?>