<?php
//---------------------------------------------------------------------------------------------prueba de pan con pasteles usando DB3

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
$tpl->assignInclude("body","./plantillas/pan/pan_ppn_con2.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_datos");
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
			$opera=ejecutar_script("SELECT * FROM catalogo_operadoras WHERE iduser=$_SESSION[iduser]",$dsn);
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

$companias=ejecutar_script($cia,$dsn);
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
		$contador=ejecutar_script($sql,$dsn);
		$cont = $contador[0]['count'];
		if($cont <=0) continue;
	
		$sql2="select distinct(fecha) as fecha_total from total_panaderias where num_cia=".$companias[$j]['num_cia']." and fecha between '".$fecha_inicio."' and '".$_GET['fecha_mov']."'";
		$fechas=ejecutar_script($sql2,$dsn);
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
				$total_facturas=0;
				$total_anticipadas=0;
				$total_posteriores=0;
				$total_cuenta_liquida=0;
				$total_cuenta_pago=0;
			
				$tpl->newBlock("rows");
				$var++;
				$dia=explode("/",$fechas[$i]['fecha_total']);
				$fecha_anterior=date("d/m/Y",mktime(0,0,0,$dia[1],$dia[0]-1,$dia[2]));
				
				//SOBRANTE DEL DIA ANTERIOR
				if(existe_registro("prueba_pan",array("num_cia","fecha"),array($companias[$j]['num_cia'],$fecha_anterior),$dsn)){
					$sobrante = obtener_registro("prueba_pan",array("num_cia","fecha"),array($companias[$j]['num_cia'],$fecha_anterior),"","",$dsn);
					//SE AGREGO ESTA LINEA
					if($fech[1]==4 && $fech[2]==2005 && $companias[$j]['num_cia']==31 && $dia[0]==1)
						$aux_sobrante_ayer=1000;
					else if ($fech[1]==5 && $fech[2]==2005 && $companias[$j]['num_cia']==49 && $dia[0]==1)
						$aux_sobrante_ayer=13000;
					else if ($fech[1]==5 && $fech[2]==2005 && $companias[$j]['num_cia']==74 && $dia[0]==1)
						$aux_sobrante_ayer=3203;
					else
						$aux_sobrante_ayer=$sobrante[0]['importe'];
//						$aux_sobrante_ayer=0;
	
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
				if(existe_registro("prueba_pan",array("num_cia","fecha"),array($companias[$j]['num_cia'],$fechas[$i]['fecha_total']),$dsn)){
					$pan_contado = obtener_registro("prueba_pan",array("num_cia","fecha"),array($companias[$j]['num_cia'],$fechas[$i]['fecha_total']),"","",$dsn);
					$aux_pan_contado=$pan_contado[0]['importe'];
					}
				else{
					$aux_pan_contado=$aux_sobrante_ayer;
					
					}
					
//----------------------CODIGO PARA SACAR EL PASTEL ENTREGADO
				$facturas_pago_anticipado=ejecutar_script("select sum(cuenta) from venta_pastel where fecha_entrega='".$fechas[$i]['fecha_total']."' and fecha != '".$fechas[$i]['fecha_total']."' and num_cia=".$companias[$j]['num_cia'],$dsn);
				$facturas_pago_anticipado_base=ejecutar_script("select sum(base) from venta_pastel where fecha_entrega='".$fechas[$i]['fecha_total']."' and fecha != '".$fechas[$i]['fecha_total']."' and num_cia=".$companias[$j]['num_cia'],$dsn);
//				$facturas_cuenta_liquida=ejecutar_script("select sum(resta) from venta_pastel where fecha='".$fechas[$i]['fecha_total']."' and resta is not null and cuenta=0 and dev_base is null and num_cia=".$companias[$j]['num_cia'],$dsn);
//				$facturas_cuenta_pago=ejecutar_script("select sum(cuenta) from venta_pastel where fecha='".$fechas[$i]['fecha_total']."' and fecha_entrega='".$fechas[$i]['fecha_total']."' and num_cia=".$companias[$j]['num_cia'],$dsn);
				$facturas_posteriores=ejecutar_script("select sum(cuenta) from venta_pastel where fecha='".$fechas[$i]['fecha_total']."' and resta is null and cuenta > 0 and dev_base is null and num_cia=".$companias[$j]['num_cia']." and fecha_entrega > '".$fechas[$i]['fecha_total']."'",$dsn);
				$facturas_posteriores_base=ejecutar_script("select sum(base) from venta_pastel where fecha='".$fechas[$i]['fecha_total']."' and resta is null and cuenta > 0 and dev_base is null and num_cia=".$companias[$j]['num_cia']." and fecha_entrega > '".$fechas[$i]['fecha_total']."'",$dsn);
			
				$pastel=ejecutar_script("select venta_pastel + abono_pastel as pastel from total_panaderias where num_cia=".$companias[$j]['num_cia']." and fecha = '".$fechas[$i]['fecha_total']."'",$dsn);
			
				if($facturas_pago_anticipado){
					$total_anticipadas=number_format($facturas_pago_anticipado[0]["sum"],2,'.','') - number_format($facturas_pago_anticipado_base[0]["sum"],2,'.','');
				}
/*				if($facturas_cuenta_liquida){
					$total_cuenta_liquida=number_format($facturas_cuenta_liquida[0]["sum"],2,'.','');
				}
				if($facturas_cuenta_pago){
					$total_cuenta_pago=number_format($facturas_cuenta_pago[0]["sum"],2,'.','');
				}
*/				if($facturas_posteriores){
					$total_posteriores= number_format($facturas_posteriores[0]["sum"],2,'.','') - number_format($facturas_posteriores_base[0]["sum"],2,'.','');
				}
				
//				$total_facturas=$total_cuenta_liquida + $total_cuenta_pago;
				
				$total_pasteles= $total_anticipadas - $total_posteriores;

//----------------------	


				$sql="SELECT * from total_panaderias where num_cia=".$companias[$j]['num_cia']." and fecha='".$fechas[$i]['fecha_total']."'";
				$efectivos_tabla=ejecutar_script($sql,$dsn);
					
				//PRODUCCION INCLUYE TOTAL PRODUCCION
				$sql="SELECT distinct(numcia), fecha_total, (select sum(total_produccion) from total_produccion where numcia=".$companias[$j]['num_cia']." and fecha_total = '".$fechas[$i]['fecha_total']."') as total
				FROM total_produccion WHERE numcia=".$companias[$j]['num_cia']." and fecha_total = '".$fechas[$i]['fecha_total']."'";
				$produccion=ejecutar_script($sql,$dsn);
	
				//Obtiene datos de los expendios
				$sql1="SELECT distinct(num_cia), fecha, (select sum(pan_p_venta) from mov_expendios where num_cia=".$companias[$j]['num_cia']." and fecha = '".$fechas[$i]['fecha_total']."') as reparto, (select sum(devolucion) from mov_expendios where num_cia=".$companias[$j]['num_cia']." and 
				fecha = '".$fechas[$i]['fecha_total']."') as devuelto
				FROM mov_expendios WHERE num_cia=".$companias[$j]['num_cia']." and fecha = '".$fechas[$i]['fecha_total']."'";
				$expendios=ejecutar_script($sql1,$dsn);
			
				//Obtiene el importe del pan comprado con descuento
				//AQUI MODIFIQUE GASTOS
				$sql2="SELECT distinct(num_cia), fecha, (select sum(importe) from movimiento_gastos where num_cia=".$companias[$j]['num_cia']." and codgastos=5  and codgastos not in(30,33) and captura=false and fecha ='".$fechas[$i]['fecha_total']."') as importe
				FROM movimiento_gastos
				WHERE num_cia=".$companias[$j]['num_cia']." and fecha = '".$fechas[$i]['fecha_total']."'";
				$desc=ejecutar_script($sql2,$dsn);

				//pan comprado sin descuento
				$sql3="SELECT distinct(num_cia), fecha, (select sum(importe) from movimiento_gastos where num_cia=".$companias[$j]['num_cia']." and codgastos=152  and codgastos not in(30,33) and captura=false and fecha ='".$fechas[$i]['fecha_total']."') as importe
				FROM movimiento_gastos
				WHERE num_cia=".$companias[$j]['num_cia']." and fecha = '".$fechas[$i]['fecha_total']."'";
				$desc1=ejecutar_script($sql3,$dsn);

				
				$efe="select num_cia, fecha, venta_pta, desc_pastel, pastillaje, otros from captura_efectivos where num_cia=".$companias[$j]['num_cia']." and fecha ='".$fechas[$i]['fecha_total']."'";
				$efectivos=ejecutar_script($efe,$dsn);
		
				$porc_pastel = obtener_registro("porcentaje_pan_comprado",array("num_cia"),array($companias[$j]['num_cia']),"","",$dsn);
				
				if($fech[1] < 6 and $fech[2] <= 2005){
					$descuento = $desc[0]['importe'] * ($porc_pastel[0]['porcentaje']/100);
					$descuento1= $desc[0]['importe'] + $descuento;
				}
				else
					$descuento1 = ($desc[0]['importe'] / (100 - $porc_pastel[0]['porcentaje']))*100;

				$descuento1 += number_format($desc1[0]['importe'],2,'.','');
				
				$prod=$produccion[0]['total'];
				$quebrado=$prod * 0.02;
				
				$venta_puerta=$efectivos_tabla[0]['venta_puerta'] + $total_pasteles;
				
				//CALCULO DE TOTAL DE PAN
				$total_pan=$prod+$descuento1+$aux_sobrante_ayer;
				//SOBRANTE DE HOY
//				$sobrante_hoy = $total_pan - $efectivos_tabla[0]['venta_puerta'] - $expendios[0]['reparto']-$quebrado-$efectivos[0]['desc_pastel'];
				$sobrante_hoy = $total_pan - $venta_puerta - $expendios[0]['reparto']-$quebrado-$efectivos[0]['desc_pastel'];
				//DIFERENCIA
				if(existe_registro("prueba_pan",array("num_cia","fecha"),array($companias[$j]['num_cia'],$fechas[$i]['fecha_total']),$dsn)){
					$diferencia=$pan_contado[0]['importe']-$sobrante_hoy;
					}
				else
					$diferencia=0;

//				echo $aux_sobrante_ayer;
				
				$tpl->assign("dia",$dia[0]);
				$tpl->assign("produccion",number_format($prod,2,'.',',')); //A
				if($descuento1<=0) $tpl->assign("pan_comprado","");
				else $tpl->assign("pan_comprado",number_format($descuento1,2,'.',','));//B
				
				if ($fech[1]==5 && $fech[2]==2005 && $companias[$j]['num_cia']==74 && $dia[0]==1)
					$tpl->assign("sobrante_ayer",number_format($aux_sobrante_ayer,2,'.',','));
				else if(@$sobrante[0]['importe']<=0) $tpl->assign("sobrante_ayer","");
				else $tpl->assign("sobrante_ayer",number_format($aux_sobrante_ayer,2,'.',','));//C
				
				if($total_pan<=0) $tpl->assign("total_pan","");
				else $tpl->assign("total_pan",number_format($total_pan,2,'.',','));//D
				
//				if($efectivos_tabla[0]['venta_puerta']<=0) $tpl->assign("venta_puerta","");
//				else $tpl->assign("venta_puerta",number_format($efectivos_tabla[0]['venta_puerta'],2,'.',','));//E
				if($venta_puerta<=0) $tpl->assign("venta_puerta","");
				else $tpl->assign("venta_puerta",number_format($venta_puerta,2,'.',','));//E
				
				if($expendios[0]['reparto']==0) $tpl->assign("reparto","");
				else $tpl->assign("reparto",number_format($expendios[0]['reparto'],2,'.',','));//F
				
				if($expendios[0]['devuelto']<=0) $tpl->assign("pan_devuelto","");
				else $tpl->assign("pan_devuelto",number_format($expendios[0]['devuelto'],2,'.',','));//G
				
				if($quebrado<=0) $tpl->assign("pan_quebrado","");
				else $tpl->assign("pan_quebrado",number_format($quebrado,2,'.',','));//H
				
				if($efectivos[0]['desc_pastel']<=0) $tpl->assign("desc_pastel","");
				else $tpl->assign("desc_pastel",number_format($efectivos[0]['desc_pastel'],2,'.',','));//I
				
				if($sobrante_hoy<=0) $tpl->assign("sobrante_hoy","");
				else $tpl->assign("sobrante_hoy",number_format($sobrante_hoy,2,'.',','));//J
				
				if(existe_registro("prueba_pan",array("num_cia","fecha"),array($companias[$j]['num_cia'],$fechas[$i]['fecha_total']),$dsn))
					$tpl->assign("existencia_fisica",number_format($aux_pan_contado,2,'.',','));//K
				else $tpl->assign("existencia_fisica","");
				
				if($diferencia==0) $tpl->assign("diferencia","");
				else $tpl->assign("diferencia",number_format($diferencia,2,'.',','));//L
				
				$total_gastos += $efectivos_tabla[0]['gastos'];
				$total_abono += $efectivos_tabla[0]['abono'];
//				$total_efectivos +=$efectivos_tabla[0]['venta_puerta'] + $efectivos_tabla[0]['pastillaje'] + $efectivos_tabla[0]['otros']-$efectivos_tabla[0]['raya_pagada'];
				$total_efectivos +=$venta_puerta + $efectivos_tabla[0]['pastillaje'] + $efectivos_tabla[0]['otros']-$efectivos_tabla[0]['raya_pagada'];		
				$total_produccion += $prod;
				$total_pan_comprado += $descuento1;
//				$total_venta_pta += $efectivos_tabla[0]['venta_puerta'];
				$total_venta_pta += $venta_puerta;
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
		$contador=ejecutar_script($sql,$dsn);
		$cont = $contador[0]['count'];
		if($cont <=0) continue;


		$sql2="select distinct(fecha) as fecha_total from total_panaderias where num_cia=".$companias[$j]['num_cia']." and fecha between '".$fecha_inicio."' and '".$_GET['fecha_mov']."'";
		$fechas=ejecutar_script($sql2,$dsn);

	
		for($i=0;$i<$cont;$i++){
			$var++;
			$dia=explode("/",$fechas[$i]['fecha_total']);
			$fecha_anterior=date("d/m/Y",mktime(0,0,0,$dia[1],$dia[0]-1,$dia[2]));
				//SOBRANTE DEL DIA ANTERIOR
				if(existe_registro("prueba_pan",array("num_cia","fecha"),array($companias[$j]['num_cia'],$fecha_anterior),$dsn)){
					$sobrante = obtener_registro("prueba_pan",array("num_cia","fecha"),array($companias[$j]['num_cia'],$fecha_anterior),"","",$dsn);
					$aux_sobrante_ayer=$sobrante[0]['importe'];
	
					}
				else{
					$aux_sobrante_ayer=$sobrante_hoy;
					}
				//PAN CONTADO DEL DIA
				if(existe_registro("prueba_pan",array("num_cia","fecha"),array($companias[$j]['num_cia'],$fechas[$i]['fecha_total']),$dsn)){
					$pan_contado = obtener_registro("prueba_pan",array("num_cia","fecha"),array($companias[$j]['num_cia'],$fechas[$i]['fecha_total']),"","",$dsn);
					$aux_pan_contado=$pan_contado[0]['importe'];
					}
				else{
					$aux_pan_contado=$aux_sobrante_ayer;
					
					}


//----------------------CODIGO PARA SACAR EL PASTEL ENTREGADO
				$facturas_pago_anticipado=ejecutar_script("select sum(cuenta) from venta_pastel where fecha_entrega='".$fechas[$i]['fecha_total']."' and fecha != '".$fechas[$i]['fecha_total']."' and num_cia=".$companias[$j]['num_cia'],$dsn);
				$facturas_pago_anticipado_base=ejecutar_script("select sum(base) from venta_pastel where fecha_entrega='".$fechas[$i]['fecha_total']."' and fecha != '".$fechas[$i]['fecha_total']."' and num_cia=".$companias[$j]['num_cia'],$dsn);
//				$facturas_cuenta_liquida=ejecutar_script("select sum(resta) from venta_pastel where fecha='".$fechas[$i]['fecha_total']."' and resta is not null and cuenta=0 and dev_base is null and num_cia=".$companias[$j]['num_cia'],$dsn);
//				$facturas_cuenta_pago=ejecutar_script("select sum(cuenta) from venta_pastel where fecha='".$fechas[$i]['fecha_total']."' and fecha_entrega='".$fechas[$i]['fecha_total']."' and num_cia=".$companias[$j]['num_cia'],$dsn);
				$facturas_posteriores=ejecutar_script("select sum(cuenta) from venta_pastel where fecha='".$fechas[$i]['fecha_total']."' and resta is null and cuenta > 0 and dev_base is null and num_cia=".$companias[$j]['num_cia']." and fecha_entrega > '".$fechas[$i]['fecha_total']."'",$dsn);
				$facturas_posteriores_base=ejecutar_script("select sum(base) from venta_pastel where fecha='".$fechas[$i]['fecha_total']."' and resta is null and cuenta > 0 and dev_base is null and num_cia=".$companias[$j]['num_cia']." and fecha_entrega > '".$fechas[$i]['fecha_total']."'",$dsn);
			
				$pastel=ejecutar_script("select venta_pastel + abono_pastel as pastel from total_panaderias where num_cia=".$companias[$j]['num_cia']." and fecha = '".$fechas[$i]['fecha_total']."'",$dsn);
			
				if($facturas_pago_anticipado){
					$total_anticipadas=number_format($facturas_pago_anticipado[0]["sum"],2,'.','') - number_format($facturas_pago_anticipado_base[0]["sum"],2,'.','');
				}
/*				if($facturas_cuenta_liquida){
					$total_cuenta_liquida=number_format($facturas_cuenta_liquida[0]["sum"],2,'.','');
				}
				if($facturas_cuenta_pago){
					$total_cuenta_pago=number_format($facturas_cuenta_pago[0]["sum"],2,'.','');
				}
*/				if($facturas_posteriores){
					$total_posteriores= number_format($facturas_posteriores[0]["sum"],2,'.','') - number_format($facturas_posteriores_base[0]["sum"],2,'.','');
				}
				
//				$total_facturas=$total_cuenta_liquida + $total_cuenta_pago;
				
				$total_pasteles= $total_anticipadas - $total_posteriores;

//----------------------	

					
				$sql="SELECT * from total_panaderias where num_cia=".$companias[$j]['num_cia']." and fecha='".$fechas[$i]['fecha_total']."'";
				$efectivos_tabla=ejecutar_script($sql,$dsn);
					
				//PRODUCCION INCLUYE TOTAL PRODUCCION
				$sql="SELECT distinct(numcia), fecha_total, (select sum(total_produccion) from total_produccion where numcia=".$companias[$j]['num_cia']." and fecha_total = '".$fechas[$i]['fecha_total']."') as total
				FROM total_produccion WHERE numcia=".$companias[$j]['num_cia']." and fecha_total = '".$fechas[$i]['fecha_total']."'";
				$produccion=ejecutar_script($sql,$dsn);
	
				//Obtiene datos de los expendios
				$sql1="SELECT distinct(num_cia), fecha, (select sum(pan_p_venta) from mov_expendios where num_cia=".$companias[$j]['num_cia']." and fecha = '".$fechas[$i]['fecha_total']."') as reparto, (select sum(devolucion) from mov_expendios where num_cia=".$companias[$j]['num_cia']." and 
				fecha = '".$fechas[$i]['fecha_total']."') as devuelto
				FROM mov_expendios WHERE num_cia=".$companias[$j]['num_cia']." and fecha = '".$fechas[$i]['fecha_total']."'";
				$expendios=ejecutar_script($sql1,$dsn);
			
				//Obtiene el importe del pan comprado
				//AQUI MODIFIQUE GASTOS
				$sql2="SELECT distinct(num_cia), fecha, (select sum(importe) from movimiento_gastos where num_cia=".$companias[$j]['num_cia']." and codgastos=5  and codgastos not in(30,33) and captura=false and fecha ='".$fechas[$i]['fecha_total']."') as importe
				FROM movimiento_gastos
				WHERE num_cia=".$companias[$j]['num_cia']." and fecha = '".$fechas[$i]['fecha_total']."'";
				$desc=ejecutar_script($sql2,$dsn);

				//pan comprado sin descuento
				$sql3="SELECT distinct(num_cia), fecha, (select sum(importe) from movimiento_gastos where num_cia=".$companias[$j]['num_cia']." and codgastos=152  and codgastos not in(30,33) and captura=false and fecha ='".$fechas[$i]['fecha_total']."') as importe
				FROM movimiento_gastos
				WHERE num_cia=".$companias[$j]['num_cia']." and fecha = '".$fechas[$i]['fecha_total']."'";
				$desc1=ejecutar_script($sql3,$dsn);

				
				$efe="select num_cia, fecha, venta_pta, desc_pastel, pastillaje, otros from captura_efectivos where num_cia=".$companias[$j]['num_cia']." and fecha ='".$fechas[$i]['fecha_total']."'";
				$efectivos=ejecutar_script($efe,$dsn);
		
				$porc_pastel = obtener_registro("porcentaje_pan_comprado",array("num_cia"),array($companias[$j]['num_cia']),"","",$dsn);
				
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

				$venta_puerta=$efectivos_tabla[0]['venta_puerta'] + $total_pasteles;
				
				//CALCULO DE TOTAL DE PAN
				$total_pan=$prod+$descuento1+$aux_sobrante_ayer;
				//SOBRANTE DE HOY
//				$sobrante_hoy = $total_pan - $efectivos_tabla[0]['venta_puerta'] - $expendios[0]['reparto']-$quebrado-$efectivos[0]['desc_pastel'];
				$sobrante_hoy = $total_pan - $venta_puerta - $expendios[0]['reparto']-$quebrado-$efectivos[0]['desc_pastel'];
				//DIFERENCIA
				if(existe_registro("prueba_pan",array("num_cia","fecha"),array($companias[$j]['num_cia'],$fechas[$i]['fecha_total']),$dsn)){
					$diferencia=$pan_contado[0]['importe']-$sobrante_hoy;
					}
				else
					$diferencia=0;

				$total_gastos += $efectivos_tabla[0]['gastos'];
				$total_abono += $efectivos_tabla[0]['abono'];
//				$total_efectivos +=$efectivos_tabla[0]['venta_puerta'] + $efectivos_tabla[0]['pastillaje'] + $efectivos_tabla[0]['otros']-$efectivos_tabla[0]['raya_pagada'];
				$total_efectivos +=$venta_puerta + $efectivos_tabla[0]['pastillaje'] + $efectivos_tabla[0]['otros']-$efectivos_tabla[0]['raya_pagada'];
		
				$total_produccion += $prod;
				$total_pan_comprado += $descuento1;
//				$total_venta_pta += $efectivos_tabla[0]['venta_puerta'];
				$total_venta_pta += $venta_puerta;
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