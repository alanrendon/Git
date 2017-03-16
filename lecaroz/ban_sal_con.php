<?php
// LISTADO DE GASTOS DE CAJA
// Tabla 'catalogo_gastos_caja'
// Menu 'Balance->Catálogos Especiales'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- FUNCIONES ---------------------------------------------------------------
function mes($mes) {
	switch($mes) {
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
		default: $string = "";
	}
	
	return $string;
}

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

$descripcion_error[1] = "La Compañía no existe en la Base de Datos";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_sal_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Tipo de listado -------------------------------------------------------
if (date("d") > 6) {
	$fecha1 = date("1/m/Y");
	$fecha2 = date("d/m/Y");
}
else {
	$fecha1 = date("d/m/Y",mktime(0,0,0,date("m")-1,1,date("Y")));
	$fecha2 = date("d/m/Y",mktime(0,0,0,date("m"),0,date("Y")));
}
ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$fecha2,$temp);
$dia = $temp[1];
$mes = $temp[2];
$anio = $temp[3];

$anio = date("Y");

$tpl->assign("dia",date("d"));
$tpl->assign("mes",mes(date("n")));
$tpl->assign("anio",date("Y"));

/******************** PANADERIAS *******************/
$cia = ejecutar_script("SELECT num_cia,nombre_corto,clabe_cuenta,saldo_libros,saldo_bancos FROM catalogo_companias LEFT JOIN saldos USING(num_cia) WHERE num_cia < 100 AND cuenta = 1 ORDER BY num_cia ASC",$dsn);

$anio_actual = date("Y");

$gran_total_saldo_bancos = 0;
$gran_total_saldo_libros = 0;
$gran_total_pendientes = 0;
$gran_total_saldo_pro = 0;
$gran_total_devoluciones = 0;
$gran_total_efectivo = 0;

$total_saldo_bancos = 0;
$total_saldo_libros = 0;
$total_pendientes = 0;
$total_saldo_pro = 0;
$total_devoluciones = 0;
$total_efectivo = 0;

if ($cia) {
	$tpl->newBlock("listado");
	$tpl->assign("titulo","Panader&iacute;as");
	
	for ($i=0; $i<count($cia); $i++) {
		// Obtener todos los datos
		$pendientes = ejecutar_script("SELECT SUM(importe) FROM estado_cuenta WHERE num_cia=".$cia[$i]['num_cia']." AND fecha_con IS NULL AND tipo_mov='TRUE' AND cod_mov=5 AND cuenta=1",$dsn);
		$saldo_pro = ejecutar_script("SELECT SUM(total) FROM pasivo_proveedores WHERE num_cia=".$cia[$i]['num_cia'],$dsn);	// Saldo proveedores
		$ultima_fac = ejecutar_script("SELECT id,fecha_pago FROM pasivo_proveedores WHERE num_cia=".$cia[$i]['num_cia']." ORDER BY fecha_pago ASC LIMIT 1",$dsn);
		$perdidas = ejecutar_script("SELECT monto FROM perdidas WHERE num_cia=".$cia[$i]['num_cia'],$dsn);
		$devoluciones = ejecutar_script("SELECT SUM(importe) FROM estado_cuenta WHERE num_cia=".$cia[$i]['num_cia']." AND cod_mov=18 AND fecha>='1/1/$anio' AND fecha<='31/12/$anio' AND cuenta=1",$dsn);
		$efectivo = ejecutar_script("SELECT SUM(importe) FROM otros_depositos WHERE num_cia=".$cia[$i]['num_cia']." AND fecha>='$fecha1' AND fecha<='$fecha2'",$dsn);
		if ($cia[$i]['num_cia'] > 100 && $cia[$i]['num_cia'] < 200 /*|| $cia[$i]['num_cia'] == 702 || $cia[$i]['num_cia'] == 703 || $cia[$i]['num_cia'] == 704*/)
			$sql = "SELECT AVG(efectivo) AS promedio FROM total_companias WHERE num_cia=".$cia[$i]['num_cia']." AND fecha>='$fecha1' AND fecha<='$fecha2'";
		else
			$sql = "SELECT AVG(efectivo) AS promedio FROM total_panaderias WHERE num_cia = ".$cia[$i]['num_cia']." AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND efe = 'TRUE' AND exp = 'TRUE' AND gas = 'TRUE' AND pro = 'TRUE' AND pas = 'TRUE'";
		$promedio = ejecutar_script($sql,$dsn);
		//$dias = ($saldo_pro && $promedio && $promedio[0]['promedio'] > 0 && $saldo_pro[0]['sum'] > 0)?ceil($saldo_pro[0]['sum']/$promedio[0]['promedio']):"&nbsp;";
		$dias = ($promedio[0]['promedio'] > 0 && floor(($saldo_pro[0]['sum']-$cia[$i]['saldo_libros'])/$promedio[0]['promedio']) > 0)?floor(($saldo_pro[0]['sum']-$cia[$i]['saldo_libros'])/$promedio[0]['promedio']):"&nbsp;";
		
		$tpl->newBlock("fila");
		$tpl->assign("dia",$dia);
		$tpl->assign("mes",$mes);
		$tpl->assign("anio",$anio);
		$tpl->assign("num_cia",$cia[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
		$tpl->assign("cuenta",$cia[$i]['clabe_cuenta']);
		$tpl->assign("color_saldo_libros", $cia[$i]['saldo_libros'] > 0 ? "0000FF" : "FF0000");
		$tpl->assign("saldo_libros",number_format($cia[$i]['saldo_libros'],2,".",","));
		$tpl->assign("color_saldo_bancos", $cia[$i]['saldo_bancos'] > 0 ? "000000" : "FF0000");
		$tpl->assign("saldo_bancos",number_format($cia[$i]['saldo_bancos'],2,".",","));
		$tpl->assign("pendientes",($pendientes[0]['sum'] > 0)?number_format($pendientes[0]['sum'],2,".",","):"&nbsp;");
		$tpl->assign("saldo_pro",($saldo_pro[0]['sum'] > 0)?number_format($saldo_pro[0]['sum'],2,".",","):"&nbsp;");
		if ($ultima_fac)
			ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$ultima_fac[0]['fecha_pago'],$fecha_pago);
		
		$dif1 = mktime(0,0,0,$fecha_pago[2],$fecha_pago[1],$fecha_pago[3]);
		$dif2 = mktime(0,0,0,$mes,$dia,$anio);
		
		$dif = ($dif2 - $dif1) / 86400;
		
		$tpl->assign("id_ultima_fac",($ultima_fac)?$ultima_fac[0]['id']:"");
		$tpl->assign("ultima_fac",($ultima_fac)?"<font color='#".($dif > 90?"FF0000":"000000")."'>".$ultima_fac[0]['fecha_pago']."</font>":"&nbsp;");
		$tpl->assign("perdidas",$perdidas && $perdidas[0]['monto'] != 0?number_format($perdidas[0]['monto'],2,".",","):"&nbsp;");
		$tpl->assign("devoluciones",($devoluciones[0]['sum'] > 0)?number_format($devoluciones[0]['sum'],2,".",","):"&nbsp;");
		$tpl->assign("pro_efectivo",($promedio[0]['promedio'] > 0)?number_format($promedio[0]['promedio'],2,".",","):"&nbsp;");
		$tpl->assign("efectivo",($efectivo[0]['sum'] > 0)?number_format($efectivo[0]['sum'],2,".",","):"&nbsp");
		$tpl->assign("dias",($saldo_pro && $promedio)?$dias:"&nbsp;");
		
		$total_saldo_bancos += $cia[$i]['saldo_bancos'];
		$total_saldo_libros += $cia[$i]['saldo_libros'];
		$total_pendientes += $pendientes[0]['sum'];
		$total_saldo_pro += $saldo_pro[0]['sum'];
		$total_devoluciones += $devoluciones[0]['sum'];
		$total_efectivo += $efectivo[0]['sum'];
		
		$gran_total_saldo_bancos += $cia[$i]['saldo_bancos'];
		$gran_total_saldo_libros += $cia[$i]['saldo_libros'];
		$gran_total_pendientes += $pendientes[0]['sum'];
		$gran_total_saldo_pro += $saldo_pro[0]['sum'];
		$gran_total_devoluciones += $devoluciones[0]['sum'];
		$gran_total_efectivo += $efectivo[0]['sum'];
	}
	// Totales
	$tpl->assign("listado.total_saldo_bancos",number_format($total_saldo_bancos,2,".",","));
	$tpl->assign("listado.total_saldo_libros",number_format($total_saldo_libros,2,".",","));
	$tpl->assign("listado.total_pendientes",number_format($total_pendientes,2,".",","));
	$tpl->assign("listado.total_saldo_pro",number_format($total_saldo_pro,2,".",","));
	$tpl->assign("listado.total_devoluciones",number_format($total_devoluciones,2,".",","));
	$tpl->assign("listado.total_efectivo",number_format($total_efectivo,2,".",","));
}

/******************** ROSTICERIAS *******************/
$cia = ejecutar_script("SELECT num_cia,nombre_corto,clabe_cuenta,saldo_libros,saldo_bancos FROM catalogo_companias LEFT JOIN saldos USING(num_cia) WHERE (num_cia > 100 AND num_cia < 200 OR num_cia > 700 AND num_cia < 800) AND cuenta = 1 ORDER BY num_cia ASC",$dsn);

$total_saldo_bancos = 0;
$total_saldo_libros = 0;
$total_pendientes = 0;
$total_saldo_pro = 0;
$total_devoluciones = 0;
$total_efectivo = 0;

if ($cia) {
	$tpl->newBlock("listado");
	$tpl->assign("titulo","Rosticer&iacute;as");
	
	for ($i=0; $i<count($cia); $i++) {
		// Obtener todos los datos
		$pendientes = ejecutar_script("SELECT SUM(importe) FROM estado_cuenta WHERE num_cia=".$cia[$i]['num_cia']." AND fecha_con IS NULL AND tipo_mov='TRUE' AND cod_mov=5 AND cuenta = 1",$dsn);
		$saldo_pro = ejecutar_script("SELECT SUM(total) FROM pasivo_proveedores WHERE num_cia=".$cia[$i]['num_cia'],$dsn);	// Saldo proveedores
		$ultima_fac = ejecutar_script("SELECT id,fecha_pago FROM pasivo_proveedores WHERE num_cia=".$cia[$i]['num_cia']." ORDER BY fecha_pago ASC LIMIT 1",$dsn);
		$perdidas = ejecutar_script("SELECT monto FROM perdidas WHERE num_cia=".$cia[$i]['num_cia'],$dsn);
		$devoluciones = ejecutar_script("SELECT SUM(importe) FROM estado_cuenta WHERE num_cia=".$cia[$i]['num_cia']." AND cod_mov=18 AND fecha>='$fecha1' AND fecha<='$fecha2' AND cuenta = 1",$dsn);
		$efectivo = ejecutar_script("SELECT SUM(importe) FROM otros_depositos WHERE num_cia=".$cia[$i]['num_cia']." AND fecha>='$fecha1' AND fecha<='$fecha2'",$dsn);
		if ($cia[$i]['num_cia'] > 100 && $cia[$i]['num_cia'] < 200 || $cia[$i]['num_cia'] == 702 || $cia[$i]['num_cia'] == 703 || $cia[$i]['num_cia'] == 704)
			$sql = "SELECT AVG(efectivo) AS promedio FROM total_companias WHERE num_cia=".$cia[$i]['num_cia']." AND fecha>='$fecha1' AND fecha<='$fecha2'";
		else
			$sql = "SELECT AVG(efectivo) AS promedio FROM total_panaderias WHERE num_cia = ".$cia[$i]['num_cia']." AND fecha BETWEEN '$fecha1' AND '$fecha2' AND efe = 'TRUE' AND exp = 'TRUE' AND gas = 'TRUE' AND pro = 'TRUE' AND pas = 'TRUE'";
		$promedio = ejecutar_script($sql,$dsn);
		$dias = ($saldo_pro && $promedio && $promedio[0]['promedio'] > 0 && $saldo_pro[0]['sum'] > 0)?ceil($saldo_pro[0]['sum']/$promedio[0]['promedio']):"&nbsp;";
		
		$tpl->newBlock("fila");
		$tpl->assign("dia",$dia);
		$tpl->assign("mes",$mes);
		$tpl->assign("anio",$anio);
		$tpl->assign("num_cia",$cia[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
		$tpl->assign("cuenta",$cia[$i]['clabe_cuenta']);
		$tpl->assign("color_saldo_libros", $cia[$i]['saldo_libros'] > 0 ? "0000FF" : "FF0000");
		$tpl->assign("saldo_libros",number_format($cia[$i]['saldo_libros'],2,".",","));
		$tpl->assign("color_saldo_bancos", $cia[$i]['saldo_bancos'] > 0 ? "000000" : "FF0000");
		$tpl->assign("saldo_bancos",number_format($cia[$i]['saldo_bancos'],2,".",","));
		$tpl->assign("pendientes",($pendientes[0]['sum'] > 0)?number_format($pendientes[0]['sum'],2,".",","):"&nbsp;");
		$tpl->assign("saldo_pro",($saldo_pro[0]['sum'] > 0)?number_format($saldo_pro[0]['sum'],2,".",","):"&nbsp;");
		if ($ultima_fac)
			ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$ultima_fac[0]['fecha_pago'],$fecha_pago);
		
		$dif1 = mktime(0,0,0,$fecha_pago[2],$fecha_pago[1],$fecha_pago[3]);
		$dif2 = mktime(0,0,0,$mes,$dia,$anio);
		
		$dif = ($dif2 - $dif1) / 86400;
		
		$tpl->assign("id_ultima_fac",($ultima_fac)?$ultima_fac[0]['id']:"");
		$tpl->assign("ultima_fac",($ultima_fac)?"<font color='#".($dif > 90?"FF0000":"000000")."'>".$ultima_fac[0]['fecha_pago']."</font>":"&nbsp;");
		$tpl->assign("perdidas",$perdidas && $perdidas[0]['monto'] != 0?number_format($perdidas[0]['monto'],2,".",","):"&nbsp;");
		$tpl->assign("devoluciones",($devoluciones[0]['sum'] > 0)?number_format($devoluciones[0]['sum'],2,".",","):"&nbsp;");
		$tpl->assign("pro_efectivo",($promedio[0]['promedio'] != 0)?number_format($promedio[0]['promedio'],2,".",","):"&nbsp;");
		$tpl->assign("efectivo",($efectivo[0]['sum'] != 0)?number_format($efectivo[0]['sum'],2,".",","):"&nbsp");
		$tpl->assign("dias",($saldo_pro && $promedio)?$dias:"&nbsp;");
		
		$total_saldo_bancos += $cia[$i]['saldo_bancos'];
		$total_saldo_libros += $cia[$i]['saldo_libros'];
		$total_pendientes += $pendientes[0]['sum'];
		$total_saldo_pro += $saldo_pro[0]['sum'];
		$total_devoluciones += $devoluciones[0]['sum'];
		$total_efectivo += $efectivo[0]['sum'];
		
		$gran_total_saldo_bancos += $cia[$i]['saldo_bancos'];
		$gran_total_saldo_libros += $cia[$i]['saldo_libros'];
		$gran_total_pendientes += $pendientes[0]['sum'];
		$gran_total_saldo_pro += $saldo_pro[0]['sum'];
		$gran_total_devoluciones += $devoluciones[0]['sum'];
		$gran_total_efectivo += $efectivo[0]['sum'];
	}
	// Totales
	$tpl->assign("listado.total_saldo_bancos",number_format($total_saldo_bancos,2,".",","));
	$tpl->assign("listado.total_saldo_libros",number_format($total_saldo_libros,2,".",","));
	$tpl->assign("listado.total_pendientes",number_format($total_pendientes,2,".",","));
	$tpl->assign("listado.total_saldo_pro",number_format($total_saldo_pro,2,".",","));
	$tpl->assign("listado.total_devoluciones",number_format($total_devoluciones,2,".",","));
	$tpl->assign("listado.total_efectivo",number_format($total_efectivo,2,".",","));
}

/******************** INMOVILIARIAS *******************/
$cia = ejecutar_script("SELECT num_cia,nombre_corto,clabe_cuenta,saldo_libros,saldo_bancos FROM catalogo_companias LEFT JOIN saldos USING(num_cia) WHERE (num_cia >= 600 AND num_cia <= 700 OR num_cia >= 800 AND num_cia < 900) AND cuenta = 1 ORDER BY num_cia ASC",$dsn);

$total_saldo_bancos = 0;
$total_saldo_libros = 0;
$total_pendientes = 0;
$total_saldo_pro = 0;
$total_devoluciones = 0;
$total_efectivo = 0;

if ($cia) {
	$tpl->newBlock("listado");
	$tpl->assign("titulo","Inmobiliarias");
	
	for ($i=0; $i<count($cia); $i++) {
		// Obtener todos los datos
		$pendientes = ejecutar_script("SELECT SUM(importe) FROM estado_cuenta WHERE num_cia=".$cia[$i]['num_cia']." AND fecha_con IS NULL AND tipo_mov='TRUE' AND cod_mov=5 AND cuenta = 1",$dsn);
		$saldo_pro = ejecutar_script("SELECT SUM(total) FROM pasivo_proveedores WHERE num_cia=".$cia[$i]['num_cia'],$dsn);	// Saldo proveedores
		$ultima_fac = ejecutar_script("SELECT id,fecha_pago FROM pasivo_proveedores WHERE num_cia=".$cia[$i]['num_cia']." ORDER BY fecha_pago ASC LIMIT 1",$dsn);
		$perdidas = ejecutar_script("SELECT monto FROM perdidas WHERE num_cia=".$cia[$i]['num_cia'],$dsn);
		$devoluciones = ejecutar_script("SELECT SUM(importe) FROM estado_cuenta WHERE num_cia=".$cia[$i]['num_cia']." AND cod_mov=18 AND fecha>='$fecha1' AND fecha<='$fecha2' AND cuenta = 1",$dsn);
		$efectivo = ejecutar_script("SELECT SUM(importe) FROM otros_depositos WHERE num_cia=".$cia[$i]['num_cia']." AND fecha>='$fecha1' AND fecha<='$fecha2'",$dsn);
		if ($cia[$i]['num_cia'] > 100 && $cia[$i]['num_cia'] < 200 || $cia[$i]['num_cia'] == 702 || $cia[$i]['num_cia'] == 703 || $cia[$i]['num_cia'] == 704)
			$sql = "SELECT AVG(efectivo) AS promedio FROM total_companias WHERE num_cia=".$cia[$i]['num_cia']." AND fecha>='$fecha1' AND fecha<='$fecha2'";
		else
			$sql = "SELECT AVG(efectivo) AS promedio FROM total_panaderias WHERE num_cia = ".$cia[$i]['num_cia']." AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND efe = 'TRUE' AND exp = 'TRUE' AND gas = 'TRUE' AND pro = 'TRUE' AND pas = 'TRUE'";
		$promedio = ejecutar_script($sql,$dsn);
		$dias = ($saldo_pro && $promedio && $promedio[0]['promedio'] > 0 && $saldo_pro[0]['sum'] > 0)?ceil($saldo_pro[0]['sum']/$promedio[0]['promedio']):"&nbsp;";
		
		$tpl->newBlock("fila");
		$tpl->assign("dia",$dia);
		$tpl->assign("mes",$mes);
		$tpl->assign("anio",$anio);
		$tpl->assign("num_cia",$cia[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
		$tpl->assign("cuenta",$cia[$i]['clabe_cuenta']);
		$tpl->assign("color_saldo_libros", $cia[$i]['saldo_libros'] > 0 ? "0000FF" : "FF0000");
		$tpl->assign("saldo_libros",number_format($cia[$i]['saldo_libros'],2,".",","));
		$tpl->assign("color_saldo_bancos", $cia[$i]['saldo_bancos'] > 0 ? "000000" : "FF0000");
		$tpl->assign("saldo_bancos",number_format($cia[$i]['saldo_bancos'],2,".",","));
		$tpl->assign("pendientes",($pendientes[0]['sum'] > 0)?number_format($pendientes[0]['sum'],2,".",","):"&nbsp;");
		$tpl->assign("saldo_pro",($saldo_pro[0]['sum'] > 0)?number_format($saldo_pro[0]['sum'],2,".",","):"&nbsp;");
		if ($ultima_fac)
			ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$ultima_fac[0]['fecha_pago'],$fecha_pago);
		
		$dif1 = mktime(0,0,0,$fecha_pago[2],$fecha_pago[1],$fecha_pago[3]);
		$dif2 = mktime(0,0,0,$mes,$dia,$anio);
		
		$dif = ($dif2 - $dif1) / 86400;
		
		$tpl->assign("id_ultima_fac",($ultima_fac)?$ultima_fac[0]['id']:"");
		$tpl->assign("ultima_fac",($ultima_fac)?"<font color='#".($dif > 90?"FF0000":"000000")."'>".$ultima_fac[0]['fecha_pago']."</font>":"&nbsp;");
		$tpl->assign("perdidas",$perdidas && $perdidas[0]['monto'] != 0?number_format($perdidas[0]['monto'],2,".",","):"&nbsp;");
		$tpl->assign("devoluciones",($devoluciones[0]['sum'] > 0)?number_format($devoluciones[0]['sum'],2,".",","):"&nbsp;");
		$tpl->assign("pro_efectivo",($promedio[0]['promedio'] > 0)?number_format($promedio[0]['promedio'],2,".",","):"&nbsp;");
		$tpl->assign("efectivo",($efectivo[0]['sum'] > 0)?number_format($efectivo[0]['sum'],2,".",","):"&nbsp");
		$tpl->assign("dias",($saldo_pro && $promedio)?$dias:"&nbsp;");
		
		$total_saldo_bancos += $cia[$i]['saldo_bancos'];
		$total_saldo_libros += $cia[$i]['saldo_libros'];
		$total_pendientes += $pendientes[0]['sum'];
		$total_saldo_pro += $saldo_pro[0]['sum'];
		$total_devoluciones += $devoluciones[0]['sum'];
		$total_efectivo += $efectivo[0]['sum'];
		
		$gran_total_saldo_bancos += $cia[$i]['saldo_bancos'];
		$gran_total_saldo_libros += $cia[$i]['saldo_libros'];
		$gran_total_pendientes += $pendientes[0]['sum'];
		$gran_total_saldo_pro += $saldo_pro[0]['sum'];
		$gran_total_devoluciones += $devoluciones[0]['sum'];
		$gran_total_efectivo += $efectivo[0]['sum'];
	}
	// Totales
	$tpl->assign("listado.total_saldo_bancos",number_format($total_saldo_bancos,2,".",","));
	$tpl->assign("listado.total_saldo_libros",number_format($total_saldo_libros,2,".",","));
	$tpl->assign("listado.total_pendientes",number_format($total_pendientes,2,".",","));
	$tpl->assign("listado.total_saldo_pro",number_format($total_saldo_pro,2,".",","));
	$tpl->assign("listado.total_devoluciones",number_format($total_devoluciones,2,".",","));
	$tpl->assign("listado.total_efectivo",number_format($total_efectivo,2,".",","));
}

$tpl->gotoBlock("_ROOT");
$tpl->assign("total_saldo_bancos",number_format($gran_total_saldo_bancos,2,".",","));
$tpl->assign("total_saldo_libros",number_format($gran_total_saldo_libros,2,".",","));
$tpl->assign("total_pendientes",number_format($gran_total_pendientes,2,".",","));
$tpl->assign("total_saldo_pro",number_format($gran_total_saldo_pro,2,".",","));
$tpl->assign("total_devoluciones",number_format($gran_total_devoluciones,2,".",","));
$tpl->assign("total_efectivo",number_format($gran_total_efectivo,2,".",","));

$tpl->printToScreen();
?>