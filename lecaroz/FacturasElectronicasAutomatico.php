<?php
//die('ACTUALIZANDO CERTIFICADOS, NO SE FACTURARA HOY');

include('/var/www/lecaroz/includes/class.db.inc.php');
include('/var/www/lecaroz/includes/dbstatus.php');
// include_once('/var/www/lecaroz/includes/class.facturas.v2.inc.php');
include_once('/var/www/lecaroz/includes/class.facturas.v3.inc.php');

function toInt($value) {
	return intval($value, 10);
}

$db = new DBclass($dsn, 'autocommit=yes');
// $dbf = new DBclass('pgsql://lecaroz:pobgnj@192.168.1.251:5432/ob_lecaroz', 'autocommit=yes');

$sql = '
	SELECT
		*
	FROM
		facturas_electronicas_status_automatico
	WHERE
		tipo = 2
';

$status_automatico = $db->query($sql);

if ($status_automatico) {
	echo '@@ Proceso automático de facturas electrónicas de zapaterias bloqueado';

	die(-1);
}
else {
	$sql = '
		INSERT INTO
			facturas_electronicas_status_automatico (
				tipo
			)
			VALUES (
				2
			)
	';

	$db->query($sql);
}

$sql = '
	UPDATE
		facturas_panaderias_tmp
	SET
		fecha_pago = fecha
	WHERE
		fecha_pago IS NULL
';

$db->query($sql);

$condiciones = array();

$condiciones[] = 'tsreg IS NULL';

$condiciones[] = 'num_cia >= 900';

if (isset($_REQUEST['cias']) && $_REQUEST['cias'] != '') {
	if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
		$cias = array();

		$pieces = explode(',', $_REQUEST['cias']);
		foreach ($pieces as $piece) {
			if (count($exp = explode('-', $piece)) > 1) {
				$cias[] =  implode(', ', range($exp[0], $exp[1]));
			}
			else {
				$cias[] = $piece;
			}
		}

		if (count($cias) > 0) {
			$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
		}
	}
}

$sql = '
	SELECT
		f.id,
		f.num_cia,
		f.tipo_serie,
		f.fecha,
		/*DATE_TRUNC(\'SECOND\', f.hora)
			AS hora,*/
		f.hora,
		f.consecutivo,
		f.clave_cliente,
		TRIM(regexp_replace(f.nombre_cliente, \'\s+\', \' \', \'g\'))
			AS nombre_cliente,
		TRIM(regexp_replace(f.rfc, \'\s+\', \' \', \'g\'))
			AS rfc,
		TRIM(regexp_replace(f.calle, \'\s+\', \' \', \'g\'))
			AS calle,
		TRIM(regexp_replace(f.no_exterior, \'\s+\', \' \', \'g\'))
			AS no_exterior,
		TRIM(regexp_replace(f.no_interior, \'\s+\', \' \', \'g\'))
			AS no_interior,
		TRIM(regexp_replace(f.colonia, \'\s+\', \' \', \'g\'))
			AS colonia,
		TRIM(regexp_replace(f.localidad, \'\s+\', \' \', \'g\'))
			AS localidad,
		TRIM(regexp_replace(f.referencia, \'\s+\', \' \', \'g\'))
			AS referencia,
		TRIM(regexp_replace(f.municipio, \'\s+\', \' \', \'g\'))
			AS municipio,
		CASE
			WHEN TRIM(f.estado) <> \'\' THEN
				TRIM(regexp_replace(f.estado, \'\s+\', \' \', \'g\'))
			ELSE
				TRIM(regexp_replace(cc.estado, \'\s+\', \' \', \'g\'))
		END
			AS estado,
		TRIM(regexp_replace(f.pais, \'\s+\', \' \', \'g\'))
			AS pais,
		TRIM(regexp_replace(f.codigo_postal, \'\s+\', \' \', \'g\'))
			AS codigo_postal,

		TRIM(regexp_replace(f.nombre_consignatario, \'\s+\', \' \', \'g\'))
			AS nombre_consignatario,
		TRIM(regexp_replace(f.rfc_consignatario, \'\s+\', \' \', \'g\'))
			AS rfc_consignatario,
		TRIM(regexp_replace(f.calle_consignatario, \'\s+\', \' \', \'g\'))
			AS calle_consignatario,
		TRIM(regexp_replace(f.no_exterior_consignatario, \'\s+\', \' \', \'g\'))
			AS no_exterior_consignatario,
		TRIM(regexp_replace(f.no_interior_consignatario, \'\s+\', \' \', \'g\'))
			AS no_interior_consignatario,
		TRIM(regexp_replace(f.colonia_consignatario, \'\s+\', \' \', \'g\'))
			AS colonia_consignatario,
		TRIM(regexp_replace(f.localidad_consignatario, \'\s+\', \' \', \'g\'))
			AS localidad_consignatario,
		TRIM(regexp_replace(f.referencia_consignatario, \'\s+\', \' \', \'g\'))
			AS referencia_consignatario,
		TRIM(regexp_replace(f.municipio_consignatario, \'\s+\', \' \', \'g\'))
			AS municipio_consignatario,
		TRIM(regexp_replace(f.estado_consignatario, \'\s+\', \' \', \'g\'))
			AS estado_consignatario,
		TRIM(regexp_replace(f.pais_consignatario, \'\s+\', \' \', \'g\'))
			AS pais_consignatario,
		TRIM(regexp_replace(f.codigo_postal_consignatario, \'\s+\', \' \', \'g\'))
			AS codigo_postal_consignatario,

		TRIM(regexp_replace(f.email_cliente, \'\s+\', \' \', \'g\'))
			AS email_cliente,
		TRIM(regexp_replace(f.observaciones, \'\s+\', \' \', \'g\'))
			AS observaciones,
		f.cantidad,
		TRIM(regexp_replace(f.descripcion, \'\s+\', \' \', \'g\'))
			AS descripcion,
		f.precio,
		TRIM(regexp_replace(f.unidad, \'\s+\', \' \', \'g\'))
			AS unidad,
		f.importe,
		f.porcentaje_descuento,
		f.descuento,
		f.ieps,
		f.iva,
		f.ret_iva,
		f.ret_isr,
		f.total,
		f.status,
		f.tipo,
		/*(
			SELECT
				tipo_pago
			FROM
				facturas_electronicas_clientes
			WHERE
				rfc_cliente = TRIM(regexp_replace(f.rfc, \'\s+\', \' \', \'g\'))
				AND tsbaja IS NULL
		)
			AS */tipo_pago,
		/*(
			SELECT
				cuenta_pago
			FROM
				facturas_electronicas_clientes
			WHERE
				rfc_cliente = TRIM(regexp_replace(f.rfc, \'\s+\', \' \', \'g\'))
				AND tsbaja IS NULL
		)
			AS */cuenta_pago,
		/*(
			SELECT
				condiciones_pago
			FROM
				facturas_electronicas_clientes
			WHERE
				rfc_cliente = TRIM(regexp_replace(f.rfc, \'\s+\', \' \', \'g\'))
				AND tsbaja IS NULL
		)
			AS */condiciones_pago,
		f.arrendador,
		f.iduser,
		f.numero_pedimento,
		f.fecha_entrada,
		f.aduana_entrada
	FROM
		facturas_panaderias_tmp f
		LEFT JOIN catalogo_companias cc
			USING (num_cia)
	WHERE
		' . implode(' AND ', $condiciones) . '
	ORDER BY
		num_cia,
		consecutivo
';
$result = $db->query($sql);

if ($result) {
	$facturas_emitidas = 0;
	$errores = 0;

	echo "--- INICIO: " . date('d/m/Y H:i:s') . " ---";

	/*
	@ Obtener última fecha de facturación
	*/
	// $sql = '
	// 	SELECT
	// 		num_cia,
	// 		(MAX(fecha) + INTERVAL \'1 day\')::date
	// 			AS fecha
	// 	FROM
	// 		facturas_electronicas
	// 	WHERE
	// 		tipo = 1
	// 		AND status = 1
	// 	GROUP BY
	// 		num_cia
	// 	ORDER BY
	// 		num_cia
	// ';
	// $ultimas_fechas_facturacion = $db->query($sql);

	// $fecha_facturacion = array();

	// if ($ultimas_fechas_facturacion) {
	// 	foreach ($ultimas_fechas_facturacion as $rec) {
	// 		list($dia, $mes, $anio) = array_map('toInt', explode('/', $rec['fecha']));

	// 		$fecha_facturacion[$rec['num_cia']] = array(
	// 			'fecha' => $rec['fecha'],
	// 			'ts'    => mktime(0, 0, 0, $mes, $dia, $anio)
	// 		);
	// 	}
	// }

	$datos = array();

	$num_cia = NULL;
	$consecutivo = NULL;

	$status_factura = FALSE;

	$cont = 0;

	foreach ($result as $rec) {
		if ($num_cia != $rec['num_cia'] || $consecutivo != $rec['consecutivo']) {
			if ($status_factura) {
				$cont++;
			}

			$num_cia = $rec['num_cia'];
			$consecutivo = $rec['consecutivo'];

			// if (!isset($fecha_facturacion[$num_cia])) {
			// 	echo "\n\n@@ Registro temporal $num_cia-$consecutivo";
			// 	echo "\n@@ Error: La compañía no tiene fecha de facturación.";

			// 	$status_factura = FALSE;

			// 	$errores++;
			// }
			// else {
				$datos[$cont] = array(
					'cabecera'          => array(
						'num_cia'               => $num_cia,
						'clasificacion'         => $rec['rfc'] == 'XAXX010101000' ? 1 : ($rec['tipo'] > 0 ? $rec['tipo'] : 2),
						'fecha'                 => $rec['fecha']/*$rec['rfc'] == 'XAXX010101000' ? $rec['fecha'] : ($rec['tipo'] > 0 ? $rec['fecha'] : $fecha_facturacion[$num_cia])*/,
						'hora'                  => $rec['hora'],
						'clave_cliente'         => $rec['clave_cliente'],
						'nombre_cliente'        => strtoupper(trim($rec['nombre_cliente'])),
						'rfc_cliente'           => strtoupper(trim($rec['rfc'])),
						'calle'                 => strtoupper(trim($rec['calle'])),
						'no_exterior'           => strtoupper(trim($rec['no_exterior'])),
						'no_interior'           => strtoupper(trim($rec['no_interior'])),
						'colonia'               => strtoupper(trim($rec['colonia'])),
						'localidad'             => strtoupper(trim($rec['localidad'])),
						'referencia'            => strtoupper(trim($rec['referencia'])),
						'municipio'             => strtoupper(trim($rec['municipio'])),
						'estado'                => strtoupper(trim($rec['estado'])),
						'pais'                  => trim($rec['pais']) != '' ? strtoupper(trim($rec['pais'])) : 'MEXICO',
						'codigo_postal'         => $rec['codigo_postal'],
						'email'                 => $rec['email_cliente'],
						'observaciones'         => strtoupper(trim($rec['observaciones'])),
						'importe'               => $rec['importe'],
						'porcentaje_descuento'  => $rec['porcentaje_descuento'],
						'descuento'             => 0,
						'ieps'                  => $rec['ieps'],
						'porcentaje_iva'        => $rec['iva'] > 0 ? 16 : 0,
						'importe_iva'           => $rec['iva'],
						'aplicar_retenciones'   => 'N',
						'importe_retencion_isr' => $rec['ret_isr'],
						'importe_retencion_iva' => $rec['ret_iva'],
						'total'                 => $rec['total'],
						'tipo_pago'             => $rec['tipo_pago'],
						'cuenta_pago'           => $rec['cuenta_pago'],
						'condiciones_pago'      => $rec['condiciones_pago'],
						'arrendador'            => $rec['arrendador']
					),
					'consignatario' => array (
						'nombre'        => $rec['nombre_consignatario'],
						'rfc'           => $rec['rfc_consignatario'],
						'calle'         => $rec['calle_consignatario'],
						'no_exterior'   => $rec['no_exterior_consignatario'],
						'no_interior'   => $rec['no_interior_consignatario'],
						'colonia'       => $rec['colonia_consignatario'],
						'localidad'     => $rec['localidad_consignatario'],
						'referencia'    => $rec['referencia_consignatario'],
						'municipio'     => $rec['municipio_consignatario'],
						'estado'        => $rec['estado_consignatario'],
						'pais'          => $rec['pais_consignatario'],
						'codigo_postal' => $rec['codigo_postal_consignatario']
					),
					'detalle'       => array(),
					'ids'           => array(),
					'num_cia'       => $num_cia,
					'tipo_serie'    => $rec['tipo_serie'] > 0 ? $rec['tipo_serie'] : 1,
					'fecha'         => $rec['fecha'],
					'consecutivo'   => $consecutivo,
					'status'        => $rec['status'],
					'iduser'		=> $rec['iduser'] > 0 ? $rec['iduser'] : 0
				);

				$clave_producto = 1;

				$status_factura = TRUE;
			// }
		}

		if ($status_factura) {
			$datos[$cont]['detalle'][] = array(
				'clave'            => $clave_producto++,
				'descripcion'      => strtoupper(trim($rec['descripcion'])),
				'cantidad'         => $rec['cantidad'],
				'unidad'           => strtoupper(trim($rec['unidad'])),
				'precio'           => $rec['precio'],
				'importe'          => round($rec['cantidad'] * $rec['precio'], 2),
				'descuento'        => $rec['descuento'],
				'porcentaje_iva'   => $rec['iva'] > 0 ? 16 : 0,
				'importe_iva'      => $rec['iva'] > 0 ? round($rec['cantidad'] * $rec['precio'] * 0.16, 2) : 0,
				'numero_pedimento' => $rec['numero_pedimento'],
				'fecha_entrada'    => $rec['fecha_entrada'],
				'aduana_entrada'   => $rec['aduana_entrada']
			);

			$datos[$cont]['cabecera']['descuento'] += $rec['descuento'];

			$datos[$cont]['ids'][] = $rec['id'];
		}
	}

	if (count($datos) > 0) {
		$fac = new FacturasClass();

		foreach ($datos as &$d) {
			// list($dia_cia, $mes_cia, $anio_cia) = array_map('toInt', explode('/', $fecha_facturacion[$d['num_cia']]));
			// list($dia_fac, $mes_fac, $anio_fac) = array_map('toInt', explode('/', $d['fecha']));

			if ($d['num_cia'] > 0) {
				/*if (mktime(0, 0, 0, $mes_cia, $dia_cia, $anio_cia) > mktime(0, 0, 0, $mes_fac, $dia_fac, $anio_fac)) {
					 $d['fecha'] = $fecha_facturacion[$d['num_cia']];
				}*/

				$status = $fac->generarFactura(0, $d['num_cia'], 1, $d);
			}
			else {
				$status = -900;
			}

			echo "\n\n@@ Registro temporal $d[num_cia]-$d[consecutivo]";

			if ($status < 0) {
				if ($status == -900) {
					echo "\n@@ Error: Compañía enviada es 0";
				}
				else if ($status == -800) {
					echo "\n@@ Error: El efectivo de la compañía no esta actualizado";
				}
				else {
					echo "\n@@ Error: " . $fac->ultimoError();
				}

				if ($d['status'] == 0) {
					$fac->enviarEmailError();
				}

				if (in_array($status, array(-9, -80, -100, -150, -151, -900))) {
					$sql = '
						INSERT INTO
							facturas_panaderias_error
								(
									num_cia,
									fecha,
									hora,
									consecutivo,
									clave_cliente,
									nombre_cliente,
									rfc,
									calle,
									no_exterior,
									no_interior,
									colonia,
									localidad,
									referencia,
									municipio,
									estado,
									pais,
									codigo_postal,
									observaciones,
									cantidad,
									descripcion,
									precio,
									unidad,
									importe,
									iva,
									total,
									tsins,
									tsreg,
									total_partida,
									email_cliente,
									tipo,
									nombre_consignatario,
									rfc_consignatario,
									calle_consignatario,
									no_exterior_consignatario,
									no_interior_consignatario,
									colonia_consignatario,
									localidad_consignatario,
									referencia_consignatario,
									municipio_consignatario,
									estado_consignatario,
									pais_consignatario,
									codigo_postal_consignatario,
									tipo_serie
								)
							SELECT
								num_cia,
								fecha,
								hora,
								consecutivo,
								clave_cliente,
								nombre_cliente,
								rfc,
								calle,
								no_exterior,
								no_interior,
								colonia,
								localidad,
								referencia,
								municipio,
								estado,
								pais,
								codigo_postal,
								observaciones,
								cantidad,
								descripcion,
								precio,
								unidad,
								importe,
								iva,
								total,
								tsins,
								tsreg,
								total_partida,
								email_cliente,
								tipo,
								nombre_consignatario,
								rfc_consignatario,
								calle_consignatario,
								no_exterior_consignatario,
								no_interior_consignatario,
								colonia_consignatario,
								localidad_consignatario,
								referencia_consignatario,
								municipio_consignatario,
								estado_consignatario,
								pais_consignatario,
								codigo_postal_consignatario,
								tipo_serie
							FROM
								facturas_panaderias_tmp
							WHERE
								id IN (' . implode(', ', $d['ids']) . ')
					' . ";\n";

					$sql .= '
						DELETE FROM
							facturas_panaderias_tmp
						WHERE
							id IN (' . implode(', ', $d['ids']) . ')
					' . ";\n";

					$db->query($sql);
				}
				else {
					$sql = '
						UPDATE
							facturas_panaderias_tmp
						SET
							status = ' . $status . '
						WHERE
							id IN (' . implode(', ', $d['ids']) . ')
					';

					$db->query($sql);
				}

				$errores++;
			}
			else {
				echo "\n@@ Comprobante generado $status";

				$email_status = $fac->enviarEmail();

				if ($email_status !== TRUE) {
					echo "\n@@ Email error: " . $email_status;
				}

				$sql = '
					UPDATE
						facturas_panaderias_tmp
					SET
						tsreg = now()
					WHERE
						id IN (' . implode(', ', $d['ids']) . ')
				';

				$db->query($sql);

				$facturas_emitidas++;
			}
		}

		echo "\n\nFacturas emitidas:             " . number_format($facturas_emitidas);
		echo "\nErrores:                       " . number_format($errores);
		echo "\nTotal de registros procesados: " . number_format($facturas_emitidas + $errores);
	}

	echo "\n\n--- TERMINO: " . date('d/m/Y H:i:s') . " ---\n\n";
}

$sql = '
	DELETE FROM
		facturas_electronicas_status_automatico
	WHERE
		tipo = 2
';

$db->query($sql);
?>
