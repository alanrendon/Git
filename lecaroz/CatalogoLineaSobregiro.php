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
				num_cia
					BETWEEN
							1
						AND
							899
			AND
				num_cia = ' . $_GET['c'];
	$result = $db->query($sql);
	
	if ($result)
		echo $result[0]['nombre'];
	
	die;
}

if (isset($_GET['nc'])) {
	$sql = '
		SELECT
			cuenta_sobregiro,
			cuenta_efectivo,
			importe_autorizado
		FROM
			catalogo_linea_sobregiro
		WHERE
				num_cia = ' . $_GET['nc'] . '
			AND
				num_sec = ' . $_GET['ns'] . '
	';
	$r = $db->query($sql);
	
	if (!$r)
		echo '0|||';
	else
		echo '-1|' . $r[0]['cuenta_sobregiro'] . '|' . $r[0]['cuenta_efectivo'] . '|' . $r[0]['importe_autorizado'];
	
	die;
}

if (isset($_POST['num_cia'])) {
	$sql = '';
	
	$length = count($_POST['num_cia']);
	for ($i = 0; $i < $length; $i++)
		if ($_POST['num_cia'][$i] > 0 && $_POST['num_sec'][$i] > 0 && strlen(trim($_POST['cuenta_sobregiro'][$i])) == 11 && strlen(trim($_POST['cuenta_sobregiro'][$i])) == 11 && get_val($_POST['importe_autorizado'][$i]) > 0) {
			if ($id = $db->query('SELECT id FROM catalogo_linea_sobregiro WHERE num_cia = ' . $_POST['num_cia'][$i] . ' AND num_sec = ' . $_POST['num_sec'][$i])) {
				$sql .= '
					UPDATE
						catalogo_linea_sobregiro
					SET
						cuenta_sobregiro = \'' . $_POST['cuenta_sobregiro'][$i] . '\',
						cuenta_efectivo = \'' . $_POST['cuenta_efectivo'][$i] . '\',
						importe_autorizado = ' . get_val($_POST['importe_autorizado'][$i]) . ',
						iduser = ' . $_SESSION['iduser'] . ',
						tsmod = now()
					WHERE
						id = ' . $id[0]['id'] . '
				' . ";\n";
			}
			else {
				$sql .= '
					INSERT INTO
						catalogo_linea_sobregiro
							(
								num_cia,
								num_sec,
								cuenta_sobregiro,
								cuenta_efectivo,
								importe_autorizado,
								iduser,
								tsmod
							)
					VALUES
						(
							' . $_POST['num_cia'][$i] . ',
							' . $_POST['num_sec'][$i] . ',
							\'' . $_POST['cuenta_sobregiro'][$i] . '\',
							\'' . $_POST['cuenta_efectivo'][$i] . '\',
							' . get_val($_POST['importe_autorizado'][$i]) . ',
							' . $_SESSION['iduser'] . ',
							now()
						)
				' . ";\n";
			}
		}
	
	if ($sql != '')
		$db->query($sql);
	
	die(header('location: CatalogoLineaSobregiro.php'));
}

$tpl = new TemplatePower('plantillas/ban/CatalogoLineaSobregiro.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>