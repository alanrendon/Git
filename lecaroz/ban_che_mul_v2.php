<?php
// CAPTURA DE CHEQUE MANUAL
// Tablas 'folios_cheque, cheques, facturas, facturas_pagadas, estado_cuenta'
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

//if (!in_array($_SESSION['iduser'], array(1))) die('Modificando programa');

$users = array(28, 29, 30, 31, 32);

// VARIABLES GLOBALES
$numfilas = 125;

// --------------------------------- Insertar datos a la base ------------------------------------------------
if (isset($_POST['fecha'])) {
	$sql = "";
	$cont = 0;
	$concepto = strtoupper($_POST['concepto']);
	$num_cia = NULL;
	for ($i=0; $i < $numfilas; $i++) {
		$importe = floatval(str_replace(",", "", $_POST['importe'][$i]));

		if ($_POST['num_cia'][$i] > 0 && $_POST['num_pro'][$i] > 0 && $importe > 0) {
			if ($num_cia != $_POST['num_cia'][$i]) {
				$num_cia = $_POST['num_cia'][$i];

				if (!isset($folio_cheque[$_POST['num_cia'][$i]])) {
					$result = $db->query("SELECT folio FROM folios_cheque WHERE num_cia = {$_POST['num_cia'][$i]} AND cuenta = $_POST[cuenta] ORDER BY folio DESC LIMIT 1");
					$folio_cheque[$num_cia] = $result ? $result[0]['folio'] + 1 : 51;
				}
			}

			// Actualizar saldo en libros
			if ($id = $db->query("SELECT id FROM saldos WHERE num_cia = {$_POST['num_cia'][$i]} AND cuenta = $_POST[cuenta]"))
				$sql .= "UPDATE saldos SET saldo_libros = saldo_libros - $importe WHERE id = {$id[0]['id']};\n";

			// Ordenar datos para cheques
			$cheque[$cont]['cod_mov'] = isset($_POST['trans']) ? 41 : 5;
			$cheque[$cont]['codgastos'] = $_POST['codgastos'];
			$cheque[$cont]['num_proveedor'] = $_POST['num_pro'][$i];
			$cheque[$cont]['num_cia'] = $_POST['num_cia'][$i];
			$cheque[$cont]['a_nombre'] = $_POST['nombre_pro'][$i];
			$cheque[$cont]['concepto'] = $concepto;
			$cheque[$cont]['fecha'] = $_POST['fecha'];
			$cheque[$cont]['folio'] = $folio_cheque[$num_cia];
			$cheque[$cont]['importe'] = $importe;
			$cheque[$cont]['iduser'] = $_SESSION['iduser'];
			$cheque[$cont]['imp'] = "FALSE";
			$cheque[$cont]['cuenta'] = $_POST['cuenta'];
			$cheque[$cont]['poliza'] = isset($_POST['poliza']) || isset($_POST['trans']) ? "TRUE" : "FALSE";
			$cheque[$cont]['acuenta'] = isset($_POST['acuenta']) ? 'TRUE' : 'FALSE';

			$cuenta[$cont]['num_cia'] = $_POST['num_cia'][$i];
			$cuenta[$cont]['fecha'] = $_POST['fecha'];
			$cuenta[$cont]['fecha_con'] = "";
			$cuenta[$cont]['concepto'] = $concepto;
			$cuenta[$cont]['tipo_mov'] = "TRUE";
			$cuenta[$cont]['importe'] = $importe;
			$cuenta[$cont]['cod_mov'] = isset($_POST['trans']) ? 41 : 5;
			$cuenta[$cont]['folio'] = $folio_cheque[$num_cia];
			$cuenta[$cont]['cuenta'] = $_POST['cuenta'];

			if (isset($_POST['trans']) && $_POST['cuenta'] == 2) {
				$transfer[$cont]['num_cia']       = $_POST['num_cia'][$i];
				$transfer[$cont]['num_proveedor'] = $_POST['num_pro'][$i];
				$transfer[$cont]['folio']         = $folio_cheque[$num_cia];
				$transfer[$cont]['importe']       = number_format($importe,2,".","");
				$transfer[$cont]['fecha_gen']     = $_POST['fecha'];
				$transfer[$cont]['tipo']          = "FALSE";
				$transfer[$cont]['status']        = "0";
				$transfer[$cont]['folio_archivo'] = 0;
				$transfer[$cont]['cuenta']        = 2;
				$transfer[$cont]['concepto']      = $concepto;
				$transfer[$cont]['gen_dep']       = 'TRUE';
			}
			else if (isset($_POST['trans']) && $_POST['cuenta'] == 1) {
				$transfer[$cont]['num_cia']       = $_POST['num_cia'][$i];
				$transfer[$cont]['num_proveedor'] = $_POST['num_pro'][$i];
				$transfer[$cont]['folio']         = $folio_cheque[$num_cia];
				$transfer[$cont]['importe']       = number_format($importe,2,".","");
				$transfer[$cont]['fecha_gen']     = $_POST['fecha'];
				$transfer[$cont]['tipo']          = "FALSE";
				$transfer[$cont]['status']        = "0";
				$transfer[$cont]['folio_archivo'] = 0;
				$transfer[$cont]['cuenta']        = 1;
				$transfer[$cont]['concepto']      = $concepto;
				$transfer[$cont]['gen_dep']       = 'TRUE';
			}

			// Ordenar datos para folios_cheque
			$folio[$cont]['folio'] = $folio_cheque[$num_cia];
			$folio[$cont]['num_cia'] = $_POST['num_cia'][$i];
			$folio[$cont]['reservado'] = "FALSE";
			$folio[$cont]['utilizado'] = "TRUE";
			$folio[$cont]['fecha'] = $_POST['fecha'];
			$folio[$cont]['cuenta'] = $_POST['cuenta'];

			// Ordenar datos para gastos
			$gasto[$cont]['num_cia'] = $_POST['num_cia'][$i];
			$gasto[$cont]['codgastos'] = $_POST['codgastos'];
			$gasto[$cont]['fecha'] = $_POST['fecha'];
			$gasto[$cont]['importe'] = $importe;
			$gasto[$cont]['concepto'] = $concepto;
			$gasto[$cont]['captura'] = "TRUE";
			$gasto[$cont]['folio'] = $folio_cheque[$num_cia];

			$folio_cheque[$num_cia]++;
			$cont++;
		}
	}

	if ($cont > 0) {
		$sql .= $db->multiple_insert("cheques", $cheque);
		$sql .= $db->multiple_insert("estado_cuenta", $cuenta);
		$sql .= $db->multiple_insert('transferencias_electronicas', $transfer);
		$sql .= $db->multiple_insert("folios_cheque", $folio);
		$sql .= $db->multiple_insert("movimiento_gastos", $gasto);
	}

	if ($sql != "")
		$db->query($sql);
	//echo '<pre>' . print_r($cuenta, TRUE) . '</pre>';
	//echo "<pre>$sql</pre>";die;

	header("location: ./ban_che_mul_v2.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_che_mul_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign("fecha",date("d/m/Y"));

// Generar listado de compañías
$cias = $db->query("SELECT num_cia,catalogo_companias.nombre AS nombre_cia, num_proveedor AS num_pro, catalogo_proveedores.nombre AS nombre_pro FROM catalogo_companias LEFT JOIN catalogo_proveedores USING (num_proveedor)" . ($_SESSION['iduser'] != 1 ? ' WHERE num_cia BETWEEN ' . (in_array($_SESSION['iduser'], $users) ? '900 AND 998' : '1 AND 899') : '') . " ORDER BY num_cia ASC");
foreach ($cias as $cia) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia['num_cia']);
	$tpl->assign("nombre_cia", $cia['nombre_cia']);
	$tpl->assign("num_pro", $cia['num_pro'] > 0 ? $cia['num_pro'] : "null");
	$tpl->assign("nombre_pro", $cia['nombre_pro']);
}

$saldos = $db->query("SELECT num_cia, saldo_libros, cuenta FROM saldos" . ($_SESSION['iduser'] != 1 ? ' WHERE num_cia BETWEEN ' . (in_array($_SESSION['iduser'], $users) ? '900 AND 998' : '1 AND 899') : '') . " ORDER BY num_cia");
foreach ($saldos as $saldo) {
	$tpl->newBlock("saldo" . $saldo['cuenta']);
	$tpl->assign("num_cia", $saldo['num_cia']);
	$tpl->assign("saldo", number_format($saldo['saldo_libros'], 2, ".", ","));
}

// Generar listado de proveedores
$pros = $db->query("SELECT num_proveedor AS num_pro, nombre FROM catalogo_proveedores ORDER BY num_proveedor ASC");
foreach ($pros as $pro) {
	$tpl->newBlock("pro");
	$tpl->assign("num_pro", $pro['num_pro']);
	$tpl->assign("nombre", str_replace(array("\""), array(""), $pro['nombre']));
}

// Generar listado de gastos
$gastos = $db->query("SELECT codgastos, descripcion FROM catalogo_gastos ORDER BY codgastos ASC");
foreach ($gastos as $gasto) {
	$tpl->newBlock("gasto");
	$tpl->assign("codgastos", $gasto['codgastos']);
	$tpl->assign("nombre", $gasto['descripcion']);
}

for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message",$descripcion_error[$_GET['codigo_error']]);
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
die;
?>
