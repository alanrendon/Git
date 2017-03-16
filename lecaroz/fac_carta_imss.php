<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No tenemos productos registrados para esta compañía";
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
$tpl->assignInclude("body","./plantillas/fac/fac_carta_imss.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia0'])) {
	$tpl->newBlock("obtener_datos");
	$contadores=ejecutar_script("SELECT * from catalogo_contadores order by idcontador",$dsn);
	for($i=0;$i<count($contadores);$i++)
	{
		$tpl->newBlock("contadores");
		$tpl->assign("num_contador",$contadores[$i]['idcontador']);
		$tpl->assign("nombre",$contadores[$i]['nombre_contador']);
	}
	
	for($i=0;$i<20;$i++){
		$tpl->newBlock("rows");
		$tpl->assign("i",$i);
		$tpl->assign("next",$i+1);
		if(isset($_SESSION['carta_imss']))
		{
			$tpl->assign("num_proveedor",$_SESSION['carta_imss']['num_cia'.$i]);
			$tpl->assign("num_proveedor",$_SESSION['carta_imss']['num_emp'.$i]);
		}
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


//AQUI GENERA LA CARTA
$tpl->newBlock("carta");

$fecha_dia=date("d/m/Y");
$_dia=explode("/",$fecha_dia);
$nombre=mes_escrito($_dia[1],true);

$fecha="MÉXICO, D.F. A ".$_dia[0]." DE ".$nombre." DEL ".$_dia[2];
$tpl->assign("fecha", $fecha);
//print_r($_GET);
$nom_cont=obtener_registro("catalogo_contadores",array('idcontador'),array($_GET['contador']),"","",$dsn);
$tpl->assign("contador",$nom_cont[0]['nombre_contador']);

if($_GET['tipo_carta']==0)
	$tpl->assign("estado","ALTA");
else
	$tpl->assign("estado","BAJA");


for($i=0;$i<20;$i++)
{
	if($_GET['num_cia'.$i]!="" and $_GET['num_emp'.$i] !=""){
		if(existe_registro("catalogo_trabajadores",array("num_emp","num_cia"),array($_GET['num_emp'.$i],$_GET['num_cia'.$i]),$dsn)){
			$trabajador=obtener_registro("catalogo_trabajadores",array("num_emp","num_cia"),array($_GET['num_emp'.$i],$_GET['num_cia'.$i]),"","",$dsn);
			$tpl->newBlock("empleados");
			$nombre_compuesto= $trabajador[0]['ap_paterno']." ".$trabajador[0]['ap_materno']." ".$trabajador[0]['nombre'];
			$tpl->assign("nombre_empleado",$nombre_compuesto);
			$cia=obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia'.$i]),"","",$dsn);
			$tpl->assign("nombre_cia",$cia[0]['nombre']);
		}
		else
			continue;
	}
	else
		continue;
	
}


$tpl->printToScreen();

?>