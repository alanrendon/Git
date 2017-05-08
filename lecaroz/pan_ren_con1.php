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
$descripcion_error[1] = "No hay movimientos operados";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/pan/pan_ren_con.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));
	$tpl->assign("m",date("m"));
	$tpl->assign("d",date("d"));

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
$tpl->newBlock("prueba_pan");
//ARROJA EL NUMERO DE ITERACIONES DENTRO DEL FOR A PARTIR DEL RANGO DE FECHAS
//$fecha_inicio='1/'.date("m").'/'.date("Y");
$fech=explode("/",$_GET['fecha_mov']);
$fecha_inicio='1/'.$fech[1].'/'.$fech[2];
$turno1=0;
$turno2=0;
$turno3=0;
$turno4=0;

if($_GET['tipo_cia'] == 0)
	$cia="select num_cia, nombre_corto from catalogo_companias where num_cia='".$_GET['num_cia']."'";
else if($_GET['tipo_cia'] == 1)
	$cia="select num_cia, nombre_corto from catalogo_companias where num_cia between 1 and 100";

if($_GET['tipo_turno']==0){
	$turno1=2;
	$turno2=1;
	}
else if($_GET['tipo_turno']==1){
	$turno1=3;
	$turno2=4;
	}
else if($_GET['tipo_turno']==2){
	$turno1=2;
	$turno2=1;
	$turno3=3;
	$turno4=4;
	}

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


$companias=ejecutar_script($cia,$dsn);
//print_r($companias);

$relacion=44;
if($_GET['tipo_turno']==0 or $_GET['tipo_turno']==1)
{
	$bultos=0;
	$bultos1=0;
	for($j=0;$j<count($companias);$j++)//INICIA EL CICLO PARA LAS COMPAÑÍAS A CONSULTAR PUEDE SER UNA O TODAS
	{
	// VARIABLES
		$total_consumo1 = 0;
		$total_produccion1 = 0;
		$total_raya1 = 0;
		$total_rendimiento1 = 0;
		$total_consumo2 = 0;
		$total_produccion2 = 0;
		$total_raya2 = 0;
		$total_rendimiento2 = 0;
		$consumo_total = 0;
		$produccion_total = 0;
		$rendimiento_total = 0;
		$francesero_dia = false;
		$francesero_noche = false;
		$repostero = false;
		$bizcochero = false;
		
		//VERIFICA LA EXISTENCIA DEL FRANCESERO DE DIA PARA LA COMPAÑÍA EN CAPTURA DE PRODUCCION
		if(existe_registro("total_produccion",array("numcia","codturno"),array($companias[$j]['num_cia'],1),$dsn)){
			$francesero_dia=true;
			$sql_dia="
			SELECT 
			numcia as cia, 
			codturno as turno, 
			total_produccion as produccion, 
			raya_pagada as raya, 
			fecha_total 
			FROM 
			total_produccion
			WHERE 
			numcia=".$companias[$j]['num_cia']." and 
			fecha_total between '".$fecha_inicio."' and '".$_GET['fecha_mov']."' and 
			codturno=1 
			";
			$sql_dia1="
			SELECT 
			num_cia as cia, 
			cod_turno as turno, 
			cantidad as cantidad, 
			fecha 
			FROM 
			mov_inv_virtual
			WHERE 
			num_cia=".$companias[$j]['num_cia']." and 
			fecha between '".$fecha_inicio."' and '".$_GET['fecha_mov']."' and 
			cod_turno=1 and 
			tipo_mov=true and
			codmp=1
			";
			$produccion_dia=ejecutar_script($sql_dia,$dsn);
			$movimiento_dia=ejecutar_script($sql_dia1,$dsn);
		}
		
		//VERIFICA LA EXISTENCIA DEL FRANCESERO DE NOCHE PARA LA COMPAÑÍA EN CAPTURA DE PRODUCCION
		if(existe_registro("total_produccion",array("numcia","codturno"),array($companias[$j]['num_cia'],2),$dsn)){
			$francesero_noche=true;
			$sql_noche="
			SELECT 
			numcia as cia, 
			codturno as turno, 
			total_produccion as produccion, 
			raya_pagada as raya, 
			fecha_total 
			FROM 
			total_produccion
			WHERE 
			numcia=".$companias[$j]['num_cia']." and 
			fecha_total between '".$fecha_inicio."' and '".$_GET['fecha_mov']."' and 
			codturno=2 
			";
			$sql_noche1="
			SELECT 
			num_cia as cia, 
			cod_turno as turno, 
			cantidad as cantidad, 
			fecha 
			FROM 
			mov_inv_virtual
			WHERE 
			num_cia=".$companias[$j]['num_cia']." and 
			fecha between '".$fecha_inicio."' and '".$_GET['fecha_mov']."' and 
			cod_turno=2 and 
			tipo_mov=true and
			codmp=1
			";
			$produccion_noche=ejecutar_script($sql_noche,$dsn);
			$movimiento_noche=ejecutar_script($sql_noche1,$dsn);

		}
	
		//VERIFICA LA EXISTENCIA DEL BIZCOCHERO PARA LA COMPAÑÍA EN CAPTURA DE PRODUCCION
		if(existe_registro("total_produccion",array("numcia","codturno"),array($companias[$j]['num_cia'],3),$dsn)){
			$bizcochero=true;
			$sql_biz="
			SELECT 
			numcia as cia, 
			codturno as turno, 
			total_produccion as produccion, 
			raya_pagada as raya, 
			fecha_total 
			FROM 
			total_produccion
			WHERE 
			numcia=".$companias[$j]['num_cia']." and 
			fecha_total between '".$fecha_inicio."' and '".$_GET['fecha_mov']."' and 
			codturno=3 
			";
			$sql_biz1="
			SELECT 
			num_cia as cia, 
			cod_turno as turno, 
			cantidad as cantidad, 
			fecha 
			FROM 
			mov_inv_virtual
			WHERE 
			num_cia=".$companias[$j]['num_cia']." and 
			fecha between '".$fecha_inicio."' and '".$_GET['fecha_mov']."' and 
			cod_turno=3 and 
			tipo_mov=true and
			codmp=1
			";
			$produccion_biz=ejecutar_script($sql_biz,$dsn);
			$movimiento_biz=ejecutar_script($sql_biz1,$dsn);
			
		}
		
		//VERIFICA LA EXISTENCIA DEL REPOSTERO PARA LA COMPAÑÍA EN CAPTURA DE PRODUCCION
		if(existe_registro("total_produccion",array("numcia","codturno"),array($companias[$j]['num_cia'],4),$dsn)){
			$repostero=true;
			$sql_rep="
			SELECT 
			numcia as cia, 
			codturno as turno, 
			total_produccion as produccion, 
			raya_pagada as raya, 
			fecha_total 
			FROM 
			total_produccion
			WHERE 
			numcia=".$companias[$j]['num_cia']." and 
			fecha_total between '".$fecha_inicio."' and '".$_GET['fecha_mov']."' and 
			codturno=4 
			";
			$sql_rep1="
			SELECT 
			num_cia as cia, 
			cod_turno as turno, 
			cantidad as cantidad, 
			fecha 
			FROM 
			mov_inv_virtual
			WHERE 
			num_cia=".$companias[$j]['num_cia']." and 
			fecha between '".$fecha_inicio."' and '".$_GET['fecha_mov']."' and 
			cod_turno=4 and 
			tipo_mov=true and
			codmp=1
			";
			$produccion_rep=ejecutar_script($sql_rep,$dsn);
			$movimiento_rep=ejecutar_script($sql_rep1,$dsn);

		}

		$tpl->newBlock("compania");
		$dia_1 = explode("/",$_GET['fecha_mov']);
		$tpl->assign("dia",$dia_1[0]);
		$tpl->assign("anio",$dia_1[2]);
//		$tpl->assign("mes",$nombremes[date("m")]);
		$tpl->assign("num_cia",$companias[$j]['num_cia']);
		$tpl->assign("nom_cia",$companias[$j]['nombre_corto']);
		$nom_turno1=obtener_registro("catalogo_turnos",array("cod_turno"),array($turno1),"","",$dsn);
		$nom_turno2=obtener_registro("catalogo_turnos",array("cod_turno"),array($turno2),"","",$dsn);
		
		if($turno1==2)$tpl->assign("nom_turno","F&nbsp;&nbsp;R&nbsp;&nbsp;A&nbsp;&nbsp;N&nbsp;&nbsp;C&nbsp;&nbsp;E&nbsp;&nbsp;S&nbsp;&nbsp;E&nbsp;&nbsp;R&nbsp;&nbsp;O&nbsp;&nbsp;&nbsp; D&nbsp;&nbsp;E&nbsp;&nbsp; N&nbsp;&nbsp;O&nbsp;&nbsp;C&nbsp;&nbsp;H&nbsp;&nbsp;E");
		else $tpl->assign("nom_turno","B&nbsp;&nbsp;I&nbsp;&nbsp;Z&nbsp;&nbsp;C&nbsp;&nbsp;O&nbsp;&nbsp;C&nbsp;&nbsp;H&nbsp;&nbsp;E&nbsp;&nbsp;R&nbsp;&nbsp;O");
		if($turno2==1)$tpl->assign("nom_turno1","F&nbsp;R&nbsp;A&nbsp;N&nbsp;C&nbsp;E&nbsp;S&nbsp;E&nbsp;R&nbsp;O&nbsp;&nbsp; D&nbsp;&nbsp;E&nbsp;&nbsp; D&nbsp;&nbsp;I&nbsp;&nbsp;A");
		else $tpl->assign("nom_turno1","R&nbsp;&nbsp;E&nbsp;&nbsp;P&nbsp;&nbsp;O&nbsp;&nbsp;S&nbsp;&nbsp;T&nbsp;&nbsp;E&nbsp;&nbsp;R&nbsp;&nbsp;O");
		
		$aux_t1_prod=0;
		$aux_t1_mov=0;
		$aux_t2_prod=0;
		$aux_t2_prod=0;
		for($i=0;$i<$dia_1[0];$i++){
	
			$fecha_armada=$i."/".$dia_1[1]."/".$dia_1[2];
			$tpl->newBlock("rows");
			$dia=explode("/",$produccion[$i]['fecha_total']);
			$tpl->assign("dia",$dia[0]);
//************************			
			if($_GET['tipo_turno']==0){//FRANCESEROS
				if($francesero_dia==true)
				{
					if($fecha_armada==$produccion_dia[$aux_t1_prod]['fecha'] and $fecha_armada==$movimiento_dia[$aux_t1_mov]['fecha'])
					{
							$rendimiento1=$produccion_dia[$aux_t1_prod]['produccion'] / ($movimiento_dia[$aux_t1_mov]['cantidad'] / $relacion);
							$cantidad1=$movimiento_dia[$aux_t1_mov]['cantidad'];
							$produccion1=$produccion_dia[$aux_t1_prod]['produccion'];
							$raya1=$produccion_dia[$aux_t1_prod]['raya'];
					}
					else $rendimiento1=0;
					if($fecha_armada==$produccion_dia[$aux_t1_prod]['fecha']) $aux_t1_prod++;
					if($fecha_armada==$movimiento_dia[$aux_t1_mov]['fecha']) $aux_t1_mov++;
				}

				if($francesero_noche==true)
				{
					if($fecha_armada==$produccion_noche[$aux_t2_prod]['fecha'] and $fecha_armada==$movimiento_noche[$aux_t2_mov]['fecha'])
					{
							$rendimiento2=$produccion_noche[$aux_t2_prod]['produccion'] / ($movimiento_noche[$aux_t2_mov]['cantidad'] / $relacion);
							$cantidad2=$movimiento_noche[$aux_t2_mov]['cantidad'];
							$produccion2=$produccion_noche[$aux_t2_prod]['produccion'];
							$raya2=$produccion_noche[$aux_t2_prod]['raya'];
					}
					else $rendimiento2=0;
					if($fecha_armada==$produccion_dia[$aux_t2_prod]['fecha']) $aux_t2_prod++;
					if($fecha_armada==$movimiento_dia[$aux_t2_mov]['fecha']) $aux_t2_mov++;
				}

			}
			if($_GET['tipo_turno']==1{//BIZCOCHERO, REPOSTERO
				if($bizcochero==true)
				{
					if($fecha_armada==$produccion_biz[$aux_t1_prod]['fecha'] and $fecha_armada==$movimiento_biz[$aux_t1_mov]['fecha'])
					{
							$rendimiento1=$produccion_biz[$aux_t1_prod]['produccion'] / ($movimiento_biz[$aux_t1_mov]['cantidad'] / $relacion);
							$cantidad1=$movimiento_biz[$aux_t1_mov]['cantidad'];
							$produccion1=$produccion_biz[$aux_t1_prod]['produccion'];
							$raya1=$produccion_biz[$aux_t1_prod]['raya'];
					}
					else $rendimiento1=0;
					if($fecha_armada==$produccion_biz[$aux_t1_prod]['fecha']) $aux_t1_prod++;
					if($fecha_armada==$movimiento_biz[$aux_t1_mov]['fecha']) $aux_t1_mov++;
				}

				if($repostero==true)
				{
					if($fecha_armada==$produccion_rep[$aux_t2_prod]['fecha'] and $fecha_armada==$movimiento_rep[$aux_t2_mov]['fecha'])
					{
							$rendimiento2=$produccion_rep[$aux_t2_prod]['produccion'] / ($movimiento_rep[$aux_t2_mov]['cantidad'] / $relacion);
							$cantidad2=$movimiento_rep[$aux_t2_mov]['cantidad'];
							$produccion2=$produccion_rep[$aux_t2_prod]['produccion'];
							$raya2=$produccion_biz[$aux_t2_prod]['raya'];
					}
					else $rendimiento2=0;
					if($fecha_armada==$produccion_rep[$aux_t2_prod]['fecha']) $aux_t2_prod++;
					if($fecha_armada==$movimiento_rep[$aux_t2_mov]['fecha']) $aux_t2_mov++;
				}
			}
			
			if($francesero_noche==true or $bizcochero==true)
			{
				if($cantidad1 <=0) $tpl->assign("consumo","");
				else{ 
					$bultos=$cantidad1 / $relacion;
					$tpl->assign("consumo",number_format($bultos,2,'.',','));
				}
				
				if($produccion1 <=0) $tpl->assign("produccion","");
				else $tpl->assign("produccion",number_format($produccion1,2,'.',','));
				
				if($raya1 <=0) $tpl->assign("raya","");
				else $tpl->assign("raya",number_format($raya1,2,'.',','));
				
				if($rendimiento1 <=0) $tpl->assign("rendimiento","");
				$tpl->assign("rendimiento",number_format($rendimiento1,2,'.',','));
			}

			if($francesero_noche==true or $repostero==true)
			{
				if($cantidad2 <=0) $tpl->assign("consumo1","");
				else{
					$bultos2=$cantidad2 / $relacion;
					$tpl->assign("consumo1",number_format($bultos2,2,'.',','));
				}
				
				if($produccion2 <=0) $tpl->assign("produccion1","");
				else $tpl->assign("produccion1",number_format($produccion2,2,'.',','));
				
				if($raya2 <=0) $tpl->assign("raya1","");
				else $tpl->assign("raya1",number_format($raya2,2,'.',','));
				
				if($rendimiento2 <=0) $tpl->assign("rendimiento1","");
				$tpl->assign("rendimiento1",number_format($rendimiento2,2,'.',','));
			}

			if($francesero_noche==true or $bizcochero==true)
			{
				$total_consumo1 += ($mov[$i]['cantidad1'] / $relacion);
				$total_produccion1 += $produccion[$i]['produccion1'];
				$total_raya1 += $produccion[$i]['raya1'];
			}
			if($francesero_noche==true or $repostero==true)
			{
				$total_consumo2 += ($mov[$i]['cantidad2'] / $relacion);
				$total_produccion2 += $produccion[$i]['produccion2'];
				$total_raya2 += $produccion[$i]['raya2'];
			}
		}
	
		$tpl->gotoBlock("compania");
		if($total_consumo1 > 0)
			$total_rendimiento1=$total_produccion1/$total_consumo1;
		else
			$total_rendimiento1=0;
		if($total_consumo2 > 0)
			$total_rendimiento2=$total_produccion2/$total_consumo2;
		else
			$total_rendimiento2=0;
		$consumo_total=$total_consumo1 + $total_consumo2;
		$produccion_total= $total_produccion1 + $total_produccion2;
		if($consumo_total > 0)
			$rendimiento_total= $produccion_total / $consumo_total;
		else
			$rendimiento_total=0;
			
		$tpl->assign("total_consumo",number_format($total_consumo1,2,'.',','));
		$tpl->assign("total_produccion",number_format($total_produccion1,2,'.',','));
		$tpl->assign("total_raya",number_format($total_raya1,2,'.',','));
		$tpl->assign("total_rendimiento",number_format($total_rendimiento1,2,'.',','));
	
		$tpl->assign("total_consumo1",number_format($total_consumo2,2,'.',','));
		$tpl->assign("total_produccion1",number_format($total_produccion2,2,'.',','));
		$tpl->assign("total_raya1",number_format($total_raya2,2,'.',','));
		$tpl->assign("total_rendimiento1",number_format($total_rendimiento2,2,'.',','));
		
		$tpl->assign("consumo_total",number_format($consumo_total,2,'.',','));
		$tpl->assign("produccion_total",number_format($produccion_total,2,'.',','));
		$tpl->assign("rendimiento_total",number_format($rendimiento_total,2,'.',','));
	
	//*************************************************************************************************
	}

			
}
$tpl->printToScreen();
// --------------------------------------------------------------------------------------------------------
?>