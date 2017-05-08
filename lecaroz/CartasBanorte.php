<?php
include('includes/dbstatus.php');
include('includes/class.db.inc.php');
include('includes/fpdf/fpdf.php');

$db = new DBclass($dsn, 'autocommit=yes');

$sql = '
	SELECT
		num_cia,
		nombre,
		TRIM(regexp_replace(COALESCE(direccion, \'\'), \'\s+\', \' \', \'g\'))
			AS domicilio,
		LPAD(clabe_cuenta::VARCHAR, 11, \'0\')
			AS cuenta,
		folio_ini,
		folio_fin
	FROM
		folios_brincados
		LEFT JOIN catalogo_companias
			USING (num_cia)
	ORDER BY
		num_cia
';

$result = $db->query($sql);

class PDF extends FPDF {
	function Header() {
		$this->Image('imagenes/logo_banorte.jpg', 8, 10, 0, 0, 'JPG');
		
		$this->SetFont('Arial', 'B', 14);
		
		$this->Cell(0, 0, 'MEXICO, D.F., A 31 DE DICIEMBRE DE 2011');
	}
}

$pdf = new PDF('P', 'mm', 'Letter');

$pdf->SetDisplayMode('fullpage', 'continuous');

$pdf->SetMargins(10, 60, 10);

foreach ($result as $rec) {
	$pdf->AddPage('P', 'Letter');
	
	$pdf->SetFont('Arial', 'B', 14);
	
	$pdf->Ln(20);
	
	$pdf->Cell(0, 5, utf8_decode($rec['nombre']));
	
	$pdf->Ln(8);
	
	$pdf->SetFont('Arial', 'I', 12);
	
	$pdf->MultiCell(100, 4, utf8_decode($rec['domicilio']), 0, 'L');
	
	$pdf->SetFont('Arial', '', 14);
	
	$pdf->Ln(15);
	
	$pdf->MultiCell(0, 4, 'POR ESTE MEDIO LE INFORMO QUE DE LA CUENTA ' . $rec['cuenta'] . ' SE LE CANCELARON LOS FOLIOS ' . $rec['folio_ini'] . ' AL ' . $rec['folio_fin'] . ', ESTO EN VIRTUD DE QUE POR FORMATOS OBSOLETOS YA NO PERMITE LA NORMATIVA SU USO.');
	
	$pdf->Ln(10);
	
	$pdf->MultiCell(0, 1, 'EN ESPERA DE SU COMPRESION GRACIAS.');
	
	$pdf->Ln(15);
	
	$pdf->SetFont('Arial', 'B', 14);
	
	$pdf->Cell(0, 1, 'MARILU RODRIGUEZ SANCHEZ');
	$pdf->Ln(5);
	$pdf->Cell(0, 1, 'TITULAR DE SUCURSAL');
	$pdf->Ln(5);
	$pdf->Cell(0, 1, '679 LINDAVISTA');
}

$pdf->Output();

?>