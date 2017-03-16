<?php
// CONTROL DE AVIO
// Tabla 'control_avio'
// Menu 'Panaderias->Producción'

define ('IDSCREEN',1213); // ID de pantalla

// --------------------------------- INCLUDES ---------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores ---------------------------------
$descripcion_error[1] = "No hay registros";

// --------------------------------- Validar usuario ---------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener informacion de la pantalla ---------------------------------
//$session->info_pantalla();

// --------------------------------- Generar pantalla ---------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_avi_ord.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['tabla'])) {
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		if ($_POST['num_orden'.$i] > 0)
			ejecutar_script("UPDATE control_avio SET num_orden = ".$_POST['num_orden'.$i]." WHERE num_cia = $_POST[num_cia] AND codmp = ".$_POST['codmp'.$i],$dsn);
	}
}

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("cia");
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
	}
	
	$tpl->printToScreen();
	die;
}

$sql = "SELECT DISTINCT ON (codmp) codmp,nombre,num_orden FROM control_avio JOIN catalogo_mat_primas USING(codmp) WHERE num_cia = $_GET[num_cia] ORDER BY codmp ASC";
$mp = ejecutar_script($sql,$dsn);

if (!$mp) {
	header("location: ./pan_avi_ord.php?codigo_error=1");
	die;
}

$tpl->newBlock("orden");
$cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]",$dsn);
$tpl->assign("num_cia",$_GET['num_cia']);
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
$tpl->assign("numfilas",count($mp));

for ($i=0; $i<count($mp); $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i",$i);
	$tpl->assign("codmp",$mp[$i]['codmp']);
	$tpl->assign("nombre_mp",$mp[$i]['nombre']);
	$tpl->assign("num_orden",$mp[$i]['num_orden']);
	
	$tpl->assign("back",($i > 0)?$i-1:count($mp)-1);
	$tpl->assign("next",($i < count($mp)-1)?$i+1:0);
}

$tpl->printToScreen();
?>