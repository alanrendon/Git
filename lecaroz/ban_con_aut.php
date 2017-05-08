<?php
// CONCILIACION AUTOMÁTICA
// Tabla 'estado_cuenta'
// Menu 'Banco->Conciliación automática'

//define ('IDSCREEN',1221); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "El archivo ya fue cargado en el sistema";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/ban/ban_con_aut.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Cancelar movimientos
if (isset($_GET['cancelar'])) {
	//ejecutar_script("TRUNCATE TABLE mov_banorte",$dsn);
	// Cambiar permisos al directorio
	@chmod("/var/www/html/lecaroz/mov",0777);
	// Borrar archivo
	@unlink($_SESSION['filename']);
	// Borrar registro hash MD5 de la tabla 'upload_files'
	ejecutar_script("DELETE FROM upload_files WHERE hash = '".strtolower($_SESSION['hash'])."'",$dsn);
	// Borrar registros copn hash MD5 de la tabla de 'mov_banorte'
	ejecutar_script("DELETE FROM mov_banorte WHERE hash = '".$_SESSION['hash']."'",$dsn);
	// Cambiar permisos del directorio
	@chmod("/var/www/html/lecaroz/mov",0000);
	// Eliminar variables de sesión
	unset($_SESSION['filename']);
	unset($_SESSION['hash']);
}

// Pedir archivo de Banorte
if (!isset($_POST['MAX_FILE_SIZE']) && !isset($_GET['val_cue']) && !isset($_GET['val_cod']) && !isset($_GET['cod3']) && !isset($_GET['conciliar']) && !isset($_GET['saldos']) && !isset($_GET['resultados'])) {
	unset($_SESSION['mov_con']);
	unset($_SESSION['mov_aut']);

	$tpl->newBlock("enviar_archivo");

	$sql = '
		SELECT
			*
		FROM
			(
				SELECT
					num_cia,
					nombre_corto,
					cuenta,
					banco,
					CASE
						WHEN saldo_bancos IS NULL THEN
							0
						ELSE
							saldo_bancos
					END
						AS saldo_bancos,
					saldo,
					CASE
						WHEN pendientes IS NULL THEN
							0
						ELSE
							pendientes
					END
						AS pendientes
				FROM
					(
						SELECT
							num_cia,
							nombre_corto,
							clabe_cuenta
								AS cuenta,
							1
								AS banco,
							ROUND(saldo_bancos::numeric, 2)
								AS saldo_bancos,
							ROUND(saldo::numeric, 2)
								AS saldo,
							(
								SELECT
									ROUND(SUM(
										CASE
											WHEN tipo_mov = FALSE THEN
												importe
											ELSE
												-importe
										END
									)::numeric, 2)
								FROM
									mov_banorte
								WHERE
									num_cia = ss.num_cia
									AND fecha_con IS NULL
							)
								AS pendientes,
							CASE
								WHEN tsdif IS NOT NULL THEN
									now()::date - tsdif::date
								ELSE
									0
							END
								AS dias
						FROM
							saldos ss
							LEFT JOIN saldo_banorte
								USING (num_cia)
							LEFT JOIN catalogo_companias cc
								USING (num_cia)
						WHERE
							cuenta = 1
					) result
			) result
		WHERE
			num_cia < 900
			AND saldo_bancos + pendientes - saldo <> 0
		ORDER BY
			num_cia
	';
	if (ejecutar_script($sql, $dsn)) {
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
}
// Analizar archivo, obtener movimientos
else if (isset($_POST['MAX_FILE_SIZE'])) {
	// Datos del archivo
	$nombre_archivo = $_FILES['userfile']['name'];
	$tipo_archivo   = $_FILES['userfile']['type'];
	$tamano_archivo = $_FILES['userfile']['size'];
	$archivo_temp   = $_FILES['userfile']['tmp_name'];

	// Comprobar características del fichero
	// El archivo debe ser texto plano, no mayor a 5 Megabytes
	if (!/*(stristr($tipo_archivo,"text/plain") && $tamano_archivo < 5242880)*/1) {
		echo $tipo_archivo." ". $tamano_archivo;
		$tpl->newBlock("message");
		$tpl->assign("message","El tipo o tamaño del archivo no es correcto.<br>Se permiten archivos .txt y de tamaño no mayor a 5 MB.");
		$tpl->printToScreen();
		die;
	}
	else {
		// Cambiar permisos al directorio
		@chmod("/var/www/html/lecaroz/mov",0777);
		// Especificar la ruta de almacenamiento y el nombre del archivo
		$nombre_archivo_server = "/var/www/html/lecaroz/mov/$nombre_archivo";
		$_SESSION['filename'] = $nombre_archivo_server;
		// Si ya existe el archivo en el servidor, no sobreescribir
		if (!existe_registro("upload_files",array("hash"),array(md5_file($archivo_temp)),$dsn)) {
			// Mover archivo al subdirectorio
			if (/*move_uploaded_file($_FILES['userfile']['tmp_name'],$nombre_archivo_server)*/1) {
				// Crear cadena hash MD5
				$_SESSION['hash'] = md5_file(/*$nombre_archivo_server*/$_FILES['userfile']['tmp_name']);

				// Cargar depositos a la base de datos
				$fd = fopen(/*$nombre_archivo_server*/$_FILES['userfile']['tmp_name'],"r");
				$count=0;
				$count_cia=0;
				while (!feof($fd)) {
					// Obtener cadena del archivo y almacenarlo en el buffer
					$buffer = fgets($fd);

					// Dividir cadena en secciones y almacenarlas en variables
					if ($buffer != "") {
						$tipo_reg = substr($buffer,0,2);
						switch ($tipo_reg) {
							// Registro de cabecera de cuenta
							case 11:
								$cuenta = substr($buffer,25,10);
								$result = ejecutar_script("SELECT num_cia,nombre FROM catalogo_companias WHERE clabe_cuenta LIKE '%$cuenta'",$dsn);
								if ($result) {
									$cia    = $result[0]['num_cia'];
									$nombre = $result[0]['nombre'];
								}
								else {
									$cia    = "0";
									$nombre = substr($buffer,66,26);
								}
							break;
							// Registro principal de movimientos
							case 22:
								$mov['num_cia'.$count]       = $cia;
								$mov['nombre'.$count]        = $nombre;
								$mov['cuenta'.$count]        = $cuenta;
								$mov['fecha'.$count]         = substr($buffer,14,2)."/".substr($buffer,12,2)."/".substr($buffer,10,2);
								$mov['tipo_mov'.$count]      = (substr($buffer,27,1) == 1)?"TRUE":"FALSE";
								$mov['importe'.$count]       = number_format(substr($buffer,28,12).".".substr($buffer,40,2),2,".","");
								$mov['num_documento'.$count] = ((int)substr($buffer,42,10) >= 0)?(int)substr($buffer,42,10):"";
								$mov['concepto'.$count]      = trim(substr($buffer,52,12).substr($buffer,64,16));
								$mov['fecha_con'.$count]     = "";
								$mov['cod_banco'.$count]     = ((int)substr($buffer,23,4) >= 0)?substr($buffer,23,4):"";
								$mov['cod_mov'.$count]       = "";
								$mov['aut'.$count]           = "FALSE";
								$mov['hash'.$count]          = $_SESSION['hash'];
								$mov['imprimir'.$count]      = "FALSE";
								$count++;
							break;
							// Registro final de cuenta
							case 33:
								$saldo_fin['num_cia'] = $cia;
								$saldo_fin['saldo'] = number_format(substr($buffer,74,12).".".substr($buffer,86,2),2,".","");

								if (existe_registro("saldo_banorte",array("num_cia"),array($cia),$dsn))
									ejecutar_script("UPDATE saldo_banorte SET saldo=".$saldo_fin['saldo']." WHERE num_cia = $cia",$dsn);
								else
									ejecutar_script("INSERT INTO saldo_banorte (num_cia,saldo) VALUES ($cia,".$saldo_fin['saldo'].")",$dsn);
							break;
						}
					}
				}
				fclose($fd);

				// Almacenar movimientos en mov_banorte
				$db = new DBclass($dsn,"mov_banorte",$mov);
				$db->xinsertar();

				/*****************************************************************************************************************************/
				// PROCESO TEMPORAL --- BORRAR TODOS LOS MOVIMIENTOS MENOS ROSTICERIAS Y DEPOSITOS
				// Borrar todas las compañías menos rosticerias
				//ejecutar_script("DELETE FROM mov_banorte WHERE num_cia < 100",$dsn);
				// Borrar todos los cheques
				//ejecutar_script("DELETE FROM mov_banorte WHERE concepto LIKE '%CHEQUE PAGADO%' OR concepto LIKE '%CHEQUE CAMARA%'",$dsn);

				/*****************************************************************************************************************************/

				// Almacenar entrada de archivo
				ejecutar_script("INSERT INTO upload_files (hash) VALUES ('".$_SESSION['hash']."')",$dsn);
			}
			else {
				$tpl->newBlock("message");
				$tpl->assign("message","Ocurrió algún error al cargar el archivo. No pudo guardarse");
				$tpl->printToScreen();
				die;
			}
			// Cambiar permisos del directorio
			@chmod("/var/www/html/lecaroz/mov",0000);
		}
		// Si ya existe el archivo en el server, mandarlo a la página de resultados
		else {
			// Crear cadena hash MD5
			header("location: ./ban_con_aut.php?codigo_error=1");
			die;
		}

		header("location: ./ban_con_aut.php?val_cue=1");
		die;
	}
}
// Validar cuentas
if (isset($_GET['val_cue'])) {
	$sql = "SELECT nombre,cuenta FROM mov_banorte WHERE num_cia = 0 AND fecha_con IS NULL GROUP BY cuenta,nombre ORDER BY nombre";
	$result = ejecutar_script($sql,$dsn);

	if ($result) {
		$tpl->newBlock("val_cue");
		for ($i=0; $i<count($result); $i++) {
			$tpl->newBlock("fila_cue");
			$tpl->assign("cuenta",$result[$i]['cuenta']);
			$tpl->assign("nombre",$result[$i]['nombre']);
		}

		$tpl->printToScreen();
		die;
	}
	else {
		header("location: ./ban_con_aut.php?val_cod=1");
		die;
	}
}
// Validar códigos de movimientos bancarios
if (isset($_GET['val_cod'])) {
	$sql = "SELECT cod_banco FROM catalogo_mov_bancos GROUP BY cod_banco ORDER BY cod_banco ASC";
	$cod_mov = ejecutar_script($sql,$dsn);

	$sql = "SELECT DISTINCT ON(cod_banco) num_cia,cod_banco,concepto FROM mov_banorte WHERE num_cia > 0/* AND num_cia < 900*/ AND fecha_con IS NULL";
	$mov_ban = ejecutar_script($sql,$dsn);

	$count = 0;
	for ($i=0; $i<count($mov_ban); $i++) {
		$ok = FALSE;
		for ($j=0; $j<count($cod_mov); $j++)
			if ($mov_ban[$i]['cod_banco'] == $cod_mov[$j]['cod_banco'])
				$ok = TRUE;
		if (!$ok) {
			$cod[$count]['cod_banco'] = $mov_ban[$i]['cod_banco'];
			$cod[$count]['concepto']  = $mov_ban[$i]['concepto'];
			$cod[$count]['num_cia']   = $mov_ban[$i]['num_cia'];
			$count++;
		}
	}

	if ($count > 0) {
		$tpl->newBlock("val_cod");
		for ($i=0; $i<$count; $i++) {
			$tpl->newBlock("fila_cod");
			$tpl->assign("cod_mov",$cod[$i]['cod_banco']);
			$tpl->assign("concepto",$cod[$i]['concepto']);
		}

		$sql = "DELETE FROM mov_banorte WHERE hash = '".strtoupper($_SESSION['hash'])."';";
		$sql .= "DELETE FROM upload_files WHERE hash = '".$_SESSION['hash']."';";
		ejecutar_script($sql,$dsn);

		unset($_SESSION['hash']);

		$tpl->printToScreen();
		die;
	}
	else {
		header("location: ./ban_con_aut.php?cod3=1");
		die;
	}
}
// Mostrar todos los movimientos con código 3
if (isset($_GET['cod3'])) {
	$sql = "SELECT * FROM mov_banorte WHERE fecha_con IS NULL AND num_cia > 0 AND num_cia < 900 AND cod_banco = 3 ORDER BY num_cia,fecha ASC";
	$mov_ban = ejecutar_script($sql,$dsn);
	$sql = "SELECT cod_mov,descripcion FROM catalogo_mov_bancos WHERE cod_banco = 3 ORDER BY cod_mov ASC";
	$cod_mov = ejecutar_script($sql,$dsn);

	if ($mov_ban) {
		$tpl->newBlock("cod3");
		$tpl->assign("numfilas",count($mov_ban));
		for ($i=0; $i<count($mov_ban); $i++) {
			$tpl->newBlock("fila3");
			$tpl->assign("i",$i);
			$tpl->assign("id",$mov_ban[$i]['id']);
			$tpl->assign("num_cia",$mov_ban[$i]['num_cia']);
			$tpl->assign("nombre_cia",$mov_ban[$i]['nombre']);
			$tpl->assign("cuenta",$mov_ban[$i]['cuenta']);
			$tpl->assign("deposito",$mov_ban[$i]['importe']);
			$tpl->assign("fdeposito",number_format($mov_ban[$i]['importe'],2,".",","));
			$tpl->assign("cod_banco",$mov_ban[$i]['cod_banco']);
			$tpl->assign("concepto",$mov_ban[$i]['concepto']);
			for ($j=0; $j<count($cod_mov); $j++) {
				$tpl->newBlock("cod_mov");
				$tpl->assign("cod_mov",$cod_mov[$j]['cod_mov']);
				$tpl->assign("descripcion",$cod_mov[$j]['descripcion']);
				if ($cod_mov[$j]['cod_mov'] == /*$mov_ban[$i]['cod_mov']*/13) $tpl->assign("selected","selected");
			}
		}
		$tpl->printToScreen();
		die;
	}
	else {
		header("location: ./ban_con_aut.php?conciliar=1");
	}
}
// Conciliar movimientos de libros y de bancos
if (isset($_GET['conciliar'])) {
	// Si hubo movimientos con código 3, conciliarlos automáticamente
	if (isset($_POST['numfilas'])) {
		for ($i=0; $i<$_POST['numfilas']; $i++) {
			if (isset($_POST['id'.$i])) {
				// Actualizar fecha de conciliación
				$sql = "UPDATE mov_banorte SET fecha_con = fecha,cod_mov = ".$_POST['cod_mov'.$i].",aut = 'TRUE',imprimir = 'TRUE' WHERE id = ".$_POST['id'.$i];
				ejecutar_script($sql,$dsn);
				// Insertar movimiento conciliado en libros
				$sql = "INSERT INTO estado_cuenta (num_cia,fecha,fecha_con,concepto,tipo_mov,importe,cod_mov,cuenta) SELECT num_cia,fecha,fecha_con,concepto,tipo_mov,importe,cod_mov,1 FROM mov_banorte WHERE id = ".$_POST['id'.$i];
				ejecutar_script($sql,$dsn);

				//if (existe_registro("saldos",array("num_cia"),array($_POST['num_cia'.$i]),$dsn))
					ejecutar_script("UPDATE saldos SET saldo_bancos=saldo_bancos+".$_POST['importe'.$i].",saldo_libros=saldo_libros+".$_POST['importe'.$i]." WHERE num_cia=".$_POST['num_cia'.$i]." AND cuenta=1",$dsn);
				/*else
					ejecutar_script("INSERT INTO saldos (num_cia,saldo_libros,saldo_bancos) VALUES (".$_POST['num_cia'.$i].",".$_POST['importe'.$i].",".$_POST['importe'.$i].")",$dsn);*/
			}
		}
	}

	// Conciliar tarjetas de credito y su iva
	$tar = ejecutar_script("SELECT * FROM mov_banorte WHERE fecha_con IS NULL AND num_cia > 0 AND cod_banco = 60 ORDER BY num_cia, fecha", $dsn);
	if ($tar) {
		$sql = '';
		foreach ($tar as $reg)
			if ($com = ejecutar_script("SELECT * FROM mov_banorte WHERE num_cia = $reg[num_cia] AND cod_banco IN (600, 601) AND fecha_con IS NULL AND id IN ($reg[id] + 1, $reg[id] + 2)", $dsn)) {
				// Conciliar tarjeta
				$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp) ";
				$sql .= "SELECT num_cia, fecha - interval '1 day', fecha, 'FALSE', importe, 44, 'DEPOSITO TARJETA CREDITO', 1, $_SESSION[iduser], now() FROM mov_banorte WHERE id = $reg[id];\n";
				$sql .= "UPDATE mov_banorte SET fecha_con = fecha, cod_mov = 44, imprimir = 'TRUE', aut = 'FALSE', iduser = $_SESSION[iduser], timestamp = now() WHERE id = $reg[id];\n";
				$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos + $reg[importe], saldo_libros = saldo_libros + $reg[importe] WHERE num_cia = $reg[num_cia] AND cuenta = 1;\n";
				// Conciliar comisiones de la tarjeta
				foreach ($com as $c) {
					$cod_mov = $c['cod_banco'] == 600 ? 46 : 10;
					$concepto = $c['cod_banco'] == 600 ? "COM. TARJETA DE CREDITO" : "IVA POR COMISIONES";
					$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp) ";
					$sql .= "SELECT num_cia, fecha - interval '1 day', fecha, tipo_mov, importe, $cod_mov, '$concepto', 1, $_SESSION[iduser], now() FROM mov_banorte WHERE id = $c[id];\n";
					$sql .= "UPDATE mov_banorte SET cod_mov = $cod_mov, fecha_con = fecha, imprimir = 'TRUE', aut = 'FALSE', iduser = $_SESSION[iduser], timestamp = now() WHERE id = $c[id];\n";
					$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos - $c[importe], saldo_libros = saldo_libros - $c[importe] WHERE num_cia = $c[num_cia] AND cuenta = 1;\n";
				}
			}
		ejecutar_script($sql, $dsn);
	}

	// Conciliar comision por cheque protegido
	$com = ejecutar_script("SELECT * FROM mov_banorte WHERE fecha_con IS NULL AND num_cia > 0 AND cod_banco = 583 ORDER BY num_cia, fecha", $dsn);
	if ($com) {
		$sql = '';
		foreach ($com as $reg)
			if ($iva = ejecutar_script("SELECT * FROM mov_banorte WHERE num_cia = $reg[num_cia] AND cod_banco IN (517) AND fecha_con IS NULL AND id IN ($reg[id] + 1) AND concepto = 'IVA COM POR CHEQUE PROTEGIDO'", $dsn)) {
				// Conciliar comision
				$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp) ";
				$sql .= "SELECT num_cia, fecha, fecha, TRUE, importe, 110, 'COMISION POR CHEQUE PROTEGID', 1, $_SESSION[iduser], now() FROM mov_banorte WHERE id = $reg[id];\n";
				$sql .= "UPDATE mov_banorte SET fecha_con = fecha, cod_mov = 110, imprimir = 'TRUE', aut = 'FALSE', iduser = $_SESSION[iduser], timestamp = now() WHERE id = $reg[id];\n";
				$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos - $reg[importe], saldo_libros = saldo_libros - $reg[importe] WHERE num_cia = $reg[num_cia] AND cuenta = 1;\n";

				$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp) ";
				$sql .= "SELECT num_cia, fecha, fecha, TRUE, importe, 10, 'IVA COM POR CHEQUE PROTEGIDO', 1, $_SESSION[iduser], now() FROM mov_banorte WHERE id = {$iva[0]['id']};\n";
				$sql .= "UPDATE mov_banorte SET cod_mov = 10, fecha_con = fecha, imprimir = 'TRUE', aut = 'FALSE', iduser = $_SESSION[iduser], timestamp = now() WHERE id = {$iva[0]['id']};\n";
				$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos - {$iva[0]['importe']}, saldo_libros = saldo_libros - {$iva[0]['importe']} WHERE num_cia = {$iva[0]['num_cia']} AND cuenta = 1;\n";
			}
		ejecutar_script($sql, $dsn);
	}

	// [14-Oct-2009] Conciliar movimientos de SUA IMSS
	$imss = ejecutar_script("SELECT * FROM mov_banorte WHERE fecha_con IS NULL AND num_cia > 0 AND cod_banco = 503 AND concepto IN ('PAGO DE SUA IMSS', 'PAGO DE LDC IMSS') ORDER BY num_cia, fecha", $dsn);
	if ($imss) {
		$sql = '';
		$num_cia = NULL;
		foreach ($imss as $reg) {
			if ($num_cia != $reg['num_cia']) {
				$num_cia = $reg['num_cia'];

				$tmp = ejecutar_script("SELECT folio FROM folios_cheque WHERE num_cia = $num_cia AND cuenta = 1 ORDER BY folio DESC LIMIT 1", $dsn);
				$folio = $tmp ? $tmp[0]['folio'] + 1 : 1;
			}
			$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, folio, concepto, cuenta, iduser, timestamp) SELECT num_cia, fecha, fecha, tipo_mov, importe, 43, $folio, concepto, 1, $_SESSION[iduser], now() FROM mov_banorte WHERE id = $reg[id];\n";
			$sql .= "INSERT INTO cheques (cod_mov, num_proveedor, num_cia, fecha, folio, importe, iduser, a_nombre, imp, concepto, codgastos, proceso, cuenta, archivo, poliza, site) SELECT 43, 235, num_cia, fecha, $folio, importe, $_SESSION[iduser], 'INSTITUTO MEXICANO DEL SEGURO SOCIAL', 'FALSE', concepto, 141, 'TRUE', 1, 'FALSE', 'TRUE', 'FALSE' FROM mov_banorte WHERE id = $reg[id];\n";
			$sql .= "INSERT INTO movimiento_gastos (codgastos, num_cia, fecha, importe, captura, folio, concepto, cuenta) SELECT 141, num_cia, fecha, importe, 'TRUE', $folio, concepto, 1 FROM mov_banorte WHERE id = $reg[id];\n";
			$sql .= "INSERT INTO folios_cheque (folio, num_cia, reservado, utilizado, fecha, cuenta) SELECT $folio, num_cia, 'FALSE', 'TRUE', fecha, 1 FROM mov_banorte WHERE id = $reg[id];\n";
			$sql .= "UPDATE mov_banorte SET fecha_con = fecha, cod_mov = 43, imprimir = 'TRUE' WHERE id = $reg[id];\n";
			$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos - $reg[importe], saldo_libros = saldo_libros - $reg[importe] WHERE num_cia = $reg[num_cia] AND cuenta = 1;\n";

			$folio++;
		}

		ejecutar_script($sql, $dsn);
	}

	// [14-Oct-2009] Conciliar movimientos de IMPUESTOS FEDERALES
	$imss = ejecutar_script("SELECT * FROM mov_banorte WHERE fecha_con IS NULL AND num_cia > 0 AND cod_banco = 531 AND concepto IN ('PAGO DE IMPUESTOS FEDERALES', 'PAGO REFERENCIADO') ORDER BY num_cia, fecha", $dsn);
	if ($imss) {
		$sql = '';
		$num_cia = NULL;
		foreach ($imss as $reg) {
			if ($num_cia != $reg['num_cia']) {
				$num_cia = $reg['num_cia'];

				$tmp = ejecutar_script("SELECT folio FROM folios_cheque WHERE num_cia = $num_cia AND cuenta = 1 ORDER BY folio DESC LIMIT 1", $dsn);
				$folio = $tmp ? $tmp[0]['folio'] + 1 : 1;
			}
			$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, folio, concepto, cuenta, iduser, timestamp) SELECT num_cia, fecha, fecha, tipo_mov, importe, 33, $folio, concepto, 1, $_SESSION[iduser], now() FROM mov_banorte WHERE id = $reg[id];\n";
			$sql .= "INSERT INTO cheques (cod_mov, num_proveedor, num_cia, fecha, folio, importe, iduser, a_nombre, imp, concepto, codgastos, proceso, cuenta, archivo, poliza, site) SELECT 33, 237, num_cia, fecha, $folio, importe, $_SESSION[iduser], 'TESORERIA DE LA FEDERACION', 'FALSE', concepto, 140, 'TRUE', 1, 'FALSE', 'TRUE', 'FALSE' FROM mov_banorte WHERE id = $reg[id];\n";
			$sql .= "INSERT INTO movimiento_gastos (codgastos, num_cia, fecha, importe, captura, folio, concepto, cuenta) SELECT 140, num_cia, fecha, importe, 'TRUE', $folio, concepto, 1 FROM mov_banorte WHERE id = $reg[id];\n";
			$sql .= "INSERT INTO folios_cheque (folio, num_cia, reservado, utilizado, fecha, cuenta) SELECT $folio, num_cia, 'FALSE', 'TRUE', fecha, 1 FROM mov_banorte WHERE id = $reg[id];\n";
			$sql .= "UPDATE mov_banorte SET fecha_con = fecha, cod_mov = 33, imprimir = 'TRUE' WHERE id = $reg[id];\n";
			$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos - $reg[importe], saldo_libros = saldo_libros - $reg[importe] WHERE num_cia = $reg[num_cia] AND cuenta = 1;\n";

			$folio++;
		}

		ejecutar_script($sql, $dsn);
	}

	// [18-Ene-2017] Conciliar movimientos 503 CARGO POR PAGO CONCENTRACION
	// $result = ejecutar_script("SELECT
	// 	*
	// FROM
	// 	mov_banorte
	// WHERE
	// 	fecha_con IS NULL
	// 	AND num_cia > 0
	// 	AND cod_banco = 503
	// 	AND concepto IN ('CARGO POR PAGO CONCENTRACION')
	// ORDER BY
	// 	num_cia,
	// 	fecha", $dsn);

	if (/*$result*/FALSE)
	{
		$sql = '';
		$num_cia = NULL;

		foreach ($result as $row)
		{
			if ($num_cia != $row['num_cia'])
			{
				$num_cia = $row['num_cia'];

				$tmp = ejecutar_script("SELECT
					folio
				FROM
					folios_cheque
				WHERE
					num_cia = $num_cia
					AND cuenta = 1
				ORDER BY
					folio DESC
				LIMIT 1", $dsn);

				$folio = $tmp ? $tmp[0]['folio'] + 1 : 1;
			}

			$sql .= "INSERT INTO estado_cuenta (
				num_cia,
				fecha,
				fecha_con,
				tipo_mov,
				importe,
				cod_mov,
				folio,
				concepto,
				cuenta,
				iduser,
				timestamp
			)
			SELECT
				num_cia,
				fecha,
				fecha,
				tipo_mov,
				importe,
				31,
				{$folio},
				concepto,
				1,
				{$_SESSION['iduser']},
				now()
			FROM
				mov_banorte
			WHERE
				id = {$row['id']};\n";

			$sql .= "INSERT INTO cheques (
				cod_mov,
				num_proveedor,
				num_cia,
				fecha,
				folio,
				importe,
				iduser,
				a_nombre,
				imp,
				concepto,
				codgastos,
				proceso,
				cuenta,
				archivo,
				poliza,
				site
			)
			SELECT
				31,
				237,
				num_cia,
				fecha,
				{$folio},
				importe,
				{$_SESSION['iduser']},
				'TESORERIA DE LA FEDERACION',
				'FALSE',
				concepto,
				140,
				'TRUE',
				1,
				'FALSE',
				'TRUE',
				'FALSE'
			FROM
				mov_banorte
			WHERE
				id = {$row['id']};\n";

			$sql .= "INSERT INTO movimiento_gastos (
				codgastos,
				num_cia,
				fecha,
				importe,
				captura,
				folio,
				concepto,
				cuenta
			)
			SELECT
				140,
				num_cia,
				fecha,
				importe,
				'TRUE',
				{$folio},
				concepto,
				1
			FROM
				mov_banorte
			WHERE
				id = {$row['id']};\n";

			$sql .= "INSERT INTO folios_cheque (
				folio,
				num_cia,
				reservado,
				utilizado,
				fecha,
				cuenta
			)
			SELECT
				{$folio},
				num_cia,
				'FALSE',
				'TRUE',
				fecha,
				1
			FROM
				mov_banorte
			WHERE
				id = {$row['id']};\n";

			$sql .= "UPDATE mov_banorte
			SET
				fecha_con = fecha,
				cod_mov = 31,
				imprimir = TRUE
			WHERE
				id = {$row['id']};\n";

			$sql .= "UPDATE saldos
			SET
				saldo_bancos = saldo_bancos - {$row['importe']},
				saldo_libros = saldo_libros - {$row['importe']}
			WHERE
				num_cia = {$row['num_cia']}
				AND cuenta = 1;\n";

			$folio++;
		}

		ejecutar_script($sql, $dsn);
	}

	// [01-Jun-2010] Conciliar movimientos de CARGO IMPUESTO LIDE
	$imss = ejecutar_script("SELECT * FROM mov_banorte WHERE fecha_con IS NULL AND num_cia > 0 AND cod_banco = 590 AND concepto = 'CARGO IMPUESTO LIDE' ORDER BY num_cia, fecha", $dsn);
	if ($imss) {
		$sql = '';
		$num_cia = NULL;
		foreach ($imss as $reg) {
			$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp) SELECT num_cia, fecha, fecha, tipo_mov, importe, 78, concepto, 1, $_SESSION[iduser], now() FROM mov_banorte WHERE id = $reg[id];\n";
			$sql .= "UPDATE mov_banorte SET fecha_con = fecha, cod_mov = 78, imprimir = 'TRUE' WHERE id = $reg[id];\n";
			$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos - $reg[importe], saldo_libros = saldo_libros - $reg[importe] WHERE num_cia = $reg[num_cia] AND cuenta = 1;\n";
		}

		ejecutar_script($sql, $dsn);
	}

	/*
	* [17-May-2012] Conciliar cheques certificados
	*/
	$sql = '
		SELECT
			*
		FROM
			mov_banorte
		WHERE
			cod_banco = 526
			AND num_cia > 0
			AND fecha_con IS NULL
		ORDER BY
			num_cia,
			num_documento
	';

	$result = ejecutar_script($sql, $dsn);

	if ($result) {
		foreach ($result as $rec) {
			$sql = '
				SELECT
					*
				FROM
					estado_cuenta
				WHERE
					num_cia = ' . $rec['num_cia'] . '
					AND cuenta = 1
					AND folio = ' . $rec['num_documento'] . '
					AND fecha_con IS NULL
			';

			$cheque = ejecutar_script($sql, $dsn);

			$sql = '
				SELECT
					*
				FROM
					mov_banorte
				WHERE
					id IN (' . $rec['id'] . ' + 1, ' . $rec['id'] . ' + 2)
					AND num_cia = ' . $rec['num_cia'] . '
					AND cod_banco IN (542, 517)
					AND fecha_con IS NULL
				ORDER BY
					id
			';

			$movs = ejecutar_script($sql, $dsn);

			if ($movs && $cheque) {
				$sql = '
					UPDATE
						estado_cuenta
					SET
						fecha_con = \'' . $rec['fecha'] . '\',
						importe = ' . $rec['importe'] . ',
						iduser = ' . $_SESSION['iduser'] . ',
						timestamp = NOW(),
						tipo_con = 3
					WHERE
						id = ' . $cheque[0]['id'] . '
				' . ";\n";

				$sql .= '
					UPDATE
						saldos
					SET
						saldo_bancos = saldo_bancos - ' . $rec['importe'] . '
					WHERE
						num_cia = ' . $rec['num_cia'] . '
						AND cuenta = 1
				' . ";\n";

				$sql .= '
					UPDATE
						mov_banorte
					SET
						fecha_con = NOW()::DATE,
						timestamp = NOW(),
						iduser = ' . $_SESSION['iduser'] . ',
						imprimir = TRUE
					WHERE
						id = ' . $rec['id'] . '
				' . ";\n";

				foreach ($movs as $m) {
					$sql .= '
						INSERT INTO
							estado_cuenta
								(
									num_cia,
									fecha,
									fecha_con,
									cuenta,
									tipo_mov,
									cod_mov,
									concepto,
									importe,
									iduser,
									tipo_con
								)
						SELECT
							num_cia,
							fecha,
							fecha,
							1,
							TRUE,
							' . ($m['cod_banco'] == 542 ? 14 : 10) . ',
							TRIM(concepto),
							importe,
							' . $_SESSION['iduser'] . ',
							5
						FROM
							mov_banorte
						WHERE
							id = ' . $m['id'] . '
					' . ";\n";

					$sql .= '
						UPDATE
							saldos
						SET
							saldo_bancos = saldo_bancos - ' . $m['importe'] . ',
							saldo_libros = saldo_libros - ' . $m['importe'] . '
						WHERE
							num_cia = ' . $m['num_cia'] . '
							AND cuenta = 1
					' . ";\n";

					$sql .= '
						UPDATE
							mov_banorte
						SET
							fecha_con = NOW()::DATE,
							cod_mov = ' . ($m['cod_banco'] == 542 ? 14 : 10) . ',
							timestamp = NOW(),
							iduser = ' . $_SESSION['iduser'] . ',
							imprimir = TRUE
						WHERE
							id = ' . $m['id'] . '
					' . ";\n";
				}

				ejecutar_script($sql, $dsn);
			}
		}
	}

	// Obtener movimientos no conciliados
	$mov_lib = ejecutar_script("SELECT * FROM estado_cuenta WHERE fecha_con IS NULL AND cuenta=1 ORDER BY num_cia ASC,fecha ASC,tipo_mov ASC,importe DESC",$dsn);
	// Obtener movimientos en bancos
	//$mov_ban = ejecutar_script("SELECT mov_banorte.id AS id,num_cia,nombre,cuenta,fecha,importe,num_documento AS folio,concepto,catalogo_mov_bancos.cod_mov AS cod_mov,cod_banco,fecha_con,mov_banorte.tipo_mov FROM mov_banorte JOIN catalogo_mov_bancos USING(cod_banco) WHERE num_cia > 0 AND fecha_con IS NULL ORDER BY num_cia ASC,catalogo_mov_bancos.tipo_mov ASC,importe DESC",$dsn);
	$mov_ban = ejecutar_script("SELECT id,num_cia,nombre,cuenta,fecha,importe,num_documento AS folio,concepto,cod_mov,cod_banco,fecha_con,tipo_mov FROM mov_banorte WHERE num_cia > 0/* AND num_cia < 900*/ AND fecha_con IS NULL ORDER BY num_cia ASC,fecha ASC,tipo_mov ASC,importe DESC",$dsn);
	// Obtener movimientos autorizados
	$mov_aut = ejecutar_script("SELECT * FROM catalogo_mov_autorizados JOIN catalogo_mov_bancos USING(cod_mov) ORDER BY cod_mov ASC",$dsn);

	$num_conciliados = 0;
	$num_autorizados = 0;
	$num_no_conciliados = 0;

	// *** 1er. barrido ***
	// Comparar movientos de libros contra movimiento de bancos
	for ($i=0; $i<count($mov_lib); $i++) {
		// Calcular el importe mínimo y el importe máximo para la comparación
		$imp_min = /*floor(*/$mov_lib[$i]['tipo_mov'] == 'f' ? floatval($mov_lib[$i]['importe'])/*)*/ - 0.99 : floatval($mov_lib[$i]['importe']);
		$imp_max = /*floor(*/$mov_lib[$i]['tipo_mov'] == 'f' ? floatval($mov_lib[$i]['importe'])/*)*/ + 0.99 : floatval($mov_lib[$i]['importe']);

		// Comparar cada movimiento de libros con todos los de bancos
		for ($j=0; $j<count($mov_ban); $j++) {
			if ($mov_ban[$j]['fecha_con'] == "" && $mov_lib[$i]['fecha_con'] == "" &&
				(int)$mov_lib[$i]['num_cia'] == (int)$mov_ban[$j]['num_cia'] &&
				((int)$mov_lib[$i]['folio'] == (int)$mov_ban[$j]['folio'] || (int)$mov_ban[$j]['cod_banco'] == 531 || (int)$mov_ban[$j]['cod_banco'] == 503 || (int)$mov_ban[$j]['cod_banco'] == 605 || $mov_lib[$i]['cod_mov'] == 41 || $mov_lib[$i]['cod_mov'] == 29 || $mov_lib[$i]['cod_mov'] == 8) &&	// Si el código bancario es 531 dejar pasar el folio (17/08/2005) o si es 605 (12/10/2011)
				(floatval($mov_ban[$j]['importe']) >= $imp_min && floatval($mov_ban[$j]['importe']) <= $imp_max)) {

				// Obtener códigos bancarios del movimiento equivalente
				$cod_mov = ejecutar_script("SELECT * FROM catalogo_mov_bancos WHERE cod_mov=".$mov_lib[$i]['cod_mov'],$dsn);

				// Verificar que el código bancario sea igual
				$cod_ok = FALSE;
				for ($cm=0; $cm<count($cod_mov); $cm++)
					if ((int)$mov_ban[$j]['cod_banco'] == (int)$cod_mov[$cm]['cod_banco'])
						$cod_ok = TRUE;

				// Si el código paso, conciliar el movimiento
				if ($cod_ok) {
					$mov_ban[$j]['fecha_con'] = $mov_lib[$i]['fecha'];
					$mov_lib[$i]['fecha_con'] = $mov_ban[$j]['fecha'];

					// Poner fecha de conciliacion en libros y bancos y actualizar codigo de movimiento en bancos
					ejecutar_script("UPDATE estado_cuenta SET fecha_con = '".$mov_ban[$j]['fecha']."',importe = ".$mov_ban[$j]['importe'].", iduser = $_SESSION[iduser], timestamp = now(), tipo_con = 1 WHERE id = ".$mov_lib[$i]['id'],$dsn);
					ejecutar_script("UPDATE mov_banorte SET fecha_con = '".$mov_lib[$i]['fecha']."',cod_mov = ".$mov_lib[$i]['cod_mov'].",imprimir = 'TRUE',iduser = $_SESSION[iduser], timestamp = now() WHERE id = ".$mov_ban[$j]['id'],$dsn);

					// Actualizar saldo en bancos para la compañía conciliada
					//if (existe_registro("saldos",array("num_cia"),array($mov_lib[$i]['num_cia']),$dsn))
						ejecutar_script("UPDATE saldos SET saldo_bancos=saldo_bancos".(($mov_lib[$i]['tipo_mov']=='f')?"+":"-").(float)$mov_ban[$j]['importe']." WHERE num_cia=".$mov_lib[$i]['num_cia']." AND cuenta=1",$dsn);
					//else
						//ejecutar_script("INSERT INTO saldos (num_cia,saldo_libros,saldo_bancos) VALUES (".$mov_lib[$i]['num_cia'].",".(float)$mov_ban[$j]['importe'].",".(float)$mov_ban[$j]['importe'].")",$dsn);

					// Detener ciclo
					//break 1;
				}
			}
		}
	}

	// *** 2o. barrido ***
	// Comparar movimientos de bancos contra los movimientos autorizados

	// Obtener movimientos en bancos para el segundo barrido
	unset($mov_ban);
	$mov_ban = ejecutar_script("SELECT * FROM mov_banorte WHERE num_cia > 0/* AND num_cia < 900*/ AND fecha_con IS NULL ORDER BY num_cia ASC,tipo_mov ASC,importe DESC",$dsn);

	for ($i=0; $i<count($mov_ban); $i++) {
		$ok = FALSE;
		// Comparar cada movimiento de bancos con todos los autorizados
		for ($j=0; $j<count($mov_aut); $j++) {
			if ($mov_ban[$i]['fecha_con'] == "" &&
				(int)$mov_ban[$i]['cod_banco'] == (int)$mov_aut[$j]['cod_banco'] &&
				(float)$mov_ban[$i]['importe'] <= (float)$mov_aut[$j]['importe']) {

				$mov_ban[$i]['fecha_con'] = $mov_ban[$i]['fecha'];

				// Actualizar fecha de conciliacion
				ejecutar_script("UPDATE mov_banorte SET fecha_con = fecha,cod_mov = ".$mov_aut[$j]['cod_mov'].",aut = 'TRUE',imprimir = 'TRUE', iduser = $_SESSION[iduser], timestamp = now() WHERE id = ".$mov_ban[$i]['id'],$dsn);

				// Insertar movimiento conciliado en libros
				$sql = "INSERT INTO estado_cuenta (num_cia,fecha,fecha_con,concepto,tipo_mov,importe,cod_mov,folio,cuenta, iduser, timestamp, tipo_con) SELECT num_cia,fecha,fecha_con,concepto,tipo_mov,importe,cod_mov,num_documento AS folio,1,$_SESSION[iduser],now(),5 FROM mov_banorte WHERE id = ".$mov_ban[$i]['id'];
				ejecutar_script($sql,$dsn);

				// Actualizar saldos de bancos
				//if (existe_registro("saldos",array("num_cia"),array($mov_ban[$i]['num_cia']),$dsn))
					ejecutar_script("UPDATE saldos SET saldo_bancos=saldo_bancos".(($mov_ban[$i]['tipo_mov']=='f')?"+":"-").(float)$mov_ban[$i]['importe'].",saldo_libros=saldo_libros".(($mov_ban[$i]['tipo_mov']=='f')?"+":"-").(float)$mov_ban[$i]['importe']." WHERE num_cia=".$mov_ban[$i]['num_cia']." AND cuenta=1",$dsn);
				//else
					//ejecutar_script("INSERT INTO saldos (num_cia,saldo_libros,saldo_bancos) VALUES (".$mov_ban[$i]['num_cia'].",".(float)$mov_ban[$i]['importe'].",".(float)$mov_ban[$i]['importe'].")",$dsn);

				$num_autorizados++;

				break;
			}
		}
	}

	// [21-Oct-2009] Actualizar saldos
	$sql = "SELECT num_cia, tipo_mov, sum(importe) AS importe FROM estado_cuenta WHERE cuenta = 1 AND fecha_con IS NULL GROUP BY num_cia, tipo_mov ORDER BY num_cia";
	$movs = ejecutar_script($sql, $dsn);
	$sql = "UPDATE saldos SET saldo_libros = saldo_bancos WHERE cuenta = 1;\n";
	if ($movs) {
		foreach ($movs as $mov)
			$sql .= "UPDATE saldos SET saldo_libros = saldo_libros " . ($mov['tipo_mov'] == "f" ? "+" : "-") . " $mov[importe] WHERE num_cia = $mov[num_cia] AND cuenta = 1;\n";
	}
	ejecutar_script($sql, $dsn);

	header("location: ./ban_con_aut.php?saldos=1");
	die;
}

// [25-May-2007] Listar los saldos que estan por debajo de 100,000 pesos
if (isset($_GET['saldos'])) {
	$sql = "SELECT num_cia, nombre_corto, saldo FROM saldo_banorte LEFT JOIN catalogo_companias USING (num_cia) WHERE ((num_cia NOT IN (619, 630) AND saldo BETWEEN 0.01 AND 100000) OR (num_cia IN (619, 630) AND saldo BETWEEN 0.01 AND 150000)) AND aviso_saldo = 'TRUE' ORDER BY num_cia";
	$result = ejecutar_script($sql, $dsn);

	if (!$result)
		die(header('location: ./ban_con_aut.php?resultados=1'));

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

// Mostrar resultados
if (isset($_GET['resultados'])) {
	// Obtener todos los movimientos (por marca de impresión)
	$mov_lib = ejecutar_script("SELECT * FROM mov_banorte WHERE fecha_con IS NOT NULL AND imprimir = 'TRUE' AND aut = 'FALSE' ORDER BY num_cia,fecha ASC",$dsn);
	$mov_aut = ejecutar_script("SELECT * FROM mov_banorte WHERE fecha_con IS NOT NULL AND imprimir = 'TRUE' AND aut = 'TRUE' ORDER BY num_cia,fecha ASC",$dsn);
	$mov_ban = ejecutar_script("SELECT * FROM mov_banorte WHERE fecha_con IS NULL AND num_cia > 0 ORDER BY num_cia,fecha ASC",$dsn);

	if (!$mov_lib && !$mov_aut) die(header('location: ./ban_con_aut.php'));

	// Obtener todos los movimientos (por fecha de conciliacion)
	/*$mov_lib = ejecutar_script("SELECT * FROM mov_banorte WHERE fecha_con = '".date("d/m/Y")."' AND aut = 'FALSE' ORDER BY num_cia,fecha ASC",$dsn);
	$mov_aut = ejecutar_script("SELECT * FROM mov_banorte WHERE fecha_con = '".date("d/m/Y")."' AND aut = 'TRUE' ORDER BY num_cia,fecha ASC",$dsn);
	$mov_ban = ejecutar_script("SELECT * FROM mov_banorte WHERE fecha_con IS NULL AND num_cia > 0 ORDER BY num_cia,fecha ASC",$dsn);*/

	// Quitar marca de impresión
	ejecutar_script("UPDATE mov_banorte SET imprimir = 'FALSE' WHERE imprimir = 'TRUE'",$dsn);

	$tpl->newBlock("resultados");
	// Listar movimientos conciliados
	if ($mov_lib) {
		$tpl->newBlock("conciliados");
		$cia = NULL;
		$gran_total_deposito = 0;
		$gran_total_retiro = 0;

		$total_deposito = 0;
		$total_retiro = 0;
		for ($i=0; $i<count($mov_lib); $i++) {
			if ($mov_lib[$i]['num_cia'] != $cia) {
				if ($cia != NULL) {
					$tpl->assign("cia_con.total_deposito",number_format($total_deposito,2,".",","));
					$tpl->assign("cia_con.total_retiro",number_format($total_retiro,2,".",","));
					$total_deposito = 0;
					$total_retiro = 0;
				}

				$tpl->newBlock("cia_con");
				$result = ejecutar_script("SELECT nombre,nombre_corto,clabe_cuenta FROM catalogo_companias WHERE num_cia=".$mov_lib[$i]['num_cia'],$dsn);
				$tpl->assign("num_cia",$mov_lib[$i]['num_cia']);
				$tpl->assign("cuenta",$result[0]['clabe_cuenta']);
				$tpl->assign("nombre_cia",$result[0]['nombre']);
				$tpl->assign("nombre_corto",$result[0]['nombre_corto']);
				$cia = $mov_lib[$i]['num_cia'];

				$tpl->newBlock("fila_con");
				$tpl->assign("fecha",$mov_lib[$i]['fecha']);
				$result = ejecutar_script("SELECT descripcion FROM catalogo_mov_bancos WHERE cod_mov=".$mov_lib[$i]['cod_mov'],$dsn);
				$tpl->assign("codigo",$mov_lib[$i]['cod_mov']);
				$tpl->assign("descripcion",$result[0]['descripcion']);
				$tpl->assign("deposito",($mov_lib[$i]['tipo_mov'] == "f")?number_format($mov_lib[$i]['importe'],2,".",","):"&nbsp;");
				$tpl->assign("retiro",($mov_lib[$i]['tipo_mov'] == "t")?number_format($mov_lib[$i]['importe'],2,".",","):"&nbsp;");
				$tpl->assign("folio",($mov_lib[$i]['num_documento'] > 0)?$mov_lib[$i]['num_documento']:"&nbsp;");
				$tpl->assign("concepto",$mov_lib[$i]['concepto']);

				if ($mov_lib[$i]['tipo_mov'] == "f") {
					$total_deposito += $mov_lib[$i]['importe'];
					$gran_total_deposito += $mov_lib[$i]['importe'];
				}
				else {
					$total_retiro += $mov_lib[$i]['importe'];
					$gran_total_retiro += $mov_lib[$i]['importe'];
				}
			}
			else {
				$tpl->newBlock("fila_con");
				$tpl->assign("fecha",$mov_lib[$i]['fecha']);
				$result = ejecutar_script("SELECT descripcion FROM catalogo_mov_bancos WHERE cod_mov=".$mov_lib[$i]['cod_mov'],$dsn);
				$tpl->assign("codigo",$mov_lib[$i]['cod_mov']);
				$tpl->assign("descripcion",$result[0]['descripcion']);
				$tpl->assign("deposito",($mov_lib[$i]['tipo_mov'] == "f")?number_format($mov_lib[$i]['importe'],2,".",","):"&nbsp;");
				$tpl->assign("retiro",($mov_lib[$i]['tipo_mov'] == "t")?number_format($mov_lib[$i]['importe'],2,".",","):"&nbsp;");
				$tpl->assign("folio",($mov_lib[$i]['num_documento'] > 0)?$mov_lib[$i]['num_documento']:"&nbsp;");
				$tpl->assign("concepto",$mov_lib[$i]['concepto']);

				if ($mov_lib[$i]['tipo_mov'] == "f") {
					$total_deposito += $mov_lib[$i]['importe'];
					$gran_total_deposito += $mov_lib[$i]['importe'];
				}
				else {
					$total_retiro += $mov_lib[$i]['importe'];
					$gran_total_retiro += $mov_lib[$i]['importe'];
				}
			}
		}
		if ($cia != NULL) {
			$tpl->assign("cia_con.total_deposito",number_format($total_deposito,2,".",","));
			$tpl->assign("cia_con.total_retiro",number_format($total_retiro,2,".",","));
		}
		$tpl->assign("conciliados.gran_total_deposito",number_format($gran_total_deposito,2,".",","));
		$tpl->assign("conciliados.gran_total_retiro",number_format($gran_total_retiro,2,".",","));
	}

	// Listar movimientos autorizados durante la conciliación
	if ($mov_aut) {
		$tpl->newBlock("autorizados");
		$cia = NULL;
		$gran_total_deposito = 0;
		$gran_total_retiro = 0;

		$total_deposito = 0;
		$total_retiro = 0;
		for ($i=0; $i<count($mov_aut); $i++) {
			if ($mov_aut[$i]['num_cia'] != $cia) {
				if ($cia != NULL) {
					$tpl->assign("cia_aut.total_deposito",number_format($total_deposito,2,".",","));
					$tpl->assign("cia_aut.total_retiro",number_format($total_retiro,2,".",","));
					$total_deposito = 0;
					$total_retiro = 0;
				}

				$tpl->newBlock("cia_aut");
				$result = ejecutar_script("SELECT nombre,nombre_corto,clabe_cuenta FROM catalogo_companias WHERE num_cia=".$mov_aut[$i]['num_cia'],$dsn);
				$tpl->assign("num_cia",$mov_aut[$i]['num_cia']);
				$tpl->assign("cuenta",$result[0]['clabe_cuenta']);
				$tpl->assign("nombre_cia",$result[0]['nombre']);
				$tpl->assign("nombre_corto",$result[0]['nombre_corto']);
				$cia = $mov_aut[$i]['num_cia'];

				$tpl->newBlock("fila_aut");
				$tpl->assign("fecha",$mov_aut[0]['fecha']);
				$result = ejecutar_script("SELECT descripcion FROM catalogo_mov_bancos WHERE cod_mov=".$mov_aut[$i]['cod_mov'],$dsn);
				$tpl->assign("codigo",$mov_aut[$i]['cod_mov']);
				$tpl->assign("descripcion",$result[0]['descripcion']);
				$tpl->assign("deposito",($mov_aut[$i]['tipo_mov'] == "f")?number_format($mov_aut[$i]['importe'],2,".",","):"&nbsp;");
				$tpl->assign("retiro",($mov_aut[$i]['tipo_mov'] == "t")?number_format($mov_aut[$i]['importe'],2,".",","):"&nbsp;");
				$tpl->assign("folio",($mov_aut[$i]['num_documento'] > 0)?$mov_aut[$i]['num_documento']:"&nbsp;");
				$tpl->assign("concepto",$mov_aut[$i]['concepto']);

				if ($mov_aut[$i]['tipo_mov'] == "f") {
					$total_deposito += $mov_aut[$i]['importe'];
					$gran_total_deposito += $mov_aut[$i]['importe'];
				}
				else {
					$total_retiro += $mov_aut[$i]['importe'];
					$gran_total_retiro += $mov_aut[$i]['importe'];
				}
			}
			else {
				$tpl->newBlock("fila_aut");
				$tpl->assign("fecha",$mov_aut[$i]['fecha']);
				$result = ejecutar_script("SELECT descripcion FROM catalogo_mov_bancos WHERE cod_mov=".$mov_aut[$i]['cod_mov'],$dsn);
				$tpl->assign("codigo",$mov_aut[$i]['cod_mov']);
				$tpl->assign("descripcion",$result[0]['descripcion']);
				$tpl->assign("deposito",($mov_aut[$i]['tipo_mov'] == "f")?number_format($mov_aut[$i]['importe'],2,".",","):"&nbsp;");
				$tpl->assign("retiro",($mov_aut[$i]['tipo_mov'] == "t")?number_format($mov_aut[$i]['importe'],2,".",","):"&nbsp;");
				$tpl->assign("folio",($mov_aut[$i]['num_documento'] > 0)?$mov_aut[$i]['num_documento']:"&nbsp;");
				$tpl->assign("concepto",$mov_aut[$i]['concepto']);

				if ($mov_aut[$i]['tipo_mov'] == "f") {
					$total_deposito += $mov_aut[$i]['importe'];
					$gran_total_deposito += $mov_aut[$i]['importe'];
				}
				else {
					$total_retiro += $mov_aut[$i]['importe'];
					$gran_total_retiro += $mov_aut[$i]['importe'];
				}
			}
		}
		if ($cia != NULL) {
			$tpl->assign("cia_aut.total_deposito",number_format($total_deposito,2,".",","));
			$tpl->assign("cia_aut.total_retiro",number_format($total_retiro,2,".",","));
		}
		$tpl->assign("autorizados.gran_total_deposito",number_format($gran_total_deposito,2,".",","));
		$tpl->assign("autorizados.gran_total_retiro",number_format($gran_total_retiro,2,".",","));
	}

	// Listar movimientos no conciliados
	/*if ($mov_ban) {
		$tpl->newBlock("no_conciliados");
		$cia = NULL;
		$gran_total_deposito = 0;
		$gran_total_retiro = 0;

		$total_deposito = 0;
		$total_retiro = 0;
		for ($i=0; $i<count($mov_ban); $i++) {
			if ($mov_ban[$i]['num_cia'] != $cia) {
				if ($cia != NULL) {
					if ($count_depositos > 0) {
						$tpl->newBlock("boton_d");
						$tpl->assign("numfilas",$count_depositos);
					}

					$tpl->assign("cia_nocon.total_deposito",number_format($total_deposito,2,".",","));
					$tpl->assign("cia_nocon.total_retiro",number_format($total_retiro,2,".",","));
					$total_deposito = 0;
					$total_retiro = 0;
				}

				$count_depositos = 0;
				$count_retiros = 0;

				$tpl->newBlock("cia_nocon");
				$result = ejecutar_script("SELECT nombre,nombre_corto,clabe_cuenta FROM catalogo_companias WHERE num_cia=".$mov_ban[$i]['num_cia'],$dsn);
				$tpl->assign("num_cia",$mov_ban[$i]['num_cia']);
				$tpl->assign("cuenta",$result[0]['clabe_cuenta']);
				$tpl->assign("nombre_cia",$result[0]['nombre']);
				$tpl->assign("nombre_corto",$result[0]['nombre_corto']);
				$cia = $mov_ban[$i]['num_cia'];

				$tpl->newBlock("fila_nocon");
				$tpl->assign("id",$mov_ban[$i]['id']);
				$tpl->assign("fecha",$mov_ban[$i]['fecha']);
				$tpl->assign("codigo",$mov_ban[$i]['cod_banco']);
				$tpl->assign("deposito",($mov_ban[$i]['tipo_mov'] == "f")?number_format($mov_ban[$i]['importe'],2,".",","):"&nbsp;");
				$tpl->assign("retiro",($mov_ban[$i]['tipo_mov'] == "t")?number_format($mov_ban[$i]['importe'],2,".",","):"&nbsp;");
				$tpl->assign("folio",($mov_ban[$i]['num_documento'] > 0)?$mov_ban[$i]['num_documento']:"&nbsp;");
				$tpl->assign("concepto",$mov_ban[$i]['concepto']);

				if ($mov_ban[$i]['tipo_mov'] == "f") {
					$total_deposito += $mov_ban[$i]['importe'];
					$gran_total_deposito += $mov_ban[$i]['importe'];

					// Crear checkbox de modificación de depósito
					$tpl->newBlock("modifica_depositos");
					$tpl->assign("i",$count_depositos);
					$tpl->assign("id",$mov_ban[$i]['id']);
					$count_depositos++;
				}
				else {
					$total_retiro += $mov_ban[$i]['importe'];
					$gran_total_retiro += $mov_ban[$i]['importe'];

					// Crear boton de modificación de retiros
					$tpl->newBlock("modifica_retiros");
					$tpl->assign("id",$mov_ban[$i]['id']);
					$count_retiros++;
				}
			}
			else {
				$tpl->newBlock("fila_nocon");
				$tpl->assign("id",$mov_ban[$i]['id']);
				$tpl->assign("fecha",$mov_ban[$i]['fecha']);
				$tpl->assign("codigo",$mov_ban[$i]['cod_banco']);
				$tpl->assign("deposito",($mov_ban[$i]['tipo_mov'] == "f")?number_format($mov_ban[$i]['importe'],2,".",","):"&nbsp;");
				$tpl->assign("retiro",($mov_ban[$i]['tipo_mov'] == "t")?number_format($mov_ban[$i]['importe'],2,".",","):"&nbsp;");
				$tpl->assign("folio",($mov_ban[$i]['num_documento'] > 0)?$mov_ban[$i]['num_documento']:"&nbsp;");
				$tpl->assign("concepto",$mov_ban[$i]['concepto']);

				if ($mov_ban[$i]['tipo_mov'] == "f") {
					// Asignación de importe
					$total_deposito += $mov_ban[$i]['importe'];
					$gran_total_deposito += $mov_ban[$i]['importe'];

					// Crear checkbox de modificación de depósito
					$tpl->newBlock("modifica_depositos");
					$tpl->assign("i",$count_depositos);
					$tpl->assign("id",$mov_ban[$i]['id']);
					$count_depositos++;
				}
				else {
					// Asignación de importe
					$total_retiro += $mov_ban[$i]['importe'];
					$gran_total_retiro += $mov_ban[$i]['importe'];

					// Crear boton de modificación de retiros
					$tpl->newBlock("modifica_retiros");
					$tpl->assign("id",$mov_ban[$i]['id']);
					$count_retiros++;
				}
			}
		}
		if ($cia != NULL) {
			$tpl->assign("cia_nocon.total_deposito",number_format($total_deposito,2,".",","));
			$tpl->assign("cia_nocon.total_retiro",number_format($total_retiro,2,".",","));
		}
		$tpl->assign("no_conciliados.gran_total_deposito",number_format($gran_total_deposito,2,".",","));
		$tpl->assign("no_conciliados.gran_total_retiro",number_format($gran_total_retiro,2,".",","));
	}*/

	$tpl->printToScreen();

	die;
}
?>
