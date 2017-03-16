<?php
// CAPTURA DE FACTURAS DE PASTEL EN USO
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos, revisa bien la compañia";

$session = new sessionclass($dsn);
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body","./plantillas/pan/pan_rfa_cap1.tpl");
$tpl->prepare();
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
$tpl->assign("tabla","venta_pastel");


if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

//*********************************************************************************************
//	SE OBTIENE LA COMPAÑÍA 
$compania = obtener_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia']),"","",$dsn);
//**********************************************************************************************
$tpl->assign("num_cia1",$_POST['num_cia']);
$tpl->assign("fecha1",$_POST['fecha']);
$tpl->assign("nombre_cia",$compania[0]['nombre_corto']);

//	SI EXISTEN VARIABLES DE SESION SE LIMPIAN Y SE GENERAN NUEVAS
if (isset($_SESSION['fac_pas'])) unset($_SESSION['fac_pas']);
$_SESSION['fac_pas']['num_cia'] = $_POST['num_cia'];
$_SESSION['fac_pas']['fecha']   = $_POST['fecha'];
$_SESSION['fac_pas']['fecha_oculta'] = $_POST['fecha_oculta'];


//EL SIGUIENTE ARREGLO ESTA CONFORMADO POR LOS SIGUIENTES POSICIONES O INDICES
//POSICION  0 : CONTIENE LA LETRA DE LA NOTA DE PASTEL
//POSICION  1 : CONTIENE EL NUMERO DE LA NOTA
//POSICION  2 : VALOR DE 0 PARA NOTA BRINCADA VALOR DE 1 PARA NOTA NO BRINCADA
//POSICION  3 : REFERENTE AL TIPO DE FACTURA:
//					0 CONTROL AMARILLO
//					1 CONTROL VERDE
//					2 CONTROL AZUL
//POSICION  4 : INDICE ASIGNADO PARA NOTAS REPETIDAS EN LA MISMA PANTALLA DE CAPTURA :0 PARA REPETIDA Y 1 PARA NO REPETIDA


//CODIGO QUE REVISA LAS FACTURAS BRINCADAS***************************************************************
$arreglo=array();
for ($i=0;$i<65;$i++){
	if($_POST['num_remi'.$i]!="" ){
		if($_POST['let_remi'.$i]=="")
			$letra1='X';
		else
			$letra1=$_POST['let_remi'.$i];
		$arreglo[$i][0] = strtoupper($letra1);
		$arreglo[$i][1] = $_POST['num_remi'.$i];
		$arreglo[$i][2] = -1;
		$arreglo[$i][3] = -1;
		$arreglo[$i][4] = -1;
	}
}
//	SE ORDENA EL ARREGLO
sort($arreglo);

//	ENTRA AL ARREGLO DE NUMEROS DE FACTURA ORDENADOS
for($i=0;$i<count($arreglo);$i++){
	$bloc=ejecutar_script("select * from bloc where folio_inicio <= ".$arreglo[$i][1]." and folio_final >=".$arreglo[$i][1]." and idcia=".$_POST['num_cia']." and let_folio='".$arreglo[$i][0]."'",$dsn);
	if(!$bloc){
		$arreglo[$i][2]=0;
		continue;
	}
	//RECORRE EL BLOC ENCONTRADO
	for($j=$bloc[0]['folio_inicio']; $j<=$bloc[0]['folio_final']; $j++){
		if($j==$arreglo[$i][1]){
			$arreglo[$i][2]=1;
			break;
		}

		elseif(existe_registro("venta_pastel",array("letra_folio","num_remi","num_cia"),array($arreglo[$i][0],$j,$_POST['num_cia']),$dsn)){
			continue;
		}
		else{
			if($i-1 >= 0){
				if(($arreglo[$i][1])-1 == $arreglo[$i-1][1] and $arreglo[$i][0]==$arreglo[$i-1][0]){
					if($arreglo[$i-1][2]==1){
						$arreglo[$i][2]=1;
						break;
					}
					else{
						$arreglo[$i][2]=0;
						break;
					}
				}
				else{
					$arreglo[$i][2]=0;
					break;
				}
			}
			else{
				$arreglo[$i][2]=0;
				break;
			}
		}
	}
}

//*******************************************************************************************************
//	VARIABLES QUE ALMACENAN LOS TOTALES Y LOS MUESTRAN EN PANTALLA
$total_dev_base=0;
$total_base=0;
$total_vta_pta=0;
$total_ab_exp=0;
$total_ab_base=0;
$total_ab_base=0;
//	VARIABLES DE BANDERA, CON ESTAS BANDERAS ACTIVADAS TODAS EN VALOR VERDADERO SE PODRAN GUARDAR LAS FACTURAS, SI NO
//	SE DETALLARA EL ERROR

$ok_total=true;
$valida_bloc=false;
$valida_factura=false;
//**********************************************************************************************
$i=0;
$var=0;
//TIPO DE FACTURA
// 0 - CONTROL AMARILLO
// 1 - CONTROL VERDE
// 2 - CONTROL AZUL
$tipo_factura=(-1); 
//print_r($arreglo);

//SE REVISARA EL TIPO DE FACTURA PARA INGRESARLO EN EL ARREGLO
for($i=0;$i<65;$i++){
	if($_POST['num_remi'.$i] == "") continue;

	if($_POST['let_remi'.$i] == "") $letra='X';
	else $letra=strtoupper($_POST['let_remi'.$i]);
	
	for($t=0;$t<count($arreglo);$t++){
		if($arreglo[$t][0]==$letra and $arreglo[$t][1]==$_POST['num_remi'.$i] and $arreglo[$t][3] == (-1)){
			break;
		}
	}
	
	if($_POST['fecha_entrega'.$i]!=""){
		//VALIDACION DE QUE UNICAMENTE SEA CONTROL AMARILLO----------------	
		if($_POST['dev_base'.$i]!="" or $_POST['resta'.$i]!="" or $_POST['cuenta'.$i]=="")
			$arreglo[$t][3] = -1;
		elseif($_POST['kilos'.$i]!="" and $_POST['precio_unidad'.$i] =="")
			$arreglo[$t][3] = -1;
		elseif($_POST['kilos'.$i]=="" and $_POST['otros'.$i]=="")
			$arreglo[$t][3] = -1;
		else
			$arreglo[$t][3] = 0;
	}

	elseif($_POST['resta'.$i]!=""){
		if($_POST['kilos'.$i]!="" or $_POST['precio_unidad'.$i]!="" or $_POST['otros'.$i]!="" or $_POST['cuenta'.$i]!="" or $_POST['base'.$i]!="" or $_POST['dev_base'.$i]!="" or $_POST['fecha_entrega'.$i]!="" or $_POST['pastillaje'.$i] or $_POST['otros_efectivos'.$i])
			$arreglo[$t][3] = -1;
		else
			$arreglo[$t][3] = 1;
	}

	elseif($_POST['dev_base'.$i]!=""){
		if($_POST['kilos'.$i]!="" or $_POST['precio_unidad'.$i]!="" or $_POST['otros'.$i]!="" or $_POST['cuenta'.$i]!="" or $_POST['base'.$i]!="" or $_POST['resta'.$i]!="" or $_POST['fecha_entrega'.$i]!="" or $_POST['pastillaje'.$i]!="" or $_POST['otros_efectivos'.$i]!="")
			$arreglo[$t][3] = -1;
		else
			$arreglo[$t][3] = 2;
	}
	else
		$arreglo[$t][3] = -1;
}
//print_r($arreglo);


for($i=0;$i<65;$i++){
	$ok=true;
	$valida_factura=true;
	$total_factura=0;
//VARIABLES DE SESION
	$_SESSION['fac_pas']['let_remi'.$i] = $_POST['let_remi'.$i];
	$_SESSION['fac_pas']['num_remi'.$i] = $_POST['num_remi'.$i];
	$_SESSION['fac_pas']['idexpendio'.$i] = $_POST['idexpendio'.$i];
	$_SESSION['fac_pas']['kilos'.$i] = $_POST['kilos'.$i];
	$_SESSION['fac_pas']['precio_unidad'.$i] = $_POST['precio_unidad'.$i];
	$_SESSION['fac_pas']['otros'.$i] = $_POST['otros'.$i];
	$_SESSION['fac_pas']['base'.$i] = $_POST['base'.$i];
	$_SESSION['fac_pas']['cuenta'.$i] = $_POST['cuenta'.$i];
	$_SESSION['fac_pas']['dev_base'.$i] = $_POST['dev_base'.$i];
	$_SESSION['fac_pas']['resta'.$i] = $_POST['resta'.$i];
	$_SESSION['fac_pas']['fecha_entrega'.$i] = $_POST['fecha_entrega'.$i];
	$_SESSION['fac_pas']['pastillaje'.$i] = $_POST['pastillaje'.$i];
	$_SESSION['fac_pas']['otros_efectivos'.$i] = $_POST['otros_efectivos'.$i];
//----------------------------------------------------------------------------------
	if($_POST['num_remi'.$i] == "") continue;//Si no hay número de factura no toma el registro
	
	if($_POST['let_remi'.$i] == "") $letra='X';
	else $letra=strtoupper($_POST['let_remi'.$i]);
	
	
	$tpl->newBlock("rows");
	$tpl->assign("i",$var);
	$cuenta=0;
	$cuenta=number_format($_POST['cuenta'.$i],2,'.','') - number_format($_POST['pastillaje'.$i],2,'.','') - number_format($_POST['otros_efectivos'.$i],2,'.','');
	$cuenta=number_format($cuenta,2,'.','');
	$res=0;

	$tpl->assign("num_cia", $_POST['num_cia']);
	$tpl->assign("fecha", $_POST['fecha']);
	$tpl->assign("let_remi",$letra);
	$tpl->assign("num_remi",$_POST['num_remi'.$i]);
	$tpl->assign("idexpendio",$_POST['idexpendio'.$i]);
	if($_POST['idexpendio'.$i]!=""){
		$nombre_exp=ejecutar_script("select nombre from catalogo_expendios where num_cia=".$_POST['num_cia']." and num_expendio=".$_POST['idexpendio'.$i],$dsn);
		$tpl->assign("expendio",$nombre_exp[0]['nombre']);
	}

	$tpl->assign("kilos", $_POST['kilos'.$i]);
	if($_POST['kilos'.$i]!="")
		$tpl->assign("kilos1",number_format($_POST['kilos'.$i],2,'.',','));
	
	$tpl->assign("precio_unidad", $_POST['precio_unidad'.$i]);
	if($_POST['precio_unidad'.$i]!="")
		$tpl->assign("precio_unidad1",number_format($_POST['precio_unidad'.$i],2,'.',','));

	$tpl->assign("otros", $_POST['otros'.$i]);
	if($_POST['otros'.$i]!="")
		$tpl->assign("otros1",number_format($_POST['otros'.$i],2,'.',','));
	
	$tpl->assign("base", $_POST['base'.$i]);
	if($_POST['base'.$i]!="")
		$tpl->assign("base1",number_format($_POST['base'.$i],2,'.',','));
	
	$tpl->assign("dev_base", $_POST['dev_base'.$i]);
	if($_POST['dev_base'.$i]!="")
		$tpl->assign("dev_base1",number_format($_POST['dev_base'.$i],2,'.',','));
	
	$tpl->assign("cuenta", $cuenta);
	if($_POST['cuenta'.$i]!="")
		$tpl->assign("cuenta1",number_format($cuenta,2,'.',','));
	
	$tpl->assign("pastillaje",$_POST['pastillaje'.$i]);
	if($_POST['pastillaje'.$i]!="")
		$tpl->assign("pastillaje1",number_format($_POST['pastillaje'.$i],2,'.',','));
	
	$tpl->assign("otros_efectivos",$_POST['otros_efectivos'.$i]);
	if($_POST['otros_efectivos'.$i]!="")
		$tpl->assign("otros_efectivos1",number_format($_POST['otros_efectivos'.$i],2,'.',','));

	$tpl->assign("resta", $_POST['resta'.$i]);
	$tpl->assign("fecha_entrega", $_POST['fecha_entrega'.$i]);


//REVISION DE BLOC
	$bloc=ejecutar_script("select * from bloc where folio_inicio <= ".$_POST['num_remi'.$i]." and folio_final >=".$_POST['num_remi'.$i]." and idcia=".$_POST['num_cia']." and let_folio='".$letra."'",$dsn);
	if(!$bloc){
		$valida_bloc=false;
		$ok=false;
	}
	else{
		$valida_bloc=true;
		$tpl->assign("bloc",$bloc[0]['id']);
	}

//---------------
	//VALIDACION DE SALTO
	for($t=0;$t<count($arreglo);$t++){
		if($arreglo[$t][0]==$letra and $arreglo[$t][1]==$_POST['num_remi'.$i] and $arreglo[$t][4] == -1){
			break;
		}
	}
	
	//LA FACTURA ES DE CONTROL AMARILLO**************************************************************************************************************
	if($_POST['fecha_entrega'.$i]!=""){
		//VALIDACION DE QUE UNICAMENTE SEA CONTROL AMARILLO----------------	
		if($_POST['dev_base'.$i]!="" or $_POST['resta'.$i]!="" or $_POST['cuenta'.$i]==""){
			$valida_factura=false;
			$ok=false;
		}
		elseif($_POST['kilos'.$i]!="" and $_POST['precio_unidad'.$i] ==""){
			$valida_factura=false;
			$ok=false;
		}
		elseif($_POST['kilos'.$i]=="" and $_POST['otros'.$i]==""){
			$valida_factura=false;
			$ok=false;
		}
		else{
			$tipo_factura=0;
			$valida_factura=true;
			$tpl->assign("tipo","0");

			//BUSCA SI LA NOTA YA FUE CAPTURADA CON ANTERIORIDAD
			$nota=ejecutar_script("select * from venta_pastel where num_cia=".$_POST['num_cia']." and letra_folio='".$letra."' and num_remi=".$_POST['num_remi'.$i]." and fecha_entrega is not null",$dsn);
			//REVISA SI EL CONTROL AMARILLO ES DE EXPENDIO
			if($_POST['idexpendio'.$i]==""){
				//si existe la nota marca error
				if($nota){
					$valida_factura=false;
					$ok=false;
				}
				else{
					$total_base += $_POST['base'.$i];
					if($_POST['cuenta'.$i] >0){
						$total_vta_pta += $_POST['cuenta'.$i] - $_POST['pastillaje'.$i] - $_POST['otros_efectivos'.$i];
						$total_vta_pta -= $_POST['base'.$i];
					}
					$total_factura = $_POST['kilos'.$i] * $_POST['precio_unidad'.$i] + $_POST['otros'.$i] + $_POST['base'.$i];
					$total_factura=number_format($total_factura,2,'.','');
					$ok=true;
					$valida_factura=true;
					if($total_factura < ($_POST['cuenta'.$i] - number_format($_POST['pastillaje'.$i],2,'.','') -  number_format($_POST['otros_efectivos'.$i],2,'.',''))){
						$ok=false;
						$valida_factura=false;
					}
				}
			}
			else{
				if(!existe_registro("mov_expendios",array("num_cia","fecha"),array($_POST['num_cia'],$_POST['fecha']),$dsn)){
					$valida_factura=false;
					$ok=false;
				}
				elseif($nota){
					$valida_factura=false;
					$ok=false;
				}
				else{
					$porcentaje=obtener_registro("catalogo_expendios",array('num_cia','num_expendio'),array($_POST['num_cia'],$_POST['idexpendio'.$i]),"","",$dsn);
					$total_base += $_POST['base'.$i];
					if($_POST['cuenta'.$i] > 0){
						$v_aux=$_POST['cuenta'.$i] - $_POST['pastillaje'.$i] - $_POST['otros_efectivos'.$i] - $_POST['base'.$i];
						$total_ab_exp += $v_aux;
						$total_ab_exp = $total_ab_exp - $total_ab_exp * ($porcentaje[0]['porciento_ganancia']/100);
					}
					$total_factura = $_POST['kilos'.$i] * $_POST['precio_unidad'.$i] + $_POST['otros'.$i] + $_POST['base'.$i];
					$total_factura=number_format($total_factura,2,'.','');
					$valida_factura=true;
					if($total_factura < $_POST['cuenta'.$i]){
						$ok=false;
						$valida_factura=false;
					}
				}
			}
		}
	}
	//LA FACTURA ES DE CONTROL VERDE *************************************************************************************************************
	elseif($_POST['resta'.$i]!=""){
		if($_POST['kilos'.$i]!="" or $_POST['precio_unidad'.$i]!="" or $_POST['otros'.$i]!="" or $_POST['cuenta'.$i]!="" or $_POST['base'.$i]!="" or $_POST['dev_base'.$i]!="" or $_POST['fecha_entrega'.$i]!="" or $_POST['pastillaje'.$i] or $_POST['otros_efectivos'.$i]){
			$valida_factura=false;
			$ok=false;
		}
		else{
			$tipo_factura=1;
			$tpl->assign("tipo","1");
			$nota=ejecutar_script("select * from venta_pastel where num_cia=".$_POST['num_cia']." and letra_folio='".$letra."' and num_remi=".$_POST['num_remi'.$i]." and fecha_entrega is not null",$dsn);
			if(!$nota){
				$valida_factura=false;
				$ok=false;
			}
			else{
				if($_POST['idexpendio'.$i]==""){ //FACTURA NO ES DE EXPENDIO
					if($nota[0]['idexpendio']!=""){// VALIDA SI LA NOTA GUARDADA ES DE EXPENDIO, SI ES DE EXPENDIO MARCA ERROR
						$valida_factura=false;
						$ok=false;
					}
					else{
						//CASO EN QUE NO SE DEJO DINERO A CUENTA, Y QUEDA PENDIENTE EL PAGO DE LA BASE
						if($nota[0]['cuenta'] == 0 and $nota[0]['base'] != ""){
							if(($_POST['resta'.$i] <= $nota[0]['resta_pagar']) and $nota[0]['estado'] == 0){
								$total_vta_pta += ($_POST['resta'.$i] - $nota[0]['base']);
								$total_base += $nota[0]['base'];
							}
							else{
								$valida_factura=false;
								$ok=false;
							}
						}
						else{
							if(($_POST['resta'.$i] <= $nota[0]['resta_pagar']) and $nota[0]['estado'] == 0){
								$total_vta_pta += $_POST['resta'.$i];
							}
							else{
								$valida_factura=false;
								$ok=false;
							}
						}
					}
				}
				else{//LA FACTURA ES DE EXPENDIO
					if(!existe_registro("mov_expendios",array("num_cia","fecha"),array($_POST['num_cia'],$_POST['fecha']),$dsn)){
						$valida_factura=false;
						$ok=false;
					}
					elseif($nota[0]['idexpendio']==""){//SI NO ENCUENTRA EXPENDIO EN LA FACTURA GUARDADA, ENCONCES MARCA ERROR
						$valida_factura=false;
						$ok=false;
					}
					else{
						$porcentaje=obtener_registro("catalogo_expendios",array('num_cia','num_expendio'),array($_POST['num_cia'],$_POST['idexpendio'.$i]),"","",$dsn);
						//CASO EN QUE NO SE DEJO DINERO A CUENTA, Y QUEDA PENDIENTE EL PAGO DE LA BASE
						if($nota[0]['cuenta']==0 and $nota[0]['base']!=""){
							if($_POST['resta'.$i] <= $nota[0]['resta_pagar']){
								$total_ab_exp += $_POST['resta'.$i] /*-  $nota[0]['base']*/;
								$total_ab_exp = $total_ab_exp - $total_ab_exp * ($porcentaje[0]['porciento_ganancia']/100);
							}
							else{
								$valida_factura=false;
								$ok=false;
							}
						}
						else{
							if($_POST['resta'.$i] <= $nota[0]['resta_pagar']){
								$total_ab_exp += $_POST['resta'.$i] -  $_POST['resta'.$i] * ($porcentaje[0]['porciento_ganancia']/100);
							}
							else{
								$valida_factura=false;
								$ok=false;
							}
						}
					}
				}
			}
		}
	}
	//LA FACTURA ES DE CONTROL AZUL ****************************************************************************************************
	elseif($_POST['dev_base'.$i]!=""){
		if($_POST['kilos'.$i]!="" or $_POST['precio_unidad'.$i]!="" or $_POST['otros'.$i]!="" or $_POST['cuenta'.$i]!="" or $_POST['base'.$i]!="" or $_POST['resta'.$i]!="" or $_POST['fecha_entrega'.$i]!="" or $_POST['pastillaje'.$i]!="" or $_POST['otros_efectivos'.$i]!=""){
			$valida_factura=false;
			$ok=false;
		}
		else{
			$tipo_factura=2;
			$tpl->assign("tipo","2");
			$valida_bloc=true;
			$arreglo[$t][2]=1;
			$nota=ejecutar_script("select * from venta_pastel where num_cia=".$_POST['num_cia']." and letra_folio='".$letra."' and num_remi=".$_POST['num_remi'.$i]." and fecha_entrega is not null",$dsn);
			if(!$nota){
				$valida_factura=false;
				$ok=false;
			}
			else{
				$nota_dev=ejecutar_script("select * from venta_pastel where num_cia=".$_POST['num_cia']." and letra_folio='".$letra."' and num_remi=".$_POST['num_remi'.$i]." and dev_base is not null",$dsn);
				if($nota_dev){
					$valida_factura=false;
					$ok=false;
				}
				else{
					if($_POST['dev_base'.$i] == $nota[0]['base']){
						$valida_factura=true;
						$total_dev_base += number_format($_POST['dev_base'.$i],2,'.','');
						
					}
					else
						$valida_factura=false;
				}
			}
		}
	}
	else
		$valida_factura=false;
	
//BUSQUEDA DE FACTURAS REPETIDAS EN LA PANTALLA DE CAPTURA
	if( ($t-1) >=0){
		if($arreglo[$t][0] == $arreglo[$t-1][0] and $arreglo[$t][1] == $arreglo[$t-1][1]){
			if($arreglo[$t][3]==$arreglo[$t-1][3]){
				$arreglo[$t][4]=0;
				$ok=false;
			}
			else
				$arreglo[$t][4]=1;
		}
		else
			$arreglo[$t][4]=1;
	}
	else
		$arreglo[$t][4]=1;

	if ($valida_bloc == true and $valida_factura == true and $arreglo[$t][2]==1 and $arreglo[$t][4]==1){
		$tpl->newBlock("rows_ok");
		$tpl->assign("let_remi1",strtoupper($_POST['let_remi'.$i]));
		$tpl->assign("num_remi",$_POST['num_remi'.$i]);
		$tpl->gotoBlock("rows");
		$tpl->assign("total",number_format($total_factura,2,'.',''));
		
		
		if($tipo_factura==0){
			$tpl->assign("total_factura",number_format($total_factura,2,'.',','));
			$tpl->newBlock("resta2");
			$tpl->assign("i",$var);
			$resta_mostrar=$total_factura - $cuenta;
			$tpl->assign("resta2",number_format($resta_mostrar,2,'.',','));
			$tpl->assign("resta3",$resta_mostrar);
			$tpl->gotoBlock("rows");
		}
		elseif($tipo_factura==1){
			$tpl->newBlock("resta1");
			$tpl->assign("i",$var);
			$tpl->assign("resta1",number_format($_POST['resta'.$i],2,'.',','));
			$tpl->gotoBlock("rows");
		}
		elseif($tipo_factura==2){
			$tpl->newBlock("resta1");
			$tpl->assign("i",$var);
			$tpl->assign("resta1","");
			$tpl->gotoBlock("rows");
		}
		$ok=true;
	}
	else{
		if($valida_bloc==false and $valida_factura==true){
			$tpl->newBlock("rows_error_bloc");
			$tpl->assign("let_remi1",strtoupper($_POST['let_remi'.$i]));
			$tpl->assign("num_remi",$_POST['num_remi'.$i]);
			$tpl->gotoBlock("rows");
			if($_POST['resta'.$i]!=""){
				$tpl->newBlock("resta1");
				$tpl->assign("i",$var);
				$tpl->assign("resta1",number_format($_POST['resta'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
			}
			
		}
		elseif($valida_bloc==false and $valida_factura==false){
			$tpl->newBlock("rows_error_bloc");
			$tpl->assign("let_remi1",strtoupper($_POST['let_remi'.$i]));
			$tpl->assign("num_remi",$_POST['num_remi'.$i]);
			$tpl->gotoBlock("rows");
			if($_POST['resta'.$i]!=""){
				$tpl->newBlock("resta1");
				$tpl->assign("i",$var);
				$tpl->assign("resta1",number_format($_POST['resta'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
			}
			
		}
		else if($valida_bloc==true and $valida_factura==false){
			$tpl->newBlock("rows_error_nota");
			$tpl->assign("let_remi1",strtoupper($_POST['let_remi'.$i]));
			$tpl->assign("num_remi",$_POST['num_remi'.$i]);
			$tpl->gotoBlock("rows");
			if($_POST['resta'.$i]!=""){
				$tpl->newBlock("resta1");
				$tpl->assign("i",$var);
				$tpl->assign("resta1",number_format($_POST['resta'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
			}
			
		}
		else if($valida_bloc==true and $valida_factura==true and $arreglo[$t][2]==0){
			$ok=false;
			$tpl->newBlock("rows_error_brincada");
			$tpl->assign("let_remi1",strtoupper($_POST['let_remi'.$i]));
			$tpl->assign("num_remi",$_POST['num_remi'.$i]);
			$tpl->gotoBlock("rows");
			if($_POST['resta'.$i]!=""){
				$tpl->newBlock("resta1");
				$tpl->assign("i",$var);
				$tpl->assign("resta1",number_format($_POST['resta'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
			}
		}
		else if(/*$valida_bloc==true and $valida_factura==true and */$arreglo[$t][4]==0) {
			$ok=false;
			$tpl->newBlock("rows_error_repetida");
			$tpl->assign("let_remi1",strtoupper($_POST['let_remi'.$i]));
			$tpl->assign("num_remi",$_POST['num_remi'.$i]);
			$tpl->gotoBlock("rows");
			if($_POST['resta'.$i]!=""){
				$tpl->newBlock("resta1");
				$tpl->assign("i",$var);
				$tpl->assign("resta1",number_format($_POST['resta'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
			}
			
		}
		else{
			$tpl->newBlock("rows_error_nota");
			$tpl->assign("let_remi1",strtoupper($_POST['let_remi'.$i]));
			$tpl->assign("num_remi",$_POST['num_remi'.$i]);
			$tpl->gotoBlock("rows");
			if($_POST['resta'.$i]!=""){
				$tpl->newBlock("resta1");
				$tpl->assign("i",$var);
				$tpl->assign("resta1",number_format($_POST['resta'.$i],2,'.',','));
				$tpl->gotoBlock("rows");
			}
		}
	}
	$ok_total &= $ok;
	$var++;
}

if ($ok_total == true){
	$tpl->newBlock("totales");
	$tpl->assign("total_vta_pta", number_format($total_vta_pta,2,'.',','));
	$tpl->assign("total_ab_exp",number_format($total_ab_exp,2,'.',','));
	$tpl->assign("total_base",number_format($total_base,2,'.',','));
	$tpl->assign("total_dev_base",number_format($total_dev_base,2,'.',','));
	$tpl->newBlock("continuar");
}

// Imprimir el resultado
$tpl->printToScreen();
?>