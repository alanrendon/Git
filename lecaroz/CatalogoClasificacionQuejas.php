<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

$text = array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'ñ');
$html = array('&Aacute;', '&Eacute;', '&Iacute;', '&Oacute;', '&Uacute;', '&Ntilde;', '&Ntilde;');

if (isset($_POST['accion'])) {
	if ($_POST['accion'] == 'insert') {
		$sql = '
			INSERT INTO
				catalogo_clasificacion_quejas
					(
						concepto
					)
			VALUES
					(
						\'' . $_POST['concepto'] . '\'
					)
		';
		$db->query($sql);
		
		$sql = '
			SELECT
				last_value
					AS
						id
			FROM
				catalogo_clasificacion_quejas_id_seq
		';
		$id = $db->query($sql);
		
		echo $id[0]['id'];
	}
	else if ($_POST['accion'] == 'delete') {
		$sql = '
			UPDATE
				catalogo_clasificacion_quejas
			SET
				status = \'FALSE\'
			WHERE
				id = ' . $_POST['id'] . '
		';
		$db->query($sql);
	}
	else if ($_POST['accion'] == 'update') {
		$sql = '
			UPDATE
				catalogo_clasificacion_quejas
			SET
				concepto = \'' . $_POST['concepto'] . '\'
			WHERE
				id = ' . $_POST['id'] . '
		';
		$db->query($sql);
		
		echo $_POST['id'];
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ped/CatalogoClasificacionQuejas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$sql = '
	SELECT
		id,
		concepto
	FROM
		catalogo_clasificacion_quejas
	WHERE
		status = \'TRUE\'
	ORDER BY
		concepto
';
$result = $db->query($sql);

if ($result)
	foreach ($result as $i => $r) {
		$tpl->newBlock('fila');
		$tpl->assign('row_style', $i % 2 == 0 ? 'off' : 'on');
		$tpl->assign('id', $r['id']);
		$tpl->assign('concepto', $r['concepto']);
	}

$tpl->printToScreen();
?>