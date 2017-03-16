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
$tpl->assignInclude("body","./plantillas/ped/ped_inv_con.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_datos");
	
	for($i=0;$i<9;$i++){
		$tpl->newBlock("codmp");
		$tpl->assign("i",$i);
		$tpl->assign("next",$i+1);
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

$fecha_dia_ultimo=date("d/n/Y",mktime(0,0,0,date("n"),0,date("Y")));
$_fecha=explode("/",$fecha_dia_ultimo);
//******************************************************************************************LISTADO PARA LOS INVENTARIO DE FIN DE MES
if($_GET['tipo_inv']==0){
//LISTADO PARA UNA SOLA COMPAÑÍA Y TODA LA MATERIA PRIMA QUE MANEJE
	if($_GET['tipo_con'] == 0)
	{
		$tpl->newBlock("cia_fin_mes");
		$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
		$tpl->assign("num_cia",$_GET['num_cia']);
		$tpl->assign("nom_cia",$cia[0]['nombre_corto']);
		$tpl->assign("mes",$nombremes[$_fecha[1]]);
		$tpl->assign("anio",$_fecha[2]);
	
		$sql="select * from inventario_fin_mes where num_cia=".$_GET['num_cia']." AND fecha = '$fecha_dia_ultimo' order by codmp";
		$inventario=ejecutar_script($sql,$dsn);
	
		if(!$inventario)
		{
			header("location: ./ped_inv_con.php?codigo_error=1");
			die();
		}		
		for($i=0;$i<count($inventario);$i+=2)
		{
			$tpl->newBlock("rows_cia_fin_mes");
			$nom=obtener_registro("catalogo_mat_primas",array("codmp"),array($inventario[$i]["codmp"]),"","",$dsn);
			$tpl->assign("codmp",$inventario[$i]["codmp"]);
			$tpl->assign("nom_mp",$nom[0]['nombre']);
			$tpl->assign("existencia",number_format($inventario[$i]['inventario'],2,'.',','));
			if(($i+1)==count($inventario)) break;
			$nom1=obtener_registro("catalogo_mat_primas",array("codmp"),array($inventario[$i+1]["codmp"]),"","",$dsn);
			$tpl->assign("codmp1",$inventario[$i+1]["codmp"]);
			$tpl->assign("nom_mp1",$nom1[0]['nombre']);
			$tpl->assign("existencia1",number_format($inventario[$i+1]['inventario'],2,'.',','));
		}
		$tpl->printToScreen();
		die();
	}
	
	//LISTADO PARA TODAS LAS COMPAÑIAS
	elseif($_GET['tipo_con'] == 1)
	{
		//LISTADO PARA TODAS LAS COMPAÑÍAS REVISANDO LAS MATERIAS PRIMAS QUE SE SOLICITARON
		if($_GET['mat_prima'] == 0){
			$tpl->newBlock("todos_fin_mes");
//			$tpl->assign("dia",date("d"));
			$tpl->assign("mes",$nombremes[$_fecha[1]]);
			$tpl->assign("anio",$_fecha[2]);
			$sql="select num_cia,nombre_corto from catalogo_companias where num_cia <= 300 order by num_cia";
			$cias=ejecutar_script($sql,$dsn);
			for($i=0;$i<9;$i++){
				if($_GET['codmp'.$i]!=""){
					if(existe_registro("catalogo_mat_primas",array("codmp"),array($_GET["codmp".$i]),$dsn)){
						$tpl->newBlock("mat_prima_mes");
						$tpl->assign("codmp",$_GET['codmp'.$i]);
						$nombre=obtener_registro("catalogo_mat_primas",array("codmp"),array($_GET['codmp'.$i]),"","",$dsn);
						$tpl->assign("nom_mp",$nombre[0]['nombre']);
					}
				}
			}
			for($j=0;$j<count($cias);$j++)
			{
				$tpl->newBlock("rows_todos_mes");
				$tpl->assign("num_cia",$cias[$j]['num_cia']);
				$tpl->assign("nom_cia",$cias[$j]['nombre_corto']);
				for($i=0;$i<9;$i++){
					if($_GET['codmp'.$i]!=""){
						if(existe_registro("catalogo_mat_primas",array("codmp"),array($_GET["codmp".$i]),$dsn)){
							$sql="select * from inventario_fin_mes where num_cia=".$cias[$j]['num_cia']." and codmp= ".$_GET["codmp".$i]." and fecha='$fecha_dia_ultimo'";
							$mp=ejecutar_script($sql,$dsn);
							$tpl->newBlock("existencia_todos_mes");
							if($mp)
								$tpl->assign("existencia",number_format($mp[0]['inventario'],2,'.',','));
							else
								$tpl->assign("existencia","");
						}
					}
				}
			}
			$tpl->gotoBlock("todos_fin_mes");
		}
	
		elseif($_GET['mat_prima']==1)
		{
			$cias="select num_cia from catalogo_companias where num_cia <= 300 order by num_cia";
			$companias=ejecutar_script($cias,$dsn);
			$mat="select * from catalogo_mat_primas order by codmp";
			$mat_primas=ejecutar_script($mat,$dsn);
			$total=count($mat_primas);
	
			$paginas=$total/10;
			$paginas=ceil($paginas);
			
			$aux1=0;
			$aux2=10;
	
			$sql="select num_cia,codmp,inventario from inventario_fin_mes where num_cia between 1 and 300 AND fecha = '$fecha_dia_ultimo' order by num_cia,codmp";
	
			$mprima=ejecutar_script($sql,$dsn);
			
			for($z=0;$z<$paginas;$z++)
			{	
				$tpl->newBlock("todos_fin_mes");
				$tpl->assign("mes",$nombremes[$_fecha[1]]);
				$tpl->assign("anio",$_fecha[2]);
				
				for($j=$aux1;$j<$aux2;$j++)
				{
					if($j==$total) break;
					$tpl->newBlock("mat_prima_mes");
					$tpl->assign("codmp",$mat_primas[$j]['codmp']);
					$tpl->assign("nom_mp",$mat_primas[$j]['nombre']);
				}
				for($i=0;$i<count($companias);$i++)
				{
					$tpl->newBlock("rows_todos_mes");
					$tpl->assign("num_cia",$companias[$i]['num_cia']);
					
					for($j=$aux1;$j<$aux2;$j++)
					{
						$var=0;
						if($j==$total) break;
						$tpl->newBlock("existencia_todos_mes");
						if(!$mprima[$i]) $tpl->assign("existencia","");
						else{
							for($a=0;$a<count($mprima);$a++)
							{
								if($var==count($mprima)) $tpl->assign("existencia","");
								else
								{
									$var++;
									if($mat_primas[$j]['codmp'] == $mprima[$a]['codmp'] and $mprima[$a]['num_cia'] == $companias[$i]['num_cia']) 
									{
									$tpl->assign("existencia",number_format($mprima[$a]['existencia'],2,'.',','));
									break;
									}
								}
							}
						}
					}
				}
				$aux1 = $aux2;
				$aux2 += 10;
			}
		}
	}
}

//****************************************************************************************************LISTADO PARA EXISTENCIAS ACTUALES

if($_GET['tipo_inv']==1){
	if($_GET['tipo_con'] == 0)
	{
		$tpl->newBlock("cia_actual");
		$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
		$tpl->assign("num_cia",$_GET['num_cia']);
		$tpl->assign("nom_cia",$cia[0]['nombre_corto']);
		$tpl->assign("dia",date("d"));
		$tpl->assign("mes",$nombremes[date("n")]);
		$tpl->assign("anio",date("Y"));
//cambio solicitado por miriam para revisar el inventario virtual
		$sql="select * from inventario_virtual where num_cia=".$_GET['num_cia']." order by codmp";
		$inventario=ejecutar_script($sql,$dsn);
	
		if(!$inventario)
		{
			header("location: ./ped_inv_con.php?codigo_error=1");
			die();
		}		
		for($i=0;$i<count($inventario);$i+=2)
		{
			$tpl->newBlock("rows_cia_actual");
			$nom=obtener_registro("catalogo_mat_primas",array("codmp"),array($inventario[$i]["codmp"]),"","",$dsn);
			$tpl->assign("codmp",$inventario[$i]["codmp"]);
			$tpl->assign("nom_mp",$nom[0]['nombre']);
			$tpl->assign("existencia",number_format($inventario[$i]['existencia'],2,'.',','));
			if(($i+1)==count($inventario)) break;
			$nom1=obtener_registro("catalogo_mat_primas",array("codmp"),array($inventario[$i+1]["codmp"]),"","",$dsn);
			$tpl->assign("codmp1",$inventario[$i+1]["codmp"]);
			$tpl->assign("nom_mp1",$nom1[0]['nombre']);
			$tpl->assign("existencia1",number_format($inventario[$i+1]['existencia'],2,'.',','));
		}
		$tpl->printToScreen();
		die();
	}
	elseif($_GET['tipo_con'] == 1)
	{
		if($_GET['mat_prima'] == 0){//REVISARA LAS MATERIAS PRIMAS SOLICITADAS
			$tpl->newBlock("todos_actual");
			$tpl->assign("dia",date("d"));
			$tpl->assign("mes",$nombremes[date("n")]);
			$tpl->assign("anio",date("Y"));
			$sql="select num_cia,nombre_corto from catalogo_companias where num_cia <= 300 order by num_cia";
			$cias=ejecutar_script($sql,$dsn);

			for($i=0;$i<9;$i++){
				if($_GET['codmp'.$i]!=""){
					if(existe_registro("catalogo_mat_primas",array("codmp"),array($_GET["codmp".$i]),$dsn)){
						$tpl->newBlock("mat_prima_actual");
						$tpl->assign("codmp",$_GET['codmp'.$i]);
						$nombre=obtener_registro("catalogo_mat_primas",array("codmp"),array($_GET['codmp'.$i]),"","",$dsn);
						$tpl->assign("nom_mp",$nombre[0]['nombre']);
					}
				}
			}
			for($j=0;$j<count($cias);$j++)
			{
				$tpl->newBlock("rows_todos_actual");
				$tpl->assign("num_cia",$cias[$j]['num_cia']);
				$tpl->assign("nom_cia",$cias[$j]['nombre_corto']);
				for($i=0;$i<9;$i++){
					if($_GET['codmp'.$i]!=""){
						if(existe_registro("catalogo_mat_primas",array("codmp"),array($_GET["codmp".$i]),$dsn)){
						//cambio a inventario virtual
							$sql="select * from inventario_virtual where num_cia=".$cias[$j]['num_cia']." and codmp= ".$_GET["codmp".$i];
							$mp=ejecutar_script($sql,$dsn);
							$tpl->newBlock("existencia_todos_actual");
							if($mp)
								$tpl->assign("existencia",number_format($mp[0]['existencia'],2,'.',','));
							else
								$tpl->assign("existencia","");
						}
					}
				}
			}
			$tpl->gotoBlock("todos_actual");
		}
	
		elseif($_GET['mat_prima']==1)//REVISARA TODAS LAS MATERIAS PRIMAS DEL INVENTARIO REAL
		{
			$cias="select num_cia from catalogo_companias where num_cia <= 300 order by num_cia";
			$companias=ejecutar_script($cias,$dsn);
			$mat="select * from catalogo_mat_primas order by codmp";
			$mat_primas=ejecutar_script($mat,$dsn);
			$total=count($mat_primas);
	
			$paginas=$total/10;
			$paginas=ceil($paginas);
			
//			$paginas=17;
			$aux1=0;
			$aux2=10;
			$sql="select catalogo_companias.num_cia, codmp, precio_unidad from catalogo_companias join inventario_virtual on (inventario_virtual.num_cia=catalogo_companias.num_cia and catalogo_companias.num_cia between 1 and 300)
			order by catalogo_companias.num_cia, codmp";
			
			$mprima=ejecutar_script($sql,$dsn);
			
			for($z=0;$z<$paginas;$z++)
			{	
				$tpl->newBlock("todos_actual");
				$tpl->assign("dia",date("d"));
				$tpl->assign("mes",$nombremes[date("n")]);
				$tpl->assign("anio",date("Y"));
				
				for($j=$aux1;$j<$aux2;$j++)
				{
					if($j==$total) break;
					$tpl->newBlock("mat_prima_actual");
					$tpl->assign("codmp",$mat_primas[$j]['codmp']);
					$tpl->assign("nom_mp",$mat_primas[$j]['nombre']);
				}
		
				for($i=0;$i<count($companias);$i++)
				{
					$tpl->newBlock("rows_todos_actual");
					$tpl->assign("num_cia",$companias[$i]['num_cia']);
					//cambio a inventario virtual
					$sql="select codmp, precio_unidad from inventario_virtual where num_cia=".$companias[$i]['num_cia']." and codmp between ".$aux1." and ".$aux2."order by codmp";
					$mprima=ejecutar_script($sql,$dsn);
					
					for($j=$aux1;$j<$aux2;$j++)
					{
						$var=0;
						if($j==$total) break;
						$tpl->newBlock("existencia_todos_actual");
						if(!$mprima[$i]) $tpl->assign("existencia","");
						else{
							for($a=0;$a<count($mprima);$a++)
							{
								if($var==count($mprima)) $tpl->assign("existencia","");
								else
								{
									$var++;
									if($mat_primas[$j]['codmp'] == $mprima[$a]['codmp'] and $mprima[$a]['num_cia'] == $companias[$i]['num_cia']) 
									{
									$tpl->assign("existencia",number_format($mprima[$a]['precio_unidad'],2,'.',','));
									break;
									}
								}
							}
						}
					}
				}
				$aux1 = $aux2;
				$aux2 += 10;
			}
		}
	}
}
$tpl->printToScreen();
?>