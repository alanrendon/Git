<?php
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';
include 'includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'verificar':
			$sql = '
				SELECT
					id
				FROM
					catalogo_trabajadores ct
				WHERE
					fecha_baja IS NULL
					AND empleado_especial IS NULL
					AND fecha_termino_contrato BETWEEN NOW()::DATE + INTERVAL \'1 DAY\' AND NOW()::DATE + INTERVAL \'8 DAYS\'
					' . ($_SESSION['iduser'] > 1 ? ('AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899')) : '') . '
				LIMIT
					1
			';
			$result = $db->query($sql);
			
			if ($result && in_array($_SESSION['iduser'], array(4, 28, 34, 40, 52, 53, 54, 59, 58, 61))) {
				echo 1;
			}
		break;
		
		case 'listado':
			$sql = '
				SELECT
					ct.id,
					ct.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					ca.nombre_administrador
						AS admin,
					num_emp,
					TRIM(regexp_replace(COALESCE(ct.ap_paterno, \'\') || \' \' || COALESCE(ct.ap_materno, \'\') || \' \' || COALESCE(ct.nombre, \'\'), \'\s+\', \' \', \'g\'))
						AS empleado,
					fecha_inicio_contrato
						AS fecha_inicio,
					fecha_termino_contrato
						AS fecha_termino
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_administradores ca
						USING (idadministrador)
				WHERE
					fecha_baja IS NULL
					AND empleado_especial IS NULL
					AND fecha_termino_contrato BETWEEN NOW()::DATE + INTERVAL \'1 DAY\' AND NOW()::DATE + INTERVAL \'8 DAYS\'
					' . ($_SESSION['iduser'] > 1 ? ('AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899')) : '') . '
				ORDER BY
					admin,
					num_cia,
					empleado
			';
			$result = $db->query($sql);
			
			if ($result && in_array($_SESSION['iduser'], array(1, 4, 28, 34, 40, 52, 53, 54, 59, 58, 61))) {
				$tpl = new TemplatePower('plantillas/AlertaTrabajadoresContratosProximosVencer.tpl');
				$tpl->prepare();
				
				$admin = NULL;
				foreach ($result as $rec) {
					if ($admin != $rec['admin']) {
						if ($admin != NULL) {
							$tpl->assign('admin.salto', '<br style="page-break-after:always;" />');
						}
						
						$admin = $rec['admin'];
						
						$tpl->newBlock('admin');
						$tpl->assign('admin', utf8_encode($rec['admin']));
						
						$num_cia = NULL;
					}
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
					}
					
					$tpl->newBlock('empleado');
					$tpl->assign('id', $rec['id']);
					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('num_emp', $rec['num_emp']);
					$tpl->assign('empleado', utf8_encode($rec['empleado']));
					$tpl->assign('fecha_inicio', $rec['fecha_inicio']);
					$tpl->assign('fecha_termino', $rec['fecha_termino']);
				}
				
				$tpl->printToScreen();
			}
		break;
		
		case 'renovar':
			$ids = array();
			$sql = '';
			
			if (isset($_REQUEST['id'])) {
				foreach ($_REQUEST['id'] as $i => $id) {
					if ($_REQUEST['meses'][$i] > 0) {
						$sql .= '
							UPDATE
								catalogo_trabajadores
							SET
								fecha_inicio_contrato = fecha_termino_contrato + INTERVAL \'1 DAY\',
								fecha_termino_contrato = fecha_termino_contrato + INTERVAL \'' . $_REQUEST['meses'][$i] . ' MONTHS\',
								firma_contrato = FALSE
							WHERE
								id = ' . $id . '
						' . ";\n";
						
						$sql .= '
							INSERT INTO
								contratos_firmas_pendientes
									(
										idempleado,
										fecha_inicio,
										fecha_termino,
										iduser_ins
									)
								SELECT
									id,
									fecha_inicio_contrato,
									fecha_termino_contrato,
									' . $_SESSION['iduser'] . '
								FROM
									catalogo_trabajadores
								WHERE
									id = ' . $id . '
						' . ";\n";
						
						$ids[] = $id;
					}
				}
			}
			
			if (isset($_REQUEST['ind'])) {
				foreach ($_REQUEST['ind'] as $i => $id) {
					$sql .= '
						UPDATE
							catalogo_trabajadores
						SET
							fecha_inicio_contrato = fecha_termino_contrato + INTERVAL \'1 DAY\',
							fecha_termino_contrato = NULL,
							firma_contrato = FALSE
						WHERE
							id = ' . $id . '
					' . ";\n";
					
					$sql .= '
						INSERT INTO
							contratos_firmas_pendientes
								(
									idempleado,
									fecha_inicio,
									fecha_termino,
									iduser_ins
								)
							SELECT
								id,
								fecha_inicio_contrato,
								fecha_termino_contrato,
								' . $_SESSION['iduser'] . '
							FROM
								catalogo_trabajadores
							WHERE
								id = ' . $id . '
					' . ";\n";
					
					$ids[] = $id;
				}
			}
			
			
			$db->query($sql);
			
			echo json_encode($ids);
		break;
		
		case 'email':
			$sql = '
				SELECT
					ct.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					cc.email
						AS email_cia,
					num_emp,
					TRIM(REGEXP_REPLACE(CONCAT_WS(\' \', ap_paterno, ap_materno, ct.nombre), \'\s+\', \' \', \'g\'))
						AS empleado,
					fecha_termino_contrato
						AS fecha
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_administradores ca
						USING (idadministrador)
				WHERE
					fecha_baja IS NULL
					AND empleado_especial IS NULL
					AND fecha_termino_contrato BETWEEN NOW()::DATE + INTERVAL \'1 DAY\' AND NOW()::DATE + INTERVAL \'8 DAYS\'
					' . ($_SESSION['iduser'] > 1 ? ('AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899')) : '') . '
				ORDER BY
					num_cia,
					empleado
			';
			$result = $db->query($sql);
			
			if ($result) {
				if (!class_exists('PHPMailer')) {
					include_once('includes/phpmailer/class.phpmailer.php');
				}
				
				$num_cia = NULL;
				
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						if ($num_cia != NULL) {
							$mail = new PHPMailer();
							
							$mail->IsSMTP();
							$mail->Host = 'mail.lecaroz.com';
							$mail->Port = 587;
							$mail->SMTPAuth = true;
							$mail->Username = 'mollendo@lecaroz.com';
							$mail->Password = 'L3c4r0z*';
							
							$mail->From = 'mollendo@lecaroz.com';
							$mail->FromName = 'Lecaroz :: Recursos Humanos';
							
							if ($email_cia != '') {
								$mail->AddAddress($email_cia);
							}
							
							$mail->AddCC('olga.espinoza@lecaroz.com');
							
							//$mail->AddBCC('carlos.candelario@lecaroz.com');
							
							$mail->Subject = utf8_decode('[' . $num_cia . ' ' . $nombre_cia . '] Contratos de trabajadores próximos a vencer [' . date('d/m/Y H:i:s') . ']');
							
							$mail->Body = $tpl->getOutputContent();
							
							$mail->IsHTML(true);
							
							if(!$mail->Send()) {
								//return $mail->ErrorInfo;
							}
						}
						
						$num_cia = $rec['num_cia'];
						$nombre_cia = $rec['nombre_cia'];
						$email_cia = $rec['email_cia'];
						
						$tpl = new TemplatePower('plantillas/nom/EmailContratosProximosVencer.tpl');
						$tpl->prepare();
						
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $rec['nombre_cia']);
					}
					
					$tpl->newBlock('row');
					$tpl->assign('num_emp', $rec['num_emp']);
					$tpl->assign('nombre', $rec['empleado']);
					$tpl->assign('fecha', $rec['fecha']);
				}
				
				if ($num_cia != NULL) {
					$mail = new PHPMailer();
					
					$mail->IsSMTP();
					$mail->Host = 'mail.lecaroz.com';
					$mail->Port = 587;
					$mail->SMTPAuth = true;
					$mail->Username = 'mollendo@lecaroz.com';
					$mail->Password = 'L3c4r0z*';
					
					$mail->From = 'mollendo@lecaroz.com';
					$mail->FromName = 'Lecaroz :: Recursos Humanos';
					
					if ($email_cia != '') {
						$mail->AddAddress($email_cia);
					}
					
					$mail->AddCC('olga.espinoza@lecaroz.com');
					
					//$mail->AddBCC('carlos.candelario@lecaroz.com');
					
					$mail->Subject = utf8_decode('[' . $num_cia . ' ' . $nombre_cia . '] Contratos de trabajadores próximos a vencer [' . date('d/m/Y H:i:s') . ']');
					
					$mail->Body = $tpl->getOutputContent();
					
					$mail->IsHTML(true);
					
					if(!$mail->Send()) {
						//return $mail->ErrorInfo;
					}
				}
			}
		break;
	}
}


?>
