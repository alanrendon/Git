<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'consultar':
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
					codmp = 148
				GROUP BY
					num_pro,
					nombre
				ORDER BY
					num_pro
			';
			
			$result = $db->query($sql);
			
			$proveedores = array();
			
			foreach ($result as $rec) {
				$proveedores[$rec['num_pro']] = $rec['nombre'];
			}
			
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));
			
			$dias = date('j', $_REQUEST['anio'] == date('Y') && $_REQUEST['mes'] == date('n') ? time() : mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));
			
			$sql = '
				SELECT
					EXTRACT(day FROM fecha)
						AS dia,
					num_proveedor
						AS num_pro,
					precio,
					observaciones
				FROM
					huevo_precios
				WHERE
					fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
				ORDER BY
					dia,
					num_pro
			';
			
			$result = $db->query($sql);
			
			$precios = array();
			
			if ($result) {
				foreach ($result as $rec) {
					$precios[$rec['dia']]['precios'][$rec['num_pro']] = $rec['precio'];
					$precios[$rec['dia']]['observaciones'] = utf8_decode($rec['observaciones']);
				}
			}
			
			$tpl = new TemplatePower('plantillas/fac/HuevoPreciosResultado.tpl');
			$tpl->prepare();
			
			foreach ($proveedores as $num_pro => $nombre_pro) {
				$tpl->newBlock('th');
				$tpl->assign('num_pro', $num_pro);
				$tpl->assign('nombre_pro', $nombre_pro);
			}
			
			$row_color = FALSE;
			
			for ($dia = 1; $dia <= $dias; $dia++) {
				$tpl->newBlock('row');
				
				$tpl->assign('row_color', $row_color ? 'on' : 'off');
				
				$row_color = !$row_color;
				
				$tpl->assign('dia', $dia);
				
				$tpl->assign('observaciones', isset($precios[$dia]) ? $precios[$dia]['observaciones'] : '');
				
				foreach ($proveedores as $num_pro => $nombre_pro) {
					$tpl->newBlock('td');
					$tpl->assign('dia', $dia);
					$tpl->assign('num_pro', $num_pro);
					$tpl->assign('precio', isset($precios[$dia]['precios'][$num_pro]) && $precios[$dia]['precios'][$num_pro] > 0 ? number_format($precios[$dia]['precios'][$num_pro], 2, '.', ',') : '');
				}
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'actualizar':
			$sql = '';
			
			foreach ($_REQUEST['dia'] as $dia) {
				$fecha = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], $dia, $_REQUEST['anio']));
				
				foreach ($_REQUEST['num_pro' . $dia] as $i => $num_pro) {
					if ($id = $db->query('
						SELECT
							id
						FROM
							huevo_precios
						WHERE
							fecha = \'' . $fecha . '\'
							AND num_proveedor = ' . $num_pro . '
					')) {
						$sql .= '
							UPDATE
								huevo_precios
							SET
								precio = ' . get_val($_REQUEST['precio' . $dia][$i]) . ',
								observaciones = \'' . utf8_decode($_REQUEST['observaciones' . $dia]) . '\',
								idmod = ' . $_SESSION['iduser'] . ',
								tsmod = now()
							WHERE
								id = ' . $id[0]['id'] . '
						' . ";\n";
					}
					else {
						$sql .= '
							INSERT INTO
								huevo_precios
									(
										fecha,
										num_proveedor,
										precio,
										observaciones,
										idins,
										tsins,
										idmod,
										tsmod
									)
								VALUES
									(
										\'' . $fecha . '\',
										' . $num_pro . ',
										' . get_val($_REQUEST['precio' . $dia][$i]) . ',
										\'' . utf8_decode($_REQUEST['observaciones' . $dia]) . '\',
										' . $_SESSION['iduser'] . ',
										now(),
										' . $_SESSION['iduser'] . ',
										now()
									)
						' . ";\n";
					}
				}
			}
			
			$db->query($sql);
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/HuevoPrecios.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$sql = '
	SELECT
		EXTRACT(year FROM MAX(fecha))
			AS anio,
		EXTRACT(month FROM MAX(fecha))
			AS mes
	FROM
		huevo_precios
';

$result = $db->query($sql);

$tpl->assign('anio', $result[0]['anio'] > 0 ? $result[0]['anio'] : date('Y'));
$tpl->assign($result[0]['mes'] > 0 ? $result[0]['mes'] : date('n'), ' selected');

$tpl->printToScreen();
?>
