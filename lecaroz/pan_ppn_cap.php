<?php
// CAPTURA PARA AFECTIVOS DIRECTOS
// TABLA "IMPORTE_EFECTIVOS"
// PANADERIAS -- EFECTIVOS -- CAPTURA DIRECTA
//define ('IDSCREEN',1321); // ID de pantalla
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe, por favor revisalo";
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
$tpl->assignInclude("body","./plantillas/pan/pan_ppn_cap.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// Seleccionar tabla
if (!isset($_GET['fecha'])) {
	$tpl->newBlock("obtener_datos");
//	$tpl->assign("tabla",$session->tabla);
	$tpl->assign("dia",date("d"));
	$tpl->assign("mes",date("m"));
	$tpl->assign("anio_actual",date("Y"));
	
	$fecha_anterior=date("j/n/Y",mktime(0,0,0,date("m"),date("d") - 1,date("Y")));
	$tpl->assign("fecha",$fecha_anterior);
	
	if(isset($_SESSION['ppn'])){
		$tpl->assign("fecha",$_SESSION['ppn']['fecha']);
		$limite=(count($_SESSION['ppn'])-1)/2;
	}
	
	
	for ($i=0; $i<15; $i++) {
		$tpl->newBlock("rows1");
		$tpl->assign("i",$i);
		$tpl->assign("next",$i+1);
		if(isset($_SESSION['ppn'])){
			if($i<$limite){
				$tpl->assign("num_cia",$_SESSION['ppn']['num_cia'.$i]);
				$tpl->assign("importe",$_SESSION['ppn']['importe'.$i]);
			}
		}
		
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
//---------------------------------------------------------
$tpl->newBlock("prueba_pan");
if(isset($_SESSION['ppn'])) unset($_SESSION['ppn']);
$_SESSION['ppn']['fecha']=$_GET['fecha'];
$var=0;
$ok=true;
$tpl->assign("fecha",$_GET['fecha']);
//$fecha="1/".date("m")."/".date("Y");
$fech=explode("/",$_GET['fecha']);
$fecha='1/'.$fech[1].'/'.$fech[2];

$diasxmes[1] = 31; // Enero
if ($fech[2]%4 == 0)
	$diasxmes[2] = 29; // Febrero año bisiesto
else
	$diasxmes[2] = 28; // Febrero
$diasxmes[3] = 31; // Marzo
$diasxmes[4] = 30; // Abril
$diasxmes[5] = 31; // Mayo
$diasxmes[6] = 30; // Junio
$diasxmes[7] = 31; // Julio
$diasxmes[8] = 31; // Agosto
$diasxmes[9] = 30; // Septiembre
$diasxmes[10] = 31; // Octubre
$diasxmes[11] = 30; // Noviembre
$diasxmes[12] = 31; // Diciembre



$fecha_inicio_anterior= date("d/m/Y",mktime(0,0,0,$fech[1]-1,1,$fech[2]));
$fecha_fin_anterior =date("d/m/Y",mktime(0,0,0,$fech[1]-1,$diasxmes[$fech[1]],$fech[2]));
//echo $diasxmes[$fech[0]];
//echo $fecha_inicio_anterior ."<br>";
//echo $fecha_fin_anterior;
$ok=true;
$ok1=true;

$_d1 = explode("/", $_GET['fecha']);
   $d1 = $_d1[0];
for ($i=0; $i<15; $i++) {
	if($_GET['num_cia'.$i] != "")
	{
		$tpl->newBlock("rows");
		$tpl->assign("i",$var);
		$_SESSION['ppn']['num_cia'.$var]=$_GET['num_cia'.$i];
		$_SESSION['ppn']['importe'.$var]=number_format($_GET['importe'.$i],2,'.','');
		
		if($fech[0]==1)
			$sql="select sum(total_produccion) / ".$diasxmes[$fech[0]]." as sum from total_produccion where numcia = ".$_GET['num_cia'.$i]." and fecha_total between '".$fecha_inicio_anterior."' and '".$fecha_fin_anterior."'";
		else
			$sql="select sum(total_produccion) / ".$d1." as sum from total_produccion where numcia = ".$_GET['num_cia'.$i]." and fecha_total between '".$fecha."' and '".$_GET['fecha']."'";
		$prod=ejecutar_script($sql,$dsn);
		$tpl->assign("produccion",$prod[0]['sum']);
		$tpl->assign("produccion1",number_format($prod[0]['sum'],2,'.',','));
		$var++;
		$tpl->assign("num_cia",$_GET['num_cia'.$i]);
		$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia'.$i]),"","",$dsn);
		$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
		$tpl->assign("importe",$_GET['importe'.$i]);
		$tpl->assign("importe1",number_format($_GET['importe'.$i],2,'.',','));
		$limite=$prod[0]['sum'] / 2;
		if ($_GET['importe'.$i] > $limite){
			$tpl->newBlock("aviso_error");
			$tpl->assign("aviso","EL IMPORTE ES MAYOR AL 50% DE LA PRODUCCION");
			$ok=false;
			}
		else 
		{
			$tpl->newBlock("aviso_ok");
			$tpl->assign("aviso","Importe correcto");
		}
//******************		
		$sql="select num_cia,idoperadora FROM catalogo_companias WHERE num_cia=".$_GET['num_cia'.$i];
		$id=ejecutar_script($sql,$dsn);
		
		if($_SESSION['iduser']==1 or $_SESSION['iduser']==4){
			if(existe_registro("prueba_pan",array("num_cia","fecha"),array($_GET['num_cia'.$i],$_GET['fecha']),$dsn)){
//				$ok1 &=false;
				$ok1 &=true;
				//SE QUITO LA RESTRICCION DE QUE SI EXISTE EL REGISTRO MARQUE ERROR, AHORA SE IMPONDRA EL NUEVO REGISTRO 12/05/2005
				$tpl->newBlock("cia_error");
				$tpl->assign("color","FF99CC");
				$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
			}
			else{
				$ok1 &=true;
				$tpl->newBlock("cia_ok");
				$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
			}
		}
		
		else{
			$operadora=obtener_registro("catalogo_operadoras",array("iduser"),array($_SESSION['iduser']),"","",$dsn);
			if($id[0]['idoperadora']!=$operadora[0]['idoperadora']){
				$ok1 &=false;
				$tpl->newBlock("cia_error");
				$tpl->assign("color","FFFF00");
				$tpl->assign("num_cia1",$_GET['num_cia'].$i);
			}
			else if(existe_registro("prueba_pan",array("num_cia","fecha"),array($_GET['num_cia'.$i],$_GET['fecha']),$dsn)){
//				$ok1 &=false;
				//SE QUITO LA RESTRICCION DE QUE SI EXISTE EL REGISTRO MARQUE ERROR, AHORA SE IMPONDRA EL NUEVO REGISTRO 12/05/2005
				$ok1 &=true;
				$tpl->newBlock("cia_error");
				$tpl->assign("color","FF99CC");
				$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
			}
			else{
				$tpl->newBlock("cia_ok");
				$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
				$ok1 &=true;
			}
		}
//******************		
	}

}
$tpl->gotoBlock("prueba_pan");
$tpl->assign("cont",$var);
if($ok==true)
	$tpl->assign("bandera","1");
else if ($ok==false)
	$tpl->assign("bandera","0");

if($ok1==false)	
	$tpl->assign("disabled","disabled");

$tpl->printToScreen();
?>