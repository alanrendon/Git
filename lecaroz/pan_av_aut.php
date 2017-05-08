<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No tenemos registrados accionistas";
$descripcion_error[2] = "NO EXISTE LA COMPAÑÍA";
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
$tpl->assignInclude("body","./plantillas/pan/pan_av_aut.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
	
// Si viene de una página que genero error

if (!isset($_GET['codmp'])) {
	$tpl->newBlock("obtener_datos");

	$sql="SELECT codmp,nombre FROM catalogo_mat_primas WHERE controlada='TRUE' order by codmp";
	$mp=ejecutar_script($sql,$dsn);
	for($i=0;$i<count($mp);$i++){
		$tpl->newBlock("nombre_mp");
		$tpl->assign("codmp",$mp[$i]['codmp']);
		$tpl->assign("nombre_mp",$mp[$i]['nombre']);
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
$sql="SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia <= 300 order by num_cia";
$cia=ejecutar_script($sql,$dsn);
$nmp=obtener_registro("catalogo_mat_primas",array("codmp"),array($_GET['codmp']),"","",$dsn);
$tpl->assign("codmp",$nmp[0]['nombre']);
$tpl->assign("codmp1",$_GET['codmp']);
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

	if(existe_registro("catalogo_avio_autorizado",array("num_cia","codmp"),array($cia[$i]["num_cia"],$_GET['codmp']),$dsn)){
		$avio=obtener_registro("catalogo_avio_autorizado",array("num_cia","codmp"),array($cia[$i]['num_cia'],$_GET['codmp']),"","",$dsn);
		if($avio[0]['frances_dia']==0) $tpl->assign("fd","");
		else $tpl->assign("fd",number_format($avio[0]['frances_dia'],2,'.',''));
		
		if($avio[0]['frances_noche']==0) $tpl->assign("fn","");
		else $tpl->assign("fn",number_format($avio[0]['frances_noche'],2,'.',''));
		
		if($avio[0]['bizcochero']==0) $tpl->assign("biz","");
		else $tpl->assign("biz",number_format($avio[0]['bizcochero'],2,'.',''));
		
		if($avio[0]['repostero']==0) $tpl->assign("rep","");
		else $tpl->assign("rep",number_format($avio[0]['repostero'],2,'.',''));

		if($avio[0]['piconero']==0) $tpl->assign("pic","");
		else $tpl->assign("pic",number_format($avio[0]['piconero'],2,'.',''));

	}
}

$tpl->printToScreen();


?>