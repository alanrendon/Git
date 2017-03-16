<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

//if ($_SESSION['iduser'] != 1) die(header('location: ./offline.htm'));

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_GET['folio'])) {
	$tpl = new TemplatePower( "./plantillas/zap/vale_dev.tpl" );
	$tpl->prepare();

	$sql = "SELECT folio, num_cia_cheque AS num_cia, cc.nombre AS nombre_cia, dz.num_proveedor, cp.nombre AS nombre_pro, folio_cheque, (SELECT fecha FROM cheques WHERE num_cia = dz.num_cia_cheque AND cuenta = dz.cuenta AND folio = dz.folio_cheque) AS fecha_cheque, dz.cuenta, modelo, color, talla, piezas, precio, importe FROM devoluciones_zap AS dz LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc ON (cc.num_cia = dz.num_cia_cheque) WHERE";
	$sql .= $_GET['folio'] > 0 ? " folio = $_GET[folio]" : " imp = 'TRUE'";
	$sql .= " ORDER BY folio";
	$result = $db->query($sql);

	if (!$result) {
		$tpl->newBlock('cerrar');
		$tpl->printToScreen();
		die;
	}

	$sql = "UPDATE devoluciones_zap SET imp = 'FALSE' WHERE imp = 'TRUE'" . ($_GET['folio'] > 0 ? " AND folio = $_GET[folio]" : '');
	$db->query($sql);

	$folio = NULL;
	foreach ($result as $reg) {
		if ($folio != $reg['folio']) {
			$folio = $reg['folio'];

			$tpl->newBlock('vale');
			$tpl->assign('folio', $folio);
			$tpl->assign('num_cia', $reg['num_cia']);
			$tpl->assign('nombre_cia', $reg['nombre_cia']);
			$tpl->assign('nombre_pro', $reg['nombre_pro']);
			$tpl->assign('cheque', $reg['folio_cheque']);
			$tpl->assign('fecha', $reg['fecha_cheque']);
			$tpl->assign('banco', $reg['cuenta'] == 1 ? 'BANORTE' : 'SANTANDER');

			$total = 0;
			$pares = 0;
			$cont = 0;
		}
		$tpl->newBlock('fila');
		$tpl->assign('num', ++$cont);
		$tpl->assign('modelo', $reg['modelo'] != '' ? $reg['modelo'] : '&nbsp;');
		$tpl->assign('color', $reg['color'] != '' ? $reg['color'] : '&nbsp;');
		$tpl->assign('talla', $reg['talla'] > 0 ? $reg['talla'] : '&nbsp;');
		$tpl->assign('piezas', $reg['piezas'] > 0 ? number_format($reg['piezas']) : '&nbsp;');
		$tpl->assign('precio', $reg['precio'] > 0 ? number_format($reg['precio'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('importe', $reg['importe'] > 0 ? number_format($reg['importe'], 2, '.', ',') : '&nbsp;');
		$total += $reg['importe'];
		$pares += $reg['piezas'];
		$tpl->assign('vale.total', number_format($total, 2, '.', ','));
		$tpl->assign('vale.total_pares', number_format($pares));
	}
	$tpl->printToScreen();
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/zap/zap_imp_dev.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}
$tpl->printToScreen();
?>
