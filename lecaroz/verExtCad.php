<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

if (!in_array($_SESSION['iduser'], array(7, 25))) die;

// Conectarse a la base de datos
$db = new DBclass($dsn, 'autocommit=yes');

$sql = '
	SELECT
		num_cia,
		nombre_corto
			AS
				nombre,
		fecha_caducidad
	FROM
			caducidad_extintores
		LEFT JOIN
			catalogo_companias
				USING
					(
						num_cia
					)
	WHERE
		fecha_caducidad < now()::date + interval \'1 month\'
	ORDER BY
		num_cia,
		id
';
if (isset($_GET['status']))
	$sql .= '
		LIMIT
			1
	';

$result = $db->query($sql);

if (isset($_GET['status']) && $result) {
	echo 1;
	die;
}

$tpl = new TemplatePower( "./plantillas/verExtCad.tpl" );
$tpl->prepare();

$num_cia = NULL;
foreach ($result as $reg) {
	if ($num_cia != $reg['num_cia']) {
		$num_cia = $reg['num_cia'];
		
		$tpl->newBlock('cia');
		$tpl->assign('num_cia', $num_cia);
		$tpl->assign('nombre', $reg['nombre']);
		
		$num = 1;
	}
	$tpl->newBlock('row');
	$tpl->assign('num', $num++);
	$tpl->assign('fecha_caducidad', $reg['fecha_caducidad']);
}

$tpl->printToScreen();
?>