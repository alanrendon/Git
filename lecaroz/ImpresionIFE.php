<?php
include('includes/dbstatus.php');
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('Image/Barcode.php');
include('includes/fpdf/fpdf.php');

$db = new DBclass($dsn, 'autocommit=yes');
//$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'generarPDF':
			class PDF extends FPDF {
				function Header() {
					$this->Image('logo_pb.png', 0, 0, 158);
				}
			}
			
			$pagew = 158;
			$pageh = 198;
			
			$pdf = new PDF('P', 'mm', array($pageh, $pagew));
			
			$pdf->SetDisplayMode('fullpage', 'single');
			
			$pdf->SetMargins(0, 0, 0);
			
			$pdf->AddPage('L', array($pageh, $pagew));
			
			$pdf->Image('imagenes/rebuelta1.png', 0, 0, 86, 54);
			
			$pdf->AddPage('L', array($pageh, $pagew));
			
			$pdf->Image('imagenes/rebuelta2.png', 0, 0, 86, 54);
			
			$pdf->Output();
		break;
	}
}

?>