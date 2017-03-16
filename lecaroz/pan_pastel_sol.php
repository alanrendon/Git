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
$descripcion_error[1] = "No existe la factura para esta compañía";
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
$tpl->assignInclude("body","./plantillas/pan/pan_pastel_sol.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
// Generar listado de turnos
// Si viene de una página que genero error

if(!isset($_GET['num_cia0']))
{
	$tpl->newBlock("obtener_datos");
	
	$sql="select num_cia, nombre_corto from catalogo_companias where num_cia between 1 and 300 or num_cia=702 order by num_cia";
	$companias=ejecutar_script($sql,$dsn);
	
	for($i=0;$i<count($companias);$i++)
	{
		$tpl->newBlock("nombre_cia");
		$tpl->assign("num_cia",$companias[$i]['num_cia']);
		$tpl->assign("nombre_cia",$companias[$i]['nombre_corto']);
	}
	$tpl->gotoBlock("obtener_datos");

	for($i=0;$i<10;$i++)
	{
		$tpl->newBlock("rows");
		$tpl->assign("i",$i);
		$tpl->assign("next",$i+1);
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

$tpl->newBlock("pasteles");
$j=0;
$bandera=1;

for($i=0;$i<10;$i++)
{
	if($_GET['num_cia'.$i]!="" and $_GET['num_fact'.$i]!="" and $_GET['descripcion'.$i]!="")
	{
		$tpl->newBlock("renglones");
		$tpl->assign("i",$j);
		$tpl->assign("num_cia",$_GET['num_cia'.$i]);
		$tpl->assign("nom_cia",$_GET['nom_cia'.$i]);
		if($_GET['let_folio'.$i]=="")
		{
			$tpl->assign("let_folio1","");
			$tpl->assign("let_folio",'X');
		}
		else
		{
			$tpl->assign("let_folio1",strtoupper($_GET['let_folio'.$i]));
			$tpl->assign("let_folio",strtoupper($_GET['let_folio'.$i]));
		}
		$tpl->assign("num_fact",$_GET['num_fact'.$i]);
		$tpl->assign("descripcion",$_GET['descripcion'.$i]);

		if($_GET['kilos1'.$i]==(-1) and $_GET['precio_unidad1'.$i]==0 and $_GET['otros1'.$i]==0 and $_GET['base1'.$i]==0 and $_GET['cancelar1'.$i]==0 and $_GET['cambio_fecha1'.$i]==0 and $_GET['perdida1'.$i]==0 and $_GET['fecha_nueva1'.$i]==0){
			$bandera.=0;
		}

		
		if($_GET['cancelar1'.$i]==1)
		{
			$tpl->assign("cancelar","CANCELACION");
			$tpl->assign("kilos1",-1);
			$tpl->assign("precio_unidad1",0);
			$tpl->assign("otros1",0);
			$tpl->assign("base1",0);
			$tpl->assign("cancelar1",1);
			$tpl->assign("perdida1",0);
			$tpl->assign("cambio_fecha1",0);
			$tpl->assign("fecha_nueva1",0);
		}
		else if($_GET['fecha_nueva1'.$i]==1)
		{
			$tpl->assign("fecha_nueva","Modificar");
			$tpl->assign("kilos1",-1);
			$tpl->assign("precio_unidad1",0);
			$tpl->assign("otros1",0);
			$tpl->assign("base1",0);
			$tpl->assign("cancelar1",0);
			$tpl->assign("perdida1",0);
			$tpl->assign("cambio_fecha1",0);
			$tpl->assign("fecha_nueva1",1);
		}

		else if($_GET['cambio_fecha1'.$i]==1)
		{
			$tpl->assign("cambio_fecha","Modificar");
			$tpl->assign("kilos1",-1);
			$tpl->assign("precio_unidad1",0);
			$tpl->assign("otros1",0);
			$tpl->assign("base1",0);
			$tpl->assign("cancelar1",0);
			$tpl->assign("perdida1",0);
			$tpl->assign("cambio_fecha1",1);
			$tpl->assign("fecha_nueva1",0);
		}
		else if($_GET['perdida1'.$i]==1)
		{
			$tpl->assign("perdida","Modificar");
			$tpl->assign("kilos1",-1);
			$tpl->assign("precio_unidad1",0);
			$tpl->assign("otros1",0);
			$tpl->assign("base1",0);
			$tpl->assign("cancelar1",0);
			$tpl->assign("perdida1",1);
			$tpl->assign("cambio_fecha1",0);
			$tpl->assign("fecha_nueva1",0);
		}
		else
		{
			$tpl->assign("kilos1",$_GET['kilos1'.$i]);
			$tpl->assign("precio_unidad1",$_GET['precio_unidad1'.$i]);
			$tpl->assign("otros1",$_GET['otros1'.$i]);
			$tpl->assign("base1",$_GET['base1'.$i]);

			if($_GET['kilos1'.$i]==1)
				$tpl->assign("kilos","Kilos de mas");
			else if($_GET['kilos1'.$i]==0)
				$tpl->assign("kilos","Kilos de menos");
			else
				$tpl->assign("kilos"," ");
			if($_GET['precio_unidad1'.$i]==1)
				$tpl->assign("precio_unidad","Modificar");
			else
				$tpl->assign("precio_unidad"," ");
			if($_GET['otros1'.$i]==1)
				$tpl->assign("otros","Modificar");
			else
				$tpl->assign("otros"," ");
			if($_GET['base1'.$i]==1)
				$tpl->assign("base","Modificar");
			else
				$tpl->assign("base"," ");
		}
		$j++;
		if($_GET['let_folio'.$i]=="")
			$letra='X';
		else
			$letra=strtoupper($_GET['let_folio'.$i]);
		
		
		if (existe_registro("venta_pastel", array("num_remi","num_cia","letra_folio"), array($_GET['num_fact'.$i],$_GET['num_cia'.$i],$letra), $dsn))
		{
//CODIGO COMENTARIADO PARA RECHAZAR VARIAS SOLICITUDES DE MODIFICACION PARA UNA MISMA FACTURA DE PASTEL
//			if (existe_registro("modificacion_pastel", array("num_remi","num_cia"/*,"let_folio"*/), array($_GET['num_fact'.$i],$_GET['num_cia'.$i]/*,$_GET['let_folio'.$i]*/), $dsn))
//			{
//				$tpl->newBlock("edo_error");
//				$tpl->assign("edo","Ya solicitaste modificacion");
//				$bandera=false;
//			}
//			else
//			{			
				$tpl->newBlock("edo_ok");
				$tpl->assign("edo","Verificado");
//			}
		}
		else
		{
			$tpl->newBlock("edo_error");
			$tpl->assign("edo","No esta capturada");
			$bandera.=0;
		}
	}
}
	if($bandera==1)
	{
		$tpl->newBlock("enviar");
	}
	$tpl->gotoBlock("pasteles");
	$tpl->assign("cont",$j);
	$tpl->printToScreen();
?>