<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

if (!in_array($_SESSION['iduser'], array(10))) die;

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

$sql = "SELECT idcia, nombre_corto, bloc.estado, folios_usados FROM bloc LEFT JOIN catalogo_companias ON (num_cia = idcia) ORDER BY idcia, let_folio, folio_inicio";
$result = $db->query($sql);

if (!$result) die;

$num_cia = NULL;
$blocks = array();
foreach ($result as $i => $reg) {
	if ($num_cia != $reg['idcia']) {
		$num_cia = $reg['idcia'];
		
		$blocks[$num_cia] = array('nombre' => $reg['nombre_corto'], 'blocks' => 0, 'sin_usar' => 0, 'en_proceso' => 0, 'terminados' => 0);
	}
	$blocks[$num_cia]['blocks'] += 1;
	$blocks[$num_cia]['terminados'] += $reg['estado'] == 't' ? 1 : 0;
	$blocks[$num_cia]['en_proceso'] += $reg['estado'] != 't' && $reg['folios_usados'] > 0 ? 1 : 0;
	$blocks[$num_cia]['sin_usar'] += $reg['estado'] != 't' && $reg['folios_usados'] == 0 ? 1 : 0;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/verBlockPas.tpl" );
$tpl->prepare();

foreach ($blocks as $num_cia => $reg) {
	$tpl->newBlock('fila');
	$tpl->assign('num_cia', $num_cia);
	$tpl->assign('nombre', $reg['nombre']);
	$tpl->assign('blocks', $reg['blocks'] > 0 ? $reg['blocks'] : '&nbsp;');
	$tpl->assign('sin_usar', $reg['sin_usar'] > 0 ? $reg['sin_usar'] : '&nbsp;');
	$tpl->assign('en_proceso', $reg['en_proceso'] > 0 ? $reg['en_proceso'] : '&nbsp;');
	$tpl->assign('terminados', $reg['terminados'] > 0 ? $reg['terminados'] : '&nbsp;');
}

die($tpl->getOutputContent());
?>
