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
else if($_GET['tipo_cia'] == 1){
	if($_SESSION['iduser']==1 or $_SESSION['iduser']==4 or $_SESSION['iduser']==42 or $_SESSION['iduser']==14){
		if($_SESSION['iduser']==14 and $_GET['tipo_turno']!=2){
			$opera=ejecutar_script("SELECT * FROM catalogo_operadoras WHERE iduser=$_SESSION[iduser]",$dsn);
			if(!$opera){
				header("location: ./pan_efe_con.php?codigo_error=5");
				die();
			}
			$cia="select num_cia, nombre_corto from catalogo_companias where idoperadora=".$opera[0]['idoperadora']." and num_cia < 900 order by num_cia";
		}
		else
			$cia="select num_cia, nombre_corto from catalogo_companias where num_cia <= 300 order by num_cia";
	}
	else{
		$opera=ejecutar_script("SELECT * FROM catalogo_operadoras WHERE iduser=$_SESSION[iduser]",$dsn);
		if(!$opera){
			header("location: ./pan_efe_con.php?codigo_error=5");
			die();
		}
		$cia="select num_cia, nombre_corto from catalogo_companias where idoperadora=".$opera[0]['idoperadora']." and num_cia < 900 order by num_cia";
	}
}

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

function buscar($array, $dia) {
	if (!$array)
		return false;
	
	foreach ($array as $i => $reg) {
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $reg['fecha'], $tmp);
		if ($dia == $tmp[1])
			return $i;
	}
	
	return NULL;
}

$companias=ejecutar_script($cia,$dsn);
//print_r($companias);
$salto=1;
$salto1=1;
$relacion=44;
$bandera=0;
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
	$total_consumo3=0;
	$total_rendimiento3=0;
	$total_consumo4=0;
	$total_rendimiento4=0;
	$total_efectivo=0;
	
	//VERIFICA LA EXISTENCIA DEL FRANCESERO DE DIA PARA LA COMPAÑÍA EN CAPTURA DE PRODUCCION

	if(existe_registro("total_produccion",array("numcia","codturno"),array($companias[$j]['num_cia'],1),$dsn)){
		$francesero_dia=true;
		$sql_dia="
		SELECT 
		numcia as cia, 
		codturno as turno, 
		total_produccion as produccion, 
		raya_pagada as raya, 
		fecha_total as fecha
		FROM 
		total_produccion
		WHERE 
		numcia=".$companias[$j]['num_cia']." and 
		fecha_total between '".$fecha_inicio."' and '".$_GET['fecha_mov']."' and 
		codturno=1 
		order by fecha
		";
		$sql_dia1="
		SELECT 
		num_cia as cia, 
		cod_turno as turno, 
		cantidad as cantidad, 
		fecha 
		FROM 
		mov_inv_real
		WHERE 
		num_cia=".$companias[$j]['num_cia']." and 
		fecha between '".$fecha_inicio."' and '".$_GET['fecha_mov']."' and 
		cod_turno=1 and 
		tipo_mov=true and
		codmp=1
		group by cia,turno,fecha,cantidad
		order by fecha
		";
		$produccion_dia=ejecutar_script($sql_dia,$dsn);
		$movimiento_dia=ejecutar_script($sql_dia1,$dsn);
		$bandera+=1;
	}
	else $bandera-=1;
	
	//VERIFICA LA EXISTENCIA DEL FRANCESERO DE NOCHE PARA LA COMPAÑÍA EN CAPTURA DE PRODUCCION
	if(existe_registro("total_produccion",array("numcia","codturno"),array($companias[$j]['num_cia'],2),$dsn)){
		$francesero_noche=true;
		$sql_noche="
		SELECT 
		numcia as cia, 
		codturno as turno, 
		total_produccion as produccion, 
		raya_pagada as raya, 
		fecha_total as fecha
		FROM 
		total_produccion
		WHERE 
		numcia=".$companias[$j]['num_cia']." and 
		fecha_total between '".$fecha_inicio."' and '".$_GET['fecha_mov']."' and 
		codturno=2 
		order by fecha
		";
		$sql_noche1="
		SELECT 
		num_cia as cia, 
		cod_turno as turno, 
		cantidad as cantidad, 
		fecha 
		FROM 
		mov_inv_real
		WHERE 
		num_cia=".$companias[$j]['num_cia']." and 
		fecha between '".$fecha_inicio."' and '".$_GET['fecha_mov']."' and 
		cod_turno=2 and 
		tipo_mov=true and
		codmp=1
		group by cia,turno,fecha,cantidad
		order by fecha
		";
		$produccion_noche=ejecutar_script($sql_noche,$dsn);
		$movimiento_noche=ejecutar_script($sql_noche1,$dsn);
		$bandera+=1;
	}
	else $bandera-=1;

	//VERIFICA LA EXISTENCIA DEL BIZCOCHERO PARA LA COMPAÑÍA EN CAPTURA DE PRODUCCION
	if(existe_registro("total_produccion",array("numcia","codturno"),array($companias[$j]['num_cia'],3),$dsn)){
		$bizcochero=true;
		$sql_biz="
		SELECT 
		numcia as cia, 
		codturno as turno, 
		total_produccion as produccion, 
		raya_pagada as raya, 
		fecha_total as fecha
		FROM 
		total_produccion
		WHERE 
		numcia=".$companias[$j]['num_cia']." and 
		fecha_total between '".$fecha_inicio."' and '".$_GET['fecha_mov']."' and 
		codturno=3 
		order by fecha
		";
		$sql_biz1="
		SELECT 
		num_cia as cia, 
		cod_turno as turno, 
		cantidad as cantidad, 
		fecha 
		FROM 
		mov_inv_real
		WHERE 
		num_cia=".$companias[$j]['num_cia']." and 
		fecha between '".$fecha_inicio."' and '".$_GET['fecha_mov']."' and 
		cod_turno=3 and 
		tipo_mov=true and
		codmp=1
		group by cia,turno,fecha,cantidad
		order by fecha
		";
		$produccion_biz=ejecutar_script($sql_biz,$dsn);
		$movimiento_biz=ejecutar_script($sql_biz1,$dsn);
		$bandera+=1;
	}
	else $bandera-=1;
	
	//VERIFICA LA EXISTENCIA DEL REPOSTERO PARA LA COMPAÑÍA EN CAPTURA DE PRODUCCION
	if(existe_registro("total_produccion",array("numcia","codturno"),array($companias[$j]['num_cia'],4),$dsn)){
		$repostero=true;
		$sql_rep="
		SELECT 
		numcia as cia, 
		codturno as turno, 
		total_produccion as produccion, 
		raya_pagada as raya, 
		fecha_total as fecha
		FROM 
		total_produccion
		WHERE 
		numcia=".$companias[$j]['num_cia']." and 
		fecha_total between '".$fecha_inicio."' and '".$_GET['fecha_mov']."' and 
		codturno=4 
		order by fecha
		";
		$sql_rep1="
		SELECT 
		num_cia as cia, 
		cod_turno as turno, 
		cantidad as cantidad, 
		fecha 
		FROM 
		mov_inv_real
		WHERE 
		num_cia=".$companias[$j]['num_cia']." and 
		fecha between '".$fecha_inicio."' and '".$_GET['fecha_mov']."' and 
		cod_turno=4 and 
		tipo_mov=true and
		codmp=1
		group by cia,turno,fecha,cantidad
		order by fecha;
		";
		$produccion_rep=ejecutar_script($sql_rep,$dsn);
		$movimiento_rep=ejecutar_script($sql_rep1,$dsn);
		$bandera+=1;
	}
	else $bandera-=1;
//************************************
	if($bandera<0)
	{
	//SI LA CONSULTA SE HACE PARA UNA SOLA COMPAÑÍA Y NO SE ENCUENTRAN MOVIMIENTOS EN PRODUCCION PASA A LA PANTALLA DE ERROR
		if($_GET['tipo_cia'] == 0){
			header("location: ./pan_ren_con.php?codigo_error=1");
			die();
		}
	//SI LA CONSULTA SE HACE PARA TODAS LAS COMPAÑÍAS  NO SE ENCUENTRAN MOVIMIENTOS ENTONCES PASA A LA SIGUIENTE COMPAÑÍA
		else
			continue;
	}

//***************************************
	if($_GET['tipo_turno']==0 or $_GET['tipo_turno']==1)
	{
		$tpl->newBlock("compania");
		$dia_1 = explode("/",$_GET['fecha_mov']);
		$tpl->assign("dia",$dia_1[0]);
		$tpl->assign("anio",$dia_1[2]);
		$tpl->assign("mes",$nombremes[$dia_1['1']]);

		$tpl->assign("num_cia",$companias[$j]['num_cia']);
		$tpl->assign("nom_cia",$companias[$j]['nombre_corto']);
		$nom_turno1=obtener_registro("catalogo_turnos",array("cod_turno"),array($turno1),"","",$dsn);
		$nom_turno2=obtener_registro("catalogo_turnos",array("cod_turno"),array($turno2),"","",$dsn);
		
	//
		//FRANCESERO DE NOCHE
		if($turno1==2)$tpl->assign("nom_turno","F&nbsp;&nbsp;R&nbsp;&nbsp;A&nbsp;&nbsp;N&nbsp;&nbsp;C&nbsp;&nbsp;E&nbsp;&nbsp;S&nbsp;&nbsp;E&nbsp;&nbsp;R&nbsp;&nbsp;O&nbsp;&nbsp;&nbsp; D&nbsp;&nbsp;E&nbsp;&nbsp; N&nbsp;&nbsp;O&nbsp;&nbsp;C&nbsp;&nbsp;H&nbsp;&nbsp;E");
		else $tpl->assign("nom_turno","B&nbsp;&nbsp;I&nbsp;&nbsp;Z&nbsp;&nbsp;C&nbsp;&nbsp;O&nbsp;&nbsp;C&nbsp;&nbsp;H&nbsp;&nbsp;E&nbsp;&nbsp;R&nbsp;&nbsp;O");
		//FRANCESERO DE DIA
		if($turno2==1)$tpl->assign("nom_turno1","F&nbsp;R&nbsp;A&nbsp;N&nbsp;C&nbsp;E&nbsp;S&nbsp;E&nbsp;R&nbsp;O&nbsp;&nbsp; D&nbsp;&nbsp;E&nbsp;&nbsp; D&nbsp;&nbsp;I&nbsp;&nbsp;A");
		else $tpl->assign("nom_turno1","R&nbsp;&nbsp;E&nbsp;&nbsp;P&nbsp;&nbsp;O&nbsp;&nbsp;S&nbsp;&nbsp;T&nbsp;&nbsp;E&nbsp;&nbsp;R&nbsp;&nbsp;O");
		
		$aux_t1_prod=0;
		$aux_t1_mov=0;
		$aux_t2_prod=0;
		$aux_t2_mov=0;
		
		$aux_dias_ren1=0;
		$aux_dias_ren2=0;
		
		$total_rendimiento1=0;
		$total_rendimiento2=0;
		for($i=0;$i<$dia_1[0];$i++){
			$tpl->newBlock("rows");
			$dia=$i+1;
			$tpl->assign("dia",$dia);
			$fecha_armada=$dia."/".$dia_1[1]."/".$dia_1[2];
//************************			
			if($_GET['tipo_turno']==0){//FRANCESEROS
				if($francesero_dia==true)
				{
					$dia_produccion=explode("/",@$produccion_dia[$aux_t2_prod]['fecha']);
					$dia_movimiento=explode("/",@$movimiento_dia[$aux_t2_mov]['fecha']);
					$index_pro = buscar($produccion_dia, $dia);
					$index_mov = buscar($movimiento_dia, $dia);
					$dia_pro = $index_pro !== NULL ? $produccion_dia[$index_pro] : NULL;
					$dia_mov = $index_mov !== NULL ? $movimiento_dia[$index_mov] : NULL;
					if ($index_pro !== NULL || $index_mov !== NULL)
					//if($dia==number_format($dia_produccion[0],'','','') and $dia==number_format($dia_movimiento[0],'','',''))
					{
							@$rendimiento2=$produccion_dia[/*$aux_t2_prod*/$index_pro]['produccion'] / ($movimiento_dia[/*$aux_t2_mov*/$index_mov]['cantidad'] / $relacion);
							$rendimiento2=number_format($rendimiento2,2,'.','');
							$aux_dias_ren2++;
							@$cantidad2=$movimiento_dia[/*$aux_t2_mov*/$index_mov]['cantidad'];
							@$produccion2=$produccion_dia[/*$aux_t2_prod*/$index_pro]['produccion'];
							@$raya2=$produccion_dia[/*$aux_t2_prod*/$index_pro]['raya'];
							$cantidad2=number_format($cantidad2,2,'.','');
							$produccion2=number_format($produccion2,2,'.','');
							$raya2=number_format($raya2,2,'.','');
							
					}
					else{
						$rendimiento2=0;
						$cantidad2=0;
						$produccion2=0;
						$raya2=0;
					}
					if($dia==number_format(floatval($dia_produccion[0]))) $aux_t2_prod++;
					if($dia==number_format(floatval($dia_movimiento[0]))) $aux_t2_mov++;

				}
				else{
					$rendimiento2=0;
					$cantidad2=0;
					$produccion2=0;
					$raya2=0;
				}
				

				if($francesero_noche==true)
				{
					$noche_produccion=explode("/",@$produccion_noche[$aux_t1_prod]['fecha']);
					$noche_movimiento=explode("/",@$movimiento_noche[$aux_t1_mov]['fecha']);
					$index_pro = buscar($produccion_noche, $dia);
					$index_mov = buscar($movimiento_noche, $dia);
					$dia_pro = $index_pro !== NULL ? $produccion_noche[$index_pro] : NULL;
					$dia_mov = $index_mov !== NULL ? $movimiento_noche[$index_mov] : NULL;
					if ($index_pro !== NULL || $index_mov !== NULL)
					//if($dia==number_format($noche_produccion[0],'','','') and $dia==number_format($noche_movimiento[0],'','',''))
					{
							@$rendimiento1=$produccion_noche[/*$aux_t1_prod*/$index_pro]['produccion'] / ($movimiento_noche[/*$aux_t1_mov*/$index_mov]['cantidad'] / $relacion);
							$rendimiento1=number_format($rendimiento1,2,'.','');
							$aux_dias_ren1++;
							@$cantidad1=$movimiento_noche[/*$aux_t1_mov*/$index_mov]['cantidad'];
							$cantidad1=number_format($cantidad1,2,'.','');
							@$produccion1=$produccion_noche[/*$aux_t1_prod*/$index_pro]['produccion'];
							@$raya1=$produccion_noche[/*$aux_t1_prod*/$index_pro]['raya'];
							$cantidad1=number_format($cantidad1,2,'.','');
							$produccion1=number_format($produccion1,2,'.','');
							$raya1=number_format($raya1,2,'.','');
					}
					else{
						$rendimiento1=0;
						$cantidad1=0;
						$produccion1=0;
						$raya1=0;
					}
					if($dia==number_format(floatval($noche_produccion[0]))) $aux_t1_prod++;
					if($dia==number_format(floatval($noche_movimiento[0]))) $aux_t1_mov++;
				}
				else{
					$rendimiento1=0;
					$cantidad1=0;
					$produccion1=0;
					$raya1=0;
				}

			}
			if($_GET['tipo_turno']==1)//BIZCOCHERO, REPOSTERO
			{
				if($bizcochero==true)
				{
					$biz_produccion=explode("/",@$produccion_biz[$aux_t1_prod]['fecha']);
					$biz_movimiento=explode("/",@$movimiento_biz[$aux_t1_mov]['fecha']);
					$index_pro = buscar($produccion_biz, $dia);
					$index_mov = buscar($movimiento_biz, $dia);
					$dia_pro = $index_pro !== NULL ? $produccion_biz[$index_pro] : NULL;
					$dia_mov = $index_mov !== NULL ? $movimiento_biz[$index_mov] : NULL;
					if ($index_pro !== NULL || $index_mov !== NULL)
					//if($dia==number_format($biz_produccion[0],'','','') and $dia==number_format($biz_movimiento[0],'','',''))
					{
							@$rendimiento1=$produccion_biz[/*$aux_t1_prod*/$index_pro]['produccion'] / ($movimiento_biz[/*$aux_t1_mov*/$index_mov]['cantidad'] / $relacion);
							$rendimiento1=number_format($rendimiento1,2,'.','');
							$aux_dias_ren1++;
							@$cantidad1=$movimiento_biz[/*$aux_t1_mov*/$index_mov]['cantidad'];
							@$produccion1=$produccion_biz[/*$aux_t1_prod*/$index_pro]['produccion'];
							@$raya1=$produccion_biz[/*$aux_t1_prod*/$index_pro]['raya'];
							$cantidad1=number_format($cantidad1,2,'.','');
							$produccion1=number_format($produccion1,2,'.','');
							$raya1=number_format($raya1,2,'.','');
							
					}
					else{
						$rendimiento1=0;
						$cantidad1=0;
						$produccion1=0;
						$raya1=0;
					}
					if($dia==number_format(floatval($biz_produccion[0]))) $aux_t1_prod++;
					if($dia==number_format(floatval($biz_movimiento[0]))) $aux_t1_mov++;

				}
				else{
					$rendimiento1=0;
					$cantidad1=0;
					$produccion1=0;
					$raya1=0;
				}

				if($repostero==true)
				{
					$rep_produccion=explode("/",@$produccion_rep[$aux_t2_prod]['fecha']);
					$rep_movimiento=explode("/",@$movimiento_rep[$aux_t2_mov]['fecha']);
					$index_pro = buscar($produccion_rep, $dia);
					$index_mov = buscar($movimiento_rep, $dia);
					$dia_pro = $index_pro !== NULL ? $produccion_rep[$index_pro] : NULL;
					$dia_mov = $index_mov !== NULL ? $movimiento_rep[$index_mov] : NULL;
					if ($index_pro !== NULL || $index_mov !== NULL)
					//if($dia==number_format($rep_produccion[0],'','','') and $dia==number_format($rep_movimiento[0],'','',''))
					{
							@$rendimiento2=$produccion_rep[/*$aux_t2_prod*/$index_pro]['produccion'] / ($movimiento_rep[/*$aux_t2_mov*/$index_mov]['cantidad'] / $relacion);
							$rendimiento2=number_format($rendimiento2,2,'.','');
							$aux_dias_ren2++;
							@$cantidad2=$movimiento_rep[/*$aux_t2_mov*/$index_mov]['cantidad'];
							@$produccion2=$produccion_rep[/*$aux_t2_prod*/$index_pro]['produccion'];
							@$raya2=$produccion_rep[/*$aux_t2_prod*/$index_pro]['raya'];
							$cantidad2=number_format($cantidad2,2,'.','');
							$produccion2=number_format($produccion2,2,'.','');
							$raya2=number_format($raya2,2,'.','');
							
					}
					else{
						$rendimiento2=0;
						$cantidad2=0;
						$produccion2=0;
						$raya2=0;
					}
					if($dia==number_format(floatval($rep_produccion[0]))) $aux_t2_prod++;
					if($dia==number_format(floatval($rep_movimiento[0]))) $aux_t2_mov++;

				}
				else{
						$rendimiento2=0;
						$cantidad2=0;
						$produccion2=0;
						$raya2=0;
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

			if($francesero_dia==true or $repostero==true)
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
				$total_consumo1 += ($cantidad1 / $relacion);
				$total_produccion1 += $produccion1;
				$total_raya1 += $raya1;
			}
			if($francesero_dia==true or $repostero==true)
			{
				$total_consumo2 += ($cantidad2 / $relacion);
				$total_produccion2 += $produccion2;
				$total_raya2 += $raya2;
			}
		$total_rendimiento1 += number_format($rendimiento1,2,'.','');
		$total_rendimiento2 += number_format($rendimiento2,2,'.','');
		//echo "$total_consumo2 - $total_produccion2 - $total_raya2 - $total_rendimiento2<br>";
		}
		
		$tpl->gotoBlock("compania");
		
		if($total_consumo2 > 0)
			$total_rendimiento2 = $total_produccion2 / $total_consumo2;
		else
			$total_rendimiento2 = 0;
			
		if($total_consumo1 > 0)
			$total_rendimiento1 = $total_produccion1 / $total_consumo1;
		else
			$total_rendimiento1 = 0;
		
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
	//echo "$total_consumo2 - $total_produccion2 - $total_raya2 - $total_rendimiento2\n";
		$tpl->assign("total_consumo1",number_format($total_consumo2,2,'.',','));
		$tpl->assign("total_produccion1",number_format($total_produccion2,2,'.',','));
		$tpl->assign("total_raya1",number_format($total_raya2,2,'.',','));
		$tpl->assign("total_rendimiento1",number_format($total_rendimiento2,2,'.',','));
		
		$tpl->assign("consumo_total",number_format($consumo_total,2,'.',','));
		$tpl->assign("produccion_total",number_format($produccion_total,2,'.',','));
		$tpl->assign("rendimiento_total",number_format($rendimiento_total,2,'.',','));
		
		if($salto % 2 == 0)
			$tpl->newBlock("salto");
		$salto++;
	
	}
	//********************************************EFECTIVOS Y RENDIMIENTOS
	else if($_GET['turno']==2){
		$tpl->newBlock("turnos");
		$dia_1 = explode("/",$_GET['fecha_mov']);
		$tpl->assign("dia",$dia_1[0]);
		$tpl->assign("anio",$dia_1[2]);
		$tpl->assign("mes",$nombremes[$dia_1['1']]);
		$tpl->assign("num_cia",$companias[$j]['num_cia']);
		$tpl->assign("nom_cia",$companias[$j]['nombre_corto']);
			$aux_t1_prod=0;
			$aux_t1_mov=0;
			$aux_t2_prod=0;
			$aux_t2_mov=0;
			$aux_t3_prod=0;
			$aux_t3_mov=0;
			$aux_t4_prod=0;
			$aux_t4_mov=0;
			
			$aux_dias_ren1=0;
			$aux_dias_ren2=0;
			$aux_dias_ren3=0;
			$aux_dias_ren4=0;
			$total_produccion1=0;
			$total_produccion2=0;
			$total_produccion3=0;
			$total_produccion4=0;
			
			
		for($i=0;$i<$dia_1[0];$i++){
		
			$tpl->newBlock("renglones");
			$dia=$i+1;
			$tpl->assign("dia",$dia);
			$fecha_armada=$dia."/".$dia_1[1]."/".$dia_1[2];
			$sql="select efectivo from total_panaderias where num_cia=".$companias[$j]['num_cia']." and fecha='".$fecha_armada."'";
			$efectivo=ejecutar_script($sql,$dsn);
			$tpl->assign("efectivo",number_format($efectivo[0]['efectivo'],2,'.',','));
			$total_efectivo += $efectivo[0]['efectivo'];
			
			if($francesero_dia==true)
			{
				$dia_produccion=explode("/",@$produccion_dia[$aux_t2_prod]['fecha']);
				$dia_movimiento=explode("/",@$movimiento_dia[$aux_t2_mov]['fecha']);
				if($dia==number_format($dia_produccion[0],2,'','') and $dia==number_format($dia_movimiento[0],2,'',''))
				{
					@$rendimiento2=$produccion_dia[$aux_t2_prod]['produccion'] / ($movimiento_dia[$aux_t2_mov]['cantidad'] / $relacion);
					$aux_dias_ren2++;
					$cantidad2=$movimiento_dia[$aux_t2_mov]['cantidad'];
					$produccion2=$produccion_dia[$aux_t2_prod]['produccion'];
					$raya2=$produccion_dia[$aux_t2_prod]['raya'];
					
					$cantidad2=number_format($cantidad2,2,'.','');
					$produccion2=number_format($produccion2,2,'.','');
					$raya2=number_format($raya2,2,'.','');
				}
				else{
					$rendimiento2=0;
					$cantidad2=0;
					$produccion2=0;
					$raya2=0;
				}
				if($dia==number_format($dia_produccion[0],2,'','')) $aux_t2_prod++;
				if($dia==number_format($dia_movimiento[0],2,'','')) $aux_t2_mov++;

			}
			else{
				$rendimiento2=0;
				$cantidad2=0;
				$produccion2=0;
				$raya2=0;
			}
			
			if($francesero_noche==true)
			{
				$noche_produccion=explode("/",@$produccion_noche[$aux_t1_prod]['fecha']);
				$noche_movimiento=explode("/",@$movimiento_noche[$aux_t1_mov]['fecha']);
				
				if($dia==number_format($noche_produccion[0],2,'','') and $dia==number_format($noche_movimiento[0],2,'',''))
				{
					@$rendimiento1=$produccion_noche[$aux_t1_prod]['produccion'] / ($movimiento_noche[$aux_t1_mov]['cantidad'] / $relacion);
					$aux_dias_ren1++;
					$cantidad1=$movimiento_noche[$aux_t1_mov]['cantidad'];
					$produccion1=$produccion_noche[$aux_t1_prod]['produccion'];
					$raya1=$produccion_noche[$aux_t1_prod]['raya'];

					$cantidad1=number_format($cantidad1,2,'.','');
					$produccion1=number_format($produccion1,2,'.','');
					$raya1=number_format($raya1,2,'.','');
					
				}
				else{
					$rendimiento1=0;
					$cantidad1=0;
					$produccion1=0;
					$raya1=0;
				}
				if($dia==number_format($noche_produccion[0],2,'','')) $aux_t1_prod++;
				if($dia==number_format($noche_movimiento[0],2,'','')) $aux_t1_mov++;
			}
			else{
				$rendimiento1=0;
				$cantidad1=0;
				$produccion1=0;
				$raya1=0;
			}

			if($bizcochero==true)
			{
				$biz_produccion=explode("/",@$produccion_biz[$aux_t3_prod]['fecha']);
				$biz_movimiento=explode("/",@$movimiento_biz[$aux_t3_mov]['fecha']);
				if($dia==number_format($biz_produccion[0],2,'','') and $dia==number_format($biz_movimiento[0],2,'',''))
				{
					@$rendimiento3=$produccion_biz[$aux_t3_prod]['produccion'] / ($movimiento_biz[$aux_t3_mov]['cantidad'] / $relacion);
					$aux_dias_ren3++;
					$cantidad3=$movimiento_biz[$aux_t3_mov]['cantidad'];
					$produccion3=$produccion_biz[$aux_t3_prod]['produccion'];
					$raya3=$produccion_biz[$aux_t3_prod]['raya'];

					$cantidad3=number_format($cantidad3,2,'.','');
					$produccion3=number_format($produccion3,2,'.','');
					$raya3=number_format($raya3,2,'.','');
					
				}
				else{
					$rendimiento3=0;
					$cantidad3=0;
					$produccion3=0;
					$raya3=0;
				}
				if($dia==number_format($biz_produccion[0],2,'','')) $aux_t3_prod++;
				if($dia==number_format($biz_movimiento[0],2,'','')) $aux_t3_mov++;

			}
			else{
				$rendimiento3=0;
				$cantidad3=0;
				$produccion3=0;
				$raya3=0;
			}

			if($repostero==true)
			{
				$rep_produccion=explode("/",@$produccion_rep[$aux_t4_prod]['fecha']);
				$rep_movimiento=explode("/",@$movimiento_rep[$aux_t4_mov]['fecha']);

				if($dia==number_format($rep_produccion[0],2,'','') and $dia==number_format($rep_movimiento[0],2,'',''))
				{
					@$rendimiento4=$produccion_rep[$aux_t4_prod]['produccion'] / ($movimiento_rep[$aux_t4_mov]['cantidad'] / $relacion);
					$aux_dias_ren4++;
					$cantidad4=$movimiento_rep[$aux_t4_mov]['cantidad'];
					$produccion4=$produccion_rep[$aux_t4_prod]['produccion'];
					$raya4=$produccion_rep[$aux_t4_prod]['raya'];

					$cantidad4=number_format($cantidad4,2,'.','');
					$produccion4=number_format($produccion4,2,'.','');
					$raya4=number_format($raya4,2,'.','');
					
				}
				else{
					$rendimiento4=0;
					$cantidad4=0;
					$produccion4=0;
					$raya4=0;
				}
				if($dia==number_format($rep_produccion[0],2,'','')) $aux_t4_prod++;
				if($dia==number_format($rep_movimiento[0],2,'','')) $aux_t4_mov++;

			}
			else{
				$rendimiento4=0;
				$cantidad4=0;
				$produccion4=0;
				$raya4=0;
			}
			
			//FRANCESERO DE NOCHE
			if($cantidad2 <=0) $tpl->assign("consumo2","");
			else{
				$bultos2=$cantidad2 / $relacion;
				$total_consumo2+=$bultos2;
				$total_produccion2 += $produccion2;
				$tpl->assign("consumo2",number_format($bultos2,2,'.',','));
			}
			if($rendimiento2 <=0) $tpl->assign("rendimiento2","");
			else{
				$tpl->assign("rendimiento2",number_format($rendimiento2,2,'.',','));
				$total_rendimiento2+=number_format($rendimiento2,2,'.','');
			}
			
			//FRANCESERO DE DIA
			if($cantidad1 <=0) $tpl->assign("consumo1","");
			else{
				$bultos1=$cantidad1 / $relacion;
				$total_consumo1+=$bultos1;
				$total_produccion1 += $produccion1;
				$tpl->assign("consumo1",number_format($bultos1,2,'.',','));
			}
			if($rendimiento1 <=0) $tpl->assign("rendimiento1","");
			else {
				$tpl->assign("rendimiento1",number_format($rendimiento1,2,'.',','));
				$total_rendimiento1+=number_format($rendimiento1,2,'.','');
			}

			//BIZCOCHERO
			if($cantidad3 <=0) $tpl->assign("consumo3","");
			else{
				$bultos3=$cantidad3 / $relacion;
				$total_consumo3+=$bultos3;				
				$total_produccion3 += $produccion3;
				$tpl->assign("consumo3",number_format($bultos3,2,'.',','));
			}
			if($rendimiento3 <=0) $tpl->assign("rendimiento3","");
			else{
				$tpl->assign("rendimiento3",number_format($rendimiento3,2,'.',','));
				$total_rendimiento3+=number_format($rendimiento3,2,'.','');
			}

			//REPOSTERO
			if($cantidad4 <=0) $tpl->assign("consumo4","");
			else{
				$bultos4=$cantidad4 / $relacion;
				$total_consumo4+=$bultos4;
				$total_produccion4 += $produccion4;
				$tpl->assign("consumo4",number_format($bultos4,2,'.',','));
			}
			if($rendimiento4 <=0) $tpl->assign("rendimiento4","");
			else{
				$tpl->assign("rendimiento4",number_format($rendimiento4,2,'.',','));
				$total_rendimiento4+=number_format($rendimiento4,2,'.','');
			}
			
		}//FIN DEL CICLO
		$tpl->gotoBlock("turnos");
		if($total_consumo2 > 0)
			$total_rendimiento2 = $total_produccion2 / $total_consumo2;
		else
			$total_rendimiento2 = 0;
			
		if($total_consumo1 > 0)
			$total_rendimiento1 = $total_produccion1 / $total_consumo1;
		else
			$total_rendimiento1 = 0;

		if($total_consumo3 > 0)
			$total_rendimiento3 = $total_produccion3 / $total_consumo3;
		else
			$total_rendimiento3 = 0;
			
		if($total_consumo4 > 0)
			$total_rendimiento4 = $total_produccion4 / $total_consumo4;
		else
			$total_rendimiento4 = 0;

		$tpl->assign("total_consumo1",number_format($total_consumo1,2,'.',','));
		$tpl->assign("total_rendimiento1",number_format($total_rendimiento1,2,'.',','));
		$tpl->assign("total_consumo2",number_format($total_consumo2,2,'.',','));
		$tpl->assign("total_rendimiento2",number_format($total_rendimiento2,2,'.',','));
		$tpl->assign("total_consumo3",number_format($total_consumo3,2,'.',','));
		$tpl->assign("total_rendimiento3",number_format($total_rendimiento3,2,'.',','));
		$tpl->assign("total_consumo4",number_format($total_consumo4,2,'.',','));
		$tpl->assign("total_rendimiento4",number_format($total_rendimiento4,2,'.',','));
		$tpl->assign("total_efectivo",number_format($total_efectivo,2,'.',','));

		if($salto1 % 2 == 0)
			$tpl->newBlock("salto1");
		$salto1++;
		
	}
}
$tpl->printToScreen();
// --------------------------------------------------------------------------------------------------------
?>