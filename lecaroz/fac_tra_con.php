<?php
// CONSULTA DE INFONAVIT
// Tablas 'infonavit'
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

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
$descripcion_error[1] = "No se encontraron trabajadores";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_tra_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Pantalla inicial
if (!isset($_GET['cia'])) {
	$tpl->newBlock("obtener_datos");
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	
	$tpl->printToScreen();
	die();
}

$tpl->newBlock("listado");
if($_GET['tipo_con']==0){
	if($_GET['cia']=="")
		$sql="SELECT * from catalogo_trabajadores order by num_cia,nombre_completo";
	else
		$sql="SELECT * FROM catalogo_trabajadores WHERE num_cia=".$_GET['cia']." ORDER BY nombre_completo";
}
else if($_GET['tipo_con']==1)
{
	$sql="SELECT * FROM catalogo_trabajadores WHERE nombre_completo like '%".strtoupper($_GET['nombre'])."%' order by num_cia,nombre_completo";
}
else if($_GET['tipo_con']==2){
	$sql="SELECT * FROM catalogo_trabajadores WHERE num_afiliacion = "
}

$trabajadores = ejecutar_script($sql,$dsn);
if(!$trabajadores){
	header("location: ./pan_tra_con.php?codigo_error=1");
	die();
}
$aux=0;

for($i=0;$i<count($trabajadores);$i++){
	if($aux!=$trabajadores[$i]['num_cia']){
		$tpl->newBlock("cia");
		$cia=obtener_registro("catalogo_companias",array("num_cia"),array($trabajadores[$i]['num_cia']),"","",$dsn);
		$tpl->assign("num_cia",$trabajadores[$i]['num_cia']);
		$tpl->assign("nom_cia",$cia[0]['nombre_corto']);
		$aux=$trabajadores[$i]['num_cia'];
	}

	$tpl->newBlock("rows");
	$tpl->assign("num_emp",$trabajadores[$i]['num_emp']);
	$tpl->assign("nombre",$trabajadores[$i]['nombre_completo']);

	if($trabajadores[$i]['cod_turno'] !=""){
		$turno=obtener_registro("catalogo_turnos",array("cod_turno"),array($trabajadores[$i]['cod_turno']),"","",$dsn);
		if($turno)
			$tpl->assign("turno",$turno[0]['descripcion']);
	}
	if($trabajadores[$i]['cod_horario'] !=""){
		$horario=obtener_registro("catalogo_horarios",array("cod_horario"),array($trabajadores[$i]['cod_horario']),"","",$dsn);
		if($horario)
			$tpl->assign("horario",$horario[0]['descripcion']);
	}
	if($trabajadores[$i]['cod_puestos'] !=""){
		$puesto=obtener_registro("catalogo_puestos",array("cod_puestos"),array($trabajadores[$i]['cod_puestos']),"","",$dsn);
		if($puesto)
			$tpl->assign("puesto",$puesto[0]['descripcion']);
	}


	$aux=$trabajadores[$i]['num_cia'];
}

$tpl->printToScreen();


?>