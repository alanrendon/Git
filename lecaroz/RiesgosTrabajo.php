<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		
		case 'inicio':
			$tpl = new TemplatePower('plantillas/nom/RiesgosTrabajoInicio.tpl');
			$tpl->prepare();
			
			$tpl->assign('fecha1', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 7, date('Y'))));
			$tpl->assign('fecha2', date('d/m/Y'));
			
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
			
			$result = $db->query($sql);
			
			if ($result) {
				foreach ($result as $rec) {
					$tpl->newBlock('admin');
					$tpl->assign('value', $rec['value']);
					$tpl->assign('text', utf8_encode($rec['text']));
				}
			}
			
			$sql = '
				SELECT
					idcontador
						AS value,
					nombre_contador
						AS text
				FROM
					catalogo_contadores
				ORDER BY
					text
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				foreach ($result as $rec) {
					$tpl->newBlock('contador');
					$tpl->assign('value', $rec['value']);
					$tpl->assign('text', utf8_encode($rec['text']));
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'obtener_cia':
			$condiciones = array();
			
			$condiciones[] = 'num_cia = ' . $_REQUEST['num_cia'];
			
			$sql = '
				SELECT
					nombre_corto
						AS nombre_cia
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';
			
			$query = $db->query($sql);
			
			if ($query) {
				$data = array(
					'nombre_cia'   => utf8_encode($query[0]['nombre_cia']),
					'trabajadores' => array(
						array(
							'value'    => NULL,
							'text'     => NULL,
							'selected' => TRUE
						)
					)
				);
				
				$sql = '
					SELECT
						id
							AS value,
						\'[\' || LPAD(num_emp::varchar(5), 5, \'0\') || \'] \' || CONCAT_WS(\' \', ap_paterno, ap_materno, nombre)
							AS text,
						CASE
							WHEN num_afiliacion IS NULL OR TRIM(num_afiliacion) = \'\' THEN
								\'red\'
							ELSE
								NULL
						END
							AS class
					FROM
						catalogo_trabajadores
					WHERE
						num_cia = ' . $_REQUEST['num_cia'] . '
						AND fecha_baja IS NULL
						AND fecha_baja_imss IS NULL
						AND baja_rh IS NULL
					ORDER BY
						ap_paterno,
						ap_materno,
						nombre
				';
				
				$query = $db->query($sql);
				
				if ($query) {
					foreach ($query as $row) {
						$data['trabajadores'][] = array(
							'value' => $row['value'],
							'text'  => utf8_encode($row['text']),
							'class' => $row['class']
						);
					}
				}
				
				echo json_encode($data);
			}
			
			break;
		
		case 'consultar':
			$condiciones = array();
			
			$condiciones[] = 'rt.tsbaja IS NULL';
			
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
					$condiciones[] = 'rt.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] != '') {
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}
			
			if (isset($_REQUEST['contador']) && $_REQUEST['contador'] != '') {
				$condiciones[] = 'cc.idcontador = ' . $_REQUEST['contador'];
			}
			
			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
				|| (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
					&& (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
					$condiciones[] = 'rt.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				} else if (isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') {
					$condiciones[] = 'rt.fecha >= \'' . $_REQUEST['fecha1'] . '\'';
				} else if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '') {
					$condiciones[] = 'rt.fecha = \'' . $_REQUEST['fecha2'] . '\'';
				}
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
					$condiciones[] = 'rt.folio IN (' . implode(', ', $folios) . ')';
				}
			}
						
			$sql = '
				SELECT
					rt.idriesgotrabajo
						AS id,
					rt.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					rt.folio,
					rt.fecha,
					CONCAT_WS(\' \', ct.ap_paterno, ct.ap_materno, ct.nombre)
						AS nombre_trabajador
				FROM
					riesgos_trabajo rt
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_trabajadores ct
						ON (ct.id = rt.idtrabajador)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					rt.num_cia,
					rt.folio
			';
			
			$query = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/nom/RiesgosTrabajoConsultaRiesgos.tpl');
			$tpl->prepare();
			
			if ($query) {
				$num_cia = NULL;
				
				foreach ($query as $row) {
					if ($num_cia != $row['num_cia']) {
						$num_cia = $row['num_cia'];
						
						$tpl->newBlock('cia');
						
						$tpl->assign('num_cia', $row['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
					}
					
					$tpl->newBlock('row');
					
					$tpl->assign('id', $row['id']);
					$tpl->assign('folio', $row['folio']);
					$tpl->assign('fecha', $row['fecha']);
					$tpl->assign('nombre_trabajador', utf8_encode($row['nombre_trabajador']));
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
			
		case 'alta_riesgo':
			$tpl = new TemplatePower('plantillas/nom/RiesgosTrabajoAltaRiesgo.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_alta_riesgo':
			$sql = '
				INSERT INTO
					riesgos_trabajo (
						idalta,
						folio,
						num_cia,
						idtrabajador,
						tipo_identificacion,
						umf_adscripcion,
						delegacion_imss,
						dia_descanso_previo_accidente,
						hora_entrada,
						hora_salida,
						fecha_accidente,
						hora_accidente,
						fecha_servicio_medico,
						hora_servicio_medico,
						descripcion_accidente,
						descripcion_lesiones,
						impresion_diagnostica,
						tratamientos,
						intoxicacion_alcoholica,
						intoxicacion_enervantes,
						hubo_rina,
						nombre_servicio_medico_externo,
						amerita_incapacidad,
						fecha_inicio_incapacidad,
						folio_incapacidad,
						dias_incapacidad,
						nombre_servicio,
						nombre_medico,
						matricula_medico,
						unidad_medica,
						ocupacion_trabajador,
						antiguedad_trabajador,
						salario_diario,
						matricula_trabajador,
						clave_presupuestal_trabajador,
						fecha_suspenso,
						hora_suspenso,
						circunstancias_accidente,
						descripcion_area_trabajo,
						nombre_informante,
						cargo_informante,
						fecha_informe_accidente,
						hora_informe_accidente,
						informacion_testigos,
						informacion_autoridades,
						observaciones,
						nombre_representante_legal,
						lugar,
						fecha
					) VALUES (
						' . $_SESSION['iduser'] . ',
						(
							SELECT
								COALESCE(MAX(folio), 0) + 1
							FROM
								riesgos_trabajo
						),
						' . $_REQUEST['num_cia'] . ',
						' . $_REQUEST['idtrabajador'] . ',
						\'' . utf8_decode($_REQUEST['tipo_identificacion']) . '\',
						\'' . utf8_decode($_REQUEST['umf_adscripcion']) . '\',
						\'' . utf8_decode($_REQUEST['delegacion_imss']) . '\',
						\'' . utf8_decode($_REQUEST['dia_descanso_previo_accidente']) . '\',
						\'' . utf8_decode($_REQUEST['hora_entrada']) . '\',
						\'' . utf8_decode($_REQUEST['hora_salida']) . '\',
						' . ($_REQUEST['fecha_accidente'] != '' ? '\'' . $_REQUEST['fecha_accidente'] . '\'' : 'NULL') . ',
						\'' . utf8_decode($_REQUEST['hora_accidente']) . '\',
						' . ($_REQUEST['fecha_servicio_medico'] != '' ? '\'' . $_REQUEST['fecha_servicio_medico'] . '\'' : 'NULL') . ',
						\'' . utf8_decode($_REQUEST['hora_servicio_medico']) . '\',
						\'' . utf8_decode($_REQUEST['descripcion_accidente']) . '\',
						\'' . utf8_decode($_REQUEST['descripcion_lesiones']) . '\',
						\'' . utf8_decode($_REQUEST['impresion_diagnostica']) . '\',
						\'' . utf8_decode($_REQUEST['tratamientos']) . '\',
						' . $_REQUEST['intoxicacion_alcoholica'] . ',
						' . $_REQUEST['intoxicacion_enervantes'] . ',
						' . $_REQUEST['hubo_rina'] . ',
						\'' . utf8_decode($_REQUEST['nombre_servicio_medico_externo']) . '\',
						' . $_REQUEST['amerita_incapacidad'] . ',
						' . ($_REQUEST['fecha_inicio_incapacidad'] != '' ? '\'' . $_REQUEST['fecha_inicio_incapacidad'] . '\'' : 'NULL') . ',
						' . (get_val($_REQUEST['folio_incapacidad']) > 0 ? get_val($_REQUEST['folio_incapacidad']) : 'NULL') . ',
						' . get_val($_REQUEST['dias_incapacidad']) . ',
						\'' . utf8_decode($_REQUEST['nombre_servicio']) . '\',
						\'' . utf8_decode($_REQUEST['nombre_medico']) . '\',
						\'' . utf8_decode($_REQUEST['matricula_medico']) . '\',
						\'' . utf8_decode($_REQUEST['unidad_medica']) . '\',
						\'' . utf8_decode($_REQUEST['ocupacion_trabajador']) . '\',
						\'' . utf8_decode($_REQUEST['antiguedad_trabajador']) . '\',
						' . get_val($_REQUEST['salario_diario']) . ',
						\'' . utf8_decode($_REQUEST['matricula_trabajador']) . '\',
						\'' . utf8_decode($_REQUEST['clave_presupuestal_trabajador']) . '\',
						' . ($_REQUEST['fecha_suspenso'] != '' ? '\'' . $_REQUEST['fecha_suspenso'] . '\'' : 'NULL') . ',
						\'' . utf8_decode($_REQUEST['hora_suspenso']) . '\',
						' . $_REQUEST['circunstancias_accidente'] . ',
						\'' . utf8_decode($_REQUEST['descripcion_area_trabajo']) . '\',
						\'' . utf8_decode($_REQUEST['nombre_informante']) . '\',
						\'' . utf8_decode($_REQUEST['cargo_informante']) . '\',
						' . ($_REQUEST['fecha_informe_accidente'] != '' ? '\'' . $_REQUEST['fecha_informe_accidente'] . '\'' : 'NULL') . ',
						\'' . utf8_decode($_REQUEST['hora_informe_accidente']) . '\',
						\'' . utf8_decode($_REQUEST['informacion_testigos']) . '\',
						\'' . utf8_decode($_REQUEST['informacion_autoridades']) . '\',
						\'' . utf8_decode($_REQUEST['observaciones']) . '\',
						\'' . utf8_decode($_REQUEST['nombre_representante_legal']) . '\',
						\'' . utf8_decode($_REQUEST['lugar']) . '\',
						' . ($_REQUEST['fecha'] != '' ? '\'' . $_REQUEST['fecha'] . '\'' : 'NULL') . '
					)
			' . ";\n";
			
			$db->query($sql);
			
			$sql = '
				SELECT
					last_value
				FROM
					riesgos_trabajo_idriesgotrabajo_seq
			';
			
			$last = $db->query($sql);
			
			echo $last[0]['last_value'];
			
			break;
		
		case 'generar_reporte':
			$sql = '
				SELECT
					cc.razon_social
						AS nombre_cia,
					CONCAT_WS(\' \', cc.calle, cc.no_exterior, cc.no_interior)
						AS calle_cia,
					cc.colonia
						AS colonia_cia,
					CONCAT_WS(\', \', cc.colonia, cc.municipio, cc.estado)
						AS colonia2_cia,
					CONCAT_WS(\', \', cc.municipio, cc.estado)
						AS delegacion_cia,
					cc.codigo_postal
						AS codigo_postal_cia,
					cc.telefono
						AS telefono_cia,
					cc.email
						AS email_cia,
					cc.no_imss
						AS registro_patronal,
					ct.num_afiliacion
						AS num_seguridad_social,
					CONCAT_WS(\' \', ct.ap_paterno, ct.ap_materno, ct.nombre)
						AS nombre_trabajador,
					rt.tipo_identificacion,
					ct.curp,
					CASE
						WHEN ct.fecha_nac IS NOT NULL THEN
							EXTRACT(YEAR FROM AGE(COALESCE(rt.fecha, NOW()::DATE), ct.fecha_nac))
						ELSE
							NULL
					END
						AS edad,
					CASE
						WHEN ct.sexo = TRUE THEN
							\'F\'
						WHEN ct.sexo = FALSE THEN
							\'M\'
						ELSE
							NULL
					END
						AS sexo,
					CASE
						WHEN ct.estado_civil = 1 THEN
							\'SOLTERO\'
						WHEN ct.estado_civil = 2 THEN
							\'CASADO\'
						WHEN ct.estado_civil = 3 THEN
							\'VIUDO\'
						WHEN ct.estado_civil = 4 THEN
							\'SEPARADO\'
						WHEN ct.estado_civil = 5 THEN
							\'DIVORCIADO\'
						WHEN ct.estado_civil = 6 THEN
							\'UNION LIBRE\'
						ELSE
							\'SIN DEFINIR\'
					END
						AS estado_civil,
					ct.calle
						AS calle_trabajador,
					ct.colonia
						AS colonia_trabajador,
					CONCAT_WS(\', \', del_mun, entidad)
						AS delegacion_trabajador,
					ct.telefono_casa
						AS telefono_trabajador,
					ct.cod_postal
						AS codigo_postal_trabajador,
					rt.umf_adscripcion,
					rt.delegacion_imss,
					rt.dia_descanso_previo_accidente,
					CONCAT_WS(\' - \', rt.hora_entrada, rt.hora_salida)
						AS horario_trabajo,
					EXTRACT(DAY FROM rt.fecha_accidente)
						AS dia_accidente,
					EXTRACT(MONTH FROM rt.fecha_accidente)
						AS mes_accidente,
					EXTRACT(YEAR FROM rt.fecha_accidente)
						AS anio_accidente,
					rt.hora_accidente,
					EXTRACT(DAY FROM rt.fecha_servicio_medico)
						AS dia_servicio_medico,
					EXTRACT(MONTH FROM rt.fecha_servicio_medico)
						AS mes_servicio_medico,
					EXTRACT(YEAR FROM rt.fecha_servicio_medico)
						AS anio_servicio_medico,
					rt.hora_servicio_medico,
					rt.descripcion_accidente,
					rt.descripcion_lesiones,
					rt.impresion_diagnostica,
					rt.tratamientos,
					rt.intoxicacion_alcoholica,
					rt.intoxicacion_enervantes,
					rt.hubo_rina,
					rt.nombre_servicio_medico_externo,
					rt.nombre_servicio_medico_externo,
					rt.amerita_incapacidad,
					EXTRACT(DAY FROM rt.fecha_inicio_incapacidad)
						AS dia_inicio_incapacidad,
					EXTRACT(MONTH FROM rt.fecha_inicio_incapacidad)
						AS mes_inicio_incapacidad,
					EXTRACT(YEAR FROM rt.fecha_inicio_incapacidad)
						AS anio_inicio_incapacidad,
					rt.folio_incapacidad,
					rt.dias_incapacidad,
					rt.nombre_servicio,
					rt.nombre_medico,
					rt.matricula_medico,
					rt.unidad_medica,
					rt.ocupacion_trabajador,
					rt.antiguedad_trabajador,
					rt.salario_diario,
					rt.matricula_trabajador,
					rt.clave_presupuestal_trabajador,
					EXTRACT(DAY FROM rt.fecha_suspenso)
						AS dia_suspenso,
					EXTRACT(MONTH FROM rt.fecha_suspenso)
						AS mes_suspenso,
					EXTRACT(YEAR FROM rt.fecha_suspenso)
						AS anio_suspenso,
					rt.hora_suspenso,
					rt.circunstancias_accidente,
					rt.descripcion_area_trabajo,
					CONCAT_WS(\', \', rt.nombre_informante, rt.cargo_informante)
						AS nombre_informante,
					EXTRACT(DAY FROM rt.fecha_informe_accidente)
						AS dia_informe_accidente,
					EXTRACT(MONTH FROM rt.fecha_informe_accidente)
						AS mes_informe_accidente,
					EXTRACT(YEAR FROM rt.fecha_informe_accidente)
						AS anio_informe_accidente,
					rt.hora_informe_accidente,
					rt.informacion_testigos,
					rt.informacion_autoridades,
					rt.observaciones,
					rt.nombre_representante_legal,
					CONCAT_WS(\', \', rt.lugar, rt.fecha)
						AS lugar_fecha
				FROM
					riesgos_trabajo rt
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_trabajadores ct
						ON (ct.id = rt.idtrabajador)
				WHERE
					rt.idriesgotrabajo = ' . $_REQUEST['id'] . '
			';
			
			$query = $db->query($sql);
			
			if ($query) {
				$row = $query[0];
				
				require_once('includes/fpdf/fpdf.php');
				require_once('includes/fpdi/fpdi.php');
				
				$pdf = new FPDI();
				
				$pagecount = $pdf->setSourceFile('at_ST-7.pdf'); 
				
				$tplidx = $pdf->importPage(1, '/MediaBox'); 
				
				$pdf->addPage();
				$pdf->useTemplate($tplidx);
				
				$pdf->SetFont('Arial', '', 6);
				$pdf->SetTextColor(0, 0, 0);
				
				$pdf->SetXY(115, 22);
				$pdf->Write(0, $row['nombre_cia']);
				
				$pdf->SetXY(115, 29);
				$pdf->Write(0, $row['calle_cia']);
				
				$pdf->SetXY(115, 36);
				$pdf->Write(0, $row['colonia2_cia']);
				
				$pdf->SetXY(115, 43);
				$pdf->Write(0, $row['codigo_postal_cia']);
				
				$pdf->SetXY(153, 43);
				$pdf->Write(0, (strlen(trim($row['telefono_cia'])) <= 8 ? '(55) ' : '') . $row['telefono_cia']);
				
				$pdf->SetXY(115, 50);
				$pdf->Write(0, $row['registro_patronal']);
				
				$pdf->SetXY(10, 58);
				$pdf->Write(0, $row['num_seguridad_social']);
				
				$pdf->SetXY(71, 58);
				$pdf->Write(0, $row['nombre_trabajador']);
				
				$pdf->SetXY(10, 65);
				$pdf->Write(0, $row['tipo_identificacion']);
				
				$pdf->SetXY(107, 65);
				$pdf->Write(0, $row['curp']);
				
				$pdf->SetXY(185, 65);
				$pdf->Write(0, $row['edad']);
				
				if ($row['sexo'] != '') {
					$pdf->SetXY($row['sexo'] == 'M' ? 16 : 25, 72);
					$pdf->Write(0, 'X');
				}
				
				$pdf->SetXY(30, 72);
				$pdf->Write(0, $row['estado_civil']);
				
				$pdf->SetXY(57, 72);
				$pdf->Write(0, $row['calle_trabajador']);
				
				$pdf->SetXY(145, 72);
				$pdf->Write(0, $row['colonia_trabajador']);
				
				$pdf->SetXY(10, 79);
				$pdf->Write(0, $row['delegacion_trabajador']);
				
				$pdf->SetXY(94, 79);
				$pdf->Write(0, $row['telefono_trabajador']);
				
				$pdf->SetXY(145, 79);
				$pdf->Write(0, $row['codigo_postal_trabajador']);
				
				$pdf->SetXY(176, 79);
				$pdf->Write(0, $row['umf_adscripcion']);
				
				$pdf->SetXY(10, 92);
				$pdf->Write(0, $row['delegacion_imss']);
				
				$pdf->SetXY(36, 92);
				$pdf->Write(0, $row['dia_descanso_previo_accidente']);
				
				$pdf->SetXY(63, 92);
				$pdf->Write(0, $row['horario_trabajo']);
				
				$pdf->SetXY(92, 92);
				$pdf->Write(0, str_pad($row['dia_accidente'], 2, '0', STR_PAD_LEFT));
				
				$pdf->SetXY(106, 92);
				$pdf->Write(0, str_pad($row['mes_accidente'], 2, '0', STR_PAD_LEFT));
				
				$pdf->SetXY(120, 92);
				$pdf->Write(0, str_pad($row['anio_accidente'], 2, '0', STR_PAD_LEFT));
				
				$pdf->SetXY(134, 92);
				$pdf->Write(0, $row['hora_accidente']);
				
				$pdf->SetXY(150, 92);
				$pdf->Write(0, str_pad($row['dia_servicio_medico'], 2, '0', STR_PAD_LEFT));
				
				$pdf->SetXY(165, 92);
				$pdf->Write(0, str_pad($row['mes_servicio_medico'], 2, '0', STR_PAD_LEFT));
				
				$pdf->SetXY(179, 92);
				$pdf->Write(0, str_pad($row['anio_servicio_medico'], 2, '0', STR_PAD_LEFT));
				
				$pdf->SetXY(192, 92);
				$pdf->Write(0, $row['hora_servicio_medico']);
				
				$pdf->SetXY(10, 98);
				$pdf->MultiCell(192, 2, $row['descripcion_accidente']);
				
				$pdf->SetXY(10, 126);
				$pdf->MultiCell(192, 2, $row['descripcion_lesiones']);
				
				$pdf->SetXY(10, 154);
				$pdf->MultiCell(192, 2, $row['impresion_diagnostica']);
				
				$pdf->SetXY(10, 172);
				$pdf->MultiCell(192, 2, $row['tratamientos']);
				
				if ($row['intoxicacion_alcoholica'] != '') {
					$pdf->SetXY($row['intoxicacion_alcoholica'] == 't' ? 84 : 98, 196);
					$pdf->Write(0, 'X');
				}
				
				if ($row['intoxicacion_enervantes'] != '') {
					$pdf->SetXY($row['intoxicacion_enervantes'] == 't' ? 161 : 175, 196);
					$pdf->Write(0, 'X');
				}
				
				if ($row['hubo_rina'] != '') {
					$pdf->SetXY($row['hubo_rina'] == 't' ? 57 : 71, 204);
					$pdf->Write(0, 'X');
				}
				
				$pdf->SetXY(106, 205);
				$pdf->Write(0, $row['nombre_servicio_medico_externo']);
				
				if ($row['amerita_incapacidad'] != '') {
					$pdf->SetXY($row['amerita_incapacidad'] == 't' ? 43 : 57, 216);
					$pdf->Write(0, 'X');
				}
				
				$pdf->SetXY(67, 218);
				$pdf->Write(0, str_pad($row['dia_inicio_incapacidad'], 2, '0', STR_PAD_LEFT));
				
				$pdf->SetXY(81, 218);
				$pdf->Write(0, str_pad($row['mes_inicio_incapacidad'], 2, '0', STR_PAD_LEFT));
				
				$pdf->SetXY(95, 218);
				$pdf->Write(0, str_pad($row['anio_inicio_incapacidad'], 2, '0', STR_PAD_LEFT));
				
				$pdf->SetXY(110, 218);
				$pdf->Write(0, $row['folio_incapacidad']);
				
				$pdf->SetXY(150, 218);
				$pdf->Write(0, $row['dias_incapacidad']);
				
				$pdf->SetXY(165, 218);
				$pdf->Write(0, $row['nombre_servicio']);
				
				$pdf->SetXY(10, 229);
				$pdf->Write(0, $row['nombre_medico']);
				
				$pdf->SetXY(80, 229);
				$pdf->Write(0, $row['matricula_medico']);
				
				$pdf->SetXY(165, 229);
				$pdf->Write(0, $row['unidad_medica']);
				
				$tplidx = $pdf->importPage(2, '/MediaBox');
				
				$pdf->addPage();
				$pdf->useTemplate($tplidx);
				
				$pdf->SetRightMargin(5);
				
				$pdf->SetXY(10, 19);
				$pdf->Write(0, $row['nombre_cia']);
				
				$pdf->SetXY(156, 19);
				$pdf->Write(0, $row['registro_patronal']);
				
				$pdf->SetXY(10, 26);
				$pdf->Write(0, $row['calle_cia']);
				
				$pdf->SetXY(130, 26);
				$pdf->Write(0, $row['colonia_cia']);
				
				$pdf->SetXY(10, 33);
				$pdf->Write(0, $row['delegacion_cia']);
				
				$pdf->SetXY(88, 33);
				$pdf->Write(0, $row['codigo_postal_cia']);
				
				$pdf->SetXY(110, 33);
				$pdf->Write(0, (strlen(trim($row['telefono_cia'])) <= 8 ? '(55) ' : '') . $row['telefono_cia']);
				
				$pdf->SetXY(153, 33);
				$pdf->Write(0, $row['email_cia']);
				
				$pdf->SetXY(10, 40);
				$pdf->Write(0, $row['nombre_trabajador']);
				
				$pdf->SetXY(85, 40);
				$pdf->Write(0, $row['num_seguridad_social']);
				
				$pdf->SetXY(131, 40);
				$pdf->Write(0, $row['calle_trabajador']);
				
				$pdf->SetXY(10, 47);
				$pdf->Write(0, $row['colonia_trabajador']);
				
				$pdf->SetXY(96, 47);
				$pdf->Write(0, $row['delegacion_trabajador']);
				
				$pdf->SetXY(182, 47);
				$pdf->Write(0, $row['codigo_postal_trabajador']);
				
				$pdf->SetXY(10, 56);
				$pdf->Write(0, $row['ocupacion_trabajador']);
				
				$pdf->SetXY(85, 56);
				$pdf->Write(0, $row['antiguedad_trabajador']);
				
				$pdf->SetXY(126, 56);
				$pdf->Write(0, $row['dia_descanso_previo_accidente']);
				
				$pdf->SetXY(178, 56);
				$pdf->Write(0, $row['salario_diario'] > 0 ? number_format($row['salario_diario'], 2) : '');
				
				$pdf->SetXY(15, 66);
				$pdf->Write(0, $row['horario_trabajo']);
				
				$pdf->SetXY(74, 66);
				$pdf->Write(0, $row['matricula_trabajador']);
				
				$pdf->SetXY(117, 66);
				$pdf->Write(0, $row['clave_presupuestal_trabajador']);
				
				$pdf->SetXY(45, 73);
				$pdf->Write(0, str_pad($row['dia_accidente'], 2, '0', STR_PAD_LEFT));
				
				$pdf->SetXY(60, 73);
				$pdf->Write(0, str_pad($row['mes_accidente'], 2, '0', STR_PAD_LEFT));
				
				$pdf->SetXY(74, 73);
				$pdf->Write(0, str_pad($row['anio_accidente'], 2, '0', STR_PAD_LEFT));
				
				$pdf->SetXY(89, 73);
				$pdf->Write(0, $row['hora_accidente']);
				
				$pdf->SetXY(150, 73);
				$pdf->Write(0, str_pad($row['dia_suspenso'], 2, '0', STR_PAD_LEFT));
				
				$pdf->SetXY(165, 73);
				$pdf->Write(0, str_pad($row['mes_suspenso'], 2, '0', STR_PAD_LEFT));
				
				$pdf->SetXY(179, 73);
				$pdf->Write(0, str_pad($row['anio_suspenso'], 2, '0', STR_PAD_LEFT));
				
				$pdf->SetXY(192, 73);
				$pdf->Write(0, $row['hora_suspenso']);
				
				if ($row['circunstancias_accidente'] > 0) {
					switch ($row['circunstancias_accidente']) {
						
						case 1:
							$pos = 49;
							break;
						
						case 2:
							$pos = 79;
							break;
						
						case 3:
							$pos = 119;
							break;
						
						case 4:
							$pos = 160;
							break;
						
						case 5:
							$pos = 200;
						
					}
					
					$pdf->SetXY($pos, 81);
					$pdf->Write(0, 'X');
				}
				
				$pdf->SetXY(10, 86);
				$pdf->MultiCell(192, 2, $row['descripcion_area_trabajo']);
				
				$pdf->SetXY(10, 133);
				$pdf->Write(0, $row['nombre_informante']);
				
				$pdf->SetXY(136, 133);
				$pdf->Write(0, str_pad($row['dia_informe_accidente'], 2, '0', STR_PAD_LEFT));
				
				$pdf->SetXY(156, 133);
				$pdf->Write(0, str_pad($row['mes_informe_accidente'], 2, '0', STR_PAD_LEFT));
				
				$pdf->SetXY(173, 133);
				$pdf->Write(0, str_pad($row['anio_informe_accidente'], 2, '0', STR_PAD_LEFT));
				
				$pdf->SetXY(192, 133);
				$pdf->Write(0, $row['hora_informe_accidente']);
				
				$pdf->SetXY(10, 138);
				$pdf->MultiCell(192, 2, $row['informacion_testigos']);
				
				$pdf->SetXY(10, 151);
				$pdf->Write(0, $row['nombre_servicio_medico_externo']);
				
				$pdf->SetXY(10, 158);
				$pdf->Write(0, $row['informacion_autoridades']);
				
				$pdf->SetXY(10, 163);
				$pdf->MultiCell(192, 2, $row['observaciones']);
				
				$pdf->SetXY(10, 180);
				$pdf->Write(0, $row['nombre_representante_legal']);
				
				$pdf->SetXY(110, 180);
				$pdf->Write(0, $row['lugar_fecha']);
				
				$pdf->Output('/var/www/lecaroz/riesgos/riesgo_' . $_REQUEST['id'] . '.pdf', 'F');
			}
			
			echo $_REQUEST['id'];
			
			break;
		
		case 'ver_reporte':
			header('Content-type: application/pdf');
			header('Content-Disposition: inline; filename="riesgo-trabajo-id-' . $_REQUEST['id'] . '.pdf"');
			
			readfile('/var/www/lecaroz/riesgos/riesgo_' . $_REQUEST['id'] . '.pdf');
			
			break;
		
		case 'descargar_reporte':
			header('Content-type: application/pdf');
			header('Content-Disposition: attachment; filename="riesgo-trabajo-id-' . $_REQUEST['id'] . '.pdf"');
			
			readfile('/var/www/lecaroz/riesgos/riesgo_' . $_REQUEST['id'] . '.pdf');
			
			break;
		
		case 'modificar_riesgo':
			$sql = '
				SELECT
					rt.idriesgotrabajo
						AS id,
					rt.num_cia,
					cc.razon_social
						AS nombre_cia,
					rt.idtrabajador,
					rt.tipo_identificacion,
					rt.umf_adscripcion,
					rt.delegacion_imss,
					rt.dia_descanso_previo_accidente,
					rt.hora_entrada,
					rt.hora_salida,
					rt.fecha_accidente,
					rt.hora_accidente,
					rt.fecha_servicio_medico,
					rt.hora_servicio_medico,
					rt.descripcion_accidente,
					rt.descripcion_lesiones,
					rt.impresion_diagnostica,
					rt.tratamientos,
					rt.intoxicacion_alcoholica,
					rt.intoxicacion_enervantes,
					rt.hubo_rina,
					rt.nombre_servicio_medico_externo,
					rt.amerita_incapacidad,
					rt.fecha_inicio_incapacidad,
					rt.folio_incapacidad,
					rt.dias_incapacidad,
					rt.nombre_servicio,
					rt.nombre_medico,
					rt.matricula_medico,
					rt.unidad_medica,
					rt.ocupacion_trabajador,
					rt.antiguedad_trabajador,
					rt.salario_diario,
					rt.matricula_trabajador,
					rt.clave_presupuestal_trabajador,
					rt.fecha_suspenso,
					rt.hora_suspenso,
					rt.circunstancias_accidente,
					rt.descripcion_area_trabajo,
					rt.nombre_informante,
					rt.cargo_informante,
					rt.fecha_informe_accidente,
					rt.hora_informe_accidente,
					rt.informacion_testigos,
					rt.informacion_autoridades,
					rt.observaciones,
					rt.nombre_representante_legal,
					rt.lugar,
					rt.fecha
				FROM
					riesgos_trabajo rt
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_trabajadores ct
						ON (ct.id = rt.idtrabajador)
				WHERE
					rt.idriesgotrabajo = ' . $_REQUEST['id'] . '
			';
			
			$query = $db->query($sql);
			
			if ($query) {
				$row = $query[0];
				
				$tpl = new TemplatePower('plantillas/nom/RiesgosTrabajoModificarRiesgo.tpl');
				$tpl->prepare();
				
				foreach ($row as $key => $value) {
					if (in_array($key, array(
						'intoxicacion_alcoholica',
						'intoxicacion_enervantes',
						'hubo_rina',
						'amerita_incapacidad',
						'circunstancias_accidente'))) {
						$tpl->assign($key . '_' . $value, ' checked="checked"');
					} else if (in_array($key, array(
						'salario_diario'
					))) {
						$tpl->assign($key, $value > 0 ? number_format($value, 2) : '');
					} else if (in_array($key, array(
						'dias_incapacidad'
					))) {
						$tpl->assign($key, $value > 0 ? $value : '');
					} else {
						$tpl->assign($key, utf8_encode($value));
					}
				}
				
				$data = array(
					'nombre_cia'   => utf8_encode($query[0]['nombre_cia']),
					'trabajadores' => array(
						array(
							'value'    => NULL,
							'text'     => NULL,
							'selected' => TRUE
						)
					)
				);
				
				$sql = '
					SELECT
						id
							AS value,
						\'[\' || LPAD(num_emp::varchar(5), 5, \'0\') || \'] \' || CONCAT_WS(\' \', ap_paterno, ap_materno, nombre)
							AS text,
						CASE
							WHEN num_afiliacion IS NULL OR TRIM(num_afiliacion) = \'\' THEN
								\'red\'
							ELSE
								NULL
						END
							AS class
					FROM
						catalogo_trabajadores
					WHERE
						num_cia = ' . $row['num_cia'] . '
						AND fecha_baja IS NULL
						AND fecha_baja_imss IS NULL
						AND baja_rh IS NULL
					ORDER BY
						ap_paterno,
						ap_materno,
						nombre
				';
				
				$query = $db->query($sql);
				
				if ($query) {
					foreach ($query as $opt) {
						$tpl->newBlock('trabajador');
						$tpl->assign('value', $opt['value']);
						$tpl->assign('text', utf8_encode($opt['text']));
						$tpl->assign('class', $opt['class'] != '' ? ' class="' . $opt['class'] . '"' : '');
						$tpl->assign('selected', $opt['value'] == $row['idtrabajador'] ? ' selected="selected"' : '');
					}
				}
				
				echo $tpl->getOutputContent();
			}
			
			break;
		
		case 'do_modificar_riesgo':
			$sql = '
				UPDATE
					riesgos_trabajo
				SET
					tsmod = NOW(),
					idmod = ' . $_SESSION['iduser'] . ',
					num_cia = ' . $_REQUEST['num_cia'] . ',
					idtrabajador = ' . $_REQUEST['idtrabajador'] . ',
					tipo_identificacion = \'' . utf8_decode($_REQUEST['tipo_identificacion']) . '\',
					umf_adscripcion = \'' . utf8_decode($_REQUEST['umf_adscripcion']) . '\',
					delegacion_imss = \'' . utf8_decode($_REQUEST['delegacion_imss']) . '\',
					dia_descanso_previo_accidente = \'' . utf8_decode($_REQUEST['dia_descanso_previo_accidente']) . '\',
					hora_entrada = \'' . utf8_decode($_REQUEST['hora_entrada']) . '\',
					hora_salida = \'' . utf8_decode($_REQUEST['hora_salida']) . '\',
					fecha_accidente = ' . ($_REQUEST['fecha_accidente'] != '' ? '\'' . $_REQUEST['fecha_accidente'] . '\'' : 'NULL') . ',
					hora_accidente = \'' . utf8_decode($_REQUEST['hora_accidente']) . '\',
					fecha_servicio_medico = ' . ($_REQUEST['fecha_servicio_medico'] != '' ? '\'' . $_REQUEST['fecha_servicio_medico'] . '\'' : 'NULL') . ',
					hora_servicio_medico = \'' . utf8_decode($_REQUEST['hora_servicio_medico']) . '\',
					descripcion_accidente = \'' . utf8_decode($_REQUEST['descripcion_accidente']) . '\',
					descripcion_lesiones = \'' . utf8_decode($_REQUEST['descripcion_lesiones']) . '\',
					impresion_diagnostica = \'' . utf8_decode($_REQUEST['impresion_diagnostica']) . '\',
					tratamientos = \'' . utf8_decode($_REQUEST['tratamientos']) . '\',
					intoxicacion_alcoholica = ' . $_REQUEST['intoxicacion_alcoholica'] . ',
					intoxicacion_enervantes = ' . $_REQUEST['intoxicacion_enervantes'] . ',
					hubo_rina = ' . $_REQUEST['hubo_rina'] . ',
					nombre_servicio_medico_externo = \'' . utf8_decode($_REQUEST['nombre_servicio_medico_externo']) . '\',
					amerita_incapacidad = ' . $_REQUEST['amerita_incapacidad'] . ',
					fecha_inicio_incapacidad = ' . ($_REQUEST['fecha_inicio_incapacidad'] != '' ? '\'' . $_REQUEST['fecha_inicio_incapacidad'] . '\'' : 'NULL') . ',
					folio_incapacidad = ' . (get_val($_REQUEST['folio_incapacidad']) > 0 ? get_val($_REQUEST['folio_incapacidad']) : 'NULL') . ',
					dias_incapacidad = ' . get_val($_REQUEST['dias_incapacidad']) . ',
					nombre_servicio = \'' . utf8_decode($_REQUEST['nombre_servicio']) . '\',
					nombre_medico = \'' . utf8_decode($_REQUEST['nombre_medico']) . '\',
					matricula_medico = \'' . utf8_decode($_REQUEST['matricula_medico']) . '\',
					unidad_medica = \'' . utf8_decode($_REQUEST['unidad_medica']) . '\',
					ocupacion_trabajador = \'' . utf8_decode($_REQUEST['ocupacion_trabajador']) . '\',
					antiguedad_trabajador = \'' . utf8_decode($_REQUEST['antiguedad_trabajador']) . '\',
					salario_diario = ' . get_val($_REQUEST['salario_diario']) . ',
					matricula_trabajador = \'' . utf8_decode($_REQUEST['matricula_trabajador']) . '\',
					clave_presupuestal_trabajador = \'' . utf8_decode($_REQUEST['clave_presupuestal_trabajador']) . '\',
					fecha_suspenso = ' . ($_REQUEST['fecha_suspenso'] != '' ? '\'' . $_REQUEST['fecha_suspenso'] . '\'' : 'NULL') . ',
					hora_suspenso = \'' . utf8_decode($_REQUEST['hora_suspenso']) . '\',
					circunstancias_accidente = ' . $_REQUEST['circunstancias_accidente'] . ',
					descripcion_area_trabajo = \'' . utf8_decode($_REQUEST['descripcion_area_trabajo']) . '\',
					nombre_informante = \'' . utf8_decode($_REQUEST['nombre_informante']) . '\',
					cargo_informante = \'' . utf8_decode($_REQUEST['cargo_informante']) . '\',
					fecha_informe_accidente = ' . ($_REQUEST['fecha_informe_accidente'] != '' ? '\'' . $_REQUEST['fecha_informe_accidente'] . '\'' : 'NULL') . ',
					hora_informe_accidente = \'' . utf8_decode($_REQUEST['hora_informe_accidente']) . '\',
					informacion_testigos = \'' . utf8_decode($_REQUEST['informacion_testigos']) . '\',
					informacion_autoridades = \'' . utf8_decode($_REQUEST['informacion_autoridades']) . '\',
					observaciones = \'' . utf8_decode($_REQUEST['observaciones']) . '\',
					nombre_representante_legal = \'' . utf8_decode($_REQUEST['nombre_representante_legal']) . '\',
					lugar = \'' . utf8_decode($_REQUEST['lugar']) . '\',
					fecha = ' . ($_REQUEST['fecha'] != '' ? '\'' . $_REQUEST['fecha'] . '\'' : 'NULL') . '
				WHERE
					idriesgotrabajo = ' . $_REQUEST['id'] . '
			' . ";\n";
			
			$db->query($sql);
			
			echo $_REQUEST['id'];
			
			break;
		
		case 'do_baja_riesgo':
			$sql = '
				UPDATE
					riesgos_trabajo
				SET
					tsbaja = NOW(),
					idbaja = ' . $_SESSION['iduser'] . '
				WHERE
					idriesgotrabajo = ' . $_REQUEST['id'] . '
			' . ";\n";
			
			$db->query($sql);
			
			break;
		
		case 'info_riesgo':
			$sql = '
				SELECT
					rt.idriesgotrabajo
						AS id,
					CONCAT_WS(\' \', rt.num_cia, cc.razon_social)
						AS cia,
					CONCAT_WS(\' \', ct.ap_paterno, ct.ap_materno, ct.nombre)
						AS trabajador,
					rt.tipo_identificacion,
					rt.umf_adscripcion,
					rt.delegacion_imss,
					rt.dia_descanso_previo_accidente,
					rt.hora_entrada,
					rt.hora_salida,
					CONCAT_WS(\' \', rt.fecha_accidente, rt.hora_accidente)
						AS fecha_accidente,
					CONCAT_WS(\' \', rt.fecha_servicio_medico, rt.hora_servicio_medico)
						AS fecha_servicio_medico,
					rt.descripcion_accidente,
					rt.descripcion_lesiones,
					rt.impresion_diagnostica,
					rt.tratamientos,
					CASE
						WHEN rt.intoxicacion_alcoholica = TRUE THEN
							\'SI\'
						WHEN rt.intoxicacion_alcoholica = FALSE THEN
							\'NO\'
						ELSE
							\'&nbsp;\'
					END
						AS intoxicacion_alcoholica,
					CASE
						WHEN rt.intoxicacion_enervantes = TRUE THEN
							\'SI\'
						WHEN rt.intoxicacion_enervantes = FALSE THEN
							\'NO\'
						ELSE
							\'&nbsp;\'
					END
						AS intoxicacion_enervantes,
					CASE
						WHEN rt.hubo_rina = TRUE THEN
							\'SI\'
						WHEN rt.hubo_rina = FALSE THEN
							\'NO\'
						ELSE
							\'&nbsp;\'
					END
						AS hubo_rina,
					rt.nombre_servicio_medico_externo,
					CASE
						WHEN rt.amerita_incapacidad = TRUE THEN
							\'SI\'
						WHEN rt.amerita_incapacidad = FALSE THEN
							\'NO\'
						ELSE
							\'&nbsp;\'
					END
						AS amerita_incapacidad,
					rt.fecha_inicio_incapacidad,
					rt.folio_incapacidad,
					rt.dias_incapacidad,
					rt.nombre_servicio,
					rt.nombre_medico,
					rt.matricula_medico,
					rt.unidad_medica,
					rt.ocupacion_trabajador,
					rt.antiguedad_trabajador,
					rt.salario_diario,
					rt.matricula_trabajador,
					rt.clave_presupuestal_trabajador,
					CONCAT_WS(\' \', rt.fecha_suspenso, rt.hora_suspenso)
						AS fecha_suspenso,
					CASE
						WHEN rt.circunstancias_accidente = 1 THEN
							\'EN LA EMPRESA\'
						WHEN rt.circunstancias_accidente = 2 THEN
							\'EN UNA COMISION\'
						WHEN rt.circunstancias_accidente = 3 THEN
							\'EN TRAYECTO A SU TRABAJO\'
						WHEN rt.circunstancias_accidente = 4 THEN
							\'EN TRAYECTO A SU DOMICILIO\'
						WHEN rt.circunstancias_accidente = 5 THEN
							\'TRABAJANDO TIEMPO EXTRA\'
						ELSE
							\'&nbsp;\'
					END
						AS circunstancias_accidente,
					rt.descripcion_area_trabajo,
					CONCAT_WS(\', \', rt.nombre_informante, rt.cargo_informante)
						AS nombre_informante,
					CONCAT_WS(\' \', rt.fecha_informe_accidente, rt.hora_informe_accidente)
						AS fecha_informe_accidente,
					rt.informacion_testigos,
					rt.informacion_autoridades,
					rt.observaciones,
					rt.nombre_representante_legal,
					CONCAT_WS(\' \', rt.lugar, rt.fecha)
						AS lugar_fecha
				FROM
					riesgos_trabajo rt
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_trabajadores ct
						ON (ct.id = rt.idtrabajador)
				WHERE
					rt.idriesgotrabajo = ' . $_REQUEST['id'] . '
			';
			
			$query = $db->query($sql);
			
			if ($query) {
				$row = $query[0];
				
				$tpl = new TemplatePower('plantillas/nom/RiesgosTrabajoInfoRiesgo.tpl');
				$tpl->prepare();
				
				foreach ($row as $key => $value) {
					if (in_array($key, array(
						'salario_diario'
					))) {
						$tpl->assign($key, $value > 0 ? number_format($value, 2) : '');
					} else if (in_array($key, array(
						'dias_incapacidad'
					))) {
						$tpl->assign($key, $value > 0 ? $value : '');
					} else {
						$tpl->assign($key, utf8_encode($value));
					}
				}
				
				$sql = '
					SELECT
						idincapacidad
							AS id,
						fecha,
						fecha_inicio_incapacidad,
						dias_incapacidad,
						folio_incapacidad
					FROM
						incapacidades
					WHERE
						idriesgotrabajo = ' . $_REQUEST['id'] . '
						AND tipo_incapacidad = 1
						AND tsbaja IS NULL
					ORDER BY
						idincapacidad
				';
				
				$query = $db->query($sql);
				
				if ($query) {
					foreach ($query as $row) {
						$tpl->newBlock('incapacidad');
						$tpl->assign('id', $row['id']);
						$tpl->assign('fecha', $row['fecha']);
						$tpl->assign('fecha_inicio_incapacidad', $row['fecha_inicio_incapacidad']);
						$tpl->assign('dias_incapacidad', $row['dias_incapacidad']);
						$tpl->assign('folio_incapacidad', $row['folio_incapacidad']);
					}
				}
				
				echo $tpl->getOutputContent();
			}
			
			break;
		
		case 'do_alta_incapacidad':
			$sql = '
				INSERT INTO
					incapacidades (
						idalta,
						idriesgotrabajo,
						fecha,
						fecha_inicio_incapacidad,
						dias_incapacidad,
						folio_incapacidad,
						tipo_incapacidad
					) VALUES (
						' . $_SESSION['iduser'] . ',
						' . $_REQUEST['id'] . ',
						\'' . $_REQUEST['fecha_nueva_incapacidad'] . '\',
						\'' . $_REQUEST['fecha_inicio_nueva_incapacidad'] . '\',
						' . get_val($_REQUEST['dias_nueva_incapacidad']) . ',
						\'' . utf8_decode($_REQUEST['folio_nueva_incapacidad']) . '\',
						1
					)
			' . ";\n";
			
			$db->query($sql);
			
			$sql = '
				SELECT
					last_value
				FROM
					incapacidades_idincapacidad_seq
			';
			
			$last = $db->query($sql);
			
			echo $_REQUEST['id'];
			
			break;
		
		case 'do_baja_incapacidad':
			$sql = '
				UPDATE
					incapacidades
				SET
					tsbaja = NOW(),
					idbaja = ' . $_SESSION['iduser'] . '
				WHERE
					idincapacidad = ' . $_REQUEST['id'] . '
			';
			
			$db->query($sql);
			
			$sql = '
				SELECT
					idriesgotrabajo
						AS id
				FROM
					incapacidades
				WHERE
					idincapacidad = ' . $_REQUEST['id'] . '
			';
			
			$query = $db->query($sql);
			
			echo $query[0]['id'];
			
			break;
		
		case 'digitalizar_incapacidad':
			$tpl = new TemplatePower('plantillas/nom/RiesgosTrabajoDigitalizarIncapacidad.tpl');
			$tpl->prepare();
			
			$tpl->assign('host', $_SERVER['SERVER_ADDR']);
			$tpl->assign('idincapacidad', $_REQUEST['id']);
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'guardar_imagen':
			$img = pg_escape_bytea(file_get_contents($_FILES['image']['tmp_name'][0]));
			
			$db_scans = new DBclass('pgsql://mollendo:pobgnj@127.0.0.1:5432/scans', 'autocommit=yes');
			
			$sql = '
				INSERT INTO
					incapacidades
						(
							idincapacidad
							img,
							idalta
						)
				VALUES
					(
						' . $_REQUEST['idincapacidad'] . ',
						\'' . $img . '\',
						' . $_SESSION['iduser'] . '
					);
			';
			
			$db_scans->query($sql);
			
			break;
		
		case 'mostrar_imagen_miniatura':
			$db_scans = new DBclass('pgsql://mollendo:pobgnj@127.0.0.1:5432/scans', 'autocommit=yes');
			
			$sql = '
				SELECT
					img
				FROM
					incapacidades
				WHERE
					idincapacidad = ' . $_REQUEST['id'] . '
					AND tsbaja IS NULL
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
			} else {
				header('Content-Type: image/jpeg');
				
				readfile('imagenes/no_imagen_incapacidad.jpg');
			}
			
			break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/nom/RiesgosTrabajo.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
