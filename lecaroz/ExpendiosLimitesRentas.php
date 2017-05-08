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

		case 'cia':
			$sql = "
				SELECT
					nombre_corto
				FROM
					catalogo_companias
					LEFT JOIN catalogo_operadoras
						USING (idoperadora)
				WHERE
					num_cia = {$_REQUEST['num_cia']}
					AND num_cia <= 300
					" . ( ! in_array($_SESSION['iduser'], array(1, 4)) ? "iduser = {$_SESSION['iduser']}" : '') . "
			";

			$result = $db->query($sql);

			if ($result)
			{
				echo utf8_encode($result[0]['nombre_corto']);
			}

			break;

		case 'exp':
			$sql = "
				SELECT
					nombre
				FROM
					catalogo_expendios
				WHERE
					num_cia = {$_REQUEST['num_cia']}
					AND num_expendio = {$_REQUEST['num_exp']}
			";

			$result = $db->query($sql);

			if ($result)
			{
				echo utf8_encode($result[0]['nombre']);
			}

			break;
		
		case 'inicio':
			$tpl = new TemplatePower('plantillas/pan/ExpendiosLimitesRentasInicio.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'consultar':
			$condiciones = array();

			$condiciones[] = 'cre.status = 1';

			if ( ! in_array($_SESSION['iduser'], array(1, 4)))
			{
				$condiciones[] = "co.iduser = {$_SESSION['iduser']}";
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
					$condiciones[] = 'cre.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if (isset($_REQUEST['nombre']) && $_REQUEST['nombre'] != '') {
				$condiciones[] = 'nombre LIKE \'%' . $_REQUEST['nombre'] . '%\'';
			}
			
			$sql = "
				SELECT
					cre.id,
					cre.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					cre.num_exp,
					COALESCE(ce.nombre, cre.nombre)
						AS nombre_exp,
					cre.contrato,
					CASE
						WHEN cre.tipo_pago = 1 THEN
							'<span class=\"green\">EFECTIVO</span>'
						WHEN cre.tipo_pago = 2 THEN
							'<span class=\"orange\">CHEQUE</span>'
					END
						AS pago,
					cre.fecha_inicio,
					cre.fecha_termino,
					cre.importe_bruto
						AS importe,
					cre.iva,
					cre.ret_iva,
					cre.ret_isr,
					cre.importe
						AS total
				FROM
					catalogo_renta_exp cre
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_operadoras co
						USING (idoperadora)
					LEFT JOIN catalogo_expendios ce
						ON (ce.num_cia = cre.num_cia AND ce.num_expendio = cre.num_exp)
				" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
				ORDER BY
					cre.num_cia,
					cre.num_exp,
					nombre_exp
			";
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/pan/ExpendiosLimitesRentasConsulta.tpl');
			$tpl->prepare();
			
			if ($result) {
				$num_cia = NULL;

				foreach ($result as $row) {
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));

						$total_cia = 0;
					}

					$tpl->newBlock('row');
					
					$tpl->assign('id', $row['id']);
					$tpl->assign('num_exp', $row['num_exp']);
					$tpl->assign('nombre_exp', utf8_encode($row['nombre_exp']));
					$tpl->assign('contrato', $row['contrato'] == 't' ? '<img src="/lecaroz/iconos/accept.png" width="16" height="16">' : '&nbsp;');
					$tpl->assign('pago', utf8_encode($row['pago']));
					$tpl->assign('periodo', $row['fecha_inicio'] != '' && $row['fecha_termino'] != '' ? $row['fecha_inicio'] . ' al ' . $row['fecha_termino'] : '&nbsp;');
					$tpl->assign('importe', $row['importe'] > 0 ? number_format($row['importe'], 2) : '&nbsp;');
					$tpl->assign('iva', $row['iva'] > 0 ? number_format($row['iva'], 2) : '&nbsp;');
					$tpl->assign('ret_iva', $row['ret_iva'] > 0 ? number_format($row['ret_iva'], 2) : '&nbsp;');
					$tpl->assign('ret_isr', $row['ret_isr'] > 0 ? number_format($row['ret_isr'], 2) : '&nbsp;');
					$tpl->assign('total', $row['total'] > 0 ? number_format($row['total'], 2) : '&nbsp;');

					$total_cia += $row['total'];

					$tpl->assign('cia.total', number_format($total_cia, 2));
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'alta':
			$tpl = new TemplatePower('plantillas/pan/ExpendiosLimitesRentasAlta.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_alta':
			$sql = '
				INSERT INTO
					catalogo_renta_exp (
						nombre,
						importe,
						num_cia,
						status,
						num_exp,
						contrato,
						tipo_pago,
						fecha_inicio,
						fecha_termino,
						importe_bruto,
						iva,
						ret_iva,
						ret_isr
					)
					VALUES (
						\'' . utf8_decode($_REQUEST['nombre']) . '\',
						' . get_val($_REQUEST['importe']) . ',
						' . $_REQUEST['num_cia'] . ',
						1,
						' . (isset($_REQUEST['num_exp']) && $_REQUEST['num_exp'] > 0 ? $_REQUEST['num_exp'] : 'NULL') . ',
						' . $_REQUEST['contrato'] . ',
						' . $_REQUEST['tipo_pago'] . ',
						' . (isset($_REQUEST['fecha_inicio']) && $_REQUEST['fecha_inicio'] != '' ? "'{$_REQUEST['fecha_inicio']}'" : 'NULL') . ',
						' . (isset($_REQUEST['fecha_termino']) && $_REQUEST['fecha_termino'] != '' ? "'{$_REQUEST['fecha_termino']}'" : 'NULL') . ',
						' . get_val($_REQUEST['importe_bruto']) . ',
						' . get_val($_REQUEST['iva']) . ',
						' . get_val($_REQUEST['ret_iva']) . ',
						' . get_val($_REQUEST['ret_isr']) . '
					)
			';
			
			$db->query($sql);
			
			break;
		
		case 'modificar':
			$sql = '
				SELECT
					cre.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					cre.nombre,
					cre.num_exp,
					ce.nombre
						AS nombre_exp,
					cre.contrato,
					cre.tipo_pago,
					cre.fecha_inicio,
					cre.fecha_termino,
					cre.importe_bruto,
					cre.iva,
					cre.ret_iva,
					cre.ret_isr,
					cre.importe
				FROM
					catalogo_renta_exp cre
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_expendios ce
						ON (ce.num_cia = cre.num_cia AND ce.num_expendio = cre.num_exp)
				WHERE
					cre.id = ' . $_REQUEST['id'] . '
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/pan/ExpendiosLimitesRentasModificar.tpl');
			$tpl->prepare();
			
			$tpl->assign('id', $_REQUEST['id']);
			$tpl->assign('num_cia', $result[0]['num_cia']);
			$tpl->assign('nombre_cia', utf8_encode($result[0]['nombre_cia']));
			$tpl->assign('nombre', utf8_encode($result[0]['nombre']));
			$tpl->assign('num_exp', $result[0]['num_exp']);
			$tpl->assign('nombre_exp', utf8_encode($result[0]['nombre_exp']));
			$tpl->assign('contrato_' . $result[0]['contrato'], ' checked="checked"');
			$tpl->assign('tipo_pago_' . $result[0]['tipo_pago'], ' checked="checked"');
			$tpl->assign('fecha_inicio', $result[0]['fecha_inicio']);
			$tpl->assign('fecha_termino', $result[0]['fecha_termino']);
			$tpl->assign('importe_bruto', $result[0]['importe_bruto'] > 0 ? number_format($result[0]['importe_bruto'], 2) : '');
			$tpl->assign('iva', $result[0]['iva'] > 0 ? number_format($result[0]['iva'], 2) : '');
			$tpl->assign('ret_iva', $result[0]['ret_iva'] > 0 ? number_format($result[0]['ret_iva'], 2) : '');
			$tpl->assign('ret_isr', $result[0]['ret_isr'] > 0 ? number_format($result[0]['ret_isr'], 2) : '');
			$tpl->assign('importe', $result[0]['importe'] > 0 ? number_format($result[0]['importe'], 2) : '');
			$tpl->assign('aplicar_iva', $result[0]['iva'] > 0 ? ' checked="checked"' : '');
			$tpl->assign('aplicar_ret', $result[0]['ret_iva'] > 0 ? ' checked="checked"' : '');
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_modificar':
			$sql = '
				UPDATE
					catalogo_renta_exp
				SET
					nombre = \'' . utf8_decode($_REQUEST['nombre']) . '\',
					importe = ' . get_val($_REQUEST['importe']) . ',
					num_cia = ' . $_REQUEST['num_cia'] . ',
					num_exp = ' . (isset($_REQUEST['num_exp']) && $_REQUEST['num_exp'] > 0 ? $_REQUEST['num_exp'] : 'NULL') . ',
					contrato = ' . $_REQUEST['contrato'] . ',
					tipo_pago = ' . $_REQUEST['tipo_pago'] . ',
					fecha_inicio =' . (isset($_REQUEST['fecha_inicio']) && $_REQUEST['fecha_inicio'] != '' ? "'{$_REQUEST['fecha_inicio']}'" : 'NULL') . ',
					fecha_termino = ' . (isset($_REQUEST['fecha_termino']) && $_REQUEST['fecha_termino'] != '' ? "'{$_REQUEST['fecha_termino']}'" : 'NULL') . ',
					importe_bruto = ' . get_val($_REQUEST['importe_bruto']) . ',
					iva = ' . get_val($_REQUEST['iva']) . ',
					ret_iva = ' . get_val($_REQUEST['ret_iva']) . ',
					ret_isr = ' . get_val($_REQUEST['ret_isr']) . '
				WHERE
					id = ' . $_REQUEST['id'] . '
			';
			
			$db->query($sql);
			
			break;
		
		case 'do_baja':
			$sql = "
				UPDATE
					catalogo_renta_exp
				SET
					status = 0
				WHERE
					id = {$_REQUEST['id']}
			";

			$db->query($sql);
			
			break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/pan/ExpendiosLimitesRentas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
