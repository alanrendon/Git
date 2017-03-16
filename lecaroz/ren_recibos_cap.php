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
$descripcion_error[1] = "Ya existen registros";
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
$tpl->assignInclude("body","./plantillas/ren/ren_recibos_cap.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// Seleccionar tabla
if (!isset($_GET['mes'])) {
	
	if(isset($_SESSION['recibos']))
		unset($_SESSION['recibos']);
	
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));
	for($i=0;$i<=12;$i++){
		$tpl->newBlock("mes");
		$tpl->assign("mes",$i);
		$tpl->assign("nombre_mes",mes_escrito($i));
		if($i==date("n"))
			$tpl->assign("selected","selected");
	}

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

$fecha_rev="1/".$_GET['mes']."/".$_GET['anio'];
/*if(existe_registro("recibos_rentas",array("fecha_pago"),array($fecha_rev),$dsn)){
	header("location: ./ren_recibos_cap.php?codigo_error=1");
	die();
}*/

$tpl->newBlock("captura");

if(isset($_SESSION['recibos'])){
	$tpl->assign("anio",$_SESSION['recibos']['anio']);
	$tpl->assign("nombre_mes",mes_escrito($_SESSION['recibos']['mes']));
	$tpl->assign("mes",$_SESSION['recibos']['mes']);
}
else{
	$tpl->assign("nombre_mes",mes_escrito($_GET['mes']));
	$tpl->assign("anio",$_GET['anio']);
	$tpl->assign("mes",$_GET['mes']);
}


$arrendadores=ejecutar_script("select distinct(cod_arrendador),catalogo_arrendadores.nombre from catalogo_locales join catalogo_arrendadores using(cod_arrendador) where ocupado = true order by cod_arrendador",$dsn);
$tpl->assign("cont",count($arrendadores));


$bloque=0;
for($i=0;$i<count($arrendadores);$i++){
	$tpl->newBlock("arrendadores");
	$tpl->assign("i",$i);
	if($i+1 >= count($arrendadores))
		$tpl->assign("next","0");
	else
		$tpl->assign("next",$i+1);
	
	$tpl->assign("cod_arrendador",$arrendadores[$i]['cod_arrendador']);
	$tpl->assign("nombre_arrendador",strtoupper($arrendadores[$i]['nombre']));
	
	if(isset($_SESSION['recibos']))
		$tpl->assign("folio",$_SESSION['recibos']['folios'.$i]);
}
//print_r($arrendatarios);
$tpl->printToScreen();
die();
?>