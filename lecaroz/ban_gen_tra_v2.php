<?php
// LISTADO DE ESTADOS DE CUENTA
// Tabla 'estado_cuenta'
// Menu 'pendiente'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 30) die('MODIFICANDO');

// --------------------------------- Descripción de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_gen_tra_v2.tpl");
$tpl->prepare();

if (isset($_GET['gen'])) {
	$pros = array();
	foreach ($_GET['num_pro'] as $pro)
		if ($pro > 0)
			$pros[] = $pro;

	$cias = array();
	foreach ($_GET['num_cia'] as $cia)
		if ($cia > 0)
			$cias[] = $cia;

	$current_week_day = date('w');

	$sql = "SELECT
		te.id,
		te.num_cia,
		te.cuenta AS cuenta,
		te.num_proveedor AS num_pro,
		cp.nombre AS beneficiario,
		cp.rfc,
		te.folio,
		te.importe,
		tipo,
		sucursal,
		plaza_banxico,
		clave,
		num_banco,
		cp.cuenta AS cuenta_pro,
		cp.clabe AS clabe_pro,
		clabe_cuenta AS cuenta_banorte,
		clabe_cuenta2 AS cuenta_santander,
		concepto,
		ref,
		COALESCE((SELECT referencia FROM referencias_bancarias WHERE num_cia = COALESCE(poc.num_cia_aplica, te.num_cia) AND num_proveedor = te.num_proveedor AND tsbaja IS NULL), clave_referencia_bancaria, (SELECT referencia_bancaria FROM catalogo_companias WHERE num_cia = COALESCE(poc.num_cia_aplica, te.num_cia))) AS referencia_bancaria,
		(NOW()::DATE + INTERVAL '" . ($current_week_day == 5 ? '3 DAYS' : '1 DAY') . "')::DATE AS fecha_aplicacion,
		tipo_renta
	FROM
		transferencias_electronicas AS te
		LEFT JOIN cheques c ON (
			c.num_cia = te.num_cia
			AND c.cuenta = te.cuenta
			AND c.folio = te.folio
			AND c.fecha = te.fecha_gen
		)
		LEFT JOIN pagos_otras_cias poc ON (
			poc.num_cia = c.num_cia
			AND poc.cuenta = c.cuenta
			AND poc.folio = c.folio
			AND poc.fecha = c.fecha
		)
		LEFT JOIN catalogo_proveedores AS cp ON (cp.num_proveedor = te.num_proveedor)
		LEFT JOIN catalogo_companias AS cc ON (cc.num_cia = te.num_cia)
		LEFT JOIN catalogo_bancos USING (idbanco)
	WHERE
		te.status = 0";
	$sql .= $_GET['cuenta'] > 0 ? " AND te.cuenta = $_GET[cuenta]" : '';
	$sql .= ' AND te.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');

	if ($_GET['fecha_corte'] != '') {
		$sql .= ' AND te.fecha_gen <= \'' . $_GET['fecha_corte'] . '\'';
	}

	if (count($pros) > 0) {
		$sql .= ' AND te.num_proveedor IN (';
		foreach ($pros as $i => $pro)
			$sql .= $pro . ($i < count($pros) - 1 ? ', ' : ')');
	}
	if (count($cias) > 0)
		$sql .= ' AND te.num_cia IN (' . implode(', ', $cias) . ')';
	$sql .= ' ORDER BY te.cuenta, beneficiario, te.num_cia, te.folio';
	$result = $db->query($sql);//echo '<pre>' . $sql . '</pre><pre>' . print_r($result, TRUE) . '</pre>';die;

	if (!$result) {
		$tpl->newBlock("no_result");
		$tpl->printToScreen();
		die;
	}

	function filler($str, $length, $chr, $side = TRUE) {
		$tmp = "";

		for ($i = 0; $i < $length - strlen($str); $i++)
			$tmp .= $chr;

		return $side ? $str . $tmp : $tmp . $str;
	}

	$tmp = $db->query("SELECT folio_archivo FROM transferencias_electronicas WHERE folio_archivo > 0 ORDER BY folio_archivo DESC LIMIT 1");
	$folio = $tmp ? $tmp[0]['folio_archivo'] + 1 : 3;

	// Número de registros por archivo (aplica solo a Santander)
	$num_reg_x_archivo_banorte = /*10000000*/999;
	$num_reg_x_archivo_santander = 150;

	$ban = "";	// Cadena de datos de banorte
	$int = "";	// Cadena de datos de pagos mismo banco santander
	$ext = "";	// Cadena de datos de pagos otros bancos santander
	$date = date("dmY");
	// Construir cadenas para archivos
	$index_int = 0;
	$index_ext = 0;
	$cont_int = $num_reg_x_archivo_santander;
	$cont_ext = $num_reg_x_archivo_santander;

	$banorte = array(
		'TRASPASOS' => array(),
		'PAGOS' => array(),
		'SPEI' => array(),
		'TEF' => array(),
		'RENTAS_PAGOS' => array(),
		'RENTAS_SPEI' => array()
	);

	$santander = array(
		'internas' => array(),
		'interbancarias' => array()
	);

	$ids = array();
	$errores = array();

	foreach ($result as $reg) {
		// Cadena de datos de transferencias de banorte
		if ($reg['cuenta'] == 1) {
			// Omitir registro si la cuenta o la clabe bancaria (dependiendo del que se vaya a usar) esta en blanco
			if ($reg['num_banco'] == '072' && strlen(trim($reg['cuenta_pro'])) != 11) {
				$errores[] = "Proveedor {$reg['num_pro']} {$reg['beneficiario']}, cuenta invalida {$reg['cuenta_pro']}";
				continue;
			}
			else if (strlen(trim($reg['clabe_pro'])) != 18){
				$errores[] = "Proveedor {$reg['num_pro']} {$reg['beneficiario']}, CLABE invalida {$reg['clabe_pro']}";
				continue;
			}

			$data = '';

			// Campo: Operación
			// Tipo: Numérico
			// Longitud: 2
			// Inicio: 1
			// Fin: 2
			// Obligatorio: SI
			// Notas: Si la cuenta destino se encuentra dentro de las cuentas de la empresa, se usara el tipo de operación 1 Traspaso, en caso contrario, se determina
			// el banco del cual es la cuenta del proveedor, si es propia de banorte se usara el tipo de operación 2 Terceros, en caso que sea de otro banco se usara
			// el tipo de operación 4 SPEI para transferencias interbancarias
			if ($db->query("SELECT num_cia FROM catalogo_companias WHERE LENGTH(TRIM(clabe_cuenta)) = 11 AND clabe_cuenta = '$reg[cuenta_pro]'")) {
				$data .= '01';

				$key = 'TRASPASOS';
			}
			else {
				if ($reg['num_banco'] == '072') {
					/*
					@ [11-Mar-2013] Ajuste para pagos de zapaterias, ellos no usarán código de operación 2 si no 4
					*/
					// $data .= $reg['num_cia'] >= 900 ? '04' : '02';
					$data .= $reg['num_cia'] >= 900 ? '02' : '02';

					$key = $reg['tipo_renta'] == 2 ? 'RENTAS_PAGOS' : 'PAGOS';
				}
				else {
					/*if ($reg['num_cia'] < 900) {*/
						$data .= '04';

						$key = $reg['tipo_renta'] == 2 ? 'RENTAS_SPEI' : 'SPEI';
					/*}
					else {
						$data .= '05';

						$key = 'TEF';
					}*/
				}
			}
			// ***************************
			// Campo: Clave ID
			// Tipo: Alfanumérico
			// Longitud: 13
			// Inicio: 3
			// Fin: 15
			// Notas: La clave es igual al número de proveedor del catálogo de proveedores
			$data .= str_pad($reg['num_pro'], 13, ' ', STR_PAD_RIGHT);
			// ***************************
			// Campo: Cuenta Origen
			// Tipo: Numérico
			// Longitud: 20
			// Inicio: 16
			// Fin: 35
			// Notas: Rellenar con ceros a la izquierda
			$data .= str_pad($reg['cuenta_banorte'], 20, '0', STR_PAD_LEFT);
			// ***************************
			// Campo: Cuenta Destino
			// Tipo: Numérico
			// Longitud: 20
			// Inicio: 36
			// Fin: 55
			// Notas: Rellenar con ceros a la izquierda
			$data .= str_pad($reg['num_banco'] == '072' ? $reg['cuenta_pro'] : $reg['clabe_pro'], 20, '0', STR_PAD_LEFT);
			// ***************************
			// Campo: Importe
			// Tipo: Numérico
			// Longitud: 14
			// Inicio: 56
			// Fin: 69
			// Notas: Sin punto decimal, los 2 últimos dígitos son los decimales; rellenar con ceros a la izquierda
			$data .= str_pad(number_format($reg['importe'], 2, '', ''), 14, '0', STR_PAD_LEFT);
			// ***************************
			// Campo: Referencia
			// Tipo: Alfanumérico
			// Longitud: 10
			// Inicio: 70
			// Fin: 79
			// Notas: Rellenar con ceros a la izquierda. Se usara como referencia el número de folio generado en el sistema
			// $data .= str_pad($reg['folio'], 10, '0', STR_PAD_LEFT);
			if (trim($reg['referencia_bancaria']) != '' && ctype_digit($reg['referencia_bancaria']))
				$data .= str_pad(trim($reg['referencia_bancaria']), 10, '0', STR_PAD_LEFT);
			else
				$data .= str_pad($reg['folio'], 10, '0', STR_PAD_LEFT);
			// ***************************
			// Campo: Descripción
			// Tipo: Alfanumérico
			// Longitud: 30
			// Inicio: 80
			// Fin: 109
			// Notas: Justificar a la izquierda, rellenar con espacios a la derecha para completar longitud
			// if ($reg['num_pro'] == 1116 && trim($reg['ref']) != '')
			// 	$data .= str_pad(trim($reg['ref']), 30, ' ', STR_PAD_RIGHT);
			/*else */if (trim($reg[/*'clave_referencia_bancaria'*/'referencia_bancaria']) != '')
				$data .= str_pad(trim($reg[/*'clave_referencia_bancaria'*/'referencia_bancaria']), 30, ' ', STR_PAD_RIGHT);
			else
				$data .= str_pad(trim(substr(preg_replace('/[^a-z0-9\s]/i', ' ', trim($reg['concepto']) != '' ? trim($reg['concepto']) : 'SIN DESCRIPCION'), 0, 30)), 30, ' ', STR_PAD_RIGHT);
			// ***************************
			// Campo: Moneda Origen
			// Tipo: Numérico
			// Longitud: 1
			// Inicio: 110
			// Fin: 110
			// Notas: 1 = PESOS (MXP); 2 = DOLARES (USD)
			$data .= '1';
			// ***************************
			// Campo: Moneda Destino
			// Tipo: Numérico
			// Longitud: 1
			// Inicio: 111
			// Fin: 111
			// Notas: 1 = PESOS (MXP); 2 = DOLARES (USD)
			$data .= '1';
			// ***************************
			// Campo: RFC Ordenante
			// Tipo: Alfanumérico
			// Longitud: 13
			// Inicio: 112
			// Fin: 124
			// Notas: Aplica en operación 04 y 05; Justificar a la izquierda; personas físicas 13 posiciones, morales 12 posiciones,
			// la última posición será en blanco
			$data .= str_pad($reg['num_banco'] != '072' ? trim($reg['rfc']) : '', 13, ' ', STR_PAD_RIGHT);
			// ***************************
			// Campo: I.V.A.
			// Tipo: Numérico
			// Longitud: 14
			// Inicio: 125
			// Fin: 138
			// Notas: Sin punto decimal, los 2 últimos dígitos son los decimales; rellenar con ceros a la izquierda
			$data .= str_pad('', 14, '0', STR_PAD_LEFT);
			// ***************************
			// Campo: e-mail beneficiario
			// Tipo: Alfanumérico
			// Longitud: 39
			// Inicio: 139
			// Fin: 177
			// Notas: Se puede indicar en caso de requerir enviar un correo informando del envío de la operación al beneficiario
			$data .= str_pad('', 39, ' ', STR_PAD_RIGHT);
			// Campo: Fecha de aplicación
			// Tipo: Numérico
			// Longitud: 8
			// Inicio: 178
			// Fin: 185
			// Notas: Cuando la operación = 05, Formato = DDMMAAAA; 20 JUN 2002 = 20062002
			// Operación = 01, 02, 04 a menos que se requiera programar el pago para fecha posterior.
			$data .= str_pad('', 8, ' ', STR_PAD_RIGHT);
			// Campo: Instrucción de pago
			// Tipo: Alfanumérico
			// Longitud: 70
			// Inicio: 186
			// Fin: 255
			// Notas: C<Compañía>F<Folio de pago>P<Proveedor>
			$data .= str_pad('C' . $reg['num_cia'] . 'F' . $reg['folio'] . 'P' . $reg['num_pro'], 70, ' ', STR_PAD_RIGHT);

			$banorte[$key][] = $data;

			$ids[] = $reg['id'];
		}
		else if ($reg['cuenta'] == 2) {
			if ($reg['num_banco'] == '014') {
				if ($cont_int >= $num_reg_x_archivo_santander) {
					$index_int++;
					$int[$index_int] = '';
					$cont_int = 0;
				}

				$int[$index_int] .= str_pad($reg['cuenta_santander'], 16, ' ', STR_PAD_RIGHT);
				$int[$index_int] .= str_pad($reg['cuenta_pro'], 16, ' ', STR_PAD_RIGHT);
				$int[$index_int] .= str_pad(number_format($reg['importe'], 2, '.', ''), 13, ' ', STR_PAD_LEFT);
				if (/*$reg['num_pro'] == 1116 && */trim($reg[/*'ref'*/'referencia_bancaria']) != '')
					$int[$index_int] .= str_pad(trim($reg[/*'ref'*/'referencia_bancaria']), 40, ' ', STR_PAD_RIGHT);
				else
					$int[$index_int] .= str_pad(trim(substr($reg['folio'] . ' ' . preg_replace('/[^a-z0-9\s]/i', ' ', $reg['concepto']), 0, 30)), 40, ' ', STR_PAD_RIGHT);
				$int[$index_int] .= $date;
				$int[$index_int] .= "\r\n";

				$cont_int++;
			}
			else {
				if ($cont_ext >= $num_reg_x_archivo_santander) {
					$index_ext++;
					$ext[$index_ext] = '';
					$cont_ext = 0;
				}

				$ext[$index_ext] .= str_pad($reg['cuenta_santander'], 16, ' ', STR_PAD_RIGHT);
				$ext[$index_ext] .= str_pad($reg['clabe_pro'], 20, ' ', STR_PAD_RIGHT);
				$ext[$index_ext] .= str_pad($reg['clave'], 5, ' ', STR_PAD_RIGHT);
				$ext[$index_ext] .= str_pad(substr(preg_replace(array("/[^A-ZÑ\s]/", "/Ñ/"), array('', 'N'), $reg['beneficiario']), 0, 40), 40, ' ', STR_PAD_RIGHT);
				$ext[$index_ext] .= $reg['sucursal'];
				$ext[$index_ext] .= str_pad(number_format($reg['importe'], 2, '', ''), 15, '0', STR_PAD_LEFT);
				$ext[$index_ext] .= $reg['plaza_banxico'];
				if (/*$reg['num_pro'] == 1116 && */trim($reg[/*'ref'*/'referencia_bancaria']) != '')
					$ext[$index_ext] .= str_pad(trim($reg[/*'ref'*/'referencia_bancaria']), 30, ' ', STR_PAD_RIGHT);
				else
					$ext[$index_ext] .= str_pad(trim(substr($reg['folio'] . ' ' . preg_replace('/[^a-z0-9\s]/i', ' ', $reg['concepto']), 0, 30)), 30, ' ', STR_PAD_RIGHT);
				$ext[$index_ext] .= str_pad('', 90, ' ', STR_PAD_RIGHT) . "\r\n";

				$cont_ext++;
			}

			$ids[] = $reg['id'];
		}
	}

	$tpl->newBlock("archivos");
	$tpl->assign("folio", $folio);

	if (count($banorte['TRASPASOS']) > 0 || count($banorte['PAGOS']) > 0 || count($banorte['SPEI']) > 0 || count($banorte['TEF']) > 0 || count($banorte['RENTAS_PAGOS']) > 0 || count($banorte['RENTAS_SPEI']) > 0) {
		$tpl->newBlock('banorte');

		foreach ($banorte as $key => $regs) {
			if (count($regs) > 0) {
				$tpl->newBlock($key);

				foreach (array_chunk($regs, $num_reg_x_archivo_banorte) as $i => $reg) {
					$f = fopen('trans/BANORTE_' . $key . '_' . $folio . '_' . ($i + 1) . '.txt', 'w');
					fwrite($f, implode("\r\n", $reg));
					fclose($f);

					$tpl->newBlock($key . '_ROW');
					$tpl->assign('folio', $folio);
					$tpl->assign('i', $i + 1);
				}
			}
		}
	}
	if ($index_int > 0 || $index_ext > 0) {
		$tpl->newBlock('santander');
		// Archivo para pagos del mismo banco
		if ($index_int > 0) {
			foreach ($int as $i => $string) {
				$f = fopen('trans/SANTANDER_' . $folio . '_' . $i . '.txt', 'w');
				fwrite($f, $string);
				fclose($f);

				$tpl->newBlock('int');
				$tpl->assign('folio', $folio);
				$tpl->assign('num', $i);
			}
		}
		// Archivo para pagos a otros bancos
		if ($index_ext > 0) {
			if ($index_int > 0) {
				$tpl->newBlock('extra');
			}

			foreach ($ext as $i => $string) {
				$f = fopen('trans/SANTANDER_INTERBANCARIAS_' . $folio . '_' . $i . '.txt', 'w');
				fwrite($f, $string);
				fclose($f);

				$tpl->newBlock('ext');
				$tpl->assign('folio', $folio);
				$tpl->assign('num', $i);
			}
		}
	}

	$tpl->assign('archivos.errores', implode("\n", $errores));

	$sql = '';

	// [09-Feb-2007] Generar depositos de rentas y honorarios
	// $rel = array(
	// 	array(605, 625),
	// 	array(312, 628),
	// 	array(1231, 628),	// Agregado el 12-Nov-2010
	// 	array(423, 601),
	// 	array(434, 617),
	// 	array(417, 614),
	// 	array(435, 611),
	// 	array(576, 618),
	// 	array(174, 603),
	// 	array(441, 613),
	// 	array(644, 605),
	// 	array(171, 604),
	// 	array(173, 607),
	// 	array(422, 610),
	// 	array(436, 606),
	// 	array(609, 623),
	// 	array(290, 627),
	// 	array(945, 615),
	// 	array(948, 616),
	// 	array(176, 612),
	// 	array(433, 619),
	// 	array(172, 602),
	// 	array(230, 700),
	// 	array(229, 800),
	// 	array(35, 625),		// Agregado el 23-Oct-2009
	// 	array(713, 622),	// Agregado el 23-Oct-2009
	// 	array(390, 608),	// Agregado el 23-Oct-2009
	// 	array(715, 620)		// Agregado el 25-May-2010
	// );

	// foreach ($rel as $r) {
	// 	$sql .= '
	// 		INSERT INTO
	// 			estado_cuenta
	// 				(
	// 					num_cia,
	// 					fecha,
	// 					tipo_mov,
	// 					importe,
	// 					cod_mov,
	// 					concepto,
	// 					cuenta,
	// 					iduser,
	// 					timestamp,
	// 					tipo_con
	// 				)
	// 			SELECT
	// 				' . $r[1] . ',
	// 				fecha_gen,
	// 				FALSE,
	// 				te.importe,
	// 				CASE
	// 					WHEN concepto LIKE \'%RENTA%\' THEN
	// 						2
	// 					WHEN concepto LIKE \'%HONORARIOS%\' OR concepto LIKE \'%OFICINA%\' OR concepto LIKE \'%TALLERES%\' THEN
	// 						29
	// 					ELSE
	// 						29
	// 				END,
	// 				CASE
	// 					WHEN concepto LIKE \'%RENTA%\' THEN
	// 						\'RENTA (\' || num_cia || \' \' || nombre_corto || \' \' || folio || \')\'
	// 					WHEN concepto LIKE \'%HONORARIOS%\' THEN
	// 						\'HONORARIOS (\' || num_cia || \' \' || nombre_corto || \' \' || folio || \')\'
	// 					WHEN concepto LIKE \'%OFICINA%\' THEN
	// 						\'OFICINA (\' || num_cia || \' \' || nombre_corto || \' \' || folio || \')\'
	// 					WHEN concepto LIKE \'%TALLERES%\' THEN
	// 						\'TALLERES (\' || num_cia || \' \' || nombre_corto || \' \' || folio || \')\'
	// 					WHEN concepto LIKE  \'%CAPACITACION\' THEN
	// 						\'CAPACITACION (\' || num_cia || \' \' || nombre_corto || \' \' || folio || \')\'
	// 					ELSE
	// 						\'TRASPASO (\' || num_cia || \' \' || nombre_corto || \' \' || folio || \')\'
	// 				END,
	// 				CASE
	// 					WHEN num_banco = \'072\' THEN
	// 						1
	// 					WHEN num_banco = \'014\' THEN
	// 						2
	// 					ELSE
	// 						1
	// 				END,
	// 				' . $_SESSION['iduser'] . ',
	// 				NOW(),
	// 				0
	// 			FROM
	// 				transferencias_electronicas te
	// 				LEFT JOIN cheques
	// 					USING (num_cia, folio, cuenta)
	// 				LEFT JOIN catalogo_proveedores cp
	// 					ON (cp.num_proveedor = te.num_proveedor)
	// 				LEFT JOIN catalogo_companias
	// 					USING (num_cia)
	// 				LEFT JOIN catalogo_bancos
	// 					USING (idbanco)
	// 			WHERE
	// 				te.num_proveedor = ' . $r[0] . '
	// 				AND folio_archivo = ' . $folio . '
	// 				AND te.status = 1
	// 				AND te.gen_dep = TRUE
	// 				/*AND (num_cia, te.cuenta, fecha_gen, te.importe) NOT IN (
	// 					SELECT
	// 						num_cia,
	// 						cuenta,
	// 						fecha,
	// 						importe
	// 					FROM
	// 						estado_cuenta
	// 					WHERE
	// 						(num_cia, fecha, tipo_mov, importe, concepto) IN (
	// 							SELECT
	// 								' . $r[1] . ',
	// 								fecha_gen,
	// 								FALSE,
	// 								ste.importe,
	// 								CASE
	// 									WHEN concepto LIKE \'%RENTA%\' THEN
	// 										\'RENTA (\' || num_cia || \' \' || nombre_corto || \' \' || folio || \')\'
	// 									WHEN concepto LIKE \'%HONORARIOS%\' THEN
	// 										\'HONORARIOS (\' || num_cia || \' \' || nombre_corto || \' \' || folio || \')\'
	// 									WHEN concepto LIKE \'%OFICINA%\' THEN
	// 										\'OFICINA (\' || num_cia || \' \' || nombre_corto || \' \' || folio || \')\'
	// 									WHEN concepto LIKE \'%TALLERES%\' THEN
	// 										\'TALLERES (\' || num_cia || \' \' || nombre_corto || \' \' || folio || \')\'
	// 									WHEN concepto LIKE \'%CAPACITACION\' THEN
	// 										\'CAPACITACION (\' || num_cia || \' \' || nombre_corto || \' \' || folio || \')\'
	// 									ELSE
	// 										\'TRASPASO (\' || num_cia || \' \' || nombre_corto || \' \' || folio || \')\'
	// 								END
	// 							FROM
	// 								transferencias_electronicas ste
	// 								LEFT JOIN cheques sc
	// 									USING (num_cia, folio, cuenta)
	// 								LEFT JOIN catalogo_proveedores scp
	// 									ON (cp.num_proveedor = te.num_proveedor)
	// 								LEFT JOIN catalogo_companias scc
	// 									USING (num_cia)
	// 								LEFT JOIN catalogo_bancos scb
	// 									USING (idbanco)
	// 							WHERE
	// 								te.num_proveedor = ' . $r[0] . '
	// 								AND folio_archivo = ' . $folio . '
	// 								AND te.status = 1
	// 						)
	// 				)*/

	// 		' . ";\n";
	// }


	if ( !! $ids) {
		// Actualizar status, folio y usuario que genero el archivo
		$db->query("UPDATE transferencias_electronicas SET status = 1, folio_archivo = {$folio}, iduser = {$_SESSION['iduser']} WHERE id IN (" . implode(', ', $ids) . ")");

		$sql = "INSERT INTO estado_cuenta (
				num_cia,
				fecha,
				tipo_mov,
				importe,
				cod_mov,
				concepto,
				cuenta,
				iduser,
				TIMESTAMP,
				tipo_con
			)
			SELECT
				CASE
					WHEN c.num_proveedor BETWEEN 7001 AND 7999 THEN
						c.num_proveedor - 7000
					WHEN c.num_proveedor BETWEEN 8001 AND 8999 THEN
						c.num_proveedor - 8000
					WHEN c.num_proveedor = 605 THEN
						625
					WHEN c.num_proveedor = 312 THEN
						628
					WHEN c.num_proveedor = 1231 THEN
						628
					WHEN c.num_proveedor = 423 THEN
						601
					WHEN c.num_proveedor = 434 THEN
						617
					WHEN c.num_proveedor = 417 THEN
						614
					WHEN c.num_proveedor = 435 THEN
						611
					WHEN c.num_proveedor = 576 THEN
						618
					WHEN c.num_proveedor = 174 THEN
						603
					WHEN c.num_proveedor = 441 THEN
						613
					WHEN c.num_proveedor = 644 THEN
						605
					WHEN c.num_proveedor = 171 THEN
						604
					WHEN c.num_proveedor = 173 THEN
						607
					WHEN c.num_proveedor = 422 THEN
						610
					WHEN c.num_proveedor = 436 THEN
						606
					WHEN c.num_proveedor = 609 THEN
						623
					WHEN c.num_proveedor = 290 THEN
						627
					WHEN c.num_proveedor = 945 THEN
						615
					WHEN c.num_proveedor = 948 THEN
						616
					WHEN c.num_proveedor = 176 THEN
						612
					WHEN c.num_proveedor = 433 THEN
						619
					WHEN c.num_proveedor = 172 THEN
						602
					WHEN c.num_proveedor = 230 THEN
						700
					WHEN c.num_proveedor = 229 THEN
						800
					WHEN c.num_proveedor = 35 THEN
						625
					WHEN c.num_proveedor = 713 THEN
						622
					WHEN c.num_proveedor = 390 THEN
						608
					WHEN c.num_proveedor = 715 THEN
						620
				END,
				fecha_gen,
				FALSE,
				te.importe,
				CASE
					WHEN concepto LIKE '%RENTA%' THEN
						2
					WHEN concepto LIKE '%HONORARIOS%'
					OR concepto LIKE '%OFICINA%'
					OR concepto LIKE '%TALLERES%' THEN
						29
					ELSE
						29
				END,
				CASE
					WHEN concepto LIKE '%RENTA%' THEN
						'RENTA (' || num_cia || ' ' || nombre_corto || ' ' || folio || ')'
					WHEN concepto LIKE '%HONORARIOS%' THEN
						'HONORARIOS (' || num_cia || ' ' || nombre_corto || ' ' || folio || ')'
					WHEN concepto LIKE '%OFICINA%' THEN
						'OFICINA (' || num_cia || ' ' || nombre_corto || ' ' || folio || ')'
					WHEN concepto LIKE '%TALLERES%' THEN
						'TALLERES (' || num_cia || ' ' || nombre_corto || ' ' || folio || ')'
					WHEN concepto LIKE '%CAPACITACION' THEN
						'CAPACITACION (' || num_cia || ' ' || nombre_corto || ' ' || folio || ')'
					ELSE
						'TRASPASO (' || num_cia || ' ' || nombre_corto || ' ' || folio || ')'
				END,
				CASE
					WHEN num_banco = '072' THEN
						1
					WHEN num_banco = '014' THEN
						2
					ELSE
						1
				END,
				1,
				NOW(),
				0
			FROM
				transferencias_electronicas te
			LEFT JOIN cheques c USING (num_cia, folio, cuenta)
			LEFT JOIN catalogo_proveedores cp ON (
				cp.num_proveedor = te.num_proveedor
			)
			LEFT JOIN catalogo_companias USING (num_cia)
			LEFT JOIN catalogo_bancos USING (idbanco)
			WHERE
				(
					te.num_proveedor IN (
						605,
						312,
						1231,
						423,
						434,
						417,
						435,
						576,
						174,
						441,
						644,
						171,
						173,
						422,
						436,
						609,
						290,
						945,
						948,
						176,
						433,
						172,
						230,
						229,
						35,
						713,
						390,
						715
					)
					OR te.num_proveedor BETWEEN 7001 AND 8999
				)
			AND te.folio_archivo = {$folio}
			AND te.status = 1
			AND te.gen_dep = TRUE;\n";

		$sql .= "UPDATE transferencias_electronicas SET gen_dep = FALSE WHERE folio = {$folio};\n";

		$db->query($sql);
	}



	// [18-Nov-2009] Insertar depositos de condimento
	//$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, tipo_con) SELECT 403, fecha, 'FALSE', importe, 29, 'CONDIMENTO (' || num_cia || ' ' || nombre_corto || ')', 2, 1, now(), 0 FROM cheques LEFT JOIN catalogo_companias USING (num_cia) WHERE (num_cia, cuenta, folio) IN (SELECT num_cia, cuenta, folio FROM transferencias_electronicas WHERE folio_archivo = $folio AND num_proveedor = 937 AND status = 1);\n";


	$tpl->printToScreen();

	die;
}

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['folio'])) {
	$pros = array();
	if (isset($_GET['num_pro']))
		foreach ($_GET['num_pro'] as $pro)
			if ($pro > 0)
				$pros[] = $pro;

	$sql = "
		SELECT
			te.num_cia,
			cc.nombre
				AS nombre_cia,
			te.num_proveedor
				AS num_pro,
			a_nombre
				AS nombre_pro,
			te.folio,
			ch.facturas,
			te.importe,
			te.fecha_gen,
			ch.concepto
				AS concepto,
			te.folio_archivo
		FROM
			transferencias_electronicas AS te
			LEFT JOIN catalogo_companias AS cc
				USING (num_cia)
			LEFT JOIN cheques AS ch
				ON (ch.num_cia = te.num_cia AND ch.cuenta = te.cuenta AND ch.folio = te.folio AND ch.fecha = te.fecha_gen)
				-- USING (num_cia, folio, cuenta)
		WHERE
			folio_archivo = $_GET[folio]
			AND te.status = 1";
	$sql .= count($pros) > 0 ? ' AND te.num_proveedor IN (' . implode(', ', $pros) . ')' : '';
	$sql .= " ORDER BY nombre_pro, num_pro, num_cia, folio";
	$result = $db->query($sql);

	// Listados para proveedores
	$num_pro = NULL;
	foreach ($result as $reg) {
		if ($num_pro != $reg['num_pro']) {
			if ($num_pro != NULL) {
				$tpl->assign("listado.total", number_format($total, 2, ".", ","));
				$tpl->assign("listado.salto", "<br style=\"page-break-after:always;\">");
			}

			$num_pro = $reg['num_pro'];

			$pass = $db->query("SELECT pass_site FROM catalogo_proveedores WHERE num_proveedor = $num_pro");

			$tpl->newBlock("listado");
			$tpl->assign("num_pro", $num_pro);
			$tpl->assign("nombre", $reg['nombre_pro']);
			$tpl->assign("folio", $reg['folio_archivo']);
			$tpl->assign('user', str_pad($num_pro, 5, '0', STR_PAD_LEFT));
			$tpl->assign('pass', $pass[0]['pass_site']);
			$total = 0;
		}
		$tpl->newBlock("fila");
		$tpl->assign("num_cia", $reg['num_cia']);
		$tpl->assign("nombre", $reg['nombre_cia']);
		$tpl->assign("fecha", $reg['fecha_gen']);
		$tpl->assign("folio", $reg['folio']);
		$tpl->assign("concepto", $reg['concepto']);
		$tpl->assign("facturas", $reg['facturas']);
		$tpl->assign("importe", number_format($reg['importe'], 2, ".", ","));
		$total += $reg['importe'];
	}
	if ($num_pro != NULL) {
		$tpl->assign("listado.total", number_format($total, 2, ".", ","));
		$tpl->assign("listado.salto", "<br style=\"page-break-after:always;\">");
	}

	// Listado de Totales
	$sql = "SELECT num_proveedor AS num_pro, nombre, te.cuenta, sum(importe) AS importe FROM transferencias_electronicas te LEFT JOIN catalogo_proveedores USING (num_proveedor) WHERE";
	$sql .= " folio_archivo = $_GET[folio] AND status = 1 GROUP BY num_pro, nombre, te.cuenta ORDER BY nombre, te.cuenta";
	$totales = $db->query($sql);

	$tpl->newBlock("totales");
	$tpl->assign("fecha", date("d/m/Y"));
	$tpl->assign("folio", $_GET['folio']);

	$total_ban = 0;
	$total_san = 0;
	foreach ($totales as $total) {
		$tpl->newBlock("total");
		$tpl->assign("num_pro", $total['num_pro']);
		$tpl->assign("nombre", $total['nombre']);
		$tpl->assign("banco", $total['cuenta'] == 1 ? "BANORTE" : "SANTANDER");
		$tpl->assign("importe", number_format($total['importe'], 2, ".", ","));
		$total_ban += $total['cuenta'] == 1 ? $total['importe'] : 0;
		$total_san += $total['cuenta'] == 2 ? $total['importe'] : 0;
	}
	$tpl->assign("totales.total_san", number_format($total_san, 2, ".", ","));
	$tpl->assign("totales.total_ban", number_format($total_ban, 2, ".", ","));
	$tpl->assign("totales.total", number_format($total_san + $total_ban, 2, ".", ","));

	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

$tpl->assign('fecha_corte', date('d/m/Y'));

$tpl->printToScreen();
?>
