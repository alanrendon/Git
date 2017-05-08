<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_GET['c'])) {
	$reg = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[c] AND num_cia BETWEEN 301 AND 599 OR num_cia IN (702)");
	if (!$reg) die();
	else {
		echo $reg[0]['nombre_corto'];
		die;
	}
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ros/ros_kil_per.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$sql = 'SELECT num_cia, nombre_corto AS nombre, sum(kilos) AS kilos FROM fact_rosticeria f LEFT JOIN catalogo_companias cc USING (num_cia) WHERE';
	$sql .= " fecha_mov BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'";
	$sql .= ' AND codmp IN (';
	if (isset($_GET['codmp']))
		foreach ($_GET['codmp'] as $i => $codmp)
			$sql .= $codmp . ($i < count($_GET['codmp']) - 1 ? ', ' : ')');
	else
		$sql .= '160, 600, 700, 573, 334)';
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '';
	$sql .= $_GET['num_pro'] > 0 ? " AND f.num_proveedor = $_GET[num_pro]" : '';
	$sql .= ' GROUP BY num_cia, nombre_corto ORDER BY num_cia';
	$result = $db->query($sql);

	if (!$result) die(header('location: ./ros_kil_per.php?codigo_error=1'));

	$maxfilas = 58;
	$numfilas = $maxfilas;
	$total = 0;
	foreach ($result as $reg) {
		if ($numfilas >= $maxfilas) {
			$tpl->newBlock('listado');
			$tpl->assign('fecha1', $_GET['fecha1']);
			$tpl->assign('fecha2', $_GET['fecha2']);

			$numfilas = 0;
		}
		$tpl->newBlock('fila');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre']);
		$tpl->assign('kilos', number_format($reg['kilos']));

		$total += $reg['kilos'];
		$numfilas++;
	}
	if (count($result) > 1) {
		$tpl->newBlock('total');
		$tpl->assign('total', number_format($total));
	}
	$tpl->newBlock('back');
	die($tpl->printToScreen());
}

$descripcion_error[1] = "No hay resultados";

$tpl->newBlock("datos");
$tpl->assign('fecha1', date('d/m/Y', mktime(0, 0, 0, date('n'), date('d') - 7, date('Y'))));
$tpl->assign('fecha2', date('d/m/Y', mktime(0, 0, 0, date('n'), date('d'), date('Y'))));

$admins = $db->query('SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores WHERE idadministrador NOT IN (11, 12) ORDER BY admin');
foreach ($admins as $a) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $a['id']);
	$tpl->assign('admin', $a['admin']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message",$descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
die;
?>
