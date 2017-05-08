<?php
// PROCESO SECUENCIAL
// Tablas 'compra_directa', 'hoja_dia_rost', 'movimiento_gastos', 'total_companias'
// Menu 'No definido'

define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// --------------------------------- COMPRA DIRECTA ----------------------------------------------------------
if (!isset($_GET['tabla'])) {
	// Crear bloque para la pantalla de compra directa
	$tpl->newBlock("compra_directa");
	
	// Obtener los listados de compañías, proveedores y materias prima
	$cia = obtener_registro("catalogo_companias",array(),array(),"num_cia","ASC",$dsn);
	$proveedor = obtener_registro("catalogo_proveedores",array(),array(),"num_proveedor","ASC",$dsn);
	$mp = obtener_registro("catalogo_mat_primas",array(),array(),"codmp","ASC",$dsn);
	
	//$tpl->assign("fecha_mov",date("d/m/Y"));
	
	for ($i=0; $i<count($cia); $i++) {
		if ($cia[$i]['num_cia'] > 100 && $cia[$i]['num_cia'] < 201) {
			$tpl->newBlock("nombre_cia");
			$tpl->assign("num_cia",$cia[$i]['num_cia']);
			$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
		}
	}
	
	for ($i=0; $i<count($proveedor); $i++) {
		$tpl->newBlock("nombre_proveedor");
		$tpl->assign("num_proveedor",$proveedor[$i]['num_proveedor']);
		$tpl->assign("nombre_proveedor",$proveedor[$i]['nombre']);
	}
	
	for ($i=0; $i<count($mp); $i++) {
		$tpl->newBlock("nombre_mp");
		$tpl->assign("codmp",$mp[$i]['codmp']);
		$tpl->assign("nombre_mp",$mp[$i]['nombre']);
	}
	
	$num_filas = 15;
	for ($i=0; $i<$num_filas; $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("i",$i);
	}
	
	$tpl->printToScreen();
	die();
}
// --------------------------------- HOJA DIARIA ----------------------------------------------------------
else if ($_GET['tabla'] == "hoja_dia_rost") {
	$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia']),"","",$dsn);
	
	// Almacenar valores de la pantalla de compra directa
	$_SESSION['num_cia'] = $_POST['num_cia']; // Almacenar para toda la sesión el número de compañía
	$_SESSION['num_proveedor'] = $_POST['num_proveedor'];
	$_SESSION['numero_fact'] = $_POST['numero_fact'];
	$_SESSION['fecha'] = $_POST['fecha_mov']; // Almacenar para toda la sesión la fecha de movimiento
	$_SESSION['fecha_pago'] = $_POST['fecha_pago'];
	$_SESSION['cd_total'] = $_POST['total'];  // Almacenar para toda la sesión el total de la compra directa
	$_SESSION['aplica_gasto'] = $_POST['aplica_gasto']; // Almacenar si aplica gasto o no la compra directa
	
	$j = 0;
	for ($i=0; $i<15; $i++) {
		if ($_POST['codmp'.$i] != "") {
			$_SESSION['cd_codmp'.$j] = $_POST['codmp'.$i];
			$_SESSION['cd_cantidad'.$j] = $_POST['cantidad'.$i];
			$_SESSION['cd_kilos'.$j] = $_POST['kilos'.$i];
			$_SESSION['cd_precio_unit'.$j] = $_POST['precio_unit'.$i];
			$_SESSION['cd_total'.$j] = $_POST['total'.$i];
			$j++;
		}
	}
	$_SESSION['cd_numfilas'] = $j;
	
	// Crear bloque de captura
	$tpl->newBlock("hoja_diaria");
	$tpl->assign("num_cia_hoja",$_SESSION['num_cia']);
	$tpl->assign("nombre_cia_hoja",$cia[0]['nombre_corto']);
	$tpl->assign("fecha_hoja",$_SESSION['fecha']);
	
	// Obtener materias primas y sus existencias del inventario
	$rosticeria = obtener_registro("inventario_real",array("num_cia"),array($_SESSION['num_cia']),"codmp","ASC",$dsn);
	$tpl->assign("numfilas",count($rosticeria));
	
	// Trazar tabla de captura
	for ($i=0; $i<count($rosticeria); $i++) {
		$tpl->newBlock("fila_hoja");
		$mp = obtener_registro("catalogo_mat_primas",array("codmp"),array($rosticeria[$i]['codmp']),"","",$dsn);
		$tpl->assign("i",$i);
		$tpl->assign("codmp_hoja",$rosticeria[$i]['codmp']);
		$tpl->assign("nombre_mp_hoja",$mp[0]['nombre']);
		$tpl->assign("existencia",$rosticeria[$i]['existencia']);
	}
	$tpl->printToScreen();
	die();
}

// --------------------------------- GASTOS ----------------------------------------------------------------
else if ($_GET['tabla'] == "movimiento_gastos") {
	$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_SESSION['num_cia']),"","",$dsn);
	
	// Almacenar valores de la pantalla anterior
	$j = 0;
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		if ($_POST['precio_total'.$i] != "") {
			$_SESSION['hd_codmp'.$j] = $_POST['codmp'.$i];
			$_SESSION['hd_unidades'.$j] = $_POST['unidades'.$i];
			$_SESSION['hd_precio_unitario'.$j] = $_POST['precio_unitario'.$i];
			$_SESSION['hd_precio_total'.$j] = $_POST['precio_total'.$i];
			$j++;
		}
	}
	$_SESSION['hd_numfilas'] = $j;
	
	// Almacena el total de la hoja diaria
	$_SESSION['hd_total'] = $_POST['venta_total'];
	
	// Crear bloque de captura
	$tpl->newBlock("gastos");
	
	$tpl->assign("num_cia_gastos",$_SESSION['num_cia']);
	$tpl->assign("nombre_cia_gastos",$cia[0]['nombre_corto']);
	$tpl->assign("fecha_gastos",$_SESSION['fecha']);
	
	$gasto = obtener_registro("catalogo_gastos",array(),array(),"codgastos","ASC",$dsn);
	for ($i=0; $i<count($gasto); $i++) {
		$tpl->newBlock("nombre_gasto");
		$tpl->assign("codgasto",$gasto[$i]['codgastos']);
		$tpl->assign("nombregasto",$gasto[$i]['descripcion']);
	}
	
	$tpl->assign("num_cia_gastos",$_SESSION['num_cia']);
	$tpl->assign("fecha_gastos",$_SESSION['fecha']);
	
	for ($i=0; $i<10; $i++) {
		$tpl->newBlock("fila_gastos");
		$tpl->assign("i",$i);
	}
	$tpl->gotoBlock("gastos");
	
	if ($_SESSION['aplica_gasto'] == "TRUE") {
		$tpl->assign("gastos_directos",$_SESSION['cd_total']);
	}
	else {
		$tpl->assign("gastos_directos",0);
	}
	
	$tpl->printToScreen();
	die();
}

// --------------------------------- TOTALES -------------------------------------------------------------
if ($_GET['tabla'] == "total_companias") {
	$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_SESSION['num_cia']),"","",$dsn);

	// Almacenar valores de la pantalla de gastos
	$j = 0;
	for ($i=0; $i<10; $i++) {
		if ($_POST['codgastos'.$i] != "") {
			$_SESSION['g_codgastos'.$j] = $_POST['codgastos'.$i];
			$_SESSION['g_importe'.$j] = $_POST['importe'.$i];
			$_SESSION['g_concepto'.$j] = $_POST['concepto'.$i];
			$j++;
		}
	}
	$_SESSION['g_numfilas'] = $j;
	
	// Almacenar total de gastos
	$_SESSION['g_total'] = $_POST['gastos_dia'];
	
	$tpl->newBlock("totales");
	$tpl->assign("num_cia_total",$_SESSION['num_cia']);
	$tpl->assign("nombre_cia_total",$cia[0]['nombre_corto']);
	$tpl->assign("fecha_total",$_SESSION['fecha']);
	
	$tpl->assign("venta",$_SESSION['hd_total']);
	$tpl->assign("gastos",$_SESSION['g_total']);
	$tpl->assign("efectivo",$_SESSION['hd_total']);

	$tpl->assign("ventaf",number_format($_SESSION['hd_total'],2,".",","));
	$tpl->assign("gastosf",number_format($_SESSION['g_total'],2,".",","));
	$tpl->assign("efectivof",number_format($_SESSION['hd_total']-$_POST['gastos_dia'],2,".",","));
	
	$tpl->printToScreen();
}
if ($_GET['tabla'] == "insertar") {
	// Ordenar datos de compra directa
	for ($i=0; $i<$_SESSION['cd_numfilas']; $i++) {
		$cd['codmp'.$i] = $_SESSION['cd_codmp'.$i];
		$cd['num_proveedor'.$i] = $_SESSION['num_proveedor'];
		$cd['num_cia'.$i] = $_SESSION['num_cia'];
		$cd['numero_fact'.$i] = $_SESSION['numero_fact'];
		$cd['fecha_mov'.$i] = $_SESSION['fecha'];
		$cd['cantidad'.$i] = $_SESSION['cd_cantidad'.$i];
		$cd['kilos'.$i] = $_SESSION['cd_kilos'.$i];
		$cd['precio_unit'.$i] = $_SESSION['cd_precio_unit'.$i];
		$cd['aplica_gasto'.$i] = $_SESSION['aplica_gasto'];
		$cd['total'.$i] = $_SESSION['cd_total'.$i];
		$cd['fecha_pago'.$i] = $_SESSION['fecha_pago'];
	}
	
	// Ordenar datos de hoja diaria
	for ($i=0; $i<$_SESSION['hd_numfilas']; $i++) {
		$hd['num_cia'.$i] = $_SESSION['num_cia'];
		$hd['codmp'.$i] = $_SESSION['hd_codmp'.$i];
		$hd['unidades'.$i] = $_SESSION['hd_unidades'.$i];
		$hd['precio_unitario'.$i] = $_SESSION['hd_precio_unitario'.$i];
		$hd['precio_total'.$i] = $_SESSION['hd_precio_total'.$i];
		$hd['fecha'.$i] = $_SESSION['fecha'];
	}
	
	// Ordenar datos de gastos
	for ($i=0; $i<$_SESSION['g_numfilas']; $i++) {
		$g['codgastos'.$i] = $_SESSION['g_codgastos'.$i];
		$g['num_cia'.$i] = $_SESSION['num_cia'];
		$g['fecha'.$i] = $_SESSION['fecha'];
		$g['importe'.$i] = $_SESSION['g_importe'.$i];
		$g['concepto'.$i] = $_SESSION['g_concepto'.$i];
	}
	
	$db_cd = new DBclass($dsn,"compra_directa",$cd);
	$db_cd->xinsertar();
	
	$db_hd = new DBclass($dsn,"hoja_diaria_rost",$hd);
	$db_hd->xinsertar();
	
	$db_g = new DBclass($dsn,"movimiento_gastos",$g);
	$db_g->xinsertar();
	
	$db_totales = new DBclass($dsn,"total_companias",$_POST);
	$db_totales->generar_script_insert("");
	$db_totales->ejecutar_script();
	
	header("location: ./");
}
?>