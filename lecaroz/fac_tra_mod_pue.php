<?php
// CONSULTA DE TRABAJADORES
// Tabla 'catalogo_trabajadores'
// Menu Proveedores y facturas -> Trabajadores

//define ('IDSCREEN',3311); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_tra_mod_pue.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	$sql = "UPDATE catalogo_trabajadores SET cod_puestos = $_POST[puesto] WHERE id = $_POST[id]";
	$db->query($sql);
	
	$puesto = $db->query("SELECT descripcion FROM catalogo_puestos WHERE cod_puestos = $_POST[puesto]");
	$tpl->newBlock("cerrar");
	$tpl->assign("i", $_POST['i']);
	$tpl->assign("descripcion", $puesto[0]['descripcion']);
	$tpl->printToScreen();
	die;
}

$sql = "SELECT cod_puestos FROM catalogo_trabajadores WHERE id = $_GET[id]";
$cod_puesto = $db->query($sql);

$puesto = $db->query("SELECT * FROM catalogo_puestos ORDER BY cod_puestos");

$sql = "SELECT nombre, ap_paterno, ap_materno, catalogo_puestos.descripcion AS puesto, catalogo_turnos.descripcion AS turno FROM catalogo_trabajadores LEFT JOIN catalogo_puestos USING (cod_puestos) LEFT JOIN catalogo_turnos USING (cod_turno)";
$sql .= " WHERE id = $_GET[id]";
$datos = $db->query($sql);

$tpl->newBlock("modificar");
$tpl->assign("nombre", "{$datos[0]['ap_paterno']} {$datos[0]['ap_materno']} {$datos[0]['nombre']}");
$tpl->assign("puesto", $datos[0]['puesto']);
$tpl->assign("turno", $datos[0]['turno']);
$tpl->assign("id", $_GET['id']);
$tpl->assign("i", $_GET['i']);
for ($i = 0; $i < count($puesto); $i++) {
	$tpl->newBlock("puesto");
	$tpl->assign("puesto", $puesto[$i]['descripcion']);
	$tpl->assign("id", $puesto[$i]['cod_puestos']);
	if ($puesto[$i]['cod_puestos'] == $cod_puesto[0]['cod_puestos']) $tpl->assign("selected", "selected");
}

// Imprimir el resultado
$tpl->printToScreen();
?>