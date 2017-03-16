<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

if(!function_exists('json_encode')) {
	include_once('includes/JSON.php');
	
	$GLOBALS['JSON_OBJECT'] = new Services_JSON();
	
	function json_encode($value) {
		return $GLOBALS['JSON_OBJECT']->encode($value); 
	}
	
	function json_decode($value) {
		return $GLOBALS['JSON_OBJECT']->decode($value); 
	}
}

$_meses = array(
	1  => 'ENERO',
	2  => 'FEBRERO',
	3  => 'MARZO',
	4  => 'ABRIL',
	5  => 'MAYO',
	6  => 'JUNIO',
	7  => 'JULIO',
	8  => 'AGOSTO',
	9  => 'SEPTIEMBRE',
	10 => 'OCTUBRE',
	11 => 'NOVIEMBRE',
	12 => 'DICIMEBRE'
);

$_dias = array(
	0 => 'D',
	1 => 'L',
	2 => 'M',
	3 => 'M',
	4 => 'J',
	5 => 'V',
	6 => 'S'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die('MODIFICANDO');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'consultar':
			$sql = '
				SELECT
					id
				FROM
					cheques
					LEFT JOIN catalogo_companias
						USING (num_cia)
				WHERE
					cuenta = 1
					AND cod_mov = 5
					AND archivo = TRUE
					AND importe > 0
					AND LENGTH(TRIM(clabe_cuenta)) >= 10
					AND fecha_cancelacion IS NULL
					AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 1 ? '1 AND 899' : '900 AND 998') . '
				LIMIT
					1
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				echo 1;
			}
		break;
		
		case 'generar':
			$sql = '
				SELECT
					id,
					clabe_cuenta
						AS cuenta,
					folio,
					importe,
					a_nombre
						AS beneficiario
				FROM
					cheques
					LEFT JOIN catalogo_companias
						USING (num_cia)
				WHERE
					cuenta = 1
					AND cod_mov = 5
					AND archivo = TRUE
					AND importe > 0
					AND LENGTH(TRIM(clabe_cuenta)) >= 10
					AND fecha_cancelacion IS NULL
					AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 1 ? '1 AND 899' : '900 AND 998') . '
				ORDER BY
					folio
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$sql = '
					SELECT
						MAX(consecutivo) + 1
							AS consecutivo
					FROM
						chequera_seguridad_banorte
					WHERE
						tsgen >= \'' . date('d/m/Y') . '\'
				';
				
				$tmp = $db->query($sql);
				
				if ($tmp[0]['consecutivo'] > 0) {
					$consecutivo = $tmp[0]['consecutivo'];
				}
				else {
					$consecutivo = 1;
				}
				
				$D = array();
				
				$cuentas = 0;
				$total = 0;
				
				$ids = array();
				
				$cuenta = NULL;
				foreach ($result as $num => $rec) {
					if ($cuenta != $rec['cuenta']) {
						$cuenta = $rec['cuenta'];
						
						$cuentas++;
					}
					
					/*
					@@ Tipo de registro
					@
					@  Tipo:        Alfabético
					@  Longitud:    1
					@  Inicio:      1
					@  Fin:         1
					@  Descripción: Valor constante D, identifica registro de detalle
					*/
					$D[$num] = 'D';
					/*
					@@ Tipo de servicio
					@
					@  Tipo:        Numérico
					@  Longitud:    2
					@  Inicio:      2
					@  Fin:         3
					@  Descripción:
					@    10 Protección de chequera 24 horas por archivo
					@    11 Protección de chequera en línea por archivo
					@    20 Protección de cheque por cheque en línea por archivo
					*/
					$D[$num] .= '11';
					/*
					@@ Consecutivo
					@
					@  Tipo:        Numérico
					@  Longitud:    5
					@  Inicio:      4
					@  Fin:         8
					@  Descripción: Número consecutivo ascendente del registro detalle dentro del archivo iniciando con 00001,
					@               alineado a la derecha y justificado con ceros a la izquierda
					*/
					$D[$num] .= str_pad($num + 1, 5, '0', STR_PAD_LEFT);
					/*
					@@ Código de operación
					@
					@  Tipo:        Numérico
					@  Longitud:    2
					@  Inicio:      9
					@  Fin:         10
					@  Descripción:
					@    60 Protección
					@    65 Cancelación de protección
					*/
					$D[$num] .= '60';
					/*
					@@ Número de cuenta
					@
					@  Tipo:        Numérico
					@  Longitud:    10
					@  Inicio:      11
					@  Fin:         20
					@  Descripción: Número de cuenta de la que tiene el servicio de cheque protegido
					*/
					$D[$num] .= substr($rec['cuenta'], -10);
					/*
					@@ Número de documento
					@
					@  Tipo:        Numérico
					@  Longitud:    7
					@  Inicio:      21
					@  Fin:         27
					@  Descripción: Número de documento (cheque), alineado a la derecha y justificado con ceros a la izquierda
					*/
					$D[$num] .= str_pad($rec['folio'], 7, '0', STR_PAD_LEFT);
					/*
					@@ Fecha de inicio de protección
					@
					@  Tipo:        Numérico
					@  Longitud:    8
					@  Inicio:      28
					@  Fin:         35
					@  Descripción: Fecha de aplicación de protección del documento, formato AAAAMMDD
					*/
					$D[$num] .= date('Ymd');
					/*
					@@ Fecha de fin de protección
					@
					@  Tipo:        Numérico
					@  Longitud:    8
					@  Inicio:      36
					@  Fin:         43
					@  Descripción: Fecha de termino de protección del documento, formato AAAAMMDD
					*/
					$D[$num] .= date('Ymd', mktime(0, 0, 0, date('n') + 6, date('d'), date('Y')));
					/*
					@@ Importe
					@
					@  Tipo:        Numérico
					@  Longitud:    13
					@  Inicio:      44
					@  Fin:         56
					@  Descripción: Importe del documento, se informarán 11 enteros y 2 decimales,
					@               alineado a la derecha y justificado con ceros a la izquierda
					*/
					$D[$num] .= str_pad(number_format($rec['importe'], 2, '', ''), 13, '0', STR_PAD_LEFT);
					/*
					@@ Número de sucursal
					@
					@  Tipo:        Numérico
					@  Longitud:    4
					@  Inicio:      57
					@  Fin:         60
					@  Descripción: Número de sucursal donde se realizo la transmisión o rellenar con ceros
					*/
					$D[$num] .= '0000';
					/*
					@@ Validar beneficiario
					@
					@  Tipo:        Alfabético
					@  Longitud:    1
					@  Inicio:      61
					@  Fin:         61
					@  Descripción: "N" no se valida
					*/
					$D[$num] .= 'N';
					/*
					@@ Nombre del beneficiario o descripción
					@
					@  Tipo:        Alfanumérico
					@  Longitud:    50
					@  Inicio:      62
					@  Fin:         111
					@  Descripción: Nombre del beneficiario o descripción corta
					*/
					$D[$num] .= str_pad(str_replace(array('ñ', 'Ñ', '&', '.', ','), array('N', 'N', '', '', ''), substr(utf8_encode($rec['beneficiario']), 0, 50)), 50, ' ');
					/*
					@@ Divisa
					@
					@  Tipo:        Alfabético
					@  Longitud:    3
					@  Inicio:      112
					@  Fin:         114
					@  Descripción: "MXP" o "DLS"
					*/
					$D[$num] .= 'MXP';
					/*
					@@ Relleno
					@
					@  Tipo:        Numérico
					@  Longitud:    78
					@  Inicio:      115
					@  Fin:         192
					@  Descripción: Rellenar con ceros
					*/
					$D[$num] .= str_pad('', 78, '0');
					
					$total += $rec['importe'];
					
					$ids[] = $rec['id'];
				}
				
				/*
				@@ Tipo de registro
				@
				@  Tipo:        Alfabético
				@  Longitud:    1
				@  Inicio:      1
				@  Fin:         1
				@  Descripción: Valor constante H, identifica registro de encabezado
				*/
				$H = 'H';
				/*
				@@ Número de cuentas
				@
				@  Tipo:        Numérico
				@  Longitud:    5
				@  Inicio:      2
				@  Fin:         6
				@  Descripción: Indica el número de cuentas que contiene el archivo, alineado a la derecha y justificado con ceros a la izquierda
				*/
				$H .= str_pad($cuentas, 5, '0', STR_PAD_LEFT);
				/*
				@@ Fecha de presentación
				@
				@  Tipo:        Numérico
				@  Longitud:    8
				@  Inicio:      7
				@  Fin:         14
				@  Descripción: Fecha en que se envía el archivo, formato AAAAMMDD
				*/
				$H .= date('Ymd');
				/*
				@@ Consecutivo
				@
				@  Tipo:        Numérico
				@  Longitud:    3
				@  Inicio:      15
				@  Fin:         17
				@  Descripción: Número consecutivo de archivo enviado en el día (000 al 999),
				@               alineado a la derecha y justificado con ceros a la izquierda
				*/
				$H .= str_pad($consecutivo, 3, '0', STR_PAD_LEFT);
				/*
				@@ Número de registros a proteger
				@
				@  Tipo:        Numérico
				@  Longitud:    5
				@  Inicio:      18
				@  Fin:         22
				@  Descripción: Número de registros a proteger, alineado a la derecha y justificado con ceros a la izquierda
				*/
				$H .= str_pad(count($result), 5, '0', STR_PAD_LEFT);
				/*
				@@ Importe total de los registros a proteger
				@
				@  Tipo:        Numérico
				@  Longitud:    18
				@  Inicio:      23
				@  Fin:         40
				@  Descripción: Importe total de los registros a proteger, se informaran 16 enteros y 2 decimales,
				@               alineado a la derecha y justificado con ceros a la izquierda
				*/
				$H .= str_pad(number_format($total, 2, '', ''), 18, '0', STR_PAD_LEFT);
				/*
				@@ Número de registros a desproteger
				@
				@  Tipo:        Numérico
				@  Longitud:    5
				@  Inicio:      41
				@  Fin:         45
				@  Descripción: Número de registros a desproteger, alineado a la derecha y justificado con ceros a la izquierda
				*/
				$H .= '00000';
				/*
				@@ Importe total de los registros a desproteger
				@
				@  Tipo:        Numérico
				@  Longitud:    18
				@  Inicio:      46
				@  Fin:         63
				@  Descripción: Importe total de los registros a desproteger, se informaran 16 enteros y 2 decimales,
				@               alineado a la derecha y justificado con ceros a la izquierda
				*/
				$H .= str_pad('', 18, '0', STR_PAD_LEFT);
				/*
				@@ Número de registros total
				@
				@  Tipo:        Numérico
				@  Longitud:    5
				@  Inicio:      64
				@  Fin:         68
				@  Descripción: Número de registros contenidos en el archivo
				*/
				$H .= str_pad(count($result), 5, '0', STR_PAD_LEFT);
				/*
				@@ Importe total de los registros en el archivo
				@
				@  Tipo:        Numérico
				@  Longitud:    18
				@  Inicio:      69
				@  Fin:         86
				@  Descripción: Importe total de los registros en el archivo, se informaran 16 enteros y 2 decimales,
				@               alineado a la derecha y justificado con ceros a la izquierda
				*/
				$H .= str_pad(number_format($total, 2, '', ''), 18, '0', STR_PAD_LEFT);
				/*
				@@ Relleno
				@
				@  Tipo:        Numérico
				@  Longitud:    106
				@  Inicio:      87
				@  Fin:         192
				@  Descripción: Rellenar con ceros
				*/
				$H .= str_pad('', 106, '0');
				
				$sql = '
					UPDATE
						cheques
					SET
						archivo = FALSE
					WHERE
						id IN (' . implode(', ', $ids) . ')
				' . ";\n";
				
				$sql .= '
					INSERT INTO
						chequera_seguridad_banorte
							(
								iduser,
								tsgen,
								consecutivo,
								cantidad,
								total
							)
						VALUES
							(
								' . $_SESSION['iduser'] . ',
								now(),
								' . $consecutivo . ',
								' . count($result) . ',
								' . $total . '
							)
				' . ";\n";
				
				$db->query($sql);
				
				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename="08872838' . date('ymd') . str_pad($consecutivo, 3, '0', STR_PAD_LEFT) . '.CHP"');
				
				echo $H . "\r\n" . implode("\r\n", $D);
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ban/ChequeraSeguridadBanorte.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');



$tpl->printToScreen();
?>
