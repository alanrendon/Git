<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_tra_baj_fic.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['semana'])) {
	$sql = 'SELECT num_cia, nombre, nombre_corto FROM catalogo_companias WHERE (num_cia <= 300 OR num_cia IN (702, 800))';
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= ' ORDER BY num_cia';
	$result = $db->query($sql);
	
	$fecha = date('d/m/Y');
	
	$tpl->newBlock('styles');
	$fichas = 0;
	foreach ($result as $reg) {
		$tpl->newBlock('ficha');
		$tpl->assign('semana', $_GET['semana']);
		$tpl->assign('anio', $_GET['anio']);
		$tpl->assign('fecha', $fecha);
		$tpl->assign('nombre', $reg['nombre']);
		$tpl->assign('nombre_corto', $reg['nombre_corto']);
		$fichas++;
		
//		if ($fichas % 2 == 0)
//			$tpl->assign('salto', '<br style="page-break-after:always;" />');
	}
	
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');

$tpl->printToScreen();
?>