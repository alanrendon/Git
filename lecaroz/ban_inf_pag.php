<?php
// PAGOS DE INFONAVIT
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

// [AJAX] Obtener nombre de compañía
if (isset($_GET['c'])) {
	$sql = "SELECT nombre_corto AS nombre FROM catalogo_companias WHERE num_cia = $_GET[c] AND num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
	$result = ejecutar_script($sql, $dsn);
	
	die(trim($result[0]['nombre']));
}

// [18-Feb-2008] [AJAX] Obtener importe pendiente de pago
if (isset($_GET['id'])) {
	$sql = "SELECT importe FROM infonavit_pendientes WHERE id_emp = $_GET[id] AND anio = $_GET[anio] AND mes = $_GET[mes]";
	$result = ejecutar_script($sql, $dsn);
	
	if (!$result) die;
	else die("$_GET[i]|" . number_format($result[0]['importe'], 2, '.', ','));
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_inf_pag.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['list'])) {
	// Imprimir listado de gastos de caja
	$sql = "SELECT gc.num_cia, cc.nombre AS nombre_cia, num_emp, nombre_completo, mes, gc.importe FROM gastos_caja AS gc LEFT JOIN  catalogo_companias AS cc";
	$sql .= " USING (num_cia) LEFT JOIN catalogo_trabajadores AS ct ON (ct.id = gc.id_emp) LEFT JOIN infonavit AS inf ON (inf.id_emp = ct.id AND inf.folio =";
	$sql .= " gc.folio) WHERE imp_inf = 'TRUE' ORDER BY num_cia";
	$result = ejecutar_script($sql, $dsn);
	
	$tpl->newBlock('listado');
	$tpl->assign('fecha', date('d/m/Y'));
	$num_cia = NULL;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock('cia');
			$tpl->assign('num_cia', $num_cia);
			$tpl->assign('nombre', $reg['nombre_cia']);
			$total = 0;
		}
		$tpl->newBlock('row');
		$tpl->assign('num_emp', $reg['num_emp']);
		$tpl->assign('nombre', $reg['nombre_completo']);
		switch ($reg['mes']) {
			case 1: $mes = 'ENERO'; break;
			case 2: $mes = 'FEBRERO'; break;
			case 3: $mes = 'MARZO'; break;
			case 4: $mes = 'ABRIL'; break;
			case 5: $mes = 'MAYO'; break;
			case 6: $mes = 'JUNIO'; break;
			case 7: $mes = 'JULIO'; break;
			case 8: $mes = 'AGOSTO'; break;
			case 9: $mes = 'SEPTIEMBRE'; break;
			case 10: $mes = 'OCTUBRE'; break;
			case 11: $mes = 'NOVIEMBRE'; break;
			case 12: $mes = 'DICIEMBRE'; break;
		}
		$tpl->assign('mes', $mes);
		$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
		$total += $reg['importe'];
		$tpl->assign('cia.total', number_format($total, 2, '.', ','));
	}
	$tpl->printToScreen();
	ejecutar_script("UPDATE gastos_caja SET imp_inf = 'FALSE' WHERE imp_inf = 'TRUE'",$dsn);
	die;
}

// Pantalla inicial
if (!isset($_GET['num_cia']) && !isset($_GET['tabla'])) {
	$tpl->newBlock("inicio");
	
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

// Pantalla de listado
if (isset($_GET['num_cia'])) {
	// Obtener empleados con crédito INFONAVIT del catálogo de trabajadores
	//$emp = ejecutar_script("SELECT id,num_emp,ap_paterno,ap_materno,nombre FROM catalogo_trabajadores WHERE num_cia=$_GET[num_cia] AND credito_infonavit='TRUE' ORDER BY num_emp ASC",$dsn);
	
	$emp = ejecutar_script("SELECT ct.id AS id_emp, num_emp, ap_paterno, ap_materno, nombre, mes, anio, importe FROM infonavit_pendientes AS inf LEFT JOIN catalogo_trabajadores AS ct ON (ct.id = inf.id_emp) WHERE inf.num_cia = $_GET[num_cia] AND status = 0 ORDER BY nombre", $dsn);
	
	// Si no hubo ningun resultado, regresar a la pantalla inicial
	if (!$emp) {
		header("location:./ban_inf_pag.php?codigo_error=1");
		die;
	}
	
	// Generar pantalla
	$cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia=$_GET[num_cia]",$dsn);
	$tpl->newBlock("empleados");
	$tpl->assign("num_cia",$_GET['num_cia']);
	$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
	$tpl->assign("fecha",date("d/m/Y"));
	$tpl->assign("numfilas",count($emp));
	
	$mes = date('n', mktime(0, 0, 0, date('n'), 1, date('Y')));
	$anio = date('Y', mktime(0, 0, 0, date('n'), 1, date('Y')));
	
	foreach ($emp as $i => $e) {
		$tpl->newBlock('fila');
		//$tpl->assign('index', $i);
		//$tpl->assign('i', count($emp) > 1 ? "[$i]" : '');
		//$tpl->assign('next', count($emp) > 1 ? ($i < count($emp) - 1 ? '[' . ($i + 1) . ']' : '[0]') : '');
		//$tpl->assign('back', count($emp) > 1 ? ($i > 0 ? '[' . ($i - 1) . ']' : '[' . (count($emp) - 1) . ']') : '');
		//$tpl->assign($mes, ' selected');
		//$tpl->assign('anio', $anio);
		
		$tpl->assign("i", $i);
		$tpl->assign("id_emp", $e['id_emp']);
		$tpl->assign("num_emp", $e['num_emp']);
		$tpl->assign("nombre_emp", "$e[nombre] $e[ap_paterno] $e[ap_materno]");
		$tpl->assign("mes", $e['mes']);
		$tpl->assign("mes_escrito", mes_escrito($e['mes'], TRUE));
		$tpl->assign("anio", $e['anio']);
		$tpl->assign("importe", number_format($e['importe'], 2, '.', ','));
		
		//if ($imp = ejecutar_script("SELECT importe FROM infonavit_pendientes WHERE id_emp = $e[id] AND anio = $anio AND mes = $mes", $dsn))
			//$tpl->assign('importe', number_format($imp[0]['importe'], 2, '.', ','));
	}
	
	/*for ($i=0; $i<count($emp); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("i",$i);
		if ($i > 0)
			$tpl->assign("back",$i-1);
		else
			$tpl->assign("back",count($emp)-1);
		if ($i < count($emp)-1)
			$tpl->assign("next",$i+1);
		else
			$tpl->assign("next",0);
		
		$tpl->assign("id_emp",$emp[$i]['id']);
		$tpl->assign("num_emp",$emp[$i]['num_emp']);
		$tpl->assign("nombre_emp",$emp[$i]['nombre']." ".$emp[$i]['ap_paterno']." ".$emp[$i]['ap_materno']);
	}*/
	
	$tpl->printToScreen();
}

if (isset($_GET['tabla'])) {
	// Obtener último folio de recibos
	$result = ejecutar_script("SELECT folio FROM infonavit ORDER BY folio DESC LIMIT 1",$dsn);
	$folio = ($result)?$result[0]['folio']+1:1;
	
	ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_POST['fecha'], $tmp);
	$fecha_caja = $tmp[1] < 5 ? date('d/m/Y', mktime(0, 0, 0, $tmp[2], 0, $tmp[3])) : $_POST['fecha'];
	
	$sql = "";
	$count = 0;
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		if (isset($_POST['id_emp' . $i])) {
			$pago['id_emp'.$count] = $_POST['id_emp' . $i];
			$pago['fecha'.$count] = $_POST['fecha'];
			$pago['importe'.$count] = get_val($_POST['importe'][$i]);
			$pago['folio'.$count] = $folio;
			$pago['tipo_mov'.$count] = "FALSE";
			$pago['pagado'.$count] = "FALSE";
			$pago['mes'.$count] = $_POST['mes'][$i];
			$pago['anio'.$count] = $_POST['anio'][$i];
			$pago['ultimo'.$count] = 'TRUE';
			
			$num_cia = ejecutar_script("SELECT num_cia FROM catalogo_trabajadores WHERE id = " . $pago['id_emp'.$count],$dsn);
			
			$sql .= "INSERT INTO gastos_caja (num_cia, cod_gastos, importe, tipo_mov, clave_balance, fecha, fecha_captura, comentario, imp_inf, id_emp, folio) VALUES (";
			$sql .= "{$num_cia[0]['num_cia']}, 5, " . $pago['importe'.$count] . ", 'TRUE', 'TRUE', '$fecha_caja', CURRENT_DATE, '$folio', 'TRUE', " . $_POST['id_emp'.$i] . ", $folio);\n";
			$sql .= "UPDATE infonavit_pendientes SET status = 1, tsmov = now(), iduser = $_SESSION[iduser] WHERE id_emp = {$_POST['id_emp' . $i]} AND mes = {$_POST['mes'][$i]} AND anio = {$_POST['anio'][$i]};\n";
			
			$folio++;
			$count++;
		}
	}
	$db = new DBclass($dsn,"infonavit",$pago);
	ejecutar_script($sql,$dsn);
	$db->xinsertar();
	
	// Generar recibos
	$tpl->newBlock("recibos");
	for ($i=0; $i<$count; $i++) {
		// Obtener datos del recibo
		$recibo = ejecutar_script("SELECT num_cia,ap_paterno,ap_materno,catalogo_trabajadores.nombre AS nombre,catalogo_companias.nombre AS nombre_cia FROM catalogo_trabajadores JOIN catalogo_companias USING(num_cia) WHERE id=".$pago['id_emp'.$i],$dsn);
		
		$tpl->newBlock("recibo");
		$tpl->assign("cia",$recibo[0]['nombre_cia']);
		$tpl->assign("folio",$pago['folio'.$i]);
		$tpl->assign("nombre",$recibo[0]['nombre']." ".$recibo[0]['ap_paterno']." ".$recibo[0]['ap_materno']);
		$tpl->assign("importe",number_format($pago['importe'.$i],2,".",","));
		switch ($pago['mes'.$i]) {
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
		$tpl->assign(date("n",mktime(0,0,0,date("m")-1,1,date("Y"))),"selected");
		
		// Desglozar importe
		$dec_millar = floor($pago['importe'.$i] / 10000);
		$millares = floor(($pago['importe'.$i] % 10000) / 1000);
		$centenas = floor((($pago['importe'.$i] % 10000) % 1000) / 100);
		$decenas  = floor(((($pago['importe'.$i] % 10000) % 1000) % 100) / 10);
		$unidades = ((($pago['importe'.$i] % 10000) % 1000) % 100) % 10;
		
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
		$centavos = floor(($pago['importe'.$i] - floor($pago['importe'.$i])) * 100);
		//$centavos = $aux % 1;
		
		$tpl->assign("importe_escrito",$cadena);
		$tpl->assign("centavos",$centavos);
		if ($i < $count-1)
			$tpl->assign("br","<br>");
	}
	$tpl->printToScreen();
}

?>