<?php
// RESULTADOS DE CONCILIACION
// Tabla 'estado_cuenta'
// Menu

//define ('IDSCREEN',6214); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
//$descripcion_error[1] = "El código no existe en la Base de Datos.";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Almacenar datos ---------------------------------------------------------
if (isset($_GET['tabla'])) {
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_res_next.tpl");
$tpl->prepare();

$tpl->newBlock("resultados");
$tpl->assign("tabla","estado_cuenta");

$tpl->assign("num_cia",   $_SESSION['con']['num_cia'.$_SESSION['con']['next']]);
$tpl->assign("nombre_cia",$_SESSION['con']['nombre_cia'.$_SESSION['con']['next']]);
$tpl->assign("cuenta",    $_SESSION['con']['cuenta'.$_SESSION['con']['next']]);
$tpl->assign("fecha_con", $_SESSION['con']['fecha_con']);

for ($i=0; $i<10; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i",$i);
	
	if ($i > 0)
		$tpl->assign("back",$i-1);
	else
		$tpl->assign("back",10-1);
	
	if ($i < 10-1)
		$tpl->assign("next",$i+1);
	else
		$tpl->assign("next",0);
		
	$tpl->assign("importe",isset($_SESSION['dep'])?$_SESSION['dep']['importe'.$i]:"");
	$tpl->assign("concepto",isset($_SESSION['dep'])?$_SESSION['dep']['concepto'.$i]:"");

	for ($j=0; $j<count($mov); $j++) {
		$tpl->newBlock("mov");
		$tpl->assign("id",$mov[$j]['cod_mov']);
		$tpl->assign("descripcion",$mov[$j]['descripcion']);
		$tpl->assign("selected",isset($_SESSION['dep'])?(($_SESSION['dep']['cod_mov'.$i] == $mov[$j]['cod_mov'])?"selected":""):"");
	}
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", "La compañía no. $_GET[codigo_error] no tiene saldo inicial");	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

// Imprimir el resultado
$tpl->printToScreen();
?>