<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

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
	12 => 'DICIEMBRE'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die('EN PROCESO DE ACTUALIZACION');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'generar':
			$condiciones[] = 'anio = ' . $_REQUEST['anio'];

			$condiciones[] = 'mes = ' . $_REQUEST['mes'];

			/*
			@ Intervalo de compañías
			*/
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

			$querys = array();

			foreach ($_REQUEST['emisor'] as $emisor) {
				$data = json_decode($emisor);

				if ($data->tipo == 'oficinas') {
					$cod = 30;
				}
				else if ($data->tipo == 'talleres') {
					$cod = 66;
				}
				else if ($data->tipo == 'capacitacion') {
					$cod = 191;
				}

				$querys[] = '
					SELECT
						' . $cod . '
							AS gasto,
						' . $data->emisor . '
							AS
								emisor,
						num_cia,
						100000 + num_cia
							AS
								clave_cliente,
						razon_social
							AS
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
						email
							AS
								email_cliente,
						ROUND(importe::numeric / 1.16, 2)
							AS
								importe,
						importe - ROUND(importe::numeric / 1.16, 2)
							AS
								iva,
						importe
							AS
								total,
						3
							AS
								tipo,
						CASE
							WHEN LENGTH(TRIM(clabe_cuenta)) = 11 THEN
								RIGHT(TRIM(clabe_cuenta), 4)
							WHEN LENGTH((SELECT TRIM(clabe_cuenta) FROM catalogo_companias WHERE rfc = cc.rfc AND LENGTH(TRIM(clabe_cuenta)) = 11 ORDER BY num_cia LIMIT 1)) = 11 THEN
								(SELECT RIGHT(TRIM(clabe_cuenta), 4) FROM catalogo_companias WHERE rfc = cc.rfc AND LENGTH(TRIM(clabe_cuenta)) = 11 ORDER BY num_cia LIMIT 1)
							ELSE
								NULL
						END AS ultimos_digitos
					FROM
							importes_' . $data->tipo . '
						LEFT JOIN
							catalogo_companias cc
								USING
									(num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
				';
			}

			$sql = implode(' UNION ', $querys) . ' ORDER BY emisor, num_cia';

			$importes = $db->query($sql);

			if (!$importes) {
				echo -4;
			}
			else {
				// include_once('includes/class.facturas.v2.inc.php');
				include_once('includes/class.facturas.v3.inc.php');

				// $dbf = new DBclass('pgsql://lecaroz:pobgnj@192.168.1.251:5432/ob_lecaroz', 'autocommit=yes');

				$fac = new FacturasClass();

				$tpl = new TemplatePower('plantillas/fac/FacturasOficinasTalleresResult.tpl');
				$tpl->prepare();

				$proveedor = array(
					700 => 230,
					800 => 229
				);

				$gasto = array(
					700 => 30,
					800 => 66
				);


				$emisor = NULL;
				foreach ($importes as $rec) {
					if ($emisor != $rec['emisor']) {
						$emisor = $rec['emisor'];

						$tpl->newBlock('emisor');
						$tpl->assign('emisor', $emisor == 700 ? ($rec['gasto'] == 191 ? 'OFICINAS (CAPACITACION)' : 'OFICINAS') : 'TALLERES');

						$total = 0;
					}

					$tpl->newBlock('row');
					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('nombre_cia', $rec['nombre_cliente']);
					$tpl->assign('mes', $_meses[$_REQUEST['mes']]);
					$tpl->assign('anio', $_REQUEST['anio']);
					$tpl->assign('importe', number_format($rec['total'], 2, '.', ','));

					$total += $rec['importe'];

					$tpl->assign('emisor.total', number_format($total, 2, '.', ','));

					$datos = array(
						'cabecera' => array (
							'num_cia'               => $emisor,
							'clasificacion'         => $rec['gasto'] == 191 ? 7 : 3,
							'fecha'                 => date('d/m/Y'),
							'hora'                  => date('H:m:s'),
							'clave_cliente'         => $rec['clave_cliente'],
							'nombre_cliente'        => strtoupper($rec['nombre_cliente']),
							'rfc_cliente'           => strtoupper($rec['rfc']),
							'calle'                 => strtoupper($rec['calle']),
							'no_exterior'           => strtoupper($rec['no_exterior']),
							'no_interior'           => strtoupper($rec['no_interior']),
							'colonia'               => strtoupper($rec['colonia']),
							'localidad'             => strtoupper($rec['localidad']),
							'referencia'            => strtoupper($rec['referencia']),
							'municipio'             => strtoupper($rec['municipio']),
							'estado'                => strtoupper($rec['estado']),
							'pais'                  => strtoupper($rec['pais']),
							'codigo_postal'         => strtoupper($rec['codigo_postal']),
							'email'                 => strtolower($rec['email_cliente']),
							'observaciones'         => '',
							'importe'               => $rec['importe'],
							'porcentaje_descuento'  => 0,
							'descuento'             => 0,
							'ieps'                  => 0,
							'porcentaje_iva'        => 16,
							'importe_iva'           => $rec['iva'],
							'aplicar_retenciones'   => 'N',
							'importe_retencion_isr' => 0,
							'importe_retencion_iva' => 0,
							'total'                 => $rec['total'],
							'tipo_pago'				=> '1',
							'cuenta_pago'			=> $rec['ultimos_digitos'],
							'condiciones_pago'		=> 2
						),
						'consignatario' => array (
							'nombre'        => '',
							'rfc'           => '',
							'calle'         => '',
							'no_exterior'   => '',
							'no_interior'   => '',
							'colonia'       => '',
							'localidad'     => '',
							'referencia'    => '',
							'municipio'     => '',
							'estado'        => '',
							'pais'          => '',
							'codigo_postal' => ''
						),
						'detalle' => array()
					);

					$datos['detalle'][0]['clave'] = 1;
					$datos['detalle'][0]['descripcion'] = ($rec['gasto'] == 191 ? 'SERVICIO DE CAPACITACION INTEGRAL' : 'COBRO DE ADMINISTRACION') . ' ' . $_meses[$_REQUEST['mes']] . ' ' . $_REQUEST['anio'];
					$datos['detalle'][0]['cantidad'] = 1;
					$datos['detalle'][0]['unidad'] = 'NO APLICA';
					$datos['detalle'][0]['precio'] = $rec['importe'];
					$datos['detalle'][0]['importe'] = $rec['importe'];
					$datos['detalle'][0]['descuento'] = 0;
					$datos['detalle'][0]['porcentaje_iva'] = 16;
					$datos['detalle'][0]['importe_iva'] = $rec['iva'];
					$datos['detalle'][0]['numero_pedimento'] = '';
					$datos['detalle'][0]['fecha_entrada'] = '';
					$datos['detalle'][0]['aduana_entrada'] = '';

					$status = $fac->generarFactura($_SESSION['iduser'], $emisor, 1, $datos);

					if ($status < 0) {
						$tpl->assign('folio', '&nbsp;');

						$tpl->assign('status', '<span style="color:#600;">' . $fac->ultimoError() . '</span>');
					}
					else {
						$pieces = explode('-', $status);

						$tpl->assign('folio', $pieces[1]);

						$tpl->assign('status', '<span style="color:#060;">OK</span>');

						$sql = '
							INSERT INTO
								facturas
									(
										num_cia,
										num_proveedor,
										num_fact,
										fecha,
										importe,
										piva,
										iva,
										pretencion_isr,
										pretencion_iva,
										retencion_isr,
										retencion_iva,
										total,
										codgastos,
										tipo_factura,
										fecha_captura,
										iduser,
										concepto
									)
								VALUES
									(
										' . $rec['num_cia'] . ',
										' . $proveedor[$emisor] . ',
										\'' . $pieces[1] . '\',
										\'' . date('d/m/Y') . '\',
										' . $rec['importe'] . ',
										16,
										' . $rec['iva'] . ',
										0,
										0,
										0,
										0,
										' . $rec['total'] . ',
										' . $rec['gasto'] . ',
										3,
										now()::date,
										' . $_SESSION['iduser'] . ',
										\'' . ($rec['gasto'] == 191 ? 'SERVICIO DE CAPACITACION INTEGRAL' : 'COBRO DE ADMINISTRACION') . ' ' . $_meses[$_REQUEST['mes']] . ' ' . $_REQUEST['anio'] . '\'
									)
						' . ";\n";

						$sql .= '
							INSERT INTO
								pasivo_proveedores
									(
										num_cia,
										num_proveedor,
										num_fact,
										fecha,
										descripcion,
										codgastos,
										total,
										copia_fac
									)
								VALUES
									(
										' . $rec['num_cia'] . ',
										' . $proveedor[$emisor] . ',
										\'' .  $pieces[1] . '\',
										\'' . date('d/m/Y') . '\',
										\'' . ($rec['gasto'] == 191 ? 'SERVICIO DE CAPACITACION INTEGRAL' : 'COBRO DE ADMINISTRACION') . ' ' . $_meses[$_REQUEST['mes']] . ' ' . $_REQUEST['anio'] . '\',
										' . $rec['gasto'] . ',
										' . $rec['total'] . ',
										TRUE
									)
						' . ";\n";

						$db->query($sql);
					}
				}

				echo $tpl->getOutputContent();
			}
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturasOficinasTalleres.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y'));
$tpl->assign(date('n'), ' selected');

$tpl->printToScreen();
?>
