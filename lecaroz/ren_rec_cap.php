<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ren/ren_rec_cap.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['cancel'])) {
	if (isset($_SESSION['rr']))
		unset($_SESSION['rr']);
	
	header("location: ./ren_rec_cap.php");
	die;
}

if (isset($_GET['imp']) && isset($_SESSION['rr'])) {
	$fecha = "01/{$_SESSION['rr']['mes']}/{$_SESSION['rr']['anio']}";
	
	
	// Obtener arrendadores con folio capturado
	$folio = array();
	for ($i = 0; $i < count($_SESSION['rr']['cod']); $i++)
		if ($_SESSION['rr']['folio'][$i] > 0)
			$folio[$_SESSION['rr']['cod'][$i]] = $_SESSION['rr']['folio'][$i];
	
	// Obtener locales
	//$sql = "SELECT cod_arrendador, ca.nombre AS nombre_arrendador, num_local,  num_arrendatario, nombre_arrendatario, renta_con_recibo AS renta, agua, mantenimiento, retencion_iva,";
	//$sql .= " retencion_isr, bloque FROM catalogo_locales AS cl LEFT JOIN catalogo_arrendadores AS ca USING (cod_arrendador) LEFT JOIN catalogo_arrendatarios AS cb USING (num_local)";
	//$sql .= " WHERE recibo_mensual = 'TRUE' AND cod_arrendador IN (";
	$sql = "SELECT ca.id AS local, cod_arrendador, nombre AS nombre_arrendador, nombre_arrendatario, renta_con_recibo AS renta, agua, mantenimiento, retencion_iva, retencion_isr,";
	$sql .= " bloque, homoclave, tipo_local FROM catalogo_arrendatarios AS ca LEFT JOIN catalogo_arrendadores USING (cod_arrendador) WHERE recibo_mensual = 'TRUE' AND status = 1 AND renta_con_recibo > 0 AND homoclave IN (" . implode(', ', array_keys($folio)) . ") AND ca.id NOT IN (SELECT local FROM recibos_rentas WHERE fecha = '" . date('d/m/Y', mktime(0, 0, 0, $_SESSION['rr']['mes'], 1, $_SESSION['rr']['anio'])) . "')";
//	foreach (array_keys($folio) as $i => $cod)
//		$sql .= $cod . ($i < count(array_keys($folio)) - 1 ? ", " : ")");
	$sql .= " ORDER BY homoclave, cod_arrendador, bloque, ca.num_local";
	$result = $db->query($sql);
	
	$cod_arr = NULL;
	$cont = 0;
	foreach ($result as $reg) {
		if ($cod_arr != $reg[/*'cod_arrendador'*/'homoclave']) {
			$cod_arr = $reg[/*'cod_arrendador'*/'homoclave'];
			$arr[$reg['homoclave']]['nombre'] = $reg['nombre_arrendador'];
			$arr[$reg['homoclave']]['ini'] = $folio[$reg['homoclave']];
		}
		$renta = $reg['renta'];
		$mant = $reg['mantenimiento'];
		$agua = $reg['agua'];
		$subtotal = $renta + $mant;
		$iva = $reg['tipo_local'] == 1 ? round($subtotal * /*0.15*/0.16, 2) : 0;
		$isr = $reg['retencion_isr'] == "t" ? round(/*$renta*/$subtotal * 0.10, 2) : 0;
		$ret = $reg['retencion_iva'] == "t" ? round(/*$renta*/$subtotal * /*0.10*/0.1066, 2) : 0;
		$total = $subtotal + $iva + $agua - $isr - $ret;
		
		$data[$cont]['num_recibo'] = /*$folio[$cod_arr]*/$folio[$reg['homoclave']];
		$data[$cont]['renta'] = $renta;
		$data[$cont]['agua'] = $agua;
		$data[$cont]['mantenimiento'] = $mant;
		$data[$cont]['iva'] = $iva;
		$data[$cont]['isr_retenido'] = $isr;
		$data[$cont]['iva_retenido'] = $ret;
		$data[$cont]['neto'] = $total;
		$data[$cont]['fecha'] = $fecha;
		//$data[$cont]['cod_arrendador'] = /*$cod_arr*/$reg['cod_arrendador'];
		$data[$cont]['bloque'] = $reg['bloque'];
		$data[$cont]['impreso'] = "FALSE";
		$data[$cont]['fecha_pago'] = $fecha;
		$data[$cont]['local'] = $reg['local'];
		$data[$cont]['status'] = 1;
		$data[$cont]['concepto'] = mes_escrito($_SESSION['rr']['mes'], TRUE) . ' ' . $_SESSION['rr']['anio'];
		$cont++;
		
		$arr[$reg['homoclave']]['fin'] = $folio[$reg['homoclave']]++;
	}
	$sql = $db->multiple_insert("recibos_rentas", $data);
	$db->query($sql);
	
	ksort($arr);
	
	$tpl->newBlock("impresion");
	$tpl->assign("mes_escrito", mes_escrito($_SESSION['rr']['mes']));
	$tpl->assign("anio", $_SESSION['rr']['anio']);
	foreach ($arr as $cod => $reg) {
		$tpl->newBlock("imp");
		$tpl->assign("cod", $cod);
		$tpl->assign("nombre", $reg['nombre']);
		$tpl->assign("folio1", $reg['ini']);
		$tpl->assign("folio2", $reg['fin'] > $reg['ini'] ? " al $reg[fin]" : "");
		$tpl->assign("ini", $reg['ini']);
		$tpl->assign("fin", $reg['fin']);
		$tpl->assign("cantidad", $reg['fin'] - $reg['ini'] + 1);
	}
	$tpl->printToScreen();
	unset($_SESSION['rr']);
	die;
}

if (isset($_POST['mes'])) {
	// Almacenar datos temporalmente
	$_SESSION['rr'] = $_POST;
	
	// Obtener arrendadores con folio capturado
	$folio = array();
	for ($i = 0; $i < count($_POST['cod']); $i++)
		if ($_POST['folio'][$i] > 0)
			$folio[$_POST['cod'][$i]] = $_POST['folio'][$i];
	
	if (count($folio) == 0) {
		header("location: ./ren_rec_cap.php");
		die;
	}
	
	// Obtener locales
	$sql = "SELECT cart.id, num_local, cod_arrendador, nombre AS nombre_arrendador, nombre_arrendatario, renta_con_recibo AS renta, agua, mantenimiento, retencion_iva, retencion_isr,";
	$sql .= " bloque, homoclave, fecha_inicio, fecha_final FROM catalogo_arrendatarios AS cart LEFT JOIN catalogo_arrendadores AS carr USING (cod_arrendador) WHERE recibo_mensual = 'TRUE' AND status = 1 AND renta_con_recibo > 0 AND homoclave IN (" . implode(', ', array_keys($folio)) . ") AND cart.id NOT IN (SELECT local FROM recibos_rentas WHERE fecha = '" . date('d/m/Y', mktime(0, 0, 0, $_POST['mes'], 1, $_POST['anio'])) . "')";
//	foreach (array_keys($folio) as $i => $cod)
//		$sql .= $cod . ($i < count(array_keys($folio)) - 1 ? ", " : ")");
	$sql .= " ORDER BY homoclave, cod_arrendador, bloque, num_local";
	$result = $db->query($sql);
	
	// [27-Jun-2007] Validar contratos vencidos
	$dia = 1;
	$mes = $_POST['mes'];
	$anio = $_POST['anio'];
	$fecha1 = date("d/m/Y", mktime(0, 0, 0, $mes, 1, $anio - 1));
	$fecha2 = date("d/m/Y", mktime(0, 0, 0, $mes, 1, $anio));
	$arr = array();
	foreach ($result as $reg)
		if ($reg['bloque'] == 2) {
			$r1 = $db->query("SELECT r.fecha, r.renta, r.mantenimiento FROM recibos_rentas r LEFT JOIN catalogo_arrendatarios c ON (c.id = r.local) WHERE r.local = $reg[id] AND r.fecha = '$fecha1' AND r.status = 1 AND c.incremento_anual = 'TRUE' AND r.fecha >= c.fecha_inicio AND c.renta_con_recibo > 0 ORDER BY fecha LIMIT 1");
			
			if ($r1 && $reg['renta'] <= $r1[0]['renta'])
				$arr[] = $reg;
		}
	
//	if (count($arr) > 0) {
//		$tpl->newBlock('vencidos');
//		foreach ($arr as $reg) {
//			$tpl->newBlock('fila_ven');
//			$tpl->assign('num', $reg['num_local']);
//			$tpl->assign('nombre', $reg['nombre_arrendatario']);
//			$tpl->assign('arr', $reg['nombre_arrendador']);
//			$tpl->assign('renta', number_format($reg['renta'], 2, '.', ','));
//			$tpl->assign('fecha_ven', $reg['fecha_inicio']);
//		}
//		$tpl->printToScreen();
//		unset($_SESSION['rr']);
//		die;
//	}
	
	//****************************************
	
	$tpl->newBlock("listado");
	$tpl->assign("mes", $_POST['mes']);
	$tpl->assign("mes_escrito", mes_escrito($_POST['mes']));
	$tpl->assign("anio", $_POST['anio']);
	
	$cod_arr = NULL;
	foreach ($result as $reg) {
		if ($cod_arr != $reg['cod_arrendador']) {
			$cod_arr = $reg['cod_arrendador'];
			
			$tpl->newBlock("arrendador");
			$tpl->assign("cod", $cod_arr);
			$tpl->assign("nombre", $reg['nombre_arrendador']);
			//$recibo = $folio[$cod_arr];
		}
		$tpl->newBlock("recibo");
		$tpl->assign("recibo", /*$recibo++*/$folio[$reg['homoclave']]++);
		$tpl->assign("bloque", $reg['bloque'] == 1 ? "I" : "E");
		$tpl->assign("local", $reg['num_local']);
		$tpl->assign("nombre", $reg['nombre_arrendatario']);
		
		$renta = $reg['renta'];
		$mant = $reg['mantenimiento'];
		$agua = $reg['agua'];
		$subtotal = $renta + $mant;
		$iva = round($subtotal * /*0.15*/0.16, 2);
		$isr = $reg['retencion_isr'] == "t" ? round($renta * 0.10, 2) : 0;
		$ret = $reg['retencion_iva'] == "t" ? round($renta * /*0.10*/0.1066, 2) : 0;
		$total = $subtotal + $iva + $agua - $isr - $ret;
		
		$tpl->assign("renta", number_format($renta, 2, ".", ","));
		$tpl->assign("agua", $agua != 0 ? number_format($agua, 2, ".", ",") : "&nbsp;");
		$tpl->assign("mant", $mant != 0 ? number_format($mant, 2, ".", ",") : "&nbsp;");
		$tpl->assign("iva", $iva != 0 ? number_format($iva, 2, ".", ",") : "&nbsp;");
		$tpl->assign("isr", $isr != 0 ? number_format($isr, 2, ".", ",") : "&nbsp;");
		$tpl->assign("ret", $ret != 0 ? number_format($ret, 2, ".", ",") : "&nbsp;");
		$tpl->assign("neto", number_format($total, 2, ".", ","));
	}
	$tpl->printToScreen();
	die;
}

if (isset($_GET['mes'])) {
	$sql = "SELECT DISTINCT ON (homoclave) homoclave AS cod, nombre FROM catalogo_arrendatarios LEFT JOIN catalogo_arrendadores USING (cod_arrendador) WHERE status = 1";
	$sql .= " AND recibo_mensual = 'TRUE' ORDER BY cod";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ren_rec_cap.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("captura");
	$tpl->assign("mes", $_GET['mes']);
	$tpl->assign("mes_escrito", mes_escrito($_GET['mes'], TRUE));
	$tpl->assign("anio", $_GET['anio']);
	
	foreach ($result as $i => $reg) {
		$tpl->newBlock("fila");
		$tpl->assign("next", $i < count($result) - 1 ? $i + 1 : 0);
		$tpl->assign("cod", $reg['cod']);
		$tpl->assign("nombre", $reg['nombre']);
		// Si ya se capturo antes algun folio, restaurar el valor
		if (isset($_SESSION['rr']['folio'][$i]))
			$tpl->assign("folio", $_SESSION['rr']['folio'][$i]);
		// Buscar ultimo folio para el arrendador
		else if ($folio = $db->query("SELECT num_recibo FROM recibos_rentas LEFT JOIN catalogo_arrendatarios AS ca ON (ca.id = local) LEFT JOIN catalogo_arrendadores USING (cod_arrendador) WHERE homoclave = $reg[cod] ORDER BY num_recibo DESC LIMIT 1"))
			$tpl->assign("folio", $folio[0]['num_recibo'] + 1);
	}
	$tpl->printToScreen();
	if (isset($_SESSION['rr']))
		unset($_SESSION['rr']);
	die;
}

$tpl->newBlock("datos");
$tpl->assign(date("n"), "selected");
$tpl->assign("anio", date("Y"));

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	$tpl->printToScreen();
	die();
}

$tpl->printToScreen();
?>