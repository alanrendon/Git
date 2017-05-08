<?php
include('includes/class.db.inc.php');
include('includes/dbstatus.php');

$pdf_path = 'cfds_proveedores/';
$xml_path = 'cfds_proveedores/';
$zip_path = 'cfds_proveedores/zips/';

$db = new DBclass($dsn, 'autocommit=yes');

$sql = '
	SELECT
		f.num_proveedor
			AS num_pro,
		cp.rfc,
		f.xml_file,
		f.pdf_file
	FROM
		facturas_zap f
		LEFT JOIN catalogo_proveedores cp
			USING (num_proveedor)
	WHERE
		f.id = ' . $_REQUEST['id'] . '
';

$result = $db->query($sql);

$row = $result[0];

$zip = new ZipArchive;

$filename_zip = 'cfd_zap_' . mb_strtolower($row['rfc']) . '_' . $_REQUEST['id'] . '.zip';

$files_error = array();

if (($zip_error = $zip->open($zip_path . $filename_zip, ZipArchive::CREATE)) === TRUE) {
	if (file_exists($pdf_path . utf8_encode($row['pdf_file']))) {
		$zip->addFile($pdf_path . utf8_encode($row['pdf_file']), utf8_encode($row['pdf_file']));
	}
	else {
		$files_error[] = $row['pdf_file'];
	}

	if (file_exists($xml_path . utf8_encode($row['xml_file']))) {
		$zip->addFile($xml_path . utf8_encode($row['xml_file']), utf8_encode($row['xml_file']));
	}
	else {
		$files_error[] = $row['xml_file'];
	}
	
	if ($files_error) {
		$error_string = 'Los siguientes archivos no pudieron ser descargados:' . "\r\n\r\n";
		$error_string .= implode("\r\n", $files_error);
		
		if ($fp = @fopen($zip_path . 'errores.txt', 'wb+')) {
			fwrite($fp, $error_string);
			
			fclose($fp);
			
			$zip->addFile($zip_path . 'errores.txt', 'errores.txt');
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