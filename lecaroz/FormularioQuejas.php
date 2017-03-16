<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

// Obtener compaρνa
if (isset($_GET['c'])) {
	$sql = '
		SELECT
			nombre_corto
				AS
					nombre
		FROM
			catalogo_companias
		WHERE
				num_cia
					BETWEEN
							1
						AND
							899
			AND
				num_cia = ' . $_GET['c'];
	$result = $db->query($sql);
	
	if ($result)
		echo $result[0]['nombre'];
	
	die;
}

if (isset($_POST['num_cia'])) {
	$sql = '
		INSERT INTO
			quejas_pedidos
				(
					num_cia,
					idclase,
					tipo,
					quejoso,
					queja,
					time_queja,
					iduser,
					tsmod
				)
		VALUES
				(
					' . $_POST['num_cia'] . ',
					' . $_POST['idclase'] . ',
					' . $_POST['tipo'] . ',
					\'' . $_POST['quejoso'] . '\',
					\'' . $_POST['queja'] . '\',
					now(),
					' . $_SESSION['iduser'] . ',
					now()
				)
	';
	$db->query($sql);
	
	die(header('location: FormularioQuejas.php'));
}

$tpl = new TemplatePower('plantillas/ped/FormularioQuejas.tpl');
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
$clases = $db->query($sql);

if ($clases)
	foreach ($clases as $c) {
		$tpl->newBlock('clase');
		$tpl->assign('id', $c['id']);
		$tpl->assign('concepto', $c['concepto']);
	}

$tpl->printToScreen();
?>