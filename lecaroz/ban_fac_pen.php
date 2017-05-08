<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_fac_pen.tpl");
$tpl->prepare();

$sql = "SELECT num_proveedor AS num_pro, nombre, num_fact, fecha, total, por_aut, copia_fac FROM facturas_zap LEFT JOIN catalogo_proveedores USING (num_proveedor) WHERE num_cia = $_GET[num_cia] AND tspago IS NULL and (por_aut = 'FALSE' OR copia_fac = 'FALSE') AND facturas_zap.sucursal <> 'TRUE' AND facturas_zap.total > 0 ORDER BY num_pro, num_fact";
$result = $db->query($sql);

$total = 0;
foreach ($result as $reg) {
	$tpl->newBlock('fila');
	$tpl->assign('num_pro', $reg['num_pro']);
	$tpl->assign('nombre', $reg['nombre']);
	$tpl->assign('num_fact', $reg['num_fact']);
	$tpl->assign('fecha', $reg['fecha']);
	$tpl->assign('total', number_format($reg['total'], 2, '.', ','));
	$tpl->assign('por', $reg['por_aut'] == 't' ? 'X' : '&nbsp;');
	$tpl->assign('cop', $reg['copia_fac'] == 't' ? 'X' : '&nbsp;');
	$total += $reg['total'];
}
$tpl->assign('_ROOT.total', number_format($total, 2, '.', ','));

$tpl->printToScreen();
?>