<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value)
{
	return intval($value, 10);
}

function toNumberFormat($value)
{
	return number_format($value, 2);
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

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{

		case 'generar_cartas':
			$condiciones = array();

			$condiciones[] = "f.fecha BETWEEN '01/09/{$_REQUEST['anio']}'::DATE AND '31/12/{$_REQUEST['anio']}'::DATE";

			if (isset($_REQUEST['pros']) && trim($_REQUEST['pros']) != '')
			{
				$pros = array();

				$pieces = explode(',', $_REQUEST['pros']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$pros[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$pros[] = $piece;
					}
				}

				if (count($pros) > 0)
				{
					$condiciones[] = 'f.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
			}

			$condiciones_string = implode(' AND ', $condiciones);

			$result = $db->query("SELECT
				f.num_proveedor AS num_pro,
				cp.nombre AS nombre_pro,
				cp.email1 AS email_pro
			FROM
				facturas f
				LEFT JOIN catalogo_proveedores cp USING (num_proveedor)
			WHERE
				{$condiciones_string}
			GROUP BY
				f.num_proveedor,
				cp.nombre,
				cp.email1
			ORDER BY
				f.num_proveedor");

			if ( ! $result)
			{
				echo "NO HAY RESULTADOS";

				die;
			}

			if ($result)
			{
				if ( ! class_exists('FPDF'))
				{
					include_once(dirname(__FILE__) . '/includes/fpdf/fpdf.php');
				}

				class PDF extends FPDF
				{
					function Header()
					{
						$this->SetMargins(10, 10, 10);

						$this->SetTextColor(0, 0, 0);

						$this->SetFont('ARIAL', 'B', 12);

						$this->Ln(30);

						$this->Cell(0, 5, utf8_decode('Ciudad de México, a ' . date('j') . ' de ' . $GLOBALS['_meses'][date('n')]) . ' de ' . date('Y'), 0, 1, 'R');

						$this->Ln(30);

						$this->Cell(0, 5, utf8_decode(""), 0, 1);

						$this->Ln(10);

						$this->Cell(0, 5, utf8_decode("PRESENTE"), 0, 1);

						$this->Ln(10);

						$this->SetFont('ARIAL', '', 12);

						$this->Cell(0, 5, utf8_decode("Estimado proveedor,"), 0, 0);

						$this->Ln(10);

						$this->MultiCell(0, 6, utf8_decode("Con motivo de nuestro cierre contable del año 2015, mucho agradeceremos nos puedan enviar un estado de cuenta cortado al 31 de diciembre donde se muestre de forma detallada las facturas que nuestra empresa les adeuda a dicha fecha.\n\nAgradeceremos que la información sea enviada a la dirección de correo electronico: yolanda.arreola@lecaroz.com"));

						$this->Ln(30);

						$this->Cell(0, 5, utf8_decode("Atentamente,"), 0, 0, 'C');

						$this->Ln(30);

						$this->SetFont('ARIAL', 'B', 12);

						$this->Image(dirname(__FILE__) . '/imagenes/firma-carta-adeudo-proveedor.png', 80, 190);

						$this->Cell(0, 5, utf8_decode("_____________________________________________________________"), 0, 1, 'C');

						$this->Cell(0, 5, utf8_decode("OFICINAS ADMINISTRATIVAS MOLLENDO, S.A. DE C.V."), 0, 1, 'C');
					}
				}

				$pdf = new PDF('P', 'mm', 'Letter');

				$pdf->AliasNbPages();

				$pdf->SetDisplayMode('fullpage', 'single');

				$pdf->SetMargins(10, 10, 10);

				$pdf->SetAutoPageBreak(TRUE, 6);

				$pdf->SetFont('ARIAL', '', 12);

				foreach ($result as $row)
				{
					$pdf->AddPage('P', 'Letter');

					$pdf->SetFont('ARIAL', 'B', 12);

					$pdf->Text(11, 80, utf8_decode("{$row['nombre_pro']}"));
				}

				$pdf->Output("cartas-adeudo-proveedores-{$_REQUEST['anio']}.pdf", 'I');
			}

			break;

		case 'enviar_correos':
			echo "\n(II) Informativo, (PP) Procesando, (DD) Datos, (RR) Resultado, (EE) Error\n";

			echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Construyendo enunciado de consulta";

			$condiciones = array();

			$condiciones[] = "f.fecha BETWEEN '01/09/{$_REQUEST['anio']}'::DATE AND '31/12/{$_REQUEST['anio']}'::DATE";

			if (isset($_REQUEST['pros']) && trim($_REQUEST['pros']) != '')
			{
				$pros = array();

				$pieces = explode(',', $_REQUEST['pros']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$pros[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$pros[] = $piece;
					}
				}

				if (count($pros) > 0)
				{
					$condiciones[] = 'f.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = "cc.idadministrador = {$_REQUEST['admin']}";
			}

			$condiciones_string = implode(' AND ', $condiciones);

			echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Obteniendo información de proveedores";

			$result = $db->query("SELECT
				f.num_proveedor AS num_pro,
				cp.nombre AS nombre_pro,
				cp.email1 AS email_pro
			FROM
				facturas f
				LEFT JOIN catalogo_proveedores cp USING (num_proveedor)
			WHERE
				{$condiciones_string}
			GROUP BY
				f.num_proveedor,
				cp.nombre,
				cp.email1
			ORDER BY
				f.num_proveedor");

			if ($result)
			{
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Empezando proceso de envio de correos a destinatarios";

				/*
				@ Validar que la librería PHPMailer este cargada
				*/
				if ( ! class_exists('PHPMailer'))
				{
					include_once(dirname(__FILE__) . '/includes/phpmailer/class.phpmailer.php');
				}

				foreach ($result as $row)
				{
					echo "\n--------------------------------------------------------------------------------------";
					echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Proveedor {$row['num_pro']} {$row['nombre_pro']}";

					if (trim($row['email_pro']) == '')
					{
						echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) El proveedor no tiene correo, no se realizó envío";

						continue;
					}

					echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Conectando al servidor de correo";

					$mail = new PHPMailer();

					$mail->IsSMTP();
					$mail->Host = 'mail.lecaroz.com';
					$mail->Port = 587;
					$mail->SMTPAuth = true;
					$mail->Username = "yolanda.arreola@lecaroz.com";
					$mail->Password = 'L3c4r0z*';

					$mail->From = "yolanda.arreola@lecaroz.com";
					$mail->FromName = utf8_decode("Yolanda Arreola");

					echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](II) Destinatario {$row['email_pro']}";

					$mail->AddAddress($row['email_pro']);
					$mail->AddBCC('yolanda.arreola@lecaroz.com');
					// $mail->AddAddress('carlos.candelario@lecaroz.com');

					echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Generando correo a enviar";

					$mail->Subject = utf8_decode('¡¡¡AVISO IMPORTANTE!!!');

					$tpl = new TemplatePower(str_replace('/includes', '', dirname(__FILE__)) . '/plantillas/ban/email_adeudo_proveedor.tpl');
					$tpl->prepare();

					$tpl->assign('dia', date('j'));
					$tpl->assign('mes', $_meses[date('n')]);
					$tpl->assign('anio', date('Y'));

					$tpl->assign('nombre_pro', htmlentities(utf8_decode($row['nombre_pro'])));

					$tpl->assign('anio_cierre_contable', $_REQUEST['anio']);

					$mail->AddEmbeddedImage(dirname(__FILE__) . '/imagenes/firma-carta-adeudo-proveedor.png', 'firma-image', 'firma-image.png');

					$mail->Body = $tpl->getOutputContent();

					$mail->IsHTML(true);

					echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](PP) Enviando correo a destinatario";

					if( ! $mail->Send())
					{
						echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) No ha sido posible enviar correo: {$mail->ErrorInfo}";
					}
					else
					{
						echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](RR) Correo enviado con éxito";
					}
				}
			}
			else
			{
				echo "\n[" . date('Y-m-d H:i:s.') . substr(microtime(), 2, 6) . "](EE) No se obtuvieron resultados, cancelado envío de correos";
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/CorreoAdeudoProveedores.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y'));

$admins = $db->query("SELECT
	idadministrador
		AS value,
	nombre_administrador
		AS text
FROM
	catalogo_administradores
ORDER BY
	text");

if ($admins)
{
	foreach ($admins as $a) {
		$tpl->newBlock('admin');

		$tpl->assign('value', $a['value']);
		$tpl->assign('text', utf8_encode($a['text']));
	}
}

$tpl->printToScreen();
