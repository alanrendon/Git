<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

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
	12 => 'DICIEMBRE'
);

$_dias = array(
	0 => 'DOMINGO',
	1 => 'LUNES',
	2 => 'MARTES',
	3 => 'MIERCOLES',
	4 => 'JUEVES',
	5 => 'VIERNES',
	6 => 'SABADO'
);

$db = new DBclass($dsn, 'autocommit=yes');
//$db = new DBclass('pgsql://mollendo:pobgnj@127.0.0.1:5432/backup', 'autocommit=yes');
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die('EN PROCESO DE ACTUALIZACION');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'consultar':
			$condiciones = array();
			
			if ($_SESSION['iduser'] != 1) {
				$condiciones[] =  'num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
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
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			$condiciones[] = 'banco IN (' . implode(', ', $_REQUEST['banco']) . ')';
			
			$subcondiciones = array();
			
			$subcondiciones[] = 'ROUND((saldo_sistema + depositos_pendientes - cargos_pendientes)::NUMERIC, 2) <> saldo_banco';
			
			if (isset($_REQUEST['sin_cuenta'])) {
				$subcondiciones[] = 'cuenta IS NULL';
				$subcondiciones[] = 'LENGTH(TRIM(cuenta)) < 10';
			}
			
			if (isset($_REQUEST['sin_saldo_banco'])) {
				$pieces = array();
				
				$pieces[] = 'saldo_banco IS NULL';
				
				if (!isset($_REQUEST['sin_cuenta'])) {
					$pieces[] = 'cuenta IS NOT NULL';
					$pieces[] = 'LENGTH(TRIM(cuenta)) >= 10';
				}
				
				if (!isset($_REQUEST['sin_movimientos_pendientes'])) {
					$pieces[] = '(depositos_no_conciliados <> 0 OR cargos_no_conciliados <> 0 OR depositos_pendientes <> 0 OR cargos_pendientes <> 0)';
				}
				
				$subcondiciones[] = '(' . implode(' AND ', $pieces) . ')';
			}
			
			if (isset($_REQUEST['sin_movimientos_pendientes'])) {
				$subcondiciones[] = '(depositos_no_conciliados = 0 AND cargos_no_conciliados = 0 AND depositos_pendientes = 0 AND cargos_pendientes = 0)';
			}
			
			$condiciones[] = '(' . implode(' OR ', $subcondiciones) . ')';
			
			$sql = '
				SELECT
					*,
					saldo_sistema + depositos_pendientes - cargos_pendientes
						AS saldo_total,
					CASE
						WHEN saldo_banco IS NULL THEN
							NULL
						ELSE
							ROUND((saldo_sistema + depositos_pendientes - cargos_pendientes)::NUMERIC, 2) - saldo_banco
					END
						AS diferencia
				FROM
					(
						SELECT
							num_cia,
							nombre_corto
								AS nombre_cia,
							cuenta
								AS banco,
							CASE
								WHEN cuenta = 1 THEN
									clabe_cuenta
								WHEN cuenta = 2 THEN
									clabe_cuenta2
							END
								AS cuenta,
							ROUND(saldo_bancos::NUMERIC, 2)
								AS saldo_sistema,
							CASE
								WHEN cuenta = 1 THEN
									(
										SELECT
											ROUND(saldo::NUMERIC, 2)
										FROM
											saldo_banorte
										WHERE
											num_cia = s.num_cia
									)
								WHEN cuenta = 2 THEN
									(
										SELECT
											ROUND(saldo::NUMERIC, 2)
										FROM
											saldo_santander
										WHERE
											num_cia = s.num_cia
									)
							END
								AS saldo_banco,
							COALESCE(
								(
									SELECT
										SUM(importe)
									FROM
										estado_cuenta
									WHERE
										num_cia = s.num_cia
										AND cuenta = s.cuenta
										AND fecha_con IS NULL
										AND tipo_mov = FALSE
								), 0
							)
								AS depositos_no_conciliados,
							COALESCE(
								(
									SELECT
										SUM(importe)
									FROM
										estado_cuenta
									WHERE
										num_cia = s.num_cia
										AND cuenta = s.cuenta
										AND fecha_con IS NULL
										AND tipo_mov = TRUE
								), 0
							)
								AS cargos_no_conciliados,
							COALESCE(
								(
									CASE
										WHEN cuenta = 1 THEN
											(
												SELECT
													SUM(importe)
												FROM
													mov_banorte
												WHERE
													num_cia = s.num_cia
													AND fecha_con IS NULL
													AND tipo_mov = FALSE
											)
										WHEN cuenta = 2 THEN
											(
												SELECT
													SUM(importe)
												FROM
													mov_santander
												WHERE
													num_cia = s.num_cia
													AND fecha_con IS NULL
													AND tipo_mov = FALSE
											)
									END
								), 0
							)
								AS depositos_pendientes,
							COALESCE(
								(
									CASE
										WHEN cuenta = 1 THEN
											(
												SELECT
													SUM(importe)
												FROM
													mov_banorte
												WHERE
													num_cia = s.num_cia
													AND fecha_con IS NULL
													AND tipo_mov = TRUE
											)
										WHEN cuenta = 2 THEN
											(
												SELECT
													SUM(importe)
												FROM
													mov_santander
												WHERE
													num_cia = s.num_cia
													AND fecha_con IS NULL
													AND tipo_mov = TRUE
											)
									END
								), 0
							)
								AS cargos_pendientes
						FROM
							saldos s
							LEFT JOIN catalogo_companias cc
								USING (num_cia)
					) saldos
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					banco,
					num_cia
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ban/DiferenciaSaldosConciliadosReporte.tpl');
			$tpl->prepare();
			
			$condiciones = array();
			
			if ($_SESSION['iduser'] != 1) {
				$condiciones[] =  'num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
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
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			$condiciones[] = 'cuenta IN (' . implode(', ', $_REQUEST['banco']) . ')';
			
			$sql = '
				DELETE FROM
					dif_saldos
				WHERE
					' . implode(' AND ', $condiciones) . '
			' . ";\n";
			
			if ($result) {
				$banco = NULL;
				
				foreach ($result as $rec) {
					if ($banco != $rec['banco']) {
						$banco = $rec['banco'];
						
						$tpl->newBlock('reporte');
						$tpl->assign('banco', $banco == 1 ? 'BANORTE' : 'SANTANDER');
						$tpl->assign('dia_escrito', $_dias[date('w')]);
						$tpl->assign('dia', date('j'));
						$tpl->assign('mes_escrito', $_meses[date('n')]);
						$tpl->assign('anio', date('Y'));
						$tpl->assign('hora', date('h:iA'));
						
						$totales = array(
							'saldo_sistema'        => 0,
							'depositos_pendientes' => 0,
							'cargos_pendientes'    => 0,
							'saldo_total'          => 0,
							'saldo_banco'          => 0,
							'diferencia'           => 0
						);
					}
					
					$tpl->newBlock('row');
					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('nombre_cia', $rec['nombre_cia']);
					$tpl->assign('cuenta', $rec['cuenta']);
					$tpl->assign('saldo_sistema', $rec['saldo_sistema'] != 0 ? number_format($rec['saldo_sistema'], 2) : '&nbsp;');
					$tpl->assign('depositos_pendientes', $rec['depositos_pendientes'] != 0 ? number_format($rec['depositos_pendientes'], 2) : '&nbsp;');
					$tpl->assign('cargos_pendientes', $rec['cargos_pendientes'] != 0 ? number_format($rec['cargos_pendientes'], 2) : '&nbsp;');
					$tpl->assign('saldo_total', $rec['saldo_total'] != 0 ? number_format($rec['saldo_total'], 2) : '&nbsp;');
					$tpl->assign('color_saldo_total', $rec['saldo_total'] < 0 ? 'red' : 'blue');
					$tpl->assign('saldo_banco', $rec['saldo_banco'] != 0 ? number_format($rec['saldo_banco'], 2) : '&nbsp;');
					$tpl->assign('diferencia', $rec['saldo_banco'] == '' ? 'NO HAY SALDO DEL BANCO' : ($rec['diferencia'] != 0 ? number_format($rec['diferencia'], 2) : ''));
					$tpl->assign('color_diferencia', $rec['saldo_banco'] == '' ? 'orange' : ($rec['diferencia'] < 0 ? 'red' : 'blue'));
					
					$totales['saldo_sistema'] += $rec['saldo_sistema'];
					$totales['depositos_pendientes'] += $rec['depositos_pendientes'];
					$totales['cargos_pendientes'] += $rec['cargos_pendientes'];
					$totales['saldo_total'] += $rec['saldo_total'];
					$totales['saldo_banco'] += $rec['saldo_banco'];
					$totales['diferencia'] += $rec['diferencia'];
					
					foreach ($totales as $key => $value) {
						$tpl->assign('reporte.' . $key, $value != 0 ? number_format($value, 2, '.', ',') : '&nbsp;');
					}
					
					if ($rec['saldo_banco'] != '' && $rec['diferencia'] != 0) {
						$sql .= '
							UPDATE
								saldos
							SET
								tsdif = now()
							WHERE
								num_cia = ' . $rec['num_cia'] . '
								AND cuenta = ' . $rec['banco'] . '
								AND tsdif IS NULL
						' . ";\n";
						
						$sql .= '
							INSERT INTO
								dif_saldos
									(
										num_cia,
										cuenta,
										saldo_sistema,
										saldo_banco
									)
								VALUES
									(
										' . $rec['num_cia'] . ',
										' . $rec['banco'] . ',
										' . $rec['saldo_total'] . ',
										' . $rec['saldo_banco'] . '
									)
						' . ";\n";
					}
					else {
						$sql .= '
							UPDATE
								saldos
							SET
								tsdif = NULL
							WHERE
								num_cia = ' . $rec['num_cia'] . '
								AND cuenta = ' . $rec['banco'] . '
								AND tsdif IS NOT NULL
						' . ";\n";
					}
				}
			}
			
			if ($sql != '') {
				$db->query($sql);
			}
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ban/DiferenciaSaldosConciliados.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
