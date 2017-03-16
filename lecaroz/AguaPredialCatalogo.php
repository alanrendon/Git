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
			$tpl = new TemplatePower('plantillas/fac/AguaPredialCatalogoInicio.tpl');
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

		case 'obtener_pro':
			$sql = '
				SELECT
					nombre
				FROM
					catalogo_proveedores
				WHERE
					num_proveedor = ' . $_REQUEST['num_pro'] . '
			';

			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode($result[0]['nombre']);
			}

			break;

		case 'obtener_arrendador':
			$sql = '
				SELECT
					idarrendador,
					arrendador,
					nombre_arrendador
				FROM
					rentas_arrendadores
				WHERE
					arrendador = ' . $_REQUEST['arrendador'] . '
					AND tsdel IS NULL
			';

			$result = $db->query($sql);

			if ($result) {
				echo json_encode(array(
					'id'			=> intval($result[0]['idarrendador']),
					'arrendador'	=> intval($result[0]['arrendador']),
					'nombre'		=> utf8_encode($result[0]['nombre_arrendador'])
				));
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
					$condiciones[] = 'cc.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			$sql = "
				SELECT
					cat.id,
					cat.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					CASE
						WHEN tipo = 1 THEN
							'AGUA'
						WHEN tipo = 2 THEN
							'PREDIAL'
					END
						AS tipo,
					cat.num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					cat.cuenta,
					CASE
						WHEN recurrencia = 1 THEN
							'MENSUAL'
						WHEN recurrencia = 2 THEN
							'BIMESTRAL'
						WHEN recurrencia = 3 THEN
							'ANUAL'
					END
						AS recurrencia,
					arr.arrendador,
					arr.nombre_arrendador,
					cat.observaciones
				FROM
					catalogo_agua_predial cat
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN rentas_arrendadores arr
						USING (idarrendador)

				" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
				ORDER BY
					cat.num_cia,
					cat.tipo,
					cat.num_proveedor
			";

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/fac/AguaPredialCatalogoConsulta.tpl');
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

						$tpl->assign('num_cia', $num_cia);
						$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));
					}

					$tpl->newBlock('row');

					$tpl->assign('id', $row['id']);
					$tpl->assign('tipo', utf8_encode($row['tipo']));
					$tpl->assign('num_pro', $row['num_pro']);
					$tpl->assign('nombre_pro', utf8_encode($row['nombre_pro']));
					$tpl->assign('cuenta', utf8_encode($row['cuenta']));
					$tpl->assign('recurrencia', utf8_encode($row['recurrencia']));
					$tpl->assign('arrendador', $row['arrendador']);
					$tpl->assign('nombre_arrendador', utf8_encode($row['nombre_arrendador']));
					$tpl->assign('observaciones', utf8_encode($row['observaciones']));
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'alta':
			$tpl = new TemplatePower('plantillas/fac/AguaPredialCatalogoAlta.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();

			break;

		case 'do_alta':
			$sql = "
				INSERT INTO
					catalogo_agua_predial (
						num_cia,
						tipo,
						num_proveedor,
						cuenta,
						recurrencia,
						idarrendador,
						observaciones,
						idalta
					)
					VALUES (
						{$_REQUEST['num_cia']},
						{$_REQUEST['tipo']},
						{$_REQUEST['num_pro']},
						'" . utf8_decode($_REQUEST['cuenta']) . "',
						{$_REQUEST['recurrencia']},
						{$_REQUEST['idarrendador']},
						'" . utf8_decode($_REQUEST['observaciones']) . "',
						{$_SESSION['iduser']}
					);
			";

			$db->query($sql);

			header('Content-Type: application/json');

			echo json_encode(array(
				'status'	=> 1
			));

			return FALSE;

			break;

		case 'modificar':
			$sql = "
				SELECT
					cat.id,
					cat.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					cat.tipo,
					cat.num_proveedor
						AS num_pro,
					cp.nombre
						AS nombre_pro,
					cat.cuenta,
					cat.recurrencia,
					cat.idarrendador,
					arr.arrendador,
					arr.nombre_arrendador,
					cat.observaciones
				FROM
					catalogo_agua_predial cat
					LEFT JOIN catalogo_proveedores cp
						USING (num_proveedor)
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN rentas_arrendadores arr
						USING (idarrendador)

				WHERE
					cat.id = {$_REQUEST['id']}
				ORDER BY
					cat.num_cia,
					cat.tipo,
					cat.num_proveedor
			";

			$result = $db->query($sql);

			$row = $result[0];

			$tpl = new TemplatePower('plantillas/fac/AguaPredialCatalogoModificar.tpl');
			$tpl->prepare();

			$tpl->assign('id', $row['id']);

			$tpl->assign('num_cia', $row['num_cia']);
			$tpl->assign('nombre_cia', utf8_encode($row['nombre_cia']));

			$tpl->assign('tipo_' . $row['tipo'], ' checked="checked"');

			$tpl->assign('num_pro', $row['num_pro']);
			$tpl->assign('nombre_pro', utf8_encode($row['nombre_pro']));

			$tpl->assign('cuenta', utf8_encode($row['cuenta']));

			$tpl->assign('recurrencia_' . $row['recurrencia'], ' checked="checked"');

			$tpl->assign('idarrendador', $row['idarrendador']);
			$tpl->assign('arrendador', $row['arrendador']);
			$tpl->assign('nombre_arrendador', utf8_encode($row['nombre_arrendador']));

			$tpl->assign('observaciones', utf8_encode($row['observaciones']));

			echo $tpl->getOutputContent();

			break;

		case 'do_modificar':
			$sql = "
				UPDATE
					catalogo_agua_predial
				SET
					num_cia = {$_REQUEST['num_cia']},
					tipo = {$_REQUEST['tipo']},
					num_proveedor = {$_REQUEST['num_pro']},
					cuenta = '" . utf8_decode($_REQUEST['cuenta']) . "',
					recurrencia = {$_REQUEST['recurrencia']},
					idarrendador = {$_REQUEST['idarrendador']},
					observaciones = '" . utf8_decode($_REQUEST['observaciones']) . "',
					tsmod = NOW(),
					idmod = {$_SESSION['iduser']}
				WHERE
					id = {$_REQUEST['id']};
			";

			$db->query($sql);

			break;

		case 'do_baja':
			$sql = '
				DELETE FROM
					catalogo_tanques
				WHERE
					id = ' . $_REQUEST['id'] . '
			';

			$db->query($sql);

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/AguaPredialCatalogo.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
