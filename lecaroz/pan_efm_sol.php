<?php
// CONTROL DE BLOCKS
// Tabla 'BLOCKS'
// Menu
//define ('IDSCREEN',1620); //ID de pantalla
// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No tiene compañías a su cargo";
$descripcion_error[2] = "No hay registros que modificar";
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
$tpl->assignInclude("body","./plantillas/pan/pan_efm_sol.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
// Generar listado de turnos
// Si viene de una página que genero error
if(!isset($_GET['num_cia']))
{
	$tpl->newBlock("obtener_datos");
	$tpl->assign("dia",date("d"));
//	$tpl->assign("dia_m",date("d"));
	$tpl->assign("anio_actual",date("Y"));
	$tpl->assign("mes",date("m"));
	
	$sql="select * from catalogo_operadoras where iduser=".$_SESSION['iduser'];
	$usuario=ejecutar_script($sql,$dsn);
	
	if(!$usuario){
		if($_SESSION['iduser']==1 or $_SESSION['iduser']==4){
			$sql="select num_cia, nombre_corto from catalogo_companias where num_cia between 1 and 300 or num_cia=702 order by num_cia";
			$companias=ejecutar_script($sql,$dsn);
		}
		else{
			$tpl->assign("disabled","disabled");
			header("location: ./pan_efm_sol.php?codigo_error=1");
			die();
		}
	}
	else{
		$sql="SELECT * FROM catalogo_companias WHERE idoperadora=".$usuario[0]['idoperadora']." order by num_cia";
		$companias=ejecutar_script($sql,$dsn);
	}

	for($i=0;$i<count($companias);$i++)
	{
		$tpl->newBlock("nombre_cia");
		$tpl->assign("num_cia",$companias[$i]['num_cia']);
		$tpl->assign("nombre_cia",$companias[$i]['nombre_corto']);
	}
	$tpl->gotoBlock("obtener_datos");
	
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

if(existe_registro("captura_efectivos",array("num_cia","fecha"),array($_GET['num_cia'],$_GET['fecha']),$dsn)){
	$sql="INSERT INTO modificacion_efectivos(num_cia,fecha,descripcion,revisado,fecha_solicitud) VALUES(".$_GET['num_cia'].",'".$_GET['fecha']."','".$_GET['descripcion']."','false','".date("d/m/Y")."')";
	ejecutar_script($sql,$dsn);
	header("location: ./pan_efm_sol.php");
}
else{
	header("location: ./pan_efm_sol.php?codigo_error=2");
}

?>