<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$descripcion_error[1] = "No hay resultados";

// [AJAX] Obtener compañía
if (isset($_GET['c'])) {
	$sql = "SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[c]";
	$result = $db->query($sql);
	
	echo $_GET['i'] . '|' . ($result ? $result[0]['nombre_corto'] : '');
	die;
}

$numfilas = 20;

// Insertar datos
if (isset($_POST['num_cia'])) {
	$sql = '';
	for ($i = 0; $i < $numfilas; $i++)
		if ($_POST['num_cia'][$i] > 0 && ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_POST['fecha'][$i])) {
			$sql .= 'INSERT INTO "VencimientoFacturas" (num_cia, fecha, iduser) VALUES (';
			$sql .= "{$_POST['num_cia'][$i]}, '{$_POST['fecha'][$i]}', $_SESSION[iduser]);\n";
		}
	
	if ($sql != '') $db->query($sql);
	
	die(header('location: ban_ven_fac.php'));
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_ven_fac.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock('fila');
	$tpl->assign('i', $i);
	$tpl->assign('next', $i < $numfilas - 1 ? $i + 1 : 0);
	$tpl->assign('back', $i > 0 ? $i - 1 : $numfilas - 1);
}

$tpl->printToScreen();
?>