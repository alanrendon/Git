<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function clean($value)
{
	$value = trim(str_replace('"', '', $value));

	return $value != '' ? $value : NULL;
}

function prepare($value)
{
	return $value !== NULL ? "'$value'" : 'NULL';
}

function toInt($value)
{
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

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{

		case 'obtener_cia':
			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';

			$result = $db->query($sql);

			if ($result)
			{
				echo utf8_encode($result[0]['nombre_corto']);
			}

			break;

		case 'obtener_periodos':
			$periodos = array();

			if ($_REQUEST['num_cia'] < 900)
			{
				for ($i = 1; $i <= 20; $i++)
				{//echo "<br>" . date('Y-m-d', strtotime('next saturday - ' . $i . ' week + 6 days')) . " - " . date('Y-m-d', strtotime('next saturday - ' . $i . ' week'));
					if ( ! in_array($_REQUEST['num_cia'], array(700, 800)))
					{
						if (date('Y', strtotime('next saturday - ' . $i . ' week + 6 days')) > date('Y', strtotime('next saturday - ' . $i . ' week')))
						{
							$semana = 1;

							if (date('Y', strtotime('next saturday - ' . $i . ' week + 6 days')) < 2017)
							{
								$periodos[] = array(
									'value' => '01/01/' . date('Y', strtotime('next saturday - ' . $i . ' week + 6 days')) . '|' . date('d/m/Y', strtotime('next saturday - ' . $i . ' week + 6 days')) . '|' . $semana,
									'text'  => 'SEMANA 1 DEL 01/01/' . date('Y', strtotime('next saturday - ' . $i . ' week + 6 days')) . ' AL ' . date('d/m/Y', strtotime('next saturday - ' . $i . ' week + 6 days'))
								);
							}
							else
							{
								$periodos[] = array(
									'value' => '01/01/' . date('Y', strtotime('next sunday - ' . $i . ' week + 6 days')) . '|' . date('d/m/Y', strtotime('next sunday - ' . $i . ' week + 6 days')) . '|' . $semana,
									'text'  => 'SEMANA 1 DEL 01/01/' . date('Y', strtotime('next sunday - ' . $i . ' week + 6 days')) . ' AL ' . date('d/m/Y', strtotime('next sunday - ' . $i . ' week + 6 days'))
								);
							}

							$semana = 53;
							$periodos[] = array(
								'value' => date('d/m/Y', strtotime('next saturday - ' . $i . ' week')) . '|31/12/' . date('Y', strtotime('next saturday - ' . $i . ' week')) . '|' . $semana,
								'text'  => 'SEMANA 53 DEL ' . date('d/m/Y', strtotime('next saturday - ' . $i . ' week')) . ' AL 31/12/' . date('Y', strtotime('next saturday - ' . $i . ' week'))
							);
						}
						else
						{
							$year = date('Y', strtotime('next saturday - ' . $i . ' week'));

							$semana_extra = 0;

							if ($year < 2017)
							{
								if (date('Y', strtotime("january {$year} first saturday")) > date('Y', strtotime("january {$year} first saturday - 6 days")) && intval(date('W', strtotime("january {$year} first saturday")), 10) > 1)
								{
									$semana_extra = 1;
								}

								$semana = intval(date('W', strtotime('next saturday - ' . $i . ' week + 6 days')), 10) + $semana_extra;
								$periodos[] = array(
									'value' => date('d/m/Y', strtotime('next saturday - ' . $i . ' week')) . '|' . date('d/m/Y', strtotime('next saturday - ' . $i . ' week + 6 days')) . '|' . $semana,
									'text'  => 'SEMANA ' . $semana . ' DEL ' . date('d/m/Y', strtotime('next saturday - ' . $i . ' week')) . ' AL ' . date('d/m/Y', strtotime('next saturday - ' . $i . ' week + 6 days'))
								);
							}
							else
							{
								if (date('Y', strtotime("january {$year} first sunday")) > date('Y', strtotime("january {$year} first sunday - 6 days")) && intval(date('W', strtotime("january {$year} first sunday")), 10) > 1)
								{
									$semana_extra = 1;
								}

								$semana = intval(date('W', strtotime('next sunday - ' . $i . ' week + 6 days')), 10) + $semana_extra;
								$periodos[] = array(
									'value' => date('d/m/Y', strtotime('next sunday - ' . $i . ' week')) . '|' . date('d/m/Y', strtotime('next sunday - ' . $i . ' week + 6 days')) . '|' . $semana,
									'text'  => 'SEMANA ' . $semana . ' DEL ' . date('d/m/Y', strtotime('next sunday - ' . $i . ' week')) . ' AL ' . date('d/m/Y', strtotime('next sunday - ' . $i . ' week + 6 days'))
								);
							}

						}
					}
					else {
						if (!isset($ts1))
						{
							$ts1 = date('j') < 15 ? strtotime('first day of this month') : strtotime('16 ' . date('F') . ' ' . date('Y'));
							$ts2 = date('j') < 15 ? strtotime('15 ' . date('F') . ' ' . date('Y')) : strtotime('last day of this month');
						}

						$periodos[] = array(
							'value' => date('d/m/Y', $ts1) . '|' . date('d/m/Y', $ts2),
							'text'  => 'QUINCENA DEL ' . date('d/m/Y', $ts1) . ' AL ' . date('d/m/Y', $ts2)
						);

						if (date('j', $ts1) == 1)
						{
							$ts2 = strtotime(date('j F Y', $ts1) . ' - 1 day');
							$ts1 = strtotime(date('16 F Y', $ts1) . ' - 1 month');
						}
						else if (date('j', $ts1) == 16)
						{
							$ts2 = strtotime(date('j F Y', $ts1) . ' - 1 day');
							$ts1 = strtotime('first day of ' . date('F Y', $ts1));
						}
					}
				}
			}
			else {
				for ($i = 0; $i < 8; $i++)
				{
					if (date('Y', strtotime('last monday - ' . $i . ' week + 6 days')) > date('Y', strtotime('last monday - ' . $i . ' week')))
					{
						$semana = 1;
						$periodos[] = array(
							'value' => '01/01/' . date('Y', strtotime('last monday - ' . $i . ' week + 6 days')) . '|' . date('d/m/Y', strtotime('last monday - ' . $i . ' week + 6 days')) . '|' . $semana,
							'text'  => 'SEMANA 1 DEL 01/01/' . date('Y', strtotime('last monday - ' . $i . ' week + 6 days')) . ' AL ' . date('d/m/Y', strtotime('last monday - ' . $i . ' week + 6 days'))
						);

						$semana = 53;
						$periodos[] = array(
							'value' => date('d/m/Y', strtotime('last monday - ' . $i . ' week')) . '|31/12/' . date('Y', strtotime('last monday - ' . $i . ' week')) . '|' . $semana,
							'text'  => 'SEMANA 53 DEL ' . date('d/m/Y', strtotime('last monday - ' . $i . ' week')) . ' AL 31/12/' . date('Y', strtotime('last monday - ' . $i . ' week'))
						);
					}
					else
					{
						$year = date('Y', strtotime('next saturday - ' . $i . ' week'));

						$semana_extra = 0;

						if (date('Y', strtotime("january {$year} first monday")) > date('Y', strtotime("january {$year} first monday - 6 days")) && intval(date('W', strtotime("january {$year} first monday")), 10) == 1)
						{
							$semana_extra = 1;
						}

						$semana = date('W', strtotime('last monday - ' . $i . ' week + 6 days')) + $semana_extra;
						$periodos[] = array(
							'value' => date('d/m/Y', strtotime('last monday - ' . $i . ' week')) . '|' . date('d/m/Y', strtotime('last monday - ' . $i . ' week + 6 days')) . '|' . $semana,
							'text'  => 'SEMANA ' . $semana . ' DEL ' . date('d/m/Y', strtotime('last monday - ' . $i . ' week')) . ' AL ' . date('d/m/Y', strtotime('last monday - ' . $i . ' week + 6 days'))
						);
					}
				}
			}

			echo utf8_encode(json_encode($periodos));

			break;

		case 'inicio':
			$db->query("DELETE FROM reporte_nomina_tmp WHERE idins = {$_SESSION['iduser']}");

			$tpl = new TemplatePower('plantillas/nom/ReporteNominaV2Inicio.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();

			break;

		case 'cargar_datos':
			$num_cia = $_REQUEST['num_cia'];

			@list($fecha1, $fecha2, $semana) = explode('|', $_REQUEST['periodo']);

			list($dia1, $mes1, $anio1) = array_map('toInt', explode('/', $fecha1));
			list($dia2, $mes2, $anio2) = array_map('toInt', explode('/', $fecha2));

			ini_set('auto_detect_line_endings', TRUE);

			$file_data = file($_FILES['archivo_carga']['tmp_name'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

			if (count(explode(',', $file_data[0])) < 25)
			{
				$tpl = new TemplatePower('plantillas/nom/ReporteNominaV2Error.tpl');
				$tpl->prepare();

				$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

				$tpl->assign('error', 'El "Archivo" contiene errores y no puede ser procesado');

				$tpl->printToScreen();

				die;
			}

			$result = $db->query("
				SELECT
					id_horario,
					descripcion
				FROM
					catalogo_horarios_nomina
				ORDER BY
					id_horario
			");

			$horarios = array();

			if ($result)
			{
				foreach ($result as $row) {
					$horarios[$row['id_horario']] = $row['descripcion'];
				}
			}

			$data = array();

			foreach ($file_data as $file_row)
			{
				$fields = array_map('clean', explode(',', $file_row));

				$asistencia = array(
					'A',
					'A',
					'A',
					'A',
					'A',
					'A',
					'A'
				);

				if ($fields[3] != '')
				{
					list($dia_ingreso, $mes_ingreso, $anio_ingreso) = array_map('toInt', explode('/', $fields[3]));

					$fields[3] = "$dia_ingreso/$mes_ingreso/$anio_ingreso";
				}
				else {
					list($dia_ingreso, $mes_ingreso, $anio_ingreso) = array_map('toInt', explode('/', $fecha1));
				}

				$last_day = 0;

				if (mktime(0, 0, 0, $mes_ingreso, $dia_ingreso, $anio_ingreso) > mktime(0, 0, 0, $mes1, $dia1, $anio1))
				{
					$dif = round((mktime(0, 0, 0, $mes_ingreso, $dia_ingreso, $anio_ingreso) - mktime(0, 0, 0, $mes1, $dia1, $anio1)) / 86400);

					for ($day = $last_day; $day < $dif; $day++)
					{
						$asistencia[$day] = '';
					}

					$last_day += $dif;
				}

				if (intval($fields[11]) > 0)
				{
					for ($day = $last_day; $day < intval($fields[11]) + $last_day; $day++)
					{
						$asistencia[$day] = 'I';
					}

					$last_day += intval($fields[11]);
				}

				if (intval($fields[10]) > 0)
				{
					for ($day = $last_day; $day < intval($fields[10]) + $last_day; $day++)
					{
						$asistencia[$day] = 'F';
					}

					$last_day += intval($fields[10]);
				}

				if ($last_day == 0)
				{
					if (floatval($fields[13]) == 0)
					{
						if ($num_cia < 900)
						{
							$asistencia[mt_rand(0, 6)] = 'D';
						}
						else {
							$asistencia[6] = 'D';
						}
					}
					else {
						if ($num_cia < 900)
						{
							$semana_pan = array(
								0,
								2,
								3,
								4,
								5,
								6
							);

							$asistencia[$semana_pan[mt_rand(0, 5)]] = 'D';
						}
						else {
							$asistencia[mt_rand(0, 5)] = 'D';
						}
					}
				}

				// if ($last_day < 6 && mktime(0, 0, 0, $mes_ingreso, $dia_ingreso, $anio_ingreso) < mktime(0, 0, 0, $mes1, $dia1, $anio1))
				// {
				// 	$asistencia[mt_rand($last_day, 6)] = 'D';
				// }

				$data[] = array(
					'num_cia'				=> intval($num_cia),
					'fecha1'				=> $fecha1,
					'fecha2'				=> $fecha2,
					'clave'					=> intval($fields[0]),
					'nombre'				=> utf8_encode(mb_strtoupper($fields[1])),
					'puesto'				=> utf8_encode(mb_strtoupper($fields[2])),
					'fecha_alta'			=> $fields[3],
					'curp'					=> utf8_encode(mb_strtoupper($fields[4])),
					'num_afiliacion'		=> $fields[5],
					'salario_diario'		=> floatval($fields[6]),
					'salario_integrado'		=> floatval($fields[7]),
					'horario'				=> isset($horarios[$fields[8]]) ? $horarios[$fields[8]] : NULL,
					'asistencia0'			=> $asistencia[0],
					'asistencia1'			=> $asistencia[1],
					'asistencia2'			=> $asistencia[2],
					'asistencia3'			=> $asistencia[3],
					'asistencia4'			=> $asistencia[4],
					'asistencia5'			=> $asistencia[5],
					'asistencia6'			=> $asistencia[6],
					'dias_trabajados'		=> intval($fields[9]),
					'faltas'				=> intval($fields[10]),
					'incapacidades'			=> intval($fields[11]),
					'sueldo_semanal'		=> floatval($fields[12]),
					'prima_dominical'		=> floatval($fields[13]),
					'vacaciones'			=> intval($fields[14]),
					'prima_vacacional'		=> floatval($fields[15]),
					'total_percepciones'	=> floatval($fields[16]),
					'isr'					=> floatval($fields[17]),
					'subsidio_al_empleo'	=> abs(floatval($fields[18])),
					'credito_infonavit'		=> floatval($fields[19]),
					'pension_alimenticia'	=> floatval($fields[20]),
					'imss'					=> floatval($fields[21]),
					'total_deducciones'		=> abs(floatval($fields[22])),
					'total'					=> floatval($fields[23]),
					'dia_festivo'			=> isset($fields[24]) ? floatval($fields[24]) : 0,
					'uuid'					=> isset($fields[25]) ? utf8_encode(mb_strtoupper($fields[25])) : '',
					'idins'					=> intval($_SESSION['iduser']),
					'extra'					=> isset($fields[26]) && isset($_REQUEST['nombre_extra']) && $_REQUEST['nombre_extra'] != '' ? floatval($fields[26]) : 0,
					'nombre_extra'			=> isset($fields[26]) && isset($_REQUEST['nombre_extra']) && $_REQUEST['nombre_extra'] != '' ? $_REQUEST['nombre_extra'] : '',
					'leyenda_extra'			=> isset($fields[26]) && isset($_REQUEST['leyenda_extra']) && $_REQUEST['leyenda_extra'] != '' ? $_REQUEST['leyenda_extra'] : '',
					'semana'				=> $semana
				);
			}

			$sql = '';

			foreach ($data as $d)
			{
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
					fecha1,
					fecha2,
					clave,
					rn.nombre,
					puesto,
					horario,
					asistencia0
						AS a0,
					asistencia1
						AS a1,
					asistencia2
						AS a2,
					asistencia3
						AS a3,
					asistencia4
						AS a4,
					asistencia5
						AS a5,
					asistencia6
						AS a6,
					fecha_alta,
					rn.curp,
					num_afiliacion,
					salario_diario,
					salario_integrado,
					dias_trabajados,
					faltas,
					incapacidades,
					sueldo_semanal,
					prima_dominical,
					dia_festivo,
					vacaciones,
					prima_vacacional,
					total_percepciones,
					isr,
					subsidio_al_empleo,
					credito_infonavit,
					pension_alimenticia,
					imss,
					total_deducciones,
					total,
					uuid,
					extra,
					nombre_extra,
					leyenda_extra,
					semana
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

			$tpl = new TemplatePower('plantillas/nom/ReporteNominaV2Datos.tpl');
			$tpl->prepare();

			$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

			list($dia, $mes, $anio) = array_map('toInt', explode('/', $result[0]['fecha2']));

			$tpl->assign('num_cia', $result[0]['num_cia']);
			$tpl->assign('nombre_cia', $result[0]['nombre_cia']);
			$tpl->assign('rfc_cia', $result[0]['rfc_cia']);
			$tpl->assign('no_imss', $result[0]['no_imss']);
			$tpl->assign('semana', $result[0]['semana'] > 0 ? $result[0]['semana'] : ($mes1 - 1) * 2 + ($dia1 == 1 ? 1 : 2));
			$tpl->assign('fecha1', $result[0]['fecha1']);
			$tpl->assign('fecha2', $result[0]['fecha2']);
			$tpl->assign('empleados', number_format(count($result), 0, '', ','));

			$row_color = FALSE;

			$ok = TRUE;

			$totales = array(
				'sueldo_semanal'		=> 0,
				'subsidio_al_empleo'	=> 0,
				'prima_dominical'		=> 0,
				'dia_festivo'			=> 0,
				'prima_vacacional'		=> 0,
				'total_percepciones'	=> 0,
				'credito_infonavit'		=> 0,
				'isr'					=> 0,
				'imss'					=> 0,
				'pension_alimenticia'	=> 0,
				'total_deducciones'		=> 0,
				'total'					=> 0,
				'extra'					=> 0
			);

			if (trim($result[0]['nombre_extra']) != '')
			{
				$tpl->assign('colspan', 11);
				$tpl->assign('title_extra', '<th>' . trim($result[0]['nombre_extra']) . '</th>');
			}
			else
			{
				$tpl->assign('colspan', 10);
			}

			foreach ($result as $rec)
			{
				$tpl->newBlock('row');

				if ($rec['nombre'] == ''/* || $rec['curp'] == ''*/ || $rec['puesto'] == '' || $rec['fecha_alta'] == '' || $rec['num_afiliacion'] == '' || $rec['horario'] == '')
				{
					$tpl->assign('error', ' style="background-color:#FF9;"');

					$ok = FALSE;
				}

				$row_color = !$row_color;

				foreach ($rec as $key => $value)
				{
					if (in_array($key, array('num_cia', 'nombre_cia', 'rfc_cia', 'no_imss', 'fecha1', 'fecha2')))
					{
						continue;
					}
					else if (in_array($key, array('clave', 'nombre', 'fecha_alta', 'curp', 'num_afiliacion', 'puesto', 'horario')))
					{
						$tpl->assign($key, $value);
					}
					else if (in_array($key, array('dias_trabajados', 'faltas', 'incapacidades')))
					{
						$tpl->assign($key, $value);
					}
					else if (in_array($key, array('a0', 'a1', 'a2', 'a3', 'a4', 'a5', 'a6')))
					{
						$tpl->assign($key, $value);

						switch ($value)
						{
							case 'A':
								$tpl->assign('color' . $key[1], 'green');
							break;

							case 'D':
								$tpl->assign('color' . $key[1], 'blue');
							break;

							case 'I':
								$tpl->assign('color' . $key[1], 'orange');
							break;

							case 'F':
								$tpl->assign('color' . $key[1], 'red');
							break;
						}
					}
					else if (in_array($key, array('extra')) && trim($rec['nombre_extra']) != '')
					{
						$tpl->assign('importe_extra', '<td align="right" class="blue">' . (round(abs($value), 2) != 0 ? number_format(abs($value), 2, '.', ',') : '&nbsp;') . '</td>');
					}
					else {
						$tpl->assign($key, round(abs($value), 2) != 0 ? number_format(abs($value), 2, '.', ',') : '&nbsp;');
					}

					if (in_array($key, array_keys($totales)))
					{
						$totales[$key] += $value;
					}
				}

				foreach ($totales as $key => $value)
				{
					$tpl->assign('_ROOT.' . $key, number_format($value, 2, '.', ','));
				}
			}

			if (trim($result[0]['nombre_extra']) != '')
			{
				$tpl->assign('_ROOT.total_extra', '<th align="right" class="blue">' . number_format($totales['extra'], 2, '.', ',') . '</th>');
			}

			if ( ! $ok)
			{
				$tpl->assign('_ROOT.disabled', ' disabled');
			}

			echo $tpl->getOutputContent();

			break;

		case 'registrar_datos':
			$result = $db->query("SELECT COALESCE(MAX(folio) + 1, 1) AS folio FROM reporte_nomina");

			$folio = $result[0]['folio'];

			$sql = "
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
							dia_festivo,
							prima_vacacional,
							total_percepciones,
							isr,
							subsidio_al_empleo,
							credito_infonavit,
							pension_alimenticia,
							imss,
							total_deducciones,
							total,
							uuid,
							status,
							idins,
							tsins,
							extra,
							nombre_extra,
							leyenda_extra,
							semana
						)
					SELECT
						{$folio},
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
						dia_festivo,
						prima_vacacional,
						total_percepciones,
						isr,
						subsidio_al_empleo,
						credito_infonavit,
						pension_alimenticia,
						imss,
						total_deducciones,
						total,
						uuid,
						1,
						idins,
						now(),
						extra,
						nombre_extra,
						leyenda_extra,
						semana
					FROM
						reporte_nomina_tmp
					WHERE
						idins = {$_SESSION['iduser']}
			";

			$db->query($sql);

			echo $folio;

			break;

		case 'reporte_pdf':
			$sql = "
				SELECT
					num_cia,
					cc.razon_social
						AS nombre_cia,
					cc.nombre_corto,
					cc.rfc
						AS rfc_cia,
					cc.email,
					no_imss,
					fecha1,
					fecha2,
					semana,
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
					dia_festivo,
					vacaciones,
					prima_vacacional,
					total_percepciones,
					isr,
					subsidio_al_empleo,
					credito_infonavit,
					pension_alimenticia,
					imss,
					total_deducciones,
					total,
					uuid,
					extra,
					nombre_extra,
					leyenda_extra
				FROM
					reporte_nomina rn
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					folio = {$_REQUEST['folio']}
				ORDER BY
					clave
			";

			$result = $db->query($sql);

			if ( ! class_exists('FPDF'))
			{
				include_once('includes/fpdf/fpdf.php');
			}

			list($dia1, $mes1, $anio1) = array_map('toInt', explode('/', $result[0]['fecha1']));
			list($dia2, $mes2, $anio2) = array_map('toInt', explode('/', $result[0]['fecha2']));

			class PDF extends FPDF
			{
				function Header()
				{
					global $result, $dia1, $mes1, $anio1, $dia2, $mes2, $anio2, $_meses;

					$this->SetMargins(0, 5, 12);

					$this->SetFont('ARIAL', 'BU', 8);

					$this->Cell(100, 0, $result[0]['nombre_cia']);

					$this->Ln(4);

					$this->SetFont('ARIAL', 'B', 8);

					$this->Cell(35, 0, 'R.F.C.:');
					$this->Cell(65, 0, utf8_decode($result[0]['rfc_cia']));

					if (!in_array($result[0]['num_cia'], array(700, 800)))
					{
						$this->Cell(35, 0, 'SEMANA:');

						$this->Cell(0, 0, $result[0]['semana']);
					}
					else {
						$this->Cell(35, 0, 'QUINCENA:');
						$this->Cell(0, 0, ($mes1 - 1) * 2 + ($dia1 == 1 ? 1 : 2));
					}

					$this->Ln(4);

					$this->Cell(35, 0, 'REG. I.M.S.S.:');
					$this->Cell(65, 0, $result[0]['no_imss']);

					$this->Cell(35, 0, 'PERIODO DE PAGO:');

					if ($mes1 == $mes2)
					{
						$this->Cell(0, 0, 'DEL ' . $dia1 . ' AL ' . $dia2 . ' DE ' . mb_strtoupper($_meses[$mes2]) . ' DE ' . $anio2);
					}
					else {
						$this->Cell(0, 0, 'DEL ' . $dia1 . ' DE ' . mb_strtoupper($_meses[$mes1]) . ' AL ' . $dia2 . ' DE ' . mb_strtoupper($_meses[$mes2]) . ' DE ' . $anio2);
					}

					$this->Ln(4);

					$this->SetFont('ARIAL', 'BU', 8);

					$this->Text(330, 10, $result[0]['num_cia']);

					$this->SetFont('ARIAL', 'B', 6);

					$this->Text(210, 10, 'A=ASISTENCIA');
					$this->Text(210, 12, 'F=FALTA');
					$this->Text(210, 14, 'I=INCAPACIDAD');
					$this->Text(210, 16, 'D=DESCANSO');

					$this->Text(230, 10, 'P.D.=PRIMA DOMINICAL');
					$this->Text(230, 12, 'P.V.=PRIMA VACACIONAL');
					$this->Text(230, 14, 'S.D.=SALARIO DIARIO');
					$this->Text(230, 16, 'S.D.I.=SALARIO DIARIO INTEGRADO');

					$this->Text(270, 10, 'V=VACACIONES');
					$this->Text(270, 12, 'INFO.=INFONAVIT');
					$this->Text(270, 14, 'P.A.=PENSION ALIMENTICIA');

					if ($result[0]['leyenda_extra'] != '')
					{
						$this->Text(270, 16, mb_strtoupper($result[0]['leyenda_extra']));
					}

					$this->Text(305, 10, 'F=FIJO');
					$this->Text(305, 12, 'V=VARIABLE');
					$this->Text(305, 14, 'M=MIXTO');

					$this->Ln(5);

					$this->SetFont('ARIAL', 'B', 5);

					$this->Cell(104, 3, '', 0, 0);
					$this->Cell(14, 3, 'ASISTENCIA', 1, 0, 'C');
					$this->Cell(85 + ($result[0]['nombre_extra'] != '' ? 11 : 0), 3, 'PERCEPCIONES', 1, 0, 'C');
					$this->Cell(51, 3, 'DEDUCCIONES', 1, 0, 'C');
					$this->Cell(79, 3, '', 0, 0);
					$this->Ln();

					$this->Cell(4, 7, 'NO.', 1, 0, 'C');
					$this->Cell(43, 7, 'NOMBRE DEL EMPLEADO', 1, 0, 'C');
					$this->Cell(10, 7, "INGRESO", 1, 0, 'C');
					$this->Cell(22, 7, 'RFC/CURP', 1, 0, 'C');
					$this->Cell(13, 7, "N.S.S.", 1, 0, 'C');
					$this->Cell(12, 7, 'HORARIO', 1, 0, 'C');

					if ($result[0]['num_cia'] < 900)
					{
						$this->Cell(2, 7, 'S', 1, 0, 'C');
						$this->Cell(2, 7, 'D', 1, 0, 'C');
						$this->Cell(2, 7, 'L', 1, 0, 'C');
						$this->Cell(2, 7, 'M', 1, 0, 'C');
						$this->Cell(2, 7, 'M', 1, 0, 'C');
						$this->Cell(2, 7, 'J', 1, 0, 'C');
						$this->Cell(2, 7, 'V', 1, 0, 'C');
					}
					else {
						$this->Cell(2, 7, 'L', 1, 0, 'C');
						$this->Cell(2, 7, 'M', 1, 0, 'C');
						$this->Cell(2, 7, 'M', 1, 0, 'C');
						$this->Cell(2, 7, 'J', 1, 0, 'C');
						$this->Cell(2, 7, 'V', 1, 0, 'C');
						$this->Cell(2, 7, 'S', 1, 0, 'C');
						$this->Cell(2, 7, 'D', 1, 0, 'C');
					}

					$this->Cell(7, 7, 'S.D.', 1, 0, 'C');
					$this->Cell(7, 7, 'S.D.I.', 1, 0, 'C');
					$this->Cell(5, 7, 'TIPO', 1, 0, 'C');
					$this->Cell(5, 7, 'DIAS', 1, 0, 'C');
					$this->Cell(5, 7, 'FAL.', 1, 0, 'C');
					$this->Cell(5, 7, 'INC.', 1, 0, 'C');
					$this->Cell(5, 7, 'HRS.', 1, 0, 'C');
					$this->Cell(11, 7, 'SUELDO', 1, 0, 'C');
					$this->Cell(8, 7, 'P.D.', 1, 0, 'C');
					$this->Cell(8, 7, 'D.F.', 1, 0, 'C');
					//$this->Cell(5, 7, 'V', 1, 0, 'C');
					$this->Cell(8, 7, 'P.V.', 1, 0, 'C');

					if ($result[0]['nombre_extra'] != '')
					{
						$this->Cell(11, 7, mb_strtoupper($result[0]['nombre_extra']), 1, 0, 'C');
					}

					$this->Cell(11, 7, 'TOTAL', 1, 0, 'C');
					$this->Cell(8, 7, 'I.S.R.', 1, 0, 'C');
					$this->Cell(8, 7, "SUB.EMP.", 1, 0, 'C');
					$this->Cell(8, 7, 'INFO.', 1, 0, 'C');
					$this->Cell(8, 7, 'P.A.', 1, 0, 'C');
					$this->Cell(8, 7, 'I.M.S.S.', 1, 0, 'C');
					$this->Cell(11, 7, 'TOTAL', 1, 0, 'C');
					$this->Cell(11, 7, "NETO", 1, 0, 'C');
					$this->Cell(37, 7, '', 1, 0, 'C');
					$this->Cell(37 - ($result[0]['nombre_extra'] != '' ? 11 : 0), 7, 'FIRMA', 1, 0, 'C');

					$this->Ln();
				}

				function Footer()
				{
					$this->SetY(-7);
					$this->SetFont('Arial', '', 6);
					$this->Cell(0, 10, 'PAGINA ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
				}
			}

			$pdf = new PDF('L', 'mm', array(216, 340));

			$pdf->AliasNbPages();

			$pdf->SetDisplayMode('fullwidth', 'single');

			$pdf->SetMargins(0, 5, 12);

			$pdf->SetAutoPageBreak(FALSE);

			$pdf->AddPage('L', array(216, 340));

			$totales = array(
				'sueldo_semanal'      => 0,
				'subsidio_al_empleo'  => 0,
				'prima_dominical'     => 0,
				'dia_festivo'         => 0,
				'prima_vacacional'    => 0,
				'total_percepciones'  => 0,
				'credito_infonavit'   => 0,
				'isr'                 => 0,
				'imss'                => 0,
				'pension_alimenticia' => 0,
				'total_deducciones'   => 0,
				'total'               => 0,
				'extra'               => 0
			);

			$leyenda1 = utf8_decode("RECIBI LA CANTIDAD SEÑALADA EN EL APARTADO");
			$leyenda2 = utf8_decode('CORRESPONDIENTE, MANIFESTANDO BAJO');
			$leyenda3 = utf8_decode('PROTESTA DE DECIR VERDAD QUE NO SE ME');
			$leyenda4 = utf8_decode('ADEUDA CANTIDAD ALGUNA POR NINGUN');
			$leyenda5 = utf8_decode('CONCEPTO Y QUE EN ESTE PERIODO HE');
			$leyenda6 = utf8_decode('LABORADO DENTRO DEL HORARIO MAXIMO LEGAL');
			$leyenda7 = utf8_decode('ESTABLECIDO.');

			$row = 0;

			foreach ($result as $rec)
			{
				$pdf->SetFont('ARIAL', '', 5);

				$pdf->Cell(4, 12, $rec['clave'], 1, 0, 'R');

				$pdf->SetFont('ARIAL', 'B', 5);

				$pdf->Cell(43, 12, $rec['nombre'], 1, 0, 'L');

				$pdf->SetFont('ARIAL', '', 5);

				$pdf->Cell(10, 12, $rec['fecha_alta'], 1, 0, 'C');
				$pdf->Cell(22, 12, /*$rec['curp']*/'', 1, 0, 'L');

				$pdf->Text(58, 28 + (16 * $row + 8), substr($rec['curp'], 0, 10));
				$pdf->Text(58, 32 + (16 * $row + 8), $rec['curp']);

				$pdf->Cell(13, 12, $rec['num_afiliacion'], 1, 0, 'C');
				$pdf->Cell(12, 12, $rec['horario'], 1, 0, 'C');
				$pdf->Cell(2, 12, $rec['asistencia0'], 1, 0, 'C');
				$pdf->Cell(2, 12, $rec['asistencia1'], 1, 0, 'C');
				$pdf->Cell(2, 12, $rec['asistencia2'], 1, 0, 'C');
				$pdf->Cell(2, 12, $rec['asistencia3'], 1, 0, 'C');
				$pdf->Cell(2, 12, $rec['asistencia4'], 1, 0, 'C');
				$pdf->Cell(2, 12, $rec['asistencia5'], 1, 0, 'C');
				$pdf->Cell(2, 12, $rec['asistencia6'], 1, 0, 'C');
				$pdf->Cell(7, 12, number_format($rec['salario_diario'], 2, '.', ','), 1, 0, 'R');
				$pdf->Cell(7, 12, number_format($rec['salario_integrado'], 2, '.', ','), 1, 0, 'R');
				$pdf->Cell(5, 12, 'F', 1, 0, 'C');
				$pdf->Cell(5, 12, $rec['dias_trabajados'], 1, 0, 'C');
				$pdf->Cell(5, 12, $rec['faltas'] > 0 ? $rec['faltas'] : '', 1, 0, 'C');
				$pdf->Cell(5, 12, $rec['incapacidades'] > 0 ? $rec['incapacidades'] : '', 1, 0, 'C');
				$pdf->Cell(5, 12, round(($rec['dias_trabajados'] + $rec['incapacidades'] - 1) * 7.5, 1), 1, 0, 'C');
				$pdf->Cell(11, 12, number_format($rec['sueldo_semanal'], 2, '.', ','), 1, 0, 'R');
				$pdf->Cell(8, 12, $rec['prima_dominical'] != 0 ? number_format($rec['prima_dominical'], 2, '.', ',') : '', 1, 0, 'R');
				$pdf->Cell(8, 12, $rec['dia_festivo'] != 0 ? number_format($rec['dia_festivo'], 2, '.', ',') : '', 1, 0, 'R');
				//$pdf->Cell(5, 12, $rec['vacaciones'] > 0 ? $rec['vacaciones'] : '', 1, 0, 'C');
				$pdf->Cell(8, 12, $rec['prima_vacacional'] != 0 ? number_format($rec['prima_vacacional'], 2, '.', ',') : '', 1, 0, 'R');

				if ($rec['nombre_extra'] != '')
				{
					$pdf->Cell(11, 12, $rec['extra'] != 0 ? number_format($rec['extra'], 2, '.', ',') : '', 1, 0, 'R');
				}

				$pdf->SetFont('ARIAL', 'B', 5);

				$pdf->Cell(11, 12, number_format($rec['total_percepciones'], 2, '.', ','), 1, 0, 'R');

				$pdf->SetFont('ARIAL', '', 5);

				$pdf->Cell(8, 12, $rec['isr'] != 0 ? number_format($rec['isr'], 2, '.', ',') : '', 1, 0, 'R');
				$pdf->Cell(8, 12, $rec['subsidio_al_empleo'] != 0 ? number_format(abs($rec['subsidio_al_empleo']), 2, '.', ',') : '', 1, 0, 'R');
				$pdf->Cell(8, 12, $rec['credito_infonavit'] != 0 ? number_format($rec['credito_infonavit'], 2, '.', ',') : '', 1, 0, 'R');
				$pdf->Cell(8, 12, $rec['pension_alimenticia'] != 0 ? number_format($rec['pension_alimenticia'], 2, '.', ',') : '', 1, 0, 'R');
				$pdf->Cell(8, 12, $rec['imss'] != 0 ? number_format($rec['imss'], 2, '.', ',') : '', 1, 0, 'R');

				$pdf->SetFont('ARIAL', 'B', 5);

				$pdf->Cell(11, 12, number_format($rec['total_deducciones'], 2, '.', ','), 1, 0, 'R');

				$pdf->Cell(11, 12, number_format($rec['total'], 2, '.', ','), 1, 0, 'R');
				$pdf->Cell(37, 12, '', 1, 0, 'L');
				$pdf->Cell(37 - ($rec['nombre_extra'] != '' ? 11 : 0), 12, '', 1, 0, 'L');

				$pdf->Text(5, 35 + (16 * $row + 8), 'PUESTO: '. $rec['puesto']);

				$pdf->SetFont('ARIAL', '', 4);

				$pdf->Text(266 + ($rec['nombre_extra'] != '' ? 11 : 0), 26 + (16 * $row + 8), $leyenda1);
				$pdf->Text(266 + ($rec['nombre_extra'] != '' ? 11 : 0), 27.5 + (16 * $row + 8), $leyenda2);
				$pdf->Text(266 + ($rec['nombre_extra'] != '' ? 11 : 0), 29 + (16 * $row + 8), $leyenda3);
				$pdf->Text(266 + ($rec['nombre_extra'] != '' ? 11 : 0), 30.5 + (16 * $row + 8), $leyenda4);
				$pdf->Text(266 + ($rec['nombre_extra'] != '' ? 11 : 0), 32 + (16 * $row + 8), $leyenda5);
				$pdf->Text(266 + ($rec['nombre_extra'] != '' ? 11 : 0), 33.5 + (16 * $row + 8), $leyenda6);
				$pdf->Text(266 + ($rec['nombre_extra'] != '' ? 11 : 0), 35 + (16 * $row + 8), $leyenda7);

				$pdf->Ln();

				$pdf->SetFont('ARIAL', 'B', 5);

				$pdf->Cell(339, 4, 'UUID: ' . $rec['uuid'], 1, 0);

				if ($row < 10)
				{
					$pdf->Ln();

					$row++;
				}
				else {
					$row = 0;

					$pdf->AddPage('L', array(216, 340));
					$pdf->SetMargins(0, 5, 12);
				}

				$totales['sueldo_semanal'] += $rec['sueldo_semanal'];
				$totales['subsidio_al_empleo'] += abs($rec['subsidio_al_empleo']);
				$totales['prima_dominical'] += $rec['prima_dominical'];
				$totales['dia_festivo'] += $rec['dia_festivo'];
				$totales['prima_vacacional'] += $rec['prima_vacacional'];
				$totales['total_percepciones'] += $rec['total_percepciones'];
				$totales['credito_infonavit'] += $rec['credito_infonavit'];
				$totales['isr'] += $rec['isr'];
				$totales['imss'] += $rec['imss'];
				$totales['pension_alimenticia'] += $rec['pension_alimenticia'];
				$totales['total_deducciones'] += $rec['total_deducciones'];
				$totales['total'] += $rec['total'];
				$totales['extra'] += $rec['extra'];
			}

			$pdf->SetFont('ARIAL', 'B', 5);

			$pdf->Cell(47, 7, 'EMPLEADOS: ' . count($result), 1, 0, 'L');
			$pdf->Cell(110, 7, 'TOTALES', 1, 0, 'R');
			$pdf->Cell(11, 7, number_format($totales['sueldo_semanal'], 2, '.', ','), 1, 0, 'R');
			$pdf->Cell(8, 7, number_format($totales['prima_dominical'], 2, '.', ','), 1, 0, 'R');
			$pdf->Cell(8, 7, number_format($totales['dia_festivo'], 2, '.', ','), 1, 0, 'R');
			//$pdf->Cell(5, 7, '', 1, 0, 'R');
			$pdf->Cell(8, 7, number_format($totales['prima_vacacional'], 2, '.', ','), 1, 0, 'R');

			if ($result[0]['nombre_extra'] != '')
			{
				$pdf->Cell(11, 7, number_format($totales['extra'], 2, '.', ','), 1, 0, 'R');
			}

			$pdf->Cell(11, 7, number_format($totales['total_percepciones'], 2, '.', ','), 1, 0, 'R');

			$pdf->Cell(8, 7, number_format($totales['isr'], 2, '.', ','), 1, 0, 'R');
			$pdf->Cell(8, 7, number_format($totales['subsidio_al_empleo'], 2, '.', ','), 1, 0, 'R');
			$pdf->Cell(8, 7, number_format($totales['credito_infonavit'], 2, '.', ','), 1, 0, 'R');
			$pdf->Cell(8, 7, number_format($totales['pension_alimenticia'], 2, '.', ','), 1, 0, 'R');
			$pdf->Cell(8, 7, number_format($totales['imss'], 2, '.', ','), 1, 0, 'R');
			$pdf->Cell(11, 7, number_format($totales['total_deducciones'], 2, '.', ','), 1, 0, 'R');
			$pdf->Cell(11, 7, number_format($totales['total'], 2, '.', ','), 1, 0, 'R');
			$pdf->Cell(74 - ($result[0]['nombre_extra'] != '' ? 11 : 0), 7, '', 1, 0);

			$pdf->Output("reporte-nomina-{$_REQUEST['folio']}.pdf", 'I');

			$pdf->Output("pdfs/reporte-nomina-{$_REQUEST['folio']}.pdf", 'F');

			/*
			@ [25-Oct-2012] Agregar el formato de cambios al final del reporte
			*/

			class _PDF extends FPDF {
				function Header()
				{
					global $result, $dia1, $mes1, $anio1, $dia2, $mes2, $anio2, $_meses;

					for ($f = 0; $f < 2; $f++)
					{
						$this->Rect(6, 8 + $f * 130, 203, 130, 'D');

						$this->Rect(8, 21 + $f * 130, 199, 66, 'D');

						for ($i = 0; $i < 10; $i++)
						{
							$this->Line(8, 26 + $i * 6 + $f * 130, 207, 26 + $i * 6 + $f * 130);
						}

						$this->SetFont('ARIAL', 'B', 10);

						$this->SetXY(6, 1 + $f * 130);
						$this->Cell(199, 20, $result[0]['num_cia'] . ' ' . $result[0]['nombre_cia'] . ' (' . $result[0]['nombre_corto'] . ')', 0, 0, 'C');
						$this->Ln(4);
						$this->SetX(6);
						if (!in_array($result[0]['num_cia'], array(700, 800)))
						{
							$this->Cell(199, 20, 'SEMANA: ' . date('W Y', mktime(0, 0, 0, $mes2, $dia2, $anio2)), 0, 0, 'C');
						}
						else {
							$this->Cell(199, 20, 'QUINCENA: ' . (($mes1 - 1) * 2 + ($dia1 == 1 ? 1 : 2)) . ' ' . $anio2, 0, 0, 'C');
						}
						$this->Ln(4);
						$this->SetX(6);
						$this->Cell(199, 20, 'FECHA: ' . date('d/m/Y'), 0, 0, 'C');

						$this->SetFont('Arial', 'B', 8);

						$this->SetXY(8, 21 + $f * 130);
						$this->Cell(199, 6, 'OBSERVACIONES DEL ENCARGADO', 0, 0, 'C');

						$this->SetXY(8, 81 + $f * 130);
						$this->Cell(199, 6, 'Sr. Encargado para poder agilizar las bajas de los trabajadores FAVOR DE ENVIAR POR MEDIO DEL SISTEMA ADMINISTRATIVO', 0, 0, 'C');

						$this->SetFont('ARIAL', 'B', 10);

						$this->SetXY(8, 89 + $f * 130);
						$this->MultiCell(199, 4, utf8_decode('¡¡IMPORTANTE!! FAVOR DE NO RAYAR, TACHAR O PONER CORRECTOR EN LA NOMINA, LAS BAJAS E INCAPACIDADES LAS DEBE PONER EN ESTE FORMATO Y NO EN EL REPORTE.'), 0, 'C');

						$this->SetXY(12, 100 + $f * 130);
						$this->MultiCell(85, 4, 'NOTA 1: SR. ENCARGADO TIENE USTED UNA SEMANA PARA REGRESAR COMPLETAMENTE FIRMADA Y REVISADA LA NOMINA ANTES DE MANDARLA A LA OFICINA.', 1, 'L');

						$this->SetXY(12, 120 + $f * 130);
						$this->MultiCell(85, 4, 'NOTA 2: EN CASO DE FALTAR ALGUNA FIRMA EN LA NOMINA SE LE REGRESARA, TENIENDO QUE DEVOLVERLA LO MAS PRONTO POSIBLE A LA OFICINA PARA SU REVISION.', 1, 'L');

						$this->SetFont('ARIAL', 'B', 8);

						$this->Line(120, 115 + $f * 130, 200, 115 + $f * 130);
						$this->SetXY(120, 115 + $f * 130);
						$this->Cell(80, 6, 'NOMBRE Y FIRMA', 0, 0, 'C');

						$this->Line(120, 130 + $f * 130, 200, 130 + $f * 130);
						$this->SetXY(120, 130 + $f * 130);
						$this->Cell(80, 6, 'PUESTO', 0, 0, 'C');
					}
				}
			}

			$pdf = new _PDF('P', 'mm', 'Letter');

			$pdf->AliasNbPages();

			$pdf->SetDisplayMode('fullpage', 'single');

			$pdf->SetMargins(0, 5, 12);

			$pdf->SetAutoPageBreak(FALSE);

			$pdf->AddPage('P', 'Letter');

			$pdf->Output("pdfs/formato-aclaraciones-{$_REQUEST['folio']}.pdf", 'F');

			/*
			@ [25-Oct-2012] Enviar un correo electrónico a Margarita y a la panadería
			*/

			include_once('includes/phpmailer/class.phpmailer.php');

			$mail = new PHPMailer();

			if ($result[0]['num_cia'] >= 900)
			{
				$mail->IsSMTP();
				$mail->Host = 'mail.zapateriaselite.com';
				$mail->Port = 587;
				$mail->SMTPAuth = true;
				$mail->Username = 'elite@zapateriaselite.com';
				$mail->Password = 'facturaselectronicas';

				$mail->From = 'elite@zapateriaselite.com';
				$mail->FromName = utf8_decode('Oficinas Elite');
			}
			else {
				$mail->IsSMTP();
				$mail->Host = 'mail.lecaroz.com';
				$mail->Port = 587;
				$mail->SMTPAuth = true;
				$mail->Username = 'mollendo@lecaroz.com';
				$mail->Password = 'L3c4r0z*';

				$mail->From = 'mollendo@lecaroz.com';
				$mail->FromName = utf8_decode('Oficinas Administrativas Mollendo, S.A de R.L.');
			}

			if ($result[0]['num_cia'] >= 900)
			{
				$mail->AddAddress('elite@zapateriaselite.com');
			} else {
				$mail->AddAddress('margarita.hernandez@lecaroz.com');
			}
			// $mail->AddAddress('carlos.candelario@lecaroz.com');

			if ($result[0]['email'] != '')
			{
				$mail->AddAddress($result[0]['email']);
			}

			$mail->Subject = utf8_decode('[' . $result[0]['num_cia'] . ' ' . $result[0]['nombre_corto'] . '] Reporte de Nómina - ' . (!in_array($result[0]['num_cia'], array(700, 800)) ? 'Semana ' . $result[0]['semana'] : 'Quincena ' . (($mes1 - 1) * 2 + ($dia1 == 1 ? 1 : 2))));

			$tpl = new TemplatePower('plantillas/nom/email_reporte_nomina.tpl');
			$tpl->prepare();

			$tpl->assign('num_cia', $result[0]['num_cia']);
			$tpl->assign('nombre_cia', $result[0]['nombre_cia']);

			if (!in_array($result[0]['num_cia'], array(700, 800)))
			{
				$tpl->assign('semana', 'SEMANA: ' . $result[0]['semana']);
			}
			else {
				$tpl->assign('semana', 'QUINCENA: ' . (($mes1 - 1) * 2 + ($dia1 == 1 ? 1 : 2)));
			}

			if ($mes1 == $mes2)
			{
				$tpl->assign('periodo', 'DEL ' . $dia1 . ' AL ' . $dia2 . ' DE ' . $_meses[$mes2] . ' DE ' . $anio2);
			}
			else {
				$tpl->assign('periodo', 'DEL ' . $dia1 . ' DE ' . $_meses[$mes1] . ' AL ' . $dia2 . ' DE ' . $_meses[$mes2] . ' DE ' . $anio2);
			}

			$tpl->assign('email_ayuda', $result[0]['num_cia'] >= 900 ? 'elite@zapateriaselite.com' : 'margarita.hernandez@lecaroz.com');

			$mail->Body = $tpl->getOutputContent();

			$mail->IsHTML(true);

			$mail->AddAttachment("pdfs/reporte-nomina-{$_REQUEST['folio']}.pdf");
			$mail->AddAttachment("pdfs/formato-aclaraciones-{$_REQUEST['folio']}.pdf");

			if( ! $mail->Send())
			{
				echo $mail->ErrorInfo;
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/nom/ReporteNominaV2.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
