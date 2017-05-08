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
			$tpl = new TemplatePower('plantillas/fac/TanquesGasInicio.tpl');
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
					$condiciones[] = 'tg.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			$sql = "
				SELECT
					tg.id,
					tg.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					tg.num_tanque,
					tg.nombre,
					tg.capacidad
				FROM
					catalogo_tanques tg
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				" . ($condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '') . "
				ORDER BY
					tg.num_cia,
					tg.num_tanque,
					tg.nombre
			";

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/fac/TanquesGasConsulta.tpl');
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
					$tpl->assign('num', $row['num_tanque']);
					$tpl->assign('nombre', utf8_encode($row['nombre']));
					$tpl->assign('capacidad', number_format($row['capacidad'], 2));
				}
			}

			echo $tpl->getOutputContent();

			break;

		case 'alta':
			$tpl = new TemplatePower('plantillas/fac/TanquesGasAlta.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();

			break;

		case 'do_alta':
			$sql = "
				INSERT INTO
					catalogo_tanques (
						num_cia,
						num_tanque,
						capacidad,
						nombre
					)
					VALUES (
						{$_REQUEST['num_cia']},
						" . get_val($_REQUEST['num_tanque']) . ",
						" . get_val($_REQUEST['capacidad']) . ",
						'" . utf8_decode($_REQUEST['nombre']) . "'
					);
			";

			$sql .= "
				INSERT INTO
					actualizacion_panas (
						num_cia,
						metodo,
						parametros,
						iduser
					)
					VALUES (
						(SELECT num_cia_ros FROM catalogo_companias WHERE num_cia = {$_REQUEST['num_cia']}),
						'actualizar_tanques_gas',
						'num_cia=' || (SELECT num_cia_ros FROM catalogo_companias WHERE num_cia = {$_REQUEST['num_cia']}),
						{$_SESSION['iduser']}
					);
			";

			$db->query($sql);

			break;

		case 'modificar':
			$sql = '
				SELECT
					tg.id,
					tg.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					tg.num_tanque,
					tg.nombre,
					tg.capacidad
				FROM
					catalogo_tanques tg
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					tg.id = ' . $_REQUEST['id'] . '
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/fac/TanquesGasModificar.tpl');
			$tpl->prepare();

			$tpl->assign('id', $_REQUEST['id']);
			$tpl->assign('num_cia', $result[0]['num_cia']);
			$tpl->assign('nombre_cia', utf8_encode($result[0]['nombre_cia']));
			$tpl->assign('num_tanque', $result[0]['num_tanque']);
			$tpl->assign('nombre', utf8_encode($result[0]['nombre']));
			$tpl->assign('capacidad', number_format($result[0]['capacidad'], 2));

			echo $tpl->getOutputContent();

			break;

		case 'do_modificar':
			$sql = "
				UPDATE
					catalogo_tanques
				SET
					num_cia = {$_REQUEST['num_cia']},
					num_tanque = " . get_val($_REQUEST['num_tanque']) . ",
					nombre = '" . utf8_decode($_REQUEST['nombre']) . "',
					capacidad = " . get_val($_REQUEST['capacidad']) . "
				WHERE
					id = {$_REQUEST['id']};
			";

			$sql .= "
				INSERT INTO
					actualizacion_panas (
						num_cia,
						metodo,
						parametros,
						iduser
					)
					VALUES (
						(SELECT num_cia_ros FROM catalogo_companias WHERE num_cia = {$_REQUEST['num_cia']}),
						'actualizar_tanques_gas',
						'num_cia=' || (SELECT num_cia_ros FROM catalogo_companias WHERE num_cia = {$_REQUEST['num_cia']}),
						{$_SESSION['iduser']}
					);
			";

			$db->query($sql);

			break;

		case 'do_baja':
			$num_cia = $db->query("SELECT num_cia FROM catalogo_tanques WHERE id = {$_REQUEST['id']}");

			$sql = '
				DELETE FROM
					catalogo_tanques
				WHERE
					id = ' . $_REQUEST['id'] . ';
			';

			$sql .= "
				INSERT INTO
					actualizacion_panas (
						num_cia,
						metodo,
						parametros,
						iduser
					)
					VALUES (
						(SELECT num_cia_ros FROM catalogo_companias WHERE num_cia = {$num_cia[0]['num_cia']}),
						'actualizar_tanques_gas',
						'num_cia=' || (SELECT num_cia_ros FROM catalogo_companias WHERE num_cia = {$num_cia[0]['num_cia']}),
						{$_SESSION['iduser']}
					);
			";

			$db->query($sql);

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/TanquesGas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
