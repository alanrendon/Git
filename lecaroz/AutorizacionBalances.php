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
		case 'actualizar':
			$data = explode('|', $_REQUEST['data']);
			
			$sql = '
				UPDATE
					balances_aut
				SET
					nivel = ' . $data[1] . ',
					tsmod = now()
				WHERE
					iduser = ' . $data[0] . '
			';
			$db->query($sql);
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/adm/AutorizacionBalances.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$sql = '
	SELECT
		id,
		iduser,
		nombre,
		nivel
	FROM
			balances_aut
		LEFT JOIN
			auth
				USING
					(
						iduser
					)
	WHERE
		iduser <> 1
	ORDER BY
		nombre
';
$result = $db->query($sql);

if ($result) {
	$color = FALSE;
	foreach ($result as $r) {
		$tpl->newBlock('usuario');
		$tpl->assign('color', $color ? 'on' : 'off');
		$tpl->assign('usuario', $r['nombre']);
		$tpl->assign('iduser', $r['iduser']);
		$tpl->assign($r['nivel'], ' selected');
		$color = !$color;
	}
}

$tpl->printToScreen();
?>
