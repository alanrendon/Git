<?php

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

function preparar_imagen($source, $destination, $target_width = 128, $target_height = 128, $quality = 100)
{
	if (file_exists($source))
	{
		//get image info
		list($source_width, $source_height, $source_type, $source_attr) = getimagesize($source);

		//set dimensions
		if ($source_width > $source_height) {
			$ratio = $source_height / $source_width;

			$t_width = $target_width;

			//respect the ratio
			$t_height = round($ratio * $target_width);

			//set the offset
			$off_x = 0;
			$off_y = ceil(($target_height - $t_height) / 2);
		}
		else if ($source_height > $source_width)
		{
			$ratio = $source_width / $source_height;

			$t_height = $target_height;

			$t_width = round($ratio * $target_height);

			$off_x = ceil(($target_width - $t_width) / 2);
			$off_y = 0;
		}
		else {
			$t_width = $t_height = $target_width;
			$off_x = $off_y = 0;
		}
		                
		$thumb = imagecreatefromjpeg($source);
		$thumb_p = imagecreatetruecolor($target_width, $target_height);

		//default background is black
		$bg = imagecolorallocate($thumb_p, 255, 255, 255);

		imagefill($thumb_p, 0, 0, $bg);

		imagecopyresampled($thumb_p, $thumb, $off_x, $off_y, 0, 0, $t_width, $t_height, $source_width, $source_height);
		
		return imagejpeg($thumb_p, $destination, $quality);
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

//if ($_SESSION['iduser'] != 1) die('EN PROCESO DE ACTUALIZACION');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'inicio':
			$tpl = new TemplatePower('plantillas/ped/MateriasPrimasCatalogoInicio.tpl');
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
					$condiciones[] = 'codmp IN (' . implode(', ', $productos) . ')';
				}
			}
			
			if (isset($_REQUEST['nombre']) && $_REQUEST['nombre'] != '') {
				$condiciones[] = 'cmp.nombre LIKE \'%' . $_REQUEST['nombre'] . '%\'';
			}
			
			if (isset($_REQUEST['categoria'])) {
				$condiciones[] = 'cmp.tipo IN (\'' . implode('\', \'', $_REQUEST['categoria']) . '\')';
			}
			
			if (isset($_REQUEST['controlada'])) {
				$condiciones[] = 'cmp.controlada IN (\'' . implode('\', \'', $_REQUEST['controlada']) . '\')';
			}
			
			if (isset($_REQUEST['tipo'])) {
				$condiciones[] = 'cmp.tipo_cia IN (' . implode(', ', $_REQUEST['tipo']) . ')';
			}
			
			if (isset($_REQUEST['pedido'])) {
				$condiciones[] = 'cmp.procpedautomat IN (' . implode(', ', $_REQUEST['pedido']) . ')';
			}
			
			$page_size = 30;
			
			$sql = '
				SELECT
					COUNT(codmp)
						AS count
				FROM
					catalogo_mat_primas cmp
					LEFT JOIN tipo_unidad_consumo tuc
						ON (tuc.idunidad = cmp.unidadconsumo)
				WHERE
					' . implode(' AND ', $condiciones) . '
			';
			
			$tmp = $db->query($sql);
			
			$num_rows = $tmp ? $tmp[0]['count'] : 0;
			
			$pages = ceil($num_rows / $page_size);
			
			if (!isset($_REQUEST['page'])) {
				$page = 1;
				
				$offset = 0;
			}
			else if (isset($_REQUEST['page']) && $_REQUEST['page'] > 0) {
				$page = $_REQUEST['page'];
				
				$offset = ($page - 1) * $page_size;
			}
			
			$sql = '
				SELECT
					codmp,
					nombre
						AS nombre_mp,
					tuc.descripcion
						AS unidad,
					CASE
						WHEN tipo = \'1\' THEN
							\'MATERIA PRIMA\'
						WHEN tipo = \'2\' THEN
							\'MATERIAL DE EMPAQUE\'
					END
						AS categoria,
					CASE
						WHEN controlada = \'TRUE\' THEN
							TRUE
						ELSE
							FALSE
					END
						AS controlada,
					procpedautomat
						AS pedido,
					COALESCE(no_exi, FALSE)
						AS sin_existencia,
					reporte_consumos_mas,
					grasa,
					azucar,
					imagen,
					porcentaje_ieps
				FROM
					catalogo_mat_primas cmp
					LEFT JOIN tipo_unidad_consumo tuc
						ON (tuc.idunidad = cmp.unidadconsumo)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					codmp
				LIMIT
					' . $page_size . '
				OFFSET
					' . $offset . '
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ped/MateriasPrimasCatalogoConsulta.tpl');
			$tpl->prepare();
			
			if ($result) {
				if ($pages > 1) {
					$index = '';
					
					for ($i = 1; $i <= $pages; $i++) {
						if ($i == $page) {
							$index .= $i . ' ';
						}
						else {
							$index .= '<a id="page" href="#" page="' . $i . '">' . $i . '</a> ';
						}
					}
					
					$index = '<div class="bold font12" style="width:500px; margin-top:20px; margin-bottom:20px;">' . $index . '</div>';
					
					$tpl->assign('index', $index);
				}
				
				$row_color = FALSE;
				
				foreach ($result as $rec) {
					$tpl->newBlock('row');
					
					$tpl->assign('row_color', $row_color ? 'on' : 'off');
					
					$row_color = !$row_color;
					
					$tpl->assign('imagen', $rec['imagen'] != '' ? '<img src="/lecaroz/iconos/picture.png" width="16" height="16" align="absmiddle" id="imagen_{codmp}" /> ' : '');
					$tpl->assign('codmp', $rec['codmp']);
					$tpl->assign('nombre_mp', utf8_encode($rec['nombre_mp']));
					$tpl->assign('unidad', utf8_encode($rec['unidad']));
					$tpl->assign('categoria', utf8_encode($rec['categoria']));
					$tpl->assign('ieps', $rec['porcentaje_ieps'] > 0 ? number_format($rec['porcentaje_ieps'], 2) : '&nbsp;');
					
					$tpl->assign('controlada', $rec['controlada'] == 'f' ? '_blank' : '');
					$tpl->assign('pedido', $rec['pedido'] == 'f' ? '_blank' : '');
					$tpl->assign('sin_existencia', $rec['sin_existencia'] == 'f' ? '_blank' : '');
					$tpl->assign('reporte_consumos_mas', $rec['reporte_consumos_mas'] == 'f' ? '_blank' : '');
					$tpl->assign('grasa', $rec['grasa'] == 'f' ? '_blank' : '');
					$tpl->assign('azucar', $rec['azucar'] == 'f' ? '_blank' : '');
				}
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'alta':
			$tpl = new TemplatePower('plantillas/ped/MateriasPrimasCatalogoAlta.tpl');
			$tpl->prepare();
			
			$sql = '
				SELECT
					idunidad
						AS value,
					descripcion
						AS text
				FROM
					tipo_unidad_consumo
				ORDER BY
					value
			';
			
			$unidades = $db->query($sql);
			
			if ($unidades) {
				foreach ($unidades as $u) {
					$tpl->newBlock('unidad');
					$tpl->assign('value', $u['value']);
					$tpl->assign('text', utf8_encode($u['text']));
				}
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'doAlta':
			$sql = '
				SELECT
					codmp
				FROM
					catalogo_mat_primas
				ORDER BY
					codmp
			';
			
			$result = $db->query($sql);
			
			$codmp = 1;
			
			if ($result) {
				foreach ($result as $rec) {
					if ($codmp != $rec['codmp']) {
						$codmp = $rec['codmp'];
						
						break;
					}
					else {
						$codmp++;
					}
				}
			}
			
			$sql = '
				INSERT INTO
					catalogo_mat_primas (
						codmp,
						nombre,
						unidadconsumo,
						tipo,
						controlada,
						procpedautomat,
						tipo_cia,
						no_exi,
						reporte_consumos_mas,
						grasa,
						azucar,
						prioridad_orden,
						imagen,
						porcentaje_ieps
					)
					VALUES (
						' . $codmp . ',
						\'' . utf8_encode($_REQUEST['nombre']) . '\',
						' . $_REQUEST['unidad'] . ',
						' . $_REQUEST['tipo'] . ',
						' . $_REQUEST['controlada'] . ',
						' . $_REQUEST['procpedautomat'] . ',
						' . $_REQUEST['tipo_cia'] . ',
						' . $_REQUEST['no_exi'] . ',
						' . $_REQUEST['reporte_consumos_mas'] . ',
						' . $_REQUEST['grasa'] . ',
						' . $_REQUEST['azucar'] . ',
						' . (isset($_REQUEST['prioridad_orden']) && $_REQUEST['prioridad_orden'] > 0 ? $_REQUEST['prioridad_orden'] : 0) . ',
						' . ($_REQUEST['imagen_tmp'] != '' ? "'img_mp/img_mp_{$codmp}.jpg'" : 'NULL') . ',
						' . (isset($_REQUEST['porcentaje_ieps']) && get_val($_REQUEST['porcentaje_ieps']) > 0 ? get_val($_REQUEST['porcentaje_ieps']) : 0) . '
					)
			' . ";\n";
			
			$db->query($sql);

			if ($_REQUEST['imagen_tmp'] != '')
			{
				rename($_REQUEST['imagen_tmp'], 'img_mp/img_mp_' . $codmp . '.jpg');
			}
			
			echo json_encode(array(
				'codmp'  => intval($codmp),
				'nombre' => utf8_encode($_REQUEST['nombre'])
			));
		break;
		
		case 'modificar':
			$sql = '
				SELECT
					codmp,
					nombre,
					unidadconsumo,
					tipo,
					controlada,
					procpedautomat,
					tipo_cia,
					no_exi,
					reporte_consumos_mas,
					grasa,
					azucar,
					prioridad_orden,
					imagen,
					porcentaje_ieps
				FROM
					catalogo_mat_primas
				WHERE
					codmp = ' . $_REQUEST['codmp'] . '
			';
			$result = $db->query($sql);
			
			$rec = $result[0];
			
			$tpl = new TemplatePower('plantillas/ped/MateriasPrimasCatalogoModificar.tpl');
			$tpl->prepare();
			
			$tpl->assign('codmp', $rec['codmp']);
			$tpl->assign('nombre', utf8_encode($rec['nombre']));
			$tpl->assign('tipo_cia_' . $rec['tipo_cia'], ' checked');
			$tpl->assign('tipo_' . $rec['tipo'], ' selected');
			$tpl->assign('controlada_' . $rec['controlada'], ' checked');
			$tpl->assign('procpedautomat_' . $rec['procpedautomat'], ' checked');
			$tpl->assign('no_exi_' . $rec['no_exi'], ' checked');
			$tpl->assign('reporte_consumos_mas_' . $rec['reporte_consumos_mas'], ' checked');
			$tpl->assign('grasa_' . $rec['grasa'], ' checked');
			$tpl->assign('azucar_' . $rec['azucar'], ' checked');
			$tpl->assign('prioridad_orden', $rec['prioridad_orden'] > 0 ? $rec['prioridad_orden'] : '');
			$tpl->assign('imagen_src', $rec['imagen'] != '' ? $rec['imagen'] . '?v=' . time() : 'img_mp/sin_imagen.jpg');
			$tpl->assign('porcentaje_ieps', $rec['porcentaje_ieps'] > 0 ? number_format($rec['porcentaje_ieps'], 2) : '');
			
			$sql = '
				SELECT
					idunidad
						AS value,
					descripcion
						AS text
				FROM
					tipo_unidad_consumo
				ORDER BY
					value
			';
			
			$unidades = $db->query($sql);
			
			if ($unidades) {
				foreach ($unidades as $u) {
					$tpl->newBlock('unidad');
					$tpl->assign('value', $u['value']);
					$tpl->assign('text', utf8_encode($u['text']));
					
					if ($u['value'] == $rec['unidadconsumo']) {
						$tpl->assign('selected', ' selected');
					}
				}
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'doModificar':
			$sql = '
				UPDATE
					catalogo_mat_primas
				SET
					nombre = \'' . utf8_decode($_REQUEST['nombre']) . '\',
					unidadconsumo = ' . $_REQUEST['unidad'] . ',
					tipo = ' . $_REQUEST['tipo'] . ',
					controlada = ' . $_REQUEST['controlada'] . ',
					procpedautomat = ' . $_REQUEST['procpedautomat'] . ',
					tipo_cia = ' . $_REQUEST['tipo_cia'] . ',
					no_exi = ' . $_REQUEST['no_exi'] . ',
					reporte_consumos_mas = ' . $_REQUEST['reporte_consumos_mas'] . ',
					grasa = ' . $_REQUEST['grasa'] . ',
					azucar = ' . $_REQUEST['azucar'] . ',
					prioridad_orden = ' . (isset($_REQUEST['prioridad_orden']) && $_REQUEST['prioridad_orden'] > 0 ? $_REQUEST['prioridad_orden'] : 0) . ',
					imagen = ' . ($_REQUEST['imagen_src'] != '' || $_REQUEST['imagen_tmp'] != '' ? "'img_mp/img_mp_{$_REQUEST['codmp']}.jpg'" : 'NULL') . ',
					porcentaje_ieps = ' . (isset($_REQUEST['porcentaje_ieps']) && get_val($_REQUEST['porcentaje_ieps']) > 0 ? get_val($_REQUEST['porcentaje_ieps']) : 0) . '
				WHERE
					codmp = ' . $_REQUEST['codmp'] . '
			' . ";\n";
			
			$db->query($sql);

			if ($_REQUEST['imagen_tmp'] != '')
			{
				rename($_REQUEST['imagen_tmp'], 'img_mp/img_mp_' . $_REQUEST['codmp'] . '.jpg');
			}
			else if ($_REQUEST['imagen_src'] == '')
			{
				@unlink('img_mp/img_mp_' . $_REQUEST['codmp'] . '.jpg');
			}
		break;
		
		case 'cambiarStatusControlada':
			$sql = '
				UPDATE
					catalogo_mat_primas
				SET
					controlada = (
						CASE
							WHEN controlada = TRUE THEN
								FALSE
							WHEN controlada = FALSE THEN
								TRUE
						END
					)
				WHERE
					codmp = ' . $_REQUEST['codmp'] . '
			';
			
			$db->query($sql);
			
			$sql = '
				SELECT
					controlada
				FROM
					catalogo_mat_primas
				WHERE
					codmp = ' . $_REQUEST['codmp'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$data['status'] = $result[0]['controlada'] == 't' ? TRUE : FALSE;
				
				echo json_encode($data);
			}
		break;
		
		case 'cambiarStatusPedido':
			$sql = '
				UPDATE
					catalogo_mat_primas
				SET
					procpedautomat = (
						CASE
							WHEN procpedautomat = TRUE THEN
								FALSE
							WHEN procpedautomat = FALSE THEN
								TRUE
						END
					)
				WHERE
					codmp = ' . $_REQUEST['codmp'] . '
			';
			
			$db->query($sql);
			
			$sql = '
				SELECT
					procpedautomat
				FROM
					catalogo_mat_primas
				WHERE
					codmp = ' . $_REQUEST['codmp'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$data['status'] = $result[0]['procpedautomat'] == 't' ? TRUE : FALSE;
				
				echo json_encode($data);
			}
		break;
		
		case 'cambiarStatusSinExistencia':
			$sql = '
				UPDATE
					catalogo_mat_primas
				SET
					no_exi = (
						CASE
							WHEN no_exi = TRUE THEN
								FALSE
							WHEN no_exi = FALSE THEN
								TRUE
						END
					)
				WHERE
					codmp = ' . $_REQUEST['codmp'] . '
			';
			
			$db->query($sql);
			
			$sql = '
				SELECT
					no_exi
				FROM
					catalogo_mat_primas
				WHERE
					codmp = ' . $_REQUEST['codmp'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$data['status'] = $result[0]['no_exi'] == 't' ? TRUE : FALSE;
				
				echo json_encode($data);
			}
		break;

		case 'cambiarStatusReporteConsumosMas':
			$sql = '
				UPDATE
					catalogo_mat_primas
				SET
					reporte_consumos_mas = (
						CASE
							WHEN reporte_consumos_mas = TRUE THEN
								FALSE
							WHEN reporte_consumos_mas = FALSE THEN
								TRUE
						END
					)
				WHERE
					codmp = ' . $_REQUEST['codmp'] . '
			';
			
			$db->query($sql);
			
			$sql = '
				SELECT
					reporte_consumos_mas
				FROM
					catalogo_mat_primas
				WHERE
					codmp = ' . $_REQUEST['codmp'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$data['status'] = $result[0]['reporte_consumos_mas'] == 't' ? TRUE : FALSE;
				
				echo json_encode($data);
			}
		break;

		case 'cambiarStatusGrasa':
			$sql = '
				UPDATE
					catalogo_mat_primas
				SET
					grasa = (
						CASE
							WHEN grasa = TRUE THEN
								FALSE
							WHEN grasa = FALSE THEN
								TRUE
						END
					)
				WHERE
					codmp = ' . $_REQUEST['codmp'] . '
			';
			
			$db->query($sql);
			
			$sql = '
				SELECT
					grasa
				FROM
					catalogo_mat_primas
				WHERE
					codmp = ' . $_REQUEST['codmp'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$data['status'] = $result[0]['grasa'] == 't' ? TRUE : FALSE;
				
				echo json_encode($data);
			}
		break;

		case 'cambiarStatusAzucar':
			$sql = '
				UPDATE
					catalogo_mat_primas
				SET
					azucar = (
						CASE
							WHEN azucar = TRUE THEN
								FALSE
							WHEN azucar = FALSE THEN
								TRUE
						END
					)
				WHERE
					codmp = ' . $_REQUEST['codmp'] . '
			';
			
			$db->query($sql);
			
			$sql = '
				SELECT
					azucar
				FROM
					catalogo_mat_primas
				WHERE
					codmp = ' . $_REQUEST['codmp'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$data['status'] = $result[0]['azucar'] == 't' ? TRUE : FALSE;
				
				echo json_encode($data);
			}
		break;
		
		case 'listado':
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
					$condiciones[] = 'codmp IN (' . implode(', ', $productos) . ')';
				}
			}
			
			if (isset($_REQUEST['nombre']) && $_REQUEST['nombre'] != '') {
				$condiciones[] = 'cmp.nombre LIKE \'%' . $_REQUEST['nombre'] . '%\'';
			}
			
			if (isset($_REQUEST['categoria'])) {
				$condiciones[] = 'cmp.tipo IN (\'' . implode('\', \'', $_REQUEST['categoria']) . '\')';
			}
			
			if (isset($_REQUEST['controlada'])) {
				$condiciones[] = 'cmp.controlada IN (\'' . implode('\', \'', $_REQUEST['controlada']) . '\')';
			}
			
			if (isset($_REQUEST['tipo'])) {
				$condiciones[] = 'cmp.tipo_cia IN (' . implode(', ', $_REQUEST['tipo']) . ')';
			}
			
			if (isset($_REQUEST['pedido'])) {
				$condiciones[] = 'cmp.procpedautomat IN (' . implode(', ', $_REQUEST['pedido']) . ')';
			}
			
			$sql = '
				SELECT
					codmp,
					nombre
						AS nombre_mp,
					tuc.descripcion
						AS unidad,
					CASE
						WHEN tipo = \'1\' THEN
							\'MATERIA PRIMA\'
						WHEN tipo = \'2\' THEN
							\'MATERIAL DE EMPAQUE\'
					END
						AS categoria,
					CASE
						WHEN controlada = \'TRUE\' THEN
							TRUE
						ELSE
							FALSE
					END
						AS controlada,
					procpedautomat
						AS pedido,
					COALESCE(no_exi, FALSE)
						AS sin_existencia
				FROM
					catalogo_mat_primas cmp
					LEFT JOIN tipo_unidad_consumo tuc
						ON (tuc.idunidad = cmp.unidadconsumo)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					codmp
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/ped/MateriasPrimasCatalogoListadoNormal.tpl');
			$tpl->prepare();
			
			if ($result) {
				$rows_per_sheet = 56;
				
				$rows = $rows_per_sheet;
				
				foreach ($result as $i => $rec) {
					if ($rows >= $rows_per_sheet) {
						if ($i > 0) {
							$tpl->assign('reporte.salto', '<br class="page-break" />');
						}
						
						$tpl->newBlock('reporte');
						
						$rows = 0;
					}
					
					$tpl->newBlock('row');
					
					$tpl->assign('codmp', $rec['codmp']);
					$tpl->assign('nombre_mp', utf8_encode($rec['nombre_mp']));
					$tpl->assign('unidad', utf8_encode($rec['unidad']));
					$tpl->assign('categoria', utf8_encode($rec['categoria']));
					$tpl->assign('categoria_color', $rec['categoria'] == 'MATERIA PRIMA' ? 'blue' : 'red');
					$tpl->assign('controlada', $rec['controlada'] == 't' ? '&#x2713;' : '&nbsp;');
					$tpl->assign('pedido', $rec['pedido'] == 't' ? '&#x2713;' : '&nbsp;');
					$tpl->assign('sin_existencia', $rec['sin_existencia'] == 't' ? '&#x2713;' : '&nbsp;');
					
					$rows++;
				}
			}
			
			$tpl->printToScreen();
		break;

		case 'imagen_tmp':
			$allowed_formats = array(
				'image/jpeg',
				'image/jpg'
			);

			$route_tmp = 'img_mp/tmp/';

			$tmp_file_name = 'ìmg-tmp-' . time() . '.jpg';

			if ($_FILES['imagen']['error'] > 0)
			{
				header('Content-Type: application/json');
				echo json_encode(array(
					'status'	=> -1,
					'error'		=> 'Error en la carga del archivo al servidor'
				));
			}
			else if (!in_array($_FILES['imagen']['type'], $allowed_formats))
			{
				header('Content-Type: application/json');
				echo json_encode(array(
					'status'	=> -2,
					'error'		=> 'El tipo de imágen debe ser JPG'
				));
			}
			else
			{
				if ( ! preparar_imagen($_FILES["imagen"]["tmp_name"], $route_tmp . $tmp_file_name))
				{
					header('Content-Type: application/json');
					echo json_encode(array(
						'status'	=> -3,
						'error'		=> 'Error en la carga del archivo al servidor'
					));
				}
				else
				{
					header('Content-Type: application/json');
					echo json_encode(array(
						'status'	=> 1,
						'image'		=> $route_tmp . $tmp_file_name
					));
				}
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ped/MateriasPrimasCatalogo.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
