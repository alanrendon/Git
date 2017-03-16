<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("AVISAR A CARLOS ANTES DE CONCILIAR");

$descripcion_error[1] = "Los archivos solo puede ser de tipo 'gzip' (extensión .gz)";
$descripcion_error[2] = "El CHECKSUM de los archivos <strong>" . (isset($_GET['archivo1']) ? $_GET['archivo1'] : "") . "</strong> y " . (isset($_GET['archivo2']) ? $_GET['archivo2'] : "") . "</strong> son iguales";
$descripcion_error[3] = "El archivo <strong>" . (isset($_GET['archivo']) ? $_GET['archivo'] : "") . "</strong> ya ha sido cargado en el sistema";

function buscar_cia($catCias, $cuenta) {
	for ($i = 0; $i < count($catCias); $i++)
		if ($cuenta == $catCias[$i]['clabe_cuenta2'])
			return $catCias[$i]['num_cia'];

	return FALSE;
}

// Cancelar todo y borrar movimientos
if (isset($_GET['accion']) && $_GET['accion'] == "cancel") {
	$db->query("DELETE FROM mov_santander WHERE hash = '$_GET[hash]'");
	header("location: ./ban_con_aut_san_v2.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_con_aut_san_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Cargar y validar archivo de datos al sistema
if (isset($_FILES['userfile'])) {
	// *** Verificar que el tipo de archivo sea texto plano
	/*if ($_FILES['userfile']['tmp_name'] != "" && !stristr(mime_content_type($_FILES['userfile']['tmp_name']), "gzip")) {
		header("location: ./ban_con_aut_san_v2.php?codigo_error=1");
		die();
	}*/

	// Abrir el archivo
	$lines = gzfile($_FILES['userfile']['tmp_name']);
	$hash = md5(implode($lines));

	// *** Verificar que el archivo no haya sido cargado en el sistema
	if ($_FILES['userfile']['tmp_name'] != "" && $db->query("SELECT hash FROM mov_santander WHERE hash = '$hash' LIMIT 1")) {
		header("location: ./ban_con_aut_san_v2.php?codigo_error=3&archivo={$_FILES['userfile']['name']}");
		die;
	}

	// Cargar catálogo de compañías
	$catCias = $db->query("SELECT num_cia, clabe_cuenta2 FROM catalogo_companias WHERE clabe_cuenta2 IS NOT NULL ORDER BY clabe_cuenta2");

	$i = 0;
	$saldo = array();

	// Recorrer el archivo
	foreach ($lines as $i => $line) {
		if (trim($line) == '') {
			continue;
		}

		$data[$i]['num_cia'] = buscar_cia($catCias, trim(substr($line, 0, 11)));
		$data[$i]['cuenta'] = trim(substr($line, 0, 11));
		$data[$i]['fecha'] = substr($line, 18, 2) . "/" . substr($line, 16, 2) . "/" . substr($line, 20, 4);
		$data[$i]['cod_banco'] = substr($line, 32, 4);
		$data[$i]['descripcion'] = trim(substr($line, 36, 40));
		$data[$i]['tipo_mov'] = substr($line, 76, 1) == "+" ? "FALSE" : "TRUE";
		$data[$i]['importe'] = floatval(substr($line, 77, 12) . "." . substr($line, 89, 2));
		$data[$i]['num_documento'] = intval(substr($line, 105, 8)) > 0 ? intval(substr($line, 105, 8)) : "";
		$data[$i]['concepto'] = trim(trim(substr($line, 36, 40)) . " " . trim(substr($line, 113, 40)));
		$data[$i]['hash'] = $hash;

		// Saldo para la compañía
		$saldo[$data[$i]['num_cia']]['saldo'] = floatval(substr($line, 91, 12) . "." . substr($line, 103, 2));
		$saldo[$data[$i]['num_cia']]['fecha'] = $data[$i]['fecha'];
	}
	// Guardar movimientos en la tabla mov_santander
	$db->query($db->multiple_insert("mov_santander", $data));

	// ****** Validación de códigos ******
	// Obtener códigos del catálogo
	$sql = "SELECT cod_banco, descripcion, tipo_mov FROM mov_santander WHERE num_cia > 0 AND cod_banco NOT IN (SELECT cod_banco FROM catalogo_mov_santander";
	$sql .= " GROUP BY cod_banco ORDER BY cod_banco) GROUP BY cod_banco, descripcion, tipo_mov ORDER BY cod_banco";
	$cod = $db->query($sql);

	if ($cod) {
		$tpl->newBlock("val_cod");
		for ($i = 0; $i < count($cod); $i++) {
			$tpl->newBlock("cod_banco");
			$tpl->assign("cod_banco", $cod[$i]['cod_banco'] != 0 ? $cod[$i]['cod_banco'] : "0");
			$tpl->assign("descripcion", $cod[$i]['descripcion']);
			$tpl->assign("tipo_mov", $cod[$i]['tipo_mov'] == "f" ? "ABONO" : "CARGO");
			$tmp = $db->query("SELECT num_cia, nombre_corto, cuenta, concepto, importe FROM mov_santander LEFT JOIN catalogo_companias USING (num_cia) WHERE cod_banco = {$cod[$i]['cod_banco']} AND num_cia > 0 LIMIT 1");
			$tpl->assign("num_cia", $tmp[0]['num_cia']);
			$tpl->assign("nombre_cia", $tmp[0]['nombre_corto']);
			$tpl->assign("cuenta", $tmp[0]['cuenta']);
			$tpl->assign("concepto", $tmp[0]['concepto']);
			$tpl->assign("importe", number_format($tmp[0]['importe'], 2, ".", ","));
		}

		// Borrar movimientos de los archivos recien insertados
		$db->query("DELETE FROM mov_santander WHERE hash = '$hash'");

		$tpl->printToScreen();
		die;
	}

	// ****** Validar cuentas ******
	// Obtener cuentas sin compañía
	/*$cia = $db->query("SELECT cuenta FROM mov_santander WHERE num_cia IS NULL GROUP BY cuenta ORDER BY cuenta");

	if ($cia) {
		$tpl->newBlock("val_cuentas");
		$tpl->assign("hash", $hash);
		for ($i = 0; $i < count($cia); $i++) {
			$tpl->newBlock("cuenta");
			$tpl->assign("cuenta", $cia[$i]['cuenta']);
		}

		$tpl->printToScreen();
		die;
	}*/

	// Guardar saldos en bancos
	$sql = "";
	foreach ($saldo as $num_cia => $saldo)
		if ($num_cia > 0) {
			if ($db->query("SELECT num_cia FROM saldo_santander WHERE num_cia = $num_cia"))
				$sql .= "UPDATE saldo_santander SET saldo = $saldo[saldo], fecha_archivo = '$saldo[fecha]' WHERE num_cia = $num_cia;\n";
			else
				$sql .= "INSERT INTO saldo_santander (num_cia, saldo, fecha_archivo) VALUES ($num_cia, $saldo[saldo], '$saldo[fecha]');\n";
		}
	if ($sql != "") $db->query($sql);

	header("location: ./ban_con_aut_san_v2.php?accion=imp&hash=$hash");
	die;
}

// [23/Oct/2006] Alerta y listado de Impuestos Federales, IMSS e Infonavit en el archivo de conciliacion
if (isset($_GET['accion']) && $_GET['accion'] == "imp") {
	$sql = "SELECT num_cia, nombre_corto, clabe_cuenta2 AS cuenta, fecha, concepto, importe FROM mov_santander LEFT JOIN catalogo_companias USING (num_cia) WHERE";
	$sql .= " fecha_con IS NULL AND num_cia BETWEEN 1 AND 800 AND cod_banco IN (740, 741, 990, 1679) ORDER BY num_cia, fecha";
	$result = $db->query($sql);

	if (!$result) {
		header("location: ./ban_con_aut_san_v2.php?accion=codsob&hash=$_GET[hash]");
		die;
	}

	$tpl->newBlock("imp");
	$tpl->assign("hash", $_GET['hash']);
	foreach ($result as $reg) {
		$tpl->newBlock("imp_rec");
		$tpl->assign("num_cia", $reg['num_cia']);
		$tpl->assign("nombre", $reg['nombre_corto']);
		$tpl->assign("cuenta", $reg['cuenta']);
		$tpl->assign("fecha", $reg['fecha']);
		$tpl->assign("concepto", $reg['concepto']);
		$tpl->assign("importe", number_format($reg['importe'], 2, ".", ","));
	}
	$tpl->printToScreen();
	die;
}

// Revisar códigos de sobrante
if (isset($_GET['accion']) && $_GET['accion'] == "codsob") {
	$sql = "SELECT id, num_cia, nombre_corto, cuenta, importe, cod_banco, concepto FROM mov_santander LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha_con IS NULL";
	$sql .= " AND num_cia > 0 AND cod_banco IN (142, 143) ORDER BY num_cia, fecha";
	$result = $db->query($sql);

	if (!$result) {
		header("location: ./ban_con_aut_san_v2.php?accion=con&hash=$_GET[hash]");
		die;
	}

	$cods = $db->query("SELECT cod_mov, descripcion FROM catalogo_mov_santander WHERE tipo_mov = 'FALSE' GROUP BY cod_mov, descripcion ORDER BY cod_mov");

	$tpl->newBlock("cod_sob");
	$tpl->assign("hash", $_GET['hash']);
	foreach ($result as $i => $reg) {
		$tpl->newBlock("cod_sob_fila");
		$tpl->assign("i", $i);
		$tpl->assign("id", $reg['id']);
		$tpl->assign("num_cia", $reg['num_cia']);
		$tpl->assign("nombre", $reg['nombre_corto']);
		$tpl->assign("cuenta", $reg['cuenta']);
		$tpl->assign("importe", number_format($reg['importe'], 2, ".", ","));
		$tpl->assign("cod", $reg['cod_banco']);
		$tpl->assign("concepto", $reg['concepto']);

		foreach ($cods as $cod) {
			$tpl->newBlock("cod");
			$tpl->assign("cod", $cod['cod_mov']);
			$tpl->assign("nombre", $cod['descripcion']);
			if ($cod['cod_mov'] == 13) $tpl->assign("selected", " selected");
		}
	}
	$tpl->printToScreen();
	die;
}

// Ejecutar proceso de conciliación
if (isset($_GET['accion']) && $_GET['accion'] == "con") {
	// [15-Nov-2006] Insertar movimientos de Impuestos, IMSS e Infonavit
	$result = $db->query("SELECT num_cia, fecha, cod_banco, importe, num_documento FROM mov_santander WHERE num_cia IS NOT NULL AND cod_banco IN (990, 740, 741, 1679) AND fecha_con IS NULL AND hash = '$_GET[hash]' ORDER BY num_cia, fecha");
	if ($result) {
		$num_cia = NULL;
		$cont = 0;
		foreach ($result as $reg) {
			//if ($reg['num_cia'] <= 800) {
				if ($num_cia != $reg['num_cia']) {
					$num_cia = $reg['num_cia'];

					// Obtener ultimo folio
					$temp = $db->query("SELECT folio FROM folios_cheque WHERE num_cia = $num_cia AND cuenta = 2 ORDER BY folio DESC LIMIT 1");
					$folio_cheque = $temp ? $temp[0]['folio'] + 1 : 1;
				}

				$mov[$cont]['num_cia'] = $num_cia;
				$mov[$cont]['cuenta'] = 2;
				$mov[$cont]['fecha'] = $reg['fecha'];
				$mov[$cont]['tipo_mov'] = "TRUE";
				$mov[$cont]['importe'] = $reg['importe'];
				$mov[$cont]['concepto'] = in_array($reg['cod_banco'], array(990, 1679)) ? "PAGO DE IMPUESTOS FEDERALES" : ($reg['cod_banco'] == 740 ? "PAGO IMSS" : "PAGO INFONAVIT");
				$mov[$cont]['cod_mov'] = in_array($reg['cod_banco'], array(990, 1679)) ? 33 : 43;
				$mov[$cont]['folio'] = $folio_cheque;
				$mov[$cont]['num_doc'] = $reg['num_documento'];

				$cheque[$cont]['num_cia']       = $num_cia;
				$cheque[$cont]['num_proveedor'] = in_array($reg['cod_banco'], array(990, 1679)) ? 237 : 235;
				$cheque[$cont]['a_nombre']      = in_array($reg['cod_banco'], array(990, 1679)) ? "TESORERIA DE LA FEDERACION" : "INSTITUTO MEXICANO DEL SEGURO SOCIAL";
				$cheque[$cont]['fecha']         = $mov[$cont]['fecha'];
				$cheque[$cont]['importe']       = $mov[$cont]['importe'];
				$cheque[$cont]['iduser']        = $_SESSION['iduser'];
				$cheque[$cont]['imp']           = "FALSE";
				$cheque[$cont]['concepto']      = $mov[$cont]['concepto'];
				$cheque[$cont]['cod_mov']       = in_array($reg['cod_banco'], array(990, 1679)) ? 33 : 43;
				$cheque[$cont]['codgastos']     = in_array($reg['cod_banco'], array(990, 1679)) ? 140 : 141;
				$cheque[$cont]['folio']         = $folio_cheque;
				$cheque[$cont]['cuenta']        = 2;
				$cheque[$cont]['poliza']        = "TRUE";

				$gasto[$cont]['num_cia']       = $num_cia;
				$gasto[$cont]['fecha']         = $mov[$cont]['fecha'];
				$gasto[$cont]['importe']       = $mov[$cont]['importe'];
				$gasto[$cont]['concepto']      = $mov[$cont]['concepto'];
				$gasto[$cont]['codgastos']     = in_array($reg['cod_banco'], array(990, 1679)) ? 140 : 141;
				$gasto[$cont]['folio']         = $folio_cheque;
				$gasto[$cont]['captura']       = "TRUE";

				$folio[$cont]['num_cia']   = $num_cia;
				$folio[$cont]['folio']     = $folio_cheque++;
				$folio[$cont]['reservado'] = "FALSE";
				$folio[$cont]['utilizado'] = "TRUE";
				$folio[$cont]['fecha']     = $mov[$cont]['fecha'];
				$folio[$cont]['cuenta']    = 2;

				$cont++;
			//}
		}

		$tmp  = $db->multiple_insert("estado_cuenta", $mov);
		$tmp .= $db->multiple_insert("cheques", $cheque);
		$tmp .= $db->multiple_insert("movimiento_gastos", $gasto);
		$tmp .= $db->multiple_insert("folios_cheque", $folio);
		$db->query($tmp);
	}

	// [08-Oct-2009] Insertar movimientos de Impuestos 2%
	$result = $db->query("SELECT num_cia, fecha, cod_banco, importe, num_documento FROM mov_santander WHERE num_cia IS NOT NULL AND cod_banco IN (681) AND (concepto LIKE 'CGO TRANS ELEC 101001%' OR concepto LIKE 'CGO TRANS ELEC 707001%') AND fecha_con IS NULL AND hash = '$_GET[hash]' ORDER BY num_cia, fecha");
	if ($result) {
		$num_cia = NULL;
		$cont = 0;
		foreach ($result as $reg) {
			//if ($reg['num_cia'] <= 800) {
				if ($num_cia != $reg['num_cia']) {
					$num_cia = $reg['num_cia'];

					// Obtener ultimo folio
					$temp = $db->query("SELECT folio FROM folios_cheque WHERE num_cia = $num_cia AND cuenta = 2 ORDER BY folio DESC LIMIT 1");
					$folio_cheque = $temp ? $temp[0]['folio'] + 1 : 1;
				}

				$mov[$cont]['num_cia'] = $num_cia;
				$mov[$cont]['cuenta'] = 2;
				$mov[$cont]['fecha'] = $reg['fecha'];
				$mov[$cont]['tipo_mov'] = "TRUE";
				$mov[$cont]['importe'] = $reg['importe'];
				$mov[$cont]['concepto'] = "PAGO DE IMPUESTOS EROGACIONES";
				$mov[$cont]['cod_mov'] = 103;
				$mov[$cont]['folio'] = $folio_cheque;
				$mov[$cont]['num_doc'] = $reg['num_documento'];

				$cheque[$cont]['num_cia']       = $num_cia;
				$cheque[$cont]['num_proveedor'] = 121;
				$cheque[$cont]['a_nombre']      = 'GOBIERNO DEL ESTADO DE MEXICO';
				$cheque[$cont]['fecha']         = $mov[$cont]['fecha'];
				$cheque[$cont]['importe']       = $mov[$cont]['importe'];
				$cheque[$cont]['iduser']        = $_SESSION['iduser'];
				$cheque[$cont]['imp']           = "FALSE";
				$cheque[$cont]['concepto']      = "PAGO DE IMPUESTOS EROGACIONES";
				$cheque[$cont]['cod_mov']       = 103;
				$cheque[$cont]['codgastos']     = 182;
				$cheque[$cont]['folio']         = $folio_cheque;
				$cheque[$cont]['cuenta']        = 2;
				$cheque[$cont]['poliza']        = "TRUE";

				$gasto[$cont]['num_cia']       = $num_cia;
				$gasto[$cont]['fecha']         = $mov[$cont]['fecha'];
				$gasto[$cont]['importe']       = $mov[$cont]['importe'];
				$gasto[$cont]['concepto']      = "PAGO DE IMPUESTOS EROGACIONES";
				$gasto[$cont]['codgastos']     = 182;
				$gasto[$cont]['folio']         = $folio_cheque;
				$gasto[$cont]['captura']       = "TRUE";

				$folio[$cont]['num_cia']   = $num_cia;
				$folio[$cont]['folio']     = $folio_cheque++;
				$folio[$cont]['reservado'] = "FALSE";
				$folio[$cont]['utilizado'] = "TRUE";
				$folio[$cont]['fecha']     = $mov[$cont]['fecha'];
				$folio[$cont]['cuenta']    = 2;

				$cont++;
			//}
		}

		$tmp  = $db->multiple_insert("estado_cuenta", $mov);
		$tmp .= $db->multiple_insert("cheques", $cheque);
		$tmp .= $db->multiple_insert("movimiento_gastos", $gasto);
		$tmp .= $db->multiple_insert("folios_cheque", $folio);
		$db->query($tmp);
	}

	// Query
	$sql = "";

	// Registros a omitir
	$id = array();

	// Conciliar movimientos catalogados como sobrantes de caja
	if (isset($_POST['cod_mov']))
		foreach ($_POST['cod_mov'] as $i => $cod_mov)
			if (isset($_POST['id' . $i])) {
				$sql .= "UPDATE mov_santander SET fecha_con = fecha, cod_mov = $cod_mov WHERE id = {$_POST['id' . $i]};\n";
				$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, concepto, cuenta, num_doc) SELECT num_cia, fecha, fecha, tipo_mov, importe, concepto, 2, num_documento FROM";
				$sql .= " mov_santander WHERE id = {$_POST['id' . $i]};\n";
				$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos + mov_santander.importe, saldo_libros = saldo_libros + mov_santander.importe WHERE";
				$sql .= " mov_santander.id = {$_POST['id' . $i]} AND num_cia = mov_santander.num_cia AND cuenta = 2;\n";
				$id[] = $_POST['id' . $i];
			}

	// *** [15-Nov-2006] Conciliar tarjetas de crédito ***
	// $tar = $db->query("SELECT * FROM mov_santander WHERE fecha_con IS NULL AND num_cia > 0 AND cod_banco = 38 ORDER BY num_cia, fecha");
	// if ($tar)
	// {
	// 	foreach ($tar as $reg)
	// 	{echo "<br>[{$reg['num_documento']}] TARJETA ID {$reg['id']}<br>CONCILIADO";
	// 		if ($reg['num_documento'] > 0 && $com = $db->query("SELECT * FROM mov_santander WHERE num_cia = $reg[num_cia] AND cod_banco IN (864, 898, 571, 2571, 2677) AND fecha_con IS NULL AND num_documento IN ($reg[num_documento], $reg[num_documento] + 1) ORDER BY id")) {
	// 			// Conciliar tarjeta
	// 			$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, num_doc) ";
	// 			$sql .= "SELECT num_cia, fecha - interval '1 day', fecha, 'FALSE', importe, 44, 'DEPOSITO TARJETA CREDITO', 2, $_SESSION[iduser], now(), num_documento FROM mov_santander WHERE id = $reg[id];\n";
	// 			$sql .= "UPDATE mov_santander SET fecha_con = fecha, cod_mov = 44, imprimir = 'TRUE', aut = 'FALSE', iduser = $_SESSION[iduser], timestamp = now() WHERE id = $reg[id];\n";
	// 			$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos + $reg[importe], saldo_libros = saldo_libros + $reg[importe] WHERE num_cia = $reg[num_cia] AND cuenta = 2;\n";
	// 			$id[] = $reg['id'];
	// 			// Conciliar comisiones de la tarjeta
	// 			foreach ($com as $c) {echo "<br>[{$reg['num_documento']}] " . (in_array($c['cod_banco'], array(864, 2677)) ? 'COMISION' : 'IVA') . " ID {$c['id']}";
	// 				if (!in_array($c['id'], $id)) {echo "<br>CONCILIADO";
	// 					$cod_mov = in_array($c['cod_banco'], array(864, 2677)) ? 46 : 10;
	// 					$concepto = in_array($c['cod_banco'], array(864, 2677)) ? "COM. TARJETA DE CREDITO" : "IVA POR COMISIONES";
	// 					$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, num_doc) ";
	// 					$sql .= "SELECT num_cia, fecha - interval '1 day', fecha, tipo_mov, importe, $cod_mov, '$concepto', 2, $_SESSION[iduser], now(), num_documento FROM mov_santander WHERE id = $c[id];\n";
	// 					$sql .= "UPDATE mov_santander SET cod_mov = $cod_mov, fecha_con = fecha, imprimir = 'TRUE', aut = 'FALSE', iduser = $_SESSION[iduser], timestamp = now() WHERE id = $c[id];\n";
	// 					$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos - $c[importe], saldo_libros = saldo_libros - $c[importe] WHERE num_cia = $c[num_cia] AND cuenta = 2;\n";
	// 					$id[] = $c['id'];
	// 				}
	// 				else
	// 				{
	// 					echo "<br><span style=\"color:red\">NO CONCILIADO</span>";
	// 				}
	// 			}
	// 		}
	// 	}
	// }die;

	// [13-Sep-2016] Nueva forma para conciliar tarjetas de crédito, basado en la compañía, fecha y número de documento
	$tarjetas = $db->query("SELECT
		num_cia,
		fecha,
		num_documento
	FROM
		mov_santander
	WHERE
		fecha_con IS NULL
		AND num_cia > 0
		AND cod_banco IN (38)
	GROUP BY
		num_cia,
		fecha,
		num_documento
	ORDER BY
		num_cia,
		fecha,
		num_documento");

	if ($tarjetas)
	{
		foreach ($tarjetas as $t)
		{
			if ($t['num_documento'] == '')
			{
				continue;
			}

			$registros = $db->query("SELECT
				*
			FROM
				mov_santander
			WHERE
				num_cia = {$t['num_cia']}
				AND fecha = '{$t['fecha']}'
				AND num_documento = {$t['num_documento']}
				AND fecha_con IS NULL
			ORDER BY
				id");

			if ($registros)
			{
				foreach ($registros as $row)
				{
					$sql .= "INSERT INTO estado_cuenta (
						num_cia,
						fecha,
						fecha_con,
						tipo_mov,
						importe,
						cod_mov,
						concepto,
						cuenta,
						iduser,
						timestamp,
						num_doc
					)
					SELECT
						num_cia,
						fecha - INTERVAL '1 DAY',
						fecha,
						tipo_mov,
						importe,
						CASE
							WHEN cod_banco IN (38) THEN
								44
							WHEN cod_banco IN (864, 2677) THEN
								46
							ELSE
								10
						END,
						CASE
							WHEN cod_banco IN (38) THEN
								'DEPOSITO TARJETA CREDITO'
							WHEN cod_banco IN (864, 2677) THEN
								'COM. TARJETA DE CREDITO'
							ELSE
								'IVA POR COMISIONES'
						END,
						2,
						{$_SESSION['iduser']},
						NOW(),
						num_documento
					FROM
						mov_santander
					WHERE
						id = {$row['id']};\n";

					$sql .= "UPDATE saldos
					SET
						saldo_bancos = saldo_bancos " . ($row['tipo_mov'] == 'f' ? '+' : '-') . " {$row['importe']},
						saldo_libros = saldo_libros " . ($row['tipo_mov'] == 'f' ? '+' : '-') . " {$row['importe']}
					WHERE
						num_cia = {$row['num_cia']}
						AND cuenta = 2;\n";

					$sql .= "UPDATE mov_santander
					SET
						cod_mov = (
							CASE
								WHEN cod_banco IN (38) THEN
									44
								WHEN cod_banco IN (864, 2677) THEN
									46
								ELSE
									10
							END
						),
						fecha_con = fecha,
						imprimir = TRUE,
						aut = FALSE,
						iduser = {$_SESSION['iduser']},
						timestamp = NOW()
					WHERE
						id = {$row['id']};\n";
				}
			}
		}
	}

	// *** [17-Nov-2007] Conciliar devoluciones de IVA con código 1183 y que el concepto contenga la cadena 'IVA CONVEN' ***
	// [09-Oct-2009] Agregado código 291 que contenga la cadena 'DEVOLUCION INGRESOS FEDERALES'
	$dev = $db->query("SELECT * FROM mov_santander WHERE fecha_con IS NULL AND num_cia > 0 AND ((cod_banco = 1183 AND concepto LIKE '%IVA C%') OR (cod_banco = 291 AND concepto LIKE '%DEVOLUCION INGRESOS FEDERALES')) ORDER BY num_cia, fecha");
	if ($dev)
		foreach ($dev as $reg) {
			// Conciliar devolución de IVA
			$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, num_doc) ";
			$sql .= "SELECT num_cia, fecha, fecha, 'FALSE', importe, 18, 'DEVOLUCION IMPUESTO', 2, $_SESSION[iduser], now(), num_documento FROM mov_santander WHERE id = $reg[id];\n";
			$sql .= "UPDATE mov_santander SET fecha_con = fecha, cod_mov = 18, imprimir = 'TRUE', aut = 'FALSE', iduser = $_SESSION[iduser], timestamp = now() WHERE id = $reg[id];\n";
			$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos + $reg[importe], saldo_libros = saldo_libros + $reg[importe] WHERE num_cia = $reg[num_cia] AND cuenta = 2;\n";
			$id[] = $reg['id'];
		}

	// *** [05-Ene-2009] Conciliar cargos de IDE con código 1790
	// Obtener tarjetas
	$dev = $db->query("SELECT * FROM mov_santander WHERE fecha_con IS NULL AND num_cia > 0 AND cod_banco = 1790 ORDER BY num_cia, fecha");
	if ($dev)
		foreach ($dev as $reg) {
			// Conciliar devolución de IVA
			$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, num_doc) ";
			$sql .= "SELECT num_cia, fecha, fecha, 'TRUE', importe, 78, 'IMPUESTO IDE', 2, $_SESSION[iduser], now(), num_documento FROM mov_santander WHERE id = $reg[id];\n";
			$sql .= "UPDATE mov_santander SET fecha_con = fecha, cod_mov = 78, imprimir = 'TRUE', aut = 'FALSE', iduser = $_SESSION[iduser], timestamp = now() WHERE id = $reg[id];\n";
			$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos - $reg[importe], saldo_libros = saldo_libros - $reg[importe] WHERE num_cia = $reg[num_cia] AND cuenta = 2;\n";
			$id[] = $reg['id'];
		}

	// *** [26-Nov-2008] Conciliar devoluciones de IDE con código 1183 y que el concepto contenga la cadena 'IDE PERSONAS MORALES' ***
	// Obtener tarjetas
	$dev = $db->query("SELECT * FROM mov_santander WHERE fecha_con IS NULL AND num_cia > 0 AND cod_banco = 1183 AND concepto LIKE '%IDE PERSONAS MORALES%' ORDER BY num_cia, fecha");
	if ($dev)
		foreach ($dev as $reg) {
			// Conciliar devolución de IVA
			$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, num_doc) ";
			$sql .= "SELECT num_cia, fecha, fecha, 'FALSE', importe, 82, 'DEVOLUCION IDE', 2, $_SESSION[iduser], now(), num_documento FROM mov_santander WHERE id = $reg[id];\n";
			$sql .= "UPDATE mov_santander SET fecha_con = fecha, cod_mov = 82, imprimir = 'TRUE', aut = 'FALSE', iduser = $_SESSION[iduser], timestamp = now() WHERE id = $reg[id];\n";
			$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos + $reg[importe], saldo_libros = saldo_libros + $reg[importe] WHERE num_cia = $reg[num_cia] AND cuenta = 2;\n";
			$id[] = $reg['id'];
		}

	// *** [03-Abr-2007] Conciliar AB INTERESES (60, 14 zapaterias) y RETENCION ISR (570, 510 zapaterias), que es el proximo registro a AB INTERESES
	$ab = $db->query("SELECT * FROM mov_santander WHERE fecha_con IS NULL AND num_cia > 0 AND cod_banco IN (14, 60) ORDER BY num_cia, fecha");
	if ($ab)
		foreach ($ab as $reg) {
			// Conciliar AB INTERESES
			$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, num_doc) ";
			$sql .= "SELECT num_cia, fecha, fecha, 'FALSE', importe, 11, concepto, 2, $_SESSION[iduser], now(), num_documento FROM mov_santander WHERE id = $reg[id];\n";
			$sql .= "UPDATE mov_santander SET fecha_con = fecha, cod_mov = 11, imprimir = 'TRUE', aut = 'TRUE', iduser = $_SESSION[iduser], timestamp = now() WHERE id = $reg[id];\n";
			$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos + $reg[importe], saldo_libros = saldo_libros + $reg[importe] WHERE num_cia = $reg[num_cia] AND cuenta = 2;\n";
			$id[] = $reg['id'];
			$isr = $db->query("SELECT * FROM mov_santander WHERE id = $reg[id] + 1 AND cod_banco IN (570, 510)");
			if ($isr) {
				$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, num_doc) ";
				$sql .= "SELECT num_cia, fecha, fecha, 'TRUE', importe, 12, concepto, 2, $_SESSION[iduser], now(), num_documento FROM mov_santander WHERE id = $reg[id] + 1;\n";
				$sql .= "UPDATE mov_santander SET fecha_con = fecha, cod_mov = 12, imprimir = 'TRUE', aut = 'TRUE', iduser = $_SESSION[iduser], timestamp = now() WHERE id = $reg[id] + 1;\n";
				$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos - {$isr[0]['importe']}, saldo_libros = saldo_libros - {$isr[0]['importe']} WHERE num_cia = $reg[num_cia] AND cuenta = 2;\n";
				$id[] = $reg['id'] + 1;
			}
		}

	// [29-Ago-2007] Conciliar Comisión sobre Certificación de Cheque para Pago de Luz
	// Importe comisión al:
	// *** 29-Ago-2007 = $8.05
	$com = $db->query("SELECT * FROM mov_santander WHERE fecha_con IS NULL AND num_cia > 0 AND cod_banco IN (501) AND importe = 8.05 ORDER BY num_cia, fecha");
	if ($com)
		foreach ($com as $reg)
			// Buscar que exista un pago con cheque a la compañía de luz con la misma fecha que la comisión
			if ($db->query("SELECT ms.id FROM mov_santander AS ms LEFT JOIN cheques AS c ON (c.num_cia = ms.num_cia AND c.folio = ms.num_documento AND c.importe = ms.importe AND c.cuenta = 2) WHERE ms.num_cia = $reg[num_cia] AND ms.fecha = '$reg[fecha]' AND num_proveedor = 216")) {
				$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, num_doc) ";
				$sql .= "SELECT num_cia, fecha, fecha, 'TRUE', importe, 51, 'COMISION PAGO LUZ EN BANCO', 2, $_SESSION[iduser], now(), num_documento FROM mov_santander WHERE id = $reg[id];\n";
				$sql .= "UPDATE mov_santander SET fecha_con = fecha, cod_mov = 51, imprimir = 'TRUE', aut = 'TRUE', iduser = $_SESSION[iduser], timestamp = now() WHERE id = $reg[id];\n";
				$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos - $reg[importe], saldo_libros = saldo_libros - $reg[importe] WHERE num_cia = $reg[num_cia] AND cuenta = 2;\n";
				$id[] = $reg['id'];
			}

	// [29-Ago-2007] Conciliar Comisión por Certifiación de Cheque y su IVA respectivo para los casos de Luz y Agua
	$che = $db->query("SELECT ms.id, ms.num_cia, codgastos FROM mov_santander AS ms LEFT JOIN cheques AS c ON (c.num_cia = ms.num_cia AND c.folio = ms.num_documento AND c.importe = ms.importe AND c.cuenta = 2) WHERE ms.fecha_con IS NULL AND codgastos IN (12, 13, 79) AND ms.num_cia < 900 ORDER BY ms.num_cia, num_documento");
	if ($che)
		foreach ($che as $reg)
			if ($com = $db->query("SELECT * FROM mov_santander WHERE num_cia = $reg[num_cia] AND cod_banco IN (810, 571) AND fecha_con IS NULL AND id IN ($reg[id] + 1, $reg[id] + 2)")) {
				foreach ($com as $c) {
					$cod_mov = $c['cod_banco'] == 810 ? 14 : 10;
					$concepto = $c['cod_banco'] == 810 ? "COM. CERTIF CHEQUE " . ($reg['codgastos'] == 79 ? 'AGUA' : 'LUZ') : "IVA POR COMISIONES";
					$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, num_doc) ";
					$sql .= "SELECT num_cia, fecha, fecha, tipo_mov, importe, $cod_mov, '$concepto', 2, $_SESSION[iduser], now(), num_documento FROM mov_santander WHERE id = $c[id];\n";
					$sql .= "UPDATE mov_santander SET cod_mov = $cod_mov, fecha_con = fecha, imprimir = 'TRUE', aut = 'FALSE', iduser = $_SESSION[iduser], timestamp = now() WHERE id = $c[id];\n";
					$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos - $c[importe], saldo_libros = saldo_libros - $c[importe] WHERE num_cia = $c[num_cia] AND cuenta = 2;\n";
					$id[] = $c['id'];
				}
			}

	// [07-May-2010] Conciliar Comisión por Certifiación de Cheque y su IVA respectivo
	$che = $db->query("SELECT ms.id, ms.num_cia, codgastos FROM mov_santander AS ms LEFT JOIN cheques AS c ON (c.num_cia = ms.num_cia AND c.folio = ms.num_documento AND c.importe = ms.importe AND c.cuenta = 2) WHERE ms.fecha_con IS NULL AND codgastos NOT IN (12, 13, 79) AND ms.num_cia < 900 AND c.folio IS NOT NULL ORDER BY ms.num_cia, num_documento");
	if ($che)
		foreach ($che as $reg)
			if ($com = $db->query("SELECT * FROM mov_santander WHERE num_cia = $reg[num_cia] AND cod_banco IN (810, 571) AND fecha_con IS NULL AND id IN ($reg[id] + 1, $reg[id] + 2)")) {
				foreach ($com as $c) {
					$cod_mov = $c['cod_banco'] == 810 ? 14 : 10;
					$concepto = $c['cod_banco'] == 810 ? "COM. CERTIF CHEQUE" : "IVA POR COMISIONES";
					$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, num_doc) ";
					$sql .= "SELECT num_cia, fecha, fecha, tipo_mov, importe, $cod_mov, '$concepto', 2, $_SESSION[iduser], now(), num_documento FROM mov_santander WHERE id = $c[id];\n";
					$sql .= "UPDATE mov_santander SET cod_mov = $cod_mov, fecha_con = fecha, imprimir = 'TRUE', aut = 'FALSE', iduser = $_SESSION[iduser], timestamp = now() WHERE id = $c[id];\n";
					$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos - $c[importe], saldo_libros = saldo_libros - $c[importe] WHERE num_cia = $c[num_cia] AND cuenta = 2;\n";
					$id[] = $c['id'];
				}
			}

	// [04-Oct-2010] Conciliar LIQ INTS PLAZO y su ISR
	$ab = $db->query("SELECT * FROM mov_santander WHERE fecha_con IS NULL AND num_cia > 0 AND cod_banco IN (83) ORDER BY num_cia, fecha");
	if ($ab)
		foreach ($ab as $reg) {
			// Conciliar LIQ INTS PLAZO
			$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, num_doc) ";
			$sql .= "SELECT num_cia, fecha, fecha, 'FALSE', importe, 11, concepto, 2, $_SESSION[iduser], now(), num_documento FROM mov_santander WHERE id = $reg[id];\n";
			$sql .= "UPDATE mov_santander SET fecha_con = fecha, cod_mov = 11, imprimir = 'TRUE', aut = 'TRUE', iduser = $_SESSION[iduser], timestamp = now() WHERE id = $reg[id];\n";
			$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos + $reg[importe], saldo_libros = saldo_libros + $reg[importe] WHERE num_cia = $reg[num_cia] AND cuenta = 2;\n";
			$id[] = $reg['id'];
			$isr = $db->query("SELECT * FROM mov_santander WHERE id = $reg[id] + 1 AND cod_banco IN (585)");
			if ($isr) {
				$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, num_doc) ";
				$sql .= "SELECT num_cia, fecha, fecha, 'TRUE', importe, 12, concepto, 2, $_SESSION[iduser], now(), num_documento FROM mov_santander WHERE id = $reg[id] + 1;\n";
				$sql .= "UPDATE mov_santander SET fecha_con = fecha, cod_mov = 12, imprimir = 'TRUE', aut = 'TRUE', iduser = $_SESSION[iduser], timestamp = now() WHERE id = $reg[id] + 1;\n";
				$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos - {$isr[0]['importe']}, saldo_libros = saldo_libros - {$isr[0]['importe']} WHERE num_cia = $reg[num_cia] AND cuenta = 2;\n";
				$id[] = $reg['id'] + 1;
			}
		}

	// Obtener todos los movimientos no conciliados del estado de cuenta
	$esc = $db->query("SELECT * FROM estado_cuenta WHERE fecha_con IS NULL AND cuenta = 2 ORDER BY num_cia, fecha");
	// Obtener todos los movimientos pendientes por conciliar del archivo de Santander
	$tmp = "SELECT * FROM mov_santander WHERE fecha_con IS NULL AND num_cia > 0";
	if (count($id) > 0) {
		$tmp .= " AND id NOT IN (";
		foreach ($id as $i => $val)
			$tmp .= $val . ($i < count($id) - 1 ? ", " : ")");
	}
	// No conciliar depositos de American Express (codigo 291) y concepto que contenga la cadena 'AMEXCO'
	$tmp .= " AND NOT (cod_banco = 291 AND concepto LIKE '%AMEXCO%')";
	$tmp .= " ORDER BY num_cia, fecha";
	$san = $db->query($tmp);
	// Obtener los movimientos autorizados
	$aut = $db->query("SELECT cod_mov, cod_banco, importe FROM catalogo_mov_aut_santander LEFT JOIN catalogo_mov_santander USING (cod_mov) ORDER BY cod_mov");
	// Obetner códigos bancarios de santander
	$cod = $db->query("SELECT * FROM catalogo_mov_santander ORDER BY cod_mov");

	// Buscar indice de cada compañía
	$index = array();
	$cia = NULL;
	for ($i = 0; $i < count($san); $i++)
		if ($cia != $san[$i]['num_cia']) {
			$cia = $san[$i]['num_cia'];
			$index[$cia] = $i;
		}

	$cod_omitidos = array(/*990, 740, 741, 681, 790*/512, 513, 556, 994);

	// 1er Barrido. Comparar movimientos de libros contra bancos
	for ($i = 0; $i < count($esc); $i++) {
		if (isset($index[$esc[$i]['num_cia']])) {
			$min = round(floatval($esc[$i]['importe']), 2) - 0.10;	// Importe mínimo del movimiento
			$max = round(floatval($esc[$i]['importe']), 2) + 0.10;	// Importe máximo del movimiento

			$cia = $esc[$i]['num_cia'];
			$j = $index[$esc[$i]['num_cia']];
			while ($j < count($san) || (isset($san[$j]['num_cia']) && $cia == $san[$j]['num_cia'])) {
				// Validar movimiento
				if ($san[$j]['fecha_con'] == "" && $esc[$i]['fecha_con'] == "" &&
					intval($san[$j]['num_cia']) == intval($esc[$i]['num_cia']) &&
					(intval($san[$j]['num_documento']) == intval($esc[$i]['folio']) || !in_array($san[$j]['cod_banco'], $cod_omitidos)) &&
					(floatval($san[$j]['importe']) >= $min && floatval($san[$j]['importe']) <= $max)) {
					// Validar código
					$ok = FALSE;
					$san[$j]['cod_banco'] = $san[$j]['cod_banco'] > 0 ? $san[$j]['cod_banco'] : "0";
					for ($k = 0; $k < count($cod); $k++)
						if ($esc[$i]['cod_mov'] == $cod[$k]['cod_mov'] && $san[$j]['cod_banco'] == $cod[$k]['cod_banco']) {
							$ok = TRUE;
							break;
						}

					// Si la validacion del código fue correcta
					if ($ok) {
						$esc[$i]['fecha_con'] = $san[$j]['fecha'];
						$san[$j]['fecha_con'] = date("d/m/Y");

						$num_doc = intval($san[$j]['num_documento'], 10) > 0 ? intval($san[$j]['num_documento'], 10) : 'NULL';

						$sql .= "UPDATE estado_cuenta SET fecha_con = '{$esc[$i]['fecha_con']}', importe = {$san[$j]['importe']}, timestamp = now(), iduser = $_SESSION[iduser], tipo_con = 1, num_doc = $num_doc WHERE id = {$esc[$i]['id']};\n";
						$sql .= "UPDATE mov_santander SET fecha_con = '{$san[$j]['fecha_con']}', cod_mov = {$esc[$i]['cod_mov']}, imprimir = 'TRUE', aut = 'FALSE', timestamp = now(), iduser = $_SESSION[iduser] WHERE id = {$san[$j]['id']};\n";
						$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos " . ($san[$j]['tipo_mov'] == "f" ? "+" : "-") . " {$san[$j]['importe']} WHERE num_cia = {$san[$j]['num_cia']} AND cuenta = 2;\n";
						break;
					}
					else
						$j++;
				}
				else
					$j++;
			}	// Fin while
		}	// Fin if
	}	// Fin for

	// 2o. Barrido. Comparar movimientos de libros contra autorizados
	if ($aut)
		for ($i = 0; $i < count($san); $i++)
			// Comparar cada movimiento de bancos con todos los autorizados
			for ($j = 0; $j < count($aut); $j++)
				if ($san[$i]['fecha_con'] == "" &&
					(int)$san[$i]['cod_banco'] == (int)$aut[$j]['cod_banco'] &&
					(float)$san[$i]['importe'] <= (float)$aut[$j]['importe']) {

					$san[$i]['fecha_con'] = $san[$i]['fecha'];

					// Actualizar fecha de conciliacion
					$sql .= "UPDATE mov_santander SET fecha_con = fecha, cod_mov = {$aut[$j]['cod_mov']}, aut = 'TRUE', imprimir = 'TRUE', timestamp = now(), iduser = $_SESSION[iduser] WHERE id = {$san[$i]['id']};\n";

					// Insertar movimiento conciliado en libros
					$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, concepto, tipo_mov, importe, cod_mov, folio, cuenta, timestamp, iduser, tipo_con, num_doc) ";
					$sql .= " SELECT num_cia, fecha, fecha_con, concepto, tipo_mov, importe, cod_mov, num_documento AS folio, 2, now(), $_SESSION[iduser], 5, num_documento FROM mov_santander WHERE id = {$san[$i]['id']};\n";

					// Actualizar saldos de bancos
					$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos" . ($san[$i]['tipo_mov'] == 'f' ? " + " : " - ") . (float)$san[$i]['importe'];
					$sql .= ", saldo_libros = saldo_libros" . ($san[$i]['tipo_mov'] == 'f' ? " + " : " - ") . (float)$san[$i]['importe'];
					$sql .= " WHERE num_cia = {$san[$i]['num_cia']} AND cuenta = 2";

					break;
				}

	if ($sql != "") $db->query($sql);

	// [21-Oct-2009] Actualizar saldos
	$query = 'SELECT num_cia, tipo_mov, sum(importe) AS importe FROM estado_cuenta WHERE cuenta = 2 AND fecha_con IS NULL GROUP BY num_cia, tipo_mov ORDER BY num_cia';
	$movs = $db->query($query);
	$sql = "UPDATE saldos SET saldo_libros = saldo_bancos WHERE cuenta = 2;\n";
	if ($movs) {
		foreach ($movs as $mov)
			$sql .= "UPDATE saldos SET saldo_libros = saldo_libros " . ($mov['tipo_mov'] == "f" ? "+" : "-") . " $mov[importe] WHERE num_cia = $mov[num_cia] AND cuenta = 2;\n";
	}

	if ($sql != "") $db->query($sql);

	header("location: ./ban_con_aut_san_v2.php?accion=saldos");
	die;
}

// [25-May-2007] Listar los saldos que estan por debajo de 100,000 pesos
if (isset($_GET['accion']) && $_GET['accion'] == 'saldos') {
	$sql = "SELECT num_cia, nombre_corto, saldo FROM saldo_santander LEFT JOIN catalogo_companias USING (num_cia) WHERE ((num_cia NOT IN (619, 630) AND saldo BETWEEN 0.01 AND 100000) OR (num_cia IN (619, 630) AND saldo BETWEEN 0.01 AND 150000)) AND aviso_saldo = 'TRUE' ORDER BY num_cia";
	$result = $db->query($sql);

	if (!$result)
		die(header('location: ./ban_con_aut_san_v2.php?accion=res'));

	$tpl->newBlock('saldos');
	foreach ($result as $reg) {
		$tpl->newBlock('saldo');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre_corto']);
		$tpl->assign('saldo', number_format($reg['saldo'], 2, '.', ','));

		if ($reg['num_cia'] == 630) {
			$tpl->assign('styles', ' style="font-weight:bold;color:#C00;"');
		}
	}
	$tpl->printToScreen();
	die;
}

// Listar resultados de conciliación
if (isset($_GET['accion']) && $_GET['accion'] == "res") {
	$cod_mov = $db->query("SELECT cod_mov, descripcion FROM catalogo_mov_santander GROUP BY cod_mov, descripcion ORDER BY cod_mov");
	function buscarCod($cod) {
		global $cod_mov;

		for ($i = 0; $i < count($cod_mov); $i++)
			if ($cod_mov[$i]['cod_mov'] == $cod)
				return $cod_mov[$i]['descripcion'];
	}

	// Movimientos conciliados
	$sql = "SELECT num_cia, clabe_cuenta2, nombre, fecha, importe, tipo_mov, num_documento, cod_mov, concepto";
	$sql .= " FROM mov_santander LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha_con IS NOT NULL AND imprimir = 'TRUE' AND aut = 'FALSE' ORDER BY num_cia, fecha";
	$result = $db->query($sql);

	if ($result) {
		$tpl->newBlock("palomeados");
		$tpl->assign("dia", date("d"));
		$tpl->assign("mes", mes_escrito(date("n")));
		$tpl->assign("anio", date("Y"));

		$num_cia = NULL;
		$total_abonos = 0;
		$total_cargos = 0;
		for ($i = 0; $i < count($result); $i++) {
			if ($num_cia != $result[$i]['num_cia']) {
				if ($num_cia != NULL) {
					$tpl->assign("cia_pal.abonos", number_format($abonos, 2, ".", ","));
					$tpl->assign("cia_pal.cargos", number_format($cargos, 2, ".", ","));
				}

				$num_cia = $result[$i]['num_cia'];
				$tpl->newBlock("cia_pal");
				$tpl->assign("num_cia", $num_cia);
				$tpl->assign("cuenta", $result[$i]['clabe_cuenta2']);
				$tpl->assign("nombre_cia", $result[$i]['nombre']);

				$abonos = 0;
				$cargos = 0;
			}
			$tpl->newBlock("fila_pal");
			$tpl->assign("fecha", $result[$i]['fecha']);
			$tpl->assign("abono", $result[$i]['tipo_mov'] == "f" ? number_format($result[$i]['importe'], 2, ".", ",") : "&nbsp;");
			$tpl->assign("cargo", $result[$i]['tipo_mov'] == "t" ? number_format($result[$i]['importe'], 2, ".", ",") : "&nbsp;");
			$tpl->assign("folio", $result[$i]['num_documento'] > 0 ? $result[$i]['num_documento'] : "&nbsp;");
			$tpl->assign("cod_mov", $result[$i]['cod_mov']);
			$tpl->assign("descripcion", buscarCod($result[$i]['cod_mov']));
			$tpl->assign("concepto", $result[$i]['concepto']);

			$abonos += $result[$i]['tipo_mov'] == "f" ? $result[$i]['importe'] : 0;
			$cargos += $result[$i]['tipo_mov'] == "t" ? $result[$i]['importe'] : 0;
			$total_abonos += $result[$i]['tipo_mov'] == "f" ? $result[$i]['importe'] : 0;
			$total_cargos += $result[$i]['tipo_mov'] == "t" ? $result[$i]['importe'] : 0;
		}
		if ($num_cia != NULL) {
			$tpl->assign("cia_pal.abonos", number_format($abonos, 2, ".", ","));
			$tpl->assign("cia_pal.cargos", number_format($cargos, 2, ".", ","));
			$tpl->assign("palomeados.total_abonos", number_format($total_abonos, 2, ".", ","));
			$tpl->assign("palomeados.total_cargos", number_format($total_cargos, 2, ".", ","));
		}
	}

	// Movimientos autorizados
	$sql = "SELECT num_cia, clabe_cuenta2, nombre, fecha, importe, tipo_mov, num_documento, cod_mov, concepto";
	$sql .= " FROM mov_santander LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha_con IS NOT NULL AND imprimir = 'TRUE' AND aut = 'TRUE' ORDER BY num_cia, fecha";
	$result = $db->query($sql);

	if ($result) {
		$tpl->newBlock("autorizados");
		$tpl->assign("dia", date("d"));
		$tpl->assign("mes", mes_escrito(date("n")));
		$tpl->assign("anio", date("Y"));

		$num_cia = NULL;
		$total_abonos = 0;
		$total_cargos = 0;
		for ($i = 0; $i < count($result); $i++) {
			if ($num_cia != $result[$i]['num_cia']) {
				if ($num_cia != NULL) {
					$tpl->assign("cia_aut.abonos", number_format($abonos, 2, ".", ","));
					$tpl->assign("cia_aut.cargos", number_format($cargos, 2, ".", ","));
				}

				$num_cia = $result[$i]['num_cia'];
				$tpl->newBlock("cia_aut");
				$tpl->assign("num_cia", $num_cia);
				$tpl->assign("cuenta", $result[$i]['clabe_cuenta2']);
				$tpl->assign("nombre_cia", $result[$i]['nombre']);

				$abonos = 0;
				$cargos = 0;
			}
			$tpl->newBlock("fila_aut");
			$tpl->assign("fecha", $result[$i]['fecha']);
			$tpl->assign("abono", $result[$i]['tipo_mov'] == "f" ? number_format($result[$i]['importe'], 2, ".", ",") : "&nbsp;");
			$tpl->assign("cargo", $result[$i]['tipo_mov'] == "t" ? number_format($result[$i]['importe'], 2, ".", ",") : "&nbsp;");
			$tpl->assign("folio", $result[$i]['num_documento'] > 0 ? $result[$i]['num_documento'] : "&nbsp;");
			$tpl->assign("cod_mov", $result[$i]['cod_mov']);
			$tpl->assign("descripcion", buscarCod($result[$i]['cod_mov']));
			$tpl->assign("concepto", $result[$i]['concepto']);

			$abonos += $result[$i]['tipo_mov'] == "f" ? $result[$i]['importe'] : 0;
			$cargos += $result[$i]['tipo_mov'] == "t" ? $result[$i]['importe'] : 0;
			$total_abonos += $result[$i]['tipo_mov'] == "f" ? $result[$i]['importe'] : 0;
			$total_cargos += $result[$i]['tipo_mov'] == "t" ? $result[$i]['importe'] : 0;
		}
		if ($num_cia != NULL) {
			$tpl->assign("cia_aut.abonos", number_format($abonos, 2, ".", ","));
			$tpl->assign("cia_aut.cargos", number_format($cargos, 2, ".", ","));
			$tpl->assign("autorizados.total_abonos", number_format($total_abonos, 2, ".", ","));
			$tpl->assign("autorizados.total_cargos", number_format($total_cargos, 2, ".", ","));
		}
	}

	// Quitar marca de impresión
	$db->query("UPDATE mov_santander SET imprimir = 'FALSE' WHERE imprimir = 'TRUE'");

	// Movimientos pendientes
	/*$sql = "SELECT num_cia, clabe_cuenta2, nombre, fecha, importe, tipo_mov, num_documento, cod_banco, concepto FROM mov_santander LEFT JOIN catalogo_companias USING (num_cia)";
	$sql .= " WHERE fecha_con IS NULL ORDER BY num_cia, fecha";
	$result = $db->query($sql);

	if ($result) {
		$tpl->newBlock("pendientes");
		$tpl->assign("dia", date("d"));
		$tpl->assign("mes", mes_escrito(date("n")));
		$tpl->assign("anio", date("Y"));

		$num_cia = NULL;
		$total_abonos = 0;
		$total_cargos = 0;
		for ($i = 0; $i < count($result); $i++) {
			if ($num_cia != $result[$i]['num_cia']) {
				if ($num_cia != NULL) {
					$tpl->assign("cia_pen.abonos", number_format($abonos, 2, ".", ","));
					$tpl->assign("cia_pen.cargos", number_format($cargos, 2, ".", ","));
				}

				$num_cia = $result[$i]['num_cia'];
				$tpl->newBlock("cia_pen");
				$tpl->assign("num_cia", $num_cia);
				$tpl->assign("cuenta", $result[$i]['clabe_cuenta2']);
				$tpl->assign("nombre_cia", $result[$i]['nombre']);

				$abonos = 0;
				$cargos = 0;
			}
			$tpl->newBlock("fila_pen");
			$tpl->assign("fecha", $result[$i]['fecha']);
			$tpl->assign("abono", $result[$i]['tipo_mov'] == "f" ? number_format($result[$i]['importe'], 2, ".", ",") : "&nbsp;");
			$tpl->assign("cargo", $result[$i]['tipo_mov'] == "t" ? number_format($result[$i]['importe'], 2, ".", ",") : "&nbsp;");
			$tpl->assign("folio", $result[$i]['num_documento'] > 0 ? $result[$i]['num_documento'] : "&nbsp;");
			$tpl->assign("cod_banco", $result[$i]['cod_banco']);
			$tpl->assign("concepto", $result[$i]['concepto']);

			$abonos += $result[$i]['tipo_mov'] == "f" ? $result[$i]['importe'] : 0;
			$cargos += $result[$i]['tipo_mov'] == "t" ? $result[$i]['importe'] : 0;
			$total_abonos += $result[$i]['tipo_mov'] == "f" ? $result[$i]['importe'] : 0;
			$total_cargos += $result[$i]['tipo_mov'] == "t" ? $result[$i]['importe'] : 0;
		}
		if ($num_cia != NULL) {
			$tpl->assign("cia_pen.abonos", number_format($abonos, 2, ".", ","));
			$tpl->assign("cia_pen.cargos", number_format($cargos, 2, ".", ","));
			$tpl->assign("pendientes.total_abonos", number_format($total_abonos, 2, ".", ","));
			$tpl->assign("pendientes.total_cargos", number_format($total_cargos, 2, ".", ","));
		}
	}*/

	$tpl->printToScreen();
	die;
}

// Si no se ha cargado archivo, solicitarlo
$tpl->newBlock("archivo");

$sql = 'SELECT * FROM (SELECT num_cia, nombre_corto, clabe_cuenta2 AS cuenta, 2 AS banco, round(saldo_bancos::numeric, 2) AS saldo_bancos, round(saldo::numeric, 2) AS saldo, COALESCE((SELECT round(sum(CASE WHEN tipo_mov = \'FALSE\' THEN importe ELSE -importe END)::numeric, 2) FROM mov_santander WHERE num_cia = ss.num_cia AND fecha_con IS NULL), 0) AS pendientes, CASE WHEN tsdif IS NOT NULL THEN now()::date - tsdif::date ELSE 0 END AS dias FROM saldos ss LEFT JOIN saldo_santander USING (num_cia) LEFT JOIN catalogo_companias cc USING (num_cia) WHERE cuenta = 2) result WHERE num_cia < 900 AND saldo_bancos + pendientes - saldo <> 0';
if ($db->query($sql)) {
	$tpl->assign('mensaje', '<p style="font-size:14pt;font-family:Arial, Helvetica, sans-serif;color:#C00;font-weight:bold;">Existen diferencias en los saldos.</p>');
	if (!in_array($_SESSION['iduser'], array(1)))
	{
		$tpl->assign('disabled', ' disabled');
	}
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>
