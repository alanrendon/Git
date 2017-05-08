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
$descripcion_error[1] = "No se encontraron gastos";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/pan/pan_mga_con.tpl");
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
	$tpl->assign("mes",date("m"));
	$tpl->assign("dia",date("d"));

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
$total=0;

function calcula_fecha($fecha)
{
	$_fecha=explode("/",$fecha);
	$fecha_hoy=explode("/",date("d/m/Y"));
	
	if($_fecha[2]==$fecha_hoy[2])//Caso en el que el año es el mismo para las dos fechas
	{
		if($_fecha[1]==$fecha_hoy[1]){//mismo año, mismo mes
			if($_fecha[0] >= $fecha_hoy[0]) //dia mayor al corriente bloquea el boton
				return true;
			else return true;
		}
		else if ($_fecha[1] >= $fecha_hoy[1])//mes mayor restringe el boton
			return true;
		else if($_fecha[1] == ($fecha_hoy[1] - 1)){//el mes de la fecha es anterior al actual
			if($fecha_hoy[0]==1 or $fecha_hoy[0]==2 or $fecha_hoy[0]==3 or $fecha_hoy[0]==4 or $fecha_hoy[0]==5)
				return true;
			else return false;
		}
		else return false;
	}
	else if($_fecha[2] == ($fecha_hoy[2] - 1)){//el año de la factura es anterior al actual
		if($fecha_hoy[1]==1 and $_fecha[1]==12){
			if($fecha_hoy[0]==1 or $fecha_hoy[0]==2 or $fecha_hoy[0]==3 or $fecha_hoy[0]==4 or $fecha_hoy[0]==5)
				return true;
			else return false;
		}
		else return false;
	}
	else return false;
}




	$sql="select * from movimiento_gastos where num_cia=".$_GET['cia']." and fecha='".$_GET['fecha']."' and captura=false and codgastos not in (33) order by idmovimiento_gastos";
	$gastos=ejecutar_script($sql,$dsn);
	if(!($gastos))
	{
		header("location: ./pan_mga_con.php?codigo_error=1");
		die();
	}

	$_fecha=explode("/",$_GET['fecha']);
	$tpl->newBlock("gastos");
	$tpl->assign("num_cia",$_GET['cia']);
	$cia=obtener_registro("catalogo_companias",array("num_cia"),array($_GET['cia']),"","",$dsn);
	$tpl->assign("nom_cia",$cia[0]['nombre_corto']);
	$tpl->assign("fecha",$_fecha[0]." de ".$nombremes[$_fecha[1]]." del ".$_fecha[2]);
	if(!calcula_fecha($_GET['fecha']))
		$valor="disabled";
	else
		$valor="";
	
	for($i=0;$i<count($gastos);$i++)
	{
		$tpl->newBlock("rows");
		$tpl->assign("i",$i);
		$tpl->assign("id",$gastos[$i]['idmovimiento_gastos']);
		$tpl->assign("codgasto",$gastos[$i]['codgastos']);
		$tpl->assign("concepto",$gastos[$i]['concepto']);
		$tpl->assign("importe",number_format($gastos[$i]['importe'],2,'.',','));
		$tpl->assign("importe1",$gastos[$i]['importe']);
		$tpl->assign("fecha",$_GET['fecha']);
		$tpl->assign("num_cia",$_GET['cia']);
		$tpl->assign("disabled",$valor);
		
		$gasto=obtener_registro("catalogo_gastos",array('codgastos'),array($gastos[$i]['codgastos']),"","",$dsn);
		$tpl->assign("nom_gasto",$gasto[0]['descripcion']);
		$total+=$gastos[$i]['importe'];
	}
	$tpl->gotoBlock("gastos");
	$tpl->assign("total",number_format($total,2,'.',','));
	
	
$tpl->printToScreen();

?>