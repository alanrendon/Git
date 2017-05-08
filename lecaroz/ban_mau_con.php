<?php
// LISTADO DE MOVIMIENTOS AUTORIZADOS
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
$descripcion_error[1] = "No hay registros";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_mau_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Obtener listado de movimientos autorizados
$cod_mov = ejecutar_script("SELECT cod_mov,descripcion,importe FROM catalogo_mov_autorizados JOIN catalogo_mov_bancos USING(cod_mov) GROUP BY  importe,descripcion,cod_mov ORDER BY cod_mov ASC",$dsn);

if ($cod_mov) {
	for ($i=0; $i<count($cod_mov); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("cod_mov",$cod_mov[$i]['cod_mov']);
		$tpl->assign("descripcion",$cod_mov[$i]['descripcion']);
		$tpl->assign("importe",number_format($cod_mov[$i]['importe'],2,".",","));
	}
}
else {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[1]);
}

$tpl->printToScreen();
?>