<?php
require_once('includes/fpdf/fpdf.php');
require_once('includes/cheques.inc.php');

class PDF extends FPDF {
	function Header() {
		for ($f = 0; $f < 2; $f++) {
			$this->Rect(6, 8 + $f * 130, 203, 130, 'D');
			
			$this->Rect(8, 21 + $f * 130, 199, 66, 'D');
			
			for ($i = 0; $i < 10; $i++) {
				$this->Line(8, 26 + $i * 6 + $f * 130, 207, 26 + $i * 6 + $f * 130);
			}
			
			$this->SetFont('ARIAL', 'B', 10);
			
			$this->SetXY(6, 1 + $f * 130);
			$this->Cell(199, 20, 'COMPAÑIA (ALIAS)', 0, 0, 'C');
			$this->Ln(4);
			$this->SetX(6);
			$this->Cell(199, 20, 'SEMANA: X', 0, 0, 'C');
			$this->Ln(4);
			$this->SetX(6);
			$this->Cell(199, 20, 'FECHA: X', 0, 0, 'C');
			
			$this->SetFont('Arial', 'B', 8);
			
			$this->SetXY(8, 21 + $f * 130);
			$this->Cell(199, 6, 'OBSERVACIONES DEL ENCARGADO', 0, 0, 'C');
			
			$this->SetXY(8, 81 + $f * 130);
			$this->Cell(199, 6, 'Sr. Encargado para poder agilizar las bajas de los trabajadores FAVOR DE MARCAR ESTE NUM. DE TELEFONO. 5276-6579', 0, 0, 'C');
			
			$this->SetFont('ARIAL', 'B', 10);
			
			$this->SetXY(8, 89 + $f * 130);
			$this->MultiCell(199, 4, '¡¡IMPORTANTE!! FAVOR DE NO RAYAR, TACHAR O PONER CORRECTOR EN LA NOMINA, LAS BAJAS E INCAPACIDADES LAS DEBE PONER EN ESTE FORMATO Y NO EN EL REPORTE.', 0, 'C');
			
			$this->SetXY(12, 100 + $f * 130);
			$this->MultiCell(85, 4, 'NOTA 1: SR. ENCARGADO TIENE USTED UNA SEMANA PARA REGRESAR COMPLETAMENTE FIRMADA Y REVISADA LA NOMINA ANTES DE MANDARLA A LA OFICINA.', 1, 'L');
			
			$this->SetXY(12, 120 + $f * 130);
			$this->MultiCell(85, 4, 'NOTA 2: EN CASO DE FALTAR ALGUNA FIRMA EN LA NOMINA SE LE REGRESARA, TENIENDO QUE DEVOLVERLA LO MAS PRONTO POSIBLE A LA OFICINA PARA SU REVISION.', 1, 'L');
			
			$this->SetFont('ARIAL', 'B', 8);
			
			$this->Line(120, 115 + $f * 130, 200, 115 + $f * 130);
			$this->SetXY(120, 115 + $f * 130);
			$this->Cell(80, 6, 'NOMBRE Y FIRMA', 0, 0, 'C');
			
			$this->Line(120, 130 + $f * 130, 200, 130 + $f * 130);
			$this->SetXY(120, 130 + $f * 130);
			$this->Cell(80, 6, 'PUESTO', 0, 0, 'C');
		}
	}
}

$pdf = new PDF('P', 'mm', array(216, 340));

$pdf->AliasNbPages();

$pdf->SetDisplayMode('fullpage', 'single');

$pdf->SetMargins(0, 5, 12);

$pdf->SetAutoPageBreak(FALSE);

$pdf->AddPage('P', 'Letter');



$pdf->Output();
?>
