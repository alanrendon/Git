<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/class.auxinv.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'cia':
			$sql = '
				SELECT
					nombre_corto
				FROM
						catalogo_companias
					LEFT JOIN
						catalogo_operadoras
							USING
								(
									idoperadora
								)
				WHERE
						num_cia <= 300
					AND
						num_cia = ' . $_REQUEST['num_cia'] . '
			';

			if (!in_array($_SESSION['iduser'], array(1, 4, 62))) {
				$sql .= '
					AND
						iduser = ' . $_SESSION['iduser'] . '
				';
			}

			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode($result[0]['nombre_corto']);
			}
		break;

		case 'validarExp':
			$sql = '
				SELECT
					num_expendio,
					nombre
				FROM
					catalogo_expendios
				WHERE
						num_cia = ' . $_REQUEST['num_cia'] . '
					AND
						num_expendio = ' . $_REQUEST['num_expendio'] . '
			';
			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode('Ya existe un expendio con el número ' . $_REQUEST['num_expendio'] . ' llamado "' . $result[0]['nombre'] . '"');
			}
		break;

		case 'validarRef':
			$sql = '
				SELECT
					num_expendio,
					num_referencia,
					nombre
				FROM
					catalogo_expendios
				WHERE
						num_cia = ' . $_REQUEST['num_cia'] . '
					AND
						num_referencia = ' . $_REQUEST['num_referencia'] . '
			';
			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode('Ya existe un expendio con el número de referencia ' . $_REQUEST['num_referencia'] . ' llamado "' . $result[0]['num_expendio'] . ' ' . $result[0]['nombre'] . '"');
			}
		break;

		case 'alta':
			$sql = '
				INSERT INTO
					catalogo_expendios
						(
							num_cia,
							num_expendio,
							num_referencia,
							nombre,
							tipo_expendio,
							direccion,
							porciento_ganancia,
							importe_fijo,
							total_fijo,
							notas,
							aut_dev,
							tipo_devolucion,
							devolucion_maxima,
							idagven,
							num_cia_exp,
							paga_renta,
							devolucion_fin_mes
						)
				VALUES
					(
						' . $_REQUEST['num_cia'] . ',
						' . $_REQUEST['num_expendio'] . ',
						' . (isset($_REQUEST['num_referencia']) ? $_REQUEST['num_referencia'] : 'NULL') . ',
						\'' . $_REQUEST['nombre'] . '\',
						' . $_REQUEST['tipo'] . ',
						' . (isset($_REQUEST['direccion']) ? '\'' . $_REQUEST['direccion'] . '\'' : 'NULL') . ',
						' . (isset($_REQUEST['porciento_ganancia']) ? $_REQUEST['porciento_ganancia'] : 0) . ',
						' . (isset($_REQUEST['importe_fijo']) && get_val($_REQUEST['importe_fijo']) > 0 ? get_val($_REQUEST['importe_fijo']) : 'NULL') . ',
						\'' . (isset($_REQUEST['total_fijo']) ? 'TRUE' : 'FALSE') . '\',
						\'' . (isset($_REQUEST['notas']) ? 'TRUE' : 'FALSE') . '\',
						' . (isset($_REQUEST['aut_dev']) ? 'TRUE' : 'FALSE') . ',
						' . (isset($_REQUEST['aut_dev']) ? $_REQUEST['tipo_devolucion'] : '0') . ',
						' . (isset($_REQUEST['aut_dev']) ? get_val($_REQUEST['devolucion_maxima']) : '0') . ',
						' . (isset($_REQUEST['idagven']) && $_REQUEST['idagven'] > 0 ? $_REQUEST['idagven'] : 'NULL') . ',
						' . (isset($_REQUEST['num_cia_exp']) && $_REQUEST['num_cia_exp'] > 0 ? $_REQUEST['num_cia_exp'] : 'NULL') . ',
						' . (isset($_REQUEST['paga_renta']) && $_REQUEST['paga_renta'] != '' ? $_REQUEST['paga_renta'] : 'NULL') . ',
						' . (isset($_REQUEST['devolucion_fin_mes']) ? 'TRUE' : 'FALSE') . '
					)
			' . ";\n";

			// [02-Mar-2014] Guardar movimiento en la tabla de modificaciones de panaderias
			$sql .= "
				INSERT INTO
					actualizacion_panas (
						num_cia,
						iduser,
						metodo,
						parametros
					)
					VALUES (
						{$_REQUEST['num_cia']},
						{$_SESSION['iduser']},
						'alta_expendio',
						'num_cia={$_REQUEST['num_cia']}&num_expendio={$_REQUEST['num_expendio']}'
					);\n
			";

			$db->query($sql);

			echo 'Registrado nuevo expendio "' . $_REQUEST['num_expendio'] . ' ' . $_REQUEST['nombre'] . '"';
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/pan/AltaExpendio.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

if (!in_array($_SESSION['iduser'], array(1, 4))) {
	$tpl->assign('readonly', ' readonly');
	$tpl->assign('disabled', ' disabled');
	$tpl->assign('leyenda', ' <span style="color:#C00;font-weight:bold;font-size:6pt;">(Autorizado por Miguel Rebuelta)</span>');
}

$sql = '
	SELECT
		idtipoexpendio
			AS
				id,
		descripcion
			AS
				nombre
	FROM
		tipo_expendio
	ORDER BY
		id
';
$tipos = $db->query($sql);

foreach ($tipos as $t) {
	$tpl->newBlock('tipo');
	$tpl->assign('id', $t['id']);
	$tpl->assign('nombre', $t['nombre']);
}

$sql = '
	SELECT
		idagven
			AS
				id,
		nombre
	FROM
		catalogo_agentes_venta
	ORDER BY
		nombre
';
$agentes = $db->query($sql);

foreach ($agentes as $a) {
	$tpl->newBlock('agente');
	$tpl->assign('id', $a['id']);
	$tpl->assign('nombre', $a['nombre']);
}

$tpl->printToScreen();
?>
