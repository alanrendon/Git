<?php
// LISTADO DE GASTOS DE OFICINA
// Tabla 'gastos_caja'
// Menu 'pendiente'

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
$tpl->assignInclude("body","./plantillas/ros/ros_cmd_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Tipo de listado -------------------------------------------------------
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");

	$tpl->assign("fecha",date("d/m/Y"));

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

// -------------------------------- Consulta -------------------------------------------------------
$sql = "SELECT codmp,catalogo_mat_primas.nombre AS nombre_mp,num_proveedor,catalogo_proveedores.nombre AS nombre_pro,numero_fact,fecha_mov,cantidad,kilos,precio_unit,aplica_gasto,total,fecha_pago FROM compra_directa LEFT JOIN catalogo_mat_primas USING (codmp) LEFT JOIN catalogo_proveedores USING (num_proveedor) WHERE num_cia = $_GET[num_cia] AND fecha_mov = '$_GET[fecha]'";
$result = ejecutar_script($sql,$dsn);
if (!$result) {
	header("location: ./ros_cmd_con.php?mensaje=No+hay+resultados");
	die;
}

$tpl->newBlock("consulta");
$tpl->assign("num_cia",$_GET['num_cia']);
$cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]",$dsn);
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
$tpl->assign("fecha_mov",$result[0]['fecha_mov']);
$tpl->assign("fecha_pago",$result[0]['fecha_pago']);

for ($i=0; $i<count($result); $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("codmp",$result[$i]['codmp']);
	$tpl->assign("nombre_mp",$result[$i]['nombre_mp']);
	$tpl->assign("cantidad",$result[$i]['cantidad']);
	$tpl->assign("kilos",number_format($result[$i]['kilos'],2,".",","));
	$tpl->assign("precio_unit",number_format($result[$i]['precio_unit'],4,".",","));
	$tpl->assign("total",number_format($result[$i]['total'],2,".",","));
	if ($result[$i]['aplica_gasto'] == "t")
		$tpl->assign("aplica_gasto","SI");
	else
		$tpl->assign("aplica_gasto","&nbsp;");
	$tpl->assign("num_proveedor",$result[$i]['num_proveedor']);
	$tpl->assign("nombre_proveedor",$result[$i]['nombre_pro']);
	$tpl->assign("numero_fact",$result[$i]['numero_fact']);
}
$tpl->gotoBlock("consulta");
$total = ejecutar_script("SELECT sum(total) FROM compra_directa WHERE num_cia = $_GET[num_cia] AND fecha_mov = '$_GET[fecha]'",$dsn);
$tpl->assign("total",number_format($total[0]['sum'],2,'.',','));
$tpl->printToScreen();
?>
