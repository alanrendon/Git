<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

if (!function_exists('json_encode')) {
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

//if ($_SESSION['iduser'] != 1) {
//	die('PROGRAMA EN MANTENIMIENTO');
//}

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'cambiarCia':
			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';

			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode($result[0]['nombre_corto']);
			}
		break;

		case 'escanear':
			$tpl = new TemplatePower('plantillas/ban/CometraCartaSolicitudVideoScan.tpl');
			$tpl->prepare();

			$tpl->assign('host', $_SERVER['SERVER_ADDR']);

			echo $tpl->getOutputContent();
		break;

		case 'cargarImagen':
			$img = pg_escape_bytea(file_get_contents($_FILES['image']['tmp_name'][0]));

			$db_scans = new DBclass('pgsql://mollendo:pobgnj@127.0.0.1:5432/scans', 'autocommit=yes');

			$sql = '
				INSERT INTO
					img_car_sol_vid_tmp
						(
							img,
							idins
						)
				VALUES
					(
						\'' . $img . '\',
						' . $_SESSION['iduser'] . '
					);
			';

			$db_scans->query($sql);
		break;

		case 'actualizar':
			$tpl = new TemplatePower('plantillas/ban/CometraCartaSolicitudVideoHidden.tpl');
			$tpl->prepare();

			$db_scans = new DBclass('pgsql://mollendo:pobgnj@127.0.0.1:5432/scans', 'autocommit=yes');

			$sql = '
				SELECT
					last_value
				FROM
					img_car_sol_vid_tmp_id_seq
			';

			$result = $db_scans->query($sql);

			$tpl->assign('id', $result[0]['last_value']);

			$tpl->printToScreen();
		break;

		case 'obtenerMiniatura':
			$db_scans = new DBclass('pgsql://mollendo:pobgnj@127.0.0.1:5432/scans', 'autocommit=yes');

			$sql = '
				SELECT
					img
				FROM
					img_car_sol_vid_tmp
				WHERE
					id = ' . $_REQUEST['id'] . '
			';

			$result = $db_scans->query($sql);

			if ($result) {
				$img = pg_unescape_bytea($result[0]['img']);

				$src = imagecreatefromstring($img);

				$tipo = 'jpeg';

				$width = imagesx($src);
				$height = imagesy($src);

				$aspect_ratio = $height / $width;

				$sizeW = isset($_REQUEST['width']) && $_REQUEST['width'] > 0 ? $_REQUEST['width'] : $width;
				$sizeH = isset($_REQUEST['height']) && $_REQUEST['height'] > 0 ? $_REQUEST['height'] : (isset($_REQUEST['width']) && $_REQUEST['width'] > 0 ? abs($_REQUEST['width'] * $aspect_ratio) : $height);

				$img = imagecreatetruecolor($sizeW, $sizeH);

				imagecopyresampled($img, $src, 0, 0, 0, 0, $sizeW, $sizeH, $width, $height);

				header('Content-Type: image/' . $tipo);

				imagejpeg($img);

				imagedestroy($img);
			}
		break;

		case 'borrarMiniatura':
			$db_scans = new DBclass('pgsql://mollendo:pobgnj@127.0.0.1:5432/scans', 'autocommit=yes');

			$sql = '
				DELETE FROM
					img_car_sol_vid_tmp
				WHERE
					id = ' . $_REQUEST['id'] . '
			';

			$db_scans->query($sql);
		break;

		case 'pdf':
			include_once('includes/fpdf/fpdf.php');

			class PDF extends FPDF {
				function Header() {
				}

				function Footer() {
				}
			}

			$pdf = new PDF('P', 'mm', 'Letter');

			$pdf = new PDF('L', 'mm', array(216, 340));

			$pdf->AliasNbPages();

			$pdf->SetDisplayMode('fullwidth', 'single');

			$pdf->SetMargins(18, 15, 18);

			$pdf->SetAutoPageBreak(FALSE);

			$pdf->AddPage('P', 'Letter');

			$pdf->SetFont('ARIAL', 'B', 16);

			$pdf->Cell(0, 0, utf8_decode('Oficinas Administrativas Mollendo, S. de R.L. de C.V.'), 0, 1, 'C');

			$pdf->Ln(6);

			$pdf->SetFont('ARIAL', 'B', 10);

			$pdf->Cell(0, 0, utf8_decode('OAM 920522 R45'), 0, 0, 'C');

			$pdf->Line(20, 24, 196, 24);

			$pdf->Line(20, 24.3, 196, 24.3);

			$pdf->Ln(18);

			$pdf->SetFont('ARIAL', 'B', 12);

			$pdf->Cell(0, 0, utf8_decode('México, D.F., a ' . date('j') . ' de ' . $_meses[date('n')] . ' de ' . date('Y')), 0, 0, 'R');

			$pdf->Ln(30);

			$pdf->Cell(0, 0, utf8_decode('COMETRA'), 0, 0);

			$pdf->Ln(6);

			$pdf->Cell(0, 0, utf8_decode('At\'n. Marco Antonio Alanis'), 0, 0);

			$pdf->Ln(15);

			$pdf->SetFont('ARIAL', '', 12);

			$pdf->Cell(0, 0, utf8_decode('Por medio de la presente me permito saludarle y a la vez solicitarle el siguiente video que podrá'), 0, 0);

			$pdf->Ln(5);

			$pdf->Cell(0, 0, utf8_encode('ser visto por el Lic. Miguel Angel Rebuelta Diez y el Sr(a). ' . ucwords(strtolower($_REQUEST['encargado'])) . ':'));

			$pdf->Ln(10);

			$pdf->SetFont('ARIAL', 'B', 12);

			$sql = '
				SELECT
					nombre
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';

			$result = $db->query($sql);

			$pdf->Cell(0, 0, utf8_decode('Compañía: ' . $_REQUEST['num_cia'] . ' ' . utf8_encode($result[0]['nombre'])), 0, 0);
			$pdf->Ln(6);
			$pdf->Cell(0, 0, utf8_decode('Comprobante: ' . $_REQUEST['comprobante']), 0, 0);
			$pdf->Ln(6);
			$pdf->Cell(0, 0, utf8_decode($_REQUEST['tipo'] . ': $' . $_REQUEST['importe']), 0, 0);
			$pdf->Ln(6);
			$pdf->Cell(0, 0, utf8_decode('Fecha: ' . $_REQUEST['fecha']), 0, 0);

			$pdf->Ln(10);

			$pdf->SetFont('ARIAL', '', 12);

			$pdf->Cell(0, 0, utf8_decode('Sin más por el momento, quedo a sus órdenes para cualquier comentario.'), 0, 0);

			$pdf->Ln(30);

			$pdf->Cell(0, 0, utf8_decode('Atentamente'), 0, 0, 'C');

			$pdf->Ln(30);

			$pdf->SetFont('ARIAL', 'B', 12);

			$pdf->Cell(0, 0, utf8_decode('Oficinas Administrativas Mollendo, S. de R.L. de C.V.'), 0, 0, 'C');

			$pdf->Line(52, 184, 164, 184);

			$pdf->Image('imagenes/firma_carta_video.jpg', 90, 165, 40, 18, 'JPG');

			$db_scans = new DBclass('pgsql://mollendo:pobgnj@127.0.0.1:5432/scans', 'autocommit=yes');

			$sql = '
				SELECT
					id,
					img
				FROM
					img_car_sol_vid_tmp
				ORDER BY
					id
			';

			$result = $db_scans->query($sql);

			if ($result) {
				shell_exec("chmod ugo=rwx billetes_falsos");

				foreach ($result as $i => $rec) {
					$img = pg_unescape_bytea($rec['img']);

					$src = imagecreatefromstring($img);

					unset($img);

					imagejpeg($src, 'billetes_falsos/doc' . $i . '.jpg');

					imagedestroy($src);

					$file = 'billetes_falsos/doc' . $i . '.jpg';

					$pdf->AddPage('P', 'Letter');

					$pdf->Image($file, 20, 20, 180, 0, 'JPG');
				}

				shell_exec("chmod ugo=r billetes_falsos");
			}

			$pdf->Output('carta.pdf', 'I');

			$sql = '
				DELETE FROM
					img_car_sol_vid_tmp
				WHERE
					idins = ' . $_SESSION['iduser'] . '
			';

			$db_scans->query($sql);
		break;

		case 'carta':
			$tpl = new TemplatePower('plantillas/ban/CometraCartaSolicitudVideoFormato.tpl');
			$tpl->prepare();

			$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

			$sql = '
				SELECT
					nombre
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';

			$result = $db->query($sql);

			$nombre_cia = utf8_encode($result[0]['nombre']);

			$tpl->assign('dia', date('j'));
			$tpl->assign('mes', $_meses[date('n')]);
			$tpl->assign('anio', date('Y'));

			$tpl->assign('num_cia', $_REQUEST['num_cia']);
			$tpl->assign('nombre_cia', $nombre_cia);
			$tpl->assign('comprobante', $_REQUEST['comprobante']);
			$tpl->assign('faltante', $_REQUEST['faltante']);
			$tpl->assign('fecha', $_REQUEST['fecha']);

			$tpl->printToScreen();

		break;
	}

	die;
}

$db_scans = new DBclass('pgsql://mollendo:pobgnj@127.0.0.1:5432/scans', 'autocommit=yes');

$sql = '
	DELETE FROM
		img_car_sol_vid_tmp
	WHERE
		idins = ' . $_SESSION['iduser'] . '
';

$db_scans->query($sql);

$tpl = new TemplatePower('plantillas/ban/CometraCartaSolicitudVideo.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha', date('d/m/Y'));

$tpl->printToScreen();

?>
