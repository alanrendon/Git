<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Delaracion de variables -------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/ban/ban_con_des.tpl" );
$tpl->prepare();

$sql = "SELECT fecha, concepto, importe FROM estado_cuenta WHERE num_cia = $_GET[num_cia] AND cod_mov = $_GET[cod_mov] AND fecha BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]' ORDER BY fecha";
$result = $db->query($sql);

$tpl->assign("num_cia", $_GET['num_cia']);
$nombre_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
$tpl->assign("nombre_cia", $nombre_cia[0]['nombre_corto']);
$tpl->assign("cod_mov", $_GET['cod_mov']);
$cod_mov = $db->query("SELECT descripcion FROM catalogo_mov_bancos WHERE cod_mov = $_GET[cod_mov] LIMIT 1");
$tpl->assign("descripcion", $cod_mov[0]['descripcion']);
$tpl->assign("fecha1", $_GET['fecha1']);
$tpl->assign("fecha2", $_GET['fecha2']);

$total = 0;
for ($i = 0; $i < count($result); $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("fecha", $result[$i]['fecha']);
	$tpl->assign("concepto", $result[$i]['concepto']);
	$tpl->assign("importe", number_format($result[$i]['importe'], 2, ".", ","));
	
	$total += $result[$i]['importe'];
}
$tpl->assign("_ROOT.total", number_format($total, 2, ".", ","));

$tpl->printToScreen();
$db->desconectar();
?>