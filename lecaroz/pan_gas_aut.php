<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No tenemos registrado ese gasto";
//$descripcion_error[2] = "Número de Gasto no existe en la Base de Datos, revisa bien codigo del gasto";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl");
// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/pan/pan_gas_aut.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
	
// Si viene de una página que genero error

if (!isset($_GET['codgastos'])) {
	$tpl->newBlock("obtener_datos");

	$sql="SELECT codgastos,descripcion FROM catalogo_gastos order by codgastos";
	$gas=ejecutar_script($sql,$dsn);
	for($i=0;$i<count($gas);$i++){
		$tpl->newBlock("nombre_gas");
		$tpl->assign("codgastos1",$gas[$i]['codgastos']);
		$tpl->assign("nombre_gas",$gas[$i]['descripcion']);
	}

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
	die();
}

$tpl->newBlock('captura');
$sql="SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia < 301 or num_cia in(702, 703) order by num_cia";
$cia=ejecutar_script($sql,$dsn);

$ngas=obtener_registro("catalogo_gastos",array("codgastos"),array($_GET['codgastos']),"","",$dsn);
$tpl->assign("nombre_gastos",$ngas[0]['descripcion']);
$tpl->assign("codgastos1",$_GET['codgastos']);
$tpl->assign("contador",count($cia));

for($i=0;$i<count($cia);$i++){
	$tpl->newBlock("rows");
	$tpl->assign("i",$i);
	if((count($cia)-1)==$i)
		$tpl->assign("next",0);
	else
		$tpl->assign("next",$i+1);
	$tpl->assign("num_cia",$cia[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);

	if(existe_registro("catalogo_limite_gasto",array("num_cia","codgastos"),array($cia[$i]["num_cia"],$_GET['codgastos']),$dsn)){
		$gastos=obtener_registro("catalogo_limite_gasto",array("num_cia","codgastos"),array($cia[$i]['num_cia'],$_GET['codgastos']),"","",$dsn);
		if($gastos[0]['limite']==0) $tpl->assign("limite","");
		else $tpl->assign("limite",number_format($gastos[0]['limite'],2,'.',''));
		
	}
}

$tpl->printToScreen();


?>