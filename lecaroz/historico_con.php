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
$descripcion_error[1] = "Lo siento pero no se encontraron registros";
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/historico_con.tpl");
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

$sql="SELECT * FROM historico";
/*
SELECT 
p.num_cia, p.mes, p.anio_anterior, s.anio_actual, s.ventas
FROM historico as p, historico as s WHERE
p.num_cia=111
AND
s.num_cia=111
AND
p.num_cia=s.num_cia
AND
p.mes=s.mes
and p.anio_actual=0 and p.ventas=0 and (s.anio_anterior = 0 and p.anio_anterior>0)
order by mes


*/
if($_GET['tipo_con']==0)
{
	$con=" WHERE num_cia > 0 AND num_cia < 200 order by num_cia, mes";//Para todas las compañías
	$bandera=false;
}
else
{
	$con=" WHERE num_cia ='".$_GET['num_cia']."' order by mes";//Para una sola Compañía
	$bandera=true;
}

$sql.=$con;
$jump=NULL;
$historico=ejecutar_script($sql,$dsn);
$total_anio_anterior=0;
$total_anio_actual=0;
$total_ventas=0;

if(!$historico){
	header("location: ./historico_con.php?codigo_error=1");
	die;
}
else
{
	$tpl->newBlock("historico");
//-------------------------------------------------------------------------------------------------------------------------------------
	for($i=0;$i<count($historico);$i++)
	{
		if($historico[$i]['num_cia']!=$jump)
		{
			$tpl->newBlock("companias");
			$ca1 = obtener_registro("catalogo_companias",array("num_cia"),array($historico[$i]['num_cia']),"","",$dsn);
			$tpl->assign("num_cia",$historico[$i]['num_cia']);
			$tpl->assign("nombre_cia",$ca1[0]['nombre_corto']);
			$ban=true;
			$total_anio_anterior=0;
			$total_anio_actual=0;
			$total_ventas=0;
			$tpl->assign("anio_anterior","2003");
			$tpl->assign("anio_actual","2004");

			
		}
		$jump=$historico[$i]['num_cia'];			
		$ban=false;
		$tpl->newBlock("rows");
		switch ($historico[$i]['mes']) {
			   case 1:
				   $tpl->assign("nombre_mes","Enero");
				   break;
			   case 2:
				   $tpl->assign("nombre_mes","Febrero");
				   break;
			   case 3:
				   $tpl->assign("nombre_mes","Marzo");
				   break;
			   case 4:
				   $tpl->assign("nombre_mes","Abril");
				   break;
			   case 5:
				   $tpl->assign("nombre_mes","Mayo");
				   break;
			   case 6:
				   $tpl->assign("nombre_mes","Junio");
				   break;
			   case 7:
				   $tpl->assign("nombre_mes","Julio");
				   break;
			   case 8:
				   $tpl->assign("nombre_mes","Agosto");
				   break;
			   case 9:
				   $tpl->assign("nombre_mes","Septiembre");
				   break;
			   case 10:
				   $tpl->assign("nombre_mes","Octubre");
				   break;
			   case 11:
				   $tpl->assign("nombre_mes","Noviembre");
				   break;
			   case 12:
				   $tpl->assign("nombre_mes","Diciembre");
				   break;
				}

		$tpl->assign("res_anio_anterior",number_format($historico[$i]['anio_anterior'],2,'.',','));
		$tpl->assign("res_anio_actual",number_format($historico[$i]['anio_actual'],2,'.',','));
		$tpl->assign("ventas",number_format($historico[$i]['ventas'],2,'.',','));
		$total_anio_anterior += $historico[$i]['anio_anterior'];
		$total_anio_actual += $historico[$i]['anio_actual'];
		$total_ventas += $historico[$i]['ventas'];

		if($ban == false)
		{
			$tpl->gotoBlock("companias");
			$tpl->assign("total_anio_anterior",number_format($total_anio_anterior,2,'.',','));
			$tpl->assign("total_anio_actual",number_format($total_anio_actual,2,'.',','));
			$tpl->assign("total_ventas",number_format($total_ventas,2,'.',','));
		}
	}


	

// Imprimir el resultado
$tpl->printToScreen();

}
?>