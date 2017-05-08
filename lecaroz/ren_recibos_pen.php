<?php
// CAPTURA PARA AFECTIVOS DIRECTOS
// TABLA "IMPORTE_EFECTIVOS"
// PANADERIAS -- EFECTIVOS -- CAPTURA DIRECTA
//define ('IDSCREEN',1321); // ID de pantalla
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No se encontraron recibos sin imprimir";
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
$tpl->assignInclude("body","./plantillas/ren/ren_recibos_pen.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['temp'])) {
	$tpl->newBlock("obtener_datos");
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
		$tpl->printToScreen();
		die();
	}
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
		$tpl->printToScreen();
		die();
	}
	
	$sql="select distinct(fecha) from recibos_rentas where impreso =false";
	$pendientes=ejecutar_script($sql,$dsn);
	
	if(!$pendientes){
		header("location: ./ren_recibos_pen.php?codigo_error=1");
		die();
	}
	for($i=0;$i<count($pendientes);$i++){
		$fecha=explode("/",$pendientes[$i]['fecha']);
		$tpl->newBlock("mes");
		$tpl->assign("mes",$fecha[1]);
		$tpl->assign("anio",$fecha[2]);
		$tpl->assign("nombre_mes",strtoupper(mes_escrito($fecha[1])));
	}


$tpl->printToScreen();
die();
}


$fecha="1/".$_GET['mes']."/".$_GET['anio'];
$sql="select * from recibos_rentas where fecha='$fecha' and impreso=false order by cod_arrendador, num_recibo";
$recibos=ejecutar_script($sql,$dsn);

$arreglo=array();
$aux=0;
$contador=0;
$auxiliar=0;
for($i=0;$i<count($recibos);$i++){
	if($auxiliar != $recibos[$i]['cod_arrendador']){
		$arreglo[$contador]['numero']=$recibos[$i]['cod_arrendador'];
		$arreglo[$contador]['finicio'] = $recibos[$i]['num_recibo'];
		$aux= $i-1;
		if($i > 0)
			$arreglo[$contador - 1]['ffinal'] = $recibos[$aux]['num_recibo'];
		$contador++;
		$auxiliar = $recibos[$i]['cod_arrendador'];
	}
}

$aux=$i-1;
$arreglo[$contador - 1]['ffinal']=$recibos[$aux]['num_recibo'];



$tpl->newBlock("impresion");
$tpl->assign("anio",$_GET['anio']);
$tpl->assign("nombre_mes",mes_escrito($_GET['mes']));

$tpl->assign("valor",count($arreglo));
for($i=0;$i<count($arreglo);$i++){
	$tpl->newBlock("recibos_arrendador");
	$nombre=ejecutar_script("select nombre from catalogo_arrendadores where cod_arrendador=".$arreglo[$i]['numero'],$dsn);
	$tpl->assign("cod_arrendador",$arreglo[$i]['numero']);
	$tpl->assign("finicio",$arreglo[$i]['finicio']);
	$tpl->assign("ffinal",$arreglo[$i]['ffinal']);
	$tpl->assign("nombre_arrendador",strtoupper($nombre[0]['nombre']));
}



$tpl->printToScreen();
die();
?>