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
			$tpl = new TemplatePower('plantillas/fac/GastosCatalogoInicio.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'consultar':
			$condiciones = array();
			
			if (isset($_REQUEST['gastos']) && trim($_REQUEST['gastos']) != '') {
				$gastos = array();
				
				$pieces = explode(',', $_REQUEST['gastos']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$gastos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$gastos[] = $piece;
					}
				}
				
				if (count($gastos) > 0) {
					$condiciones[] = 'codgastos IN (' . implode(', ', $gastos) . ')';
				}
			}
			
			if (isset($_REQUEST['descripcion']) && $_REQUEST['descripcion'] != '') {
				$condiciones[] = 'descripcion LIKE \'%' . $_REQUEST['descripcion'] . '%\'';
			}
			
			$sql = '
				SELECT
					codgastos
						AS gasto,
					descripcion,
					CASE
						WHEN codigo_edo_resultados = 0 THEN
							\'<span class="green">NO INCLUIDO</span>\'
						WHEN codigo_edo_resultados = 1 THEN
							\'<span class="blue">OPERACION</span>\'
						WHEN codigo_edo_resultados = 2 THEN
							\'<span class="orange">GENERAL</span>\'
					END
						AS balance,
					CASE
						WHEN tipo_gasto = FALSE THEN
							\'<span class="green">VARIABLE</span>\'
						WHEN tipo_gasto = TRUE THEN
							\'<span class="blue">FIJO</span>\'
					END
						AS tipo,
					CASE
						WHEN aplicacion_gasto = FALSE THEN
							\'<span class="green">PANADERIA</span>\'
						WHEN aplicacion_gasto = TRUE THEN
							\'<span class="blue">REPARTO</span>\'
					END
						AS aplicacion,
					CASE
						WHEN pan_comprado = TRUE THEN
							\'<img src="/lecaroz/iconos/accept.png" width="16" height="16">\'
						ELSE
							\'&nbsp;\'
					END
						AS pan_comprado,
					CASE
						WHEN pan_comprado = TRUE THEN
							COALESCE(pan_comprado_descuento, 0) || \'%\'
						ELSE
							\'&nbsp;\'
					END
						AS descuento
				FROM
					catalogo_gastos cg
				' . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . '
				ORDER BY
					gasto
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/fac/GastosCatalogoConsulta.tpl');
			$tpl->prepare();
			
			if ($result) {
				foreach ($result as $row) {
					$tpl->newBlock('row');
					
					$tpl->assign('gasto', $row['gasto']);
					$tpl->assign('descripcion', utf8_encode($row['descripcion']));
					$tpl->assign('balance', utf8_encode($row['balance']));
					$tpl->assign('tipo', utf8_encode($row['tipo']));
					$tpl->assign('aplicacion', utf8_encode($row['aplicacion']));
					$tpl->assign('pan_comprado', $row['pan_comprado']);
					$tpl->assign('descuento', $row['descuento']);
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'alta':
			$tpl = new TemplatePower('plantillas/fac/GastosCatalogoAlta.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_alta':
			$sql = '
				SELECT
					codgastos
				FROM
					catalogo_gastos
				ORDER BY
					codgastos
			';
			
			$query = $db->query($sql);
			
			$gasto = 1;
			
			if ($query) {
				foreach ($query as $row) {
					if ($gasto == $row['codgastos']) {
						$gasto++;
					} else {
						break;
					}
				}
			}
			
			$sql = '
				INSERT INTO
					catalogo_gastos (
						codgastos,
						descripcion,
						codigo_edo_resultados,
						tipo_gasto,
						aplicacion_gasto,
						orden,
						pan_comprado,
						pan_comprado_descuento
					)
					VALUES (
						' . $gasto . ',
						\'' . utf8_decode($_REQUEST['descripcion']) . '\',
						' . $_REQUEST['balance'] . ',
						' . $_REQUEST['tipo'] . ',
						' . $_REQUEST['aplicacion'] . ',
						' . (isset($_REQUEST['orden']) && get_val($_REQUEST['orden']) >= 0 ? get_val($_REQUEST['orden']) : 2) . ',
						' . (isset($_REQUEST['pan_comprado']) ? 'TRUE' : 'FALSE') . ',
						' . (isset($_REQUEST['descuento']) ? get_val($_REQUEST['descuento']) : 0) . '
					)
			';
			
			$db->query($sql);
			
			echo json_encode(array(
				'gasto'       => $gasto,
				'descripcion' => $_REQUEST['descripcion']
			));
			
			break;
		
		case 'modificar':
			$sql = '
				SELECT
					codgastos
						AS gasto,
					descripcion,
					codigo_edo_resultados
						AS balance,
					tipo_gasto
						AS tipo,
					aplicacion_gasto
						AS aplicacion,
					orden,
					pan_comprado,
					pan_comprado_descuento
						AS descuento
				FROM
					catalogo_gastos cg
				WHERE
					codgastos = ' . $_REQUEST['gasto'] . '
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/fac/GastosCatalogoModificar.tpl');
			$tpl->prepare();
			
			$tpl->assign('gasto', $_REQUEST['gasto']);
			$tpl->assign('descripcion', utf8_encode($result[0]['descripcion']));
			$tpl->assign('balance_' . $result[0]['balance'], ' selected="selected"');
			$tpl->assign('tipo_' . $result[0]['tipo'], ' checked="checked"');
			$tpl->assign('aplicacion_' . $result[0]['aplicacion'], ' checked="checked"');
			$tpl->assign('orden', $result[0]['orden']);
			$tpl->assign('pan_comprado', $result[0]['pan_comprado'] == 't' ? ' checked="checked"' : '');
			$tpl->assign('descuento', number_format($result[0]['descuento'], 2));
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_modificar':
			$sql = '
				UPDATE
					catalogo_gastos
				SET
					descripcion = \'' . utf8_decode($_REQUEST['descripcion']) . '\',
					codigo_edo_resultados = ' . $_REQUEST['balance'] . ',
					tipo_gasto = ' . $_REQUEST['tipo'] . ',
					aplicacion_gasto = ' . $_REQUEST['aplicacion'] . ',
					orden = ' . (isset($_REQUEST['orden']) && get_val($_REQUEST['orden']) >= 0 ? get_val($_REQUEST['orden']) : 2) . ',
					pan_comprado = ' . (isset($_REQUEST['pan_comprado']) ? 'TRUE' : 'FALSE') . ',
					pan_comprado_descuento = ' . (isset($_REQUEST['descuento']) ? get_val($_REQUEST['descuento']) : 0) . '
				WHERE
					codgastos = ' . $_REQUEST['gasto'] . '
			';
			
			$db->query($sql);
			
			break;
		
		case 'do_baja':
			if (!$db->query('
				SELECT
					codgastos
				FROM
					movimiento_gastos
				WHERE
					codgastos = ' . $_REQUEST['gasto'] . '
				LIMIT
					1
			')) {
				$sql = '
					DELETE FROM
						catalogo_gastos
					WHERE
						codgastos = ' . $_REQUEST['gasto'] . '
				';
				
				$db->query($sql);
				
				echo 1;
			} else {
				echo -1;
			}
			
			break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/GastosCatalogo.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
