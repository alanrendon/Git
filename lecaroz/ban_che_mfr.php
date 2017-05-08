<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_che_mfr.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	/*$sql = "SELECT cheques.id, num_cia, nombre, folio, cheques.fecha, a_nombre, codgastos, descripcion, importe FROM cheques LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN catalogo_gastos";
	$sql .= " USING (codgastos) LEFT JOIN folios_cheque USING (num_cia, folio, cuenta) WHERE cuenta = $_GET[cuneta] AND reservado = 'FALSE' AND imp = 'FALSE'";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
	$sql .= $_GET['num_pro'] > 0 ? " AND num_proveedor = $_GET[num_pro]" : "";
	$sql .= " ORDER BY num_cia, folio";*/
	$sql = "SELECT cheques.id, num_cia, nombre, folio, cheques.fecha, a_nombre, codgastos, descripcion, importe FROM cheques LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN catalogo_gastos";
	$sql .= " USING (codgastos) LEFT JOIN folios_cheque USING (num_cia, folio, cuenta) WHERE cuenta = 2 AND cheques.id BETWEEN 92058 AND 92077";
	$sql .= " ORDER BY num_cia, folio";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ban_che_mfr.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("cheques");
	
	$fecha = "31/12/" . (date("Y") - 1);
	$num_cia = NULL;
	foreach ($result as $i => $cheque) {
		if ($num_cia != $cheque['num_cia']) {
			$num_cia = $cheque['num_cia'];
			
			$tpl->newBlock("cia");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre_cia", $cheque['nombre']);
			$tpl->assign("ini", $i);
			
			$folios = $db->query("SELECT folio FROM folios_cheque WHERE num_cia = $num_cia AND cuenta = $_GET[cuenta] AND reservado = 'TRUE' AND utilizado = 'FALSE' AND fecha = '$fecha'");
			if ($folios) {
				$tpl->gotoBlock("cheques");
				$tpl->newBlock("array_cia");
				$tpl->assign("num_cia", $num_cia);
				foreach ($folios as $k => $folio) {
					$tpl->newBlock("array_folio");
					$tpl->assign("num_cia", $num_cia);
					$tpl->assign("i", $k);
					$tpl->assign("folio", $folio['folio']);
				}
			}
			$tpl->gotoBlock("cia");
		}
		$tpl->newBlock("cheque");
		$tpl->assign("num_cia", $num_cia);
		$tpl->assign("cia.fin", $i);
		$tpl->assign("id", $cheque['id']);
		$tpl->assign("folio", $cheque['folio']);
		$tpl->assign("fecha", $cheque['fecha']);
		$tpl->assign("a_nombre", $cheque['a_nombre']);
		$tpl->assign("codgastos", $cheque['codgastos']);
		$tpl->assign("descripcion", $cheque['descripcion']);
		$tpl->assign("importe", number_format($cheque['importe'], 2, ".", ","));
		if ($folios)
			foreach ($folios as $folio) {
				$tpl->newBlock("folio");
				$tpl->assign("folio", $folio['folio']);
			}
	}
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

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
?>