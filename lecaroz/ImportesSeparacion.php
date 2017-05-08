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
		case 'actualizar':
			$status = array(
				'insertados'   => 0,
				'actualizados' => 0,
				'borrados'     => 0
			);
			
			$sql = '';
			
			foreach ($_REQUEST['num_cia'] as $i => $num_cia) {
				$datos = $db->query('
					SELECT
						idseparacion,
						importe,
						porcentaje
					FROM
						cometra_separacion
					WHERE
						num_cia = ' . $num_cia . '
						AND tsbaja IS NULL
				');
				
				if (!$datos && (get_val($_REQUEST['importe'][$i]) > 0 || get_val($_REQUEST['porcentaje'][$i]) > 0)) {
					$sql .= '
						INSERT INTO
							cometra_separacion
								(
									num_cia,
									importe,
									porcentaje,
									tsalta,
									idalta
								)
							VALUES
								(
									' . $num_cia . ',
									' . get_val($_REQUEST['importe'][$i]) . ',
									' . get_val($_REQUEST['porcentaje'][$i]) . ',
									NOW(),
									' . $_SESSION['iduser'] . '
								)
					' . ";\n";
					
					$status['insertados']++;
				}
				else if ($datos && (get_val($_REQUEST['importe'][$i]) == 0 && get_val($_REQUEST['porcentaje'][$i]) == 0)) {
					$sql .= '
						UPDATE
							cometra_separacion
						SET
							tsbaja = NOW(),
							idbaja = ' . $_SESSION['iduser'] . '
						WHERE
							idseparacion = ' . $datos[0]['idseparacion'] . '
					' . ";\n";
					
					$status['borrados']++;
				}
				else if ($datos && (get_val($_REQUEST['importe'][$i]) != $datos[0]['importe'] || get_val($_REQUEST['porcentaje'][$i]) != $datos[0]['porcentaje'])) {
					$sql .= '
						UPDATE
							cometra_separacion
						SET
							tsbaja = NOW(),
							idbaja = ' . $_SESSION['iduser'] . '
						WHERE
							idseparacion = ' . $datos[0]['idseparacion'] . '
					' . ";\n";
					
					$sql .= '
						INSERT INTO
							cometra_separacion
								(
									num_cia,
									importe,
									porcentaje,
									tsalta,
									idalta
								)
							VALUES
								(
									' . $num_cia . ',
									' . get_val($_REQUEST['importe'][$i]) . ',
									' . get_val($_REQUEST['porcentaje'][$i]) . ',
									NOW(),
									' . $_SESSION['iduser'] . '
								)
					' . ";\n";
					
					$status['actualizados']++;
				}
			}
			
			if ($sql != '') {
				$db->query($sql);
			}
			
			echo json_encode($status);
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/cometra/ImportesSeparacion.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$fecha1 = date('j') > 6 ? date('01/m/Y') : date('d/m/Y', mktime(0, 0, 0, date('n') - 1, 1));
$fecha2 = date('j') > 6 ? date('d/m/Y') : date('d/m/Y', mktime(0, 0, 0, date('n'), 0));

$sql = '
	SELECT
		num_cia,
		nombre_corto
			AS nombre_cia,
		COALESCE(
			(
				SELECT
					importe
				FROM
					cometra_separacion
				WHERE
					num_cia = cc.num_cia
					AND tsbaja IS NULL
			), 0
		)
			AS importe,
		COALESCE((
			SELECT
				porcentaje
			FROM
				cometra_separacion
			WHERE
				num_cia = cc.num_cia
				AND tsbaja IS NULL
		), 0)
			AS porcentaje,
		COALESCE((
			SELECT
				AVG(efectivo)
			FROM
				total_panaderias
			WHERE
				num_cia = cc.num_cia
				AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
				AND efe = TRUE
				AND exp = TRUE
				AND gas = TRUE
				AND pro = TRUE
				AND pas = TRUE
		), (
			SELECT
				AVG(efectivo)
			FROM
				total_companias
			WHERE
				num_cia = cc.num_cia
				AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
		), 0)
			AS promedio,
		CASE
			WHEN COALESCE((
				SELECT
					AVG(efectivo)
				FROM
					total_panaderias
				WHERE
					num_cia = cc.num_cia
					AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
					AND efe = TRUE
					AND exp = TRUE
					AND gas = TRUE
					AND pro = TRUE
					AND pas = TRUE
			), (
				SELECT
					AVG(efectivo)
				FROM
					total_companias
				WHERE
					num_cia = cc.num_cia
					AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
			), 0) > 0 THEN
				FLOOR(((
					SELECT
						SUM(total)
					FROM
						pasivo_proveedores
					WHERE
						num_cia = cc.num_cia
						AND total > 0
				) - (
					SELECT
						SUM(saldo_libros)
					FROM
						saldos
					WHERE
						num_cia = cc.num_cia
				)) / COALESCE((
						SELECT
							AVG(efectivo)
						FROM
							total_panaderias
						WHERE
							num_cia = cc.num_cia
							AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
							AND efe = TRUE
							AND exp = TRUE
							AND gas = TRUE
							AND pro = TRUE
							AND pas = TRUE
					), (
						SELECT
							AVG(efectivo)
						FROM
							total_companias
						WHERE
							num_cia = cc.num_cia
							AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
					), 0))
			ELSE
				0
		END
			AS dias
	FROM
		catalogo_companias cc
	WHERE
		num_cia <= 800
	ORDER BY
		num_cia
';

$result = $db->query($sql);

if ($result) {
	$row_color = FALSE;
	
	foreach($result as $rec) {
		$tpl->newBlock('row');
		
		$tpl->assign('row_color', $row_color ? 'on' : 'off');
		
		$row_color = !$row_color;
		
		$tpl->assign('num_cia', $rec['num_cia']);
		$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
		$tpl->assign('importe', $rec['importe'] > 0 ? number_format($rec['importe'], 2) : '');
		$tpl->assign('porcentaje', $rec['porcentaje'] > 0 ? number_format($rec['porcentaje'], 2) : '');
		$tpl->assign('promedio', $rec['promedio'] != 0 ? number_format($rec['promedio'], 2) : '');
		$tpl->assign('dias', $rec['dias'] != 0 ? $rec['dias'] : '&nbsp;');
	}
}

$tpl->printToScreen();
?>
