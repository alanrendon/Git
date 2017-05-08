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
$descripcion_error[1] = "No se encontraron registros";
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
$tpl->assignInclude("body","./plantillas/bal/bal_gast_caja_mod.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$nombremes[1]="ENERO";
$nombremes[2]="FEBRERO";
$nombremes[3]="MARZO";
$nombremes[4]="ABRIL";
$nombremes[5]="MAYO";
$nombremes[6]="JUNIO";
$nombremes[7]="JULIO";
$nombremes[8]="AGOSTO";
$nombremes[9]="SEPTIEMBRE";
$nombremes[10]="OCTUBRE";
$nombremes[11]="NOVIEMBRE";
$nombremes[12]="DICIEMBRE";

if(!isset($_GET['num_cia']))
{
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));
	$sql="select num_cia,nombre_corto from catalogo_companias where num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . " order by num_cia";
	$cias=ejecutar_script($sql,$dsn);
	for($i=0;$i<count($cias);$i++){
		$tpl->newBlock("nombre_cia");
		$tpl->assign("num_cia",$cias[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cias[$i]['nombre_corto']);
	}
	
	$tpl->gotoBlock("obtener_datos");
	for($i=0;$i<12;$i++){
		$tpl->newBlock("mes");
		$tpl->assign("mes",$i+1);
		$tpl->assign("nombre_mes",$nombremes[$i+1]);
		if(date("n")==($i+1))
			$tpl->assign("selected","selected");
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

$diasxmes[1] = 31; // Enero
if ($_GET['anio']%4 == 0)
	$diasxmes[2] = 29; // Febrero año bisiesto
else
	$diasxmes[2] = 28; // Febrero
$diasxmes[3] = 31; // Marzo
$diasxmes[4] = 30; // Abril
$diasxmes[5] = 31; // Mayo
$diasxmes[6] = 30; // Junio
$diasxmes[7] = 31; // Julio
$diasxmes[8] = 31; // Agosto
$diasxmes[9] = 30; // Septiembre
$diasxmes[10] = 31; // Octubre
$diasxmes[11] = 30; // Noviembre
$diasxmes[12] = 31; // Diciembre



$sql="SELECT * FROM gastos_caja where num_cia=".$_GET['num_cia']." and fecha between '1/".$_GET['mes']."/".$_GET['anio']."' and '".$diasxmes[$_GET['mes']]."/".$_GET['mes']."/".$_GET['anio']."'";
$gastos=ejecutar_script($sql,$dsn);
if(!$gastos){
	header("location: ./bal_gast_caja_mod.php?codigo_error=1");
	die();
}

$tpl->newBlock("modificar");

$tpl->assign("nombre_cia",$_GET['nombre']);
$tpl->assign("mes",$nombremes[$_GET['mes']]);
$tpl->assign("anio",$_GET['anio']);

$sql="SELECT * FROM catalogo_gastos_caja order by descripcion";
$gastos_caja=ejecutar_script($sql,$dsn);

$tpl->assign("contador",count($gastos));

for($i=0;$i<count($gastos);$i++){
	$tpl->newBlock("rows");
	$tpl->assign("i",$i);
	
	if(($i+1)==count($gastos))
		$tpl->assign("next",0);
	else
		$tpl->assign("next",$i+1);
	
	$tpl->assign("id",$gastos[$i]['id']);
	$tpl->assign("fecha",$gastos[$i]['fecha']);
	$tpl->assign("importe",number_format($gastos[$i]['importe'],2,'.',''));
	$tpl->assign('comentario', $gastos[$i]['comentario']);
	
	if($gastos[$i]['clave_balance']=='t')
		$tpl->assign("selected1","selected");
	else
		$tpl->assign("selected2","selected");
		
	if($gastos[$i]['tipo_mov']=='t')
		$tpl->assign("selected4","selected");
	else
		$tpl->assign("selected3","selected");
	
	for($j=0;$j<count($gastos_caja);$j++){
		$tpl->newBlock("concepto");
		$tpl->assign("codigo",$gastos_caja[$j]['id']);
		$tpl->assign("descripcion",$gastos_caja[$j]['descripcion']);
		if($gastos[$i]['cod_gastos']==$gastos_caja[$j]['id'])
			$tpl->assign("selected","selected");
	}
}



// Imprimir el resultado
$tpl->printToScreen();
?>