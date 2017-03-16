<?php
// CONSULTA DE CUENTAS POR COMPAÑÍA
// Tablas ''
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_ras_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign("message",$descripcion_error[$_GET['codigo_error']]);	
		$tpl->printToScreen();
		die;
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
		$tpl->printToScreen();
		die;
	}
	


$sql = "SELECT num_cia,nombre,nombre_corto FROM catalogo_companias WHERE" . (in_array($_SESSION['iduser'], $users) ? " num_cia BETWEEN 900 AND 950" : " num_cia < 200") . " order by num_cia";

$cia = ejecutar_script($sql,$dsn);

if (!$cia) {
	header("location: ./ban_ras_con.php?codigo_error=1");
	die;
}

$numfilas_x_hoja = 57;
$numfilas = $numfilas_x_hoja;
for ($i=0; $i<count($cia); $i++) {
	if ($numfilas == $numfilas_x_hoja) {
		$tpl->newBlock("listado");
		$tpl->assign("dia",date("j"));
		$tpl->assign("mes",mes_escrito(date("n")));
		$tpl->assign("anio",date("Y"));
		
		$numfilas = 0;
	}
	$tpl->newBlock("fila");
	$tpl->assign("num_cia",$cia[$i]['num_cia']);
	$tpl->assign("nombre_corto",$cia[$i]['nombre_corto']);
	$tpl->assign("nombre",$cia[$i]['nombre']);
	
	$numfilas++;
}

$tpl->printToScreen();
?>