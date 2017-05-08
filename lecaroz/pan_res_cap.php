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
$descripcion_error[2] = "Ya existen registros para esa fecha";

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
$tpl->assignInclude("body","./plantillas/pan/pan_res_cap.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_dato");
	$tpl->assign("anio_actual",date("Y"));
	$tpl->assign("mes",date("m"));
	$tpl->assign("dia",date("d"));

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

//$tpl->assign("tabla","");
$sql="select num_cia, nombre, num_expendio from catalogo_expendios where num_cia=".$_GET['num_cia']."order by num_cia, num_expendio";
$expendios=ejecutar_script($sql,$dsn);

if(!$expendios)
{
	header("location: ./pan_res_cap.php?codigo_error=1");
	die();
}

if(existe_registro("mov_expendios",array("num_cia","fecha"),array($_GET['num_cia'],$_GET['fecha']),$dsn)){
	header("location: ./pan_res_cap.php?codigo_error=2");
	die();
}


$tpl->newBlock("resagos");
$tpl->assign("num_cia",$_GET['num_cia']);
$nomcia = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
$tpl->assign("nom_cia",$nomcia[0]['nombre_corto']);
$tpl->assign("fecha",$_GET['fecha']);
$cont=0;
for($i=0;$i<count($expendios);$i++){
	$tpl->newBlock("rows");
	$tpl->assign("i",$i);
	$tpl->assign("next",$i+1);
	$cont++;
	$tpl->assign("nombre_exp",$expendios[$i]['nombre']);
	$tpl->assign("num_exp",$expendios[$i]['num_expendio']);
}
$tpl->gotoBlock("resagos");
$tpl->assign("cont",$cont);

// Imprimir el resultado
$tpl->printToScreen();
?>