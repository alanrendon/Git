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
			$tpl = new TemplatePower('plantillas/fac/ReportePolizasEntregadasContadoresInicio.tpl');
			$tpl->prepare();

			$sql = "
				SELECT
					idcontador
						AS value,
					nombre_contador
						AS text
				FROM
					catalogo_contadores
				ORDER BY
					text
			";

			$result = $db->query($sql);

			if ($result)
			{
				foreach ($result as $row)
				{
					$tpl->newBlock('conta');
					$tpl->assign('value', $row['value']);
					$tpl->assign('text', utf8_encode($row['text']));
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'obtener_poliza':
			$sql = "
				SELECT
					c.id,
					c.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					c.fecha,
					cbl.nombre_banco
						AS banco,
					c.folio,
					c.num_proveedor
						AS num_pro,
					c.a_nombre
						AS nombre_pro,
					c.codgastos
						AS gasto,
					cg.descripcion
						AS nombre_gasto,
					c.importe
						AS importe_cheque,
					c.fecha_cancelacion,
					COALESCE(fp.num_fact, fz.num_fact)
						AS num_fact,
					COALESCE(fp.fecha, fz.fecha)
						AS fecha_fact,
					COALESCE(fp.descripcion, fz.concepto)
						AS descripcion,
					COALESCE(fp.total, fz.total, c.importe)
						AS importe_fact,
					c.folio_conta,
					conta.nombre_contador
				FROM
					cheques c
					LEFT JOIN estado_cuenta ec
						USING (num_cia, cuenta, folio, fecha)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_contadores conta
						USING (idcontador)
					LEFT JOIN catalogo_bancos_lecaroz cbl
						ON (cbl.banco = c.cuenta)
					LEFT JOIN catalogo_gastos cg
						USING (codgastos)
					LEFT JOIN facturas_pagadas fp
						ON (fp.num_cia = c.num_cia AND fp.cuenta = c.cuenta AND fp.folio_cheque = c.folio AND fp.fecha_cheque = c.fecha)
					LEFT JOIN facturas_zap fz
						ON (fz.num_cia = c.num_cia AND fz.cuenta = c.cuenta AND fz.folio = c.folio AND fz.tspago::DATE = c.fecha)
				WHERE
					c.id = {$_REQUEST['id']}
			";

			$result = $db->query($sql);

			if ($result) {
				$row = $result[0];

				$data = array(
					'status'			=> $row['folio_conta'] > 0 ? -1 : 1,
					'id'				=> intval($row['id']),
					'folio_conta'		=> $row['folio_conta'] > 0 ? intval($row['folio_conta']) : NULL,
					'num_cia'			=> intval($row['num_cia']),
					'nombre_cia'		=> utf8_encode($row['nombre_cia']),
					'banco'				=> utf8_encode($row['banco']),
					'folio'				=> intval($row['folio']),
					'fecha'				=> $row['fecha'],
					'num_pro'			=> intval($row['num_pro']),
					'nombre_pro'		=> utf8_encode($row['nombre_pro']),
					'gasto'				=> intval($row['gasto']),
					'nombre_gasto'		=> utf8_encode($row['nombre_gasto']),
					'importe_cheque'	=> floatval($row['importe_cheque']),
					'cancelado'			=> $row['fecha_cancelacion'] != '' ? TRUE : FALSE,
					'contador'			=> utf8_encode($row['nombre_contador']),
					'comprobantes'		=> array()
				);

				foreach ($result as $row)
				{
					$data['comprobantes'][] = array(
						'num_fact'		=> utf8_encode($row['num_fact']),
						'fecha'			=> $row['fecha'],
						'descripcion'	=> utf8_encode($row['descripcion']),
						'importe'		=> floatval($row['importe_fact'])
					);
				}
			}
			else
			{
				$data = array(
					'status'	=> -2
				);
			}

			header('Content-Type: application/json');
			echo json_encode($data);

			break;

		case 'consultar':
			$condiciones = array();

			$condiciones[] = 'c.folio_conta > 0';

			if ($_SESSION['iduser'] != 1)
			{
				$condiciones[] = $_SESSION['tipo_usuario'] == 2 ? 'c.num_cia BETWEEN 900 AND 998' : 'c.num_cia BETWEEN 1 AND 899';
			}

			if (isset($_REQUEST['folios']) && trim($_REQUEST['folios']) != '') {
				$folios = array();

				$pieces = explode(',', $_REQUEST['folios']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$folios[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$folios[] = $piece;
					}
				}

				if (count($folios) > 0) {
					$condiciones[] = 'c.folio_conta IN (' . implode(', ', $folios) . ')';
				}
			}

			if (isset($_REQUEST['conta']) && $_REQUEST['conta'] > 0) {
				$condiciones[] = 'cc.idcontador = ' . $_REQUEST['conta'];
			}

			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') || (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') && (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')
					&& $_REQUEST['fecha1'] != $_REQUEST['fecha2']) {
					$condiciones[] = 'c.tsconta::DATE BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				} else if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') && (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')
					&& $_REQUEST['fecha1'] == $_REQUEST['fecha2']) {
					$condiciones[] = 'c.tsconta::DATE = \'' . $_REQUEST['fecha1'] . '\'';
				}  else if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') || (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != '')) {
					$condiciones[] = 'c.tsconta::DATE = \'' . ($_REQUEST['fecha1'] ? $_REQUEST['fecha1'] : $_REQUEST['fecha2']) . '\'';
				}
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
					$condiciones[] = 'c.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['pros']) && trim($_REQUEST['pros']) != '') {
				$pros = array();

				$pieces = explode(',', $_REQUEST['pros']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$pros[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$pros[] = $piece;
					}
				}

				if (count($pros) > 0) {
					$condiciones[] = 'c.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			if (isset($_REQUEST['facs']) && trim($_REQUEST['facs']) != '') {
				$facs = array();

				$pieces = explode(',', $_REQUEST['facs']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$facs[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$facs[] = $piece;
					}
				}

				if (count($facs) > 0) {
					if ($_SESSION['iduser'] != 1)
					{
						if ($_SESSION['tipo_usuario'] == 2)
						{
							$condiciones[] = 'fz.num_fact IN (\'' . implode('\', \'', $facs) . '\')';
						}
						else
						{
							$condiciones[] = 'fp.num_fact IN (\'' . implode('\', \'', $facs) . '\')';
						}
					}
					else
					{
						$condiciones[] = '(fp.num_fact IN (\'' . implode('\', \'', $facs) . '\') OR fz.num_fact IN (\'' . implode('\', \'', $facs) . '\'))';
					}
				}
			}

			$sql = "
				SELECT
					folio_conta,
					COUNT(folio)
						AS polizas,
					fecha_reporte
				FROM
					(
						SELECT
							c.folio_conta,
							c.num_cia,
							c.cuenta,
							c.folio,
							c.tsconta::DATE
								AS fecha_reporte
						FROM
							cheques c
							LEFT JOIN estado_cuenta ec
								USING (num_cia, cuenta, folio, fecha)
							LEFT JOIN catalogo_companias cc
								USING (num_cia)
							LEFT JOIN catalogo_bancos_lecaroz cbl
								ON (cbl.banco = c.cuenta)
							LEFT JOIN catalogo_gastos cg
								USING (codgastos)
							LEFT JOIN facturas_pagadas fp
								ON (fp.num_cia = c.num_cia AND fp.cuenta = c.cuenta AND fp.folio_cheque = c.folio AND fp.fecha_cheque = c.fecha)
							LEFT JOIN facturas_zap fz
								ON (fz.num_cia = c.num_cia AND fz.cuenta = c.cuenta AND fz.folio = c.folio AND fz.tspago::DATE = c.fecha)
						WHERE
							" . implode(' AND ', $condiciones) . "
						GROUP BY
							c.folio_conta,
							c.num_cia,
							c.cuenta,
							c.folio,
							fecha_reporte
					)
						AS result
				GROUP BY
					folio_conta,
					fecha_reporte
				ORDER BY
					folio_conta
			";

			$result = $db->query($sql);

			if ($result)
			{
				$cia = $result[0];

				$tpl = new TemplatePower('plantillas/fac/ReportePolizasEntregadasContadoresResultado.tpl');
				$tpl->prepare();

				foreach ($result as $row)
				{
					$tpl->newBlock('row');
					$tpl->assign('folio', $row['folio_conta']);
					$tpl->assign('fecha', $row['fecha_reporte']);
					$tpl->assign('polizas', number_format($row['polizas']));
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'nuevo':
			$tpl = new TemplatePower('plantillas/fac/ReportePolizasEntregadasContadoresNuevo.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();

			break;

		case 'do_nuevo':
			$sql = "
				UPDATE
					cheques
				SET
					folio_conta = (
						SELECT
							COALESCE(MAX(folio_conta), 0) + 1
						FROM
							cheques
					),
					tsconta = NOW(),
					iduser_conta = {$_SESSION['iduser']}
				WHERE
					id IN (" . implode(', ', $_REQUEST['id']) . ")
			";

			$db->query($sql);

			$sql = "
				SELECT
					MAX(folio_conta)
						AS folio
				FROM
					cheques
				WHERE
					folio_conta > 0
			";

			$result = $db->query($sql);

			if ($result)
			{
				echo $result[0]['folio'];
			}

			break;

		case 'mostrar_reporte':
			$condiciones = array();

			$condiciones[] = "c.folio_conta = {$_REQUEST['folio']}";

			if ($_SESSION['iduser'] != 1)
			{
				$condiciones[] = $_SESSION['tipo_usuario'] == 2 ? 'c.num_cia BETWEEN 900 AND 998' : 'c.num_cia BETWEEN 1 AND 899';
			}

			$sql = "
				SELECT
					c.id,
					c.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					c.fecha,
					cbl.nombre_banco
						AS banco,
					c.folio,
					c.num_proveedor
						AS num_pro,
					c.a_nombre
						AS nombre_pro,
					c.codgastos
						AS gasto,
					cg.descripcion
						AS nombre_gasto,
					c.importe
						AS importe_cheque,
					c.fecha_cancelacion,
					COALESCE(fp.num_fact, fz.num_fact)
						AS num_fact,
					COALESCE(fp.fecha, fz.fecha)
						AS fecha_fact,
					COALESCE(fp.descripcion, fz.concepto)
						AS descripcion,
					COALESCE(fp.total, fz.total, c.importe)
						AS importe_fact,
					c.folio_conta
				FROM
					cheques c
					LEFT JOIN estado_cuenta ec
						USING (num_cia, cuenta, folio, fecha)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_bancos_lecaroz cbl
						ON (cbl.banco = c.cuenta)
					LEFT JOIN catalogo_gastos cg
						USING (codgastos)
					LEFT JOIN facturas_pagadas fp
						ON (fp.num_cia = c.num_cia AND fp.cuenta = c.cuenta AND fp.folio_cheque = c.folio AND fp.fecha_cheque = c.fecha)
					LEFT JOIN facturas_zap fz
						ON (fz.num_cia = c.num_cia AND fz.cuenta = c.cuenta AND fz.folio = c.folio AND fz.tspago::DATE = c.fecha)
				WHERE
					" . implode(' AND ', $condiciones) . "
				ORDER BY
					c.num_cia,
					c.cuenta,
					c.folio
			";

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/fac/ReportePolizasEntregadasContadoresListado.tpl');
			$tpl->prepare();

			if ($result)
			{
				$num_cia = NULL;

				$tpl->newBlock('reporte');

				$tpl->assign('folio', $_REQUEST['folio']);

				$num_polizas = 0;
				$num_facturas = 0;

				foreach ($result as $row) {
					if ($num_cia != $row['num_cia'] || $banco != $row['banco'] || $folio != $row['folio'] || $num_pro != $row['num_pro'])
					{
						if ($num_cia != NULL && $facturas > 1)
						{
							$tpl->newBlock('total');

							$tpl->assign('total', number_format($total, 2));
						}

						$num_cia = $row['num_cia'];
						$banco = $row['banco'];
						$folio = $row['folio'];
						$num_pro = $row['num_pro'];

						$tpl->newBlock('poliza');

						$total = 0;
						$facturas = 0;

						$num_polizas++;
					}

					$tpl->newBlock('factura');
					$tpl->assign('num_cia', $row['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
					$tpl->assign('banco', utf8_encode($row['banco']));
					$tpl->assign('folio', ($row['fecha_cancelacion'] != '' ? ' [CANCELADO]' : '') . $row['folio']);
					$tpl->assign('fecha', $row['fecha']);
					$tpl->assign('num_pro', $row['num_pro']);
					$tpl->assign('nombre_pro', utf8_encode($row['nombre_pro']));
					$tpl->assign('num_fact', utf8_encode($row['num_fact']));
					$tpl->assign('fecha_fact', $row['fecha_fact']);
					$tpl->assign('gasto', $row['gasto']);
					$tpl->assign('nombre_gasto', utf8_encode($row['nombre_gasto']));
					$tpl->assign('importe', number_format($row['importe_fact'], 2));

					$total += $row['importe_fact'];
					$facturas++;

					$num_facturas++;
				}

				if ($num_pro != NULL && $facturas > 1)
				{
					$tpl->newBlock('total');

					$tpl->assign('total', number_format($total, 2));
				}

				$tpl->assign('reporte.polizas', number_format($num_polizas));
				$tpl->assign('reporte.facturas', number_format($num_facturas));
			}

			$tpl->printToScreen();

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/ReportePolizasEntregadasContadores.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
