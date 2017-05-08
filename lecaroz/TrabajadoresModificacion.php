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
		case 'obtenerCia':
			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					num_cia BETWEEN ' . ($_SESSION['iduser'] != 1 ? ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 998') : '1 AND 10000') . '
					AND num_cia = ' . $_REQUEST['num_cia'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$sql = '
					SELECT
						cod_puestos
							AS value,
						descripcion
							AS text
					FROM
						catalogo_puestos
					WHERE
						giro = ' . ($_REQUEST['num_cia'] < 900 ? 1 : 2) . '
					ORDER BY
						descripcion
				';
				
				$puestos = $db->query($sql);
				
				foreach ($puestos as &$p) {
					$p['text'] = utf8_encode($p['text']);
				}
				
				$sql = '
					SELECT
						cod_turno
							AS value,
						descripcion
							AS text
					FROM
						catalogo_turnos
					WHERE
						giro = ' . ($_REQUEST['num_cia'] < 900 ? 1 : 2) . '
					ORDER BY
						descripcion
				';
				
				$turnos = $db->query($sql);
				
				foreach ($turnos as &$t) {
					$t['text'] = utf8_encode($t['text']);
				}
				
				$data = array(
					'nombre_cia' => utf8_encode($result[0]['nombre_corto']),
					'puestos'    => $puestos,
					'turnos'     => $turnos
				);
				
				echo json_encode($data);
			}
		break;
		
		case 'validarEdad':
			if (!in_array($_SESSION['iduser'], array(1, 4, 19, 28))) {
				list($dia_nac, $mes_nac, $anio_nac) = array_map('toInt', explode('/', $_REQUEST['fecha_nac']));
				list($dia_act, $mes_act, $anio_act) = explode('/', date('d/m/Y'));
				
				$edad = 0;
				
				if ($mes_nac > $mes_act) {
					$edad = $anio_act - $anio_nac - 1;
				}
				else if ($mes_nac == $mes_act && $dia_nac > $dia_act) {
					$edad = $anio_act - $anio_nac - 1;
				}
				else {
					$edad = $anio_act - $anio_nac;
				}
				
				if ($_REQUEST['num_afiliacion'] != '' && $edad < 18) {
					echo -1;
				}
				else {
					echo $edad;
				}
			}
		break;
		
		case 'validarNombre':
			$sql = '
				SELECT
					num_emp,
					num_cia,
					cc.nombre_corto
						AS nombre_cia,
					ap_paterno || (CASE WHEN ap_materno IS NOT NULL AND ap_materno <> \'\' THEN \' \' || ap_materno ELSE \'\' END) || \' \' || ct.nombre
						AS nombre_trabajador,
					ct.rfc,
					fecha_alta
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					fecha_baja IS NULL
					AND (
						(
							ct.nombre LIKE \'%' . $_REQUEST['nombre'] . '%\'
							AND (
								ap_paterno LIKE \'%' . $_REQUEST['ap_paterno'] . '%\'
								OR ap_materno LIKE \'%' . $_REQUEST['ap_paterno'] . '%\'
							)
							AND (
								ap_paterno LIKE \'%' . $_REQUEST['ap_materno'] . '%\'
								OR ap_materno LIKE \'%' . $_REQUEST['ap_materno'] . '%\'
							)
						)
						OR ct.rfc LIKE \'' . substr($_REQUEST['rfc'], 0, 10) . '%\'
					)
				ORDER BY
					fecha_alta,
					num_cia
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				foreach ($result as &$rec) {
					$rec['nombre_cia'] = utf8_encode($rec['nombre_cia']);
					$rec['nombre_trabajador'] = utf8_encode($rec['nombre_trabajador']);
					$rec['rfc'] = utf8_encode($rec['rfc']);
				}
				
				echo json_encode($result);
			}
		break;
		
		case 'inicio':
			$tpl = new TemplatePower('plantillas/nom/TrabajadoresModificacionInicio.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
		break;
		
		case 'buscar':
			$condiciones = array();
			
			if (in_array($_SESSION['iduser'], array(1, 4, 25, 28, 40, 43))) {
				$condiciones[] = '(ct.fecha_baja IS NULL OR ct.fecha_baja > NOW() - INTERVAL \'2 MONTHS\')';
			}
			else {
				$condiciones[] = 'ct.fecha_baja IS NULL';
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
					$condiciones[] = 'ct.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if (isset($_REQUEST['trabajadores']) && trim($_REQUEST['trabajadores']) != '') {
				$trabajadores = array();
				
				$pieces = explode(',', $_REQUEST['trabajadores']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$trabajadores[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$trabajadores[] = $piece;
					}
				}
				
				if (count($trabajadores) > 0) {
					$condiciones[] = 'ct.num_emp IN (' . implode(', ', $trabajadores) . ')';
				}
			}
			
			if (isset($_REQUEST['nombre'])) {
				$condiciones[] = 'ct.nombre LIKE \'%' . $_REQUEST['nombre'] . '%\'';
			}
			
			if (isset($_REQUEST['ap_paterno'])) {
				$condiciones[] = 'ct.ap_paterno LIKE \'%' . $_REQUEST['ap_paterno'] . '%\'';
			}
			
			if (isset($_REQUEST['ap_materno'])) {
				$condiciones[] = 'ct.ap_materno LIKE \'%' . $_REQUEST['ap_materno'] . '%\'';
			}
			
			if (isset($_REQUEST['rfc'])) {
				$condiciones[] = 'ct.rfc LIKE \'%' . $_REQUEST['rfc'] . '%\'';
			}
			
			if (!isset($_REQUEST['cias'])
				&& !isset($_REQUEST['trabajadores'])
				&& !isset($_REQUEST['nombre'])
				&& !isset($_REQUEST['ap_paterno'])
				&& !isset($_REQUEST['ap_materno'])
				&& !isset($_REQUEST['rfc'])) {
				$orden = '
					ORDER BY
						ct.id
					LIMIT
						20
				';
			}
			else {
				$orden = '
					ORDER BY
						ct.num_cia,
						ct.ap_paterno,
						ct.ap_materno,
						ct.nombre
				';
			}
			
			$sql = '
				SELECT
					ct.id,
					ct.num_emp,
					ct.num_cia,
					cc.nombre
						AS nombre_cia,
					ct.nombre_completo
						AS nombre_trabajador,
					ct.rfc,
					fecha_alta,
					CASE
						WHEN fecha_baja IS NULL THEN
							\'LABORANDO\'
						ELSE
							\'BAJA DESDE \' || fecha_baja
					END
						AS status,
					auth.nombre
						AS usuario
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN auth
						ON (auth.iduser = ct.idins)
				WHERE
					' . implode(' AND ', $condiciones) . '
			' . $orden;
			
			$result = $db->query($sql);
			
			if ($result) {
				$tpl = new TemplatePower('plantillas/nom/TrabajadoresModificacionResultado.tpl');
				$tpl->prepare();
				
				$num_cia = NULL;
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
						
						$row_color = FALSE;
					}
					
					$tpl->newBlock('trabajador');
					
					$tpl->assign('row_color', $row_color ? 'on' : 'off');
					
					$row_color = !$row_color;
					
					$tpl->assign('id', $rec['id']);
					$tpl->assign('num_emp', $rec['num_emp']);
					$tpl->assign('nombre_trabajador', utf8_encode($rec['nombre_trabajador']));
					$tpl->assign('rfc', utf8_encode($rec['rfc']));
					$tpl->assign('fecha_alta', $rec['fecha_alta']);
					$tpl->assign('status', $rec['status']);
					$tpl->assign('status_color', $rec['status'] == 'LABORANDO' ? 'green' : 'red');
					$tpl->assign('usuario', $rec['usuario'] != '' ? utf8_encode($rec['usuario']) : '&nbsp;');
				}
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'modificar':
			$sql = '
				SELECT
					ct.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					ct.num_cia_emp,
					cct.nombre_corto
						AS nombre_cia_emp,
					ct.nombre,
					ct.ap_paterno,
					ct.ap_materno,
					ct.rfc,
					ct.fecha_nac,
					ct.lugar_nac,
					ct.sexo,
					ct.calle,
					ct.colonia,
					ct.del_mun,
					ct.entidad,
					ct.cod_postal,
					ct.telefono_casa,
					ct.telefono_movil,
					ct.email,
					ct.fecha_alta,
					ct.cod_puestos,
					ct.cod_turno,
					ct.cod_horario,
					ct.salario,
					ct.salario_integrado,
					ct.fecha_alta_imss,
					ct.no_baja,
					ct.num_afiliacion,
					ct.credito_infonavit,
					ct.no_infonavit,
					ct.solo_aguinaldo,
					ct.tipo,
					ct.observaciones,
					ct.uniforme,
					ct.talla,
					ct.control_bata,
					ct.deposito_bata
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_companias cct
						ON (cct.num_cia = ct.num_cia_emp)
				WHERE
					ct.id = ' . $_REQUEST['id'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$rec = $result[0];
				
				$sql = '
					SELECT
						SUM()
				';
				
				$tpl = new TemplatePower('plantillas/nom/TrabajadoresModificacionDatos.tpl');
				$tpl->prepare();
				
				$tpl->assign('id', $_REQUEST['id']);
				$tpl->assign('num_cia', $rec['num_cia']);
				$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
				$tpl->assign('num_cia_emp', $rec['num_cia_emp']);
				$tpl->assign('nombre_cia_emp', utf8_encode($rec['nombre_cia_emp']));
				$tpl->assign('nombre', utf8_encode($rec['nombre']));
				$tpl->assign('ap_paterno', utf8_encode($rec['ap_paterno']));
				$tpl->assign('ap_materno', utf8_encode($rec['ap_materno']));
				$tpl->assign('rfc', utf8_encode($rec['rfc']));
				$tpl->assign('fecha_nac', $rec['fecha_nac']);
				$tpl->assign('lugar_nac', utf8_encode($rec['lugar_nac']));
				$tpl->assign('sexo_' . $rec['sexo'], ' checked');
				$tpl->assign('calle', utf8_encode($rec['calle']));
				$tpl->assign('colonia', utf8_encode($rec['colonia']));
				$tpl->assign('del_mun', utf8_encode($rec['del_mun']));
				$tpl->assign('entidad', utf8_encode($rec['entidad']));
				$tpl->assign('cod_postal', utf8_encode($rec['cod_postal']));
				$tpl->assign('telefono_casa', utf8_encode($rec['telefono_casa']));
				$tpl->assign('telefono_movil', utf8_encode($rec['telefono_movil']));
				$tpl->assign('email', utf8_encode($rec['email']));
				$tpl->assign('fecha_alta', $rec['fecha_alta']);
				$tpl->assign('salario', $rec['salario'] > 0 ? number_format($rec['salario']) : '');
				$tpl->assign('salario_integrado', $rec['salario_integrado'] > 0 ? number_format($rec['salario_integrado']) : '');
				$tpl->assign('fecha_alta_imss', $rec['fecha_alta_imss']);
				$tpl->assign('no_baja', $rec['no_baja'] == 't' ? ' checked' : '');
				$tpl->assign('num_afiliacion', $rec['num_afiliacion']);
				$tpl->assign('credito_infonavit', $rec['credito_infonavit'] == 't' ? ' checked' : '');
				$tpl->assign('no_infonavit', $rec['no_infonavit']);
				$tpl->assign('solo_aguinaldo', $rec['solo_aguinaldo'] == 't' ? ' checked' : '');
				$tpl->assign('tipo_' . $rec['tipo'], ' selected');
				$tpl->assign('observaciones', $rec['observaciones']);
				$tpl->assign('uniforme', $rec['uniforme']);
				$tpl->assign('talla_' . $rec['talla'], ' selected');
				$tpl->assign('control_bata', $rec['control_bata'] == 't' ? ' checked' : '');
				$tpl->assign('deposito_bata', $rec['deposito_bata'] > 0 ? number_format($rec['deposito_bata']) : '');
				
				$sql = '
					SELECT
						cod_puestos
							AS value,
						descripcion
							AS text
					FROM
						catalogo_puestos
					WHERE
						giro = ' . ($rec['num_cia'] < 900 ? 1 : 2) . '
					ORDER BY
						descripcion
				';
				
				$puestos = $db->query($sql);
				
				foreach ($puestos as $puesto) {
					$tpl->newBlock('puesto');
					$tpl->assign('value', $puesto['value']);
					$tpl->assign('text', $puesto['text']);
					
					if ($puesto['value'] == $rec['cod_puestos']) {
						$tpl->assign('selected', ' selected');
					}
				}
				
				$sql = '
					SELECT
						cod_turno
							AS value,
						descripcion
							AS text
					FROM
						catalogo_turnos
					WHERE
						giro = ' . ($rec['num_cia'] < 900 ? 1 : 2) . '
					ORDER BY
						descripcion
				';
				
				$turnos = $db->query($sql);
				
				foreach ($turnos as $turno) {
					$tpl->newBlock('turno');
					$tpl->assign('value', $turno['value']);
					$tpl->assign('text', $turno['text']);
					
					if ($turno['value'] == $rec['cod_turno']) {
						$tpl->assign('selected', ' selected');
					}
				}
				
				$sql = '
					SELECT
						cod_horario
							AS value,
						horaentrada || \'-\' || horasalida
							AS text
					FROM
						catalogo_horarios
					ORDER BY
						value
				';
				
				$horarios = $db->query($sql);
				
				foreach ($horarios as $horario) {
					$tpl->newBlock('horario');
					$tpl->assign('value', $horario['value']);
					$tpl->assign('text', $horario['text']);
					
					if ($horario['value'] == $rec['cod_horario']) {
						$tpl->assign('selected', ' selected');
					}
				}
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'actualizar':
			$sql = '
				SELECT
					*
				FROM
					catalogo_trabajadores
				WHERE
					id = ' . $_REQUEST['id'] . '
			';
			
			$tmp = $db->query($sql);
			
			$old = $tmp[0];
			
			if ($_REQUEST['num_cia'] == $old['num_cia']) {
				$sql = '
					UPDATE
						catalogo_trabajadores
					SET
						num_cia_emp = ' . (isset($_REQUEST['num_cia_emp']) && $_REQUEST['num_cia_emp'] > 0 ? $_REQUEST['num_cia_emp'] : 'NULL') . ',
						nombre = \'' . $_REQUEST['nombre'] . '\',
						ap_paterno = \'' . $_REQUEST['ap_paterno'] . '\',
						ap_materno = \'' . (isset($_REQUEST['ap_materno']) ? $_REQUEST['ap_materno'] : '') . '\',
						rfc = \'' . $_REQUEST['rfc'] . '\',
						fecha_nac = \'' . $_REQUEST['fecha_nac'] . '\',
						lugar_nac = \'' . (isset($_REQUEST['lugar_nac']) ? $_REQUEST['lugar_nac'] : '') . '\',
						sexo = ' . $_REQUEST['sexo'] . ',
						calle = \'' . (isset($_REQUEST['calle']) ? $_REQUEST['calle'] : '') . '\',
						colonia = \'' . (isset($_REQUEST['colonia']) ? $_REQUEST['colonia'] : '') . '\',
						del_mun = \'' . (isset($_REQUEST['del_mun']) ? $_REQUEST['del_mun'] : '') . '\',
						entidad = \'' . (isset($_REQUEST['entidad']) ? $_REQUEST['entidad'] : '') . '\',
						cod_postal = \'' . (isset($_REQUEST['cod_postal']) ? $_REQUEST['cod_postal'] : '') . '\',
						telefono_casa = \'' . (isset($_REQUEST['telefono_casa']) ? $_REQUEST['telefono_casa'] : '') . '\',
						telefono_movil = \'' . (isset($_REQUEST['telefono_movil']) ? $_REQUEST['telefono_movil'] : '') . '\',
						email = \'' . (isset($_REQUEST['email']) ? $_REQUEST['email'] : '') . '\',
						cod_puestos = ' . $_REQUEST['cod_puestos'] . ',
						cod_turno = ' . $_REQUEST['cod_turno'] . ',
						cod_horario = ' . $_REQUEST['cod_horario'] . ',
						salario = ' . (isset($_REQUEST['salario']) ? get_val($_REQUEST['salario']) : 0) . ',
						salario_integrado = ' . (isset($_REQUEST['salario_integrado']) ? get_val($_REQUEST['salario_integrado']) : 0) . ',
						fecha_alta_imss = ' . (isset($_REQUEST['fecha_alta_imss']) ? '\'' . $_REQUEST['fecha_alta_imss'] . '\'' : 'NULL') . ',
						no_baja = ' . (isset($_REQUEST['no_baja']) ? 'TRUE' : 'FALSE') . ',
						num_afiliacion = \'' . (isset($_REQUEST['num_afiliacion']) ? $_REQUEST['num_afiliacion'] : '') . '\',
						credito_infonavit = ' . (isset($_REQUEST['credito_infonavit']) ? 'TRUE' : 'FALSE') . ',
						no_infonavit = \'' . (isset($_REQUEST['no_infonavit']) ? $_REQUEST['no_infonavit'] : '') . '\',
						solo_aguinaldo = ' . (isset($_REQUEST['solo_aguinaldo']) ? 'TRUE' : 'FALSE') . ',
						tipo = ' . $_REQUEST['tipo'] . ',
						observaciones = \'' . (isset($_REQUEST['observaciones']) ? substr($_REQUEST['observaciones'], 0, 1000) : '') . '\',
						uniforme = ' . (isset($_REQUEST['uniforme']) ? '\'' . $_REQUEST['uniforme'] . '\'' : 'NULL') . ',
						talla = ' . (isset($_REQUEST['talla']) ? $_REQUEST['talla'] : 'NULL') . ',
						control_bata = ' . (isset($_REQUEST['control_bata']) ? 'TRUE' : 'FALSE') . ',
						deposito_bata = ' . (isset($_REQUEST['deposito_bata']) ? get_val($_REQUEST['deposito_bata']) : 0) . ',
						fecha_alta = \'' . $_REQUEST['fecha_alta'] . '\',
						imp_alta = ' . (isset($_REQUEST['num_afiliacion']) && $_REQUEST['num_afiliacion'] != $old['num_afiliacion'] ? 'TRUE' : 'FALSE') . ',
						pendiente_alta = ' . (isset($_REQUEST['num_afiliacion']) && $_REQUEST['num_afiliacion'] != $old['num_afiliacion'] ? '\'' . date('d/m/Y') . '\'' : 'NULL') . ',
						idmod = ' . $_SESSION['iduser'] . ',
						tsmod = NOW()
					WHERE
						id = ' . $_REQUEST['id'] . '
				' . ";\n";
				
				$sql .= '
					UPDATE
						catalogo_trabajadores
					SET
						nombre_completo = ap_paterno || (
							CASE
								WHEN ap_materno IS NOT NULL AND TRIM(ap_materno) <> \'\' THEN
									\' \' || TRIM(ap_materno)
								ELSE
									\'\'
							END
						) || nombre
					WHERE
						id = ' . $_REQUEST['id'] . '
				' . ";\n";
			}
			else {
				$sql = '
					UPDATE
						catalogo_trabajadores
					SET
						fecha_baja = NOW()::DATE,
						imp_baja = ' . ($old['num_afiliacion'] != '' ? 'TRUE' : 'FALSE') . ',
						pendiente_baja = ' . ($old['num_afiliacion'] != '' ? 'NOW()::DATE' : 'NULL') . ',
						iddel = ' . $_SESSION['iduser'] . ',
						tsdel = NOW()
					WHERE
						id = ' . $_REQUEST['id'] . '
				' . ";\n";
				
				$sql .= '
					INSERT INTO
						catalogo_trabajadores
							(
								num_emp,
								num_cia,
								num_cia_emp,
								nombre,
								ap_paterno,
								ap_materno,
								rfc,
								fecha_nac,
								lugar_nac,
								sexo,
								calle,
								colonia,
								del_mun,
								entidad,
								cod_postal,
								telefono_casa,
								telefono_movil,
								email,
								cod_puestos,
								cod_turno,
								cod_horario,
								salario,
								salario_integrado,
								fecha_alta_imss,
								no_baja,
								num_afiliacion,
								credito_infonavit,
								no_infonavit,
								solo_aguinaldo,
								tipo,
								observaciones,
								uniforme,
								talla,
								control_bata,
								deposito_bata,
								fecha_alta,
								imp_alta,
								pendiente_alta,
								idins,
								tsins
							)
						VALUES
							(
								' . $old['num_emp'] . ',
								' . $_REQUEST['num_cia'] . ',
								' . (isset($_REQUEST['num_cia_emp']) && $_REQUEST['num_cia_emp'] > 0 ? $_REQUEST['num_cia_emp'] : $_REQUEST['num_cia']) . ',
								\'' . utf8_decode($_REQUEST['nombre']) . '\',
								\'' . utf8_decode($_REQUEST['ap_paterno']) . '\',
								\'' . (isset($_REQUEST['ap_materno']) ? utf8_decode($_REQUEST['ap_materno']) : '') . '\',
								\'' . utf8_decode($_REQUEST['rfc']) . '\',
								\'' . $_REQUEST['fecha_nac'] . '\',
								\'' . (isset($_REQUEST['lugar_nac']) ? utf8_decode($_REQUEST['lugar_nac']) : '') . '\',
								' . $_REQUEST['sexo'] . ',
								\'' . (isset($_REQUEST['calle']) ? utf8_decode($_REQUEST['calle']) : '') . '\',
								\'' . (isset($_REQUEST['colonia']) ? utf8_decode($_REQUEST['colonia']) : '') . '\',
								\'' . (isset($_REQUEST['del_mun']) ? utf8_decode($_REQUEST['del_mun']) : '') . '\',
								\'' . (isset($_REQUEST['entidad']) ? utf8_decode($_REQUEST['entidad']) : '') . '\',
								\'' . (isset($_REQUEST['cod_postal']) ? $_REQUEST['cod_postal'] : '') . '\',
								\'' . (isset($_REQUEST['telefono_casa']) ? $_REQUEST['telefono_casa'] : '') . '\',
								\'' . (isset($_REQUEST['telefono_movil']) ? $_REQUEST['telefono_movil'] : '') . '\',
								\'' . (isset($_REQUEST['email']) ? $_REQUEST['email'] : '') . '\',
								' . $_REQUEST['cod_puestos'] . ',
								' . $_REQUEST['cod_turno'] . ',
								' . $_REQUEST['cod_horario'] . ',
								' . (isset($_REQUEST['salario']) ? get_val($_REQUEST['salario']) : 0) . ',
								' . (isset($_REQUEST['salario_integrado']) ? get_val($_REQUEST['salario_integrado']) : 0) . ',
								' . (isset($_REQUEST['fecha_alta_imss']) ? '\'' . $_REQUEST['fecha_alta_imss'] . '\'' : 'NULL') . ',
								' . (isset($_REQUEST['no_baja']) ? 'TRUE' : 'FALSE') . ',
								\'' . (isset($_REQUEST['num_afiliacion']) ? $_REQUEST['num_afiliacion'] : '') . '\',
								' . (isset($_REQUEST['credito_infonavit']) ? 'TRUE' : 'FALSE') . ',
								\'' . (isset($_REQUEST['no_infonavit']) ? $_REQUEST['no_infonavit'] : '') . '\',
								' . (isset($_REQUEST['solo_aguinaldo']) ? 'TRUE' : 'FALSE') . ',
								' . $_REQUEST['tipo'] . ',
								\'' . (isset($_REQUEST['observaciones']) ? substr(utf8_decode($_REQUEST['observaciones']), 0, 1000) : '') . '\',
								' . (isset($_REQUEST['uniforme']) ? '\'' . $_REQUEST['uniforme'] . '\'' : 'NULL') . ',
								' . (isset($_REQUEST['talla']) ? $_REQUEST['talla'] : 'NULL') . ',
								' . (isset($_REQUEST['control_bata']) ? 'TRUE' : 'FALSE') . ',
								' . (isset($_REQUEST['deposito_bata']) ? get_val($_REQUEST['deposito_bata']) : 0) . ',
								' . (isset($_REQUEST['fecha_alta']) ? '\'' . $_REQUEST['fecha_alta'] . '\'' : 'NOW()::DATE') . ',
								' . (isset($_REQUEST['num_afiliacion']) ? 'TRUE' : 'FALSE') . ',
								' . (isset($_REQUEST['num_afiliacion']) ? 'NOW()::DATE' : 'NULL') . ',
								' . $_SESSION['iduser'] . ',
								NOW()
							)
				' . ";\n";
				
				$sql .= '
					UPDATE
						catalogo_trabajadores
					SET
						nombre_completo = ap_paterno || (
							CASE
								WHEN ap_materno IS NOT NULL AND ap_materno <> \'\' THEN
									\' \' || ap_materno
								ELSE
									\'\'
							END) || \' \' || nombre
					WHERE
						id = (
							SELECT
								last_value
							FROM
								catalogo_trabajadores_id_seq
						)
				' . ";\n";
				
				$sql .= '
					UPDATE
						aguinaldos
					SET
						id_empleado = (
							SELECT
								last_value
							FROM
								catalogo_trabajadores_id_seq
						)
					WHERE
						id_empleado = ' . $_REQUEST['id'] . '
				' . ";\n";
				
				$sql .= '
					UPDATE
						infonavit
					SET
						id_emp = (
							SELECT
								last_value
							FROM
								catalogo_trabajadores_id_seq
						)
					WHERE
						id_emp = ' . $_REQUEST['id'] . '
				' . ";\n";
			}
			
			$db->query($sql);
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/nom/TrabajadoresModificacion.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
