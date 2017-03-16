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

		case 'obtener_pro':
			$result = $db->query("SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = {$_REQUEST['num_pro']}");

			if ($result)
			{
				echo utf8_encode($result[0]['nombre']);
			}

			break;

		case 'obtener_cia':
			$result = $db->query("SELECT
				nombre_corto
			FROM
				catalogo_companias
			WHERE
				num_cia = {$_REQUEST['num_cia']}
				AND tipo_cia IN (" . ($_SESSION['tipo_usuario'] == 2 ? '4' : '1, 2, 3, 5, 6') . ")
				AND num_cia NOT IN (SELECT num_cia FROM referencias_bancarias WHERE num_proveedor = {$_REQUEST['num_pro']} AND tsbaja IS NULL)
			");

			if ($result)
			{
				echo utf8_encode($result[0]['nombre_corto']);
			}

			break;

		case 'inicio':
			$tpl = new TemplatePower('plantillas/ban/ReferenciasBancariasCatalogoInicio.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();

			break;

		case 'consultar':
			$condiciones = array();

			$condiciones[] = "ref.num_proveedor = {$_REQUEST['num_pro']}";

			$condiciones[] = "tsbaja IS NULL";

			$sql = "SELECT
				ref.id,
				ref.num_proveedor AS num_pro,
				cp.nombre AS nombre_pro,
				ref.num_cia,
				cc.nombre_corto AS nombre_cia,
				ref.referencia
			FROM
				referencias_bancarias ref
				LEFT JOIN catalogo_proveedores cp USING (num_proveedor)
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				" . implode(' AND ', $condiciones) . "
			ORDER BY
				ref.num_proveedor,
				ref.num_cia";

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ban/ReferenciasBancariasCatalogoConsulta.tpl');
			$tpl->prepare();

			$pro = $db->query("SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = {$_REQUEST['num_pro']}");

			$tpl->assign('num_pro', $_REQUEST['num_pro']);
			$tpl->assign('nombre_pro', utf8_encode($pro[0]['nombre']));

			if ($result) {
				foreach ($result as $row) {
					$tpl->newBlock('row');

					$tpl->assign('id', $row['id']);
					$tpl->assign('num_cia', $row['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
					$tpl->assign('referencia', utf8_encode($row['referencia']));
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'alta':
			$tpl = new TemplatePower('plantillas/ban/ReferenciasBancariasCatalogoAlta.tpl');
			$tpl->prepare();

			$tpl->assign('num_pro', $_REQUEST['num_pro']);

			echo $tpl->getOutputContent();

			break;

		case 'do_alta':
			$sql = "INSERT INTO referencias_bancarias (
				num_cia,
				num_proveedor,
				referencia,
				idalta
			)
			VALUES (
				{$_REQUEST['num_cia']},
				{$_REQUEST['num_pro']},
				'" . utf8_decode($_REQUEST['referencia']) . "',
				{$_SESSION['iduser']}
			)";

			$db->query($sql);

			break;

		case 'modificar':
			$sql = "SELECT
				ref.id,
				ref.num_proveedor AS num_pro,
				ref.num_cia,
				cc.nombre_corto AS nombre_cia,
				ref.num_proveedor AS num_pro,
				cp.nombre AS nombre_pro,
				ref.referencia
			FROM
				referencias_bancarias ref
				LEFT JOIN catalogo_proveedores cp USING (num_proveedor)
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				id = {$_REQUEST['id']}";

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ban/ReferenciasBancariasCatalogoModificar.tpl');
			$tpl->prepare();

			$tpl->assign('id', $result[0]['id']);
			$tpl->assign('num_pro', $result[0]['num_pro']);
			$tpl->assign('num_cia', $result[0]['num_cia']);
			$tpl->assign('nombre_cia', utf8_encode($result[0]['nombre_cia']));
			$tpl->assign('referencia', utf8_encode($result[0]['referencia']));

			echo $tpl->getOutputContent();

			break;

		case 'do_modificar':
			$sql = "UPDATE referencias_bancarias
			SET
				num_cia = {$_REQUEST['num_cia']},
				referencia = '" . utf8_decode($_REQUEST['referencia']) . "',
				idmod = {$_SESSION['iduser']},
				tsmod = NOW()
			WHERE
				id = {$_REQUEST['id']}";

			$db->query($sql);

			break;

		case 'do_baja':
			$db->query("UPDATE referencias_bancarias SET tsbaja = NOW(), idbaja = {$_SESSION['iduser']} WHERE id = {$_REQUEST['id']}");

			break;

		case 'importar':
			ini_set('auto_detect_line_endings', TRUE);

			$data = array_map('str_getcsv', file($_FILES['file']['tmp_name'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));

			$cont = 0;

			foreach ($data as $row)
			{
				if ($row[0] > 0 && $row[1] != '')
				{
					$cia = $db->query("SELECT num_cia FROM catalogo_companias WHERE num_cia = {$row[0]}");

					if ($cia)
					{
						if ($id = $db->query("SELECT id FROM referencias_bancarias WHERE num_proveedor = {$_REQUEST['num_pro']} AND num_cia = {$row[0]} AND tsbaja IS NULL"))
						{
							$db->query("UPDATE referencias_bancarias SET referencia = '" . substr($row[1], 0, 10) . "', tsmod = NOW(), idmod = {$_SESSION['iduser']} WHERE id = {$id[0]['id']}");
						}
						else
						{
							$db->query("INSERT INTO referencias_bancarias (num_proveedor, num_cia, referencia, idalta) VALUES ({$_REQUEST['num_pro']}, {$row[0]}, '" . substr($row[1], 0, 10) . "', {$_SESSION['iduser']})");
						}

						$cont++;
					}
				}
			}

			header('Content-Type: application/json');
			echo json_encode(array(
				'status'		=> 1,
				'importados'	=> $cont
			));

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/ReferenciasBancariasCatalogo.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
