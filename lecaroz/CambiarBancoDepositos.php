<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/class.auxinv.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'buscar':
			$sql = '
				SELECT
					\'a\'
						AS
							tipo,
					id,
					num_cia,
					nombre_corto
						AS
							nombre_cia,
					fecha_mov
						AS
							fecha,
					cod_mov,
					concepto,
					importe,
					cuenta
				FROM
						depositos
					LEFT JOIN
						catalogo_companias
							USING
								(
									num_cia
								)
				WHERE
						fecha_cap = \'' . $_REQUEST['fecha_cap'] . '\'
					AND
						cuenta = ' . $_REQUEST['banco'] . '
					AND
						manual = \'FALSE\'
				
				UNION
				
				SELECT
					\'c\'
						AS
							tipo,
					id,
					num_cia,
					nombre_corto
						AS
							nombre_cia,
					fecha_mov
						AS
							fecha,
					cod_mov,
					concepto,
					importe,
					cuenta
				FROM
						retiros
					LEFT JOIN
						catalogo_companias
							USING
								(
									num_cia
								)
				WHERE
						fecha_cap = \'' . $_REQUEST['fecha_cap'] . '\'
					AND
						cuenta = ' . $_REQUEST['banco'] . '
					AND
						manual = \'FALSE\'
				
				ORDER BY
					num_cia,
					fecha,
					tipo,
					importe DESC
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$tpl = new TemplatePower('plantillas/ban/CambiarBancoDepositosResult.tpl');
				$tpl->prepare();
				
				$color = FALSE;
				$total = 0;
				
				$tpl->assign('banco', $result[0]['cuenta']);
				$tpl->assign('nombre_banco', $result[0]['cuenta'] == 1 ? 'BANORTE' : 'SANTANDER');
				foreach ($result as $r) {
					$tpl->newBlock('row');
					$tpl->assign('color', $color ? 'on' : 'off');
					$tpl->assign('id', $r['id']);
					$tpl->assign('tipo', $r['tipo']);
					$tpl->assign('num_cia', $r['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($r['nombre_cia']));
					$tpl->assign('fecha', $r['fecha']);
					$tpl->assign('cod_mov', $r['cod_mov']);
					$tpl->assign('concepto', $r['concepto']);
					$tpl->assign('importe', number_format($r['importe'], 2, '.', ','));
					$tpl->assign('color_importe', $r['tipo'] == 'a' ? '00C' : 'C00');
					
					$total += $r['tipo'] == 'a' ? $r['importe'] : -$r['importe'];
					
					$color = !$color;
				}
				$tpl->assign('_ROOT.total', number_format($total, 2, '.', ','));
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'cambiar':
			$banco = $_REQUEST['banco'] == 1 ? 2 : 1;
			
			if (isset($_REQUEST['id_a'])) {
				$condiciones[] = '
					SELECT
						num_cia,
						cuenta,
						fecha_mov,
						cod_mov,
						importe
					FROM
						depositos
					WHERE
						id
							IN
								(
									' . implode(', ', $_REQUEST['id_a']) . '
								)
				';
			}
			
			if (isset($_REQUEST['id_c'])) {
				$condiciones[] = '
					SELECT
						num_cia,
						cuenta,
						fecha_mov,
						cod_mov,
						importe
					FROM
						retiros
					WHERE
						id
							IN
								(
									' . implode(', ', $_REQUEST['id_c']) . '
								)
				';
			}
			
			$sql = '
				UPDATE
					estado_cuenta
				SET
					cuenta = ' . $banco . '
				WHERE
					(
						num_cia,
						cuenta,
						fecha,
						cod_mov,
						importe
					)
						IN
							(
								' . implode(' UNION ', $condiciones) . '
							)
			' . ";\n";echo $sql;
			
			if (isset($_REQUEST['id_a'])) {
				$sql .= '
					UPDATE
						depositos
					SET
						cuenta = ' . $banco . '
					WHERE
						id
							IN
								(
									' . implode(', ', $_REQUEST['id_a']) . '
								)
				' . ";\n";
			}
			
			if (isset($_REQUEST['id_c'])) {
				$sql .= '
					UPDATE
						retiros
					SET
						cuenta = ' . $banco . '
					WHERE
						id
							IN
								(
									' . implode(', ', $_REQUEST['id_c']) . '
								)
				' . ";\n";
			}
			
			$sql .= '
				UPDATE
					saldos
				SET
					saldo_libros = saldo_bancos + movs.importe
						FROM
							(
								SELECT
									num_cia,
									cuenta,
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
									estado_cuenta
								WHERE
									fecha_con IS NULL
								GROUP BY
									num_cia,
									cuenta
							)
								movs
				WHERE
						saldos.num_cia = movs.num_cia
					AND
						saldos.cuenta = movs.cuenta
			';
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ban/CambiarBancoDepositos.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
