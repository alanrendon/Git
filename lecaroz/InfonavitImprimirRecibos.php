<?php
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';
include 'includes/dbstatus.php';

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

function toInt($value) {
	return intval($value, 10);
}

$_meses = array(
	1  => 'Enero',
	2  => 'Febrero',
	3  => 'Marzo',
	4  => 'Abril',
	5  => 'Mayo',
	6  => 'Junio',
	7  => 'Julio',
	8  => 'Agosto',
	9  => 'Septiembre',
	10 => 'Octubre',
	11 => 'Noviembre',
	12 => 'Diciembre'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

$path = 'infonavit/';

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'generar':
			$condiciones = array();
			
			if ($_SESSION['iduser'] != 1) {
				$condiciones[] = 'ct.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
			}
			
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
					$condiciones[] = 'ct.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}
			
			if (isset($_REQUEST['folios']) && trim($_REQUEST['folios']) != '') {
				$folios = array();
				
				$pieces = explode(',', $_REQUEST['folios']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$folios[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$folios[] = $piece;
					}
				}
				
				if (count($folios) > 0) {
					$condiciones[] = 'inf.folio IN (' . implode(', ', $folios) . ')';
				}
			}
			
			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') || (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') && (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
					$condiciones[] = 'inf.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				}
				else if (isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') {
					$condiciones[] = 'inf.fecha = \'' . $_REQUEST['fecha1'] . '\'';
				}
				else if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '') {
					$condiciones[] = 'inf.fecha >= \'' . $_REQUEST['fecha2'] . '\'';
				}
			}
			
			if (isset($_REQUEST['anio']) && $_REQUEST['anio'] > 0) {
				$condiciones[] = 'inf.anio = ' . $_REQUEST['anio'];
			}
			
			if (isset($_REQUEST['mes']) && $_REQUEST['mes'] > 0) {
				$condiciones[] = 'inf.mes = ' . $_REQUEST['mes'];
			}
			
			if (!isset($_REQUEST['cias'])
				&& !isset($_REQUEST['admin'])
				&& !isset($_REQUEST['folios'])
				&& !isset($_REQUEST['fecha1'])
				&& !isset($_REQUEST['fecha2'])
				&& !isset($_REQUEST['anio'])
				&& !isset($_REQUEST['mes'])) {
				$condiciones[] = 'inf.ultimo = TRUE';
			}
			
			if ($_REQUEST['tipo'] == 'print') {
				$order = 'folio';
			}
			else if ($_REQUEST['tipo'] == 'email') {
				$order = 'num_cia, folio';
			}
			
			$sql = '
				SELECT
					inf.id,
					inf.folio,
					ct.num_cia,
					cc.nombre
						AS nombre_cia,
					cc.email
						AS email_cia,
					ca.email
						AS email_admin,
					ct.nombre_completo
						AS nombre_trabajador,
					inf.anio,
					inf.mes,
					importe,
					inf.fecha
				FROM
					infonavit inf
					LEFT JOIN catalogo_trabajadores ct
						ON (ct.id = inf.id_emp)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_administradores ca
						USING (idadministrador)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					' . $order . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				include_once('includes/fpdf/fpdf.php');
				include_once('includes/cheques.inc.php');
				
				if (!isset($_REQUEST['cias'])
					&& !isset($_REQUEST['admin'])
					&& !isset($_REQUEST['folios'])
					&& !isset($_REQUEST['fecha1'])
					&& !isset($_REQUEST['fecha2'])
					&& !isset($_REQUEST['anio'])
					&& !isset($_REQUEST['mes'])) {
					$sql = '
						UPDATE
							infonavit
						SET
							ultimo = FALSE
						WHERE
							ultimo = TRUE
					';
					
					$db->query($sql);
				}
				
				if ($_REQUEST['tipo'] == 'print') {
					$pdf = new FPDF('P', 'mm', 'Letter');
					
					$pdf->SetDisplayMode('fullpage', 'continuous');
			
					$pdf->SetMargins(0, 0, 0);
					
					$pdf->SetAutoPageBreak(FALSE);
					
					$pdf->SetFont('Arial', '', 12);
					
					foreach ($result as $i => $rec) {
						if ($i % 2 == 0) {
							$pdf->AddPage('P', 'Letter');
							
							$pdf->Line(6, 140, 209, 140);
							
							$yoffset = 0;
						}
						
						$pdf->Image('imagenes/escudo_lecaroz.jpg', 6, $yoffset + 5, 25);
						
						$pdf->SetFont('Arial', 'B', 14);
						
						$pdf->SetXY(35, $yoffset + 10);
						$pdf->MultiCell(150, 4, $rec['nombre_cia'], 0, 'C');
						
						$pdf->SetFontSize(12);
						
						$pdf->SetXY(190, $yoffset + 10);
						$pdf->MultiCell(20, 0, str_pad($rec['folio'], 6, '0', STR_PAD_LEFT), 2, 'C');
						
						list($dia, $mes, $anio) = array_map('toInt', explode('/', $rec['fecha']));
						
						$pdf->SetXY(100, $yoffset + 40);
						$pdf->Cell(80, 1, utf8_decode('México D.F., a ') . $dia . ' de ' . $_meses[$mes] . ' de ' . $anio, 0, 0, 'R');
						
						$text = utf8_decode('Recibí de ') . $rec['nombre_trabajador'] . ' la cantidad de ' . number_format($rec['importe'], 2) . ' PESOS (' . num2string($rec['importe']) . ') correspondiente al pago del mes de ' . strtoupper($_meses[$rec['mes']]) . ' de ' . $rec['anio'] . ' por el concepto de PAGO DE INFONAVIT.';
						
						$pdf->SetFont('Arial', '', 12);
						
						$pdf->SetXY(35,$yoffset +  65);
						$pdf->MultiCell(150, 4, $text, 0, 'J');
						
						$pdf->SetFont('Arial', 'B', 12);
						
						$pdf->SetXY(100, $yoffset + 115);
						$pdf->MultiCell(80, 4, $rec['nombre_cia'], 'T', 'C');
						
						$yoffset += 140;
					}
					
					$filename = 'infonavit' . date('YmdHisu') . '.pdf';
					
					$pdf->Output($path . $filename, 'F');
					
					echo $filename;
				}
				else if ($_REQUEST['tipo'] == 'email') {
					$num_cia = NULL;
					
					$json_data = array();
					
					foreach ($result as $rec) {
						if ($num_cia != $rec['num_cia']) {
							if ($num_cia != NULL) {
								$pdf->Output($path . $filename, 'F');
							}
							
							$num_cia = $rec['num_cia'];
							
							$email = $rec['email_cia'];
							
							$filename = 'infonavit' . date('YmdHis') . mt_rand(1000, 9999) . '.pdf';
							
							$json_data[] = array(
								'num_cia'     => $num_cia,
								'email_cia'   => $rec['email_cia'],
								'email_admin' => $rec['email_admin'],
								'filename'    => $filename
							);
							
							$pdf = new FPDF('P', 'mm', 'Letter');
							
							$pdf->SetDisplayMode('fullpage', 'continuous');
					
							$pdf->SetMargins(0, 0, 0);
							
							$pdf->SetAutoPageBreak(FALSE);
							
							$pdf->SetFont('Arial', '', 12);
							
							$cont = 0;
						}
						
						if ($cont % 2 == 0) {
							$pdf->AddPage('P', 'Letter');
							
							$pdf->Line(6, 140, 209, 140);
							
							$yoffset = 0;
						}
						
						$pdf->Image('imagenes/escudo_lecaroz.jpg', 6, $yoffset + 5, 25);
						
						$pdf->Image('imagenes/firma_recibos_infonavit.jpg', 110, $yoffset + 85);
						
						$pdf->SetFont('Arial', 'B', 14);
						
						$pdf->SetXY(35, $yoffset + 10);
						$pdf->MultiCell(150, 4, $rec['nombre_cia'], 0, 'C');
						
						$pdf->SetFontSize(12);
						
						$pdf->SetXY(190, $yoffset + 10);
						$pdf->MultiCell(20, 0, str_pad($rec['folio'], 6, '0', STR_PAD_LEFT), 2, 'C');
						
						$pdf->SetXY(100, $yoffset + 40);
						$pdf->Cell(80, 1, utf8_decode('México D.F., a ') . date('j') . ' de ' . $_meses[date('n')] . ' de ' . date('Y'), 0, 0, 'R');
						
						$text = utf8_decode('Recibí de ') . $rec['nombre_trabajador'] . ' la cantidad de ' . number_format($rec['importe'], 2) . ' PESOS (' . num2string($rec['importe']) . ') correspondiente al pago del mes de ' . strtoupper($_meses[$rec['mes']]) . ' de ' . $rec['anio'] . ' por el concepto de PAGO DE INFONAVIT.';
						
						$pdf->SetFont('Arial', '', 12);
						
						$pdf->SetXY(35,$yoffset +  65);
						$pdf->MultiCell(150, 4, $text, 0, 'J');
						
						$pdf->SetFont('Arial', 'B', 12);
						
						$pdf->SetXY(100, $yoffset + 115);
						$pdf->MultiCell(80, 4, $rec['nombre_cia'], 'T', 'C');
						
						$yoffset += 140;
						
						$cont++;
					}
					
					if ($num_cia != NULL) {
						$pdf->Output($path . $filename, 'F');
					}
					
					echo json_encode($json_data);
				}
			}
		break;
		
		case 'imprimir':
			header('Content-type: application/pdf');
			header('Content-Disposition: inline; filename="' . $_REQUEST['filename'] . '"');
			
			readfile($path . $_REQUEST['filename']);
			
			unlink($path . $_REQUEST['filename']);
		break;
		
		case 'email':
			if (!class_exists('PHPMailer')) {
				include_once('includes/phpmailer/class.phpmailer.php');
			}
			
			$data = json_decode($_REQUEST['data']);
			
			foreach ($data as $d) {
				$mail = new PHPMailer();
				
				if ($d->num_cia >= 900) {
					$mail->IsSMTP();
					$mail->Host = 'mail.zapateriaselite.com';
					$mail->Port = 587;
					$mail->SMTPAuth = true;
					$mail->Username = 'facturas.electronicas@zapateriaselite.com';
					$mail->Password = 'facturaselectronicas';
					
					$mail->From = 'facturas.electronicas@zapateriaselite.com';
					$mail->FromName = utf8_decode('Oficinas Elite');
				}
				else {
					$mail->IsSMTP();
					$mail->Host = 'mail.lecaroz.com';
					$mail->Port = 587;
					$mail->SMTPAuth = true;
					$mail->Username = 'mollendo@lecaroz.com';
					$mail->Password = 'L3c4r0z*';
					
					$mail->From = 'mollendo@lecaroz.com';
					$mail->FromName = utf8_decode('Oficinas Administrativas Mollendo, S.A de R.L.');
				}
				
				if (trim($d->email_cia) != '') {
					$mail->AddAddress($d->email_cia);
				}
				
				if (trim($d->email_admin) != '') {
					$mail->AddAddress($d->email_admin);
				}
				
				if ($d->num_cia < 900) {
					$mail->AddAddress('infonavit@lecaroz.com');
				}
				
				$mail->Subject = 'Recibos de Infonavit [' . date('d/m/Y H:i:s') . ']';
				
				$tpl = new TemplatePower('plantillas/nom/email_recibo_infonavit.tpl');
				$tpl->prepare();
				
				$tpl->assign('fecha', date('d/m/Y H:i:s'));
				
				$tpl->assign('email_ayuda', $d->num_cia >= 900 ? 'elite@zapateriaselite.com' : 'beatriz.flores@lecaroz.com');
				
				$mail->Body = $tpl->getOutputContent();
				
				$mail->IsHTML(true);
				
				$mail->AddAttachment($path . $d->filename);
				
				if(!$mail->Send()) {
					echo $mail->ErrorInfo;
				}
			}
			
			foreach ($data as $d) {
				unlink($path . $d->filename);
			}
			
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/nom/InfonavitImprimirRecibos.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$sql = '
	SELECT
		idadministrador
			AS value,
		nombre_administrador
			AS text
	FROM
		catalogo_administradores
	ORDER BY
		text
';

$admins = $db->query($sql);

if ($admins) {
	foreach ($admins as $a) {
		$tpl->newBlock('admin');
		$tpl->assign('value', $a['value']);
		$tpl->assign('text', utf8_encode($a['text']));
	}
}

$tpl->printToScreen();
?>
