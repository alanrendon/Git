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
$descripcion_error[1] = "Número de compañía no existe, por favor revisalo";
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
$tpl->assignInclude("body","./plantillas/ren/ren_arrendador_mod.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// Seleccionar tabla

if(isset($_POST['cod_arrendador'])){
	if($_POST['tipo_persona']==0)
		$tipo='false';
	else
		$tipo='true';
	ejecutar_script("UPDATE catalogo_arrendadores SET nombre='".$_POST['nombre']."', representante='".$_POST['representante']."', tipo_persona='".$tipo."', num_acta='".$_POST['num_acta']."',num_notario=".$_POST['notario']." ,ent_fed='".$_POST['ent_fed']."' where cod_arrendador= ".$_POST['cod_arrendador'],$dsn);
	
	header("location: ./ren_arrendador_mod.php");
	die();
}

if(!isset($_GET['arrendador'])){
	$tpl->newBlock("obtener_arrendador");
	$arrendador=ejecutar_script("select cod_arrendador, nombre from catalogo_arrendadores order by cod_arrendador",$dsn);
	if($arrendador){
		for($i=0;$i<count($arrendador);$i++){
			$tpl->newBlock("nombre_arrendador");
			$tpl->assign("cod_arrendador",$arrendador[$i]['cod_arrendador']);
			$tpl->assign("nombre_arrendador",$arrendador[$i]['nombre']);
		}
	}
	$tpl->printToScreen();
	die();
}



$tpl->newBlock("modificar_datos");
$arrendador=ejecutar_script("select * from catalogo_arrendadores where cod_arrendador=".$_GET['arrendador'],$dsn);
$notario=ejecutar_script("select * from catalogo_notario order by cod_notario",$dsn);

$tpl->assign("id",$_GET['arrendador']);
$tpl->assign("nombre",$_GET['nombre_arrendador']);
$tpl->assign("representante",$arrendador[0]['representante']);
$tpl->assign("acta",$arrendador[0]['num_acta']);
$tpl->assign("entidad",$arrendador[0]['ent_fed']);

if($arrendador[0]['tipo_persona']=='f')
	$tpl->assign("checked1","checked");
else
	$tpl->assign("checked2","checked");



for($i=0;$i<count($notario);$i++){
	$tpl->newBlock("notario");
	if($notario[$i]['cod_notario'] == $arrendador[0]['num_notario'])
		$tpl->assign("selected","selected");
	$tpl->assign("cod_notario",$notario[$i]['cod_notario']);
	$tpl->assign("nombre_notario",$notario[$i]['nombre']);
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
die();

?>