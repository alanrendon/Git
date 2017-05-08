<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = 'No existe el proveedor';

//if ($_SESSION['iduser'] != 1) die('location: offline.htm');

if (isset($_GET['p'])) {
	$sql = "SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = $_GET[p]";
	$result = $db->query($sql);
	
	if ($result) echo $result[0]['nombre'];
	die;
}

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
	$sql = 'UPDATE catalogo_proveedores SET';
	$sql .= " nombre = '$_POST[nombre]',";
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
	//$sql .= " tiempoentrega = " . ($_POST['tiempoentrega'] > 0 ? $_POST['tiempoentrega'] : "NULL") . ",";
	$sql .= " tipopersona = '$_POST[tipopersona]',";
	$sql .= " prioridad = '$_POST[prioridad]',";
	$sql .= " para_abono = '" . (isset($_POST['para_abono']) ? 'TRUE' : 'FALSE') . "',";
	$sql .= ' idbanco = ' . ($_POST['idbanco'] > 0 ? $_POST['idbanco'] : 'NULL') . ',';
	$sql .= " cuenta = '$_POST[cuenta]',";
	$sql .= " clabe = '$_POST[clabe]',";
	$sql .= ' "IdEntidad" = ' . ($_POST['IdEntidad'] > 0 ? $_POST['IdEntidad'] : 'NULL') . ',';
	$sql .= " plaza_banxico = '$_POST[plaza_banxico]',";
	$sql .= " trans = '$_POST[trans]',";
	$sql .= " sucursal = '$_POST[sucursal]',";
	$sql .= " contacto = '$_POST[contacto]',";
	$sql .= " verfac = '$_POST[verfac]',";
	$sql .= " tipo_doc = $_POST[tipo_doc],";
	$sql .= " observaciones = '$_POST[observaciones]',";
	if ($_SESSION['iduser'] >= 28 || $_SESSION['iduser'] == 1) {
		$sql .= ' desc1 = ' . ($_POST['desc1'] > 0 ? $_POST['desc1'] : 0) . ',';
		$sql .= ' desc2 = ' . ($_POST['desc2'] > 0 ? $_POST['desc2'] : 0) . ',';
		$sql .= ' desc3 = ' . ($_POST['desc3'] > 0 ? $_POST['desc3'] : 0) . ',';
		$sql .= ' desc4 = ' . ($_POST['desc4'] > 0 ? $_POST['desc4'] : 0) . ',';
		$sql .= ' cod_desc1 = ' . ($_POST['cod_desc1'] > 0 ? $_POST['cod_desc1'] : 0) . ',';
		$sql .= ' cod_desc2 = ' . ($_POST['cod_desc2'] > 0 ? $_POST['cod_desc2'] : 0) . ',';
		$sql .= ' cod_desc3 = ' . ($_POST['cod_desc3'] > 0 ? $_POST['cod_desc3'] : 0) . ',';
		$sql .= ' cod_desc4 = ' . ($_POST['cod_desc4'] > 0 ? $_POST['cod_desc4'] : 0) . ',';
		$sql .= " con_desc1 = '$_POST[con_desc1]',";
		$sql .= " con_desc2 = '$_POST[con_desc2]',";
		$sql .= " con_desc3 = '$_POST[con_desc3]',";
		$sql .= " con_desc4 = '$_POST[con_desc4]',";
		$sql .= " contacto1 = '$_POST[contacto1]',";
		$sql .= " contacto2 = '$_POST[contacto2]',";
		$sql .= " contacto3 = '$_POST[contacto3]',";
		$sql .= " contacto4 = '$_POST[contacto4]',";
	}
	$sql .= " referencia = '$_POST[referencia]',";
	$sql .= ' clave_seguridad = ' . ($_POST['clave_seguridad'] > 0 ? $_POST['clave_seguridad'] : 'NULL');
	$sql .= " WHERE num_proveedor = $_POST[num_proveedor];\n";
	$sql .= "UPDATE catalogo_proveedores SET nombre = upper(nombre), rfc = upper(rfc), direccion = upper(direccion), contacto = upper(contacto), referencia = upper(referencia), con_desc1 = upper(con_desc1), con_desc2 = upper(con_desc2), con_desc3 = upper(con_desc3), con_desc4 = upper(con_desc4), contacto1 = upper(contacto1), contacto2 = upper(contacto2), contacto3 = upper(contacto3), contacto4 = upper(contacto4), observaciones = upper(observaciones) WHERE num_proveedor = $_POST[num_proveedor];\n";
	$db->query($sql);
	
	header("location: ./fac_pro_mod_v3.php");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_pro_mod_v3.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_pro'])) {
	$sql = "SELECT * FROM catalogo_proveedores WHERE num_proveedor = $_GET[num_pro]";
	$result = $db->query($sql);
	
	if (!$result) die(header('location: ./fac_pro_mod_v3.php?codigo_error=1'));
	
	$tpl->newBlock('mod');
	
	$fields = array(
		'text' => array(
			'num_proveedor', 'clave_seguridad', 'nombre', 'rfc', 'direccion', 'telefono1', 'telefono2', 'fax', 'email', 'contacto', 'referencia', 'cuenta', 'clabe', 'sucursal', 'plaza_banxico', 'diascredito', 'observaciones'
		),
		'checkbox' => array(
			'para_abono'
		),
		'radio' => array(
			'tipopersona', 'prioridad', 'restacompras', 'verfac', 'tipo_doc', 'idtipopago', 'trans'
		),
		'select' => array(
			'idtipoproveedor', 'idbanco', 'IdEntidad'
		),
		'zap_text' => array(
			'contacto1', 'contacto2', 'contacto3', 'contacto4'
		),
		'zap_int' => array(
			'cod_desc1', 'cod_desc2', 'cod_desc3', 'cod_desc4'
		),
		'zap_float' => array(
			'desc1', 'desc2', 'desc3', 'desc4'
		)
	);
	
	if ($_SESSION['iduser'] >= 28 || $_SESSION['iduser'] == 1)
		$tpl->newBlock('zap');
	
	foreach ($result[0] as $key => $value) {
		if (in_array($key, $fields['text']))
			$tpl->assign('mod.' . $key, $value);
		else if (($_SESSION['iduser'] >= 28 || $_SESSION['iduser'] == 1) && in_array($key, $fields['zap_text']))
			$tpl->assign('zap.' . $key, $value);
		else if (($_SESSION['iduser'] >= 28 || $_SESSION['iduser'] == 1) && in_array($key, $fields['zap_int']))
			$tpl->assign('zap.' . $key, $value > 0 ? $value : '');
		else if (($_SESSION['iduser'] >= 28 || $_SESSION['iduser'] == 1) && in_array($key, $fields['zap_float']))
			$tpl->assign('zap.' . $key, $value > 0 ? number_format($value, 2, '.', ',') : '');
		else if (in_array($key, $fields['radio']))
			$tpl->assign('mod.' . $key . '_' . $value, ' checked');
		else if (in_array($key, $fields['checkbox']) && $value == 't')
			$tpl->assign('mod.' . $key, ' checked');
		else if (in_array($key, $fields['select'])) {
			if ($key == 'idtipoproveedor')
				$tpl->assign($key . '_' . $value, ' selected');
			else if ($key == 'idbanco') {
				if ($value <= 0)
					$tpl->assign($key, ' selected');
				
				$bancos = $db->query('SELECT idbanco, num_banco, clave, nombre FROM catalogo_bancos ORDER BY idbanco');
				foreach ($bancos as $ban) {
					$tpl->newBlock('banco');
					$tpl->assign('idbanco', $ban['idbanco']);
					$tpl->assign('num', $ban['num_banco']);
					$tpl->assign('clave', $ban['clave']);
					$tpl->assign("nombre", $ban['nombre']);
					if ($ban['idbanco'] == $value)
						$tpl->assign('selected', ' selected');
				}
			}
			else if ($key == 'IdEntidad') {
				if ($value <= 0)
					$tpl->assign($key, ' selected');
				
				$ent = $db->query('SELECT "IdEntidad", "Entidad" FROM catalogo_entidades ORDER BY "IdEntidad"');
				foreach ($ent as $e) {
					$tpl->newBlock('entidad');
					$tpl->assign('IdEntidad', $e['IdEntidad']);
					$tpl->assign('Entidad', $e['Entidad']);
					if ($e['IdEntidad'] == $value)
						$tpl->assign('selected', ' selected');
				}
			}
		}
	}
	
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');

// Si viene de una pgina que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>