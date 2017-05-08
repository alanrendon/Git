<?php
// CAPTURA DE FACTURAS DE PASTEL (DESUSO)
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos, revisa bien la compañia";
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
$tpl->assignInclude("body","./plantillas/pan/pan_rfa_cap1.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
//Debe de capturar primero los movimientos a expendios y los efectivos
// Seleccionar tabla
$tpl->assign("tabla","venta_pastel");


//*********************************************************************************************
//	SE OBTIENE LA COMPAÑÍA JUNTO CON SUS BLOCS
$compania = obtener_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia']),"","",$dsn);
$blocs= obtener_registro("bloc",array("idcia"),array($_POST['num_cia']),"folio_inicio","",$dsn);
$numbloc = count($blocs);
//**********************************************************************************************
//	VARIABLES QUE ALMACENAN LOS TOTALES Y LOS MUESTRAN EN PANTALLA
$total_dev_base=0;
$total_base=0;
$total_vta_pta=0;
$total_ab_exp=0;
$total_ab_base=0;
$total_ab_base=0;
//	VARIABLES DE BANDERA, CON ESTAS BANDERAS ACTIVADAS TODAS EN VALOR VERDADERO SE PODRAN GUARDAR LAS FACTURAS, SI NO
//	SE DETALLARA EL ERROR
$ok=true;
$ok_total=true;
$valida_bloc=false;
$valida_factura=false;
//**********************************************************************************************

$i=0;
$var=0;
$tpl->assign("num_cia1",$_POST['num_cia']);
$tpl->assign("fecha1",$_POST['fecha']);
$tpl->assign("nombre_cia",$compania[0]['nombre_corto']);


//**********************************************************************************************
//	EN ESTE BLOQUE SE GENERAN LAS VARIABLES DE SESION, SI EXISTEN VARIABLES LAS BORRA Y LAS GENERA DE NUEVO
//	HACIENDO BARRIDO DE LA PANTALLA DE CAPTURA DE NOTAS DE PASTEL
if (isset($_SESSION['fac_pas'])) unset($_SESSION['fac_pas']);
$_SESSION['fac_pas']['num_cia'] = $_POST['num_cia'];
$_SESSION['fac_pas']['fecha']   = $_POST['fecha'];
$_SESSION['fac_pas']['fecha_oculta'] = $_POST['fecha_oculta'];
for ($i=0; $i<65; $i++) {
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
}
//print_r($_POST);

//**********************************************************************************************
//	EN ESTE BLOQUE SE VAN A REVISAR LAS FACTURAS QUE SEAN BRINCADAS
//	SE GENERA UN ARREGLO VACIO QUE SE VA LLENANDO CON LAS NOTAS DE PASTEL DE LA PANTALLA DE CAPTURA
//	EL ARREGLO ES BIDIMENSIONAL Y CONTIENE LA LETRA DE LA FACTURA Y EL NUMERO DE REMISION
$arreglo=array();
for ($i=0;$i<65;$i++){
	if($_POST['num_remi'.$i]!="" ){
		if($_POST['let_remi'.$i]=="")
			$letra1='X';
		else
			$letra1=$_POST['let_remi'.$i];
		$arreglo[$i][0]=strtoupper($letra1);
		$arreglo[$i][1]=$_POST['num_remi'.$i];
	}
}
//	SE ORDENA EL ARREGLO
sort($arreglo);
//print_r($arreglo);
//	ESTAS VARIABLES SIRVEN DE PUNTEROS PARA RECORRER EL ARREGLO ORDENADO
//		VARIABLES DE FACTURAS ENTRANTES
$aux_fac_ent = -1;
$aux_let_ent = -1;
$aux_fac_ent1 = -1;
$aux_let_ent1 = -1;
//	VARIABLES DE BLOC
$aux_fac_bloc = -1;
$aux_let_bloc = -1;
$aux_fac_bloc1 = -1;
$aux_let_bloc1 = -1;
$aux_bloc_id = -1;
$auxiliar = -1;

//	ENTRA AL ARREGLO DE NUMEROS DE FACTURA ORDENADOS
for($i=0;$i<count($arreglo);$i++){
	$aux_fac_ent=$arreglo[$i][1];
	$aux_let_ent=$arreglo[$i][0];
	$bloc_encontrado=false;
	
//	echo "factura ".$arreglo[$i][0]." ".$arreglo[$i][1]." <br>";
	
//	REVISA LOS BLOCKS PARA ENCONTRAR EL CORRESPONDIENTE A LA FACTURA
	for($j=0;$j<count($blocs);$j++){
	
//	REALIZA LA COMPARACION DE LA LETRA Y NUMERO DE LA NOTA CON EL INTERVALO DE NOTAS QUE SE ENCUENTRAN EN EL BLOC
//	SI ENCUENTRA EL BLOC ENTRA AL BLOQUE DEL IF
		if (($arreglo[$i][1] >= $blocs[$j]['folio_inicio']) and ($arreglo[$i][1] <= $blocs[$j]['folio_final']) and ($arreglo[$i][0]==$blocs[$j]['let_folio']))
		{
			$aux_let_bloc=$blocs[$j]['let_folio'];
			$bloc_encontrado=true;
//	VARIABLE AUXILIAR QUE CONTIENE EL VALOR CONSECUTIVO SIGUIENTE DEL INICIO DEL FOLIO DEL BLOC
			$auxiliar=$blocs[$j]['folio_inicio'];
//			$auxiliar++;
//			echo "YA ENCONTRE EL BLOCK Y ES EL QUE VA DEL ".$blocs[$j]['let_folio']." ".$blocs[$j]['folio_inicio']." AL ".$blocs[$j]['let_folio']." ".$blocs[$j]['folio_final']." <br>";


//	RECORRE EL BLOCK QUE FUE ENCONTRADO
//	SE ASIGNA A UNA VARIABLE EL FOLIO INICIAL DEL BLOC PARA RECORRELO
			for($z=$blocs[$j]['folio_inicio'];$z<=$blocs[$j]['folio_final'];$z++){
//				echo "factura block $z<br>";
//echo "pibote $z ; nota ".$arreglo[$i][1]."<br>";				
				$aux_fac_bloc=$z;

//	AQUI REVISA SI LA FACTURA YA EXISTE DENTRO DE LA TABLA DE VENTA PASTEL, SI EXISTE ENTONCES ROMPE EL CICLO Y AL 
//	ELEMENTO DEL ARREGLO LE ASIGNA EL VALOR DE 1, LO QUE SIGNIFICA QUE LA NOTA NO ESTA SALTADA
//	ESTE SE OCUPA PARA CONTROLES AZUL Y VERDE PORQUE SI YA EXISTE UN REGISTRO QUIERE DECIR QUE YA SE CAPTURO EL CONTROL AMARILLO
				if(existe_registro("venta_pastel",array("num_cia","num_remi","letra_folio"),array($_POST['num_cia'],$arreglo[$i][1],$arreglo[$i][0]),$dsn)){
					$arreglo[$i][2]="1";
//					$aux_fac_bloc=$z;
					break;
/*
SE INSERTARA UNA NUEVA MODIFICACION AQUI
*/
				}
//	VA REVISANDO LAS NOTAS CONSECUTIVAS DENTRO DEL BLOC, SI LA ENCUENTRA ENTONCES CONTINUA CON EL SIGUIENTE FOLIO
				else if(existe_registro("venta_pastel",array("num_cia","num_remi","letra_folio"),array($_POST['num_cia'],$z,$arreglo[$i][0]),$dsn)){
					continue;
				}
				else{
//	SE HACE LA REVISION SI LA NOTA DEL ARREGLO ORDENADO ES MAYOR A LA VARIABLE QUE CONTIENE EL FOLIO DEL BLOC, SE RECUERDA QUE ESTA VARIABLE
//	VA CAMBIANDO CON LOS VALORES DE LOS FOLIOS DEL BLOC

					$aux_fac_ent=$aux_fac_bloc;
					if($arreglo[$i][1] > $z){
						$auxiliar++;
						if($aux_fac_bloc==$aux_fac_ent1){
							$valor=$arreglo[$i][1];
							$valor--;
							if($aux_fac_ent1==$valor and @$arreglo[$i-1][2]==1){
								$arreglo[$i][2]=1;
								break;
							}
							else{
								$arreglo[$i][2]=0;
								break;
							}
						}
						else if($auxiliar > $arreglo[$i][1]){
							$arreglo[$i][2]=0;
							$aux_fac_bloc=$z;
							break;
						}
						else{
							$aux_fac_bloc=$z;
							$aux_fac_ent1=$aux_fac_ent;
							continue;
						}
					}
					else if($z==$arreglo[$i][1]){
						$arreglo[$i][2]=1;
						$aux_fac_bloc=$z;
						break;
					}
					else{
						$arreglo[$i][2]=0;
						$aux_fac_bloc=$z;
						break;
					}
				}
			$aux_fac_ent1=$aux_fac_ent;

			}
			continue;
		}
	}
	$aux_fac_ent1=$aux_fac_ent;
//	$aux_let_ent1=$aux_let_ent;
	if($bloc_encontrado==false){
		$arreglo[$i][2]=0;
	}
}

//print_r($arreglo);

for ($i=0;$i<65;$i++)
{
	if ($_POST['num_remi'.$i] != "")//Si no hay número de factura no toma el registro
	{
		$total_factura=0;
		if($_POST['let_remi'.$i]=="")
			$letra='X';
		else
			$letra=strtoupper($_POST['let_remi'.$i]);
	//Búsqueda del block al que pertenece la factura, debe estar el estado como falso
		for ($j=0; $j<$numbloc; $j++)
		{
			//busqueda del bloc teniendo en cuenta que cumple con le rango de folios y debe estar en estado falso
			if (($_POST['num_remi'.$i] >= $blocs[$j]['folio_inicio']) and ($_POST['num_remi'.$i] <= $blocs[$j]['folio_final']) and ($letra==$blocs[$j]['let_folio']))
			{
				$tpl->assign("idencontrado",$blocs[$j]['id']);
				//Se encontro el bloc y se va a determinar el estado del mismo
				if($blocs[$j]['estado'] == "f")
				{
					//el bloc cumple con las validaciones
					$valida_bloc=true;
					$valida_factura=true;
					$ok=true;
					break;
				}
				else
				{
					//el bloc no cumple con las validaciones
					$valida_bloc=false;
//					echo "NO VALIDO LA FACTURA POR CULPA DEL BLOC <BR>";
					$valida_factura=false;
					$ok = false;
					break;				
				}
			}
			else 
			{
				//el bloc no cumple con las validaciones
				$valida_bloc=false;
				$valida_factura=false;
//				echo "NO VALIDO LA FACTURA POR CULPA DEL BLOC <BR>";
				$ok = false;
			}
		}
	//verificacion de facturas en la base de datos para encontrar facturas duplicadas o que tengan control azul o verde
//verifica si la factura ya existe
		if (existe_registro("venta_pastel", array("num_remi","num_cia","letra_folio"), array($_POST['num_remi'.$i],$_POST['num_cia'],$letra), $dsn))
			{
//			La factura ya existe dentro de la base, ahora se tiene que especificar el tipo de factura a entrar
			$facturas = obtener_registro("venta_pastel",array("num_cia","num_remi","letra_folio"),array($_POST['num_cia'],$_POST['num_remi'.$i],$letra),"id","",$dsn);
				for ($k=0; $k<count($facturas); $k++)
				{
					//revision de facturas que tengan control verde
					//Factura con Resta y la resta debe ser igual a resta a pagar
					if($_POST['resta'.$i] != "" and $facturas[$k]['resta'] == "" and $facturas[$k]['dev_base'] == "" and $_POST['dev_base'.$i] == "" and $_POST['resta'.$i] == $facturas[$k]['resta_pagar']) 
					{
//						Revisa si es una factura que afecta a expendios o a los efectivos
						if($_POST['idexpendio'.$i]!="") {
							if($facturas[$k]['cuenta']==0 and $facturas[$k]['base']!="" and $facturas[$k]['resta_pagar']==$facturas[$k]['total_factura'])
							{
								$porcentaje=obtener_registro("catalogo_expendios",array('num_cia','num_expendio'),array($_POST['num_cia'.$i],$_POST['idexpendio'.$i]),"","",$dsn);
								$total_ab_exp += $_POST['resta'.$i] -  $facturas[$k]['base'];
								$total_ab_exp = $total_ab_exp - $total_ab_exp * ($porcentaje[0]['porciento_ganancia']/100);
								$total_base += $facturas[$k]['base'];
							}
							else
								$total_ab_exp += $_POST['resta'.$i]; 
						}
						else 
							$total_vta_pta+=$_POST['resta'.$i];
						
						$valida_factura=true;
						$ok=true;
						break;
					}
					//revision de factura si es de control azul
					//la factura  tanto en la base como la capturada, no tiene que tener valor en resta, la devolucion de la base debe ser igual a la base en la factura ya capturada
					else if ($_POST['resta'.$i] == "" and $facturas[$k]['resta'] == "" and $_POST['dev_base'.$i] !="" and $facturas[$k]['dev_base'] == "" and ($_POST['dev_base'.$i] == $facturas[$k]['base']))
					{
//						echo "entre a control azul";
						$valida_factura=true;
						$total_dev_base += $_POST['dev_base'.$i];
						$ok=true;
						if($valida_bloc==false) $valida_bloc=true;
						break;
					}
					else
					{
						//no cumplio ninguna condicion, se toma como error
						$valida_factura=false;
//						echo "NO VALIDO LA FACTURA PORQUE NO ENTRA EN NINGUN CASO <BR>";
						$ok = false;
					}
				}
			$nombre_exp="";
			}
				//si la factura no existe tiene que validar que solo sea de control amarillo (pastel o pan)
			else 
			{
//				sin número de expendio, con fecha de entrega, numero de remision, y deja dinero a cuenta
				if ($_POST['idexpendio'.$i] == "" and $_POST['fecha_entrega'.$i] != "" and $_POST['num_remi'.$i] != "" and $_POST['cuenta'.$i] != "")
				{	
					$valida_factura=true;
					$total_base += $_POST['base'.$i];
					$total_vta_pta += $_POST['cuenta'.$i] - $_POST['pastillaje'.$i] - $_POST['otros_efectivos'.$i];
					$total_vta_pta -= $_POST['base'.$i];
					$total_factura = $_POST['kilos'.$i] * $_POST['precio_unidad'.$i] + $_POST['otros'.$i] + $_POST['base'.$i];
					$total_factura=number_format($total_factura,2,'.','');
					$ok=true;
					$nombre_exp="";
				}

//************************************** N O T A  D E  E X P E N D I O
//				con número de expendio, con fecha de entrega, numero de remision, y deja dinero a cuenta
				else if	($_POST['idexpendio'.$i] !="" and $_POST['fecha_entrega'.$i] != "" and $_POST['num_remi'.$i] != "" and $_POST['cuenta'.$i] != "")
				{
					$porcentaje=obtener_registro("catalogo_expendios",array('num_cia','num_expendio'),array($_POST['num_cia'],$_POST['idexpendio'.$i]),"","",$dsn);

					if(!$porcentaje){
						$nombre_exp="EXPENDIO NO ENCONTRADO";
					}
					else{
						$nombre_exp = $porcentaje[0]['nombre'];
					}
					
					$valida_factura=true;
					if($_POST['cuenta'.$i]>0){
						$total_base += $_POST['base'.$i];
						$v_aux=$_POST['cuenta'.$i] - $_POST['pastillaje'.$i] - $_POST['otros_efectivos'.$i] - $_POST['base'.$i];
						$total_ab_exp += $v_aux;
						$total_ab_exp = $total_ab_exp - $total_ab_exp * ($porcentaje[0]['porciento_ganancia']/100);
//						$total_ab_exp -= $_POST['base'.$i];
					}
					$total_factura = $_POST['kilos'.$i] * $_POST['precio_unidad'.$i] + $_POST['otros'.$i] + $_POST['base'.$i];
					$total_factura=number_format($total_factura,2,'.','');
					$ok=true;
				}
				else 
				{
					$valida_factura=false;
					$ok = false;
				}
			}
	//-----------------------------------------------------TERMINA BLOQUE A DEPURAR-----------------------------------------------		
			$tpl->newBlock("rows");
			$tpl->assign("i",$var);
			$cuenta=0;
			$cuenta=number_format($_POST['cuenta'.$i],2,'.','') - number_format($_POST['pastillaje'.$i],2,'.','') - number_format($_POST['otros_efectivos'.$i],2,'.','');
			$cuenta=number_format($cuenta,2,'.','');
			$res=0;
			$tpl->assign("idexpendio",$_POST['idexpendio'.$i]);
			$tpl->assign("expendio",$nombre_exp);
			$tpl->assign("kilos", $_POST['kilos'.$i]);
			$tpl->assign("kilos1",number_format($_POST['kilos'.$i],2,'.',','));
			$tpl->assign("precio_unidad", $_POST['precio_unidad'.$i]);
			$tpl->assign("precio_unidad1",number_format($_POST['precio_unidad'.$i],2,'.',','));
			$tpl->assign("otros", $_POST['otros'.$i]);
			$tpl->assign("otros1",number_format($_POST['otros'.$i],2,'.',','));
			$tpl->assign("base", $_POST['base'.$i]);
			$tpl->assign("base1",number_format($_POST['base'.$i],2,'.',','));
			$tpl->assign("dev_base", $_POST['dev_base'.$i]);
			$tpl->assign("dev_base1",number_format($_POST['dev_base'.$i],2,'.',','));
			$tpl->assign("cuenta", $cuenta);
			$tpl->assign("cuenta1",number_format($cuenta,2,'.',','));
			$tpl->assign("resta", $_POST['resta'.$i]);
			$tpl->assign("num_cia", $_POST['num_cia']);
			$tpl->assign("fecha", $_POST['fecha']);
			$tpl->assign("let_remi",$letra);
			$tpl->assign("num_remi",$_POST['num_remi'.$i]);
			$tpl->assign("fecha_entrega", $_POST['fecha_entrega'.$i]);
			$tpl->assign("pastillaje",$_POST['pastillaje'.$i]);
			$tpl->assign("otros_efectivos",$_POST['otros_efectivos'.$i]);
			$tpl->assign("pastillaje1",number_format($_POST['pastillaje'.$i],2,'.',','));
			$tpl->assign("otros_efectivos1",number_format($_POST['otros_efectivos'.$i],2,'.',','));
			
			for($t=0;$t<count($arreglo);$t++){
				if($arreglo[$t][0]==$letra and $arreglo[$t][1]==$_POST['num_remi'.$i]){
//					$indice=$t;
					break;
				}
			}
//echo "t = $t<br>";
			$arreglo[$t][2]=1;
/*			
			if($_SESSION['iduser']==4 or $_SESSION['iduser']==1)
				$arreglo[$t][2]=1;
//estas lineas se van a quita				
			if($_POST['num_cia']==49 or $_POST['num_cia']==34 or $_POST['num_cia']==69 or $_POST['num_cia']==68)
				$arreglo[$t][2]=1;

			if($_POST['resta'.$i] == "" and $_POST['dev_base'.$i] !="" and $valida_bloc==true and $valida_factura==true)
				$arreglo[$t][2]=1;
*/
			if ($valida_bloc == true and $valida_factura == true and $arreglo[$t][2]==1)
			{
				$tpl->newBlock("rows_ok");
				$tpl->assign("let_remi1",strtoupper($_POST['let_remi'.$i]));
				$tpl->assign("num_remi",$_POST['num_remi'.$i]);
				$tpl->gotoBlock("rows");
				$tpl->assign("total",number_format($total_factura,2,'.',''));
				$tpl->assign("total_factura",number_format($total_factura,2,'.',','));
				
				if($_POST['resta'.$i] !="" and $_POST['fecha_entrega'.$i] =="" or $_POST['dev_base'.$i] !="")
				{
					$tpl->newBlock("resta1");
					$tpl->assign("i",$var);
					$tpl->assign("resta1",number_format($_POST['resta'.$i],2,'.',','));
					$tpl->gotoBlock("rows");
				}
				else
				{
				$tpl->newBlock("resta2");
				$tpl->assign("i",$var);
				$resta_mostrar=$total_factura - $cuenta /*+ number_format($_POST['pastillaje'.$i],2,'.','')+number_format($_POST['pastillaje'.$i],2,'.','')*/;
				$tpl->assign("resta2",number_format($resta_mostrar,2,'.',','));
				$tpl->assign("resta3",$resta_mostrar);
				$tpl->gotoBlock("rows");
				}
				$ok=true;
			}
			else
			{
//				echo "bandera block $valida_bloc   bandera factura $valida_factura   estado de la factura ".$arreglo[$t][2]."<br>";
				
				if($valida_bloc==false and $valida_factura==true and $arreglo[$t][2]==0){
					$tpl->newBlock("rows_error_bloc");
					$tpl->assign("let_remi1",strtoupper($_POST['let_remi'.$i]));
					$tpl->assign("num_remi",$_POST['num_remi'.$i]);
					$tpl->gotoBlock("rows");

				}
				else if($valida_bloc==false and $valida_factura==true and $arreglo[$t][2]==1){
					$tpl->newBlock("rows_error_bloc");
					$tpl->assign("let_remi1",strtoupper($_POST['let_remi'.$i]));
					$tpl->assign("num_remi",$_POST['num_remi'.$i]);
					$tpl->gotoBlock("rows");
				}
				else if($valida_bloc==true and $valida_factura==false and $arreglo[$t][2]==0){
					$tpl->newBlock("rows_error_nota");
					$tpl->assign("let_remi1",strtoupper($_POST['let_remi'.$i]));
					$tpl->assign("num_remi",$_POST['num_remi'.$i]);
					$tpl->gotoBlock("rows");
				}
				else if($valida_bloc==true and $valida_factura==true and $arreglo[$t][2]==0){
					$tpl->newBlock("rows_error_brincada");
					$tpl->assign("let_remi1",strtoupper($_POST['let_remi'.$i]));
					$tpl->assign("num_remi",$_POST['num_remi'.$i]);
					$tpl->gotoBlock("rows");
				}
				else{
					$tpl->newBlock("rows_error_nota");
					$tpl->assign("let_remi1",strtoupper($_POST['let_remi'.$i]));
					$tpl->assign("num_remi",$_POST['num_remi'.$i]);
					$tpl->gotoBlock("rows");
				}
				$ok=false;
			}
			$ok_total &= $ok;
			$var++;
		}
}
if ($ok_total == true)
		{
			$tpl->newBlock("totales");
			$tpl->assign("total_vta_pta", number_format($total_vta_pta,2,'.',','));
			$tpl->assign("total_ab_exp",number_format($total_ab_exp,2,'.',','));
			$tpl->assign("total_base",number_format($total_base,2,'.',','));
			$tpl->assign("total_dev_base",number_format($total_dev_base,2,'.',','));
			$tpl->newBlock("continuar");
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

// Imprimir el resultado
$tpl->printToScreen();
?>