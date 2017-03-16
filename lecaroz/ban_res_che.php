<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

if (isset($_GET['fecha'])) {
	$sql = "SELECT num_cia, (SELECT folio FROM folios_cheque WHERE num_cia = cc.num_cia AND cuenta = $_GET[cuenta] ORDER BY folio DESC LIMIT 1) AS folio FROM catalogo_companias AS cc WHERE";
	$sql .= ' num_cia BETWEEN ' . ($_SESSION['iduser'] != 1 ? ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') : '1 AND 998');
	$sql .= ' AND (num_cia IN (SELECT num_cia FROM cheques WHERE num_cia BETWEEN ' . ($_SESSION['iduser'] != 1 ? ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') : '1 AND 998') . " AND fecha BETWEEN CURRENT_DATE - interval '2 months' AND CURRENT_DATE AND fecha_cancelacion IS NULL AND cuenta = $_GET[cuenta] GROUP BY num_cia)" . ($_SESSION['tipo_usuario'] == 1 ? ' OR num_cia BETWEEN 600 AND 699 OR num_cia IN (700, 800)' : '') . ')';
	$sql .= $_GET['num_cia1'] > 0 ? (' AND num_cia ' . ($_GET['num_cia2'] > 0 ? "BETWEEN $_GET[num_cia1] AND $_GET[num_cia2]" : "= $_GET[num_cia1]")) : '';
	$sql .= " ORDER BY num_cia";
	$result = $db->query($sql);
	
	if (!$result) die('No hay resultados');
	
	$data = array();
	$cont = 0;
	foreach ($result as $reg)
		if ($reg['folio'] > 0)
			for ($folio = $reg['folio'] + 1; $folio <= $reg['folio'] + $_GET['cantidad']; $folio++) {
				$data[$cont]['folio'] = $folio;
				$data[$cont]['num_cia'] = $reg['num_cia'];
				$data[$cont]['reservado'] = 'TRUE';
				$data[$cont]['utilizado'] = 'FALSE';
				$data[$cont]['fecha'] = $_GET['fecha'];
				$data[$cont]['cuenta'] = $_GET['cuenta'];
				$cont++;
			}
	
	$sql = $db->multiple_insert('folios_cheque', $data);
	$db->query($sql);
	
	die('Se han reservado los folios con exito');
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_res_che.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign('fecha', date('d/m/Y'));

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