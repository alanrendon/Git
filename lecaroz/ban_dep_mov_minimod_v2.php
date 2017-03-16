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
$tpl->assignInclude("body", "./plantillas/ban/ban_dep_mov_minimod_v2.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	$cuenta = $_POST['cuenta'];
	$num_cia = $_POST['num_cia'];
	$tabla_mov = $cuenta == 1 ? "mov_banorte" : "mov_santander";
	$concepto = strtoupper($_POST['concepto']);
	$importe = str_replace(",", "", $_POST['importe']);
	$total = isset($_POST['total']) ? str_replace(",", "", $_POST['total']) : 0;
	
	if ($total != 0) {
		$sql = "";
		
		// Recolectar abonos
		$cont_a = 0;
		foreach ($_POST['abono'] as $i => $a) {
			$tmp = str_replace(",", "", $a);
			if ($tmp != 0) {
				$abono[$cont_a]['importe'] = $tmp;
				$abono[$cont_a]['cod_mov'] = $_POST['cod_mov_abo'][$i];
				$cont_a++;
			}
		}
		// Recolectar cargos
		$cont_c = 0;
		foreach ($_POST['cargo'] as $i => $c) {
			$tmp = str_replace(",", "", $c);
			if ($tmp != 0) {
				$cargo[$cont_c]['importe'] = $tmp;
				$cargo[$cont_c]['cod_mov'] = $_POST['cod_mov_car'][$i];
				$cont_c++;
			}
		}
		
		// Buscar cargos dentro de los movimientos pendientes
		if (isset($cargo))
			foreach ($cargo as $c)
				if ($id = $db->query("SELECT id FROM $tabla_mov WHERE num_cia = $num_cia AND tipo_mov = 'TRUE' AND fecha_con IS NULL AND importe = $c[importe]")) {
					$sql .= "UPDATE $tabla_mov SET cod_mov = $c[cod_mov], concepto = '$concepto', fecha_con = now()::date, imprimir = 'TRUE', iduser = $_SESSION[iduser], timestamp = CURRENT_TIMESTAMP WHERE id = {$id[0]['id']};\n";
					$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp) SELECT num_cia, '$_POST[fecha]', '$_POST[fecha_con]', tipo_mov, importe, cod_mov, concepto, $cuenta,";
					$sql .= " $_SESSION[iduser], CURRENT_TIMESTAMP FROM $tabla_mov WHERE id = {$id[0]['id']};\n";
					$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos - $c[importe], saldo_libros = saldo_libros - $c[importe] WHERE num_cia = $num_cia AND cuenta = $cuenta;\n";
				}
				else {
					$concepto = $db->query("SELECT cod_mov, descripcion FROM catalogo_mov_santander WHERE cod_mov = $c[cod_mov] GROUP BY cod_mov, descripcion");
					$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp) VALUES ($num_cia, '$_POST[fecha]', '$_POST[fecha_con]', 'TRUE', $c[importe],";
					$sql .= " $c[cod_mov], '{$concepto[0]['descripcion']}', $cuenta, $_SESSION[iduser], CURRENT_TIMESTAMP);\n";
					$sql .= "INSERT INTO $tabla_mov (num_cia, fecha, tipo_mov, importe, concepto, cod_mov, fecha_con, imprimir, descripcion, iduser, timestamp) VALUES (";
					$sql .= "$num_cia, '$_POST[fecha]', 'TRUE', $c[importe], '{$concepto[0]['descripcion']}', $c[cod_mov], '$_POST[fecha]', 'TRUE', '{$concepto[0]['descripcion']}', $_SESSION[iduser], CURRENT_TIMESTAMP);\n";
					$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos - $c[importe], saldo_libros = saldo_libros - $c[importe] WHERE num_cia = $num_cia AND cuenta = $cuenta;\n";
				}
		
		foreach ($abono as $a) {
			$concepto = $db->query("SELECT cod_mov, descripcion FROM catalogo_mov_santander WHERE cod_mov = $a[cod_mov] GROUP BY cod_mov, descripcion");
			$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp) VALUES ($num_cia, '$_POST[fecha]', '$_POST[fecha]', 'FALSE', $a[importe],";
			$sql .= " $a[cod_mov], '{$concepto[0]['descripcion']}', $cuenta, $_SESSION[iduser], CURRENT_TIMESTAMP);\n";
			$sql .= "INSERT INTO mov_santander (num_cia, fecha, tipo_mov, importe, concepto, cod_mov, fecha_con, imprimir, descripcion, iduser, timestamp) VALUES (";
			$sql .= "$num_cia, '$_POST[fecha]', 'TRUE', $a[importe], '{$concepto[0]['descripcion']}', $a[cod_mov], '$_POST[fecha]', 'FALSE', '{$concepto[0]['descripcion']}', $_SESSION[iduser], CURRENT_TIMESTAMP);\n";
			$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos + $a[importe], saldo_libros = saldo_libros + $a[importe] WHERE num_cia = $num_cia AND cuenta = $cuenta;\n";
		}
		
		$sql .= "DELETE FROM $tabla_mov WHERE id = $_POST[id];\n";
		
		//echo "<pre>$sql</pre>";die;
	}
	else {
		$sql = "UPDATE $tabla_mov SET cod_mov = $_POST[cod_mov], concepto = '$concepto', fecha_con = now()::date, imprimir = 'TRUE' WHERE id = $_POST[id];\n";
		$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta) SELECT num_cia, '$_POST[fecha]', '$_POST[fecha_con]', tipo_mov, importe, cod_mov, concepto, $cuenta";
		$sql .= " FROM $tabla_mov WHERE id = $_POST[id];\n";
		$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos + $importe, saldo_libros = saldo_libros + $importe WHERE num_cia = $num_cia AND cuenta = $cuenta;\n";
	}
	$db->query($sql);
	
	$tpl->newBlock("cerrar");
	$tpl->assign("num_cia", $num_cia);
	$tpl->printToScreen();
	
	if ($_POST['cod_mov'] == 1 && strtoupper($_POST['concepto']) != strtoupper($_POST['concepto_ant'])) {
		include_once('includes/phpmailer/class.phpmailer.php');
		
		$sql = '
			SELECT
				cc.num_cia,
				cc.nombre_corto
					AS nombre_cia,
				cc.email
					AS email_cia,
				ca.email
					AS email_admin,
				COALESCE((
					SELECT
						TRUE
					FROM
						catalogo_expendios
					WHERE
						num_cia = ' . $_REQUEST['num_cia'][$i] . '
						AND idagven > 0
					LIMIT
						1
				), FALSE)
					AS agente_ventas,
				idadministrador
					AS admin
			FROM
				catalogo_companias cc
				LEFT JOIN catalogo_administradores ca
					USING (idadministrador)
			WHERE
				num_cia = ' . $num_cia . ';
		';
		
		$query = $db->query($sql);
		
		$cia = $query[0];
		
		$mail = new PHPMailer();
		
		$mail->IsSMTP();
		$mail->Host = 'mail.lecaroz.com';
		$mail->Port = 587;
		$mail->SMTPAuth = true;
		$mail->Username = 'mollendo@lecaroz.com';
		$mail->Password = 'L3c4r0z*';
		
		$mail->From = 'mollendo@lecaroz.com';
		$mail->FromName = utf8_decode('Oficinas Administrativas Mollendo, S. de R.L. de C.V.');
		
		if ($cia['email_cia'] != '') {
			$mail->AddAddress($cia['email_cia']);
		}
		
		if ($cia['email_admin'] != '') {
			$mail->AddCC($cia['email_admin']);
		}
		
		$mail->AddBCC('miguelrebuelta@lecaroz.com');
		
		if ($cia['agente_ventas'] == 't') {
			$mail->AddCC('liliabalcazar@hotmail.com');
			$mail->AddCC('lilia.balcazar@lecaroz.com');
		}
		
		if ($cia['admin'] == 13) {
			$mail->AddBCC('ilarracheai@hotmail.com');
			$mail->AddBCC('jmjuan68@hotmail.com');
		}
		
		$mail->Subject = utf8_decode('Depósito de pago de cliente');
		
		$tpl = new TemplatePower('plantillas/ban/email_deposito_cliente.tpl');
		$tpl->prepare();
		
		$tpl->assign('num_cia', $cia['num_cia']);
		$tpl->assign('nombre_cia', $cia['nombre_cia']);
		
		$tpl->assign('cliente', strtoupper($_POST['concepto']));
		$tpl->assign('importe', number_format(get_val($_POST['importe']), 2));
		$tpl->assign('fecha', $_POST['fecha']);
		
		$mail->Body = $tpl->getOutputContent();
		
		$mail->IsHTML(true);
		
		@$mail->Send();
	}
	
	die;
}

$id = $_GET['id'];
$cuenta = $_GET['cuenta'];
$tabla_mov = $cuenta == 1 ? "mov_banorte" : "mov_santander";
$cat_mov = $cuenta == 1 ? "catalogo_mov_bancos" : "catalogo_mov_santander";

// Si no se ha cargado archivo, solicitarlo
$tpl->newBlock("datos");
$tpl->assign("id", $id);
$tpl->assign("cuenta", $cuenta);

$result = $db->query("SELECT num_cia, (fecha - interval '1 day')::DATE AS fecha, fecha AS fecha_con, cod_banco, concepto, importe, num_documento FROM $tabla_mov WHERE id = $id");
$cat = $db->query("SELECT cod_mov, descripcion FROM $cat_mov WHERE tipo_mov = 'FALSE' AND cod_mov NOT IN (2) GROUP BY cod_mov, descripcion ORDER BY cod_mov");

$tpl->assign("num_cia", $result[0]['num_cia']);
$nombre = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = {$result[0]['num_cia']}");
$tpl->assign("nombre", $nombre[0]['nombre']);
$tpl->assign("fecha", $result[0]['fecha']);
$tpl->assign("fecha_con", $result[0]['fecha_con']);
$tpl->assign("concepto", $result[0]['concepto']);
$tpl->assign("importe", number_format($result[0]['importe'], 2, ".", ","));

foreach ($cat as $cod) {
	$tpl->newBlock("cod_mov");
	$tpl->assign("cod_mov", $cod['cod_mov']);
	$tpl->assign("descripcion", $cod['descripcion']);
	
	if (($result[0]['num_cia'] > 100 && $result[0]['num_cia'] < 200 || $result[0]['num_cia'] == 702 || $result[0]['num_cia'] == 703 || $result[0]['num_cia'] == 704) && $cod['cod_mov'] == 16)
		$tpl->assign("selected", "selected");
}

if ($cuenta == 2 && in_array($result[0]['cod_banco'], array(38))/* && $_SESSION['iduser'] == 28*/) {
	$tpl->newBlock("tarjeta");
	
	$cat_car = $db->query("SELECT cod_mov, descripcion FROM $cat_mov WHERE tipo_mov = 'TRUE' GROUP BY cod_mov, descripcion ORDER BY cod_mov");
	
	$total = 0;
	
	// Obtener cargos con codigo 864 y 571
	$cargos = $db->query("SELECT cod_banco, importe FROM $tabla_mov WHERE num_cia = {$result[0]['num_cia']} AND cod_banco IN (864, 571) AND fecha_con IS NULL AND num_documento = {$result[0]['num_documento']} + 1");
	
	for ($i = 0; $i < 4; $i++) {
		$tpl->newBlock("cargo");
		$tpl->assign("campo", $i < 3 ? "cargo[" . ($i + 1) . "]" : "abono[0]");
		if (isset($cargos[$i])) {
			$tpl->assign("cargo", number_format($cargos[$i]['importe'], 2, ".", ","));
			$total -= $cargos[$i]['importe'];
		}
		foreach ($cat_car as $reg) {
			$tpl->newBlock("cod_cargo");
			$tpl->assign("cod", $reg['cod_mov']);
			$tpl->assign("nombre", $reg['descripcion']);
			if (isset($cargos[$i]) && $cargos[$i]['cod_banco'] == 864 && $reg['cod_mov'] == 46) $tpl->assign("selected", "selected");
			if (isset($cargos[$i]) && $cargos[$i]['cod_banco'] == 571 && $reg['cod_mov'] == 10) $tpl->assign("selected", "selected");
		}
	}
	
	for ($i = 0; $i < 2; $i++) {
		$tpl->newBlock("abono");
		$tpl->assign("campo", $i < 1 ? "abono[" . ($i + 1) . "]" : "cargo[0]");
		if ($i == 0) {
			$tpl->assign("abono", number_format($result[0]['importe'], 2, ".", ","));
			$total += $result[0]['importe'];
		}
		foreach ($cat as $reg) {
			$tpl->newBlock("cod_abono");
			$tpl->assign("cod", $reg['cod_mov']);
			$tpl->assign("nombre", $reg['descripcion']);
			if ($reg['cod_mov'] == 44) $tpl->assign("selected", "selected");
		}
	}
	$tpl->assign("tarjeta.total", number_format($total, 2, ".", ","));
	$tpl->assign("tarjeta.color", $total > 0 ? "0000CC" : "CC0000");
}

$tpl->printToScreen();
?>