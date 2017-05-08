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
$descripcion_error[1] = "No se encontró el local especificado";
$descripcion_error[2] = "No hay locales registrados";
$descripcion_error[3] = "No se encontraron locales para la compañía especificada";
$descripcion_error[4] = "No se encontraron locales para el arrendador especificado";

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
$tpl->assignInclude("body","./plantillas/ren/ren_local_con.tpl");
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
//----------------------------------------------------------CONSULTAR POR LOCAL
if($_GET['tipo_con']==0){
	$local=ejecutar_script("select catalogo_locales.*, direccion, nombre_corto, catalogo_arrendadores.nombre as nombre_arrendador from catalogo_locales join catalogo_companias using(num_cia) join catalogo_arrendadores using (cod_arrendador) where num_local=".$_GET['num_local'],$dsn);
	if(!$local){
		header("location: ./ren_local_con.php?codigo_error=1");
		die();
	}
	$tpl->newBlock("por_local");
	$tpl->assign("id",$local[0]['num_local']);
	$tpl->assign("nombre_local",$local[0]['nombre']);
	$tpl->assign("predial",$local[0]['cta_predial']);
	$tpl->assign("metros",$local[0]['metros']);
	$tpl->assign("m2",$local[0]['metros_cuadrados']);
	$tpl->assign("num_cia",$local[0]['num_cia']);
	$tpl->assign("nombre_cia",$local[0]['nombre_corto']);
	$tpl->assign("cod_arrendador",$local[0]['cod_arrendador']);
	$tpl->assign("nombre_arrendador",strtoupper($local[0]['nombre_arrendador']));
	$tpl->assign("direccion",$local[0]['direccion']);
	if($local[0]['bloque']==1)
		$bloque="INTERNO";
	else
		$bloque="EXTERNO";
	$tpl->assign("bloque",$bloque);
}

//-------------------------------------------------CONSULTAR POR ARRENDADOR
elseif($_GET['tipo_con']==1){
	$local=ejecutar_script("select catalogo_locales.*, direccion, nombre_corto, catalogo_arrendadores.nombre as nombre_arrendador from catalogo_locales join catalogo_companias using(num_cia) join catalogo_arrendadores using (cod_arrendador) where catalogo_locales.cod_arrendador=".$_GET['num_arrendador'],$dsn);
	if(!$local){
		header("location: ./ren_local_con.php?codigo_error=1");
		die();
	}
	$tpl->newBlock("por_arrendador");
	$tpl->assign("num_arrendador",$local[0]['cod_arrendador']);
	$tpl->assign("nombre_arrendador",strtoupper($local[0]['nombre_arrendador']));

	for($i=0;$i<count($local);$i++){
		$tpl->newBlock("locales_arrendador");
		$tpl->assign("id",$local[$i]['num_local']);
		$tpl->assign("nombre_local",$local[$i]['nombre']);
		$tpl->assign("predial",$local[$i]['cta_predial']);
		$tpl->assign("metros",$local[$i]['metros']);
		$tpl->assign("m2",$local[$i]['metros_cuadrados']);
		$tpl->assign("num_cia",$local[$i]['num_cia']);
		$tpl->assign("nombre_cia",$local[$i]['nombre_corto']);
		$tpl->assign("direccion",$local[$i]['direccion']);
		if($local[$i]['bloque']==1)
			$bloque="INTERNO";
		else
			$bloque="EXTERNO";
		$tpl->assign("bloque",$bloque);
	}

}


//-------------------------------------------------CONSULTAR POR COMPAÑÍA
elseif($_GET['tipo_con']==2){
	$tpl->newBlock("por_cia");
	$local=ejecutar_script("select catalogo_locales.*, direccion, nombre_corto, catalogo_arrendadores.nombre as nombre_arrendador from catalogo_locales join catalogo_companias using(num_cia) join catalogo_arrendadores using (cod_arrendador) where catalogo_locales.num_cia=".$_GET['num_cia'],$dsn);
	if(!$local){
		header("location: ./ren_local_con.php?codigo_error=3");
		die();
	}
	$tpl->assign("num_cia",$local[0]['num_cia']);
	$tpl->assign("nombre_cia",strtoupper($local[0]['nombre_corto']));
	$tpl->assign("direccion",strtoupper($local[0]['direccion']));

	for($i=0;$i<count($local);$i++){
		$tpl->newBlock("locales_cia");
		$tpl->assign("id",$local[$i]['num_local']);
		$tpl->assign("nombre_local",$local[$i]['nombre']);
		$tpl->assign("predial",$local[$i]['cta_predial']);
		$tpl->assign("metros",$local[$i]['metros']);
		$tpl->assign("m2",$local[$i]['metros_cuadrados']);
		$tpl->assign("cod_arrendador",$local[$i]['cod_arrendador']);
		$tpl->assign("nombre_arrendador",strtoupper($local[$i]['nombre_arrendador']));
		if($local[$i]['bloque']==1)
			$bloque="INTERNO";
		else
			$bloque="EXTERNO";
		$tpl->assign("bloque",$bloque);
	}
}

//--------------------------------------------------CONSULTAR POR TODOS LOS LOCALES
elseif($_GET['tipo_con']==3){
	$tpl->newBlock("todos");
	$local=ejecutar_script("select catalogo_locales.*, direccion, nombre_corto, catalogo_arrendadores.nombre as nombre_arrendador from catalogo_locales join catalogo_companias using(num_cia) join catalogo_arrendadores using (cod_arrendador)order by num_local",$dsn);

	for($i=0;$i<count($local);$i++){
		$tpl->newBlock("locales");
		$tpl->assign("id",$local[$i]['num_local']);
		$tpl->assign("nombre_local",$local[$i]['nombre']);
		$tpl->assign("predial",$local[$i]['cta_predial']);
		$tpl->assign("metros",$local[$i]['metros']);
		$tpl->assign("m2",$local[$i]['metros_cuadrados']);
		$tpl->assign("num_cia",$local[$i]['num_cia']);
		$tpl->assign("nombre_cia",$local[$i]['nombre_corto']);
		$tpl->assign("cod_arrendador",$local[$i]['cod_arrendador']);
		$tpl->assign("nombre_arrendador",strtoupper($local[$i]['nombre_arrendador']));
		$tpl->assign("direccion",$local[$i]['direccion']);
		if($local[$i]['bloque']==1)
			$bloque="INTERNO";
		else
			$bloque="EXTERNO";
		$tpl->assign("bloque",$bloque);
	}
}
$tpl->printToScreen();
die();
?>