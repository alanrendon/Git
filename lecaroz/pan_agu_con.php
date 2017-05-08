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
$descripcion_error[1] = "NO SE ENCONTRARON REGISTROS";


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
$tpl->assignInclude("body","./plantillas/pan/pan_agu_con.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla

if(!isset($_GET['num_cia'])){
	$tpl->newBlock("obtener_datos");
//	print_r($_SESSION['agua']);
	
	$tpl->assign("tabla",$session->tabla);
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



$sql="SELECT * FROM medidor_agua WHERE num_cia=".$_GET['num_cia']." and fecha between '".$_GET['fecha_inicio']."' and '".$_GET['fecha_final']."' order by fecha";
$registros=ejecutar_script($sql,$dsn);
if(!$registros){
	header("location: ./pan_agu_con.php?codigo_error=1");
	die();
}

$nombremes[1]="Enero";
$nombremes[2]="Febrero";
$nombremes[3]="Marzo";
$nombremes[4]="Abril";
$nombremes[5]="Mayo";
$nombremes[6]="Junio";
$nombremes[7]="Julio";
$nombremes[8]="Agosto";
$nombremes[9]="Septiembre";
$nombremes[10]="Octubre";
$nombremes[11]="Noviembre";
$nombremes[12]="Diciembre";


$_fecha=explode("/",$_GET['fecha_inicio']);
$_fecha1=explode("/",$_GET['fecha_final']);


$tpl->newBlock("consulta");

$tpl->assign("fecha1",$_fecha[0]." DE ".strtoupper($nombremes[$_fecha[1]])." DEL ".$_fecha[2]);
$tpl->assign("fecha2",$_fecha1[0]." DE ".strtoupper($nombremes[$_fecha1[1]])." DEL ".$_fecha1[2]);

$tpl->assign("num_cia",$_GET['num_cia']);
$ncia=obtener_registro("catalogo_companias",array('num_cia'),array($_GET['num_cia']),"","",$dsn);
$tpl->assign("nombre_cia",$ncia[0]['nombre_corto']);
//print_r($registros);
for($i=0;$i<count($registros);$i++){
	$tpl->newBlock("rows1");
	$tpl->assign("fecha",$registros[$i]['fecha']);
	$tpl->assign("hora",$registros[$i]['hora']);
	$tpl->assign("medidor1",number_format($registros[$i]['medida1'],2,'.',','));
	
	if($registros[$i]['medida2']==0)
		$tpl->assign("medidor2","");
	else
		$tpl->assign("medidor2",number_format($registros[$i]['medida2'],2,'.',','));
		
	if($registros[$i]['medida3']==0)
		$tpl->assign("medidor3","");
	else
		$tpl->assign("medidor3",number_format($registros[$i]['medida3'],2,'.',','));
		
	if($registros[$i]['medida4']==0)
		$tpl->assign("medidor4","");
	else
		$tpl->assign("medidor4",number_format($registros[$i]['medida4'],2,'.',','));
	
}

$total1=$registros[count($registros)-1]['medida1'] - $registros[0]['medida1'];
$total2=$registros[count($registros)-1]['medida2'] - $registros[0]['medida2'];
$total3=$registros[count($registros)-1]['medida3'] - $registros[0]['medida3'];
$total4=$registros[count($registros)-1]['medida4'] - $registros[0]['medida4'];

$tpl->gotoBlock("consulta");
if($total1==0) $tpl->assign("total1","");
else $tpl->assign("total1",number_format($total1,2,'.',','));

if($total2==0) $tpl->assign("total2","");
else $tpl->assign("total2",number_format($total2,2,'.',','));

if($total3==0) $tpl->assign("total3","");
else $tpl->assign("total3",number_format($total3,2,'.',','));

if($total4==0) $tpl->assign("total4","");
else $tpl->assign("total4",number_format($total4,2,'.',','));


if(count($registros)==1)
	$dias=1;
else if(count($registros) > 1)
	$dias=$registros[count($registros)-1]['fecha'] - $registros[0]['fecha'];
//echo $dias;
$tpl->assign("dias",$dias);


$p1=$total1/$dias;
$p2=$total2/$dias;
$p3=$total3/$dias;
$p4=$total4/$dias;

if($p1==0) $tpl->assign("p1","");
else $tpl->assign("p1",number_format($p1,2,'.',','));

if($p2==0) $tpl->assign("p2","");
else $tpl->assign("p2",number_format($p2,2,'.',','));

if($p3==0) $tpl->assign("p3","");
else $tpl->assign("p3",number_format($p3,2,'.',','));

if($p4==0) $tpl->assign("p4","");
else $tpl->assign("p4",number_format($p4,2,'.',','));



$tpl->printToScreen();
?>