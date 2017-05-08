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
	1 => 'Enero',
	2 => 'Febrero',
	3 => 'Marzo',
	4 => 'Abril',
	5 => 'Mayo',
	6 => 'Junio',
	7 => 'Julio',
	8 => 'Agosto',
	9 => 'Septiembre',
	10 => 'Octubre',
	11 => 'Noviembre',
	12 => 'Diciembre'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'inicio':
			$tpl = new TemplatePower('plantillas/nom/TrabajadoresSolicitudCompaniasInicio.tpl');
			$tpl->prepare();
			
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
			
			$admins = $db->query($sql);
			
			foreach ($admins as $admin) {
				$tpl->newBlock('admin');
				$tpl->assign('value', $admin['value']);
				$tpl->assign('text', utf8_encode($admin['text']));
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'consultar':
			$condiciones = array();
			
			$condiciones[] = 'mov.tsreg IS NULL';
			
			$condiciones[] = 'mov.tsdel IS NULL';
			
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
					$condiciones[] = 'mov.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}
			
			$sql = '
				SELECT
					idaltaimss
						AS id,
					mov.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					mov.tipo,
					mov.fecha,
					mov.appaterno
						AS ap_paterno,
					mov.apmaterno
						AS ap_materno,
					mov.nombre,
					mov.observaciones
				FROM
					altaimss_tmp mov
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_administradores ca
						USING (idadministrador)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					mov.num_cia,
					mov.tipo,
					mov.fecha,
					ap_paterno,
					ap_materno,
					mov.nombre
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$tpl = new TemplatePower('plantillas/nom/TrabajadoresSolicitudCompaniasResultado.tpl');
				$tpl->prepare();
				
				$num_cia = NULL;
				
				foreach ($result as $i => $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('cia');
						
						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
						
						$tipo = NULL;
					}
					
					if ($tipo != $rec['tipo']) {
						$tipo = $rec['tipo'];
						
						$tpl->newBlock('tipo');
						
						if ($tipo == 0) {
							$tpl->assign('tipo', 'Altas');
							$tpl->assign('color', 'blue');
						}
						else if ($tipo == 1) {
							$tpl->assign('tipo', 'Bajas');
							$tpl->assign('color', 'red');
						}
						
						$row_color = FALSE;
					}
					
					$tpl->newBlock('trabajador');
					
					$tpl->assign('row_color', $row_color ? 'on' : 'off');
					
					$row_color = !$row_color;
					
					$tpl->assign('id', $rec['id']);
					$tpl->assign('value', htmlentities(json_encode(array(
						'id'   => intval($rec['id']),
						'tipo' => intval($rec['tipo'])
					))));
					$tpl->assign('tipo', $rec['tipo']);
					$tpl->assign('fecha', $rec['fecha']);
					$tpl->assign('ap_paterno', utf8_encode($rec['ap_paterno']));
					$tpl->assign('ap_materno', utf8_encode($rec['ap_materno']));
					$tpl->assign('nombre', utf8_encode($rec['nombre']));
					$tpl->assign('observaciones', utf8_encode($rec['observaciones']));
				}
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'validarAlta':
			$sql = '
				SELECT
					folio,
					observaciones
				FROM
					lista_negra_trabajadores
				WHERE
					(nombre, ap_paterno, ap_materno) IN (
						SELECT
							nombre,
							appaterno,
							apmaterno
						FROM
							altaimss_tmp
						WHERE
							idaltaimss = ' . $_REQUEST['id'] . '
					)
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$result[0]['observaciones'] = utf8_encode($result[0]['observaciones']);
				
				$result[0]['status'] = -1;
				
				echo json_encode($result[0]);
				
				return;
			}
			
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
					AND (ct.nombre, ct.ap_paterno, ct.ap_materno) IN (
						SELECT
							nombre,
							appaterno,
							apmaterno
						FROM
							altaimss_tmp
						WHERE
							idaltaimss = ' . $_REQUEST['id'] . '
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
				
				$result['status'] = -2;
				
				echo json_encode($result);
				
				return;
			}
			
			echo json_encode(array(
				'status' => 1,
				'id'     => intval($_REQUEST['id'])
			));
		break;
		
		case 'doAlta':
			$sql = '
				SELECT
					num_emp
				FROM
					catalogo_trabajadores
				WHERE
					fecha_baja IS NULL
					AND num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . '
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
				INSERT INTO
					catalogo_trabajadores (
						num_emp,
						num_cia,
						num_cia_emp,
						nombre,
						ap_paterno,
						ap_materno,
						observaciones,
						fecha_alta,
						imp_alta,
						pendiente_alta,
						idalta,
						tsalta
					)
				SELECT
					' . $num_emp . ',
					num_cia,
					num_cia,
					nombre,
					appaterno,
					apmaterno,
					observaciones,
					NOW()::DATE,
					TRUE,
					NOW()::DATE,
					' . $_SESSION['iduser'] . ',
					NOW()
				FROM
					altaimss_tmp
				WHERE
					idaltaimss = ' . $_REQUEST['id'] . '
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
					altaimss_tmp
				SET
					tsreg = NOW(),
					idreg = ' . $_SESSION['iduser'] . '
				WHERE
					idaltaimss = ' . $_REQUEST['id'] . '
			' . ";\n";
			
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
		
		case 'validarBaja':
			$sql = '
				SELECT
					id,
					num_emp,
					ap_paterno,
					ap_materno,
					nombre,
					rfc,
					fecha_alta
				FROM
					catalogo_trabajadores
				WHERE
					fecha_baja IS NULL
					AND no_baja = FALSE
					AND num_cia = (
						SELECT
							num_cia
						FROM
							altaimss_tmp
						WHERE
							idaltaimss = ' . $_REQUEST['id'] . '
					)
				ORDER BY
					ap_paterno,
					ap_materno,
					nombre
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$tpl = new TemplatePower('plantillas/nom/TrabajadoresSolicitudCompaniasBajaPopup.tpl');
				$tpl->prepare();
				
				$sql = '
					SELECT
						appaterno
							AS ap_paterno,
						apmaterno
							AS ap_materno,
						nombre
					FROM
						altaimss_tmp
					WHERE
						idaltaimss = ' . $_REQUEST['id'] . '
				';
				
				$tmp = $db->query($sql);
				
				$tpl->assign('id', $_REQUEST['id']);
				$tpl->assign('ap_paterno', utf8_encode($tmp[0]['ap_paterno']));
				$tpl->assign('ap_materno', utf8_encode($tmp[0]['ap_materno']));
				$tpl->assign('nombre', utf8_encode($tmp[0]['nombre']));
				
				$row_color = FALSE;
				
				foreach ($result as $rec) {
					$tpl->newBlock('trabajador');
					
					$tpl->assign('row_color', $row_color ? 'on' : 'off');
					
					$row_color = !$row_color;
					
					$tpl->assign('id', $rec['id']);
					$tpl->assign('num_emp', $rec['num_emp']);
					$tpl->assign('ap_paterno', utf8_encode($rec['ap_paterno']));
					$tpl->assign('ap_materno', utf8_encode($rec['ap_materno']));
					$tpl->assign('nombre', utf8_encode($rec['nombre']));
					$tpl->assign('rfc', utf8_encode($rec['rfc']));
					$tpl->assign('fecha_alta', $rec['fecha_alta']);
				}
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'doBaja':
			$sql = '
				UPDATE
					catalogo_trabajadores
				SET
					fecha_baja = NOW()::DATE,
					imp_baja = CASE
						WHEN num_afiliacion IS NOT NULL AND TRIM(num_afiliacion) <> \'\' THEN
							TRUE
						ELSE
							FALSE
					END,
					pendiente_baja = CASE
						WHEN num_afiliacion IS NOT NULL AND TRIM(num_afiliacion) <> \'\' THEN
							NOW()::DATE
						ELSE
							NULL
					END,
					tsbaja = NOW(),
					idbaja = ' . $_SESSION['iduser'] . '
				WHERE
					id = ' . $_REQUEST['id'] . '
			' . ";\n";
			
			$sql .= '
				UPDATE
					altaimss_tmp
				SET
					tsreg = NOW(),
					idreg = ' . $_SESSION['iduser'] . ',
					idtrabajador = ' . $_REQUEST['id'] . '
				WHERE
					idaltaimss = ' . $_REQUEST['idaltaimss'] . '
			' . ";\n";
			
			$db->query($sql);
		break;
		
		case 'borrarMovimiento':
			$sql = '
				UPDATE
					altaimss_tmp
				SET
					tsdel = NOW(),
					iddel = ' . $_SESSION['iduser'] . '
				WHERE
					idaltaimss = ' . $_REQUEST['id'] . '
			' . ";\n";
			
			$db->query($sql);
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/nom/TrabajadoresSolicitudCompanias.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
