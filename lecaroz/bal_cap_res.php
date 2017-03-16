<?php
//define ('IDSCREEN',6213); //ID de pantalla
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);
// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Lo siento pero ya exite esta reserva para esta compañía";
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_cap_res.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla	
///$tpl->assign("tabla",$session->tabla);


// Si viene de una página que genero error
//------------------------------------------------Obtener Datos------------------------------------------------------------
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio",date("Y"));

	$cia = obtener_registro("catalogo_companias",array(),array(),"num_cia","ASC",$dsn);
	for ($i=0; $i<count($cia); $i++) 
	{

			$tpl->newBlock("nom_cia");
			$tpl->assign("num_cia",$cia[$i]['num_cia']);
			$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);

	}
	
	$res = obtener_registro("catalogo_reservas",array(),array(),"tipo_res","ASC",$dsn);
	//print_r ($res);
	for ($i=0; $i<count($res); $i++) 
	{
			$tpl->newBlock("nombre_reserva");
			$tpl->assign("tipo_res",$res[$i]['tipo_res']);
			$tpl->assign("descripcion",$res[$i]['descripcion']);
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
//------------------------------------------------***Reservas***------------------------------------------------------------

//$verifica = obtener_registro("reservas_cias",array("num_cia","cod_reserva"),array($_GET['num_cia'], $_GET['cod_reserva']),"","", $dsn);
$sql="SELECT * FROM reservas_cias WHERE num_cia='".$_GET['num_cia']."' and cod_reserva='".$_GET['cod_reserva']."'";
$verifica = ejecutar_script($sql,$dsn);

$meses = $_GET['mes'];

//print_r ($verifica);
$ban = false;
/*
$a=explode("/",$verifica[0]['fecha']); 

echo $a[0]."<br>";
echo $a[1]."<br>";
echo $a[2]."<br>";
*/
//echo count($verifica);
if ($verifica){
	for($j=0;$j<count($verifica);$j++)
	{
		$a=explode("/",$verifica[$j]['fecha']);
		if ($_GET['anio'] == $a[2] and $_GET['cod_reserva']==$verifica[$j]['cod_reserva'])
			$ban=false;
		else
			$ban=true;
	}
}
else $ban=true;


if ($ban==false)
{
	header("location: ./bal_cap_res.php?codigo_error=1");
	
	die;
}
else
{
	$tpl->newBlock("reservas");
	//print_r ($verifica);

	$tpl->assign("tabla","reservas_cias");
	$tpl->assign("num_cia",$_GET['num_cia']);
	$tpl->assign("anio",$_GET['anio']);
	
	$cia_r = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
	$tpl->assign("nombre_cia",$cia_r[0]['nombre_corto']);
	
	//print_r ($_GET);
	$total=0;
	$nombre_r = obtener_registro("catalogo_reservas",array("tipo_res"),array($_GET['cod_reserva']),"","",$dsn);
	if ($nombre_r[0]['codgastos'] > 0) {
		$fecha1 = "01/01/$_GET[anio]";
		$fecha2 = $_GET['anio'] == date("Y") ? date("d/m/Y") : "31/12/$_GET[anio]";
		
		$sql = "SELECT sum(importe) AS pagado FROM movimiento_gastos WHERE num_cia = $_GET[num_cia] AND codgastos = {$nombre_r[0]['codgastos']} AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$temp = ejecutar_script($sql,$dsn);
		$pagado = $temp[0]['pagado'];
	}
	else
		$pagado = "";
	
	for ($i=0; $i<12; $i++) {
		$tpl->newBlock("meses");
		$tpl->assign("i",$i);
		$tpl->assign("m",$i+1);
		$tpl->assign("cod_reserva",$_GET['cod_reserva']);
		//$nombre_r = obtener_registro("catalogo_reservas",array("tipo_res"),array($_GET['cod_reserva']),"","",$dsn);
		$tpl->assign("nombre_reserva",$nombre_r[0]['descripcion']);
		if (array_search($i+1,$meses) !== FALSE) {
			$total += $_GET['importe'];
			$tpl->assign("importe",$_GET['importe']);
		}
		else
			$tpl->assign("importe","");
		$tpl->assign("cod_reserva",$_GET['cod_reserva']);
		
//		$tpl->assign("importe1",number_format($_GET['importe'],2,'.',','));
		
	
		switch ($i) {
			   case 0:
				   $tpl->assign("nombre_mes","Enero");
				   break;
			   case 1:
				   $tpl->assign("nombre_mes","Febrero");
				   break;
			   case 2:
				   $tpl->assign("nombre_mes","Marzo");
				   break;
			   case 3:
				   $tpl->assign("nombre_mes","Abril");
				   break;
			   case 4:
				   $tpl->assign("nombre_mes","Mayo");
				   break;
			   case 5:
				   $tpl->assign("nombre_mes","Junio");
				   break;
			   case 6:
				   $tpl->assign("nombre_mes","Julio");
				   break;
			   case 7:
				   $tpl->assign("nombre_mes","Agosto");
				   break;
			   case 8:
				   $tpl->assign("nombre_mes","Septiembre");
				   break;
			   case 9:
				   $tpl->assign("nombre_mes","Octubre");
				   break;
			   case 10:
				   $tpl->assign("nombre_mes","Noviembre");
				   break;
			   case 11:
				   $total -= $_GET['importe'];
				   
				   $tpl->assign("nombre_mes","Diciembre");
				   $tpl->assign("importe",$pagado - $total > 0 ? number_format($pagado - $total,2,".","") : "0.00");
				   //$tpl->assign("importe1","");
				   break;
			}
	}
	$tpl->newBlock("totales");
	
	$tpl->assign("total",$total);
	$tpl->assign("total1",number_format($total,2,'.',','));
	
	$tpl->assign("pagado",$pagado > 0 ? number_format($pagado,2,".","") : "");
}
// Imprimir el resultado
$tpl->printToScreen();
?>