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
$tpl->assignInclude("body","./plantillas/adm/admin_fac_con.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
// Generar listado de turnos
// Si viene de una página que genero error
if(!isset($_GET['fecha']))
{
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));

	$tpl->assign("fecha",date("d/m/Y",mktime(0,0,0,date("m"),date("d") - 1,date("Y"))));
	
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


$tpl->newBlock("facturas");
$tpl->assign("fecha",$_GET['fecha']);
$sql="select distinct(idcia) from(
select * from bloc where idcia <101 AND estado=false and folios_usados > 0 order by idcia
) as consulta where idcia < 101 order by idcia";
$cias=ejecutar_script($sql,$dsn);
$aux_cias=0;
$_fecha=explode("/",$_GET['fecha']);
for($z=0;$z<count($cias);$z++){
	$sql="select * from bloc where idcia=".$cias[$z]['idcia']." AND estado=false and folios_usados > 0 order by let_folio,folio_inicio";
	$blocks=ejecutar_script($sql,$dsn);
	


//RECORRE EL ARREGLO DE LOS BLOCKS
	for($i=0;$i<count($blocks);$i++){
		$folio=$blocks[$i]['folio_inicio'];
		$letra=$blocks[$i]['let_folio'];
		
		//RECORRE EL BLOCK
		for($y=0;$y<$blocks[$i]['num_folios'];$y++){
			
			$resta=0;
			if(existe_registro("venta_pastel",array("num_remi","letra_folio","num_cia"),array($folio,$letra,$cias[$z]['idcia']),$dsn))
			{
				$sql="select * from venta_pastel where num_remi=".$folio." and letra_folio='".$letra."' and num_cia=".$cias[$z]['idcia']." and dev_base is NULL order by id";
				$factura=ejecutar_script($sql,$dsn);
				$_fecha2=explode("/",$factura[0]['fecha_entrega']);
				for($j=0;$j<count($factura);$j++){
	//			echo "factura ".$factura[$j]['num_remi']." <br>";
					if(count($factura)==1){
						if($factura[$j]['resta_pagar'] == 0 and $factura[$j]['fecha_entrega'] != ""){
							continue;
						}
						else if($factura[$j]['resta_pagar'] > 0 and $factura[$j]['fecha_entrega'] != ""){
							if($_fecha2[2] > $_fecha[2]){
								break;
							}
							else if(number_format($_fecha2[1],"","","") > $_fecha[1]){
								break;
							}
							else if(number_format($_fecha2[1],"","","") == $_fecha[1]){
								if(number_format($_fecha2[0],"","","") > $_fecha[0])
									break;
							}
							$tpl->newBlock("rows");
							if($aux_cias!=$cias[$z]['idcia']){
								$tpl->newBlock("cias");
								$tpl->assign("num_cia",$cias[$z]['idcia']);
								$compa=obtener_registro("catalogo_companias",array('num_cia'),array($cias[$z]['idcia']),"","",$dsn);
								$tpl->assign("nombre_cia",$compa[0]['nombre_corto']);
								$opera=obtener_registro("catalogo_operadoras",array("idoperadora"),array($compa[0]['idoperadora']),"","",$dsn);
								$tpl->assign("operadora",$opera[0]['nombre']);
								$aux_cias=$cias[$z]['idcia'];
							}
							$aux_cias=$cias[$z]['idcia'];
							$tpl->gotoBlock("rows");
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
//								echo "cia".$cias[$z]['idcia']." fecha entrega ".$factura[$j]['fecha_entrega']."  fecha entrada ".$_GET['fecha']."<br>";
								if($_fecha2[2] > $_fecha[2]){
									break;
								}
								else if(number_format($_fecha2[1],"","","") > $_fecha[1] and $_fecha2[2] <= $_fecha[2]){
									break;
								}
								else if(number_format($_fecha2[1],"","","") == $_fecha[1] and $_fecha2[2] <= $_fecha[2]){
									if(number_format($_fecha2[0],"","","") > $_fecha[0])
										break;
								}
								$tpl->newBlock("rows");
								if($aux_cias!=$cias[$z]['idcia']){
									$tpl->newBlock("cias");
									$tpl->assign("num_cia",$cias[$z]['idcia']);
									$compa=obtener_registro("catalogo_companias",array('num_cia'),array($cias[$z]['idcia']),"","",$dsn);
									$tpl->assign("nombre_cia",$compa[0]['nombre_corto']);
									$opera=obtener_registro("catalogo_operadoras",array("idoperadora"),array($compa[0]['idoperadora']),"","",$dsn);
									$tpl->assign("operadora",$opera[0]['nombre']);
									$aux_cias=$cias[$z]['idcia'];
									
								}
								$aux_cias=$cias[$z]['idcia'];
								$tpl->gotoBlock("rows");
								if($factura[$j]['let_folio']=='X')
									$tpl->assign("let_folio","");
								else
									$tpl->assign("let_folio",$factura[$j]['let_folio']);
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
	}
	$aux_cias=$cias[$z]['idcia'];
}


$tpl->printToScreen();
?>