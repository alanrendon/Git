<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---	------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No se encontraron registros";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/ros/ros_esp_mod.tpl");

$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if(isset($_POST['cia'])){
//print_r($_POST);
//echo "entre a borrar";
	if($_POST['borrado_general']==1){
		ejecutar_script("DELETE FROM control_expendios_rosticerias WHERE num_cia=$_POST[cia]",$dsn);
	}
	else{
		for($i=0;$i<$_POST['contador'];$i++){
			if($_POST['borrado'.$i]==1)
				ejecutar_script("DELETE FROM control_expendios_rosticerias WHERE num_cia=$_POST[cia] and num_exp=".$_POST['num_exp'.$i],$dsn);
		}
	}
	header("location: ./ros_esp_mod.php");
	die();
}


if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_datos");
	
	$cias=ejecutar_script("SELECT distinct(num_cia) FROM control_expendios_rosticerias order by num_cia",$dsn);
	for($i=0;$i<count($cias);$i++){
		$tpl->newBlock("cias");
		$tpl->assign("num_cia",$cias[$i]['num_cia']);
		$nombre=obtener_registro("catalogo_companias",array("num_cia"),array($cias[$i]['num_cia']),"","",$dsn);
		$tpl->assign("nombre_cia",$nombre[0]['nombre_corto']);
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
}


$expendios=ejecutar_script("select * from control_expendios_rosticerias where num_cia=$_GET[num_cia] order by num_exp",$dsn);
$tpl->newBlock("modificar");
$tpl->assign("num_cia",$_GET['num_cia']);
$nombre=obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
$tpl->assign("nombre_cia",$nombre[0]['nombre_corto']);
$tpl->assign("count",count($expendios)-1);
$var=0;
for($i=0;$i<count($expendios);$i++){
	if($expendios[$i]['num_exp']==$_GET['num_cia'])
		continue;
	$tpl->newBlock("cias1");
	$tpl->assign("i",$var);
	$tpl->assign("num_cia",$expendios[$i]['num_exp']);
	$nombre=obtener_registro("catalogo_companias",array("num_cia"),array($expendios[$i]['num_exp']),"","",$dsn);
	$tpl->assign("nombre_cia",$nombre[0]['nombre_corto']);
	$var++;
}

$tpl->printToScreen();
// --------------------------------------------------------------------------------------------------------
?>