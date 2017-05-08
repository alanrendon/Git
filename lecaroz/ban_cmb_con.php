<?php
// LISTADO DE GASTOS DE CAJA
// Tabla 'catalogo_gastos_caja'
// Menu 'Balance->Catálogos Especiales'

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

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_cmb_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Tipo de listado -------------------------------------------------------
if (!isset($_GET['tipo'])) {
	$tpl->newBlock("tipo_listado");
	
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
	die();
}

if ($_GET['tipo'] == "nombre")
	$result = obtener_registro($_GET['tabla'],array(),array(),"descripcion","ASC",$dsn);
else if ($_GET['tipo'] == "codigo")
	$result = obtener_registro($_GET['tabla'],array(),array(),"cod_mov","ASC",$dsn);

$tpl->newBlock("listado");
$cod_ant = NULL;
for ($i=0; $i<count($result); $i++) {
	if ($cod_ant != $result[$i]['cod_mov']) {
		$cod_ant = $result[$i]['cod_mov'];
		$tpl->newBlock("fila");
		$tpl->assign("cod_mov",$result[$i]['cod_mov']);
		$tpl->newBlock("cod_banco");
		$tpl->assign("cod_banco",$result[$i]['cod_banco']);
		$tpl->gotoBlock("fila");
		$tpl->assign("descripcion",$result[$i]['descripcion']);
		if ($result[$i]['tipo_mov'] == "t")
			$tpl->assign("tipo_mov","CARGO");
		else
			$tpl->assign("tipo_mov","ABONO");
		if ($result[$i]['entra_bal'] == "t")
			$tpl->assign("entra_bal","SI");
		else
			$tpl->assign("entra_bal","NO");
	}
	else {
		$tpl->newBlock("cod_banco");
		$tpl->assign("cod_banco","- ".$result[$i]['cod_banco']);
	}
}

$tpl->printToScreen();
?>