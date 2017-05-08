<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------

$db = new DBclass($dsn);

if (isset($_POST['id'])) {
	$sql = "DELETE FROM facturas_clientes WHERE id IN (";
	foreach ($_POST['id'] as $key => $value)
		$sql .= $value . ($key < count($_POST['id']) - 1 ? "," : ")");
	$db->empezar_transaccion();
	$db->query($sql);
	$db->terminar_transaccion();
	header("location: ./pan_fac_del.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/pan/pan_fac_del.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	$tpl->assign("fecha", date("d/m/Y"));
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}

	$tpl->printToScreen();
	die();
}

$sql = "SELECT * FROM facturas_clientes WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' ORDER BY folio";
$result = $db->query($sql);

if (!$result) {
	header("location: ./pan_fac_del.php?codigo_error=1");
	die;
}

$tpl->newBlock("cancelacion");

$folio = NULL;
for ($i=0; $i<count($result); $i++) {
	if ($folio != $result[$i]['folio']) {
		if ($folio != NULL) {
			$tpl->assign("factura.r2", $i);
			$tpl->assign("factura.subtotal", number_format($subtotal, 2, ".", ","));
			$tpl->assign("factura.iva", number_format($iva, 2, ".", ","));
			$tpl->assign("factura.total", number_format($total, 2, ".", ","));
		}
		
		$folio = $result[$i]['folio'];
		
		$tpl->newBlock("factura");
		$cliente = $db->query("SELECT nombre FROM catalogo_clientes WHERE id = {$result[$i]['idcliente']}");
		$tpl->assign("cliente", $cliente[0]['nombre']);
		$tpl->assign("folio", $folio);
		$tpl->assign("r1", $i);
		
		$subtotal = 0;
		$iva = 0;
		$total = 0;
	}
	$tpl->newBlock("fila");
	$tpl->assign("id", $result[$i]['id']);
	$tpl->assign("cantidad", number_format($result[$i]['cantidad']));
	$tpl->assign("descripcion", $result[$i]['descripcion']);
	$tpl->assign("precio_unidad", $result[$i]['precio_unidad'] != 0 ? number_format($result[$i]['precio_unidad'], 2, ".", ",") : "&nbsp;");
	$tpl->assign("importe", number_format($result[$i]['subtotal'], 2, ".", ","));
	
	$subtotal += $result[$i]['subtotal'];
	$iva += $result[$i]['impuestos'];
	$total += $result[$i]['importe_total'];
}
if ($folio != NULL) {
	$tpl->assign("factura.r2", $i);
	$tpl->assign("factura.subtotal", number_format($subtotal, 2, ".", ","));
	$tpl->assign("factura.iva", number_format($iva, 2, ".", ","));
	$tpl->assign("factura.total", number_format($total, 2, ".", ","));
}

$tpl->printToScreen();
$db->desconectar();
?>