<?php

include_once('includes/class.db.inc.php');
include_once('includes/class.session2.inc.php');
include_once('includes/class.TemplatePower.inc.php');
include_once('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'alta':
			$sql = '';
			
			foreach ($_REQUEST['nombre'] as $i => $nombre) {
				if ($nombre != '' && $_REQUEST['ap_paterno'] != '') {
					$sql .= '
						INSERT INTO
							lista_negra_trabajadores
								(
									folio,
									nombre,
									ap_paterno,
									ap_materno,
									idtipobaja,
									observaciones,
									idins
								)
							VALUES
								(
									(
										SELECT
											COALESCE(MAX(folio), 0) + 1
										FROM
											lista_negra_trabajadores
									),
									\'' . $nombre . '\',
									\'' . $_REQUEST['ap_paterno'][$i] . '\',
									\'' . $_REQUEST['ap_materno'][$i] . '\',
									' . $_REQUEST['tipo_baja'][$i] . ',
									\'' . $_REQUEST['observaciones'][$i] . '\',
									' . $_SESSION['iduser'] . '
								)
					' . ";\n";
				}
			}
			
			if ($sql != '') {
				$db->query($sql);
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/nom/ListaNegraAlta.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

if (!in_array($_SESSION['iduser'], array(1, 4, 7, 25, 53, 43)))
{
	$tpl->assign('disabled', ' disabled="disabled"');
}

$sql = '
	SELECT
		idtipobaja
			AS value,
		nombre_tipo_baja
			AS text
	FROM
		catalogo_tipos_baja
	WHERE
		tsbaja IS NULL
	ORDER BY
		text
';

$tipos = $db->query($sql);

if ($tipos) {
	foreach ($tipos as $t) {
		$tpl->newBlock('tipo_baja');
		$tpl->assign('value', $t['value']);
		$tpl->assign('text', utf8_encode($t['text']));
	}
} else {
	$tpl->newBlock('tipo_baja');
	$tpl->assign('value', 'NULL');
	$tpl->assign('text', '');
}

$tpl->printToScreen();
?>
