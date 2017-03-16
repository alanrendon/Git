<?php

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

function ceil_thousands($value) {
	return ceil($value / 1000) * 1000;
}

$_meses = array(
	1  => 'ENE',
	2  => 'FEB',
	3  => 'MAR',
	4  => 'ABR',
	5  => 'MAY',
	6  => 'JUN',
	7  => 'JUL',
	8  => 'AGO',
	9  => 'SEP',
	10 => 'OCT',
	11 => 'NOV',
	12 => 'DIC'
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

//if ($_SESSION['iduser'] != 1) die('EN PROCESO DE ACTUALIZACION');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'inicio':
			$tpl = new TemplatePower('plantillas/ban/CompaniasCondependenciaTraspasoSaldoInicio.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
		break;
		
		case 'consultar':
			$condiciones = array();
			
			$condiciones[] = 'c.tsbaja IS NULL';
			
			$condiciones[] = 'sp.cuenta = ' . $_REQUEST['banco'];
			
			$condiciones[] = 'ss.cuenta = ' . $_REQUEST['banco'];
			
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
					$condiciones[] = 'c.num_cia_pri IN (' . implode(', ', $cias) . ')';
				}
			}
			
			$sql = '
				SELECT
					c.num_cia_pri,
					ccp.nombre
						AS nombre_cia_pri,
					ccp.clabe_cuenta
						AS cuenta_cia_pri,
					ROUND(sp.saldo_libros::NUMERIC, 2)
						AS saldo_cia_pri,
					c.num_cia_sec,
					ccs.nombre
						AS nombre_cia_sec,
					ccs.clabe_cuenta
						AS cuenta_cia_sec,
					ROUND(ss.saldo_libros::NUMERIC, 2)
						AS saldo_cia_sec,
					ROUND(COALESCE((
						SELECT
							SUM(total)
						FROM
							pasivo_proveedores
						WHERE
							num_cia = c.num_cia_sec
							AND num_cia > 0
					), 0)::NUMERIC, 2)
						AS saldo_pro
				FROM
					cias_condependencia c
					LEFT JOIN catalogo_companias ccp
						ON (ccp.num_cia = c.num_cia_pri)
					LEFT JOIN catalogo_companias ccs
						ON (ccs.num_cia = c.num_cia_sec)
					LEFT JOIN saldos sp
						ON (sp.num_cia = c.num_cia_pri)
					LEFT JOIN saldos ss
						ON (ss.num_cia = c.num_cia_sec)
				WHERE
					c.tsbaja IS NULL
					AND sp.cuenta = 1
					AND ss.cuenta = 1
				ORDER BY
					c.num_cia_pri,
					c.num_cia_sec
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ban/CompaniasCondependenciaTraspasoSaldoResultado.tpl');
			$tpl->prepare();
			
			if ($result) {
				$tpl->assign('banco', $_REQUEST['banco']);
				$tpl->assign('nombre_banco', $_REQUEST['banco'] == 1 ? 'BANORTE' : 'SANTANDER');
				
				$num_cia_pri = NULL;
				
				foreach ($result as $rec) {
					if ($num_cia_pri != $rec['num_cia_pri']) {
						$num_cia_pri = $rec['num_cia_pri'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $rec['num_cia_pri']);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia_pri']));
						$tpl->assign('cuenta', $rec['cuenta_cia_pri']);
						
						$total = 0;
						
						$row_color = FALSE;
					}
					
					$saldo_pro = round($rec['saldo_pro'], 2);
					
					$dif = $rec['saldo_cia_sec'] - ceil_thousands($saldo_pro) - 15000;
					
					$saldo_tra = $dif > 0 ? $dif : 0;
					
					$tpl->newBlock('row');
					
					$tpl->assign('row_color', $row_color ? 'on' : 'off');
					
					$row_color = !$row_color;
					
					$tpl->assign('num_cia_pri', $num_cia_pri);
					
					$tpl->assign('num_cia', $rec['num_cia_sec']);
					$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia_sec']));
					$tpl->assign('cuenta', $rec['cuenta_cia_sec']);
					$tpl->assign('saldo_cia', $rec['saldo_cia_sec'] != 0 ? number_format($rec['saldo_cia_sec'], 2) : '&nbsp;');
					$tpl->assign('saldo_pro', $rec['saldo_pro'] != 0 ? number_format($rec['saldo_pro'], 2) : '&nbsp;');
					$tpl->assign('saldo_tra', $saldo_tra != 0 ? number_format($saldo_tra, 2) : '&nbsp;');
					
					$tpl->assign('data', htmlentities(json_encode(array(
						'num_cia_pri' => intval($rec['num_cia_pri']),
						'cuenta_cia_pri' => $rec['cuenta_cia_pri'],
						'saldo_cia_pri' => floatval($rec['saldo_cia_pri']),
						'num_cia_sec' => intval($rec['num_cia_sec']),
						'cuenta_cia_sec' => $rec['cuenta_cia_sec'],
						'saldo_cia_sec' => floatval($rec['saldo_cia_sec']),
						'saldo_pro' => floatval($rec['saldo_pro']),
						'saldo_pro_rounded' => floatval($saldo_pro),
						'saldo_tra' => floatval($saldo_tra)
					))));
					
					$tpl->assign('disabled', $saldo_tra > 0 ? '' : ' disabled');
					
					$total += $saldo_tra;
					
					$tpl->assign('cia.total', number_format($total, 2));
				}
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'seleccionarTipo':
			$tpl = new TemplatePower('plantillas/ban/CompaniasCondependenciaTraspasoSaldoTipo.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
		break;
		
		case 'traspasar':
			$ts = date('d/m/Y H:i:s');
			
			foreach ($_REQUEST['data'] as $data_string) {
				$data = json_decode($data_string);
				
				if ($_REQUEST['tipo'] == 2 && !($pro = $db->query('
					SELECT
						num_proveedor
							AS num_pro,
						nombre
							AS nombre_pro
					FROM
						catalogo_proveedores
					WHERE
						cuenta = (
							SELECT
								' . ($_REQUEST['banco'] == 2 ? 'clabe_cuenta2' : 'clabe_cuenta') . '
							FROM
								catalogo_companias
							WHERE
								num_cia = ' . $data->num_cia_pri . '
								AND LENGTH(TRIM(' . ($_REQUEST['banco'] == 2 ? 'clabe_cuenta2' : 'clabe_cuenta') . ')) = 11
						)
				'))) {
					continue;
				}
				
				$sql = '
					SELECT
						COALESCE(MAX(folio), 50) + 1
							AS folio
					FROM
						folios_cheque
					WHERE
						cuenta = ' . $_REQUEST['banco'] . '
						AND num_cia = ' . $data->num_cia_sec . '
				';
				
				$tmp = $db->query($sql);
				
				$folio = $tmp[0]['folio'];
				
				$sql = '
					INSERT INTO
						cheques (
							cod_mov,
							num_proveedor,
							num_cia,
							fecha,
							folio,
							importe,
							iduser,
							a_nombre,
							imp,
							codgastos,
							cuenta,
							archivo,
							poliza,
							site,
							tsmod,
							concepto
						)
					VALUES (
						' . ($_REQUEST['tipo'] == 2 ? 41 : 5) . ',
						' . ($_REQUEST['tipo'] == 2 ? $pro[0]['num_pro'] : 5001) . ',
						' . $data->num_cia_sec . ',
						NOW()::DATE,
						' . $folio . ',
						' . $data->saldo_tra . ',
						' . $_SESSION['iduser'] . ',
						' . ($_REQUEST['tipo'] == 2 ? '\'' . $pro[0]['nombre_pro'] . '\'' : '(
							SELECT
								razon_social
							FROM
								catalogo_companias
							WHERE
								num_cia = ' . $data->num_cia_pri . '
						)') . ',
						FALSE,
						32,
						' . $_REQUEST['banco'] . ',
						TRUE,
						' . ($_REQUEST['tipo'] == 2 ? 'TRUE' : 'FALSE') . ',
						FALSE,
						\'' . $ts . '\',
						\'TRASPASO DE SALDO\'
					)
				' . ";\n";
				
				$sql .= '
					INSERT INTO
						estado_cuenta (
							num_cia,
							fecha,
							tipo_mov,
							importe,
							cod_mov,
							folio,
							cuenta,
							iduser,
							timestamp,
							tipo_con,
							concepto
						)
					VALUES (
						' . $data->num_cia_sec . ',
						NOW()::DATE,
						TRUE,
						' . $data->saldo_tra . ',
						' . ($_REQUEST['tipo'] == 2 ? 41 : 5) . ',
						' . $folio . ',
						' . $_REQUEST['banco'] . ',
						' . $_SESSION['iduser'] . ',
						\'' . $ts . '\',
						0,
						\'TRASPASO DE SALDO DE POLLOS A PANADERIA ' . $data->num_cia_pri . ' \' || (
							SELECT
								razon_social
							FROM
								catalogo_companias
							WHERE
								num_cia = ' . $data->num_cia_pri . '
						) || \' ' . $folio . '\'
					)
				' . ";\n";
				
				$sql .= '
					INSERT INTO
						estado_cuenta (
							num_cia,
							fecha,
							tipo_mov,
							importe,
							cod_mov,
							folio,
							cuenta,
							iduser,
							timestamp,
							tipo_con,
							concepto
						)
					VALUES (
						' . $data->num_cia_pri . ',
						NOW()::DATE,
						FALSE,
						' . $data->saldo_tra . ',
						29,
						NULL,
						' . $_REQUEST['banco'] . ',
						' . $_SESSION['iduser'] . ',
						\'' . $ts . '\',
						0,
						\'TRASPASO DE SALDO DE POLLOS A PANADERIA ' . $data->num_cia_pri . ' \' || (
							SELECT
								razon_social
							FROM
								catalogo_companias
							WHERE
								num_cia = ' . $data->num_cia_pri . '
						) || \' ' . $folio . '\'
					)
				' . ";\n";
				
				if ($_REQUEST['tipo'] == 2) {
					$sql .= '
						INSERT INTO
							transferencias_electronicas (
								num_cia,
								num_proveedor,
								folio,
								importe,
								fecha_gen,
								status,
								iduser,
								cuenta
							) VALUES (
								' . $data->num_cia_sec . ',
								' . $pro[0]['num_pro'] . ',
								' . $folio . ',
								' . $data->saldo_tra . ',
								NOW()::DATE,
								0,
								' . $_SESSION['iduser'] . ',
								' . $_REQUEST['banco'] . '
							)
					' . ";\n";
				}
				
				$sql .= '
					INSERT INTO
						movimiento_gastos (
							codgastos,
							num_cia,
							fecha,
							importe,
							captura,
							folio,
							concepto,
							cuenta
						)
					VALUES (
						32,
						' . $data->num_cia_sec . ',
						NOW()::DATE,
						' . $data->saldo_tra . ',
						TRUE,
						' . $folio . ',
						\'TRASPASO DE SALDO DE POLLOS A PANADERIA ' . $data->num_cia_pri . ' \' || (
							SELECT
								razon_social
							FROM
								catalogo_companias
							WHERE
								num_cia = ' . $data->num_cia_pri . '
						) || \' ' . $folio . ')\',
						' . $_REQUEST['banco'] . '
					)
				' . ";\n";
				
				$sql .= '
					INSERT INTO
						folios_cheque (
							folio,
							num_cia,
							reservado,
							utilizado,
							fecha,
							cuenta
						)
					VALUES (
						' . $folio . ',
						' . $data->num_cia_sec . ',
						FALSE,
						TRUE,
						NOW()::DATE,
						' . $_REQUEST['banco'] . '
					)
				' . ";\n";
				
				$sql .= '
					UPDATE
						saldos
					SET
						saldo_libros = saldo_libros - ' . $data->saldo_tra . '
					WHERE
						num_cia = ' . $data->num_cia_sec . '
						AND cuenta = ' . $_REQUEST['banco'] . '
				' . ";\n";
				
				$sql .= '
					UPDATE
						saldos
					SET
						saldo_libros = saldo_libros + ' . $data->saldo_tra . '
					WHERE
						num_cia = ' . $data->num_cia_pri . '
						AND cuenta = ' . $_REQUEST['banco'] . '
				' . ";\n";
				
				$db->query($sql);
			}
			
			echo $ts;
		break;
		
		case 'listado':
			$sql = '
				SELECT
					num_cia,
					nombre
						AS nombre_cia,
					cuenta,
					folio,
					fecha,
					concepto,
					importe
				FROM
					estado_cuenta
					LEFT JOIN catalogo_companias
						USING (num_cia)
				WHERE
					timestamp = \'' . $_REQUEST['ts'] . '\'
					AND tipo_mov = TRUE
				ORDER BY
					num_cia,
					folio
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ban/CompaniasCondependenciaTraspasoSaldoListado.tpl');
			$tpl->prepare();
			
			$tpl->newBlock('reporte');
			
			$tpl->assign('banco', $result[0]['cuenta'] == 1 ? 'BANORTE' : 'SANTANDER');
			$tpl->assign('fecha', $result[0]['fecha']);
			
			$total = 0;
			
			foreach ($result as $rec) {
				$tpl->newBlock('row');
				
				$tpl->assign('num_cia', $rec['num_cia']);
				$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
				$tpl->assign('folio', $rec['folio']);
				$tpl->assign('concepto', utf8_encode($rec['concepto']));
				$tpl->assign('importe', number_format($rec['importe'], 2));
				
				$total += $rec['importe'];
			}
			
			$tpl->assign('reporte.total', number_format($total, 2));
			
			$tpl->printToScreen();
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ban/CompaniasCondependenciaTraspasoSaldo.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
