<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ren/ren_art_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['local'])) {
	$sql = "SELECT cart.id, num_local, nombre_local, cod_arrendador, nombre, bloque, nombre_arrendatario, rfc, renta_con_recibo AS renta, mantenimiento, agua, retencion_isr, retencion_iva";
	$sql .= " FROM catalogo_arrendatarios AS cart LEFT JOIN catalogo_arrendadores AS carr USING (cod_arrendador) WHERE status = 1";
	$sql .= $_GET['local'] > 0 ? " AND num_local = $_GET[local]" : "";
	$sql .= $_GET['arr'] > 0 ? " AND cod_arrendador = $_GET[arr]" : "";
	$sql .= $_GET['bloque'] > 0 ? " AND bloque = $_GET[bloque]" : "";
	$sql .= " ORDER BY cod_arrendador, num_local";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ren_art_con.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("listado");
	
	$arr = NULL;
	foreach ($result as $reg) {
		if ($arr != $reg['cod_arrendador']) {
			$arr = $reg['cod_arrendador'];
			
			$tpl->newBlock("arr");
			$tpl->assign("cod", $reg['cod_arrendador']);
			$tpl->assign("arr", $reg['nombre']);
		}
		$tpl->newBlock("fila");
		$tpl->assign("id", $reg['id']);
		$tpl->assign("local", $reg['num_local']);
		$tpl->assign("nombre", $reg['nombre_local']);
		$tpl->assign("color", $reg['bloque'] == 1 ? "0000CC" : "CC0000");
		$tpl->assign("bloque", $reg['bloque'] == 1 ? "PROPIO" : "AJENO");
		$tpl->assign("art", $reg['nombre_arrendatario']);
		$tpl->assign("rfc", $reg['rfc']);
		$tpl->assign("renta", $reg['renta'] != 0 ? number_format($reg['renta'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("mantenimiento", $reg['mantenimiento'] != 0 ? number_format($reg['mantenimiento'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("agua", $reg['agua'] != 0 ? number_format($reg['agua'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("ret", $reg['retencion_iva'] == "t" ? "SI" : "&nbsp;");
		$tpl->assign("isr", $reg['retencion_isr'] == "t" ? "SI" : "&nbsp;");
	}
	$tpl->printToScreen();
	die;
}


$tpl->newBlock("datos");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>