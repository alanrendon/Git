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
$tpl->assignInclude("body","./plantillas/bal/bal_avio_con.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['mes'])) {
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

$sql="SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia < 101 order by num_cia";
$cias=ejecutar_script($sql,$dsn);

$relacion_harina=44;
$relacion_azucar=50;
$relacion_huevo=360;

$total_harina_ent=0;
$total_harina_sal=0;
$total_az1_ent=0;
$total_az1_sal=0;
$total_az2_ent=0;
$total_az2_sal=0;
$total_cha_ent=0;
$total_cha_sal=0;
$total_gra_ent=0;
$total_gra_sal=0;
$total_man1_ent=0;
$total_man1_sal=0;
$total_man2_ent=0;
$total_man2_sal=0;

$total_mar_ent=0;
$total_mar_sal=0;
$total_por_ent=0;
$total_por_sal=0;
$total_ace_ent=0;
$total_ace_sal=0;
$total_hue_ent=0;
$total_hue_sal=0;
$total_lev_ent=0;
$total_lev_sal=0;
$total_gas_ent=0;
$total_gas_sal=0;



$tpl->newBlock("listado");
$mes=mes_escrito($_GET['mes']);
$tpl->assign("mes",strtoupper($mes));
$tpl->assign("anio",$_GET['anio']);

for($i=0;$i<count($cias);$i++){
	$tpl->newBlock("rows");
	$tpl->assign("num_cia",$cias[$i]['num_cia']);
	$tpl->assign("nombre",$cias[$i]['nombre_corto']);
	
	//HARINA
	$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',1,false)";
	$harina_ent=ejecutar_script($sql,$dsn);
	$harina_entrada = $harina_ent[0]['cantidades']/$relacion_harina;
	
	$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',1,true)";
	$harina_sal=ejecutar_script($sql,$dsn);
	$harina_salida = $harina_sal[0]['cantidades']/$relacion_harina;
	
	//AZUCAR REFINADA
	$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',3,false)";
	$az1_ent=ejecutar_script($sql,$dsn);
	$azucar1_entrada=$az1_ent[0]['cantidades']/$relacion_azucar;
	
	$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',3,true)";
	$az1_sal=ejecutar_script($sql,$dsn);
	$azucar1_salida=$az1_sal[0]['cantidades']/$relacion_azucar;	

	//AZUCAR ESTANDAR
	$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',4,false)";
	$az2_ent=ejecutar_script($sql,$dsn);
	$azucar2_entrada=$az2_ent[0]['cantidades']/$relacion_azucar;	
	
	$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',4,true)";
	$az2_sal=ejecutar_script($sql,$dsn);
	$azucar2_salida=$az2_sal[0]['cantidades']/$relacion_azucar;	

	//CHANTILLY
	$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',30,false)";
	$cha_ent=ejecutar_script($sql,$dsn);
	
	$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',30,true)";
	$cha_sal=ejecutar_script($sql,$dsn);
	
	//GRASA
	$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',38,false)";
	$gra_ent=ejecutar_script($sql,$dsn);
	
	$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',38,true)";
	$gra_sal=ejecutar_script($sql,$dsn);

	//MANTEQUILLA FINA
	$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',44,false)";
	$man1_ent=ejecutar_script($sql,$dsn);
	
	$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',44,true)";
	$man1_sal=ejecutar_script($sql,$dsn);
	
	//MANTEQUILLA HOJALDRE
	$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',45,false)";
	$man2_ent=ejecutar_script($sql,$dsn);
	
	$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',45,true)";
	$man2_sal=ejecutar_script($sql,$dsn);
	
	
	
	$total_harina_ent+=$harina_salida;
	$total_harina_sal+=$harina_entrada;
	$total_az1_ent+=$azucar1_entrada;
	$total_az1_sal+=$azucar1_salida;
	$total_az2_ent+=$azucar2_entrada;
	$total_az2_sal+=$azucar2_salida;
	$total_cha_ent+=$cha_ent[0]['cantidades'];
	$total_cha_sal+=$cha_sal[0]['cantidades'];
	$total_gra_ent+=$gra_ent[0]['cantidades'];
	$total_gra_sal+=$gra_sal[0]['cantidades'];
	$total_man1_ent+=$man1_ent[0]['cantidades'];
	$total_man1_sal+=$man1_sal[0]['cantidades'];
	$total_man2_ent+=$man2_ent[0]['cantidades'];
	$total_man2_sal+=$man2_sal[0]['cantidades'];
	
	if($harina_entrada==0) $tpl->assign("harina_ent","");
	else	
		$tpl->assign("harina_ent",number_format($harina_entrada,2,'.',','));

	if($harina_salida==0) $tpl->assign("harina_sal","");
	else
		$tpl->assign("harina_sal",number_format($harina_salida,2,'.',','));

	if($azucar1_entrada==0) $tpl->assign("az1_ent","");
	else
		$tpl->assign("az1_ent",number_format($azucar1_entrada,2,'.',','));
	
	if($azucar1_salida==0) $tpl->assign("az2_sal","");
	else
		$tpl->assign("az1_sal",number_format($azucar1_salida,2,'.',','));

	if($azucar2_entrada==0) $tpl->assign("az2_ent","");
	else
		$tpl->assign("az2_ent",number_format($azucar2_entrada,2,'.',','));
	
	if($azucar2_salida==0) $tpl->assign("az2_sal","");
	else
		$tpl->assign("az2_sal",number_format($azucar2_salida,2,'.',','));

	if($cha_ent[0]['cantidades']==0) $tpl->assign("cha_ent","");
	else
		$tpl->assign("cha_ent",number_format($cha_ent[0]['cantidades'],2,'.',','));
	
	if($cha_sal[0]['cantidades']==0) $tpl->assign("cha_sal","");
	else
		$tpl->assign("cha_sal",number_format($cha_sal[0]['cantidades'],2,'.',','));

	if($gra_ent[0]['cantidades']==0) $tpl->assign("gra_ent","");
	else
		$tpl->assign("gra_ent",number_format($gra_ent[0]['cantidades'],2,'.',','));
	
	if($gra_sal[0]['cantidades']==0) $tpl->assign("gra_sal","");
	else
		$tpl->assign("gra_sal",number_format($gra_sal[0]['cantidades'],2,'.',','));

	if($man1_ent[0]['cantidades']==0) $tpl->assign("man1_ent","");
	else
		$tpl->assign("man1_ent",number_format($man1_ent[0]['cantidades'],2,'.',','));
	
	if($man1_sal[0]['cantidades']==0) $tpl->assign("man1_sal","");
	else
		$tpl->assign("man1_sal",number_format($man1_sal[0]['cantidades'],2,'.',','));

	if($man2_ent[0]['cantidades']==0) $tpl->assign("man2_ent","");
	else
		$tpl->assign("man2_ent",number_format($man2_ent[0]['cantidades'],2,'.',','));
	
	if($man2_sal[0]['cantidades']==0) $tpl->assign("man2_sal","");
	else
		$tpl->assign("man2_sal",number_format($man2_sal[0]['cantidades'],2,'.',','));
	
}

	$tpl->gotoBlock("listado");
	if($total_harina_ent==0) $tpl->assign("total_harina_ent","");
	else	
		$tpl->assign("total_harina_ent",number_format($total_harina_ent,2,'.',','));

	if($total_harina_sal==0) $tpl->assign("total_harina_sal","");
	else	
		$tpl->assign("total_harina_sal",number_format($total_harina_sal,2,'.',','));

	if($total_az1_ent==0) $tpl->assign("total_az1_ent","");
	else	
		$tpl->assign("total_az1_ent",number_format($total_az1_ent,2,'.',','));

	if($total_az1_sal==0) $tpl->assign("total_az1_sal","");
	else	
		$tpl->assign("total_az1_sal",number_format($total_az1_sal,2,'.',','));

	if($total_az2_ent==0) $tpl->assign("total_az2_ent","");
	else	
		$tpl->assign("total_az2_ent",number_format($total_az2_ent,2,'.',','));

	if($total_az2_sal==0) $tpl->assign("total_az2_sal","");
	else	
		$tpl->assign("total_az2_sal",number_format($total_az2_sal,2,'.',','));
	
	if($total_cha_ent==0) $tpl->assign("total_cha_ent","");
	else	
		$tpl->assign("total_cha_ent",number_format($total_cha_ent,2,'.',','));

	if($total_cha_sal==0) $tpl->assign("total_cha_sal","");
	else	
		$tpl->assign("total_cha_sal",number_format($total_cha_sal,2,'.',','));

	if($total_gra_ent==0) $tpl->assign("total_gra_ent","");
	else	
		$tpl->assign("total_gra_ent",number_format($total_gra_ent,2,'.',','));

	if($total_gra_sal==0) $tpl->assign("total_gra_sal","");
	else	
		$tpl->assign("total_gra_sal",number_format($total_gra_sal,2,'.',','));

	if($total_man1_ent==0) $tpl->assign("total_man1_ent","");
	else	
		$tpl->assign("total_man1_ent",number_format($total_man1_ent,2,'.',','));

	if($total_man1_sal==0) $tpl->assign("total_man1_sal","");
	else	
		$tpl->assign("total_man1_sal",number_format($total_man1_sal,2,'.',','));

	if($total_man2_ent==0) $tpl->assign("total_man2_ent","");
	else	
		$tpl->assign("total_man2_ent",number_format($total_man2_ent,2,'.',','));

	if($total_man2_sal==0) $tpl->assign("total_man2_sal","");
	else	
		$tpl->assign("total_man2_sal",number_format($total_man2_sal,2,'.',','));

// SE GENERA EL SALTO DE PAGINA

	$tpl->assign("salto_pagina","<br style='page-break-after:always;'>");
	
	
	for($i=0;$i<count($cias);$i++){
		$tpl->newBlock("rows1");
		$tpl->assign("num_cia",$cias[$i]['num_cia']);
		$tpl->assign("nombre",$cias[$i]['nombre_corto']);
		
		//MARGARINA
		$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',47,false)";
		$mar_ent=ejecutar_script($sql,$dsn);
		
		$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',47,true)";
		$mar_sal=ejecutar_script($sql,$dsn);
		
		//PORCINA
		$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',49,false)";
		$por_ent=ejecutar_script($sql,$dsn);
		
		$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',49,true)";
		$por_sal=ejecutar_script($sql,$dsn);
	
		//ACEITE
		$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',86,false)";
		$ace_ent=ejecutar_script($sql,$dsn);
		
		$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',86,true)";
		$ace_sal=ejecutar_script($sql,$dsn);
	
		//HUEVO
		$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',148,false)";
		$hue_ent=ejecutar_script($sql,$dsn);
		$huevo_entrada=$hue_ent[0]['cantidades']/$relacion_huevo;
		
		$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',148,true)";
		$hue_sal=ejecutar_script($sql,$dsn);
		$huevo_salida=$hue_sal[0]['cantidades']/$relacion_huevo;
	
		//LEVADURA
		$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',149,false)";
		$lev_ent=ejecutar_script($sql,$dsn);
		
		$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',149,true)";
		$lev_sal=ejecutar_script($sql,$dsn);
	
		//GAS
		$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',90,false)";
		$gas_ent=ejecutar_script($sql,$dsn);
		
		$sql="select cantidades(".$cias[$i]['num_cia'].",'$fecha1','$fecha2',90,true)";
		$gas_sal=ejecutar_script($sql,$dsn);

		$total_mar_ent+=$mar_ent[0]['cantidades'];
		$total_mar_sal+=$mar_sal[0]['cantidades'];
		$total_por_ent+=$por_ent[0]['cantidades'];
		$total_por_sal+=$por_sal[0]['cantidades'];
		$total_ace_ent+=$ace_ent[0]['cantidades'];
		$total_ace_sal+=$ace_sal[0]['cantidades'];
		$total_hue_ent+=$huevo_entrada;
		$total_hue_sal+=$huevo_salida;
		$total_lev_ent+=$lev_ent[0]['cantidades'];
		$total_lev_sal+=$lev_sal[0]['cantidades'];
		$total_gas_ent+=$gas_ent[0]['cantidades'];
		$total_gas_sal+=$gas_sal[0]['cantidades'];
		
		if($mar_ent==0) $tpl->assign("mar_ent","");
		else	
			$tpl->assign("mar_ent",number_format($mar_ent[0]['cantidades'],2,'.',','));
	
		if($mar_sal==0) $tpl->assign("mar_sal","");
		else
			$tpl->assign("mar_sal",number_format($mar_sal[0]['cantidades'],2,'.',','));
	
		if($por_ent[0]['cantidades']==0) $tpl->assign("por_ent","");
		else
			$tpl->assign("por_ent",number_format($por_ent[0]['cantidades'],2,'.',','));
		
		if($por_sal[0]['cantidades']==0) $tpl->assign("por_sal","");
		else
			$tpl->assign("por_sal",number_format($por_sal[0]['cantidades'],2,'.',','));
	
		if($ace_ent[0]['cantidades']==0) $tpl->assign("ace_ent","");
		else
			$tpl->assign("ace_ent",number_format($ace_ent[0]['cantidades'],2,'.',','));
		
		if($ace_sal[0]['cantidades']==0) $tpl->assign("ace_sal","");
		else
			$tpl->assign("ace_sal",number_format($ace_sal[0]['cantidades'],2,'.',','));
	
		if($huevo_entrada==0) $tpl->assign("hue_ent","");
		else
			$tpl->assign("hue_ent",number_format($huevo_entrada,2,'.',','));
		
		if($huevo_salida==0) $tpl->assign("hue_sal","");
		else
			$tpl->assign("hue_sal",number_format($huevo_salida,2,'.',','));
	
		if($lev_ent[0]['cantidades']==0) $tpl->assign("lev_ent","");
		else
			$tpl->assign("lev_ent",number_format($lev_ent[0]['cantidades'],2,'.',','));
		
		if($lev_sal[0]['cantidades']==0) $tpl->assign("lev_sal","");
		else
			$tpl->assign("lev_sal",number_format($lev_sal[0]['cantidades'],2,'.',','));
	
		if($gas_ent[0]['cantidades']==0) $tpl->assign("gas_ent","");
		else
			$tpl->assign("gas_ent",number_format($gas_ent[0]['cantidades'],2,'.',','));
		
		if($gas_sal[0]['cantidades']==0) $tpl->assign("gas_sal","");
		else
			$tpl->assign("gas_sal",number_format($gas_sal[0]['cantidades'],2,'.',','));
	}

	$tpl->gotoBlock("listado");
	if($total_mar_ent==0) $tpl->assign("total_mar_ent","");
	else	
		$tpl->assign("total_mar_ent",number_format($total_mar_ent,2,'.',','));

	if($total_mar_sal==0) $tpl->assign("total_mar_sal","");
	else	
		$tpl->assign("total_mar_sal",number_format($total_mar_sal,2,'.',','));

	if($total_por_ent==0) $tpl->assign("total_por_ent","");
	else	
		$tpl->assign("total_por_ent",number_format($total_por_ent,2,'.',','));

	if($total_por_sal==0) $tpl->assign("total_por_sal","");
	else	
		$tpl->assign("total_por_sal",number_format($total_por_sal,2,'.',','));

	if($total_ace_ent==0) $tpl->assign("total_ace_ent","");
	else	
		$tpl->assign("total_ace_ent",number_format($total_ace_ent,2,'.',','));

	if($total_ace_sal==0) $tpl->assign("total_ace_sal","");
	else	
		$tpl->assign("total_ace_sal",number_format($total_ace_sal,2,'.',','));
	
	if($total_hue_ent==0) $tpl->assign("total_hue_ent","");
	else	
		$tpl->assign("total_hue_ent",number_format($total_hue_ent,2,'.',','));

	if($total_hue_sal==0) $tpl->assign("total_hue_sal","");
	else	
		$tpl->assign("total_hue_sal",number_format($total_hue_sal,2,'.',','));

	if($total_lev_ent==0) $tpl->assign("total_lev_ent","");
	else	
		$tpl->assign("total_lev_ent",number_format($total_lev_ent,2,'.',','));

	if($total_lev_sal==0) $tpl->assign("total_lev_sal","");
	else	
		$tpl->assign("total_lev_sal",number_format($total_lev_sal,2,'.',','));

	if($total_gas_ent==0) $tpl->assign("total_gas_ent","");
	else	
		$tpl->assign("total_gas_ent",number_format($total_gas_ent,2,'.',','));

	if($total_gas_sal==0) $tpl->assign("total_gas_sal","");
	else	
		$tpl->assign("total_gas_sal",number_format($total_gas_sal,2,'.',','));
	

$tpl->printToScreen();
die();

?>