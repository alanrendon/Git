<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');
include('includes/phpmailer/class.phpmailer.php');

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

//if ($_SESSION['iduser'] != 1) die('EN PROCESO DE ACTUALIZACION');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'inicio':
			$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasSeriesInicio.tpl');
			$tpl->prepare();
			
			$sql = '
				SELECT
					idcontador
						AS
							id,
					nombre_contador
						AS
							nombre
				FROM
					catalogo_contadores
				ORDER BY
					nombre
			';
			$contadores = $db->query($sql);
			
			foreach ($contadores as $c) {
				$tpl->newBlock('contador');
				$tpl->assign('id', $c['id']);
				$tpl->assign('nombre', utf8_encode($c['nombre']));
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'consultar':
			$condiciones = array();
			
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
			
			if (isset($_REQUEST['contador'])) {
				$condiciones[] = 'idcontador = ' . $_REQUEST['contador'];
			}
			
			if (isset($_REQUEST['status'])) {
				$condiciones[] = 'fes.status IN (' . implode(', ', $_REQUEST['status']) . ')';
			}
			
			if (isset($_REQUEST['tipo_serie'])) {
				$condiciones[] = 'tipo_serie IN (' . implode(', ', $_REQUEST['tipo_serie']) . ')';
			}
			
			$sql = '
				SELECT
					id,
					num_cia,
					razon_social || \' (\' || nombre_corto || \')\'
						AS
							nombre_cia,
					CASE
						WHEN tipo_serie = 1 THEN
							\'FACTURA\'
						WHEN tipo_serie = 2 THEN
							\'ARRENDAMIENTO\'
						WHEN tipo_serie = 3 THEN
							\'NOTA DE CREDITO\'
					END
						AS
							tipo_serie,
					serie,
					folio_inicial,
					folio_final,
					ultimo_folio_usado
						AS
							folio_actual,
					no_aprobacion,
					fecha_aprobacion,
					anio_aprobacion,
					serie_certificado,
					archivo_certificado,
					archivo_llave,
					contrasenia_certificado,
					contrasenia_llave,
					CASE
						WHEN fes.status = 1 THEN
							\'<span class="green">ACTIVO</span>\'
						WHEN fes.status = 2 THEN
							\'<span class="blue">TERMINADO</span>\'
						WHEN fes.status = 3 THEN
							\'<span class="red">CANCELADO</span>\'
						ELSE
							\'<span>DESCONOCIDO</span>\'
					END
						AS status,
					fes.tipo_cfd
				FROM
						facturas_electronicas_series fes
					LEFT JOIN
						catalogo_companias cc
							USING (num_cia)
			';
			
			if (count($condiciones) > 0) {
				$sql .= '
					WHERE
						' . implode(' AND ', $condiciones) . '
				';
			}
			
			$sql .= '
				ORDER BY
					num_cia,
					serie,
					folio_inicial
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasSeriesConsulta.tpl');
				$tpl->prepare();
				
				$num_cia = NULL;
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
						
						$color = FALSE;
					}
					
					$tpl->newBlock('row');
					$tpl->assign('color', $color ? 'on' : 'off');
					$color = !$color;
					
					$tpl->assign('id', $rec['id']);
					$tpl->assign('tipo_serie', $rec['tipo_serie']);
					$tpl->assign('serie', $rec['serie']);
					$tpl->assign('folio_inicial', $rec['folio_inicial']);
					$tpl->assign('folio_final', $rec['folio_final']);
					$tpl->assign('folio_actual', $rec['folio_actual'] > 0 ? $rec['folio_actual'] : '&nbsp;');
					$tpl->assign('no_aprobacion', $rec['no_aprobacion']);
					$tpl->assign('fecha_aprobacion', $rec['fecha_aprobacion']);
					$tpl->assign('anio_aprobacion', $rec['anio_aprobacion']);
					$tpl->assign('serie_certificado', $rec['serie_certificado']);
					$tpl->assign('archivo_certificado', $rec['archivo_certificado']);
					$tpl->assign('contrasenia_certificado', $rec['contrasenia_certificado']);
					$tpl->assign('archivo_llave', $rec['archivo_llave']);
					$tpl->assign('contrasenia_llave', $rec['contrasenia_llave']);
					$tpl->assign('tipo_cfd', $rec['tipo_cfd'] == 1 ? '<span class="orange">CFD</span>' : '<span class="blue">CFDI</span>');
					$tpl->assign('status', $rec['status']);
				}
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'alta':
			$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasSeriesAlta.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
		break;
		
		case 'obtenerCia':
			$sql = '
				SELECT
					nombre_corto
						AS
							nombre_cia
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				echo $result[0]['nombre_cia'];
			}
		break;
		
		case 'registrarAlta':
			$sql = '
				INSERT INTO
					facturas_electronicas_series
						(
							num_cia,
							tipo_serie,
							serie,
							folio_inicial,
							folio_final,
							ultimo_folio_usado,
							no_aprobacion,
							fecha_aprobacion,
							anio_aprobacion,
							serie_certificado,
							archivo_certificado,
							contrasenia_certificado,
							archivo_llave,
							contrasenia_llave,
							status,
							tipo_cfd
						)
					VALUES
						(
							' . $_REQUEST['num_cia'] . ',
							' . $_REQUEST['tipo_serie'] . ',
							\'' . (isset($_REQUEST['serie']) ? $_REQUEST['serie'] : '') . '\',
							' . $_REQUEST['folio_inicial'] . ',
							' . $_REQUEST['folio_final'] . ',
							' . $_REQUEST['folio_actual'] . ',
							' . $_REQUEST['no_aprobacion'] . ',
							\'' . $_REQUEST['fecha_aprobacion'] . '\',
							' . $_REQUEST['anio_aprobacion'] . ',
							\'' . $_REQUEST['serie_certificado'] . '\',
							\'' . $_REQUEST['archivo_certificado'] . '\',
							\'' . $_REQUEST['contrasenia_certificado'] . '\',
							\'' . $_REQUEST['archivo_llave'] . '\',
							\'' . $_REQUEST['contrasenia_llave'] . '\',
							1,
							' . $_REQUEST['tipo_cfd'] . '
						)
			';
			
			$db->query($sql);
		break;
		
		case 'modificar':
			$sql = '
				SELECT
					id,
					num_cia,
					nombre_corto
						AS
							nombre_cia,
					tipo_serie,
					serie,
					folio_inicial,
					folio_final,
					ultimo_folio_usado
						AS
							folio_actual,
					no_aprobacion,
					fecha_aprobacion,
					anio_aprobacion,
					serie_certificado,
					archivo_certificado,
					contrasenia_certificado,
					archivo_llave,
					contrasenia_llave,
					tipo_cfd
				FROM
						facturas_electronicas_series
					LEFT JOIN
						catalogo_companias
							USING (num_cia)
				WHERE
					id = ' . $_REQUEST['id'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$rec = $result[0];
				
				$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasSeriesModificar.tpl');
				$tpl->prepare();
				
				foreach ($rec as $key => $value) {
					$tpl->assign($key, $value);
				}
				
				$tpl->assign('tipo_serie_' . $rec['tipo_serie'], ' checked');
				$tpl->assign('tipo_cfd_' . $rec['tipo_cfd'], ' checked');
				
				echo $tpl->getOutputContent();
			}
		break;
		
		case 'registrarModificacion':
			$sql = '
				UPDATE
					facturas_electronicas_series
				SET
					tipo_serie = ' . $_REQUEST['tipo_serie'] . ',
					serie = \'' . (isset($_REQUEST['serie']) ? $_REQUEST['serie'] : '') . '\',
					folio_inicial = ' . $_REQUEST['folio_inicial'] . ',
					folio_final = ' . $_REQUEST['folio_final'] . ',
					ultimo_folio_usado = ' . $_REQUEST['folio_actual'] . ',
					no_aprobacion = ' . $_REQUEST['no_aprobacion'] . ',
					fecha_aprobacion = \'' . $_REQUEST['fecha_aprobacion'] . '\',
					anio_aprobacion = ' . $_REQUEST['anio_aprobacion'] . ',
					serie_certificado = \'' . $_REQUEST['serie_certificado'] . '\',
					archivo_certificado = \'' . $_REQUEST['archivo_certificado'] . '\',
					contrasenia_certificado = \'' . $_REQUEST['contrasenia_certificado'] . '\',
					archivo_llave = \'' . $_REQUEST['archivo_llave'] . '\',
					contrasenia_llave = \'' . $_REQUEST['contrasenia_llave'] . '\',
					tipo_cfd = ' . $_REQUEST['tipo_cfd'] . '
				WHERE
					id = ' . $_REQUEST['id'] . '
			';
			
			$db->query($sql);
		break;
		
		case 'baja':
			$sql = '
				UPDATE
					facturas_electronicas_series
				SET
					status = 0
				WHERE
					id = ' . $_REQUEST['id'] . '
			';
			
			$db->query($sql);
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasSeries.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
