<?php
// --------------------------------- INCLUDES -----------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No tenemos Materia Prima registrada para esta compañía";
//$descripcion_error[2] = "Número de Gasto no existe en la Base de Datos, revisa bien codigo del gasto";
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
$tpl->assignInclude("body","./plantillas/bal/bal_comp_list.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
if (!isset($_GET['temp'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));
	for($j=0;$j<12;$j++)
	{
		$tpl->newBlock("mes");
		switch ($j) {
		   case 0:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Enero");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 1:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Febrero");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 2:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Marzo");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
  		   case 3:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Abril");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 4:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Mayo");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 5:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Junio");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 6:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Julio");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 7:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Agosto");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 8:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Septiembre");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 9:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Octubre");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 10:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Noviembre");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 11:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Diciembre");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;

		}		
	}

	
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
$tpl->printToScreen();
die();
}
$fecha1="1/".$_GET['mes']."/".$_GET['anio'];
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

$nombre_mes[1]="Enero";
$nombre_mes[2]="Febrero";
$nombre_mes[3]="Marzo";
$nombre_mes[4]="Abril";
$nombre_mes[5]="Mayo";
$nombre_mes[6]="Junio";
$nombre_mes[7]="Julio";
$nombre_mes[8]="Agosto";
$nombre_mes[9]="Septiembre";
$nombre_mes[10]="Octubre";
$nombre_mes[11]="Noviembre";
$nombre_mes[12]="Diciembre";


$fecha2=$diasxmes[$_GET['mes']]."/".$_GET['mes']."/".$_GET['anio'];

$totales_accion = array();
$total_prueba_efectivos=0;
$total_efectivos=0;
$total_general=0;
$total_diferencia=0;
$total_diferencia1=0;
$total_depositos=0;

	if($_GET['mes']==12 and $_GET['anio']==2004)
		$companias=ejecutar_script("select distinct(num_cia), nombre_corto from catalogo_companias where num_cia between 101 and 200 or num_cia = 704 order by num_cia",$dsn);
	else	
		$companias=ejecutar_script("select distinct(num_cia), nombre_corto from catalogo_companias where num_cia between 1 and 200 or num_cia between 701 and 704 order by num_cia",$dsn);
	
	$paginas_companias=count($companias)/45;
	$paginas_companias=ceil($paginas_companias);

	$aux_cia1=0;
	if(count($companias) < 45)
		$aux_cia2=count($companias);
	else
		$aux_cia2=45;
	for($z=0;$z<$paginas_companias;$z++)
	{
//		echo "de la $aux_cia1 a la $aux_cia2";
		$tpl->newBlock("listado_todos");
		$tpl->assign("mes",strtoupper($nombre_mes[$_GET['mes']]));
		$tpl->assign("anio",$_GET['anio']);
		
		for($j=$aux_cia1;$j<$aux_cia2;$j++)
		{
			$tpl->newBlock("rows1");
			$tpl->assign("num_cia",$companias[$j]['num_cia']);
			$tpl->assign("nom_cia",$companias[$j]['nombre_corto']);

			$otros_depositos=ejecutar_script("select sum(importe) from otros_depositos where num_cia=".$companias[$j]['num_cia']." and fecha between '".$fecha1."' and '".$fecha2."'",$dsn);			
			$gastos_caja_ingresos=ejecutar_script("select sum(importe) from gastos_caja where num_cia=".$companias[$j]['num_cia']." and fecha between '".$fecha1."' and '".$fecha2."' and tipo_mov=true",$dsn);
			$gastos_caja_egresos=ejecutar_script("select sum(importe) from gastos_caja where num_cia=".$companias[$j]['num_cia']." and fecha between '".$fecha1."' and '".$fecha2."' and tipo_mov=false",$dsn);
			$prueba_efectivo=$otros_depositos[0]['sum'] + $gastos_caja_ingresos[0]['sum'] - $gastos_caja_egresos[0]['sum'];
			$total_prueba_efectivos+=$prueba_efectivo;
			
			if($prueba_efectivo==0) $tpl->assign("distribucion","");
			else $tpl->assign("distribucion",number_format($prueba_efectivo,2,'.',','));
			
			if($companias[$j]['num_cia'] >0 and $companias[$j]['num_cia'] <101 or($companias[$j]['num_cia'] >700 and $companias[$j]['num_cia'] <704))
			{
				$sql="select sum(efectivo) from total_panaderias where num_cia=".$companias[$j]['num_cia']." and fecha between '$fecha1' and '$fecha2'";
				$efectivo_pan=ejecutar_script($sql,$dsn);
				if($efectivo_pan[0]['sum']==0) $tpl->assign("efectivo","");
				else $tpl->assign("efectivo",number_format($efectivo_pan[0]['sum'],2,'.',','));

				$sql="select sum(importe) from estado_cuenta where num_cia=".$companias[$j]['num_cia']." and fecha between '$fecha1' and '$fecha2' and cod_mov in(1,16)";
				$depositos_pan=ejecutar_script($sql,$dsn);

				$depositos_cia=$depositos_pan[0]['sum'] + $otros_depositos[0]['sum'];
				if($depositos_cia==0) $tpl->assign("deposito","");
				else $tpl->assign("deposito",number_format($depositos_cia,2,'.',','));

				$diferencia1= $efectivo_pan[0]['sum']-$depositos_cia;
				if($diferencia1==0) $tpl->assign("diferencia1","");
				else $tpl->assign("diferencia1",number_format($diferencia1,2,'.',','));
				
				$total_efectivos+=$efectivo_pan[0]['sum'];
				$total_depositos+=$depositos_cia;
				$total_diferencia1+=$diferencia1;				
				
			}
			if($companias[$j]['num_cia'] > 100 and $companias[$j]['num_cia'] < 201 or $companias[$j]['num_cia']==704)
			{
				$sql="select sum(efectivo) from total_companias where num_cia=".$companias[$j]['num_cia']." and fecha between '$fecha1' and '$fecha2'";
				$efectivo_ros=ejecutar_script($sql,$dsn);
				if($efectivo_ros[0]['sum']==0) $tpl->assign("efectivo","");
				else $tpl->assign("efectivo",number_format($efectivo_ros[0]['sum'],2,'.',','));

				$sql="select sum(importe) from estado_cuenta where num_cia=".$companias[$j]['num_cia']." and fecha between '$fecha1' and '$fecha2' and cod_mov in(16,1)";
				$depositos_ros=ejecutar_script($sql,$dsn);
				$depositos_ros=$depositos_ros[0]['sum'] +$otros_depositos[0]['sum'];
				if($depositos_ros==0) $tpl->assign("deposito","");
				else $tpl->assign("deposito",number_format($depositos_ros,2,'.',','));				

				$diferencia1= $efectivo_ros[0]['sum']-$depositos_ros;
				if($diferencia1==0) $tpl->assign("diferencia1","");
				else $tpl->assign("diferencia1",number_format($diferencia1,2,'.',','));

				$total_efectivos+=$efectivo_ros[0]['sum'];
				$total_depositos+=$depositos_ros;
				$total_diferencia1+=$diferencia1;				

			}
			
		}

		$aux_cia1=$aux_cia2;
		if($aux_cia2 +45 > count($companias))
			$aux_cia2=count($companias);
		else
			$aux_cia2+=45;
	}
	$tpl->newBlock("totales");
	$tpl->assign("total_distribucion",number_format($total_prueba_efectivos,2,'.',','));
//	$tpl->assign("total_general",number_format($total_general,2,'.',','));
//	$tpl->assign("total_diferencia",number_format($total_diferencia,2,'.',','));
	$tpl->assign("total_efectivo",number_format($total_efectivos,2,'.',','));
	$tpl->assign("total_deposito",number_format($total_depositos,2,'.',','));
	$tpl->assign("total_diferencia1",number_format($total_diferencia1,2,'.',','));

$tpl->printToScreen();
die();

?>