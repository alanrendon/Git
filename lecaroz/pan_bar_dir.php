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
$tpl->assignInclude("body","./plantillas/pan/pan_bar_dir.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");



// Seleccionar tabla
if(!isset($_GET['fecha'])){
	$tpl->newBlock("obtener_datos");
	$tpl->assign("dia",date("d"));
	$tpl->assign("mes",date("m"));
	$tpl->assign("anio_actual",date("Y"));
	
	if(isset($_SESSION['bar_dir'])){
		$limite=(count($_SESSION['bar_dir'])-1)/ 2;
		$tpl->assign("fecha",$_SESSION['bar_dir']['fecha']);
	}


	for ($i=0; $i<20; $i++) {
		$tpl->newBlock("rows");
		$tpl->assign("i",$i);
		$tpl->assign("next",$i+1);
		
		if(isset($_SESSION['bar_dir'])){
			if($i<$limite){
				$tpl->assign("num_cia",$_SESSION['bar_dir']["num_cia".$i]);
				$tpl->assign("importe",$_SESSION['bar_dir']["importe".$i]);
			}
		}
	}

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
// Imprimir el resultado
$tpl->newBlock("barredura");
$tpl->assign("fecha",$_GET['fecha']);
$total=0;
$aux=0;
$ok=true;
if(isset($_SESSION['bar_dir'])) unset($_SESSION['bar_dir']);

$_SESSION['bar_dir']['fecha']=$_GET['fecha'];
for($i=0;$i<20;$i++)
{
	if($_GET['num_cia'.$i]!="" and $_GET['importe'.$i]!=""){
		if(existe_registro("catalogo_companias",array('num_cia'),array($_GET['num_cia'.$i]),$dsn)){
			$cia=obtener_registro("catalogo_companias",array('num_cia'),array($_GET['num_cia'.$i]),"","",$dsn);
			$tpl->newBlock("registros");
			$tpl->assign("i",$aux);
			$tpl->assign("num_cia",$_GET['num_cia'.$i]);
			$tpl->assign("nombre",$cia[0]['nombre_corto']);
			$tpl->assign("importe",number_format($_GET['importe'.$i],2,'.',''));
			$tpl->assign("importe1",number_format($_GET['importe'.$i],2,'.',','));
			$_SESSION['bar_dir']['num_cia'.$aux]=$_GET['num_cia'.$i];
			$_SESSION['bar_dir']['importe'.$aux]=number_format($_GET['importe'.$i],2,'.','');
			$total+=number_format($_GET['importe'.$i],2,'.','');
			$aux++;
			
			$sql="select num_cia,idoperadora FROM catalogo_companias WHERE num_cia=".$_GET['num_cia'.$i];
			$id=ejecutar_script($sql,$dsn);
			
			if($_SESSION['iduser']==1 or $_SESSION['iduser']==4){
				if(existe_registro("barredura",array("num_cia","fecha_cap"),array($_GET['num_cia'.$i],$_GET['fecha']),$dsn)){
					$ok &=false;
					$tpl->newBlock("cia_error");
					$tpl->assign("color","FF99CC");
					$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
				}
				else{
					$ok &=true;
					$tpl->newBlock("cia_ok");
					$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
				}
			}
			
			else{
				$operadora=obtener_registro("catalogo_operadoras",array("iduser"),array($_SESSION['iduser']),"","",$dsn);
				if($id[0]['idoperadora']!=$operadora[0]['idoperadora']){
					$ok &=false;
					$tpl->newBlock("cia_error");
					$tpl->assign("color","FFFF00");
					$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
				}
				else if(existe_registro("barredura",array("num_cia","fecha_cap"),array($_GET['num_cia'.$i],$_GET['fecha']),$dsn)){
					$ok &=false;
					$tpl->newBlock("cia_error");
					$tpl->assign("color","FF99CC");
					$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
				}
				else{
					$tpl->newBlock("cia_ok");
					$tpl->assign("num_cia1",$_GET['num_cia'.$i]);
					$ok &=true;
				}
			}
		}
	}
}
$tpl->gotoBlock("barredura");
$tpl->assign("contador",$aux);
$tpl->assign("total_importe",number_format($total,2,'.',','));

if($ok==false)
	$tpl->assign("disabled","disabled");

$tpl->printToScreen();
?>