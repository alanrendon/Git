<?php
// CAPTURA PARA AFECTIVOS DIRECTOS
// TABLA "IMPORTE_EFECTIVOS"
// PANADERIAS -- EFECTIVOS -- CAPTURA DIRECTA

//define ('IDSCREEN',1321); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "Número de compañía no existe, por favor revisalo";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_cometra_fac1.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia " . (in_array($_SESSION['iduser'], $users) ? "BETWEEN 900 AND 950" : "< 101") . " AND status = 'TRUE' ORDER BY num_cia";
$cias=ejecutar_script($sql,$dsn);

$tpl->assign("contador",count($cias));

for($i=0;$i<count($cias);$i++){
	$tpl->newBlock("cia");
	$tpl->assign("i",$i);
	if(($i + 1) == count($cias))
		$tpl->assign("next","0");
	else
		$tpl->assign("next",$i + 1);
	
	$tpl->assign("num_cia",$cias[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cias[$i]['nombre_corto']);
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