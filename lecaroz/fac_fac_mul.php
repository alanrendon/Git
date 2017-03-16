<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$descripcion_error[1] = "No hay resultados";

$numfilas = 100;

// [AJAX] Validar número de factura
//if (isset($_GET['num_pro'])) {
//	if ($db->query("SELECT id FROM facturas WHERE num_proveedor = $_GET[num_pro] AND num_fact = '$_GET[num_fact]'"))
//		echo 0;
//	else
//		echo 1;
//	die;
//}

// Insertar datos
if (isset($_POST['num_pro'])) {
	$cont = 0;
	$ids = array();
	for ($i = 0; $i < count($_POST['num_cia']); $i++)
		if ($_POST['num_cia'][$i] > 0 && $_POST['num_fact'][$i] != '') {
			$fac[$cont]['num_proveedor'] = $_POST['num_pro'];
			$fac[$cont]['num_cia'] = $_POST['num_cia'][$i];
			$fac[$cont]['num_fact'] = $_POST['num_fact'][$i];
			$fac[$cont]['fecha'] = $_POST['fecha'];
			$fac[$cont]['importe'] = get_val($_POST['importe']);
			$fac[$cont]['piva'] = get_val($_POST['iva']) > 0 ? /*15*/16 : 0;
			$fac[$cont]['iva'] = get_val($_POST['iva']);
			$fac[$cont]['pretencion_isr'] = get_val($_POST['ret_isr']) > 0 ? 10 : 0;
			$fac[$cont]['pretencion_iva'] = get_val($_POST['ret_iva']) > 0 ? /*10*/10.66666667 : 0;
			$fac[$cont]['retencion_isr'] = get_val($_POST['ret_isr']) > 0 ? get_val($_POST['ret_isr']) : 0;
			$fac[$cont]['retencion_iva'] = get_val($_POST['ret_iva']) > 0 ? get_val($_POST['ret_iva']) : 0;
			$fac[$cont]['codgastos'] = $_POST['codgastos'];
			$fac[$cont]['total'] = get_val($_POST['total']);
			$fac[$cont]['tipo_factura'] = 0;
			$fac[$cont]['fecha_captura'] = date('d/m/Y');
			$fac[$cont]['iduser'] = $_SESSION['iduser'];
			$fac[$cont]['concepto'] = trim(strtoupper($_POST['concepto']));
			
			$pas[$cont]['num_proveedor'] = $_POST['num_pro'];
			$pas[$cont]['num_cia'] = $_POST['num_cia'][$i];
			$pas[$cont]['num_fact'] = $_POST['num_fact'][$i];
			$pas[$cont]['fecha'] = $_POST['fecha'];
			$pas[$cont]['codgastos'] = $_POST['codgastos'];
			$pas[$cont]['total'] = get_val($_POST['total']);
			$pas[$cont]['descripcion'] = substr(trim(strtoupper($_POST['concepto'])), 0, 50);
			$pas[$cont]['copia_fac'] = ($val = $db->query("SELECT id FROM facturas_validacion WHERE num_cia = {$_REQUEST['num_cia'][$i]} AND num_pro = {$_REQUEST['num_pro']} AND num_fact = '{$_REQUEST['num_fact'][$i]}' AND tsbaja IS NULL")) ? 'TRUE' : 'FALSE';

			if ($val)
			{
				$ids[] = $val[0]['id'];
			}
			
			$cont++;
		}
	
	if ($cont > 0) {
		$sql = $db->multiple_insert('facturas', $fac);
		$sql .= $db->multiple_insert('pasivo_proveedores', $pas);

		if ($ids)
		{
			$sql .= "
				UPDATE
					facturas_validacion
				SET
					tsvalid = NOW(),
					idvalid = {$_SESSION['iduser']}
				WHERE
					id IN (" . implode(', ', $ids) . ");
			";
		}

		$db->query($sql);
	}
	
	header('location: ./fac_fac_mul.php');
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_fac_mul.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_pro'])) {
	$tpl->newBlock('captura');
	$tpl->assign('num_pro', $_GET['num_pro']);
	$tpl->assign('nombre_pro', $_GET['nombre_pro']);
	$tpl->assign('fecha', $_GET['fecha']);
	$tpl->assign('codgastos', $_GET['codgastos']);
	$tpl->assign('concepto', trim(strtoupper($_GET['concepto'])));
	$tpl->assign('desc', $_GET['desc']);
	$tpl->assign('importe', $_GET['importe']);
	$tpl->assign('iva', get_val($_GET['iva']) > 0 ? $_GET['iva'] : '&nbsp;');
	$tpl->assign('ret_iva', get_val($_GET['ret_iva']) > 0 ? $_GET['ret_iva'] : '&nbsp;');
	$tpl->assign('ret_isr', get_val($_GET['ret_isr']) > 0 ? $_GET['ret_isr'] : '&nbsp;');
	$tpl->assign('total', $_GET['total']);
	
	$cias = $db->query('SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN 1 AND 800 ORDER BY num_cia');
	foreach ($cias as $cia) {
		$tpl->newBlock('c');
		$tpl->assign('num_cia', $cia['num_cia']);
		$tpl->assign('nombre', $cia['nombre_corto']);
	}
	
	for ($i = 0; $i < $numfilas; $i++) {
		$tpl->newBlock('fila');
		$tpl->assign('i', $i);
		$tpl->assign('next', $i < $numfilas - 1 ? $i + 1 : 0);
		$tpl->assign('back', $i > 0 ? $i - 1 : $numfilas - 1);
	}
	
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');

$tpl->assign('fecha', date('d/m/Y'));

$result = $db->query('SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN 1 AND 899 ORDER BY num_cia');
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre_corto']);
}

$result = $db->query('SELECT num_proveedor AS num_pro, nombre FROM catalogo_proveedores ORDER BY num_proveedor');
foreach ($result as $reg) {
	$tpl->newBlock('p');
	$tpl->assign('num_pro', $reg['num_pro']);
	$tpl->assign('nombre', $reg['nombre']);
}

$result = $db->query('SELECT codgastos AS cod, descripcion AS desc FROM catalogo_gastos ORDER BY codgastos');
foreach ($result as $reg) {
	$tpl->newBlock('cod');
	$tpl->assign('cod', $reg['cod']);
	$tpl->assign('desc', $reg['desc']);
}

$tpl->printToScreen();
?>