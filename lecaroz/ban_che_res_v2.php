<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_che_res_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['num_cia'])) {
	$sql = "";
	for ($i = 0; $i < count($_POST['folio']); $i++)
		if ($_POST['folio'][$i] > 0 && $_POST['num_pro'][$i] > 0 && ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_POST['fecha'][$i]) && get_val($_POST['importe'][$i]) > 0 && $_POST['codgastos'][$i] > 0) {
			// Actualizar folio reservado como usado
			$sql .= "UPDATE folios_cheque SET utilizado = 'TRUE', fecha = '{$_POST['fecha'][$i]}' WHERE num_cia = {$_POST['num_cia'][$i]} AND folio = {$_POST['folio'][$i]} AND cuenta = $_POST[cuenta];\n";
			// Genrar datos para las tablas de cheques, estado de cuenta y gastos
			$cheque['cod_mov'] = 5;
			$cheque['num_proveedor'] = $_POST['num_pro'][$i];
			$cheque['num_cia'] = $_POST['num_cia'][$i];
			$cheque['fecha'] = $_POST['fecha'][$i];
			$cheque['folio'] = $_POST['folio'][$i];
			$cheque['importe'] = get_val($_POST['importe'][$i]);
			$cheque['iduser'] = $_SESSION['iduser'];
			$cheque['a_nombre'] = $_POST['nombre_pro'][$i];
			$cheque['imp'] = "FALSE";
			$cheque['concepto'] = strtoupper($_POST['concepto'][$i]);
			$cheque['codgastos'] = $_POST['codgastos'][$i];
			$cheque['cuenta'] = $_POST['cuenta'];
			$cheque['proceso'] = "FALSE";
			$cheque['poliza'] = 'FALSE';
			$cheque['archivo'] = $_POST['cuenta'] == 2 ? 'TRUE' : 'FALSE';
			
			$esc['num_cia'] = $_POST['num_cia'][$i];
			$esc['fecha'] = $_POST['fecha'][$i];
			$esc['tipo_mov'] = "TRUE";
			$esc['importe'] = get_val($_POST['importe'][$i]);
			$esc['cod_mov'] = 5;
			$esc['folio'] = $_POST['folio'][$i];
			$esc['concepto'] = strtoupper($_POST['concepto'][$i]);
			$esc['cuenta'] = $_POST['cuenta'];
			
			$gasto['codgastos'] = $_POST['codgastos'][$i];
			$gasto['num_cia'] = $_POST['num_cia'][$i];
			$gasto['fecha'] = $_POST['fecha'][$i];
			$gasto['importe'] = get_val($_POST['importe'][$i]);
			$gasto['captura'] = "TRUE";
			$gasto['folio'] = $_POST['folio'][$i];
			$gasto['concepto'] = strtoupper($_POST['concepto'][$i]);
			
			$sql .= $db->preparar_insert("cheques", $cheque) . ";\n";
			$sql .= $db->preparar_insert("estado_cuenta", $esc) . ";\n";
			$sql .= $db->preparar_insert("movimiento_gastos", $gasto) . ";\n";
			$sql .= "UPDATE saldos SET saldo_libros = saldo_libros - $cheque[importe] WHERE num_cia = $cheque[num_cia] AND cuenta = $_POST[cuenta];\n";
		}
	
	$db->query($sql);
	die(header('location: ./ban_che_res_v2.php'));
}

if (isset($_GET['num_cia'])) {
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_GET['mes'], 1, $_GET['anio']));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
	
	$sql = "SELECT num_cia, nombre, folio, fecha FROM folios_cheque LEFT JOIN catalogo_companias USING (num_cia) WHERE cuenta = $_GET[cuenta] AND reservado = 'TRUE' AND utilizado = 'FALSE' AND fecha BETWEEN '$fecha1' AND '$fecha2' AND num_cia BETWEEN " . ($_SESSION['iduser'] >= 28 ? '900 AND 998' : '1 AND 800');
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= " ORDER BY num_cia, folio";
	$result = $db->query($sql);
	
	if (!$result) die(header('location: ./ban_che_res_v2.php'));
	
	$tpl->newBlock('result');
	$tpl->assign('cuenta', $_GET['cuenta']);
	$tpl->assign('banco', $_GET['cuenta'] == 1 ? 'BANORTE' : 'SANTANDER');
	
	$pros = $db->query('SELECT num_proveedor AS num_pro, nombre FROM catalogo_proveedores ORDER BY num_pro');
	foreach ($pros as $pro) {
		$tpl->newBlock('p');
		$tpl->assign('num_pro', $pro['num_pro']);
		$tpl->assign('nombre', $pro['nombre']);
	}
	
	$cods = $db->query('SELECT codgastos AS cod, descripcion AS desc FROM catalogo_gastos ORDER BY cod');
	foreach ($cods as $cod) {
		$tpl->newBlock('cod');
		$tpl->assign('cod', $cod['cod']);
		$tpl->assign('desc', $cod['desc']);
	}
	
	$num_cia = NULL;
	foreach ($result as $i => $reg) {
		if ($num_cia != $reg['num_cia']) {
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock('cia');
			$tpl->assign('num_cia', $num_cia);
			$tpl->assign('nombre', $reg['nombre']);
		}
		$tpl->newBlock('folio');
		$tpl->assign('i', $i);
		$tpl->assign('index', count($result) > 1 ? "[$i]" : '');
		$tpl->assign('next', count($result) > 1 ? '[' . ($i < count($result) - 1 ? $i + 1 : 0) . ']' : '');
		$tpl->assign('back', count($result) > 1 ? '[' . ($i > 0 ? $i - 1 : count($result) - 1) . ']' : '');
		$tpl->assign('num_cia', $num_cia);
		$tpl->assign('folio', $reg['folio']);
		$tpl->assign('fecha', $reg['fecha']);
	}
	
	die($tpl->printToScreen());
}

$tpl->newBlock("datos");
$tpl->assign(date("n"), "selected");
$tpl->assign("anio", date("Y"));

$cias = $db->query("SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN " . ($_SESSION['iduser'] >= 28 ? '900 AND 998' : '1 AND 800') . " ORDER BY num_cia");
foreach ($cias as $cia) {
	$tpl->newBlock('c');
	$tpl->assign('num_cia', $cia['num_cia']);
	$tpl->assign('nombre', $cia['nombre_corto']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>