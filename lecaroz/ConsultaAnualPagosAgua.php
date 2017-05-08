<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

$text = array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'ñ');
$html = array('&Aacute;', '&Eacute;', '&Iacute;', '&Oacute;', '&Uacute;', '&Ntilde;', '&Ntilde;');

if (isset($_POST['accion'])) {
	if ($_POST['accion'] == 'retrieveCia') {
		$sql = '
			SELECT
				nombre_corto
			FROM
				catalogo_companias
			WHERE
					num_cia <= 300
				AND
					num_cia = ' . $_POST['num_cia'] . '
		';
		$result = $db->query($sql);
		
		if ($result)
			echo str_replace($text, $html, $result[0]['nombre_corto']);
	}
	
	die;
}

if (isset($_POST['num_cia'])) {
	$sql = '
		SELECT
			num_cia,
			nombre_corto,
			bimestre
		FROM
				facturas
					f
			LEFT JOIN
				catalogo_companias
					cc
						USING
							(
								num_cia
							)
		WHERE
				codgastos = 79
			AND
				anio = ' . $_GET['anio'] . '
		ORDER BY
			num_cia,
			bimestre
	';
	
	die;
}

$tpl = new TemplatePower('plantillas/bal/ConsultaAnualPagosAgua.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));

$sql = '
	SELECT
		idadministrador
			AS
				id,
		nombre_administrador
			AS
				admin
	FROM
		catalogo_administradores
	ORDER BY
		admin
';
$result = $db->query($sql);

if ($result)
	foreach ($result as $r) {
		$tpl->newBlock('admin');
		$tpl->assign('id', $r['id']);
		$tpl->assign('admin', $r['admin']);
	}

$tpl->printToScreen();
?>