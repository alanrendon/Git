<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

if ($_SESSION['iduser'] != 1) die('MODIFICANDO');

$descripcion_error[1] = "";

$numfilas = 10;

if (isset($_POST['local'])) {
	$sql = '';
	for ($i = 0; $i < $numfilas; $i++)
		if (get_val($_POST['local'][$i]) > 0 && get_val($_POST['anio'][$i]) > 0)
			$sql .= "INSERT INTO estatus_locales (local, mes, anio, tipo, iduser) VALUES ({$_POST['local'][$i]}, {$_POST['mes'][$i]}, {$_POST['anio'][$i]}, {$_POST['tipo'][$i]}, $_SESSION[iduser]);\n";
	
	if (trim($sql) != '') $db->query($sql);
	die(header('location: ./ban_est_loc.php'));
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_est_loc.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock('fila');
	$tpl->assign('i', $i);
	$tpl->assign('back', $i > 0 ? $i - 1 : $numfilas - 1);
	$tpl->assign('next', $i < $numfilas - 1 ? $i + 1 : 0);
	$tpl->assign(date('n'), ' selected');
	$tpl->assign('anio', date('Y'));
}

$result = $db->query('SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias ORDER BY num_cia');
foreach ($result as $reg) {
	$tpl->newBlock('cia');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre']);
}

$sql = "SELECT cod_arrendador AS arr, id, num_local AS local, nombre_local, nombre_arrendatario AS nombre_arr, renta_con_recibo AS renta, agua, mantenimiento, retencion_isr, retencion_iva FROM catalogo_arrendatarios WHERE status = 1";
$sql .= " ORDER BY cod_arrendador, nombre_local";
$result = $db->query($sql);
$arr = NULL;
foreach ($result as $reg) {
	if ($arr != $reg['arr']) {
		if ($arr != NULL)
			$tpl->assign('arr.locales', $locales);
		
		$arr = $reg['arr'];
		
		$tpl->newBlock('arr');
		$tpl->assign('arr', $arr);
		
		$locales = '';
	}
	if ($locales != '')
		$locales .= ', ';
	$locales .= "$reg[id], '$reg[local] $reg[nombre_local]', '$reg[nombre_arr]'";
}
if ($arr != NULL)
	$tpl->assign('arr.locales', $locales);

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>