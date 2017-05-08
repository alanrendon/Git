<?php
// ACTUALIZACION DE INVENTARIOS
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
$descripcion_error[2] = "Ha caducado la fecha de actualización";
$descripcion_error[3] = "No hay un inventario capturado para la compañía";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ros/ros_act_inv.tpl");
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
else if (isset($_GET['num_cia']) && existe_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),$dsn)) {
	if (date("d") > 5) {
		header("location: ./ros_act_inv.php?codigo_error=2");
		die;
	}
	
	$sql =  "SELECT codmp,nombre,existencia,inventario,diferencia FROM inventario_fin_mes JOIN catalogo_mat_primas USING(codmp) ";
	$sql .= "WHERE num_cia=$_GET[num_cia] AND fecha>='".date("d/m/Y",mktime(0,0,0,date("m")-1,1,date("Y")))."'";
	if ($_GET['tipo'] == "controlada")
		$sql .= " AND controlada='TRUE'";
	else
		$sql .= " AND controlada='FALSE'";
	$sql .= " ORDER BY codmp ASC";
	$result = ejecutar_script($sql,$dsn);
	
	if (!$result) {
		header("location: ./ros_act_inv.php?codigo_error=3");
		die;
	}
	
	$tpl->newBlock("listado");
	$cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia=$_GET[num_cia]",$dsn);
	$tpl->assign("num_cia",$_GET['num_cia']);
	$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
	switch (date("n")-1) {
		case 1: $mes = "ENERO"; break;
		case 2: $mes = "FEBRERO"; break;
		case 3: $mes = "MARZO"; break;
		case 4: $mes = "ABRIL"; break;
		case 5: $mes = "MAYO"; break;
		case 6: $mes = "JUNIO"; break;
		case 7: $mes = "JULIO"; break;
		case 8: $mes = "AGOSTO"; break;
		case 9: $mes = "SEPTIEMBRE"; break;
		case 10: $mes = "OCTUBRE"; break;
		case 11: $mes = "NOVIEMBRE"; break;
		case 12: $mes = "DICIEMBRE"; break;
	}
	$tpl->assign("mes",$mes);
	
	for ($i=0; $i<count($result); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("codmp",$result[$i]['codmp']);
		$tpl->assign("mp",$result[$i]['nombre']);
		$tpl->assign("existencia",$result[$i]['existencia']);
		$tpl->assign("inventario",$result[$i]['inventario']);
		$tpl->assign("falta",($result[$i]['inventario']-$result[$i]['existencia'] < 0)?abs($result[$i]['inventario']-$result[$i]['existencia']):"&nbsp;");
		$tpl->assign("sobra",($result[$i]['inventario']-$result[$i]['existencia'] > 0)?abs($result[$i]['inventario']-$result[$i]['existencia']):"&nbsp;");
	}
	$tpl->printToScreen();
	die;
}
else {
	header("location: ./ros_act_inv.php?codigo_error=1");
	die;
}
?>