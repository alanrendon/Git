<?php
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';
include 'includes/dbstatus.php';

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
	1 => 'Enero',
	2 => 'Febrero',
	3 => 'Marzo',
	4 => 'Abril',
	5 => 'Mayo',
	6 => 'Junio',
	7 => 'Julio',
	8 => 'Agosto',
	9 => 'Septiembre',
	10 => 'Octubre',
	11 => 'Noviembre',
	12 => 'Diciembre'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'inicio':
			$tpl = new TemplatePower('plantillas/ped/PedidosConsultaInicio.tpl');
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

			foreach ($admins as $admin) {
				$tpl->newBlock('admin');
				$tpl->assign('value', $admin['value']);
				$tpl->assign('text', utf8_encode($admin['text']));
			}

			echo $tpl->getOutputContent();
		break;

		case 'consultar':
			$condiciones = array();

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
					$condiciones[] = 'p.folio IN (' . implode(', ', $folios) . ')';
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
					$condiciones[] = 'p.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
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
					$condiciones[] = 'p.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			if (isset($_REQUEST['mps']) && trim($_REQUEST['mps']) != '') {
				$mps = array();

				$pieces = explode(',', $_REQUEST['mps']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$mps[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$mps[] = $piece;
					}
				}

				if (count($mps) > 0) {
					$condiciones[] = 'p.codmp IN (' . implode(', ', $mps) . ')';
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
					$condiciones[] = 'p.num_cia NOT IN (' . implode(', ', $omitir_cias) . ')';
				}
			}

			if (isset($_REQUEST['omitir_pros']) && trim($_REQUEST['omitir_pros']) != '') {
				$omitir_pros = array();

				$pieces = explode(',', $_REQUEST['omitir_pros']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir_pros[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir_pros[] = $piece;
					}
				}

				if (count($omitir_pros) > 0) {
					$condiciones[] = 'p.num_proveedor NOT IN (' . implode(', ', $omitir_pros) . ')';
				}
			}

			if (isset($_REQUEST['omitir_mps']) && trim($_REQUEST['omitir_mps']) != '') {
				$omitir_mps = array();

				$pieces = explode(',', $_REQUEST['omitir_mps']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$omitir_mps[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$omitir_mps[] = $piece;
					}
				}

				if (count($omitir_mps) > 0) {
					$condiciones[] = 'p.codmp NOT IN (' . implode(', ', $omitir_mps) . ')';
				}
			}

			if (isset($_REQUEST['fecha1']) || isset($_REQUEST['fecha2'])) {
				if (isset($_REQUEST['fecha1']) && isset($_REQUEST['fecha2'])) {
					$condiciones[] = 'p.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				}
				else if (isset($_REQUEST['fecha1'])) {
					$condiciones[] = 'p.fecha = \'' . $_REQUEST['fecha1'] . '\'';
				}
				else if (isset($_REQUEST['fecha2'])) {
					$condiciones[] = 'p.fecha >= \'' . $_REQUEST['fecha2'] . '\'';
				}
			}

			if (count($condiciones) == 0) {
				$condiciones[] = 'p.folio = (SELECT MAX(folio) FROM pedidos_new)';
			}

			$condiciones[] = 'p.tsbaja IS NULL';

			$sql = '
				SELECT
					p.id,
					p.folio,
					p.fecha,
					num_cia,
					cc.nombre_corto
						AS nombre_cia,
					codmp,
					cmp.nombre
						AS nombre_mp,
					pedido,
					p.unidad,
					entregar,
					p.presentacion,
					p.contenido,
					p.num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					cp.telefono1,
					cp.telefono2,
					cp.email1,
					cp.email2,
					cp.email3,
					fecha_solicitud,
					programa,
					auth.nombre
						AS usuario
				FROM
					pedidos_new p
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_mat_primas cmp
						USING (codmp)
					LEFT JOIN auth
						ON (iduser = idins)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia,
					p.folio,
					p.codmp,
					pedido
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ped/PedidosConsultaResultado.tpl');
			$tpl->prepare();

			if ($result) {
				$programa = array(
					NULL => NULL,
					-1   => 'INSERCION DIRECTA A BASE DE DATOS',
					1    => 'AUTOMATICO',
					2    => 'MANUAL',
					3    => 'AL CORTE',
					4    => 'DE PANADERIA',
					5    => 'MENSUAL'
				);

				$num_cia = NULL;

				$total_general_pedido = 0;
				$total_general_entrega = 0;

				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						if ($num_cia != NULL && isset($mps) && count($mps) == 1)
						{
							$tpl->newBlock('totales');

							$tpl->assign('pedido', number_format($total_pedido, 2));
							$tpl->assign('unidad', $rec['unidad'] . ($total_pedido > 1 ? (in_array($rec['unidad'][strlen($rec['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : ''));
							$tpl->assign('entregar', number_format($total_entrega, 2));
							$tpl->assign('presentacion', $rec['presentacion'] . ($total_entrega > 1 ? (in_array($rec['presentacion'][strlen($rec['presentacion']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') . ($rec['unidad'] != $rec['presentacion'] || $rec['contenido'] > 1 ? ' DE ' . $rec['contenido'] . ' ' . $rec['unidad'] . ($rec['contenido'] > 1 ? (in_array($rec['unidad'][strlen($rec['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') : ''));
						}

						$num_cia = $rec['num_cia'];

						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));

						$row_color = FALSE;

						$total_pedido = 0;
						$total_entrega = 0;
					}

					$tpl->newBlock('pedido');

					$tpl->assign('row_color', $row_color ? 'on' : 'off');

					$row_color = !$row_color;

					$tpl->assign('id', $rec['id']);
					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('codmp', $rec['codmp']);
					$tpl->assign('nombre_mp', utf8_encode($rec['nombre_mp']));
					$tpl->assign('folio', $rec['folio']);
					$tpl->assign('fecha', $rec['fecha']);
					$tpl->assign('fecha_soicitud', $rec['fecha_solicitud'] != '' ? $rec['fecha_solicitud'] : '&nbsp;');
					$tpl->assign('pedido', number_format($rec['pedido'], 2, '.', ','));
					$tpl->assign('unidad', $rec['unidad'] . ($rec['pedido'] > 1 ? (in_array($rec['unidad'][strlen($rec['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : ''));
					$tpl->assign('entregar', number_format($rec['entregar'], 2, '.', ','));
					$tpl->assign('presentacion', $rec['presentacion'] . ($rec['entregar'] > 1 ? (in_array($rec['presentacion'][strlen($rec['presentacion']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') . ($rec['unidad'] != $rec['presentacion'] || $rec['contenido'] > 1 ? ' DE ' . $rec['contenido'] . ' ' . $rec['unidad'] . ($rec['contenido'] > 1 ? (in_array($rec['unidad'][strlen($rec['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') : ''));
					$tpl->assign('num_pro', $rec['num_pro']);
					$tpl->assign('nombre_pro', utf8_encode($rec['nombre_pro']));

					$tpl->assign('info', 'Hecho en: ' . $programa[$rec['programa']] . '<br />Pedido por: ' . $rec['usuario']);

					if (isset($mps) && count($mps) == 1)
					{
						$total_pedido += $rec['pedido'];
						$total_entrega += $rec['entregar'];

						$total_general_pedido += $rec['pedido'];
						$total_general_entrega += $rec['entregar'];
					}
				}

				if ($num_cia != NULL && isset($mps) && count($mps) == 1)
				{
					$tpl->newBlock('totales');

					$tpl->assign('pedido', number_format($total_pedido, 2));
					$tpl->assign('unidad', $rec['unidad'] . ($total_pedido > 1 ? (in_array($rec['unidad'][strlen($rec['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : ''));
					$tpl->assign('entregar', number_format($total_entrega, 2));
					$tpl->assign('presentacion', $rec['presentacion'] . ($total_entrega > 1 ? (in_array($rec['presentacion'][strlen($rec['presentacion']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') . ($rec['unidad'] != $rec['presentacion'] || $rec['contenido'] > 1 ? ' DE ' . $rec['contenido'] . ' ' . $rec['unidad'] . ($rec['contenido'] > 1 ? (in_array($rec['unidad'][strlen($rec['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') : ''));

					$tpl->newBlock('totales_generales');

					$tpl->assign('pedido', number_format($total_general_pedido, 2));
					$tpl->assign('unidad', $rec['unidad'] . ($total_general_pedido > 1 ? (in_array($rec['unidad'][strlen($rec['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : ''));
					$tpl->assign('entregar', number_format($total_general_entrega, 2));
					$tpl->assign('presentacion', $rec['presentacion'] . ($total_general_entrega > 1 ? (in_array($rec['presentacion'][strlen($rec['presentacion']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') . ($rec['unidad'] != $rec['presentacion'] || $rec['contenido'] > 1 ? ' DE ' . $rec['contenido'] . ' ' . $rec['unidad'] . ($rec['contenido'] > 1 ? (in_array($rec['unidad'][strlen($rec['unidad']) - 1], array('A', 'E', 'I', 'O', 'U')) ? 'S' : 'ES') : '') : ''));
				}
			}

			echo $tpl->getOutputContent();
		break;

		case 'borrar':
			$sql = '
				UPDATE
					pedidos_new
				SET
					tsbaja = NOW(),
					idbaja = ' . $_SESSION['iduser'] . '
				WHERE
					id IN (' . implode(', ', $_REQUEST['id']) . ')
			';

			$db->query($sql);
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ped/PedidosConsulta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
