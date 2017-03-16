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

$descripcion_error[1] = "No hay resultados";

$users = array(28, 29, 30, 31);

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_pro_mod_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Obtener datos de la compañía a modificar ---------------------------------
if (isset($_POST['num_proveedor'])) {
	$sql = "UPDATE catalogo_proveedores SET";
	$sql .= " nombre = '" . trim(str_replace(array("\"", "'"), "", $_POST['nombre'])) . "',";
	$sql .= " direccion = '$_POST[direccion]',";
	$sql .= " rfc = '$_POST[rfc]',";
	$sql .= " idtipopago = $_POST[idtipopago],";
	$sql .= " diascredito = " . ($_POST['diascredito'] > 0 ? $_POST['diascredito'] : "NULL") . ",";
	$sql .= " telefono1 = '$_POST[telefono1]',";
	$sql .= " telefono2 = '$_POST[telefono2]',";
	$sql .= " fax = '$_POST[fax]',";
	$sql .= " email = '$_POST[email]',";
	$sql .= " restacompras = '$_POST[restacompras]',";
	$sql .= " idtipoproveedor = $_POST[idtipoproveedor],";
	$sql .= " tiempoentrega = " . ($_POST['tiempoentrega'] > 0 ? $_POST['tiempoentrega'] : "NULL") . ",";
	$sql .= " tipopersona = '$_POST[tipopersona]',";
	$sql .= " prioridad = '$_POST[prioridad]',";
	$sql .= " para_abono = '$_POST[para_abono]',";
	$sql .= " idbanco = $_POST[idbanco],";
	$sql .= " cuenta = '$_POST[cuenta]',";
	$sql .= " plaza_banxico = '$_POST[plaza_banxico]',";
	$sql .= " san = '$_POST[san]',";
	$sql .= " trans = '$_POST[trans]',";
	$sql .= " sucursal = '$_POST[sucursal]',";
	$sql .= " verfac = '$_POST[verfac]',";
	$sql .= " desc1 = " . ($_POST['desc1'] > 0 ? $_POST['desc1'] : 0) . ',';
	$sql .= " desc2 = " . ($_POST['desc2'] > 0 ? $_POST['desc2'] : 0) . ',';
	$sql .= " desc3 = " . ($_POST['desc3'] > 0 ? $_POST['desc3'] : 0) . ',';
	$sql .= " desc4 = " . ($_POST['desc4'] > 0 ? $_POST['desc4'] : 0) . ',';
	$sql .= " con_desc1 = '" . trim(strtoupper($_POST['con_desc1'])) . "',";
	$sql .= " con_desc2 = '" . trim(strtoupper($_POST['con_desc2'])) . "',";
	$sql .= " con_desc3 = '" . trim(strtoupper($_POST['con_desc3'])) . "',";
	$sql .= " con_desc4 = '" . trim(strtoupper($_POST['con_desc4'])) . "',";
	$sql .= " tipo_doc = $_POST[tipo_doc],";
	$sql .= " contacto1 = '" . trim(strtoupper($_POST['contacto1'])) . "',";
	$sql .= " contacto2 = '" . trim(strtoupper($_POST['contacto2'])) . "',";
	$sql .= " contacto3 = '" . trim(strtoupper($_POST['contacto3'])) . "',";
	$sql .= " contacto4 = '" . trim(strtoupper($_POST['contacto4'])) . "',";
	$sql .= " referencia = '" . trim(strtoupper($_POST['referencia'])) . "',";
	$sql .= " clave_seguridad = " . ($_POST['clave_seguridad'] > 0 ? $_POST['clave_seguridad'] : 'NULL') /*. ','*/;
	//$sql .= " tipo_desc1 = $_POST[tipo_desc1],";
	//$sql .= " tipo_desc2 = $_POST[tipo_desc2],";
	//$sql .= " tipo_desc3 = $_POST[tipo_desc3],";
	//$sql .= " tipo_desc4 = $_POST[tipo_desc4]";
	
	$sql .= " WHERE num_proveedor = $_POST[num_proveedor];\n";
	$sql .= "UPDATE catalogo_proveedores SET nombre = upper(nombre), rfc = upper(rfc), direccion = upper(direccion), contacto = upper(contacto) WHERE num_proveedor = $_POST[num_proveedor];\n";
	$db->query($sql);
	
	header("location: ./fac_pro_mod_v2.php");
	die;
}

if (!isset($_GET['num_pro'])) {
	$tpl->newBlock("datos");
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	}
	
	$tpl->printToScreen();
	die;
}

// -------------------------------- Modificar -------------------------------------------------------
$pro = $db->query("SELECT * FROM catalogo_proveedores WHERE num_proveedor = $_GET[num_pro]" . (in_array($_SESSION['iduser'], $users) ? " AND num_proveedor BETWEEN 9001 AND 9999" : ""));

if (!$pro) {
	header("location: ./fac_pro_mod_v2.php?codigo_error=1");
	die;
}

$tpl->newBlock("mod");
$tpl->assign("num_pro", $pro[0]['num_proveedor']);
$tpl->assign("nombre", $pro[0]['nombre']);
$tpl->assign("dir", $pro[0]['direccion']);
$tpl->assign("tel1", $pro[0]['telefono1']);
$tpl->assign("tel2", $pro[0]['telefono2']);
$tpl->assign("fax", $pro[0]['fax']);
$tpl->assign("email", $pro[0]['email']);
$tpl->assign("rfc", $pro[0]['rfc']);
$tpl->assign("tipo_" . $pro[0]['tipopersona'], "checked");
$tpl->assign("prio_" . $pro[0]['prioridad'], "checked");
$tpl->assign("contacto", $pro[0]['contacto']);
$tpl->assign("cuenta", $pro[0]['cuenta']);
$tpl->assign("plaza", $pro[0]['plaza_banxico']);
$tpl->assign("suc", $pro[0]['sucursal']);
$tpl->assign("san_" . $pro[0]['san'], "checked");
$tpl->assign("abono_" . $pro[0]['para_abono'], "checked");
$tpl->assign("trans_" . $pro[0]['trans'], "checked");
$tpl->assign("tipopago_" . $pro[0]['idtipopago'], "selected");
$tpl->assign('diascred', $pro[0]['diascredito']);
$tpl->assign("resta_" . $pro[0]['restacompras'], "checked");
$tpl->assign("tipopro_" . $pro[0]['idtipoproveedor'], "selected");
$tpl->assign("tiempo", $pro[0]['tiempoentrega']);
$tpl->assign("ver_" . $pro[0]['verfac'], "checked");

$tpl->assign('desc1', $pro[0]['desc1'] > 0 ? number_format($pro[0]['desc1'], 2) : '');
$tpl->assign('con_desc1', $pro[0]['con_desc1']);
$tpl->assign('desc2', $pro[0]['desc2'] > 0 ? number_format($pro[0]['desc2'], 2) : '');
$tpl->assign('con_desc2', $pro[0]['con_desc2']);
$tpl->assign('desc3', $pro[0]['desc3'] > 0 ? number_format($pro[0]['desc3'], 2) : '');
$tpl->assign('con_desc3', $pro[0]['con_desc3']);
$tpl->assign('desc4', $pro[0]['desc4'] > 0 ? number_format($pro[0]['desc4'], 2) : '');
$tpl->assign('con_desc4', $pro[0]['con_desc4']);
$tpl->assign('tipo_desc1_' . $pro[0]['tipo_desc1'], ' checked');
$tpl->assign('tipo_desc2_' . $pro[0]['tipo_desc2'], ' checked');
$tpl->assign('tipo_desc3_' . $pro[0]['tipo_desc3'], ' checked');
$tpl->assign('tipo_desc4_' . $pro[0]['tipo_desc4'], ' checked');

$tpl->assign('tipo_doc_' . $pro[0]['tipo_doc'], 'checked');

$tpl->assign('contacto1', $pro[0]['contacto1']);
$tpl->assign('contacto2', $pro[0]['contacto2']);
$tpl->assign('contacto3', $pro[0]['contacto3']);
$tpl->assign('contacto4', $pro[0]['contacto4']);

$tpl->assign('referencia', $pro[0]['referencia']);

$tpl->assign('clave_seguridad', $pro[0]['clave_seguridad']);

$bancos = $db->query("SELECT idbanco, nombre FROM catalogo_bancos ORDER BY idbanco");
foreach ($bancos as $ban) {
	$tpl->newBlock("banco");
	$tpl->assign("id", $ban['idbanco']);
	$tpl->assign("nombre", $ban['nombre']);
	if ($pro[0]['idbanco'] == $ban['idbanco']) $tpl->assign("selected", "selected");
}

$tpl->printToScreen();
?>