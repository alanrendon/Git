<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value)
{
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

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{

		case 'inicio':
			$tpl = new TemplatePower('plantillas/rh/TrabajadoresBajasConsultaInicio.tpl');
			$tpl->prepare();

			$fecha1 = date('j') <= 5 ? date('01/m/Y', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('01/m/Y');
			$fecha2 = date('j') <= 5 ? date('d/m/Y', mktime(0, 0, 0, date('n'), 0, date('Y'))) : date('d/m/Y');

			$tpl->assign('fecha1', $fecha1);
			$tpl->assign('fecha2', $fecha2);

			echo $tpl->getOutputContent();

			break;

		case 'consultar':
			$condiciones = array();

			$condiciones[] = "ct.fecha_baja IS NOT NULL";

			// Intervalo de compañías
			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '')
			{
				$cias = array();

				$pieces = explode(',', $_REQUEST['cias']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}

				if (count($cias) > 0)
				{
					$condiciones[] = 'ct.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}


			// Intervalo de folios
			if (isset($_REQUEST['folios']) && trim($_REQUEST['folios']) != '')
			{
				$folios = array();

				$pieces = explode(',', $_REQUEST['folios']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$folios[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$folios[] = $piece;
					}
				}

				if (count($folios) > 0)
				{
					$condiciones[] = 'bt.folio IN (' . implode(', ', $folios) . ')';
				}
			}


			// Periodo

			if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') || (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != ''))
			{
				if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != '') && (isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != ''))
				{
					$condiciones[] = "ct.fecha_baja BETWEEN '{$_REQUEST['fecha1']}' AND '{$_REQUEST['fecha2']}'";
				}
				else if ((isset($_REQUEST['fecha1']) && $_REQUEST['fecha1'] != ''))
				{
					$condiciones[] = "ct.fecha_baja >= '{$_REQUEST['fecha1']}'";
				}
				else if ((isset($_REQUEST['fecha2']) && $_REQUEST['fecha2'] != ''))
				{
					$condiciones[] = "ct.fecha_baja = '{$_REQUEST['fecha2']}'";
				}
			}

			$sql = "SELECT
				bt.folio,
				ct.id,
				ct.num_emp,
				ct.num_cia,
				cc.nombre AS nombre_cia,
				ct.nombre_completo AS nombre_trabajador,
				ct.rfc,
				puestos.descripcion AS puesto,
				turnos.descripcion AS turno,
				CASE
					WHEN num_afiliacion IS NOT NULL AND TRIM(num_afiliacion) <> '' THEN
						TRUE
					ELSE
						FALSE
				END AS afiliado,
				fecha_baja,
				fecha_baja_imss,
				COALESCE((
					SELECT
						SUM(
							CASE
								WHEN tipo_mov = FALSE THEN
									importe
								ELSE
									-importe
							END
						)
					FROM
						prestamos
					WHERE
						id_empleado = ct.id
						AND pagado = FALSE
				), 0) AS saldo,
				auth.username AS usuario
			FROM
				catalogo_trabajadores ct
				LEFT JOIN catalogo_puestos puestos USING (cod_puestos)
				LEFT JOIN catalogo_turnos turnos USING (cod_turno)
				LEFT JOIN catalogo_companias cc USING (num_cia)
				LEFT JOIN bajas_trabajadores bt ON (bt.id_empleado = ct.id)
				LEFT JOIN auth ON (auth.iduser = ct.idbaja)
			WHERE
				" . implode(' AND ', $condiciones) . "
			ORDER BY
				num_cia,
				nombre_trabajador";

			$query = $db->query($sql);

			if ($query)
			{
				$tpl = new TemplatePower('plantillas/rh/TrabajadoresBajasConsultaResultado.tpl');
				$tpl->prepare();

				$total = 0;

				$num_cia = NULL;

				foreach ($query as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$tpl->newBlock('cia');

						$tpl->assign('num_cia', $row['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
					}

					$tpl->newBlock('row');

					$tpl->assign('folio', $row['folio'] > 0 ? $row['folio'] : '&nbsp;');
					$tpl->assign('num_emp', $row['num_emp']);
					$tpl->assign('nombre_trabajador', utf8_encode($row['nombre_trabajador']));
					$tpl->assign('rfc', utf8_encode($row['rfc']));
					$tpl->assign('puesto', utf8_encode($row['puesto']));
					$tpl->assign('turno', utf8_encode($row['turno']));
					$tpl->assign('afiliado', $row['afiliado'] == 't' ? '<img src="/lecaroz/iconos/accept.png">' : '&nbsp;');
					$tpl->assign('saldo', $row['saldo'] != 0 ? number_format($row['saldo'], 2) : '&nbsp;');
					$tpl->assign('fecha_baja', $row['fecha_baja'] != '' ? $row['fecha_baja'] : '&nbsp;');
					$tpl->assign('fecha_baja_imss', $row['fecha_baja_imss'] != '' ? $row['fecha_baja_imss'] : '&nbsp;');
					$tpl->assign('usuario', utf8_encode($row['usuario']));
				}

				echo $tpl->getOutputContent();
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/rh/TrabajadoresBajasConsulta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha_cancelacion', date('d/m/Y'));

$tpl->printToScreen();
