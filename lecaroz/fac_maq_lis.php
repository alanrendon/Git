<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_maq_lis.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign("message", $descripcion_error[$_GET['codigo_error']]);	
	}
	$tpl->printToScreen();
	die;
}

$sql = "SELECT num_maquina, marca, m.descripcion, capacidad, ct.descripcion AS turno, num_serie, num_cia, cc.nombre_corto AS nombre, fecha FROM maquinaria AS m LEFT JOIN";
$sql .= " catalogo_companias AS cc USING (num_cia) LEFT JOIN catalogo_turnos AS ct USING (cod_turno) WHERE m.status = 1";
$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
$sql .= $_GET['num_maquina'] > 0 ? " AND num_maquina = $_GET[num_maquina]" : "";
$sql .= " ORDER BY " . ($_GET['tipo'] == 1 ? "num_maquina" : "num_cia, num_maquina");
$result = $db->query($sql);

if (!$result) {
	header("location: ./fac");
	die;
}

if ($_GET['tipo'] == 1) {
	$tpl->newBlock("listado1");
	foreach ($result as $reg) {
		$tpl->newBlock("fila1");
		foreach ($reg as $tag => $value)
			$tpl->assign($tag, $value != "" ? $value : "&nbsp;");
	}
}
else {
	$num_cia = NULL;
	for ($i = 0; $i < count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			if ($num_cia != NULL)
				$tpl->assign("listado2.salto", "<br style=\"page-break-after:always;\">");
			
			$num_cia = $result[$i]['num_cia'];
			
			$tpl->newBlock("listado2");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre", $result[$i]['nombre']);
		}
		$tpl->newBlock("fila2");
		$tpl->assign("num_maquina", $result[$i]['num_maquina']);
		$tpl->assign("marca", $result[$i]['marca']);
		$tpl->assign("descripcion", $result[$i]['descripcion']);
		$tpl->assign("capacidad", $result[$i]['capacidad']);
		$tpl->assign("num_serie", $result[$i]['num_serie']);
		$tpl->assign("fecha", $result[$i]['fecha']);
		$tpl->assign("turno", $result[$i]['turno']);
	}
}
$tpl->printToScreen();
?>