<?php
//--------------------------------------------------------------------------------prueba de pan normal sin pasteles usando DB

// CONSULTA DE PRODUCCION
// Tabla 'produccion'
// Menu 'Panaderías->Producción'
//define ('IDSCREEN',1241); // ID de pantalla
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
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
$descripcion_error[1] = "La compañía no existe en la Base de Datos";
$descripcion_error[2] = "No hay registros";
$descripcion_error[3] = "Fecha incorrecta, vericar el formato (dd/mm/aaaa)";
$descripcion_error[4] = "Fecha fuera de rango, vericar el formato (dd/mm/aaaa)";
$descripcion_error[5] = "NO TIENES ASIGNADAS PANADERIAS";
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/pan/pan_ppn_con.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$db = new DBclass($dsn,"autocommit=yes");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_datos");
//	echo "prueba de pan sin pasteles y usando DB";
	$tpl->assign("anio_actual",date("Y"));
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
$tpl->newBlock("prueba_pan");
//ARROJA EL NUMERO DE ITERACIONES DENTRO DEL FOR A PARTIR DEL RANGO DE FECHAS
//$fecha_inicio='1/'.date("m").'/'.date("Y");
$fech=explode("/",$_GET['fecha_mov']);
$fecha_inicio='1/'.$fech[1].'/'.$fech[2];

if($_GET['tipo_cia'] == 0)
	$cia="select num_cia, nombre_corto from catalogo_companias where num_cia='".$_GET['num_cia']."'";
else if($_GET['tipo_cia'] == 1){
	if($_GET['tipo_total']==0){
		if($_SESSION['iduser']==1 or $_SESSION['iduser']==4){
			$cia="select num_cia, nombre_corto from catalogo_companias where num_cia between 1 and 100 order by num_cia";
		}
		else{
			$opera=$db->query("SELECT * FROM catalogo_operadoras WHERE iduser=$_SESSION[iduser]");
			if(!$opera){
				header("location: ./pan_ppn_con.php?codigo_error=5");
				die();
			}
			$cia="select num_cia, nombre_corto from catalogo_companias where idoperadora=".$opera[0]['idoperadora']." and num_cia < 100 order by num_cia";
		}
	}
	else{
		$cia="select num_cia, nombre_corto from catalogo_companias where num_cia between 1 and 100 order by num_cia";
	}
}
$companias=$db->query($cia);

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

$salto=1;


//print_r($companias);
if($_GET['tipo_total']==0){
	for($j=0;$j<count($companias);$j++)
	{
		$quebrado=0;
		$prod=0;
		$total_produccion =0;
		$total_pan_comprado =0;
		$total_venta_pta = 0;
		$total_reparto = 0;
		$total_devuelto = 0;
		$total_quebrado = 0;
		$total_desc_pastel = 0;
		$total_diferencia = 0;
		$total_porc_dif = 0;
		$dif_promedio=0;
		$total_gastos=0;
		$total_abono=0;
		$descuento=0;
		$descuento1=0;
		$var=0;
		$total_efectivos=0;
		$arrastre_sobrante=0;
	
		$sql="select count(distinct(fecha)) from total_panaderias where num_cia=".$companias[$j]['num_cia']." and fecha between '".$fecha_inicio."' AND '".$_GET['fecha_mov']."'";
		$contador=$db->query($sql);
		$cont = $contador[0]['count'];
		if($cont <=0) continue;
	
		$sql2="select distinct(fecha) as fecha_total from total_panaderias where num_cia=".$companias[$j]['num_cia']." and fecha between '".$fecha_inicio."' and '".$_GET['fecha_mov']."'";
		$fechas=$db->query($sql2);
		if($_GET['tipo_total']==0)
		{
			$tpl->newBlock("compania");
			$dia_1=explode("/",$_GET['fecha_mov']);
			$tpl->assign("dia",$dia_1[0]);
			$tpl->assign("anio",$fech[2]);
			$tpl->assign("mes",$nombremes[$fech[1]]);
			$tpl->assign("hora",date("G:i:s"));
			
			$tpl->assign("num_cia",$companias[$j]['num_cia']);
			$tpl->assign("nom_cia",$companias[$j]['nombre_corto']);
		
			for($i=0;$i<$cont;$i++){
				$tpl->newBlock("rows");
				$var++;
				$dia=explode("/",$fechas[$i]['fecha_total']);
				$fecha_anterior=date("d/m/Y",mktime(0,0,0,$dia[1],$dia[0]-1,$dia[2]));
				
				//SOBRANTE DEL DIA ANTERIOR
				$sql="select * from prueba_pan where num_cia=".$companias[$j]['num_cia']." and fecha='".$fecha_anterior."'";
				$sobrante=$db->query($sql);
				if($sobrante){
					//SE AGREGO ESTA LINEA
					if($fech[1]==4 && $fech[2]==2005 && $companias[$j]['num_cia']==31 && $dia[0]==1)
						$aux_sobrante_ayer=1000;
					else if ($fech[1]==5 && $fech[2]==2005 && $companias[$j]['num_cia']==49 && $dia[0]==1)
						$aux_sobrante_ayer=13000;
					else if ($fech[1]==5 && $fech[2]==2005 && $companias[$j]['num_cia']==74 && $dia[0]==1)
						$aux_sobrante_ayer=3203;
					else
						$aux_sobrante_ayer=$sobrante[0]['importe'];
				}
				else{
					//se inserto este bloque de codigo
					if($fech[1]==4 && $fech[2]==2005 && $companias[$j]['num_cia']==31 && $dia[0]==1)
						$aux_sobrante_ayer=1000;
					else if ($fech[1]==5 && $fech[2]==2005 && $companias[$j]['num_cia']==49 && $dia[0]==1)
						$aux_sobrante_ayer=13000;
					else if ($fech[1]==5 && $fech[2]==2005 && $companias[$j]['num_cia']==74 && $dia[0]==1)
						$aux_sobrante_ayer=3203;
					else if ($fech[1]==6 && $fech[2]==2005 && $companias[$j]['num_cia']==75 && $dia[0]==13)
						$aux_sobrante_ayer=0;
					else
					//---------------------------------
						$aux_sobrante_ayer=@$sobrante_hoy;
				}

				//PAN CONTADO DEL DIA
				$sql="select * from prueba_pan where num_cia=".$companias[$j]['num_cia']." and fecha='".$fechas[$i]['fecha_total']."'";
				$pan_contado=$db->query($sql);
				if($pan_contado){
					$aux_pan_contado=$pan_contado[0]['importe'];
				}
				else{
					$aux_pan_contado=$aux_sobrante_ayer;
				}
					
				$sql="SELECT * from total_panaderias where num_cia=".$companias[$j]['num_cia']." and fecha='".$fechas[$i]['fecha_total']."'";
				$efectivos_tabla=$db->query($sql);
					
				//PRODUCCION INCLUYE TOTAL PRODUCCION
				$sql="SELECT distinct(numcia), fecha_total, (select sum(total_produccion) from total_produccion where numcia=".$companias[$j]['num_cia']." and fecha_total = '".$fechas[$i]['fecha_total']."') as total
				FROM total_produccion WHERE numcia=".$companias[$j]['num_cia']." and fecha_total = '".$fechas[$i]['fecha_total']."'";
				$produccion=$db->query($sql);
	
				//Obtiene datos de los expendios
				$sql1="SELECT distinct(num_cia), fecha, (select sum(pan_p_venta) from mov_expendios where num_cia=".$companias[$j]['num_cia']." and fecha = '".$fechas[$i]['fecha_total']."') as reparto, (select sum(devolucion) from mov_expendios where num_cia=".$companias[$j]['num_cia']." and 
				fecha = '".$fechas[$i]['fecha_total']."') as devuelto
				FROM mov_expendios WHERE num_cia=".$companias[$j]['num_cia']." and fecha = '".$fechas[$i]['fecha_total']."'";
				$expendios=$db->query($sql1);
			
				//Obtiene el importe del pan comprado
				//AQUI MODIFIQUE GASTOS
				$sql2="SELECT distinct(num_cia), fecha, (select sum(importe) from movimiento_gastos where num_cia=".$companias[$j]['num_cia']." and codgastos=5  and codgastos not in(30,33) and captura=false and fecha ='".$fechas[$i]['fecha_total']."') as importe
				FROM movimiento_gastos
				WHERE num_cia=".$companias[$j]['num_cia']." and fecha = '".$fechas[$i]['fecha_total']."'";
				$desc=$db->query($sql2);

				//pan comprado sin descuento
				$sql3="SELECT distinct(num_cia), fecha, (select sum(importe) from movimiento_gastos where num_cia=".$companias[$j]['num_cia']." and codgastos=152  and codgastos not in(30,33) and captura=false and fecha ='".$fechas[$i]['fecha_total']."') as importe
				FROM movimiento_gastos
				WHERE num_cia=".$companias[$j]['num_cia']." and fecha = '".$fechas[$i]['fecha_total']."'";
				$desc1=$db->query($sql3);

				
				$efe="select num_cia, fecha, venta_pta, desc_pastel, pastillaje, otros from captura_efectivos where num_cia=".$companias[$j]['num_cia']." and fecha ='".$fechas[$i]['fecha_total']."'";
				$efectivos=$db->query($efe);
		
				$sql="select * from porcentaje_pan_comprado where num_cia=".$companias[$j]['num_cia'];
				$porc_pastel = $db->query($sql);
				
				if($fech[1] < 6 and $fech[2] <= 2005){
					$descuento = number_format($desc[0]['importe'],2,'.','') * ($porc_pastel[0]['porcentaje']/100);
					$descuento1= $desc[0]['importe'] + number_format($descuento,2,'.','');
				}
				else
					$descuento1 = (number_format($desc[0]['importe'],2,'.','') / (100 - $porc_pastel[0]['porcentaje']))*100;

				$descuento1 += number_format($desc1[0]['importe'],2,'.','');

				$prod=$produccion[0]['total'];
				$quebrado=$prod * 0.02;
				
				//CALCULO DE TOTAL DE PAN
				$total_pan=$prod+$descuento1+$aux_sobrante_ayer;
				//SOBRANTE DE HOY
				$sobrante_hoy = $total_pan - $efectivos_tabla[0]['venta_puerta'] - $expendios[0]['reparto']-$quebrado-$efectivos[0]['desc_pastel'];
				//DIFERENCIA
				if($pan_contado){
					$diferencia=$pan_contado[0]['importe']-$sobrante_hoy;
					}
				else
					$diferencia=0;
				
				$tpl->assign("dia",$dia[0]);
				$tpl->assign("produccion",number_format($prod,2,'.',',')); //A
				if($descuento1<=0) $tpl->assign("pan_comprado","");
				else $tpl->assign("pan_comprado",number_format($descuento1,2,'.',','));//B
				
				if ($fech[1]==5 && $fech[2]==2005 && $companias[$j]['num_cia']==74 && $dia[0]==1)
					$tpl->assign("sobrante_ayer",number_format($aux_sobrante_ayer,2,'.',','));
				else if($aux_sobrante_ayer<=0) $tpl->assign("sobrante_ayer","");
				else $tpl->assign("sobrante_ayer",number_format($aux_sobrante_ayer,2,'.',','));//C
				
				if($total_pan<=0) $tpl->assign("total_pan","");
				else $tpl->assign("total_pan",number_format($total_pan,2,'.',','));//D
				
				if($efectivos_tabla[0]['venta_puerta']<=0) $tpl->assign("venta_puerta","");
				else $tpl->assign("venta_puerta",number_format($efectivos_tabla[0]['venta_puerta'],2,'.',','));//E
				
				if($expendios[0]['reparto']==0) $tpl->assign("reparto","");
				else $tpl->assign("reparto",number_format($expendios[0]['reparto'],2,'.',','));//F
				
				if($expendios[0]['devuelto']<=0) $tpl->assign("pan_devuelto","");
				else $tpl->assign("pan_devuelto",number_format($expendios[0]['devuelto'],2,'.',','));//G
				
				if($quebrado<=0) $tpl->assign("pan_quebrado","");
				else $tpl->assign("pan_quebrado",number_format($quebrado,2,'.',','));//H
				
				if($efectivos[0]['desc_pastel']<=0) $tpl->assign("desc_pastel","");
				else $tpl->assign("desc_pastel",number_format($efectivos[0]['desc_pastel'],2,'.',','));//I
				
				if($sobrante_hoy==0) $tpl->assign("sobrante_hoy","");
				else $tpl->assign("sobrante_hoy",number_format($sobrante_hoy,2,'.',','));//J
				
				if($pan_contado)
					$tpl->assign("existencia_fisica",number_format($aux_pan_contado,2,'.',','));//K
				else $tpl->assign("existencia_fisica","");
				
				if($diferencia==0) $tpl->assign("diferencia","");
				else $tpl->assign("diferencia",number_format($diferencia,2,'.',','));//L
				
				$total_gastos += $efectivos_tabla[0]['gastos'];
				$total_abono += $efectivos_tabla[0]['abono'];
				$total_efectivos +=$efectivos_tabla[0]['venta_puerta'] + $efectivos_tabla[0]['pastillaje'] + $efectivos_tabla[0]['otros']-$efectivos_tabla[0]['raya_pagada'];
		
				$total_produccion += $prod;
				$total_pan_comprado += $descuento1;
				$total_venta_pta += $efectivos_tabla[0]['venta_puerta'];
				$total_reparto += $expendios[0]['reparto'];
				$total_devuelto += $expendios[0]['devuelto'];
				$total_quebrado += $quebrado;
				$total_desc_pastel += $efectivos[0]['desc_pastel'];
				$total_diferencia += $diferencia;
			}
			$porc_dif=($total_diferencia/($total_produccion + $total_pan_comprado))*100;
			$dif_promedio=$total_diferencia / $var;
			$efec=$total_efectivos+$total_abono-$total_gastos;
			$ef_prod=$efec/$total_produccion;
			$tpl->gotoBlock("compania");
			$tpl->assign("total_produccion",number_format($total_produccion,2,'.',','));
			$tpl->assign("total_comprado",number_format($total_pan_comprado,2,'.',','));
			$tpl->assign("total_puerta",number_format($total_venta_pta,2,'.',','));
			$tpl->assign("total_reparto",number_format($total_reparto,2,'.',','));
			$tpl->assign("total_devuelto",number_format($total_devuelto,2,'.',','));
			$tpl->assign("total_quebrado",number_format($total_quebrado,2,'.',','));
			$tpl->assign("total_desc_pastel",number_format($total_desc_pastel,2,'.',','));
			$tpl->assign("total_diferencia",number_format($total_diferencia,2,'.',','));
			$tpl->assign("porc_dif",abs(number_format($porc_dif,2,'.',',')));
			$tpl->assign("promedio_dif",number_format($dif_promedio,2,'.',','));
			$tpl->assign("ef_prod",number_format($ef_prod,2,'.',','));
		}
		if($salto % 2 == 0)
			$tpl->newBlock("salto");
		$salto++;
	}
}

//VA A MOSTRAR LOS TOTALES POR COMPAÑÍA	
//*******************************************************************************************************
else if($_GET['tipo_total']==1)
{
	$tpl->newBlock("totales");
	$dia_1=explode("/",$_GET['fecha_mov']);
	$tpl->assign("dia",$dia_1[0]);
	$tpl->assign("anio",$fech[2]);
	$tpl->assign("mes",$nombremes[$fech[1]]);

	for($j=0;$j<count($companias);$j++)
	{
		$quebrado=0;
		$prod=0;
		$total_produccion =0;
		$total_pan_comprado =0;
		$total_venta_pta = 0;
		$total_reparto = 0;
		$total_devuelto = 0;
		$total_quebrado = 0;
		$total_desc_pastel = 0;
		$total_diferencia = 0;
		$total_porc_dif = 0;
		$dif_promedio=0;
		$total_gastos=0;
		$total_abono=0;
		$descuento=0;
		$descuento1=0;
		$var=0;
		$total_efectivos=0;
		$arrastre_sobrante=0;

		$sql="select count(distinct(fecha)) from total_panaderias where num_cia=".$companias[$j]['num_cia']." and fecha between '".$fecha_inicio."' AND '".$_GET['fecha_mov']."'";
		$contador=$db->query($sql,$dsn);
		$cont = $contador[0]['count'];
		if($cont <=0) continue;


		$sql2="select distinct(fecha) as fecha_total from total_panaderias where num_cia=".$companias[$j]['num_cia']." and fecha between '".$fecha_inicio."' and '".$_GET['fecha_mov']."'";
		$fechas=$db->query($sql2,$dsn);

	
		for($i=0;$i<$cont;$i++){
			$var++;
			$dia=explode("/",$fechas[$i]['fecha_total']);
			$fecha_anterior=date("d/m/Y",mktime(0,0,0,$dia[1],$dia[0]-1,$dia[2]));
				//SOBRANTE DEL DIA ANTERIOR
				$sql="select * from prueba_pan where num_cia=".$companias[$j]['num_cia']." and fecha='".$fecha_anterior."'";
				$sobrante=$db->query($sql);
				if($sobrante){
					$aux_sobrante_ayer=$sobrante[0]['importe'];
				}
				else{
					$aux_sobrante_ayer=$sobrante_hoy;
				}

				//PAN CONTADO DEL DIA
				$sql="select * from prueba_pan where num_cia=".$companias[$j]['num_cia']." and fecha= '".$fechas[$i]['fecha_total']."'";
				$pan_contado=$db->query($sql);
				if($pan_contado){
					$aux_pan_contado=$pan_contado[0]['importe'];
				}
				else{
					$aux_pan_contado=$aux_sobrante_ayer;
				}
					
				$sql="SELECT * from total_panaderias where num_cia=".$companias[$j]['num_cia']." and fecha='".$fechas[$i]['fecha_total']."'";
				$efectivos_tabla=$db->query($sql);
					
				//PRODUCCION INCLUYE TOTAL PRODUCCION
				$sql="SELECT distinct(numcia), fecha_total, (select sum(total_produccion) from total_produccion where numcia=".$companias[$j]['num_cia']." and fecha_total = '".$fechas[$i]['fecha_total']."') as total
				FROM total_produccion WHERE numcia=".$companias[$j]['num_cia']." and fecha_total = '".$fechas[$i]['fecha_total']."'";
				$produccion=$db->query($sql,$dsn);
	
				//Obtiene datos de los expendios
				$sql1="SELECT distinct(num_cia), fecha, (select sum(pan_p_venta) from mov_expendios where num_cia=".$companias[$j]['num_cia']." and fecha = '".$fechas[$i]['fecha_total']."') as reparto, (select sum(devolucion) from mov_expendios where num_cia=".$companias[$j]['num_cia']." and 
				fecha = '".$fechas[$i]['fecha_total']."') as devuelto
				FROM mov_expendios WHERE num_cia=".$companias[$j]['num_cia']." and fecha = '".$fechas[$i]['fecha_total']."'";
				$expendios=ejecutar_script($sql1);
			
				//Obtiene el importe del pan comprado
				//AQUI MODIFIQUE GASTOS
				$sql2="SELECT distinct(num_cia), fecha, (select sum(importe) from movimiento_gastos where num_cia=".$companias[$j]['num_cia']." and codgastos=5  and codgastos not in(30,33) and captura=false and fecha ='".$fechas[$i]['fecha_total']."') as importe
				FROM movimiento_gastos
				WHERE num_cia=".$companias[$j]['num_cia']." and fecha = '".$fechas[$i]['fecha_total']."'";
				$desc=$db->query($sql2);
				
				//pan comprado sin descuento
				$sql3="SELECT distinct(num_cia), fecha, (select sum(importe) from movimiento_gastos where num_cia=".$companias[$j]['num_cia']." and codgastos=152  and codgastos not in(30,33) and captura=false and fecha ='".$fechas[$i]['fecha_total']."') as importe
				FROM movimiento_gastos
				WHERE num_cia=".$companias[$j]['num_cia']." and fecha = '".$fechas[$i]['fecha_total']."'";
				$desc1=$db->query($sql3);

				$efe="select num_cia, fecha, venta_pta, desc_pastel, pastillaje, otros from captura_efectivos where num_cia=".$companias[$j]['num_cia']." and fecha ='".$fechas[$i]['fecha_total']."'";
				$efectivos=$db->query($efe);
		
				$sql="select * from porcentaje_pan_comprado where num_cia=".$companias[$j]['num_cia'];
				$porc_pastel = $db->query($sql);
				
				if($fech[1] < 6 and $fech[2] <= 2005){
					$descuento = $desc[0]['importe'] * ($porc_pastel[0]['porcentaje']/100);
					$descuento1= $desc[0]['importe'] + $descuento;
				}
				else
					$descuento1 = ($desc[0]['importe'] / (100 - $porc_pastel[0]['porcentaje']))*100;
				
				$descuento1 += number_format($desc1[0]['importe'],2,'.','');

//				$descuento = $desc[0]['importe'] * ($porc_pastel[0]['porcentaje']/100);
//				$descuento1= $desc[0]['importe'] + $descuento;
				$prod=$produccion[0]['total'];
				$quebrado=$prod * 0.02;
				
				//CALCULO DE TOTAL DE PAN
				$total_pan=$prod+$descuento1+$aux_sobrante_ayer;
				//SOBRANTE DE HOY
				$sobrante_hoy = $total_pan - $efectivos_tabla[0]['venta_puerta'] - $expendios[0]['reparto']-$quebrado-$efectivos[0]['desc_pastel'];
				//DIFERENCIA
				if($pan_contado){
					$diferencia=$pan_contado[0]['importe']-$sobrante_hoy;
				}
				else
					$diferencia=0;

				$total_gastos += $efectivos_tabla[0]['gastos'];
				$total_abono += $efectivos_tabla[0]['abono'];
				$total_efectivos +=$efectivos_tabla[0]['venta_puerta'] + $efectivos_tabla[0]['pastillaje'] + $efectivos_tabla[0]['otros']-$efectivos_tabla[0]['raya_pagada'];
		
				$total_produccion += $prod;
				$total_pan_comprado += $descuento1;
				$total_venta_pta += $efectivos_tabla[0]['venta_puerta'];
				$total_reparto += $expendios[0]['reparto'];
				$total_devuelto += $expendios[0]['devuelto'];
				$total_quebrado += $quebrado;
				$total_desc_pastel += $efectivos[0]['desc_pastel'];
				$total_diferencia += $diferencia;
		}
		$porc_dif=($total_diferencia/($total_produccion + $total_pan_comprado))*100;
		$dif_promedio=$total_diferencia / $var;
		$efec=$total_efectivos+$total_abono-$total_gastos;
		$ef_prod=$efec/$total_produccion;
		$tpl->newBlock("rows_totales");
		$tpl->assign("num_cia",$companias[$j]['num_cia']);
		$tpl->assign("nombre_cia",$companias[$j]['nombre_corto']);

		if($total_produccion == 0) $tpl->assign("total_produccion","");
		else
			$tpl->assign("total_produccion",number_format($total_produccion,2,'.',','));

		if($total_pan_comprado == 0) $tpl->assign("total_pan_comprado","");
		else
			$tpl->assign("total_pan_comprado",number_format($total_pan_comprado,2,'.',','));

		if($total_venta_pta == 0) $tpl->assign("total_puerta","");
		else
			$tpl->assign("total_puerta",number_format($total_venta_pta,2,'.',','));

		if($total_reparto == 0) $tpl->assign("reparto","");
		else
			$tpl->assign("reparto",number_format($total_reparto,2,'.',','));
		
		if($total_devuelto == 0) $tpl->assign("total_devuelto","");
		else
			$tpl->assign("total_devuelto",number_format($total_devuelto,2,'.',','));
		
		if($total_quebrado==0) $tpl->assign("total_quebrado","");
		else
			$tpl->assign("total_quebrado",number_format($total_quebrado,2,'.',','));
		
		if($total_desc_pastel==0) $tpl->assign("total_desc_pastel","");
		else
			$tpl->assign("total_desc_pastel",number_format($total_desc_pastel,2,'.',','));
		
		if($total_diferencia==0) $tpl->assign("total_diferencia","");
		else
			$tpl->assign("total_diferencia",number_format($total_diferencia,2,'.',','));
		
		if($porc_dif==0) $tpl->assign("porc_dif","");
		else
			$tpl->assign("porc_dif",abs(number_format($porc_dif,2,'.',',')));
		
		if($dif_promedio==0) $tpl->assign("promedio_dif","");
		else
			$tpl->assign("promedio_dif",number_format($dif_promedio,2,'.',','));

		if($ef_prod==0) $tpl->assign("ef_prod","");
		else
			$tpl->assign("ef_prod",number_format($ef_prod,2,'.',','));
	}
	
}
$tpl->printToScreen();
// --------------------------------------------------------------------------------------------------------
?>