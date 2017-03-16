<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

if (!in_array($_SESSION['iduser'], array(27))) die;

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

$today = date('d/m/Y');

$sql = "SELECT num_cia, nombre_corto, fecha FROM \"VencimientoFacturas\" vf LEFT JOIN catalogo_companias USING (num_cia) WHERE vf.status = 1 AND fecha BETWEEN now()::date AND now()::date + interval '2 months 15 days' ORDER BY num_cia, fecha";
$result = $db->query($sql);

if (!$result) die;

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/verVenFac.tpl" );
$tpl->prepare();

foreach ($result as $reg) {
	$tpl->newBlock('fila');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre_corto']);
	$tpl->assign('fecha', $reg['fecha']);
}

die($tpl->getOutputContent());
?>