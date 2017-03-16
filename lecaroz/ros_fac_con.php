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
$tpl->assignInclude("body","./plantillas/ros/ros_fac_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['compania'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio_actual",date("Y"));

	$sql = "
		SELECT
			num_proveedor
				AS value,
			nombre
				AS text
		FROM
			precios_guerra
			LEFT JOIN catalogo_proveedores
				USING (num_proveedor)
		WHERE
			precio_compra > 0
			AND codmp IN (160, 363, 297, 352, 364, 700, 600, 401, 869, 877, 334, 434, 573, 975, 573, 334, 990, 1083, 303)
		GROUP BY
			value,
			text
		ORDER BY
			value
	";

	$result = ejecutar_script($sql, $dsn);

	if ($result)
	{
		foreach ($result as $row) {
			$tpl->newBlock('pro');

			$tpl->assign('value', $row['value']);
			$tpl->assign('text', $row['text']);
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
//objeto que va a traer como datos todas las rosticerias incluyendo la cafeteria
//$ros = ejecutar_script("SELECT * FROM catalogo_companias WHERE status=true AND num_cia > 100 AND num_cia < 200 OR num_cia = 702 OR num_cia = 704 ORDER BY num_cia ASC",$dsn);
$ros = ejecutar_script("select distinct(num_cia),nombre_corto from fact_rosticeria JOIN catalogo_companias using(num_cia) where fecha_mov >='$_GET[fecha]'/* AND num_cia IN (104, 107, 130, 145, 151, 149, 166, 168, 169, 165, 158)*/".($_GET['num_pro'] > 0 ? " AND fact_rosticeria.num_proveedor = $_GET[num_pro]" : '')." order by num_cia",$dsn);


$sql="SELECT * FROM total_fac_ros WHERE ".($_GET['num_pro'] > 0 ? " num_proveedor = $_GET[num_pro] AND " : '');//inicio del query
//checa las fechas si son iguales, se mete al query una sola fecha
if($_GET['fecha']==$_GET['fecha1'])
	$fecha="fecha ='".$_GET['fecha']."'";
else
//si son diferentes las fechas, entonces se tiene un rango de fechas para la consulta fecha inicial y fecha final
	$fecha="(fecha >= '".$_GET['fecha']."' and fecha <= '".$_GET['fecha1']."')";

//verifica si la consulta la realizara para una sola compañia
if($_GET['tipo_cia']=='cia'){
	$cia=" and num_cia ='".$_GET['compania']."'";
	$bandera_cia=true;
	$ros1 = ejecutar_script("SELECT * FROM catalogo_companias WHERE num_cia ='".$_GET['compania']."' and status=true",$dsn);
	}
else{
//la consulta se realizará para todas las compañías
	$cia=" and (num_cia > '300' and num_cia < '600' or num_cia='702' or num_cia='704') and fecha >= '$_GET[fecha]'";
	$bandera_cia=false;}

if($_GET['tipo_consulta']=='pendiente')
	$pagado=" and pagado=false";
elseif($_GET['tipo_consulta']=='pagado')
	$pagado=" and pagado=true";
elseif($_GET['tipo_consulta']=='todo')
	$pagado=" ";

$sql=$sql.$fecha.$cia.$pagado." order by num_cia, fecha ASC";


$facturas = ejecutar_script($sql,$dsn);

//echo $sql."<br>";
//print_r($facturas);
// Crear bloque de listado
$tpl->newBlock("listado_dia");
$tpl->assign("fecha",$_GET['fecha']);
$tpl->assign("fecha1",$_GET['fecha1']);
$totalgral_credito = 0;
$totalgral_contado = 0;
$totalgral_factura = 0;
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
		$total_credito = 0;
		$total_contado = 0;
		$total_factura = 0;
		for ($j=0; $j<count($facturas); $j++)
		{
			if ($facturas[$j]['num_cia'] == $ros[$i]['num_cia'])
			{
				if($_GET['totales']=="desgloce")
				{
					$por_credito = $facturas[$j]['credito'] * 100 / $facturas[$j]['total_fac'];
					$por_contado = $facturas[$j]['contado'] * 100 / $facturas[$j]['total_fac'];

					$tpl->newBlock("fila");
					$tpl->assign("num_fac",$facturas[$j]['num_fac']);
					$tpl->assign("fecha",$facturas[$j]['fecha']);
					if($facturas[$j]['credito']<=0)$tpl->assign("credito","");
					else $tpl->assign("credito",number_format($facturas[$j]['credito'],2,".",",") . " (" . number_format($por_credito, 2) . "%)");
					if($facturas[$j]['contado']<=0) $tpl->assign("contado","");
					else $tpl->assign("contado",number_format($facturas[$j]['contado'],2,".",",") . " (" . number_format($por_contado, 2) . "%)");
					if($facturas[$j]['total_fac']<=0) $tpl->assign("total_fac","");
					else $tpl->assign("total_fac",number_format($facturas[$j]['total_fac'],2,".",","));
				}
				$total_credito += $facturas[$j]['credito'];
				$total_contado += $facturas[$j]['contado'];
				$total_factura += $facturas[$j]['total_fac'];

				$totalgral_credito += $facturas[$j]['credito'];
				$totalgral_contado += $facturas[$j]['contado'];
				$totalgral_factura += $facturas[$j]['total_fac'];
			}
		}
	if($_GET['totales']=="desgloce")
	{
		$por_credito = $total_credito * 100 / $total_factura;
		$por_contado = $total_contado * 100 / $total_factura;

		$tpl->newBlock("totales");
		if($total_credito <= 0) $tpl->assign("total_credito","");
		else $tpl->assign("total_credito",number_format($total_credito,2,".",",") . " (" . number_format($por_credito, 2) . "%)");
		if($total_contado <= 0) $tpl->assign("total_contado","");
		else $tpl->assign("total_contado",number_format($total_contado,2,".",",") . " (" . number_format($por_contado, 2) . "%)");
		if($total_factura <= 0) $tpl->assign("total_factura","");
		else $tpl->assign("total_factura",number_format($total_factura,2,".",","));
	}
	elseif($_GET['totales']=="total")
	{
		$por_credito = $total_credito * 100 / $total_factura;
		$por_contado = $total_contado * 100 / $total_factura;

		if($total_credito <= 0) $tpl->assign("total_credito","");
		else $tpl->assign("total_credito",number_format($total_credito,2,".",",") . " (" . number_format($por_credito, 2) . "%)");
		if($total_contado <= 0) $tpl->assign("total_contado","");
		else $tpl->assign("total_contado",number_format($total_contado,2,".",",") . " (" . number_format($por_contado, 2) . "%)");
		if($total_factura <= 0) $tpl->assign("total_factura","");
		else $tpl->assign("total_factura",number_format($total_factura,2,".",","));
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
		$total_credito = 0;
		$total_contado = 0;
		$total_factura = 0;

		for ($j=0; $j<count($facturas); $j++)
		{

			if ($facturas[$j]['num_cia'] == $ros1[0]['num_cia'])
			{
				if($_GET['totales']=="desgloce"){
					$tpl->newBlock("fila");
					$tpl->assign("num_fac",$facturas[$j]['num_fac']);
					$tpl->assign("fecha",$facturas[$j]['fecha']);
					if($facturas[$j]['credito']<=0)$tpl->assign("credito","");
					else $tpl->assign("credito",number_format($facturas[$j]['credito'],2,".",","));
					if($facturas[$j]['contado']<=0) $tpl->assign("contado","");
					else $tpl->assign("contado",number_format($facturas[$j]['contado'],2,".",","));
					if($facturas[$j]['total_fac']<=0) $tpl->assign("total_fac","");
					else $tpl->assign("total_fac",number_format($facturas[$j]['total_fac'],2,".",","));
				}
				$total_credito += $facturas[$j]['credito'];
				$total_contado += $facturas[$j]['contado'];
				$total_factura += $facturas[$j]['total_fac'];

				$totalgral_credito += $facturas[$j]['credito'];
				$totalgral_contado += $facturas[$j]['contado'];
				$totalgral_factura += $facturas[$j]['total_fac'];

			}
		}
	$tpl->newBlock("totales");
	if($total_credito <= 0) $tpl->assign("total_credito","");
	else $tpl->assign("total_credito",number_format($total_credito,2,".",","));
	if($total_contado <= 0) $tpl->assign("total_contado","");
	else $tpl->assign("total_contado",number_format($total_contado,2,".",","));
	if($total_factura <= 0) $tpl->assign("total_factura","");
	else $tpl->assign("total_factura",number_format($total_factura,2,".",","));
	}
}



if($_GET['totales']=="desgloce")
{
$tpl->newBlock("totalGeneral");
if($totalgral_credito <= 0) $tpl->assign("totalgral_credito","");
else $tpl->assign("totalgral_credito",number_format($totalgral_credito,2,".",","));
if($totalgral_contado <= 0) $tpl->assign("totalgral_contado","");
else $tpl->assign("totalgral_contado",number_format($totalgral_contado,2,".",","));
if($totalgral_factura <= 0) $tpl->assign("totalgral_factura","");
else $tpl->assign("totalgral_factura",number_format($totalgral_factura,2,".",","));
}
elseif($_GET['totales']=="total")
{
	$tpl->newBlock("solo_totalGeneral");
	if($totalgral_credito <= 0) $tpl->assign("totalgral_credito","");
	else $tpl->assign("totalgral_credito",number_format($totalgral_credito,2,".",","));
	if($totalgral_contado <= 0) $tpl->assing("totalgral_contado","");
	else $tpl->assign("totalgral_contado",number_format($totalgral_contado,2,".",","));
	if($totalgral_factura <= 0) $tpl->assign("totalgral_factura","");
	else $tpl->assign("totalgral_factura",number_format($totalgral_factura,2,".",","));
}
$tpl->printToScreen();
// --------------------------------------------------------------------------------------------------------


?>
