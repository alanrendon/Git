<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "";
$numfilas = 25;

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_dep_ren.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$numfilas = 10;

if (isset($_POST['cuenta'])) {
	$sql = '';
	$cuenta = $_POST['cuenta'];
	for ($i = 0; $i < $numfilas; $i++)
		if ($_POST['num_cia'][$i] > 0 && strlen($_POST['fecha'][$i]) >= 8 && get_val($_POST['importe'][$i])) {
			$num_cia = $_POST['num_cia'][$i];
			$fecha = $_POST['fecha'][$i];
			$importe = get_val($_POST['importe'][$i]);
			$local = $_POST['local'][$i] > 0 ? $_POST['local'][$i] : 'NULL';
			$nombre = $local > 0 ? $db->query("SELECT nombre_local FROM catalogo_arrendatarios WHERE id = $local") : '';
			$concepto = trim($_POST['mes'][$i] . ' ' . $_POST['anio'][$i] . ' ' . $nombre[0]['nombre_local']);
			switch ($_POST['mes'][$i]) {
				case 'ENERO': $fecha_renta = "01/01/{$_POST['anio'][$i]}"; break;
				case 'FEBRERO': $fecha_renta = "01/02/{$_POST['anio'][$i]}"; break;
				case 'MARZO': $fecha_renta = "01/03/{$_POST['anio'][$i]}"; break;
				case 'ABRIL': $fecha_renta = "01/04/{$_POST['anio'][$i]}"; break;
				case 'MAYO': $fecha_renta = "01/05/{$_POST['anio'][$i]}"; break;
				case 'JUNIO': $fecha_renta = "01/06/{$_POST['anio'][$i]}"; break;
				case 'JULIO': $fecha_renta = "01/07/{$_POST['anio'][$i]}"; break;
				case 'AGOSTO': $fecha_renta = "01/08/{$_POST['anio'][$i]}"; break;
				case 'SEPTIEMBRE': $fecha_renta = "01/09/{$_POST['anio'][$i]}"; break;
				case 'OCTUBRE': $fecha_renta = "01/10/{$_POST['anio'][$i]}"; break;
				case 'NOVIEMBRE': $fecha_renta = "01/11/{$_POST['anio'][$i]}"; break;
				case 'DICIEMBRE': $fecha_renta = "01/12/{$_POST['anio'][$i]}"; break;
			}
			
			// Estado de cuenta
			$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, local, fecha_renta) VALUES ($num_cia, '$fecha', 'FALSE', $importe, 2,";
			$sql .= " '$concepto', $cuenta, $_SESSION[iduser], $local, '$fecha_renta');\n";
			// Depositos
			$sql .= "INSERT INTO depositos (num_cia, cod_mov, fecha_mov, importe, concepto, fecha_cap, manual, imprimir, ficha, cuenta, local) VALUES ($num_cia, 2, '$fecha', $importe,";
			$sql .= " '$concepto', now()::date, 'TRUE', 'TRUE', 'FALSE', $cuenta, $local);\n";
			// Actualizar saldo en libros
			$sql .= "UPDATE saldos SET saldo_libros = saldo_libros + $importe WHERE num_cia = $num_cia AND cuenta = $cuenta;\n";
		}
	
	//echo "<pre>$sql</pre>"; die;
	if ($sql != '') $db->query($sql);
	
	$sql = "SELECT d.num_cia, nombre, clabe_cuenta" . ($cuenta == 2 ? 2 : '') . " AS cuenta, fecha_mov, concepto, nombre_local, importe FROM depositos AS d LEFT JOIN catalogo_companias";
	$sql .= " USING (num_cia) LEFT JOIN catalogo_arrendatarios AS arr ON (arr.id = local) WHERE imprimir = 'TRUE' AND cod_mov = 2 AND fecha_cap = CURRENT_DATE";
	$result = $db->query($sql);
	
	$db->query("UPDATE depositos SET imprimir = 'FALSE' WHERE imprimir = 'TRUE' AND cod_mov = 2 AND fecha_cap = CURRENT_DATE");
	
	if (!$result) {
		header('location: ./ban_dep_ren.php');
		die;
	}
	
	$tpl->newBlock('listado');
	$tpl->assign('fecha', date('d/m/Y'));
	
	$total = 0;
	foreach ($result as $reg) {
		$tpl->newBlock('mov');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre']);
		$tpl->assign('cuenta', $reg['cuenta']);
		$tpl->assign('fecha', $reg['fecha_mov']);
		$tpl->assign('concepto', $reg['concepto'] != '' ? $reg['concepto'] : '&nbsp;');
		$tpl->assign('local', $reg['nombre_local'] != '' ? $reg['nombre_local'] : '&nbsp;');
		$tpl->assign('importe', number_format($reg['importe'], 2, '.', ','));
		$total += $reg['importe'];
	}
	$tpl->assign('listado.total', number_format($total, 2, '.', ','));
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("captura");
for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock('fila');
	$tpl->assign('i', $i);
	$tpl->assign('back', $i > 0 ? $i - 1 : $numfilas - 1);
	$tpl->assign('next', $i < $numfilas - 1 ? $i + 1 : 0);
	$tpl->assign('fecha', date('d/m/Y'));
	$tpl->assign(date('n'), ' selected');
	$tpl->assign('anio', date('Y'));
}

$result = $db->query('SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias ORDER BY num_cia');
foreach ($result as $reg) {
	$tpl->newBlock('cia');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre']);
}

$sql = "SELECT cod_arrendador AS arr, id, num_local AS local, nombre_local, nombre_arrendatario AS nombre_arr, renta_con_recibo AS renta, agua, mantenimiento, retencion_isr, retencion_iva, tipo_local FROM catalogo_arrendatarios WHERE status = 1";
$sql .= " ORDER BY cod_arrendador, /*nombre_local*/renta DESC";
$result = $db->query($sql);
$arr = NULL;
foreach ($result as $reg) {
	if ($arr != $reg['arr']) {
		if ($arr != NULL)
			$tpl->assign('arr.locales', $locales);
		
		$arr = $reg['arr'];
		
		$tpl->newBlock('arr');
		$tpl->assign('arr', $arr);
		
		$locales = '';
	}
	if ($locales != '')
		$locales .= ', ';
	
	$subtotal = $reg['renta'] + $reg['mantenimiento'];
	$iva = $reg['tipo_local'] == 1 ? $subtotal * /*0.15*/0.16 : 0;
	$isr = $reg['retencion_isr'] == 't' && $reg['tipo_local'] == 1 ? $subtotal * 0.10 : 0;
	$ret = $reg['retencion_iva'] == 't' && $reg['tipo_local'] == 1 ? $subtotal * /*0.10*/0.10666666667 : 0;
	$agua = $reg['agua'];
	$total = $subtotal + $iva + $agua - $isr - $ret;
	
	$locales .= "$reg[id], '$reg[nombre_local] - " . number_format($total, 2, '.', ',') . "', '$reg[nombre_arr]'";
	
	$tpl->newBlock('ren');
	$tpl->assign('local', $reg['id']);
	$tpl->assign('renta', $total);
}
if ($arr != NULL)
	$tpl->assign('arr.locales', $locales);

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>