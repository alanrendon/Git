<?php
// ALTA DE MOVIMIENTOS AUTORIZADOS
// Tablas 'catalogo_mov_autorizados'
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
$descripcion_error[1] = "Ya registro ya se encuentra en el catálogo";

// --------------------------------- Insertar registro en la tabla -------------------------------------------
if (isset($_GET['tabla'])) {
	if (existe_registro($_GET['tabla'],array("cod_mov","importe"),array($_POST['cod_mov'],$_POST['importe']),$dsn)) {
		header("location: ./ban_mau_altas.php?codigo_error=1");
		die;
	}
	
	$db = new DBclass($dsn,$_GET['tabla'],$_POST);
	$db->generar_script_insert("");
	$db->ejecutar_script();
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_mau_altas.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Asignar tabla de insercion
$tpl->assign("tabla","catalogo_mov_autorizados");

// Obtener listado de códigos
$cod_mov = ejecutar_script("SELECT cod_mov,descripcion FROM catalogo_mov_bancos GROUP BY cod_mov,descripcion ORDER BY cod_mov",$dsn);
for ($i=0; $i<count($cod_mov); $i++) {
	$tpl->newBlock("cod_mov");
	$tpl->assign("id",$cod_mov[$i]['cod_mov']);
	$tpl->assign("nombre",$cod_mov[$i]['descripcion']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>