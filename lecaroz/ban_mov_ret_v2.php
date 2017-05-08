<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_mov_ret_v2.tpl");
$tpl->prepare();

if (isset($_POST['cuenta'])) {
	$sql = "";
	$fecha_con = $_POST['fecha0'];
	$tabla_mov = $_POST['cuenta'] == 1 ? "catalogo_mov_bancos" : "catalogo_mov_santander";
	$importe = str_replace(",", "", $_POST['importe']);
	$tabla_reg = $_POST['cuenta'] == 1 ? "mov_banorte" : "mov_santander";
	$cat_mov = $db->query("SELECT cod_mov, cod_banco FROM $tabla_mov WHERE tipo_mov = 'TRUE' ORDER BY cod_mov");
	
	function buscar($cod_banco) {
		global $cat_mov;
		
		if (!$cat_mov)
			return FALSE;
		
		foreach ($cat_mov as $mov)
			if ($cod_banco == $mov['cod_banco'])
				return $mov['cod_mov'];
		
		return 5;
	}
	
	// Conciliar movimientos del estado de cuenta
	if (isset($_POST['num_lib'])) {
		$idtmp = NULL;
		for ($i = 0; $i < $_POST['num_lib']; $i++)
			if (isset($_POST['idlib' . $i])) {
				$sql .= "UPDATE estado_cuenta SET fecha_con = '$fecha_con', timestamp = now(), iduser = $_SESSION[iduser], tipo_con = 3 WHERE id = {$_POST['idlib' . $i]};\n";
				$idtmp = $_POST['idlib' . $i];
			}
		
		if ($idtmp) {
			$tmp = $db->query("SELECT cod_mov FROM estado_cuenta WHERE id = $idtmp");
			$cod_mov = $tmp[0]['cod_mov'];
		}
	}
	
	for ($i = 0; $i < $_POST['num_ban']; $i++)
		if (isset($_POST['idban' . $i])) {
			$importe = str_replace(',', '', $_POST['importe_ban' . $i]);
			
			if ($cod_mov == NULL) {
				$cod = buscar($_POST['cod_banco' . $i]);
				
				$sql .= "UPDATE $tabla_reg SET fecha_con = fecha, cod_mov = (CASE WHEN cod_mov IS NOT NULL THEN cod_mov ELSE $cod END), imprimir = 'TRUE', timestamp = now(), iduser = $_SESSION[iduser] WHERE id = {$_POST['idban' . $i]};\n";
				$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos - $importe, saldo_libros = saldo_libros - $importe WHERE num_cia = $_POST[num_cia] AND cuenta = $_POST[cuenta];\n";
				$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, num_doc, concepto, cuenta, iduser, timestamp, tipo_con)";
				$sql .= " SELECT num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, num_documento, concepto, $_POST[cuenta], $_SESSION[iduser], now(), 4 FROM $tabla_reg WHERE id = {$_POST['idban' . $i]};\n";
			}
			else {
				$sql .= "UPDATE $tabla_reg SET fecha_con = fecha, cod_mov = $cod_mov, imprimir = 'TRUE', timestamp = now(), iduser = $_SESSION[iduser] WHERE id = {$_POST['idban' . $i]};\n";
				$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos - $importe WHERE num_cia = $_POST[num_cia] AND cuenta = $_POST[cuenta];\n";
			}
			
			if (isset($_POST['inv' . $i])) {
				$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, tipo_con)";
				$sql .= " SELECT num_cia, fecha, 'FALSE', importe, 29, '2A PRESENTACION CHEQUE ' || num_documento, $_POST[cuenta], $_SESSION[iduser], now(), 0 FROM $tabla_reg WHERE id = {$_POST['idban' . $i]};\n";
				$sql .= "UPDATE saldos SET saldo_libros = saldo_libros + $importe WHERE num_cia = $_POST[num_cia] AND cuenta = $_POST[cuenta];\n";
			}
		}
	
	// Poner fecha de conciliación en los registros
	/*if (isset($_POST['num_lib'])) {
		$cod_mov = $db->query("SELECT cod_mov FROM estado_cuenta WHERE id = $_POST[idlib]");
		$sql .= "UPDATE $tabla_reg SET fecha_con = fecha, cod_mov = {$cod_mov[0]['cod_mov']}, imprimir = 'TRUE', timestamp = now(), iduser = $_SESSION[iduser] WHERE id = $_POST[idban];\n";
		$sql .= "UPDATE estado_cuenta SET fecha_con = '$_POST[fecha]', timestamp = now(), iduser = $_SESSION[iduser], tipo_con = 3 WHERE id = $_POST[idlib];\n";
		$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos - $importe WHERE num_cia = $_POST[num_cia] AND cuenta = $_POST[cuenta];\n";
	}
	else {
		$cod_mov = buscar($_POST['cod_banco']);
		
		$sql .= "UPDATE $tabla_reg SET fecha_con = fecha, cod_mov = $cod_mov, imprimir = 'TRUE', timestamp = now(), iduser = $_SESSION[iduser] WHERE id = $_POST[idban];\n";
		$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos - $importe, saldo_libros = saldo_libros - $importe WHERE num_cia = $_POST[num_cia] AND cuenta = $_POST[cuenta];\n";
		$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, folio, concepto, cuenta, iduser, timestamp, tipo_con)";
		$sql .= " SELECT num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, num_documento, concepto, $_POST[cuenta], $_SESSION[iduser], now(), 4 FROM $tabla_reg WHERE id = $_POST[idban];\n";
		if (isset($_POST['inv'])) {
			$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, tipo_con)";
			$sql .= " SELECT num_cia, fecha, 'FALSE', importe, 29, '2A PRESENTACION CHEQUE ' || num_documento, $_POST[cuenta], $_SESSION[iduser], now(), 0 FROM $tabla_reg WHERE id = $_POST[idban];\n";
			$sql .= "UPDATE saldos SET saldo_libros = saldo_libros + $importe WHERE num_cia = $_POST[num_cia] AND cuenta = $_POST[cuenta];\n";
		}
	}*/
	//echo "<pre>$sql</pre>";die;
	$db->query($sql);
	
	$tpl->newBlock("cerrar");
	$tpl->assign("num_cia", $_POST['num_cia']);
	$tpl->printToScreen();
	die;
}

$num_cia = $_GET['num_cia'];
$cuenta = $_GET['cuenta'];
$tabla_mov = $cuenta == 1 ? "mov_banorte" : "mov_santander";
$cat_mov = $cuenta == 1 ? "catalogo_mov_bancos" : "catalogo_mov_santander";

// Si no se ha cargado archivo, solicitarlo
$tpl->newBlock("datos");
$tpl->assign("cuenta", $cuenta);
$tpl->assign("num_cia", $num_cia);
$nombre = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = $num_cia");
$tpl->assign("nombre", $nombre[0]['nombre']);

// Obtener movimientos de bancos
$sql = "SELECT id, fecha, cod_banco, concepto, importe, num_documento FROM $tabla_mov WHERE id IN (";
foreach ($_GET['id'] as $i => $id)
	$sql .= $id . ($i < count($_GET['id']) - 1 ? ', ' : ')');
$result = $db->query($sql);

$total = 0;
$tpl->assign('num_ban', count($result));
foreach ($result as $i => $reg) {
	$tpl->newBlock('movban');
	$tpl->assign('i', $i);
	$tpl->assign("id", $reg['id']);
	$tpl->assign("fecha", $reg['fecha']);
	$tpl->assign("folio", $reg['num_documento']);
	$tpl->assign("cod_banco", $reg['cod_banco']);
	$tpl->assign("concepto", $reg['concepto']);
	$tpl->assign("importe_ban", number_format($reg['importe'], 2, ".", ","));
	$tpl->assign("disabled", !in_array($reg['cod_banco'], array(506/*Banorte*/, 515/*Santander*/)) ? "disabled" : "");
	$total += $reg['importe'];
	$tpl->assign('datos.total_ban', number_format($total, 2, '.', ','));
}

// Obtener movimientos de libros
$sql = "SELECT estado_cuenta.id, fecha, cod_mov, descripcion, folio, concepto, importe FROM estado_cuenta LEFT JOIN $cat_mov USING (cod_mov) WHERE num_cia = $num_cia AND cuenta = $cuenta";
$sql .= " AND estado_cuenta.tipo_mov = 'TRUE' AND fecha_con IS NULL AND importe = {$result[0]['importe']} GROUP BY estado_cuenta.id, fecha, cod_mov, descripcion, folio, concepto, importe";
$sql .= " ORDER BY fecha, importe";
$result = $db->query($sql);

if (!$result) {
	$sql = "SELECT estado_cuenta.id, fecha, cod_mov, descripcion, folio, concepto, importe FROM estado_cuenta LEFT JOIN $cat_mov USING (cod_mov) WHERE num_cia = $num_cia AND cuenta = $cuenta";
	$sql .= " AND estado_cuenta.tipo_mov = 'TRUE' AND fecha_con IS NULL AND cod_mov IN (19, 48) GROUP BY estado_cuenta.id, fecha, cod_mov, descripcion, folio, concepto, importe";
	$sql .= " ORDER BY fecha, importe";
	$result = $db->query($sql);
}

if ($result) {
	$tpl->newBlock("movs");
	$tpl->assign('num_lib', count($result));
	foreach ($result as $i => $row) {
		$tpl->newBlock("movlib");
		$tpl->assign('i', $i);
		$tpl->assign("id", $row['id']);
		$tpl->assign("fecha", $row['fecha']);
		$tpl->assign("cod_mov", $row['cod_mov']);
		$tpl->assign("folio", $row['folio']);
		$tpl->assign("descripcion", $row['descripcion']);
		$tpl->assign("concepto", $row['concepto']);
		$tpl->assign("importe_lib", number_format($row['importe'], 2, ".", ","));
	}
}
else {
	$tpl->newBlock("no_movs");
}

$tpl->printToScreen();
?>