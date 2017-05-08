<?php
require_once('includes/fpdf/fpdf.php');
require_once('includes/fpdi/fpdi.php');



$pdf = new FPDI(); 

$pagecount = $pdf->setSourceFile('at_ST-7.pdf'); 

$tplidx = $pdf->importPage(1, '/MediaBox'); 

$pdf->addPage();
$pdf->useTemplate($tplidx); 

$pdf->SetFont('Arial', '', 20);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetXY(90, 160);

$pdf->Write(0, 'Hello World');

$tplidx = $pdf->importPage(2, '/MediaBox'); 

$pdf->addPage();
$pdf->useTemplate($tplidx);

$pdf->SetFont('Arial', '', 20);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetXY(90, 160);

$pdf->Write(0, 'Hello World');

$pdf->Output('newpdf.pdf', 'I');
