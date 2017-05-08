<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');
include('includes/auxinv.inc.php');

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

function toInt($value) {
	return intval($value);
}

$_meses = array(
	1  => 'ENE',
	2  => 'FEB',
	3  => 'MAR',
	4  => 'ABR',
	5  => 'MAY',
	6  => 'JUN',
	7  => 'JUL',
	8  => 'AGO',
	9  => 'SEP',
	10 => 'OCT',
	11 => 'NOV',
	12 => 'DIC'
);

$_dias = array(
	0 => 'D',
	1 => 'L',
	2 => 'M',
	3 => 'M',
	4 => 'J',
	5 => 'V',
	6 => 'S'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die('MODIFICANDO');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'obtenerCia':
			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias cc
					LEFT JOIN catalogo_operadoras co
						USING (idoperadora)
				WHERE
					num_cia <= 300
					AND num_cia = ' . $_REQUEST['num_cia'] . '
			';
			
			if (!in_array($_SESSION['iduser'], array(1, 4, 8, 14, 18, 39))) {
				$sql .= 'AND co.iduser = ' . $_SESSION['iduser'];
			}
			
			$result = $db->query($sql);
			
			if ($result) {
				echo $result[0]['nombre_corto'];
			}
		break;
		
		case 'obtenerMP':
			$sql = '
				SELECT
					nombre
				FROM
					inventario_real
					LEFT JOIN catalogo_mat_primas
						USING (codmp)
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
					AND codmp IN (179, 291)
					AND codmp = ' . $_REQUEST['codmp'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$sql = '
					SELECT
						num_proveedor
							AS num_pro,
						nombre
					FROM
						catalogo_productos_proveedor cpp
						LEFT JOIN catalogo_proveedores cp
							USING (num_proveedor)
					WHERE
						codmp = ' . $_REQUEST['codmp'] . '
					GROUP BY
						num_pro,
						nombre
					ORDER BY
						nombre
				';
				
				$proveedores = $db->query($sql);
				
				if ($proveedores) {
					$options = array();
					
					foreach ($proveedores as $pro) {
						$options[] = array(
							'value' => intval($pro['num_pro']),
							'text'  => $pro['nombre']
						);
					}
					
					$data = array(
						'status' => 1,
						'nombre' => $result[0]['nombre'],
						'options' => $options
					);
					
					echo json_encode($data);
				}
				else {
					echo json_encode(array('status' => -2));
				}
			}
			else {
				echo json_encode(array('status' => -1));
			}
		break;
		
		case 'validarFecha':
			$sql = '
				SELECT
					CASE
						WHEN \'' . $_REQUEST['fecha'] . '\'::date < (MAX(fecha) + INTERVAL \'1 MONTH\')::date THEN
							-1
						WHEN \'' . $_REQUEST['fecha'] . '\'::date > (MAX(fecha) + INTERVAL \'2 MONTH\' - INTERVAL \'1 DAY\')::date THEN
							-2
						WHEN MAX(fecha) IS NULL THEN
							-3
						ELSE
							0
					END
						AS status
				FROM
					balances_pan
				-- WHERE
					-- num_cia = ' . $_REQUEST['num_cia'] . '
			';
			
			$result = $db->query($sql);
			
			echo json_encode(array('status' => intval($result[0]['status'])));
		break;
		
		case 'validarRemision':
			$sql = '
				SELECT
					fr.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					fr.num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					num_rem,
					num_fact,
					fecha,
					SUM(total)
						AS total
				FROM
					fruta_remisiones fr
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					fr.num_proveedor = ' . $_REQUEST['num_pro'] . '
					AND fr.num_rem = \'' . strtoupper($_REQUEST['num_rem']) . '\'
				GROUP BY
					fr.num_cia,
					nombre_cia,
					num_pro,
					nombre_pro,
					num_rem,
					num_fact,
					fecha
				ORDER BY
					fecha DESC
				LIMIT
					1
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$result[0]['nombre_cia'] = utf8_encode($result[0]['nombre_cia']);
				$result[0]['nombre_pro'] = utf8_encode($result[0]['nombre_pro']);
				$result[0]['total'] = floatval($result[0]['total']);
				
				echo json_encode($result[0]);
			}
		break;
		
		case 'ingresar':
			foreach ($_REQUEST['num_cia'] as $i => $num_cia) {
				if ($num_cia > 0
					&& $_REQUEST['fecha'][$i] != ''
					&& $_REQUEST['num_rem'][$i] != ''
					&& $_REQUEST['codmp'][$i] > 0
					&& get_val($_REQUEST['cantidad'][$i]) > 0
					&& get_val($_REQUEST['precio'][$i]) > 0
					&& get_val($_REQUEST['total'][$i]) > 0) {
					$sql = '
						INSERT INTO
							fruta_remisiones
								(
									num_cia,
									fecha,
									num_proveedor,
									num_rem,
									codmp,
									cantidad,
									precio,
									total,
									idins,
									tsins
								)
							VALUES
								(
									' . $num_cia . ',
									\'' . $_REQUEST['fecha'][$i] . '\',
									' . $_REQUEST['num_pro'][$i] . ',
									\'' . $_REQUEST['num_rem'][$i] . '\',
									' . $_REQUEST['codmp'][$i] . ',
									' . get_val($_REQUEST['cantidad'][$i]) . ',
									' . get_val($_REQUEST['precio'][$i]) . ',
									' . get_val($_REQUEST['total'][$i]) . ',
									' . $_SESSION['iduser'] . ',
									now()
								)
					' . ";\n";
					
					$cantidad = get_val($_REQUEST['cantidad'][$i]);
					$precio = get_val($_REQUEST['precio'][$i]);
					$total = get_val($_REQUEST['total'][$i]);
					$precio_unidad = $total / $cantidad;
					
					$sql .= '
						INSERT INTO
							mov_inv_real
								(
									num_cia,
									codmp,
									fecha,
									tipo_mov,
									cantidad,
									precio,
									total_mov,
									precio_unidad,
									descripcion,
									num_proveedor,
									num_fact,
									tsins
								)
							VALUES
								(
									' . $num_cia . ',
									' . $_REQUEST['codmp'][$i] . ',
									\'' . $_REQUEST['fecha'][$i] . '\',
									FALSE,
									' . $cantidad . ',
									' . get_val($_REQUEST['precio'][$i]) . ',
									' . $total . ',
									' . $precio_unidad . ',
									\'REMISION NO. ' . $_REQUEST['num_rem'][$i] . '\',
									' . $_REQUEST['num_pro'][$i] . ',
									\'' . $_REQUEST['num_rem'][$i] . '\',
									now()
								)
					' . ";\n";
					
					$db->query($sql);
					
					list($dia, $mes, $anio) = array_map('toInt', explode('/', $_REQUEST['fecha'][$i]));
					
					ActualizarInventario($num_cia, $anio, $mes, $_REQUEST['codmp'][$i], TRUE, FALSE, TRUE, FALSE);
				}
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/FrutaCapturaRemisiones.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
