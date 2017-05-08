<?php
// AQUI VA EL NOMBRE DE LA CAPTURA
// Tabla 'captura_efectivos'
// Menu 'Nombre del menu->Nombre del submenu'

define ('IDSCREEN',1322); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No existe numero de compañia";
$descripcion_error[2] = "No se efectuo ninguna captura";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");


// Seleccionar tabla
$tpl->assign("tabla",$session->tabla);

// Asignar valores a los campos del formulario
// EJEMPLO.:
//$tpl->assign("num_cia",$result->num_cia);

// Si viene de una página que genero error

//**********************************************************************************
//ACTIVAR EL SEGMENTO CUANDO SE HAGAN CAMBIOS EN LA PAGINA, SOLO ACCESARA EL ADMINISTRADOR
/*
if($_SESSION['iduser']!=1){
	header("location:./mantenimiento.php");
	die();
}
*/
//**********************************************************************************


if(!isset($_GET['fecha'])){
	$tpl->newBlock("obtener_datos");
	$tpl->assign("dia",date("d"));
	$tpl->assign("mes",date("m"));
	$tpl->assign("anio_actual",date("Y"));


	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	}
	
	if(isset($_SESSION['efm'])){
		$limite=(count($_SESSION['efm']) -1) / 13;
		$tpl->assign("fecha",$_SESSION['efm']['fecha']);
	}
//	echo $limite;
	for ($i=0;$i<15;$i++) {
		$tpl->newBlock("rows");
		$tpl->assign("i",$i);
		if($i+1 == 15)
			$tpl->assign("next","0");
		else
			$tpl->assign("next",$i+1);
		
		if($i > 0)
			$tpl->assign("ant",$i-1);
		else
			$tpl->assign("ant","14");
		if(isset($_SESSION['efm'])){
			if($i<$limite){
				$tpl->assign("num_cia",$_SESSION['efm']['num_cia'.$i]);
				$tpl->assign("am",$_SESSION['efm']['am'.$i]);
				$tpl->assign("am_error",$_SESSION['efm']['am_error'.$i]);
				$tpl->assign("pm",$_SESSION['efm']['pm'.$i]);
				$tpl->assign("pm_error",$_SESSION['efm']['pm_error'.$i]);
				$tpl->assign("pastel",$_SESSION['efm']['pastel'.$i]);
				$tpl->assign("venta_pta",$_SESSION['efm']['venta_pta'.$i]);
				$tpl->assign("pastillaje",$_SESSION['efm']['pastillaje'.$i]);
				$tpl->assign("otros",$_SESSION['efm']['otros'.$i]);
				$tpl->assign("ctes",$_SESSION['efm']['ctes'.$i]);
				$tpl->assign("corte1",$_SESSION['efm']['corte1'.$i]);
				$tpl->assign("corte2",$_SESSION['efm']['corte2'.$i]);
				$tpl->assign("desc_pastel",$_SESSION['efm']['desc_pastel'.$i]);
			}
		}
		$tpl->gotoBlock("_ROOT");
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}

	$tpl->printToScreen();
	die();
}

if(isset($_SESSION['efm'])) unset($_SESSION['efm']);
$tpl->newBlock("movimientos");

$arreglo=array();
$var_aux=0;
for ($i=0;$i<15;$i++){
	if($_GET['num_cia'.$i]!="" ){
		$arreglo[$i][0]=$_GET['num_cia'.$i];
		$arreglo[$i][1]=$var_aux;
		$var_aux++;
	}
}
//	SE ORDENA EL ARREGLO
sort($arreglo);
//print_r($arreglo);
//echo "registros: ".count($arreglo);

//echo $arreglo[0][0];

$nombremes[1]="Enero";
$nombremes[2]="Febrero";
$nombremes[3]="Marzo";
$nombremes[4]="Abril";
$nombremes[5]="Mayo";
$nombremes[6]="Junio";
$nombremes[7]="Julio";
$nombremes[8]="Agosto";
$nombremes[9]="Septiembre";
$nombremes[10]="Octubre";
$nombremes[11]="Noviembre";
$nombremes[12]="Diciembre";
$_fecha=explode("/",$_GET['fecha']);
$tpl->assign("fecha",$_fecha[0]." DE ".strtoupper($nombremes[$_fecha[1]])." DEL ".$_fecha[2]);

if (($_fecha[0] == 10 && $_fecha[1] == 5) || 
($_fecha[0] == 30 && $_fecha[1] == 4) || 
($_fecha[0] == 12 && $_fecha[1] == 12) || 
($_fecha[0] == 24 && $_fecha[1] == 12) || 
($_fecha[0] == 26 && $_fecha[1] == 12) || 
($_fecha[0] == 31 && $_fecha[1] == 12) ||
($_fecha[0] == 2 && $_fecha[1] == 1) || 
($_fecha[0] == 5 && $_fecha[1] == 1) || 
($_fecha[0] == 6 && $_fecha[1] == 1)) {
	$pro1 = 0.75;
	$pro2 = 3.00;
}
else if (($_fecha[0] == 25 && $_fecha[1] == 12) || ($_fecha[0] == 1 && $_fecha[1] == 1)) {
	$pro1 = 0;
	$pro2 = 10.00;
}
else {
	$pro1 = 0.75;
	$pro2 = 1.25;
}

$var=0;
$_SESSION['efm']['fecha']=$_GET['fecha'];
$tpl->assign("fecha",$_GET['fecha']);
$ok=true;
for($i=0;$i<15;$i++){
	if($_GET['num_cia'.$i]!=""){
		$tpl->newBlock("rows1");
		$tpl->assign("i",$var);
		$tpl->assign("num_cia",$_GET['num_cia'.$i]);
		$tpl->assign("am",number_format($_GET['am'.$i],2,'.',''));
		$tpl->assign("am_error",number_format($_GET['am_error'.$i],2,'.',''));
		$tpl->assign("pm",number_format($_GET['pm'.$i],2,'.',''));
		$tpl->assign("pm_error",number_format($_GET['pm_error'.$i],2,'.',''));
		$tpl->assign("pastel",number_format($_GET['pastel'.$i],2,'.',''));
		$tpl->assign("venta_pta",number_format($_GET['venta_pta'.$i],2,'.',''));
		$tpl->assign("pastillaje",number_format($_GET['pastillaje'.$i],2,'.',''));
		$tpl->assign("otros",number_format($_GET['otros'.$i],2,'.',''));
		$tpl->assign("ctes",number_format($_GET['ctes'.$i],0,'',''));
		$tpl->assign("corte1",number_format($_GET['corte1'.$i],0,'',''));
		$tpl->assign("corte2",number_format($_GET['corte2'.$i],0,'',''));
		$tpl->assign("desc_pastel",number_format($_GET['desc_pastel'.$i],2,'.',''));
		
		$_SESSION['efm']['num_cia'.$var]=$_GET['num_cia'.$i];
		$_SESSION['efm']['am'.$var]=$_GET['am'.$i];
		$_SESSION['efm']['am_error'.$var]=$_GET['am_error'.$i];
		$_SESSION['efm']['pm'.$var]=$_GET['pm'.$i];
		$_SESSION['efm']['pm_error'.$var]=$_GET['pm_error'.$i];
		$_SESSION['efm']['pastel'.$var]=$_GET['pastel'.$i];
		$_SESSION['efm']['venta_pta'.$var]=$_GET['venta_pta'.$i];
		$_SESSION['efm']['pastillaje'.$var]=$_GET['pastillaje'.$i];
		$_SESSION['efm']['otros'.$var]=$_GET['otros'.$i];
		$_SESSION['efm']['ctes'.$var]=$_GET['ctes'.$i];
		$_SESSION['efm']['corte1'.$var]=$_GET['corte1'.$i];
		$_SESSION['efm']['corte2'.$var]=$_GET['corte2'.$i];
		$_SESSION['efm']['desc_pastel'.$var]=$_GET['desc_pastel'.$i];

		$cia=obtener_registro("catalogo_companias",array('num_cia'),array($_GET['num_cia'.$i]),"","",$dsn);
		$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
		$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
		$tpl->assign("am1",number_format($_GET['am'.$i],2,'.',','));
		$tpl->assign("am_error1",number_format($_GET['am_error'.$i],2,'.',','));
		$tpl->assign("pm1",number_format($_GET['pm'.$i],2,'.',','));
		$tpl->assign("pm_error1",number_format($_GET['pm_error'.$i],2,'.',','));
		$tpl->assign("pastel1",number_format($_GET['pastel'.$i],2,'.',','));
		$tpl->assign("venta_pta1",number_format($_GET['venta_pta'.$i],2,'.',','));
		$tpl->assign("pastillaje1",number_format($_GET['pastillaje'.$i],2,'.',','));
		$tpl->assign("otros1",number_format($_GET['otros'.$i],2,'.',','));
		$tpl->assign("ctes1",number_format($_GET['ctes'.$i],0,'',''));
		$tpl->assign("corte11",number_format($_GET['corte1'.$i],0,'',''));
		$tpl->assign("corte21",number_format($_GET['corte2'.$i],0,'',''));
		$tpl->assign("desc_pastel1",number_format($_GET['desc_pastel'.$i],2,'.',','));

		$var++;		

		$repetida=0;
//-----------------------REVISA DUPLICADOS AL MOMENTO DE INSERTAR LAS COMPAÑIAS
		for($j=0;$j<count($arreglo);$j++){
			if($_GET['num_cia'.$i]==$arreglo[$j][0]){
//				if($arreglo[$j][1] > 0){
				if(($j - 1) >= 0){
					if($arreglo[$j-1][0]==$_GET['num_cia'.$i]){
						$repetida=1;
						break;
					}
				}
			}
		}

		//CORRECCION DE ERRORES
		$sql="select num_cia,idoperadora FROM catalogo_companias WHERE num_cia=".$_GET['num_cia'.$i];
		$id=ejecutar_script($sql,$dsn);
		
		
		if($_SESSION['iduser']==1 or $_SESSION['iduser']==4){
			$fecha1 = $_fecha[0] < 4 ? date("d/m/Y",mktime(0,0,0,$_fecha[1]-1,1,$_fecha[2])) : date("d/m/Y",mktime(0,0,0,$_fecha[1],1,$_fecha[2]));
			$fecha2 = $_fecha[0] < 4 ? date("d/m/Y",mktime(0,0,0,$_fecha[1],0,$_fecha[2])) : date("d/m/Y",mktime(0,0,0,$_fecha[1],$_fecha[0]-1,$_fecha[2]));
			
			$prom_clientes = ejecutar_script("SELECT avg(ctes) FROM captura_efectivos WHERE num_cia=".$_GET['num_cia'.$i]." AND fecha BETWEEN '$fecha1' AND '$fecha2'",$dsn);
			
			if(existe_registro("captura_efectivos",array('num_cia','fecha'),array($_GET['num_cia'.$i],$_GET['fecha']),$dsn)){
				$ok &= false;
				$tpl->newBlock("cia_error");
				$tpl->assign("color","FF99CC");
				$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
			}
			else if($repetida==1){
				$ok &= false;
				$tpl->newBlock("cia_error");
				$tpl->assign("color","0000FF");
				$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
			}
			/*else if (($_GET['ctes'.$i] < $prom_clientes[0]['avg']*$pro1 || $_GET['ctes'.$i] > $prom_clientes[0]['avg']*$pro2) && $_GET['num_cia'.$i] != 702 && $_GET['num_cia'.$i] != 703) {
				$ok &= false;
				$tpl->newBlock("cia_error");
				$tpl->assign("color","FF3399");
				$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
			}*/
			else if ($_GET['ctes'.$i] <= 0) {
				$ok &= false;
				$tpl->newBlock("cia_error");
				$tpl->assign("color","FF3399");
				$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
			}
			else{
				if($_GET['desc_pastel'.$i]!=""){
					if(existe_registro("venta_pastel",array("num_cia","fecha"),array($_GET['num_cia'.$i],$_GET['fecha']),$dsn)){
						$pastel=ejecutar_script("select * from total_panaderias where num_cia=".$_GET['num_cia'.$i]." and fecha='".$_GET['fecha']."'",$dsn);
						$total_pastel=number_format($pastel[0]['venta_pastel'],2,'.','') + number_format($pastel[0]['abono_pastel'],2,'.','');
						if($_GET['desc_pastel'.$i] > ($total_pastel * 0.20)){
							if($_GET['desc_pastel'.$i] > 1000){
								$ok &= false;
								$tpl->newBlock("cia_error");
								$tpl->assign("color","FFCC99");
								$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
							}
							else{
								$tpl->newBlock("cia_ok");
								$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
								$ok &=true;
							}
						}
						else{
							$tpl->newBlock("cia_ok");
							$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
							$ok &=true;
						}
					}
					else{
						if($_GET['desc_pastel'.$i] > 1000){
							$ok &= false;
							$tpl->newBlock("cia_error");
							$tpl->assign("color","FFCC99");
							$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
						}
						else{
							$tpl->newBlock("cia_ok");
							$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
							$ok &=true;
						}
					}
				}
				else{
					$tpl->newBlock("cia_ok");
					$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
					$ok &=true;
				}
			}
		}

		else{
			$operadora=obtener_registro("catalogo_operadoras",array("iduser"),array($_SESSION['iduser']),"","",$dsn);
			$fecha1 = $_fecha[0] < 4 ? date("d/m/Y",mktime(0,0,0,$_fecha[1]-1,1,$_fecha[2])) : date("d/m/Y",mktime(0,0,0,$_fecha[1],1,$_fecha[2]));
			$fecha2 = $_fecha[0] < 4 ? date("d/m/Y",mktime(0,0,0,$_fecha[1],0,$_fecha[2])) : date("d/m/Y",mktime(0,0,0,$_fecha[1],$_fecha[0]-1,$_fecha[2]));
			
			$prom_clientes = ejecutar_script("SELECT avg(ctes) FROM captura_efectivos WHERE num_cia=".$_GET['num_cia'.$i]." AND fecha BETWEEN '$fecha1' AND '$fecha2'",$dsn);
			if($id[0]['idoperadora']!=$operadora[0]['idoperadora']){
				$ok &=false;
				$tpl->newBlock("cia_error");
				$tpl->assign("color","FFFF00");
				$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
			}
			else if(existe_registro("captura_efectivos",array('num_cia','fecha'),array($_GET['num_cia'.$i],$_GET['fecha']),$dsn)){
				$ok &=false;
				$tpl->newBlock("cia_error");
				$tpl->assign("color","FF99CC");
				$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
			}
			else if($repetida==1){
				$ok &= false;
				$tpl->newBlock("cia_error");
				$tpl->assign("color","0000FF");
				$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
			}
			else if (($_GET['ctes'.$i] < $prom_clientes[0]['avg']*$pro1 || $_GET['ctes'.$i] > $prom_clientes[0]['avg']*$pro2) && $_GET['num_cia'.$i] != 702 && $_GET['num_cia'.$i] != 703) {
				$ok &= false;
				$tpl->newBlock("cia_error");
				$tpl->assign("color","FF3399");
				$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
			}
			else if ($_GET['ctes'.$i] <= 0) {
				$ok &= false;
				$tpl->newBlock("cia_error");
				$tpl->assign("color","FF3399");
				$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
			}
			else{
/*				if($_GET['num_cia'.$i]==72){
					$tpl->newBlock("cia_ok");
					$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
					$ok &=true;
				}
				elseif($_GET['desc_pastel'.$i]!=""){
					if(existe_registro("venta_pastel",array("num_cia","fecha"),array($_GET['num_cia'.$i],$_GET['fecha']),$dsn)){
						$pastel=ejecutar_script("select * from total_panaderias where num_cia=".$_GET['num_cia'.$i]." and fecha='".$_GET['fecha']."'",$dsn);
						$total_pastel=number_format($pastel[0]['venta_pastel'],2,'.','') + number_format($pastel[0]['abono_pastel'],2,'.','');
						if($_GET['desc_pastel'.$i] > ($total_pastel * 0.20)){
							if($_GET['desc_pastel'.$i] > 1000){
								if($_GET['desc_pastel'.$i] <= 2000 and ($_GET['num_cia'.$i]==41 or $_GET['num_cia'.$i]==65)){
									$tpl->newBlock("cia_ok");
									$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
									$ok &=true;
								}
								else{							
									$ok &= false;
									$tpl->newBlock("cia_error");
									$tpl->assign("color","FFCC99");
									$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
								}
							}
							else{
								$tpl->newBlock("cia_ok");
								$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
								$ok &=true;
							}
						}
						else{
							$tpl->newBlock("cia_ok");
							$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
							$ok &=true;
						}
					}
					else{
						if($_GET['desc_pastel'.$i] > 1000){
							if($_GET['desc_pastel'.$i] <= 2000 and ($_GET['num_cia'.$i]==41 or $_GET['num_cia'.$i]==65)){
								$tpl->newBlock("cia_ok");
								$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
								$ok &=true;
							}
							else{							
								$ok &= false;
								$tpl->newBlock("cia_error");
								$tpl->assign("color","FFCC99");
								$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
							}
						}
						else{
							$tpl->newBlock("cia_ok");
							$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
							$ok &=true;
						}
					}
				}
				else{
*/					$tpl->newBlock("cia_ok");
					$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
					$ok &=true;
//				}
			}
		}
	}
}

$tpl->gotoBlock("movimientos");
$tpl->assign("cont",$var);

if($ok==false)
	$tpl->assign("disabled","disabled");
//print_r($_SESSION['efm']);

//echo count($_SESSION['efm']);

$tpl->printToScreen();
?>
