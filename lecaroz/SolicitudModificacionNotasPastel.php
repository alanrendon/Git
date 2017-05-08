<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_POST['accion'])) {
	if ($_POST['accion'] == 'cia') {
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
					num_cia = ' . $_POST['num_cia'] . '
		';
		if (!in_array($_SESSION['iduser'], array(1, 4, 19)))
			$sql .= '
				AND
					iduser = ' . $_SESSION['iduser'] . '
			';
		$result = $db->query($sql);
		
		echo $result[0]['nombre_corto'];
	}
	else if ($_POST['accion'] == 'validar') {
		$sql = '
			SELECT
				id
			FROM
				venta_pastel
			WHERE
					num_cia = ' . $_POST['num_cia'] . '
				AND
					num_remi = ' . $_POST['num_remi'] . '
				AND
					letra_folio = \'' . ($_POST['letra_folio'] != '' ? $_POST['letra_folio'] : 'X') . '\'
		';
		$result = $db->query($sql);
		
		if (!$result)
			echo -1;
	}
	
	die;
}

if (isset($_POST['num_cia'])) {
	$sql = '';
	
	for ($i = 0, $len = count($_POST['num_cia']); $i < $len; $i++)
		if ($_POST['num_cia'][$i] > 0 && $_POST['num_remi'][$i] > 0 && $_POST['descripcion'][$i] != '' && (isset($_POST['mod_' . $i]) || (isset($_POST['kilos_' . $i]) || isset($_POST['precio']) || isset($_POST['base'])))) {
			$sql .= '
				INSERT INTO
					solicitudes_modificacion_pastel
						(
							num_cia,
							letra_folio,
							num_remi,
							descripcion,
							iduser_solicitud,
							ts_solicitud,
							modificar,
							kilos,
							precio,
							base
						)
				VALUES
						(
							' . $_POST['num_cia'][$i] . ',
							\'' . ($_POST['letra_folio'][$i] != '' ? $_POST['letra_folio'][$i] : 'X') . '\',
							' . $_POST['num_remi'][$i] . ',
							\'' . $_POST['descripcion'][$i] . '\',
							' . $_SESSION['iduser'] . ',
							now(),
							' . (isset($_POST['mod_' . $i]) ? $_POST['mod_' . $i] : 1) . ',
							' . (isset($_POST['kilos_' . $i]) ? $_POST['kilos_' . $i] : 0) . ',
							\'' . (isset($_POST['precio_' . $i]) ? 'TRUE' : 'FALSE') . '\',
							\'' . (isset($_POST['base_' . $i]) ? 'TRUE' : 'FALSE') . '\'
						)
			' . ";\n";
		}
	$db->query($sql);
	
	header('location: SolicitudModificacionNotasPastel.php');
	die;
}

$tpl = new TemplatePower('plantillas/pan/SolicitudModificacionNotasPastel.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>