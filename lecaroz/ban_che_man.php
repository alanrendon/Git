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

// --------------------------------- Insertar datos a la base ------------------------------------------------
if (isset($_GET['tabla'])) {
	// Almacenar los valores temporalmente
	$_SESSION['fecha'] = $_POST['fecha'];
	$_SESSION['num_cia'] = $_POST['num_cia'];
	$_SESSION['nombre_cia'] = $_POST['nombre_cia'];
	$_SESSION['num_proveedor'] = $_POST['num_proveedor'];
	$_SESSION['nombre_proveedor'] = $_POST['a_nombre'];
	$_SESSION['concepto'] = $_POST['concepto'];
	$_SESSION['codgastos'] = $_POST['codgastos'];
	$_SESSION['nombre_gasto'] = $_POST['nombre_gasto'];
	$_SESSION['factura1'] = $_POST['factura1'];
	$_SESSION['importe1'] = $_POST['importe1'];
	$_SESSION['iva1'] = $_POST['iva1'];
	$_SESSION['total1'] = $_POST['total1'];
	$_SESSION['factura2'] = $_POST['factura2'];
	$_SESSION['importe2'] = $_POST['importe2'];
	$_SESSION['iva2'] = $_POST['iva2'];
	$_SESSION['total2'] = $_POST['total2'];
	$_SESSION['factura3'] = $_POST['factura3'];
	$_SESSION['importe3'] = $_POST['importe3'];
	$_SESSION['iva3'] = $_POST['iva3'];
	$_SESSION['total3'] = $_POST['total3'];
	$_SESSION['importe'] = $_POST['importe'];
	
	// Validar si ya tiene folio inicial
	/*if (!existe_registro("folios_cheque",array("num_cia"),array($_POST['num_cia']),$dsn)) {
		header("location: ./ban_che_man.php?codigo_error=2");
		die;
	}*/
	/*if (!existe_registro("estado_cuenta",array("num_cia"),array($_POST['num_cia']),$dsn)) {
		header("location: ./ban_che_man.php?codigo_error=3");
		die;
	}*/
	
	// Validar que no exista la factura para ese proveedor
	if ($_POST['factura1'] > 0 && existe_registro("facturas",array("num_proveedor","num_fact"),array($_SESSION['num_proveedor'],$_POST['factura1']),$dsn)) {
		header("location: ./ban_che_man.php?codigo_error=1&factura=$_POST[factura1]");
		die;
	}
	if ($_POST['factura2'] > 0 && existe_registro("facturas",array("num_proveedor","num_fact"),array($_SESSION['num_proveedor'],$_POST['factura2']),$dsn)) {
		header("location: ./ban_che_man.php?codigo_error=1&factura=$_POST[factura2]");
		die;
	}
	if ($_POST['factura3'] > 0 && existe_registro("facturas",array("num_proveedor","num_fact"),array($_SESSION['num_proveedor'],$_POST['factura3']),$dsn)) {
		header("location: ./ban_che_man.php?codigo_error=1&factura=$_POST[factura3]");
		die;
	}
	
	$result = ejecutar_script("SELECT folio FROM folios_cheque WHERE num_cia=$_POST[num_cia] ORDER BY folio DESC",$dsn);
	$folio_cheque  = $result[0]['folio'] + 1;
	
	// Actualizar saldo en libros
	if (existe_registro("saldos",array("num_cia"),array($_POST['num_cia']),$dsn))
		ejecutar_script("UPDATE saldos SET saldo_libros=saldo_libros-".$_POST['importe']." WHERE num_cia=".$_POST['num_cia']." AND cuenta=1",$dsn);
	/*else {
		header("location: ./ban_che_man.php?codigo_error=4");
		die;
	}*/

	// Ordenar datos para cheques
	$cheque['cod_mov'] = 5;
	$cheque['codgastos'] = $_POST['codgastos'];
	$cheque['num_proveedor'] = $_POST['num_proveedor'];
	$cheque['num_cia'] = $_POST['num_cia'];
	$cheque['a_nombre'] = $_POST['a_nombre'];
	$cheque['concepto'] = $_POST['concepto'];
	$cheque['fecha'] = $_POST['fecha'];
	$cheque['folio'] = $folio_cheque;
	$cheque['importe'] = $_POST['importe'];
	$cheque['iduser'] = $_SESSION['iduser'];
	$cheque['imp'] = "FALSE";
	$cheque['facturas'] = $_POST['factura1']." ".$_POST['factura2']." ".$_POST['factura3'];
	$cheque['num_cheque'] = "";
	$cheque['fecha_cancelacion'] = "";
	$cheque['codgastos'] = $_POST['codgastos'];
	$cheque['cuenta'] = 1;
	
	$cuenta['num_cia'] = $_POST['num_cia'];
	$cuenta['fecha'] = $_POST['fecha'];
	$cuenta['fecha_con'] = "";
	$cuenta['concepto'] = $_POST['concepto'];
	$cuenta['tipo_mov'] = "TRUE";
	$cuenta['importe'] = $_POST['importe'];
	$cuenta['saldo_ini'] = "";
	$cuenta['saldo_fin'] = "";
	$cuenta['cod_mov'] = 5;
	$cuenta['folio'] = $folio_cheque;
	$cuenta['cuenta'] = 1;
	
	// Ordenar datos para folios_cheque
	$folio['folio'] = $folio_cheque;
	$folio['num_cia'] = $_POST['num_cia'];
	$folio['reservado'] = "FALSE";
	$folio['utilizado'] = "TRUE";
	$folio['fecha'] = $_POST['fecha'];
	$folio['cuenta'] = 1;
	
	// Ordenar datos para gastos
	$gasto['num_cia'] = $_POST['num_cia'];
	$gasto['codgastos'] = $_POST['codgastos'];
	$gasto['fecha'] = $_POST['fecha'];
	$gasto['importe'] = $_POST['importe'];
	$gasto['concepto'] = $_POST['concepto'];
	$gasto['captura'] = "TRUE";
	$gasto['factura'] = "";
	$gasto['folio'] = $folio_cheque;
	
	// Ordenar datos para facturas y facturas_pagadas
	$pro = ejecutar_script("SELECT diascredito FROM catalogo_proveedores WHERE num_proveedor=$_POST[num_proveedor]",$dsn);
	// Descomponer fecha
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})",$_POST['fecha'], $fecha);
	$fecha_ven = date("d/m/Y",mktime(0,0,0,$fecha[2],$fecha[1]+$pro[0]['diascredito'],$fecha[3]));
	
	$count = 0;
	for ($i=1; $i<=3; $i++) {
		if ($_POST['factura'.$i] > 0 && $_POST['importe'.$i] > 0) {
			$fac['num_proveedor'.$count] = $_POST['num_proveedor'];
			$fac['num_cia'.$count] = $_POST['num_cia'];
			$fac['num_fact'.$count] = $_POST['factura'.$i];
			$fac['fecha_mov'.$count] = $_POST['fecha'];
			$fac['fecha_ven'.$count] = $fecha_ven;
			$fac['concepto'.$count] = $_POST['concepto'];
			$fac['imp_sin_iva'.$count] = $_POST['importe'.$i];
			$fac['porciento_iva'.$count] = $_POST['iva'.$i];
			$fac['importe_iva'.$count] = $_POST['total'.$i] - $_POST['importe'.$i];
			$fac['porciento_ret_isr'.$count] = 0;
			$fac['porciento_ret_iva'.$count] = 0;
			$fac['codgastos'.$count] = $_POST['codgastos'];
			$fac['importe_total'.$count] = $_POST['total'.$i];
			$fac['tipo_factura'.$count] = 0;
			$fac['fecha_captura'.$count] = date("d/m/Y");
			$fac['iduser'.$count] = $_SESSION['iduser'];
			
			$fac_pag['num_cia'.$count] = $_POST['num_cia'];
			$fac_pag['num_proveedor'.$count] = $_POST['num_proveedor'];
			$fac_pag['num_fact'.$count] = $_POST['factura'.$i];
			$fac_pag['total'.$count] = $_POST['total'.$i];
			$fac_pag['descripcion'.$count] = $_POST['concepto'];
			$fac_pag['fecha_mov'.$count] = $_POST['fecha'];
			$fac_pag['fecha_pago'.$count] = $fecha_ven;
			$fac_pag['fecha_cheque'.$count] = $_POST['fecha'];
			$fac_pag['folio_cheque'.$count] = $folio_cheque;
			$fac_pag['codgastos'.$count] = $_POST['codgastos'];
			$fac_pag['proceso'.$count] = "FALSE";
			$fac_pag['imp'.$count] = "TRUE";
		}
	}
	
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
	
	if (isset($fac)) {
		$db_fac = new DBclass($dsn,"facturas",$fac);
		$db_fac->xinsertar();
	}
	
	if (isset($fac_pag)) {
		$db_fac_pag = new DBclass($dsn,"facturas_pagadas",$fac_pag);
		$db_fac_pag->xinsertar();
	}
	
	// Borrar valores temporales
	//unset($_SESSION['fecha']);
	unset($_SESSION['num_cia']);
	unset($_SESSION['nombre_cia']);
	unset($_SESSION['num_proveedor']);
	unset($_SESSION['nombre_proveedor']);
	//unset($_SESSION['concepto']);
	unset($_SESSION['codgastos']);
	unset($_SESSION['nombre_gasto']);
	unset($_SESSION['factura1']);
	unset($_SESSION['importe1']);
	unset($_SESSION['iva1']);
	unset($_SESSION['total1']);
	unset($_SESSION['factura2']);
	unset($_SESSION['importe2']);
	unset($_SESSION['iva2']);
	unset($_SESSION['total2']);
	unset($_SESSION['factura3']);
	unset($_SESSION['importe3']);
	unset($_SESSION['iva3']);
	unset($_SESSION['total3']);
	unset($_SESSION['importe']);
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_che_man.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Asignar tabla de captura
$tpl->assign("tabla","cheques");

// Restablecer valores de campos despues de un error
if (isset($_GET['codigo_error'])) {
	$tpl->assign("fecha",$_SESSION['fecha']);
	$tpl->assign("num_cia",$_SESSION['num_cia']);
	$tpl->assign("nombre_cia",$_SESSION['nombre_cia']);
	$tpl->assign("num_proveedor",$_SESSION['num_proveedor']);
	$tpl->assign("nombre_proveedor",$_SESSION['nombre_proveedor']);
	$tpl->assign("concepto",$_SESSION['concepto']);
	$tpl->assign("codgastos",$_SESSION['codgastos']);
	$tpl->assign("nombre_gasto",$_SESSION['nombre_gasto']);
	$tpl->assign("factura1",$_SESSION['factura1']);
	$tpl->assign("importe1",number_format($_SESSION['importe1'],2,".",""));
	$tpl->assign("iva1_$_SESSION[iva1]","selected");
	$tpl->assign("total1",number_format($_SESSION['total1'],2,".",""));
	$tpl->assign("factura2",$_SESSION['factura2']);
	$tpl->assign("importe2",number_format($_SESSION['importe2'],2,".",""));
	$tpl->assign("iva2_$_SESSION[iva2]","selected");
	$tpl->assign("total2",number_format($_SESSION['total2'],2,".",""));
	$tpl->assign("factura3",$_SESSION['factura3']);
	$tpl->assign("importe3",number_format($_SESSION['importe3'],2,".",""));
	$tpl->assign("iva3_$_SESSION[iva3]","selected");
	$tpl->assign("total3",number_format($_SESSION['total3'],2,".",""));
	$tpl->assign("importe",number_format($_SESSION['importe'],2,".",""));
}
else {
	$tpl->assign("fecha",date("d/m/Y"));
	$tpl->assign("iva1_0","selected");
	$tpl->assign("total1","0.00");
	$tpl->assign("iva2_0","selected");
	$tpl->assign("total2","0.00");
	$tpl->assign("iva3_0","selected");
	$tpl->assign("total3","0.00");
	$tpl->assign("importe","0.00");
}

if (isset($_SESSION['concepto']))
	$tpl->assign("concepto",$_SESSION['concepto']);

$tpl->assign("fecha",(isset($_SESSION['fecha']))?$_SESSION['fecha']:"");

// Generar listado de compañías
$cia = ejecutar_script("SELECT num_cia,nombre FROM catalogo_companias ORDER BY num_cia ASC",$dsn);
for ($i=0; $i<count($cia); $i++) {
	$tpl->newBlock("nombre_cia");
	$tpl->assign("num_cia",$cia[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cia[$i]['nombre']);
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

//Generar listado de saldos
$saldo=ejecutar_script("SELECT * from saldos WHERE cuenta = 1 order by num_cia",$dsn);
for ($i=0; $i<count($saldo); $i++) {
	$tpl->newBlock("importe_saldo");
	$tpl->assign("num_cia",$saldo[$i]['num_cia']);
	$tpl->assign("saldo",$saldo[$i]['saldo_libros']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	if (isset($_GET['factura']))
		$descripcion_error[1] = "La factura no. $_GET[factura] para el proveedor $_SESSION[num_proveedor] ya existe en la Base de Datos";
	$descripcion_error[2] = "La compañía no tiene folio inicial para cheques";
	$descripcion_error[3] = "La compañía no tiene saldo inicial";
	$descripcion_error[4] = "La compañía no tiene saldo para pagar el cheque";
	
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