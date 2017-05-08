<?php
// CAPTURA DE FACTURAS DE ROSTICERIAS
// Tabla ''
// Menu ''

//define ('IDSCREEN',1721); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos, revisa bien la compañia";
//$descripcion_error[2] = "Número de Gasto no existe en la Base de Datos, revisa bien codigo del gasto";

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
$tpl->assignInclude("body","./plantillas/ros/ros_capturas_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	die();
}
	
if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
	die();
}

//--ESTA FUNCION REGRESA LAS ULTMIAS FUNCIONES DE TRABAJO
function fecha_insercion($num_cia, $dsn, $tipo)
{
	if($tipo==1)
		$sql="SELECT * FROM fact_rosticeria WHERE num_cia='".$num_cia."' order by fecha_mov";
	else
		$sql="SELECT * FROM total_companias WHERE num_cia='".$num_cia."' order by fecha";

	$cias = ejecutar_script($sql,$dsn);
	$i=count($cias);
	if($tipo==1)
		$fecha_trabajo=$cias[$i-1]['fecha_mov'];
	else
		$fecha_trabajo=$cias[$i-1]['fecha'];
	//echo $fecha_trabajo;

	if($fecha_trabajo){
		$_dt=explode("/",$fecha_trabajo);
		$d2 = $_dt[0];
		$m2 = $_dt[1];
		$y2 = $_dt[2];
		
		$fecha=date( "d/m/Y", mktime(0,0,0,$m2,$d2,$y2) );
		return $fecha; 
	}
	else{
		$fecha=date( "d/m/Y", mktime(0,0,0,date("m"),date("d") - 1,date("Y")) );
	}
}
$cia="SELECT num_cia,nombre_corto FROM catalogo_companias WHERE status=true and num_cia between 301 and 599 or num_cia in (702, 704, 705) order by num_cia";
$compania=ejecutar_script($cia,$dsn);

for($i=0;$i<count($compania);$i++){
	$tpl->newBlock("rows");
	$tpl->assign("num_cia",$compania[$i]['num_cia']);
	$tpl->assign("nom_cia",$compania[$i]['nombre_corto']);
	$fecha=fecha_insercion($compania[$i]['num_cia'],$dsn,1);
	$tpl->assign("fecha_fac",$fecha);
	$fecha1=fecha_insercion($compania[$i]['num_cia'],$dsn,0);
	$tpl->assign("fecha_efe",$fecha1);
	if($fecha==$fecha1) $tpl->assign("estado","");
	else $tpl->assign("estado","RETRASO");
}



$tpl->printToScreen();

?>