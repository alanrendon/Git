<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/class.auxinv.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

$tpl = new TemplatePower('plantillas/bal/ImprimirBalancesPanaderias.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$isIpad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

$tpl->newBlock($isIpad ? 'ipad' : 'normal');

$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0)));
$tpl->assign(date('n', mktime(0, 0, 0, date('n'), 0)), ' selected');

if ($isIpad) {
	$condiciones[] = 'num_cia <= 300';
	
	if (!in_array($_SESSION['iduser'], array(1, 4, 18, 19, 20, 24, 37, 48, 50))) {
		$condiciones[] = 'ca.iduser = ' . $_SESSION['iduser'];
	}
	
	$sql = '
		SELECT
			num_cia,
			nombre_corto
				AS
					nombre_cia
		FROM
				catalogo_companias cc
			LEFT JOIN
				catalogo_administradores ca
					USING
						(
							idadministrador
						)
		WHERE
			' . implode(' AND ', $condiciones) . '
		ORDER BY
			num_cia
	';
	$cias = $db->query($sql);
	
	foreach ($cias as $c) {
		$tpl->newBlock('cia');
		$tpl->assign('num_cia', $c['num_cia']);
		$tpl->assign('nombre_cia', $c['nombre_cia']);
	}
}

$sql = '
	SELECT
		idadministrador
			AS
				id,
		nombre_administrador
			AS
				nombre
	FROM
		catalogo_administradores
	ORDER BY
		nombre
';
$admins = $db->query($sql);

if ($admins) {
	foreach ($admins as $a) {
		$tpl->newBlock($isIpad ? 'admin_ipad' : 'admin');
		$tpl->assign('id', $a['id']);
		$tpl->assign('nombre', $a['nombre']);
	}
}

$sql = '
	SELECT
		nivel
	FROM
		balances_aut
	WHERE
		iduser = ' . $_SESSION['iduser'] . '
';
$nivel = $db->query($sql);

if (!$isIpad && (!$nivel || $nivel[0]['nivel'] == 0)) {
	$tpl->assign('normal.disabled_generar', ' disabled');
	$tpl->assign('normal.disabled_imprimir', ' disabled');
}
else if (!$isIpad && $nivel[0]['nivel'] == 1) {
	$tpl->assign('normal.disabled_generar', ' disabled');
}
else if ($isIpad && $nivel[0]['nivel'] == 0) {
	$tpl->assign('ipad.disabled_consultar', ' disabled');
}

$tpl->printToScreen();
?>
