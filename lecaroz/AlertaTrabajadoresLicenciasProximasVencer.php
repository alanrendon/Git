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
			$sql = "
				SELECT
					id
				FROM
					catalogo_trabajadores ct
				WHERE
					fecha_baja IS NULL
					AND empleado_especial IS NULL
					AND baja_rh IS NULL
					AND fecha_vencimiento_licencia_manejo BETWEEN NOW()::DATE AND NOW()::DATE + INTERVAL '30 DAYS'
					" . ($_SESSION['iduser'] > 1 ? ('AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899')) : '') . "
				LIMIT
					1
			";
			$result = $db->query($sql);

			if ($result && in_array($_SESSION['iduser'], array(4, 28, 34, 40, 52, 53, 54, 59, 58, 61))) {
				echo 1;
			}
		break;

		case 'listado':
			$sql = "
				SELECT
					ct.id,
					ct.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					ca.nombre_administrador
						AS admin,
					num_emp,
					TRIM(regexp_replace(COALESCE(ct.ap_paterno, '') || ' ' || COALESCE(ct.ap_materno, '') || ' ' || COALESCE(ct.nombre, ''), '\s+', ' ', 'g'))
						AS empleado,
					fecha_vencimiento_licencia_manejo
						AS vencimiento
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_administradores ca
						USING (idadministrador)
				WHERE
					fecha_baja IS NULL
					AND empleado_especial IS NULL
					AND baja_rh IS NULL
					AND fecha_vencimiento_licencia_manejo BETWEEN NOW()::DATE AND NOW()::DATE + INTERVAL '30 DAYS'
					" . ($_SESSION['iduser'] > 1 ? ('AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899')) : '') . "
				ORDER BY
					admin,
					num_cia,
					empleado
			";
			$result = $db->query($sql);

			if ($result && in_array($_SESSION['iduser'], array(1, 4, 28, 34, 40, 52, 53, 54, 59, 58, 61))) {
				$tpl = new TemplatePower('plantillas/AlertaTrabajadoresLicenciasProximasVencer.tpl');
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
					$tpl->assign('num_emp', $rec['num_emp']);
					$tpl->assign('empleado', utf8_encode($rec['empleado']));
					$tpl->assign('vencimiento', $rec['vencimiento']);
				}

				$tpl->printToScreen();
			}
		break;

		case 'email':
			$sql = "
				SELECT
					ct.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					cc.email
						AS email_cia,
					num_emp,
					TRIM(regexp_replace(COALESCE(ct.ap_paterno, '') || ' ' || COALESCE(ct.ap_materno, '') || ' ' || COALESCE(ct.nombre, ''), '\s+', ' ', 'g'))
						AS empleado,
					fecha_vencimiento_licencia_manejo
						AS vencimiento
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_administradores ca
						USING (idadministrador)
				WHERE
					fecha_baja IS NULL
					AND empleado_especial IS NULL
					AND baja_rh IS NULL
					AND fecha_vencimiento_licencia_manejo BETWEEN NOW()::DATE AND NOW()::DATE + INTERVAL '30 DAYS'
					" . ($_SESSION['iduser'] > 1 ? ('AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899')) : '') . "
				ORDER BY
					num_cia,
					empleado
			";
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

							// $mail->AddBCC('carlos.candelario@lecaroz.com');

							$mail->Subject = '[' . $num_cia . ' ' . $nombre_cia . '] Trabajadores sin contrato firmado (URGENTE) [' . date('d/m/Y H:i:s') . ']';

							$mail->Body = $tpl->getOutputContent();

							$mail->IsHTML(true);

							if(!$mail->Send()) {
								//return $mail->ErrorInfo;
							}
						}

						$num_cia = $rec['num_cia'];
						$nombre_cia = $rec['nombre_cia'];
						$email_cia = $rec['email_cia'];

						$tpl = new TemplatePower('plantillas/nom/EmailLicenciasProximasVencer.tpl');
						$tpl->prepare();

						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $rec['nombre_cia']);
					}

					$tpl->newBlock('row');
					$tpl->assign('num_emp', $rec['num_emp']);
					$tpl->assign('nombre', $rec['empleado']);
					$tpl->assign('vencimiento', $rec['vencimiento']);
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

					// $mail->AddBCC('carlos.candelario@lecaroz.com');

					$mail->Subject = '[' . $num_cia . ' ' . $nombre_cia . '] Licencias de choferes próximas a vencer (URGENTE) [' . date('d/m/Y H:i:s') . ']';

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
