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
			
			$tpl = new TemplatePower('plantillas/bal/ReservasCatalogoInicio.tpl');
			$tpl->prepare();
			
			$sql = '
				SELECT
					id
						AS reserva,
					cr.descripcion
						AS descripcion_reserva,
					codgastos
						AS gasto,
					cg.descripcion
						AS descripcion_gasto,
					aplicar_promedio,
					distribuir_diferencia
				FROM
					catalogo_reservas cr
					LEFT JOIN catalogo_gastos cg
						USING (codgastos)
				ORDER BY
					reserva
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				foreach ($result as $row) {
					$tpl->newBlock('row');
					
					$tpl->assign('reserva', $row['reserva']);
					$tpl->assign('descripcion_reserva', utf8_encode($row['descripcion_reserva']));
					$tpl->assign('gasto', $row['gasto'] > 0 ? $row['gasto'] : '&nbsp;');
					$tpl->assign('descripcion_gasto', $row['gasto'] > 0 ? utf8_encode($row['descripcion_gasto']) : '');
					$tpl->assign('aplicar_promedio', $row['aplicar_promedio'] == 't' ? '<img src="/lecaroz/iconos/accept.png" width="16" height="16" />' : '&nbsp;');
					$tpl->assign('distribuir_diferencia', $row['distribuir_diferencia'] == 't' ? '<img src="/lecaroz/iconos/accept.png" width="16" height="16" />' : '&nbsp;');
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'obtener_gasto':
			$sql = '
				SELECT
					descripcion
				FROM
					catalogo_gastos
				WHERE
					codgastos = ' . $_REQUEST['gasto'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				echo utf8_encode($result[0]['descripcion']);
			}
			
			break;
		
		case 'alta':
			$tpl = new TemplatePower('plantillas/bal/ReservasCatalogoAlta.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_alta':
			$sql = '
				INSERT INTO
					catalogo_reservas (
						descripcion,
						codgastos,
						aplicar_promedio,
						distribuir_diferencia
					)
					VALUES (
						\'' . utf8_decode($_REQUEST['descripcion_reserva']) . '\',
						' . ($_REQUEST['gasto'] > 0 ? $_REQUEST['gasto'] : 'NULL') . ',
						' . (isset($_REQUEST['aplicar_gasto']) ? 'TRUE' : 'FALSE') . ',
						' . (isset($_REQUEST['distribuir_diferencia']) ? 'TRUE' : 'FALSE') . '
					)
			' . ";\n";
			
			$sql .= '
				UPDATE
					catalogo_reservas
				SET
					tipo_res = (
						SELECT
							last_value
						FROM
							catalogo_reservas_id_seq
					)
				WHERE
					id = (
						SELECT
							last_value
						FROM
							catalogo_reservas_id_seq
					)
			' . ";\n";
			
			$db->query($sql);
			
			break;
		
		case 'modificar':
			$sql = '
				SELECT
					cr.descripcion
						AS descripcion_reserva,
					codgastos
						AS gasto,
					cg.descripcion
						AS descripcion_gasto,
					aplicar_promedio,
					distribuir_diferencia
				FROM
					catalogo_reservas cr
					LEFT JOIN catalogo_gastos cg
						USING (codgastos)
				WHERE
					cr.id = ' . $_REQUEST['reserva'] . '
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/bal/ReservasCatalogoModificar.tpl');
			$tpl->prepare();
			
			$tpl->assign('reserva', $_REQUEST['reserva']);
			$tpl->assign('descripcion_reserva', utf8_encode($result[0]['descripcion_reserva']));
			$tpl->assign('gasto', $result[0]['gasto']);
			$tpl->assign('descripcion_gasto', utf8_encode($result[0]['descripcion_gasto']));
			$tpl->assign('aplicar_promedio', $result[0]['aplicar_promedio'] == 't' ? ' checked="checked"' : '');
			$tpl->assign('distribuir_diferencia', $result[0]['distribuir_diferencia'] == 't' ? ' checked="checked"' : '');
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_modificar':
			$sql = '
				UPDATE
					catalogo_reservas
				SET
					descripcion = \'' . utf8_decode($_REQUEST['descripcion_reserva']) . '\',
					codgastos = ' . ($_REQUEST['gasto'] > 0 ? $_REQUEST['gasto'] : 'NULL') . ',
					aplicar_promedio = ' . (isset($_REQUEST['aplicar_promedio']) ? 'TRUE' : 'FALSE') . ',
					distribuir_diferencia = ' . (isset($_REQUEST['distribuir_diferencia']) ? 'TRUE' : 'FALSE') . '
				WHERE
					id = ' . $_REQUEST['reserva'] . '
			';
			
			$db->query($sql);
			
			break;
		
		case 'do_baja':
			$sql = '
				DELETE FROM
					catalogo_reservas
				WHERE
					id = ' . $_REQUEST['reserva'] . '
			';
			
			$db->query($sql);
			
			break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/bal/ReservasCatalogo.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
