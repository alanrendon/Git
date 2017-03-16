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
$tpl->assignInclude("body","./plantillas/fac/fac_dmp_mod.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");



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

$prov = obtener_registro("catalogo_proveedores",array("num_proveedor"),array($_POST['num_proveedor']),"","",$dsn);
$tpl->assign("num_proveedor",$prov[0]['num_proveedor']);
$tpl->assign("nom_proveedor",$prov[0]['nombre']);

$tpl->assign("tabla","catalogo_productos_proveedor");


$sql = '
	SELECT
		idpresentacion
			AS value,
		descripcion
			AS text
	FROM
		tipo_presentacion
	ORDER BY
		value
';

$presentaciones = ejecutar_script($sql, $dsn);

//print_r ($_POST);
//echo count($_POST['cont']);
$var=0;
if($_POST['cont'] >0)
{
	for($i=0;$i<$_POST['cont'];$i++)
	{
		if($_POST['modificar'.$i]==1)
		{
			$tpl->newBlock("rows");
			$tpl->assign("i",$var);
//			$tpl->assign("i",$i);
			$var++;
			$tpl->assign('codmp',$_POST['codmp'.$i]);
			$tpl->assign('id',$_POST['id'.$i]);
			$mp = obtener_registro("catalogo_mat_primas",array("codmp"),array($_POST['codmp'.$i]),"","",$dsn);
			$tpl->assign('nom_mp',$mp[0]['nombre']);
	//		nombre formateado
			$tpl->assign('contenido',$_POST['contenido'.$i]);
			$tpl->assign('precio',$_POST['precio'.$i]);
			$tpl->assign('desc1',$_POST['desc1'.$i]);
			$tpl->assign('desc2',$_POST['desc2'.$i]);
			$tpl->assign('desc3',$_POST['desc3'.$i]);
			$tpl->assign('iva',$_POST['iva'.$i]);
			$tpl->assign('ieps',$_POST['ieps'.$i]);
			$tpl->assign('checked',$_POST['para_pedido'.$i]==1?'checked=""':'');
			$tpl->assign('para_pedido',$_POST['para_pedido'.$i]);

			foreach ($presentaciones as $p) {
				$tpl->newBlock('presentacion');
				$tpl->assign('value', $p['value']);
				$tpl->assign('text', $p['text']);

				if ($p['value'] == $_POST['presentacion' . $i]) {
					$tpl->assign('selected', ' selected');
				}
			}
		}
	}
	$tpl->newBlock("contador");
	$tpl->assign("cont",$var);

}
else
{
	header("location: ./fac_dmp_con.php");
	die;
}
// Imprimir el resultado
$tpl->printToScreen();

?>
