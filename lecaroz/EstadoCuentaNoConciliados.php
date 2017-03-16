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
			$tpl = new TemplatePower('plantillas/ban/EstadoCuentaNoConciliadosInicio.tpl');
			$tpl->prepare();

			//$tpl->assign('fecha1', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 30, date('Y'))));
			$tpl->assign('fecha2', date('d/m/Y'));

			echo $tpl->getOutputContent();

			break;

		case 'consultar':
			$condiciones = array();

			$condiciones[] = 'fecha_con IS NULL';

			if (!in_array($_SESSION['iduser'], array(1, 4, 2, 79)))
			{
				$condiciones[] = 'ec.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
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
					$condiciones[] = 'ec.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['omitir_cias']) && trim($_REQUEST['omitir_cias']) != '') {
				$omitir_cias = array();

				$pieces = explode(',', $_REQUEST['omitir_cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir_cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir_cias[] = $piece;
					}
				}

				if (count($omitir_cias) > 0) {
					$condiciones[] = 'ec.num_cia NOT IN (' . implode(', ', $omitir_cias) . ')';
				}
			}

			if (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0) {
				$condiciones[] = 'cuenta = ' . $_REQUEST['banco'];
			}

			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
				|| (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
					&& (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
					$condiciones[] = 'ec.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				} else if (isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') {
					$condiciones[] = 'ec.fecha = \'' . $_REQUEST['fecha1'] . '\'';
				} else if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '') {
					$condiciones[] = 'ec.fecha <= \'' . $_REQUEST['fecha2'] . '\'';
				}
			}

			$condiciones_aux = array();

			if (isset($_REQUEST['depositos'])) {
				$condicion = '(ec.tipo_mov = FALSE';

				if (isset($_REQUEST['codigos_depositos']) && trim($_REQUEST['codigos_depositos']) != '') {
					$codigos_depositos = array();

					$pieces = explode(',', $_REQUEST['codigos_depositos']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$codigos_depositos[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$codigos_depositos[] = $piece;
						}
					}

					if (count($codigos_depositos) > 0) {
						$condicion .= ' AND ec.cod_mov IN (' . implode(', ', $codigos_depositos) . ')';
					}
				}

				$condicion .= ')';

				$condiciones_aux[] = $condicion;
			}

			if (isset($_REQUEST['cargos'])) {
				$condicion = '(ec.tipo_mov = TRUE';

				if (isset($_REQUEST['codigos_cargos']) && trim($_REQUEST['codigos_cargos']) != '') {
					$codigos_cargos = array();

					$pieces = explode(',', $_REQUEST['codigos_cargos']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$codigos_cargos[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$codigos_cargos[] = $piece;
						}
					}

					if (count($codigos_cargos) > 0) {
						$condicion .= 'ec.cod_mov IN (' . implode(', ', $codigos_cargos) . ')';
					}
				}

				$condicion .= ')';

				$condiciones_aux[] = $condicion;
			}

			$condiciones[] = '(' . implode(' OR ', $condiciones_aux) . ')';

			$sql = '
				SELECT
					ec.id,
					ec.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					ec.cuenta
						AS banco,
					cc.clabe_cuenta
						AS cuenta_banorte,
					cc.clabe_cuenta2
						AS cuenta_santander,
					ec.fecha,
					ec.fecha_con
						AS conciliado,
					CASE
						WHEN ec.tipo_mov = FALSE THEN
							ec.importe
						ELSE
							NULL
					END
						AS deposito,
					CASE
						WHEN ec.tipo_mov = TRUE THEN
							ec.importe
						ELSE
							NULL
					END
						AS cargo,
					ec.folio,
					c.num_proveedor || \' \' || c.a_nombre
						AS beneficiario,
					c.codgastos || \' \' || (
						SELECT
							descripcion
						FROM
							catalogo_gastos
						WHERE
							codgastos = c.codgastos
					)
						AS gasto,
					ec.concepto,
					ec.cod_mov || \' \' || (
						CASE
							WHEN cuenta = 1 THEN
								(
									SELECT
										descripcion
									FROM
										catalogo_mov_bancos
									WHERE
										cod_mov = ec.cod_mov
									LIMIT
										1
								)
							WHEN cuenta = 2 THEN
								(
									SELECT
										descripcion
									FROM
										catalogo_mov_santander
									WHERE
										cod_mov = ec.cod_mov
									LIMIT
										1
								)
						END
					)
						AS codigo,
					ec.cod_mov
				FROM
					estado_cuenta ec
					LEFT JOIN cheques c
						USING (num_cia, cuenta, folio, fecha)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					ec.num_cia,
					ec.fecha,
					ec.id
			';

			$query = $db->query($sql);

			if ($query) {
				$tpl = new TemplatePower('plantillas/ban/EstadoCuentaNoConciliadosConsulta.tpl');
				$tpl->prepare();

				$num_cia = NULL;

				foreach ($query as $row) {
					if ($num_cia != $row['num_cia']) {
						$num_cia = $row['num_cia'];

						$tpl->newBlock('cia');

						$tpl->assign('num_cia', $row['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));

						$cuentas = array();

						if (trim($row['cuenta_banorte']) != '' && !(isset($_REQUEST['banco']) && $_REQUEST['banco'] == 2)) {
							$cuentas[] = '<img src="/lecaroz/imagenes/Banorte16x16.png" width="16" height="16" /> ' . $row['cuenta_banorte'];
						}

						if (trim($row['cuenta_santander']) != '' && !(isset($_REQUEST['banco']) && $_REQUEST['banco'] == 1)) {
							$cuentas[] = '<img src="/lecaroz/imagenes/Santander16x16.png" width="16" height="16" /> ' . $row['cuenta_santander'];
						}

						$tpl->assign('cuentas', $cuentas ? '<br />' . implode('<br />', $cuentas) : '');

						$depositos = 0;
						$cargos = 0;
					}

					$tpl->newBlock('row');

					$tpl->assign('id', $row['id']);
					$tpl->assign('banco', $row['banco'] == 1 ? 'Banorte' : 'Santander');
					$tpl->assign('fecha', $row['fecha']);
					$tpl->assign('deposito', $row['deposito'] != 0 ? number_format($row['deposito'], 2) : '&nbsp;');
					$tpl->assign('cargo', $row['cargo'] != 0 ? number_format($row['cargo'], 2) : '&nbsp;');
					$tpl->assign('folio', $row['folio'] > 0 ? '<span class="' . ($row['cod_mov'] == 41 ? 'purple' : ($row['cod_mov'] == 41 ? 'orange' : 'green')) . '" info="' . $row['gasto'] . '">' . $row['folio'] . '</span>' : '&nbsp;');
					$tpl->assign('beneficiario', $row['beneficiario'] != '' ? utf8_encode($row['beneficiario']) : '&nbsp;');
					$tpl->assign('concepto', $row['concepto'] != '' ? utf8_encode($row['concepto']) : '&nbsp;');
					$tpl->assign('codigo', utf8_encode($row['codigo']));

					$depositos += $row['deposito'];
					$cargos += $row['cargo'];

					$tpl->assign('cia.depositos', number_format($depositos, 2));
					$tpl->assign('cia.cargos', number_format($cargos, 2));
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'listado':
			$condiciones = array();

			$condiciones[] = 'fecha_con IS NULL';

			if (!in_array($_SESSION['iduser'], array(1, 4, 2)))
			{
				$condiciones[] = 'ec.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
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
					$condiciones[] = 'ec.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['omitir_cias']) && trim($_REQUEST['omitir_cias']) != '') {
				$omitir_cias = array();

				$pieces = explode(',', $_REQUEST['omitir_cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir_cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir_cias[] = $piece;
					}
				}

				if (count($omitir_cias) > 0) {
					$condiciones[] = 'ec.num_cia NOT IN (' . implode(', ', $omitir_cias) . ')';
				}
			}

			if (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0) {
				$condiciones[] = 'cuenta = ' . $_REQUEST['banco'];
			}

			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
				|| (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
					&& (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
					$condiciones[] = 'ec.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				} else if (isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') {
					$condiciones[] = 'ec.fecha = \'' . $_REQUEST['fecha1'] . '\'';
				} else if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '') {
					$condiciones[] = 'ec.fecha <= \'' . $_REQUEST['fecha2'] . '\'';
				}
			}

			$condiciones_aux = array();

			if (isset($_REQUEST['depositos'])) {
				$condicion = '(ec.tipo_mov = FALSE';

				if (isset($_REQUEST['codigos_depositos']) && trim($_REQUEST['codigos_depositos']) != '') {
					$codigos_depositos = array();

					$pieces = explode(',', $_REQUEST['codigos_depositos']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$codigos_depositos[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$codigos_depositos[] = $piece;
						}
					}

					if (count($codigos_depositos) > 0) {
						$condicion .= ' AND ec.cod_mov IN (' . implode(', ', $codigos_depositos) . ')';
					}
				}

				$condicion .= ')';

				$condiciones_aux[] = $condicion;
			}

			if (isset($_REQUEST['cargos'])) {
				$condicion = '(ec.tipo_mov = TRUE';

				if (isset($_REQUEST['codigos_cargos']) && trim($_REQUEST['codigos_cargos']) != '') {
					$codigos_cargos = array();

					$pieces = explode(',', $_REQUEST['codigos_cargos']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$codigos_cargos[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$codigos_cargos[] = $piece;
						}
					}

					if (count($codigos_cargos) > 0) {
						$condicion .= 'ec.cod_mov IN (' . implode(', ', $codigos_cargos) . ')';
					}
				}

				$condicion .= ')';

				$condiciones_aux[] = $condicion;
			}

			$condiciones[] = '(' . implode(' OR ', $condiciones_aux) . ')';

			$sql = '
				SELECT
					ec.id,
					ec.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					ec.cuenta
						AS banco,
					cc.clabe_cuenta
						AS cuenta_banorte,
					cc.clabe_cuenta2
						AS cuenta_santander,
					ec.fecha,
					ec.fecha_con
						AS conciliado,
					CASE
						WHEN ec.tipo_mov = FALSE THEN
							ec.importe
						ELSE
							NULL
					END
						AS deposito,
					CASE
						WHEN ec.tipo_mov = TRUE THEN
							ec.importe
						ELSE
							NULL
					END
						AS cargo,
					ec.folio,
					c.num_proveedor || \' \' || c.a_nombre
						AS beneficiario,
					c.codgastos || \' \' || (
						SELECT
							descripcion
						FROM
							catalogo_gastos
						WHERE
							codgastos = c.codgastos
					)
						AS gasto,
					ec.concepto,
					ec.cod_mov || \' \' || (
						CASE
							WHEN cuenta = 1 THEN
								(
									SELECT
										descripcion
									FROM
										catalogo_mov_bancos
									WHERE
										cod_mov = ec.cod_mov
									LIMIT
										1
								)
							WHEN cuenta = 2 THEN
								(
									SELECT
										descripcion
									FROM
										catalogo_mov_santander
									WHERE
										cod_mov = ec.cod_mov
									LIMIT
										1
								)
						END
					)
						AS codigo,
					ec.cod_mov
				FROM
					estado_cuenta ec
					LEFT JOIN cheques c
						USING (num_cia, cuenta, folio, fecha)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					ec.num_cia,
					ec.fecha,
					ec.id
			';

			$query = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ban/EstadoCuentaNoConciliadosListado.tpl');
			$tpl->prepare();

			if ($query) {
				$tpl->newBlock('reporte');

				$num_cia = NULL;

				foreach ($query as $row) {
					if ($num_cia != $row['num_cia']) {
						$num_cia = $row['num_cia'];

						$tpl->newBlock('cia');

						$tpl->assign('num_cia', $row['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));

						$cuentas = array();

						if (trim($row['cuenta_banorte']) != '' && !(isset($_REQUEST['banco']) && $_REQUEST['banco'] == 2)) {
							$cuentas[] = '<img src="/lecaroz/imagenes/Banorte16x16.png" width="16" height="16" /> ' . $row['cuenta_banorte'];
						}

						if (trim($row['cuenta_santander']) != '' && !(isset($_REQUEST['banco']) && $_REQUEST['banco'] == 1)) {
							$cuentas[] = '<img src="/lecaroz/imagenes/Santander16x16.png" width="16" height="16" /> ' . $row['cuenta_santander'];
						}

						$tpl->assign('cuentas', $cuentas ? '<br />' . implode('<br />', $cuentas) : '');

						$depositos = 0;
						$cargos = 0;
					}

					$tpl->newBlock('row');

					$tpl->assign('id', $row['id']);
					$tpl->assign('banco', $row['banco'] == 1 ? 'Banorte' : 'Santander');
					$tpl->assign('fecha', $row['fecha']);
					$tpl->assign('deposito', $row['deposito'] != 0 ? number_format($row['deposito'], 2) : '&nbsp;');
					$tpl->assign('cargo', $row['cargo'] != 0 ? number_format($row['cargo'], 2) : '&nbsp;');
					$tpl->assign('folio', $row['folio'] > 0 ? '<span class="' . ($row['cod_mov'] == 41 ? 'purple' : ($row['cod_mov'] == 41 ? 'orange' : 'green')) . '" info="' . $row['gasto'] . '">' . $row['folio'] . '</span>' : '&nbsp;');
					$tpl->assign('beneficiario', $row['beneficiario'] != '' ? utf8_encode($row['beneficiario']) : '&nbsp;');
					$tpl->assign('concepto', $row['concepto'] != '' ? utf8_encode($row['concepto']) : '&nbsp;');
					$tpl->assign('codigo', utf8_encode($row['codigo']));

					$depositos += $row['deposito'];
					$cargos += $row['cargo'];

					$tpl->assign('cia.depositos', number_format($depositos, 2));
					$tpl->assign('cia.cargos', number_format($cargos, 2));
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'exportar':
			$condiciones = array();

			$condiciones[] = 'fecha_con IS NULL';

			if (!in_array($_SESSION['iduser'], array(1, 4, 2)))
			{
				$condiciones[] = 'ec.num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
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
					$condiciones[] = 'ec.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['omitir_cias']) && trim($_REQUEST['omitir_cias']) != '') {
				$omitir_cias = array();

				$pieces = explode(',', $_REQUEST['omitir_cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir_cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir_cias[] = $piece;
					}
				}

				if (count($omitir_cias) > 0) {
					$condiciones[] = 'ec.num_cia NOT IN (' . implode(', ', $omitir_cias) . ')';
				}
			}

			if (isset($_REQUEST['banco']) && $_REQUEST['banco'] > 0) {
				$condiciones[] = 'cuenta = ' . $_REQUEST['banco'];
			}

			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
				|| (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '')
					&& (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
					$condiciones[] = 'ec.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				} else if (isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') {
					$condiciones[] = 'ec.fecha = \'' . $_REQUEST['fecha1'] . '\'';
				} else if (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '') {
					$condiciones[] = 'ec.fecha <= \'' . $_REQUEST['fecha2'] . '\'';
				}
			}

			$condiciones_aux = array();

			if (isset($_REQUEST['depositos'])) {
				$condicion = '(ec.tipo_mov = FALSE';

				if (isset($_REQUEST['codigos_depositos']) && trim($_REQUEST['codigos_depositos']) != '') {
					$codigos_depositos = array();

					$pieces = explode(',', $_REQUEST['codigos_depositos']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$codigos_depositos[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$codigos_depositos[] = $piece;
						}
					}

					if (count($codigos_depositos) > 0) {
						$condicion .= ' AND ec.cod_mov IN (' . implode(', ', $codigos_depositos) . ')';
					}
				}

				$condicion .= ')';

				$condiciones_aux[] = $condicion;
			}

			if (isset($_REQUEST['cargos'])) {
				$condicion = '(ec.tipo_mov = TRUE';

				if (isset($_REQUEST['codigos_cargos']) && trim($_REQUEST['codigos_cargos']) != '') {
					$codigos_cargos = array();

					$pieces = explode(',', $_REQUEST['codigos_cargos']);
					foreach ($pieces as $piece) {
						if (count($exp = explode('-', $piece)) > 1) {
							$codigos_cargos[] =  implode(', ', range($exp[0], $exp[1]));
						}
						else {
							$codigos_cargos[] = $piece;
						}
					}

					if (count($codigos_cargos) > 0) {
						$condicion .= 'ec.cod_mov IN (' . implode(', ', $codigos_cargos) . ')';
					}
				}

				$condicion .= ')';

				$condiciones_aux[] = $condicion;
			}

			$condiciones[] = '(' . implode(' OR ', $condiciones_aux) . ')';

			$sql = '
				SELECT
					/*ec.num_cia || \' \' || cc.nombre_corto
						AS cia,*/
					ec.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					CASE
						WHEN ec.cuenta = 1 THEN
							\'BANORTE\'
						WHEN ec.cuenta = 2 THEN
							\'SANTANDER\'
					END
						AS banco,
					/*cc.clabe_cuenta
						AS cuenta_banorte,
					cc.clabe_cuenta2
						AS cuenta_santander,*/
					CASE
						WHEN ec.cuenta = 1 THEN
							cc.clabe_cuenta
						WHEN ec.cuenta = 2 THEN
							cc.clabe_cuenta2
					END
						AS cuenta,
					ec.fecha,
					CASE
						WHEN ec.tipo_mov = FALSE THEN
							ec.importe
						ELSE
							NULL
					END
						AS deposito,
					CASE
						WHEN ec.tipo_mov = TRUE THEN
							ec.importe
						ELSE
							NULL
					END
						AS cargo,
					ec.folio,
					c.num_proveedor || \' \' || c.a_nombre
						AS beneficiario,
					ec.concepto,
					ec.cod_mov || \' \' || (
						CASE
							WHEN cuenta = 1 THEN
								(
									SELECT
										descripcion
									FROM
										catalogo_mov_bancos
									WHERE
										cod_mov = ec.cod_mov
									LIMIT
										1
								)
							WHEN cuenta = 2 THEN
								(
									SELECT
										descripcion
									FROM
										catalogo_mov_santander
									WHERE
										cod_mov = ec.cod_mov
									LIMIT
										1
								)
						END
					)
						AS codigo
				FROM
					estado_cuenta ec
					LEFT JOIN cheques c
						USING (num_cia, cuenta, folio, fecha)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					ec.num_cia,
					ec.fecha,
					ec.id
			';

			$query = $db->query($sql);

			//$data = '"MOVIMIENTOS NO CONCILIADOS"' . "\n\n";
			$data = '"#CIA","COMPAÑIA","BANCO","CUENTA","FECHA","DEPOSITO","CARGO","FOLIO","BENEFICIARIO","CONCEPTO","CODIGO"' . "\n";

			if ($query) {
				// $cia = NULL;

				// foreach ($query as $row) {
				// 	if ($cia != $row['cia']) {
				// 		if ($cia != NULL) {
				// 			$data .= '"","TOTAL","' . $depositos . '","' . $cargos . '"' . "\n\n";
				// 		}

				// 		$cia = $row['cia'];

				// 		$data .= '"' . utf8_encode($row['cia']) . '"' . "\n";

				// 		$cuentas = array();

				// 		if (trim($row['cuenta_banorte']) != '' && !(isset($_REQUEST['banco']) && $_REQUEST['banco'] == 2)) {
				// 			$cuentas[] = '"CUENTA BANORTE","\'' . $row['cuenta_banorte'] . '"';
				// 		}

				// 		if (trim($row['cuenta_santander']) != '' && !(isset($_REQUEST['banco']) && $_REQUEST['banco'] == 1)) {
				// 			$cuentas[] = '"CUENTA SANTANDER","\'' . $row['cuenta_santander'] . '"';
				// 		}

				// 		$data .= implode("\n", $cuentas) . "\n";

				// 		$data .= "\n" . '"BANCO","FECHA","DEPOSITO","CARGO","FOLIO","BENEFICIARIO","CONCEPTO","CODIGO"' . "\n";

				// 		$depositos = 0;
				// 		$cargos = 0;
				// 	}

				// 	unset($row['cia']);
				// 	unset($row['cuenta_banorte']);
				// 	unset($row['cuenta_santander']);

				// 	$data .= '"' . implode('","', $row) . '"' . "\n";

				// 	$depositos += $row['deposito'];
				// 	$cargos += $row['cargo'];
				// }

				// if ($cia != NULL) {
				// 	$data .= '"","TOTAL","' . $depositos . '","' . $cargos . '"' . "\n";
				// }

				foreach ($query as $key => $value) {
					$data .= '"' . implode('","', $value) . '"' . "\n";
				}
			}

			header('Content-Type: application/download');
			header('Content-Disposition: attachment; filename=noconciliados.csv');

			echo $data;

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/EstadoCuentaNoConciliados.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
