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

$users = array(28, 29, 30, 31);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

$descripcion_error[1] = "La Compañía no existe en la Base de Datos";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_lib_pro.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['dif'])) {
	$tpl->newBlock("datos");
	$tpl->printToScreen();
	die;
}

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

$tpl->newBlock("listado");
$tpl->assign("dia",date("d"));
$tpl->assign("mes",mes(date("n")));
$tpl->assign("anio",date("Y"));
$tpl->assign("hora", date('h:ia'));

/******************** PANADERIAS *******************/
$cia = ejecutar_script("SELECT num_cia,nombre_corto,clabe_cuenta,saldo_libros,saldo_bancos FROM catalogo_companias LEFT JOIN saldos USING(num_cia) WHERE" . (in_array($_SESSION['iduser'], $users) ? " num_cia BETWEEN 900 AND 950" : " num_cia < 100") . " ORDER BY num_cia ASC",$dsn);

$anio_actual = date("Y");

$total_saldo_libros = 0;
$total_saldo_pro = 0;
$total_dif = 0;

for ($i=0; $i<count($cia); $i++) {
	// Obtener todos los datos
	$saldo_pro = ejecutar_script("SELECT SUM(total) FROM pasivo_proveedores WHERE num_cia=".$cia[$i]['num_cia'],$dsn);	// Saldo proveedores
	
	if (($_GET['dif'] == 1 && $cia[$i]['saldo_libros'] - $saldo_pro[0]['sum'] > 0) || ($_GET['dif'] == 2 && $cia[$i]['saldo_libros'] - $saldo_pro[0]['sum'] < 0)) {
		$tpl->newBlock("fila");
		$tpl->assign("dia",$dia);
		$tpl->assign("mes",$mes);
		$tpl->assign("anio",$anio);
		$tpl->assign("num_cia",$cia[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
		$tpl->assign("color_saldo_libros", $cia[$i]['saldo_libros'] > 0 ? "0000FF" : "FF0000");
		$tpl->assign("saldo_libros",number_format($cia[$i]['saldo_libros'],2,".",","));
		$tpl->assign("saldo_pro",($saldo_pro[0]['sum'] > 0)?number_format($saldo_pro[0]['sum'],2,".",","):"&nbsp;");
		$tpl->assign("color_dif", $cia[$i]['saldo_libros'] - $saldo_pro[0]['sum'] > 0 ? "0000FF" : "FF0000");
		$tpl->assign("diferencia",number_format($cia[$i]['saldo_libros'] - $saldo_pro[0]['sum'],2,".",","));
		
		$total_saldo_libros += $cia[$i]['saldo_libros'];
		$total_saldo_pro += $saldo_pro[0]['sum'];
		$total_dif += $cia[$i]['saldo_libros'] - $saldo_pro[0]['sum'];
	}
}
// Totales
$tpl->assign("listado.total_saldo_libros",number_format($total_saldo_libros,2,".",","));
$tpl->assign("listado.total_saldo_pro",number_format($total_saldo_pro,2,".",","));
$tpl->assign("listado.total_diferencia",number_format($total_dif,2,".",","));

$tpl->printToScreen();
?>