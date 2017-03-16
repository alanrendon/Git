<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

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
	1  => 'ENERO',
	2  => 'FEBRERO',
	3  => 'MARZO',
	4  => 'ABRIL',
	5  => 'MAYO',
	6  => 'JUNIO',
	7  => 'JULIO',
	8  => 'AGOSTO',
	9  => 'SEPTIEMBRE',
	10 => 'OCTUBRE',
	11 => 'NOVIEMBRE',
	12 => 'DICIMEBRE'
);

$_dias = array(
	0 => 'D',
	1 => 'L',
	2 => 'M',
	3 => 'M',
	4 => 'J',
	5 => 'V',
	6 => 'S'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die('MODIFICANDO');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'reporte':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));
			
			$fecha3 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio'] - 1));
			$fecha4 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio'] - 1));
			
			$anios = array(
				1 => $_REQUEST['anio'],
				2 => $_REQUEST['anio'] - 1
			);
			
			$condiciones[] = '(fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\' OR fecha BETWEEN \'' . $fecha3 . '\' AND \'' . $fecha4 . '\')';
			
			/*
			@ Intervalo de compañías
			*/
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
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			/*
			@ Administrador
			*/
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
			/*
			@ Usuario
			*/
			if (!in_array($_SESSION['iduser'], array(1, 4, 19))) {
				$condiciones[] = '(co.iduser = ' . $_SESSION['iduser'] . ' OR ca.iduser = ' . $_SESSION['iduser'] . ')';
			}
			
			$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS nombre_cia,
					CASE
						WHEN EXTRACT(year FROM fecha) = ' . $_REQUEST['anio'] . ' THEN
							1
						ELSE
							2
					END
						AS bloque,
					SUM(piezas)
						AS piezas
				FROM
					produccion pro
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_administradores ca
						USING (idadministrador)
					LEFT JOIN catalogo_operadoras co
						USING (idoperadora)
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					num_cia,
					nombre_cia,
					bloque
				ORDER BY
					num_cia,
					bloque
			';
			$tmp = $db->query($sql);
			
			$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');
			
			$tpl = new TemplatePower('plantillas/bal/' . ($isIpad ? 'ComparativoPiezasProducidasReporteIpad.tpl' : 'ComparativoPiezasProducidasReporte.tpl'));
			$tpl->prepare();
			
			if ($tmp) {
				$result = array();
				
				$num_cia = NULL;
				foreach ($tmp as $t) {
					if ($num_cia != $t['num_cia']) {
						$num_cia = $t['num_cia'];
						
						$result[$num_cia] = array(
							'nombre_cia' => $t['nombre_cia'],
							'piezas_1'   => 0,
							'piezas_2'   => 0
						);
					}
					
					$result[$num_cia]['piezas_' . $t['bloque']] = $t['piezas'];
				}
				
				$filas_por_hoja = 45;
				$filas = $filas_por_hoja;
				
				$totales = array(
					'total_1'    => 0,
					'total_2'    => 0,
					'diferencia' => 0
				);
				
				foreach ($result as $num_cia => $data_cia) {
					if ($filas == $filas_por_hoja) {
						$filas = 0;
						
						$tpl->newBlock('reporte');
						
						$tpl->assign('anio', $_REQUEST['anio']);
						$tpl->assign('mes', $_meses[$_REQUEST['mes']]);
						
						foreach ($anios as $bloque => $anio) {
							$tpl->assign('anio_' . $bloque, $anio);
						}
					}
					
					$tpl->newBlock('row');
					
					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', utf8_encode($data_cia['nombre_cia']));
					
					$tpl->assign('piezas_1', $data_cia['piezas_1'] > 0 ? number_format($data_cia['piezas_1'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('piezas_2', $data_cia['piezas_2'] > 0 ? number_format($data_cia['piezas_2'], 2, '.', ',') : '&nbsp;');
					
					$diferencia = $data_cia['piezas_1'] - $data_cia['piezas_2'];
					
					$tpl->assign('color', $diferencia > 0 ? 'blue' : 'red');
					$tpl->assign('diferencia', $diferencia != 0 ? number_format($diferencia, 2, '.', ',') : '&nbsp;');
					
					$totales['total_1'] += $data_cia['piezas_1'];
					$totales['total_2'] += $data_cia['piezas_2'];
					$totales['diferencia'] += $diferencia;
					
					$filas++;
				}
				
				$tpl->newBlock('totales');
				
				foreach ($totales as $key => $value) {
					$tpl->assign($key, $value != 0 ? number_format($value, 2, '.', ',') : '&nbsp;');
				}
				
				$tpl->assign('color', $value > 0 ? 'blue' : 'red');
			}
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/bal/ComparativoPiezasProducidas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

$tpl->newBlock($isIpad ? 'ipad' : 'normal');

if ($isIpad) {
	$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));
	$tpl->assign(date('n', mktime(0, 0, 0, date('n'), 0, date('Y'))), ' selected');
	
	$condiciones[] = 'num_cia <= 300';
	
	if (!in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 24, 37))) {
		$condiciones[] = '(co.iduser = ' . $_SESSION['iduser'] . ' OR ca.iduser = ' . $_SESSION['iduser'] . ')';
	}
	
	$sql = '
		SELECT
			num_cia,
			nombre_corto
				AS
					nombre_cia
		FROM
				catalogo_companias cc
			LEFT JOIN
				catalogo_administradores ca
					USING
						(
							idadministrador
						)
			LEFT JOIN
				catalogo_operadoras co
					USING
						(
							idoperadora
						)
		WHERE
			' . implode(' AND ', $condiciones) . '
		ORDER BY
			num_cia
	';
	$cias = $db->query($sql);
	
	foreach ($cias as $c) {
		$tpl->newBlock('cia');
		$tpl->assign('num_cia', $c['num_cia']);
		$tpl->assign('nombre_cia', $c['nombre_cia']);
	}
}
else {
	$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));
	$tpl->assign(date('n', mktime(0, 0, 0, date('n'), 0, date('Y'))), ' selected');
	
	$sql = '
		SELECT
			idadministrador
				AS
					id,
			nombre_administrador
				AS
					nombre
		FROM
			catalogo_administradores
		ORDER BY
			nombre
	';
	$admins = $db->query($sql);
	
	foreach ($admins as $a) {
		$tpl->newBlock('admin');
		$tpl->assign('id', $a['id']);
		$tpl->assign('nombre', utf8_encode($a['nombre']));
	}
}

$tpl->printToScreen();
?>
