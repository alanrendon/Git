<?php
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';
include 'includes/dbstatus.php';

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
	1 =>  'ENERO',
	2 =>  'FEBRERO',
	3 =>  'MARZO',
	4 =>  'ABRIL',
	5 =>  'MAYO',
	6 =>  'JUNIO',
	7 =>  'JULIO',
	8 =>  'AGOSTO',
	9 =>  'SEPTIEMBRE',
	10 => 'OCTUBRE',
	11 => 'NOVIEMBRE',
	12 => 'DICIEMBRE'
);

function clean($value) {
	$value = trim(str_replace('"', '', $value));
	
	return $value != '' ? $value : NULL;
}

function prepare($value) {
	return $value !== NULL ? "'$value'" : 'NULL';
}

function toInt($var) {
	return intval($var, 10);
}

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die('MODIFICANDO');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'obtenerCia':
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
				echo $result[0]['nombre_corto'];
			}
		break;
		
		case 'cargarDatos':
			$num_cia = $_REQUEST['num_cia'];
			
			list($fecha1, $fecha2) = array(date('d/m/Y', mktime(0, 0, 0, 12, 31, $_REQUEST['anio'])), date('d/m/Y', mktime(0, 0, 0, 12, 31, $_REQUEST['anio'])));
			
			list($dia1, $mes1, $anio1) = array_map('toInt', explode('/', $fecha1));
			list($dia2, $mes2, $anio2) = array_map('toInt', explode('/', $fecha2));
			
			$data1 = file($_FILES['archivo1']['tmp_name'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			$data2 = file($_FILES['archivo2']['tmp_name'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			$data3 = file($_FILES['archivo3']['tmp_name'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			
			if (count(explode(',', $data1[0])) < 20) {
				$tpl = new TemplatePower('plantillas/nom/ReporteAguinaldoError.tpl');
				$tpl->prepare();
				
				$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');
				
				$tpl->assign('error', 'El "Archivo1" contiene errores y no puede ser procesado');
				
				$tpl->printToScreen();
				
				die;
			}
			else if (count(explode(',', $data2[0])) < 4) {
				$tpl = new TemplatePower('plantillas/nom/ReporteAguinaldoError.tpl');
				$tpl->prepare();
				
				$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');
				
				$tpl->assign('error', 'El "Archivo2" contiene errores y no puede ser procesado');
				
				$tpl->printToScreen();
				
				die;
			}
			else if (count(explode(',', $data3[0])) < 2) {
				$tpl = new TemplatePower('plantillas/nom/ReporteAguinaldoError.tpl');
				$tpl->prepare();
				
				$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');
				
				$tpl->assign('error', 'El "Archivo3" contiene errores y no puede ser procesado');
				
				$tpl->printToScreen();
				
				die;
			}
			
			$horario1 = array(
				'X' => NULL,
				'M' => '06:00 A 14:00',
				'V' => '14:00 A 21:30',
				'N' => '22:00 A 05:00'
			);
			
			$horario2 = array(
				'X' => NULL,
				'M' => 'MATUTINO',
				'V' => 'VESPERTINO'
			);
			
			$puesto = array(
				NULL => NULL
			);
			
			foreach ($data3 as $value) {
				$fields = array_map('clean', explode(',', $value));
				
				$puesto[$fields[0]] = $fields[1];
			}
			
			$data = array();
			
			for ($i = 0; $i < count($data1); $i++) {
				$fields = array_merge(array_map('clean', explode(',', $data1[$i])), array_map('clean', explode(',', $data2[$i])));
				
				if ($fields[3] != '') {
					list($dia_ingreso, $mes_ingreso, $anio_ingreso) = array_map('toInt', explode('/', $fields[3]));
					
					$anio_ingreso = $anio_ingreso < 38 ? $anio_ingreso + 2000 : $anio_ingreso + 1900;
					
					$fields[3] = "$dia_ingreso/$mes_ingreso/$anio_ingreso";
				}
				else {
					list($dia_ingreso, $mes_ingreso, $anio_ingreso) = array_map('toInt', explode('/', $fecha1));
				}
				
				$data[] = array(
					'num_cia'             => intval($num_cia),
					'fecha1'              => $fecha1,
					'fecha2'              => $fecha2,
					'clave'               => intval($fields[0]),
					'nombre'              => $fields[1],
					'puesto'              => $puesto[$fields[2]],
					'fecha_alta'          => $fields[3],
					'curp'                => $fields[4],
					'num_afiliacion'      => $fields[5],
					'salario_diario'      => floatval($fields[6]),
					'salario_integrado'   => floatval($fields[7]),
					'horario'             => intval($num_cia) < 900 ? $horario1[$fields[8]] : $horario2[$fields[8]],
					'asistencia0'         => NULL,
					'asistencia1'         => NULL,
					'asistencia2'         => NULL,
					'asistencia3'         => NULL,
					'asistencia4'         => NULL,
					'asistencia5'         => NULL,
					'asistencia6'         => NULL,
					'dias_trabajados'     => 0,
					'faltas'              => 0,
					'incapacidades'       => 0,
					'sueldo_semanal'      => floatval($fields[12]),
					'prima_dominical'     => floatval($fields[13]),
					'vacaciones'          => intval($fields[14]),
					'prima_vacacional'    => floatval($fields[15]),
					'total_percepciones'  => floatval($fields[16]),
					'isr'                 => floatval($fields[17]),
					'subsidio_al_empleo'  => abs(floatval($fields[18])),
					'credito_infonavit'   => floatval($fields[19]),
					'pension_alimenticia' => floatval($fields[20]),
					'imss'                => floatval($fields[21]),
					'total_deducciones'   => abs(floatval($fields[22])),
					'total'               => floatval($fields[23]),
					'idins'               => intval($_SESSION['iduser'])
				);
			}
			
			$sql = '';
			
			foreach ($data as $d) {
				$sql .= 'INSERT INTO reporte_nomina_tmp (' . implode(', ', array_keys($d)) . ') VALUES (' . implode(', ', array_map('prepare', $d)) . ')' . ";\n";
			}
			
			$db->query($sql);
			
			$sql = '
				SELECT
					num_cia,
					cc.razon_social
						AS nombre_cia,
					cc.rfc
						AS rfc_cia,
					no_imss,
					EXTRACT(year FROM fecha1)
						AS anio,
					clave,
					rn.nombre,
					puesto,
					horario,
					fecha_alta,
					rn.curp,
					num_afiliacion,
					salario_diario,
					salario_integrado,
					sueldo_semanal,
					prima_dominical,
					vacaciones,
					prima_vacacional,
					total_percepciones,
					isr,
					subsidio_al_empleo,
					credito_infonavit,
					pension_alimenticia,
					imss,
					total_deducciones,
					total
				FROM
					reporte_nomina_tmp rn
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					idins = ' . $_SESSION['iduser'] . '
				ORDER BY
					clave
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/nom/ReporteAguinaldoDatos.tpl');
			$tpl->prepare();
			
			$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');
			
			$tpl->assign('num_cia', $result[0]['num_cia']);
			$tpl->assign('nombre_cia', $result[0]['nombre_cia']);
			$tpl->assign('rfc_cia', $result[0]['rfc_cia']);
			$tpl->assign('no_imss', $result[0]['no_imss']);
			$tpl->assign('anio', $result[0]['anio']);
			$tpl->assign('empleados', number_format(count($result), 0, '', ','));
			
			$row_color = FALSE;
			
			$ok = TRUE;
			
			$totales = array(
				'sueldo_semanal'      => 0,
				'subsidio_al_empleo'  => 0,
				'prima_dominical'     => 0,
				'prima_vacacional'    => 0,
				'total_percepciones'  => 0,
				'credito_infonavit'   => 0,
				'isr'                 => 0,
				'imss'                => 0,
				'pension_alimenticia' => 0,
				'total_deducciones'   => 0,
				'total'               => 0
			);
			
			foreach ($result as $rec) {
				$tpl->newBlock('row');
				
				if ($rec['nombre'] == ''/* || $rec['curp'] == ''*/ || $rec['puesto'] == '' || $rec['fecha_alta'] == '' || $rec['num_afiliacion'] == '' || $rec['horario'] == '') {
					$tpl->assign('error', ' style="background-color:#FF9;"');
					
					$ok = FALSE;
				}
				
				$tpl->assign('row_color', $row_color ? 'on' : 'off');
				
				$row_color = !$row_color;
				
				foreach ($rec as $key => $value) {
					if (in_array($key, array('num_cia', 'nombre_cia', 'rfc_cia', 'no_imss', 'fecha1', 'fecha2'))) {
						continue;
					}
					else if (in_array($key, array('clave', 'nombre', 'fecha_alta', 'curp', 'num_afiliacion', 'puesto', 'horario'))) {
						$tpl->assign($key, $value);
					}
					else if (in_array($key, array('dias_trabajados', 'faltas', 'incapacidades'))) {
						$tpl->assign($key, $value);
					}
					else {
						$tpl->assign($key, round(abs($value), 2) != 0 ? number_format(abs($value), 2, '.', ',') : '&nbsp;');
					}
					
					if (in_array($key, array_keys($totales))) {
						$totales[$key] += $value;
					}
				}
				
				foreach ($totales as $key => $value) {
					$tpl->assign('_ROOT.' . $key, number_format($value, 2, '.', ','));
				}
			}
			
			if (!$ok) {
				$tpl->assign('_ROOT.disabled', ' disabled');
			}
			
			$tpl->printToScreen();
		break;
		
		case 'registrar':
			$sql = '
				SELECT
					COALESCE(MAX(folio) + 1, 1)
						AS folio
				FROM
					reporte_nomina
			';
			
			$result = $db->query($sql);
			
			$folio = $result[0]['folio'];
			
			$sql = '
				INSERT INTO
					reporte_nomina
						(
							folio,
							num_cia,
							fecha1,
							fecha2,
							clave,
							nombre,
							puesto,
							horario,
							asistencia0,
							asistencia1,
							asistencia2,
							asistencia3,
							asistencia4,
							asistencia5,
							asistencia6,
							fecha_alta,
							curp,
							num_afiliacion,
							salario_diario,
							salario_integrado,
							dias_trabajados,
							faltas,
							incapacidades,
							sueldo_semanal,
							prima_dominical,
							prima_vacacional,
							total_percepciones,
							isr,
							subsidio_al_empleo,
							credito_infonavit,
							pension_alimenticia,
							imss,
							total_deducciones,
							total,
							status,
							idins,
							tsins
						)
					SELECT
						' . $folio . ',
						num_cia,
						fecha1,
						fecha2,
						clave,
						nombre,
						puesto,
						horario,
						asistencia0,
						asistencia1,
						asistencia2,
						asistencia3,
						asistencia4,
						asistencia5,
						asistencia6,
						fecha_alta,
						curp,
						num_afiliacion,
						salario_diario,
						salario_integrado,
						dias_trabajados,
						faltas,
						incapacidades,
						sueldo_semanal,
						prima_dominical,
						prima_vacacional,
						total_percepciones,
						isr,
						subsidio_al_empleo,
						credito_infonavit,
						pension_alimenticia,
						imss,
						total_deducciones,
						total,
						1,
						idins,
						now()
					FROM
						reporte_nomina_tmp
					WHERE
						idins = ' . $_SESSION['iduser'] . '
			' . ";\n";
			
			$db->query($sql);
			
			$sql = '
				SELECT
					num_cia,
					cc.razon_social
						AS nombre_cia,
					cc.rfc
						AS rfc_cia,
					no_imss,
					EXTRACT(year FROM fecha1)
						AS anio
				FROM
					reporte_nomina rn
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					folio = ' . $folio . '
				GROUP BY
					num_cia,
					nombre_cia,
					rfc_cia,
					no_imss,
					anio
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/nom/ReporteAguinaldoFin.tpl');
			$tpl->prepare();
			
			$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');
			
			$tpl->assign('folio', $folio);
			
			$tpl->assign('num_cia', $result[0]['num_cia']);
			$tpl->assign('nombre_cia', $result[0]['nombre_cia']);
			$tpl->assign('rfc_cia', $result[0]['rfc_cia']);
			$tpl->assign('no_imss', $result[0]['no_imss']);
			$tpl->assign('anio', $result[0]['anio']);
			
			$tpl->printToScreen();
		break;
		
		case 'pdf':
			$sql = '
				SELECT
					num_cia,
					cc.razon_social
						AS nombre_cia,
					cc.rfc
						AS rfc_cia,
					no_imss,
					EXTRACT(year FROM fecha1)
						AS anio,
					clave,
					rn.nombre,
					puesto,
					fecha_alta,
					rn.curp,
					num_afiliacion,
					horario,
					asistencia0,
					asistencia1,
					asistencia2,
					asistencia3,
					asistencia4,
					asistencia5,
					asistencia6,
					salario_diario,
					salario_integrado,
					dias_trabajados,
					faltas,
					incapacidades,
					sueldo_semanal,
					prima_dominical,
					vacaciones,
					prima_vacacional,
					total_percepciones,
					isr,
					subsidio_al_empleo,
					credito_infonavit,
					pension_alimenticia,
					imss,
					total_deducciones,
					total
				FROM
					reporte_nomina rn
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					folio = ' . $_REQUEST['folio'] . '
				ORDER BY
					clave
			';
			
			$result = $db->query($sql);
			
			if (!class_exists('FPDF')) {
				include_once('includes/fpdf/fpdf.php');
			}
			
			$anio = $result[0]['anio'];
			
			class PDF extends FPDF {
				function Header() {
					global $result, $anio, $_meses;
					
					$this->SetMargins(0, 5, 12);
					
					$this->SetFont('ARIAL', 'BU', 8);
					
					$this->Cell(100, 0, utf8_decode($result[0]['nombre_cia']));
					
					$this->Ln(4);
					
					$this->SetFont('ARIAL', 'B', 8);
					
					$this->Cell(35, 0, 'R.F.C.:');
					$this->Cell(45, 0, utf8_decode($result[0]['rfc_cia']));
					
					$this->Ln(4);
					
					$this->Cell(35, 0, 'REG. I.M.S.S.:');
					$this->Cell(45, 0, $result[0]['no_imss']);
					
					$this->Cell(35, 0, 'PERIODO DE PAGO:');
					
					$this->Cell(0, 0, 'AGUINALDO DICIEMBRE ' . $anio);
					
					$this->Ln(4);
					
					$this->SetFont('ARIAL', 'BU', 8);
					
					$this->Text(255, 10, $result[0]['num_cia']);
					
					$this->SetFont('ARIAL', 'B', 6);
					
					$this->Text(165, 10, 'S.D.=SALARIO DIARIO');
					$this->Text(165, 12, 'S.D.I.=SALARIO DIARIO INTEGRADO');
					
					$this->Text(210, 10, 'F=FIJO');
					$this->Text(210, 12, 'V=VARIABLE');
					$this->Text(210, 14, 'M=MIXTO');
					
					$this->Ln(5);
					
					$this->SetFont('ARIAL', 'B', 5);
					
					$this->Cell(106, 3, '', 0, 0);
					$this->Cell(43, 3, 'PERCEPCIONES', 1, 0, 'C');
					$this->Cell(42, 3, 'DEDUCCIONES', 1, 0, 'C');
					$this->Cell(80, 3, '', 0, 0);
					$this->Ln();
					
					$this->Cell(4, 7, 'NO.', 1, 0, 'C');
					$this->Cell(45, 7, 'NOMBRE DEL EMPLEADO', 1, 0, 'C');
					$this->Cell(10, 7, "INGRESO", 1, 0, 'C');
					$this->Cell(22, 7, 'CURP', 1, 0, 'C');
					$this->Cell(13, 7, "N.S.S.", 1, 0, 'C');
					$this->Cell(12, 7, 'HORARIO', 1, 0, 'C');
					
					$this->Cell(7, 7, 'S.D.', 1, 0, 'C');
					$this->Cell(7, 7, 'S.D.I.', 1, 0, 'C');
					$this->Cell(5, 7, 'TIPO', 1, 0, 'C');
					$this->Cell(12, 7, 'SUELDO', 1, 0, 'C');
					$this->Cell(12, 7, 'TOTAL', 1, 0, 'C');
					$this->Cell(10, 7, 'I.S.R.', 1, 0, 'C');
					$this->Cell(10, 7, "SUB.EMP.", 1, 0, 'C');
					$this->Cell(10, 7, 'I.M.S.S.', 1, 0, 'C');
					$this->Cell(12, 7, 'TOTAL', 1, 0, 'C');
					$this->Cell(12, 7, "NETO", 1, 0, 'C');
					$this->Cell(37, 7, '', 1, 0, 'C');
					$this->Cell(30, 7, 'FIRMA', 1, 0, 'C');
					
					$this->Ln();
				}
				
				function Footer() {
					$this->SetY(-7);
					$this->SetFont('Arial', '', 6);
					$this->Cell(0, 10, 'PAGINA ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
				}
			}
			
			$pdf = new PDF('L', 'mm', 'Letter');
			
			$pdf->AliasNbPages();
			
			$pdf->SetDisplayMode('fullwidth', 'single');
			
			$pdf->SetMargins(0, 5, 12);
			
			$pdf->SetAutoPageBreak(FALSE);
			
			$pdf->AddPage('L', 'Letter');
			
			$totales = array(
				'sueldo_semanal'      => 0,
				'subsidio_al_empleo'  => 0,
				'prima_dominical'     => 0,
				'prima_vacacional'    => 0,
				'total_percepciones'  => 0,
				'credito_infonavit'   => 0,
				'isr'                 => 0,
				'imss'                => 0,
				'pension_alimenticia' => 0,
				'total_deducciones'   => 0,
				'total'               => 0
			);
			
			$leyenda1 = utf8_decode("RECIBI LA CANTIDAD SEÑALADA EN EL APARTADO");
			$leyenda2 = utf8_decode('CORRESPONDIENTE, MANIFESTANDO BAJO');
			$leyenda3 = utf8_decode('PROTESTA DE DECIR VERDAD QUE NO SE ME');
			$leyenda4 = utf8_decode('ADEUDA CANTIDAD ALGUNA POR NINGUN');
			$leyenda5 = utf8_decode('CONCEPTO Y QUE EN ESTE PERIODO HE');
			$leyenda6 = utf8_decode('LABORADO DENTRO DEL HORARIO MAXIMO LEGAL');
			$leyenda7 = utf8_decode('ESTABLECIDO.');
			
			$row = 0;
			
			foreach ($result as $rec) {
				$pdf->SetFont('ARIAL', '', 5);
				
				$pdf->Cell(4, 12, $rec['clave'], 1, 0, 'R');
				
				$pdf->SetFont('ARIAL', 'B', 5);
				
				$pdf->Cell(45, 12, $rec['nombre'], 1, 0, 'L');
				
				$pdf->SetFont('ARIAL', '', 5);
				
				$pdf->Cell(10, 12, $rec['fecha_alta'], 1, 0, 'C');
				$pdf->Cell(22, 12, $rec['curp'], 1, 0, 'C');
				$pdf->Cell(13, 12, $rec['num_afiliacion'], 1, 0, 'C');
				$pdf->Cell(12, 12, $rec['horario'], 1, 0, 'C');
				$pdf->Cell(7, 12, number_format($rec['salario_diario'], 2, '.', ','), 1, 0, 'R');
				$pdf->Cell(7, 12, number_format($rec['salario_integrado'], 2, '.', ','), 1, 0, 'R');
				$pdf->Cell(5, 12, 'F', 1, 0, 'C');
				$pdf->Cell(12, 12, number_format($rec['sueldo_semanal'], 2, '.', ','), 1, 0, 'R');
				
				$pdf->SetFont('ARIAL', 'B', 5);
				
				$pdf->Cell(12, 12, number_format($rec['total_percepciones'], 2, '.', ','), 1, 0, 'R');
				
				$pdf->SetFont('ARIAL', '', 5);
				
				$pdf->Cell(10, 12, $rec['isr'] != 0 ? number_format($rec['isr'], 2, '.', ',') : '', 1, 0, 'R');
				$pdf->Cell(10, 12, $rec['subsidio_al_empleo'] != 0 ? number_format(abs($rec['subsidio_al_empleo']), 2, '.', ',') : '', 1, 0, 'R');
				$pdf->Cell(10, 12, $rec['imss'] != 0 ? number_format($rec['imss'], 2, '.', ',') : '', 1, 0, 'R');
				
				$pdf->SetFont('ARIAL', 'B', 5);
				
				$pdf->Cell(12, 12, number_format($rec['total_deducciones'], 2, '.', ','), 1, 0, 'R');
				
				$pdf->Cell(12, 12, number_format($rec['total'], 2, '.', ','), 1, 0, 'R');
				$pdf->Cell(37, 12, '', 1, 0, 'L');
				$pdf->Cell(30, 12, '', 1, 0, 'L');
				
				$pdf->Text(5, 35 + (12 * $row + 8), 'PUESTO: '. $rec['puesto']);
				
				$pdf->SetFont('ARIAL', '', 4);
				
				$pdf->Text(204, 26 + (12 * $row + 8), $leyenda1);
				$pdf->Text(204, 27.5 + (12 * $row + 8), $leyenda2);
				$pdf->Text(204, 29 + (12 * $row + 8), $leyenda3);
				$pdf->Text(204, 30.5 + (12 * $row + 8), $leyenda4);
				$pdf->Text(204, 32 + (12 * $row + 8), $leyenda5);
				$pdf->Text(204, 33.5 + (12 * $row + 8), $leyenda6);
				$pdf->Text(204, 35 + (12 * $row + 8), $leyenda7);
				
				if ($row < 14) {
					$pdf->Ln();
					
					$row++;
				}
				else {
					$row = 0;
					
					$pdf->AddPage('L', 'Letter');
					$pdf->SetMargins(0, 5, 12);
				}
				
				$totales['sueldo_semanal'] += $rec['sueldo_semanal'];
				$totales['subsidio_al_empleo'] += abs($rec['subsidio_al_empleo']);
				$totales['total_percepciones'] += $rec['total_percepciones'];
				$totales['isr'] += $rec['isr'];
				$totales['imss'] += $rec['imss'];
				$totales['total_deducciones'] += $rec['total_deducciones'];
				$totales['total'] += $rec['total'];
			}
			
			$pdf->SetFont('ARIAL', 'B', 5);
			
			$pdf->Cell(49, 7, 'EMPLEADOS: ' . count($result), 1, 0, 'L');
			$pdf->Cell(76, 7, 'TOTALES', 1, 0, 'R');
			$pdf->Cell(12, 7, number_format($totales['sueldo_semanal'], 2, '.', ','), 1, 0, 'R');
			$pdf->Cell(12, 7, number_format($totales['total_percepciones'], 2, '.', ','), 1, 0, 'R');
			
			$pdf->Cell(10, 7, number_format($totales['isr'], 2, '.', ','), 1, 0, 'R');
			$pdf->Cell(10, 7, number_format($totales['subsidio_al_empleo'], 2, '.', ','), 1, 0, 'R');
			$pdf->Cell(10, 7, number_format($totales['imss'], 2, '.', ','), 1, 0, 'R');
			$pdf->Cell(12, 7, number_format($totales['total_deducciones'], 2, '.', ','), 1, 0, 'R');
			$pdf->Cell(12, 7, number_format($totales['total'], 2, '.', ','), 1, 0, 'R');
			$pdf->Cell(67, 7, '', 1, 0);
			
			$pdf->Output('reporte_aguinaldo_' . $_REQUEST['folio'] . '.pdf', 'I');
			
		break;
	}
	
	die;
}

$sql = '
	DELETE FROM
		reporte_nomina_tmp
	WHERE
		idins = ' . $_SESSION['iduser'] . '
';

$db->query($sql);

$tpl = new TemplatePower('plantillas/nom/ReporteAguinaldoInicio.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y'));

$tpl->printToScreen();
?>
