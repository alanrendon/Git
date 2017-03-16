<?php
include('includes/class.db.inc.php');
include('includes/dbstatus.php');

$pdf_path = 'facturas/comprobantes_pdf/';
$xml_path = 'facturas/comprobantes_xml/';
$zip_path = 'facturas/';

$db = new DBclass($dsn, 'autocommit=yes');

if (isset($_REQUEST['id'])) {
	$ids = array();
	
	if (isset($_REQUEST['renta'])) {
		$sql = '
			SELECT
				idcfd
			FROM
				rentas_recibos
			WHERE
				idreciborenta IN (' . implode(', ', $_REQUEST['id']) . ')
		';
		
		$result = $db->query($sql);
		
		foreach ($result as $rec) {
			$ids[] = $rec['idcfd'];
		}
	} else {
		$ids = $_REQUEST['id'];
	}
	
	$sql = '
		SELECT
			num_cia,
			num_cia || \'-\' || COALESCE((
				SELECT
					serie
				FROM
					facturas_electronicas_series
				WHERE
						num_cia = fe.num_cia
					AND
						tipo_serie = fe.tipo_serie
					AND
						fe.consecutivo BETWEEN folio_inicial AND folio_final
			), \'\') || consecutivo || \'.pdf\'
				AS
					filename_pdf,
			num_cia || \'-\' || COALESCE((
				SELECT
					serie
				FROM
					facturas_electronicas_series
				WHERE
						num_cia = fe.num_cia
					AND
						tipo_serie = fe.tipo_serie
					AND
						fe.consecutivo BETWEEN folio_inicial AND folio_final
			), \'\') || consecutivo || \'.xml\'
				AS
					filename_xml,
			(
				SELECT
					tipo_factura
				FROM
					facturas_electronicas_series
				WHERE
						num_cia = fe.num_cia
					AND
						tipo_serie = fe.tipo_serie
					AND
						fe.consecutivo BETWEEN folio_inicial AND folio_final
			)
				AS
					tipo_factura
		FROM
			facturas_electronicas fe
		WHERE
			id IN (' . implode(', ', $ids) . ')
	';
	
	$result = $db->query($sql);
} else if (isset($_REQUEST['filename'])) {
	$pieces = explode('-', $_REQUEST['filename']);
	
	$folio = preg_replace("/\D/", '', $pieces[1]);
	
	$serie = preg_replace("/[^A-Z]/", '', $pieces[1]);
	
	$sql = '
		SELECT
			tipo_factura
		FROM
			facturas_electronicas_series
		WHERE
				num_cia = ' . $pieces[0] . '
			AND
				serie = \'' . $serie . '\'
			AND
				' . $folio . ' BETWEEN folio_inicial AND folio_final
	';
	$tipo = $db->query($sql);
	
	$result[0]['num_cia'] = $pieces[0];
	$result[0]['filename_pdf'] = $_REQUEST['filename'] . '.pdf';
	$result[0]['filename_xml'] = $_REQUEST['filename'] . '.xml';
	$result[0]['tipo_factura'] = $tipo[0]['tipo_factura'];
}

$zip = new ZipArchive;

$filename_zip = 'facturas' . time() . '.zip';

$files_error = array();

if (($zip_error = $zip->open($zip_path . $filename_zip, ZipArchive::CREATE)) === TRUE) {
	foreach ($result as $rec) {
		if (file_exists($pdf_path . $rec['num_cia'] . '/' . utf8_encode($rec['filename_pdf']))) {
			$zip->addFile($pdf_path . $rec['num_cia'] . '/' . utf8_encode($rec['filename_pdf']), utf8_encode($rec['filename_pdf']));
		}
		else {
			$files_error[] = $rec['filename_pdf'];
		}
		
		if ($rec['tipo_factura'] == 1) {
			if (file_exists($xml_path . $rec['num_cia'] . '/' . utf8_encode($rec['filename_xml']))) {
				$zip->addFile($xml_path . $rec['num_cia'] . '/' . utf8_encode($rec['filename_xml']), utf8_encode($rec['filename_xml']));
			}
			else {
				$files_error[] = $rec['filename_xml'];
			}
		}
	}
	
	if ($files_error) {
		$error_string = 'Los siguientes archivos no pudieron ser descargados:' . "\r\n\r\n";
		$error_string .= implode("\r\n", $files_error);
		
		if ($fp = @fopen($zip_path . '/errores.txt', 'wb+')) {
			fwrite($fp, $error_string);
			
			fclose($fp);
			
			$zip->addFile($zip_path . '/errores.txt', 'errores.txt');
		}
	}
	
	$zip->close();
	
	header('Content-type: application/zip');
	header('Content-Disposition: attachment; filename="' . $filename_zip . '"');
	
	readfile($zip_path . $filename_zip);
	
	unlink($zip_path . $filename_zip);
}
else {
	echo $zip_error;
}

?>