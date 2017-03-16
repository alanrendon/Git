<?php
// CONSULTA DE PRODUCCION
// Tabla 'produccion'
// Menu 'Panaderías->Producción'
//define ('IDSCREEN',1241); // ID de pantalla
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);
// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No tienes compañías a cargo";
$descripcion_error[2] = "No se tienen expendios en la compañía";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/pan/pan_exp_list.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['cia'])){
	$tpl->newBlock("obtener_datos");
	if($_SESSION['iduser'] == 1 or $_SESSION['iduser'] == 4 or $_SESSION['iduser'] == 62){
		$cias = ejecutar_script("SELECT * FROM catalogo_companias WHERE num_cia <= 300 order by num_cia",$dsn);
	}
	else{
		$user = ejecutar_script("select * from catalogo_operadoras where iduser = ".$_SESSION['iduser'],$dsn);
		if(!$user){
			header("location: pan_exp_list.php?codigo_error=1");
			die();
		}
		$cias=ejecutar_script("select * from catalogo_companias where idoperadora=".$user[0]['idoperadora']." and num_cia <= 300 order by num_cia",$dsn);
	}
	
	for($i=0;$i<count($cias);$i++){
		$tpl->newBlock("cias");
		$tpl->assign("num_cia",$cias[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cias[$i]['nombre_corto']);
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
// -------------------------------- SCRIPT ---------------------------------------------------------
// -------------------------------- Mostrar listado ---------------------------------------------------------

$expendios=ejecutar_script("select * from catalogo_expendios where num_cia=$_GET[cia] order by num_expendio",$dsn);
if(!$expendios){
	header("location: pan_exp_list.php?codigo_error=2");
	die();
}

$tpl->newBlock("listado");
$cia=obtener_registro("catalogo_companias",array("num_cia"),array($_GET['cia']),"","",$dsn);
$tpl->assign("num_cia",$cia[0]['num_cia']);
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);

for($i=0;$i<count($expendios);$i++){
	$tpl->newBlock("rows");
	$tpl->assign("num_exp",$expendios[$i]['num_expendio']);
	$tpl->assign("num_ref",$expendios[$i]['num_referencia']);
	$tpl->assign("nombre",strtoupper($expendios[$i]['nombre']));
	$tpl->assign("direccion",trim($expendios[$i]['direccion']) != '' ? strtoupper(trim($expendios[$i]['direccion'])) : '&nbsp;');
	$tipo=ejecutar_script("select * from tipo_expendio where idtipoexpendio = ".$expendios[$i]['tipo_expendio'],$dsn);
	$tpl->assign("tipo_exp",strtoupper($tipo[0]['descripcion']));
	$tpl->assign("porciento",$expendios[$i]['porciento_ganancia']);
	$tpl->assign("devolucion",$expendios[$i]['aut_dev'] == 't' ? 'SI' : '&nbsp;');

	if($expendios[$i]['importe_fijo']=="")
		$tpl->assign("importe","&nbsp;");
	else
		$tpl->assign("importe",number_format($expendios[$i]['importe_fijo'],2,'.',','));
}

$tpl->printToScreen();
// --------------------------------------------------------------------------------------------------------
?>