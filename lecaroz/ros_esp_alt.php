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
$descripcion_error[1] = "La compañía no existe en la Base de Datos";
$descripcion_error[2] = "No hay registros";
$descripcion_error[3] = "Fecha incorrecta, vericar el formato (dd/mm/aaaa)";
$descripcion_error[4] = "Fecha fuera de rango, vericar el formato (dd/mm/aaaa)";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/ros/ros_esp_alt.tpl");

$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compañía -------------------------------------------------
if(isset($_POST['cia'])){

	for($i=0;$i<5;$i++){
		if($_POST['num_cia'.$i]!=""){
			if(existe_registro("control_expendios_rosticerias",array("num_cia","num_exp"),array($_POST['cia'],$_POST['num_cia'.$i]),$dsn))
				continue;
			else{
				$sql="INSERT INTO control_expendios_rosticerias(num_cia,num_exp) VALUES(".$_POST['cia'].",".$_POST['num_cia'.$i].")";
				ejecutar_script($sql,$dsn);
			}
		}
	}
	if(!existe_registro("control_expendios_rosticerias",array("num_cia","num_exp"),array($_POST['cia'],$_POST['cia']),$dsn)){
		$sql="INSERT INTO control_expendios_rosticerias(num_cia,num_exp) VALUES(".$_POST['cia'].",".$_POST['cia'].")";
		ejecutar_script($sql,$dsn);
	}
	header("location: ./ros_esp_alt.php");
	die();

}





if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio",date("Y"));
	
	$sql="select num_cia from catalogo_companias where num_cia between 301 and 599 EXCEPT select num_exp from control_expendios_rosticerias where num_exp not in (select distinct(num_cia) from control_expendios_rosticerias)";
	$cias=ejecutar_script($sql,$dsn);
	for($i=0;$i<count($cias);$i++){
		$tpl->newBlock("compania");
		$tpl->assign("num_cia",$cias[$i]['num_cia']);
		$nombre=ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia=".$cias[$i]['num_cia'],$dsn);
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




$tpl->newBlock("asignacion");
$tpl->assign("num_cia",$_GET['num_cia']);
$nombre=ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia=$_GET[num_cia]",$dsn);
$tpl->assign("nombre_cia",$nombre[0]['nombre_corto']);

for($i=0;$i<5;$i++){
	$tpl->newBlock("cias");
	$tpl->assign("i",$i);
	if(($i+1)==5)
		$tpl->assign("next",0);
	else
		$tpl->assign("next",$i+1);
}

$sql="select num_cia from catalogo_companias where num_cia between 301 and 599 EXCEPT select num_exp from control_expendios_rosticerias";
$cias=ejecutar_script($sql,$dsn);

for($i=0;$i<count($cias);$i++){
	$tpl->newBlock("nombre_cia2");
	$tpl->assign("num_cia",$cias[$i]['num_cia']);
	$nombre=ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia=".$cias[$i]['num_cia'],$dsn);
	$tpl->assign("nombre_cia",$nombre[0]['nombre_corto']);
}
$tpl->printToScreen();
// --------------------------------------------------------------------------------------------------------
?>