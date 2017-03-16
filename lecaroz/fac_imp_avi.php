<?php
// IMPRESION DE AVISOS PARA CONTADORES SOBRE LOS TRABAJADORES PENDIENTES DE HACER UN MOVIMIENTO
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
	$campo_fecha = $_GET['tipo'] == "alta" ? "pendiente_alta" : "pendiente_baja";
	$campo_fecha_confirmacion = $_GET['tipo'] == "alta" ? "fecha_alta_imss" : "fecha_baja_imss";
	$fecha_actual = time();
	
	$tpl = new TemplatePower( "./plantillas/fac/aviso_mov.tpl" );
	$tpl->prepare();
	
	// Obtener registros de empleados con movimientos pendientes
	$sql = "SELECT ap_paterno,ap_materno,catalogo_trabajadores.nombre AS nombre,num_cia,catalogo_companias.nombre_corto AS nombre_cia,$campo_fecha FROM catalogo_trabajadores JOIN catalogo_companias USING (num_cia) WHERE num_cia BETWEEN " . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') . " AND $campo_fecha IS NOT NULL AND idcontador = $_GET[idcontador] ORDER BY $campo_fecha";
	$result = ejecutar_script($sql,$dsn);
	
	if (!$result) {
		$tpl->newBlock("cerrar");
		$tpl->printToScreen();
		die;
	}
	
	$tpl->assign("dia",date("d"));
	$tpl->assign("mes",mes_escrito(date("n")));
	$tpl->assign("anio",date("Y"));
	
	// Obtener el nombre del contador
	$nombre_contador = ejecutar_script("SELECT nombre_contador FROM catalogo_contadores WHERE idcontador = $_GET[idcontador]",$dsn);
	
	$tpl->assign("nombre_contador",$nombre_contador[0]['nombre_contador']);
	
	$tpl->assign("movimiento",$_GET['tipo'] == "alta"?"el <strong>\"Alta\"</strong>":"la <strong>\"Baja\"</strong>");
	
	$tpl->newBlock("lista");
	$tpl->assign("mov",$_GET['tipo'] == "alta"?"Alta":"Baja");
	
	$prom = 0;
	for ($i=0; $i<count($result); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("nombre",$result[$i]['ap_paterno']." ".$result[$i]['ap_materno']." ".$result[$i]['nombre']);
		$tpl->assign("nombre_cia",$result[$i]['nombre_cia']);
		$tpl->assign("fecha",$result[$i][$campo_fecha]);
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$result[$i][$campo_fecha],$temp);
		$fecha_inicial = mktime(0,0,0,$temp[2],$temp[1],$temp[3]);
		$dias = ceil(($fecha_actual - $fecha_inicial) / 86400);
		$tpl->assign("dias",$dias);
		
		$prom += $dias;
	}
	$prom = ceil($prom / $i);
	$tpl->assign("lista.dias_prom",$prom);
	
	$tpl->printToScreen();
	die;
}


// --------------------------------- Generar pantalla para pedir tipo de listado --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_imp_avi.tpl");
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