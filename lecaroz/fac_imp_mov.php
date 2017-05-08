<?php
// IMPRESION DE AVISOS PARA CONTADORES SOBRE LOS TRABAJADORES CON ALTA O BAJA DEL IMSS
// Tabla ''
// Menu ''

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
$descripcion_error[1] = "No hay resultados";

// Generar listado
if (isset($_GET['tipo'])) {
	$impresion = $_GET['tipo'] == "altas" ? "imp_alta" : "imp_baja";
	$fecha_actual = time();
	
	$tpl = new TemplatePower( "./plantillas/fac/aviso.tpl" );
	$tpl->prepare();
	
	// Obtener registros de empleados con movimientos pendientes
	$sql = "SELECT id,ap_paterno,ap_materno,catalogo_trabajadores.nombre AS nombre,num_cia,catalogo_companias.nombre_corto AS nombre_corto,catalogo_companias.nombre AS nombre_cia FROM catalogo_trabajadores JOIN catalogo_companias USING (num_cia) WHERE num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . " AND $impresion = 'TRUE' AND idcontador = $_GET[idcontador] ORDER BY num_cia";
	$result = ejecutar_script($sql,$dsn);
	
	if (!$result) {
		$tpl->newBlock("cerrar");
		$tpl->printToScreen();
		die;
	}
	
	// Quitar marcas de impresión
	//$sql = "UPDATE catalogo_trabajadores SET $impresion = 'FALSE' WHERE id IN (";
	$sql = "UPDATE catalogo_trabajadores SET ultimo = 'TRUE' WHERE id IN (";
	for ($i=0; $i<count($result); $i++)
		$sql .= $result[$i]['id'] . ($i < count($result) - 1 ? "," : ")");
	
	ejecutar_script($sql,$dsn);
	
	$tpl->assign("dia",date("d"));
	$tpl->assign("mes",mes_escrito(date("n")));
	$tpl->assign("anio",date("Y"));
	
	// Obtener nombre del contador
	$nombre_contador = ejecutar_script("SELECT nombre_contador FROM catalogo_contadores WHERE idcontador = $_GET[idcontador]",$dsn);
	
	$tpl->assign("nombre_contador",$nombre_contador[0]['nombre_contador']);
	
	$tpl->assign("movimiento",$_GET['tipo'] == "altas"?"<strong>\"Alta\"</strong>":"<strong>\"Baja\"</strong>");
	
	$tpl->newBlock("lista");
	for ($i=0; $i<count($result); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("nombre",$result[$i]['ap_paterno']." ".$result[$i]['ap_materno']." ".$result[$i]['nombre']);
		$tpl->assign("nombre_cia",$result[$i]['nombre_cia']." (".$result[$i]['nombre_corto'].")");
	}
	
	$tpl->printToScreen();
	die;
}


// --------------------------------- Generar pantalla para pedir tipo de listado --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_imp_mov.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$sql = "SELECT * FROM catalogo_contadores ORDER BY nombre_contador";
$contador = ejecutar_script($sql,$dsn);
for ($i=0; $i<count($contador); $i++) {
	$tpl->newBlock("contador");
	$tpl->assign("idcontador",$contador[$i]['idcontador']);
	$tpl->assign("contador",$contador[$i]['nombre_contador']);
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
?>