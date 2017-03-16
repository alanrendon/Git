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
	1  => 'Ene',
	2  => 'Feb',
	3  => 'Mar',
	4  => 'Abr',
	5  => 'May',
	6  => 'Jun',
	7  => 'Jul',
	8  => 'Ago',
	9  => 'Sep',
	10 => 'Oct',
	11 => 'Nov',
	12 => 'Dic'
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
			$tpl = new TemplatePower('plantillas/ban/BilletesFalsosScan.tpl');
			$tpl->prepare();
			
			$ip = explode('.', $_SERVER['REMOTE_ADDR']);
			
			if ($ip[0] == '192' && $ip[1] == '168' && $ip[2] == '1') {
				$host = '192.168.1.250';
			}
			else {
				$ip[3] = '1';
				
				$host = implode('.', $ip);
			}
			
			$tpl->assign('host', $host);
			$tpl->assign('doc', $_REQUEST['doc']);
			
			echo $tpl->getOutputContent();
		break;
		
		case 'cargarImagen':
			$img = pg_escape_bytea(file_get_contents($_FILES['image']['tmp_name'][0]));
			
			$db_scans = new DBclass('pgsql://mollendo:pobgnj@127.0.0.1:5432/scans', 'autocommit=yes');
			
			$sql = '
				DELETE FROM
					img_bill_fal_tmp
				WHERE
					doc = ' . $_REQUEST['doc'] . ';
				
				INSERT INTO
					img_bill_fal_tmp
						(
							doc,
							img,
							idins
						)
				VALUES
					(
						' . $_REQUEST['doc'] . ',
						\'' . $img . '\',
						' . $_SESSION['iduser'] . '
					);
			';
			
			$db_scans->query($sql);
		break;
		
		case 'actualizar':
			$tpl = new TemplatePower('plantillas/ban/BilletesFalsosHidden.tpl');
			$tpl->prepare();
			
			$tpl->assign('doc', $_REQUEST['doc']);
			
			$tpl->printToScreen();
		break;
		
		case 'obtenerMiniatura':
			$db_scans = new DBclass('pgsql://mollendo:pobgnj@127.0.0.1:5432/scans', 'autocommit=yes');
			
			$sql = '
				SELECT
					img
				FROM
					img_bill_fal_tmp
				WHERE
					doc = ' . $_REQUEST['doc'] . '
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
		
		case 'registrar':
			$sql = '
				INSERT INTO
					billetes_falsos
						(
							num_cia,
							fecha,
							importe,
							idins
						)
					VALUES
						(
							' . $_REQUEST['num_cia'] . ',
							\'' . $_REQUEST['fecha'] . '\',
							' . get_val($_REQUEST['importe']) . ',
							' . $_SESSION['iduser'] . '
						)
			';
			
			$db->query($sql);
			
			$sql = '
				SELECT
					last_value
				FROM
					billetes_falsos_id_seq
			';
			
			$result = $db->query($sql);
			
			$last_value = $result[0]['last_value'];
			
			$db_scans = new DBclass('pgsql://mollendo:pobgnj@127.0.0.1:5432/scans', 'autocommit=yes');
			
			$sql = '
				INSERT INTO
					img_bill_fal
						(
							idbillfalso,
							doc,
							img,
							idins
						)
				SELECT
					' . $last_value . ',
					doc,
					img,
					idins
				FROM
					img_bill_fal_tmp
				ORDER BY
					doc
			';
			
			$db_scans->query($sql);
			
			$sql = '
				DELETE FROM
					img_bill_fal_tmp
			';
			
			$db_scans->query($sql);
			
			$sql = '
				SELECT
					doc,
					img
				FROM
					img_bill_fal
				WHERE
					idbillfalso = ' . $last_value . '
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
			$mail->Username = 'mollendo@lecaroz.com';
			$mail->Password = 'L3c4r0z*';
			
			$mail->From = 'mollendo@lecaroz.com';
			$mail->FromName = utf8_decode('Oficinas Administrativas Mollendo, S. de R.L. de C.V.');
			
			$sql = '
				SELECT
					email
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
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

$tpl = new TemplatePower('plantillas/ban/BilletesFalsos.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha', date('d/m/Y'));

$tpl->printToScreen();

?>
