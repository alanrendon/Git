<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

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
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';
			$result = $db->query($sql);
			
			if ($result) {
				echo $result[0]['nombre_corto'];
			}
		break;
		
		case 'autorizar':
			$sql = '
				INSERT INTO
					control_produccion_aut
						(
							iduser_aut,
							tsaut,
							num_cia
						)
					VALUES
						(
							' . $_SESSION['iduser'] . ',
							now(),
							' . $_REQUEST['num_cia'] . '
						)
			';
			$db->query($sql);
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/adm/AutorizarControlProduccion.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
