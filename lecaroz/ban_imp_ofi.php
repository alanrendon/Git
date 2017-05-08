<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay productos para el proveedor indicado";
$descripcion_error[2] = "El número de factura ya existe en la Base de Datos.";
$descripcion_error[3] = "La factura es del mes pasado y no puede ser ingresada al sistema";

$db = new DBclass($dsn, "autocommit=yes");

$numfilas = 50;

// Insertar datos
if (isset($_POST['num_cia'])) {
	$sql = "";
	for ($i=0; $i<$numfilas; $i++)
		if ($_POST['num_cia'][$i] > 0 && $_POST['importe'][$i] > 0)
			if ($id = $db->query("SELECT id FROM importes_oficinas WHERE num_cia = {$_POST['num_cia'][$i]} AND mes = $_POST[mes] AND anio = $_POST[anio]"))
				$sql .= "UPDATE importes_oficinas SET importe = {$_POST['importe'][$i]} WHERE id = {$id[0]['id']};\n";
			else
				$sql .= "INSERT INTO importes_oficinas (num_cia,importe,mes,anio) VALUES ({$_POST['num_cia'][$i]},{$_POST['importe'][$i]},$_POST[mes],$_POST[anio]);\n";
		else if ($_POST['num_cia'][$i] > 0 && ($_POST['importe'][$i] == 0 || $_POST['importe'][$i] == ""))
			$sql .= "DELETE FROM importes_oficinas WHERE num_cia = {$_POST['num_cia'][$i]} AND mes = $_POST[mes] AND anio = $_POST[anio];\n";
	
	$db->query($sql);
	$db->desconectar();
	
	header("location: ./ban_imp_ofi.php");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_imp_ofi.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign(date("n", mktime(0,0,0,date("m"),1,date("Y"))), "selected");
$tpl->assign("anio", date("Y", mktime(0,0,0,date("m"),1,date("Y"))));

$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias ORDER BY num_cia";
$cia = $db->query($sql);

for ($i = 0; $i < count($cia); $i++) {
	$tpl->newBlock("nombre_cia");
	$tpl->assign("num_cia", $cia[$i]['num_cia']);
	$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
}

for ($i=0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
$db->desconectar();
die;
?>