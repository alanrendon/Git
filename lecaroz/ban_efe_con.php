<?php
// IMPRESIΣN DE LISTADOS DE EFECTIVOS
// Tabla 'varias'
// Menu ''

//define ('IDSCREEN',1221); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$users = array(1, 4, 28, 48, 10);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener informaciσn de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "La compaρνa no existe en la Base de Datos";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_efe_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

/*$tpl->assign(date("n"),"selected");
$tpl->assign("anio",date("Y"));*/
$tpl->assign("fecha", date("d/m/Y", mktime(0, 0, 0, date('n'), date('d') - 2, date('Y'))));

if (in_array($_SESSION['iduser'], $users)) {
	$tpl->newBlock('options');
	
	if (in_array($_SESSION['iduser'], array(1, 4, 28, 48, 55))) {
		$tpl->newBlock("bloque_admin");
		$admins = $db->query('SELECT idadministrador AS id, nombre_administrador AS nombre FROM catalogo_administradores ORDER BY nombre_administrador');
		foreach ($admins as $a) {
			$tpl->newBlock("admin");
			$tpl->assign("id", $a['id']);
			$tpl->assign("nombre", $a['nombre']);
		}
	}
	
	if (in_array($_SESSION['iduser'], array(1, 4, 28, 48))) {
		$tpl->newBlock("bloque_opc");
		$tpl->newBlock("bloque_cias");
	}
}
else if ($_SESSION['iduser'] == 55) {
	if (in_array($_SESSION['iduser'], array(1, 4, 48, 55))) {
		$tpl->newBlock("bloque_admin");
		$admins = $db->query('SELECT idadministrador AS id, nombre_administrador AS nombre FROM catalogo_administradores ORDER BY nombre_administrador');
		foreach ($admins as $a) {
			$tpl->newBlock("admin");
			$tpl->assign("id", $a['id']);
			$tpl->assign("nombre", $a['nombre']);
		}
	}
	
	$tpl->newBlock('only_admin');
}

$tpl->printToScreen();
?>