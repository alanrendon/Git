<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value)
{
	return intval($value, 10);
}

function toNumberFormat($value)
{
	return number_format($value, 2);
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
			$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasVentaDiariaAutomaticoV2Inicio.tpl');
			$tpl->prepare();

			$tpl->assign('fecha_corte', date('d/m/Y', mktime(0, 0, 0, date('n'), date('j') - 2, date('Y'))));

			$admins = $db->query("SELECT
				idadministrador AS value,
				nombre_administrador AS text
			FROM
				catalogo_administradores
			ORDER BY
				text");

			if ($admins)
			{
				foreach ($admins as $a)
				{
					$tpl->newBlock('admin');

					$tpl->assign('value', $a['value']);
					$tpl->assign('text', utf8_encode($a['text']));
				}
			}

			if ($query = $db->query("SELECT diferencia_maxima FROM maxima_diferencia_efectivo WHERE tsbaja IS NULL ORDER BY id DESC LIMIT 1"))
			{
				$diferencia_maxima = $query[0]['diferencia_maxima'];
			}
			else
			{
				$db->query("INSERT INTO maxima_diferencia_efectivo (diferencia_maxima, idalta) VALUES (0, {$_SESSION['iduser']})");

				$diferencia_maxima = 0;
			}

			$tpl->assign('_ROOT.diferencia_maxima', number_format($diferencia_maxima, 2));

			echo $tpl->getOutputContent();

			break;

		case 'generar':
			$diferencia_maxima = get_val($_REQUEST['diferencia_maxima']);

			$result = $db->query("SELECT id, diferencia_maxima FROM maxima_diferencia_efectivo WHERE tsbaja IS NULL ORDER BY id DESC LIMIT 1");

			if ($diferencia_maxima != $result[0]['diferencia_maxima'])
			{
				$db->query("UPDATE maxima_diferencia_efectivo SET tsbaja = NOW(), idbaja = {$_SESSION['iduser']} WHERE id = {$result[0]['id']}");
				$db->query("INSERT INTO maxima_diferencia_efectivo (diferencia_maxima, idalta) VALUES ({$diferencia_maxima}, {$_SESSION['iduser']})");
			}

			$params = array();

			$params[] = "--fecha_corte={$_REQUEST['fecha_corte']}";
			$params[] = "--dif_max={$diferencia_maxima}";
			$params[] = "--iduser={$_SESSION['iduser']}";

			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '')
			{
				$params[] = "--cias={$_REQUEST['cias']}";
			}

			if (isset($_REQUEST['omitir']) && trim($_REQUEST['omitir']) != '')
			{
				$params[] = "--omitir={$_REQUEST['omitir']}";
			}

			if (isset($_REQUEST['admin']) && trim($_REQUEST['admin']) > 0)
			{
				$params[] = "--admin={$_REQUEST['admin']}";
			}

			exec("php FacturadorVentaDiariaAutomatico.php " . implode(' ', $params) . " > tmp/facturas-venta-diaria-`date \"+%Y%m%d%H%M%S\"`.log 2>&1 &");

			break;

		case 'estatus_bloqueo':
			if ($db->query("SELECT * FROM facturas_electronicas_status_automatico"))
			{
				echo 1;
			}

			break;

		case 'obtener_mensajes':
			if ($result = $db->query("SELECT * FROM facturas_electronicas_mensajes"))
			{
				echo nl2br($result[0]['mensaje']);
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturasElectronicasVentaDiariaAutomaticoV2.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
