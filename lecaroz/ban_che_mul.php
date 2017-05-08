<?php
// CAPTURA DE CHEQUE MANUAL
// Tablas 'folios_cheque, cheques, facturas, facturas_pagadas, estado_cuenta'
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

// VARIABLES GLOBALES
$numfilas = 100;

// --------------------------------- Insertar datos a la base ------------------------------------------------
if (isset($_POST['fecha'])) {
	for ($i=0; $i<$numfilas; $i++) {
		if ($_POST['num_cia'.$i] > 0 && $_POST['num_proveedor'.$i] > 0 && $_POST['importe'.$i] > 0) {
			$result = ejecutar_script("SELECT folio FROM folios_cheque WHERE num_cia=".$_POST['num_cia'.$i]." AND cuenta = $_POST[cuenta] ORDER BY folio DESC LIMIT 1",$dsn);
			$folio_cheque = $result ? $result[0]['folio'] + 1 : 1;
			
			// Actualizar saldo en libros
			if (existe_registro("saldos",array("num_cia"),array($_POST['num_cia'.$i]),$dsn))
				ejecutar_script("UPDATE saldos SET saldo_libros=saldo_libros-".$_POST['importe'.$i]." WHERE cuenta = $_POST[cuenta] AND num_cia=".$_POST['num_cia'.$i],$dsn);
		
			// Ordenar datos para cheques
			$cheque['cod_mov'] = 5;
			$cheque['codgastos'] = $_POST['codgastos'];
			$cheque['num_proveedor'] = $_POST['num_proveedor'.$i];
			$cheque['num_cia'] = $_POST['num_cia'.$i];
			$cheque['a_nombre'] = $_POST['nombre_proveedor'.$i];
			$cheque['concepto'] = $_POST['concepto'];
			$cheque['fecha'] = $_POST['fecha'];
			$cheque['folio'] = $folio_cheque;
			$cheque['importe'] = $_POST['importe'.$i];
			$cheque['iduser'] = $_SESSION['iduser'];
			$cheque['imp'] = "FALSE";
			$cheque['cuenta'] = $_POST['cuenta'];
			
			$cuenta['num_cia'] = $_POST['num_cia'.$i];
			$cuenta['fecha'] = $_POST['fecha'];
			$cuenta['fecha_con'] = "";
			$cuenta['concepto'] = $_POST['concepto'];
			$cuenta['tipo_mov'] = "TRUE";
			$cuenta['importe'] = $_POST['importe'.$i];
			$cuenta['cod_mov'] = 5;
			$cuenta['folio'] = $folio_cheque;
			$cuenta['cuenta'] = $_POST['cuenta'];
			
			// Ordenar datos para folios_cheque
			$folio['folio'] = $folio_cheque;
			$folio['num_cia'] = $_POST['num_cia'.$i];
			$folio['reservado'] = "FALSE";
			$folio['utilizado'] = "TRUE";
			$folio['fecha'] = $_POST['fecha'];
			$folio['cuenta'] = $_POST['cuenta'];
			
			// Ordenar datos para gastos
			$gasto['num_cia'] = $_POST['num_cia'.$i];
			$gasto['codgastos'] = $_POST['codgastos'];
			$gasto['fecha'] = $_POST['fecha'];
			$gasto['importe'] = $_POST['importe'.$i];
			$gasto['concepto'] = $_POST['concepto'];
			$gasto['captura'] = "TRUE";
			$gasto['folio'] = $folio_cheque;
			
			// Insertar datos
			$db_cheques = new DBclass($dsn,"cheques",$cheque);
			$db_cheques->generar_script_insert("");
			$db_cheques->ejecutar_script();
			
			$db_cuenta = new DBclass($dsn,"estado_cuenta",$cuenta);
			$db_cuenta->generar_script_insert("");
			$db_cuenta->ejecutar_script();
			
			$db_folios = new DBclass($dsn,"folios_cheque",$folio);
			$db_folios->generar_script_insert("");
			$db_folios->ejecutar_script();
			
			$db_gastos = new DBclass($dsn,"movimiento_gastos",$gasto);
			$db_gastos->generar_script_insert("");
			$db_gastos->ejecutar_script();
		}
	}
	
	header("location: ./ban_che_mul.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_che_mul.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Asignar tabla de captura
$tpl->assign("tabla","cheques");

$tpl->assign("fecha",date("d/m/Y"));

// Generar listado de compañías
$cia = ejecutar_script("SELECT num_cia,catalogo_companias.nombre AS nombre_cia,num_proveedor,catalogo_proveedores.nombre AS nombre_proveedor FROM catalogo_companias LEFT JOIN catalogo_proveedores USING (num_proveedor) ORDER BY num_cia ASC",$dsn);
for ($i=0; $i<count($cia); $i++) {
	$tpl->newBlock("nombre_cia");
	$tpl->assign("num_cia",$cia[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cia[$i]['nombre_cia']);
	$tpl->assign("num_pro",$cia[$i]['num_proveedor'] > 0 ? $cia[$i]['num_proveedor'] : "null");
	$tpl->assign("nombre_pro",$cia[$i]['nombre_proveedor']);
}

// Generar listado de proveedores
$pro = ejecutar_script("SELECT num_proveedor,nombre FROM catalogo_proveedores ORDER BY num_proveedor ASC",$dsn);
for ($i=0; $i<count($pro); $i++) {
	$tpl->newBlock("nombre_pro");
	$tpl->assign("num_pro",$pro[$i]['num_proveedor']);
	$tpl->assign("nombre_pro",$pro[$i]['nombre']);
}

// Generar listado de gastos
$gas = ejecutar_script("SELECT codgastos,descripcion FROM catalogo_gastos ORDER BY codgastos ASC",$dsn);
for ($i=0; $i<count($gas); $i++) {
	$tpl->newBlock("nombre_gasto");
	$tpl->assign("codgasto",$gas[$i]['codgastos']);
	$tpl->assign("nombre_gasto",$gas[$i]['descripcion']);
}

for ($i=0; $i<$numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i",$i);
	$tpl->assign("back",$i > 0 ? $i-1 : $numfilas-1);
	$tpl->assign("next",$i < $numfilas-1 ? $i+1 : 0);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message",$descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
die;

?>