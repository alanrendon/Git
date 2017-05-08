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
$tpl->assignInclude("body","./plantillas/pan/pan_fpan_pend.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
// Generar listado de turnos
// Si viene de una página que genero error

//print_r($_GET);
//$notas=ejecutar_script("select * from venta_pastel where tipo=0 and estado = 0 and num_cia=".$_GET['cias']." order by letra_folio,num_remi",$dsn);
$tpl->newBlock("facturas");
$tpl->assign("num_cia",$_GET['cias']);
$compa=obtener_registro("catalogo_companias",array('num_cia'),array($_GET['cias']),"","",$dsn);
$tpl->assign("nom_cia",$compa[0]['nombre_corto']);
$sql="select * from catalogo_operadoras where iduser=".$_SESSION['iduser'];

$operadora=ejecutar_script($sql,$dsn);
$tpl->assign("operadora",$operadora[0]['nombre_operadora']);
$notas=ejecutar_script("select * from venta_pastel where estado = 0 and fecha_entrega < '".$_GET['fecha']."' and num_cia=".$_GET['cias']." and tipo = 0 order by letra_folio,num_remi",$dsn);

if(!$notas){
	$tpl->newBlock("sin_notas");
}

else{
	
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
	$tpl->newBlock("con_notas");

}


$tpl->printToScreen();
?>