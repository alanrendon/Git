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
$tpl->assignInclude("body","./plantillas/ros/ros_control_pollo2.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// Seleccionar tabla
//$tpl->assign("tabla",$session->tabla);

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

$tpl->assign("tabla","control_pollos");
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
$var=0;

$sql="select num_cia,nombre_corto from catalogo_companias where num_cia between 100 and 200 order by num_cia";
$cias=ejecutar_script($sql,$dsn);
$sql="select distinct(codmp) from fact_rosticeria order by codmp";
$mp=ejecutar_script($sql,$dsn);
$anio=date("Y");
$anio--;


$pibote=date("d");
//$pibote1=0;
for($i=0;$i<=6;$i++){
	$fecha_final= date("j/n/Y",mktime(0,0,0,date("m"),$pibote,date("Y")));
	$letra= date("D",mktime(0,0,0,date("m"),$pibote,date("Y")));
	if($letra=="Sun"){
//		echo "$fecha_final <br>";
		for($j=0;$j<=6;$j++){
			$fecha_inicial=date("j/n/Y",mktime(0,0,0,date("m"),$pibote,date("Y")));
			$letra1=date("D",mktime(0,0,0,date("m"),$pibote,date("Y")));
			if($letra1=="Mon"){
//				echo "$fecha_inicial <br>";
				break;
			}
			$pibote--;
		}
		break;
	}
	$pibote--;
}

/*

if(date("n") < 8 and date("Y") <= 2005){
	$fecha_inicial="1/1/2005";
	$fecha_final="31/7/2005";
}
else{
	$fecha_inicial=date("j/n/Y",mktime(0,0,0,date("n"),1,$anio));
	$fecha_final=date("j/n/Y",mktime(0,0,0,date("n")+1,0,$anio));
}
*/

$count=0;
for($j=0;$j<count($cias);$j++)
{
	$tpl->newBlock("cias");
	$tpl->assign("num_cia",$cias[$j]['num_cia']);
	$tpl->assign("nombre_cia",$cias[$j]['nombre_corto']);
	for($z=0;$z<count($mp);$z++){
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

		$sql="select num_cia, codmp, cantidad, fecha_mov from fact_rosticeria where fecha_mov between '$fecha_inicial' and '$fecha_final' and codmp=".$mp[$z]['codmp']." and num_cia=".$cias[$j]['num_cia']." order by fecha_mov";
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
		if($lun==0 and $mar==0 and $mier==0 and $jue==0 and $vie==0 and $sab==0 and $dom==0) continue;
		$tpl->newBlock("rows");
		$count++;
		$tpl->assign("i",$var);
		$tpl->assign("num_cia",$cias[$j]['num_cia']);
		$tpl->assign("codmp",$mp[$z]['codmp']);
		$nombre=ejecutar_script("select nombre from catalogo_mat_primas where codmp=".$mp[$z]['codmp'],$dsn);
		$tpl->assign("nombre",$nombre[0]['nombre']);
		
		$tpl->assign("lunes",number_format($lunes,2,'.',','));
		$tpl->assign("martes",number_format($martes,2,'.',','));
		$tpl->assign("miercoles",number_format($miercoles,2,'.',','));
		$tpl->assign("jueves",number_format($jueves,2,'.',','));
		$tpl->assign("viernes",number_format($viernes,2,'.',','));
		$tpl->assign("sabado",number_format($sabado,2,'.',','));
		$tpl->assign("domingo",number_format($domingo,2,'.',','));
		$var++;
	}
	$tpl->gotoBlock("_ROOT");
	$tpl->assign("count",$count);
}
// Imprimir el resultado
$tpl->printToScreen();
?>