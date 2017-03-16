<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

$users = array(28, 29, 30, 31, 32, 33, 34, 35);

function color($placa) {
	$ultimo_digito = NULL;

	if ($placa == "")
		return 0;

	// Recorrer todos los caracteres de la placa
	for ($i = 0; $i < strlen($placa); $i++)
		if ($placa{$i} == "0" || $placa{$i} == "1" || $placa{$i} == "2" || $placa{$i} == "3" || $placa{$i} == "4" || $placa{$i} == "5" ||
			$placa{$i} == "6" || $placa{$i} == "7" || $placa{$i} == "8" || $placa{$i} == "9")
			$ultimo_digito = (int)$placa{$i};

	switch ($ultimo_digito) {
		case 5:
		case 6: $color = "FFFF00"; break;
		case 7:
		case 8: $color = "FF99CC"; break;
		case 3:
		case 4: $color = "FF0000"; break;
		case 1:
		case 2: $color = "669900"; break;
		case 9:
		case 0: $color = "0000FF"; break;
		default: $color = "EBF8FF";
	}

	return $color;
}

function regresa_color($placa) {
	$ultimo_digito = NULL;

	if ($placa == "")
		return 0;

	// Recorrer todos los caracteres de la placa
	for ($i = 0; $i < strlen($placa); $i++)
		if ($placa{$i} == "0" || $placa{$i} == "1" || $placa{$i} == "2" || $placa{$i} == "3" || $placa{$i} == "4" || $placa{$i} == "5" ||
			$placa{$i} == "6" || $placa{$i} == "7" || $placa{$i} == "8" || $placa{$i} == "9")
			$ultimo_digito = (int)$placa{$i};

	switch ($ultimo_digito) {
		case 5:
		case 6: $color = 1; break;
		case 7:
		case 8: $color = 2; break;
		case 3:
		case 4: $color = 3; break;
		case 1:
		case 2: $color = 4; break;
		case 9:
		case 0: $color = 5; break;
		default: $color = 0;
	}

	return $color;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/doc/doc_cam_con.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['num_cia'])) {
	$_SESSION['scan']['num_cia'] = $_POST['num_cia'];
	$_SESSION['scan']['tipo_doc'] = $_POST['tipo_doc'];
	$_SESSION['scan']['descripcion'] = $_POST['descripcion'];

	header("location: ./doc_doc_scan.php");
}

// Pedir datos del documento
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");

	$admins = $db->query('SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores ORDER BY admin');
	foreach ($admins as $a) {
		$tpl->newBlock('admin');
		$tpl->assign('id', $a['id']);
		$tpl->assign('admin', $a['admin']);
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
	$db->desconectar();
	die;
}

$condiciones = array();

$condiciones[] = 'num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 998');

if ($_GET['num_cia'] > 0) {
	$condiciones[] = 'num_cia = ' . $_GET['num_cia'];
}

if ($_GET['idcamioneta'] > 0) {
	$condiciones[] = 'idcamioneta = ' . $_GET['idcamioneta'];
}

if ($_GET['entidad'] > 0) {
	$condiciones[] = 'entidad = ' . $_GET['entidad'];
}

if ($_GET['estatus'] > 0) {
	$condiciones[] = 'estatus = ' . $_GET['estatus'];
}

if ($_GET['idadmin'] > 0) {
	$condiciones[] = 'idadministrador = ' . $_GET['idadmin'];
}

if ($_GET['tipo_unidad'] > 0) {
	$condiciones[] = 'tipo_unidad = ' . $_GET['tipo_unidad'];
}

if ($_GET['color'] > 0) {
	$condiciones[] = 'color_placa(placas) = ' . $_GET['color'];
}

if (in_array($_SESSION['iduser'], array(52, 53, 54, 58, 59, 61))) {
	$condiciones[] = 'tipo_unidad != 3';
}

$sql = "
	SELECT
		idcamioneta,
		modelo,
		anio,
		placas,
		num_cia,
		nombre_corto,
		propietario,
		usuario,
		num_serie,
		num_motor,
		num_poliza,
		ven_poliza
	FROM
		catalogo_camionetas
		LEFT JOIN catalogo_companias
			USING (num_cia)
	WHERE
		" . implode(' AND ', $condiciones) . "
	ORDER BY
		$_GET[orden]
";

$result = $db->query($sql);

$db->desconectar();

$tpl->newBlock("listado");
$tpl->assign("query_string", $_SERVER['QUERY_STRING']);
if ($_SESSION['iduser'] == 26) $tpl->assign("disabled", "disabled");

if ($result) {
	$tpl->newBlock("result");
	for ($i = 0; $i < count($result); $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("id", $result[$i]['idcamioneta']);
		$tpl->assign("modelo", $result[$i]['modelo']);
		$tpl->assign("anio", $result[$i]['anio']);
		$tpl->assign("color", color($result[$i]['placas']));
		$tpl->assign("placas", $result[$i]['placas']);
		$tpl->assign("num_cia", $result[$i]['num_cia']);
		$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
		$tpl->assign("propietario", $result[$i]['propietario'] != "" ? $result[$i]['propietario'] : "&nbsp;");
		$tpl->assign("usuario", $result[$i]['usuario'] != "" ? $result[$i]['usuario'] : "&nbsp;");
		$tpl->assign("num_serie", $result[$i]['num_serie'] != "" ? $result[$i]['num_serie'] : "&nbsp;");
		$tpl->assign("num_motor", $result[$i]['num_motor'] != "" ? $result[$i]['num_motor'] : "&nbsp;");
		$tpl->assign("num_poliza", $result[$i]['num_poliza'] != '' ? $result[$i]['num_poliza'] : '&nbsp;');
		$tpl->assign("ven_poliza", $result[$i]['ven_poliza'] != '' ? $result[$i]['ven_poliza'] : '&nbsp;');
		if ($_SESSION['iduser'] == 26) $tpl->assign("disabled", "disabled");
	}
}
else
	$tpl->newBlock("no_result");

$tpl->printToScreen();
?>
