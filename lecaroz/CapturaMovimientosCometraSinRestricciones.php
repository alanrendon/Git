<?php

include_once('includes/class.db.inc.php');
include_once('includes/class.session2.inc.php');
include_once('includes/class.TemplatePower.inc.php');
include_once('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'codigos':
			$sql = '
				SELECT
					banco
				FROM
					cometra
				WHERE
						tsend IS NULL
					AND
						banco IS NOT NULL
				LIMIT 1
			';
			$banco = $db->query($sql);
			
			if ($banco) {
				$sql = '
					SELECT
						cod_mov,
						descripcion
					FROM
						' . ($banco[0]['banco'] == 1 ? 'catalogo_mov_bancos' : 'catalogo_mov_santander') . '
					WHERE
							tipo_mov = \'FALSE\'
						OR
							cod_mov IN (19, 48, 21)
					GROUP BY
						cod_mov,
						descripcion
					ORDER BY
						cod_mov
				';
				$codigos = $db->query($sql);
			}
			else {
				$sql = '
					SELECT
						cod_mov,
						descripcion
					FROM
						catalogo_mov_santander
					WHERE
							tipo_mov = \'FALSE\'
						OR
							cod_mov IN (19, 48, 21)
					
					UNION
					
					SELECT
						cod_mov,
						descripcion
					FROM
						catalogo_mov_bancos
					WHERE
							tipo_mov = \'FALSE\'
						OR
							cod_mov IN (19, 48, 21)
					
					GROUP BY
						cod_mov,
						descripcion
					ORDER BY
						cod_mov
				';
				$codigos = $db->query($sql);
			}
			
			echo json_encode($codigos);
		break;
		
		case 'validarComprobante':
			$sql = '
				SELECT
					comprobante
				FROM
					cometra
				WHERE
						tsend IS NULL
					AND
						comprobante = ' . $_REQUEST['comprobante'] . '
				LIMIT 1
			';
			$result = $db->query($sql);
			
			if ($result) {
				echo -1;
			}
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
				echo utf8_encode($result[0]['nombre_cia']);
			}
			else {
				echo -1;
			}
		break;
		
		case 'obtenerFecha':
			$sql = '
				SELECT
					(now() - interval \'2 days\')::date
						AS
							fecha
			';
			$result = $db->query($sql);
			
			echo $result[0]['fecha'];
		break;
		
		case 'validarFecha':
			$sql = '
				SELECT
					\'' . $_REQUEST['fecha'] . '\' BETWEEN now() - interval \'15 days\' AND now()
						AS
							status
			';
			
			$result = $db->query($sql);
			
			if ($result[0]['status'] == 'f') {
				echo -1;
			}
		break;
		
		case 'obtenerLocales':
			$sql = '
				SELECT
					id,
					num_local,
					nombre_local,
					subtotal + iva + agua - ret_isr - ret_iva
						AS
							renta
				FROM
					(
						SELECT
							id,
							num_local,
							nombre_local,
							COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0)
								AS
									subtotal,
							CASE
								WHEN tipo_local = 1 THEN
									(COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0)) * 0.16
								ELSE
									0
							END
								AS
									iva,
							CASE
								WHEN retencion_isr = \'TRUE\' AND tipo_local = 1 THEN
									(COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0)) * 0.10
								ELSE
									0
							END
								AS
									ret_isr,
							CASE
								WHEN retencion_iva = \'TRUE\' AND tipo_local = 1 THEN
									(COALESCE(renta_con_recibo, 0) + COALESCE(mantenimiento, 0)) * 0.10666666667
								ELSE
									0
							END
								AS
									ret_iva,
							COALESCE(agua, 0)
								AS
									agua
						FROM
							catalogo_arrendatarios
						WHERE
							cod_arrendador = ' . $_REQUEST['num_cia'] . '
					)
						local
				ORDER BY
					renta
						DESC
			';
			$locales = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/cometra/CapturaMovimientosCometraSinRestriccionesLocales.tpl');
			$tpl->prepare();
			
			$tpl->assign(date('n'), ' selected');
			$tpl->assign('anio', date('Y'));
			
			if ($locales) {
				foreach ($locales as $l) {
					$tpl->newBlock('local');
					$tpl->assign('id', $l['id']);
					$tpl->assign('num_local', str_pad($l['num_local'], 4, '0', STR_PAD_LEFT));
					$tpl->assign('nombre_local', $l['nombre_local']);
					$tpl->assign('renta', number_format($l['renta'], 2, '.', ','));
				}
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'obtenerRentas':
			$sql = '
				SELECT
					idreciborenta,
					EXTRACT(MONTH FROM fecha)
						AS mes,
					EXTRACT(YEAR FROM fecha)
						AS anio,
					fecha,
					idarrendatario,
					arrendatario,
					alias_arrendatario
						AS nombre_arrendatario,
					recibos.total
						AS renta
				FROM
					rentas_recibos recibos
					LEFT JOIN rentas_arrendatarios arrendatarios
						USING (idarrendatario)
					LEFT JOIN rentas_arrendadores arrendadores
						USING (idarrendador)
				WHERE
					arrendador = ' . $_REQUEST['num_cia'] . '
					AND fecha >= \'2012/03/01\'
					AND idreciborenta NOT IN (
						SELECT
							idreciborenta
						FROM
							estado_cuenta
						WHERE
							cod_mov = 2
							AND idreciborenta IS NOT NULL
					)
				ORDER BY
					renta DESC
			';
			
			$recibos = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/cometra/CapturaMovimientosCometraSinRestriccionesLocalesV2.tpl');
			$tpl->prepare();
			
			if ($recibos) {
				foreach ($recibos as $r) {
					$tpl->newBlock('recibo');
					$tpl->assign('id', htmlentities(json_encode(array(
						'idreciborenta'       => intval($r['idreciborenta']),
						'idarrendatario'      => intval($r['idarrendatario']),
						'nombre_arrendatario' => utf8_encode($r['nombre_arrendatario']),
						'fecha'               => $r['fecha'],
						'anio'                => intval($r['anio']),
						'mes'                 => $_meses[$r['mes']],
						'renta'               => floatval($r['renta'])
					))));
					$tpl->assign('mes', $_meses[$r['mes']]);
					$tpl->assign('anio', $r['anio']);
					$tpl->assign('arrendatario', str_pad($r['arrendatario'], 4, '0', STR_PAD_LEFT));
					$tpl->assign('nombre_arrendatario', utf8_encode($r['nombre_arrendatario']));
					$tpl->assign('renta', number_format($r['renta'], 2, '.', ','));
				}
			}
			
			echo $tpl->getOutputContent();
		break;
		
		case 'registrar':
			$sql = '';
			
			foreach ($_REQUEST['num_cia'] as $i => $id) {
				if ($_REQUEST['comprobante'] > 0
					&& $_REQUEST['num_cia'][$i] > 0
					&& $_REQUEST['fecha'][$i] != ''
					&& $_REQUEST['cod_mov'][$i] > 0
					&& get_val($_REQUEST['importe'][$i]) > 0) {
					$sql .= '
						INSERT INTO
							cometra
								(
									comprobante,
									tipo_comprobante,
									num_cia,
									fecha,
									cod_mov,
									concepto,
									importe,
									iduser_ins,
									tsins,
									iduser_mod,
									tsmod,
									/*local,*/
									fecha_renta,
									es_cheque,
									idreciborenta,
									idarrendatario
								)
							VALUES
								(
									' . $_REQUEST['comprobante'] . ',
									3,
									' . $_REQUEST['num_cia'][$i] . ',
									\'' . $_REQUEST['fecha'][$i] . '\',
									' . $_REQUEST['cod_mov'][$i] . ',
									\'' . $_REQUEST['concepto'][$i] . '\',
									' . get_val($_REQUEST['importe'][$i]) . ',
									' . $_SESSION['iduser'] . ',
									now(),
									' . $_SESSION['iduser'] . ',
									now(),
									/*' . ($_REQUEST['cod_mov'][$i] == 2 && $_REQUEST['local'][$i] > 0 ? $_REQUEST['local'][$i] : 'NULL') . ',*/
									' . ($_REQUEST['cod_mov'][$i] == 2 && $_REQUEST['fecha_renta'][$i] != '' ? '\'' . $_REQUEST['fecha_renta'][$i] . '\'' : 'NULL') . ',
									\'' . $_REQUEST['es_cheque'][$i] . '\',
									' . ($_REQUEST['cod_mov'][$i] == 2 && $_REQUEST['idreciborenta'][$i] > 0 ? $_REQUEST['idreciborenta'][$i] : 'NULL') . ',
									' . ($_REQUEST['cod_mov'][$i] == 2 && $_REQUEST['idarrendatario'][$i] > 0 ? $_REQUEST['idarrendatario'][$i] : 'NULL') . '
								)
					' . ";\n";
				}
			}
			
			$sql .= '
				UPDATE
					cometra
				SET
					banco = actual.banco
				FROM
					(
						SELECT
							banco
						FROM
							cometra
						WHERE
								tsend IS NULL
							AND
								banco IS NOT NULL
						LIMIT
							1
					)
						actual
				WHERE
					cometra.banco IS NULL
			' . ";\n";
			
			if ($sql != '') {
				$db->query($sql);
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/cometra/CapturaMovimientosCometraSinRestricciones.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$sql = '
	SELECT
		CASE
			WHEN banco = 1 THEN
				\'BANORTE\'
			WHEN banco = 2 THEN
				\'SANTANDER\'
			ELSE
				\'SIN DEFINIR\'
		END
			AS
				nombre_banco
	FROM
		cometra
	WHERE
			tsend IS NULL
		AND
			banco IS NOT NULL
	LIMIT
		1
';
$banco = $db->query($sql);

if ($banco)  {
	$tpl->assign('nombre_banco', $banco[0]['nombre_banco']);
}
else {
	$tpl->assign('nombre_banco', 'SIN DEFINIR');
}

$maxRows = 15;

for ($i = 0; $i < $maxRows; $i++) {
	$tpl->newBlock('row');
}

$tpl->printToScreen();
?>
