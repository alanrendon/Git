<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
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
$tpl->assignInclude("body","./plantillas/adm/admin_porcentajes_con.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
if (!isset($_GET['con'])) {
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

$fecha2=$diasxmes[$_GET['mes']]."/".$_GET['mes']."/".$_GET['anio'];

$totales_accion = array();
$limite_cias=61;


//	$companias=ejecutar_script("select distinct(num_cia) from catalogo_companias where num_cia <= 100 order by num_cia",$dsn);
	
//	if($_GET['con']==0)	
//	{
		$companias=ejecutar_script("select distinct(accionistas.num_cia), nombre_corto from accionistas join catalogo_companias using(num_cia) order by accionistas.num_cia",$dsn);
		$porcentajes=ejecutar_script("select * from accionistas order by num_cia, accionista",$dsn);
/*	}
	else
	{
		$companias=ejecutar_script("select distinct(distribuciones.num_cia), nombre_corto from distribuciones join catalogo_companias using(num_cia) order by distribuciones.num_cia",$dsn);
		$porcentajes=ejecutar_script("select * from distribuciones order by num_cia, accionista",$dsn);
	}
*/
	$accionistas=ejecutar_script("select * from catalogo_accionistas order by num",$dsn);
	
	for($i=0;$i<count($accionistas);$i++)
	{
		$totales_accion[$i]=0;
	}
	$total_accionistas=count($accionistas);
	$total_companias=count($companias);

	$paginas_accionistas = $total_accionistas/7;
	$paginas_companias = $total_companias/$limite_cias;
	
	$paginas_accionistas=ceil($paginas_accionistas);
	$paginas_companias=ceil($paginas_companias);
	$total_paginas=$paginas_accionistas + $paginas_companias;
	$aux_acc1=0;
	$aux_acc2=7;

	$aux_cia1=0;
	if(count($companias) <= $limite_cias)
		$aux_cia2=count($companias);
	else
		$aux_cia2=$limite_cias;


	
for($e=0;$e<$paginas_accionistas;$e++)
{	
//print_r($porcentajes);
	$aux_cia1=0;
	if(count($companias) <= $limite_cias)
		$aux_cia2=count($companias);
	else
		$aux_cia2=$limite_cias;
	$total_prueba_efectivo=0;
	for($z=0;$z<$paginas_companias;$z++)
	{
//		echo "de la $aux_cia1 a la $aux_cia2";
		$tpl->newBlock("listado_todos");
		//echo "$aux_acc1 - $aux_acc2<br>";print_r($accionistas);
		for($i=$aux_acc1;$i<$aux_acc2;$i++)
		{//echo "$i<br>";
			$tpl->newBlock("accionistas");
			$tpl->assign("accionista",isset($accionistas[$i]['nombre_corto']) ? $accionistas[$i]['nombre_corto'] : '&nbsp;');
		}
		for($j=$aux_cia1;$j<$aux_cia2;$j++)
		{
			if($_GET['tipo_con']==0){
				if ($_GET['tipo'] == 1) {
					$otros_depositos=ejecutar_script("select sum(importe) from otros_depositos where num_cia=".$companias[$j]['num_cia']." and fecha between '".$fecha1."' and '".$fecha2."'",$dsn);			
					$gastos_caja_ingresos=ejecutar_script("select sum(importe) from gastos_caja where num_cia=".$companias[$j]['num_cia']." and fecha between '".$fecha1."' and '".$fecha2."' and tipo_mov=true",$dsn);
					$gastos_caja_egresos=ejecutar_script("select sum(importe) from gastos_caja where num_cia=".$companias[$j]['num_cia']." and fecha between '".$fecha1."' and '".$fecha2."' and tipo_mov=false",$dsn);
					$prueba_efectivo=$otros_depositos[0]['sum'] + $gastos_caja_ingresos[0]['sum'] - $gastos_caja_egresos[0]['sum'];
				}
				else {
					$sql = "SELECT utilidad_neta AS importe FROM balances_pan WHERE num_cia = {$companias[$j]['num_cia']} AND mes = $_GET[mes] AND anio = $_GET[anio] UNION";
					$sql .= " SELECT utilidad_neta FROM balances_ros WHERE num_cia = {$companias[$j]['num_cia']} AND mes = $_GET[mes] AND anio = $_GET[anio]";
					$tmp = ejecutar_script($sql, $dsn);
					$prueba_efectivo=$tmp ? $tmp[0]['importe'] : false;
				}
				if(!$prueba_efectivo) continue;
			}
			else{
				$porcentajes=ejecutar_script("select sum(porcentaje) from accionistas where num_cia=".$companias[$j]['num_cia'],$dsn);
			}
			$tpl->newBlock("rows1");
			$tpl->assign("num_cia",$companias[$j]['num_cia']);
			$tpl->assign("nom_cia",$companias[$j]['nombre_corto']);
			
			if($_GET['tipo_con']==0){
				if($prueba_efectivo==0) $tpl->assign("importe","");
				else $tpl->assign("importe",number_format($prueba_efectivo,2,'.',','));
				$total_prueba_efectivo+=$prueba_efectivo;
			}
			else{
				$tpl->assign("importe",$porcentajes[0]['sum']);
				$total_prueba_efectivo+=$porcentajes[0]['sum'];
			}
			for($y=$aux_acc1;$y<$aux_acc2;$y++)
			{
				$tpl->newBlock("porcentaje");
				
				$porc=isset($accionistas[$y]['num']) ? ejecutar_script("select porcentaje from accionistas where num_cia=".$companias[$j]['num_cia']." and accionista=".$accionistas[$y]['num'],$dsn) : array(0 => array('porcentaje' => 0));
				if($_GET['tipo_con']==0){
					$porcentaje_accionista=($porc[0]['porcentaje'] /100) * $prueba_efectivo; 
					if (isset($totales_accion[$y]))
						$totales_accion[$y]+=$porcentaje_accionista;
				if ($porcentaje_accionista==0) 	$tpl->assign("porcentaje","");
				else $tpl->assign("porcentaje",number_format($porcentaje_accionista,2,'.',','));

				}
				else{
					$porcentaje_accionista = $porc[0]['porcentaje'];
					$totales_accion[$y]+=$porcentaje_accionista;
					if ($porcentaje_accionista==0) 	$tpl->assign("porcentaje","");
					else $tpl->assign("porcentaje",$porcentaje_accionista);
				}
			}
		}

		$aux_cia1=$aux_cia2;
		if($aux_cia2 +$limite_cias > count($companias))
			$aux_cia2=count($companias);
		else
			$aux_cia2+=$limite_cias;
	}
	//if($_GET['tipo_con']==0){
		$tpl->newBlock("totales");
		$tpl->assign("total_compania",number_format($total_prueba_efectivo,$_GET['tipo_con'] == 0 ? 2 : 4,'.',','));
		
		for($q=$aux_acc1;$q<$aux_acc2;$q++){
			$tpl->newBlock("totales_accionistas");
			if(isset($totales_accion[$q]) && $totales_accion[$q]==0) 
				$tpl->assign("total_accionista","");
			else if (isset($totales_accion[$q]) && $totales_accion[$q]!=0)
				$tpl->assign("total_accionista",number_format($totales_accion[$q],$_GET['tipo_con'] == 0 ? 2 : 4,'.',','));
		}
	//}
	$aux_acc1=$aux_acc2;
	$aux_acc2+=7;
}

$tpl->printToScreen();
die();

?>