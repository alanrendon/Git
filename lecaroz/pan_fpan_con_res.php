<?php
// CONTROL DE BLOCKS
// Tabla 'BLOCKS'
// Menu

//define ('IDSCREEN',1620); //ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No se encontraron blocks para la compañía";
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
$tpl->assignInclude("body","./plantillas/pan/pan_fpan_con.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
// Generar listado de turnos
// Si viene de una página que genero error
if(!isset($_GET['operadora']))
{
	$tpl->newBlock("obtener_datos");
	$sql="select * from catalogo_operadoras where iduser=".$_SESSION['iduser'];
	$operadora=ejecutar_script($sql,$dsn);
	
	if($operadora){
		$tpl->assign("opera",$operadora[0]['idoperadora']);
		$tpl->newBlock("lista");
		$sql="select num_cia, nombre_corto, idoperadora from catalogo_companias where idoperadora=".$operadora[0]['idoperadora']." and num_cia <101 order by num_cia";
		$cias=ejecutar_script($sql,$dsn);
		
		for($i=0;$i<count($cias);$i++){
			$tpl->newBlock("cias");
			$tpl->assign("num_cia",$cias[$i]['num_cia']);
			$tpl->assign("nom_cia",$cias[$i]['nombre_corto']);
		}
		$tpl->gotoBlock("obtener_datos");
	}
	else{
//		$tpl->assign("disabled","disabled");
		if($_SESSION['iduser']==1 or $_SESSION['iduser']==4 or $_SESSION['iduser']==19){
			$tpl->newBlock("lista");
			$sql="select num_cia, nombre_corto, idoperadora from catalogo_companias where num_cia <101 order by num_cia";
			$cias=ejecutar_script($sql,$dsn);
			
			for($i=0;$i<count($cias);$i++){
				$tpl->newBlock("cias");
				$tpl->assign("num_cia",$cias[$i]['num_cia']);
				$tpl->assign("nom_cia",$cias[$i]['nombre_corto']);
			}
			
			$tpl->newBlock("mensaje");
			$tpl->assign("mensaje","USUARIO ADMINISTRADOR");
			$tpl->gotoBlock("obtener_datos");
		}
		else{
			$tpl->newBlock("mensaje");
			$tpl->assign("mensaje","NO TIENES ACCESO A ESTA INFORMACIÓN");
			$tpl->gotoBlock("obtener_datos");
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
$sql="select * from bloc where idcia=".$_GET['cias']." AND estado=false and folios_usados > 0 order by let_folio,folio_inicio";
$blocks=ejecutar_script($sql,$dsn);

if(!$blocks){
	header("location: ./pan_fpan_con.php?codigo_error=1");
	die();
}

$tpl->newBlock("facturas");
$tpl->assign("num_cia",$_GET['cias']);
$compa=obtener_registro("catalogo_companias",array('num_cia'),array($_GET['cias']),"","",$dsn);
$tpl->assign("nom_cia",$compa[0]['nombre_corto']);

//$opera=obtener_registro("catalogo_operadoras",array("idoperadora"),array($_GET['operadora']),"","",$dsn);
$opera=obtener_registro("catalogo_operadoras",array("idoperadora"),array($compa[0]['idoperadora']),"","",$dsn);
//print_r($opera);
$tpl->assign("operadora",$opera[0]['nombre_operadora']);




//RECORRE EL ARREGLO DE LOS BLOCKS
for($i=0;$i<count($blocks);$i++){
	$folio=$blocks[$i]['folio_inicio'];
	$letra=$blocks[$i]['let_folio'];
	
	//RECORRE EL BLOCK
	for($y=0;$y<$blocks[$i]['num_folios'];$y++){
		
		$resta=0;
		if(existe_registro("venta_pastel",array("num_remi","letra_folio","num_cia"),array($folio,$letra,$_GET['cias']),$dsn))
		{
//			echo "bloc $y <br>";
			$sql="select * from venta_pastel where num_remi=".$folio." and letra_folio='".$letra."' and num_cia=".$_GET['cias']." and dev_base is NULL order by id";
			$factura=ejecutar_script($sql,$dsn);
			for($j=0;$j<count($factura);$j++){
//			echo "factura ".$factura[$j]['letra_folio']." - ".$factura[$j]['num_remi']." <br>";
				if(count($factura)==1){
					if($factura[$j]['resta_pagar'] == 0 and $factura[$j]['fecha_entrega'] != ""){
						continue;
					}
					else if($factura[$j]['resta_pagar'] > 0 and $factura[$j]['fecha_entrega'] != ""){
						$tpl->newBlock("rows");
						if($factura[$j]['letra_folio']=='X')
							$tpl->assign("let_folio","");
						else
							$tpl->assign("let_folio",$factura[$j]['letra_folio']);
						$tpl->assign("num_fact",$factura[$j]['num_remi']);
							
//						$tpl->assign("abono",number_format($factura[$j]['cuenta'],2,'.',','));
						$tpl->assign("total",number_format($factura[$j]['total_factura'],2,'.',','));
						$tpl->assign("resta",number_format($factura[$j]['resta_pagar'],2,'.',','));
						$tpl->assign("fecha_entrega",$factura[$j]['fecha_entrega']);
					}
				}
				else if(count($factura) > 1){
	//				echo "control amarillo y verde $folio <br>";			
					if($factura[$j]['fecha_entrega']!=""){
						if($factura[$j+1]['resta']==$factura[$j]['resta_pagar']){
							break;
						}
						else{
							$tpl->newBlock("rows");
							if($factura[$j]['letra_folio']=='X')
								$tpl->assign("let_folio","");
							else
								$tpl->assign("let_folio",$factura[$j]['letra_folio']);
							$tpl->assign("num_fact",$factura[$j]['num_remi']);

							$tpl->assign("total",number_format($factura[$j]['total_factura'],2,'.',','));
							$tpl->assign("fecha_entrega",$factura[$j]['fecha_entrega']);
							$resta=number_format($factura[$j]['total_factura'],2,'.','');

							$tpl->assign("resta",number_format($factura[$j]['resta_pagar'],2,'.',','));
						}
					}
				}
			}
		}
		$folio++;	
	}

//***********



}



$tpl->printToScreen();
?>