<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

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
		
		case 'inicio':
			$tpl = new TemplatePower('plantillas/bal/PagosAnticipadosInicio.tpl');
			$tpl->prepare();
			
			echo $tpl->getOutputContent();
			
			break;

		case 'obtener_cia':
			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';
			
			$result = $db->query($sql);
			
			if ($result) {
				echo utf8_encode($result[0]['nombre_corto']);
			}
			
			break;
		
		case 'consultar':
			$condiciones = array();
			
			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '')
			{
				$cias = array();
				
				$pieces = explode(',', $_REQUEST['cias']);

				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1)
					{
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else
					{
						$cias[] = $piece;
					}
				}
				
				if (count($cias) > 0)
				{
					$condiciones[] = 'pa.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			$sql = "
				SELECT
					pa.id,
					pa.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					pa.importe,
					pa.fecha_ini,
					pa.fecha_fin,
					pa.concepto,
					CASE
						WHEN NOW()::DATE BETWEEN pa.fecha_ini AND pa.fecha_fin THEN
							TRUE
						ELSE
							FALSE
					END
						AS activo,
					CASE
						WHEN pa.fecha_fin - NOW()::DATE > 0 THEN
							EXTRACT(YEARS FROM AGE(pa.fecha_fin, NOW()::DATE)) * 12 + EXTRACT(MONTHS FROM AGE(pa.fecha_fin, NOW()::DATE)) + 1
						ELSE
							NULL
					END
						AS meses_restantes
				FROM
					pagos_anticipados pa
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
				ORDER BY
					pa.num_cia,
					pa.fecha_ini,
					pa.concepto
			";
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/bal/PagosAnticipadosConsulta.tpl');
			$tpl->prepare();
			
			if ($result)
			{
				$num_cia = NULL;

				foreach ($result as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $row['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
					}

					$tpl->newBlock('row');
					
					$tpl->assign('id', $row['id']);
					$tpl->assign('inicio', $row['fecha_ini']);
					$tpl->assign('termino', $row['fecha_fin']);
					$tpl->assign('concepto', utf8_encode($row['concepto']));
					$tpl->assign('importe', number_format($row['importe'], 2));
					$tpl->assign('resta', $row['importe'] > 0 && $row['meses_restantes'] > 0 ? number_format($row['importe'] * $row['meses_restantes'], 2) : '&nbsp;');
					$tpl->assign('meses', $row['meses_restantes'] > 0 ? $row['meses_restantes'] : '&nbsp;');

					$tpl->assign('activo', $row['activo'] == 't' ? ' class="bold"' : '');
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'alta':
			$tpl = new TemplatePower('plantillas/bal/PagosAnticipadosAlta.tpl');
			$tpl->prepare();

			$tpl->assign('anio1', date('Y'));
			$tpl->assign('anio2', date('Y'));

			$sql = "
				SELECT
					mes,
					nombre
				FROM
					meses
				ORDER BY
					mes
			";

			$result = $db->query($sql);

			if ($result)
			{
				foreach ($result as $row)
				{
					$tpl->newBlock('mes1');
					$tpl->assign('mes', $row['mes']);
					$tpl->assign('nombre_mes', utf8_encode($row['nombre']));
					$tpl->assign('selected', $row['mes'] == date('n') ? ' selected="selected"' : '');

					$tpl->newBlock('mes2');
					$tpl->assign('mes', $row['mes']);
					$tpl->assign('nombre_mes', utf8_encode($row['nombre']));
					$tpl->assign('selected', $row['mes'] == date('n') ? ' selected="selected"' : '');
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_alta':
			$sql = "
				INSERT INTO
					pagos_anticipados (
						num_cia,
						importe,
						fecha_ini,
						fecha_fin,
						concepto
					)
					VALUES (
						{$_REQUEST['num_cia']},
						" . get_val($_REQUEST['importe']) . ",
						'" . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes1'], 1, $_REQUEST['anio1'])) . "',
						'" . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes2'] + 1, 0, $_REQUEST['anio2'])) . "',
						'" . utf8_decode($_REQUEST['concepto']) . "'
					)
			";
			
			$db->query($sql);
			
			break;
		
		case 'modificar':
			$sql = '
				SELECT
					pa.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					pa.importe,
					EXTRACT(MONTH FROM pa.fecha_ini)
						AS mes1,
					EXTRACT(YEAR FROM pa.fecha_ini)
						AS anio1,
					EXTRACT(MONTH FROM pa.fecha_fin)
						AS mes2,
					EXTRACT(YEAR FROM pa.fecha_fin)
						AS anio2,
					concepto
				FROM
					pagos_anticipados pa
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					pa.id = ' . $_REQUEST['id'] . '
			';
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/bal/PagosAnticipadosModificar.tpl');
			$tpl->prepare();
			
			$tpl->assign('id', $_REQUEST['id']);
			$tpl->assign('num_cia', $result[0]['num_cia']);
			$tpl->assign('nombre_cia', utf8_encode($result[0]['nombre_cia']));
			$tpl->assign('anio1', $result[0]['anio1']);
			$tpl->assign('anio2', $result[0]['anio2']);
			$tpl->assign('concepto', utf8_encode($result[0]['concepto']));
			$tpl->assign('importe', number_format($result[0]['importe'], 2));

			$sql = "
				SELECT
					mes,
					nombre
				FROM
					meses
				ORDER BY
					mes
			";

			$meses = $db->query($sql);

			if ($meses)
			{
				foreach ($meses as $mes)
				{
					$tpl->newBlock('mes1');
					$tpl->assign('mes', $mes['mes']);
					$tpl->assign('nombre_mes', utf8_encode($mes['nombre']));
					$tpl->assign('selected', $mes['mes'] == $result[0]['mes1'] ? ' selected="selected"' : '');

					$tpl->newBlock('mes2');
					$tpl->assign('mes', $mes['mes']);
					$tpl->assign('nombre_mes', utf8_encode($mes['nombre']));
					$tpl->assign('selected', $mes['mes'] == $result[0]['mes2'] ? ' selected="selected"' : '');
				}
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
		case 'do_modificar':
			$sql = "
				UPDATE
					pagos_anticipados
				SET
					num_cia = {$_REQUEST['num_cia']},
					importe = " . get_val($_REQUEST['importe']) . ",
					fecha_ini = '" . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes1'], 1, $_REQUEST['anio1'])) . "',
					fecha_fin = '" . date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes2'] + 1, 0, $_REQUEST['anio2'])) . "',
					concepto = '" . utf8_decode($_REQUEST['concepto']) . "'
				WHERE
					id = {$_REQUEST['id']}
			";
			
			$db->query($sql);
			
			break;
		
		case 'do_baja':
			$sql = '
				DELETE FROM
					pagos_anticipados
				WHERE
					id = ' . $_REQUEST['id'] . '
			';
			
			$db->query($sql);
			
			break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/bal/PagosAnticipados.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
