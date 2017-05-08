<?php
// PAGO DE PRESTAMOS (PANADERIAS)
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
$descripcion_error[1] = "No hay prestamos para la compañía";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_pre_pago.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['tabla'])) {
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		if ($_POST['importe'.$i] > 0) {
			$datos['id_empleado'] = $_POST['id_empleado'.$i];
			$datos['num_cia'] = $_POST['num_cia'];
			$datos['fecha'] = $_POST['fecha'];
			$datos['importe'] = $_POST['importe'.$i];
			$datos['tipo_mov'] = "TRUE";
			$datos['pagado'] = "TRUE";
			ejecutar_script("INSERT INTO prestamos (id_empleado,num_cia,fecha,importe,tipo_mov,pagado) VALUES (".$_POST['id_empleado'.$i].",$_POST[num_cia],'$_POST[fecha]',".$_POST['importe'.$i].",'TRUE','FALSE')",$dsn);
			
			if ($_POST['falta'.$i] == 0) {
				ejecutar_script("UPDATE prestamos SET pagado = 'TRUE' WHERE id_empleado = ".$_POST['id_empleado'.$i]." AND pagado = 'FALSE'",$dsn);
			}
			
			// Actualizar efectivo
			if ($id = ejecutar_script("SELECT " . ($_POST['num_cia'] <= 300 ? 'id' : 'idtotal_rosticeria AS id') . " FROM " . ($_POST['num_cia'] <= 300 ? 'total_panaderias' : 'total_companias') . " WHERE num_cia=$_POST[num_cia] AND fecha='$_POST[fecha]'",$dsn))
				$sql = "UPDATE " . ($_POST['num_cia'] <= 300 ? 'total_panaderias' : 'total_companias') . " SET efectivo=efectivo+".$_POST['importe'.$i]."," . ($_POST['num_cia'] <= 300 ? 'otros=otros' : 'venta=venta') . "+".$_POST['importe'.$i]." WHERE " . ($_POST['num_cia'] <= 300 ? 'id' : 'idtotal_rosticeria') . " = ".$id[0]['id'];
			else {
				if ($_POST['num_cia'] <= 300)
					$sql = "INSERT INTO total_panaderias (num_cia,fecha,venta_puerta,pastillaje,otros,abono,gastos,raya_pagada,venta_pastel,abono_pastel,efectivo,efe,exp,gas,pro,pas) VALUES (
				$_POST[num_cia],'$_POST[fecha]',0,0,".$_POST['importe'.$i].",0,0,0,0,0,".$_POST['importe'.$i].",'FALSE','FALSE','FALSE','FALSE','FALSE')";
				else
					$sql = "INSERT INTO total_companias (num_cia, fecha, venta, gastos, efectivo) VALUES ($_POST[num_cia], '$_POST[fecha]', {$_POST['importe' . $i]}, 0, {$_POST['importe' . $i]})";
			}
			ejecutar_script($sql,$dsn);
		}
	}
}

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("cia");
	$tpl->assign("fecha",date("d/m/Y"));
	
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
	die;
}

$sql = "SELECT id_empleado,nombre,ap_paterno,ap_materno,importe FROM prestamos JOIN catalogo_trabajadores ON(prestamos.id_empleado=catalogo_trabajadores.id) WHERE prestamos.num_cia = $_GET[num_cia] AND tipo_mov = 'FALSE' AND pagado = 'FALSE' ORDER BY num_emp";
$result = ejecutar_script($sql,$dsn);

if (!$result) {
	header("location: ./pan_pre_pago.php?codigo_error=1");
	die;
}

$tpl->newBlock("prestamos");
$cia = ejecutar_script("SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]",$dsn);
$tpl->assign("num_cia",$_GET['num_cia']);
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
$tpl->assign("fecha",$_GET['fecha']);

$tpl->assign("numfilas",count($result));

for ($i=0; $i<count($result); $i++) {
	$debe = ejecutar_script("SELECT sum(importe) FROM prestamos WHERE id_empleado = ".$result[$i]['id_empleado']." AND pagado = 'FALSE' AND tipo_mov = 'TRUE'",$dsn);
	
	$tpl->newBlock("fila");
	
	$tpl->assign("i",$i);
	if ($i < count($result)-1)
		$tpl->assign("next",$i+1);
	else
		$tpl->assign("next",0);
	if ($i > 0 )
		$tpl->assign("back",$i-1);
	else
		$tpl->assign("back",count($result)-1);
	
	$tpl->assign("id_empleado",$result[$i]['id_empleado']);
	$tpl->assign("nombre_trabajador",$result[$i]['nombre']." ".$result[$i]['ap_paterno']." ".$result[$i]['ap_materno']);
	$tpl->assign("debe",$result[$i]['importe']);
	$tpl->assign("fdebe",number_format($result[$i]['importe'],2,".",","));
	if ($debe)
		$tpl->assign("falta",number_format($result[$i]['importe'] - $debe[0]['sum'],2,".",""));
	else
		$tpl->assign("falta",number_format($result[$i]['importe'],2,".",","));
}
$tpl->printToScreen();
?>