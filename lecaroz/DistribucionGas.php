<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

// Obtener compañía
if (isset($_GET['c'])) {
	$sql = '
		SELECT
			nombre_corto
				AS
					nombre
		FROM
			catalogo_companias
		WHERE
			/*(
					num_cia
						BETWEEN
								300
							AND
								599
				OR
					num_cia
						IN
							(
								702,
								101,
								100,
								114,
								89,
								113,
								108,
								117,
								119,
								122,
								132,
								133
							)
			)
			AND*/
				num_cia = ' . $_GET['c'];
	$result = $db->query($sql);
	
	if ($result)
		echo trim(str_replace(array('ROSTICERIA', 'ROST.'), '', $result[0]['nombre']));
	
	die;
}

if (isset($_POST['num_cia'])) {
	$sql = '
		TRUNCATE
			distribucion_gas
	' . ";\n";
	
	foreach ($_POST['num_cia'] as $i => $c) {
		for ($j = 1; $j <= 5; $j++)
			if ($_POST['num_cia' . $j][$i] > 0)
				$sql .= '
					INSERT INTO
						distribucion_gas
							(
								num_cia,
								ros,
								iduser
							)
						VALUES
							(
								' . $c . ',
								' . $_POST['num_cia' . $j][$i] . ',
								' . $_SESSION['iduser'] . '
							)
				' . ";\n";
	}
	
	$db->query($sql);
	
	die(header('location: DistribucionGas.php'));
}

$tpl = new TemplatePower('plantillas/bal/DistribucionGas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));
$tpl->assign(date('n', mktime(0, 0, 0, date('n'), 0, date('Y'))), ' selected');

$sql = '
	SELECT
		num_cia,
		nombre_corto
			AS
				nombre_cia
	FROM
		catalogo_companias
	WHERE
			num_cia <= 300
		OR
			num_cia
				IN
					(
						--328,
						702,
						704,
						705
					)
		OR num_cia BETWEEN 301 AND 599
	ORDER BY
		num_cia
';
$cias = $db->query($sql);

$sql = '
	SELECT
		dg.num_cia,
		ros,
		nombre_corto
			AS
				nombre_ros
	FROM
			distribucion_gas
				dg
		LEFT JOIN
			catalogo_companias
				cc
					ON
						(
							cc.num_cia = dg.ros
						)
	ORDER BY
		dg.num_cia,
		ros
';
$tmp = $db->query($sql);

$porc = array();
foreach ($tmp as $t)
	$porc[$t['num_cia']][] = array(
		'ros' => $t['ros'],
		'nombre_ros' => $t['nombre_ros']
	);

foreach ($cias as $i => $c) {
	$tpl->newBlock('fila');
	$tpl->assign('i', $i);
	$tpl->assign('estilo_linea', $i % 2 == 0 ? 'linea_off' : 'linea_on');
	$tpl->assign('num_cia', $c['num_cia']);
	$tpl->assign('nombre_cia', $c['nombre_cia']);
	
	if (isset($porc[$c['num_cia']]))
		foreach ($porc[$c['num_cia']] as $j => $p) {
			$tpl->assign('num_cia' . ($j + 1), $p['ros']);
			$tpl->assign('nombre_cia' . ($j + 1), trim(str_replace(array('ROSTICERIA', 'ROST.'), '', $p['nombre_ros'])));
		}
}

$tpl->printToScreen();
?>
