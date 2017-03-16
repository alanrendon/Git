<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No se encontraron registros";
$descripcion_error[1] = "Operadora no cuenta con compañías asignadas";
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
$tpl->assignInclude("body","./plantillas/adm/admin_opera_asign.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

	
// Si viene de una página que genero error

$sql="SELECT * FROM catalogo_operadoras/* WHERE idoperadora not in (8)*/ order by idoperadora";
$operadora=ejecutar_script($sql,$dsn);

if (!isset($_GET['temp'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("temp","1");

	for($j=0;$j<count($operadora);$j++){
		$tpl->newBlock("nombre_opera0");
		$tpl->assign("idoperadora",$operadora[$j]['idoperadora']);
		$tpl->assign("nombre_opera",$operadora[$j]['nombre']);
	}

	
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





if($_GET['status']==0){
	$tpl->newBlock("por_cia");
	$tpl->assign("status","0");
	$sql="SELECT * FROM catalogo_companias WHERE num_cia <= 300 or num_cia in(702, 703) order by num_cia";
	$cias=ejecutar_script($sql,$dsn);
	
	
	for($i=0;$i<count($cias);$i++){
		$tpl->newBlock("rows1");
		$tpl->assign("i",$i);
		$tpl->assign("num_cia",$cias[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cias[$i]['nombre_corto']);
		
		for($j=0;$j<count($operadora);$j++){
			$tpl->newBlock("nombre_opera1");
			$tpl->assign("idoperadora",$operadora[$j]['idoperadora']);
			$tpl->assign("nombre_opera",$operadora[$j]['nombre']);
			if($operadora[$j]['idoperadora']==$cias[$i]['idoperadora'])
				$tpl->assign("selected","selected");
		}
	}
	$tpl->gotoBlock("por_cia");
	$tpl->assign("contador",$i);
}

else if($_GET['status']==1){
	$tpl->newBlock("por_operadora");
	$tpl->assign("status","1");
	$sql="SELECT * FROM catalogo_companias WHERE num_cia <= 300 or num_cia in(702,703) order by num_cia";
	$cias=ejecutar_script($sql,$dsn);
	for($i=0;$i<count($cias);$i++){
		$tpl->newBlock("nombre_cia");
		$tpl->assign("i",$i);
		$tpl->assign("num_cia",$cias[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cias[$i]['nombre_corto']);
	}
	$tpl->gotoBlock("por_operadora");
	
	$operadora=obtener_registro("catalogo_operadoras",array('idoperadora'),array($_GET['operadora']),"","",$dsn);
	if($operadora){
		$sql="select num_cia,nombre_corto from catalogo_companias where idoperadora=".$operadora[0]['idoperadora']." order by num_cia";
		$cia_opera=ejecutar_script($sql,$dsn);
	}
	
	$sql="SELECT * FROM catalogo_operadoras WHERE idoperadora = ".$_GET['operadora'];
	$opera=ejecutar_script($sql,$dsn);
	$tpl->assign("operadora",$opera[0]['nombre']);
	$tpl->assign("idoperadora",$opera[0]['idoperadora']);
	for($i=0;$i<20;$i++){
		$tpl->newBlock("rows2");
		if(($i+1)>=20)
			$tpl->assign("next","0");
		else
			$tpl->assign("next",$i+1);
		
		$tpl->assign("i",$i);
		if($cia_opera){
			if($i<count($cia_opera)){
				$tpl->assign("num_cia",$cia_opera[$i]['num_cia']);
				$tpl->assign("nombre",$cia_opera[$i]['nombre_corto']);
			}
		}
	}	
}

else if($_GET['status']==2){
	$tpl->newBlock("por_traspaso");
	$tpl->assign("status","2");
	
	$sql="SELECT * FROM catalogo_operadoras WHERE idoperadora = ".$_GET['operadora'];
	$opera=ejecutar_script($sql,$dsn);
	$tpl->assign("operadora",$opera[0]['nombre']);
	if($opera){
		$sql="select num_cia,nombre_corto from catalogo_companias where idoperadora=".$opera[0]['idoperadora']." order by num_cia";
		$cias=ejecutar_script($sql,$dsn);
		
		if($cias){
			for($i=0;$i<count($cias);$i++){
				$tpl->newBlock("rows3");
				$tpl->assign("i",$i);
				$tpl->assign("num_cia",$cias[$i]['num_cia']);
				$tpl->assign("nombre_cia",$cias[$i]['nombre_corto']);
				for($j=0;$j<count($operadora);$j++){
					$tpl->newBlock("nombre_opera2");
					$tpl->assign("idoperadora",$operadora[$j]['idoperadora']);
					$tpl->assign("nombre_opera",$operadora[$j]['nombre']);
				}
			}
			$tpl->gotoBlock("por_traspaso");
			$tpl->assign("contador",$i);
		}
		else{
			header("location: ./admin_opera_asign.php?codigo_error=1");
			die();
		}
	}
}
$tpl->printToScreen();


?>