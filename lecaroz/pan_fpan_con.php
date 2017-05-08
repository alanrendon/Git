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
$descripcion_error[1] = "No se encontraron notas pendientes";
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
$tpl->assignInclude("body","./plantillas/pan/pan_fpan_con.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
// Generar listado de turnos
// Si viene de una página que genero error
if(!isset($_GET['operadora']))
{
	$tpl->newBlock("obtener_datos");
	$sql="select * from catalogo_operadoras where iduser=".$_SESSION['iduser'];
	$operadora=ejecutar_script($sql,$dsn);
	
	if($operadora){
		$tpl->assign("opera",$operadora[0]['idoperadora']);
		$tpl->newBlock("lista");
		$sql="select num_cia, nombre_corto, idoperadora from catalogo_companias where idoperadora=".$operadora[0]['idoperadora']." and num_cia <101 order by num_cia";
		$cias=ejecutar_script($sql,$dsn);
		
		for($i=0;$i<count($cias);$i++){
			$tpl->newBlock("cias");
			$tpl->assign("num_cia",$cias[$i]['num_cia']);
			$tpl->assign("nom_cia",$cias[$i]['nombre_corto']);
		}
		$tpl->gotoBlock("obtener_datos");
	}
	else{
//		$tpl->assign("disabled","disabled");
		if($_SESSION['iduser']==1 or $_SESSION['iduser']==4 or $_SESSION['iduser']==19 or $_SESSION['iduser']==12 or $_SESSION['iduser'] == 27){
			$tpl->newBlock("lista");
			$sql="select num_cia, nombre_corto, idoperadora from catalogo_companias where num_cia <101 order by num_cia";
			$cias=ejecutar_script($sql,$dsn);
			
			for($i=0;$i<count($cias);$i++){
				$tpl->newBlock("cia");
				$tpl->assign("num_cia",$cias[$i]['num_cia']);
				$tpl->assign("nombre",$cias[$i]['nombre_corto']);
			}
			
			$tpl->newBlock("mensaje");
			$tpl->assign("mensaje","USUARIO ADMINISTRADOR");
			$tpl->gotoBlock("obtener_datos");
		}
		else{
			$tpl->newBlock("mensaje");
			$tpl->assign("mensaje","NO TIENES ACCESO A ESTA INFORMACIÓN");
			$tpl->gotoBlock("obtener_datos");
		}
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


$notas=ejecutar_script("select * from venta_pastel where tipo=0 and estado = 0 and num_cia=".$_GET['cias']." order by letra_folio,num_remi",$dsn);

if(!$notas){
	header("location: ./pan_fpan_con.php?codigo_error=1");
	die();
}

$tpl->newBlock("facturas");
$tpl->assign("num_cia",$_GET['cias']);
$compa=obtener_registro("catalogo_companias",array('num_cia'),array($_GET['cias']),"","",$dsn);
$tpl->assign("nom_cia",$compa[0]['nombre_corto']);

//$opera=obtener_registro("catalogo_operadoras",array("idoperadora"),array($_GET['operadora']),"","",$dsn);
$opera=obtener_registro("catalogo_operadoras",array("idoperadora"),array($compa[0]['idoperadora']),"","",$dsn);
//print_r($opera);
$tpl->assign("operadora",$opera[0]['nombre_operadora']);



for($i=0;$i<count($notas);$i++){
	$tpl->newBlock("rows");
	if($notas[$i]['letra_folio']=='X')
		$tpl->assign("let_folio","");
	else
		$tpl->assign("let_folio",$notas[$i]['letra_folio']);
	$tpl->assign("num_fact",$notas[$i]['num_remi']);
		
	$tpl->assign("total",number_format($notas[$i]['total_factura'],2,'.',','));
	$tpl->assign("resta",number_format($notas[$i]['resta_pagar'],2,'.',','));
	$tpl->assign("fecha_entrega",$notas[$i]['fecha_entrega']);
	
}




$tpl->printToScreen();
?>