<?php
// DIVISIÓN DE DEPOSITOS EN CONCILIACIÓN AUTOMÁTICA
// Tablas 'depositos'
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

// --------------------------------- Desplegar pantalla ------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_mnc_ded.tpl");
$tpl->prepare();

// --------------------------------- Almacenar datos ---------------------------------------------------------
if (isset($_GET['tabla'])) {
	// Obtener datos del deposito antes de eliminar
	$dep = ejecutar_script("SELECT * FROM estado_cuenta WHERE id = $_POST[id]",$dsn);
	// Borrar registro de deposito anterior
	ejecutar_script("DELETE FROM estado_cuenta WHERE id=$_POST[id]",$dsn);
	
	// Reconstruir datos
	$count = 0;
	$saldo_ini = $dep[0]['saldo_ini'];
	for ($i=1; $i<=5; $i++) {
		if ($_POST['dep'.$i] > 0) {
			$datos['num_cia'.$count]   = $dep[0]['num_cia'];
			$datos['fecha'.$count]     = $dep[0]['fecha'];
			$datos['fecha_con'.$count] = $dep[0]['fecha_con'];
			$datos['concepto'.$count]  = $dep[0]['concepto'];
			$datos['tipo_mov'.$count]  = $dep[0]['tipo_mov'];
			$datos['importe'.$count]   = $_POST['dep'.$i];
			$datos['saldo_ini'.$count] = $saldo_ini;
			$datos['saldo_fin'.$count] = $saldo_ini = $saldo_ini + $_POST['dep'.$i];
			$datos['cod_mov'.$count]   = $dep[0]['cod_mov'];
			$datos['folio'.$count]     = $dep[0]['folio'];
			$datos['cuenta'.$count]    = 1;
			
			$count++;
		}
	}
	$db = new DBclass($dsn,"estado_cuenta",$datos);
	$db->xinsertar();
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

// Generar pantalla de captura
$dep = ejecutar_script("SELECT * FROM estado_cuenta WHERE id = $_GET[id]",$dsn);
$cia = ejecutar_script("SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia = ".$dep[0]['num_cia'],$dsn);

$tpl->newBlock("dividir");
$tpl->assign("id",$_GET['id']);
$tpl->assign("num_cia",$dep[0]['num_cia']);
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
$tpl->assign("fecha_dep",$dep[0]['fecha']);
$tpl->assign("deposito",$dep[0]['importe']);
$tpl->assign("fdeposito",number_format($dep[0]['importe'],2,".",","));
$tpl->assign("tabla","depositos");

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