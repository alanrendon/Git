<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

$descripcion_error[1] = "El folio esta repetido";
$descripcion_error[2] = "El contrato de arrendamiento esta vencido";
$descripcion_error[3] = "El recibo para el mes dado ya ha sido registrado en el sistema";
$descripcion_error[4] = 'El folio no es el consecutivo del ultimo capturado';

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ren/ren_rec_man.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['local'])) {
	$_SESSION['rm'] = $_POST;
	
	// Validar que el número de recibo no este repetido
	$homoclave = $db->query("SELECT homoclave FROM catalogo_arrendadores WHERE cod_arrendador = $_POST[arr]");
	$sql = "SELECT rr.id FROM recibos_rentas AS rr LEFT JOIN catalogo_arrendatarios AS ca ON (ca.id = rr.local) LEFT JOIN catalogo_arrendadores USING (cod_arrendador) WHERE num_recibo = $_POST[recibo] AND homoclave = {$homoclave[0]['homoclave']}";
	$result = $db->query($sql);
	
	if ($result) {
		header("location: ./ren_rec_man.php?codigo_error=1");
		die;
	}
	
	// [01-Oct-2009] Validar que no se salten folios
	$sql = 'SELECT num_recibo FROM recibos_rentas rr LEFT JOIN catalogo_arrendatarios ca ON (ca.id = rr.local) LEFT JOIN catalogo_arrendadores USING (cod_arrendador) WHERE homoclave = ' . $homoclave[0]['homoclave'] . ' ORDER BY num_recibo DESC LIMIT 1';
	$result = $db->query($sql);
	
	if ($result && $_POST['recibo'] - $result[0]['num_recibo'] > 1) {
		header('location: ./ren_rec_man.php?codigo_error=4');
		die;
	}
	
	$fecha = "01/$_POST[mes]/$_POST[anio]";
	
	// [21-Sep-2007] Validar que el recibo no se haga 2 veces en el mes al menos que sea una diferencia menor al 10% de la renta
	$sql = "SELECT rr.id, rr.renta FROM recibos_rentas AS rr LEFT JOIN catalogo_arrendatarios AS ca ON (ca.id = rr.local) LEFT JOIN catalogo_arrendadores USING (cod_arrendador) WHERE fecha = '$fecha' AND local = $_POST[id] AND rr.status = 1 AND ca.bloque <> 1";
	$result = $db->query($sql);
	if (!in_array($_SESSION['iduser'], array(/*1,*/ 4, 27)) && $result && get_val($_POST['importe_renta']) > round($result[0]['renta'] * 0.10, 2))
		die(header('location: ./ren_rec_man.php?codigo_error=3'));
	
	// [27-Jun-2007] Validar vencimiento de contrato
	// [21-Sep-2007] Modificado para que tome la fecha del recibo para validar el vencimiento
	if (!in_array($_SESSION['iduser'], array(1, 4, 27)) && $_POST['bloque'] == 'EXTERNO') {
		$dia = 1;
		$mes = $_POST['mes'];
		$anio = $_POST['anio'];
		$fecha1 = date("d/m/Y", mktime(0, 0, 0, $mes, 1, $anio - 1));
		
		$r1 = $db->query("SELECT r.fecha, r.renta, r.mantenimiento FROM recibos_rentas r LEFT JOIN catalogo_arrendatarios c ON (c.id = r.local) WHERE r.local = $_POST[id] AND r.fecha = '$fecha1' AND r.status = 1 AND c.incremento_anual = 'TRUE' AND r.fecha >= c.fecha_inicio ORDER BY r.fecha LIMIT 1");
		
		if ($r1 && get_val($_POST['importe_renta']) <= $r1[0]['renta'] && get_val($_POST['importe_mant']) <= $r1[0]['mantenimiento'])
			die(header('location: ./ren_rec_man.php?codigo_error=2'));
	}
	
	$sql = "INSERT INTO recibos_rentas (num_recibo, renta, agua, mantenimiento, iva, isr_retenido, iva_retenido, neto, fecha, bloque, impreso, fecha_pago, concepto, local, status) VALUES (";
	$sql .= "$_POST[recibo], ";
	$sql .= $_POST['importe_renta'] != "" ? floatval(str_replace(",", "", $_POST['importe_renta'])) . ", " : "0, ";
	$sql .= $_POST['importe_agua'] != "" ? floatval(str_replace(",", "", $_POST['importe_agua'])) . ", " : "0, ";
	$sql .= $_POST['importe_mant'] != "" ? floatval(str_replace(",", "", $_POST['importe_mant'])) . ", " : "0, ";
	$sql .= $_POST['importe_iva'] != "" ? floatval(str_replace(",", "", $_POST['importe_iva'])) . ", " : "0, ";
	$sql .= $_POST['importe_ret_isr'] != "" ? floatval(str_replace(",", "", $_POST['importe_ret_isr'])) . ", " : "0, ";
	$sql .= $_POST['importe_ret_iva'] != "" ? floatval(str_replace(",", "", $_POST['importe_ret_iva'])) . ", " : "0, ";
	$sql .= $_POST['total'] != "" ? floatval(str_replace(",", "", $_POST['total'])) . ", " : "0, ";
	$sql .= "'$fecha',";
	$sql .= $_POST['bloque'] == "INTERNO" ? " 1," : " 2,";
	$sql .= " 'FALSE', '$fecha', '" . trim(strtoupper($_POST['concepto'])) . "', $_POST[id], 1)";
	$db->query($sql);
	
	$tpl->newBlock("popup");
	$tpl->assign("arr", $_POST['arr']);
	$tpl->assign("ini", $_POST['recibo']);
	$tpl->assign("fin", $_POST['recibo']);
	$tpl->printToScreen();
	unset($_SESSION['rm']);
	die;
}

$tpl->newBlock("datos");
if (!isset($_SESSION['rm'])) {
	$tpl->assign(date("n"), "selected");
	$tpl->assign("anio", date("Y"));
	$tpl->assign("color", "transparent");
}
else {
	$tpl->assign("id", $_SESSION['rm']['id']);
	$tpl->assign("arr", $_SESSION['rm']['arr']);
	$tpl->assign("local", $_SESSION['rm']['local']);
	$tpl->assign("nombre_local", $_SESSION['rm']['nombre_local']);
	$tpl->assign("arrendador", $_SESSION['rm']['arrendador']);
	$tpl->assign("arrendatario", $_SESSION['rm']['arrendatario']);
	$tpl->assign("bloque", $_SESSION['rm']['bloque']);
	$tpl->assign('tipo_local', $_SESSION['rm']['tipo_local']);
	$tpl->assign("recibo", $_SESSION['rm']['recibo']);
	$tpl->assign($_SESSION['rm']['mes'], "selected");
	$tpl->assign("anio", $_SESSION['rm']['anio']);
	$tpl->assign("meses", $_SESSION['rm']['meses']);
	$tpl->assign("concepto", $_SESSION['rm']['concepto']);
	$tpl->assign("importe_renta", $_SESSION['rm']['importe_renta']);
	$tpl->assign("importe_mant", $_SESSION['rm']['importe_mant']);
	$tpl->assign("subtotal", $_SESSION['rm']['subtotal']);
	$tpl->assign("importe_iva", $_SESSION['rm']['importe_iva']);
	$tpl->assign("importe_agua", $_SESSION['rm']['importe_agua']);
	$tpl->assign("importe_ret_iva", $_SESSION['rm']['importe_ret_iva']);
	$tpl->assign("importe_ret_isr", $_SESSION['rm']['importe_ret_isr']);
	$tpl->assign("total", $_SESSION['rm']['total']);
	$tpl->assign("color", $_GET['codigo_error'] == 1 ? "FFCC00" : 'transparent');
	unset($_SESSION['rm']);
}

$sql = "SELECT ca.id AS idlocal, homoclave AS arr, num_local, nombre_local, nombre, nombre_arrendatario, renta_con_recibo AS renta, mantenimiento AS mant, agua, retencion_isr,";
$sql .= " retencion_iva, bloque, tipo_local FROM catalogo_arrendatarios AS ca LEFT JOIN catalogo_arrendadores USING (cod_arrendador) WHERE status = 1 ORDER BY num_local";
$result = $db->query($sql);
foreach ($result as $reg) {
	$tpl->newBlock("local");
	$tpl->assign("local", $reg['num_local']);
	$tpl->assign("nombre_local", $reg['nombre_local']);
	$tpl->assign("arrendador", $reg['nombre']);
	$tpl->assign("arrendatario", $reg['nombre_arrendatario']);
	$tpl->assign("renta", $reg['renta'] != 0 ? $reg['renta'] : 0);
	$tpl->assign("mant", $reg['mant'] != 0 ? $reg['mant'] : 0);
	$tpl->assign("agua", $reg['agua'] != 0 ? $reg['agua'] : 0);
	$tpl->assign("ret_iva", $reg['retencion_iva'] == "t" ? "true" : "false");
	$tpl->assign("ret_isr", $reg['retencion_isr'] == "t" ? "true" : "false");
	$tpl->assign("bloque", $reg['bloque'] == 1 ? "INTERNO" : "EXTERNO");
	$tpl->assign("id", $reg['idlocal']);
	$tpl->assign("arr", $reg['arr']);
	$tpl->assign('tipo_local', $reg['tipo_local']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>
