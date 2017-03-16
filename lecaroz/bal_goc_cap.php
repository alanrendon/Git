<?php
// CAPTURA DE GASTOS PAGADOS A OTRAS COMPAÑIAS
// Tabla 'gastos_otras_cia'
// Menu 'Balance->pendiente'

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

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_goc_cap.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Asignar tabla
$tpl->assign("tabla","gastos_otras_cia");

$tpl->assign("anio",date("Y"));
$tpl->assign(date("n"), "selected");

// Generar listado de compañías
$cia = ejecutar_script("SELECT * FROM catalogo_companias ORDER BY num_cia ASC",$dsn);
for ($i=0; $i<count($cia); $i++) {
	$tpl->newBlock("nombre_cia");
	$tpl->assign("num_cia",$cia[$i]['num_cia']);
	$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
}

if (!isset($_GET['tabla'])) {
	
	for ($i=0; $i<10; $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("i",$i);
		if ($i == 0)
			$tpl->assign("back",$i);
		else
			$tpl->assign("back",$i-1);
			
		if ($i < 9)
			$tpl->assign("next",$i+1);
		else
			$tpl->assign("next",$i);
	}
	$tpl->printToScreen();
	die;
}

// Reconstruir datos para captura
for ($i=0; $i<10; $i++) {
	if ($_POST['num_cia_egreso'.$i] > 0 && $_POST['concepto'.$i] != "" && $_POST['monto'.$i] > 0 && $_POST['num_cia_ingreso'.$i] > 0) {
		// Datos de de la compañía que da el prestamo
		$datos['num_cia_egreso'.$i]  = $_POST['num_cia_egreso'.$i];
		$datos['concepto'.$i] = $_POST['concepto'.$i];
		$datos['monto'.$i]    = $_POST['monto'.$i];
		$datos['fecha'.$i]    = $_POST['fecha'];
		$datos['num_cia_ingreso'.$i] = $_POST['num_cia_ingreso'.$i];
	}
}

// Insertar datos
$db  = new DBclass($dsn,$_GET['tabla'],$datos);
$db->xinsertar();

header("location: ./bal_goc_cap.php");
?>