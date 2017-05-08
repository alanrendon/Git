<?php
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';
include 'includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'verificar':
			$sql = "
				SELECT
					fv.id
				FROM
					facturas_validacion fv
				WHERE
					fv.tsvalid IS NULL
					AND fv.tsbaja IS NULL
					AND fv.tsalta::DATE < NOW()::DATE - INTERVAL '2 DAYS'
				LIMIT
					1
			";

			$result = $db->query($sql);

			if ($result && in_array($_SESSION['iduser'], array(64))) {
				echo 1;
			}
		break;

		case 'listado':
			$sql = "
				SELECT
					fv.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					fv.num_pro,
					cp.nombre
						AS nombre_pro,
					fv.num_fact,
					fv.tsalta::DATE
						AS fecha_alta
				FROM
					facturas_validacion fv
					LEFT JOIN catalogo_proveedores cp
						ON (cp.num_proveedor = fv.num_pro)
					LEFT JOIN catalogo_companias cc
						ON (cc.num_cia = fv.num_cia)
				WHERE
					fv.tsvalid IS NULL
					AND fv.tsbaja IS NULL
					AND fv.tsalta::DATE < NOW()::DATE - INTERVAL '2 DAYS'
				ORDER BY
					fv.num_pro,
					fv.num_fact
			";
			$result = $db->query($sql);

			if ($result && in_array($_SESSION['iduser'], array(1, 64))) {
				$tpl = new TemplatePower('plantillas/AlertaFacturasValidadasVencidas.tpl');
				$tpl->prepare();

				foreach ($result as $rec) {
					$tpl->newBlock('row');
					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
					$tpl->assign('num_pro', $rec['num_pro']);
					$tpl->assign('nombre_pro', utf8_encode($rec['nombre_pro']));
					$tpl->assign('num_fact', utf8_encode($rec['num_fact']));
					$tpl->assign('fecha_alta', $rec['fecha_alta']);
				}

				$tpl->printToScreen();
			}
		break;
	}
}


?>
