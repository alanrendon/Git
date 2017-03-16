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
			WHERE
				num_cia = ' . $_POST['num_cia'] . '
		';
		$result = $db->query($sql);
		
		echo $result[0]['nombre_corto'];
	}
	
	die;
}

if (isset($_POST['num_cia'])) {
	$sql = '';
	
	foreach ($_POST['fecha_caducidad'] as $fecha)
		if (ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $fecha))
			$sql .= '
				INSERT INTO
					caducidad_extintores
						(
							num_cia,
							fecha_caducidad,
							iduser
						)
				VALUES
						(
							' . $_POST['num_cia'] . ',
							\'' . $fecha . '\',
							' . $_SESSION['iduser'] . '
						)
			' . ";\n";
	
	if (trim($sql) != '')
		$db->query($sql);
	
	header('location: AltaExtintores.php');
	die;
}

$tpl = new TemplatePower('plantillas/fac/AltaExtintores.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>