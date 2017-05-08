<?php
// FACTURAS VARIOS
// Tabla 'facturas'
// Menu Proveedores y facturas -> Facturas de proveedores varios

define ('IDSCREEN',3121); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos.";
$descripcion_error[2] = "Número de proveedor no existe en la Base de Datos.";
$descripcion_error[3] = "Código de gasto no existe en la Base de Datos.";
$descripcion_error[4] = "El número de factura ya existe en la Base de Datos";


// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

$users = array(28, 29, 30, 31, 32, 33);

// --------------------------------- Verificar y almacenar datos ---------------------------------------------
if (isset($_GET['tabla'])) {
	// Almacenar datos en variables de sesión
	$_SESSION['num_cia'] = $_POST['num_cia'];
	$_SESSION['num_proveedor'] = $_POST['num_proveedor'];
	$_SESSION['num_fact'] = $_POST['num_fact'];
	$_SESSION['fecha_mov'] = $_POST['fecha_mov'];
	$_SESSION['fecha_ven'] = $_POST['fecha_ven'];
	$_SESSION['concepto'] = $_POST['concepto'];
	$_SESSION['tipo_factura'] = $_POST['tipo_factura'];
	$_SESSION['imp_sin_iva'] = $_POST['imp_sin_iva'];
	$_SESSION['porciento_iva'] = $_POST['porciento_iva'];
	$_SESSION['importe_iva'] = $_POST['importe_iva'];
	$_SESSION['porciento_ret_isr'] = $_POST['porciento_ret_isr'];
	$_SESSION['importe_ret_isr'] = $_POST['importe_ret_isr'];
	$_SESSION['porciento_ret_iva'] = $_POST['porciento_ret_iva'];
	$_SESSION['importe_ret_iva'] = $_POST['importe_ret_iva'];
	$_SESSION['importe_total'] = $_POST['importe_total'];
	$_SESSION['codgastos'] = $_POST['codgastos'];
	
	// Validar si ya esta capturada la factura
	if (existe_registro("facturas",array("num_proveedor","num_fact"),array($_POST['num_proveedor'],$_POST['num_fact']),$dsn)) {
		header("location: ./fac_fpv_cap.php?num_cia=$_POST[num_cia]&num_proveedor=$_POST[num_proveedor]&codigo_error=4");
		die;
	}
	// Validar si existe el código del gasto
	if (!existe_registro("catalogo_gastos",array("codgastos"),array($_POST['codgastos']),$dsn)) {
		header("location: ./fac_fpv_cap.php?num_cia=$_POST[num_cia]&num_proveedor=$_POST[num_proveedor]&codigo_error=3");
		die;
	}
	
	if ($_POST['num_cia'] < 900) {
		// Ordenar datos para facturas
		$fac['num_proveedor'] = $_POST['num_proveedor'];
		$fac['num_cia'] = $_POST['num_cia'];
		$fac['num_fact'] = $_POST['num_fact'];
		$fac['fecha_mov'] = $_POST['fecha_mov'];
		$fac['fecha_ven'] = $_POST['fecha_ven'];
		$fac['concepto'] = $_POST['concepto'];
		$fac['imp_sin_iva'] = $_POST['imp_sin_iva'];
		$fac['porciento_iva'] = $_POST['porciento_iva'];
		$fac['importe_iva'] = $_POST['importe_iva'];
		$fac['porciento_ret_isr'] = $_POST['porciento_ret_isr'];
		$fac['porciento_ret_iva'] = $_POST['porciento_ret_iva'];
		$fac['importe_total'] = $_POST['importe_total'];
		$fac['codgastos'] = $_POST['codgastos'];
		$fac['tipo_factura'] = $_POST['tipo_factura'];
		$fac['fecha_captura'] = date("d/m/Y");
		$fac['iduser'] = $_SESSION['iduser'];
		
		// Ordenar datos para gastos
		$gas['codgastos'] = $_POST['codgastos'];
		$gas['num_cia'] = $_POST['num_cia'];
		$gas['fecha'] = $_POST['fecha_mov'];
		$gas['importe'] = $_POST['importe_total'];
		$gas['concepto'] = $_POST['concepto'];
		$gas['captura'] = "true";
		
		// Ordenar datos para pasivo
		$pas['num_cia'] = $_POST['num_cia'];
		$pas['codmp'] = "";
		$pas['num_fact'] = $_POST['num_fact'];
		$pas['total'] = $_POST['importe_total'];
		$pas['descripcion'] = $_POST['concepto'];
		$pas['fecha_mov'] = $_POST['fecha_mov'];
		$pas['fecha_pago'] = $_POST['fecha_ven'];
		$pas['num_proveedor'] = $_POST['num_proveedor'];
		$pas['codgastos'] = $_POST['codgastos'];
		
		$db_fac = new DBclass($dsn,"facturas",$fac);
		$db_fac->generar_script_insert("");
		$db_fac->ejecutar_script();
		
		//$db_gas = new DBclass($dsn,"movimiento_gastos",$gas);
		//$db_gas->generar_script_insert("");
		//$db_gas->ejecutar_script();
		
		$db_pas = new DBclass($dsn,"pasivo_proveedores",$pas);
		$db_pas->generar_script_insert("");
		$db_pas->ejecutar_script();
	}
	else {
		$sql = "INSERT INTO facturas_zap (num_cia, num_proveedor, num_fact, fecha, concepto, codgastos, importe, iva, pisr, isr, pivaret, ivaret, total, iduser, tscap, por_aut, copia_fac) VALUES (";
		$sql .= "$_POST[num_cia], $_POST[num_proveedor], $_POST[num_fact], '$_POST[fecha_mov]','$_POST[concepto]', $_POST[codgastos], $_POST[imp_sin_iva], $_POST[importe_iva], $_POST[porciento_ret_isr],";
		$sql .= " $_POST[importe_ret_isr], $_POST[porciento_ret_iva], $_POST[importe_ret_iva], $_POST[importe_total], $_SESSION[iduser], now(), 'TRUE', 'FALSE')";
		ejecutar_script($sql, $dsn);
	}
	
	unset($_SESSION['num_cia']);
	//unset($_SESSION['num_proveedor']);
	unset($_SESSION['num_fact']);
	//unset($_SESSION['fecha_mov']);
	unset($_SESSION['fecha_ven']);
	//unset($_SESSION['concepto']);
	unset($_SESSION['tipo_mov']);
	unset($_SESSION['imp_sin_iva']);
	unset($_SESSION['porciento_iva']);
	unset($_SESSION['importe_iva']);
	unset($_SESSION['porciento_ret_isr']);
	unset($_SESSION['importe_ret_isr']);
	unset($_SESSION['porciento_ret_iva']);
	unset($_SESSION['importe_ret_iva']);
	unset($_SESSION['importe_total']);
	//unset($_SESSION['codgastos']);
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_compania");
	
	// Asignar fecha de movimiento
	$tpl->assign("fecha",date("d/m/Y",mktime(0,0,0,date("m"),date("d")-1,date("Y"))));
	
	if (isset($_SESSION['num_proveedor']))
		$tpl->assign("num_proveedor",$_SESSION['num_proveedor']);
	if (isset($_SESSION['fecha_mov']))
		$tpl->assign("fecha",$_SESSION['fecha_mov']);
	
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
	
	// Imprimir el resultado
	$tpl->printToScreen();
	die;
}

if (!ejecutar_script("SELECT num_cia FROM catalogo_companias WHERE num_cia = $_GET[num_cia] AND num_cia BETWEEN ".(in_array($_SESSION['iduser'], $users) ? '900 AND 950' : '1 AND 800'),$dsn)) {
	header("location: ./fac_fpv_cap.php?codigo_error=1");
	die;
}
if (!existe_registro("catalogo_proveedores",array("num_proveedor"),array($_GET['num_proveedor']),$dsn)) {
	header("location: ./fac_fpv_cap.php?codigo_error=2");
	die;
}

// Crear bloque de captura
$tpl->newBlock("captura");

// Seleccionar tabla
$tpl->assign("tabla",$session->tabla);

// Obtener datos del la compañía y del proveedor
$cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]",$dsn);
$pro = ejecutar_script("SELECT nombre,diascredito FROM catalogo_proveedores WHERE num_proveedor = $_GET[num_proveedor]",$dsn);

// Calcular fecha de vencimiento
if (isset($_GET['fecha']))
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})",$_GET['fecha'], $fecha);
else
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})",$_SESSION['fecha_mov'], $fecha);
$fecha1 = $fecha[0];
$fecha2 = date("d/m/Y",mktime(0,0,0,$fecha[2],$fecha[1]+$pro[0]['diascredito'],$fecha[3]));

$tpl->assign("num_cia",$_GET['num_cia']);
$tpl->assign("nombre_corto",$cia[0]['nombre_corto']);
$tpl->assign("num_proveedor",$_GET['num_proveedor']);
$tpl->assign("nombre",$pro[0]['nombre']);
$tpl->assign("fecha_mov",$fecha1);
$tpl->assign("fecha_ven",$fecha2);

if (isset($_SESSION['concepto']))
	$tpl->assign("concepto",$_SESSION['concepto']);

if (isset($_SESSION['codgastos']))
	$tpl->assign("codgastos",$_SESSION['codgastos']);

if (isset($_SESSION['num_fact']))
	$tpl->assign("num_fact",$_SESSION['num_fact']);

if (isset($_SESSION['tipo_factura']))
	$tpl->assign("$_SESSION[tipo_factura]","selected");
else
	$tpl->assign("0","selected");

if (isset($_SESSION['imp_sin_iva']))
	$tpl->assign("imp_sin_iva",number_format($_SESSION['imp_sin_iva'],2,".",""));
else
	$tpl->assign("imp_sin_iva","0.00");

if (isset($_SESSION['porciento_iva']))
	$tpl->assign("porciento_iva",number_format($_SESSION['porciento_iva'],2,".",""));
else
	$tpl->assign("porciento_iva","15.00");

if (isset($_SESSION['importe_iva']))
	$tpl->assign("importe_iva",number_format($_SESSION['importe_iva'],2,".",""));
else
	$tpl->assign("importe_iva","0.00");

if (isset($_SESSION['porciento_ret_isr']))
	$tpl->assign("porciento_ret_isr",number_format($_SESSION['porciento_ret_isr'],2,".",""));
else
	$tpl->assign("porciento_ret_isr","0.00");

if (isset($_SESSION['importe_ret_isr']))
	$tpl->assign("importe_ret_isr",number_format($_SESSION['importe_ret_isr'],2,".",""));
else
	$tpl->assign("importe_ret_isr","0.00");

if (isset($_SESSION['porciento_ret_iva']))
	$tpl->assign("porciento_ret_iva",number_format($_SESSION['porciento_ret_iva'],2,".",""));
else
	$tpl->assign("porciento_ret_iva","0.00");

if (isset($_SESSION['importe_ret_iva']))
	$tpl->assign("importe_ret_iva",number_format($_SESSION['importe_ret_iva'],2,".",""));
else
	$tpl->assign("importe_ret_iva","0.00");

if (isset($_SESSION['importe_total']))
	$tpl->assign("importe_total",number_format($_SESSION['importe_total'],2,".",""));
else
	$tpl->assign("importe_total","0.00");

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
?>