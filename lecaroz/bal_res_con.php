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
$tpl->assignInclude("body","./plantillas/bal/bal_res_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['anio'])) {
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
$sql="SELECT * FROM reservas_cias";

if ($_GET['tipo']=='cia')
{
	$cia=" where num_cia='".$_GET['num_cia']."' and anio='".$_GET['anio']."' order by fecha, cod_reserva";
	$sql= $sql.$cia;
	$bandera=true;
}
else if($_GET['tipo']=='reserva')
{
	$res=" where cod_reserva='".$_GET['cod_reserva']."' and anio='".$_GET['anio']."' AND num_cia BETWEEN 1 AND 899 order by num_cia, fecha";
	$sql= $sql.$res;
	$bandera=false;
}

$reservas = ejecutar_script($sql,$dsn);
///print_r ($reservas);
if(!$reservas)
{
	header("location: ./bal_res_con.php?codigo_error=1");
	die();
}
$var=2;
$jump=(-1);
if(!$bandera)
{
	$tpl->newBlock("listado_reserva");
//		echo "entre a listado_reserva";
		$res = obtener_registro("catalogo_reservas",array("tipo_res"),array($_GET['cod_reserva']),"","",$dsn);
		$tpl->assign("nom_reserva",$res[0]['descripcion']);
		/*for($i=0;$i<count($reservas);$i++)
		{
			if($reservas[$i]['num_cia']!=$jump)//--------------------------------------------------------------------si no es diferente
			{
				$tpl->newBlock("rows");
				$cia = obtener_registro("catalogo_companias",array("num_cia"),array($reservas[$i]['num_cia']),"","",$dsn);
				$tpl->assign("num_cia",$reservas[$i]['num_cia']);
				$tpl->assign("nombre_corto",$cia[0]['nombre_corto']);
				$tpl->assign("importe1",number_format($reservas[$i]['importe'],2,'.',','));
				$var=2;
				$tpl->newBlock("pagado");
				$sql="select sum(importe) from reservas_cias where num_cia='".$reservas[$i]['num_cia']."' and cod_reserva='".$_GET['cod_reserva']."' and anio='".$_GET['anio']."'";
				$importe=ejecutar_script($sql,$dsn);
				$tpl->assign("total",number_format($importe[0]['sum'],2,'.',','));
				$tpl->assign("pagado",number_format($reservas[$i]['pagado'],2,'.',','));
				$tpl->gotoBlock("rows");
			}
			else
			{
				$tpl->assign("importe".$var,number_format($reservas[$i]['importe'],2,'.',','));
				$var++;
			}
				$jump=$reservas[$i]['num_cia'];			
		}*/
		$num_cia = NULL;
		for ($i = 0; $i < count($reservas); $i++) {
			if ($num_cia != $reservas[$i]['num_cia']) {
				if ($num_cia != NULL) {
					$tpl->newBlock("pagado");
					$tpl->assign("pagado", number_format($pagado, 2, ".", ","));
					$tpl->assign("total", number_format($total - $pagado, 2, ".", ","));
				}
				
				$num_cia = $reservas[$i]['num_cia'];
				$tpl->newBlock("rows");
				$tpl->assign("num_cia", $num_cia);
				$nombre_cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $num_cia",$dsn);
				$tpl->assign("nombre_corto", $nombre_cia[0]['nombre_corto']);
				$total = 0;
				$pagado = $reservas[$i]['pagado'];
			}
			ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $reservas[$i]['fecha'], $fecha);
			$tpl->assign("importe" . intval($fecha[2]), number_format($reservas[$i]['importe'], 2, ".", ","));
			$total += $reservas[$i]['importe'];
		}
		if ($num_cia != NULL) {
			$tpl->newBlock("pagado");
			$tpl->assign("pagado", number_format($pagado, 2, ".", ","));
			$tpl->assign("total", number_format($total - $pagado, 2, ".", ","));
		}
}
else if($bandera)
{
	$tpl->newBlock("listado_compania");
	$nom_cia = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
	$tpl->assign("nom_cia",$nom_cia[0]['nombre_corto']);


	$sql="select distinct(cod_reserva), num_cia from reservas_cias where num_cia='".$_GET['num_cia']."' order by cod_reserva";
	$res=ejecutar_script($sql,$dsn);
//s	print_r($res);
	for($i=0;$i<count($res);$i++)
		{
			$tpl->newBlock("reserva");
//			$tpl->assign("cod_reserva",$res[$i]['cod_reserva']);
			$nom_res = obtener_registro("catalogo_reservas",array("tipo_res"),array($res[$i]['cod_reserva']),"","",$dsn);
			$tpl->assign("nom_res",$nom_res[0]['descripcion']);
		}
//	$a=count($res) * 12;
	$a=0;
	for($i=0;$i<12;$i++)
	{
		$tpl->newBlock("meses");
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
				   $tpl->assign("nombre_mes","Diciembre");
				   break;
			}
			
		for($j=0;$j<count($res);$j++)
		{
		$tpl->newBlock("importes");
		$tpl->assign("importe",number_format($reservas[$a]['importe'],2,'.',','));
		$a++;
		}
	}
	for($i=0;$i<count($res);$i++)
	{
		$tpl->newBlock("pagado2");
		$tpl->assign("pagado2",number_format($reservas[$i]['pagado'],2,'.',','));
	}
	
	for($i=0;$i<count($res);$i++)
	{
		$tpl->newBlock("total");
		$sql="select sum(importe) from reservas_cias where num_cia='".$_GET['num_cia']."' and cod_reserva='".$reservas[$i]['cod_reserva']."' and anio='".$_GET['anio']."'";
		$importe=ejecutar_script($sql,$dsn);
		$tpl->assign("total",number_format($importe[0]['sum'],2,'.',','));

	}
	
	

}

$tpl->printToScreen();

?>