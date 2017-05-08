<?php
// ALTA DE DESCUENTOS MATERIA PRIMAS
// Tabla 'catalogo_productos_proveedor'
// Menu Proveedores y facturas -> 

//define ('IDSCREEN',); //ID de pantalla sin ID


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";
$descripcion_error[2] = "El número de factura ya existe en la Base de Datos.";

$db = new DBclass($dsn);

// Insertar datos
if (isset($_POST['id'])) {
	// Obtener movimientos a insertar
	$sql = "SELECT * FROM entrada_mp_temp WHERE id IN (";
	for ($i=0; $i<count($_POST['id']); $i++)
		$sql .= $_POST['id'][$i] . ($i < count($_POST['id']) - 1 ? ", " : ")");
	$mp = $db->query($sql);
	
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_POST['fecha'], $fecha);
	
	$fecha_his = date("d/m/Y", mktime(0,0,0,$fecha[2],0,$fecha[3]));
	
	$sql = "";
	for ($i=0; $i<count($mp); $i++) {
		if ($id = $db->query("SELECT idinv FROM inventario_real WHERE num_cia = {$mp[$i]['num_cia']} AND codmp = {$mp[$i]['codmp']}")) {
			$sql .= "UPDATE inventario_real SET existencia = existencia + {$mp[$i]['cantidad']}, precio_unidad = {$mp[$i]['precio_unidad']} WHERE idinv = {$id[0]['idinv']};\n";
			$sql .= "INSERT INTO mov_inv_real (num_cia,codmp,fecha,tipo_mov,cantidad,precio_unidad,total_mov,descripcion) ";
			$sql .= "SELECT num_cia,codmp,'$_POST[fecha]','FALSE',cantidad,precio_unidad,cantidad*precio_unidad,descripcion FROM entrada_mp_temp WHERE id = {$mp[$i]['id']};\n";
		}
		else {
			$sql .= "INSERT INTO inventario_real (num_cia,codmp,existencia,precio_unidad) SELECT num_cia,codmp,cantidad,precio_unidad FROM entrada_mp_temp WHERE id = {$mp[$i]['id']};\n";
			$sql .= "INSERT INTO historico_inventario (num_cia,codmp,existencia,precio_unidad,fecha) SELECT num_cia,codmp,0,0,'$fecha_his' FROM entrada_mp_temp WHERE id = {$mp[$i]['id']};\n";
			$sql .= "INSERT INTO mov_inv_real (num_cia,codmp,fecha,tipo_mov,cantidad,precio_unidad,total_mov,descripcion) ";
			$sql .= "SELECT num_cia,codmp,'$_POST[fecha]','FALSE',cantidad,precio_unidad,cantidad*precio_unidad,descripcion FROM entrada_mp_temp WHERE id = {$mp[$i]['id']};\n";
		}
	}
	
	// Borrar movimientos de entrada_mp_temp
	$sql .= "DELETE FROM entrada_mp_temp WHERE id IN (";
	for ($i=0; $i<count($_POST['id']); $i++)
		$sql .= $_POST['id'][$i] . ($i < count($_POST['id']) - 1 ? ", " : ");\n");
	
	$db->comenzar_transaccion();
	$db->query($sql);
	$db->terminar_transaccion();
	$db->desconectar();
	
	header("location: ./fac_ing_inv.php");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_ing_inv.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	
	$tpl->assign("fecha",date("d/m/Y", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"))));
	
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
	$db->desconectar();
	die;
}

$sql = "SELECT id,num_cia,codmp,catalogo_mat_primas.nombre AS nombremp,fecha,cantidad,precio_unidad,entrada_mp_temp.descripcion AS descripcion,num_fact FROM entrada_mp_temp LEFT JOIN catalogo_mat_primas USING(codmp)" . ($_GET['num_cia'] > 0 ? " WHERE num_cia = $_GET[num_cia]" : "") . " ORDER BY num_cia, num_fact, codmp";
$result = $db->query($sql);

if (!$result) {
	header("location: ./fac_ing_inv.php?codigo_error=1");
	$db->desconectar();
	die;
}

$tpl->newBlock("listado");
$tpl->assign("fecha", $_GET['fecha']);

$num_cia = 0;
for ($i=0; $i<count($result); $i++) {
	if ($num_cia != $result[$i]['num_cia']) {
		if ($num_cia != NULL)
			$tpl->assign("cia.fin", $i);
		
		$num_cia = $result[$i]['num_cia'];
		
		$tpl->newBlock("cia");
		$tpl->assign("ini", $i);
		$tpl->assign("num_cia", $num_cia);
		$nombre_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $num_cia");
		$tpl->assign("nombre_cia", $nombre_cia[0]['nombre_corto']);
	}
	$tpl->newBlock("fila");
	$tpl->assign("id", $result[$i]['id']);
	$tpl->assign("codmp", $result[$i]['codmp']);
	$tpl->assign("nombremp", $result[$i]['nombremp']);
	$tpl->assign("cantidad", number_format($result[$i]['cantidad'],2,".",","));
	$tpl->assign("precio_unidad", number_format($result[$i]['precio_unidad'],2,".",","));
	$tpl->assign("num_fact", $result[$i]['num_fact']);
	$tpl->assign("fecha", $result[$i]['fecha']);
}
if ($num_cia != NULL)
	$tpl->assign("cia.fin", $i);

$tpl->printToScreen();
$db->desconectar();
?>