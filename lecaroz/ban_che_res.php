<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay folios reservados para la compañía seleccionada";

$db = new DBclass($dsn, "autocommit=yes");

if (isset($_POST['num_cia'])) {
	$sql = "";
	for ($i = 0; $i < count($_POST['folio']); $i++)
		if ($_POST['folio'][$i] > 0 && $_POST['num_proveedor'][$i] > 0 && ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_POST['fecha'][$i]) && $_POST['importe'][$i] > 0 && $_POST['codgastos'][$i] > 0) {
			// Actualizar folio reservado como usado
			$sql .= "UPDATE folios_cheque SET utilizado = 'TRUE', fecha = '{$_POST['fecha'][$i]}' WHERE num_cia = $_POST[num_cia] AND folio = {$_POST['folio'][$i]};\n";
			// Genrar datos para las tablas de cheques, estado de cuenta y gastos
			$cheque['cod_mov'] = 5;
			$cheque['num_proveedor'] = $_POST['num_proveedor'][$i];
			$cheque['num_cia'] = $_POST['num_cia'];
			$cheque['fecha'] = $_POST['fecha'][$i];
			$cheque['folio'] = $_POST['folio'][$i];
			$cheque['importe'] = $_POST['importe'][$i];
			$cheque['iduser'] = $_SESSION['iduser'];
			$cheque['a_nombre'] = $_POST['nombre_proveedor'][$i];
			$cheque['imp'] = "FALSE";
			$cheque['concepto'] = strtoupper($_POST['concepto'][$i]);
			$cheque['codgastos'] = $_POST['codgastos'][$i];
			$cheque['proceso'] = "FALSE";
			
			$esc['num_cia'] = $_POST['num_cia'];
			$esc['fecha'] = date("d/m/Y");
			$esc['tipo_mov'] = "TRUE";
			$esc['importe'] = $_POST['importe'][$i];
			$esc['cod_mov'] = 5;
			$esc['folio'] = $_POST['folio'][$i];
			$esc['concepto'] = strtoupper($_POST['concepto'][$i]);
			
			$gasto['codgastos'] = $_POST['codgastos'][$i];
			$gasto['num_cia'] = $_POST['num_cia'];
			$gasto['fecha'] = date("d/m/Y");
			$gasto['importe'] = $_POST['importe'][$i];
			$gasto['captura'] = "TRUE";
			$gasto['folio'] = $_POST['folio'][$i];
			$gasto['concepto'] = strtoupper($_POST['concepto'][$i]);
			
			$sql .= $db->preparar_insert("cheques", $cheque) . ";\n";
			$sql .= $db->preparar_insert("estado_cuenta", $esc) . ";\n";
			$sql .= $db->preparar_insert("movimiento_gastos", $gasto) . ";\n";
		}
	
	echo $sql;
	//$db->query($sql);
	$db->desconectar();
	
	//header("location: ./ban_che_res.php");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_che_res.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
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
	$db->desconectar();
	die;
}

$sql = "SELECT * FROM folios_cheque WHERE num_cia = $_GET[num_cia] AND reservado = 'TRUE' AND utilizado = 'FALSE' ORDER BY folio";
$folio = $db->query($sql);

if (!$folio) {
	$db->desconectar();
	header("location: ./ban_che_res.php?codigo_error=1");
	die;
}

$tpl->newBlock("captura");
$sql = "SELECT nombre FROM catalogo_companias WHERE num_cia = $_GET[num_cia]";
$nombre_cia = $db->query($sql);
$tpl->assign("num_cia", $_GET['num_cia']);
$tpl->assign("nombre_cia", $nombre_cia[0]['nombre']);

// Obtener proveedores
$sql = "SELECT num_proveedor, nombre FROM catalogo_proveedores ORDER BY num_proveedor";
$pro = $db->query($sql);
for ($i = 0; $i < count($pro); $i++) {
	$tpl->newBlock("proveedor");
	$tpl->assign("num_proveedor", $pro[$i]['num_proveedor']);
	$tpl->assign("nombre_proveedor", $pro[$i]['nombre']);
}

// Obtener gastos
$sql = "SELECT codgastos, descripcion FROM catalogo_gastos ORDER BY codgastos";
$gas = $db->query($sql);
for ($i = 0; $i < count($gas); $i++) {
	$tpl->newBlock("gasto");
	$tpl->assign("codgastos", $gas[$i]['codgastos']);
	$tpl->assign("nombre_gasto", $gas[$i]['descripcion']);
}

$numfilas = count($folio) < 20 ? count($folio) : 20;

for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
	
	for ($j = 0; $j < count($folio); $j++) {
		$tpl->newBlock("folio");
		$tpl->assign("folio", $folio[$j]['folio']);
	}
}

$tpl->printToScreen();
$db->desconectar();
?>