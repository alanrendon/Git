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

$descripcion_error[1] = "No existe el código en el catálogo de gastos";

if (isset($_POST['codgasto'])) {
	$sql = "UPDATE catalogo_gastos SET ";
	$sql .= "descripcion = '$_POST[nombre]',";
	$sql .= "codigo_edo_resultados = $_POST[estado_resultados],";
	$sql .= "tipo_gasto = $_POST[tipo_gasto],";
	$sql .= "aplicacion_gasto = $_POST[aplicacion_gasto]";
	$sql .= " WHERE codgastos = $_POST[codgasto]";
	ejecutar_script($sql,$dsn);
	header("location: ./fac_gasto_mod.php");
	die();
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_gasto_mod.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['codgasto'])) {
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

$sql = "SELECT * FROM catalogo_gastos WHERE codgastos = $_GET[codgasto]";
$gasto = ejecutar_script($sql,$dsn);

if (!$gasto) {
	header("location: ./fac_gasto_mod.php?codigo_error=1");
	die;
}

$tpl->newBlock("modificar");

$tpl->assign("codgastos",$gasto[0]['codgastos']);
$tpl->assign("nombre",$gasto[0]['descripcion']);

if ($gasto[0]['tipo_gasto'] == "t"){
	$tpl->assign("fijo","checked");
	$tpl->assign("tipo","true");
}
else{
	$tpl->assign("variable","checked");
	$tpl->assign("tipo","false");
}

$tpl->assign($gasto[0]['aplicacion_gasto'] == "f" ? "panaderia" : "reparto", "checked");

$estado = ejecutar_script("SELECT * FROM catalogo_edo_cod_res ORDER BY idcodigoedoresultados",$dsn);
for ($i=0; $i<count($estado); $i++) {
	$tpl->newBlock("estado");
	$tpl->assign("valueestado",$estado[$i]['idcodigoedoresultados']);
	$tpl->assign("nameestado",$estado[$i]['descripcion']);
	if ($estado[$i]['idcodigoedoresultados'] == $gasto[0]['codigo_edo_resultados']) $tpl->assign("selected","selected");
}


$tpl->printToScreen();
?>