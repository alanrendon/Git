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

/*if ($_SESSION['iduser'] != 1) {
	die('<div style="font-size:16pt; border:solid 2px #000; padding:30px 10px;">ESTOY HACIENDO MODIFICACIONES AL PROGRAMA, NO ME LLAMEN PARA PREGUNTAR CUANDO QUEDARA, YO LES AVISO.</div>');
}*/

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		
		case 'inicio':
			$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaSimpleInicio.tpl');
			$tpl->prepare();
			
			$sql = '
				SELECT
					cod_puestos
						AS value,
					descripcion
						AS text,
					CASE
						WHEN giro = 1 THEN
							\'blue\'
						ELSE
							\'green\'
					END
						AS color
				FROM
					catalogo_puestos
				' . (!in_array($_SESSION['iduser'], array(1, 4, 46, 62)) ? 'WHERE giro = ' . $_SESSION['tipo_usuario'] : '') . '
				ORDER BY
					giro,
					text
			';
			
			$puestos = $db->query($sql);
			
			if ($puestos) {
				foreach ($puestos as $p) {
					$tpl->newBlock('puesto');
					$tpl->assign('value', $p['value']);
					$tpl->assign('text', $p['text']);
					$tpl->assign('color', $p['color']);
				}
			}
			
			$sql = '
				SELECT
					cod_turno
						AS value,
					descripcion
						AS text,
					CASE
						WHEN giro = 1 THEN
							\'blue\'
						ELSE
							\'green\'
					END
						AS color
				FROM
					catalogo_turnos
				' . (!in_array($_SESSION['iduser'], array(1, 4, 46, 62)) ? 'WHERE giro = ' . $_SESSION['tipo_usuario'] : '') . '
				ORDER BY
					giro,
					text
			';
			
			$turnos = $db->query($sql);
			
			if ($turnos) {
				foreach ($turnos as $t) {
					$tpl->newBlock('turno');
					$tpl->assign('value', $t['value']);
					$tpl->assign('text', $t['text']);
					$tpl->assign('color', $t['color']);
				}
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'buscar':
			$condiciones = array();
			
			if (!in_array($_SESSION['iduser'], array(1, 4, 62))) {
				$condiciones[] = 'ct.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
			}
			
			if (!in_array($_SESSION['iduser'], array(1, 4, 62))) {
				$condiciones[] = 'co.iduser = ' . $_SESSION['iduser'];
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
				$condiciones[] = 'ct.nombre LIKE \'%' . utf8_decode($_REQUEST['nombre']) . '%\'';
			}
			
			if (isset($_REQUEST['ap_paterno'])) {
				$condiciones[] = 'ct.ap_paterno LIKE \'%' . utf8_decode($_REQUEST['ap_paterno']) . '%\'';
			}
			
			if (isset($_REQUEST['ap_materno'])) {
				$condiciones[] = 'ct.ap_materno LIKE \'%' . utf8_decode($_REQUEST['ap_materno']) . '%\'';
			}
			
			if (isset($_REQUEST['rfc'])) {
				$condiciones[] = 'ct.rfc LIKE \'%' . utf8_decode($_REQUEST['rfc']) . '%\'';
			}
			
			$condiciones[] = 'ct.fecha_baja IS NULL';
			
			if (!isset($_REQUEST['cias'])
				&& !isset($_REQUEST['trabajadores'])
				&& !isset($_REQUEST['nombre'])
				&& !isset($_REQUEST['ap_paterno'])
				&& !isset($_REQUEST['ap_materno'])
				&& !isset($_REQUEST['rfc'])
				&& in_array($_SESSION['iduser'], array(1, 4, 62))) {
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
					CASE
						WHEN ct.num_cia <> ct.num_cia_emp THEN
							ct.num_cia_emp || \' \' || (
								SELECT
									nombre_corto
								FROM
									catalogo_companias
								WHERE
									num_cia = ct.num_cia_emp
							)
						ELSE
							\'\'
					END
						cia_emp,
					cc.nombre
						AS nombre_cia,
					ct.nombre_completo
						AS nombre_trabajador,
					ct.rfc,
					puestos.descripcion
						AS puesto,
					turnos.descripcion
						AS turno,
					COALESCE((
						SELECT
							SUM(
								CASE
									WHEN tipo_mov = FALSE THEN
										importe
									ELSE
										-importe
								END
							)
						FROM
							prestamos
						WHERE
							id_empleado = ct.id
							AND pagado = FALSE
					), 0)
						AS saldo,
					NOW()::DATE - COALESCE((
						SELECT
							MAX(fecha)
						FROM
							prestamos
						WHERE
							id_empleado = ct.id
							AND pagado = FALSE
							AND tipo_mov = TRUE
					), (
						SELECT
							MAX(fecha)
						FROM
							prestamos
						WHERE
							id_empleado = ct.id
							AND pagado = FALSE
							AND tipo_mov = FALSE
					), NULL)
						AS ultimo_abono,
					(
						COALESCE(firma_contrato, FALSE)
						AND (
							(
								fecha_inicio_contrato IS NOT NULL
								AND fecha_termino_contrato IS NOT NULL
								AND NOW()::DATE < fecha_termino_contrato
							)
							OR (
								fecha_inicio_contrato IS NOT NULL
								AND fecha_termino_contrato IS NULL
							)
						)
					)
						AS contrato
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_puestos puestos
						USING (cod_puestos)
					LEFT JOIN catalogo_turnos turnos
						USING (cod_turno)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_operadoras co
						USING (idoperadora)
				WHERE
					' . implode(' AND ', $condiciones) . '
			' . $orden;
			
			$result = $db->query($sql);
			
			if ($result) {
				$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaSimpleResultado.tpl');
				$tpl->prepare();
				
				$num_cia = NULL;
				foreach ($result as $num => $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
						
						$row_color = FALSE;
						
						$cont = 0;
						$afiliados = 0;
					}
					
					$tpl->newBlock('trabajador');
					
					$tpl->assign('row_color', $row_color ? 'on' : 'off');
					
					$row_color = !$row_color;
					
					$tpl->assign('num', $num + 1);
					$tpl->assign('id', $rec['id']);
					$tpl->assign('num_emp', $rec['num_emp']);
					$tpl->assign('no_firma', $rec['contrato'] == 'f' ? ' class="underline red"' : '');
					$tpl->assign('nombre_trabajador', ($rec['cia_emp'] != '' ? '<img src="/lecaroz/iconos/info.png" alt="' . utf8_encode($rec['cia_emp']) . '" name="cia_emp" width="16" height="16" id="cia_emp" title="Labora en" style="float:right;" />' : '') . utf8_encode($rec['nombre_trabajador']) . ($rec['cia_emp'] != '' ? '&nbsp;' : ''));
					$tpl->assign('rfc', utf8_encode($rec['rfc']));
					$tpl->assign('cia_emp', $rec['cia_emp'] != '' ? utf8_encode($rec['cia_emp']) : '&nbsp;');
					$tpl->assign('puesto', utf8_encode($rec['puesto']));
					$tpl->assign('turno', utf8_encode($rec['turno']));
					$tpl->assign('saldo', $rec['saldo'] != 0 ? '<span style="float:left;" class="orange">(' . $rec['ultimo_abono'] . ' d&iacute;a(s))</span>&nbsp;' . number_format($rec['saldo'], 2) : '&nbsp;');
					//$tpl->assign('status', $rec['status']);
					$tpl->assign('status', $rec['contrato'] == 'f' ? 'SIN CONTRATO' : '');
					$tpl->assign('status_color', $rec['contrato'] == 'f' ? 'red' : '');
				}
				
				echo $tpl->getOutputContent();
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/nom/TrabajadoresConsultaSimple.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
