<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

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

$_meses = array(
	1  => 'ENE',
	2  => 'FEB',
	3  => 'MAR',
	4  => 'ABR',
	5  => 'MAY',
	6  => 'JUN',
	7  => 'JUL',
	8  => 'AGO',
	9  => 'SEP',
	10 => 'OCT',
	11 => 'NOV',
	12 => 'DIC'
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

//if ($_SESSION['iduser'] != 1) die('EN PROCESO DE ACTUALIZACION');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'inicio':
			$tpl = new TemplatePower('plantillas/ban/BilletesFalsosConsultaInicio.tpl');
			$tpl->prepare();

			$tpl->assign('fecha1', date('01/m/Y'));
			$tpl->assign('fecha2', date('d/m/Y'));

			$admins = $db->query("SELECT idadministrador AS value, nombre_administrador AS text FROM catalogo_administradores ORDER BY text");

			if ($admins)
			{
				foreach ($admins as $a) {
					$tpl->newBlock('admin');
					$tpl->assign('value', $a['value']);
					$tpl->assign('text', utf8_encode($a['text']));
				}
			}

			echo $tpl->getOutputContent();
		break;

		case 'consultar':
			$condiciones = array();

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
					$condiciones[] = 'num_cia IN (' . implode(', ', $arrendadores) . ')';
				}
			}

			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') || (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') && (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')
					&& $_REQUEST['fecha1'] != $_REQUEST['fecha2']) {
					$condiciones[] = 'fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				} else if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') && (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')
					&& $_REQUEST['fecha1'] == $_REQUEST['fecha2']) {
					$condiciones[] = 'fecha = \'' . $_REQUEST['fecha1'] . '\'';
				}  else if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') || (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
					$condiciones[] = 'fecha = \'' . ($_REQUEST['fecha1'] ? $_REQUEST['fecha1'] : $_REQUEST['fecha2']) . '\'';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = "idadministrador = {$_REQUEST['admin']}";
			}

			$sql = '
				SELECT
					id,
					num_cia,
					nombre_corto
						AS nombre_cia,
					fecha,
					importe
				FROM
					billetes_falsos bf
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				' . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . '
				ORDER BY
					num_cia,
					fecha
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ban/BilletesFalsosConsultaResultado.tpl');
			$tpl->prepare();

			if ($result) {
				$num_cia = NULL;
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];

						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));

						$color = FALSE;
					}

					$tpl->newBlock('row');

					$tpl->assign('color', $color ? 'on' : 'off');

					$color = !$color;

					$tpl->assign('id', $rec['id']);
					$tpl->assign('fecha', $rec['fecha']);
					$tpl->assign('importe', number_format($rec['importe'], 2));
				}
			}

			echo $tpl->getOutputContent();
		break;

		case 'verImagenes':
			$db_scans = new DBclass('pgsql://mollendo:pobgnj@127.0.0.1:5432/scans', 'autocommit=yes');

			$sql = '
				SELECT
					id
				FROM
					img_bill_fal
				WHERE
					idbillfalso = ' . $_REQUEST['id'] . '
			';

			$result = $db_scans->query($sql);

			if ($result) {
				$tpl = new TemplatePower('plantillas/ban/BilletesFalsosConsultaVer.tpl');
				$tpl->prepare();

				foreach ($result as $rec) {
					$tpl->newBlock('row');

					$tpl->assign('id', $rec['id']);
				}

				$tpl->printToScreen();
			}
		break;

		case 'imagen':
			$db_scans = new DBclass('pgsql://mollendo:pobgnj@127.0.0.1:5432/scans', 'autocommit=yes');

			$sql = '
				SELECT
					img
				FROM
					img_bill_fal
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

		case 'enviar':
			$db_scans = new DBclass('pgsql://mollendo:pobgnj@127.0.0.1:5432/scans', 'autocommit=yes');

			$sql = '
				SELECT
					doc,
					img
				FROM
					img_bill_fal
				WHERE
					idbillfalso = ' . $_REQUEST['id'] . '
			';

			$result = $db_scans->query($sql);

			$files = array();

			shell_exec("chmod ugo=rwx billetes_falsos");

			foreach ($result as $rec) {
				$img = pg_unescape_bytea($rec['img']);

				$src = imagecreatefromstring($img);

				unset($img);

				imagejpeg($src, 'billetes_falsos/doc' . $rec['doc'] . '.jpg');

				imagedestroy($src);

				$files[] = 'billetes_falsos/doc' . $rec['doc'] . '.jpg';
			}

			if (!class_exists('PHPMailer')) {
				include_once('includes/phpmailer/class.phpmailer.php');
			}

			$mail = new PHPMailer();

			$mail->IsSMTP();
			$mail->Host = 'mail.lecaroz.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'mollendo+lecaroz.com';
			$mail->Password = 'L3c4r0z*';

			$mail->From = 'mollendo@lecaroz.com';
			$mail->FromName = utf8_decode('Oficinas Administrativas Mollendo, S. de R.L. de C.V.');

			$sql = '
				SELECT
					email
				FROM
					billetes_falsos
					LEFT JOIN catalogo_companias
						USING (num_cia)
				WHERE
					id = ' . $_REQUEST['id'] . '
			';

			$email = $db->query($sql);

			if ($email && trim($email[0]['email']) != '') {
				$mail->AddAddress($email[0]['email']);
			}

			$mail->AddCC('billetes.falsos@lecaroz.com');
			//$mail->AddBCC('carlos.candelario@lecaroz.com');

			$mail->Subject = 'BILLETE FALSO';

			$mail->Body = 'BILLETE FALSO';

			$mail->IsHTML(true);

			foreach ($files as $file) {
				$mail->AddAttachment($file);
			}

			if(!$mail->Send()) {
				echo $mail->ErrorInfo;
			}

			shell_exec("chmod ugo=r billetes_falsos");
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/BilletesFalsosConsulta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
