<?php
// MODIFICACION DE EXPENDIOS
// Tabla 'catalogo_expendios'
// Menu 'Panaderías->Expendios'

define ('IDSCREEN',1131); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
$session->info_pantalla();

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "El expendio no existe en la Base de Datos";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

//
if (!isset($_GET['compania'])) {
	$tpl->newBlock("buscar");
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

	$tpl->printToScreen();
	die();	
}

// ------------------------------- Modificar expendio -------------------------------------------------------
// Verificar si existe la compañía y el expendio
if (!existe_registro("catalogo_expendios", array("num_cia","num_expendio"), array($_GET['compania'],$_GET['expendio']), $dsn)) {
	header("location: ./pan_exp_mod.php?codigo_error=1");
	die();
}

$reg = obtener_registro("catalogo_expendios",array("num_cia","num_expendio"),array($_GET['compania'],$_GET['expendio']),"","",$dsn);

$tpl->newBlock("modificacion");

// Seleccionar tabla
$tpl->assign("tabla",$session->tabla);

$tpl->assign("num_cia",            $reg[0]['num_cia']);
$tpl->assign("num_expendio",       $reg[0]['num_expendio']);
$tpl->assign("num_referencia",     $reg[0]['num_referencia']);
$tpl->assign("nombre",             $reg[0]['nombre']);
$tpl->assign("direccion",          $reg[0]['direccion']);
$tpl->assign("porciento_ganancia", $reg[0]['porciento_ganancia']);
if (!in_array($_SESSION['iduser'], array(4, 1, 19))) $tpl->assign("readonly", "readonly");
$tpl->assign("importe_fijo",       $reg[0]['importe_fijo']);
$tpl->assign($reg[0]['total_fijo'] == "f" ? "checked_false" : "checked_true", "checked");
$tpl->assign('nota_' . $reg[0]['notas'], ' checked');
$tpl->assign('dev_' . ($reg[0]['aut_dev'] != '' ? $reg[0]['aut_dev'] : 'f'), ' checked');
$tpl->assign('num_cia_exp', $reg[0]['num_cia_exp']);

$tipos = obtener_registro("tipo_expendio",array(),array(),"","",$dsn);

for ($i=0; $i<count($tipos)-1; $i++) {
	if ($tipos[$i]['idtipoexpendio'] == $reg[0]['tipo_expendio']) {
		$tpl->newBlock("tipo_selected");
		$tpl->assign("valuetipo",$reg[0]['tipo_expendio']);
		$tpl->assign("nametipo",$tipos[$i]['descripcion']);
		$tpl->gotoBlock("modificacion");
	}
	else {
		$tpl->newBlock("tipo");
		$tpl->assign("valuetipo",$tipos[$i]['idtipoexpendio']);
		$tpl->assign("nametipo",$tipos[$i]['descripcion']);
		$tpl->gotoBlock("modificacion");
	}
}

$ag = ejecutar_script('SELECT idagven, nombre FROM catalogo_agentes_venta ORDER BY nombre', $dsn);
if ($ag)
	foreach ($ag as $a) {
		$tpl->newBlock('agente');
		$tpl->assign('id', $a['idagven']);
		$tpl->assign('nombre', $a['nombre']);
		if ($a['idagven'] == $reg[0]['idagven']) $tpl->assign('selected', ' selected');
	}

// Imprimir el resultado
$tpl->printToScreen();

?>