<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'inicio':
			$tpl = new TemplatePower('plantillas/ban/ConciliarDepositosComisionesInicio.tpl');
			$tpl->prepare();
			
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
			
			echo $tpl->getOutputContent();
		break;
		
		case 'codigos':
			switch ($_REQUEST['banco']) {
				case 1:
					$tabla = 'mov_banorte';
				break;
				
				case 2:
					$tabla = 'mov_santander';
				break;
			}
			
			$condiciones = array();
			
			if (!in_array($_SESSION['iduser'], array(1))) {
				if ($_SESSION['tipo_usuario'] == 2) {
					$condiciones[] = 'num_cia BETWEEN 900 AND 998';
				}
				else {
					$condiciones[] = 'num_cia BETWEEN 1 AND 899';
				}
			}
			else {
				$condiciones[] = 'num_cia > 0';
			}
			
			$condiciones[] = 'fecha_con IS NULL';
			
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
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') || (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') && (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
					$condiciones[] = 'fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				}
				else if (isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') {
					$condiciones[] = 'fecha = \'' . $_REQUEST['fecha1'] . '\'';
				}
				else if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '') {
					$condiciones[] = 'fecha >= \'' . $_REQUEST['fecha2'] . '\'';
				}
			}
			
			$sql = '
				SELECT
					cod_banco
						AS
							cod,
					CASE
						WHEN tipo_mov = \'TRUE\' THEN
							\'#C00\'
						ELSE
							\'#00C\'
					END
						AS
							tipo,
					MIN(concepto)
						AS
							concepto
				FROM
					' . $tabla . ' movs
				LEFT JOIN
					catalogo_companias cc
						USING
							(num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					cod,
					tipo
				ORDER BY
					cod
			';
			$result = $db->query($sql);
			
			if ($result) {
				echo '{"codigos":[';
				
				foreach ($result as $r) {
					$data[] = '{"value":"' . $r['cod'] . '","text":"[' . $r['cod'] . '] '  . utf8_encode($r['concepto']) . '","styles":{"color":"' . $r['tipo'] . '"}}';
				}
				
				echo implode(',', $data) . ']}';
			}
		break;
		
		case 'buscar':
			$tabla_movs = $_REQUEST['banco'] == 1 ? 'mov_banorte' : 'mov_santander';
			$tabla_cods = $_REQUEST['banco'] == 1 ? 'catalogo_mov_bancos' : 'catalogo_mov_santander';
			
			$condiciones = array();
			
			if (!in_array($_SESSION['iduser'], array(1))) {
				if ($_SESSION['iduser'] == 2) {
					$condiciones[] = 'num_cia BETWEEN 900 AND 998';
				}
				else {
					$condiciones[] = 'num_cia BETWEEN 1 AND 899';
				}
			}
			else {
				$condiciones[] = 'num_cia > 0';
			}
			
			$condiciones[] = 'fecha_con IS NULL';
			
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
			
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') || (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') && (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
					$condiciones[] = 'fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				}
				else if (isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') {
					$condiciones[] = 'fecha = \'' . $_REQUEST['fecha1'] . '\'';
				}
				else if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '') {
					$condiciones[] = 'fecha >= \'' . $_REQUEST['fecha2'] . '\'';
				}
			}
			
			if (isset($_REQUEST['codigos'])) {
				$condiciones[] = 'cod_banco IN (' . implode(', ', $_REQUEST['codigos']). ')';
			}
			
			$sql = '
				SELECT
					id,
					num_cia,
					nombre_corto AS nombre_cia,
					fecha,
					CASE
						WHEN tipo_mov = \'TRUE\' THEN
							\'#C00\'
						ELSE
							\'#00C\'
					END
						AS
							color,
					tipo_mov
						AS
							tipo,
					cod_banco,
					concepto,
					importe
				FROM
						' . $tabla_movs . ' movs
					LEFT JOIN
						catalogo_companias cc
							USING
								(num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia,
					fecha,
					tipo,
					importe DESC
			';
			$result = $db->query($sql);
			
			if ($result) {
				$sql = '
					SELECT
						cod_banco,
						tipo_mov
							AS
								tipo,
						MIN(concepto)
							AS
								concepto
					FROM
						' . $tabla_movs . ' movs
					LEFT JOIN
						catalogo_companias cc
							USING
								(num_cia)
					WHERE
						' . implode(' AND ', $condiciones) . '
					GROUP BY
						cod_banco,
						tipo
					ORDER BY
						cod_banco DESC
				';
				$cod_banco = $db->query($sql);
				
				$tpl = new TemplatePower('plantillas/ban/ConciliarDepositosComisionesResultado.tpl');
				$tpl->prepare();
				
				$tpl->assign('banco', $_REQUEST['banco']);
				$tpl->assign('nombre_banco', $_REQUEST['banco'] == 1 ? 'BANORTE' : 'SANTANDER');
				
				$color = FALSE;
				foreach ($cod_banco as $cb) {
					$tpl->newBlock('cod_banco');
					$tpl->assign('color', $color ? 'on' : 'off');
					$tpl->assign('cod_banco', $cb['cod_banco']);
					$tpl->assign('concepto', $cb['concepto']);
					
					$color = !$color;
					
					$sql = '
						SELECT
							cod_mov
								AS
									cod,
							descripcion
								AS
									concepto
						FROM
							' . $tabla_cods . '
						WHERE
							cod_banco = ' . $cb['cod_banco'] . '
						ORDER BY
							cod_mov
					';
					$cod_mov = $db->query($sql);
					
					foreach ($cod_mov as $cm) {
						$tpl->newBlock('cod_mov');
						$tpl->assign('cod', $cm['cod']);
						$tpl->assign('concepto', $cm['concepto']);
					}
					
					if ($cb['tipo'] == 't') {
						$tpl->newBlock('cod_banco_cargo');
					}
					else {
						$tpl->newBlock('cod_banco_abono');
					}
				}
				
				$num_cia = NULL;
				$gran_total_depositos = 0;
				$gran_total_comisiones = 0;
				$color = FALSE;
				foreach ($result as $r) {
					if ($num_cia != $r['num_cia']) {
						$num_cia = $r['num_cia'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', utf8_encode($r['nombre_cia']));
						
						$total_depositos = 0;
						$total_comisiones = 0;
					}
					$tpl->newBlock('row');
					$tpl->assign('color', $color ? 'on' : 'off');
					$tpl->assign('id', $r['id']);
					$tpl->assign('fecha', $r['fecha']);
					$tpl->assign('cod_banco', $r['cod_banco']);
					$tpl->assign('concepto', $r['concepto']);
					$tpl->assign('color', $r['color']);
					$tpl->assign('importe', number_format($r['importe'], 2, '.', ','));
					
					$total_depositos += $r['tipo'] == 'f' ? $r['importe'] : 0;
					$total_comisiones += $r['tipo'] == 't' ? $r['importe'] : 0;
					$gran_total_depositos += $r['tipo'] == 'f' ? $r['importe'] : 0;
					$gran_total_comisiones += $r['tipo'] == 't' ? $r['importe'] : 0;
					
					$tpl->assign('cia.total_depositos', number_format($total_depositos, 2, '.', ','));
					$tpl->assign('cia.total_comisiones', number_format($total_comisiones, 2, '.', ','));
					
					$color = !$color;
				}
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'conciliar':
			$tabla = $_REQUEST['banco'] == 1 ? 'mov_banorte' : 'mov_santander';
			
			$sql = '';
			
			foreach ($_REQUEST['cod_banco'] as $i => $cod_banco) {
				$sql .= '
					UPDATE
						' . $tabla . '
					SET
						cod_mov = ' . $_REQUEST['cod_mov'][$i] . ',
						concepto = ' . (trim($_REQUEST['concepto'][$i]) != '' ? '\'' . trim($_REQUEST['concepto'][$i]) . '\'' : 'concepto') . '
					WHERE
							id IN (' . implode(', ', $_REQUEST['id']) . ')
						AND
							cod_banco = ' . $cod_banco . '
				' . ";\n";
			}
			
			$sql .= '
				INSERT INTO
					estado_cuenta
						(
							num_cia,
							fecha,
							fecha_con,
							tipo_mov,
							importe,
							cod_mov,
							folio,
							concepto,
							cuenta,
							iduser,
							timestamp,
							tipo_con
						)
					SELECT
						num_cia,
						fecha,
						fecha,
						tipo_mov,
						importe,
						cod_mov,
						num_documento,
						concepto,
						' . $_REQUEST['banco'] . ',
						' . $_SESSION['iduser'] . ',
						now(),
						9
					FROM
						' . $tabla . '
					WHERE
						id
							IN
								(
									' . implode(', ', $_REQUEST['id']) . '
								)
			' . ";\n";
			
			$sql .= '
				UPDATE
					' . $tabla . '
				SET
					fecha_con = fecha,
					iduser = ' . $_SESSION['iduser'] . ',
					timestamp = now()
				WHERE
					id
						IN
							(
								' . implode(', ', $_REQUEST['id']) . '
							)
			' . ";\n";
			
			$sql .= '
				UPDATE
					saldos
				SET
						saldo_bancos = saldo_bancos + movimientos.importe,
						saldo_libros = saldo_libros + movimientos.importe
					FROM
						(
							SELECT
								num_cia,
								SUM(
									CASE
										WHEN tipo_mov = \'FALSE\' THEN
											importe
										ELSE
											-importe
									END
								)
									AS
										importe
							FROM
								' . $tabla . '
							WHERE
								id
									IN
										(
											' . implode(', ', $_REQUEST['id']) . '
										)
							GROUP BY
								num_cia
						)
							AS
								movimientos
				WHERE
						saldos.cuenta = ' . $_REQUEST['banco'] . '
					AND
						saldos.num_cia = movimientos.num_cia
			' . ";\n";
			
			if (isset($_REQUEST['bonificaciones'])) {
				$bon_com = array();
				$bon_iva = array();
				foreach ($_REQUEST['cod_banco'] as $i => $cod_banco) {
					if ($_REQUEST['tipo_bonificacion'][$i] == 35) {
						$bon_com[] = $cod_banco;
					}
					else if ($_REQUEST['tipo_bonificacion'][$i] == 34) {
						$bon_iva[] = $cod_banco;
					}
				}
				
				$case = array();
				
				if (count($bon_com) > 0) {
					$case[] = '
						WHEN cod_banco IN (' . implode(', ', $bon_com) . ') THEN
							35
					';
				}
				
				if (count($bon_iva) > 0) {
					$case[] = '
						WHEN cod_banco IN (' . implode(', ', $bon_iva) . ') THEN
							34
					';
				}
				
				$sql .= '
					INSERT INTO
						estado_cuenta
							(
								num_cia,
								fecha,
								tipo_mov,
								importe,
								cod_mov,
								concepto,
								cuenta,
								iduser,
								timestamp,
								tipo_con
							)
						SELECT
							num_cia,
							fecha,
							\'FALSE\',
							importe,
							CASE
								' . implode("\n", $case) . '
								ELSE
									29
							END,
							\'BONIF \' || concepto,
							' . $_REQUEST['banco'] . ',
							' . $_SESSION['iduser'] . ',
							now(),
							0
						FROM
							' . $tabla . '
						WHERE
								id
									IN
										(
											' . implode(', ', $_REQUEST['id']) . '
										)
							AND
								cod_banco
									IN
										(
											' . implode(', ', $bon_com) . implode(', ', $bon_iva) . '
										)
				' . ";\n";
				
				$sql .= '
					UPDATE
						saldos
					SET
							saldo_libros = saldo_libros + comisiones.importe
						FROM
							(
								SELECT
									num_cia,
									SUM(importe)
										AS
											importe
								FROM
									' . $tabla . '
								WHERE
										id
											IN
												(
													' . implode(', ', $_REQUEST['id']) . '
												)
									AND
										cod_banco
											IN
												(
													' . implode(', ', $bon_com) . implode(', ', $bon_iva) . '
												)
								GROUP BY
									num_cia
							)
								AS
									comisiones
					WHERE
							saldos.cuenta = ' . $_REQUEST['banco'] . '
						AND
							saldos.num_cia = comisiones.num_cia
				' . ";\n";
			}
			
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
			
			$db->query($sql);
			
			$arr['banco'] = $_REQUEST['banco'];
			$arr['ids'] = $_REQUEST['id'];
			
			echo json_encode($arr);
		break;
		
		case 'reporte':
			$tabla = $_REQUEST['banco'] == 1 ? 'mov_banorte' : 'mov_santander';
			$cuenta = $_REQUEST['banco'] == 1 ? 'clabe_cuenta' : 'clabe_cuenta2';
			
			$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS
							nombre_cia,
					' . $cuenta . '
						AS
							cuenta,
					fecha,
					cod_banco || \' \' || concepto
						AS
							concepto,
					CASE
						WHEN tipo_mov = \'FALSE\' THEN
							\'blue\'
						ELSE
							\'red\'
					END
						AS
							color,
					tipo_mov
						AS
							tipo,
					importe
				FROM
						' . $tabla . ' movs
					LEFT JOIN
						catalogo_companias cc
							USING
								(
									num_cia
								)
				WHERE
					id IN (' . implode(', ', $_REQUEST['id']) . ')
				ORDER BY
					num_cia,
					fecha,
					tipo,
					importe DESC
			';
			$result = $db->query($sql);
			
			if ($result) {
				$tpl = new TemplatePower('plantillas/ban/ConciliarDepositosComisionesReporte.tpl');
				$tpl->prepare();
				
				$tpl->newBlock('reporte');
				$tpl->assign('banco', $_REQUEST['banco'] == 1 ? 'Banorte' : 'Santander');
				$tpl->assign('fecha', date('d/m/Y'));
				
				$num_cia = NULL;
				$gran_total_depositos = 0;
				$gran_total_comisiones = 0;
				foreach ($result as $r) {
					if ($num_cia != $r['num_cia']) {
						$num_cia = $r['num_cia'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $r['num_cia']);
						$tpl->assign('nombre_cia', $r['nombre_cia']);
						$tpl->assign('cuenta', $r['cuenta']);
						
						$total_depositos = 0;
						$total_comisiones = 0;
					}
					$tpl->newBlock('row');
					$tpl->assign('fecha', $r['fecha']);
					$tpl->assign('concepto', $r['concepto']);
					$tpl->assign('color', $r['color']);
					$tpl->assign('importe', number_format($r['importe'], 2, '.', ','));
					
					$total_depositos += $r['tipo'] == 'f' ? $r['importe'] : 0;
					$total_comisiones += $r['tipo'] == 't' ? $r['importe'] : 0;
					$gran_total_depositos += $r['tipo'] == 'f' ? $r['importe'] : 0;
					$gran_total_comisiones += $r['tipo'] == 't' ? $r['importe'] : 0;
					
					$tpl->assign('cia.total_depositos', number_format($total_depositos, 2, '.', ','));
					$tpl->assign('cia.total_comisiones', number_format($total_comisiones, 2, '.', ','));
					$tpl->assign('reporte.total_depositos', number_format($gran_total_depositos, 2, '.', ','));
					$tpl->assign('reporte.total_comisiones', number_format($gran_total_comisiones, 2, '.', ','));
				}
				
				$tpl->printToScreen();
			}
			else {
				echo 'NO HAY RESULTADOS';
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ban/ConciliarDepositosComisiones.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
