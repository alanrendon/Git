<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_maq_alta.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['num_maquina'])) {
	if ($db->query("SELECT id FROM maquinaria WHERE num_maquina = $_POST[num_maquina] AND status = 1")) {
		$tpl->newBlock("validar");
		$tpl->printToScreen();
		die;
	}
	
	$data = $_POST;
	$data['marca'] = trim(strtoupper($data['marca']));
	$data['descripcion'] = trim(strtoupper($data['descripcion']));
	$data['num_serie'] = trim(strtoupper($data['num_serie']));
	$data['status'] = 1;
	
	$db->query($db->preparar_insert("maquinaria", $data));
	
	$tpl->newBlock("redir");
	$tpl->printToScreen();
	die;
}

$tmp = $db->query("SELECT num_maquina FROM maquinaria WHERE status = 1 ORDER BY num_maquina");
function buscarNum() {
	global $tmp;
	
	if (!$tmp)
		return 1;
	
	$cont = 1;
	foreach ($tmp as $reg)
		if ($reg['num_maquina'] == $cont)
			$cont++;
		else
			return $cont;
	
	return $cont;
}

$num = buscarNum();

$tpl->newBlock("datos");
$tpl->assign("num_maquina", $num);

$cias = $db->query("SELECT num_cia, nombre_corto FROM catalogo_companias ORDER BY num_cia");
foreach ($cias as $cia) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia['num_cia']);
	$tpl->assign("nombre", $cia['nombre_corto']);
}

$turnos = $db->query("SELECT cod_turno, descripcion FROM catalogo_turnos WHERE cod_turno NOT IN (5, 6, 7, 10) ORDER BY cod_turno");
foreach ($turnos as $turno) {
	$tpl->newBlock("turno");
	$tpl->assign("cod", $turno['cod_turno']);
	$tpl->assign("turno", $turno['descripcion']);
}

$tpl->printToScreen();
?>