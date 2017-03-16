<?php
// MODIFICACIÓN RÁPIDA DE PRECIOS PROMEDIO DE INVENTARIO FIN MES
// Tablas 'inventario_fin_mes'
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
$tpl->assignInclude("body","./plantillas/bal/bal_ifm_minimod.tpl");
$tpl->prepare();

if (isset($_GET['tabla'])) {
	$num_cia = ejecutar_script("SELECT num_cia FROM inventario_fin_mes WHERE id = $_POST[id]",$dsn);
	
	// Almacenar valores temporalmente
	ejecutar_script("UPDATE inventario_fin_mes SET inventario=$_POST[inventario],diferencia=existencia-$_POST[inventario] WHERE id=$_POST[id]",$dsn);
	
	$tpl->newBlock("cerrar");
	$tpl->assign("tipo",$_POST['tipo']);
	$tpl->assign("rango",$_POST['rango']);
	$tpl->assign("num_cia",$num_cia[0]['num_cia']);
	
	$tpl->printToScreen();
	die;
}

// Generar pantalla de captura
$result = ejecutar_script("SELECT id,num_cia,catalogo_companias.nombre_corto AS nombre_cia,codmp,nombre,existencia,inventario FROM inventario_fin_mes JOIN catalogo_mat_primas USING(codmp) WHERE id=$_GET[id] AND catalogo_companias.num_cia=num_cia",$dsn);

$tpl->newBlock("modificar");
$tpl->assign("tabla","inventario_fin_mes");

$tpl->assign("tipo",$_GET['tipo']);

$tpl->assign("id",$_GET['id']);
$tpl->assign("rango",$_GET['rango']);
$tpl->assign("num_cia",$result[0]['num_cia']);
$tpl->assign("nombre_cia",$result[0]['nombre_cia']);
$tpl->assign("cod",$result[0]['codmp']);
$tpl->assign("mp",$result[0]['nombre']);
$tpl->assign("existencia",$result[0]['existencia']);
$tpl->assign("inventario",$result[0]['inventario']);

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
?>