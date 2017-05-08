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

$ok = true;
for($i=0;$i<$contador;$i++)
{
	$tpl->newBlock("rows");
	$resta=0;
	$tpl->assign("let_folio",$letra);
	$tpl->assign("num_folio",$folio);
	$tpl->assign("num_cia", $_GET['cia']);
		
	$factura=ejecutar_script("select * from venta_pastel where letra_folio='".$_GET['letra']."' and num_cia=".$_GET['cia']." and num_remi=".$folio." and tipo=0",$dsn);
	if($factura){
		if($factura[0]['estado']==1 or $factura[0]['estado']==2){
			$bloc=ejecutar_script("select * from bloc where id=".$_GET['id'],$dsn);
			$num_folios=$bloc[0]['folios_usados'];
			$num_folios++;
			$sql="UPDATE bloc SET folios_usados=folios_usados + 1 WHERE id=".$_GET['id'];
			ejecutar_script($sql,$dsn);
			if($num_folios >=$bloc[0]['num_folios'])
				ejecutar_script("UPDATE bloc SET estado=true WHERE id=".$_GET['id'],$dsn);
				
			if($factura[0]['estado']==1){
				$tpl->assign("abono",number_format($factura[0]['cuenta'],2,'.',','));
				$tpl->assign("total",number_format($factura[0]['total_factura'],2,'.',','));
			}
			elseif($factura[0]['estado']==2){
				$tpl->assign("abono","CANCELADA");
			}
			$tpl->newBlock("ok");
		}
		else{
			$tpl->assign("abono",number_format($factura[0]['cuenta'],2,'.',','));
			$tpl->assign("total",number_format($factura[0]['total_factura'],2,'.',','));
			$tpl->assign("resta",number_format($factura[0]['resta_pagar'],2,'.',','));
			$tpl->assign("fecha_entrega",$factura[0]['fecha_entrega']);
			$tpl->newBlock("error");
			$tpl->gotoBlock("rows");
			$ok = false;
		}
	}
	else {
		$tpl->newBlock("error");
		$ok = false;
	}
	
	$folio++;
}
if ($ok) {
	$tpl->newBlock('borrar');
	$tpl->assign('id', $_GET['id']);
}
$tpl->printToScreen();
?>