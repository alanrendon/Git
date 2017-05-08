<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_con_max_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$sql = "SELECT num_cia, cc.nombre_corto AS nombre_cia, codmp, cm.nombre AS nombre_mp, frances_dia, frances_noche, bizcochero, repostero, piconero FROM catalogo_avio_autorizado ca LEFT JOIN catalogo_mat_primas cm USING (codmp) LEFT JOIN catalogo_companias cc USING (num_cia) LEFT JOIN catalogo_administradores cd USING (idadministrador)";
	if ($_GET['num_cia'] > 0 || $_GET['idadmin'] > 0 || $_GET['codmp'] > 0) {
		$sql .= " WHERE";
		$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia]" : '';
		$sql .= $_GET['idadmin'] > 0 ? ($_GET['num_cia'] > 0 ? ' AND' : '') . " idadministrador = $_GET[idadmin]" : '';
		$sql .= $_GET['codmp'] > 0 ? ($_GET['num_cia'] > 0 || $_GET['idadmin'] > 0 ? ' AND' : '') . " codmp = $_GET[codmp]" : '';
	}
	$sql .= " ORDER BY codmp, num_cia";
	$result = $db->query($sql);
	
	if (!$result) die('location: ./pan_con_max_con.php?codigo_error=1');
	
	$tpl->newBlock('listado');
	if ($_GET['idadmin'] > 0) {
		$admin = $db->query("SELECT nombre_administrador FROM catalogo_administradores WHERE idadministrador = $_GET[idadmin]");
		$tpl->assign('admin', $admin[0]['nombre_administrador']);
	}
	$tpl->assign('hora', date('d/m/Y H:i'));
	
	$codmp = NULL;
	foreach ($result as $reg) {
		if ($codmp != $reg['codmp']) {
			$codmp = $reg['codmp'];
			
			$tpl->newBlock('pro');
			$tpl->assign('codmp', $codmp);
			$tpl->assign('nombre', $reg['nombre_mp']);
		}
		$tpl->newBlock('fila');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre_cia']);
		$tpl->assign('1', $reg['frances_dia'] != 0 ? number_format($reg['frances_dia'], 2) : '&nbsp;');
		$tpl->assign('2', $reg['frances_noche'] != 0 ? number_format($reg['frances_noche'], 2) : '&nbsp;');
		$tpl->assign('3', $reg['bizcochero'] != 0 ? number_format($reg['bizcochero'], 2) : '&nbsp;');
		$tpl->assign('4', $reg['repostero'] != 0 ? number_format($reg['repostero'], 2) : '&nbsp;');
		$tpl->assign('8', $reg['piconero'] != 0 ? number_format($reg['piconero'], 2) : '&nbsp;');
	}
	die($tpl->printToScreen());
}

$tpl->newBlock("datos");

$result = $db->query('SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores ORDER BY admin');
foreach ($result as $reg) {
	$tpl->newBlock('a');
	$tpl->assign('id', $reg['id']);
	$tpl->assign('admin', $reg['admin']);
}

$result = $db->query('SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN 1 AND 800 ORDER BY num_cia');
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre_corto']);
}

$result = $db->query("SELECT codmp AS cod, nombre FROM catalogo_mat_primas WHERE controlada = 'TRUE' ORDER BY cod");
foreach ($result as $reg) {
	$tpl->newBlock('m');
	$tpl->assign('cod', $reg['cod']);
	$tpl->assign('nombre', $reg['nombre']);
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
?>