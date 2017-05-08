<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ros/ros_imp_dat.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	// Obtener movimientos
	$sql = "SELECT codmp, tipo_mov, cantidad, precio_unidad AS precio FROM mov_inv_real WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' ORDER BY codmp, tipo_mov";
	$movs = $db->query($sql);
	if (!$movs) {
		$sql = "SELECT codmp, tipomov AS tipo_mov, cantidad, precio FROM mov_inv_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' ORDER BY codmp, tipo_mov";
		$movs = $db->query($sql);
	}
	// Obtener gastos
	$sql = "SELECT concepto, importe FROM movimiento_gastos WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND captura = 'FALSE' ORDER BY concepto";
	$gastos = $db->query($sql);
	if (!$gastos) {
		$sql = "SELECT concepto, importe FROM gastos_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' UNION SELECT 'PRESTAMO ' || nombre AS concepto, importe FROM prestamos_tmp";
		$sql .= " WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND tipo_mov = 'FALSE' ORDER BY concepto";
		$gastos = $db->query($sql);
	}
	// Obtener abonos de prestamos
	$sql = "SELECT 'ABONO PRESTAMO ' || nombre AS concepto, importe FROM prestamos AS p LEFT JOIN catalogo_trabajadores AS ct ON (id_empleado = ct.id) WHERE p.num_cia = $_GET[num_cia]";
	$sql .= " AND fecha = '$_GET[fecha]' AND tipo_mov = 'TRUE' ORDER BY nombre";
	$abonos = $db->query($sql);
	if (!$abonos) {
		$sql = "SELECT 'ABONO PRESTAMO ' || nombre AS concepto, importe FROM prestamos_tmp WHERE num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND tipo_mov = 'TRUE' ORDER BY nombre";
		$abonos = $db->query($sql);
	}
	
	if (!$movs && !$gastos && !$abonos)
		die(header('location: ./ros_imp_dat.php?codigo_error=1'));
	
	$sql = "SELECT codmp, nombre, existencia, orden, 1 AS status FROM inventario_real LEFT JOIN catalogo_mat_primas USING (codmp) WHERE num_cia = $_GET[num_cia] AND codmp NOT IN";
	$sql .= " (90, 425, 194, 138, 364, 167, 61, 170, 169) UNION SELECT codmp, nombre, '0', orden, 0 AS status FROM mov_inv_tmp LEFT JOIN catalogo_mat_primas USING (codmp) WHERE";
	$sql .= " num_cia = $_GET[num_cia] AND fecha = '$_GET[fecha]' AND codmp NOT IN (SELECT codmp FROM inventario_real WHERE num_cia = $_GET[num_cia]) AND codmp NOT IN (90, 425, 194,";
	$sql .= " 138, 364, 167, 61, 170, 169) ORDER BY orden";
	$productos = $db->query($sql);
	
	$tpl->newBlock('result');
	$tpl->assign('num_cia', $_GET['num_cia']);
	$nombre = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$tpl->assign('nombre', $nombre[0]['nombre']);
	ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_GET['fecha'], $fecha_des);
	$tpl->assign('dia', $fecha_des[1]);
	$tpl->assign('mes', mes_escrito($fecha_des[2]));
	$tpl->assign('anio', $fecha_des[3]);
	
	if ($productos)
		foreach ($productos as $p) {
			$tpl->newBlock('producto');
			$tpl->assign('codmp', $p['codmp']);
			$tpl->assign('nombre', $p['nombre']);
		}
	
	if ($gastos)
		foreach ($gastos as $g) {
			$tpl->newBlock('gasto');
			$tpl->assign('concepto', $g['concepto']);
			$tpl->assign('importe', number_format($g['importe'], 2, '.', ','));
		}
	
	if ($abonos)
		foreach ($abonos as $a) {
			$tpl->newBlock('otro');
			$tpl->assign('concepto', $a['concepto']);
			$tpl->assign('importe', number_format($a['importe'], 2, '.', ','));
		}
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

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