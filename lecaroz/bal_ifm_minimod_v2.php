<?php
// MODIFICACIÓN RÁPIDA DE PRECIOS PROMEDIO DE INVENTARIO FIN MES
// Tablas 'inventario_fin_mes'
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
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

$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_ifm_minimod_v2.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	$num_cia = $db->query("SELECT num_cia FROM inventario_fin_mes WHERE id = $_POST[id]");
	
	// Almacenar valores temporalmente
	$db->query("UPDATE inventario_fin_mes SET inventario=$_POST[inventario],diferencia=existencia-$_POST[inventario] WHERE id=$_POST[id]");
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	
	$tpl->desconectar();
	die;
}

// Generar pantalla de captura
$result = $db->query("SELECT id,num_cia,codmp,nombre,existencia,inventario FROM inventario_fin_mes JOIN catalogo_mat_primas USING(codmp) WHERE id=$_GET[id]");

$tpl->newBlock("modificar");
$tpl->assign("tabla","inventario_fin_mes");

$tpl->assign("id",$_GET['id']);
$tpl->assign("num_cia",$result[0]['num_cia']);
$nombre_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = {$result[0]['num_cia']}");
$tpl->assign("nombre_cia",$nombre_cia[0]['nombre_corto']);
$tpl->assign("cod",$result[0]['codmp']);
$tpl->assign("mp",$result[0]['nombre']);
$tpl->assign("existencia",number_format($result[0]['existencia'],2,".",""));
$tpl->assign("inventario",number_format($result[0]['inventario'],2,".",""));

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
$db->desconectar();
?>