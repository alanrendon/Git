<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

if (!in_array($_SESSION['iduser'], array(28))) die;

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

$sql = "SELECT num_cia, nombre_corto, fecha, acre, cn.nombre, importe FROM otros_depositos LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN catalogo_nombres cn ON (cn.id = idnombre) WHERE ficha = 'FALSE' AND num_cia BETWEEN 900 AND 998 AND fecha < CURRENT_DATE - interval '5 days' ORDER BY num_cia, fecha";
$result = $db->query($sql);

if (!$result) die;

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/verFicDepPen.tpl" );
$tpl->prepare();

$num_cia = NULL;
$gran_total = 0;
foreach ($result as $reg) {
	if ($num_cia != $reg['num_cia']) {
		$num_cia = $reg['num_cia'];
		
		$tpl->newBlock('cia');
		$tpl->assign('num_cia', $num_cia);
		$tpl->assign('nombre', $reg['nombre_corto']);
		$total = 0;
	}
	$tpl->newBlock('fila');
	$tpl->assign('fecha', $reg['fecha']);
	if ($reg['acre'] > 0) {
		$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $reg[acre]");
		$tpl->assign('acre', $tmp[0]['nombre_corto']);
	}
	else $tpl->assign('acre', '&nbsp;');
	$tpl->assign('nombre', trim($reg['nombre']) != '' ? trim($reg['nombre']) : '&nbsp;');
	$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
	$total += $reg['importe'];
	$gran_total += $reg['importe'];
	$tpl->assign('cia.total', number_format($total, 2, '.', ','));
}
$tpl->assign('_ROOT.gran_total', number_format($gran_total, 2, '.', ','));

die($tpl->getOutputContent());
?>