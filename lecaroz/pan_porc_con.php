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
$descripcion_error[2] = "NO TIENES COMPAÑÍAS ASIGNADAS";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/pan/pan_porc_con.tpl");
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

	$pibote=date("d");
	for($i=0;$i<=6;$i++){
		$fecha_anterior= date("j/n/Y",mktime(0,0,0,date("m"),$pibote,date("Y")));
		$letra= date("D",mktime(0,0,0,date("m"),$pibote,date("Y")));
		if($letra=="Sun"){
			$tpl->assign("fecha_anterior",$fecha_anterior);
			break;
		}
		$pibote--;
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
	$tpl->printToScreen();
	die();
}
// -------------------------------- SCRIPT ---------------------------------------------------------
// -------------------------------- Mostrar listado ---------------------------------------------------------
//$tpl->newBlock("prueba_pan");
//ARROJA EL NUMERO DE ITERACIONES DENTRO DEL FOR A PARTIR DEL RANGO DE FECHAS
//$fecha_inicio='1/'.date("m").'/'.date("Y");
$fech=explode("/",$_GET['fecha_mov']);
$fecha_inicio='1/'.$fech[1].'/'.$fech[2];
$turno1=0;
$turno2=0;
$turno3=0;
$turno4=0;
$relacion=44;
if($_GET['tipo_cia'] == 0)
	$cia="select num_cia, nombre_corto from catalogo_companias where num_cia='".$_GET['num_cia']."'";
else if($_GET['tipo_cia'] == 1){
	if($_SESSION['iduser']==1 or $_SESSION['iduser']==4 or $_SESSION['iduser']==42){
		$cia="select num_cia, nombre_corto from catalogo_companias where num_cia between 1 and 300";
	}
	else{
		$sql="select * from catalogo_operadoras where iduser=".$_SESSION['iduser'];
		$operadora=ejecutar_script($sql,$dsn);
		if(!$operadora){
			header("location: ./pan_porc_con.php?codigo_error=2");
			die();
		}
		else{
		$cia="select num_cia, nombre_corto from catalogo_companias where idoperadora=".$operadora[0]['idoperadora']." and num_cia < 300 order by num_cia";
		}
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


$companias=ejecutar_script($cia,$dsn);
//print_r($companias);
$salto=1;
$salto1=1;
$relacion=44;
$bandera=/*0*/FALSE;
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
		$sql_harina="
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
		order by fecha
		";
		$sql_azucar1="
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
		codmp=3
		order by fecha
		";
		$sql_azucar2="
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
		codmp=4
		order by fecha
		";

		$sql_ultrapan="
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
		codmp=67
		order by fecha
		";

		$sql_levadura="
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
		codmp=149
		order by fecha
		";
		$sql_grasa="
		SELECT 
		num_cia as cia, 
		cod_turno as turno, 
		SUM(cantidad) as cantidad, 
		fecha 
		FROM 
		mov_inv_real
		LEFT JOIN catalogo_mat_primas USING (codmp)
		WHERE 
		num_cia=".$companias[$j]['num_cia']." and 
		fecha between '".$fecha_inicio."' and '".$_GET['fecha_mov']."' and 
		cod_turno=1 and 
		tipo_mov=true and
		--codmp=38
		grasa = TRUE AND
		codmp NOT IN (86)
		GROUP BY cia, turno, fecha
		order by fecha
		";
		$sql_aceite="
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
		codmp=86
		order by fecha
		";
		$fd_harina=ejecutar_script($sql_harina,$dsn);
		$fd_azucar1=ejecutar_script($sql_azucar1,$dsn);
		$fd_azucar2=ejecutar_script($sql_azucar2,$dsn);
		$fd_ultrapan=ejecutar_script($sql_ultrapan,$dsn);
		$fd_levadura=ejecutar_script($sql_levadura,$dsn);
		$fd_grasa=ejecutar_script($sql_grasa,$dsn);
		$fd_aceite=ejecutar_script($sql_aceite,$dsn);
//		echo $sql_ultrapan;
//		print_r($fd_ultrapan);
		/*$bandera+=1*/$bandera = TRUE;
	}
	//else $bandera-=1;
	
	//VERIFICA LA EXISTENCIA DEL FRANCESERO DE NOCHE PARA LA COMPAÑÍA EN CAPTURA DE PRODUCCION
	if(existe_registro("total_produccion",array("numcia","codturno"),array($companias[$j]['num_cia'],2),$dsn)){
		$francesero_noche=true;
		$sql_harina="
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
		order by fecha
		";
		$sql_azucar1="
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
		codmp=3
		order by fecha
		";
		$sql_azucar2="
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
		codmp=4
		order by fecha
		";

		$sql_ultrapan="
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
		codmp=67
		order by fecha
		";

		$sql_levadura="
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
		codmp=149
		order by fecha
		";
		
		$sql_grasa="
		SELECT 
		num_cia as cia, 
		cod_turno as turno, 
		SUM(cantidad) as cantidad, 
		fecha 
		FROM 
		mov_inv_real
		LEFT JOIN catalogo_mat_primas USING (codmp)
		WHERE 
		num_cia=".$companias[$j]['num_cia']." and 
		fecha between '".$fecha_inicio."' and '".$_GET['fecha_mov']."' and 
		cod_turno=2 and 
		tipo_mov=true and
		--codmp=38
		grasa = TRUE AND
		codmp NOT IN (86)
		GROUP BY cia, turno, fecha
		order by fecha
		";
		$sql_aceite="
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
		codmp=86
		order by fecha
		";
		
		$fn_harina=ejecutar_script($sql_harina,$dsn);
		$fn_azucar1=ejecutar_script($sql_azucar1,$dsn);
		$fn_azucar2=ejecutar_script($sql_azucar2,$dsn);
		$fn_ultrapan=ejecutar_script($sql_ultrapan,$dsn);
		$fn_levadura=ejecutar_script($sql_levadura,$dsn);
		$fn_grasa=ejecutar_script($sql_grasa,$dsn);
		$fn_aceite=ejecutar_script($sql_aceite,$dsn);
//		echo "$sql_harina<br>";
		/*$bandera+=1*/$bandera = TRUE;
	}
	//else $bandera-=1;

	//VERIFICA LA EXISTENCIA DEL BIZCOCHERO PARA LA COMPAÑÍA EN CAPTURA DE PRODUCCION
	if(existe_registro("total_produccion",array("numcia","codturno"),array($companias[$j]['num_cia'],3),$dsn)){
		$bizcochero=true;
		$sql_harina="
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
		order by fecha
		";
		$sql_azucar1="
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
		codmp=3
		order by fecha
		";
		$sql_azucar2="
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
		codmp=4
		order by fecha
		";
//------
		$sql_grasa="
		SELECT 
		num_cia as cia, 
		cod_turno as turno, 
		SUM(cantidad) as cantidad, 
		fecha 
		FROM 
		mov_inv_real
		LEFT JOIN catalogo_mat_primas USING (codmp)
		WHERE 
		num_cia=".$companias[$j]['num_cia']." and 
		fecha between '".$fecha_inicio."' and '".$_GET['fecha_mov']."' and 
		cod_turno=3 and 
		tipo_mov=true and
		--codmp=38
		grasa = TRUE
		GROUP BY cia, turno, fecha
		order by fecha
		";
		$sql_aceite="
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
		codmp=86
		order by fecha
		";
		$sql_manteca="
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
		codmp=49
		order by fecha
		";
		$sql_mantequilla="
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
		codmp=44
		order by fecha
		";
		$sql_margarina_oj="
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
		codmp=45
		order by fecha
		";
		$sql_margarina_re="
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
		codmp=47
		order by fecha
		";

		$sql_huevo="
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
		codmp=148
		order by fecha
		";
		$biz_harina=ejecutar_script($sql_harina,$dsn);
		$biz_azucar1=ejecutar_script($sql_azucar1,$dsn);
		$biz_azucar2=ejecutar_script($sql_azucar2,$dsn);
		$biz_grasa=ejecutar_script($sql_grasa,$dsn);
		$biz_aceite=ejecutar_script($sql_aceite,$dsn);
		$biz_manteca=ejecutar_script($sql_manteca,$dsn);
		$biz_mantequilla=ejecutar_script($sql_mantequilla,$dsn);
		$biz_margarina_oj=ejecutar_script($sql_margarina_oj,$dsn);
		$biz_margarina_re=ejecutar_script($sql_margarina_re,$dsn);
		$biz_huevo=ejecutar_script($sql_huevo,$dsn);

		/*$bandera+=1*/$bandera = TRUE;
	}
	//else $bandera-=1;
	
	//VERIFICA LA EXISTENCIA DEL REPOSTERO PARA LA COMPAÑÍA EN CAPTURA DE PRODUCCION
	if(existe_registro("total_produccion",array("numcia","codturno"),array($companias[$j]['num_cia'],4),$dsn)){
		$repostero=true;
		$sql_harina="
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
		order by fecha
		";
		$sql_azucar1="
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
		codmp=3
		order by fecha
		";
		$sql_azucar2="
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
		codmp=4
		order by fecha
		";
//------
		$sql_grasa="
		SELECT 
		num_cia as cia, 
		cod_turno as turno, 
		SUM(cantidad) as cantidad, 
		fecha 
		FROM 
		mov_inv_real
		LEFT JOIN catalogo_mat_primas USING (codmp)
		WHERE 
		num_cia=".$companias[$j]['num_cia']." and 
		fecha between '".$fecha_inicio."' and '".$_GET['fecha_mov']."' and 
		cod_turno=4 and 
		tipo_mov=true and
		--codmp=38
		grasa = TRUE
		GROUP BY cia, turno, fecha
		order by fecha
		";
		$sql_aceite="
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
		codmp=86
		order by fecha
		";
		$sql_manteca="
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
		codmp=49
		order by fecha
		";
		$sql_mantequilla="
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
		codmp=44
		order by fecha
		";
		$sql_margarina_oj="
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
		codmp=45
		order by fecha
		";
		$sql_margarina_re="
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
		codmp=47
		order by fecha
		";

		$sql_huevo="
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
		codmp=148
		order by fecha
		";
		$rep_harina=ejecutar_script($sql_harina,$dsn);
		$rep_azucar1=ejecutar_script($sql_azucar1,$dsn);
		$rep_azucar2=ejecutar_script($sql_azucar2,$dsn);
		$rep_grasa=ejecutar_script($sql_grasa,$dsn);
		$rep_aceite=ejecutar_script($sql_aceite,$dsn);
		$rep_manteca=ejecutar_script($sql_manteca,$dsn);
		$rep_mantequilla=ejecutar_script($sql_mantequilla,$dsn);
		$rep_margarina_oj=ejecutar_script($sql_margarina_oj,$dsn);
		$rep_margarina_re=ejecutar_script($sql_margarina_re,$dsn);
		$rep_huevo=ejecutar_script($sql_huevo,$dsn);
		/*$bandera+=1*/$bandera = TRUE;
	}
	//else $bandera-=1;
//************************************
	if(!$bandera/*<0*/)
	{
	//SI LA CONSULTA SE HACE PARA UNA SOLA COMPAÑÍA Y NO SE ENCUENTRAN MOVIMIENTOS EN PRODUCCION PASA A LA PANTALLA DE ERROR
		if($_GET['tipo_cia'] == 0){//echo "$francesero_dia, $francesero_noche, $bizcochero, $repostero";die;
			header("location: ./pan_porc_con.php?codigo_error=1");
			die();
		}
	//SI LA CONSULTA SE HACE PARA TODAS LAS COMPAÑÍAS  NO SE ENCUENTRAN MOVIMIENTOS ENTONCES PASA A LA SIGUIENTE COMPAÑÍA
		else
			continue;
	}

//***************************************
	if($_GET['tipo_turno']==0 )//FRANCESEROS
	{
		$tpl->newBlock("franceseros");
		$tpl->newBlock("compania");
		$dia_1 = explode("/",$_GET['fecha_mov']);
		$tpl->assign("dia",$dia_1[0]);
		$tpl->assign("anio",$dia_1[2]);
		$tpl->assign("mes",$nombremes[$dia_1['1']]);

		$tpl->assign("num_cia",$companias[$j]['num_cia']);
		$tpl->assign("nom_cia",$companias[$j]['nombre_corto']);
		
		$total_harina_fd=0;
		$total_harina_fn=0;
		$total_azucar_fn=0;
		$total_azucar_fd=0;
		$total_ultrapan_fn=0;
		$total_ultrapan_fd=0;
		$total_levadura_fd=0;
		$total_levadura_fn=0;
		$total_aceite_fd=0;
		$total_aceite_fn=0;
		$total_grasa_fd=0;
		$total_grasa_fn=0;
		
		$total_porc_azucar_fd=0;
		$total_porc_ultrapan_fd=0;
		$total_porc_levadura_fd=0;
		$total_porc_grasa_fd=0;
		$total_porc_aceite_fd=0;

		$total_porc_azucar_fn=0;
		$total_porc_ultrapan_fn=0;
		$total_porc_levadura_fn=0;
		$total_porc_grasa_fn=0;
		$total_porc_aceite_fn=0;

		$aux_dia_harina=0;
		$aux_dia_azucar1=0;
		$aux_dia_azucar2=0;
		$aux_dia_ultrapan=0;
		$aux_dia_levadura=0;
		$aux_dia_grasa=0;
		$aux_dia_aceite=0;
		
		$aux_noche_harina=0;
		$aux_noche_azucar1=0;
		$aux_noche_azucar2=0;
		$aux_noche_ultrapan=0;
		$aux_noche_levadura=0;
		$aux_noche_grasa=0;
		$aux_noche_aceite=0;

		for($i=0;$i<$dia_1[0];$i++){
			$tpl->newBlock("rows");
			$dia=$i+1;
			$tpl->assign("dia",$dia);
			$fecha_armada=$dia."/".$dia_1[1]."/".$dia_1[2];
//******************************************************************** DATOS DEL FRANCESERO DE DIA

			if($francesero_dia==true){
				if($fd_harina){
//					if($i<count($fd_harina)){
						$dia_harina=explode("/",@$fd_harina[$aux_dia_harina]['fecha']);
						if($dia==number_format(floatval($dia_harina[0]))){
							$harina_fd=$fd_harina[$aux_dia_harina]['cantidad'];
							$aux_dia_harina++;
						}
						else{
							$harina_fd=0;
						}
//					}
//					else $harina_fd=0;
				}
				else
					$harina_fd=0;
				
				if($fd_azucar1){
//					if($i<count($fd_azucar1)){
						$dia_azucar1=explode("/",@$fd_azucar1[$aux_dia_azucar1]['fecha']);
						if($dia==number_format(floatval($dia_azucar1[0])))
						{
							$azucar1_fd=$fd_azucar1[$aux_dia_azucar1]['cantidad'];
							$aux_dia_azucar1++;
						}
						else{
							$azucar1_fd=0;
						}
//					}
//					else $azucar1_fd=0;
				}
				else
					$azucar1_fd=0;
				
				if($fd_azucar2){
//					if($i<count($fd_azucar2)){
						$dia_azucar2=explode("/",@$fd_azucar2[$aux_dia_azucar2]['fecha']);
						if($dia==number_format(floatval($dia_azucar2[0]))){
							$azucar2_fd=$fd_azucar2[$aux_dia_azucar2]['cantidad'];
							$aux_dia_azucar2++;
						}
						else{
							$azucar2_fd=0;
						}
//					}
//					else $azucar2_fd=0;
				}
				else
					$azucar2_fd=0;
				
				if($fd_ultrapan){
//					if($i<count($fd_ultrapan)){
						$dia_ultrapan=explode("/",@$fd_ultrapan[$aux_dia_ultrapan]['fecha']);
						if($dia==number_format(floatval($dia_ultrapan[0]))){
							$ultrapan_fd=$fd_ultrapan[$aux_dia_ultrapan]['cantidad'];
							$aux_dia_ultrapan++;
						}
						else{
							$ultrapan_fd=0;
						}
//					}
//					else $ultrapan_fd=0;
				}
				else
					$ultrapan_fd=0;
					
				if($fd_levadura){
//					if($i<count($fd_levadura)){
						$dia_levadura=explode("/",@$fd_levadura[$aux_dia_levadura]['fecha']);
						if($dia==number_format(floatval($dia_levadura[0]))){
							$levadura_fd=$fd_levadura[$aux_dia_levadura]['cantidad'];
							$aux_dia_levadura++;
						}
						else{
							$levadura_fd=0;
						}
//					}
//					else $levadura_fd=0;
				}
				else
					$levadura_fd=0;
				
				if($fd_grasa){
//					if($i<count($fd_grasa)){
						$dia_grasa=explode("/",@$fd_grasa[$aux_dia_grasa]['fecha']);
						if($dia==number_format(floatval($dia_grasa[0]))){
							$grasa_fd=$fd_grasa[$aux_dia_grasa]['cantidad'];
							$aux_dia_grasa++;
						}
						else{
							$grasa_fd=0;
						}
//					}
//					else $grasa_fd=0;
				}
				else
					$grasa_fd=0;
					
				if($fd_aceite){
//					if($i<count($fd_aceite)){
						$dia_aceite=explode("/",@$fd_aceite[$aux_dia_aceite]['fecha']);
						if($dia==number_format(floatval($dia_aceite[0]))){
							$aceite_fd=$fd_aceite[$aux_dia_aceite]['cantidad'];
							$aux_dia_aceite++;
						}
						else{
							$aceite_fd=0;
						}
//					}
//					else $aceite_fd=0;
				}
				else
					$aceite_fd=0;
								
			}
			else{
				$harina_fd=0;
				$azucar1_fd=0;
				$azucar2_fd=0;
				$ultrapan_fd=0;
				$levadura_fd=0;
				$grasa_fd=0;
				$aceite_fd=0;
			}
//************************************************************************ DATOS DEL FRANCESERO DE NOCHE
			if($francesero_noche==true){
				if($fn_harina){
//					if($i<count($fn_harina)){
						$noche_harina=explode("/",@$fn_harina[$aux_noche_harina]['fecha']);
						if($dia==number_format(floatval($noche_harina[0]))){
							$harina_fn=$fn_harina[$aux_noche_harina]['cantidad'];
							$aux_noche_harina++;
						}
						else{
							$harina_fn=0;
						}
//					}
//					else $harina_fn=0;
				}
				else
					$harina_fn=0;
					
				if($fn_azucar1){
//					if($i<count($fn_azucar1)){
						$noche_azucar1=explode("/",@$fn_azucar1[$aux_noche_azucar1]['fecha']);
						if($dia==number_format(floatval($noche_azucar1[0])))
						{
							$azucar1_fn=$fn_azucar1[$aux_noche_azucar1]['cantidad'];
							$aux_noche_azucar1++;
						}
						else{
							$azucar1_fn=0;
						}
//					}
//					else $azucar1_fn=0;
				}
				else
					$azucar1_fn=0;
				
				if($fn_azucar2){
//					if($i<count($fn_azucar2)){
						$noche_azucar2=explode("/",@$fn_azucar2[$aux_noche_azucar2]['fecha']);
						if($dia==number_format(floatval($noche_azucar2[0]))){
							$azucar2_fn=$fn_azucar2[$aux_noche_azucar2]['cantidad'];
							$aux_noche_azucar2++;
						}
						else{
							$azucar2_fn=0;
						}
//					}
//					else $azucar2_fn=0;
				}
				else
					$azucar2_fn=0;
				
				if($fn_ultrapan){
//					if($i<count($fn_ultrapan)){
						$noche_ultrapan=explode("/",@$fn_ultrapan[$aux_noche_ultrapan]['fecha']);
						if($dia==number_format(floatval($noche_ultrapan[0]))){
							$ultrapan_fn=$fn_ultrapan[$aux_noche_ultrapan]['cantidad'];
							$aux_noche_ultrapan++;
						}
						else{
							$ultrapan_fn=0;
						}
//					}
//					else $ultrapan_fn=0;
				}
				else
					$ultrapan_fn=0;
				
				if($fn_levadura){
//					if($i<count($fn_levadura)){
						$noche_levadura=explode("/",@$fn_levadura[$aux_noche_levadura]['fecha']);
						if($dia==number_format(floatval($noche_levadura[0]))){
							$levadura_fn=$fn_levadura[$aux_noche_levadura]['cantidad'];
							$aux_noche_levadura++;
						}
						else{
							$levadura_fn=0;
						}
//					}
//					else $levadura_fn=0;
				}
				else
					$levadura_fn=0;
				
				if($fn_grasa){
//					if($i<count($fn_grasa)){
						$noche_grasa=explode("/",@$fn_grasa[$aux_noche_grasa]['fecha']);
						if($dia==number_format(floatval($noche_grasa[0]))){
							$grasa_fn=$fn_grasa[$aux_noche_grasa]['cantidad'];
							$aux_noche_grasa++;
						}
						else{
							$grasa_fn=0;
						}
//					}
//					else $grasa_fn=0;
				}
				else
					$grasa_fn=0;
					
				if($fn_aceite){
//					if($i<count($fn_aceite)){
						$noche_aceite=explode("/",@$fn_aceite[$aux_noche_aceite]['fecha']);
						if($dia==number_format(floatval($noche_aceite[0]))){
							$aceite_fn=$fn_aceite[$aux_noche_aceite]['cantidad'];
							$aux_noche_aceite++;
						}
						else{
							$aceite_fn=0;
						}
//					}
//					else $aceite_fn=0;
				}
				else
					$aceite_fn=0;

			}
			else{
				$harina_fn=0;
				$azucar1_fn=0;
				$azucar2_fn=0;
				$ultrapan_fn=0;
				$levadura_fn=0;
				$grasa_fn=0;
				$aceite_fn=0;
			}
//*********************************
			$harina_fd /= $relacion;
			$azucar_fd = $azucar1_fd + $azucar2_fd;
			if($harina_fd > 0){
				$por_azucar_fd= $azucar_fd / $harina_fd;
				$por_lev_fd=$levadura_fd/$harina_fd;
				$por_ult_fd=$ultrapan_fd/$harina_fd;
			}
			else{
				$por_azucar_fd = 0;
				$por_lev_fd = 0;
				$por_ult_fd = 0;
			}
			
			$harina_fn /= $relacion;
			$azucar_fn = $azucar1_fn + $azucar2_fn;
			if($harina_fn>0){
				$por_azucar_fn= $azucar_fn / $harina_fn;
				$por_lev_fn=$levadura_fn/$harina_fn;
				$por_ult_fn=$ultrapan_fn/$harina_fn;
			}
			else{
				$por_azucar_fn = 0;
				$por_lev_fn = 0;
				$por_ult_fn = 0;
			}
			
			$total_harina_fd += $harina_fd;
			$total_harina_fn += $harina_fn;
			
			$total_azucar_fd += $azucar_fd;
			$total_azucar_fn += $azucar_fn;
			
			$total_ultrapan_fd += $ultrapan_fd;
			$total_ultrapan_fn += $ultrapan_fn;
			
			$total_levadura_fd += $levadura_fd;
			$total_levadura_fn += $levadura_fn;
			
			$total_aceite_fd += $aceite_fd;
			$total_aceite_fn += $aceite_fn;
			
			$total_grasa_fd += $grasa_fd;
			$total_grasa_fn += $grasa_fn;

			if($harina_fd==0) $tpl->assign("harina2","");
			else
				$tpl->assign("harina2",number_format($harina_fd,2,'.',','));
			if($azucar_fd==0) $tpl->assign("azucar2","");
			else
				$tpl->assign("azucar2",number_format($azucar_fd,2,'.',','));
			if($por_azucar_fd==0)$tpl->assign("porc_azucar2","");
			else
				$tpl->assign("porc_azucar2",number_format($por_azucar_fd,3,'.',','));
			if($levadura_fd==0) $tpl->assign("levadura2","");
			else
				$tpl->assign("levadura2",number_format($levadura_fd,2,'.',','));
			if($por_lev_fd==0) $tpl->assign("porc_levadura2","");
			else
				$tpl->assign("porc_levadura2",number_format($por_lev_fd,3,'.',','));
			if($ultrapan_fd==0) $tpl->assign("ultrapan2","");
			else
				$tpl->assign("ultrapan2",number_format($ultrapan_fd,2,'.',','));
			if($por_ult_fd==0) $tpl->assign("porc_ultrapan2","");
			else
				$tpl->assign("porc_ultrapan2",number_format($por_ult_fd,3,'.',','));
			if($grasa_fd==0) $tpl->assign("grasa2","");
			else
				$tpl->assign("grasa2",number_format($grasa_fd,2,'.',','));
			if($aceite_fd==0) $tpl->assign("aceite2","");
			else
				$tpl->assign("aceite2",number_format($aceite_fd,2,'.',','));



			if($harina_fn==0) $tpl->assign("harina1","");
			else
				$tpl->assign("harina1",number_format($harina_fn,2,'.',','));
			if($azucar_fn==0) $tpl->assign("azucar1","");
			else
				$tpl->assign("azucar1",number_format($azucar_fn,2,'.',','));
			if($por_azucar_fn==0)$tpl->assign("porc_azucar1","");
			else
				$tpl->assign("porc_azucar1",number_format($por_azucar_fn,3,'.',','));
			if($levadura_fn==0) $tpl->assign("levadura1","");
			else
				$tpl->assign("levadura1",number_format($levadura_fn,2,'.',','));
			if($por_lev_fn==0) $tpl->assign("porc_levadura1","");
			else
				$tpl->assign("porc_levadura1",number_format($por_lev_fn,3,'.',','));
			if($ultrapan_fn==0) $tpl->assign("ultrapan1","");
			else
				$tpl->assign("ultrapan1",number_format($ultrapan_fn,2,'.',','));
			if($por_ult_fn==0) $tpl->assign("porc_ultrapan1","");
			else
				$tpl->assign("porc_ultrapan1",number_format($por_ult_fn,3,'.',','));
			if($grasa_fn==0) $tpl->assign("grasa1","");
			else
				$tpl->assign("grasa1",number_format($grasa_fn,2,'.',','));
			if($aceite_fn==0) $tpl->assign("aceite1","");
			else
				$tpl->assign("aceite1",number_format($aceite_fn,2,'.',','));
		}
		
		
		if($total_harina_fd > 0){
			$total_porc_azucar_fd=$total_azucar_fd/$total_harina_fd;
			$total_porc_ultrapan_fd=$total_ultrapan_fd/$total_harina_fd;
			$total_porc_levadura_fd=$total_levadura_fd/$total_harina_fd;
			$total_porc_grasa_fd=$total_grasa_fd/$total_harina_fd;		
			$total_porc_aceite_fd=$total_aceite_fd/$total_harina_fd;		
		}
		if($total_harina_fn > 0){
			$total_porc_azucar_fn=$total_azucar_fn/$total_harina_fn;
			$total_porc_ultrapan_fn=$total_ultrapan_fn/$total_harina_fn;
			$total_porc_levadura_fn=$total_levadura_fn/$total_harina_fn;
			$total_porc_grasa_fn=$total_grasa_fn/$total_harina_fn;		
			$total_porc_aceite_fn=$total_aceite_fn/$total_harina_fn;		
		}
		
		
		$tpl->gotoBlock("compania");
		$tpl->assign("total_harina1",number_format($total_harina_fn,2,'.',','));
		$tpl->assign("total_azucar1",number_format($total_azucar_fn,2,'.',','));
		$tpl->assign("total_ultrapan1",number_format($total_ultrapan_fn,2,'.',','));
		$tpl->assign("total_levadura1",number_format($total_levadura_fn,2,'.',','));
		$tpl->assign("total_aceite1",number_format($total_aceite_fn,2,'.',','));
		$tpl->assign("total_grasa1",number_format($total_grasa_fn,2,'.',','));
		
		$tpl->assign("total_harina2",number_format($total_harina_fd,2,'.',','));
		$tpl->assign("total_azucar2",number_format($total_azucar_fd,2,'.',','));
		$tpl->assign("total_ultrapan2",number_format($total_ultrapan_fd,2,'.',','));
		$tpl->assign("total_levadura2",number_format($total_levadura_fd,2,'.',','));
		$tpl->assign("total_aceite2",number_format($total_aceite_fd,2,'.',','));
		$tpl->assign("total_grasa2",number_format($total_grasa_fd,2,'.',','));
		
		$tpl->assign("total_porc_azucar1",number_format($total_porc_azucar_fn,2,'.',','));
		$tpl->assign("total_porc_levadura1",number_format($total_porc_levadura_fn,2,'.',','));
		$tpl->assign("total_porc_ultrapan1",number_format($total_porc_ultrapan_fn,2,'.',','));
		$tpl->assign("porc_grasa1",number_format($total_porc_grasa_fn,2,'.',','));
		$tpl->assign("porc_aceite1",number_format($total_porc_aceite_fn,2,'.',','));

		$tpl->assign("total_porc_azucar2",number_format($total_porc_azucar_fd,2,'.',','));
		$tpl->assign("total_porc_levadura2",number_format($total_porc_levadura_fd,2,'.',','));
		$tpl->assign("total_porc_ultrapan2",number_format($total_porc_ultrapan_fd,2,'.',','));
		$tpl->assign("porc_grasa2",number_format($total_porc_grasa_fd,2,'.',','));
		$tpl->assign("porc_aceite2",number_format($total_porc_aceite_fd,2,'.',','));

		if($salto % 2 == 0)
			$tpl->newBlock("salto");
		$salto++;
	
	}

//**************
	if($_GET['tipo_turno']==1 )//BIZCOCHERO Y REPOSTERO
	{
		$tpl->newBlock("bizcochero");
		$tpl->newBlock("compania2");
		$dia_1 = explode("/",$_GET['fecha_mov']);
		$tpl->assign("dia",$dia_1[0]);
		$tpl->assign("anio",$dia_1[2]);
		$tpl->assign("mes",$nombremes[$dia_1['1']]);

		$tpl->assign("num_cia",$companias[$j]['num_cia']);
		$tpl->assign("nombre_cia",$companias[$j]['nombre_corto']);

			$aux_biz_harina=0;
			$aux_biz_azucar1=0;
			$aux_biz_azucar2=0;
			$aux_biz_grasa=0;
			$aux_biz_aceite=0;
			$aux_biz_manteca=0;
			$aux_biz_mantequilla=0;
			$aux_biz_mar_oj=0;
			$aux_biz_mar_re=0;
			$aux_biz_huevo=0;
			
			$aux_rep_harina=0;
			$aux_rep_azucar1=0;
			$aux_rep_azucar2=0;
			$aux_rep_grasa=0;
			$aux_rep_aceite=0;
			$aux_rep_manteca=0;
			$aux_rep_mantequilla=0;
			$aux_rep_mar_oj=0;
			$aux_rep_mar_re=0;
			$aux_rep_huevo=0;
	
			$total_harina_biz=0;
			$total_harina_rep=0;
			$total_harina_biz1=0;
			$total_harina_rep1=0;

			$total_azucar_biz=0;
			$total_azucar_rep=0;
			$total_grasa_biz=0;
			$total_grasa_rep=0;
			$total_huevo_rep=0;
			$total_huevo_biz=0;
			
			
			$total_porc_azucar_biz=0;
			$total_porc_azucar_rep=0;
			$total_porc_grasa_biz=0;
			$total_porc_grasa_rep=0;
			$total_porc_huevo_biz=0;
			$total_porc_huevo_rep=0;

		for($i=0;$i<$dia_1[0];$i++){
			$harina_biz=0;
			$azucar1_biz=0;
			$azucar2_biz=0;
			$grasa_biz=0;
			$aceite_biz=0;
			$mantequilla_biz=0;
			$manteca_biz=0;
			$mar_oj_biz=0;
			$mar_re_biz=0;
			$huevo_biz=0;

			$harina_rep=0;
			$azucar1_rep=0;
			$azucar2_rep=0;
			$grasa_rep=0;
			$aceite_rep=0;
			$mantequilla_rep=0;
			$manteca_rep=0;
			$mar_oj_rep=0;
			$mar_re_rep=0;
			$huevo_rep=0;



			$tpl->newBlock("rows1");
			$dia=$i+1;
			$tpl->assign("dia",$dia);
			$fecha_armada=$dia."/".$dia_1[1]."/".$dia_1[2];
//******************************************************************** DATOS DEL BIZCOCHERO
//			echo "dia ".$dia."<br>";
			if($bizcochero==true){
			
				if($biz_harina){
//					if($i<count($biz_harina)){
						$dia_harina=explode("/",@$biz_harina[$aux_biz_harina]['fecha']);
						if($dia==number_format(floatval($dia_harina[0]))){
							$harina_biz=$biz_harina[$aux_biz_harina]['cantidad'];
							$aux_biz_harina++;
						}
						else{
							$harina_biz=0;
						}
//					}
//					else $harina_biz=0;
				}
				else $harina_biz=0;
				
				if($biz_azucar1){
//					if($i<=count($biz_azucar1)){
						$dia_azucar1=explode("/",@$biz_azucar1[$aux_biz_azucar1]['fecha']);
//						echo "dia_azucar1 ".$dia_azucar1[0]."<br>";
						if($dia==number_format(floatval($dia_azucar1[0])))
						{
							$azucar1_biz=$biz_azucar1[$aux_biz_azucar1]['cantidad'];
							$aux_biz_azucar1++;
						}
						else{
							$azucar1_biz=0;
						}
//					}
//					else $azucar1_biz=0;
				}
				else $azucar1_biz=0;

				if($biz_azucar2){
//					if($i<=count($biz_azucar2)){
						$dia_azucar2=explode("/",@$biz_azucar2[$aux_biz_azucar2]['fecha']);
//						echo "dia azucar2 ".$dia_azucar2[0]."<br>";
						if($dia==number_format(floatval($dia_azucar2[0]))){
							$azucar2_biz=$biz_azucar2[$aux_biz_azucar2]['cantidad'];
							$aux_biz_azucar2++;
						}
						else{
							$azucar2_biz=0;
						}
//					}
//					else $azucar2_biz=0;
				}
				else $azucar2_biz=0;

				if($biz_grasa){
//					if($i<count($biz_grasa)){
						$dia_grasa=explode("/",@$biz_grasa[$aux_biz_grasa]['fecha']);
						if($dia==number_format(floatval($dia_grasa[0]))){
							$grasa_biz=$biz_grasa[$aux_biz_grasa]['cantidad'];
							$aux_biz_grasa++;
						}
						else{
							$grasa_biz=0;
						}
//					}
//					else $grasa_biz=0;
				}
				else $grasa_biz=0;
				
				if($biz_aceite){
//					if($i<count($biz_aceite)){
						$dia_aceite=explode("/",@$biz_aceite[$aux_biz_aceite]['fecha']);
						if($dia==number_format(floatval($dia_aceite[0]))){
							$aceite_biz=$biz_aceite[$aux_biz_aceite]['cantidad'];
							$aux_biz_aceite++;
						}
						else{
							$aceite_biz=0;
						}
//					}
//					else $aceite_biz=0;
				}
				else $aceite_biz=0;
				
				if($biz_manteca){
//					if($i<count($biz_manteca)){
						$dia_manteca=explode("/",@$biz_manteca[$aux_biz_manteca]['fecha']);
						if($dia==number_format(floatval($dia_manteca[0]))){
							$manteca_biz=$biz_manteca[$aux_biz_manteca]['cantidad'];
							$aux_biz_manteca++;
						}
						else{
							$manteca_biz=0;
						}
//					}
//					else $manteca_biz=0;
				}
				else $manteca_biz=0;
				
				if($biz_mantequilla){
//					if($i<count($biz_mantequilla)){
						$dia_mantequilla=explode("/",@$biz_mantequilla[$aux_biz_mantequilla]['fecha']);
						if($dia==number_format(floatval($dia_mantequilla[0]))){
							$mantequilla_biz=$biz_mantequilla[$aux_biz_mantequilla]['cantidad'];
							$aux_biz_mantequilla++;
						}
						else{
							$mantequilla_biz=0;
						}
//					}
//					else $mantequilla_biz=0;
				}
				else $mantequilla_biz=0;
				
				if($biz_margarina_oj){
//					if($i<count($biz_margarina_oj)){
						$dia_mar_oj=explode("/",@$biz_margarina_oj[$aux_biz_mar_oj]['fecha']);
						if($dia==number_format(floatval($dia_mar_oj[0]))){
							$mar_oj_biz=$biz_margarina_oj[$aux_biz_mar_oj]['cantidad'];
							$aux_biz_mar_oj++;
						}
						else{
							$mar_oj_biz=0;
						}
//					}
//					else $mar_oj_biz=0;
				}
				else $mar_oj_biz=0;
				
				if($biz_margarina_re){
//					if($i<count($biz_margarina_re)){
						$dia_mar_re=explode("/",@$biz_margarina_re[$aux_biz_mar_re]['fecha']);
						if($dia==number_format(floatval($dia_mar_re[0]))){
							$mar_re_biz=$biz_margarina_re[$aux_biz_mar_re]['cantidad'];
							$aux_biz_mar_re++;
						}
						else{
							$mar_re_biz=0;
						}
//					}
//					else $mar_re_biz=0;
				}
				else $mar_re_biz=0;
				
				if($biz_huevo){
//					if($i<count($biz_huevo)){
						$dia_huevo=explode("/",@$biz_huevo[$aux_biz_huevo]['fecha']);
						if($dia==number_format(floatval($dia_huevo[0]))){
							$huevo_biz=$biz_huevo[$aux_biz_huevo]['cantidad'];
							$aux_biz_huevo++;
						}
						else{
							$huevo_biz=0;
						}
//					}
//					else $huevo_biz=0;
				}
				else $huevo_biz=0;
				
			}
			else{
				$harina_biz=0;
				$azucar1_biz=0;
				$azucar2_biz=0;
				$grasa_biz=0;
				$aceite_biz=0;
				$mantequilla_biz=0;
				$manteca_biz=0;
				$mar_oj_biz=0;
				$mar_re_biz=0;
				$huevo_biz=0;
			}
//************************************************************************ DATOS DEL REPOSTERO
//			echo "dia $dia <br>";
			if($repostero==true){
				
				if($rep_harina){
//					if($i<count($rep_harina)){
						$dia_harina=explode("/",@$rep_harina[$aux_rep_harina]['fecha']);
						if($dia==number_format(floatval($dia_harina[0]))){
							$harina_rep=$rep_harina[$aux_rep_harina]['cantidad'];
							$aux_rep_harina++;
						}
						else{
							$harina_rep=0;
						}
//					}
//					else $harina_rep=0;
				}
				else $harina_rep=0;
				
				if($rep_azucar1){
//					if($i<=count($rep_azucar1)){
						$dia_azucar1=explode("/",@$rep_azucar1[$aux_rep_azucar1]['fecha']);
//						echo "azucar 1 $dia_azucar1[0] <br>";
						if($dia==number_format(floatval($dia_azucar1[0])))
						{
							$azucar1_rep=$rep_azucar1[$aux_rep_azucar1]['cantidad'];
							$aux_rep_azucar1++;
						}
						else{
							$azucar1_rep=0;
						}
//					}
//					else $azucar1_rep=0;
				}
				else $azucar1_rep=0;
				
				if($rep_azucar2){
//					if($i<=count($rep_azucar2)){
						$dia_azucar2=explode("/",@$rep_azucar2[$aux_rep_azucar2]['fecha']);
//						echo "azucar 2 $dia_azucar2[0] <br>";
						if($dia==number_format(floatval($dia_azucar2[0]))){
							$azucar2_rep=$rep_azucar2[$aux_rep_azucar2]['cantidad'];
							$aux_rep_azucar2++;
						}
						else{
							$azucar2_rep=0;
						}
//					}
//					else $azucar2_rep=0;
				}
				else $azucar2_rep=0;
				
				if($rep_grasa){
//					if($i<count($rep_grasa)){
						$dia_grasa=explode("/",@$rep_grasa[$aux_rep_grasa]['fecha']);
						if($dia==number_format(floatval($dia_grasa[0]))){
							$grasa_rep=$rep_grasa[$aux_rep_grasa]['cantidad'];
							$aux_rep_grasa++;
						}
						else{
							$grasa_rep=0;
						}
//					}
//					else $grasa_rep=0;
				}
				else $grasa_rep=0;
				
				if($rep_aceite){
//					if($i<count($rep_aceite)){
						$dia_aceite=explode("/",@$rep_aceite[$aux_rep_aceite]['fecha']);
						if($dia==number_format(floatval($dia_aceite[0]))){
							$aceite_rep=$rep_aceite[$aux_rep_aceite]['cantidad'];
							$aux_rep_aceite++;
						}
						else{
							$aceite_rep=0;
						}
//					}
//					else $aceite_rep=0;
				}
				else $aceite_rep=0;
				
				if($rep_manteca){
//					if($i<count($rep_manteca)){
						$dia_manteca=explode("/",@$rep_manteca[$aux_rep_manteca]['fecha']);
						if($dia==number_format(floatval($dia_manteca[0]))){
							$manteca_rep=$rep_manteca[$aux_rep_manteca]['cantidad'];
							$aux_rep_manteca++;
						}
						else{
							$manteca_rep=0;
						}
//					}
//					else $manteca_rep=0;
				}
				else $manteca_rep=0;
				
				if($rep_mantequilla){
//					if($i<count($rep_mantequilla)){
						$dia_mantequilla=explode("/",@$rep_mantequilla[$aux_rep_mantequilla]['fecha']);
						if($dia==number_format(floatval($dia_mantequilla[0]))){
							$mantequilla_rep=$rep_mantequilla[$aux_rep_mantequilla]['cantidad'];
							$aux_rep_mantequilla++;
						}
						else{
							$mantequilla_rep=0;
						}
//					}
//					else $mantequilla_rep=0;
				}
				else $mantequilla_rep=0;
				
				if($rep_margarina_oj){
//					if($i<count($rep_margarina_oj)){
						$dia_mar_oj=explode("/",@$rep_margarina_oj[$aux_rep_mar_oj]['fecha']);
						if($dia==number_format(floatval($dia_mar_oj[0]))){
							$mar_oj_rep=$rep_margarina_oj[$aux_rep_mar_oj]['cantidad'];
							$aux_rep_mar_oj++;
						}
						else{
							$mar_oj_rep=0;
						}
//					}
//					else $mar_oj_rep=0;
				}
				else $mar_oj_rep=0;
				
				if($rep_margarina_re){
//					if($i<count($rep_margarina_re)){
						$dia_mar_re=explode("/",@$rep_margarina_re[$aux_rep_mar_re]['fecha']);
						if($dia==number_format(floatval($dia_mar_re[0]))){
							$mar_re_rep=$rep_margarina_re[$aux_rep_mar_re]['cantidad'];
							$aux_rep_mar_re++;
						}
						else{
							$mar_re_rep=0;
						}
//					}
//					else $mar_re_rep=0;
				}
				else $mar_re_rep=0;
				
				if($rep_huevo){
//					if($i<count($rep_huevo)){
						$dia_huevo=explode("/",@$rep_huevo[$aux_rep_huevo]['fecha']);
						if($dia==number_format(floatval($dia_huevo[0]))){
							$huevo_rep=$rep_huevo[$aux_rep_huevo]['cantidad'];
							$aux_rep_huevo++;
						}
						else{
							$huevo_rep=0;
						}
//					}
//					else $huevo_rep=0;
				}
				else $huevo_rep=0;
			}
			else{
				$harina_rep=0;
				$azucar1_rep=0;
				$azucar2_rep=0;
				$grasa_rep=0;
				$aceite_rep=0;
				$mantequilla_rep=0;
				$manteca_rep=0;
				$mar_oj_rep=0;
				$mar_re_rep=0;
				$huevo_rep=0;
			}
			
			$harina_rep1 = $harina_rep / $relacion;
			$azucar_rep = $azucar1_rep + $azucar2_rep;
//			echo "suma azucar $azucar_rep <br>";
			$grasas_rep = $grasa_rep/* + $aceite_rep + $manteca_rep + $mantequilla_rep + $mar_oj_rep + $mar_re_rep*/;
			if($harina_rep > 0){
				$por_azucar_rep= $azucar_rep / $harina_rep;
				$por_grasas_rep = $grasas_rep / $harina_rep;
				$por_huevo_rep = $huevo_rep / $harina_rep;
			}
			else{
				$por_azucar_rep= 0;
				$por_grasas_rep = 0;
				$por_huevo_rep = 0;
			}
			
			$harina_biz1 = $harina_biz / $relacion;
			$azucar_biz = $azucar1_biz + $azucar2_biz;
//			echo "suma azucar ".$azucar_biz."<br>";
			$grasas_biz = $grasa_biz/* + $aceite_biz + $manteca_biz + $mantequilla_biz + $mar_oj_biz + $mar_re_biz*/;
			if($harina_biz > 0){
				$por_azucar_biz= $azucar_biz / $harina_biz;
				$por_grasas_biz = $grasas_biz / $harina_biz;
				$por_huevo_biz = $huevo_biz / $harina_biz;
			}
			else{
				$por_azucar_biz= 0;
				$por_grasas_biz = 0;
				$por_huevo_biz = 0;
			}
			
			// TOTAL DE HARINA EN KILOS
			$total_harina_biz += $harina_biz;
			$total_harina_rep += $harina_rep;
			
			// TOTAL DE HARINA EN BULTOS
			$total_harina_biz1 += $harina_biz1;
			$total_harina_rep1 += $harina_rep1;
			
			$total_azucar_biz += $azucar_biz;
			$total_azucar_rep += $azucar_rep;
			
			$total_grasa_biz += $grasas_biz;
			$total_grasa_rep += $grasas_rep;

			$total_huevo_rep += $huevo_rep;
			$total_huevo_biz += $huevo_biz;

			if($harina_rep==0) $tpl->assign("harina2","");
			else
				$tpl->assign("harina2",number_format($harina_rep1,2,'.',','));
				
			if($azucar_rep==0) $tpl->assign("azucar2","");
			else
				$tpl->assign("azucar2",number_format($azucar_rep,2,'.',','));
				
			if($por_azucar_rep==0)$tpl->assign("porc_azucar2","");
			else
				$tpl->assign("porc_azucar2",number_format($por_azucar_rep,3,'.',','));
				
			if($grasas_rep==0) $tpl->assign("grasas2","");
			else
				$tpl->assign("grasas2",number_format($grasas_rep,2,'.',','));
				
			if($por_grasas_rep==0) $tpl->assign("porc_grasas2","");
			else
				$tpl->assign("porc_grasas2",number_format($por_grasas_rep,3,'.',','));
				
			if($huevo_rep==0) $tpl->assign("huevo2","");
			else
				$tpl->assign("huevo2",number_format($huevo_rep,2,'.',','));
				
			if($por_huevo_rep==0) $tpl->assign("porc_huevo2","");
			else
				$tpl->assign("porc_huevo2",number_format($por_huevo_rep,3,'.',','));


			if($harina_biz==0) $tpl->assign("harina1","");
			else
				$tpl->assign("harina1",number_format($harina_biz1,2,'.',','));
				
			if($azucar_biz==0) $tpl->assign("azucar1","");
			else
				$tpl->assign("azucar1",number_format($azucar_biz,2,'.',','));
				
			if($por_azucar_biz==0)$tpl->assign("porc_azucar1","");
			else
				$tpl->assign("porc_azucar1",number_format($por_azucar_biz,3,'.',','));
				
			if($grasas_biz==0) $tpl->assign("grasas1","");
			else
				$tpl->assign("grasas1",number_format($grasas_biz,2,'.',','));
				
			if($por_grasas_biz==0) $tpl->assign("porc_grasas1","");
			else
				$tpl->assign("porc_grasas1",number_format($por_grasas_biz,3,'.',','));
				
			if($huevo_biz==0) $tpl->assign("huevo1","");
			else
				$tpl->assign("huevo1",number_format($huevo_biz,2,'.',','));
				
			if($por_huevo_biz==0) $tpl->assign("porc_huevo1","");
			else
				$tpl->assign("porc_huevo1",number_format($por_huevo_biz,3,'.',','));
		}
		
		if($total_harina_rep > 0){
			$total_porc_azucar_rep=$total_azucar_rep/$total_harina_rep;
			$total_porc_huevo_rep=$total_huevo_rep/$total_harina_rep;
			$total_porc_grasa_rep=$total_grasa_rep/$total_harina_rep;		
		}
		
		if($total_harina_biz > 0){
			$total_porc_azucar_biz=$total_azucar_biz/$total_harina_biz;
			$total_porc_huevo_biz=$total_huevo_biz/$total_harina_biz;
			$total_porc_grasa_biz=$total_grasa_biz/$total_harina_biz;		
		}
		$tpl->gotoBlock("compania2");
		$tpl->assign("total_harina1",number_format($total_harina_biz1,2,'.',','));
		$tpl->assign("total_azucar1",number_format($total_azucar_biz,2,'.',','));
		$tpl->assign("total_grasas1",number_format($total_grasa_biz,2,'.',','));
		$tpl->assign("total_huevo1",number_format($total_huevo_biz,2,'.',','));		

		$tpl->assign("total_harina2",number_format($total_harina_rep1,2,'.',','));
		$tpl->assign("total_azucar2",number_format($total_azucar_rep,2,'.',','));
		$tpl->assign("total_huevo2",number_format($total_huevo_rep,2,'.',','));
		$tpl->assign("total_grasas2",number_format($total_grasa_rep,2,'.',','));
		
		$tpl->assign("total_porc_azucar1",number_format($total_porc_azucar_biz,2,'.',','));
		$tpl->assign("total_porc_grasas1",number_format($total_porc_grasa_biz,2,'.',','));
		$tpl->assign("total_porc_huevo1",number_format($total_porc_huevo_biz,2,'.',','));

		$tpl->assign("total_porc_azucar2",number_format($total_porc_azucar_rep,2,'.',','));
		$tpl->assign("total_porc_grasas2",number_format($total_porc_grasa_rep,2,'.',','));
		$tpl->assign("total_porc_huevo2",number_format($total_porc_huevo_rep,2,'.',','));

		if($salto1 % 2 == 0)
			$tpl->newBlock("salto1");
		$salto1++;
	}

//*************
}
$tpl->printToScreen();
// --------------------------------------------------------------------------------------------------------
?>