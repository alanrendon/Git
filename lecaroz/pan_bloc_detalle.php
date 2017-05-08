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
$tpl->assignInclude("body","./plantillas/pan/pan_bloc_detalle.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
// Generar listado de turnos
// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
	die();
}
if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}


$cia=obtener_registro("catalogo_companias",array("num_cia"),array($_GET['cia']),"","",$dsn);

$tpl->assign("num_cia",$_GET['cia']);
$tpl->assign("nombre_cia",strtoupper($cia[0]['nombre_corto']));

$contador=$_GET['folios'];

if($_GET['letra']=='X')
	$letra="";
else
	$letra=$_GET['letra'];
$folio=$_GET['inicio'];

//print_r($_GET);


$sql="UPDATE bloc SET folios_usados=0, estado=false WHERE id=".$_GET['id'];
ejecutar_script($sql,$dsn);


for($i=0;$i<$contador;$i++)
{
	$tpl->newBlock("rows");
	$resta=0;
	$tpl->assign("let_folio",$letra);
	$tpl->assign("num_folio",$folio);
	if(existe_registro("venta_pastel",array("num_remi","letra_folio","num_cia"),array($folio,$_GET['letra'],$_GET['cia']),$dsn))
	{
		$sql="select * from venta_pastel where num_remi=".$folio." and letra_folio='".$_GET['letra']."' and num_cia=".$_GET['cia']." and dev_base is NULL order by id";
		$factura=ejecutar_script($sql,$dsn);
		for($j=0;$j<count($factura);$j++){
			if(count($factura)==1){
				if($factura[$j]['resta_pagar'] == 0 and $factura[$j]['fecha_entrega'] != ""){
//				echo "control amarillo pagado $folio <br>";
//******************************************************************************************MODIFICARA Y ARREGLARA LOS BLOCS
					$sql="SELECT * FROM bloc WHERE id=".$_GET['id'];
					$bloc=ejecutar_script($sql,$dsn);
					$num_folios=$bloc[0]['folios_usados'];
					$num_folios++;
					$sql="UPDATE bloc SET folios_usados=folios_usados + 1 WHERE id=".$_GET['id'];
					ejecutar_script($sql,$dsn);
					if($num_folios >=$bloc[0]['num_folios']){
						$sql="UPDATE bloc SET estado=true WHERE id=".$_GET['id'];
						ejecutar_script($sql,$dsn);
					}
//******************************************************************************************
					$tpl->assign("abono",number_format($factura[$j]['cuenta'],2,'.',','));
					$tpl->assign("total",number_format($factura[$j]['total_factura'],2,'.',','));
					$tpl->newBlock("ok");
//					break;
				}
				else if($factura[$j]['resta_pagar'] > 0 and $factura[$j]['fecha_entrega'] != ""){
					$tpl->assign("abono",number_format($factura[$j]['cuenta'],2,'.',','));
					$tpl->assign("total",number_format($factura[$j]['total_factura'],2,'.',','));
					$tpl->assign("resta",number_format($factura[$j]['resta_pagar'],2,'.',','));
					$tpl->assign("fecha_entrega",$factura[$j]['fecha_entrega']);
					$tpl->newBlock("error");
					$tpl->gotoBlock("rows");
				}
			}
			else if(count($factura) > 1){
//				echo "control amarillo y verde $folio <br>";			
				if($factura[$j]['fecha_entrega']!=""){
					$tpl->assign("abono",number_format($factura[$j]['cuenta'],2,'.',','));
					$tpl->assign("total",number_format($factura[$j]['total_factura'],2,'.',','));
					$tpl->assign("fecha_entrega",$factura[$j]['fecha_entrega']);
					$resta=number_format($factura[$j]['total_factura'],2,'.','');
					
					if($factura[$j+1]['resta']==$factura[$j]['resta_pagar']){
//******************************************************************************************MODIFICARA Y ARREGLARA LOS BLOCS
						$sql="SELECT * FROM bloc WHERE id=".$_GET['id'];
						$bloc=ejecutar_script($sql,$dsn);
						$num_folios=$bloc[0]['folios_usados'];
						$num_folios++;
						$sql="UPDATE bloc SET folios_usados=folios_usados + 1 WHERE id=".$_GET['id'];
						ejecutar_script($sql,$dsn);
						if($num_folios >=$bloc[0]['num_folios']){
							$sql="UPDATE bloc SET estado=true WHERE id=".$_GET['id'];
							ejecutar_script($sql,$dsn);
						}
//******************************************************************************************
						$tpl->assign("resta","");
						$tpl->assign("fecha_entrega","");
						$tpl->newBlock("ok");
						break;
					}
					else{
						$tpl->assign("resta",number_format($factura[$j]['resta_pagar'],2,'.',','));
						$tpl->newBlock("error");
						$tpl->gotoBlock("rows");
					}
				}
			}
			else $tpl->newBlock("error");
		}
	}
	else
		$tpl->newBlock("error");
	$folio++;	
}
$tpl->printToScreen();
?>