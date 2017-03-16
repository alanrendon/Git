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
$descripcion_error[1] = "Lo siento pero no hay registros de inventarios para esta compañía";
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_ifm_con.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla	
$tpl->assign("tabla","inventario_fin_mes");


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


//$sql="SELECT * FROM inventario_real WHERE num_cia='".$_GET['num_cia']."' order by codmp";
if($_GET['tipo_con']==0)
	$sql="select num_cia, codmp, existencia, unidadconsumo, tipo, catalogo_mat_primas.nombre from inventario_real join catalogo_mat_primas using(codmp) where num_cia='".$_GET['num_cia']."' order by catalogo_mat_primas.tipo, catalogo_mat_primas.nombre";
else
	$sql="select num_cia, codmp, existencia, unidadconsumo, tipo, catalogo_mat_primas.nombre from inventario_real join catalogo_mat_primas using(codmp) where num_cia between 1 and 10 order by inventario_real.num_cia, catalogo_mat_primas.tipo, catalogo_mat_primas.nombre";

$inv=ejecutar_script($sql,$dsn);

if(!$inv)
{
	header("location: ./ros_ifm_con.php?codigo_error=1");
	die();
}
//$tpl->assign("dia",date("d"));
//$tpl->assign("mes",date("m"));
//$tpl->assign("anio",date("Y"));

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


if($_GET['tipo_consulta']==0)
{

	$tpl->newBlock("inventario");
	
	$tipo=$inv[0]['tipo'];
	$tmp=0;
	$cont=0;
	$aux=0;
	for($i=0;$i<count($inv);$i++)
	{
		if($tmp!=$inv[$i]['num_cia']){
	
	/*
			$aux=$cont/41;
	//		echo "<br> auxiliar".$aux;
			$aux=floor($aux);
	//		echo "<br> rebajado".$aux;
			$modulo= $aux % 2;
	//		echo "<br> modulo".$modulo;
			$tpl->newBlock("comp");
	
			if($modulo > 0){
				$tpl->newBlock("salto");
	//			$tpl->newBlock("salto1");
				$tpl->gotoBlock("comp");
			}
			else
			{
			
			}
			
	*/
	//		$tpl->gotoBlock("comp");
			$tpl->newBlock("comp");		
			$cia1 = obtener_registro("catalogo_companias",array("num_cia"),array($inv[$i]['num_cia']),"","",$dsn);
			$tpl->assign("num_cia",$inv[$i]['num_cia']);
			$tpl->assign("nombre_cia",$cia1[0]['nombre_corto']);
			$tpl->assign("nombre_mes",$nombremes[date("n")]);
			$tmp=$inv[$i]['num_cia'];
			$tipo=$inv[$i]['tipo'];
			$cont=0;
			$aux=0;
			
		}
		$tpl->newBlock("rows");
		if ($tipo!=$inv[$i]['tipo']){
			$tpl->newBlock("empaque");
			$tpl->gotoBlock("rows");
		}
	
		$tpl->assign("i",$i);
		$cont++;
		$tpl->assign("next",$i+1);
		$tpl->assign("fecha",date("d/m/Y", mktime(0,0,0,date("m"),0,date("Y"))));
	
	//	$tpl->assign("codmp",$inv[$i]['codmp']);
		$mp = obtener_registro("catalogo_mat_primas",array("codmp"),array($inv[$i]['codmp']),"","",$dsn);
		$tpl->assign("codmp",$inv[$i]['codmp']);
		$tpl->assign("nombre_mp",strtoupper($mp[0]['nombre']));
		$mp = obtener_registro("tipo_unidad_consumo",array("idunidad"),array($inv[$i]['unidadconsumo']),"","",$dsn);
		$tpl->assign("unidad",strtoupper($mp[0]['descripcion']));
		$tipo=$inv[$i]['tipo'];
	}
}


else{

	$cias=ejecutar_script("select num_cia, nombre_corto from catalogo_companias where num_cia <100 order by num_cia",$dsn);

	for($i=0;$i<count($cias);$i++)
	{
		$tpl->newBlock("recibo_avio");
		$tpl->assign("num_cia",$cias[$i]['num_cia']);
		$tpl->assign("nombre",$cias[$i]['nombre_corto']);
		if((($i+1)%2)==0) $tpl->newBlock("jump");
	}
}

$tpl->printToScreen();
?>