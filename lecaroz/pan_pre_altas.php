<?php
// ALTA DE PRESTAMOS (PANADERIAS)
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

// --------------------------------- Delaracion de variables -------------------------------------------------
$numfilas = 10;	// Número de filas en la captura

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_pre_altas.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['tabla'])) {
	// Almacenar valores temporalmente
	$_SESSION['total'] = $_POST['total'];
	for ($i=0; $i<$numfilas; $i++) {
		$_SESSION['id_empleado'.$i] = $_POST['id_empleado'.$i];
		$_SESSION['num_emp'.$i] = $_POST['num_emp'.$i];
		$_SESSION['importe'.$i] = $_POST['importe'.$i];
	}
	
	$count = 0;
	for ($i=0; $i<$numfilas; $i++) {
		if ($_POST['num_emp'.$i] > 0 && $_POST['importe'.$i] > 0) {
			$pre['id_empleado'.$count]  = $_POST['id_empleado'.$i];
			$pre['num_cia'.$count]  = $_POST['num_cia'];
			$pre['fecha'.$count]    = $_POST['fecha'];
			$pre['importe'.$count]  = $_POST['importe'.$i];
			$pre['tipo_mov'.$count] = "FALSE";
			$pre['pagado'.$count]   = "FALSE";
			
			$sql = "SELECT nombre,ap_paterno,ap_materno FROM catalogo_trabajadores WHERE id = {$_POST['id_empleado'.$i]}";
			$nombre = ejecutar_script($sql,$dsn);
			$gas['codgastos'.$count] = 41;
			$gas['num_cia'.$count]   = $_POST['num_cia'];
			$gas['fecha'.$count]     = $_POST['fecha'];
			$gas['importe'.$count]   = $_POST['importe'.$i];
			$gas['concepto'.$count]  = "PRESTAMO EMPLEADO NO. {$_POST['id_empleado'.$i]} {$nombre[0]['nombre']} {$nombre[0]['ap_paterno']} {$nombre[0]['ap_materno']}";
			$gas['captura'.$count]   = "FALSE";
			$count++;
			
			if ($id = ejecutar_script("SELECT id FROM prestamos WHERE num_cia=$_POST[num_cia] AND id_empleado=".$_POST['id_empleado'.$i]." AND tipo_mov='FALSE' AND pagado='FALSE'",$dsn))
				$sql = "UPDATE prestamos SET importe=importe+".$_POST['importe'.$i].",fecha='$_POST[fecha]' WHERE id=".$id[0]['id'];
			else
				$sql = "INSERT INTO prestamos (id_empleado,num_cia,fecha,importe,tipo_mov,pagado) VALUES (".$_POST['id_empleado'.$i].",$_POST[num_cia],'$_POST[fecha]',".$_POST['importe'.$i].",'FALSE','FALSE')";
			
			ejecutar_script($sql,$dsn);
			
			// Actualizar efectivos
			if ($id = ejecutar_script("SELECT " . ($_POST['num_cia'] <= 300 ? 'id' : 'idtotal_rosticeria AS id') . " FROM " . ($_POST['num_cia'] <= 300 ? 'total_panaderias' : 'total_companias') . " WHERE num_cia=$_POST[num_cia] AND fecha='$_POST[fecha]'",$dsn))
				$sql = "UPDATE " . ($_POST['num_cia'] <= 300 ? 'total_panaderias' : 'total_companias') . " SET gastos=gastos+".$_POST['importe'.$i].",efectivo=efectivo-".$_POST['importe'.$i]." WHERE " . ($_POST['num_cia'] <= 300 ? 'id' : 'idtotal_rosticeria') . "=".$id[0]['id'];
			else {
				if ($_POST['num_cia'] <= 300)
					$sql = "INSERT INTO total_panaderias (num_cia,fecha,venta_puerta,pastillaje,otros,abono,gastos,raya_pagada,venta_pastel,abono_pastel,efectivo,efe,exp,gas,pro,pas) VALUES (
				$_POST[num_cia],'$_POST[fecha]',0,0,0,0,".$_POST['importe'.$i].",0,0,0,-".$_POST['importe'.$i].",'FALSE','FALSE','FALSE','FALSE','FALSE')";
				else
					$sql = "INSERT INTO total_companias (num_cia, fecha, venta, gastos, efectivo) VALUES ($_POST[num_cia], '$_POST[fecha]', 0, {$_POST['importe' . $i]}, -{$_POST['importe' . $i]})";
			}
			ejecutar_script($sql,$dsn);
		}
	}
	
	$db_gas = new DBclass($dsn,"movimiento_gastos",$gas);
	$db_gas->xinsertar();
}

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("cia");
	$tpl->assign("fecha",date("d/m/Y"));
	
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
	die;
}

$cia = ejecutar_script("SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]",$dsn);
$emp = ejecutar_script("SELECT id, num_emp, ap_paterno, ap_materno, ct.nombre FROM catalogo_trabajadores ct LEFT JOIN catalogo_companias cc USING (num_cia) WHERE (num_cia = $_GET[num_cia] OR num_cia_emp = $_GET[num_cia]) AND (fecha_baja IS NULL OR id IN (SELECT id_empleado FROM prestamos WHERE num_cia = $_GET[num_cia] AND pagado = 'FALSE' GROUP BY id_empleado)) ORDER BY num_emp",$dsn);

if (!$emp) {
	header("location: ./pan_pre_altas.php?codigo_error=-1");
	die;
}

$tpl->newBlock("prestamos");
$tpl->assign("num_cia",$_GET['num_cia']);
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
$tpl->assign("fecha",$_GET['fecha']);

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
for ($i=0; $i<$numfilas; $i++) {
	$tpl->newBlock("fila");
	
	$tpl->assign("i",$i);
	if ($i < $numfilas-1)
		$tpl->assign("next",$i+1);
	else
		$tpl->assign("next",0);
	if ($i > 0)
		$tpl->assign("back",$i-1);
	else
		$tpl->assign("back",$numfilas-1);
	
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
	if ($_GET['codigo_error'] > 0)
		$tpl->assign("message","El empleado no. $_GET[codigo_error] ya tiene un prestamo");
	else
		$tpl->assign("message","No hay empleados para la compañía");
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
?>