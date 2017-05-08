<?php
// MODIFICACIÓN RÁPIDA DE UN PRODUCTO EN CONTROL DE PRODUCCION
// Tablas 'control_produccion'
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
//$descripcion_error[]
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_efm_minimod.tpl");
$tpl->prepare();
$venta=0;
$dif_otros=0;
$dif_venta=0;

//MODIFICACION DEL EFECTIVO
if (isset($_POST['idmodifica'])) {
	
	$sql="SELECT * FROM modificacion_efectivos WHERE id=".$_POST['idmodifica'];
	$modifica=ejecutar_script($sql,$dsn);
	
	$sql="SELECT * FROM captura_efectivos WHERE num_cia=".$_POST['num_cia']." and fecha='".$_POST['fecha']."'";
	$efectivo=ejecutar_script($sql,$dsn);
	
	$sql="SELECT * FROM total_panaderias WHERE num_cia=".$_POST['num_cia']." and fecha='".$_POST['fecha']."'";
	$total_cia=ejecutar_script($sql,$dsn);
	
	$efec_mas =number_format($_POST['venta_pta'],2,'.','')+number_format($_POST['pastillaje'],2,'.','')+number_format($_POST['otros'],2,'.','');
	$efec_menos =number_format($efectivo[0]['venta_pta'],2,'.','')+number_format($efectivo[0]['pastillaje'],2,'.','')+number_format($efectivo[0]['otros'],2,'.','');

	$sql="UPDATE total_panaderias set venta_puerta = venta_puerta - ".number_format($efectivo[0]['venta_pta'],2,'.','').", pastillaje=pastillaje - ".number_format($efectivo[0]['pastillaje'],2,'.','').", otros=otros-".number_format($efectivo[0]['otros'],2,'.','').", efectivo=efectivo - ".number_format($efec_menos,2,'.','')." WHERE num_cia=".$_POST['num_cia']." AND fecha='".$_POST['fecha']."'";
	ejecutar_script($sql,$dsn);
	
	$sql="UPDATE total_panaderias set venta_puerta = venta_puerta + ".number_format($_POST['venta_pta'],2,'.','').", pastillaje=pastillaje + ".number_format($_POST['pastillaje'],2,'.','').", otros=otros + ".number_format($_POST['otros'],2,'.','').", efectivo=efectivo + ".number_format($efec_mas,2,'.','')." WHERE num_cia=".$_POST['num_cia']." AND fecha='".$_POST['fecha']."'";
	ejecutar_script($sql,$dsn);
	
	$sql="UPDATE captura_efectivos set 
	am=".number_format($_POST['am'],2,'.','').", am_error=".number_format($_POST['am_error'],2,'.','').", pm=".number_format($_POST['pm'],2,'.','').", pm_error=".number_format($_POST['pm_error'],2,'.','').", venta_pta=".number_format($_POST['venta_pta'],2,'.','').", pastillaje=".number_format($_POST['pastillaje'],2,'.','').", otros=".number_format($_POST['otros'],2,'.','').", pastel=".number_format($_POST['pastel'],2,'.','').", ctes=".number_format($_POST['ctes'],2,'.','').", corte1=".number_format($_POST['corte1'],2,'.','').", corte2=".number_format($_POST['corte2'],2,'.','').", desc_pastel=".number_format($_POST['desc_pastel'],2,'.','')." WHERE num_cia=".$_POST['num_cia']." AND fecha='".$_POST['fecha']."'";
	ejecutar_script($sql,$dsn);
	
	// [07-Oct-2010] Actualizar estado si cambiaron los clientes
//	if ($total_cia[0]['status'] < -1) {
//		$sql = '
//			UPDATE
//				total_panaderias
//			SET
//				status = ' . ($total_cia[0]['status'] == -3 ? -1 : 1) . '
//			WHERE
//					num_cia = ' . $_POST['num_cia'] . '
//				AND
//					fecha = \'' . $_POST['fecha'] . '\'
//		';
//		ejecutar_script($sql, $dsn);
//	}
	
	$sql="UPDATE modificacion_efectivos set fecha_modificacion='".date("d/m/Y")."' WHERE id=".$_POST['idmodifica'];
	ejecutar_script($sql,$dsn);
	
	$tpl->newBlock("cerrar");
	$tpl->assign("num_cia",$_POST['num_cia']);
	$nom=ejecutar_script("select nombre_corto from catalogo_companias where num_cia=$_POST[num_cia]",$dsn);
	$tpl->assign("nombre",$nom[0]['nombre_corto']);
	
	$fecha=explode("/",$_POST['fecha']);
	$tpl->assign("dia",$fecha[0]);
	$tpl->assign("mes",mes_escrito($fecha[1]));
	$tpl->assign("anio",$fecha[2]);
	
	$tpl->assign("am",($_POST['am'] > 0)?number_format($_POST['am'],2,".",""):0);
	$tpl->assign("am_error",($_POST['am_error'] > 0)?number_format($_POST['am_error'],2,".",""):0);
	$tpl->assign("pm",($_POST['pm'] > 0)?number_format($_POST['pm'],2,".",""):0);
	$tpl->assign("pm_error",($_POST['pm_error'] > 0)?number_format($_POST['pm_error'],2,".",""):0);
	$tpl->assign("pastel",($_POST['pastel'] > 0)?number_format($_POST['pastel'],2,".",""):0);
	$tpl->assign("venta_pta", number_format($_POST['venta_pta'],2,".",""));
	$tpl->assign("pastillaje",($_POST['pastillaje'] > 0)?number_format($_POST['pastillaje'],2,".",""):0);
	$tpl->assign("otros",($_POST['otros'] > 0)?number_format($_POST['otros'],2,".",""):0);
	$tpl->assign("ctes",($_POST['ctes'] > 0)?number_format($_POST['ctes'],2,".",""):0);
	$tpl->assign("corte1",($_POST['corte1'] > 0)?number_format($_POST['corte1'],2,".",""):0);
	$tpl->assign("corte2",($_POST['corte2'] > 0)?number_format($_POST['corte2'],2,".",""):0);
	$tpl->assign("desc_pastel",($_POST['desc_pastel'] > 0)?number_format($_POST['desc_pastel'],2,".",""):0);

	$tpl->printToScreen();
	die;
}

//************************************************** EMPEZAMOS DE AQUI
$tpl->newBlock("modificar");

//print_r($_GET);
$tpl->assign("id",$_GET['idmodifica']);
$tpl->assign("num_cia",$_GET['num_cia']);
$nom=ejecutar_script("select nombre_corto from catalogo_companias where num_cia=$_GET[num_cia]",$dsn);
$tpl->assign("nombre",$nom[0]['nombre_corto']);
$fecha=explode("/",$_GET['fecha']);
$tpl->assign("dia",$fecha[0]);
$tpl->assign("mes",mes_escrito($fecha[1]));
$tpl->assign("anio",$fecha[2]);


$tpl->assign("fecha",$_GET['fecha']);
$sql="SELECT * FROM captura_efectivos WHERE num_cia=".$_GET['num_cia']." and fecha='".$_GET['fecha']."'";
$efectivo=ejecutar_script($sql,$dsn);
$tpl->assign("idefectivo",$efectivo[0]['idcaptura_efectivos']);
//$pastel=

$tpl->assign("am",($efectivo[0]['am'] > 0)?number_format($efectivo[0]['am'],2,".",""):0);
$tpl->assign("am_error",($efectivo[0]['am_error'] > 0)?number_format($efectivo[0]['am_error'],2,".",""):0);
$tpl->assign("pm",($efectivo[0]['pm'] > 0)?number_format($efectivo[0]['pm'],2,".",""):0);
$tpl->assign("pm_error",($efectivo[0]['pm_error'] > 0)?number_format($efectivo[0]['pm_error'],2,".",""):0);
$tpl->assign("pastel",($efectivo[0]['pastel'] > 0)?number_format($efectivo[0]['pastel'],2,".",""):0);
$tpl->assign("venta_pta", number_format($efectivo[0]['venta_pta'],2,".",""));
$tpl->assign("pastillaje",($efectivo[0]['pastillaje'] > 0)?number_format($efectivo[0]['pastillaje'],2,".",""):0);
$tpl->assign("otros",($efectivo[0]['otros'] > 0)?number_format($efectivo[0]['otros'],2,".",""):0);
$tpl->assign("ctes",($efectivo[0]['ctes'] > 0)?number_format($efectivo[0]['ctes'],2,".",""):0);
$tpl->assign("corte1",($efectivo[0]['corte1'] > 0)?number_format($efectivo[0]['corte1'],2,".",""):0);
$tpl->assign("corte2",($efectivo[0]['corte2'] > 0)?number_format($efectivo[0]['corte2'],2,".",""):0);
$tpl->assign("desc_pastel",($efectivo[0]['desc_pastel'] > 0)?number_format($efectivo[0]['desc_pastel'],2,".",""):0);

//abre los campos para modificar segun la solicitud


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
?>