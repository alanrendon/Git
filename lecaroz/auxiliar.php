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
$descripcion_error[1] = "La compañía no existe en la Base de Datos";
$descripcion_error[2] = "No hay registros";
$descripcion_error[3] = "Fecha incorrecta, vericar el formato (dd/mm/aaaa)";
$descripcion_error[4] = "Fecha fuera de rango, vericar el formato (dd/mm/aaaa)";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/ros/ros_hoja_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['compania'])) {
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

// -------------------------------- Mostrar listado ---------------------------------------------------------
// Obtener rosticerias
//objeto que va a traer como datos todas las rosticerias incluyendo la cafeteria

$ros = ejecutar_script("SELECT * FROM catalogo_companias WHERE num_cia > 100 AND num_cia < 200 ORDER BY num_cia ASC",$dsn);

$sql="SELECT * FROM hoja_diaria_rost WHERE ";//inicio del query
//checa las fechas si son iguales, se mete al query una sola fecha
if($_GET['fecha']==$_GET['fecha1'])
	$fecha="fecha ='".$_GET['fecha']."'";
else
//si son diferentes las fechas, entonces se tiene un rango de fechas para la consulta fecha inicial y fecha final
	$fecha="(fecha >= '".$_GET['fecha']."' and fecha <= '".$_GET['fecha1']."')";

//verifica si la consulta la realizara para una sola compañia
if($_GET['tipo_cia']=='cia'){
	$cia=" and num_cia ='".$_GET['compania']."'";
	$bandera_cia=true; //la consula va a ser para una sola compañía
	$ros1 = ejecutar_script("SELECT * FROM catalogo_companias WHERE num_cia ='".$_GET['compania']."'",$dsn);
	}
else{
//la consulta se realizará para todas las compañías
	$cia=" and (num_cia > '100' and num_cia < '200')";
	$bandera_cia=false; //consulta para todas las rosticerias
	}

$sql=$sql.$fecha.$cia." order by num_cia, fecha ASC";


$hoja = ejecutar_script($sql,$dsn);

//echo $sql."<br>";
//print_r($hoja);
// Crear bloque de listado
$tpl->newBlock("listado_dia");
$tpl->assign("fecha",$_GET['fecha']);
$tpl->assign("fecha1",$_GET['fecha1']);

//variables posiblemente innecesarias para esta pantalla
$totalgral_unidades = 0;
$totalgral_unitario = 0;
$totalgral_precio = 0;
//echo "<br>".$sql."<br>";
//------------------------------------------------------

if($bandera_cia==false)
{
	if ($_GET['totales']=="total") $tpl->newBlock("encabezado_solo_totales");
	for ($i=0; $i<count($ros); $i++) 
	{
		if($_GET['totales']=="desgloce")
		{
			$tpl->newBlock("rosticeria");
			$tpl->assign("num_cia",$ros[$i]['num_cia']);
			$tpl->assign("nombre_cia",$ros[$i]['nombre_corto']);
		}
		elseif ($_GET['totales']=="total")
		{
			$tpl->newBlock("solo_totales");
			$tpl->assign("num_cia",$ros[$i]['num_cia']);
			$tpl->assign("nombre_cia",$ros[$i]['nombre_corto']);
		}
		$total_unidades = 0;
		$total_unitario = 0;
		$total_precio = 0;
		for ($j=0; $j<count($hoja); $j++) 
		{
			if ($hoja[$j]['num_cia'] == $ros[$i]['num_cia']) 
			{
				if($_GET['totales']=="desgloce")
				{
					$tpl->newBlock("fila");
					$tpl->assign("codmp",$hoja[$j]['codmp']);
					$mp = obtener_registro("catalogo_mat_primas",array("codmp"),array($hoja[$j]['codmp']), "codmp","ASC", $dsn);
					$tpl->assign("nom",$mp[0]['nombre']);
					$tpl->assign("unidades",$hoja[$j]['unidades']);
					$tpl->assign("unitario",number_format($hoja[$j]['precio_unitario'],2,".",","));
					$tpl->assign("precio",number_format($hoja[$j]['precio_total'],2,".",","));
				}				
				$total_precio += $hoja[$j]['precio_total'];
				
				$totalgral_precio += $hoja[$j]['precio_total'];
			}
		}
	if($_GET['totales']=="desgloce")
	{		
		$tpl->newBlock("totales");
		$tpl->assign("total_precio",number_format($total_precio,2,".",","));
	}
	elseif($_GET['totales']=="total")
	{
		$tpl->assign("total_precio",number_format($total_precio,2,".",","));
	}
	}

}

elseif($bandera_cia==true)
{
	$tpl->newBlock("rosticeria");
	$tpl->assign("num_cia",$ros1[0]['num_cia']);
	$tpl->assign("nombre_cia",$ros1[0]['nombre_corto']);
	for ($i=0; $i<count($ros1); $i++) 
	{
		$total_unidades = 0;
		$total_unitario = 0;
		$total_precio = 0;

		for ($j=0; $j<count($hoja); $j++)
		{

			if ($hoja[$j]['num_cia'] == $ros1[0]['num_cia']) 
			{
				if($_GET['totales']=="desgloce"){
					$tpl->newBlock("fila");
					$tpl->assign("codmp",$hoja[$j]['codmp']);
					$mp = obtener_registro("catalogo_mat_primas",array("codmp"),array($hoja[$j]['codmp']), "codmp","ASC", $dsn);
					$tpl->assign("nom",$mp[0]['nombre']);
					$tpl->assign("unidades",$hoja[$j]['unidades']);
					$tpl->assign("unitario",number_format($hoja[$j]['precio_unitario'],2,".",","));
					$tpl->assign("precio",number_format($hoja[$j]['precio_total'],2,".",","));
				}				
				$total_precio += $hoja[$j]['precio_total'];
				
				$totalgral_precio += $hoja[$j]['precio_total'];
	
			}
		}
	$tpl->newBlock("totales");
	$tpl->assign("total_precio",number_format($total_precio,2,".",","));
		
	}
}
if($_GET['totales']=="desgloce")
{		
$tpl->newBlock("totalGeneral");
$tpl->assign("totalgral_precio",number_format($totalgral_precio,2,".",","));
}
elseif($_GET['totales']=="total")
{
	$tpl->newBlock("solo_totalGeneral");
	$tpl->assign("totalgral_precio",number_format($totalgral_precio,2,".",","));
}
$tpl->printToScreen();
// --------------------------------------------------------------------------------------------------------


?>