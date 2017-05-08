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
			$tpl = new TemplatePower('plantillas/bal/GasReporteLecturasInicio.tpl');
			$tpl->prepare();

			$tpl->assign('anio', date('Y'));
			$tpl->assign(date('n'), ' selected="selected"');

			$admins = $db->query("SELECT idadministrador AS value, nombre_administrador AS text FROM catalogo_administradores ORDER BY text");

			if ($admins)
			{
				foreach ($admins as $a) {
					$tpl->newBlock('admin');
					$tpl->assign('value', $a['value']);
					$tpl->assign('text', utf8_encode($a['text']));
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'desglosado':
			$anio = isset($_REQUEST['anio']) && $_REQUEST['anio'] > 0 ? $_REQUEST['anio'] : date('Y');
			$mes = isset($_REQUEST['mes']) && $_REQUEST['mes'] > 0 ? $_REQUEST['mes'] : date('n');

			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anio));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anio));

			$condiciones = array();

			$condiciones[] = "tgl.fecha BETWEEN '{$fecha1}' AND '$fecha2'";

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
					$condiciones[] = 'ct.num_cia_saldos IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			$sql = "
				SELECT
					ct.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					ct.num_tanque,
					ct.nombre
						AS nombre_tanque,
					tgl.fecha,
					EXTRACT(DAY FROM tgl.fecha)
						AS dia,
					COALESCE((
						SELECT
							SUM(cantidad)
						FROM
							tanques_gas_lecturas_tmp
						WHERE
							idtanque = tgl.idtanque
							AND fecha = tgl.fecha - INTERVAL '1 DAY'
					), 0)
						AS lectura_ayer,
					tgl.cantidad
						AS lectura_hoy,
					COALESCE((
						SELECT
							SUM(cantidad)
						FROM
							tanques_gas_entradas_tmp
						WHERE
							idtanque = tgl.idtanque
							AND fecha = tgl.fecha
					), 0)
						AS entrada,
					COALESCE((
						SELECT
							SUM(total_produccion)
						FROM
							total_produccion
						WHERE
							numcia = ct.num_cia
							AND fecha_total = tgl.fecha
					), 0)
						AS produccion
				FROM
					tanques_gas_lecturas_tmp tgl
					LEFT JOIN catalogo_tanques ct
						ON (ct.id = tgl.idtanque)
					LEFT JOIN catalogo_companias cc
						ON (cc.num_cia = ct.num_cia)
				WHERE
					" . implode(' AND ', $condiciones) . "
				ORDER BY
					ct.num_cia,
					ct.num_tanque,
					tgl.fecha
			";

			$result = $db->query($sql);

			if ($result)
			{
				$tpl = new TemplatePower('plantillas/bal/GasReporteLecturasConsultaDesglose.tpl');
				$tpl->prepare();

				$tpl->assign('anio', $anio);
				$tpl->assign('mes', $_meses[$mes]);

				$num_cia = NULL;

				foreach ($result as $row)
				{
					if ($row['num_cia'] == '')
					{
						continue;
					}

					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$tpl->newBlock('cia');

						$tpl->assign('num_cia', $row['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));

						$tanque = NULL;
					}

					if ($tanque != $row['num_tanque'])
					{
						$tanque = $row['num_tanque'];

						$tpl->newBlock('tanque');

						$tpl->assign('num_tanque', $row['num_tanque']);
						$tpl->assign('nombre_tanque', utf8_encode($row['nombre_tanque']));
					}

					$consumo = $row['lectura_ayer'] >= $row['lectura_hoy'] - $row['entrada'] ? $row['lectura_ayer'] - ($row['lectura_hoy'] - $row['entrada']) : -1;

					$porc = $consumo > 0 ? $row['produccion'] / $consumo : 0;

					$tpl->newBlock('row');

					$tpl->assign('dia', $row['dia']);
					$tpl->assign('lectura_inicial', $row['lectura_ayer'] != 0 ? number_format($row['lectura_ayer']) : '&nbsp;');
					$tpl->assign('carga', $row['entrada'] != 0 ? number_format($row['entrada']) : '&nbsp;');
					$tpl->assign('consumo', $consumo >= 0 ? number_format($consumo) : '----');
					$tpl->assign('produccion', $row['produccion'] != 0 ? number_format($row['produccion']) : '&nbsp;');
					$tpl->assign('lectura_final', $row['lectura_hoy'] != 0 ? number_format($row['lectura_hoy']) : '&nbsp;');
					$tpl->assign('produccion', $row['produccion'] != 0 ? number_format($row['produccion']) : '&nbsp;');
					$tpl->assign('pro_con', $porc != 0 ? number_format($porc, 4) : '&nbsp;');
				}

				echo $tpl->getOutputContent();
			}

			break;

		case 'totales':
			$anio = isset($_REQUEST['anio']) && $_REQUEST['anio'] > 0 ? $_REQUEST['anio'] : date('Y');
			$mes = isset($_REQUEST['mes']) && $_REQUEST['mes'] > 0 ? $_REQUEST['mes'] : date('n');

			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes, 1, $anio));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $anio));

			$condiciones = array();

			$condiciones[] = "tgl.fecha BETWEEN '{$fecha1}' AND '$fecha2'";

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
					$condiciones[] = 'ct.num_cia_saldos IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			$sql = "
				SELECT
					ct.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					ct.num_tanque,
					ct.nombre
						AS nombre_tanque,
					tgl.fecha,
					EXTRACT(DAY FROM tgl.fecha)
						AS dia,
					COALESCE((
						SELECT
							SUM(cantidad)
						FROM
							tanques_gas_lecturas_tmp
						WHERE
							idtanque = tgl.idtanque
							AND fecha = tgl.fecha - INTERVAL '1 DAY'
					), 0)
						AS lectura_ayer,
					tgl.cantidad
						AS lectura_hoy,
					COALESCE((
						SELECT
							SUM(cantidad)
						FROM
							tanques_gas_entradas_tmp
						WHERE
							idtanque = tgl.idtanque
							AND fecha = tgl.fecha
					), 0)
						AS entrada,
					COALESCE((
						SELECT
							SUM(total_produccion)
						FROM
							total_produccion
						WHERE
							numcia = ct.num_cia
							AND fecha_total = tgl.fecha
					), 0)
						AS produccion
				FROM
					tanques_gas_lecturas_tmp tgl
					LEFT JOIN catalogo_tanques ct
						ON (ct.id = tgl.idtanque)
					LEFT JOIN catalogo_companias cc
						ON (cc.num_cia = ct.num_cia)
				WHERE
					" . implode(' AND ', $condiciones) . "
				ORDER BY
					ct.num_cia,
					ct.num_tanque,
					tgl.fecha
			";

			$result = $db->query($sql);

			if ($result)
			{
				$tpl = new TemplatePower('plantillas/bal/GasReporteLecturasConsultaTotales.tpl');
				$tpl->prepare();

				$tpl->assign('anio', $anio);
				$tpl->assign('mes', $_meses[$mes]);

				$num_cia = NULL;

				foreach ($result as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$tpl->newBlock('cia');

						$tpl->assign('num_cia', $row['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));

						$tanque = NULL;
					}

					if ($tanque != $row['num_tanque'])
					{
						$tanque = $row['num_tanque'];

						$tpl->newBlock('tanque');

						$tpl->assign('num_tanque', $row['num_tanque']);
						$tpl->assign('nombre_tanque', utf8_encode($row['nombre_tanque']));
					}

					$consumo = $row['lectura_ayer'] >= $row['lectura_hoy'] - $row['entrada'] ? $row['lectura_ayer'] - ($row['lectura_hoy'] - $row['entrada']) : -1;

					$tpl->newBlock('row');

					$tpl->assign('dia', $row['dia']);
					$tpl->assign('lectura_inicial', $row['lectura_ayer'] != 0 ? number_format($row['lectura_ayer']) : '&nbsp;');
					$tpl->assign('carga', $row['entrada'] != 0 ? number_format($row['entrada']) : '&nbsp;');
					$tpl->assign('consumo', $consumo >= 0 ? number_format($consumo) : '----');
					$tpl->assign('produccion', $row['produccion'] != 0 ? number_format($row['produccion']) : '&nbsp;');
					$tpl->assign('lectura_final', $row['lectura_hoy'] != 0 ? number_format($row['lectura_hoy']) : '&nbsp;');
				}

				echo $tpl->getOutputContent();
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/bal/GasReporteLecturas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
