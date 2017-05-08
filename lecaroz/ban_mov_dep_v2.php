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
$tpl->assignInclude("body", "./plantillas/ban/ban_mov_dep_v2.tpl");
$tpl->prepare();

if (isset($_POST['cuenta'])) {
	$fecha_con = $_POST['fecha0'];
	$cuenta = $_POST['cuenta'];
	$num_cia = $_POST['num_cia'];
	$tabla_mov = $cuenta == 1 ? "catalogo_mov_bancos" : "catalogo_mov_santander";
	$tabla_reg = $cuenta == 1 ? "mov_banorte" : "mov_santander";
	$cat_mov = $db->query("SELECT cod_mov, cod_banco FROM $tabla_mov WHERE tipo_mov = 'FALSE' ORDER BY cod_mov");
	$cod_mov = NULL;
	$sql = "";
	
	function buscar($cod_banco) {
		global $cat_mov;
		
		if (!$cat_mov)
			return FALSE;
		
		foreach ($cat_mov as $mov)
			if ($cod_banco == $mov['cod_banco'])
				return $mov['cod_mov'];
		
		return FALSE;
	}
	
	// Conciliar movimientos del estado de cuenta
	if (isset($_POST['num_mov_lib'])) {
		$idtmp = NULL;
		for ($i = 0; $i < $_POST['num_mov_lib']; $i++)
			if (isset($_POST['idlib' . $i])) {
				$sql .= "UPDATE estado_cuenta SET fecha_con = '$fecha_con', timestamp = now(), iduser = $_SESSION[iduser], tipo_con = 3 WHERE id = {$_POST['idlib' . $i]};\n";
				$idtmp = $_POST['idlib' . $i];
			}
		
		if ($idtmp) {
			$tmp = $db->query("SELECT cod_mov FROM estado_cuenta WHERE id = $idtmp");
			$cod_mov = $tmp[0]['cod_mov'];
		}
	}
	
	for ($i = 0; $i < $_POST['num_mov_ban']; $i++)
		if (isset($_POST['idban' . $i])) {
			if (isset($_POST['inv' . $i])) {
				$importe = str_replace(",", "", $_POST['importe_ban' . $i]);
				// [22-Jul-2008] Inversa para transferencias electrónicas rechazadas de santander
				if (($_POST['cuenta'] == 2 && $_POST['cod_banco' . $i] == 287) || ($_POST['cuenta'] == 1 && $_POST['cod_banco' . $i] == 1)) {
					$tmp = $db->query("SELECT folio FROM estado_cuenta WHERE num_cia = $num_cia AND cuenta = $cuenta AND cod_mov IN (41) AND importe = $importe ORDER BY folio DESC LIMIT 1");
					$folio = $tmp[0]['folio'];
					
					$sql .= "UPDATE estado_cuenta SET fecha_con = NULL, timestamp = now(), iduser = $_SESSION[iduser], tipo_con = 0 WHERE num_cia = $num_cia AND folio = $folio AND importe = $importe AND cuenta = $cuenta;\n";
					$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, tipo_con)";
					$sql .= " SELECT num_cia, fecha, fecha, tipo_mov, importe, 49, 'ANULACION CARGO TRANSFERENCIA RECHAZADA NO $folio', $cuenta, $_SESSION[iduser], now(), 7 FROM $tabla_reg WHERE id = {$_POST['idban' . $i]};\n";
					$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, timestamp, iduser, tipo_con)";
					$sql .= " SELECT num_cia, fecha, fecha, 'TRUE', importe, 21, 'ANULACION CARGO TRANSFERENCIA RECHAZADA NO $folio', $cuenta, now(), $_SESSION[iduser], 7 FROM $tabla_reg WHERE id = {$_POST['idban' . $i]};\n";
					$sql .= "UPDATE $tabla_reg SET fecha_con = fecha, cod_mov = 49, imprimir = 'TRUE', timestamp = now(), iduser = $_SESSION[iduser] WHERE id = {$_POST['idban' . $i]};\n";
					$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos + $importe WHERE num_cia = $num_cia AND cuenta = $cuenta;\n";
				}
				else {
					if ($_POST['num_doc' . $i] <= 0) {
						$tmp = $db->query("SELECT folio FROM estado_cuenta WHERE num_cia = $num_cia AND cuenta = $cuenta AND cod_mov IN (5) AND importe = $importe ORDER BY folio DESC LIMIT 1");
						$folio = $tmp ? $tmp[0]['folio'] : 0;
					}
					
					$sql .= "UPDATE estado_cuenta SET fecha_con = NULL, timestamp = now(), iduser = $_SESSION[iduser], tipo_con = 0 WHERE num_cia = $num_cia AND folio = /*{$_POST['num_doc' . $i]}*/$folio AND importe = $importe AND cuenta = $cuenta;\n";
					$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, tipo_con)";
					$sql .= " SELECT num_cia, fecha, fecha, tipo_mov, importe, 25, 'ANULACION CARGO CHEQUE REBOTADO NO ' || num_documento, $cuenta, $_SESSION[iduser], now(), 7 FROM $tabla_reg WHERE id = {$_POST['idban' . $i]};\n";
					$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, timestamp, iduser, tipo_con)";
					$sql .= " SELECT num_cia, fecha, fecha, 'TRUE', importe, 21, 'ANULACION CARGO CHEQUE REBOTADO NO ' || num_documento, $cuenta, now(), $_SESSION[iduser], 7 FROM $tabla_reg WHERE id = {$_POST['idban' . $i]};\n";
					$sql .= "UPDATE $tabla_reg SET fecha_con = fecha, cod_mov = 25, imprimir = 'TRUE', timestamp = now(), iduser = $_SESSION[iduser] WHERE id = {$_POST['idban' . $i]};\n";
					$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos + $importe WHERE num_cia = $num_cia AND cuenta = $cuenta;\n";
				}
			}
			else {
				$importe = str_replace(",", "", $_POST['importe_ban' . $i]);
				if ($cod_mov == NULL) {
					$cod = buscar($_POST['cod_banco' . $i]);
					$sql .= "UPDATE $tabla_reg SET fecha_con = fecha, cod_mov = $cod, imprimir = 'TRUE', timestamp = now(), iduser = $_SESSION[iduser] WHERE id = {$_POST['idban' . $i]};\n";
					$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, timestamp, iduser, tipo_con)";
					$sql .= " SELECT num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, $cuenta, now(), $_SESSION[iduser], 4 FROM $tabla_reg WHERE id = {$_POST['idban' . $i]};\n";
					$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos + $importe, saldo_libros = saldo_libros + $importe WHERE num_cia = $num_cia AND cuenta = $cuenta;\n";
				}
				else {
					$sql .= "UPDATE $tabla_reg SET fecha_con = fecha, cod_mov = $cod_mov, imprimir = 'TRUE', timestamp = now(), iduser = $_SESSION[iduser] WHERE id = {$_POST['idban' . $i]};\n";
					$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos + $importe WHERE num_cia = $num_cia AND cuenta = $cuenta;\n";
				}
			}
		}//echo "<pre>$sql</pre>";die;
	
	$db->query($sql);
	
	$tpl->newBlock("cerrar");
	$tpl->assign("num_cia", $num_cia);
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
	$sql .= $id . ($i < count($_GET['id']) - 1 ? ", " : ")");
$result = $db->query($sql);

$tpl->assign("num_mov_ban", count($result));

$total = 0;
foreach ($result as $i => $row) {
	$tpl->newBlock("movban");
	$tpl->assign("i", $i);
	$tpl->assign("id", $row['id']);
	$tpl->assign("fecha", $row['fecha']);
	$tpl->assign("num_doc", $row['num_documento']);
	$tpl->assign("cod_banco", $row['cod_banco']);
	$tpl->assign("concepto", $row['concepto']);
	$tpl->assign("importe", number_format($row['importe'], 2, ".", ","));
	$total += $row['importe'];
}
$tpl->assign("datos.total_ban", number_format($total, 2, ".", ","));

// Obtener movimientos de libros
$sql = "SELECT estado_cuenta.id, fecha, cod_mov, descripcion, concepto, importe FROM estado_cuenta LEFT JOIN $cat_mov USING (cod_mov) WHERE num_cia = $num_cia AND cuenta = $cuenta";
$sql .= " AND estado_cuenta.tipo_mov = 'FALSE' AND fecha_con IS NULL GROUP BY estado_cuenta.id, fecha, cod_mov, descripcion, concepto, importe ORDER BY fecha, importe";
$result = $db->query($sql);

if ($result) {
	$tpl->newBlock("movs");
	$tpl->assign("num_mov_lib", count($result));
	foreach ($result as $i => $row) {
		$tpl->newBlock("movlib");
		$tpl->assign("i", $i);
		$tpl->assign("id", $row['id']);
		$tpl->assign("fecha", $row['fecha']);
		$tpl->assign("cod_mov", $row['cod_mov']);
		$tpl->assign("descripcion", $row['descripcion']);
		$tpl->assign("concepto", $row['concepto']);
		$tpl->assign("importe", number_format($row['importe'], 2, ".", ","));
	}
}
else {
	$tpl->newBlock("no_movs");
}

$tpl->printToScreen();
?>