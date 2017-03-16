<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "";
$numfilas = 25;

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_fil_imss_alta.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->newBlock("captura");
$tpl->assign(date("n"), " selected");
$tpl->assign("anio", date("Y"));

if (isset($_POST['num_cia_primaria'])) {
	$sql = "";
	for ($i = 0; $i < $numfilas; $i++)
		if ($_POST['num_cia_primaria'][$i] > 0 && $_POST['num_cia'][$i] > 0) {
			if ($db->query("SELECT id FROM catalogo_filiales_imss WHERE num_cia_primaria = {$_POST['num_cia_primaria'][$i]} AND num_cia = {$_POST['num_cia'][$i]}")) {
				$tpl->newBlock("valid");
				$tpl->assign("mensaje", "La filial {$_POST['num_cia'][$i]} '{$_POST['nombre_cia'][$i]}' para la compañía {$_POST['num_cia_primaria'][$i]} '{$_POST['nombre_cia_pri'][$i]}' ya existe en el catálogo");
				$tpl->assign("campo", "num_cia_primaria[$i]");
				$tpl->printToScreen();
				die;
			}
			$sql .= "INSERT INTO catalogo_filiales_imss (num_cia_primaria, num_cia) VALUES ({$_POST['num_cia_primaria'][$i]}, {$_POST['num_cia'][$i]});\n";
		}
	
	if ($sql != "") $db->query($sql);
	
	$tpl->newBlock("redir");
	$tpl->printToScreen();
	die;
}

// Filas de captura
for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("back", $i > 0 ? $i - 1 : $numfilas - 1);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
}

// Catálogo de Compañías
$cias = $db->query("SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN " . ($_SESSION['iduser'] >= 28 ? '900 AND 998' : '1 AND 899') . " ORDER BY num_cia");
foreach ($cias as $cia) {
	$tpl->newBlock("cia");
	foreach ($cia as $tag => $value)
		$tpl->assign($tag, $value);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>