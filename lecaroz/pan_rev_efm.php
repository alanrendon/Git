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
$descripcion_error[1] = "No existen efectivos a modificar";
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
$tpl->assignInclude("body","./plantillas/pan/pan_rev_efm.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
// Generar listado de turnos
// Si viene de una página que genero error
if(!isset($_GET['capturistas']))
{
	$tpl->newBlock("obtener_datos");
	$sql="select modificacion_efectivos.num_cia, idoperadora, catalogo_operadoras.nombre from modificacion_efectivos join catalogo_companias using(num_cia) join catalogo_operadoras using (idoperadora) where fecha_autorizacion is null group by idoperadora,catalogo_operadoras.nombre,num_cia order by idoperadora";
	$usuarios=ejecutar_script($sql,$dsn);

	$aux=0;
	if($usuarios){
		for($i=0;$i<count($usuarios);$i++)
		{
			if($aux!=$usuarios[$i]['idoperadora']){
				$tpl->newBlock("capturistas");
				$tpl->assign("num_cap",$usuarios[$i]['idoperadora']);
				$tpl->assign("nom_cap",$usuarios[$i]['nombre']);
			}
			$aux=$usuarios[$i]['idoperadora'];
		}
	}
	else $tpl->assign("disabled","disabled");
	
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

$sql="select * from modificacion_efectivos where num_cia in (select num_cia from catalogo_companias where idoperadora=".$_GET['capturistas']." and num_cia <= 300 or num_cia in (702, 703) order by num_cia) and revisado = false order by num_cia";
$efectivos=ejecutar_script($sql,$dsn);
if(!$efectivos)
{
	header("location: ./pan_rev_efm.php?codigo_error=1");
	die();
}
$tpl->newBlock("efectivos");
$cap=obtener_registro("catalogo_operadoras", array("idoperadora"),array($_GET['capturistas']),"","",$dsn);
$tpl->assign("nom_usuario",$cap[0]['nombre']);
$tpl->assign("cont",count($efectivos));

for($i=0;$i<count($efectivos);$i++)
{
	$tpl->newBlock("renglones");
	$tpl->assign("i",$i);
	$tpl->assign("id",$efectivos[$i]['id']);
	$tpl->assign("fecha",$efectivos[$i]['fecha']);
	$tpl->assign("num_cia",$efectivos[$i]['num_cia']);
	$tpl->assign("descripcion",strtoupper($efectivos[$i]['descripcion']));
	$cia=obtener_registro("catalogo_companias", array("num_cia"),array($efectivos[$i]['num_cia']),"","",$dsn);
	$tpl->assign("nom_cia",$cia[0]['nombre_corto']);
}

$tpl->printToScreen();
?>