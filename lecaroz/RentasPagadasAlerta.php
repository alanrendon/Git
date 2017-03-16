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
			$fecha = date('d/m/Y', mktime(0, 0, 0, 12, 1));
			
			$fecha_actual = date('Y') * 12 + date('n');
			
			$condiciones = array();
			
			$condiciones[] = 'arr.tsbaja IS NULL';
			
			$condiciones[] = 'arr.bloque = 2';
			
			$sql = '
				SELECT
					idarrendatario,
					arrendador,
					nombre_arrendador,
					arrendatario,
					alias_arrendatario
						AS nombre_arrendatario,
					tipo_local,
					giro,
					contacto,
					telefono1,
					telefono2,
					email,
					fecha_inicio,
					fecha_termino,
					CASE
						WHEN fecha_termino < NOW()::DATE THEN
							-1
						ELSE
							0
					END
						AS contrato_vencido,
					EXTRACT(YEAR FROM fecha_inicio)::NUMERIC * 12 + EXTRACT(MONTH FROM fecha_inicio)::NUMERIC
						AS inicio,
					EXTRACT(YEAR FROM fecha_termino)::NUMERIC * 12 + EXTRACT(MONTH FROM fecha_termino)::NUMERIC
						AS termino
				FROM
					rentas_arrendatarios arr
					LEFT JOIN rentas_arrendadores inm
						USING (idarrendador)
					LEFT JOIN rentas_locales loc
						USING (idlocal)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					arrendador,
					arrendatario
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ren/RentasPagadasAlerta.tpl');
			$tpl->prepare();
			
			if ($result) {
				$arrendadores = array();
				
				foreach ($result as $rec) {
					if (!isset($arrendadores[$rec['arrendador']])) {
						$arrendadores[$rec['arrendador']] = array(
							'nombre'        => utf8_encode($rec['nombre_arrendador']),
							'arrendatarios' => array()
						);
					}
					
					$arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']] = array(
						'id'               => $rec['idarrendatario'],
						'nombre'           => utf8_encode($rec['nombre_arrendatario']),
						'tipo_local'       => $rec['tipo_local'],
						'giro'             => utf8_encode($rec['giro']),
						'contacto'         => utf8_encode($rec['contacto']),
						'telefono1'        => $rec['telefono1'],
						'telefono2'        => $rec['telefono2'],
						'email'            => $rec['email'],
						'fecha_inicio'     => $rec['fecha_inicio'],
						'fecha_termino'    => $rec['fecha_termino'],
						'contrato_vencido' => $rec['contrato_vencido'],
						'inicio'           => $rec['inicio'],
						'termino'          => $rec['termino'],
						'status'           => array_fill_keys(range(($_REQUEST['anio'] - 1) * 12 + 7, $_REQUEST['anio'] * 12 + 12), 0),
						'vencido'          => array_fill_keys(range(($_REQUEST['anio'] - 1) * 12 + 7, $_REQUEST['anio'] * 12 + 12), FALSE)
					);
					
					foreach ($arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['status'] as $mes => &$status) {
						if ($mes < $arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['inicio']) {
							$status = 4;
						}
						
						if ($mes >= $arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['inicio']
							&& $mes <= $arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['termino']
							&& $mes <= $fecha_actual) {
							$status = -1;
						}
						
						if ($arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['termino'] == $mes) {
							$arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['vencido'][$mes] = TRUE;
						}
					}
				}
				
				$sql = '
					SELECT
						idarrendador,
						arrendador,
						idarrendatario,
						arrendatario,
						EXTRACT(YEAR FROM fecha_renta)::NUMERIC * 12 + EXTRACT(MONTH FROM fecha_renta)::NUMERIC
							AS mes,
						CASE
							WHEN fecha_con IS NOT NULL THEN
								1
							ELSE
								2
						END
							AS status
					FROM
						estado_cuenta ec
						LEFT JOIN rentas_arrendatarios arr
							USING (idarrendatario)
						LEFT JOIN rentas_arrendadores inm
							USING (idarrendador)
					WHERE
						cod_mov = 2
						AND fecha_renta BETWEEN \'' . $fecha . '\'::DATE - INTERVAL \'1 YEAR 5 MONTHS\' AND \'' . $fecha . '\'::DATE
						AND arr.tsbaja IS NULL
					ORDER BY
						idarrendatario,
						mes
				';
				
				$result = $db->query($sql);
				
				if ($result) {
					foreach ($result as $rec) {
						if (isset($arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']])) {
							$arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['status'][$rec['mes']] = $rec['status'];
						}
					}
				}
				
				$sql = '
					SELECT
						idarrendador,
						arrendador,
						idarrendatario,
						arrendatario,
						anio::NUMERIC * 12 + mes::NUMERIC
							AS mes,
						status
					FROM
						estatus_locales est
						LEFT JOIN rentas_arrendatarios arr
							USING (idarrendatario)
						LEFT JOIN rentas_arrendadores inm
							USING (idarrendador)
					WHERE
						fecha BETWEEN \'' . $fecha . '\'::DATE - INTERVAL \'1 YEAR 5 MONTHS\' AND \'' . $fecha . '\'::DATE
						AND arr.tsbaja IS NULL
						AND status IS NOT NULL
						AND (idarrendatario, fecha) NOT IN (
							SELECT
								idarrendatario,
								fecha
							FROM
								estado_cuenta ec
								LEFT JOIN rentas_arrendatarios arr
									USING (idarrendatario)
							WHERE
								cod_mov = 2
								AND fecha_renta BETWEEN \'' . $fecha . '\'::DATE - INTERVAL \'1 YEAR 5 MONTHS\' AND \'' . $fecha . '\'::DATE
								AND arr.tsbaja IS NULL
						)
					ORDER BY
						idarrendatario,
						mes
				';
				
				$result = $db->query($sql);
				
				if ($result) {
					foreach ($result as $rec) {
						if (isset($arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']])) {
							$arrendadores[$rec['arrendador']]['arrendatarios'][$rec['arrendatario']]['status'][$rec['mes']] = $rec['status'];
						}
					}
				}
				
				function filtro($value) {
					return $value < 0;
				}
				
				foreach ($arrendadores as $arrendador => $datos_arrendador) {
					foreach ($datos_arrendador['arrendatarios'] as $arrendatario => $datos_arrendatario) {
						if (!array_filter($datos_arrendatario['status'], 'filtro')) {
							unset($arrendadores[$arrendador]['arrendatarios'][$arrendatario]);
							
							if (count($arrendadores[$arrendador]['arrendatarios']) == 0) {
								unset($arrendadores[$arrendador]['arrendatarios']);
							}
						}
					}
				}
				
				if ($arrendadores) {
					$tpl = new TemplatePower('plantillas/ren/RentasPagadasAlerta.tpl');
					$tpl->prepare();
					
					foreach ($arrendadores as $arrendador => $datos_arrendador) {
						if ($datos_arrendador['arrendatarios']) {
							$tpl->newBlock('arrendador');
							
							$tpl->assign('arrendador', $arrendador);
							$tpl->assign('nombre_arrendador', $datos_arrendador['nombre']);
							
							$tpl->assign('anio1', date('Y') - 1);
							$tpl->assign('anio2', date('Y'));
							
							foreach ($datos_arrendador['arrendatarios'] as $arrendatario => $datos_arrendatario) {
								$tpl->newBlock('arrendatario');
								
								$tpl->assign('arrendatario', $arrendatario);
								$tpl->assign('nombre_arrendatario', $datos_arrendatario['nombre']);
								$tpl->assign('giro', $datos_arrendatario['giro']);
								$tpl->assign('fecha_termino', $datos_arrendatario['fecha_termino']);
								$tpl->assign('vencido', $datos_arrendatario['contrato_vencido'] < 0 ? ' vencido' : '');
								
								$cont = 0;
								foreach ($datos_arrendatario['status'] as $i => $_status) {
									if ($_status == 0) {
										$tpl->assign($cont++, $datos_arrendatario['vencido'][$i] ? '<img src="/lecaroz/imagenes/bloque_blanco_rojo.png" width="24" height="16" />' : '&nbsp;');
									}
									else if ($_status == 1) {
										$tpl->assign($cont++, '<img src="/lecaroz/imagenes/bloque_negro' . ($datos_arrendatario['vencido'][$i] ? '_rojo' : '') . '.png" width="24" height="16" />');
									}
									else if ($_status == 2) {
										$tpl->assign($cont++, '<img src="/lecaroz/imagenes/bloque_azul' . ($datos_arrendatario['vencido'][$i] ? '_rojo' : '') . '.png" width="24" height="16" />');
									}
									else if ($_status == 3) {
										$tpl->assign($cont++, '<img src="/lecaroz/imagenes/bloque_verde' . ($datos_arrendatario['vencido'][$i] ? '_rojo' : '') . '.png" width="24" height="16" />');
									}
									else if ($_status == 4) {
										$tpl->assign($cont++, '<span class="purple bold">VA</span>');
									}
									else if ($_status == -1) {
										$tpl->assign($cont++, $datos_arrendatario['vencido'][$i] ? '<img src="/lecaroz/imagenes/bloque_blanco_rojo.png" width="24" height="16" />' : '&nbsp;');
									}
									else if ($_status == -2) {
										$tpl->assign($cont++, '<span class="orange bold">DG</span>');
									}
									
								}
							}
						}
					}
				}
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ren/RentasPagadasAlertaInicio.tpl');
$tpl->prepare();

$tpl->printToScreen();

?>
