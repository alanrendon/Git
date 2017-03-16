<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---	------------------------------
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
$tpl->assignInclude("body","./plantillas/ros/ros_com_con.tpl");

$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['mes'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio",date("Y"));
	
	
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
$sql="SELECT num_cia, nombre_corto from catalogo_companias where num_cia > 100 and num_cia < 200 and status=true order by num_cia";
$cias=ejecutar_script($sql,$dsn);
$tpl->newBlock("comisiones");
$tpl->assign("mes", mes_escrito((int)$_GET['mes'], TRUE));
$tpl->assign("anio",$_GET['anio']);
$total_pollos=0;
$total_pavos=0;
$total_pescuezos=0;
$total_alas=0;
$total_comision_pollos=0;
$total_comision_pescuezos=0;
$total_general=0;

//---------------------
$fecha1=date("d/m/Y", mktime(0,0,0,$_GET['mes'],1,$_GET['anio']));
$fecha2=date( "d/m/Y", mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']) );
//----------------------
for($i=0;$i<count($cias);$i++)
{
	$tpl->newBlock("rows");
	$tpl->assign("num_cia",$cias[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cias[$i]['nombre_corto']);
	
	//-------------
	$sql1="select sum(unidades) from hoja_diaria_rost where num_cia=".$cias[$i]['num_cia']." and codmp in(160, 700, 600) and fecha >='".$fecha1."' and fecha <='".$fecha2."'";
	$sql2="select sum(unidades) from hoja_diaria_rost where num_cia=".$cias[$i]['num_cia']." and codmp in(352) and fecha >='".$fecha1."' and fecha <='".$fecha2."'";
	$sql3="select sum(unidades) from hoja_diaria_rost where num_cia=".$cias[$i]['num_cia']." and codmp in(297) and fecha >='".$fecha1."' and fecha <='".$fecha2."'";
	$sql4="select sum(unidades) from hoja_diaria_rost where num_cia=".$cias[$i]['num_cia']." and codmp in(363) and fecha >='".$fecha1."' and fecha <='".$fecha2."'";
	$pollos=ejecutar_script($sql1,$dsn);//pollos
	$pavos=ejecutar_script($sql2,$dsn);//pavos
	$pescuezos=ejecutar_script($sql3,$dsn);//pescuezos
	$alas=ejecutar_script($sql4,$dsn);//alas
	$tpl->assign("pollos", number_format($pollos[0]['sum'],'','',','));
//	$tpl->assign("cantidad1",number_format($_POST['cantidad'.$i],2,'.',','));
	$tpl->assign("pavo",number_format($pavos[0]['sum'],'','',','));
	$tpl->assign("pescuezos",number_format($pescuezos[0]['sum'],'','',','));
	$tpl->assign("alas",number_format($alas[0]['sum'],'','',','));
	
	$comision1="select * from catalogo_comisiones where codmp=160";//comision de pollos
	$comision2="select * from catalogo_comisiones where codmp=352";//comisión de pavos
	$comision3="select * from catalogo_comisiones where codmp=297";//comisión de pescuezos
	$comision4="select * from catalogo_comisiones where codmp=363";//comisión de alas
	$com_pollos=ejecutar_script($comision1,$dsn);//comision de pollos
	$com_pavos=ejecutar_script($comision2,$dsn);//comisión de pavos
	$com_pescuezos=ejecutar_script($comision3,$dsn);//comisión de pescuezos
	$com_alas=ejecutar_script($comision4,$dsn);//comisión de alas
	
	$tpl->assign("comision_pollos", number_format(($pollos[0]['sum']*$com_pollos[0]['comision'] + $pavos[0]['sum']*$com_pavos[0]['comision']),'2','.',','));
	$com_pollos=$pollos[0]['sum']*$com_pollos[0]['comision'] + $pavos[0]['sum']*$com_pavos[0]['comision'];
	$tpl->assign("comision_pescuezo", number_format(($pescuezos[0]['sum']*$com_pescuezos[0]['comision'] + $alas[0]['sum']*$com_alas[0]['comision']),2,'.',','));
	$com_pesc=$pescuezos[0]['sum']*$com_pescuezos[0]['comision'] + $alas[0]['sum']*$com_alas[0]['comision'];
	$tpl->assign("total",number_format(($com_pollos+$com_pesc),2,'.',','));
	
	$total_pollos += $pollos[0]['sum'];
	$total_pavos += $pavos[0]['sum'];
	$total_pescuezos += $pescuezos[0]['sum'];
	$total_alas += $alas[0]['sum'];
	$total_comision_pollos += $com_pollos;
	$total_comision_pescuezos += $com_pesc;
	$total_general +=$com_pollos+$com_pesc;
	
}

$tpl->newBlock("totales");
$tpl->assign("total_pollos", number_format($total_pollos),'','',',');
$tpl->assign("total_pavo", number_format($total_pavos),'','',',');
$tpl->assign("total_comision_pollos",number_format($total_comision_pollos,2,'.',','));
$tpl->assign("total_pescuezos",number_format($total_pescuezos),'','',',');
$tpl->assign("total_alas",number_format($total_alas),'','',',');
$tpl->assign("total_comision_pescuezo",number_format($total_comision_pescuezos,2,'.',','));
$tpl->assign("total_general",number_format($total_general),2,'.',',');

$tpl->printToScreen();
// --------------------------------------------------------------------------------------------------------


?>