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

if (isset($_POST['id'])) {
	$sql = '';
	
	for ($i = 0; $i < count($_POST['id']); $i++)
		if ($_POST['num_fact_new'][$i] != '') {
			if ($_SESSION['tipo_usuario'] == 2) {
				$sql .= "UPDATE facturas_zap SET num_fact = '{$_POST['num_fact_new'][$i]}' WHERE num_proveedor = {$_POST['num_pro'][$i]} AND num_fact = {$_POST['num_fact'][$i]};\n";
				$sql .= "UPDATE facturas_pendientes SET fecha_aclaracion = CURRENT_DATE, num_fact_nuevo = {$_POST['num_fact_new'][$i]}, imp = 'FALSE' WHERE id = {$_POST['id'][$i]};";
			}
			else {
				$sql .= "UPDATE facturas SET num_fact = UPPER('{$_POST['num_fact_new'][$i]}') WHERE num_proveedor = {$_POST['num_pro'][$i]} AND num_fact = UPPER('{$_POST['num_fact'][$i]}');\n";
				$sql .= "UPDATE pasivo_proveedores SET num_fact = UPPER('{$_POST['num_fact_new'][$i]}') WHERE num_proveedor = {$_POST['num_pro'][$i]} AND num_fact = UPPER('{$_POST['num_fact'][$i]}');\n";
				$sql .= "UPDATE entrada_mp SET num_fact = UPPER('{$_POST['num_fact_new'][$i]}') WHERE num_proveedor = {$_POST['num_pro'][$i]} AND num_fact = UPPER('{$_POST['num_fact'][$i]}');\n";
				$sql .= "UPDATE factura_gas SET num_fact = UPPER('{$_POST['num_fact_new'][$i]}') WHERE num_proveedor = {$_POST['num_pro'][$i]} AND num_fact = ('{$_POST['num_fact'][$i]}');\n";
				$sql .= "UPDATE mov_inv_real SET descripcion = 'COMPRA F. NO. " . strtoupper($_POST['num_fact_new'][$i]) . "', num_fact = UPPER('{$_POST['num_fact_new'][$i]}') WHERE num_proveedor = {$_POST['num_pro'][$i]} AND descripcion LIKE '%" . strtoupper($_POST['num_fact'][$i]) . "%';\n";
				$sql .= "UPDATE facturas_pendientes SET fecha_aclaracion = CURRENT_DATE, num_fact_nuevo = UPPER('{$_POST['num_fact_new'][$i]}'), imp = 'FALSE' WHERE id = {$_POST['id'][$i]};";
			}
		}
	$db->query($sql);
	die(header('location: ./fac_pen_con.php'));
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_pen_con.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_REQUEST['num_cia'])) {
	$sql = 'SELECT f.num_cia, cc.nombre_corto AS nombre_cia, fp.id, fp.num_proveedor AS num_pro, cp.nombre AS nombre_pro, num_fact, ' . ($_SESSION['tipo_usuario'] == 2 ? 'total AS importe' : 'total AS importe') . ', fecha_solicitud, obs, fecha_aclaracion, num_fact_nuevo FROM facturas_pendientes AS fp LEFT JOIN ' . ($_SESSION['tipo_usuario'] == 2 ? 'facturas_zap' : 'facturas') . ' AS f USING (num_proveedor, num_fact) LEFT JOIN catalogo_proveedores AS cp USING (num_proveedor) LEFT JOIN catalogo_companias cc USING (num_cia) WHERE';
	$sql .= ' num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 800');
	$sql .= ' AND fecha_aclaracion ' . ($_GET['tipo'] == 1 ? 'IS NULL' : 'IS NOT NULL');
	$sql .= $_GET['num_cia'] > 0 ? " AND f.num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['num_pro'] > 0 ? " AND fp.num_proveedor = $_GET[num_pro]" : '';
	$sql .= $_GET['num_fact'] != '' ? " AND num_fact = '$_GET[num_fact]'" : '';
	$sql .= ' ORDER BY num_pro, num_fact';
	$result = $db->query($sql);
	
	if (!$result)
		die(header('location: ./fac_pen_con.php?codigo_error=1'));
	
	$tpl->newBlock($_GET['tipo'] == 1 ? 'pendientes' : 'aclarados');
	foreach ($result as $i => $reg) {
		$tpl->newBlock($_GET['tipo'] == 1 ? 'pen' : 'acla');
		$tpl->assign('next', $i < count($result) - 1 ? $i + 1 : 0);
		$tpl->assign('back', $i > 0 ? $i - 1 : count($result) - 1);
		$tpl->assign('id', $reg['id']);
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre_cia', $reg['nombre_cia']);
		$tpl->assign('num_pro', $reg['num_pro']);
		$tpl->assign('nombre_pro', $reg['nombre_pro']);
		$tpl->assign('num_fact', $reg['num_fact']);
		$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
		$tpl->assign('fecha_solicitud', $reg['fecha_solicitud']);
		$tpl->assign('obs', trim($reg['obs']));
		if ($_GET['tipo'] == 2) {
			$tpl->assign('fecha_aclaracion', $reg['fecha_aclaracion']);
			$tpl->assign('num_fact_nuevo', $reg['num_fact_nuevo']);
		}
	}
	
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');

$cias = $db->query("SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 800') . " ORDER BY num_cia");
foreach ($cias as $cia) {
	$tpl->newBlock("c");
	$tpl->assign("num_cia", $cia['num_cia']);
	$tpl->assign("nombre", $cia['nombre_corto']);
}

$pros = $db->query("SELECT num_proveedor AS num_pro, nombre FROM catalogo_proveedores ORDER BY num_pro");
foreach ($pros as $pro) {
	$tpl->newBlock("p");
	$tpl->assign("num_pro", $pro['num_pro']);
	$tpl->assign("nombre", $pro['nombre']);
}

$tpl->printToScreen();
?>