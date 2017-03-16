<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] == 1) die("Pantalla no disponible...");

$users = array(28, 29, 30, 31);

$descripcion_error[1] = 'NO HAY RESULTADOS';

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_cia_ale.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['num_cia'])) {
	$sql = "DELETE FROM alerta_efectivos;\n";
	
	foreach ($_POST['num_cia'] as $num_cia)
		if ($num_cia > 0)
			$sql .= "INSERT INTO alerta_efectivos (num_cia) VALUES ($num_cia);\n";
	
	$db->query($sql);
	
	die(header('location: ./ban_cia_ale.php'));
}

$result = $db->query("SELECT num_cia, nombre_corto FROM alerta_efectivos LEFT JOIN catalogo_companias USING (num_cia)" . ($_SESSION['iduser'] != 1 ? ' WHERE num_cia BETWEEN ' . ($_SESSION['iduser'] >= 28 ? '900 AND 998' : '1 AND 800') : '') . " ORDER BY num_cia");

$numfilas = 20;
for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock('fila');
	$tpl->assign('i', $i);
	$tpl->assign('next', $i < $numfilas - 1 ? $i + 1 : 0);
	$tpl->assign('back', $i > 0 ? $i - 1 : $numfilas - 1);
	
	if (isset($result[$i])) {
		$tpl->assign('num_cia', $result[$i]['num_cia']);
		$tpl->assign('nombre', $result[$i]['nombre_corto']);
	}
}

$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias " . ($_SESSION['iduser'] != 1 ? ' WHERE num_cia BETWEEN ' . ($_SESSION['iduser'] >= 28 ? '900 AND 998' : '1 AND 800') : '') . " ORDER BY num_cia";
$result = $db->query($sql);
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre_corto']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>