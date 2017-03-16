<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die('location: offline.htm');

if (isset($_GET['cs'])) {
	$sql = "SELECT id FROM catalogo_nombres WHERE num = $_GET[cs]";
	$result = $db->query($sql);
	
	if ($result) echo 1;
	else echo 0;
	
	die();
}

if (isset($_GET['cod'])) {
	$sql = "SELECT concepto, tipo FROM cat_conceptos_descuentos WHERE cod = $_GET[cod]";
	$result = $db->query($sql);
	
	if ($result) echo "$_GET[i]|{$result[0]['concepto']}|" . ($result[0]['tipo'] == 1 ? 'COMPRA' : 'PAGO');
	else echo -$_GET['i'] . '|';
	
	die();
}

if (isset($_POST['num_proveedor'])) {
	$data = $_POST;
	if (!isset($_POST['para_abono']))
		$data['para_abono'] = 'FALSE';
	
	$sql = $db->preparar_insert("catalogo_proveedores", $_POST) . "\n;";
	$sql .= "UPDATE catalogo_proveedores SET nombre = upper(nombre), rfc = upper(rfc), direccion = upper(direccion), contacto = upper(contacto), referencia = upper(referencia), con_desc1 = upper(con_desc1), con_desc2 = upper(con_desc2), con_desc3 = upper(con_desc3), con_desc4 = upper(con_desc4), contacto1 = upper(contacto1), contacto2 = upper(contacto2), contacto3 = upper(contacto3), contacto4 = upper(contacto4), observaciones = upper(observaciones) WHERE num_proveedor = $_POST[num_proveedor];\n";
	$db->query($sql);
	
	header("location: ./fac_pro_altas_v3.php");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_pro_altas_v3.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$sql = 'SELECT num_proveedor AS num_pro FROM catalogo_proveedores' . ($_SESSION['iduser'] >= 28 ? ' WHERE num_proveedor BETWEEN 9001 AND 9999' : '') . ' ORDER BY num_pro';
$pros = $db->query($sql);

function ultimoPro() {
	global $pros, $users;
	
	if (!$pros)
		return $_SESSION['iduser'] >= 28 ? 9001 : 1;
	
	$num = $_SESSION['iduser'] >= 28 ? 9001 : 1;
	foreach ($pros as $pro)
		if ($pro['num_pro'] == $num)
			$num++;
		else
			return $num;
	
	return $num;
}

$tpl->assign('num_pro', ultimoPro());

$bancos = $db->query("SELECT idbanco, num_banco, clave, nombre FROM catalogo_bancos ORDER BY idbanco");
foreach ($bancos as $ban) {
	$tpl->newBlock("banco");
	$tpl->assign("idbanco", $ban['idbanco']);
	$tpl->assign('num', $ban['num_banco']);
	$tpl->assign('clave', $ban['clave']);
	$tpl->assign("nombre", $ban['nombre']);
}

$ent = $db->query('SELECT "IdEntidad", "Entidad" FROM catalogo_entidades ORDER BY "IdEntidad"');
foreach ($ent as $e) {
	$tpl->newBlock("entidad");
	$tpl->assign("IdEntidad", $e['IdEntidad']);
	$tpl->assign("Entidad", $e['Entidad']);
}

if ($_SESSION['iduser'] >= 28 || $_SESSION['iduser'] == 1)
	$tpl->newBlock('zap');

// Si viene de una pgina que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>