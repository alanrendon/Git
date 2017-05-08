<?php
// CONSULTA DE TRABAJADORES
// Tabla 'catalogo_trabajadores'
// Menu Proveedores y facturas -> Trabajadores

//define ('IDSCREEN',3311); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "La panaderia no tiene expendios";
$descripcion_error[2] = "El expendio tiene rezago y no se puede borrar";

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
$tpl->assignInclude("body","./plantillas/pan/pan_exp_del.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Borrar expendio y regresar al listado
if (isset($_GET['id'])) {
	// Obtener numero de expendio
	$num_expendio = ejecutar_script("SELECT num_expendio, num_referencia FROM catalogo_expendios WHERE id = $_GET[id]",$dsn);
	
	// Verificar si el expendio tiene rezago
	$sql = "SELECT rezago FROM mov_expendios WHERE num_cia = $_GET[num_cia] AND num_expendio = ".$num_expendio[0]['num_expendio']." ORDER BY fecha DESC LIMIT 1";
	$result = ejecutar_script($sql,$dsn);
	
	$ok = TRUE;
	
	if (!$result) $ok = TRUE;
	
	if ($result && $result[0]['rezago'] > 0) $ok = FALSE;
	
	if ($ok) {
		$sql = "DELETE FROM catalogo_expendios WHERE id = $_GET[id]";
		ejecutar_script($sql,$dsn);

		// [16-Abr-2014] Insertar registro de actualizacion de panaderia
		$sql = "INSERT INTO actualizacion_panas (num_cia, iduser, metodo, parametros) VALUES ({$_REQUEST['num_cia']}, {$_SESSION['iduser']}, 'baja_expendio', 'num_cia={$_REQUEST['num_cia']}&num_referencia={$num_expendio[0]['num_referencia']}')";

		ejecutar_script($sql, $dsn);
	}
	else {
		header("location: ./pan_exp_del.php?num_cia=$_GET[num_cia]&codigo_error=2");
		die;
	}
		
}

// Mostrar listado de expendios
if (isset($_GET['num_cia'])) {
	$sql = "SELECT * FROM catalogo_expendios WHERE num_cia = $_GET[num_cia] ORDER BY num_expendio";
	$exp = ejecutar_script($sql,$dsn);
	
	if (!$exp) {
		header("location: ./pan_exp_del.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("listado");
	$tpl->assign("num_cia",$_GET['num_cia']);
	$nombre_cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]",$dsn);
	$tpl->assign("nombre_cia",$nombre_cia[0]['nombre_corto']);
	
	for ($i=0; $i<count($exp); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("id",$exp[$i]['id']);
		$tpl->assign("num_cia",$exp[$i]['num_cia']);
		$tpl->assign("num_expendio",$exp[$i]['num_expendio']);
		$tpl->assign("nombre_expendio",$exp[$i]['nombre']);
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

// DATOS PARA LA BUSQUEDA
$tpl->newBlock("cia");

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