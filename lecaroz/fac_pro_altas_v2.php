<?php
// MODIFICACION DE COMPAÑÍAS V2
// Tabla 'catalogo_companias'
// Menu 'pendiente'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die('location: offline.htm');

$descripcion_error[1] = "La compañía ya existe en el catálogo";

$users = array(28, 29, 30, 31);

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_pro_altas_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Obtener datos de la compañía a modificar ---------------------------------
if (isset($_POST['num_proveedor'])) {
	$_SESSION['pro_alta'] = $_POST;
	$_SESSION['pro_alta']['nombre'] = trim(str_replace(array("\"", "'"), "", $_POST['nombre']));
	
	if ($db->query("SELECT num_proveedor FROM catalogo_proveedores WHERE num_proveedor = $_POST[num_proveedor]")) {
		header("location: ./fac_pro_altas_v2.php?codigo_error=1");
		die;
	}
	
	$sql = $db->preparar_insert("catalogo_proveedores", $_SESSION['pro_alta']) . "\n;";
	$sql .= "UPDATE catalogo_proveedores SET nombre = upper(nombre), rfc = upper(rfc), direccion = upper(direccion), contacto = upper(contacto), con_desc1 = upper(con_desc1), con_desc2 = upper(con_desc2), con_desc3 = upper(con_desc3), con_desc4 = upper(con_desc4), contacto1 = upper(contacto1), contacto2 = upper(contacto2), contacto3 = upper(contacto3), contacto4 = upper(contacto4) WHERE num_proveedor = $_POST[num_proveedor];\n";
	$db->query($sql);
	
	unset($_SESSION['pro_alta']);
	header("location: ./fac_pro_altas_v2.php");
	die;
}

// -------------------------------- Modificar -------------------------------------------------------
$sql = "SELECT num_proveedor AS num_pro FROM catalogo_proveedores" . (in_array($_SESSION['iduser'], $users) ? " WHERE num_proveedor BETWEEN 9001 AND 9999" : "") . " ORDER BY num_pro";
$pros = $db->query($sql);

function ultimoPro() {
	global $pros, $users;
	
	if (!$pros)
		return in_array($_SESSION['iduser'], $users) ? 9001 : 1;
	
	$num = in_array($_SESSION['iduser'], $users) ? 9001 : 1;
	foreach ($pros as $pro)
		if ($pro['num_pro'] == $num)
			$num++;
		else
			return $num;
	
	return $num;
}

if (in_array($_SESSION['iduser'], $users)) $tpl->assign("readonly", "readonly");

if (isset($_SESSION['pro_alta'])) {
	$tpl->assign("num_pro", $_SESSION['pro_alta']['num_proveedor']);
	$tpl->assign("nombre", $_SESSION['pro_alta']['nombre']);
	$tpl->assign("dir", $_SESSION['pro_alta']['direccion']);
	$tpl->assign("tel1", $_SESSION['pro_alta']['telefono1']);
	$tpl->assign("tel2", $_SESSION['pro_alta']['telefono2']);
	$tpl->assign("fax", $_SESSION['pro_alta']['fax']);
	$tpl->assign("email", $_SESSION['pro_alta']['email']);
	$tpl->assign("rfc", $_SESSION['pro_alta']['rfc']);
	$tpl->assign("tipo_" . $_SESSION['pro_alta']['tipopersona'], "checked");
	$tpl->assign("prio_" . $_SESSION['pro_alta']['prioridad'], "checked");
	$tpl->assign("contacto", $_SESSION['pro_alta']['contacto']);
	$tpl->assign("cuenta", $_SESSION['pro_alta']['cuenta']);
	$tpl->assign("plaza", $_SESSION['pro_alta']['plaza_banxico']);
	$tpl->assign("suc", $_SESSION['pro_alta']['sucursal']);
	$tpl->assign("san_" . $_SESSION['pro_alta']['san'], "checked");
	$tpl->assign("abono_" . $_SESSION['pro_alta']['para_abono'], "checked");
	$tpl->assign("trans_" . $_SESSION['pro_alta']['trans'], "checked");
	$tpl->assign("tipopago_" . $_SESSION['pro_alta']['idtipopago'], "selected");
	$tpl->assign("resta_" . $_SESSION['pro_alta']['restacompras'], "checked");
	$tpl->assign("tipopro_" . $_SESSION['pro_alta']['idtipoproveedor']);
	$tpl->assign("tiempo", $_SESSION['pro_alta']['tiempoentrega']);
	$tpl->assign("desc1", $_SESSION['pro_alta']['desc1']);
	$tpl->assign("con_desc1", $_SESSION['pro_alta']['con_desc1']);
	$tpl->assign("desc2", $_SESSION['pro_alta']['desc2']);
	$tpl->assign("con_desc2", $_SESSION['pro_alta']['con_desc2']);
	$tpl->assign("desc3", $_SESSION['pro_alta']['desc3']);
	$tpl->assign("con_desc3", $_SESSION['pro_alta']['con_desc3']);
	$tpl->assign("desc4", $_SESSION['pro_alta']['desc4']);
	$tpl->assign("con_desc4", $_SESSION['pro_alta']['con_desc4']);
	$tpl->assign('tipo_doc_' . $_SESSION['pro_alta']['tipo_doc'], 'checked');
	$tpl->assign('contacto1', $_SESSION['pro_alta']['contacto1']);
	$tpl->assign('contacto2', $_SESSION['pro_alta']['contacto2']);
	$tpl->assign('contacto3', $_SESSION['pro_alta']['contacto3']);
	$tpl->assign('contacto4', $_SESSION['pro_alta']['contacto4']);
	/*$tpl->assign('tipo_desc1_' . $_SESSION['pro_alta']['tipo_desc1'], ' checked');
	$tpl->assign('tipo_desc2_' . $_SESSION['pro_alta']['tipo_desc2'], ' checked');
	$tpl->assign('tipo_desc3_' . $_SESSION['pro_alta']['tipo_desc3'], ' checked');
	$tpl->assign('tipo_desc4_' . $_SESSION['pro_alta']['tipo_desc4'], ' checked');*/
	
	unset($_SESSION['pro_alta']);
}
else {
	$tpl->assign("num_pro", ultimoPro());
	$tpl->assign("tipo_TRUE", "checked");
	$tpl->assign("prio_TRUE", "checked");
	$tpl->assign("san_FALSE", "checked");
	$tpl->assign("abono_TRUE", "checked");
	$tpl->assign("trans_FALSE", "checked");
	$tpl->assign("resta_FALSE", "checked");
	$tpl->assign("tipo_doc_1", 'checked');
}

$bancos = $db->query("SELECT idbanco, nombre FROM catalogo_bancos ORDER BY idbanco");
foreach ($bancos as $ban) {
	$tpl->newBlock("banco");
	$tpl->assign("id", $ban['idbanco']);
	$tpl->assign("nombre", $ban['nombre']);
	if (isset($_SESSION['pro_alta']) && $_SESSION['pro_alta']['idbanco'] == $ban['idbanco']) $tpl->assign("selected");
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>