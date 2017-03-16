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
$tpl->assignInclude("body","./plantillas/fac/fac_costos_con.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_datos");
	
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

if($_GET['tipo_con'] == 0)
{
	if($_GET['codmp']==""){
		$tpl->newBlock("listado_cia");
		$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
		$tpl->assign("num_cia",$_GET['num_cia']);
		$tpl->assign("nom_cia",$cia[0]['nombre_corto']);
		$tpl->assign("dia",date("d"));
		$tpl->assign("mes",$nombremes[date("n")]);
		$tpl->assign("anio",date("Y"));

		$sql="select * from inventario_real where num_cia=".$_GET['num_cia']." order by codmp";
		$inventario=ejecutar_script($sql,$dsn);
		if(!$inventario)
		{
			header("location: ./fac_costos_con.php?codigo_error=1");
			die();
		}
		for($i=0;$i<count($inventario);$i+=2)
		{
			$tpl->newBlock("rows");
			$nom=obtener_registro("catalogo_mat_primas",array("codmp"),array($inventario[$i]["codmp"]),"","",$dsn);
			$tpl->assign("codmp",$inventario[$i]["codmp"]);
			$tpl->assign("nom_mp",$nom[0]['nombre']);
			$tpl->assign("costo",number_format($inventario[$i]['precio_unidad'],2,'.',','));
			if(($i+1)==count($inventario)) break;
			$nom1=obtener_registro("catalogo_mat_primas",array("codmp"),array($inventario[$i+1]["codmp"]),"","",$dsn);
			$tpl->assign("codmp1",$inventario[$i+1]["codmp"]);
			$tpl->assign("nom_mp1",$nom1[0]['nombre']);
			$tpl->assign("costo1",number_format($inventario[$i+1]['precio_unidad'],2,'.',','));
		}


	}
	else{
		$tpl->newBlock("listado_cia_mp");
		$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
		$tpl->assign("num_cia",$_GET['num_cia']);
		$tpl->assign("nom_cia",$cia[0]['nombre_corto']);
		$tpl->assign("dia",date("d"));
		$tpl->assign("mes",$nombremes[date("n")]);
		$tpl->assign("anio",date("Y"));

		$sql="select * from inventario_real where num_cia=".$_GET['num_cia']." and codmp = ".$_GET['codmp'];
		$inventario=ejecutar_script($sql,$dsn);
		if(!$inventario)
		{
			header("location: ./fac_costos_con.php?codigo_error=1");
			die();
		}
		$nom=obtener_registro("catalogo_mat_primas",array("codmp"),array($inventario[0]["codmp"]),"","",$dsn);
		$tpl->assign("codmp",$inventario[0]["codmp"]);
		$tpl->assign("nom_mp",$nom[0]['nombre']);
		$tpl->assign("costo",number_format($inventario[0]['precio_unidad'],2,'.',','));
	}

	
	$tpl->printToScreen();
	die();
}

elseif($_GET['tipo_con'] == 1)
{
	if($_GET['mat_prima'] == 0){
		$tpl->newBlock("listado_una");
		$tpl->assign("dia",date("d"));
		$tpl->assign("mes",$nombremes[date("n")]);
		$tpl->assign("anio",date("Y"));
		$sql="select * from inventario_real where codmp=".$_GET['codmp']." and num_cia < 999 order by num_cia";
		$mat_primas=ejecutar_script($sql,$dsn);
		$tpl->newBlock("mat_primaA");
		$tpl->assign("codmp",$_GET['codmp']);
		$nombre=obtener_registro("catalogo_mat_primas",array("codmp"),array($_GET['codmp']),"","",$dsn);
		$tpl->assign("nom_mp",$nombre[0]['nombre']);
		for($i=0;$i<count($mat_primas);$i++)
		{
			$tpl->newBlock("rows1A");
			$tpl->assign("num_cia",$mat_primas[$i]['num_cia']);
			$tpl->newBlock("nombre_ciaA");
			$nom_cia=obtener_registro("catalogo_companias",array("num_cia"),array($mat_primas[$i]['num_cia']),"","",$dsn);
			$tpl->assign("nom_cia",$nom_cia[0]['nombre_corto']);
			$tpl->newBlock("costoA");
			$tpl->assign("costo",number_format($mat_primas[$i]['precio_unidad'],2,'.',','));
		}
		$tpl->gotoBlock("listado_una");
	}

	elseif($_GET['mat_prima']==1)
	{
		$cias="select num_cia,nombre_corto from catalogo_companias where num_cia <= 100 order by num_cia";
		$companias=ejecutar_script($cias,$dsn);
		$mat="select * from catalogo_mat_primas where tipo_cia=true order by codmp";
		$mat_primas=ejecutar_script($mat,$dsn);
		$total=count($mat_primas);

		$paginas=$total/10;
		$paginas=ceil($paginas);
//		$paginas = 10;
		$aux1=0;
		$aux2=10;

		for($z=0;$z<$paginas;$z++){	
			$tpl->newBlock("listado_todos");
			$tpl->assign("dia",date("d"));
			$tpl->assign("mes",$nombremes[date("n")]);
			$tpl->assign("anio",date("Y"));
			
			for($j=$aux1;$j<$aux2;$j++){
				if($j==$total) break;
				$tpl->newBlock("mat_prima");
				$tpl->assign("codmp",$mat_primas[$j]['codmp']);
				$tpl->assign("nom_mp",$mat_primas[$j]['nombre']);
			}

			$sql="select catalogo_companias.num_cia, codmp, precio_unidad from catalogo_companias join inventario_real on (inventario_real.num_cia=catalogo_companias.num_cia and catalogo_companias.num_cia between 1 and 100) where codmp between ".$mat_primas[$aux1]['codmp']." and ".$mat_primas[$j-1]['codmp']."
			order by catalogo_companias.num_cia, codmp";
			$mprima=ejecutar_script($sql,$dsn);
//			echo "<br>inicio $aux1 final $aux2 <br>";
//			print_r($mprima);

	
			for($i=0;$i<count($companias);$i++){
				$tpl->newBlock("rows1");
				$tpl->assign("num_cia",$companias[$i]['num_cia']);
				$tpl->assign("nom_cia",$companias[$i]['nombre_corto']);
				
				for($j=$aux1;$j<$aux2;$j++)
				{
//					$var=0;
					if($j==$total) break;
					$tpl->newBlock("costo");
					if(!$mprima) $tpl->assign("costo","");
					else{
						for($a=0;$a<count($mprima);$a++){
//							if($var==count($mprima)) $tpl->assign("costo","");
//							else{
//								$var++;
								if($mat_primas[$j]['codmp'] == $mprima[$a]['codmp'] and $mprima[$a]['num_cia'] == $companias[$i]['num_cia']){
									$tpl->assign("costo",number_format($mprima[$a]['precio_unidad'],2,'.',','));
									break;
								}
//							}
						}
					}
				}
			}
			$aux1 = $aux2;
			$aux2 += 10;

			if($z+1 < $paginas){
				$tpl->gotoBlock("listado_todos");
				$tpl->assign("salto_pagina","<br style='page-break-after:always;'>");
			}

		}
	}


$tpl->printToScreen();

}
?>