<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$descripcion_error[1] = "No hay resultados";

// [AJAX] Obtener nombre de compañía
if (isset($_GET['c'])) {
	$sql = "SELECT nombre_corto AS nombre FROM catalogo_companias WHERE num_cia = $_GET[c]";
	$result = $db->query($sql);
	
	die(trim($result[0]['nombre']));
}

// [AJAX] Obtener empleados
if (isset($_GET['ce'])) {
	$sql = "SELECT id, num_emp, ap_paterno, ap_materno, nombre FROM catalogo_trabajadores WHERE num_cia = $_GET[ce] AND (fecha_baja IS NULL OR fecha_baja > now()::date - interval '3 month') ORDER BY ap_paterno, ap_materno, nombre";
	$result = $db->query($sql);
	
	if (!$result) die("-1");
	
	$data = "";
	foreach ($result as $i => $reg)
		$data .= "$reg[id]/$reg[num_emp]-$reg[ap_paterno] $reg[ap_materno] $reg[nombre]" . ($i < count($result) - 1 ? '|' : '');
	
	die($data);
}

if (isset($_GET['accion']) && $_GET['accion'] == 'upload') {
	// Coneccion a la base de datos de las imagenes
	$db_scans = new DBclass("pgsql://mollendo:pobgnj@127.0.0.1:5432/scans", "autocommit=yes");
	
	// Obtener RAW de la imagen
	$imgData = pg_escape_bytea(file_get_contents($_FILES['doc']['tmp_name']));
	
	// Insertar la imagen en la base de datos
	$sql = "INSERT INTO img_doc_emp (id_emp, tipo, imagen, iduser) VALUES ($_GET[id_emp], $_GET[tipo], '$imgData', $_SESSION[iduser])";
	$db_scans->query($sql);
	
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_doc_emp_alta.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$tpl->newBlock('scan');
	$tpl->assign('id_emp', $_GET['id_emp']);
	$tpl->assign('tipo', $_GET['tipo']);
	
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');

$tpl->printToScreen();
?>