<?php
include 'includes/dbstatus.php';
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_GET['c'])) {
	$sql = 'SELECT nombre_corto FROM catalogo_companias LEFT JOIN catalogo_operadoras USING (idoperadora) WHERE num_cia <= 300 AND num_cia = ' . $_GET['c'];
	$sql .= !in_array($_SESSION['iduser'], array(1, 4, 18, 19)) ? ' AND iduser = ' . $_SESSION['iduser'] : '';
	$result = $db->query($sql);
	
	if ($result)
		echo $result[0]['nombre_corto'];
	die;
}

$tpl = new TemplatePower('./plantillas/header.tpl');

$tpl->assignInclude('body', './plantillas/bal/bal_ifm_lis.tpl');
$tpl->prepare();

$tpl->newBlock('menu');
$tpl->assign('menucnt', '$_SESSION[menu]_cnt.js');
$tpl->gotoBlock('_ROOT');

if (isset($_GET['num_cia'])) {
	$fecha = date('d/m/Y', mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
	$sql = 'SELECT num_cia, nombre_corto AS nombre_cia, tipo, codmp, cmp.nombre AS nombre_producto, tuc.descripcion AS unidad, inventario FROM inventario_fin_mes ifm LEFT JOIN catalogo_mat_primas cmp USING (codmp) LEFT JOIN tipo_unidad_consumo tuc ON (idunidad = unidadconsumo) LEFT JOIN catalogo_companias cc USING (num_cia) LEFT JOIN catalogo_operadoras co USING (idoperadora) WHERE fecha = \'' . $fecha . '\' AND num_cia <= 300';
	$sql .= !in_array($_SESSION['iduser'], array(1, 4, 18, 19)) ? ' AND iduser = ' . $_SESSION['iduser'] : '';
	$sql .= $_GET['num_cia'] > 0 ? ' AND num_cia = ' . $_GET['num_cia'] : '';
	$sql .= ' ORDER BY num_cia, tipo, nombre_producto';
	$result = $db->query($sql);
	
	if (!$result)
		die(header('location: ./bal_ifm_lis.php?codigo_error=1'));
	
	$num_cia = NULL;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			if ($num_cia != NULL)
				$tpl->assign('listado.salto', '<br style="page-break-after:always;">');
			
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock('listado');
			$tpl->assign('num_cia', $reg['num_cia']);
			$tpl->assign('nombre', $reg['nombre_cia']);
			$tpl->assign('mes', mes_escrito($_GET['mes']));
			$tpl->assign('anio', $_GET['anio']);
			
			$tipo = $reg['tipo'];
		}
		if ($tipo != $reg['tipo']) {
			$tipo = $reg['tipo'];
			
			$tpl->newBlock('empaque');
		}
		$tpl->newBlock('fila');
		$tpl->assign('codmp', $reg['codmp']);
		$tpl->assign('nombre', $reg['nombre_producto']);
		$tpl->assign('existencia', $reg['inventario'] != 0 ? number_format($reg['inventario'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('unidad', $reg['unidad']);
	}
	
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');
$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));
$tpl->assign(date('n', mktime(0, 0, 0, date('n'), 0, date('Y'))), ' selected');

$tpl->printToScreen();
?>
