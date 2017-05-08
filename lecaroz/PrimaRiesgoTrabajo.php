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
			$sql = '';
			
			for ($i = 0; $i < count($_REQUEST['num_cia']); $i++) {
				if ($id = $db->query('SELECT id FROM prima_riesgo_trabajo WHERE num_cia = ' . $_REQUEST['num_cia'])) {
					$sql .= '
						UPDATE
							prima_riesgo_trabajo
						SET
							prima = ' . get_val($_REQUEST['prima']) . ',
							iduser = ' . $_REQUEST['iduser'] . ',
							tsmod = now()
						WHERE
							id = ' . $id[0]['id'] . '
					' . ";\n";
				}
				else {
					$sql .= '
						INSERT INTO
							prima_riesgo_trabajo
								(
									num_cia,
									prima,
									iduser
								)
						VALUES
								(
									' . $_REQUEST['num_cia'] . ',
									' . get_val($_REQUEST['prima']) . ',
									' . $_SESSION['iduser'] . '
								)
					' . ";\n";
				}
			}
			
			if (trim($sq1) != '') {
				$db->query($sql);
			}
			
			header('location: PrimaRiesgoTrabajo.php');
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ban/PrimaRiesgoTrabajo.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$sql = '
	SELECT
		num_cia,
		nombre_corto
			AS
				nombre_cia,
		no_imss,
		prima
	FROM
			catalogo_companias
				cc
		LEFT JOIN
			prima_riesgo_trabajo
				prt
					USING
						(
							num_cia
						)
	WHERE
			num_cia < 999
		AND
			(
					(
							no_imss IS NOT NULL
						AND
							TRIM(no_imss) <> \'\'
					)
				OR
					prima IS NOT NULL
			)
	ORDER BY
		num_cia
';
$result = $db->query($sql);

if ($result) {
	$color = FALSE;
	foreach ($result as $r) {
		$tpl->newBlock('row');
		$tpl->assign('num_cia', $r['num_cia']);
		$tpl->assign('nombre_cia', $r['nombre_cia']);
		$tpl->assign('no_imss', $r['no_imss']);
		$tpl->assign('prima', round($r['prima'], 5) > 0 ? number_format($r['prima'], 5, '.', ',') : '');
		$tpl->assign('color', $color ? 'on' : 'off');
		
		$color = !$color;
	}
}

$tpl->printToScreen();
?>
