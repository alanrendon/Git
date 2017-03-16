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
$tpl->assignInclude("body","./plantillas/bal/bal_can_des.tpl");
$tpl->prepare();

if (isset($_POST['num_cia'])) {
	$sql = "DELETE FROM desc_utilidad_mes;\n";
	foreach ($_POST['num_cia'] as $i => $cia) {
		$cantidad = str_replace(",", "", $_POST['cantidad'][$i]);
		
		if ($cantidad != 0)
			$sql .= "INSERT INTO desc_utilidad_mes (num_cia, cantidad) VALUES ($cia, $cantidad);\n";
	}
	$db->query($sql);
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

$sql = "SELECT num_cia, nombre_corto, cantidad FROM catalogo_companias LEFT JOIN desc_utilidad_mes USING (num_cia)";
$sql .= " WHERE num_cia < 100 OR num_cia BETWEEN 100 AND 200 OR num_cia IN (702, 704) ORDER BY num_cia";
$result = $db->query($sql);

$tpl->newBlock("listado");

$total = 0;
foreach ($result as $i => $reg) {
	$tpl->newBlock("fila");
	$tpl->assign("next", $i < count($result) - 1 ? $i + 1 : 0);
	$tpl->assign("num_cia", $reg['num_cia']);
	$tpl->assign("nombre_cia", $reg['nombre_corto']);
	$tpl->assign("cantidad", $reg['cantidad'] != 0 ? number_format($reg['cantidad'], 2, ".", ",") : "");
	
	$total += $reg['cantidad'];
}
$tpl->assign("listado.total", number_format($total, 2, ".", ","));

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>