<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/aux_inv.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

//if ($_SESSION['iduser'] != 1) die(header('location: ./offline.htm'));

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_POST['id'])) {
	// Determinar valor de diferencia a favor (-) y en contra (+)
	$dif = ($_POST['tipo_mov'] == 'f' ? -1 : 1) * $_POST['dif'];
	// Calcular existencia sin diferencia
	$old_existencia = get_val($_POST['existencia']) + $dif;
	// Calcular nueva diferencia
	$dif = $old_existencia - get_val($_POST['new_existencia']);
	// Actualizar existencia de historico y de inventario
	$sql = "UPDATE historico_inventario SET existencia = " . get_val($_POST['new_existencia']) . ", precio_unidad = $_POST[precio] WHERE idinv = $_POST[idinv];\n";
	$sql .= "UPDATE inventario_real SET existencia = " . get_val($_POST['new_existencia']) . ", precio_unidad = $_POST[precio] WHERE num_cia = $_POST[num_cia] AND codmp = $_POST[codmp];\n";
	$sql .= "UPDATE inventario_virtual SET existencia = " . get_val($_POST['new_existencia']) . ", precio_unidad = $_POST[precio] WHERE num_cia = $_POST[num_cia] AND codmp = $_POST[codmp];\n";
	if ($_POST['id'] > 0)
		$sql .= "DELETE FROM mov_inv_real WHERE id = $_POST[id];\n";
	if ($dif != 0)
		$sql .= "INSERT INTO mov_inv_real (num_cia, codmp, fecha, tipo_mov, cantidad, precio, total_mov, precio_unidad, descripcion) VALUES ($_POST[num_cia], $_POST[codmp], '$_POST[fecha]', TRUE, " . ($dif > 0 ? abs($dif) : -abs($dif)) . ", $_POST[precio], " . (abs($dif) * $_POST['precio']) . ", $_POST[precio], 'DIFERENCIA INVENTARIO');\n";
	if ($_POST['codmp'] == 90) {
		$sql .= "DELETE FROM movimiento_gastos WHERE num_cia = $_POST[num_cia] AND codgastos = 90 AND fecha = '$_POST[fecha]' AND concepto = 'DIFERENCIA INVENTARIO';\n";
		if ($dif > 0)
			$sql .= "INSERT INTO movimiento_gastos (codgastos, num_cia, fecha, importe, captura, concepto) VALUES (90, $_POST[num_cia], '$_POST[fecha]', $dif * $_POST[precio], 'FALSE', 'DIFERENCIA INVENTARIO');\n";
	}
	$db->query($sql);
	
	// Actualizar el inventario y el historico del producto despues de actualizada la diferencia
	$db->query(actualizarMP($db, $_POST['num_cia'], $_POST['codmp'], $_POST['mes'], $_POST['anio']));
	
	die(header('location: ./bal_dif_mod.php'));
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_dif_mod.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$fecha_dif = date('d/m/Y', mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
	
	// Actualizar antes el inventario y el historico del producto
	$db->query(actualizarMP($db, $_GET['num_cia'], $_GET['codmp'], $_GET['mes'], $_GET['anio']));
	
	$sql = "SELECT id, idinv, num_cia, nombre_corto, codmp, cm.nombre AS desc, tipo_mov, cantidad AS dif, h.existencia, h.precio_unidad AS precio FROM mov_inv_real LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN historico_inventario AS h USING (num_cia, codmp, fecha) LEFT JOIN catalogo_mat_primas AS cm USING (codmp) WHERE num_cia = $_GET[num_cia] AND fecha = '$fecha_dif' AND codmp = $_GET[codmp] AND descripcion = 'DIFERENCIA INVENTARIO'";
	$result = $db->query($sql);
	
	if (!$result) {
		$sql = "SELECT NULL AS id, idinv, num_cia, nombre_corto, codmp, cm.nombre AS desc, NULL AS tipo_mov, NULL AS dif, h.existencia, h.precio_unidad AS precio FROM historico_inventario AS h LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN catalogo_mat_primas AS cm USING (codmp) WHERE num_cia = $_GET[num_cia] AND codmp = $_GET[codmp] AND fecha = '$fecha_dif'";
		$result = $db->query($sql);
	}
	
	if (!$result)
		die(header('location: ./bal_dif_mod.php?codigo_error=1'));
	
	$tpl->newBlock('mod');
	$tpl->assign('id', $result[0]['id']);
	$tpl->assign('idinv', $result[0]['idinv']);
	$tpl->assign('num_cia', $result[0]['num_cia']);
	$tpl->assign('nombre', $result[0]['nombre_corto']);
	$tpl->assign('fecha', $fecha_dif);
	$tpl->assign('mes', mes_escrito($_GET['mes'], TRUE));
	$tpl->assign('_mes', $_GET['mes']);
	$tpl->assign('anio', $_GET['anio']);
	$tpl->assign('codmp', $result[0]['codmp']);
	$tpl->assign('desc', $result[0]['desc']);
	$tpl->assign('existencia', number_format($result[0]['existencia'], 2, '.', ','));
	$tpl->assign('dif', $result[0]['dif']);
	$tpl->assign('precio', $result[0]['precio']);
	$tpl->assign('tipo_mov', $result[0]['tipo_mov']);
	
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');

$tpl->assign(date('n', mktime(0, 0, 0, date('n'), 0, date('Y'))), ' selected');
$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));

$result = $db->query('SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN 1 AND 800 ORDER BY num_cia');
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre_corto']);
}

$result = $db->query('SELECT codmp, nombre AS desc FROM catalogo_mat_primas ORDER BY codmp');
foreach ($result as $reg) {
	$tpl->newBlock('cod');
	$tpl->assign('codmp', $reg['codmp']);
	$tpl->assign('desc', $reg['desc']);
}

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