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
$descripcion_error[1] = "No hay limites de consumo de avio para la panaderia";
$descripcion_error[1] = "No tienes asignadas panaderías";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/pan/pan_max_con.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['fecha'])) {
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
//$tpl->newBlock("maximos");
//ARROJA EL NUMERO DE ITERACIONES DENTRO DEL FOR A PARTIR DEL RANGO DE FECHAS
//$fecha_inicio='1/'.date("m").'/'.date("Y");

//**************************************************************************
//NUEVA FUNCION
function grasas($cia,$turno,$fecha,$dsn){
//	echo "compañia $cia <br>";
//	echo "turno $turno <br>";
//	echo "fecha $fecha <br>";
	
	$sql="select sum(cantidad) as kilos from mov_inv_real where num_cia=".$cia." and cod_turno=".$turno." and codmp=38 and fecha between '".$fecha."' and '".$_GET['fecha']."' and tipo_mov=true";
	$grasa=ejecutar_script($sql,$dsn);

	$sql="select sum(cantidad) as kilos from mov_inv_real where num_cia=".$cia." and cod_turno=".$turno." and codmp=86 and fecha between '".$fecha."' and '".$_GET['fecha']."' and tipo_mov=true";
	$aceite=ejecutar_script($sql,$dsn);

	$sql="select sum(cantidad) as kilos from mov_inv_real where num_cia=".$cia." and cod_turno=".$turno." and codmp=49 and fecha between '".$fecha."' and '".$_GET['fecha']."' and tipo_mov=true";
	$manteca=ejecutar_script($sql,$dsn);

	$sql="select sum(cantidad) as kilos from mov_inv_real where num_cia=".$cia." and cod_turno=".$turno." and codmp=44 and fecha between '".$fecha."' and '".$_GET['fecha']."' and tipo_mov=true";
	$mantequilla=ejecutar_script($sql,$dsn);

	$sql="select sum(cantidad) as kilos from mov_inv_real where num_cia=".$cia." and cod_turno=".$turno." and codmp=45 and fecha between '".$fecha."' and '".$_GET['fecha']."' and tipo_mov=true";
	$margarina_oj=ejecutar_script($sql,$dsn);

	$sql="select sum(cantidad) as kilos from mov_inv_real where num_cia=".$cia." and cod_turno=".$turno." and codmp=47 and fecha between '".$fecha."' and '".$_GET['fecha']."' and tipo_mov=true";
	$margarina_re=ejecutar_script($sql,$dsn);

	$sql="select sum(cantidad) as kilos from mov_inv_real where num_cia=".$cia." and cod_turno=".$turno." and codmp=1087 and fecha between '".$fecha."' and '".$_GET['fecha']."' and tipo_mov=true";
	$grasa_palma_pro=ejecutar_script($sql,$dsn);

	$sql="select sum(cantidad) as kilos from mov_inv_real where num_cia=".$cia." and cod_turno=".$turno." and codmp=1088 and fecha between '".$fecha."' and '".$_GET['fecha']."' and tipo_mov=true";
	$grasa_palma_don=ejecutar_script($sql,$dsn);

/*
	echo "grasa ".$grasa[0]['kilos']." <br>";
	echo "aceite ".$aceite[0]['kilos']." <br>";
	echo "mantenca ".$manteca[0]['kilos']." <br>";
	echo "mantequilla ".$mantequilla[0]['kilos']." <br>";
	echo "margarina ojaldre ".$margarina_oj[0]['kilos']." <br>";
	echo "margarina real ".$margarina_re[0]['kilos']." <br>";
*/	
	
	@$total=number_format($grasa[0]['kilos'],2,'.','') + number_format($aceite[0]['kilos'],2,'.','') + number_format($manteca[0]['kilos'],2,'.','') + number_format($mantequilla[0]['kilos'],2,'.','') + number_format($margarina_oj[0]['kilos'],2,'.','') + number_format($margarina_re[0]['kilos'],2,'.','') + number_format($grasa_palma_pro[0]['kilos'],2,'.','') + number_format($grasa_palma_don[0]['kilos'],2,'.','');
//	echo "total $total <br>";
//	$total=1;
	return ($total);
}
//**************************************************************************

$fech=explode("/",$_GET['fecha']);
$fecha_inicio='1/'.$fech[1].'/'.$fech[2];
$relacion_harina=44;

if($_GET['consulta']==0){
	$cias=obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
}
else{
	if($_SESSION['iduser']==1 or $_SESSION['iduser']==4 or $_SESSION['iduser']==42 or $_SESSION['iduser']==57 or $_SESSION['iduser']==62){
		$sql="select num_cia, nombre_corto from catalogo_companias where num_cia <= 300 order by num_cia";
		$cias=ejecutar_script($sql,$dsn);
	}
	else{
		$sql="select * from catalogo_operadoras where iduser=".$_SESSION['iduser'];
		$operadora=ejecutar_script($sql,$dsn);
		if(!$operadora){
			header("location: ./pan_max_con.php?codigo_error=2");
			die();
		}
		$sql="select num_cia,nombre_corto from catalogo_companias where idoperadora =".$operadora[0]['idoperadora']." order by num_cia";
		$cias=ejecutar_script($sql,$dsn);
	}
}
$salto=1;
for($i=0;$i<count($cias);$i++){
	
	$sql="select catalogo_avio_autorizado.*, catalogo_mat_primas.nombre from catalogo_avio_autorizado join catalogo_mat_primas using(codmp) where num_cia=".$cias[$i]['num_cia']." order by codmp";
	$limites=ejecutar_script($sql,$dsn);
	
	if(!$limites){
		if($_GET['consulta']==0){
			header("location: ./pan_max_con.php?codigo_error=2");
			die();
		}
		else
			continue;
	}
	
	$tpl->newBlock("maximos");
	$tpl->assign("dia",$fech[0]);
	$tpl->assign("mes",mes_escrito(number_format(floatval($fech[1])),true));
	$tpl->assign("anio",$fech[2]);
	$tpl->assign("num_cia",$cias[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cias[$i]['nombre_corto']);

	for($j=0;$j<count($limites);$j++){
		$tpl->newBlock("rows");
		$tpl->assign("codmp",$limites[$j]['codmp']);
		$tpl->assign("nombre",$limites[$j]['nombre']);
		
//	INSERTAR LOS LIMITES DE LA MATERIA PRIMA
		if($limites[$j]['frances_dia']==0)
			$tpl->assign("fd_aut","");
		else{
			$tpl->assign("fd_aut",number_format($limites[$j]['frances_dia'],3,'.',''));
			$sql="select sum(cantidad)/".$relacion_harina." as bultos from mov_inv_real where num_cia=".$cias[$i]['num_cia']." and cod_turno=1 and codmp=1 and fecha between '".$fecha_inicio."' and '".$_GET['fecha']."' and tipo_mov=true";
			$harina_dia=ejecutar_script($sql,$dsn);
			
			$sql="select sum(cantidad) as kilos from mov_inv_real where num_cia=".$cias[$i]['num_cia']." and cod_turno=1 and codmp=".$limites[$j]['codmp']." and fecha between '".$fecha_inicio."' and '".$_GET['fecha']."' and tipo_mov=true";
			$mp_dia=ejecutar_script($sql,$dsn);
			
			$consumo_fd=0;
			if($harina_dia)
				@$consumo_fd=$mp_dia[0]['kilos'] / $harina_dia[0]['bultos'];
			
			if($consumo_fd > 0){
				$tpl->assign("fd_con",number_format($consumo_fd,3,'.',','));
				$diferencia_fd = number_format($consumo_fd,3,'.','') - number_format($limites[$j]['frances_dia'],3,'.','');
				if($diferencia_fd > 0)
					$tpl->assign("fd_mas",number_format($diferencia_fd,3,'.',''));
			}
			else
				$tpl->assign("fd_con","");
		}
		
		if($limites[$j]['frances_noche']==0)
			$tpl->assign("fn_aut","");
		else{
			$tpl->assign("fn_aut",number_format($limites[$j]['frances_noche'],3,'.',''));
			
			$sql="select sum(cantidad)/".$relacion_harina." as bultos from mov_inv_real where num_cia=".$cias[$i]['num_cia']." and cod_turno=2 and codmp=1 and fecha between '".$fecha_inicio."' and '".$_GET['fecha']."' and tipo_mov=true";
			$harina_noche=ejecutar_script($sql,$dsn);
//			print_r($harina_noche);
			$sql="select sum(cantidad) as kilos from mov_inv_real where num_cia=".$cias[$i]['num_cia']." and cod_turno=2 and codmp=".$limites[$j]['codmp']." and fecha between '".$fecha_inicio."' and '".$_GET['fecha']."' and tipo_mov=true";
			$mp_noche=ejecutar_script($sql,$dsn);

			$consumo_fn=0;
			if($harina_noche)
				@$consumo_fn=$mp_noche[0]['kilos'] / $harina_noche[0]['bultos'];
			
			if($consumo_fn > 0){
				$tpl->assign("fn_con",number_format($consumo_fn,3,'.',','));

				$diferencia_fn = number_format($consumo_fn,3,'.','') - number_format($limites[$j]['frances_noche'],3,'.','');
				if($diferencia_fn > 0)
					$tpl->assign("fn_mas",number_format($diferencia_fn,3,'.',''));
			}
			else
				$tpl->assign("fn_con","");

		}

		if($limites[$j]['bizcochero']==0)
			$tpl->assign("biz_aut","");
		else{
			$tpl->assign("biz_aut",number_format($limites[$j]['bizcochero'],3,'.',''));
			
			$sql="select sum(cantidad) as kilos from mov_inv_real where num_cia=".$cias[$i]['num_cia']." and cod_turno=3 and codmp=1 and fecha between '".$fecha_inicio."' and '".$_GET['fecha']."' and tipo_mov=true";
			$harina_biz=ejecutar_script($sql,$dsn);

			$consumo_biz=0;
//**********************************************************
//NUEVO BLOQUE
			if($limites[$j]['codmp']==38){
				$mp_biz=grasas($cias[$i]['num_cia'],3,$fecha_inicio,$dsn);
				if($harina_biz)
					@$consumo_biz=$mp_biz / $harina_biz[0]['kilos'];

			}
			else{
				$sql="select sum(cantidad) as kilos from mov_inv_real where num_cia=".$cias[$i]['num_cia']." and cod_turno=3 and codmp=".$limites[$j]['codmp']." and fecha between '".$fecha_inicio."' and '".$_GET['fecha']."' and tipo_mov=true";
				$mp_biz=ejecutar_script($sql,$dsn);
				if($harina_biz)
					@$consumo_biz=$mp_biz[0]['kilos'] / $harina_biz[0]['kilos'];

			}
//**********************************************************

			if($consumo_biz > 0){
				$tpl->assign("biz_con",number_format($consumo_biz,3,'.',','));
				$diferencia_biz = number_format($consumo_biz,3,'.','') - number_format($limites[$j]['bizcochero'],3,'.','');
				if($diferencia_biz > 0)
					$tpl->assign("biz_mas",number_format($diferencia_biz,3,'.',''));
			}
			else
				$tpl->assign("biz_con","");

		}

		if($limites[$j]['repostero']==0)
			$tpl->assign("rep_aut","");
		else{
			$tpl->assign("rep_aut",number_format($limites[$j]['repostero'],3,'.',''));
			
			$sql="select sum(cantidad) as kilos from mov_inv_real where num_cia=".$cias[$i]['num_cia']." and cod_turno=4 and codmp=1 and fecha between '".$fecha_inicio."' and '".$_GET['fecha']."' and tipo_mov=true";
			$harina_rep=ejecutar_script($sql,$dsn);
			$consumo_rep=0;
			
//**********************************************************
//NUEVO BLOQUE
			if($limites[$j]['codmp']==38){
				$mp_rep=grasas($cias[$i]['num_cia'],4,$fecha_inicio,$dsn);
				if($harina_rep)
					@$consumo_rep=$mp_rep / $harina_rep[0]['kilos'];

			}
			else{
				$sql="select sum(cantidad) as kilos from mov_inv_real where num_cia=".$cias[$i]['num_cia']." and cod_turno=4 and codmp=".$limites[$j]['codmp']." and fecha between '".$fecha_inicio."' and '".$_GET['fecha']."' and tipo_mov=true";
				$mp_rep=ejecutar_script($sql,$dsn);
				if($harina_rep)
					@$consumo_rep=$mp_rep[0]['kilos'] / $harina_rep[0]['kilos'];

			}
//**********************************************************

			
			if($consumo_rep > 0){
				$tpl->assign("rep_con",number_format($consumo_rep,3,'.',','));
				$diferencia_rep = number_format($consumo_rep,3,'.','') - number_format($limites[$j]['repostero'],3,'.','');
				if($diferencia_rep > 0)
					$tpl->assign("rep_mas",number_format($diferencia_rep,3,'.',''));
				
			}
			else
				$tpl->assign("rep_con","");
		}
		
		if($limites[$j]['piconero']==0)
			$tpl->assign("pic_aut","");
		else{
			$tpl->assign("pic_aut",number_format($limites[$j]['piconero'],3,'.',''));
			
			$sql="select sum(cantidad) as kilos from mov_inv_real where num_cia=".$cias[$i]['num_cia']." and cod_turno=8 and codmp=1 and fecha between '".$fecha_inicio."' and '".$_GET['fecha']."' and tipo_mov=true";
			$harina_pic=ejecutar_script($sql,$dsn);
			$consumo_pic=0;
						
//**********************************************************
//NUEVO BLOQUE
			if($limites[$j]['codmp']==38){
				$mp_pic=grasas($cias[$i]['num_cia'],8,$fecha_inicio,$dsn);
				if($harina_pic)
					@$consumo_pic=$mp_pic / $harina_pic[0]['kilos'];

			}
			else{
				$sql="select sum(cantidad) as kilos from mov_inv_real where num_cia=".$cias[$i]['num_cia']." and cod_turno=8 and codmp=".$limites[$j]['codmp']." and fecha between '".$fecha_inicio."' and '".$_GET['fecha']."' and tipo_mov=true";
				$mp_pic=ejecutar_script($sql,$dsn);
				if($harina_pic)
					@$consumo_pic=$mp_pic[0]['kilos'] / $harina_pic[0]['kilos'];

			}
//**********************************************************

			
			if($consumo_pic > 0){
				$tpl->assign("pic_con",number_format($consumo_pic,3,'.',','));
				$diferencia_pic = number_format($consumo_pic,3,'.','') - number_format($limites[$j]['piconero'],3,'.','');
				if($diferencia_pic > 0)
					$tpl->assign("pic_mas",number_format($diferencia_pic,3,'.',''));
			}
			else
				$tpl->assign("pic_con","");
		}
	}
	if($salto % 2 == 0)
		$tpl->newBlock("salto");
	$salto++;
}

$tpl->printToScreen();
// --------------------------------------------------------------------------------------------------------
?>