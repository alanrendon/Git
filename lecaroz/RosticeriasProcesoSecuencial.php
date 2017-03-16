<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value) {
	return intval($value, 10);
}

$_meses = array(
	1	=> 'enero',
	2	=> 'febrero',
	3	=> 'marzo',
	4	=> 'abril',
	5	=> 'mayo',
	6	=> 'junio',
	7	=> 'julio',
	8	=> 'agosto',
	9	=> 'septiembre',
	10	=> 'octubre',
	11	=> 'noviembre',
	12	=> 'diciembre'
);

$_dias = array(
	0	=> 'domingo',
	1	=> 'lunes',
	2	=> 'martes',
	3	=> 'miercoles',
	4	=> 'jueves',
	5	=> 'viernes',
	6	=> 'sabado'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {

		case 'inicio':

			$tpl = new TemplatePower('plantillas/ros/RosticeriasProcesoSecuencialInicio.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();

			break;

		case 'obtener_cia':
			$sql = 'SELECT nombre_corto FROM catalogo_companias WHERE tipo_cia = 2 AND num_cia = ' . $_REQUEST['num_cia'];

			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode($result[0]['nombre_corto']);
			}

			break;

		case 'obtener_pro':
			$sql = 'SELECT nombre FROM catalogo_proveedores WHERE num_proveedor < 9000 AND num_proveedor = ' . $_REQUEST['num_pro'];

			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode($result[0]['nombre']);
			}

			break;

		case 'obtener_gasto':
			$sql = 'SELECT descripcion FROM catalogo_gastos WHERE codgastos = ' . $_REQUEST['codgastos'];

			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode($result[0]['descripcion']);
			}

			break;

		case 'obtener_mp':
			$sql = "SELECT
				codmp,
				nombre,
				--precio_compra,
				precio_unidad
					AS precio_inv,
				no_exi,
				COALESCE((SELECT precio_compra FROM precios_guerra WHERE num_cia = ir.num_cia AND codmp = ir.codmp AND precio_compra > 0 AND num_proveedor " . ($_REQUEST['num_pro'] > 0 ? "= {$_REQUEST['num_pro']}" : "IS NULL") . " LIMIT 1), 0) AS precio_compra,
				COALESCE((SELECT num_proveedor FROM precios_guerra WHERE num_cia = ir.num_cia AND codmp = ir.codmp AND precio_compra > 0 AND num_proveedor " . ($_REQUEST['num_pro'] > 0 ? "= {$_REQUEST['num_pro']}" : "IS NULL") . " LIMIT 1), 0) AS num_pro
			FROM
				catalogo_mat_primas cmp
				LEFT JOIN inventario_real ir USING (codmp)
				--LEFT JOIN precios_guerra pg USING (codmp)
			WHERE
				ir.num_cia = {$_REQUEST['num_cia']}
				AND ir.num_cia = {$_REQUEST['num_cia']}
				AND (
					cmp.tipo_cia = FALSE
					OR ir.codmp = 170
				)
				AND ir.codmp = {$_REQUEST['codmp']}";

			$result = $db->query($sql);

			if ($result) {
				echo json_encode(array(
					'codmp'			=> intval($result[0]['codmp']),
					'nombre'		=> utf8_encode($result[0]['nombre']),
					'precio_compra'	=> floatval($result[0]['precio_compra']),
					'precio_inv'	=> floatval($result[0]['precio_inv']),
					'no_exi'		=> $result[0]['no_exi'] == 't' ? TRUE : FALSE,
					'num_pro'		=> intval($result[0]['num_pro'])
				));
			}

			break;

		case 'obtener_mp_catalogo':
			$sql = "SELECT codmp, nombre FROM catalogo_mat_primas cmp WHERE tipo_cia = FALSE AND codmp = {$_REQUEST['codmp']}";

			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode($result[0]['nombre']);
			}

			break;

		case 'obtener_lista_empleados':
			$sql = "SELECT
				id
					AS idempleado,
				num_emp,
				nombre_completo
					AS nombre_emp
			FROM
				catalogo_trabajadores ct
			WHERE
				num_cia_emp = {$_REQUEST['num_cia']}
				AND fecha_baja IS NULL
				" . ( ! isset($_REQUEST['no_omitir']) ? "AND id NOT IN (
					SELECT
						id_empleado
					FROM
						prestamos sp
						LEFT JOIN catalogo_trabajadores sct
							ON (sct.id = sp.id_empleado)
					WHERE
						sct.num_cia_emp = {$_REQUEST['num_cia']}
						AND sp.pagado = FALSE
					GROUP BY
						sp.id_empleado
				)" : '') . "
			ORDER BY
				nombre_emp";

			$empleados = $db->query($sql);

			if ($empleados) {
				$tpl = new TemplatePower('plantillas/ros/RosticeriasProcesoSecuencialListaEmpleados.tpl');
				$tpl->prepare();

				$tpl->assign('index', isset($_REQUEST['index']) ? $_REQUEST['index'] : '');

				foreach ($empleados as $i => $emp) {
					$tpl->newBlock('empleado');

					$tpl->assign('color', $i % 2 == 0 ? 'fff' : 'eee');
					$tpl->assign('empleado', htmlentities(json_encode(array(
						'idempleado' => intval($emp['idempleado']),
						'num_emp'    => intval($emp['num_emp']),
						'nombre_emp' => utf8_encode($emp['nombre_emp'])
					))));
					$tpl->assign('num_emp', $emp['num_emp']);
					$tpl->assign('nombre_emp', utf8_encode($emp['nombre_emp']));
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'validar_fecha':
			$sql = "
				SELECT
					(MAX(fecha) + INTERVAL '1 DAY')::DATE
						AS fecha
				FROM
					total_companias
				WHERE
					num_cia = {$_REQUEST['num_cia']}
			";

			$result = $db->query($sql);

			header('Content-Type: application/json');

			if ($result[0]['fecha'] != '')
			{
				echo json_encode(array(
					'num_cia'	=> intval($_REQUEST['num_cia'], 10),
					'status'	=> 1,
					'fecha'		=> $result[0]['fecha']
				));
			}
			else
			{
				echo json_encode(array(
					'num_cia'	=> intval($_REQUEST['num_cia'], 10),
					'status'	=> -1
				));
			}

			break;

		case 'definir_fecha':
			$tpl = new TemplatePower('plantillas/ros/RosticeriasProcesoSecuencialDefinirFecha.tpl');
			$tpl->prepare();

			$nombre_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = {$_REQUEST['num_cia']}");

			$tpl->assign('num_cia', $_REQUEST['num_cia']);
			$tpl->assign('nombre_cia', utf8_encode($nombre_cia[0]['nombre_corto']));

			echo $tpl->getOutputContent();

			break;

		case 'validar_productos_venta':
			$sql = "SELECT
				mov.num_cia,
				cc.nombre_corto
					AS nombre_cia,
				mov.codmp,
				cmp.nombre
					AS nombremp,
				mov.precio
					AS precio_venta
			FROM
				mov_inv_tmp mov
				LEFT JOIN catalogo_companias cc
					USING (num_cia)
				LEFT JOIN catalogo_mat_primas cmp
					USING (codmp)
			WHERE
				mov.num_cia = {$_REQUEST['num_cia']}
				AND mov.fecha = '{$_REQUEST['fecha']}'
				AND mov.codmp NOT IN (
					SELECT
						codmp
					FROM
						inventario_real
						LEFT JOIN precios_guerra
							USING (num_cia, codmp)
						LEFT JOIN catalogo_mat_primas
							USING (codmp)
					WHERE
						num_cia = {$_REQUEST['num_cia']}
						AND codmp NOT IN (90, 425, 194, 138, 364, 167, 61, 169)
						AND (
							precio_venta > 0
							OR codmp = 925
						)
				)
			ORDER BY
				mov.codmp";

			$result = $db->query($sql);

			if ($result)
			{
				$tpl = new TemplatePower('plantillas/ros/RosticeriasProcesoSecuencialProductosNuevos.tpl');
				$tpl->prepare();

				$tpl->assign('num_cia', $result[0]['num_cia']);
				$tpl->assign('nombre_cia', utf8_encode($result[0]['nombre_cia']));

				list($dia, $mes, $anio) = array_map('toInt', explode('/', $_REQUEST['fecha']));

				$dia_semana = date('w', mktime(0, 0, 0, $mes, $dia, $anio));

				$tpl->assign('fecha', mb_strtoupper($_dias[$dia_semana] . ', ' . $dia . ' DE ' . $_meses[$mes] . ' DE ' . $anio));

				foreach ($result as $row) {
					$tpl->newBlock('row');

					$tpl->assign('codmp', $row['codmp']);
					$tpl->assign('nombremp', utf8_encode($row['nombremp']));
					$tpl->assign('precio_venta', number_format($row['precio_venta'], 2));
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'obtener_precios_compra':
			$tpl = new TemplatePower('plantillas/ros/RosticeriasProcesoSecuencialPreciosCompra.tpl');
			$tpl->prepare();

			// Precios de compra

			$sql = "SELECT
				pg.id,
				pg.num_cia,
				pg.codmp,
				cmp.nombre
					AS nombre_mp,
				pg.num_proveedor
					AS num_pro,
				cp.nombre
					AS nombre_pro,
				precio_compra
			FROM
				precios_guerra pg
				LEFT JOIN catalogo_proveedores cp
					USING (num_proveedor)
				LEFT JOIN catalogo_mat_primas cmp
					USING (codmp)
			WHERE
				pg.num_cia = {$_REQUEST['num_cia']}
				AND precio_compra > 0
			ORDER BY
				pg.codmp";

			$result = $db->query($sql);

			if ($result)
			{
				foreach ($result as $row)
				{
					$tpl->newBlock('precio_compra');

					$data = array(
						'id'			=> intval($row['id']),
						'num_cia'		=> intval($row['num_cia']),
						'codmp'			=> intval($row['codmp']),
						'num_pro'		=> intval($row['num_pro']),
						'precio_compra'	=> floatval($row['precio_compra']),
						'status'		=> 1
					);

					$tpl->assign('data', htmlentities(json_encode($data)));
					$tpl->assign('codmp', $row['codmp']);
					$tpl->assign('nombre_mp', utf8_encode($row['nombre_mp']));
					$tpl->assign('num_pro', $row['num_pro']);
					$tpl->assign('nombre_pro', utf8_encode($row['nombre_pro']));
					$tpl->assign('precio_compra', number_format($row['precio_compra'], 4));
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'actualizar_precios_compra':
			$sql = '';

			foreach ($_REQUEST['data'] as $key => $json_data) {
				$data = json_decode($json_data);

				if ($data->status == 0)
				{
					$sql .= "DELETE FROM precios_guerra WHERE id = {$data->id};\n";
				}
				else
				{
					if ($data->id > 0)
					{
						$sql .= "UPDATE precios_guerra SET precio_compra = {$data->precio_compra} WHERE id = {$data->id};\n";
					}
					else
					{
						$sql .= "INSERT INTO precios_guerra (num_cia, codmp, num_proveedor, precio_compra, orden) VALUES ({$data->num_cia}, {$data->codmp}, " . ($data->num_pro > 0 ? $data->num_pro : 'NULL') . ", {$data->precio_compra}, (COALESCE((SELECT MAX(orden) + 1 FROM precios_guerra WHERE num_cia = {$data->num_cia}), 1)));\n";
					}
				}
			}

			$db->query($sql);

			break;

		case 'actualizar_precio_venta':
			$sql = "UPDATE precios_guerra SET precio_venta = {$_REQUEST['precio_venta']} WHERE id = {$_REQUEST['id']};\n";

			$db->query($sql);

			break;

		case 'proceso_secuencial':
			list($dia, $mes, $anio) = array_map('toInt', explode('/', $_REQUEST['fecha']));

			$dia_semana = date('w', mktime(0, 0, 0, $mes, $dia, $anio));

			$tpl = new TemplatePower('plantillas/ros/RosticeriasProcesoSecuencialPrincipal.tpl');
			$tpl->prepare();

			$nombre_cia = $db->query("
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					num_cia = {$_REQUEST['num_cia']}
			");

			$tpl->assign('num_cia', $_REQUEST['num_cia']);
			$tpl->assign('fecha', $_REQUEST['fecha']);

			$tpl->assign('nombre_cia', utf8_encode($nombre_cia[0]['nombre_corto']));
			$tpl->assign('fecha_escrita', mb_strtoupper($_dias[$dia_semana] . ', ' . $dia . ' DE ' . $_meses[$mes] . ' DE ' . $anio));

			// Compras directas

			$sql = "SELECT
				mov.id,
				mov.codmp,
				cmp.nombre AS nombremp,
				mov.cantidad,
				mov.kilos,
				-- pg.precio_compra,
				COALESCE((SELECT precio_compra FROM precios_guerra WHERE num_cia = mov.num_cia AND codmp = mov.codmp AND precio_compra > 0 AND num_proveedor IS NULL LIMIT 1), 0) AS precio_compra,
				ir.precio_unidad AS precio_inv,
				mov.aplica,
				mov.num_proveedor AS num_pro,
				mov.num_fact
			FROM
				mov_inv_tmp mov
				LEFT JOIN catalogo_mat_primas cmp USING (codmp)
				-- LEFT JOIN precios_guerra pg USING (num_cia, codmp)
				LEFT JOIN inventario_real ir USING (num_cia, codmp)
			WHERE
				mov.num_cia = {$_REQUEST['num_cia']}
				AND mov.fecha = '{$_REQUEST['fecha']}'
				AND mov.tipomov = FALSE
				AND mov.codmp NOT IN (160, 600, 700, 297, 363, 352, 434, 869, 877, 1083)
				AND cmp.no_exi = FALSE
			ORDER BY
				mov.codmp";

			$result = $db->query($sql);

			$total_compras = 0;

			for ($i = 0; $i < 15; $i++)
			{
				$tpl->newBlock('cd_row');

				$tpl->assign('i', $i);

				if ($result && isset($result[$i]))
				{
					$row = $result[$i];

					$tpl->assign('cd_codmp', $row['codmp']);
					$tpl->assign('cd_nombremp', utf8_encode($row['nombremp']));
					$tpl->assign('cd_cantidad', number_format($row['cantidad'], 2));
					$tpl->assign('cd_kilos', $row['kilos'] != 0 ? number_format($row['kilos'], 2) : '');
					$tpl->assign('cd_precio', number_format($row['precio_compra'] > 0 ? $row['precio_compra'] : 0, 4));
					$tpl->assign('cd_precio_inv', round($row['precio_inv'], 2));
					$tpl->assign('cd_min', round($row['precio_inv'] * 0.80, 2));
					$tpl->assign('cd_max', round($row['precio_inv'] * 1.20, 2));
					$tpl->assign('cd_importe', number_format($row['cantidad'] * ($row['precio_compra'] > 0 ? $row['precio_compra'] : 0), 2));
					$tpl->assign('cd_aplica_gasto', ' checked="checked"');
					$tpl->assign('cd_num_pro_readonly', ' readonly="readonly"');
					$tpl->assign('cd_num_pro', '');
					$tpl->assign('cd_nombre_pro', 'COMPRAS DIRECTAS');

					$total_compras += $row['cantidad'] * ($row['precio_compra'] > 0 ? $row['precio_compra'] : 0);
				}

				$tpl->assign('_ROOT.cd_total', number_format($total_compras, 2));
			}

			// Ventas

			$sql = "SELECT
				pg.id,
				ir.codmp,
				pg.nombre_alt
					AS nombremp,
				CASE
					WHEN cmp.no_exi = FALSE THEN
						(
							ir.existencia - COALESCE((
								SELECT
									SUM (cantidad)
								FROM
									fact_rosticeria
								WHERE
									num_cia = ir.num_cia
									AND codmp = ir.codmp
									AND fecha_mov > '{$_REQUEST['fecha']}'
							), 0)
						)
					ELSE
						0
				END
					AS existencia_inicial,
				/*COALESCE ((
					SELECT
						SUM (cantidad)
					FROM
						fact_rosticeria
					WHERE
						num_cia = ir.num_cia
					AND codmp = ir.codmp
					AND tipomov = FALSE
					AND fecha_mov = '{$_REQUEST['fecha']}'
					AND codmp NOT IN (160, 600, 700, 297, 363, 352)
				), 0)
					AS entradas,*/
				ir.precio_unidad,
				pg.precio_venta,
				cmp.no_exi,
				COALESCE ((
					SELECT
						cantidad
					FROM
						mov_inv_tmp
					WHERE
						num_cia = ir.num_cia
						AND codmp = ir.codmp
						AND tipomov = TRUE
						AND fecha = '{$_REQUEST['fecha']}'
						AND cantidad > 0
						AND precio = pg.precio_venta
					ORDER BY
						id
					LIMIT
						1
				), 0)
					AS cantidad
			FROM
				inventario_real ir
				LEFT JOIN precios_guerra pg
					USING (num_cia, codmp)
				LEFT JOIN catalogo_mat_primas cmp
					USING (codmp)
			WHERE
				ir.num_cia = {$_REQUEST['num_cia']}
				AND ir.codmp NOT IN (90, 425, 194, 138, 364, 167, 61, 169)
				AND (pg.precio_venta > 0 OR ir.codmp = 925)
			ORDER BY
				COALESCE(CASE WHEN pg.orden > 0 THEN pg.orden ELSE NULL END, CASE WHEN cmp.orden > 0 THEN cmp.orden ELSE NULL END, 99999),
				pg.codmp";

			$result = $db->query($sql);

			$total_ventas = 0;

			$tpl->assign('v_total', number_format($total_ventas, 2));

			if ($result)
			{
				foreach ($result as $row)
				{
					$tpl->newBlock('v_row');

					$tpl->assign('v_id', $row['id']);
					$tpl->assign('v_codmp', $row['codmp']);
					$tpl->assign('v_nombremp', utf8_encode($row['nombremp']));

					$tpl->assign('v_sin_existencia', $row['no_exi'] == 't' ? '1' : '');

					$tpl->assign('v_existencia_inicial', $row['existencia_inicial']);
					$tpl->assign('v_existencia', $row['existencia_inicial'] - $row['cantidad'] != 0 && $row['no_exi'] == 'f' ? number_format($row['existencia_inicial'] - $row['cantidad']) : '');
					$tpl->assign('v_existencia_color', $row['existencia_inicial'] - $row['cantidad'] < 0 ? 'red' : 'green');

					$tpl->assign('v_cantidad', $row['cantidad'] != 0 ? number_format($row['cantidad']) : '');
					$tpl->assign('v_precio_venta', $row['precio_venta'] != 0 ? number_format($row['precio_venta'], 2) : '');

					$importe = $row['cantidad'] * $row['precio_venta'];

					$tpl->assign('v_importe', $importe != 0 ? number_format($importe, 2) : '');
					$tpl->assign('v_importe_color', $importe < 0 ? 'red' : 'blue');

					$total_ventas += $importe;

					$tpl->assign('_ROOT.v_total', number_format($total_ventas, 2));
				}
			}

			// Gastos

			$sql = "SELECT
				g.codgastos,
				cg.descripcion,
				g.concepto,
				g.importe
			FROM
				gastos_tmp g
				LEFT JOIN catalogo_gastos cg
					USING (codgastos)
			WHERE
				g.num_cia = {$_REQUEST['num_cia']}
				AND g.fecha = '{$_REQUEST['fecha']}'
			ORDER BY
				g.id";

			$result = $db->query($sql);

			$total_gastos = 0;

			for ($i = 0; $i < 15; $i++)
			{
				$tpl->newBlock('g_row');

				if ($result && isset($result[$i]))
				{
					$row = $result[$i];

					$tpl->assign('g_codgastos', $row['codgastos'] > 0 ? $row['codgastos'] : '');
					$tpl->assign('g_descripcion', $row['descripcion'] != '' ? utf8_encode($row['descripcion']) : '');
					$tpl->assign('g_concepto', utf8_encode($row['concepto']));
					$tpl->assign('g_importe', number_format($row['importe'], 2));

					$total_gastos += $row['importe'];

					$tpl->assign('_ROOT.g_total', number_format($total_gastos, 2));
					$tpl->assign('_ROOT.g_compras', number_format($total_compras, 2));
					$tpl->assign('_ROOT.g_gastos', number_format($total_compras + $total_gastos, 2));
				}
			}

			// Prestamos a empleados

			$sql = "SELECT
				p.id_empleado,
				ct.num_emp,
				ct.nombre_completo
					AS nombre_empleado,
				SUM(
					CASE
						WHEN p.tipo_mov = FALSE THEN
							p.importe
						ELSE
							-p.importe
					END
				)
					AS saldo
			FROM
				prestamos p
				LEFT JOIN catalogo_trabajadores ct
					ON (ct.id = p.id_empleado)
			WHERE
				(
					ct.num_cia = {$_REQUEST['num_cia']}
					OR ct.num_cia_emp = '{$_REQUEST['num_cia']}'
				)
				AND p.pagado = FALSE
			GROUP BY
				p.id_empleado,
				ct.num_emp,
				nombre_empleado
			ORDER BY
				nombre_empleado";

			$prestamos_sistema = $db->query($sql);

			$total_saldo = 0;

			$tpl->assign('_ROOT.p_total_saldo_inicio', number_format($total_saldo, 2));
			$tpl->assign('_ROOT.p_total_prestamos', '0.00');
			$tpl->assign('_ROOT.p_total_abonos', '0.00');
			$tpl->assign('_ROOT.p_total_saldo_final', number_format($total_saldo, 2));

			if ($prestamos_sistema)
			{
				foreach ($prestamos_sistema as $row)
				{
					$tpl->newBlock('p_prestamo_sistema');

					$tpl->assign('p_id_emp', $row['id_empleado']);
					$tpl->assign('p_num_emp', $row['num_emp']);
					$tpl->assign('p_nombre_emp', utf8_encode($row['nombre_empleado']));
					$tpl->assign('p_saldo_emp_inicio', number_format($row['saldo'], 2));
					$tpl->assign('p_saldo_emp_final', number_format($row['saldo'], 2));

					$total_saldo += $row['saldo'];

					$tpl->assign('_ROOT.p_total_saldo_inicio', number_format($total_saldo, 2));
					$tpl->assign('_ROOT.p_total_saldo_final', number_format($total_saldo, 2));
				}
			}

			$sql = "SELECT
				id,
				nombre,
				saldo,
				tipo_mov,
				importe
			FROM
				prestamos_tmp
			WHERE
				num_cia = {$_REQUEST['num_cia']}
				AND fecha = '{$_REQUEST['fecha']}'
				AND tipo_mov IS NOT NULL
				AND importe > 0
			ORDER BY
				nombre";

			$movs_rosticeria = $db->query($sql);

			if ($movs_rosticeria)
			{
				foreach ($movs_rosticeria as $row)
				{
					$tpl->newBlock('p_mov_ros');

					$tpl->assign('p_id_tmp', $row['id']);
					$tpl->assign('p_nombre_emp', utf8_encode($row['nombre']));
					$tpl->assign('p_tipo', $row['tipo_mov'] == 'f' ? 'PRESTAMO' : 'ABONO');
					$tpl->assign('p_importe', number_format($row['importe'], 2));
					$tpl->assign('p_color', $row['tipo_mov'] == 'f' ? 'red' : 'blue');
				}
			}

			// Lecturas de tanques de gas

			$sql = "SELECT
				num_tanque,
				nombre,
				capacidad,
				(
					SELECT
						cantidad
					FROM
						tanques_gas_lecturas_tmp
					WHERE
						idtanque = ct.id
					AND fecha = '{$_REQUEST['fecha']}'
					LIMIT 1
				) AS lectura,
				(
					SELECT
						cantidad
					FROM
						tanques_gas_entradas_tmp
					WHERE
						idtanque = ct.id
					AND fecha = '{$_REQUEST['fecha']}'
					LIMIT 1
				) AS entrada,
				(
					SELECT
						nota
					FROM
						tanques_gas_entradas_tmp
					WHERE
						idtanque = ct.id
					AND fecha = '{$_REQUEST['fecha']}'
					LIMIT 1
				) AS nota_entrada
			FROM
				catalogo_tanques ct
			WHERE
				num_cia = {$_REQUEST['num_cia']}";

			$tanques = $db->query($sql);

			if ($tanques)
			{
				foreach ($tanques as $tanque)
				{
					$tpl->newBlock('tanque');

					$tpl->assign('num_tanque', $tanque['num_tanque']);
					$tpl->assign('nombre_tanque', utf8_encode($tanque['nombre']));
					$tpl->assign('capacidad', number_format($tanque['capacidad']));
					$tpl->assign('lectura', $tanque['lectura'] > 0 ? number_format($tanque['lectura']) : 'NO TOMARON LECTURA');
					$tpl->assign('entrada', $tanque['entrada'] > 0 ? number_format($tanque['entrada']) : '&nbsp;');
					$tpl->assign('nota', $tanque['nota_entrada'] != '' ? $tanque['nota_entrada'] : '&nbsp;');
				}
			}
			else
			{
				$tpl->newBlock('no_tanques');
			}

			echo $tpl->getOutputContent();

			break;

		case 'registrar':
			$sql = "";

			list($dia, $mes, $anio) = array_map('toInt', explode('/', $_REQUEST['fecha']));

			foreach ($_REQUEST['cd_codmp'] as $i => $codmp)
			{
				if ($codmp > 0)
				{
					if ( ! $db->query("SELECT idinv FROM historico_inventario WHERE num_cia = {$_REQUEST['num_cia']} AND codmp = {$codmp} LIMIT 1"))
					{
						$db->query("INSERT INTO historico_inventario (
							num_cia,
							codmp,
							fecha,
							existencia,
							precio_unidad
						) VALUES (
							{$_REQUEST['num_cia']},
							{$codmp},
							'" . date('d/m/Y', mktime(0, 0, 0, $mes, 0, $anio)) . "',
							0,
							0
						)");

						$db->query("INSERT INTO inventario_real (
							num_cia,
							codmp,
							existencia,
							precio_unidad
						) VALUES (
							{$_REQUEST['num_cia']},
							{$codmp},
							0,
							0
						)");
					}

					$sql .= "INSERT INTO compra_directa (
						codmp,
						num_proveedor,
						num_cia,
						numero_fact,
						fecha_mov,
						cantidad,
						kilos,
						precio_unit,
						aplica_gasto,
						total,
						fecha_pago,
						precio_unidad
					) VALUES (
						{$codmp},
						" . ($_REQUEST['cd_num_pro'][$i] > 0 ? $_REQUEST['cd_num_pro'][$i] : 'NULL') . ",
						{$_REQUEST['num_cia']},
						'{$_REQUEST['cd_num_fact'][$i]}',
						'{$_REQUEST['fecha']}',
						" . get_val($_REQUEST['cd_cantidad'][$i]) . ",
						" . get_val($_REQUEST['cd_kilos'][$i]) . ",
						" . get_val($_REQUEST['cd_precio'][$i]) . ",
						" . (in_array($i, $_REQUEST['cd_aplica_gasto']) ? 'TRUE' : 'FALSE') . ",
						" . get_val($_REQUEST['cd_importe'][$i]) . ",
						'{$_REQUEST['fecha']}'::DATE + INTERVAL '1 DAY',
						" . (get_val($_REQUEST['cd_importe'][$i]) / get_val($_REQUEST['cd_cantidad'][$i])) . "
					);";

					$sql .= "INSERT INTO mov_inv_real (
						num_cia,
						codmp,
						fecha,
						cod_turno,
						tipo_mov,
						cantidad,
						precio,
						total_mov,
						precio_unidad,
						descripcion,
						num_proveedor,
						num_fact
					) VALUES (
						{$_REQUEST['num_cia']},
						{$codmp},
						'{$_REQUEST['fecha']}',
						11,
						FALSE,
						" . get_val($_REQUEST['cd_cantidad'][$i]) . ",
						" . get_val($_REQUEST['cd_precio'][$i]) . ",
						" . get_val($_REQUEST['cd_importe'][$i]) . ",
						" . (get_val($_REQUEST['cd_importe'][$i]) / get_val($_REQUEST['cd_cantidad'][$i])) . ",
						'" . (in_array($i, $_REQUEST['cd_aplica_gasto']) ? 'COMPRA DIRECTA' : "REMISION NO. {$_REQUEST['cd_num_fact'][$i]}") . "',
						" . (in_array($i, $_REQUEST['cd_aplica_gasto']) ? 'NULL' : $_REQUEST['cd_num_pro'][$i]) . ",
						" . (in_array($i, $_REQUEST['cd_aplica_gasto']) ? 'NULL' : "'{$_REQUEST['cd_num_fact'][$i]}'") . "
					);";

					if (in_array($i, $_REQUEST['cd_aplica_gasto']))
					{
						$sql .= "INSERT INTO movimiento_gastos (
							codgastos,
							num_cia,
							fecha,
							importe,
							captura,
							concepto
						) VALUES (
							23,
							{$_REQUEST['num_cia']},
							'{$_REQUEST['fecha']}',
							" . get_val($_REQUEST['cd_importe'][$i]) . ",
							FALSE,
							'COMPRA DIRECTA'
						);";
					}
				}
			}

			foreach ($_REQUEST['v_codmp'] as $i => $codmp)
			{
				if (get_val($_REQUEST['v_cantidad'][$i]) > 0)
				{
					if ( ! $db->query("SELECT idinv FROM historico_inventario WHERE num_cia = {$_REQUEST['num_cia']} AND codmp = {$codmp} LIMIT 1"))
					{

						$db->query("INSERT INTO
							historico_inventario (
								num_cia,
								codmp,
								fecha,
								existencia,
								precio_unidad
							) VALUES (
								{$_REQUEST['num_cia']},
								{$codmp},
								'" . date('d/m/Y', mktime(0, 0, 0, $mes, 0, $anio)) . "',
								0,
								0
							)");

						$db->query("INSERT INTO
							inventario_real (
								num_cia,
								codmp,
								existencia,
								precio_unidad
							) VALUES (
								{$_REQUEST['num_cia']},
								{$codmp},
								0,
								0
							)");
					}

					$sql .= "INSERT INTO
						hoja_diaria_rost (
							num_cia,
							codmp,
							unidades,
							precio_unitario,
							precio_total,
							fecha
						) VALUES (
							{$_REQUEST['num_cia']},
							{$codmp},
							" . get_val($_REQUEST['v_cantidad'][$i]) . ",
							" . get_val($_REQUEST['v_precio'][$i]) . ",
							" . get_val($_REQUEST['v_importe'][$i]) . ",
							'{$_REQUEST['fecha']}'
						);";

					$sql .= "INSERT INTO
						mov_inv_real (
							num_cia,
							codmp,
							fecha,
							cod_turno,
							tipo_mov,
							cantidad,
							precio,
							total_mov,
							precio_unidad,
							descripcion
						) VALUES (
							{$_REQUEST['num_cia']},
							{$codmp},
							'{$_REQUEST['fecha']}',
							11,
							TRUE,
							" . get_val($_REQUEST['v_cantidad'][$i]) . ",
							" . get_val($_REQUEST['v_precio'][$i]) . ",
							" . get_val($_REQUEST['v_importe'][$i]) . ",
							" . get_val($_REQUEST['v_precio'][$i]) . ",
							'CONSUMO DEL DIA'
						);";
				}
			}

			$sql .= "INSERT INTO
				hoja_diaria_ros_otros (
					num_cia,
					importe,
					fecha
				) VALUES (
					{$_REQUEST['num_cia']},
					" . get_val($_REQUEST['v_otros']) . ",
					'{$_REQUEST['fecha']}'
				);";

			foreach ($_REQUEST['g_codgastos'] as $i => $codgastos)
			{
				if ($codgastos > 0)
				{
					$sql .= "INSERT INTO
						movimiento_gastos (
							codgastos,
							num_cia,
							fecha,
							importe,
							captura,
							concepto
						) VALUES (
							{$codgastos},
							{$_REQUEST['num_cia']},
							'{$_REQUEST['fecha']}',
							" . get_val($_REQUEST['g_importe'][$i]) . ",
							FALSE,
							'" . utf8_decode($_REQUEST['g_concepto'][$i]) . "'
						);";

					if ($codgastos == 90)
					{
						if ( ! $db->query("SELECT idinv FROM historico_inventario WHERE num_cia = {$_REQUEST['num_cia']} AND codmp = 90 LIMIT 1"))
						{

							$db->query("INSERT INTO
								historico_inventario (
									num_cia,
									codmp,
									fecha,
									existencia,
									precio_unidad
								) VALUES (
									{$_REQUEST['num_cia']},
									90,
									'" . date('d/m/Y', mktime(0, 0, 0, $mes, 0, $anio)) . "',
									0,
									0
								)");

							$db->query("INSERT INTO
								inventario_real (
									num_cia,
									codmp,
									existencia,
									precio_unidad
								) VALUES (
									{$_REQUEST['num_cia']},
									90,
									0,
									0
								)");
						}

						$sql .= "INSERT INTO
							mov_inv_real (
								num_cia,
								codmp,
								fecha,
								tipo_mov,
								cantidad,
								precio,
								total_mov,
								precio_unidad,
								descripcion
							) VALUES (
								{$_REQUEST['num_cia']},
								90,
								'{$_REQUEST['fecha']}',
								FALSE,
								" . get_val($_REQUEST['g_cantidad'][$i]) . ",
								" . (get_val($_REQUEST['g_importe'][$i]) / get_val($_REQUEST['g_cantidad'][$i])) . ",
								" . get_val($_REQUEST['g_importe'][$i]) . ",
								" . (get_val($_REQUEST['g_importe'][$i]) / get_val($_REQUEST['g_cantidad'][$i])) . ",
								'COMPRA DIRECTA GAS'
							);";
					}
				}
			}

			if (isset($_REQUEST['p_id_emp']))
			{
				foreach ($_REQUEST['p_id_emp'] as $i => $id_emp)
				{
					if (get_val($_REQUEST['p_prestamo'][$i]) > 0)
					{
						if ($id = $db->query("SELECT id FROM prestamos WHERE id_empleado = {$id_emp} AND tipo_mov = FALSE AND pagado = FALSE ORDER BY fecha DESC LIMIT 1"))
						{
							$sql .= "UPDATE prestamos SET importe = importe + " . get_val($_REQUEST['p_prestamo'][$i]) . "WHERE id = {$id[0]['id']};";
						}
						else
						{
							$sql .= "INSERT INTO
								prestamos (
									num_cia,
									fecha,
									importe,
									tipo_mov,
									pagado,
									id_empleado
								) VALUES (
									{$_REQUEST['num_cia']},
									'{$_REQUEST['fecha']}',
									" . get_val($_REQUEST['p_prestamo'][$i]) . ",
									FALSE,
									FALSE,
									{$id_emp}
								);";
						}
					}
					else if (get_val($_REQUEST['p_abono'][$i]) > 0)
					{
						$sql .= "INSERT INTO
							prestamos (
								num_cia,
								fecha,
								importe,
								tipo_mov,
								pagado,
								id_empleado
							) VALUES (
								{$_REQUEST['num_cia']},
								'{$_REQUEST['fecha']}',
								" . get_val($_REQUEST['p_abono'][$i]) . ",
								TRUE,
								FALSE,
								{$id_emp}
							);";
					}
				}
			}

			$sql .= "INSERT INTO
				total_companias (
					num_cia,
					fecha,
					venta,
					gastos,
					efectivo
				) VALUES (
					{$_REQUEST['num_cia']},
					'{$_REQUEST['fecha']}',
					" . (get_val($_REQUEST['v_total']) + get_val($_REQUEST['p_total_abonos'])) . ",
					" . (get_val($_REQUEST['g_gastos']) + get_val($_REQUEST['p_total_prestamos'])) . ",
					" . (get_val($_REQUEST['v_total']) + get_val($_REQUEST['p_total_abonos']) - get_val($_REQUEST['g_gastos']) - get_val($_REQUEST['p_total_prestamos'])) . "
				);";

			$db->query($sql);

			include('includes/auxinv.inc.php');

			$sql = ActualizarInventario($_REQUEST['num_cia'], $anio, $mes, NULL, TRUE, FALSE, FALSE, FALSE);

			$db->query($sql);

			$sql = "SELECT
				ct.id,
				SUM(
					CASE
						WHEN p.tipo_mov = FALSE THEN
							importe
						ELSE
							-importe
					END
				)
					AS saldo
			FROM
				prestamos p
				LEFT JOIN catalogo_trabajadores ct
					ON (ct.id = p.id_empleado)
			WHERE
				ct.num_cia = {$_REQUEST['num_cia']}
				AND p.pagado = FALSE
			GROUP BY
				ct.id
			HAVING
				SUM(
					CASE
						WHEN p.tipo_mov = FALSE THEN
							importe
						ELSE
							-importe
					END
				) = 0";

			$result = $db->query($sql);

			if ($result)
			{
				foreach ($result as $row)
				{
					$db->query("UPDATE prestamos SET pagado = TRUE WHERE id_empleado = {$row['id']} AND pagado = FALSE");
				}
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ros/RosticeriasProcesoSecuencial.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
