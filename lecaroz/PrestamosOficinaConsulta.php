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
	12 => 'DICIMEBRE'
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
			$condiciones[] = 'po.pagado IS NULL';
			
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
					$condiciones[] = 'po.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			/*
			@ Administrador
			*/
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}
			
			$sql = '
				SELECT
					po.num_cia,
					cc.nombre_corto
						AS
							nombre_cia,
					id_empleado
						AS
							id,
					LPAD(num_emp, 5, \'0\')
						AS
							num,
					COALESCE(ap_paterno, \'\') || \' \' || COALESCE(ap_materno, \'\') || \' \' || COALESCE(ct.nombre, \'\')
							AS
								empleado,
					(
						SELECT
							MAX(fecha)
						FROM
							prestamos_oficina
						WHERE
								id_empleado = po.id_empleado
							AND
								pagado IS NULL
							AND
								tipo = \'FALSE\'
					)
						AS
							ultimo_prestamo,
					SUM(
						CASE
							WHEN po.tipo = \'FALSE\' THEN
								importe
							ELSE
								-importe
						END
					)
						AS
							saldo,
					(
						SELECT
							SUM(importe)
						FROM
							prestamos_oficina
						WHERE
								id_empleado = po.id_empleado
							AND
								pagado IS NULL
							AND
								tipo = \'TRUE\'
					)
						AS
							pagos,
					(
						SELECT
							MAX(fecha)
						FROM
							prestamos_oficina
						WHERE
								id_empleado = po.id_empleado
							AND
								pagado IS NULL
							AND
								tipo = \'TRUE\'
					)
						AS
							ultimo_pago,
					(
						SELECT
							importe
						FROM
							prestamos_oficina
						WHERE
								id_empleado = po.id_empleado
							AND
								pagado IS NULL
							AND
								tipo = \'TRUE\'
						ORDER BY
							fecha DESC
						LIMIT
							1
					)
						AS
							importe_ultimo_pago,
					(
						SELECT
							now()::date - MAX(fecha)::date
						FROM
							prestamos_oficina
						WHERE
								id_empleado = po.id_empleado
							AND
								pagado IS NULL
					)
						AS
							dias_atraso
				FROM
						prestamos_oficina po
					LEFT JOIN
						catalogo_companias cc
							ON
								(
									cc.num_cia = po.num_cia
								)
					LEFT JOIN
						catalogo_trabajadores ct
							ON
								(
									ct.id = po.id_empleado
								)
				WHERE
					' . implode(' AND ', $condiciones) . '
				GROUP BY
					po.num_cia,
					nombre_cia,
					po.id_empleado,
					num,
					empleado
				ORDER BY
					po.num_cia,
					empleado
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				$tpl = new TemplatePower('plantillas/adm/PrestamosOficinaReporte.tpl');
				$tpl->prepare();
				
				$tpl->newBlock('reporte');
				$tpl->assign('dia', date('d'));
				$tpl->assign('mes', $_meses[date('n')]);
				$tpl->assign('anio', date('Y'));
				
				$num_cia = NULL;
				foreach ($result as $rec) {
					if ($num_cia != $rec['num_cia']) {
						$num_cia = $rec['num_cia'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', $rec['nombre_cia']);
					}
					
					$tpl->newBlock('row');
					$tpl->assign('num', $rec['num']);
					$tpl->assign('empleado', $rec['empleado']);
					$tpl->assign('ultimo_prestamo', $rec['ultimo_prestamo']);
					$tpl->assign('saldo', number_format($rec['saldo'], 2, '.', ','));
					$tpl->assign('pagos', $rec['pagos'] > 0 ? number_format($rec['pagos'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('ultimo_pago', $rec['ultimo_pago'] != '' ? $rec['ultimo_pago'] : '&nbsp;');
					$tpl->assign('importe_ultimo_pago', $rec['importe_ultimo_pago'] > 0 ? number_format($rec['importe_ultimo_pago'], 2, '.', ',') : '&nbsp;');
					$tpl->assign('dias_atraso', $rec['dias_atraso']);
					
					$sql = '
						SELECT
							fecha,
							tipo,
							importe
						FROM
							prestamos_oficina
						WHERE
								id_empleado = ' . $rec['id'] . '
							AND
								pagado IS NULL
						ORDER BY
							fecha,
							tipo
					';
					$movs = $db->query($sql);
					
					$detalle = array();
					foreach ($movs as $m) {
						$detalle[] = '<tr><td>' . $m['fecha'] . '&nbsp;</td><td align=&quot;right&quot; class=&quot;bold ' . ($m['tipo'] == 'f' ? 'red' : 'blue') . '&quot;>' . number_format($m['importe'], 2, '.', ',') . '</td></tr>';
					}
					
					$tpl->assign('detalle', '<table style=&quot;border-collapse:collapse;&quot; align=&quot;center&quot;><tr><th>Fecha</th><th>Importe</th></tr>' . implode('', $detalle) . '</table>');
				}
				
				$tpl->printToScreen();
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/adm/PrestamosOficinaConsulta.tpl');
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
