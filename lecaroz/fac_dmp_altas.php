<?php
// ALTA DE DESCUENTOS MATERIA PRIMAS
// Tabla 'catalogo_productos_proveedor'
// Menu Proveedores y facturas -> Alta de descuentos

//define ('IDSCREEN',3512); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de proveedor no existe en la Base de Datos.";
$descripcion_error[2] = "Código de producto no existe en la Base de Datos.";


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

$tpl->assignInclude("body","./plantillas/fac/fac_dmp_altas.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
//$tpl->assign("tabla",$session->tabla);

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

// Crear los renglones
for ($i=0;$i<10;$i++) {
	$tpl->newBlock("rows");
	$tpl->assign("i",$i);
	$tpl->gotoBlock("_ROOT");
	
	foreach ($presentaciones as $p) {
		$tpl->newBlock('presentacion');
		$tpl->assign('value', $p['value']);
		$tpl->assign('text', $p['text']);
	}
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

// Imprimir el resultado
$tpl->printToScreen();
?>