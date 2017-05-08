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
$descripcion_error[1] = "Número de compañía no existe, por favor revisalo";
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
$tpl->assignInclude("body","./plantillas/ros/ros_control_pollo.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// Seleccionar tabla
//$tpl->assign("tabla",$session->tabla);
//$tpl->assign("tabla","total_cost_inv");
$lunes=0;
$martes=0;
$miercoles=0;
$jueves=0;
$viernes=0;
$sabado=0;
$domingo=0;
$lun=0;
$mar=0;
$mier=0;
$jue=0;
$vie=0;
$sab=0;
$dom=0;
$d=0;
$m=0;
$y=0;

$sql="select num_cia from catalogo_companias where num_cia between 100 and 200 order by num_cia";
$cias=ejecutar_script($sql,$dsn);

for($j=0;$j<count($cias);$j++)
{
	$lunes=0;
	$martes=0;
	$miercoles=0;
	$jueves=0;
	$viernes=0;
	$sabado=0;
	$domingo=0;
	$lun=0;
	$mar=0;
	$mier=0;
	$jue=0;
	$vie=0;
	$sab=0;
	$dom=0;
	$tpl->newBlock("rows");
	$sql="select num_cia, codmp, cantidad, fecha_mov from fact_rosticeria where fecha_mov >'2004/10/01' and codmp=160 and num_cia=".$cias[$j]['num_cia']." order by fecha_mov";
	$pollo=ejecutar_script($sql,$dsn);
	for($i=0;$i<count($pollo);$i++)
	{
		
		if(!($pollo[$i]['fecha_mov'])) continue;
		$_dt=explode("/",$pollo[$i]['fecha_mov']);
		$d = $_dt[0];
		$m = $_dt[1];
		$y = $_dt[2];
		$dia = date( "D", mktime(0,0,0,$m,$d,$y));
		
		switch($dia){
			case "Mon": 
						$lunes+=$pollo[$i]['cantidad'];
						$lun++;
						break;
			case "Tue":
						$martes+=$pollo[$i]['cantidad'];
						$mar++;
						break;
			case "Wed":
						$miercoles+=$pollo[$i]['cantidad'];
						$mier++;
						break;
			case "Thu":
						$jueves+=$pollo[$i]['cantidad'];
						$jue++;
						break;
			case "Fri": 
						$viernes+=$pollo[$i]['cantidad'];
						$vie++;
						break;
			case "Sat":
						$sabado+=$pollo[$i]['cantidad'];
						$sab++;
						break;
			case "Sun":
						$domingo+=$pollo[$i]['cantidad'];
						$dom++;
						break;
			break;
		}
	}
	if($lun>0)$lunes /= $lun;
	if($mar>0)$martes /= $mar;
	if($mier>0)$miercoles /= $mier;
	if($jue>0)$jueves /= $jue;
	if($vie>0)$viernes /= $vie;
	if($sab>0)$sabado /= $sab;
	if($dom>0)$domingo /= $dom;
	$tpl->assign("num_cia",$cias[$j]['num_cia']);
	$tpl->assign("codmp","160");
	$tpl->assign("lunes",number_format($lunes,2,'.',','));
	$tpl->assign("martes",number_format($martes,2,'.',','));
	$tpl->assign("miercoles",number_format($miercoles,2,'.',','));
	$tpl->assign("jueves",number_format($jueves,2,'.',','));
	$tpl->assign("viernes",number_format($viernes,2,'.',','));
	$tpl->assign("sabado",number_format($sabado,2,'.',','));
	$tpl->assign("domingo",number_format($domingo,2,'.',','));
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

// Imprimir el resultado
$tpl->printToScreen();
?>