<?php
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';
include 'includes/dbstatus.php';
include 'includes/phpmailer/class.phpmailer.php';

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
	1 => 'Enero',
	2 => 'Febrero',
	3 => 'Marzo',
	4 => 'Abril',
	5 => 'Mayo',
	6 => 'Junio',
	7 => 'Julio',
	8 => 'Agosto',
	9 => 'Septiembre',
	10 => 'Octubre',
	11 => 'Noviembre',
	12 => 'Diciembre'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

$condiciones = array();

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
		$condiciones[] = 'p.folio IN (' . implode(', ', $folios) . ')';
	}
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
		$condiciones[] = 'p.num_cia IN (' . implode(', ', $cias) . ')';
	}
}

if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
	$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
}

if (isset($_REQUEST['pros']) && trim($_REQUEST['pros']) != '') {
	$pros = array();
	
	$pieces = explode(',', $_REQUEST['pros']);
	foreach ($pieces as $piece) {
		if (count($exp = explode('-', $piece)) > 1) {
			$pros[] =  implode(', ', range($exp[0], $exp[1]));
		}
		else {
			$pros[] = $piece;
		}
	}
	
	if (count($pros) > 0) {
		$condiciones[] = 'p.num_proveedor IN (' . implode(', ', $pros) . ')';
	}
}

if (isset($_REQUEST['mps']) && trim($_REQUEST['mps']) != '') {
	$mps = array();
	
	$pieces = explode(',', $_REQUEST['mps']);
	foreach ($pieces as $piece) {
		if (count($exp = explode('-', $piece)) > 1) {
			$mps[] =  implode(', ', range($exp[0], $exp[1]));
		}
		else {
			$mps[] = $piece;
		}
	}
	
	if (count($mps) > 0) {
		$condiciones[] = 'p.codmp IN (' . implode(', ', $mps) . ')';
	}
}

if (isset($_REQUEST['omitir_cias']) && trim($_REQUEST['omitir_cias']) != '') {
	$omitir_cias = array();
	
	$pieces = explode(',', $_REQUEST['omitir_cias']);
	foreach ($pieces as $piece) {
		if (count($exp = explode('-', $piece)) > 1) {
			$omitir_cias[] =  implode(', ', range($exp[0], $exp[1]));
		}
		else {
			$omitir_cias[] = $piece;
		}
	}
	
	if (count($omitir_cias) > 0) {
		$condiciones[] = 'p.num_cia NOT IN (' . implode(', ', $omitir_cias) . ')';
	}
}

if (isset($_REQUEST['omitir_pros']) && trim($_REQUEST['omitir_pros']) != '') {
	$omitir_pros = array();
	
	$pieces = explode(',', $_REQUEST['omitir_pros']);
	foreach ($pieces as $piece) {
		if (count($exp = explode('-', $piece)) > 1) {
			$omitir_pros[] =  implode(', ', range($exp[0], $exp[1]));
		}
		else {
			$omitir_pros[] = $piece;
		}
	}
	
	if (count($omitir_pros) > 0) {
		$condiciones[] = 'p.num_proveedor NOT IN (' . implode(', ', $omitir_pros) . ')';
	}
}

if (isset($_REQUEST['omitir_mps']) && trim($_REQUEST['omitir_mps']) != '') {
	$omitir_mps = array();
	
	$pieces = explode(',', $_REQUEST['omitir_mps']);
	foreach ($pieces as $piece) {
		if (count($exp = explode('-', $piece)) > 1) {
			$omitir_mps[] =  implode(', ', range($exp[0], $exp[1]));
		}
		else {
			$omitir_mps[] = $piece;
		}
	}
	
	if (count($omitir_mps) > 0) {
		$condiciones[] = 'p.codmp NOT IN (' . implode(', ', $omitir_mps) . ')';
	}
}

if (isset($_REQUEST['fecha1']) || isset($_REQUEST['fecha2'])) {
	if (isset($_REQUEST['fecha1']) && isset($_REQUEST['fecha2'])) {
		$condiciones[] = 'p.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
	}
	else if (isset($_REQUEST['fecha1'])) {
		$condiciones[] = 'p.fecha = \'' . $_REQUEST['fecha1'] . '\'';
	}
	else if (isset($_REQUEST['fecha2'])) {
		$condiciones[] = 'p.fecha >= \'' . $_REQUEST['fecha2'] . '\'';
	}
}

if (isset($_REQUEST['id'])) {
	$condiciones[] = 'p.id IN (' . implode(', ', $_REQUEST['id']) . ')';
}

$condiciones[] = 'p.tsbaja IS NULL';

$sql = '
	SELECT
		p.folio,
		p.fecha,
		num_cia,
		cc.nombre_corto
			AS nombre_cia,
		codmp,
		cmp.nombre
			AS nombre_mp,
		pedido,
		p.unidad,
		entregar,
		p.presentacion,
		p.contenido,
		p.num_proveedor
			AS num_pro,
		cp.nombre
			AS nombre_pro,
		cp.telefono1,
		cp.telefono2,
		cp.email1,
		cp.email2,
		cp.email3,
		cc.email
			AS email_cia,
		ca.email
			AS email_admin,
		pa.anotaciones,
		p.urgente,
		p.programa
	FROM
		pedidos_new p
		LEFT JOIN pedidos_anotaciones pa
			USING (folio, num_proveedor)
		LEFT JOIN catalogo_proveedores cp
			USING (num_proveedor)
		LEFT JOIN catalogo_companias cc
			USING (num_cia)
		LEFT JOIN catalogo_administradores ca
			USING (idadministrador)
		LEFT JOIN catalogo_mat_primas cmp
			USING (codmp)
	WHERE
		' . implode(' AND ', $condiciones) . '
	ORDER BY
		folio,
		num_pro,
		num_cia,
		codmp,
		pedido
';

$result = $db->query($sql);

if ($result) {
	$folio = NULL;
	$num_pro = NULL;
	
	foreach ($result as $rec) {
		if ($folio != $rec['folio'] || $num_pro != $rec['num_pro']) {
			if ($num_pro != NULL) {
				$mail = new PHPMailer();
				
				$mail->IsSMTP();
				$mail->Host = 'mail.lecaroz.com';
				$mail->Port = 587;
				$mail->SMTPAuth = true;
				$mail->Username = 'wendy.barona@lecaroz.com';
				$mail->Password = 'L3c4r0z*';
				
				$mail->From = 'wendy.barona@lecaroz.com';
				$mail->FromName = 'Lecaroz :: Compras';
				
				foreach ($emails as $email) {
					$mail->AddAddress($email);
				}
				
				if ($email_cia != '') {
					$mail->AddBCC($email_cia);
				}
				
				if ($email_admin != '') {
					$mail->AddBCC($email_admin);
				}
				
				$mail->AddBCC('wendy.barona@lecaroz.com');
				
				//$mail->AddBCC('daniela.requena@lecaroz.com');
				
				//$mail->AddBCC('carlos.candelario@lecaroz.com');
				
				$mail->Subject = 'Lecaroz :: Pedido de Materias Primas - ' . $nombre_pro . ' [Folio: ' . $folio . '][' . date('d/m/Y H:i') . ']';
				
				$mail->Body = $tpl->getOutputContent();
				
				$mail->IsHTML(true);
				
				if(!$mail->Send()) {
					//echo $mail->ErrorInfo;
				}
			}
			
			$folio = $rec['folio'];
			
			$num_pro = $rec['num_pro'];
			$nombre_pro = $rec['nombre_pro'];
			
			list($dia, $mes, $anio) = explode('/', $rec['fecha']);
			
			$tpl = new TemplatePower('plantillas/ped/EmailPedidos.tpl');
			$tpl->prepare();
			
			$tpl->assign('num_pro', $num_pro);
			$tpl->assign('nombre_pro', $rec['nombre_pro']);
			$tpl->assign('telefono1', $rec['telefono1']);
			$tpl->assign('dia', intval($dia, 10));
			$tpl->assign('mes', $_meses[intval($mes, 10)]);
			$tpl->assign('anio', intval($anio));
			$tpl->assign('folio', intval($rec['folio']));
			
			$tpl->assign('anotaciones', $rec['anotaciones'] != '' ? '<p><strong class="underline">OBSERVACIONES: ' . $rec['anotaciones'] . '</strong></p>' : '');
			
			$num_cia = NULL;
			
			$emails = array();
			
			if ($rec['email1'] != '') {
				$emails[] = $rec['email1'];
			}
			if ($rec['email2'] != '') {
				$emails[] = $rec['email2'];
			}
			if ($rec['email3'] != '') {
				$emails[] = $rec['email3'];
			}
			
			$email_cia = '';
			
			if (trim($rec['email_cia']) != '' && $rec['programa'] == 4) {
				$email_cia = $rec['email_cia'];
			}
			
			$email_admin = '';
			
			if (trim($rec['email_admin']) != '' && $rec['programa'] == 4) {
				$email_admin = $rec['email_admin'];
			}
		}
		
		if ($num_cia != $rec['num_cia']) {
			$num_cia = $rec['num_cia'];
			
			$tpl->newBlock('cia');
			$tpl->assign('num_cia', $num_cia);
			$tpl->assign('nombre_cia', $rec['nombre_cia']);
		}
		
		$tpl->newBlock('row');
		$tpl->assign('codmp', $rec['codmp']);
		$tpl->assign('nombre_mp', $rec['nombre_mp']);
		$tpl->assign('pedido', number_format($rec['pedido'], 2, '.', ','));
		$tpl->assign('unidad', $rec['unidad'] . ($rec['pedido'] > 1 ? (in_array($rec['unidad'][strlen($rec['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : ''));
		$tpl->assign('entregar', number_format($rec['entregar'], 2, '.', ','));
		$tpl->assign('presentacion', $rec['presentacion'] . ($rec['entregar'] > 1 ? (in_array($rec['presentacion'][strlen($rec['presentacion']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') . ($rec['unidad'] != $rec['presentacion'] || $rec['contenido'] > 1 ? ' DE ' . $rec['contenido'] . ' ' . $rec['unidad'] . ($rec['contenido'] > 1 ? (in_array($rec['unidad'][strlen($rec['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') : ''));
		
		$tpl->assign('urgente', $rec['urgente'] == 't' ? ' class="urgente"' : '');
	}
	
	if ($num_pro != NULL) {
		$mail = new PHPMailer();
		
		$mail->IsSMTP();
		$mail->Host = 'mail.lecaroz.com';
		$mail->Port = 587;
		$mail->SMTPAuth = true;
		$mail->Username = 'wendy.barona@lecaroz.com';
		$mail->Password = 'L3c4r0z*';
		
		$mail->From = 'wendy.barona@lecaroz.com';
		$mail->FromName = 'Lecaroz :: Compras';
		
		foreach ($emails as $email) {
			$mail->AddAddress($email);
		}
		
		if ($email_cia != '') {
			$mail->AddBCC($email_cia);
		}
		
		if ($email_admin != '') {
			$mail->AddBCC($email_admin);
		}
		
		$mail->AddBCC('wendy.barona@lecaroz.com');
		
		//$mail->AddBCC('daniela.requena@lecaroz.com');
		
		$mail->Subject = 'Lecaroz :: Pedido de Materias Primas - ' . $nombre_pro . ' [Folio: ' . $folio . '][' . date('d/m/Y H:i') . ']';
		
		$mail->Body = $tpl->getOutputContent();
		
		$mail->IsHTML(true);
		
		if(!$mail->Send()) {
			//echo $mail->ErrorInfo;
		}
	}
}

?>
