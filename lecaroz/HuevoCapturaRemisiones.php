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
		
		case 'validarFecha':
			$sql = '
				SELECT
					CASE
						WHEN \'' . $_REQUEST['fecha'] . '\'::date < (MAX(fecha) + INTERVAL \'1 MONTH\')::date THEN
							-1
						WHEN \'' . $_REQUEST['fecha'] . '\'::date > (MAX(fecha) + INTERVAL \'2 MONTH\' - INTERVAL \'1 DAY\')::date THEN
							-2
						ELSE
							0
					END
						AS status
				FROM
					balances_pan
				--WHERE
				--	num_cia = ' . $_REQUEST['num_cia'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result[0]['status'] == 0) {
				$sql = '
					SELECT
						num_proveedor
							AS num_pro,
						nombre
							AS nombre_pro,
						precio
					FROM
						huevo_precios
						LEFT JOIN catalogo_proveedores cp
							USING (num_proveedor)
					WHERE
						fecha = \'' . $_REQUEST['fecha'] . '\'
						AND precio > 0
					ORDER BY
						precio
				';
				
				$result = $db->query($sql);
				
				if ($result) {
					$data = array();
					
					foreach ($result as $rec) {
						$data[] = array(
							'value' => json_encode(array(
								'num_pro' => intval($rec['num_pro']),
								'precio'  => floatval($rec['precio'])
							)),
							'text' => utf8_encode('[' . str_pad($rec['num_pro'], 4, '0', STR_PAD_LEFT) . '] ' . $rec['nombre_pro'] . ' [' . number_format($rec['precio'], 2, '.', ',') . ']')
						);
					}
					
					echo json_encode(array(
						'status' => 0,
						'options' => $data
					));
				}
				else {
					echo json_encode(array(
						'status' => -3
					));
				}
			}
			else {
				echo json_encode(array(
					'status' => intval($result[0]['status'])
				));
			}
		break;
		
		case 'validarFechaPrecio':
			list($dia_rem, $mes_rem, $anio_rem) = array_map('toInt', explode('/', $_REQUEST['fecha']));
			list($dia_pre, $mes_pre, $anio_pre) = array_map('toInt', explode('/', $_REQUEST['fecha_precio']));
			
			$tsrem = mktime(0, 0, 0, $mes_rem, $dia_rem, $anio_rem);
			$tspre = mktime(0, 0, 0, $mes_pre, $dia_pre, $anio_pre);
			
			if ($tspre < $tsrem - 86400 * 5) {
				echo json_encode(array('status' => -1));
			}
			else if ($tspre > $tsrem) {
				echo json_encode(array('status' => -2));
			}
			else {
				$sql = '
					SELECT
						num_proveedor
							AS num_pro,
						nombre
							AS nombre_pro,
						precio
					FROM
						huevo_precios
						LEFT JOIN catalogo_proveedores cp
							USING (num_proveedor)
					WHERE
						fecha = \'' . $_REQUEST['fecha_precio'] . '\'
						AND precio > 0
					ORDER BY
						precio
				';
				
				$result = $db->query($sql);
				
				if ($result) {
					$data = array();
					
					foreach ($result as $rec) {
						$data[] = array(
							'value' => json_encode(array(
								'num_pro' => intval($rec['num_pro']),
								'precio'  => floatval($rec['precio'])
							)),
							'text' => utf8_encode('[' . str_pad($rec['num_pro'], 4, '0', STR_PAD_LEFT) . '] ' . $rec['nombre_pro'] . ' [' . number_format($rec['precio'], 2, '.', ',') . ']')
						);
					}
					
					echo json_encode(array(
						'status' => 1,
						'options' => $data
					));
				}
				else {
					echo json_encode(array('status' => -3));
				}
			}
		break;
		
		case 'validarRemision':
			$sql = '
				SELECT
					hr.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					hr.num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					num_rem,
					num_fact,
					fecha,
					cajas,
					peso_bruto,
					tara,
					peso_neto,
					precio,
					total
				FROM
					huevo_remisiones hr
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					hr.num_proveedor = ' . $_REQUEST['num_pro'] . '
					AND hr.num_rem = \'' . strtoupper($_REQUEST['num_rem']) . '\'
				ORDER BY
					fecha DESC
				LIMIT
					1
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$result[0]['nombre_cia'] = utf8_encode($result[0]['nombre_cia']);
				$result[0]['nombre_pro'] = utf8_encode($result[0]['nombre_pro']);
				$result[0]['cajas'] = intval($result[0]['cajas']);
				$result[0]['peso_bruto'] = floatval($result[0]['peso_bruto']);
				$result[0]['tara'] = floatval($result[0]['tara']);
				$result[0]['peso_neto'] = floatval($result[0]['peso_neto']);
				$result[0]['precio'] = floatval($result[0]['precio']);
				$result[0]['total'] = floatval($result[0]['total']);
				
				echo json_encode($result[0]);
			}
		break;
		
		case 'ingresar':
			$pro = json_decode($_REQUEST['num_pro']);
			
			$sql = '
				INSERT INTO
					huevo_remisiones
						(
							num_cia,
							fecha,
							num_proveedor,
							num_rem,
							cajas,
							peso_bruto_remision,
							peso_bruto,
							tara,
							peso_neto,
							precio,
							total,
							idins,
							tsins
						)
					VALUES
						(
							' . $_REQUEST['num_cia'] . ',
							\'' . $_REQUEST['fecha'] . '\',
							' . $pro->num_pro . ',
							\'' . $_REQUEST['num_rem'] . '\',
							' . get_val($_REQUEST['cajas']) . ',
							' . get_val($_REQUEST['peso_bruto_remision']) . ',
							' . get_val($_REQUEST['peso_bruto']) . ',
							' . get_val($_REQUEST['tara']) . ',
							' . get_val($_REQUEST['peso_neto']) . ',
							' . get_val($_REQUEST['precio']) . ',
							' . get_val($_REQUEST['total']) . ',
							' . $_SESSION['iduser'] . ',
							now()
						)
			' . ";\n";
			
			foreach ($_REQUEST['pesada'] as $pesada) {
				$pesada = get_val($pesada);
				
				if ($pesada > 0) {
					$sql .= '
						INSERT INTO
							huevo_pesadas
								(
									num_cia,
									fecha,
									num_proveedor,
									num_rem,
									pesada,
									idins,
									tsins
								)
							VALUES
								(
									' . $_REQUEST['num_cia'] . ',
									\'' . $_REQUEST['fecha'] . '\',
									' . $pro->num_pro . ',
									\'' . $_REQUEST['num_rem'] . '\',
									' . $pesada . ',
									' . $_SESSION['iduser'] . ',
									now()
								)
					' . ";\n";
				}
			}
			
			$cantidad = get_val($_REQUEST['cajas']) * 360;
			$precio = get_val($_REQUEST['precio']);
			$total = get_val($_REQUEST['total']);
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
							' . $_REQUEST['num_cia'] . ',
							148,
							\'' . $_REQUEST['fecha'] . '\',
							FALSE,
							' . $cantidad . ',
							' . get_val($_REQUEST['precio']) . ',
							' . $total . ',
							' . $precio_unidad . ',
							\'REMISION NO. ' . $_REQUEST['num_rem'] . '\',
							' . $pro->num_pro . ',
							\'' . $_REQUEST['num_rem'] . '\',
							now()
						)
			' . ";\n";
			
			$db->query($sql);
			
			list($dia, $mes, $anio) = array_map('toInt', explode('/', $_REQUEST['fecha']));
			
			ActualizarInventario($_REQUEST['num_cia'], $anio, $mes, 148, TRUE, FALSE, TRUE, FALSE);
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/HuevoCapturaRemisiones.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
