<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{
		case 'verificar':
			$result = $db->query("SELECT
				id
			FROM
				catalogo_trabajadores ct
			WHERE
				fecha_baja IS NULL
				AND empleado_especial IS NULL
				AND (
					doc_acta_nacimiento = FALSE
					OR doc_comprobante_domicilio = FALSE
					OR doc_curp = FALSE
					OR doc_ife = FALSE
					OR doc_num_seguro_social = FALSE
					OR doc_solicitud_trabajo = FALSE
					OR doc_comprobante_estudios = FALSE
					OR doc_referencias = FALSE
					OR doc_no_antecedentes_penales = FALSE
					OR (
						cod_puestos IN (5, 915)
						AND doc_licencia_manejo = FALSE
					)
					OR doc_rfc = FALSE
					OR doc_no_adeudo_infonavit = FALSE
				)
				" . ($_SESSION['iduser'] > 1 ? ('AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899')) : '') . "
			LIMIT
				1");

			if ($result && in_array($_SESSION['iduser'], array(40, 52, 53, 54, 59, 58, 61)))
			{
				echo 1;
			}

			break;

		case 'listado':
			$result = $db->query("SELECT
				ct.id,
				ct.num_cia,
				cc.nombre_corto AS nombre_cia,
				ca.nombre_administrador AS admin,
				ct.num_emp,
				ct.nombre_completo AS empleado,
				ct.doc_acta_nacimiento AS an,
				ct.doc_comprobante_domicilio AS cd,
				ct.doc_curp AS cu,
				ct.doc_ife AS if,
				ct.doc_num_seguro_social AS ss,
				ct.doc_solicitud_trabajo AS st,
				ct.doc_comprobante_estudios AS ce,
				ct.doc_referencias AS rl,
				ct.doc_no_antecedentes_penales AS na,
				CASE
					WHEN ct.cod_puestos IN (5, 915) AND ct.doc_licencia_manejo = FALSE THEN
						FALSE
					ELSE
						TRUE
				END AS lm,
				ct.doc_rfc AS rf,
				ct.doc_no_adeudo_infonavit AS in
			FROM
				catalogo_trabajadores ct
				LEFT JOIN catalogo_companias cc USING (num_cia)
				LEFT JOIN catalogo_administradores ca USING (idadministrador)
			WHERE
				ct.fecha_baja IS NULL
				AND ct.empleado_especial IS NULL
				AND (
					ct.doc_acta_nacimiento = FALSE
					OR ct.doc_comprobante_domicilio = FALSE
					OR ct.doc_curp = FALSE
					OR ct.doc_ife = FALSE
					OR ct.doc_num_seguro_social = FALSE
					OR ct.doc_solicitud_trabajo = FALSE
					OR ct.doc_comprobante_estudios = FALSE
					OR ct.doc_referencias = FALSE
					OR ct.doc_no_antecedentes_penales = FALSE
					OR (
						ct.cod_puestos IN (5, 915)
						AND ct.doc_licencia_manejo = FALSE
					)
					OR ct.doc_rfc = FALSE
					OR ct.doc_no_adeudo_infonavit = FALSE
				)
				" . ($_SESSION['iduser'] > 1 ? ('AND ct.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899')) : '') . "
			ORDER BY
				admin,
				ct.num_cia,
				empleado");

			if ($result && in_array($_SESSION['iduser'], array(1, 40, 52, 53, 54, 59, 58, 61)))
			{
				$tpl = new TemplatePower('plantillas/AlertaTrabajadoresDocumentosFaltantes.tpl');
				$tpl->prepare();

				$admin = NULL;
				foreach ($result as $rec)
				{
					if ($admin != $rec['admin'])
					{
						if ($admin != NULL)
						{
							$tpl->assign('admin.salto', '<br style="page-break-after:always;" />');
						}

						$admin = $rec['admin'];

						$tpl->newBlock('admin');
						$tpl->assign('admin', utf8_encode($rec['admin']));

						$num_cia = NULL;
					}
					if ($num_cia != $rec['num_cia'])
					{
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

					$documentos_faltantes = array();

					if ($rec['an'] == 'f')
					{
						$documentos_faltantes[] = '<a href="javascript:void(0);" id="documentos_faltantes" class="enlace green" data-tooltip="Acta de nacimiento">AN</a>';
					}
					if ($rec['cd'] == 'f')
					{
						$documentos_faltantes[] = '<a href="javascript:void(0);" id="documentos_faltantes" class="enlace blue" data-tooltip="Comprobante de domicilio">CD</a>';
					}
					if ($rec['if'] == 'f')
					{
						$documentos_faltantes[] = '<a href="javascript:void(0);" id="documentos_faltantes" class="enlace yellow" data-tooltip="Credencial del IFE">IF</a>';
					}
					if ($rec['rf'] == 'f')
					{
						$documentos_faltantes[] = '<a href="javascript:void(0);" id="documentos_faltantes" class="enlace red" data-tooltip="RFC">RF</a>';
					}
					if ($rec['cu'] == 'f')
					{
						$documentos_faltantes[] = '<a href="javascript:void(0);" id="documentos_faltantes" class="enlace purple" data-tooltip="CURP">CU</a>';
					}
					if ($rec['ss'] == 'f')
					{
						$documentos_faltantes[] = '<a href="javascript:void(0);" id="documentos_faltantes" class="enlace orange" data-tooltip="N&uacute;mero de seguro social">SS</a>';
					}
					if ($rec['st'] == 'f')
					{
						$documentos_faltantes[] = '<a href="javascript:void(0);" id="documentos_faltantes" class="enlace black" data-tooltip="Solicitud de trabajo">ST</a>';
					}
					if ($rec['ce'] == 'f')
					{
						$documentos_faltantes[] = '<a href="javascript:void(0);" id="documentos_faltantes" class="enlace aqua" data-tooltip="Comprobante de estudios">CE</a>';
					}
					if ($rec['rl'] == 'f')
					{
						$documentos_faltantes[] = '<a href="javascript:void(0);" id="documentos_faltantes" class="enlace light_gray" data-tooltip="Referencias laborales">RL</a>';
					}
					if ($rec['na'] == 'f')
					{
						$documentos_faltantes[] = '<a href="javascript:void(0);" id="documentos_faltantes" class="enlace dark_gray" data-tooltip="Carta de no antecedentes penales">NA</a>';
					}
					if ($rec['lm'] == 'f')
					{
						$documentos_faltantes[] = '<a href="javascript:void(0);" id="documentos_faltantes" class="enlace green" data-tooltip="Licencia de manejo">LM</a>';
					}
					if ($rec['in'] == 'f')
					{
						$documentos_faltantes[] = '<a href="javascript:void(0);" id="documentos_faltantes" class="enlace blue" data-tooltip="Carta de no adeudo a Infonavit">IN</a>';
					}

					$tpl->assign('documentos_faltantes', $documentos_faltantes ? implode(', ', $documentos_faltantes) : '&nbsp;');
				}

				$tpl->printToScreen();
			}

			break;

		case 'email':
			$result = $db->query("SELECT
				ct.id,
				ct.num_cia,
				cc.nombre_corto AS nombre_cia,
				cc.email AS email_cia,
				ca.nombre_administrador AS admin,
				ca.email AS email_admin,
				ct.num_emp,
				ct.nombre_completo AS empleado,
				ct.doc_acta_nacimiento AS an,
				ct.doc_comprobante_domicilio AS cd,
				ct.doc_curp AS cu,
				ct.doc_ife AS if,
				ct.doc_num_seguro_social AS ss,
				ct.doc_solicitud_trabajo AS st,
				ct.doc_comprobante_estudios AS ce,
				ct.doc_referencias AS rl,
				ct.doc_no_antecedentes_penales AS na,
				CASE
					WHEN ct.cod_puestos IN (5, 915) AND ct.doc_licencia_manejo = FALSE THEN
						FALSE
					ELSE
						TRUE
				END AS lm,
				ct.doc_rfc AS rf,
				ct.doc_no_adeudo_infonavit AS in
			FROM
				catalogo_trabajadores ct
				LEFT JOIN catalogo_companias cc USING (num_cia)
				LEFT JOIN catalogo_administradores ca USING (idadministrador)
			WHERE
				ct.fecha_baja IS NULL
				AND ct.empleado_especial IS NULL
				AND (
					ct.doc_acta_nacimiento = FALSE
					OR ct.doc_comprobante_domicilio = FALSE
					OR ct.doc_curp = FALSE
					OR ct.doc_ife = FALSE
					OR ct.doc_num_seguro_social = FALSE
					OR ct.doc_solicitud_trabajo = FALSE
					OR ct.doc_comprobante_estudios = FALSE
					OR ct.doc_referencias = FALSE
					OR ct.doc_no_antecedentes_penales = FALSE
					OR (
						ct.cod_puestos IN (5, 915)
						AND ct.doc_licencia_manejo = FALSE
					)
					OR ct.doc_rfc = FALSE
					OR ct.doc_no_adeudo_infonavit = FALSE
				)
				" . ($_SESSION['iduser'] > 1 ? ('AND ct.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899')) : '') . "
			ORDER BY
				num_cia,
				empleado");

			if ($result)
			{
				if ( ! class_exists('PHPMailer'))
				{
					include_once('includes/phpmailer/class.phpmailer.php');
				}

				$num_cia = NULL;

				foreach ($result as $rec)
				{
					if ($num_cia != $rec['num_cia'])
					{
						if ($num_cia != NULL)
						{
							$mail = new PHPMailer();

							$mail->IsSMTP();
							$mail->Host = 'mail.lecaroz.com';
							$mail->Port = 587;
							$mail->SMTPAuth = true;
							$mail->Username = 'mollendo@lecaroz.com';
							$mail->Password = 'L3c4r0z*';

							$mail->From = 'mollendo@lecaroz.com';
							$mail->FromName = 'Lecaroz :: Recursos Humanos';

							if ($email_cia != '')
							{
								$mail->AddAddress($email_cia);
							}

							$mail->AddCC('olga.espinoza@lecaroz.com');

							// $mail->AddBCC('sistemas@lecaroz.com');
							// $mail->AddBCC('carlos.candelario@lecaroz.com');

							$mail->Subject = '[' . $num_cia . ' ' . $nombre_cia . '] Documentos faltantes de trabajadores [' . date('d/m/Y H:i:s') . ']';

							$mail->Body = $tpl->getOutputContent();

							$mail->IsHTML(true);

							if( ! $mail->Send())
							{
								echo "\n[" . date('Y-m-d H:i:s') . "] **Error al enviar correo de la compañía {$num_cia}: " . $mail->ErrorInfo;
								//return $mail->ErrorInfo;
							}
							else
							{
								echo "\n[" . date('Y-m-d H:i:s') . "] @@Correo enviado con exito de la compañía {$num_cia}";
							}
						}

						$num_cia = $rec['num_cia'];
						$nombre_cia = $rec['nombre_cia'];

						$email_cia = $rec['email_cia'];
						$email_admin = $rec['email_admin'];

						$tpl = new TemplatePower('plantillas/nom/EmailDocumentosFaltantes.tpl');
						$tpl->prepare();

						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $rec['nombre_cia']);
					}

					$tpl->newBlock('row');

					$tpl->assign('num_emp', $rec['num_emp']);
					$tpl->assign('nombre', $rec['empleado']);

					$documentos_faltantes = array();

					if ($rec['an'] == 'f')
					{
						$documentos_faltantes[] = '<li>Acta de nacimiento</li>';
					}
					if ($rec['cd'] == 'f')
					{
						$documentos_faltantes[] = '<li>Comprobante de domicilio</li>';
					}
					if ($rec['if'] == 'f')
					{
						$documentos_faltantes[] = '<li>Credencial del IFE</li>';
					}
					if ($rec['rf'] == 'f')
					{
						$documentos_faltantes[] = '<li>RFC</li>';
					}
					if ($rec['cu'] == 'f')
					{
						$documentos_faltantes[] = '<li>CURP</li>';
					}
					if ($rec['ss'] == 'f')
					{
						$documentos_faltantes[] = '<li>N&uacute;mero de seguro social</li>';
					}
					if ($rec['st'] == 'f')
					{
						$documentos_faltantes[] = '<li>Solicitud de trabajo</li>';
					}
					if ($rec['ce'] == 'f')
					{
						$documentos_faltantes[] = '<li>Comprobante de estudios</li>';
					}
					if ($rec['rl'] == 'f')
					{
						$documentos_faltantes[] = '<li>Referencias laborales</li>';
					}
					if ($rec['na'] == 'f')
					{
						$documentos_faltantes[] = '<li>Carta de no antecedentes penales</li>';
					}
					if ($rec['lm'] == 'f')
					{
						$documentos_faltantes[] = '<li>Licencia de manejo</li>';
					}
					if ($rec['in'] == 'f')
					{
						$documentos_faltantes[] = '<li>Carta de no adeudo a Infonavit</li>';
					}

					$tpl->assign('documentos', implode($documentos_faltantes));
				}

				if ($num_cia != NULL)
				{
					$mail = new PHPMailer();

					$mail->IsSMTP();
					$mail->Host = 'mail.lecaroz.com';
					$mail->Port = 587;
					$mail->SMTPAuth = true;
					$mail->Username = 'mollendo@lecaroz.com';
					$mail->Password = 'L3c4r0z*';

					$mail->From = 'mollendo@lecaroz.com';
					$mail->FromName = 'Lecaroz :: Recursos Humanos';

					if ($email_cia != '')
					{
						$mail->AddAddress($email_cia);
					}

					$mail->AddCC('olga.espinoza@lecaroz.com');

					// $mail->AddBCC('sistemas@lecaroz.com');
					// $mail->AddBCC('carlos.candelario@lecaroz.com');

					$mail->Subject = '[' . $num_cia . ' ' . $nombre_cia . '] Documentos faltantes de trabajadores [' . date('d/m/Y H:i:s') . ']';

					$mail->Body = $tpl->getOutputContent();

					$mail->IsHTML(true);

					if( ! $mail->Send())
					{
						echo "\n[" . date('Y-m-d H:i:s') . "] **Error al enviar correo de la compañía {$num_cia}: " . $mail->ErrorInfo;
						//return $mail->ErrorInfo;
					}
					else
					{
						echo "\n[" . date('Y-m-d H:i:s') . "] @@Correo enviado con exito de la compañía {$num_cia}";
					}
				}
			}

			break;
	}
}
