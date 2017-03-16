<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

if (!function_exists('json_encode')) {
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
	1  => 'Ene',
	2  => 'Feb',
	3  => 'Mar',
	4  => 'Abr',
	5  => 'May',
	6  => 'Jun',
	7  => 'Jul',
	8  => 'Ago',
	9  => 'Sep',
	10 => 'Oct',
	11 => 'Nov',
	12 => 'Dic'
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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'reporte':
			$fecha = date('d/m/Y', mktime(0, 0, 0, date('n') + (isset($_REQUEST['meses']) ? $_REQUEST['meses'] : 0), 1, date('Y')));
			
			$condiciones[] = 'ra.tsbaja IS NULL';
			
			$condiciones[] = 'total > 0';
			
			if (!isset($_REQUEST['internos'])) {
				$condiciones[] = 'bloque <> 1';
			}
			
			if (!isset($_REQUEST['externos'])) {
				$condiciones[] = 'bloque <> 2';
			}
			
			if (isset($_REQUEST['arrendadores']) && trim($_REQUEST['arrendadores']) != '') {
				$arrendadores = array();
				
				$pieces = explode(',', $_REQUEST['arrendadores']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$arrendadores[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$arrendadores[] = $piece;
					}
				}
				
				if (count($arrendadores) > 0) {
					$condiciones[] = (isset($_REQUEST['mancomunar']) ? 'homoclave' : 'arrendador') . ' IN (' . implode(', ', $arrendadores) . ')';
				}
			}
			
			$sql = '
				SELECT
					*
				FROM
					(
						SELECT
							arrendador,
							nombre_arrendador,
							idarrendatario,
							arrendatario,
							alias_arrendatario,
							nombre_arrendatario,
							giro,
							alias_local,
							bloque,
							orden,
							homoclave,
							fecha_inicio,
							fecha_termino,
							renta,
							mantenimiento,
							subtotal,
							iva,
							agua,
							retencion_iva,
							retencion_isr,
							total,
							CASE
								/**
								* Sin incremento anual
								*/
								WHEN incremento_anual = TRUE AND renta <= COALESCE((
									SELECT
										SUM(renta)
									FROM
										rentas_recibos
									WHERE
										idarrendatario = ra.idarrendatario
										AND fecha = \'' . $fecha . '\'::DATE - INTERVAL \'1 YEAR\'
										AND tsbaja IS NULL
								), 0) THEN
									-2
								/**
								* Contrato vencido
								*/
								WHEN (\'01\' || \'/\' || EXTRACT(MONTH FROM fecha_termino) || \'/\' || EXTRACT(YEAR FROM fecha_termino))::DATE < \'' . $fecha . '\'::DATE THEN
									-1
								/**
								* Contrato nuevo
								*/
								WHEN AGE(\'' . $fecha . '\', fecha_inicio) BETWEEN INTERVAL \'1 YEAR\' AND \'1 YEAR 2 MONTHS\' THEN
									-3
								ELSE
									0
							END
								AS status
						FROM
							rentas_arrendatarios ra
							LEFT JOIN rentas_arrendadores ri
								USING (idarrendador)
							LEFT JOIN rentas_locales rl
								USING (idlocal)
						WHERE
							' . implode(' AND ', $condiciones) . '
					)
						AS result
				WHERE
					status < 0
				ORDER BY
					arrendador,
					fecha_termino
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ren/RentasArrendatariosVencimientoReporte.tpl');
			$tpl->prepare();
			
			if ($result) {
				$tpl->newBlock('reporte');
				
				$arrendador = NULL;
				
				foreach ($result as $rec) {
					if ($arrendador != $rec['arrendador']) {
						$arrendador = $rec['arrendador'];
						
						$tpl->newBlock('arrendador');
						$tpl->assign('arrendador', $arrendador);
						$tpl->assign('nombre_arrendador', utf8_encode($rec['nombre_arrendador']));
					}
					
					$tpl->newBlock('arrendatario');
					$tpl->assign('arrendatario', $rec['arrendatario']);
					$tpl->assign('alias_arrendatario', utf8_encode($rec['alias_arrendatario']));
					$tpl->assign('nombre_arrendatario', utf8_encode($rec['nombre_arrendatario']));
					$tpl->assign('giro', utf8_encode($rec['giro']));
					$tpl->assign('periodo_arrendamiento', $rec['fecha_inicio'] . '-' . $rec['fecha_termino']);
					$tpl->assign('renta', number_format($rec['total'], 2));
					
					switch ($rec['status']) {
						case -1:
							$tpl->assign('tipo_vencimiento', 'VENCIMIENTO DE CONTRATO');
						break;
						
						case -2:
							$tpl->assign('tipo_vencimiento', 'SIN INCREMENTO ANUAL');
						break;
						
						case -3:
							$tpl->assign('tipo_vencimiento', 'ARRENDATARIO NUEVO');
						break;
					}
				}
			}
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ren/RentasArrendatariosVencimiento.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y'));

$tpl->printToScreen();

?>
