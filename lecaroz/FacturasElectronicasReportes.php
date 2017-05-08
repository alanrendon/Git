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
	12 => 'DICIEMBRE'
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
		case 'inicio':
			$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasReportesInicio.tpl');
			$tpl->prepare();
			
			$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));
			$tpl->assign(date('n', mktime(0, 0, 0, date('n'), 0, date('Y'))), ' selected');
			
			$sql = '
				SELECT
					idcontador
						AS value,
					nombre_contador
						AS text
				FROM
					catalogo_contadores
				ORDER BY
					text
			';
			
			$contadores = $db->query($sql);
			
			if ($contadores) {
				foreach ($contadores as $c) {
					$tpl->newBlock('contador');
					$tpl->assign('value', $c['value']);
					$tpl->assign('text', $c['text']);
				}
			}
			
			$sql = '
				SELECT
					idauditor
						AS value,
					nombre_auditor
						AS text
				FROM
					catalogo_auditores
				ORDER BY
					text
			';
			
			$auditores = $db->query($sql);
			
			if ($auditores) {
				foreach ($auditores as $a) {
					$tpl->newBlock('auditor');
					$tpl->assign('value', $a['value']);
					$tpl->assign('text', $a['text']);
				}
			}
			
			$tpl->printToScreen();
		break;
		
		case 'reportes':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));
			
			$cias = array();
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
			}
			
			$condiciones_emitidas = array();
			
			$condiciones_emitidas[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			
			$condiciones_emitidas[] = 'consecutivo > 0';
			
			if (count($cias) > 0) {
				$condiciones_emitidas[] = 'cc.rfc IN (
					SELECT
						rfc
					FROM
						catalogo_companias
					WHERE
						num_cia IN (' . implode(', ', $cias) . ')
					GROUP BY
						rfc
				)';
			}
			
			if (isset($_REQUEST['contador']) && $_REQUEST['contador'] > 0) {
				$condiciones_emitidas[] = 'idcontador = ' . $_REQUEST['contador'];
			}
			
			if (isset($_REQUEST['auditor']) && $_REQUEST['auditor'] > 0) {
				$condiciones_emitidas[] = 'idauditor = ' . $_REQUEST['auditor'];
			}
			
			$condiciones_canceladas = array();
			
			$condiciones_canceladas[] = 'tscan::DATE BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			
			$condiciones_canceladas[] = 'consecutivo > 0';
			
			if (count($cias) > 0) {
				$condiciones_canceladas[] = 'cc.rfc IN (
					SELECT
						rfc
					FROM
						catalogo_companias
					WHERE
						num_cia IN (' . implode(', ', $cias) . ')
					GROUP BY
						rfc
				)';
			}
			
			if (isset($_REQUEST['contador']) && $_REQUEST['contador'] > 0) {
				$condiciones_canceladas[] = 'idcontador = ' . $_REQUEST['contador'];
			}
			
			if (isset($_REQUEST['auditor']) && $_REQUEST['auditor'] > 0) {
				$condiciones_canceladas[] = 'idauditor = ' . $_REQUEST['auditor'];
			}
			
			$sql = '
				SELECT
					nombre_contador
						AS contador,
					num_cia,
					cc.rfc,
					(
						SELECT
							serie
						FROM
							facturas_electronicas_series
						WHERE
							num_cia = fe.num_cia
							AND tipo_serie = fe.tipo_serie
							AND fe.consecutivo BETWEEN folio_inicial AND folio_final
					)
						AS serie,
					consecutivo
						AS folio,
					1
						AS status,
					\'|\' || fe.rfc || \'|\' || (
						SELECT
							serie
						FROM
							facturas_electronicas_series
						WHERE
							num_cia = fe.num_cia
							AND tipo_serie = fe.tipo_serie
							AND fe.consecutivo BETWEEN folio_inicial AND folio_final
					) || \'|\' || consecutivo || \'|\' || (
						SELECT
							anio_aprobacion
						FROM
							facturas_electronicas_series
						WHERE
							num_cia = fe.num_cia
							AND tipo_serie = fe.tipo_serie
							AND fe.consecutivo BETWEEN folio_inicial AND folio_final
					) || (
						SELECT
							no_aprobacion
						FROM
							facturas_electronicas_series
						WHERE
							num_cia = fe.num_cia
							AND tipo_serie = fe.tipo_serie
							AND fe.consecutivo BETWEEN folio_inicial AND folio_final
					) || \'|\' || fecha || \' \' || hora || \'|\' || ROUND(total::numeric, 2) || \'|\' || ROUND(iva::numeric, 2) || \'|1|I||||\'
						AS registro
				FROM
					facturas_electronicas fe
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_contadores con
						USING (idcontador)
				WHERE
					' . implode(' AND ', $condiciones_emitidas) . '
				
				UNION
				
				SELECT
					nombre_contador
						AS contador,
					num_cia,
					cc.rfc,
					(
						SELECT
							serie
						FROM
							facturas_electronicas_series
						WHERE
							num_cia = fe.num_cia
							AND tipo_serie = fe.tipo_serie
							AND fe.consecutivo BETWEEN folio_inicial AND folio_final
					)
						AS serie,
					consecutivo
						AS folio,
					0
						AS status,
					\'|\' || fe.rfc || \'|\' || (
						SELECT
							serie
						FROM
							facturas_electronicas_series
						WHERE
							num_cia = fe.num_cia
							AND tipo_serie = fe.tipo_serie
							AND fe.consecutivo BETWEEN folio_inicial AND folio_final
					) || \'|\' || consecutivo || \'|\' || (
						SELECT
							anio_aprobacion
						FROM
							facturas_electronicas_series
						WHERE
							num_cia = fe.num_cia
							AND tipo_serie = fe.tipo_serie
							AND fe.consecutivo BETWEEN folio_inicial AND folio_final
					) || (
						SELECT
							no_aprobacion
						FROM
							facturas_electronicas_series
						WHERE
							num_cia = fe.num_cia
							AND tipo_serie = fe.tipo_serie
							AND fe.consecutivo BETWEEN folio_inicial AND folio_final
					) || \'|\' || fecha || \' \' || hora || \'|\' || ROUND(total::numeric, 2) || \'|\' || ROUND(iva::numeric, 2) || \'|0|I||||\'
						AS registro
				FROM
					facturas_electronicas fe
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_contadores con
						USING (idcontador)
				WHERE
					' . implode(' AND ', $condiciones_canceladas) . '
				
				ORDER BY
					contador,
					rfc,
					serie,
					folio,
					status DESC
			';
			
			$facturas = $db->query($sql);
			
			$condiciones_rezagadas = array();
			
			$condiciones_rezagadas[] = 'tsins::DATE BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			
			$condiciones_rezagadas[] = 'consecutivo > 0';
			
			$condiciones_rezagadas[] = 'fecha < \'' . $fecha1 . '\'';
			
			if (count($cias) > 0) {
				$condiciones_rezagadas[] = 'cc.rfc IN (
					SELECT
						rfc
					FROM
						catalogo_companias
					WHERE
						num_cia IN (' . implode(', ', $cias) . ')
					GROUP BY
						rfc
				)';
			}
			
			if (isset($_REQUEST['contador']) && $_REQUEST['contador'] > 0) {
				$condiciones_rezagadas[] = 'idcontador = ' . $_REQUEST['contador'];
			}
			
			$sql = '
				SELECT
					nombre_contador
						AS contador,
					num_cia,
					cc.rfc,
					(
						SELECT
							serie
						FROM
							facturas_electronicas_series
						WHERE
							num_cia = fe.num_cia
							AND tipo_serie = fe.tipo_serie
							AND fe.consecutivo BETWEEN folio_inicial AND folio_final
					)
						AS serie,
					consecutivo
						AS folio,
					1
						AS status,
					\'|\' || fe.rfc || \'|\' || (
						SELECT
							serie
						FROM
							facturas_electronicas_series
						WHERE
							num_cia = fe.num_cia
							AND tipo_serie = fe.tipo_serie
							AND fe.consecutivo BETWEEN folio_inicial AND folio_final
					) || \'|\' || consecutivo || \'|\' || (
						SELECT
							anio_aprobacion
						FROM
							facturas_electronicas_series
						WHERE
							num_cia = fe.num_cia
							AND tipo_serie = fe.tipo_serie
							AND fe.consecutivo BETWEEN folio_inicial AND folio_final
					) || (
						SELECT
							no_aprobacion
						FROM
							facturas_electronicas_series
						WHERE
							num_cia = fe.num_cia
							AND tipo_serie = fe.tipo_serie
							AND fe.consecutivo BETWEEN folio_inicial AND folio_final
					) || \'|\' || fecha || \' \' || hora || \'|\' || ROUND(total::numeric, 2) || \'|\' || ROUND(iva::numeric, 2) || \'|1|I||||\'
						AS registro
				FROM
					facturas_electronicas fe
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_contadores con
						USING (idcontador)
				WHERE
					' . implode(' AND ', $condiciones_rezagadas) . '
				ORDER BY
					contador,
					rfc,
					serie,
					folio
			';
			
			$rezagadas = $db->query($sql);
			
			if ($facturas || $rezagadas) {
				$path = 'facturas/reportes/';
				
				if ($facturas) {
					
					$reportes = array();
					
					$rfc = NULL;
					
					foreach ($facturas as $fac) {
						if ($rfc != $fac['rfc']) {
							if ($rfc != NULL && $status > 0) {
								fwrite($fp, $data);
								
								fclose($fp);
							}
							
							$rfc = $fac['rfc'];
							
							$status = 1;
							
							$file_name = '1' . $rfc . str_pad($_REQUEST['mes'], 2, '0', STR_PAD_LEFT) . $_REQUEST['anio'] . '.txt';
							
							if ($rfc == '') {
								$status = -1;
							}
							else if (!($fp = @fopen($path . $file_name, 'wb+'))) {
								$status = -2;
							}
							else {
								$reportes[$fac['contador'] . ' ' . $_meses[$_REQUEST['mes']] . ' ' . $_REQUEST['anio']][] = $file_name;
								
								$status = 1;
								
								$data = '';
							}
						}
						
						if ($status > 0) {
							$data .= $fac['registro'] . "\r\n";
						}
					}
					
					if ($rfc != NULL && $status > 0) {
						fwrite($fp, $data);
						
						fclose($fp);
					}
					
					if (count($reportes) > 0) {
						
						$zip = new ZipArchive;
						
						foreach ($reportes as $contador => $archivos) {
							$zip_file_name = $contador . '.zip';
							
							@unlink($path . $zip_file_name);
							
							if (($zip_error = $zip->open($path . $zip_file_name, ZipArchive::CREATE)) === TRUE) {
								
								foreach ($archivos as $archivo) {
									$zip->addFile($path . $archivo, $archivo);
								}
								
								$zip->close();
							}
							else {
								echo $zip_error;
							}
						}
						
					}
					
					$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasReportesResultado.tpl');
					$tpl->prepare();
					
					if (count($reportes) > 0) {
						foreach ($reportes as $contador => $archivos) {
							$tpl->newBlock('reporte');
							
							$tpl->assign('nombre_reporte', $contador);
							$tpl->assign('link_reporte', $path . $contador . '.zip');
						}
					}
					
					echo $tpl->getOutputContent();
				}
				
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasReportes.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
