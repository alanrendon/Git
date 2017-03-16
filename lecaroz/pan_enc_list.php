<?php
// CONSULTA DE PRODUCCION
// Tabla 'produccion'
// Menu 'Panaderías->Producción'
//define ('IDSCREEN',1241); // ID de pantalla
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
$descripcion_error[1] = "No tienes compañías a cargo";
$descripcion_error[2] = "No hay movimientos";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/pan/pan_enc_list.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['cia'])){
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));
	for($i=1;$i<=12;$i++){
		$tpl->newBlock("mes");
		$tpl->assign("mes",$i);
		$tpl->assign("nombre_mes",strtoupper(mes_escrito($i)));
		if($i==date("n"))
			$tpl->assign("select","selected");
	}
/*
	
	for($i=0;$i<count($cias);$i++){
		$tpl->newBlock("cias");
		$tpl->assign("num_cia",$cias[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cias[$i]['nombre_corto']);
	}
*/

	
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

// -------------------------------- Mostrar listado ---------------------------------------------------------

if($_GET['cia']==""){
	if($_SESSION['iduser'] == 1 or $_SESSION['iduser'] == 4){
		$cias = ejecutar_script("SELECT * FROM catalogo_companias WHERE num_cia < 100 order by num_cia",$dsn);
	}
	else{
		$user = ejecutar_script("select * from catalogo_operadoras where iduser = ".$_SESSION['iduser'],$dsn);
		if(!$user){
			header("location: ./pan_enc_list.php?codigo_error=1");
			die();
		}
		$cias=ejecutar_script("select * from catalogo_companias where idoperadora=".$user[0]['idoperadora']." and num_cia <= 300 order by num_cia",$dsn);
	}
}
else
	$cias=ejecutar_script("select * from catalogo_companias where num_cia=$_GET[cia]",$dsn);



$fecha_inicial="1/$_GET[mes]/$_GET[anio]";
$fecha_final=date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']));

$tpl->newBlock("listado");
$tpl->assign("mes",strtoupper(mes_escrito($_GET['mes'])));
$tpl->assign("anio",$_GET['anio']);

for($i=0;$i<count($cias);$i++){
	$gastos=ejecutar_script("select * from movimiento_gastos where num_cia=".$cias[$i]["num_cia"]." and codgastos=2 and fecha between '$fecha_inicial' and '$fecha_final' order by fecha",$dsn);
	if(!$gastos){
		if($_GET['cia']=="")
			continue;
		else{
			header("location: ./pan_enc_list.php?codigo_error=2");
			die();
		}
	}
	$tpl->newBlock("cias");
	$tpl->assign("num_cia",$cias[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cias[$i]['nombre_corto']);
	$encargado=ejecutar_script("select * from encargados where num_cia=".$cias[$i]['num_cia']." and mes=$_GET[mes] and anio=$_GET[anio]",$dsn);
	$tpl->assign("nombre_inicia",$encargado[0]['nombre_inicio']);
	$tpl->assign("nombre_termina",$encargado[0]['nombre_fin']);
	$total_pago=0;
	for($j=0;$j<count($gastos);$j++){
		$tpl->newBlock("rows");
		$_dia=explode("/",$gastos[$j]['fecha']);
		$tpl->assign("fecha",$_dia[0]);
		$tpl->assign("concepto",$gastos[$j]['concepto']);
		$tpl->assign("importe",number_format($gastos[$j]['importe'],2,'.',','));
		$total_pago += $gastos[$j]['importe'];
	}
	
	$limite=ejecutar_script("select * from catalogo_limite_gasto where num_cia=".$cias[$i]['num_cia']." and codgastos=2",$dsn);
	if($limite){
		$tpl->gotoBlock("cias");
		$tpl->assign("total",number_format($total_pago,2,'.',','));
		$tpl->assign("limite",number_format($limite[0]['limite'],2,'.',','));
		$diferencia=$limite[0]['limite'] - $total_pago;
		if($total_pago > $limite[0]['limite'])
			$tpl->assign("color","FF0000");
		else
			$tpl->assign("color","0000FF");
		$tpl->assign("diferencia",number_format($diferencia,2,'.',','));
	}
	else{
		$tpl->gotoBlock("cias");
		$tpl->assign("total",number_format($total_pago,2,'.',','));
		$tpl->assign("limite",number_format($limite[0]['limite'],2,'.',','));
		$tpl->assign("color","FF0000");
		$tpl->assign("diferencia",number_format($total_pago,2,'.',','));
	}
}

$tpl->printToScreen();
// --------------------------------------------------------------------------------------------------------
?>