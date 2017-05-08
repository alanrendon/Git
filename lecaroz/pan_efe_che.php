<?php

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos.";


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
$tpl->assignInclude("body","./plantillas/pan/pan_efe_che.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla

if(isset($_POST['fecha_registro'])){
	for($i=0;$i<10;$i++){
		if($_POST["num_cia".$i] != "" and $_POST['num_cia'.$i] != "" and $_POST['fecha'.$i])
			ejecutar_script("INSERT INTO revision_efectivos(num_cia, reporte, fecha_registro, fecha, estado) values(".$_POST['num_cia'.$i].", '".$_POST['reporte'.$i]."', '".$_POST['fecha_registro']."', '".$_POST['fecha'.$i]."', 'false')",$dsn);
	}

}




if(!isset($_GET['num_cia0'])){
	$permisos = ejecutar_script("select * from permiso_revision where id_user = {$_SESSION['iduser']}",$dsn);
	if(!$permisos){
		$tpl->newBlock("restriccion");
		$tpl->printToScreen();
		die();
	}

	$tpl->newBlock("efectivos");
	$tpl->assign("fecha",date("d/m/Y"));
	$tpl->assign("dia",date("d"));
	$tpl->assign("mes",date("n"));
	$tpl->assign("anio_actual",date("Y"));

	$sql="select num_cia,nombre_corto from catalogo_companias where num_cia < 300 or num_cia=999 order by num_cia";
	$cias=ejecutar_script($sql,$dsn);
	for($i=0;$i<count($cias);$i++){
		$tpl->newBlock("nombre_cia");
		$tpl->assign("num_cia",$cias[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cias[$i]['nombre_corto']);
	}
	// Crear los renglones
	for ($i=0;$i<10;$i++) {
		$tpl->newBlock("rows");
		$tpl->assign("i",$i);

		if(($i+1)>=10)
			$tpl->assign("next","0");
		else
			$tpl->assign("next",$i+1);
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

$tpl->printToScreen();
die();
?>