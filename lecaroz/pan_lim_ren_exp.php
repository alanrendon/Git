<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$numfilas = 10;

// Insertar datos
if (isset($_POST['num_cia'])) {
	$data = array();
	$cont = 0;
	for ($i = 0; $i < $numfilas; $i++)
		if ($_POST['num_cia'][$i] > 0 && strlen(trim($_POST['nombre'][$i])) > 0 && get_val($_POST['importe'][$i])) {
			$data[$cont]['num_cia'] = $_POST['num_cia'][$i];
			$data[$cont]['nombre'] = strtoupper(trim($_POST['nombre'][$i]));
			$data[$cont]['importe'] = get_val($_POST['importe'][$i]);
			$data[$cont]['status'] = 1;
			$cont++;
		}
	
	if ($cont > 0) $db->query($db->multiple_insert('catalogo_renta_exp', $data));
	die(header('location: ./pan_lim_ren_exp.php'));
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/pan/pan_lim_ren_exp.tpl");
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
}

$sql = "SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias LEFT JOIN catalogo_operadoras USING (idoperadora)";
$sql .= !in_array($_SESSION['iduser'], array(1, 4, 18, 19)) ? " WHERE iduser = $_SESSION[iduser]" : '';
$sql .= " ORDER BY num_cia";
$result = $db->query($sql);
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre']);
}

$tpl->printToScreen();
?>