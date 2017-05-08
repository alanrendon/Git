<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$mysql_dsn = "mysql://root:pobgnj@192.168.1.2:3306/actpan";

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'getCia':
			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';
			$result = $db->query($sql);
			
			if ($result)
				echo $result[0]['nombre_corto'];
		break;
		
		case 'getPro':
			$sql = '
				SELECT
					nombre
				FROM
					catalogo_productos
				WHERE
					cod_producto = ' . $_REQUEST['cod'] . '
			';
			$result = $db->query($sql);
			
			if ($result)
				echo $result[0]['nombre'];
		break;
		
		case 'inicio':
			$tpl = new TemplatePower('plantillas/pan/ControlProduccionInicio.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
		break;
		
		case 'buscar':
			$sql = '
				SELECT
					idcontrol_produccion
						AS
							id,
					cod_turno,
					t.descripcion
						AS
							turno,
					cod_producto,
					p.nombre
						AS
							producto,
					precio_raya,
					porc_raya,
					precio_venta,
					tantos,
					num_orden
						AS
							orden
				FROM
						control_produccion
							cp
					LEFT JOIN
						catalogo_turnos
							t
								USING
									(
										cod_turno
									)
					LEFT JOIN
						catalogo_productos
							p
								USING
									(
										cod_producto
									)
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
					AND p.control_produccion = TRUE
				ORDER BY
					cod_turno,
					orden,
					cod_producto
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/pan/ControlProduccionResultado.tpl');
			$tpl->prepare();
			
			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';
			$nombre = $db->query($sql);
			
			$tpl->assign('num_cia', $_REQUEST['num_cia']);
			$tpl->assign('nombre', $nombre[0]['nombre_corto']);
			
			if ($result) {
				$turno = NULL;
				foreach ($result as $r) {
					if ($turno != $r['turno']) {
						if ($turno != NULL) {
							$tpl->assign('turno.row_color', $row_color ? 'on' : 'off');
						}
						
						$turno = $r['turno'];
						
						$tpl->newBlock('turno');
						$tpl->assign('cod', $r['cod_turno']);
						$tpl->assign('turno', $r['turno']);
						
						$row_color = FALSE;
					}
					
					$tpl->newBlock('producto');
					$tpl->assign('row_color', $row_color ? 'on' : 'off');
					$row_color = !$row_color;
					$tpl->assign('id', $r['id']);
					$tpl->assign('cod', $r['cod_producto']);
					$tpl->assign('producto', $r['producto']);
					$tpl->assign('precio_raya', $r['precio_raya'] > 0 ? number_format($r['precio_raya'], 4, '.', ',') : '&nbsp;');
					$tpl->assign('porc_raya', $r['porc_raya'] > 0 ? number_format($r['porc_raya'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('precio_venta', $r['precio_venta'] > 0 ? number_format($r['precio_venta'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('tantos', $r['tantos'] > 0 ? number_format($r['tantos'], 3, '.', ',') : '&nbsp;');
					$tpl->assign('orden', $r['orden'] > 0 ? $r['orden'] : '&nbsp;');
				}
			}
			if ($turno != NULL) {
				$tpl->assign('turno.row_color', $row_color ? 'on' : 'off');
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'ultimoOrden':
			$sql = '
				SELECT
					COALESCE(MAX(num_orden), 0) + 1
						AS
							orden
				FROM
					control_produccion
				WHERE
						num_cia = ' . $_REQUEST['num_cia'] . '
					AND
						cod_turno = ' . $_REQUEST['cod_turno'] . '
					AND
						num_orden > 0
			';
			$result = $db->query($sql);
			
			echo $result[0]['orden'];
		break;
		
		case 'agregar':
			$sql = '
				SELECT
					id
				FROM
					control_produccion_aut
				WHERE
						num_cia = ' . $_REQUEST['num_cia'] . '
					AND
						tsmod IS NULL
			';
			
			if (!in_array($_SESSION['iduser'], array(1, 4, 19, 42)) && !$db->query($sql)) {
				echo -1;
			}
			else {
				$tpl = new TemplatePower('plantillas/pan/ControlProduccionAgregar.tpl');
				$tpl->prepare();
				
				$sql = '
					SELECT
						nombre_corto
					FROM
						catalogo_companias
					WHERE
						num_cia = ' . $_REQUEST['num_cia'] . '
				';
				$nombre = $db->query($sql);
				
				$tpl->assign('num_cia', $_REQUEST['num_cia']);
				$tpl->assign('nombre', $nombre[0]['nombre_corto']);
				
				$sql = '
					SELECT
						cod_turno
							AS
								cod,
						descripcion
							AS
								nombre
					FROM
						catalogo_turnos
					WHERE
						cod_turno
							IN
								(
									1,
									2,
									3,
									4,
									8,
									9
								)
					ORDER BY
						cod_turno
				';
				$turnos = $db->query($sql);
				
				foreach ($turnos as $t) {
					$tpl->newBlock('turno');
					$tpl->assign('cod', $t['cod']);
					$tpl->assign('nombre', $t['nombre']);
				}
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'grabar':
			$sql = '
				SELECT
					idcontrol_produccion
				FROM
					control_produccion
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
					AND cod_turno = ' . $_REQUEST['cod_turno'] . '
					AND cod_producto = ' . $_REQUEST['cod_producto'] . '
			';
			$result = $db->query($sql);
			
			if ($result) {
				echo -1;
			}
			else {
				$sql = '';
				
				/*
				@ [28-Sep-2010] Si el número de orden es mayor a 0 recorrer los productos posteriores
				*/
				if ($_REQUEST['num_orden'] > 0) {
					$sql .= '
						UPDATE
							control_produccion
						SET
							num_orden = num_orden + 1
						WHERE
							num_cia = ' . $_REQUEST['num_cia'] . '
							AND cod_turno = ' . $_REQUEST['cod_turno'] . '
							AND num_orden >= ' . $_REQUEST['num_orden'] . '
					' . ";\n";
				}

				$sql .= '
					INSERT INTO
						control_produccion
							(
								num_cia,
								cod_turno,
								cod_producto,
								num_orden,
								precio_raya,
								porc_raya,
								precio_venta,
								tantos
							)
					VALUES
						(
							' . $_REQUEST['num_cia'] . ',
							' . $_REQUEST['cod_turno'] . ',
							' . $_REQUEST['cod_producto'] . ',
							' . (isset($_REQUEST['num_orden']) ? $_REQUEST['num_orden'] : 'NULL') . ',
							' . (isset($_REQUEST['precio_raya']) ? get_val($_REQUEST['precio_raya']) : 0) . ',
							' . (isset($_REQUEST['porc_raya']) ? get_val($_REQUEST['porc_raya']) : 0) . ',
							' . (isset($_REQUEST['precio_venta']) ? get_val($_REQUEST['precio_venta']) : 0) . ',
							' . (isset($_REQUEST['tantos']) ? get_val($_REQUEST['tantos']) : 0) . '
						)
				' . ";\n";

				// [01-Jul-2014] Guardar movimiento en la tabla de modificaciones de panaderias
				if ( ! $db->query("SELECT id FROM actualizacion_panas WHERE num_cia = {$_REQUEST['num_cia']} AND status <= 0 AND tsalta::DATE = NOW()::DATE AND metodo = 'actualizar_control_produccion'"))
				{
					$sql .= "
						INSERT INTO
							actualizacion_panas (
								num_cia,
								iduser,
								metodo,
								parametros
							)
							VALUES (
								{$_REQUEST['num_cia']},
								{$_SESSION['iduser']},
								'actualizar_control_produccion',
								'num_cia={$_REQUEST['num_cia']}'
							);\n
					";
				}
				
				$db->query($sql);

				// $mysql_db = new DBclass($mysql_dsn, 'autocommit=yes');

				if (isset($_REQUEST['precio_venta']) && ! $db->query("
					SELECT
						*
					FROM
						productos_venta
					WHERE
						num_cia = {$_REQUEST['num_cia']}
						AND cod_producto = {$_REQUEST['cod_producto']}
						AND precio_venta = " . get_val($_REQUEST['precio_venta']) . "
						AND tsbaja IS NULL
				"))
				{
					$sql = "
						SELECT
							nombre,
							tipo_pan
						FROM
							catalogo_productos
						WHERE
							cod_producto = {$_REQUEST['cod_producto']}
					";

					$producto = $db->query($sql);

					$sql = '
						INSERT INTO
							productos_venta (
								num_cia,
								cod_producto,
								precio_venta,
								orden,
								decimales,
								venta_maxima,
								idcontrol_produccion
							)
							VALUES (
								' . $_REQUEST['num_cia'] . ',
								' . $_REQUEST['cod_producto'] . ',
								' . (isset($_REQUEST['precio_venta']) ? get_val($_REQUEST['precio_venta']) : 0) . ',
								0,
								0,
								0,
								(
									SELECT
										last_value
									FROM
										control_produccion_idcontrol_produccion_seq
								)
							)
					' . ";\n";

					$db->query($sql);

					// $sql = "
					// 	INSERT INTO
					// 		`tbl_productos` (
					// 			`Clave`,
					// 			`Descripcion`,
					// 			`Precio`,
					// 			`num_cia`,
					// 			`tipo_pan`,
					// 			`consecutivo`,
					// 			`decimal`,
					// 			`VentaMaxima`
					// 		)
					// 		VALUES (
					// 			'{$_REQUEST['cod_producto']}',
					// 			'{$producto[0]['nombre']}',
					// 			'" . get_val($_REQUEST['precio_venta']) . "',
					// 			'{$_REQUEST['num_cia']}',
					// 			'{$producto[0]['tipo_pan']}',
					// 			0,
					// 			0,
					// 			0
					// 		)
					// ";

					// $mysql_db->query($sql);

					// $sql = "
					// 	INSERT INTO
					// 		`controlproduccion` (
					// 			`num_cia`,
					// 			`IdTurno`,
					// 			`IdProducto`,
					// 			`PrecioRaya`,
					// 			`PorRaya`,
					// 			`PrecioVenta`,
					// 			`num_orden`
					// 		)
					// 		VALUES (
					// 			'{$_REQUEST['num_cia']}',
					// 			'{$_REQUEST['cod_turno']}',
					// 			'{$_REQUEST['cod_producto']}',
					// 			'" . (isset($_REQUEST['precio_raya']) ? get_val($_REQUEST['precio_raya']) : 0) . "',
					// 			'" . (isset($_REQUEST['porc_raya']) ? get_val($_REQUEST['porc_raya']) : 0) . "',
					// 			'" . (isset($_REQUEST['precio_venta']) ? get_val($_REQUEST['precio_venta']) : 0) . "',
					// 			'" . (isset($_REQUEST['num_orden']) ? $_REQUEST['num_orden'] : 0) . "'
					// 		)
					// ";

					// $mysql_db->query($sql);
				}
			}
		break;
		
		case 'modificar':
			$sql = '
				SELECT
					num_cia,
					cc.nombre_corto
						AS
							nombre,
					cod_turno,
					t.descripcion
						AS
							turno,
					cod_producto,
					p.nombre
						AS
							producto,
					num_orden,
					precio_raya,
					porc_raya,
					precio_venta,
					tantos
				FROM
						control_produccion
							cp
					LEFT JOIN
						catalogo_companias
							cc
								USING
									(
										num_cia
									)
					LEFT JOIN
						catalogo_turnos
							t
								USING
									(
										cod_turno
									)
					LEFT JOIN
						catalogo_productos
							p
								USING
									(
										cod_producto
									)
				WHERE
					idcontrol_produccion = ' . $_REQUEST['id'] . '
			';
			
			$result = $db->query($sql);
			$r = $result[0];
			
			$sql = '
				SELECT
					id
				FROM
					control_produccion_aut
				WHERE
						num_cia = ' . $r['num_cia'] . '
					AND
						tsmod IS NULL
			';
			
			if (!in_array($_SESSION['iduser'], array(1, 4, 19, 42)) && !$db->query($sql)) {
				echo -1;
			}
			else {
				$tpl = new TemplatePower('plantillas/pan/ControlProduccionModificar.tpl');
				$tpl->prepare();
				
				$tpl->assign('id', $_REQUEST['id']);
				$tpl->assign('num_cia', $r['num_cia']);
				$tpl->assign('nombre', $r['nombre']);
				$tpl->assign('cod_turno', $r['cod_turno']);
				$tpl->assign('turno', $r['turno']);
				$tpl->assign('cod_producto', $r['cod_producto']);
				$tpl->assign('producto', $r['producto']);
				$tpl->assign('num_orden', $r['num_orden']);
				$tpl->assign('precio_raya', $r['precio_raya'] > 0 ? number_format($r['precio_raya'], 4, '.', ',') : '');
				$tpl->assign('porc_raya', $r['porc_raya'] > 0 ? number_format($r['porc_raya'], 2, '.', ',') : '');
				$tpl->assign('precio_venta', $r['precio_venta'] > 0 ? number_format($r['precio_venta'], 3, '.', ',') : '');
				$tpl->assign('tantos', $r['tantos'] > 0 ? number_format($r['tantos'], 2, '.', ',') : '');
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'actualizar':
			// $mysql_db = new DBclass($mysql_dsn, 'autocommit=yes');

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
					idcontrol_produccion = {$_REQUEST['id']}
			";

			$producto = $db->query($sql);

			$sql = "
				SELECT
					*
				FROM
					control_produccion
				WHERE
					idcontrol_produccion = {$_REQUEST['id']}
			";

			$control = $db->query($sql);

			$sql = '';
			
			/*
			@ [28-Sep-2010] Verificar si es necesario recorrer el número de orden de los productos
			*/
			if ($_REQUEST['num_orden'] > 0 && $_REQUEST['num_orden'] != $_REQUEST['num_orden_old']) {
				if ($_REQUEST['num_orden_old'] > 0) {
					/*
					@ Recorrer en caso de que el nuevo número de orden sea mayor al anterior
					*/
					if ($_REQUEST['num_orden'] > $_REQUEST['num_orden_old']) {
						$sql = '
							UPDATE
								control_produccion
							SET
								num_orden = num_orden - 1
							WHERE
									num_cia = ' . $_REQUEST['num_cia'] . '
								AND
									cod_turno = ' . $_REQUEST['cod_turno'] . '
								AND
									num_orden BETWEEN ' . get_val($_REQUEST['num_orden_old']) . ' + 1 AND ' . get_val($_REQUEST['num_orden']) . '
						' . ";\n";
					}
					/*
					@ Recorrer en caso de que el nuevo número de orden sea menor al anterior
					*/
					else {
						$sql = '
							UPDATE
								control_produccion
							SET
								num_orden = num_orden + 1
							WHERE
									num_cia = ' . $_REQUEST['num_cia'] . '
								AND
									cod_turno = ' . $_REQUEST['cod_turno'] . '
								AND
									num_orden BETWEEN ' . get_val($_REQUEST['num_orden']) . ' AND ' . get_val($_REQUEST['num_orden_old']) . ' - 1
						' . ";\n";
					}
				}
				/*
				@ Recorrer en caso de que recien se asigne un número de orden (solo para productos que nunca tubieron un número de orden)
				*/
				else {
					$sql .= '
						UPDATE
							control_produccion
						SET
							num_orden = num_orden + 1
						WHERE
								num_cia = ' . $_REQUEST['num_cia'] . '
							AND
								cod_turno = ' . $_REQUEST['cod_turno'] . '
							AND
								num_orden >= ' . $_REQUEST['num_orden'] . '
					' . ";\n";
				}
			}
			
			$sql .= '
				UPDATE
					control_produccion
				SET
					num_orden = ' . (isset($_REQUEST['num_orden']) ? $_REQUEST['num_orden'] : 'NULL') . ',
					precio_raya = ' . (isset($_REQUEST['precio_raya']) ? get_val($_REQUEST['precio_raya']) : 0) . ',
					porc_raya = ' . (isset($_REQUEST['porc_raya']) ? get_val($_REQUEST['porc_raya']) : 0) . ',
					precio_venta = ' . (isset($_REQUEST['precio_venta']) ? get_val($_REQUEST['precio_venta']) : 0) . ',
					tantos = ' . (isset($_REQUEST['tantos']) ? get_val($_REQUEST['tantos']) : 0) . '
				WHERE
					idcontrol_produccion = ' . $_REQUEST['id'] . '
			' . ";\n";

			$sql .= '
				UPDATE
					productos_venta
				SET
					precio_venta = ' . get_val($_REQUEST['precio_venta']) . ',
					tsmod = NOW(),
					idmod = ' . $_SESSION['iduser'] . '
				WHERE
					idcontrol_produccion = ' . $_REQUEST['id'] . ';
			';

			// [01-Jul-2014] Guardar movimiento en la tabla de modificaciones de panaderias
			if ( ! $db->query("SELECT id FROM actualizacion_panas WHERE num_cia = {$_REQUEST['num_cia']} AND status <= 0 AND tsalta::DATE = NOW()::DATE AND metodo = 'actualizar_control_produccion'"))
			{
				$sql .= "
					INSERT INTO
						actualizacion_panas (
							num_cia,
							iduser,
							metodo,
							parametros
						)
						VALUES (
							{$_REQUEST['num_cia']},
							{$_SESSION['iduser']},
							'actualizar_control_produccion',
							'num_cia={$_REQUEST['num_cia']}'
						);\n
				";
			}

			$db->query($sql);

			// $sql = "
			// 	UPDATE
			// 		`tbl_productos`
			// 	SET
			// 		`tipo_pan` = '{$producto[0]['tipo_pan']}',
			// 		`Precio` = '" . get_val($_REQUEST['precio_venta']) . "'
			// 	WHERE
			// 		`num_cia` = '{$producto[0]['num_cia']}'
			// 		AND `Clave` = '{$producto[0]['cod_producto']}'
			// 		AND `Precio` = '{$producto[0]['precio_venta']}'
			// 		AND `decimal` = '{$producto[0]['decimales']}'
			// 		AND `VentaMaxima` = {$producto[0]['venta_maxima']}
			// 		AND `fechaBaja` IS NULL
			// ";

			// $mysql_db->query($sql);

			// $sql = "
			// 	UPDATE
			// 		`controlproduccion`
			// 	SET
			// 		`PrecioRaya` = '" . (isset($_REQUEST['precio_raya']) ? get_val($_REQUEST['precio_raya']) : 0) . "',
			// 		`PorRaya` = '" . (isset($_REQUEST['porc_raya']) ? get_val($_REQUEST['porc_raya']) : 0) . "',
			// 		`PrecioVenta` = '" . (isset($_REQUEST['precio_venta']) ? get_val($_REQUEST['precio_venta']) : 0) . "'
			// 	WHERE
			// 		`num_cia` = '{$control[0]['num_cia']}'
			// 		AND `IdTurno` = '{$control[0]['cod_turno']}'
			// 		AND `IdProducto` = '{$control[0]['cod_producto']}'
			// 		AND `PrecioRaya` = '" . get_val($control[0]['precio_raya']) . "'
			// 		AND `PorRaya` = '" . get_val($control[0]['porc_raya']) . "'
			// 		AND `PrecioVenta` = '" . get_val($control[0]['precio_venta']) . "'
			// 		AND `Status` = 1
			// ";

			// $mysql_db->query($sql);
		break;
		
		case 'borrar':
			$sql = '
				SELECT
					id
				FROM
					control_produccion_aut
				WHERE
						num_cia = ' . $_REQUEST['num_cia'] . '
					AND
						tsmod IS NULL
			';
			
			if (!in_array($_SESSION['iduser'], array(1, 4, 19, 42)) && !$db->query($sql)) {
				echo -1;
			}
			else {
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
						idcontrol_produccion = {$_REQUEST['id']}
				";

				$producto = $db->query($sql);

				$sql = "
					SELECT
						*
					FROM
						control_produccion
					WHERE
						idcontrol_produccion = {$_REQUEST['id']}
				";

				$control = $db->query($sql);

				$sql = '
					UPDATE
						productos_venta
					SET
						tsbaja = NOW(),
						idbaja = ' . $_SESSION['iduser'] . '
					WHERE
						(num_cia, cod_producto, precio_venta) IN (
							SELECT
								num_cia,
								cod_producto,
								precio_venta
							FROM
								control_produccion
							WHERE
								idcontrol_produccion = ' . $_REQUEST['id'] . '
						)
				' . ";\n";

				$sql .= '
					DELETE
					FROM
						control_produccion
					WHERE
						idcontrol_produccion = ' . $_REQUEST['id'] . '
				' . ";\n";

				// [01-Jul-2014] Guardar movimiento en la tabla de modificaciones de panaderias
				if ( ! $db->query("SELECT id FROM actualizacion_panas WHERE num_cia = {$_REQUEST['num_cia']} AND status <= 0 AND tsalta::DATE = NOW()::DATE AND metodo = 'actualizar_control_produccion'"))
				{
					$sql .= "
						INSERT INTO
							actualizacion_panas (
								num_cia,
								iduser,
								metodo,
								parametros
							)
							VALUES (
								{$_REQUEST['num_cia']},
								{$_SESSION['iduser']},
								'actualizar_control_produccion',
								'num_cia={$_REQUEST['num_cia']}'
							);\n
					";
				}

				$db->query($sql);

				// $mysql_db = new DBclass($mysql_dsn, 'autocommit=yes');

				// $sql = "
				// 	UPDATE
				// 		`tbl_productos`
				// 	SET
				// 		`fechaBaja` = '" . date('Y-m-d') . "'
				// 	WHERE
				// 		`num_cia` = '{$producto[0]['num_cia']}'
				// 		AND `Clave` = '{$producto[0]['cod_producto']}'
				// 		AND `tipo_pan` = '{$producto[0]['tipo_pan']}'
				// 		AND `Precio` = '{$producto[0]['precio_venta']}'
				// 		AND `decimal` = '{$producto[0]['decimales']}'
				// 		AND `VentaMaxima` = {$producto[0]['venta_maxima']}
				// 		AND `fechaBaja` IS NULL
				// ";

				// $mysql_db->query($sql);

				// $sql = "
				// 	UPDATE
				// 		`controlproduccion`
				// 	SET
				// 		`Status` = '0'
				// 	WHERE
				// 		`num_cia` = '{$control[0]['num_cia']}'
				// 		AND `IdTurno` = '{$control[0]['cod_turno']}'
				// 		AND `IdProducto` = '{$control[0]['cod_producto']}'
				// 		AND `PrecioRaya` = '" . get_val($control[0]['precio_raya']) . "'
				// 		AND `PorRaya` = '" . get_val($control[0]['porc_raya']) . "'
				// 		AND `PrecioVenta` = '" . get_val($control[0]['precio_venta']) . "'
				// 		AND `Status` = 1
				// ";echo $sql;

				// $mysql_db->query($sql);
			}
		break;
		
		case 'desautorizar':
			$sql = '
				UPDATE
					control_produccion_aut
				SET
					iduser_mod = ' . $_SESSION['iduser'] . ',
					tsmod = now()
				WHERE
						num_cia = ' . $_REQUEST['num_cia'] . '
					AND
						tsmod IS NULL
			';
			$db->query($sql);
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/pan/ControlProduccion.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
