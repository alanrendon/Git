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
if (!isset($_GET['tipo'])) {
	$tpl->newBlock("datos");
	
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
else if (isset($_GET['tipo'])) {
	if ($_GET['tipo'] == "cia") {
		$fecha1 = date("1/m/Y");
		$fecha2 = date("d/m/Y");
		
		$cia = ejecutar_script("SELECT num_cia,nombre,clabe_cuenta,saldo_libros,saldo_bancos FROM catalogo_companias LEFT JOIN saldos USING(num_cia) WHERE num_cia = $_GET[num_cia] ORDER BY num_cia ASC",$dsn);
		$tpl->newBlock("saldo_cia");
		$tpl->assign("num_cia",$cia[0]['num_cia']);
		$tpl->assign("nombre_cia",$cia[0]['nombre']);
		$tpl->assign("dia",date("d"));
		$tpl->assign("mes",mes(date("n")));
		$tpl->assign("anio",date("Y"));
		
		// Obtener todos los datos
		$pendientes = ejecutar_script("SELECT SUM(importe) FROM estado_cuenta WHERE num_cia=$_GET[num_cia] AND fecha_con IS NULL AND tipo_mov='TRUE' AND cod_mov=5",$dsn);
		$saldo_pro = ejecutar_script("SELECT SUM(total) FROM pasivo_proveedores WHERE num_cia=$_GET[num_cia]",$dsn);	// Saldo proveedores
		$ultima_fac = ejecutar_script("SELECT fecha_pago FROM pasivo_proveedores WHERE num_cia=$_GET[num_cia] ORDER BY fecha_pago ASC LIMIT 1",$dsn);
		$devoluciones = ejecutar_script("SELECT SUM(importe) FROM estado_cuenta WHERE num_cia=$_GET[num_cia] AND cod_mov=18 AND fecha>='$fecha1' AND fecha<='$fecha2'",$dsn);
		if ($_GET['num_cia'] && $_GET['num_cia'] < 200 || $_GET['num_cia'] == 702 || $_GET['num_cia'] == 703 || $_GET['num_cia'] == 704)
			$sql = "SELECT AVG(efectivo) AS promedio FROM total_companias WHERE num_cia=$_GET[num_cia] AND fecha>='$fecha1' AND fecha<='$fecha2'";
		else
			$sql = "SELECT AVG(efectivo) AS promedio FROM total_panaderias WHERE num_cia = $_GET[num_cia] AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND efe = 'TRUE' AND exp = 'TRUE' AND gas = 'TRUE' AND pro = 'TRUE' AND pas = 'TRUE'";
		$promedio = ejecutar_script($sql,$dsn);
		$dias = ($saldo_pro && $promedio && $promedio[0]['promedio'] > 0 && $saldo_pro[0]['sum'] > 0)?ceil($saldo_pro[0]['sum']/$promedio[0]['promedio']):"";
		
		$tpl->assign("cuenta",$cia[0]['clabe_cuenta']);
		$tpl->assign("saldo_libros",number_format($cia[0]['saldo_libros'],2,".",","));
		$tpl->assign("saldo_bancos",number_format($cia[0]['saldo_bancos'],2,".",","));
		$tpl->assign("pendientes",($pendientes[0]['sum'] > 0)?number_format($pendientes[0]['sum'],2,".",","):"&nbsp;");
		$tpl->assign("saldo_pro",($saldo_pro)?number_format($saldo_pro[0]['sum'],2,".",","):"&nbsp;");
		$tpl->assign("ultima_fac",($ultima_fac)?$ultima_fac[0]['fecha_pago']:"&nbsp;");
		$tpl->assign("devoluciones",($devoluciones)?number_format($devoluciones[0]['sum'],2,".",","):"&nbsp;");
		$tpl->assign("pro_efectivo",($promedio)?number_format($promedio[0]['promedio'],2,".",","):"&nbsp;");
		$tpl->assign("dias",($saldo_pro && $promedio)?$dias:"&nbsp;");
		$tpl->printToScreen();
	}
	else if ($_GET['tipo'] == "todas") {
		$fecha1 = date("1/m/Y");
		$fecha2 = date("d/m/Y");
		
		$cia = ejecutar_script("SELECT num_cia,nombre_corto,clabe_cuenta,saldo_libros,saldo_bancos FROM catalogo_companias LEFT JOIN saldos USING(num_cia) ORDER BY num_cia ASC",$dsn);
		$tpl->newBlock("saldos_all");
		$tpl->assign("dia",date("d"));
		$tpl->assign("mes",mes(date("n")));
		$tpl->assign("anio",date("Y"));
		
		for ($i=0; $i<count($cia); $i++) {
			// Obtener todos los datos
			$pendientes = ejecutar_script("SELECT SUM(importe) FROM estado_cuenta WHERE num_cia=".$cia[$i]['num_cia']." AND fecha_con IS NULL AND tipo_mov='TRUE' AND cod_mov=5",$dsn);
			$saldo_pro = ejecutar_script("SELECT SUM(total) FROM pasivo_proveedores WHERE num_cia=".$cia[$i]['num_cia'],$dsn);	// Saldo proveedores
			$ultima_fac = ejecutar_script("SELECT fecha_pago FROM pasivo_proveedores WHERE num_cia=".$cia[$i]['num_cia']." ORDER BY fecha_pago ASC LIMIT 1",$dsn);
			$devoluciones = ejecutar_script("SELECT SUM(importe) FROM estado_cuenta WHERE num_cia=".$cia[$i]['num_cia']." AND cod_mov=18 AND fecha>='$fecha1' AND fecha<='$fecha2'",$dsn);
			if ($cia[$i]['num_cia'] > 100 && $cia[$i]['num_cia'] < 200 || $cia[$i]['num_cia'] == 702 || $cia[$i]['num_cia'] == 703 || $cia[$i]['num_cia'] == 704)
				$sql = "SELECT AVG(efectivo) AS promedio FROM total_companias WHERE num_cia=".$cia[$i]['num_cia']." AND fecha>='$fecha1' AND fecha<='$fecha2'";
			else
				$sql = "SELECT AVG(efectivo) AS promedio FROM total_panaderias WHERE num_cia = ".$cia[$i]['num_cia']." AND fecha >= '$fecha1' AND fecha <= '$fecha2' AND efe = 'TRUE' AND exp = 'TRUE' AND gas = 'TRUE' AND pro = 'TRUE' AND pas = 'TRUE'";
			$promedio = ejecutar_script($sql,$dsn);
			$dias = ($saldo_pro && $promedio && $promedio[0]['promedio'] > 0 && $saldo_pro[0]['sum'] > 0)?ceil($saldo_pro[0]['sum']/$promedio[0]['promedio']):"";
			
			$tpl->newBlock("fila");
			$tpl->assign("num_cia",$cia[$i]['num_cia']);
			$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
			$tpl->assign("cuenta",$cia[$i]['clabe_cuenta']);
			$tpl->assign("saldo_libros",number_format($cia[$i]['saldo_libros'],2,".",","));
			$tpl->assign("saldo_bancos",number_format($cia[$i]['saldo_bancos'],2,".",","));
			$tpl->assign("pendientes",($pendientes[0]['sum'] > 0)?number_format($pendientes[0]['sum'],2,".",","):"&nbsp;");
			$tpl->assign("saldo_pro",($saldo_pro)?number_format($saldo_pro[0]['sum'],2,".",","):"&nbsp;");
			$tpl->assign("ultima_fac",($ultima_fac)?$ultima_fac[0]['fecha_pago']:"&nbsp;");
			$tpl->assign("devoluciones",($devoluciones)?number_format($devoluciones[0]['sum'],2,".",","):"&nbsp;");
			$tpl->assign("pro_efectivo",($promedio)?number_format($promedio[0]['promedio'],2,".",","):"&nbsp;");
			$tpl->assign("dias",($saldo_pro && $promedio)?$dias:"&nbsp;");
		}
		$tpl->printToScreen();
	}
}

?>