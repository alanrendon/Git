<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

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
	1  => 'ENERO',
	2  => 'FEBRERO',
	3  => 'MARZO',
	4  => 'ABRIL',
	5  => 'MAYO',
	6  => 'JUNIO',
	7  => 'JULIO',
	8  => 'AGOSTO',
	9  => 'SEPTIEMBRE',
	10 => 'OCTUBRE',
	11 => 'NOVIEMBRE',
	12 => 'DICIEMBRE'
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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'reporte':
			$condiciones[] = 'codmp IN (' . implode(', ', $_REQUEST['codmp']) . ')';
			
			/*
			@ Intervalo de periodos
			*/
			if (isset($_REQUEST['anios']) && trim($_REQUEST['anios']) != '') {
				$anios = array();
				
				$pieces = explode(',', $_REQUEST['anios']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$anios[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$anios[] = $piece;
					}
				}
				
				if (count($anios) > 0) {
					$periodos = array();
					
					foreach ($anios as $a) {
						$periodos[] = 'fecha_mov BETWEEN \'01/01/' . $a . '\' AND \'31/12/' . $a . '\'';
					}
					$condiciones[] = '(' . implode(' OR ', $periodos) . ')';
				}
			}
			
			/*
			@ Intervalo de compañías
			*/
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
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if (isset($_REQUEST['num_pro']) && $_REQUEST['num_pro'] > 0) {
				$condiciones[] = 'num_proveedor = ' . $_REQUEST['num_pro'];
			}
			
			$sql = '
				SELECT
					num_cia,
					nombre_corto
						AS
							nombre_cia,
					codmp,
					EXTRACT(year from fecha_mov)
						AS
							anio,
					EXTRACT(month from fecha_mov)
						AS
							mes,
					precio
				FROM
						fact_rosticeria fr
					LEFT JOIN
						catalogo_companias cc
							USING
								(
									num_cia
								)
				WHERE
					(
						num_cia,
						codmp,
						fecha_mov
					)
						IN
							(
								SELECT
									num_cia,
									codmp,
									fecha
								FROM
									(
										SELECT
											num_cia,
											codmp,
											EXTRACT(year from fecha_mov)
												AS
													anio,
											EXTRACT(month from fecha_mov)
												AS
													mes,
											MAX(fecha_mov)
												AS
													fecha
										FROM
											fact_rosticeria
										WHERE
											' . implode(' AND ', $condiciones) . '
										GROUP BY
											num_cia,
											codmp,
											anio,
											mes
									)
										fechas
							)
				ORDER BY
					codmp,
					anio,
					num_cia,
					mes
			';
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ros/PrecioPolloAnualReporte.tpl');
			$tpl->prepare();
			
			if ($result) {
				$mps = array(
					160 => 'POLLOS NORMALES',
					600 => 'POLLOS CHICOS',
					700 => 'POLLOS GRANDES'
				);
				
				$codmp = NULL;
				$anio = NULL;
				
				foreach ($result as $rec) {
					if ($codmp != $rec['codmp'] || $anio != $rec['anio']) {
						if ($codmp != NULL || $anio != NULL) {
							$tpl->assign('reporte.salto', '<br style="page-break-after:always;" />');
						}
						
						$codmp = $rec['codmp'];
						$anio = $rec['anio'];
						
						$tpl->newBlock('reporte');
						$tpl->assign('anio', $anio);
						$tpl->assign('codmp', $codmp);
						$tpl->assign('nombre_mp', $mps[$codmp]);
						
						$num_cia = NULL;
					}
					
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('row');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $rec['nombre_cia']);
					}
					
					$tpl->assign($rec['mes'], number_format($rec['precio'], 2, '.', ','));
				}
			}
			
			$tpl->printToScreen();
			
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ros/PrecioPolloAnual.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$sql = '
	SELECT
		idadministrador
			AS
				id,
		nombre_administrador
			AS
				nombre
	FROM
		catalogo_administradores
	ORDER BY
		nombre
';
	$admins = $db->query($sql);

foreach ($admins as $a) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $a['id']);
	$tpl->assign('nombre', $a['nombre']);
}

$tpl->printToScreen();
?>
