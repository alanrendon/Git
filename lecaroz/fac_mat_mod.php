<?php
// MODIFICACION DE MATERIA PRIMA
// Tablas 'catalogo_mat_primas'
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

$descripcion_error[1] = "No existe el código en el catálogo de compañías";

if (isset($_POST['codmp'])) {
	$sql = "UPDATE catalogo_mat_primas SET ";
	$sql .= "nombre = '$_POST[nombre]',";
	$sql .= "unidadconsumo = $_POST[unidadconsumo],";
	$sql .= "tipo = '$_POST[tipo]',";
	$sql .= "controlada = '$_POST[controlada]',";
	$sql .= "presentacion = $_POST[presentacion],";
	$sql .= "procpedautomat = '$_POST[procpedautomat]',";
	$sql .= "porcientoincremento = $_POST[porcientoincremento],";
	$sql .= "entregasfinmes = $_POST[entregafinmes],";
	$sql .= "tipo_cia = '$_POST[tipo_cia]',";
	$sql .= "orden = $_POST[orden]";
	$sql .= " WHERE codmp = $_POST[codmp]";
	ejecutar_script($sql,$dsn);
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_mat_mod.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['codmp'])) {
	$tpl->newBlock("datos");
	
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
	die;
}

$sql = "SELECT * FROM catalogo_mat_primas WHERE codmp = $_GET[codmp]";
$mp = ejecutar_script($sql,$dsn);

if (!$mp) {
	header("location: ./fac_mat_mod.php?codigo_error=1");
	die;
}

$tpl->newBlock("modificar");

$tpl->assign("codmp",$mp[0]['codmp']);
$tpl->assign("nombre",$mp[0]['nombre']);
$tpl->assign("porcientoincremento",$mp[0]['porcientoincremento']);
$tpl->assign("entregafinmes",$mp[0]['entregasfinmes']);

if ($mp[0]['controlada'] == "TRUE")
	$tpl->assign("controlada_true","checked");
else
	$tpl->assign("controlada_false","checked");

if ($mp[0]['procpedautomat'] == "t")
	$tpl->assign("aut_true","checked");
else
	$tpl->assign("aut_false","checked");

if ($mp[0]['tipo_cia'] == "t")
	$tpl->assign("cia_true","checked");
else
	$tpl->assign("cia_false","checked");

$tpl->assign("orden",$mp[0]['orden']);

$unidad = ejecutar_script("SELECT * FROM tipo_unidad_consumo ORDER BY idunidad",$dsn);
for ($i=0; $i<count($unidad); $i++) {
	$tpl->newBlock("unidad");
	$tpl->assign("valueunidad",$unidad[$i]['idunidad']);
	$tpl->assign("nameunidad",$unidad[$i]['descripcion']);
	if ($unidad[$i]['idunidad'] == $mp[0]['unidadconsumo']) $tpl->assign("selected","selected");
}

$tipo = ejecutar_script("SELECT * FROM tipo_mat_primas ORDER BY idtipomatprima",$dsn);
for ($i=0; $i<count($tipo); $i++) {
	$tpl->newBlock("tipo");
	$tpl->assign("valuetipo",$tipo[$i]['idtipomatprima']);
	$tpl->assign("nametipo",$tipo[$i]['descripcion']);
	if ($tipo[$i]['idtipomatprima'] == $mp[0]['tipo']) $tpl->assign("selected","selected");
}

$presentacion = ejecutar_script("SELECT * FROM tipo_presentacion ORDER BY idpresentacion",$dsn);
for ($i=0; $i<count($presentacion); $i++) {
	$tpl->newBlock("presentacion");
	$tpl->assign("valuepresentacion",$presentacion[$i]['idpresentacion']);
	$tpl->assign("namepresentacion",$presentacion[$i]['descripcion']);
	if ($presentacion[$i]['idpresentacion'] == $mp[0]['presentacion']) $tpl->assign("selected","selected");
}

$tpl->printToScreen();
?>