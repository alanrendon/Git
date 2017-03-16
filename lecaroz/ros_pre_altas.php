<?php
// ALTA DE PRESTAMOS
// Tablas 'prestamos'
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
//$descripcion_error[]

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ros/ros_pre_altas.tpl");
$tpl->prepare();

if (isset($_GET['tabla'])) {
	// Almacenar valores temporalmente
	$_SESSION['total'] = $_POST['total'];
	for ($i=0; $i<5; $i++) {
		$_SESSION['id_empleado'.$i] = $_POST['id_empleado'.$i];
		$_SESSION['num_emp'.$i] = $_POST['num_emp'.$i];
		$_SESSION['importe'.$i] = $_POST['importe'.$i];
	}
	
	$count = 0;
	for ($i=0; $i<5; $i++) {
		if ($_POST['num_emp'.$i] > 0 && $_POST['importe'.$i] > 0) {
			$pre['id_empleado'.$count] = $_POST['id_empleado'.$i];
			$pre['num_emp'.$count]  = $_POST['num_emp'.$i];
			$pre['num_cia'.$count]  = $_POST['num_cia'];
			$pre['fecha'.$count]    = $_POST['fecha'];
			$pre['importe'.$count]  = $_POST['importe'.$i];
			$pre['tipo_mov'.$count] = "FALSE";
			$pre['pagado'.$count]   = "FALSE";
			
			$gas['codgastos'.$count] = 41;
			$gas['num_cia'.$count]   = $_POST['num_cia'];
			$gas['fecha'.$count]     = $_POST['fecha'];
			$gas['importe'.$count]   = $_POST['importe'.$i];
			$gas['concepto'.$count]  = "PRESTAMO EMPLEADO NO. ".$_POST['id_empleado'.$i];
			$count++;
			
			/*if (existe_registro("prestamos",array("num_cia","num_emp","pagado"),array($_POST['num_cia'],$_POST['num_emp'.$i],"FALSE"),$dsn)) {
				header("location: ./ros_pre_altas.php?codigo_error=".$_POST['num_emp'.$i]);
				die;
			}*/
		}
	}
	$db_pre = new DBclass($dsn,$_GET['tabla'],$pre);
	$db_pre->xinsertar();
	
	$db_gas = new DBclass($dsn,"movimiento_gastos",$gas);
	$db_gas->xinsertar();
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

// Almacenar valores temporalmente
if (!isset($_SESSION['g']))
	$_SESSION['g'] = array();

for ($i=0; $i<10; $i++) {
	$_SESSION['g']['codgastos'.$i] = $_POST['codgastos'.$i];
	$_SESSION['g']['concepto'.$i]  = $_POST['concepto'.$i];
	$_SESSION['g']['importe'.$i]   = $_POST['importe'.$i];
}
$_SESSION['g']['total_gastos']    = $_POST['total_gastos'];
$_SESSION['g']['gastos_directos'] = $_POST['gastos_directos'];
$_SESSION['g']['total']           = $_POST['gastos_dia'];

$cia = ejecutar_script("SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia = $_SESSION[num_cia]",$dsn);
$emp = ejecutar_script("SELECT id,num_emp,nombre,ap_paterno,ap_materno FROM catalogo_trabajadores WHERE num_cia = $_SESSION[num_cia]",$dsn);

if (!$emp) {
	$tpl->newBlock("error_reg");
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("prestamos");
$tpl->assign("num_cia",$_SESSION['num_cia']);
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
$tpl->assign("fecha",$_SESSION['fecha']);

if (isset($_GET['codigo_error']))
	$tpl->assign("total",$_SESSION['total']);
else
	$tpl->assign("total","0.00");

// Generar nombres de empleados para la compañía especificada por $_SESSION[num_cia]
for ($i=0; $i<count($emp); $i++) {
	$tpl->newBlock("nombre_emp");
	$tpl->assign("id",$emp[$i]['id']);
	$tpl->assign("num_emp",$emp[$i]['num_emp']);
	$tpl->assign("nombre_emp",$emp[$i]['nombre']." ".$emp[$i]['ap_paterno']." ".$emp[$i]['ap_materno']);
}

// Generar filas
for ($i=0; $i<5; $i++) {
	$tpl->newBlock("fila");
	
	$tpl->assign("i",$i);
	if ($i < 5-1)
		$tpl->assign("next",$i+1);
	else
		$tpl->assign("next",0);
	if ($i > 0)
		$tpl->assign("back",$i-1);
	else
		$tpl->assign("back",5-1);
	
	if (isset($_GET['codigo_error'])) {
		$tpl->assign("id_empleado",$_SESSION['id_empleado'.$i]);
		$tpl->assign("num_emp",$_SESSION['num_emp'.$i]);
		if ($_SESSION['num_emp'.$i] > 0) {
			$temp = ejecutar_script("SELECT nombre,ap_paterno,ap_materno FROM catalogo_trabajadores WHERE id = ".$_SESSION['id_empleado'.$i],$dsn);
			$tpl->assign("nombre_emp",$temp[0]['nombre']." ".$temp[0]['ap_paterno']." ".$temp[0]['ap_materno']);
		}
		$tpl->assign("importe",$_SESSION['importe'.$i]);
	}
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message","El empleado no. $_GET[codigo_error] ya tiene un prestamo");	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
?>