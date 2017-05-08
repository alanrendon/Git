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

		case 'cia':
			$sql = "
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					num_cia = {$_REQUEST['num_cia']}
					AND num_cia <= 300
			";

			$result = $db->query($sql);

			if ($result)
			{
				echo utf8_encode($result[0]['nombre_corto']);
			}

			break;

		case 'pro':
			$sql = "
				SELECT
					nombre
				FROM
					catalogo_productos
				WHERE
					cod_producto = {$_REQUEST['num_cia']}
					AND control_produccion = FALSE
			";

			$result = $db->query($sql);

			if ($result)
			{
				echo utf8_encode($result[0]['nombre']);
			}

			break;
		
		case 'inicio':
			$tpl = new TemplatePower('plantillas/pan/ProductosVentaInicio.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'consultar':
			$condiciones = array();

			$condiciones[] = "pv.tsbaja IS NULL";

			$condiciones[] = "pv.precio_venta > 0";

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
					$condiciones[] = 'pv.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			$sql = "
				SELECT
					pv.id_producto_venta,
					pv.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					pv.idcontrol_produccion,
					pv.cod_producto,
					cp.nombre,
					pv.precio_venta,
					tp.descripcion
						AS tipo_pan,
					pv.orden
				FROM
					productos_venta pv
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_operadoras co
						USING (idoperadora)
					LEFT JOIN catalogo_productos cp
						USING (cod_producto)
					LEFT JOIN tipos_pan tp
						USING (tipo_pan)
				" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
				ORDER BY
					pv.num_cia,
					pv.orden,
					cp.nombre
			";
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/pan/ProductosVentaConsulta.tpl');
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
					
					$tpl->assign('id', $row['id_producto_venta']);
					$tpl->assign('orden', $row['orden'] > 0 ? $row['orden'] : '&nbsp;');
					$tpl->assign('cod', $row['cod_producto']);
					$tpl->assign('nombre', utf8_encode($row['nombre']));
					$tpl->assign('precio', $row['precio_venta'] != 0 ? number_format($row['precio_venta'], 2) : '&nbsp;');
					$tpl->assign('tipo', utf8_encode($row['tipo_pan']));
					$tpl->assign('disabled_baja', $row['idcontrol_produccion'] != '' ? '_gray' : '');
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'alta':
			$tpl = new TemplatePower('plantillas/pan/ProductosVentaAlta.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_alta':
			$mysql_db = new DBclass($mysql_dsn, 'autocommit=yes');

			$sql = '
				INSERT INTO
					productos_venta (
						num_cia,
						cod_producto,
						precio_venta,
						orden,
						decimales,
						venta_maxima,
						idalta
					)
					VALUES (
						' . $_REQUEST['num_cia'] . ',
						' . $_REQUEST['cod_producto'] . ',
						' . get_val($_REQUEST['precio_venta']) . ',
						' . ($_REQUEST['orden'] > 0 ? $_REQUEST['orden'] : 'NULL') . ',
						' . $_REQUEST['decimales'] . ',
						' . get_val($_REQUEST['venta_maxima']) . ',
						' . $_SESSION['iduser'] . '
					)
			';
			
			$db->query($sql);

			$sql = '
				SELECT
					nombre,
					tipo_pan
				FROM
					catalogo_productos
				WHERE
					cod_producto = ' . $_REQUEST['cod_producto'] . '
			';

			$producto = $db->query($sql);

			$sql = '
				INSERT INTO
					`tbl_productos` (
						`Clave`,
						`Descripcion`,
						`Precio`,
						`num_cia`,
						`tipo_pan`,
						`consecutivo`,
						`decimal`,
						`VentaMaxima`
					)
					VALUES (
						\'' . $_REQUEST['cod_producto'] . '\',
						\'' . utf8_decode($_REQUEST['nombre_pro']) . '\',
						\'' . get_val($_REQUEST['precio_venta']) . '\',
						\'' . $_REQUEST['num_cia'] . '\',
						\'' . $producto[0]['tipo_pan'] . '\',
						' . ($_REQUEST['orden'] > 0 ? $_REQUEST['orden'] : '0') . ',
						\'' . $_REQUEST['decimales'] . '\',
						\'' . get_val($_REQUEST['venta_maxima']) . '\'
					)
			';

			$mysql_db->query($sql);
			
			break;
		
		case 'modificar':
			$sql = '
				SELECT
					pv.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					pv.cod_producto,
					cp.nombre
						AS nombre_pro,
					pv.precio_venta,
					pv.decimales,
					pv.venta_maxima,
					pv.orden,
					pv.idcontrol_produccion
				FROM
					productos_venta pv
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_productos cp
						USING (cod_producto)
				WHERE
					id_producto_venta = ' . $_REQUEST['id'] . '
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/pan/ProductosVentaModificar.tpl');
			$tpl->prepare();
			
			$tpl->assign('id_producto_venta', $_REQUEST['id']);
			$tpl->assign('num_cia', $result[0]['num_cia']);
			$tpl->assign('nombre_cia', utf8_encode($result[0]['nombre_cia']));
			$tpl->assign('cod_producto', $result[0]['cod_producto']);
			$tpl->assign('nombre_pro', utf8_encode($result[0]['nombre_pro']));
			$tpl->assign('precio_venta', number_format($result[0]['precio_venta'], 2));
			$tpl->assign('decimales_' . $result[0]['decimales'], ' checked="checked"');
			$tpl->assign('venta_maxima', number_format($result[0]['venta_maxima'], 2));
			$tpl->assign('orden', $result[0]['orden']);

			$tpl->assign('readonly', $result[0]['idcontrol_produccion'] > 0 ? ' readonly="readonly"' : '');

			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_modificar':
			$mysql_db = new DBclass($mysql_dsn, 'autocommit=yes');

			$sql = "
				SELECT
					num_cia,
					cod_producto,
					tipo_pan,
					precio_venta,
					decimales,
					venta_maxima
				FROM
					productos_venta
					LEFT JOIN catalogo_productos
						USING (cod_producto)
				WHERE
					id_producto_venta = {$_REQUEST['id_producto_venta']}
			";

			$producto = $db->query($sql);

			$sql = '
				UPDATE
					productos_venta
				SET
					--num_cia = ' . $_REQUEST['num_cia'] . ',
					--cod_producto = ' . $_REQUEST['cod_producto'] . ',
					precio_venta = ' . get_val($_REQUEST['precio_venta']) . ',
					orden = ' . ($_REQUEST['orden'] > 0 ? $_REQUEST['orden'] : 'NULL') . ',
					decimales = ' . $_REQUEST['decimales'] . ',
					venta_maxima = ' . get_val($_REQUEST['venta_maxima']) . ',
					tsmod = NOW(),
					idmod = ' . $_SESSION['iduser'] . '
				WHERE
					id_producto_venta = ' . $_REQUEST['id_producto_venta'] . '
			';
			
			$db->query($sql);

			$sql = "
				UPDATE
					`tbl_productos`
				SET
					`tipo_pan` = '{$producto[0]['tipo_pan']}',
					`Precio` = '" . get_val($_REQUEST['precio_venta']) . "',
					`decimal` = '{$_REQUEST['decimales']}',
					`VentaMaxima` = '" . get_val($_REQUEST['venta_maxima']) . "',
					`consecutivo` = '" . ($_REQUEST['orden'] > 0 ? $_REQUEST['orden'] : '0') . "'
				WHERE
					`num_cia` = '{$producto[0]['num_cia']}'
					AND `Clave` = '{$producto[0]['cod_producto']}'
					AND `tipo_pan` = '{$producto[0]['tipo_pan']}'
					AND `Precio` = '{$producto[0]['precio_venta']}'
					AND `decimal` = '{$producto[0]['decimales']}'
					AND `VentaMaxima` = {$producto[0]['venta_maxima']}
					AND `fechaBaja` IS NULL
			";

			$mysql_db->query($sql);
			
			break;
		
		case 'do_baja':
			$mysql_db = new DBclass($mysql_dsn, 'autocommit=yes');

			$sql = "
				UPDATE
					productos_venta
				SET
					tsbaja = NOW(),
					idbaja = {$_SESSION['iduser']}
				WHERE
					id_producto_venta = {$_REQUEST['id']}
			";

			$db->query($sql);

			$sql = "
				SELECT
					num_cia,
					cod_producto,
					tipo_pan,
					precio_venta,
					decimales,
					venta_maxima
				FROM
					productos_venta
					LEFT JOIN catalogo_productos
						USING (cod_producto)
				WHERE
					id_producto_venta = {$_REQUEST['id']}
			";

			$producto = $db->query($sql);

			$sql = "
				UPDATE
					`tbl_productos`
				SET
					`fechaBaja` = '" . date('Y-m-d') . "'
				WHERE
					`num_cia` = '{$producto[0]['num_cia']}'
					AND `Clave` = '{$producto[0]['cod_producto']}'
					AND `tipo_pan` = '{$producto[0]['tipo_pan']}'
					AND `Precio` = '{$producto[0]['precio_venta']}'
					AND `decimal` = '{$producto[0]['decimales']}'
					AND `VentaMaxima` = {$producto[0]['venta_maxima']}
					AND `fechaBaja` IS NULL
			";

			$mysql_db->query($sql);
			
			break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/pan/ProductosVenta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
