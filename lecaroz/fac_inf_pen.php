<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$descripcion_error[1] = "No hay resultados";
$numfilas = 50;

// [AJAX] Obtener empleados con Infonavit
if (isset($_GET['num_cia'])) {
	$sql = "SELECT id, num_emp, ap_paterno, ap_materno, nombre FROM catalogo_trabajadores WHERE num_cia = $_GET[num_cia] AND credito_infonavit = 'TRUE' AND fecha_baja IS NULL AND num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . " ORDER BY num_emp ASC";
	$result = $db->query($sql);
	
	if (!$result) die("$_GET[i]|-1");
	
	$data = "$_GET[i]|";
	foreach ($result as $i => $reg)
		$data .= "$reg[id]/$reg[num_emp]-$reg[ap_paterno] $reg[ap_materno] $reg[nombre]" . ($i < count($result) - 1 ? '|' : '');
	
	die($data);
}

// Insertar datos
if (isset($_POST['num_cia'])) {
	$data = $_POST;
	$sql = "";
	for ($i = 0; $i < $numfilas; $i++)
		if ($data['num_cia'][$i] > 0 && $data['id'][$i] > 0 && $data['anio'][$i] > 0 && $data['mes'][$i] > 0/* && get_val($data['importe'][$i]) > 0*/) {
			$importe = get_val($data['importe'][$i]);
			if ($importe == 0)
				$sql .= "DELETE FROM infonavit_pendientes WHERE id_emp = {$data['id'][$i]} AND anio = {$data['anio'][$i]} AND mes = {$data['mes'][$i]} AND status = 0;\n";
			else if ($id = $db->query("SELECT id FROM infonavit_pendientes WHERE id_emp = {$data['id'][$i]} AND anio = {$data['anio'][$i]} AND mes = {$data['mes'][$i]}"))
				$sql .= "UPDATE infonavit_pendientes SET importe = $importe, iduser = $_SESSION[iduser], tsmov = now() WHERE id = {$id[0]['id']} AND status = 0;\n";
			else
				$sql .= "INSERT INTO infonavit_pendientes (num_cia, id_emp, mes, anio, importe, iduser) VALUES ({$data['num_cia'][$i]}, {$data['id'][$i]}, {$data['mes'][$i]}, {$data['anio'][$i]}, $importe, $_SESSION[iduser]);\n";
		}
	
	if ($sql != '') $db->query($sql);
	
	header('location: ./fac_inf_pen.php');
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_inf_pen.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->newBlock('captura');

$mes = date('n', mktime(0, 0, 0, date('n'), 1, date('Y')));
$anio = date('Y', mktime(0, 0, 0, date('n'), 1, date('Y')));
for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock('fila');
	$tpl->assign('i', $i);
	$tpl->assign('back', $i > 0 ? $i - 1 : $numfilas - 1);
	$tpl->assign('next', $i < $numfilas - 1 ? $i + 1 : 0);
	$tpl->assign($mes, ' selected');
	$tpl->assign('anio', $anio);
}

$result = $db->query('SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . ' ORDER BY num_cia');
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre_corto']);
}

$tpl->printToScreen();
?>