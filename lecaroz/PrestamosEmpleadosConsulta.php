<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

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

		case 'inicio':
			$tpl = new TemplatePower('plantillas/rh/PrestamosEmpleadosConsultaInicio.tpl');
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

			if ($admins) {
				foreach ($admins as $a) {
					$tpl->newBlock('admin');
					$tpl->assign('value', $a['value']);
					$tpl->assign('text', utf8_encode($a['text']));
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'consultar':
			$condiciones = array();

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
					$condiciones[] = 'ct.num_cia_emp IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			if (isset($_REQUEST['empleados']) && trim($_REQUEST['empleados']) != '') {
				$empleados = array();

				$pieces = explode(',', $_REQUEST['empleados']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$empleados[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$empleados[] = $piece;
					}
				}

				if (count($empleados) > 0) {
					$condiciones[] = 'ct.num_emp IN (' . implode(', ', $empleados) . ')';
				}
			}

			if (isset($_REQUEST['nombre']) && $_REQUEST['nombre'] != '') {
				$condiciones[] = 'ct.nombre LIKE \'%' . $_REQUEST['nombre'] . '%\'';
			}

			if (isset($_REQUEST['ap_paterno']) && $_REQUEST['ap_paterno'] != '') {
				$condiciones[] = 'ct.ap_paterno LIKE \'%' . $_REQUEST['ap_paterno'] . '%\'';
			}

			if (isset($_REQUEST['ap_materno']) && $_REQUEST['ap_materno'] != '') {
				$condiciones[] = 'ct.ap_materno LIKE \'%' . $_REQUEST['ap_materno'] . '%\'';
			}

			if (isset($_REQUEST['rfc']) && $_REQUEST['rfc'] != '') {
				$condiciones[] = 'ct.rfc LIKE \'%' . $_REQUEST['rfc'] . '%\'';
			}

			$sql = "
				SELECT
					*
				FROM
					(
						SELECT
							ct.id,
							ct.num_emp,
							ct.num_cia,
							CASE
								WHEN ct.num_cia <> ct.num_cia_emp THEN
									ct.num_cia_emp || ' ' || (
										SELECT
											nombre_corto
										FROM
											catalogo_companias
										WHERE
											num_cia = ct.num_cia_emp
									)
								ELSE
									''
							END
								cia_emp,
							cc.nombre_corto
								AS nombre_cia,
							CONCAT_WS(' ', ct.ap_paterno, ct.ap_materno, ct.nombre)
								AS nombre_empleado,
							ct.rfc,
							(
								SELECT
									fecha
								FROM
									prestamos
								WHERE
									id_empleado = ct.id
									AND pagado = FALSE
									AND tipo_mov = FALSE
							)
								AS fecha_prestamo,
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
							COALESCE((
								SELECT
									SUM(importe)
								FROM
									prestamos
								WHERE
									id_empleado = ct.id
									AND pagado = FALSE
									AND tipo_mov = TRUE
							), 0)
								AS abonos,
							(
								SELECT
									MAX(fecha)
								FROM
									prestamos
								WHERE
									id_empleado = ct.id
									AND pagado = FALSE
									AND tipo_mov = TRUE
							)
								AS ultimo_abono,
							(
								SELECT
									importe
								FROM
									prestamos
								WHERE
									id_empleado = ct.id
									AND pagado = FALSE
									AND tipo_mov = TRUE
								ORDER BY
									fecha DESC
								LIMIT
									1
							)
								AS abono,
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
								AS dias_atraso,
							fecha_baja,
							firma_contrato
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
						" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
					)
						AS result
				WHERE
					saldo != 0
				ORDER BY
					num_cia,
					nombre_empleado
			";

			$query = $db->query($sql);

			if ($query) {
				$tpl = new TemplatePower('plantillas/rh/PrestamosEmpleadosConsultaResultado.tpl');
				$tpl->prepare();

				$num_cia = NULL;

				foreach ($query as $row) {
					if ($num_cia != $row['num_cia']) {
						$num_cia = $row['num_cia'];

						$tpl->newBlock('cia');

						$tpl->assign('num_cia', $row['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));

						$saldo = 0;
						$abonos = 0;
						$dias_atraso = 0;
					}

					$tpl->newBlock('row');

					$tpl->assign('id', $row['id']);
					$tpl->assign('num_emp', '<span class="' . ($row['fecha_baja'] != '' ? 'red' : '') . ($row['firma_contrato'] == 'f' ? ' underline' : '') . '">' . $row['num_emp'] . '</span>');
					$tpl->assign('empleado', '<span class="' . ($row['fecha_baja'] != '' ? 'red' : '') . ($row['firma_contrato'] == 'f' ? ' underline' : '') . '">' . utf8_encode($row['nombre_empleado']) . '</span>' . ($row['fecha_baja'] != '' ? '<img src="/lecaroz/iconos/info.png" name="info" width="16" height="16" class="icono" id="info" style="float:right;" data-tooltip="Baja desde el ' . $row['fecha_baja'] . '" />' : ''));
					$tpl->assign('fecha_prestamo', $row['fecha_prestamo'] != '' ? $row['fecha_prestamo'] : '&nbsp;');
					$tpl->assign('saldo', $row['saldo'] != 0 ? number_format($row['saldo'], 2) : '&nbsp;');
					$tpl->assign('abonos', $row['abonos'] != 0 ? number_format($row['abonos'], 2) : '&nbsp;');
					$tpl->assign('ultimo_abono', $row['ultimo_abono'] != '' ? $row['ultimo_abono'] : '&nbsp;');
					$tpl->assign('abono', $row['abono'] != 0 ? number_format($row['abono'], 2) : '&nbsp;');
					$tpl->assign('dias_atraso', $row['dias_atraso'] != 0 ? number_format($row['dias_atraso']) : '&nbsp;');

					$saldo += $row['saldo'];
					$abonos += $row['abonos'];
					$dias_atraso += $row['dias_atraso'];

					$tpl->assign('cia.saldo', number_format($saldo, 2));
					$tpl->assign('cia.abonos', number_format($abonos, 2));
					$tpl->assign('cia.dias_atraso', number_format($dias_atraso));
				}

				echo $tpl->getOutputContent();
			}

			break;

			case 'detalle':
				$sql = "
					SELECT
						cc.num_cia,
						cc.nombre_corto
							AS nombre_cia,
						num_emp,
						CONCAT_WS(' ', ct.ap_paterno, ct.ap_materno, ct.nombre)
							AS empleado,
						fecha,
						tipo_mov,
						importe
					FROM
						prestamos p
						LEFT JOIN catalogo_trabajadores ct
							ON (p.id_empleado = ct.id)
						LEFT JOIN catalogo_companias cc
							ON (cc.num_cia = ct.num_cia)
					WHERE
						p.id_empleado = {$_REQUEST['id']}
					ORDER BY
						p.id
				";

				$result = $db->query($sql);

				if ($result) {
					$tpl = new TemplatePower('plantillas/rh/PrestamosEmpleadosConsultaDetalle.tpl');
					$tpl->prepare();

					$tpl->assign('num_cia', $result[0]['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($result[0]['nombre_cia']));
					$tpl->assign('num_emp', $result[0]['num_emp']);
					$tpl->assign('empleado', utf8_encode($result[0]['empleado']));

					$saldo = 0;

					foreach ($result as $row) {
						$tpl->newBlock('row');
						$tpl->assign('fecha', '<span class="' . ($row['tipo_mov'] == 'f' ? 'red' : 'blue') . '">' . $row['fecha'] . '</span>');
						$tpl->assign('tipo', $row['tipo_mov'] == 'f' ? '<span class="red">PRESTAMO</span>' : '<span class="blue">ABONO</span>');
						$tpl->assign('importe', '<span class="' . ($row['tipo_mov'] == 'f' ? 'red' : 'blue') . '">' . number_format($row['importe'], 2) . '</span>');

						$saldo += $row['tipo_mov'] == 'f' ? $row['importe'] : -$row['importe'];

						$tpl->assign('_ROOT.saldo', number_format($saldo, 2));
					}

					echo $tpl->getOutputContent();
				}

				break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/rh/PrestamosEmpleadosConsulta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
