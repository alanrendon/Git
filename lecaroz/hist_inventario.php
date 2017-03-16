<?php
// CAPTURA DE INVENTARIO
// Tabla 'inventario_real' e 'inventario_virtual'
// Menu 'No definido'

//define ('IDSCREEN',1); // ID de pantalla

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
$descripcion_error[1] = "La compañía no existe en la Base de Datos";
$descripcion_error[2] = "Fecha de captura ya se encuentra en el sistema";
$descripcion_error[3] = "Fecha incorrecta, vericar el formato (dd/mm/aaaa)";
$descripcion_error[4] = "Fecha fuera de rango, vericar el formato (dd/mm/aaaa)";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/hist_inventario.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");


// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['cod_mp'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("fecha",date("d/m/Y"));
	$tpl->assign("anio_actual",date("Y"));
	
	$gasto = obtener_registro("catalogo_mat_primas",array(),array(),"codmp","ASC",$dsn);
	for ($i=0; $i<count($gasto); $i++) {
		$tpl->newBlock("nom_mp");
		$tpl->assign("cod_mp",$gasto[$i]['codmp']);
		$tpl->assign("nombre_mp",$gasto[$i]['nombre']);
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

	$tpl->printToScreen();
	die;
}
	// Crear bloque de captura
	$tpl->newBlock("captura");
	$tpl->assign("tabla","historico_inventario_gas");
	$tpl->assign("codmp",$_GET['cod_mp']);
	$tpl->assign("fecha",$_GET['fecha']);
	
	$cia = obtener_registro("catalogo_companias",array(),array(),"num_cia","ASC",$dsn);
	$mp = obtener_registro("catalogo_mat_primas",array("codmp"),array($_GET["cod_mp"]),"","",$dsn);
	$tpl->assign("cod_nombre",$mp[0]['nombre']);
	for ($i=0; $i<count($cia); $i++) 
	{
		$tpl->newBlock("nombre_cia");
		$tpl->assign("num_cia",$cia[$i]['num_cia']);
		$tpl->assign("nombre_cia",$cia[$i]['nombre_corto']);
	}
	
	
	$num_filas = 45;
	
	for ($i=0; $i<$num_filas; $i++) 
	{
		$tpl->newBlock("fila");
		$tpl->assign("codmp",$_GET['cod_mp']);
		$tpl->assign("fecha",$_GET['fecha']);

		$tpl->assign("i",$i);
		$tpl->assign("next",$i+1);
	}
	
	$tpl->printToScreen();

?>
