<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

if(!function_exists('json_encode')) {
	include_once('includes/JSON.php');

	$GLOBALS['JSON_OBJECT'] = new Services_JSON();

	function json_encode($value) {
		return $GLOBALS['JSON_OBJECT']->encode($value);
	}

	function json_decode($value) {
		return $GLOBALS['JSON_OBJECT']->decode($value);
	}
}

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'inicio':
			$tpl = new TemplatePower('plantillas/fac/ProveedoresConsultaInicio.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();
		break;

		case 'consultar':
			$condiciones[] = 'num_proveedor BETWEEN ' . ($_SESSION['iduser'] != 1 ? ($_SESSION['tipo_usuario'] == 2 ? '9001 AND 9999' : '1 AND 9999') : '1 AND 9999');

			if (isset($_REQUEST['pros']) && trim($_REQUEST['pros']) != '') {
				$pros = array();

				$pieces = explode(',', $_REQUEST['pros']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$pros[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$pros[] = $piece;
					}
				}

				if (count($pros) > 0) {
					$condiciones[] = 'num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			if (isset($_REQUEST['nombre']) && trim($_REQUEST['nombre']) != '') {
				if (strlen($_REQUEST['nombre']) == 1) {
					$condiciones[] = 'nombre LIKE \'' . $_REQUEST['nombre'] . '%\'';
				}
				else {
					$condiciones[] = 'nombre LIKE \'%' . $_REQUEST['nombre'] . '%\'';
				}
			}

			$sql = '
				SELECT
					num_proveedor
						AS num_pro,
					nombre
						AS nombre_pro,
					rfc,
					contacto,
					CONCAT_WS(\', \', telefono1, telefono2)
						AS telefonos,
					CONCAT_WS(\', \', email1, email2, email3)
						AS emails,
					CASE
						WHEN trans = TRUE THEN
							\'TRANSFERENCIA\'
						ELSE
							\'CHEQUE\'
					END
						AS forma_pago
				FROM
					catalogo_proveedores
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_proveedor
			';
			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/fac/ProveedoresConsultaResultado.tpl');
			$tpl->prepare();

			if ($result) {
				foreach ($result as $i => $rec) {
					$tpl->newBlock('row');

					$tpl->assign('row_color', $i % 2 == 0 ? 'off' : 'on');

					$tpl->assign('num_pro', $rec['num_pro']);
					$tpl->assign('nombre_pro', utf8_encode($rec['nombre_pro']));
					$tpl->assign('rfc', trim($rec['rfc']) != '' ? utf8_encode($rec['rfc']) : '&nbsp;');
					$tpl->assign('contacto', utf8_encode($rec['contacto']));
					$tpl->assign('telefonos', utf8_encode($rec['telefonos']));
					$tpl->assign('emails', utf8_encode($rec['emails']));
					$tpl->assign('forma_pago', utf8_encode($rec['forma_pago']));
				}
			}

			echo $tpl->getOutputContent();
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/ProveedoresConsulta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
