<?php
// MODIFICACIÓN DE MOVIMIENTOS AUTORIZADOS
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

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_mau_minimod.tpl");
$tpl->prepare();

// --------------------------------- Modificar registro en la tabla -------------------------------------------
if (isset($_GET['tabla'])) {
	ejecutar_script("UPDATE $_GET[tabla] SET cod_mov=$_POST[cod_mov],importe=$_POST[importe] WHERE id=$_POST[id]",$dsn);
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

// Generar pantalla de modificación
$tpl->newBlock("modificar");

// Asignar tabla de insercion
$tpl->assign("tabla","catalogo_mov_autorizados");

// Asignar ID
$tpl->assign("id",$_GET['id']);

// Obtener datos del registro
$result = ejecutar_script("SELECT * FROM catalogo_mov_autorizados WHERE id = $_GET[id]",$dsn);

$tpl->assign("importe",number_format($result[0]['importe'],2,".",""));

// Obtener listado de códigos
$cod_mov = ejecutar_script("SELECT cod_mov,descripcion FROM catalogo_mov_bancos GROUP BY cod_mov,descripcion ORDER BY cod_mov",$dsn);
for ($i=0; $i<count($cod_mov); $i++) {
	$tpl->newBlock("cod_mov");
	$tpl->assign("id",$cod_mov[$i]['cod_mov']);
	$tpl->assign("nombre",$cod_mov[$i]['descripcion']);
	
	if ($cod_mov[$i]['cod_mov'] == $result[0]['cod_mov'])
		$tpl->assign("selected","selected");
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>