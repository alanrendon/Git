<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("MODIFICANDO");

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
	header("location: ./ban_con_aut_san.php");
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_con_aut_san.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Cargar y validar archivo de datos al sistema
if (isset($_FILES['userfile1'])) {
	// *** Verificar que el tipo de archivo sea texto plano
	for ($i = 0; $i < 5; $i++)
		if ($_FILES['userfile' . $i]['tmp_name'] != "" && !stristr(mime_content_type($_FILES['userfile' . $i]['tmp_name']), "gzip")) {
			header("location: ./ban_con_aut_san.php?codigo_error=1");
			die();
		}
	
	// *** Verificar que los archivos no seas iguales entre ellos
	for ($i = 0; $i < 5; $i++)
		for ($j = $i + 1; $j < 5; $j++)
			if ($_FILES['userfile' . $i]['tmp_name'] != "" && $_FILES['userfile' . $j]['tmp_name'] != "" && md5(implode(gzfile($_FILES['userfile' . $i]['tmp_name']))) == md5(implode(gzfile($_FILES['userfile' . $j]['tmp_name'])))) {
				header("location: ./ban_con_aut_san.php?codigo_error=2&archivo1={$_FILES['userfile'. $i]['name']}&archivo2={$_FILES['userfile' . $j]['name']}");
				die;
			}
	
	// *** Verificar que el archivo no haya sido cargado en el sistema
	for ($i = 0; $i < 5; $i++)
		if ($_FILES['userfile' . $i]['tmp_name'] != "" && $db->query("SELECT hash FROM mov_santander WHERE hash = '" . /*md5_file($_FILES['userfile' . $i]['tmp_name'])*/md5(implode(gzfile($_FILES['userfile' . $i]['tmp_name']))) . "' LIMIT 1")) {
			header("location: ./ban_con_aut_san.php?codigo_error=3&archivo={$_FILES['userfile'. $i]['name']}");
			die;
		}
	
	// *** Ordenar archivos por antigüedad
	$archivo = array();
	
	// Obtener nombre de los archivos y almacenarlos en un array
	$count = 0;
	for ($i = 0; $i < 5; $i++)
		if ($_FILES['userfile' . $i]['tmp_name'] != "") {
			$archivo[$count]['name'] = $_FILES['userfile' . $i]['name'];
			$archivo[$count]['tmp']  = $_FILES['userfile' . $i]['tmp_name'];
			$count++;
		}
	
	// Función de comparacion para ordenar los datos
	function cmp($a, $b) {
		// Descomponer nombre del archivo A
		$num_a  = (int)substr($a['name'], 11, 12);
		$anio_a = (int)substr($a['name'], 23, 4);
		$mes_a  = (int)substr($a['name'], 27, 2);
		$dia_a  = (int)substr($a['name'], 29, 2);
		
		// Descomponer nombre del archivo B
		$num_b  = (int)substr($b['name'], 11, 12);
		$anio_b = (int)substr($b['name'], 23, 4);
		$mes_b  = (int)substr($b['name'], 27, 2);
		$dia_b  = (int)substr($b['name'], 29, 2);
		
		// Timestamp para comparacion
		$ts_a = mktime(0, 0, 0, $mes_a, $dia_a, $anio_a);
		$ts_b = mktime(0, 0, 0, $mes_b, $dia_b, $anio_b);
		
		if ($ts_a == $ts_b) {
			if ($num_a == $num_b)
				return 0;
			else
				return $num_a < $num_b ? -1 : 1;
		}
		else
			return $ts_a < $ts_b ? -1 : 1;
	}
	
	// Ordenar arreglo de archivos
	usort($archivo, "cmp");
	
	// Cargar catálogo de compañías
	$catCias = $db->query("SELECT num_cia, clabe_cuenta2 FROM catalogo_companias WHERE clabe_cuenta2 IS NOT NULL ORDER BY clabe_cuenta2");
	
	$i = 0;
	$j = 0;
	$saldo = array();
	for ($i = 0; $i < count($archivo); $i++) {
		// Abrir el archivo
		$lines = gzfile($archivo[$i]['tmp']);
		$hash[$i] = md5(implode($lines));
		
		// Recorrer el archivo
		foreach ($lines as $line) {
			$data[$j]['num_cia'] = buscar_cia($catCias, trim(substr($line, 0, 11)));
			$data[$j]['cuenta'] = trim(substr($line, 0, 11));
			$data[$j]['fecha'] = substr($line, 18, 2) . "/" . substr($line, 16, 2) . "/" . substr($line, 20, 4);
			$data[$j]['cod_banco'] = substr($line, 32, 4);
			$data[$j]['descripcion'] = trim(substr($line, 36, 40));
			$data[$j]['tipo_mov'] = substr($line, 76, 1) == "+" ? "FALSE" : "TRUE";
			$data[$j]['importe'] = floatval(substr($line, 77, 12) . "." . substr($line, 89, 2));
			$data[$j]['num_documento'] = intval(substr($line, 105, 8)) > 0 ? intval(substr($line, 105, 8)) : "";
			$data[$j]['concepto'] = trim(trim(substr($line, 36, 40)) . " " . trim(substr($line, 113, 40)));
			$data[$j]['hash'] = $hash[$i];
			
			// Saldo para la compañía
			$saldo[$data[$j]['num_cia']]['saldo'] = floatval(substr($line, 91, 12) . "." . substr($line, 103, 2));
			$saldo[$data[$j]['num_cia']]['fecha'] = $data[$j]['fecha'];
			
			$j++;
		}
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
		$sql = "";
		foreach ($hash as $value)
			$sql .= "DELETE FROM mov_santander WHERE hash = '$value';\n";
		$db->query($sql);
		
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
	
	header("location: ./ban_con_aut_san.php?accion=con");
	die;
}

// Revisar códigos de sobrante
if (isset($_GET['accion']) && $_GET['accion'] == "codsob") {
	$sql = "SELECT id, num_cia, nombre_corto, cuenta, importe, cod_banco, concepto FROM mov_santander LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha_con IS NULL";
	$sql .= " AND num_cia > 0 AND cod_banco IN (142, 143) ORDER BY num_cia, fecha";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ban_con_aut_san.php?accion=con");
		die;
	}
	
	$cods = $db->query("SELECT cod_mov, descripcion FROM catalogo_mov_santander WHERE tipo_mov = 'FALSE' GROUP BY cod_mov, descripcion ORDER BY cod_mov");
	
	$tpl->newBlock("cod_sob");
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
	// Query
	$sql = "";
	
	// Conciliar movimientos catalogados como sobrantes de caja
	if (isset($_POST['cod_mov']))
		foreach ($_POST['cod_mov'] as $i => $cod_mov)
			if (isset($_POST['id' . $i])) {
				$sql .= "UPDATE mov_santander SET fecha_con = fecha, cod_mov = $cod_mov WHERE id = {$_POST['id' . $i]};\n";
				$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, concepto, cuenta) SELECT num_cia, fecha, fecha, tipo_mov, importe, concepto, 2 FROM";
				$sql .= " mov_santander WHERE id = {$_POST['id' . $i]};\n";
				$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos + mov_santander.importe, saldo_libros = saldo_libros + mov_santander.importe WHERE";
				$sql .= " mov_santander.id = {$_POST['id' . $i]} AND num_cia = mov_santander.num_cia AND cuenta = 2;\n";
			}
	
	// Obtener todos los movimientos no conciliados del estado de cuenta
	$esc = $db->query("SELECT * FROM estado_cuenta WHERE fecha_con IS NULL AND cuenta = 2 ORDER BY num_cia, fecha");
	// Obtener todos los movimientos pendientes por conciliar del archivo de Santander
	$tmp = "SELECT * FROM mov_santander WHERE fecha_con IS NULL AND num_cia > 0";
	if (isset($_POST['id'])) {
		$tmp .= " AND id NOT IN (";
		foreach ($_POST['id'] as $i => $id)
			$tmp .= $id . ($i < count($_POST['id']) - 1 ? ", " : ")");
	}
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
			$min = floor(floatval($esc[$i]['importe']));		// Importe mínimo del movimiento
			$max = floor(floatval($esc[$i]['importe'])) + 0.99;	// Importe máximo del movimiento
			
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
						
						$sql .= "UPDATE estado_cuenta SET fecha_con = '{$esc[$i]['fecha_con']}', importe = {$san[$j]['importe']}, timestamp = now(), iduser = $_SESSION[iduser], tipo_con = 1 WHERE id = {$esc[$i]['id']};\n";
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
					$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, concepto, tipo_mov, importe, cod_mov, folio, cuenta, timestamp, iduser, tipo_con) ";
					$sql .= "SELECT num_cia, fecha, fecha_con, concepto, tipo_mov, importe, cod_mov, num_documento AS folio, 2, now(), $_SESSION[iduser], 5 FROM mov_santander WHERE id = {$san[$i]['id']};\n";
					
					// Actualizar saldos de bancos
					$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos" . ($san[$i]['tipo_mov'] == 'f' ? " + " : " - ") . (float)$san[$i]['importe'];
					$sql .= ", saldo_libros = saldo_libros" . ($san[$i]['tipo_mov'] == 'f' ? " + " : " - ") . (float)$san[$i]['importe'];
					$sql .= " WHERE num_cia = {$san[$i]['num_cia']} AND cuenta = 2";
					
					break;
				}
	
	if ($sql != "") $db->query($sql);
	header("location: ./ban_con_aut_san.php?accion=res");
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

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>