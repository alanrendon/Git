<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No tenemos productos registrados para esta compañía";
//$descripcion_error[2] = "Número de Gasto no existe en la Base de Datos, revisa bien codigo del gasto";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/ros/ros_pesos_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("obtener_datos");
	
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

if($_GET['num_cia'] !=""){
	$tpl->newBlock('listado');
	$tpl->assign("tabla","precios_guerra");
	$cia = obtener_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),"","",$dsn);
	
	$tpl->assign("num_cia",$cia[0]['num_cia']);
	$tpl->assign("nom_cia",$cia[0]['nombre_corto']);
	
	
	$sql="SELECT * FROM pesos_companias where num_cia='".$_GET['num_cia']."' order by codmp";
	
	$reg=ejecutar_script($sql,$dsn);
	//print_r ($reg);
	$tpl->assign("count",count($reg));
	if($reg)
	{
		for($i=0;$i<count($reg);$i++)
		{
			$tpl->newBlock("rows");
			$tpl->assign("i",$i);
			$tpl->assign('codmp',$reg[$i]['codmp']);
			$tpl->assign('id',$reg[$i]['id']);
			$mp = obtener_registro("catalogo_mat_primas",array("codmp"),array($reg[$i]['codmp']),"","",$dsn);
			$tpl->assign('nom_mp',$mp[0]['nombre']);
	//		nombre formateado
			$tpl->assign('peso_minimo1',number_format($reg[$i]['peso_min'],2,'.',','));
			$tpl->assign('peso_maximo1',number_format($reg[$i]['peso_max'],2,'.',','));
	
			$tpl->assign('peso_minimo',$reg[$i]['peso_min']);
			$tpl->assign('peso_maximo',$reg[$i]['peso_max']);
		}
	}
	else
	{
		header("location: ./ros_pesos_con.php?codigo_error=1");
		die;
	}
}
else{
	$tpl->newBlock('listado1');
	
	$sql="SELECT pesos_companias.*, catalogo_mat_primas.nombre,nombre_corto FROM pesos_companias join catalogo_mat_primas using(codmp) join catalogo_companias using(num_cia) order by num_cia, codmp";
	$pesos = ejecutar_script($sql,$dsn);
	$aux1=0;
	for($i=0;$i<count($pesos);$i++){
		$tpl->newBlock("rows2");
		if($aux1!= $pesos[$i]["num_cia"]){
			$tpl->newBlock("cia2");
			$tpl->assign("num_cia",$pesos[$i]['num_cia']);
			$tpl->assign("nombre_cia",$pesos[$i]['nombre_corto']);
			$tpl->gotoBlock("rows2");
		}
		$tpl->assign("codmp",$pesos[$i]['codmp']);
		$tpl->assign("nombre_mp",$pesos[$i]['nombre']);
		$tpl->assign("maximo",$pesos[$i]['peso_max']);
		$tpl->assign("minimo",$pesos[$i]['peso_min']);
		$aux1 = $pesos[$i]["num_cia"];
	}

}
// Imprimir el resultado
$tpl->printToScreen();

?>