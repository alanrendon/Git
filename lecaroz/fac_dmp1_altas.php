<?php
// CAPTURA PRODUCTOS POR PROVEEDOR
// Tabla 'catalogo_productos_proveedor'
// Menu ''

//define ('IDSCREEN',3512); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe en la Base de Datos, revisa bien la compañia";
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
$tpl->assignInclude("body","./plantillas/fac/fac_dmp1_altas.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
//$tpl->assign("tabla",$session->tabla);
$tpl->assign("tabla",'catalogo_productos_proveedor');

$captura=true;

if (existe_registro("catalogo_proveedores", array("num_proveedor"), array($_POST['num_proveedor']), $dsn))
	{
		$proveedor= obtener_registro("catalogo_proveedores",array("num_proveedor"),array($_POST['num_proveedor']),"","",$dsn);
		$tpl->newBlock("prov_ok");
		$tpl->assign("num_proveedor1",$_POST['num_proveedor']);
		$tpl->assign("nombre",$proveedor[0]['nombre']);
	}
else
	{
		$tpl->newBlock("prov_error");
		$tpl->assign("num_proveedor1",$_POST['num_proveedor']);
		$tpl->assign("nombre","NO EXISTE");
	}




for($i=0;$i<10;$i++)
{
	if ($_POST['codmp'.$i] != "")
		{
			if (existe_registro("catalogo_mat_primas", array("codmp"), array($_POST['codmp'.$i]), $dsn))
			{
				$mprima = obtener_registro("catalogo_mat_primas",array("codmp"),array($_POST['codmp'.$i]),"","",$dsn);
				$nombre = $mprima[0]['nombre'];
				$ok=true;
			}
			else
			{
				$ok=false;
				$nombre="No existe";
			}
			
			$presentacion = ejecutar_script('SELECT descripcion FROM tipo_presentacion WHERE idpresentacion = ' . $_POST['presentacion' . $i], $dsn);
			
			$tpl->newBlock("rows");
			$tpl->assign("i",$i);
			$tpl->assign("codmp",$_POST['codmp'.$i]);
			$tpl->assign('presentacion', $_POST['presentacion' . $i]);
			$tpl->assign('tipo_presentacion', $presentacion[0]['descripcion']);
			$tpl->assign("contenido",$_POST['contenido'.$i]);
			$tpl->assign("precio",$_POST['precio'.$i]);
			$tpl->assign("desc1",$_POST['desc1'.$i]);
			$tpl->assign("desc2",$_POST['desc2'.$i]);
			$tpl->assign("desc3",$_POST['desc3'.$i]);
			$tpl->assign("iva",$_POST['iva'.$i]);
			$tpl->assign("ieps",$_POST['ieps'.$i]);
			$tpl->assign("num_proveedor",$_POST['num_proveedor']);
		
			if ($ok)
			{
				$tpl->newBlock("rows_ok");
				$tpl->assign("codmp1",$_POST['codmp'.$i]);
				$tpl->assign("nombre1",$nombre);
				$tpl->gotoBlock("rows");
			}
			else
			{
				$tpl->newBlock("rows_error");
				$tpl->assign("codmp1",$_POST['codmp'.$i]);
				$tpl->assign("nombre1",$nombre);
				$tpl->gotoBlock("rows");
			}
		//$i++;
		$captura &= $ok;
		
	}
}
if($captura)
{
	$tpl->newBlock("captura");

}


// Asignar valores a los campos del formulario
// EJEMPLO.:
//$tpl->assign("num_cia",$result->num_cia);

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

// Imprimir el resultado
$tpl->printToScreen();
?>