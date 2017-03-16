<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ros/ros_emp_altas_v2.tpl");
$tpl->prepare();

if (isset($_POST['nombre'])) {
	$sql = "SELECT num_emp FROM catalogo_trabajadores WHERE fecha_baja IS NULL ORDER BY num_emp DESC LIMIT 1";
	$result = $db->query($sql);
	$num_emp = $result[0]['num_emp'];
	
	$emp = array();
	$cont = 0;
	for ($i = 0; $i < count($_POST['nombre']); $i++)
		if ($_POST['nombre'][$i] != "" && $_POST['ap_paterno'][$i] != "") {
			$emp[$cont]['nombre'] = strtoupper($_POST['nombre'][$i]);
			$emp[$cont]['ap_paterno'] = strtoupper($_POST['ap_paterno'][$i]);
			$emp[$cont]['ap_materno'] = strtoupper($_POST['ap_materno'][$i]);
			$emp[$cont]['num_emp'] = ++$num_emp;
			$emp[$cont]['cod_puestos'] = $_POST['cod_puestos'][$i];
			$emp[$cont]['cod_turno'] = 11;
			$emp[$cont]['num_cia'] = $_SESSION['psr']['num_cia'];
			$cont++;
		}
	
	if ($cont > 0) {
		$db->query($db->multiple_insert("catalogo_trabajadores", $emp));
		
		$sql = "SELECT id, num_emp, nombre, ap_paterno, ap_materno FROM catalogo_trabajadores ORDER BY id DESC LIMIT $cont";
		$result = $db->query($sql);
		
		$tpl->newBlock("update");
		foreach ($result as $i => $reg) {
			$tpl->newBlock("emp");
			$tpl->assign("num_emp", $reg['num_emp']);
			$tpl->assign("i", $i);
			$tpl->assign("id", $reg['id']);
			$tpl->assign("nombre", trim("$reg[nombre] $reg[ap_paterno] $reg[ap_materno]"));
		}
		$tpl->printToScreen();
	}
	else {
		$tpl->newBlock("update");
		$tpl->printToScreen();
	}
	die;
}

$tpl->newBlock("altas");

$numfilas = 5;
for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
}

$tpl->printToScreen();
?>