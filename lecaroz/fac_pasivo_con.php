<?php
// CONSULTA DE PRODUCCION
// Tabla 'produccion'
// Menu 'Panaderías->Producción'
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
$descripcion_error[1] = "No hay registros";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/fac/fac_pasivo_con.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['fecha_mov'])) {
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
// -------------------------------- SCRIPT ---------------------------------------------------------
// -------------------------------- Mostrar listado ---------------------------------------------------------

//ARROJA EL NUMERO DE ITERACIONES DENTRO DEL FOR A PARTIR DEL RANGO DE FECHAS
//$fecha_inicio='1/'.date("m").'/'.date("Y");
$fech=explode("/",$_GET['fecha_mov']);
$fecha_inicio='1/'.$fech[1].'/'.date("Y");
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

$aux=0;
$suma=0;
$tpl->newBlock("pasivo");
if($_GET['tipo_cia']==0)
{
	$tpl->newBlock("compania");
	$aux=0;
	$sql="select * from pasivo_proveedores where num_cia=".$_GET['num_cia']." and num_proveedor != 13 order by fecha_mov, num_fact, num_proveedor";
	$pas=ejecutar_script($sql,$dsn);

	if(!($pas)) {
		header("location: ./fac_pasivo_con.php?codigo_error=1");
		die();
		}
	
	$tpl->assign("num_cia",$_GET['num_cia']);
	$cia=obtener_registro("catalogo_companias",array('num_cia'),array($_GET['num_cia']),"","",$dsn);
	$tpl->assign("nom_cia",$cia[0]['nombre_corto']);
	$dia_1=explode("/",$_GET['fecha_mov']);
	$tpl->assign("dia",$dia_1[0]);
	$tpl->assign("anio",date("Y"));
	$tpl->assign("mes",$nombremes[date("m")]);
	$dia_2=explode("/",$_GET['fecha_mov1']);
	$tpl->assign("dia2",$dia_2[0]);
	$tpl->assign("anio2",date("Y"));
	$tpl->assign("mes2",$nombremes[date("m")]);



	for($i=0; $i<count($pas);$i++)
	{
		if( ($i+1) == count($pas) ){
			if($pas[$i]['num_fact']==$pas[$i+1]['num_fact'])
				$suma += $pas[$i]['total'];
			else
			{
				$suma += $pas[$i]['total'];
				$tpl->newBlock("rows");
				$tpl->assign("num_fact",$pas[$i]['num_fact']);
				$prov=obtener_registro("catalogo_proveedores",array('num_proveedor'),array($pas[$i]['num_proveedor']),"","",$dsn);
				$tpl->assign("nombre_proveedor",$prov[0]['nombre']);
				$tpl->assign("num_proveedor",$pas[$i]['num_proveedor']);
				$tpl->assign("total_fac",number_format($suma,2,'.',','));
				$total+=$suma;
				$suma=0;
			}
		}
	}
	$tpl->gotoBlock("compania");
	$tpl->assign("")
}

else if($_GET['tipo_cia']==1)
{
	$tpl->newBlock("totales");
	
	$sql="select distinct(num_cia) from pasivo_proveedores where fecha_mov between '".$_GET['fecha_mov']."' and '".$_GET['fecha_mov1']."'and num_proveedor !=13 order by num_cia";
	$cias=ejecutar_script($sql,$dsn);
	for($i=0;$i<count($cias);$i++)
	{
		$tpl->newBlock("renglones");
		$sql="select "
	}
}
$tpl->printToScreen();
// --------------------------------------------------------------------------------------------------------
?>