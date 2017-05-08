<?php
include 'includes/dbstatus.php';
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_GET['c'])) {
	$sql = 'SELECT nombre_corto FROM catalogo_companias WHERE (num_cia BETWEEN 301 AND 599 OR num_cia IN (702)) AND num_cia = ' . $_GET['c'];
	$result = $db->query($sql);
	
	if ($result)
		echo $_GET['i'] . '|' . $result[0]['nombre_corto'];
	else
		echo $_GET['i'];
	die;
}

$filas = 30;

if (isset($_POST['num_cia'])) {
	$sql = '';
	for ($i = 0; $i < $filas; $i++)
		if ($_POST['num_cia'][$i] > 0 && get_val($_POST['kilos'][$i]) > 0 && get_val($_POST['importe'][$i]) > 0 && ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $_POST['fecha'][$i])) {
			$sql .= 'INSERT INTO facturacion_condimento (num_cia, fecha, kilos, precio, importe, iduser) VALUES (';
			$sql .= $_POST['num_cia'][$i] . ', \'';
			$sql .= $_POST['fecha'][$i] . '\', ';
			$sql .= get_val($_POST['kilos'][$i]) . ', ';
			$sql .= get_val($_POST['precio'][$i]) . ', ';
			$sql .= get_val($_POST['importe'][$i]) . ', ';
			$sql .= $_SESSION['iduser'] . ");\n";
		}
	$db->query($sql);
	die(header('location: ./fac_fac_condimento.php'));
}

$tpl = new TemplatePower('./plantillas/header.tpl');

$tpl->assignInclude('body', './plantillas/fac/fac_fac_condimento.tpl');
$tpl->prepare();

$tpl->newBlock('menu');
$tpl->assign('menucnt', '$_SESSION[menu]_cnt.js');
$tpl->gotoBlock('_ROOT');

$tpl->assign('fecha', date('d/m/Y', mktime(0, 0, 0, date('n'), date('d') - 2, date('Y'))));

for ($i = 0; $i < $filas; $i++) {
	$tpl->newBlock('fila');
	$tpl->assign('i', $i);
	$tpl->assign('next', $i < $filas - 1 ? $i + 1 : 0);
	$tpl->assign('back', $i > 0 ? $i - 1 : $filas - 1);
}

$tpl->printToScreen();
?>
