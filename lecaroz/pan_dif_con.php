<?php
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
$tpl->assignInclude("body","./plantillas/pan/pan_dif_con.tpl");

$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['mes'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("anio",date("Y", mktime(0,0,0,date("m"),0,date("Y"))));
	
	for($i=1;$i<13;$i++){
		$tpl->newBlock("mes");
		$tpl->assign("mes",$i);
		$tpl->assign("nombre_mes",mes_escrito($i));
		if((number_format(date("m"),'','','') - 1 )== $i)
			$tpl->assign("selected","selected");
			
//		$tpl->assi
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
$total_faltante=0;
$total_sobrante=0;
$jump=NULL;
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

//$sql="SELECT num_cia, nombre_corto FROM catalogo_companias where num_cia BETWEEN 100 AND 200 and status=true";
$sql="SELECT num_cia, nombre_corto FROM catalogo_companias where num_cia > 0 and num_cia <= 300 and status=true";
$cias=ejecutar_script($sql,$dsn);
//$diferencias="select num_cia, codmp, precio_unidad, inventario_real.existencia as existencia, inventario, diferencia, precio_unidad*diferencia as valores from inventario_real JOIN inventario_fin_mes using (num_cia,codmp)";
$diferencias="
SELECT num_cia, codmp, controlada, inventario_real.precio_unidad, inventario_real.existencia AS existencia, inventario, diferencia, inventario_real.precio_unidad*(inventario_real.existencia - inventario) AS valores FROM inventario_real JOIN catalogo_mat_primas USING (codmp) JOIN inventario_fin_mes USING (num_cia,codmp) where controlada='" . ($_GET['tipo'] == 1 ? 'TRUE' : 'FALSE') . "'" . ($_GET['tipo'] == 1 ? " AND codmp NOT IN (SELECT codmp FROM codmp_no_controlada)" : " OR codmp IN (SELECT codmp FROM codmp_no_controlada)");

$diferencias = "SELECT num_cia, codmp, controlada, precio_unidad, existencia, inventario, diferencia, precio_unidad * (existencia - inventario) AS valores FROM inventario_fin_mes LEFT JOIN catalogo_mat_primas USING (codmp) WHERE controlada = '" . ($_GET['tipo'] == 1 ? 'TRUE' : 'FALSE') . "'"/* . ($_GET['tipo'] == 1 ? " OR codmp IN (SELECT codmp FROM codmp_no_controlada)" : '')*/;

//$diferencias="SELECT num_cia, codmp, controlada, inventario_real.precio_unidad, inventario_real.existencia AS existencia, inventario, diferencia, inventario_real.precio_unidad*(inventario_real.existencia - inventario) AS valores FROM inventario_real JOIN catalogo_mat_primas USING (codmp) JOIN inventario_fin_mes USING (num_cia,codmp) where";

$fecha=" AND fecha='".$diasxmes[$_GET['mes']]."/".$_GET['mes']."/".$_GET['anio']."'";

if($_GET['tipo_con']==0)
{
	//$con=" AND num_cia BETWEEN 100 AND 200 order by num_cia, codmp";
	$con=" AND num_cia > 0 AND num_cia <= 300 order by num_cia, codmp";//Para todas las compañías
	$bandera=false;
}
else
{
	$con=" AND num_cia ='".$_GET['num_cia']."' order by codmp";//Para una sola Compañía
	$bandera=true;
}
$diferencias =$diferencias.$fecha.$con;
$dif=ejecutar_script($diferencias,$dsn);
//echo $diferencias;
if(!$dif)
{
	header("location: ./pan_dif_con.php?codigo_error=1");//Si no encuentra registros para la consulta
	die;
}
else
{
	$tpl->newBlock("diferencias");
//	echo $diferencias."<br>";
//	print_r ($dif);
	
	if($bandera==true)//Consulta para una sola compañía
	{
		$tpl->newBlock("companias");
		$tpl->assign("mes",$nombremes[$_GET['mes']]);
		$tpl->assign("anio",$_GET['anio']);
		
		$ca = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
		$tpl->assign("num_cia",$_GET['num_cia']);
		$tpl->assign("nombre_cia",$ca[0]['nombre_corto']);
		
		for($i=0;$i<count($dif);$i++)
		{
//			if($dif[$i]['diferencia']==0) continue;
			if(($dif[$i]['existencia'] - $dif[$i]['inventario'])==0) continue;
			else
			{
				$tpl->newBlock("rows");
				$tpl->assign("codmp",$dif[$i]['codmp']);
				$tpl->assign("num_cia",$_GET['num_cia']);
				$tpl->assign("mes",$_GET['mes']);
				$tpl->assign("anio",$_GET['anio']);
				$mp = obtener_registro("catalogo_mat_primas",array("codmp"),array($dif[$i]['codmp']),"","",$dsn);
				$tpl->assign("codmp",$dif[$i]['codmp']);
				$tpl->assign("nombre_mp",$mp[0]['nombre']);
				$tpl->assign("existencia",number_format($dif[$i]['existencia'],2,'.',','));
				$tpl->assign("inventario",number_format($dif[$i]['inventario'],2,'.',','));
				$tpl->assign("costo_unitario",number_format($dif[$i]['precio_unidad'],2,'.',','));
				
				
//				if($dif[$i]['diferencia'] < 0)
				if($dif[$i]['existencia'] - $dif[$i]['inventario'] > 0)//se va a quitar
				{
					$unidades=number_format($dif[$i]['existencia'],2,'.','') - number_format($dif[$i]['inventario'],2,'.','');
					$unidades=abs($unidades);
					$unidades=number_format($unidades,2,'.',',');

					$tpl->assign("faltante_unidad",$unidades);
					$tpl->assign("faltante_valor",number_format(abs($dif[$i]['valores']),2,'.',','));
					$total_faltante += $dif[$i]['valores'];
				}
//				else if ($dif[$i]['diferencia'] > 0)
				if($dif[$i]['existencia'] - $dif[$i]['inventario'] < 0)//se va a quitar
				{
					$unidades=number_format($dif[$i]['existencia'],2,'.','') - number_format($dif[$i]['inventario'],2,'.','');
					$unidades=abs($unidades);
					$unidades=number_format($unidades,2,'.',',');

					$tpl->assign("sobrante_unidad",$unidades);
					$tpl->assign("sobrante_valor",number_format(abs($dif[$i]['valores']),2,'.',','));
					$total_sobrante += $dif[$i]['valores'];
				}
			}
		}
		$tpl->gotoBlock("companias");
		$tpl->assign("total_faltante",number_format(abs($total_faltante),2,'.',','));
		$tpl->assign("total_sobrante",number_format(abs($total_sobrante),2,'.',','));
		$d = abs($total_sobrante) - abs($total_faltante);
		if ($d>0){
		$tpl->assign("dif_sob",number_format(abs($d),2,'.',','));
		$tpl->assign("dif_fal","");}
		else if($d<0){
		$tpl->assign("dif_fal",number_format(abs($d),2,'.',','));
		$tpl->assign("dif_sob","");}
		else{
		$tpl->assign("dif_fal","0.00");
		$tpl->assign("dif_sob","0.00");
				}

	}
	//--------------------------------------------------------------------------------------------------------todas las compañías
	else if($bandera==false)
	{
		for($i=0;$i<count($dif);$i++)
		{
			if($dif[$i]['num_cia']!=$jump)//--------------------------------------------------------------------si no es diferente
			{
				$tpl->newBlock("companias");
				$tpl->assign("mes",$nombremes[$_GET['mes']]);
				$tpl->assign("anio",$_GET['anio']);
				$ca1 = obtener_registro("catalogo_companias",array("num_cia"),array($dif[$i]['num_cia']),"","",$dsn);
				$tpl->assign("num_cia",$dif[$i]['num_cia']);
				$tpl->assign("nombre_cia",$ca1[0]['nombre_corto']);
				$total_faltante=0;
				$total_sobrante=0;
				$d=0;

				$ban=true;
			}
			
			$jump=$dif[$i]['num_cia'];			
			$ban=false;
//			if($dif[$i]['diferencia']==0) continue;//-------------------------------------------------------la diferencia es 0

			if(($dif[$i]['existencia'] - $dif[$i]['inventario'])==0) continue;
			else//-----------------------------------------------------------------------------------------no tiene diferencias
			{
				$tpl->newBlock("rows");
				$tpl->assign("codmp",$dif[$i]['codmp']);
				$mp = obtener_registro("catalogo_mat_primas",array("codmp"),array($dif[$i]['codmp']),"","",$dsn);
				$tpl->assign("codmp",$dif[$i]['codmp']);
				$tpl->assign("nombre_mp",$mp[0]['nombre']);
				$tpl->assign("existencia",number_format($dif[$i]['existencia'],2,'.',','));
				$tpl->assign("inventario",number_format($dif[$i]['inventario'],2,'.',','));
				$tpl->assign("costo_unitario",number_format(abs($dif[$i]['precio_unidad']),2,'.',','));

//				if($dif[$i]['diferencia'] < 0)
				if($dif[$i]['existencia'] - $dif[$i]['inventario'] > 0)//se va a quitar
				
				{
					$unidades=number_format($dif[$i]['existencia'],2,'.','') - number_format($dif[$i]['inventario'],2,'.','');
					$unidades=abs($unidades);
					$unidades=number_format($unidades,2,'.',',');

					$tpl->assign("faltante_unidad",$unidades);
					$tpl->assign("faltante_valor",number_format(abs($dif[$i]['valores']),2,'.',','));
					$total_faltante += $dif[$i]['valores'];
				}
//				else if($dif[$i]['diferencia'] > 0)
				else if($dif[$i]['existencia'] - $dif[$i]['inventario'] < 0)//se va a quitar
				{
					$unidades=number_format($dif[$i]['existencia'],2,'.','') - number_format($dif[$i]['inventario'],2,'.','');
					$unidades=abs($unidades);
					$unidades=number_format($unidades,2,'.',',');

					$tpl->assign("sobrante_unidad",$unidades);
					$tpl->assign("sobrante_valor",number_format(abs($dif[$i]['valores']),2,'.',','));
					$total_sobrante += $dif[$i]['valores'];
				}
			}
			if($ban == false)
			{
				$tpl->gotoBlock("companias");
				$tpl->assign("total_faltante",number_format(abs($total_faltante),2,'.',','));
				$tpl->assign("total_sobrante",number_format(abs($total_sobrante),2,'.',','));
				$d = abs($total_sobrante) - abs($total_faltante);
				if ($d>0){
				$tpl->assign("dif_sob",number_format(abs($d),2,'.',','));
				$tpl->assign("dif_fal","");}
				else if($d<0){
				$tpl->assign("dif_fal",number_format(abs($d),2,'.',','));
				$tpl->assign("dif_sob","");}
				else{
				$tpl->assign("dif_fal","0.00");
				$tpl->assign("dif_sob","0.00");
				}
			}
			
//			$jump=$dif[$i]['num_cia'];			

		}
	}
	$tpl->printToScreen();
}
?>