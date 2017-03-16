<?php
// ALTA DE MOVIMIENTOS BANCARIOS
// Tablas 'catalogo_mov_bancos'
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

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
$descripcion_error[1] = "El código ya existe en el catálogo de movimientos bancarios";

// --------------------------------- Insertar registro en la tabla -------------------------------------------
if (isset($_GET['tabla'])) {
	if (existe_registro($_GET['tabla'],array("cod_mov"),array($_POST['cod_mov']),$dsn)) {
		header("location: ./ban_cat_mov.php?codigo_error=1");
		die;
	}
	
	// Organizar datos
	$count = 0;
	for ($i=1; $i<=3; $i++) {
		if ($_POST['cod_banco'.$i] != "") {
			$mov['cod_mov'.$count] = $_POST['cod_mov'];
			$mov['cod_banco'.$count] = $_POST['cod_banco'.$i];
			$mov['descripcion'.$count] = $_POST['descripcion'];
			$mov['tipo_mov'.$count] = $_POST['tipo_mov'];
			$mov['entra_bal'.$count] = $_POST['entra_bal'];
			$count++;
		}
	}
	
	$db = new DBclass($dsn,$_GET['tabla'],$mov);
	$db->xinsertar();
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_cat_mov.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Asignar tabla de insercion
$tpl->assign("tabla","catalogo_mov_bancos");

// Obtener ultimo ID del catalogo
$cod_mov = nextID2("catalogo_mov_bancos","cod_mov",$dsn);

// Asignar ID
$tpl->assign("cod_mov",$cod_mov);

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>