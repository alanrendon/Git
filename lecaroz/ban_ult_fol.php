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
$descripcion_error[1] = "No se encontraron registros";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/ban/ban_ult_fol.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['mes'])){
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio",date("Y"));
	for($i=1;$i<=12;$i++){
		$tpl->newBlock("mes");
		$tpl->assign("mes",$i);
		$tpl->assign("nombre_mes",strtoupper(mes_escrito($i)));
		if($i==date("n"))
			$tpl->assign("selected","selected");
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
// -------------------------------- SCRIPT ---------------------------------------------------------
// -------------------------------- Mostrar listado ---------------------------------------------------------

$tpl->newBlock("listado");

$fecha_inicial="1/$_GET[mes]/$_GET[anio]";
$fecha_final=date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']));

$cias=ejecutar_script("select distinct(num_cia) from cheques where fecha between '$fecha_inicial' and '$fecha_final' order by num_cia",$dsn);

$tpl->assign("mes",strtoupper(mes_escrito($_GET['mes'])));
$tpl->assign("anio",$_GET['anio']);
if(!$cias){
	header("location: ./ban_ult_fol.php?codigo_error=1");
	die();
}

for($i=0;$i<count($cias);$i++){
	$cia=ejecutar_script("select num_cia, nombre_corto from catalogo_companias where num_cia=".$cias[$i]['num_cia'],$dsn);
	$cheque=ejecutar_script("select * from cheques where num_cia=".$cias[$i]['num_cia']." and fecha between '$fecha_inicial' and '$fecha_final' order by folio desc",$dsn);
	if(!$cheque)
		continue;
	$tpl->newBlock("rows");
	$tpl->assign("num_cia",$cias[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
	$tpl->assign("num_proveedor",$cheque[0]["num_proveedor"]);
	$tpl->assign("nombre_proveedor",$cheque[0]["a_nombre"]);
	$tpl->assign("fecha",$cheque[0]['fecha']);
	$tpl->assign("folio",$cheque[0]['folio']);
	$tpl->assign("importe",number_format($cheque[0]['importe'],2,'.',','));
}

$tpl->printToScreen();
// --------------------------------------------------------------------------------------------------------
?>