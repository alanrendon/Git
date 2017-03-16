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
$tpl->assignInclude("body","./plantillas/ros/ros_efe_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['compania'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio",date("Y"));
	for($j=0;$j<12;$j++)
	{
		$tpl->newBlock("mes");
		switch ($j) {
		   case 0:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Enero");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 1:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Febrero");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 2:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Marzo");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
  		   case 3:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Abril");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 4:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Mayo");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 5:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Junio");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 6:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Julio");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 7:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Agosto");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 8:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Septiembre");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 9:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Octubre");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 10:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Noviembre");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;
		   case 11:
			   $tpl->assign("num_mes",$j+1);
			   $tpl->assign("nom_mes","Diciembre");
			   if ($j+1 == date("m")) $tpl->assign("checked","selected");
			   break;

		}		
	}
	
	
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
$ros = ejecutar_script("SELECT * FROM catalogo_companias WHERE status=true and num_cia BETWEEN 301 AND 599 or num_cia IN (702, 704, 705) ORDER BY num_cia ASC",$dsn);

$fecha_i="1/".$_GET['mes']."/".$_GET['anio'];
$diasxmes[1] = 31; // Enero
if ($_GET['anio']%4 == 0)
	$diasxmes[2] = 29; // Febrero año bisiesto
else
	$diasxmes[2] = 28; // Febrero
$diasxmes[3] = 31; // Marzo
$diasxmes[4] = 30; // Abril
$diasxmes[5] = 31; // Mayo
$diasxmes[6] = 30; // Junio
$diasxmes[7] = 31; // Julio
$diasxmes[8] = 31; // Agosto
$diasxmes[9] = 30; // Septiembre
$diasxmes[10] = 31; // Octubre
$diasxmes[11] = 30; // Noviembre
$diasxmes[12] = 31; // Diciembre

$nombremes[1]="ENERO";
$nombremes[2]="FEBRERO";
$nombremes[3]="MARZO";
$nombremes[4]="ABRIL";
$nombremes[5]="MAYO";
$nombremes[6]="JUNIO";
$nombremes[7]="JULIO";
$nombremes[8]="AGOSTO";
$nombremes[9]="SEPTIEMBRE";
$nombremes[10]="OCTUBRE";
$nombremes[11]="NOVIEMBRE";
$nombremes[12]="DICIEMBRE";


$fecha_f=$diasxmes[$_GET['mes']]."/".$_GET['mes']."/".$_GET['anio'];

$sql="SELECT * FROM total_companias WHERE ";

/*if($_GET['fecha']==$_GET['fecha1'])
	$fecha="fecha ='".$_GET['fecha']."'";
else
	$fecha="fecha >= '".$_GET['fecha']."' and fecha <= '".$_GET['fecha1']."'";
*/
if($_GET['tipo_cia']=='cia')
	{
	$cia=" num_cia ='".$_GET['compania']."'";
	$bandera_cia=true;
	$ros1 = ejecutar_script("SELECT * FROM catalogo_companias WHERE num_cia ='".$_GET['compania']."' and status=true",$dsn);
	}
else{
	$cia=" (num_cia between 100 and 200 or num_cia in (704))";
	//$cia=" (num_cia > '100' and num_cia < '200' or num_cia='704')";
	$bandera_cia=false;
	}
//$fecha= " and fecha >= '".$fecha_i."' and fecha <= '".$fecha_f."'";
$fecha= " and fecha between '".$fecha_i."' and '".$fecha_f."'";
/*
if($_GET['tipo_consulta']=='pendiente')
	$pagado=" and pagado=false";
elseif($_GET['tipo_consulta']=='pagado')
	$pagado=" and pagado=true";
elseif($_GET['tipo_consulta']=='todo')
	$pagado=" and pagado=true or pagado=false";
*/
$sql=$sql.$cia.$fecha." order by num_cia, fecha ASC";


$efectivos = ejecutar_script($sql,$dsn);
// Crear bloque de listado
$tpl->newBlock("listado_dia");
$tpl->assign("mes",$nombremes[$_GET['mes']]);
$tpl->assign("anio",$_GET['anio']);
$totalgral_venta = 0;
$totalgral_gastos = 0;
$totalgral_efectivo = 0;
//echo "<br>".$sql."<br>";


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
		$total_venta = 0;
		$total_gastos = 0;
		$total_efectivo = 0;
		for ($j=0; $j<count($efectivos); $j++) 
		{
			if ($efectivos[$j]['num_cia'] == $ros[$i]['num_cia']) 
			{
				if($_GET['totales']=="desgloce")
				{
					$tpl->newBlock("fila");
					$tpl->assign("fecha",$efectivos[$j]['fecha']);
					$tpl->assign("venta",number_format($efectivos[$j]['venta'],2,".",","));
					$tpl->assign("gastos",number_format($efectivos[$j]['gastos'],2,".",","));
					$tpl->assign("efectivo",number_format($efectivos[$j]['efectivo'],2,".",","));
				}				
				$total_venta += $efectivos[$j]['venta'];
				$total_gastos += $efectivos[$j]['gastos'];
				$total_efectivo += $efectivos[$j]['efectivo'];
				
				$totalgral_venta += $efectivos[$j]['venta'];
				$totalgral_gastos += $efectivos[$j]['gastos'];
				$totalgral_efectivo += $efectivos[$j]['efectivo'];
			}
		}
if($_GET['totales']=="desgloce")
{		
	$tpl->newBlock("totales");
	$tpl->assign("total_venta",number_format($total_venta,2,".",","));
	$tpl->assign("total_gastos",number_format($total_gastos,2,".",","));
	$tpl->assign("total_efectivo",number_format($total_efectivo,2,".",","));
	$tpl->assign("comision", number_format($total_efectivo * 0.006, 2, '.', ','));
}
elseif($_GET['totales']=="total")
{
	$tpl->assign("total_venta",number_format($total_venta,2,".",","));
	$tpl->assign("total_gastos",number_format($total_gastos,2,".",","));
	$tpl->assign("total_efectivo",number_format($total_efectivo,2,".",","));
	$tpl->assign("comision", number_format($total_efectivo * 0.006, 2, '.', ','));
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
		$total_venta = 0;
		$total_gastos = 0;
		$total_efectivo = 0;

		for ($j=0; $j<count($efectivos); $j++)
		{

			if ($efectivos[$j]['num_cia'] == $ros1[0]['num_cia']) 
			{
				if($_GET['totales']=="desgloce"){
					$tpl->newBlock("fila");
					$tpl->assign("fecha",$efectivos[$j]['fecha']);
					$tpl->assign("venta",number_format($efectivos[$j]['venta'],2,".",","));
					$tpl->assign("gastos",number_format($efectivos[$j]['gastos'],2,".",","));
					$tpl->assign("efectivo",number_format($efectivos[$j]['efectivo'],2,".",","));
				}				
				$total_venta += $efectivos[$j]['venta'];
				$total_gastos += $efectivos[$j]['gastos'];
				$total_efectivo += $efectivos[$j]['efectivo'];
				
				$totalgral_venta += $efectivos[$j]['venta'];
				$totalgral_gastos += $efectivos[$j]['gastos'];
				$totalgral_efectivo += $efectivos[$j]['efectivo'];
	
			}
		}
	$tpl->newBlock("totales");
	$tpl->assign("total_venta",number_format($total_venta,2,".",","));
	$tpl->assign("total_gastos",number_format($total_gastos,2,".",","));
	$tpl->assign("total_efectivo",number_format($total_efectivo,2,".",","));
	$tpl->assign("comision", number_format($total_efectivo * 0.006, 2, '.', ','));
		
	}
}



if($_GET['totales']=="desgloce")
{		
$tpl->newBlock("totalGeneral");
$tpl->assign("totalgral_venta",number_format($totalgral_venta,2,".",","));
$tpl->assign("totalgral_gastos",number_format($totalgral_gastos,2,".",","));
$tpl->assign("totalgral_efectivo",number_format($totalgral_efectivo,2,".",","));
}
elseif($_GET['totales']=="total")
{
	$tpl->newBlock("solo_totalGeneral");
	$tpl->assign("totalgral_venta",number_format($totalgral_venta,2,".",","));
	$tpl->assign("totalgral_gastos",number_format($totalgral_gastos,2,".",","));
	$tpl->assign("totalgral_efectivo",number_format($totalgral_efectivo,2,".",","));
}
$tpl->printToScreen();
// --------------------------------------------------------------------------------------------------------


?>