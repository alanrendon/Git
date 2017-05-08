<?php
// CAPTURA Y RESERVA DE FOLIOS DE CHEQUES
// Tablas 'folios_cheque'
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
$descripcion_error[1] = "Número de compañía no se encuentra en la Base de Datos";

$users = array(28, 29, 30, 31);

// --------------------------------- Insertar datos a la base ------------------------------------------------
if (isset($_GET['mov'])) {
	if ($_GET['mov'] == "alta") {
		$datos['folio']     = $_POST['folio'];
		$datos['num_cia']   = $_POST['num_cia'];
		$datos['reservado'] = "FALSE";
		$datos['utilizado'] = "TRUE";
		$datos['fecha'] = date("d/m/Y");
		$datos['cuenta'] = $_POST['cuenta'];
		
		$db = new DBclass($dsn,"folios_cheque",$datos);
		$db->generar_script_insert("");
		$db->ejecutar_script();
	}
	else if ($_GET['mov'] == "reserva") {
		$folio = $_POST['ultimo_folio'] + 1;
		for ($i=0; $i<$_POST['num_folios']; $i++) {
			$datos['folio'.$i]     = $folio;
			$datos['num_cia'.$i]   = $_POST['num_cia'];
			$datos['reservado'.$i] = "TRUE";
			$datos['utilizado'.$i] = "FALSE";
			$datos['fecha'.$i] = date("d/m/Y");
			$datos['cuenta'.$i] = $_POST['cuenta'];
			$folio++;
		}
		$db = new DBclass($dsn,"folios_cheque",$datos);
		$db->xinsertar();
	}
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_fol_cap.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	
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

if (!ejecutar_script("SELECT num_cia FROM catalogo_companias WHERE num_cia = $_GET[num_cia] AND num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? "900 AND 950" : "1 AND 800"),$dsn)) {
//if (!existe_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),$dsn)) {
	header("location: ./ban_fol_cap.php?codigo_error=1");
	die;
}

if (!existe_registro("folios_cheque",array("num_cia"),array($_GET['num_cia']),$dsn)) {
	$tpl->newBlock("inicial");
	$tpl->assign("num_cia",$_GET['num_cia']);
	$cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia=$_GET[num_cia]",$dsn);
	$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
	$tpl->printToScreen();
	die;
}
else {
	$tpl->newBlock("reservar");
	$tpl->assign("num_cia",$_GET['num_cia']);
	$cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia=$_GET[num_cia]",$dsn);
	$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
	
	$id = ejecutar_script("SELECT folio FROM folios_cheque WHERE num_cia=$_GET[num_cia] ORDER BY folio DESC LIMIT 1",$dsn);
	$tpl->assign("ultimo_folio",$id[0]['folio']);
	$tpl->printToScreen();
	die;
}
?>