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

$mysql_dsn = "mysql://root:pobgnj@192.168.1.2:3306/actpan";

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		
		case 'inicio':
			$tpl = new TemplatePower('plantillas/pan/ProductosCatalogoInicio.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'consultar':
			$condiciones = array();
			
			if (isset($_REQUEST['productos']) && trim($_REQUEST['productos']) != '') {
				$productos = array();
				
				$pieces = explode(',', $_REQUEST['productos']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$productos[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$productos[] = $piece;
					}
				}
				
				if (count($productos) > 0) {
					$condiciones[] = 'cod_producto IN (' . implode(', ', $productos) . ')';
				}
			}
			
			if (isset($_REQUEST['nombre']) && $_REQUEST['nombre'] != '') {
				$condiciones[] = 'nombre LIKE \'%' . $_REQUEST['nombre'] . '%\'';
			}
			
			$sql = '
				SELECT
					cod_producto
						AS producto,
					nombre
						AS descripcion,
					tp.descripcion
						AS tipo_pan,
					control_produccion
				FROM
					catalogo_productos cp
					LEFT JOIN tipos_pan tp
						USING (tipo_pan)
				' . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . '
				ORDER BY
					descripcion
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/pan/ProductosCatalogoConsulta.tpl');
			$tpl->prepare();
			
			if ($result) {
				foreach ($result as $row) {
					$tpl->newBlock('row');
					
					$tpl->assign('producto', $row['producto']);
					$tpl->assign('descripcion', utf8_encode($row['descripcion']));
					$tpl->assign('tipo_pan', utf8_encode($row['tipo_pan']));
					$tpl->assign('control_produccion', $row['control_produccion'] == 't' ? '<img src="/lecaroz/iconos/accept.png" width="16" height="16">' : '&nbsp;');
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'alta':
			$tpl = new TemplatePower('plantillas/pan/ProductosCatalogoAlta.tpl');
			$tpl->prepare();

			$sql = "
				SELECT
					tipo_pan
						AS value,
					descripcion
						AS text
				FROM
					tipos_pan
				ORDER BY
					tipo_pan
			";

			$result = $db->query($sql);

			if ($result)
			{
				foreach ($result as $rec)
				{
					$tpl->newBlock('tipo_pan');
					$tpl->assign('value', $rec['value']);
					$tpl->assign('text', utf8_encode($rec['text']));
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_alta':
			$mysql_db = new DBclass($mysql_dsn, 'autocommit=yes');

			$sql = '
				SELECT
					cod_producto
				FROM
					catalogo_productos
				ORDER BY
					cod_producto
			';
			
			$query = $db->query($sql);
			
			$producto = 1;
			
			if ($query) {
				foreach ($query as $row) {
					if ($producto == $row['cod_producto']) {
						$producto++;
					} else {
						break;
					}
				}
			}
			
			$sql = '
				INSERT INTO
					catalogo_productos (
						cod_producto,
						nombre,
						imagen,
						tipo_pan,
						control_produccion
					)
					VALUES (
						' . $producto . ',
						\'' . utf8_decode($_REQUEST['descripcion']) . '\',
						NULL,
						' . $_REQUEST['tipo_pan'] . ',
						' . $_REQUEST['control_produccion'] . '
					)
			';
			
			$db->query($sql);

			$sql = '
				INSERT INTO
					`catproductos` (
						`IdProducto`,
						`CodProducto`,
						`NombreProducto`
					)
					VALUES (
						\'' . $producto . '\',
						\'' . $producto . '\',
						\'' . utf8_decode($_REQUEST['descripcion']) . '\'
					)
			';

			$mysql_db->query($sql);
			
			echo json_encode(array(
				'producto'       => $producto,
				'descripcion' => $_REQUEST['descripcion']
			));
			
			break;
		
		case 'modificar':
			$sql = '
				SELECT
					cod_producto
						AS producto,
					nombre
						AS descripcion,
					tipo_pan,
					control_produccion
				FROM
					catalogo_productos cp
				WHERE
					cod_producto = ' . $_REQUEST['producto'] . '
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/pan/ProductosCatalogoModificar.tpl');
			$tpl->prepare();
			
			$tpl->assign('producto', $_REQUEST['producto']);
			$tpl->assign('descripcion', utf8_encode($result[0]['descripcion']));
			$tpl->assign('control_produccion_' . $result[0]['control_produccion'], ' checked="checked"');

			$sql = "
				SELECT
					tipo_pan
						AS value,
					descripcion
						AS text
				FROM
					tipos_pan
				ORDER BY
					tipo_pan
			";

			$tipos_pan = $db->query($sql);

			if ($tipos_pan)
			{
				foreach ($tipos_pan as $tipo)
				{
					$tpl->newBlock('tipo_pan');
					$tpl->assign('value', $tipo['value']);
					$tpl->assign('text', utf8_encode($tipo['text']));

					if ($tipo['value'] == $result[0]['tipo_pan'])
					{
						$tpl->assign('selected', ' selected="selected"');
					}
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_modificar':
			$mysql_db = new DBclass($mysql_dsn, 'autocommit=yes');

			$sql = '
				UPDATE
					catalogo_productos
				SET
					nombre = \'' . utf8_decode($_REQUEST['descripcion']) . '\',
					imagen = NULL,
					tipo_pan = ' . $_REQUEST['tipo_pan'] . ',
					control_produccion = ' . $_REQUEST['control_produccion'] . '
				WHERE
					cod_producto = ' . $_REQUEST['producto'] . '
			';
			
			$db->query($sql);

			$sql = '
				UPDATE
					`catproductos`
				SET
					`NombreProducto` = \'' . utf8_decode($_REQUEST['descripcion']) . '\'
				WHERE
					`CodProducto` = ' . $_REQUEST['producto'] . '
			';

			$mysql_db->query($sql);

			$sql = '
				UPDATE
					`tbl_productos`
				SET
					`tipo_pan` = \'' . $_REQUEST['tipo_pan'] . '\'
				WHERE
					`Clave` = ' . $_REQUEST['producto'] . '
			';

			$mysql_db->query($sql);
			
			break;
		
		case 'do_baja':
			$mysql_db = new DBclass($mysql_dsn, 'autocommit=yes');

			if (!$db->query('
				SELECT
					cod_producto
				FROM
					produccion
				WHERE
					cod_producto = ' . $_REQUEST['producto'] . '
				LIMIT
					1
			')) {
				$sql = '
					DELETE FROM
						catalogo_productos
					WHERE
						cod_producto = ' . $_REQUEST['producto'] . '
				';
				
				$db->query($sql);

				$sql = '
					DELETE FROM
						`catproductos`
					WHERE
						`CodProducto` = \'' . $_REQUEST['producto'] . '\'
				';

				$mysql_db->query($sql);
				
				echo 1;
			} else {
				echo -1;
			}
			
			break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/pan/ProductosCatalogo.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
