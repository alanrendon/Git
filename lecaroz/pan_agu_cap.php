<?php
// MEDIDORES DE AGUA
// Tabla 'medidor_agua'
// Menu Pandaderias -> Efectivos

define ('IDSCREEN',1323); //ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos.";


// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn,IDSCREEN);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla

if(!isset($_GET['num_cia0'])){
	$tpl->newBlock("obtener_datos");
//	print_r($_SESSION['agua']);
	
	$tpl->assign("tabla",$session->tabla);
	$tpl->assign("dia",date("d"));
	$tpl->assign("mes",date("n"));
	$tpl->assign("anio_actual",date("Y"));

	$operadora=obtener_registro("catalogo_operadoras",array('iduser'),array($_SESSION['iduser']),"","",$dsn);
	
	if($operadora){
		$sql="select num_cia,nombre_corto from catalogo_companias where idoperadora=".$operadora[0]['idoperadora']." order by num_cia";
		$cias=ejecutar_script($sql,$dsn);
		for($i=0;$i<count($cias);$i++){
			$tpl->newBlock("nombre_cia");
			$tpl->assign("num_cia",$cias[$i]['num_cia']);
			$tpl->assign("nombre_cia",$cias[$i]['nombre_corto']);
		}
	}
	else{
		$sql="select num_cia,nombre_corto from catalogo_companias where num_cia < 300 order by num_cia";
		$cias=ejecutar_script($sql,$dsn);
		for($i=0;$i<count($cias);$i++){
			$tpl->newBlock("nombre_cia");
			$tpl->assign("num_cia",$cias[$i]['num_cia']);
			$tpl->assign("nombre_cia",$cias[$i]['nombre_corto']);
		}
	}
	// Crear los renglones
	for ($i=0;$i<10;$i++) {
		$tpl->newBlock("rows");
		$tpl->assign("i",$i);
		if(isset($_SESSION['agua'])){
			$var= count($_SESSION['agua']);
			$var/=8;
			if($i<$var){
				$tpl->assign("cia",$_SESSION['agua']['num_cia'.$i]);
				$tpl->assign("nombre_cia",$_SESSION['agua']['nombre_cia'.$i]);
				$tpl->assign("fecha",$_SESSION['agua']['fecha'.$i]);
				$tpl->assign("hora",$_SESSION['agua']['hora'.$i]);
				$tpl->assign("medidor1",$_SESSION['agua']['medidor1'.$i]);
				$tpl->assign("medidor2",$_SESSION['agua']['medidor2'.$i]);
				$tpl->assign("medidor3",$_SESSION['agua']['medidor3'.$i]);
				$tpl->assign("medidor4",$_SESSION['agua']['medidor4'.$i]);
			}
		}
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

$tpl->newBlock("captura");


if(isset($_SESSION['agua'])) unset($_SESSION['agua']);
$estado_error=true;
$var=0;
for($i=0;$i<10;$i++){
	if($_GET['num_cia'.$i]!=""){
		$registros=obtener_registro("medidor_agua",array('num_cia'),array($_GET['num_cia'.$i]),"fecha","DESC",$dsn);
		$tpl->newBlock("rows1");
		$tpl->assign("i",$var);
		$tpl->assign("num_cia",$_GET['num_cia'.$i]);
		$var++;
		if(existe_registro("medidor_agua",array("num_cia","fecha"),array($_GET['num_cia'.$i],$_GET['fecha'.$i]),$dsn)){
			$estado_error=false;
			$tpl->newBlock("cia_error");
			$tpl->assign("num_cia",$_GET['num_cia'.$i]);
			$tpl->gotoBlock("rows1");
		}
		else{
			$tpl->newBlock("cia_ok");
			$tpl->assign("num_cia",$_GET['num_cia'.$i]);
			$tpl->gotoBlock("rows1");
		}	
		$tpl->assign("nombre_cia",$_GET['nombre_cia'.$i]);
		$tpl->assign("fecha",$_GET['fecha'.$i]);
		$tpl->assign("hora",$_GET['hora'.$i]);

		$tpl->assign("medida1",number_format(floatval($_GET['medida1'.$i]),2,'.',''));
		$tpl->assign("medida2",number_format(floatval($_GET['medida2'.$i]),2,'.',''));
		$tpl->assign("medida3",number_format(floatval($_GET['medida3'.$i]),2,'.',''));
		$tpl->assign("medida4",number_format(floatval($_GET['medida4'.$i]),2,'.',''));
		
		$_SESSION['agua']['num_cia'.$i]=$_GET['num_cia'.$i];
		$_SESSION['agua']['nombre_cia'.$i]=$_GET['nombre_cia'.$i];
		$_SESSION['agua']['fecha'.$i]=$_GET['fecha'.$i];
		$_SESSION['agua']['hora'.$i]=$_GET['hora'.$i];
		$_SESSION['agua']['medidor1'.$i]=$_GET['medida1'.$i];
		$_SESSION['agua']['medidor2'.$i]=$_GET['medida2'.$i];
		$_SESSION['agua']['medidor3'.$i]=$_GET['medida3'.$i];
		$_SESSION['agua']['medidor4'.$i]=$_GET['medida4'.$i];
		
		if(!$registros){
			$tpl->newBlock("med1_ok");
			$tpl->assign("medidor1",number_format(floatval($_GET['medida1'.$i]),2,'.',','));
			$tpl->gotoBlock("rows1");
			$tpl->newBlock("med2_ok");
			$tpl->assign("medidor2",number_format(floatval($_GET['medida2'.$i]),2,'.',','));
			$tpl->gotoBlock("rows1");
			$tpl->newBlock("med3_ok");
			$tpl->assign("medidor3",number_format(floatval($_GET['medida3'.$i]),2,'.',','));
			$tpl->gotoBlock("rows1");
			$tpl->newBlock("med4_ok");
			$tpl->assign("medidor4",number_format(floatval($_GET['medida4'.$i]),2,'.',','));
		}
		else{
			if($registros[0]['medida1'] >= $_GET['medida1'.$i]){
				if($registros[0]['medida1']==0 and number_format(floatval($_GET['medida1'.$i]),2,'.','') == 0){
					$tpl->newBlock("med1_ok");
					$tpl->assign("medidor1",number_format(floatval($_GET['medida1'.$i]),2,'.',','));
				}
				else{				
					$tpl->newBlock("med1_error");
					$estado_error=false;
					$tpl->assign("medidor1",number_format(floatval($_GET['medida1'.$i]),2,'.',','));
				}
			}
			else{
				$tpl->newBlock("med1_ok");
				$tpl->assign("medidor1",number_format(floatval($_GET['medida1'.$i]),2,'.',','));
			}
			if($registros[0]['medida2'] >= $_GET['medida2'.$i]){
				if($registros[0]['medida2']==0 and number_format(floatval($_GET['medida2'.$i]),2,'.','') == 0){
					$tpl->newBlock("med2_ok");
					$tpl->assign("medidor2",number_format(floatval($_GET['medida2'.$i]),2,'.',','));
				}
				else{
					$tpl->newBlock("med2_error");
					$estado_error=false;
					$tpl->assign("medidor2",number_format(floatval($_GET['medida2'.$i]),2,'.',','));
					}
			}
			else{
				$tpl->newBlock("med2_ok");
				$tpl->assign("medidor2",number_format(floatval($_GET['medida2'.$i]),2,'.',','));
			}
			if($registros[0]['medida3'] >= $_GET['medida3'.$i]){
				if($registros[0]['medida3']==0 and number_format(floatval($_GET['medida3'.$i]),2,'.','') == 0){
					$tpl->newBlock("med3_ok");
					$tpl->assign("medidor3",number_format(floatval($_GET['medida3'.$i]),2,'.',','));
				}
				else{
					$tpl->newBlock("med3_error");
					$estado_error=false;
					$tpl->assign("medidor3",number_format(floatval($_GET['medida3'.$i]),2,'.',','));
				}
			}
			else{
				$tpl->newBlock("med3_ok");
				$tpl->assign("medidor3",number_format(floatval($_GET['medida3'.$i]),2,'.',','));
			}
			if($registros[0]['medida4'] >= $_GET['medida4'.$i]){
				if($registros[0]['medida4']==0 and number_format(floatval($_GET['medida4'.$i]),2,'.','') == 0){
					$tpl->newBlock("med4_ok");
					$tpl->assign("medidor4",number_format(floatval($_GET['medida4'.$i]),2,'.',','));
				}
				else{
					$tpl->newBlock("med4_error");
					$estado_error=false;
					$tpl->assign("medidor4",number_format(floatval($_GET['medida4'.$i]),2,'.',','));
				}
			}
			else{
				$tpl->newBlock("med4_ok");
				$tpl->assign("medidor4",number_format(floatval($_GET['medida4'.$i]),2,'.',','));
			}
		}
	}
}
$tpl->gotoBlock("captura");
if($estado_error==false)
	$tpl->assign("disabled","disabled");

$tpl->printToScreen();
?>