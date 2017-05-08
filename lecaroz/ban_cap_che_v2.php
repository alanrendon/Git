<?php
// DIFERENCIAS DE SALDOS
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/cheques.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$users = array(28, 29, 30, 31);
$numfilas = 20;

// --------------------------------- Descripción de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_cap_che_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['num_cia'])) {
	// Organizar datos
	$cont = 0;
	for ($i = 0; $i < $numfilas; $i++) {
		$importe = str_replace(",", "", $_POST['importe'][$i]);
		if ($_POST['num_cia'][$i] > 0 && $_POST['num_pro'][$i] > 0 && $_POST['folio'][$i] > 0 && $_POST['codgastos'][$i] > 0 && $importe > 0) {
			$data[$cont]['num_cia'] = $_POST['num_cia'][$i];
			$data[$cont]['nombre_cia'] = $_POST['nombre_cia'][$i];
			$data[$cont]['num_proveedor'] = $_POST['num_pro'][$i];
			$data[$cont]['a_nombre'] = $_POST['nombre_pro'][$i];
			$data[$cont]['fecha'] = $_POST['fecha'][$i];
			$tmp = $db->query("SELECT descripcion FROM catalogo_gastos WHERE codgastos = {$_POST['codgastos'][$i]}");
			$concepto = trim($_POST['concepto'][$i]) != "" ? trim(str_replace(array("\"", "'"), "", strtoupper($_POST['concepto'][$i]))) : $tmp[0]['descripcion'];
			$data[$cont]['concepto'] = $concepto;
			$data[$cont]['codgastos'] = $_POST['codgastos'][$i];
			$data[$cont]['nombre_gas'] = $_POST['nombre_gas'][$i];
			$data[$cont]['importe'] = $importe;
			$data[$cont]['cuenta'] = $_POST['cuenta'];
			
			$fac = "";
			$fac_ok = FALSE;
			for ($f = 0; $f < 10; $f++)
				if ($_POST['num_fact' . $f][$i] > 0) {
					$fac .= (!$fac_ok ? "" : " ") . fillZero($_POST['num_fact' . $f][$i], 7);
					$fac_ok = TRUE;
				}
			$data[$cont]['facturas'] = $fac;
			
			$cont++;
		}
	}
	
	if ($cont == 0) {
		header("location: ./ban_cap_che.php");
		die;
	}
	
	function cmp($a, $b) {
	}
	
	// Validar folios
	$cont = 0;
	$saldos = '';
	foreach ($data as $i => $reg)
		if ($db->query("SELECT id FROM cheques WHERE num_cia = $reg[num_cia] AND folio = $reg[folio] AND cuenta = $reg[cuenta]"))
			$error[] = $i;
		else {
			$cheque[$cont] = $reg;
			$cheque[$cont]['tipo_mov'] = "FALSE";
			$cheque[$cont]['proceso'] = "FALSE";
			$cheque[$cont]['iduser'] = $_SESSION['iduser'];
			$cheque[$cont]['archivo'] = "FALSE";
			$cheque[$cont]['poliza'] = "FALSE";
			$cheque[$cont]['tipo_mov'] = "TRUE";
			$cheque[$cont]['cod_mov'] = 5;
			$cheque[$cont]['imp'] = 'TRUE';
			
			$gasto[$cont] = $cheque;
			$gastos[$cont]['captura'] = "TRUE";
			
			$esc[$cont] = $cheque[$cont];
			$esc[$cont]['concepto'] = trim($cheque[$cont]['facturas']) != "" ? $cheque[$cont]['facturas'] : $cheque[$cont]['concepto'];
			
			$saldos .= "UPDATE saldos SET saldo_libros = saldo_libros - {$cheque[$cont]['importe']} WHERE num_cia = {$cheque[$cont]['num_cia']} AND cuenta = $_POST[cuenta];\n";
			
			$cont++;
		}
	
	if (isset($cheque)) {
		$db->query($db->multiple_insert("cheques", $cheque));
		$db->query($db->multiple_insert("movimiento_gastos", $gasto));
		$db->query($db->multiple_insert("estado_cuenta", $esc));
		$db->query($saldos);
	}
	
	if (isset($error)) {
		$tpl->newBlock("errores");
		foreach ($error as $i) {
			$tpl->newBlock("error");
			$tpl->assign("num_cia", $data[$i]['num_cia']);
			$tpl->assign("nombre_cia", $data[$i]['nombre_cia']);
			$tpl->assign("folio", $data[$i]['folio']);
			$tpl->assign("num_pro", $data[$i]['num_proveedor']);
			$tpl->assign("nombre_pro", $data[$i]['a_nombre']);
			$tpl->assign("importe", number_format($data[$i]['importe'], 2, ".", ","));
		}
		$tpl->printToScreen();
	}
	else
		header("location: ./ban_cap_che.php");
	die;
}

$tpl->newBlock("captura");

for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
	$tpl->assign("fecha", date("d/m/Y"));
}

$cias = $db->query("SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN 900 AND 950 ORDER BY num_cia");
foreach ($cias as $cia) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia['num_cia']);
	$tpl->assign("nombre", $cia['nombre_corto']);
}

$pros = $db->query("SELECT num_proveedor AS num_pro, nombre FROM catalogo_proveedores ORDER BY num_pro");
foreach ($pros as $pro) {
	$tpl->newBlock("pro");
	$tpl->assign("num_pro", $pro['num_pro']);
	$tpl->assign("nombre", $pro['nombre']);
}

$gas = $db->query("SELECT codgastos, descripcion FROM catalogo_gastos ORDER BY codgastos");
foreach ($gas as $g) {
	$tpl->newBlock("gas");
	$tpl->assign("cod", $g['codgastos']);
	$tpl->assign("nombre", $g['descripcion']);
}

$tpl->printToScreen();
?>