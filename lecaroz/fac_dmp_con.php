<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No tenemos productos registrados para este proveedor";
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
$tpl->assignInclude("body","./plantillas/fac/fac_dmp_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_proveedor'])) {
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


$tpl->newBlock('listado');
$tpl->assign("tabla","catalogo_productos_proveedor");
$prov = obtener_registro("catalogo_proveedores",array("num_proveedor"),array($_GET['num_proveedor']),"","",$dsn);

$tpl->assign("num_proveedor",$prov[0]['num_proveedor']);
$tpl->assign("nom_proveedor",$prov[0]['nombre']);


$sql="SELECT * FROM catalogo_productos_proveedor where num_proveedor='".$_GET['num_proveedor']."' order by codmp";

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
		$tpl->assign('presentacion', $reg[$i]['presentacion']);

		$presentacion = ejecutar_script('SELECT descripcion FROM tipo_presentacion WHERE idpresentacion = ' . $reg[$i]['presentacion'], $dsn);

		$tpl->assign('tipo_presentacion', $presentacion[0]['descripcion']);

		$tpl->assign('contenido1',number_format($reg[$i]['contenido'],2,'.',','));
		$tpl->assign('precio1',number_format($reg[$i]['precio'],8,'.',','));

		if($reg[$i]['desc1'] !="" and $reg[$i]['desc1']>0)
			$tpl->assign('desc11',number_format($reg[$i]['desc1'],2,'.',','));

		if($reg[$i]['desc2'] !="" and $reg[$i]['desc2']>0)
			$tpl->assign('desc21',number_format($reg[$i]['desc2'],2,'.',','));

		if($reg[$i]['desc3'] !="" and $reg[$i]['desc3']>0)
			$tpl->assign('desc31',number_format($reg[$i]['desc3'],2,'.',','));

		if($reg[$i]['iva'] !="" and $reg[$i]['iva']>0)
			$tpl->assign('iva1',number_format($reg[$i]['iva'],2,'.',','));

		if($reg[$i]['ieps'] !="" and $reg[$i]['ieps']>0)
			$tpl->assign('ieps1',number_format($reg[$i]['ieps'],2,'.',','));

		$tpl->assign('contenido',$reg[$i]['contenido']);
		$tpl->assign('precio',$reg[$i]['precio']);
		$tpl->assign('desc1',$reg[$i]['desc1']);
		$tpl->assign('desc2',$reg[$i]['desc2']);
		$tpl->assign('desc3',$reg[$i]['desc3']);
		$tpl->assign('iva',$reg[$i]['iva']);
		$tpl->assign('ieps',$reg[$i]['ieps']);
		$tpl->assign('para_pedido_val', $reg[$i]['para_pedido'] == 't' ? 1 : 0);
		$tpl->assign('para_pedido', $reg[$i]['para_pedido'] == 't' ? 'SI' : '&nbsp;');

	}
}
else
{
	header("location: ./fac_dmp_con.php?codigo_error=1");
	die;
}





// Imprimir el resultado
$tpl->printToScreen();

?>
