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
					num_cia BETWEEN ' . (in_array($_SESSION['iduser'], array(1, 4, 46)) ? ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 998') : '1 AND 10000') . '
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
				
				$sql = '
					SELECT
						cod_horario
							AS value,
						descripcion
							AS text
					FROM
						catalogo_horarios
					WHERE
						giro = ' . ($_REQUEST['num_cia'] < 900 ? 1 : 2) . '
					ORDER BY
						cod_horario
				';
				
				$horarios = $db->query($sql);
				
				foreach ($horarios as &$h) {
					$h['text'] = utf8_encode($h['text']);
				}
				
				$data = array(
					'nombre_cia' => utf8_encode($result[0]['nombre_corto']),
					'puestos'    => $puestos,
					'turnos'     => $turnos,
					'horarios'   => $horarios
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
				
				if ($_REQUEST['num_afiliacion'] != '' && $edad < 16) {
					echo -1;
				}
				else {
					echo $edad;
				}
			}
		break;
		
		case 'validarListaNegra':
			$sql = '
				SELECT
					folio,
					observaciones
				FROM
					lista_negra_trabajadores
				WHERE
					/*nombre LIKE \'%' . utf8_decode($_REQUEST['nombre']) . '%\'
					AND (
						ap_paterno LIKE \'%' . utf8_decode($_REQUEST['ap_paterno']) . '%\'
						OR ap_materno LIKE \'%' . utf8_decode($_REQUEST['ap_paterno']) . '%\'
					)
					AND (
						ap_paterno LIKE \'%' . utf8_decode($_REQUEST['ap_materno']) . '%\'
						OR ap_materno LIKE \'%' . utf8_decode($_REQUEST['ap_materno']) . '%\'
					)*/
					tsdel IS NULL
					AND nombre = \'' . utf8_decode($_REQUEST['nombre']) . '\'
					AND ap_paterno = \'' . utf8_decode($_REQUEST['ap_paterno']) . '\'
					AND ap_materno = \'' . utf8_decode($_REQUEST['ap_materno']) . '\'
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$result[0]['observaciones'] = utf8_encode($result[0]['observaciones']);
				
				echo json_encode($result[0]);
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
					fecha_alta,
					COALESCE(auth.nombre, \'-\')
						AS usuario
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN auth
						ON (auth.iduser = idalta)
				WHERE
					fecha_baja IS NULL
					AND (
						(
							/*ct.nombre LIKE \'%' . utf8_decode($_REQUEST['nombre']) . '%\'
							AND (
								ap_paterno LIKE \'%' . utf8_decode($_REQUEST['ap_paterno']) . '%\'
								OR ap_materno LIKE \'%' . utf8_decode($_REQUEST['ap_paterno']) . '%\'
							)
							AND (
								ap_paterno LIKE \'%' . utf8_decode($_REQUEST['ap_materno']) . '%\'
								OR ap_materno LIKE \'%' . utf8_decode($_REQUEST['ap_materno']) . '%\'
							)*/
							ct.nombre = \'' . utf8_decode($_REQUEST['nombre']) . '\'
							AND ap_paterno = \'' . utf8_decode($_REQUEST['ap_paterno']) . '\'
							AND ap_materno = \'' . utf8_decode($_REQUEST['ap_materno']) . '\'
						)
						' . (isset($_REQUEST['rfc']) && $_REQUEST['rfc'] != '' ? 'OR ct.rfc LIKE \'' . substr(utf8_decode($_REQUEST['rfc']), 0, 10) . '%\'' : '') . '
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
					$rec['usuario'] = utf8_encode($rec['usuario']);
				}
				
				echo json_encode($result);
			}
		break;
		
		case 'alta':
			$sql = '
				SELECT
					num_emp
				FROM
					catalogo_trabajadores
				WHERE
					fecha_baja IS NULL
					AND num_cia BETWEEN ' . ($_REQUEST['num_cia'] >= 900 ? '900 AND 998' : '1 AND 899') . '
				ORDER BY
					num_emp
			';
			
			$numeros = $db->query($sql);
			
			$num_emp = 1;
			
			if ($numeros) {
				foreach ($numeros as $num) {
					if ($num_emp == $num['num_emp']) {
						$num_emp++;
					}
					else {
						break;
					}
				}
			}
			
			$sql = '
				UPDATE
					catalogo_trabajadores
				SET
					imp_alta = FALSE,
					ultimo = FALSE
				WHERE
					ultimo = TRUE
					AND imp_alta = TRUE
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
							curp,
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
							idalta,
							tsalta
						)
					VALUES
						(
							' . $num_emp . ',
							' . $_REQUEST['num_cia'] . ',
							' . (isset($_REQUEST['num_cia_emp']) && $_REQUEST['num_cia_emp'] > 0 ? $_REQUEST['num_cia_emp'] : $_REQUEST['num_cia']) . ',
							\'' . utf8_decode($_REQUEST['nombre']) . '\',
							\'' . utf8_decode($_REQUEST['ap_paterno']) . '\',
							\'' . (isset($_REQUEST['ap_materno']) ? utf8_decode($_REQUEST['ap_materno']) : '') . '\',
							\'' . utf8_decode($_REQUEST['rfc']) . '\',
							\'' . (isset($_REQUEST['curp']) ? utf8_decode($_REQUEST['curp']) : '') . '\',
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
					nombre_completo = TRIM(REGEXP_REPLACE(CONCAT_WS(\' \', ap_paterno, ap_materno, nombre), \'\s+\', \' \', \'g\'))
				WHERE
					id = (
						SELECT
							last_value
						FROM
							catalogo_trabajadores_id_seq
					)
			' . ";\n";
			
			if (isset($_REQUEST['aguinaldo']) && get_val($_REQUEST['aguinaldo']) > 0) {
				$sql .= '
					INSERT INTO
						aguinaldos
							(
								importe,
								fecha,
								id_empleado,
								tipo
							)
						VALUES
							(
								' . get_val($_REQUEST['aguinaldo']) . ',
								(
									SELECT
										fecha
									FROM
										aguinaldos
									WHERE
										fecha < \'' . date('d/m/Y', mktime(0, 0, 0, 1, 1)) . '\'
									ORDER BY
										fecha DESC
									LIMIT
										1
								),
								(
									SELECT
										last_value
									FROM
										catalogo_trabajadores_id_seq
								),
								3
							)
				' . ";\n";
			}
			
			$db->query($sql);
			
			$sql = '
				SELECT
					num_cia || \' \' || nombre_corto
						AS cia,
					num_emp || \' \' || nombre_completo
						AS trabajador
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					id = (
						SELECT
							last_value
						FROM
							catalogo_trabajadores_id_seq
					)
			';
			
			$emp = $db->query($sql);
			
			$emp[0]['cia'] = utf8_encode($emp[0]['cia']);
			$emp[0]['trabajador'] = utf8_encode($emp[0]['trabajador']);
			
			echo json_encode($emp[0]);
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/nom/TrabajadoresAlta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') < 15 ? 1 : 15)));

$tpl->printToScreen();
?>
