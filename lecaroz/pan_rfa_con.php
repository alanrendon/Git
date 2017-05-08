<?php
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
$descripcion_error[1] = "No se encontraron registros";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/pan/pan_rfa_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['cia'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));

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
// MOSTRAR LISTADO POR FECHA ---------------------------------------------------------------------------------

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

//CONSULTA POR DIA DE CAPTURA
if($_GET['bandera']==0)
{
	$sql="select * from venta_pastel where num_cia=".$_GET['cia']." and fecha='".$_GET['fecha']."' order by id";
	$facturas=ejecutar_script($sql,$dsn);
	if(!($facturas))
	{
		header("location: ./pan_rfa_con.php?codigo_error=1");
		die();
	}

	$_fecha=explode("/",$_GET['fecha']);
	$tpl->newBlock("fecha"); //BLOQUE DEL LISTADO POR FECHA
	$tpl->assign("num_cia",$_GET['cia']);
	$cia=obtener_registro("catalogo_companias",array("num_cia"),array($_GET['cia']),"","",$dsn);
	$tpl->assign("nom_cia",$cia[0]['nombre_corto']);
	
	$tpl->assign("fecha",$_fecha[0]." de ".$nombremes[$_fecha[1]]." del ".$_fecha[2]);
	$tventa_puerta=0;
	$tabono_expendios=0;
	$tbase=0;
	$tdev_base=0;
	$tpastillaje=0;
	$totros_efectivos=0;
	$total_cuenta=0;
	$total_resta=0;
	
	for($i=0;$i<count($facturas);$i++)
	{
		$tpl->newBlock("rows");
		
		if($facturas[$i]['letra_folio']=="X") $tpl->assign("letra_folio","");
		else $tpl->assign("letra_folio",$facturas[$i]['letra_folio']);

		if($facturas[$i]['num_remi']<=0) $tpl->assign("num_remi","");
		else $tpl->assign("num_remi",$facturas[$i]['num_remi']);
		
		if($facturas[$i]['idexpendio']<=0) $tpl->assign("idexpendio","");
		else $tpl->assign("idexpendio",$facturas[$i]['idexpendio']);

		if($facturas[$i]['kilos']<=0) $tpl->assign("kilos","");
		else $tpl->assign("kilos",number_format($facturas[$i]['kilos'],2,'.',','));
		
		if($facturas[$i]['precio_unidad']<=0) $tpl->assign("precio_unidad","");
		else $tpl->assign("precio_unidad",number_format($facturas[$i]['precio_unidad'],2,'.',','));
		
		if($facturas[$i]['otros']<=0) $tpl->assign("otros","");
		else $tpl->assign("otros",number_format($facturas[$i]['otros'],2,'.',','));
		
		if($facturas[$i]['base']<=0) $tpl->assign("base","");
		else $tpl->assign("base",number_format($facturas[$i]['base'],2,'.',','));
		
		if($facturas[$i]['cuenta']<=0 or $facturas[$i]['cuenta']<=0) $tpl->assign("cuenta","");
		else $tpl->assign("cuenta",number_format($facturas[$i]['cuenta'],2,'.',','));

		if($facturas[$i]['dev_base']<=0) $tpl->assign("dev_base","");
		else $tpl->assign("dev_base",number_format($facturas[$i]['dev_base'],2,'.',','));
		
		if($facturas[$i]['resta']<=0) $tpl->assign("resta","");
		else $tpl->assign("resta",number_format($facturas[$i]['resta'],2,'.',','));
		
		if($facturas[$i]['pastillaje']<=0) $tpl->assign("pastillaje","");
		else $tpl->assign("pastillaje",number_format($facturas[$i]['pastillaje'],2,'.',','));

		if($facturas[$i]['otros_efectivos']<=0) $tpl->assign("otros_efe","");
		else $tpl->assign("otros_efe",number_format($facturas[$i]['otros_efectivos'],2,'.',','));

		if($facturas[$i]['fecha_entrega']=="") $tpl->assign("fecha_entrega","");
		else $tpl->assign("fecha_entrega",$facturas[$i]['fecha_entrega']);
		
		$total_cuenta += $facturas[$i]['cuenta'];
		$total_resta += $facturas[$i]['resta'];

		if($facturas[$i]['dev_base']>0)
			$tdev_base += $facturas[$i]['dev_base'];
		
		else{
			if($facturas[$i]['idexpendio']>0){
				$porcentaje=obtener_registro("catalogo_expendios",array('num_cia','num_expendio'),array($_GET['cia'],$facturas[$i]['idexpendio']),"","",$dsn);
				$temporal=0;
				if($facturas[$i]['resta']>0){
					$temporal = $facturas[$i]['resta'] - $facturas[$i]['resta'] * ($porcentaje[0]['porciento_ganancia']/100);
//					$tabono_expendios += $facturas[$i]['resta'];
					$tabono_expendios += $temporal;
				}
				else{ 
					$temporal = $facturas[$i]['cuenta'] - /*$facturas[$i]['pastillaje'] - $facturas[$i]['otros_efectivos'] - */$facturas[$i]['base'];
					$temporal = $temporal - $temporal * ($porcentaje[0]['porciento_ganancia']/100);
					$tabono_expendios += $temporal;
				}
			}
			else {
				$temporal1=0;
				if($facturas[$i]['resta']>0){
					$tventa_puerta += $facturas[$i]['resta'];
				}
				else{
					$temporal1 = $facturas[$i]['cuenta'] - $facturas[$i]['pastillaje'] - $facturas[$i]['otros_efectivos'] - $facturas[$i]['base'];
					$tventa_puerta += $temporal1;
				}
			}
		}
		if($facturas[$i]['pastillaje'] >0)
			$tpastillaje += $facturas[$i]['pastillaje'];
		if($facturas[$i]['otros_efectivos']>0)
			$totros_efectivos += $facturas[$i]['otros_efectivos'];
		if($facturas[$i]['base'] >0)
			$tbase += $facturas[$i]['base'];
	}
	$tpl->gotoBlock("fecha");
	$tpl->assign("venta_pta",number_format($tventa_puerta,2,'.',','));
	$tpl->assign("ab_expendios",number_format($tabono_expendios,2,'.',','));
	$tpl->assign("base",number_format($tbase,2,'.',','));
	$tpl->assign("dev_base",number_format($tdev_base,2,'.',','));
	$tpl->assign("pastillaje",number_format($tpastillaje,2,'.',','));
	$tpl->assign("otros_efec",number_format($totros_efectivos,2,'.',','));
	$tpl->assign("total_cuenta",number_format($total_cuenta,2,'.',','));
	$tpl->assign("total_resta",number_format($total_resta,2,'.',','));

}
//CONSULTA POR NUMERO DE FACTURA
else if($_GET['bandera']==1)
{
	$sql="select * from venta_pastel where num_cia=".$_GET['cia']." and num_remi=".$_GET['num_fac']." order by id";
	$facturas=ejecutar_script($sql,$dsn);
	if(!($facturas))
	{
		header("location: ./pan_rfa_con.php?codigo_error=1");
		die();
	}
	$tpl->newBlock("factura"); //BLOQUE DEL LISTADO POR FACTURA
	$tpl->assign("num_cia",$_GET['cia']);
	$cia=obtener_registro("catalogo_companias",array("num_cia"),array($_GET['cia']),"","",$dsn);
	$tpl->assign("nom_cia",$cia[0]['nombre_corto']);
	$tpl->assign("num_fac",$_GET['num_fac']);
	$tpl->assign("onclick", isset($_GET['close']) ? "self.close()" : "parent.history.back()");
	$tpl->assign("name", isset($_GET['close']) ? "Cerrar" : "Regresar");

	
	
	for($i=0;$i<count($facturas);$i++)
	{
		$tpl->newBlock("facturas");
		
		if($facturas[$i]['letra_folio']=="") $tpl->assign("letra_folio","--");
		else $tpl->assign("letra_folio",$facturas[$i]['letra_folio']);

		if($facturas[$i]['num_remi']=="") $tpl->assign("num_remi","");
		else $tpl->assign("num_remi",$facturas[$i]['num_remi']);
		
		if($facturas[$i]['idexpendio']=="") $tpl->assign("idexpendio","");
		else $tpl->assign("idexpendio",$facturas[$i]['idexpendio']);

		if($facturas[$i]['kilos']=="") $tpl->assign("kilos","");
		else $tpl->assign("kilos",number_format($facturas[$i]['kilos'],2,'.',','));
		
		if($facturas[$i]['precio_unidad']=="") $tpl->assign("precio_unidad","");
		else $tpl->assign("precio_unidad",number_format($facturas[$i]['precio_unidad'],2,'.',','));
		
		if($facturas[$i]['otros']=="") $tpl->assign("otros","");
		else $tpl->assign("otros",number_format($facturas[$i]['otros'],2,'.',','));
		
		if($facturas[$i]['base']=="") $tpl->assign("base","");
		else $tpl->assign("base",number_format($facturas[$i]['base'],2,'.',','));
		
		if($facturas[$i]['cuenta']=="") $tpl->assign("cuenta","");
		else $tpl->assign("cuenta",number_format($facturas[$i]['cuenta'],2,'.',','));

		if($facturas[$i]['dev_base']=="") $tpl->assign("dev_base","");
		else $tpl->assign("dev_base",number_format($facturas[$i]['dev_base'],2,'.',','));
		
		if($facturas[$i]['resta']=="") $tpl->assign("resta","");
		else $tpl->assign("resta",number_format($facturas[$i]['resta'],2,'.',','));
		
		if($facturas[$i]['fecha_entrega']=="") $tpl->assign("fecha_entrega","");
		else $tpl->assign("fecha_entrega",$facturas[$i]['fecha_entrega']);
		
		if($facturas[$i]['tipo']==0 and $facturas[$i]['estado']==0){
			if($facturas[$i]['resta_pagar']=="" or $facturas[$i]['resta_pagar']==0) $tpl->assign("faltante","");
			else $tpl->assign("faltante",number_format($facturas[$i]['resta_pagar'],2,'.',','));
		}
		
		$tpl->assign("fecha_entrada",$facturas[$i]['fecha']);

	}
}

$tpl->printToScreen();

?>