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
$tpl->assignInclude("body","./plantillas/ban/ban_dep_mod.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Tipo de listado -------------------------------------------------------
if (isset($_GET['tabla'])) {
	ejecutar_script("UPDATE depositos SET fecha_mov='$_POST[fecha_mov]',cod_mov=$_POST[cod_mov] WHERE id=$_POST[id]",$dsn);
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

if (!isset($_GET['tabla'])) {
	$result = ejecutar_script("SELECT * FROM estado_cuenta WHERE id=$_GET[id]",$dsn);
	
	$tpl->newBlock("modificar");
	
	$tpl->assign("tabla","depositos");
	
	$tpl->assign("id",$_GET['id']);
	$tpl->assign("num_cia",$result[0]['num_cia']);
	$cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia=".$result[0]['num_cia'],$dsn);
	$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
	$tpl->assign("fecha_mov",$result[0]['fecha']);
	$tpl->assign("fecha_con",$result[0]['fecha_con']);
	$tpl->assign("importe",number_format($result[0]['importe'],2,".",","));
	$tpl->assign("concepto",$result[0]['concepto']);
	$tpl->assign("cod_mov",$result[0]['cod_mov']);
	$mov = ejecutar_script("SELECT descripcion FROM catalogo_mov_bancos WHERE cod_mov=".$result[0]['cod_mov'],$dsn);
	if ($mov)
		$tpl->assign("nombre_mov",$mov[0]['descripcion']);
	else
		$tpl->assign("nombre_mov","EL CÓDIGO NO ESTA EN EL CATÁLOGO");
	
	// Generar listado de gastos
	$gas = ejecutar_script("SELECT cod_mov,descripcion FROM catalogo_mov_bancos ORDER BY cod_mov ASC",$dsn);
	for ($i=0; $i<count($gas); $i++) {
		$tpl->newBlock("nombre_mov");
		$tpl->assign("cod_mov",$gas[$i]['cod_mov']);
		$tpl->assign("nombre_mov",$gas[$i]['descripcion']);
	}
	
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
?>