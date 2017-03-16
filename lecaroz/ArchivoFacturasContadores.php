<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/class.auxinv.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'generar':
			$fecha1 = date('d/m/Y', $_REQUEST['mes'] > 0 ? mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']) : mktime(0, 0, 0, 1, 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', $_REQUEST['mes'] > 0 ? mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']) : mktime(0, 0, 0, 12, 31, $_REQUEST['anio']));

			$conditions1[] = 'f.fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

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
					$conditions1[] = 'f.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			// if (!in_array($_SESSION['iduser'], array(1, 4, 67))) {
			// 	if ($_SESSION['tipo_usuario'] == 2) {
			// 		$conditions1[] = 'f.num_cia BETWEEN 900 AND 998';
			// 	}
			// 	else {
			// 		$conditions1[] = '(con.iduser = ' . $_SESSION['iduser'] . ' OR aud.iduser = ' . $_SESSION['iduser'] . ')';
			// 	}
			// }

			if ($_REQUEST['contador'] > 0) {
				$conditions1[] = 'cc.idcontador = ' . $_REQUEST['contador'];
			}

			if ($_REQUEST['auditor'] > 0) {
				$conditions1[] = 'cc.idauditor = ' . $_REQUEST['auditor'];
			}

			if (!isset($_REQUEST['pendientes'])) {
				$conditions1[] = '(fp.fecha_cheque IS NOT NULL AND fp.fecha_cheque <= \'' . $fecha2 . '\')';
			}

			if (!isset($_REQUEST['pagadas'])) {
				$conditions1[] = '(fp.fecha_cheque IS NULL OR fp.fecha_cheque > \'' . $fecha2 . '\')';
			}

			if (isset($_REQUEST['solo_iva'])) {
				$conditions1[] = 'f.iva > 0';
			}

			$conditions2[] = 'f.fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			$conditions2[] = 'f.clave = 0';

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
					$conditions2[] = 'f.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (!in_array($_SESSION['iduser'], array(1, 4))) {
				$conditions2[] = '(con.iduser = ' . $_SESSION['iduser'] . ' OR aud.iduser = ' . $_SESSION['iduser'] . ')';
			}

			if ($_REQUEST['contador'] > 0) {
				$conditions2[] = 'cc.idcontador = ' . $_REQUEST['contador'];
			}

			if ($_REQUEST['auditor'] > 0) {
				$conditions2[] = 'cc.idauditor = ' . $_REQUEST['auditor'];
			}

			if (!isset($_REQUEST['pendientes'])) {
				$conditions2[] = '(f.folio IS NULL OR ec.fecha > \'' . $fecha2 . '\')';
			}

			if (!isset($_REQUEST['pagadas'])) {
				$conditions2[] = '(f.folio IS NOT NULL AND ec.fecha <= \'' . $fecha2 . '\')';
			}

			if (isset($_REQUEST['solo_iva'])) {
				$conditions2[] = 'f.iva > 0';
			}

			$sql = '
				SELECT
					f.num_cia
						AS
							"#CIA",
					cc.nombre
						AS
							"COMPAÑIA",
					f.num_proveedor
						AS
							"#PRO",
					cp.nombre
						AS
							"PROVEEDOR",
					cp.rfc
						AS
							"RFC",
					f.num_fact
						AS
							"FACTURA",
					f.fecha
						AS
							"FECHA",
					CASE
						WHEN f.importe != 0 THEN
							ROUND(f.importe::numeric, 2)
						ELSE
							NULL
					END
						AS
							"IMPORTE",
					0
						AS
							"DESCUENTOS",
					CASE
						WHEN f.importe != 0 THEN
							ROUND(f.importe::numeric, 2)
						ELSE
							NULL
					END
						AS
							"IMPORTE NETO",
					CASE
						WHEN f.iva != 0 THEN
							ROUND(f.iva::numeric, 2)
						ELSE
							NULL
					END
						AS
							"I.V.A.",
					CASE
						WHEN f.ieps != 0 THEN
							ROUND(f.ieps::numeric, 2)
						ELSE
							NULL
					END
						AS
							"I.E.P.S.",
					CASE
						WHEN f.pretencion_iva != 0 THEN
							ROUND(f.importe::numeric * f.pretencion_iva::numeric / 100, 2)
						ELSE
							NULL
					END
						AS
							"I.V.A. RETENIDO",
					CASE
						WHEN f.pretencion_isr != 0 THEN
							ROUND(f.importe::numeric * f.pretencion_isr::numeric / 100, 2)
						ELSE
							NULL
					END
						AS
							"I.S.R. RETENIDO",
					ROUND(f.total::numeric, 2)
						AS
							"TOTAL",
					fp.fecha_cheque
						AS
							"PAGADO",
					ec.fecha_con
						AS
							"COBRADO",
					fp.num_cia
						AS
							"#CIAPAGO",
					ccfp.nombre
						AS
							"COMPAÑIA PAGO",
					CASE
						WHEN fp.cuenta = 1 THEN
							\'BANORTE\'
						WHEN fp.cuenta = 2 THEN
							\'SANTANDER\'
						ELSE
							NULL
					END
						AS
							"BANCO",
					fp.folio_cheque
						AS
							"CHEQUE"
				FROM
						facturas
							f
					LEFT JOIN
						facturas_pagadas
							fp
								USING
									(
										num_proveedor,
										num_fact,
										fecha
									)
					LEFT JOIN
						estado_cuenta
							ec
								ON
									(
											ec.num_cia = fp.num_cia
										AND
											ec.cuenta = fp.cuenta
										AND
											ec.folio = fp.folio_cheque
									)
					LEFT JOIN
						catalogo_proveedores
							cp
								USING
									(
										num_proveedor
									)
					LEFT JOIN
						catalogo_companias
							cc
								ON
									(
										cc.num_cia = f.num_cia
									)
					LEFT JOIN
						catalogo_companias
							ccfp
								ON
									(
										ccfp.num_cia = fp.num_cia
									)
					LEFT JOIN
						catalogo_contadores
							con
								ON
									(
										con.idcontador = cc.idcontador
									)
					LEFT JOIN
						catalogo_auditores
							aud
								ON
									(
										aud.idauditor = cc.idauditor
									)
				WHERE
					' . implode(' AND ', $conditions1) . '

				UNION

				SELECT
					f.num_cia
						AS
							"#CIA",
					cc.nombre
						AS
							"COMPAÑIA",
					f.num_proveedor
						AS
							"#PRO",
					cp.nombre
						AS
							"PROVEEDOR",
					cp.rfc
						AS
							"RFC",
					f.num_fact::varchar(50)
						AS
							"FACTURA",
					f.fecha
						AS
							"FECHA",
					CASE
						WHEN f.importe != 0 THEN
							ROUND(f.importe::numeric, 2)
						ELSE
							NULL
					END
						AS
							"IMPORTE",
					CASE
						WHEN f.desc1 + f.desc2 + f.desc3 + f.desc4 != 0 THEN
							f.desc1 + f.desc2 + f.desc3 + f.desc4
						ELSE
							NULL
					END
						AS
							"DESCUENTOS",
					CASE
						WHEN f.importe - (f.desc1 + f.desc2 + f.desc3 + f.desc4) != 0 THEN
							ROUND((f.importe - (f.desc1 + f.desc2 + f.desc3 + f.desc4))::numeric, 2)
						ELSE
							NULL
					END
						AS
							"IMPORTE NETO",
					CASE
						WHEN f.iva != 0 THEN
							ROUND(f.iva::numeric, 2)
						ELSE
							NULL
					END
						AS
							"I.V.A.",
					NULL
						AS
							"I.E.P.S.",
					CASE
						WHEN f.ivaret != 0 THEN
							ROUND(ivaret::numeric, 2)
						ELSE
							NULL
					END
						AS
							"I.V.A. RETENIDO",
					CASE
						WHEN f.isr != 0 THEN
							ROUND(isr::numeric, 2)
						ELSE
							NULL
					END
						AS
							"I.S.R. RETENIDO",
					ROUND(f.total::numeric, 2)
						AS
							"TOTAL",
					ec.fecha
						AS
							"PAGADO",
					ec.fecha_con
						AS
							"COBRADO",
					CASE
						WHEN clave = 0 AND f.folio IS NOT NULL THEN
							f.num_cia
						ELSE
							NULL
					END
						AS
							"#CIAPAGO",
					CASE
						WHEN clave = 0 AND f.folio IS NOT NULL THEN
							cc.nombre
						ELSE
							NULL
					END
						AS
							"COMPAÑIA PAGO",
					CASE
						WHEN f.cuenta = 1 THEN
							\'BANORTE\'
						WHEN f.cuenta = 2 THEN
							\'SANTANDER\'
						ELSE
							NULL
					END
						AS
							"BANCO",
					f.folio
						AS
							"CHEQUE"
				FROM
						facturas_zap
							f
					LEFT JOIN
						estado_cuenta
							ec
								USING
									(
										num_cia,
										cuenta,
										folio
									)
					LEFT JOIN
						catalogo_proveedores
							cp
								USING
									(
										num_proveedor
									)
					LEFT JOIN
						catalogo_companias
							cc
								ON
									(
										cc.num_cia = f.num_cia
									)
					LEFT JOIN
						catalogo_contadores
							con
								ON
									(
										con.idcontador = cc.idcontador
									)
					LEFT JOIN
						catalogo_auditores
							aud
								ON
									(
										aud.idauditor = cc.idauditor
									)
				WHERE
					' . implode(' AND ', $conditions2) . '
				ORDER BY
					"#CIA",
					"#PRO",
					"FECHA",
					"FACTURA"
			';

			$result = $db->query($sql);

			if ($result) {
				$data = '';

				$num_cia = NULL;

				foreach ($result as $r) {
					if ($num_cia != $r['#CIA']) {
						if ($num_cia != NULL) {
							$data .= '"","","","","","","TOTALES","' . $importe . '","' . $descuentos . '","' . $importe_neto . '","' . $iva . '","' . $ieps . '","' . $iva_retenido . '","' . $isr_retenido . '","' . $total . '"' . "\n\n";
						}

						$num_cia = $r['#CIA'];

						$data .= '"' . implode('","', array_keys($result[0])) . '"' . "\n";

						$importe = 0;
						$descuentos = 0;
						$importe_neto = 0;
						$iva = 0;
						$ieps = 0;
						$iva_retenido = 0;
						$isr_retenido = 0;
						$total = 0;
					}

					$data .= '"' . implode('","', $r) . '"' . "\n";

					$importe += $r['IMPORTE'];
					$descuentos += $r['DESCUENTOS'];
					$importe_neto += $r['IMPORTE NETO'];
					$iva += $r['I.V.A.'];
					$ieps += $r['I.E.P.S.'];
					$iva_retenido += $r['I.V.A. RETENIDO'];
					$isr_retenido += $r['I.S.R. RETENIDO'];
					$total += $r['IMPORTE'];
				}

				if ($num_cia != NULL) {
					$data .= '"","","","","","","TOTALES","' . $importe . '","' . $descuentos . '","' . $importe_neto . '","' . $iva . '","' . $ieps . '","' . $iva_retenido . '","' . $isr_retenido . '","' . $total . '"' . "\n\n";
				}

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=facturas.csv');

				echo $data;
			}
			else {
				header('location: ArchivoFacturasContadores.php');
			}
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/ArchivoFacturasContadores.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y'));
$tpl->assign(date('n'), ' selected');

$sql = '
	SELECT
		idcontador
			AS
				id,
		nombre_contador
			AS
				nombre
	FROM
		catalogo_contadores
	ORDER BY
		nombre
';
$contadores = $db->query($sql);

foreach ($contadores as $c) {
	$tpl->newBlock('contador');
	$tpl->assign('id', $c['id']);
	$tpl->assign('nombre', $c['nombre']);
}

$sql = '
	SELECT
		idauditor
			AS
				id,
		nombre_auditor
			AS
				nombre
	FROM
		catalogo_auditores
	ORDER BY
		nombre
';
$auditores = $db->query($sql);

foreach ($auditores as $a) {
	$tpl->newBlock('auditor');
	$tpl->assign('id', $a['id']);
	$tpl->assign('nombre', $a['nombre']);
}

$tpl->printToScreen();
?>
