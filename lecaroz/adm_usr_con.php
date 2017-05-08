<?php
// ADMINISTRACION DE USUARIOS DE SISTEMA
// Tablas 'auth'
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
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Delaracion de variables -------------------------------------------------
$numfilas = 10;	// Número de filas en la captura

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/adm/adm_usr_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$sql = "SELECT iduser,username,nombre,apellido,authlevel,descripcion FROM auth JOIN authlevels USING (authlevel) WHERE authlevel > 0 ORDER BY iduser";
$result = ejecutar_script($sql,$dsn);

if ($result) {
	for ($i=0; $i<count($result); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("id",$result[$i]['iduser']);
		$tpl->assign("username",$result[$i]['username']);
		$tpl->assign("nombre","{$result[$i]['nombre']} {$result[$i]['apellido']}");
		//$tpl->assign("level",$result[$i]['descripcion']);
	}
	
	$tpl->printToScreen();
}

?>