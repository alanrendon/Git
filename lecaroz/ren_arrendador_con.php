<?php
// CAPTURA PARA AFECTIVOS DIRECTOS
// TABLA "IMPORTE_EFECTIVOS"
// PANADERIAS -- EFECTIVOS -- CAPTURA DIRECTA
//define ('IDSCREEN',1321); // ID de pantalla
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No se encontró el arrendador";
// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);
// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/ren/ren_arrendador_con.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// Seleccionar tabla
if (!isset($_GET['tipo_con1'])) {
	$tpl->newBlock("obtener_datos");
	

	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
		$tpl->printToScreen();
		die();
	}
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
		$tpl->printToScreen();
		die();
	}
	$tpl->printToScreen();
	die();
}


if($_GET['tipo_con1']==0){
	$tpl->newBlock("por_arrendador");
	$arrendador=ejecutar_script("select catalogo_arrendadores.*, catalogo_notario.nombre as nombre_notario from catalogo_arrendadores join catalogo_notario on(catalogo_arrendadores.num_notario=catalogo_notario.cod_notario) where cod_arrendador=".$_GET['cod_arrendador'],$dsn);
	if(!$arrendador){
		header("location: ./ren_arrendador_con.php?codigo_error=1");
		die();
	}
	$tpl->assign("id",$arrendador[0]['cod_arrendador']);
	$tpl->assign("nombre",strtoupper($arrendador[0]['nombre']));
	$tpl->assign("representante",strtoupper($arrendador[0]['representante']));
	if($arrendador[0]['tipo_persona']=='t')
		$tipo="MORAL";
	else
		$tipo="FISICA";
	$tpl->assign("tipo",$tipo);
	$tpl->assign("notario",strtoupper($arrendador[0]['nombre_notario']));
	$tpl->assign("acta",$arrendador[0]['num_acta']);
	$tpl->assign("entidad",strtoupper($arrendador[0]['ent_fed']));
}

else{
	$tpl->newBlock("todos");
	$arrendador=ejecutar_script("select catalogo_arrendadores.*, catalogo_notario.nombre as nombre_notario from catalogo_arrendadores join catalogo_notario on(catalogo_arrendadores.num_notario=catalogo_notario.cod_notario) order by cod_arrendador",$dsn);
	for($i=0;$i<count($arrendador);$i++){
		$tpl->newBlock("arrendadores");
		$tpl->assign("id",$arrendador[$i]['cod_arrendador']);
		$tpl->assign("nombre",strtoupper($arrendador[$i]['nombre']));
		$tpl->assign("representante",strtoupper($arrendador[$i]['representante']));
		if($arrendador[$i]['tipo_persona']=='t')
			$tipo="MORAL";
		else
			$tipo="FISICA";
		$tpl->assign("tipo",$tipo);
		$tpl->assign("notario",strtoupper($arrendador[$i]['nombre_notario']));
		$tpl->assign("acta",$arrendador[$i]['num_acta']);
		$tpl->assign("entidad",strtoupper($arrendador[$i]['ent_fed']));
	}
}



$tpl->printToScreen();
die();
?>