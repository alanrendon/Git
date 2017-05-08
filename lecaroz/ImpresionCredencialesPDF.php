<?php
include('includes/dbstatus.php');
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include_once('Image/Barcode.php');
include('includes/fpdf/fpdf.php');

$db = new DBclass($dsn, 'autocommit=yes');
//$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'generarPDF':
			class PDF extends FPDF {
				function Header() {
					
				}
			}
			
			$pagew = 86;
			$pageh = 54;
			
			$pdf = new PDF('L', 'mm', array($pageh, $pagew));
			
			for ($folio = $_REQUEST['folio_inicial']; $folio <= $_REQUEST['folio_final']; $folio++) {
				$pdf->SetDisplayMode('real', 'two');
				
				$pdf->SetMargins(0, 0, 0);
				
				$pdf->AddPage('L', array($pageh, $pagew));
				
				$pdf->Image('imagenes/credencial_fondo.png', 0, 0, 86, 54);
				
				$pdf->SetFont('Arial', 'B', 14);
				$string = utf8_decode('SOCIO DE CATÁLOGO');
				$length = $pdf->GetStringWidth($string);
				$pdf->Text(($pagew - $length) / 2, 27, $string);
				
				$pdf->SetFont('Arial', 'B', 8);
				$string = utf8_decode('SOCIO NO.: ' . str_pad($folio, 4, '0', STR_PAD_LEFT));
				$length = $pdf->GetStringWidth($string);
				$pdf->Text(60, 52, $string);
				
				$pdf->AddPage('L', array($pageh, $pagew));
				
				$barcode = Image_Barcode::draw(str_pad($folio, 4, '0', STR_PAD_LEFT), 'code128', 'png', FALSE);
				imagepng($barcode, 'barcodes/barcode' . str_pad($folio, 4, '0', STR_PAD_LEFT) . '.png');
				imagedestroy($barcode);
				
				$pdf->Image('barcodes/barcode' . str_pad($folio, 4, '0', STR_PAD_LEFT) . '.png', 28, 8, 30, 20);
				
				$pdf->Image('imagenes/firma.png', 36, 30, 15, 15);
				
				$pdf->SetFont('Arial', 'B', 8);
				$string = utf8_decode('AUTORIZACIÓN');
				$length = $pdf->GetStringWidth($string);
				$pdf->Text(($pagew - $length) / 2, 48, $string);
				
				$pdf->SetFont('Arial', 'B', 5);
				$string = utf8_decode('CREDENCIAL PERSONAL E INTRANSFERIBLE');
				$length = $pdf->GetStringWidth($string);
				$pdf->Text(($pagew - $length) / 2, 52, $string);
			}
			
			$pdf->Output();
		break;
	}
}

?>