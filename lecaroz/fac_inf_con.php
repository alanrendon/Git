<?php
// CONSULTA DE INFONAVIT
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
$tpl->assignInclude("body","./plantillas/fac/fac_inf_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Pantalla inicial
if (!isset($_GET['num_cia']) && !isset($_GET['id'])) {
	$tpl->newBlock("cia");
	
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
	$emp = ejecutar_script("SELECT id,num_emp,ap_paterno,ap_materno,nombre FROM catalogo_trabajadores WHERE num_cia=$_GET[num_cia] AND credito_infonavit='TRUE' AND num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . " ORDER BY num_emp ASC",$dsn);
	// Si no hubo ningun resultado, regresar a la pantalla inicial
	if (!$emp) {
		header("location:./fac_inf_con.php?codigo_error=1");
		die;
	}
	// Generar pantalla
	$cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia=$_GET[num_cia]",$dsn);
	$tpl->newBlock("empleados");
	$tpl->assign("num_cia",$_GET['num_cia']);
	$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
	
	for ($i=0; $i<count($emp); $i++) {
		$tpl->newBlock("fila1");
		$tpl->assign("id",$emp[$i]['id']);
		$tpl->assign("num_emp",$emp[$i]['num_emp']);
		$tpl->assign("nombre_emp",$emp[$i]['nombre']." ".$emp[$i]['ap_paterno']." ".$emp[$i]['ap_materno']);
	}
	
	$tpl->printToScreen();
}

if (isset($_GET['id'])) {
//echo "id $_GET[id]";
	$emp = ejecutar_script("SELECT num_cia,nombre,ap_paterno,ap_materno FROM catalogo_trabajadores WHERE id = $_GET[id]",$dsn);
	$cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = ".$emp[0]['num_cia'],$dsn);
	$pagos = ejecutar_script("SELECT * FROM infonavit WHERE id_emp = $_GET[id] ORDER BY folio ASC",$dsn);
	
	// Generar listado de pagos
	$tpl->newBlock("pagos");
	$tpl->assign("num_cia",$emp[0]['num_cia']);
	$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
	$tpl->assign("nombre",$emp[0]['nombre']." ".$emp[0]['ap_paterno']." ".$emp[0]['ap_materno']);
	$total = 0;
	for ($i=0; $i<count($pagos); $i++) {
		$tpl->newBlock("fila2");
		$tpl->assign("fecha_mov",$pagos[$i]['fecha']);
		$tpl->assign("folio",$pagos[$i]['folio']);
		$tpl->assign("importe",number_format($pagos[$i]['importe'],2,".",","));
		switch ($pagos[$i]['mes']) {
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
			default: $mes = "SIN MES"; break;
		}
//		echo $mes;
		$tpl->assign("mes",$mes);
		$total += $pagos[$i]['importe'];
	}
	$tpl->assign("pagos.total",number_format($total,2,".",","));
	
	$tpl->printToScreen();
}

?>