<?php
// IMPRESIÓN DE RECIBOS DE INFONAVIT
// Tablas 'infonavit'
// Menu 'No definido'

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

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_inf_rec.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Pantalla inicial
if (!isset($_GET['tipo'])) {
	$tpl->newBlock("datos");
	
	$tpl->assign("fecha",date("d/m/Y"));
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	
	$tpl->printToScreen();
}

if (isset($_GET['tipo'])) {
	if ($_GET['tipo'] == "fecha")
		$sql = "SELECT * FROM infonavit inf LEFT JOIN catalogo_trabajadores ct ON (ct.id = id_emp) WHERE fecha = '$_GET[fecha_mov]' AND inf.ultimo = TRUE AND num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . " ORDER BY folio ASC";
	else
		$sql = "SELECT * FROM infonavit inf LEFT JOIN catalogo_trabajadores ct ON (ct.id = id_emp) WHERE folio >= $_GET[folio1] AND folio <= $_GET[folio2] AND num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . " ORDER BY folio ASC";
		//$sql = "SELECT inf.* FROM infonavit inf LEFT JOIN catalogo_trabajadores ct ON (id_emp = ct.id) LEFT JOIN catalogo_companias USING (num_cia) WHERE idadministrador = 9 AND inf.fecha BETWEEN '01/10/2011' AND '23/11/2011'" . " ORDER BY folio ASC";
		//$sql = "SELECT * FROM infonavit WHERE folio IN (5344, 5343, 5417, 5416, 5585, 5584, 5345, 5418, 5586) ORDER BY folio ASC";
		
	$pago = ejecutar_script($sql,$dsn);
	if (!$pago) {
		header("location: ./fac_inf_rec.php?codigo_error=1");
		die;
	}
	
	if ($_GET['tipo'] == 'fecha') {
		$sql = '
			UPDATE
				infonavit
			SET
				ultimo = \'FALSE\'
			WHERE
					fecha = \'' . $_GET['fecha_mov'] . '\'
				AND
					ultimo = \'TRUE\'
		';
		ejecutar_script($sql, $dsn);
	}
	
	// Generar recibos
	$tpl->newBlock("recibos");
	for ($i=0; $i<count($pago); $i++) {
		// Obtener datos del recibo
		$recibo = ejecutar_script("SELECT num_cia,ap_paterno,ap_materno,catalogo_trabajadores.nombre AS nombre,catalogo_companias.nombre AS nombre_cia FROM catalogo_trabajadores JOIN catalogo_companias USING(num_cia) WHERE id=".$pago[$i]['id_emp'],$dsn);
		
		$tpl->newBlock("recibo");
		$tpl->assign("cia",$recibo[0]['nombre_cia']);
		$tpl->assign("folio",$pago[$i]['folio']);
		
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",/*date("d/m/Y")*/$pago[$i]['fecha'],$fecha);
		
		$tpl->assign("dia_actual",$fecha[1]);
		switch ($fecha[2]) {
			case 1:  $mes_actual = "ENERO"; break;
			case 2:  $mes_actual = "FEBRERO"; break;
			case 3:  $mes_actual = "MARZO"; break;
			case 4:  $mes_actual = "ABRIL"; break;
			case 5:  $mes_actual = "MAYO"; break;
			case 6:  $mes_actual = "JUNIO"; break;
			case 7:  $mes_actual = "JULIO"; break;
			case 8:  $mes_actual = "AGOSTO"; break;
			case 9:  $mes_actual = "SEPTIEMBRE"; break;
			case 10: $mes_actual = "OCTUBRE"; break;
			case 11: $mes_actual = "NOVIEMBRE"; break;
			case 12: $mes_actual = "DICIEMBRE"; break;
		}
		$tpl->assign("mes_actual",$mes_actual);
		$tpl->assign("anio_actual",$fecha[3]);
		
		
		$tpl->assign("nombre",$recibo[0]['nombre']." ".$recibo[0]['ap_paterno']." ".$recibo[0]['ap_materno']);
		$tpl->assign("importe",number_format($pago[$i]['importe'],2,".",","));
		switch ($pago[$i]['mes']) {
			case 1:  $mes = "ENERO"; break;
			case 2:  $mes = "FEBRERO"; break;
			case 3:  $mes = "MARZO"; break;
			case 4:  $mes = "ABRIL"; break;
			case 5:  $mes = "MAYO"; break;
			case 6:  $mes = "JUNIO"; break;
			case 7:  $mes = "JULIO"; break;
			case 8:  $mes = "AGOSTO"; break;
			case 9:  $mes = "SEPTIEMBRE"; break;
			case 10: $mes = "OCTUBRE"; break;
			case 11: $mes = "NOVIEMBRE"; break;
			case 12: $mes = "DICIEMBRE"; break;
		}
		$tpl->assign("mes",$mes);
		
		// Desglozar importe
		$dec_millar = floor($pago[$i]['importe'] / 10000);
		$millares = floor(($pago[$i]['importe'] % 10000) / 1000);
		$centenas = floor((($pago[$i]['importe'] % 10000) % 1000) / 100);
		$decenas  = floor(((($pago[$i]['importe'] % 10000) % 1000) % 100) / 10);
		$unidades = ((($pago[$i]['importe'] % 10000) % 1000) % 100) % 10;
		
		$cadena = "";
		
		// Decenas de millar
		if ($dec_millar > 0) {
			switch ($dec_millar) {
				case 1: $cadena .= ($millares > 0)?(($millares > 5)?"DIECI":""):"DIEZ MIL "; break;
				case 2: $cadena .= ($millares > 0)?"VEINTI":"VEINTE MIL "; break;
				case 3: $cadena .= ($millares > 0)?"TREINTA Y ":"TREINTA MIL "; break;
				case 4: $cadena .= ($millares > 0)?"CUARENTA Y ":"CUARENTA MIL "; break;
				case 5: $cadena .= ($millares > 0)?"CINCUENTA Y ":"CINCUENTA MIL "; break;
				case 6: $cadena .= ($millares > 0)?"SESENTA Y ":"SESENTA MIL "; break;
				case 7: $cadena .= ($millares > 0)?"SETENTA Y ":"SETENTA MIL "; break;
				case 8: $cadena .= ($millares > 0)?"OCHENTA Y ":"OCHENTA MIL "; break;
				case 9: $cadena .= ($millares > 0)?"NOVENTA Y ":"NOVENTA MIL "; break;
			}
		}
		// Millares
		if ($millares > 0) {
			switch ($millares) {
				case 1: $cadena .= ($dec_millar > 0)?(($dec_millar == 1)?"ONCE MIL ":"UN MIL"):"MIL "; break;
				case 2: $cadena .= ($dec_millar == 1)?"DOCE MIL ":"DOS MIL "; break;
				case 3: $cadena .= ($dec_millar == 1)?"TRECE MIL ":"TRES MIL "; break;
				case 4: $cadena .= ($dec_millar == 1)?"CATORCE MIL ":"CUATRO MIL "; break;
				case 5: $cadena .= ($dec_millar == 1)?"QUINCE MIL ":"CINCO MIL "; break;
				case 6: $cadena .= "SEIS MIL "; break;
				case 7: $cadena .= "SIETE MIL "; break;
				case 8: $cadena .= "OCHO MIL "; break;
				case 9: $cadena .= "NUEVE MIL "; break;
			}
		}
		// Centenas
		if ($centenas > 0) {
			switch ($centenas) {
				case 1: $cadena .= ($decenas > 0)?"CIENTO ":"CIEN "; break;
				case 2: $cadena .= "DOSCIENTOS "; break;
				case 3: $cadena .= "TRESCIENTOS "; break;
				case 4: $cadena .= "CUATROCIENTOS "; break;
				case 5: $cadena .= "QUINIENTOS "; break;
				case 6: $cadena .= "SEISCIENTOS "; break;
				case 7: $cadena .= "SETECIENTOS "; break;
				case 8: $cadena .= "OCHOCIENTOS "; break;
				case 9: $cadena .= "NOVECIENTOS "; break;
			}
		}
		// Decenas
		if ($decenas > 0) {
			switch ($decenas) {
				case 1: $cadena .= ($unidades > 0)?(($unidades > 5)?"DIECI":""):"DIEZ"; break;
				case 2: $cadena .= ($unidades > 0)?"VEINTI":"VEINTE"; break;
				case 3: $cadena .= ($unidades > 0)?"TREINTA Y ":"TREINTA"; break;
				case 4: $cadena .= ($unidades > 0)?"CUARENTA Y ":"CUARENTA"; break;
				case 5: $cadena .= ($unidades > 0)?"CINCUENTA Y ":"CINCUENTA"; break;
				case 6: $cadena .= ($unidades > 0)?"SESENTA Y ":"SESENTA"; break;
				case 7: $cadena .= ($unidades > 0)?"SETENTA Y ":"SETENTA"; break;
				case 8: $cadena .= ($unidades > 0)?"OCHENTA Y ":"OCHENTA"; break;
				case 9: $cadena .= ($unidades > 0)?"NOVENTA Y ":"NOVENTA"; break;
			}
		}
		// Unidades
		if ($unidades > 0) {
			switch ($unidades) {
				case 1: $cadena .= ($decenas == 1)?"ONCE":"UNO"; break;
				case 2: $cadena .= ($decenas == 1)?"DOCE":"DOS"; break;
				case 3: $cadena .= ($decenas == 1)?"TRECE":"TRES"; break;
				case 4: $cadena .= ($decenas == 1)?"CATORCE":"CUATRO"; break;
				case 5: $cadena .= ($decenas == 1)?"QUINCE":"CINCO"; break;
				case 6: $cadena .= "SEIS"; break;
				case 7: $cadena .= "SIETE"; break;
				case 8: $cadena .= "OCHO"; break;
				case 9: $cadena .= "NUEVE"; break;
			}
		}
		// Obtener centavos
		$centavos = floor(($pago[$i]['importe'] - floor($pago[$i]['importe'])) * 100);
		//$centavos = $aux % 1;
		
		$tpl->assign("importe_escrito",$cadena);
		$tpl->assign("centavos",$centavos);
		if ($i < count($pago)-1 && ($i + 1) % 2 == 0)
			$tpl->assign("br","<br>");
	}
	$tpl->printToScreen();
}

?>