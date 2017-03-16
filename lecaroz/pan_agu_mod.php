<?php
// MEDIDORES DE AGUA
// Tabla 'medidor_agua'
// Menu Pandaderias -> Efectivos

//define ('IDSCREEN',1323); //ID de pantalla

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
$tpl->assignInclude("body","./plantillas/pan/pan_agu_mod.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
//**************************************************************PANTALLA PARA LA MODIFICACION DE LOS MEDIDORES
if(isset($_POST['cont'])){
$tpl->newBlock("modifica");

$fecha=explode("/",$_POST['fecha2']);
$tpl->assign("dia",$fecha[0]);
$tpl->assign("mes",strtoupper(mes_escrito($fecha[1])));
$tpl->assign("anio",$fecha[2]);
$tpl->assign("fecha_link",$_POST['fecha2']);
$var=0;
	for($i=0;$i<$_POST['cont'];$i++){
		if($_POST['modificar'.$i]==1){
			$tpl->newBlock("rows1");
			$tpl->assign("i",$var);
			$var++;
			$tpl->assign("next",$var);
			$tpl->assign("idagua",$_POST['idagua'.$i]);
			$tpl->assign("num_cia",$_POST['num_cia'.$i]);
			$tpl->assign("nombre_cia",$_POST['nombre_cia'.$i]);
			
			if($_POST['medidor1'.$i]==0)
				$tpl->assign("medida1","");
			else
				$tpl->assign("medida1",$_POST['medidor1'.$i]);
				
			if($_POST['medidor2'.$i]==0)
				$tpl->assign("medida2","");
			else
				$tpl->assign("medida2",$_POST['medidor2'.$i]);
			
			if($_POST['medidor3'.$i]==0)
				$tpl->assign("medida3","");
			else
				$tpl->assign("medida3",$_POST['medidor3'.$i]);
			
			if($_POST['medidor4'.$i]==0)
				$tpl->assign("medida4","");
			else
				$tpl->assign("medida4",$_POST['medidor4'.$i]);
		}
	}
$tpl->gotoBlock("modifica");
$tpl->assign("cont",$var);
$tpl->printToScreen();
die();

}

//**********************************************************************CAPTURA DE LA FECHA DE CONSULTA
if(!isset($_GET['fecha'])){
	$tpl->newBlock("obtener_datos");
//	print_r($_SESSION['agua']);
	
	$tpl->assign("dia",date("d"));
	$tpl->assign("mes",date("n"));
	$tpl->assign("anio_actual",date("Y"));

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


//*******************************************************CONSULTA DE LOS MEDIDORES DE AGUA POR FECHA
$operadora=obtener_registro("catalogo_operadoras",array('iduser'),array($_SESSION['iduser']),"","",$dsn);
if($operadora){
	$sql="select num_cia,nombre_corto from catalogo_companias where idoperadora=".$operadora[0]['idoperadora']." order by num_cia";
	$cias=ejecutar_script($sql,$dsn);
}
else{
	$sql="select num_cia,nombre_corto from catalogo_companias where num_cia < 101 or num_cia=999 order by num_cia";
	$cias=ejecutar_script($sql,$dsn);
}

$tpl->newBlock("cias");
$var=0;
$fecha=explode("/",$_GET['fecha']);
$tpl->assign("dia",$fecha[0]);
$tpl->assign("mes",strtoupper(mes_escrito($fecha[1])));
$tpl->assign("anio",$fecha[2]);
$tpl->assign("fecha",$_GET['fecha']);

for($i=0;$i<count($cias);$i++){
	$agua=ejecutar_script("select * from medidor_agua where num_cia=".$cias[$i]['num_cia']." and fecha= '$_GET[fecha]'",$dsn);

	if($agua){
		$tpl->newBlock("rows");
		$tpl->assign("i",$var);
		$var++;
		$tpl->assign("num_cia",$cias[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cias[$i]['nombre_corto']);
		$tpl->assign("hora",$agua[0]['hora']);
		$tpl->assign("idagua",$agua[0]['id']);

		$tpl->assign("medidor1",number_format($agua[0]['medida1'],2,'.',''));
		$tpl->assign("medidor2",number_format($agua[0]['medida2'],2,'.',''));
		$tpl->assign("medidor3",number_format($agua[0]['medida3'],2,'.',''));
		$tpl->assign("medidor4",number_format($agua[0]['medida4'],2,'.',''));
		
		if($agua[0]['medida1']==0)
			$tpl->assign("medida1","");
		else
			$tpl->assign("medida1",number_format($agua[0]['medida1'],2,'.',','));
		
		if($agua[0]['medida2']==0)
			$tpl->assign("medida2","");
		else
			$tpl->assign("medida2",number_format($agua[0]['medida2'],2,'.',','));
			
		if($agua[0]['medida3']==0)
			$tpl->assign("medida3","");
		else
			$tpl->assign("medida3",number_format($agua[0]['medida3'],2,'.',','));
			
		if($agua[0]['medida4']==0)
			$tpl->assign("medida4","");
		else
			$tpl->assign("medida4",number_format($agua[0]['medida4'],2,'.',','));
	}
}
$tpl->gotoBlock("cias");
$tpl->assign("count",$var);

if($var == 0)
	$tpl->newBlock("aviso");


$tpl->printToScreen();
?>