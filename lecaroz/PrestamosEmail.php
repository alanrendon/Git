<?php

if (!in_array(date('j'), array(10, 20, date('j', mktime(0, 0, 0, date('n') + 1, 0))))) {
	die;
}

include('/var/www/lecaroz/includes/class.db.inc.php');
include('/var/www/lecaroz/includes/class.TemplatePower.inc.php');
include('/var/www/lecaroz/includes/dbstatus.php');
include('/var/www/lecaroz/includes/phpmailer/class.phpmailer.php');

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

function toInt($value) {
	return intval($value, 10);
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

$sql = '
	SELECT
		cc.idadministrador
			AS admin,
		ct.num_cia_emp
			AS num_cia,
		cc.nombre_corto
			AS nombre_cia,
		cc.email
			AS email_cia,
		ca.email
			AS email_admin,
		ct.num_emp,
		ct.ap_paterno,
		ct.ap_materno,
		ct.nombre,
		ct.rfc,
		(
			SELECT
				MAX(fecha)
			FROM
				prestamos
			WHERE
				id_empleado = ct.id
				AND tipo_mov = FALSE
				AND pagado = FALSE
		)
			AS fecha_ultimo_prestamo,
		(
			SELECT
				SUM(importe)
			FROM
				prestamos
			WHERE
				id_empleado = ct.id
				AND tipo_mov = FALSE
				AND pagado = FALSE
		)
			AS prestamo,
		(
			SELECT
				SUM(importe)
			FROM
				prestamos
			WHERE
				id_empleado = ct.id
				AND tipo_mov = TRUE
				AND pagado = FALSE
		)
			AS abonos,
		(
			SELECT
				MAX(fecha)
			FROM
				prestamos
			WHERE
				id_empleado = ct.id
				AND tipo_mov = TRUE
				AND pagado = FALSE
		)
			AS fecha_ultimo_abono,
		(
			SELECT
				importe
			FROM
				prestamos
			WHERE
				id_empleado = ct.id
				AND tipo_mov = TRUE
				AND pagado = FALSE
			ORDER BY
				fecha DESC
			LIMIT
				1
		)
			AS ultimo_abono,
		(
			SELECT
				SUM(
					CASE
						WHEN tipo_mov = TRUE THEN
							-importe
						ELSE
							importe
					END
				)
			FROM
				prestamos
			WHERE
				id_empleado = ct.id
				AND pagado = FALSE
		)
			AS saldo,
		NOW()::DATE - COALESCE((
			SELECT
				MAX(fecha)::DATE
			FROM
				prestamos
			WHERE
				id_empleado = ct.id
				AND tipo_mov = TRUE
				AND pagado = FALSE
		), (
			SELECT
				MAX(fecha)::DATE
			FROM
				prestamos
			WHERE
				id_empleado = ct.id
				AND tipo_mov = FALSE
				AND pagado = FALSE
		))
			AS dias_atraso
	FROM
		catalogo_trabajadores ct
		LEFT JOIN catalogo_companias cc
			ON (cc.num_cia = ct.num_cia_emp)
		LEFT JOIN catalogo_administradores ca
			USING (idadministrador)
	WHERE
		ct.id IN (
			SELECT
				id_empleado
			FROM
				prestamos
			WHERE
				pagado = FALSE
			GROUP BY
				id_empleado
		)
		AND fecha_baja IS NULL
	ORDER BY
		admin,
		ct.num_cia_emp,
		ct.num_emp
';

$result = $db->query($sql);

if ($result) {
	$num_cia = NULL;
	
	$ok = FALSE;
	
	foreach ($result as $rec) {
		if ($num_cia != $rec['num_cia']) {
			if ($num_cia != NULL && $ok) {
				$mail = new PHPMailer();
				
				$mail->IsSMTP();
				$mail->Host = 'mail.lecaroz.com';
				$mail->Port = 587;
				$mail->SMTPAuth = true;
				$mail->Username = 'mollendo@lecaroz.com';
				$mail->Password = 'L3c4r0z*';
				
				$mail->From = 'mollendo@lecaroz.com';
				$mail->FromName = utf8_decode('Oficinas Administrativas Mollendo, S. de R.L. de C.V.');
				
				if ($email_cia) {
					$mail->AddAddress($email_cia);
				}
				
				$mail->Subject = utf8_decode('[' . $num_cia . ' ' . $nombre_cia . '] Reporte de prestamos a empleados al día ' . date('d/m/Y'));
				
				$mail->Body = $tpl->getOutputContent();
				
				$mail->IsHTML(true);
				
				@$mail->Send();
			}
			
			$num_cia = $rec['num_cia'];
			$nombre_cia = $rec['nombre_cia'];
			
			$email_cia = trim($rec['email_cia']);
			
			if ($email_cia != '') {
				$tpl = new TemplatePower('/var/www/lecaroz/plantillas/pan/PrestamosEmail.tpl');
				$tpl->prepare();
				
				$tpl->assign('num_cia', $rec['num_cia']);
				$tpl->assign('nombre_cia', $rec['nombre_cia']);
				$tpl->assign('fecha', date('d/m/Y'));
				
				$total = 0;
				
				$ok = TRUE;
			}
		}
		
		if ($ok) {
			$tpl->newBlock('row');
			
			$tpl->assign('num_emp', $rec['num_emp']);
			$tpl->assign('nombre_emp', implode(' ', array(
				$rec['ap_paterno'],
				$rec['ap_materno'],
				$rec['nombre']
			)));
			$tpl->assign('prestamo', $rec['prestamo'] != 0 ? number_format($rec['prestamo'], 2) : '&nbsp;');
			$tpl->assign('fecha_ultimo_prestamo', $rec['fecha_ultimo_prestamo'] != '' ? $rec['fecha_ultimo_prestamo'] : '&nbsp;');
			$tpl->assign('abonos', $rec['abonos'] != 0 ? number_format($rec['abonos'], 2) : '&nbsp;');
			$tpl->assign('fecha_ultimo_abono', $rec['fecha_ultimo_abono'] != '' ? $rec['fecha_ultimo_abono'] : '&nbsp;');
			$tpl->assign('ultimo_abono', $rec['ultimo_abono'] != 0 ? number_format($rec['ultimo_abono'], 2) : '&nbsp;');
			$tpl->assign('dias_atraso', $rec['dias_atraso'] != 0 ? number_format($rec['dias_atraso']) : '&nbsp;');
			$tpl->assign('atraso', $rec['dias_atraso'] > 30 ? 'atraso' : 'red');
			$tpl->assign('saldo', $rec['saldo'] != 0 ? number_format($rec['saldo'], 2) : '&nbsp;');
			
			$total += $rec['saldo'];
			
			$tpl->assign('_ROOT.total', number_format($total, 2));
		}
	}
	
	if ($num_cia != NULL && $ok) {
		$mail = new PHPMailer();
		
		$mail->IsSMTP();
		$mail->Host = 'mail.lecaroz.com';
		$mail->Port = 587;
		$mail->SMTPAuth = true;
		$mail->Username = 'mollendo@lecaroz.com';
		$mail->Password = 'L3c4r0z*';
		
		$mail->From = 'mollendo@lecaroz.com';
		$mail->FromName = utf8_decode('Oficinas Administrativas Mollendo, S. de R.L. de C.V.');
		
		if ($email_cia) {
			$mail->AddAddress($email_cia);
		}
		
		$mail->Subject = utf8_decode('[' . $num_cia . ' ' . $nombre_cia . '] Reporte de prestamos a empleados al día ' . date('d/m/Y'));
		
		$mail->Body = $tpl->getOutputContent();
		
		$mail->IsHTML(true);
		
		@$mail->Send();
	}
	
	/******************************************************************************/
	
	$admin = NULL;
	
	$ok = FALSE;
	
	foreach ($result as $rec) {
		if ($admin != $rec['admin']) {
			if ($admin != NULL && $ok) {
				$mail = new PHPMailer();
				
				$mail->IsSMTP();
				$mail->Host = 'mail.lecaroz.com';
				$mail->Port = 587;
				$mail->SMTPAuth = true;
				$mail->Username = 'mollendo@lecaroz.com';
				$mail->Password = 'L3c4r0z*';
				
				$mail->From = 'mollendo@lecaroz.com';
				$mail->FromName = utf8_decode('Oficinas Administrativas Mollendo, S. de R.L. de C.V.');
				
				if ($email_admin) {
					$mail->AddAddress($email_admin);
				}
				
				$mail->AddBCC('miguelrebuelta@lecaroz.com');
				//$mail->AddBCC('carlos.candelario@lecaroz.com');
				
				$mail->Subject = utf8_decode('Reporte de prestamos a empleados al día ' . date('d/m/Y'));
				
				$mail->Body = $tpl->getOutputContent();
				
				$mail->IsHTML(true);
				
				@$mail->Send();
			}
			
			$admin = $rec['admin'];
			
			$email_admin = trim($rec['email_admin']);
			
			if ($email_admin != '') {
				$tpl = new TemplatePower('/var/www/lecaroz/plantillas/pan/PrestamosEmailAdmin.tpl');
				$tpl->prepare();
				
				$tpl->assign('fecha', date('d/m/Y'));
				
				$num_cia = NULL;
				
				$ok = TRUE;
			}
		}
		
		if ($ok && $num_cia != $rec['num_cia']) {
			$num_cia = $rec['num_cia'];
			
			$tpl->newBlock('cia');
			
			$tpl->assign('num_cia', $rec['num_cia']);
			$tpl->assign('nombre_cia', $rec['nombre_cia']);
			
			$total = 0;
		}
		
		if ($ok) {
			$tpl->newBlock('row');
			
			$tpl->assign('num_emp', $rec['num_emp']);
			$tpl->assign('nombre_emp', implode(' ', array(
				$rec['ap_paterno'],
				$rec['ap_materno'],
				$rec['nombre']
			)));
			$tpl->assign('prestamo', $rec['prestamo'] != 0 ? number_format($rec['prestamo'], 2) : '&nbsp;');
			$tpl->assign('fecha_ultimo_prestamo', $rec['fecha_ultimo_prestamo'] != '' ? $rec['fecha_ultimo_prestamo'] : '&nbsp;');
			$tpl->assign('abonos', $rec['abonos'] != 0 ? number_format($rec['abonos'], 2) : '&nbsp;');
			$tpl->assign('fecha_ultimo_abono', $rec['fecha_ultimo_abono'] != '' ? $rec['fecha_ultimo_abono'] : '&nbsp;');
			$tpl->assign('ultimo_abono', $rec['ultimo_abono'] != 0 ? number_format($rec['ultimo_abono'], 2) : '&nbsp;');
			$tpl->assign('dias_atraso', $rec['dias_atraso'] != 0 ? number_format($rec['dias_atraso']) : '&nbsp;');
			$tpl->assign('atraso', $rec['dias_atraso'] > 30 ? 'atraso' : 'red');
			$tpl->assign('saldo', $rec['saldo'] != 0 ? number_format($rec['saldo'], 2) : '&nbsp;');
			
			$total += $rec['saldo'];
			
			$tpl->assign('cia.total', number_format($total, 2));
		}
	}
	
	if ($admin != NULL && $ok) {
		$mail = new PHPMailer();
		
		$mail->IsSMTP();
		$mail->Host = 'mail.lecaroz.com';
		$mail->Port = 587;
		$mail->SMTPAuth = true;
		$mail->Username = 'mollendo@lecaroz.com';
		$mail->Password = 'L3c4r0z*';
		
		$mail->From = 'mollendo@lecaroz.com';
		$mail->FromName = utf8_decode('Oficinas Administrativas Mollendo, S. de R.L. de C.V.');
		
		if ($email_admin) {
			$mail->AddAddress($email_admin);
		}
		
		$mail->AddBCC('miguelrebuelta@lecaroz.com');
		//$mail->AddBCC('carlos.candelario@lecaroz.com');
		
		$mail->Subject = utf8_decode('Reporte de prestamos a empleados al día ' . date('d/m/Y'));
		
		$mail->Body = $tpl->getOutputContent();
		
		$mail->IsHTML(true);
		
		@$mail->Send();
	}
}

?>
