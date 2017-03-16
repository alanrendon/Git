<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

if (isset($_POST['id'])) {
	$sql = "DELETE FROM devoluciones_zap WHERE id IN (";
	foreach ($_POST['id'] as $i => $id)
		$sql .= $id . ($i < count($_POST['id']) - 1 ? ', ' : ')');
	
	$db->query($sql);
	
	return 1;
}

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/zap/zap_dev_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$options = array();
	
	if ($_GET['num_cia'] > 0)
		$options[] = "dz.num_cia = $_GET[num_cia]";
	if ($_GET['num_pro'] > 0)
		$options[] = "dz.num_proveedor = $_GET[num_pro]";
	if ($_GET['criterio'] == 1)
		$options[] = 'folio IS NULL';
	if ($_GET['criterio'] == 2 && $_GET['folio'] > 0)
		$options[] = "folio = $_GET[folio]";
		
	
	$sql = "
		SELECT
			dz.id,
			dz.num_cia,
			dz.num_proveedor
				AS num_pro,
			cc.nombre
				AS nombre_cia,
			cp.nombre
				AS nombre_pro,
			fecha,
			modelo,
			color,
			talla,
			piezas,
			precio,
			importe,
			obs,
			folio,
			folio_cheque,
			dz.cuenta
		FROM
			devoluciones_zap dz
			LEFT JOIN catalogo_proveedores cp
				USING (num_proveedor)
			LEFT JOIN catalogo_companias cc
				USING (num_cia)
	";
	
	if (count($options) > 0) {
		$sql .= ' WHERE ';
		$sql .= implode(' AND ', $options);
	}
	$sql .= "
		ORDER BY " . ($_GET['orden'] == 1 ? "num_cia, num_pro," : "num_pro, num_cia,") . " folio, fecha";
	$result = $db->query($sql);
	
	if (!$result)
		die(header('location: ./zap_dev_con.php?codigo_error=1'));
	
	$tpl->newBlock('result');
	$numb = NULL;
	$fnumb = $_GET['orden'] == 1 ? 'num_cia' : 'num_pro';
	$fnombreb = $_GET['orden'] == 1 ? 'nombre_cia' : 'nombre_pro';
	$fnumsb = $_GET['orden'] == 1 ? 'num_pro' : 'num_cia';
	$fnombresb = $_GET['orden'] == 1 ? 'nombre_pro' : 'nombre_cia';
	foreach ($result as $reg) {
		if ($numb != $reg[$fnumb]) {
			$numb = $reg[$fnumb];
			
			$tpl->newBlock('bloque');
			$tpl->assign('num', $numb);
			$tpl->assign('nombre', $reg[$fnombreb]);
			
			$numsb = NULL;
		}
		
		if ($numsb != $reg[$fnumsb]) {
			$numsb = $reg[$fnumsb];
			
			$tpl->newBlock('subbloque');
			$tpl->assign('num', $numsb);
			$tpl->assign('nombre', $reg[$fnombresb]);
			
			$total = 0;
			$piezas = 0;
			
			$folio = NULL;
		}
		
		if ($_GET['criterio'] == 2 && $folio != $reg['folio']) {
			$folio = $reg['folio'];
			
			$tpl->newBlock('vale');
			$tpl->assign('folio', $reg['folio']);
			$tpl->assign('cheque', $reg['folio_cheque']);
			$tpl->assign('banco', $reg['cuenta'] > 0 ? ($reg['cuenta'] == 1 ? 'BANORTE' : 'SANTANDER') : '&nbsp;');
			
			$v_total = 0;
			$v_piezas = 0;
		}
		else if ($folio == NULL) {
			$folio = TRUE;
			
			$tpl->newBlock('vale');
			$tpl->assign('folio', $reg['folio']);
			$tpl->assign('cheque', $reg['folio_cheque']);
			$tpl->assign('banco', $reg['cuenta'] > 0 ? ($reg['cuenta'] == 1 ? 'BANORTE' : 'SANTANDER') : '&nbsp;');
			
			$v_total = 0;
			$v_piezas = 0;
		}
		
		$tpl->newBlock('fila');
		$tpl->assign('id', $reg['id']);
		$tpl->assign('dis', $reg['folio'] > 0 ? ' disabled' : '');
		$tpl->assign('fecha', $reg['fecha']);
		$tpl->assign('modelo', $reg['modelo']);
		$tpl->assign('color', $reg['color']);
		$tpl->assign('talla', $reg['talla']);
		$tpl->assign('piezas', number_format($reg['piezas']));
		$tpl->assign('precio', number_format($reg['precio'], 2, '.', ','));
		$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
		$tpl->assign('obs', trim($reg['obs']) != '' ? trim($reg['obs']) : '&nbsp;');
		
		$v_total += $reg['importe'];
		$v_piezas += $reg['piezas'];
		
		$total += $reg['importe'];
		$piezas += $reg['piezas'];
		
		$tpl->assign('vale.total', number_format($v_total, 2, '.', ','));
		$tpl->assign('vale.piezas', number_format($v_piezas));
		
		$tpl->assign('subbloque.total', number_format($total, 2, '.', ','));
		$tpl->assign('subbloque.piezas', number_format($piezas));
	}
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

$result = $db->query('SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia BETWEEn 900 AND 998 ORDER BY num_cia');
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre_corto']);
}

$result = $db->query('SELECT num_proveedor AS num_pro, nombre FROM catalogo_proveedores ORDER BY num_pro');
foreach ($result as $reg) {
	$tpl->newBlock('p');
	$tpl->assign('num_pro', $reg['num_pro']);
	$tpl->assign('nombre', $reg['nombre']);
}

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