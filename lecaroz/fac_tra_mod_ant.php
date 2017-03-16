<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

function antiguedad($fecha_alta) {
	// Desglozar elementos de la fecha
	if (!ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$fecha_alta,$fecha))
		return FALSE;
	
	// Timestamp de la fecha de alta
	$ts_alta = mktime(0, 0, 0, $fecha[2], $fecha[1], $fecha[3]);
	// Timestamp actual
	$ts_current = /*time()*/mktime(0, 0, 0, date("n"), date("d") + 2, date("Y"));
	// Diferencia
	$diferencia = $ts_current - $ts_alta;
	// Calcular antiguedad
	$antiguedad[0] = date("Y", $diferencia) - 1970;	// Años
	$antiguedad[1] = date("n", $diferencia) - 1;	// Meses
	$antiguedad[2] = date("d", $diferencia) - 1;	// Días
	
	return $antiguedad;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_tra_mod_ant.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	if ($_POST['tipo'] == 1) {
		if ($_POST['anios'] > 0 || $_POST['meses'] > 0)
			$fecha_alta = date("d/m/Y", mktime(0, 0, 0, (int)date("m") - ($_POST['meses'] > 0 ? $_POST['meses'] : 0), (date("d") < 15 ? 1 : 15), (int)date("Y") - ($_POST['anios'] > 0 ? $_POST['anios'] : 0)));
		else
			$fecha_alta = NULL;
	}
	else
		$fecha_alta = ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_POST['fecha_alta']) ? $_POST['fecha_alta'] : NULL;
	
	if ($fecha_alta != NULL) {
		$sql = "UPDATE catalogo_trabajadores SET fecha_alta = " . ($fecha_alta != NULL ? "'$fecha_alta'" : "NULL") . " WHERE id = $_POST[id]";
		if ($_SESSION['iduser'] != 1) $db->query($sql);
	}
	
	$tpl->newBlock("cerrar");
	$tpl->assign("i", $_POST['i']);
	
	$antiguedad = antiguedad($fecha_alta);
	
	$tpl->assign("antiguedad", ($antiguedad[0] > 0 ? "$antiguedad[0] A " : "") . ($antiguedad[1] > 0 ? "$antiguedad[1] M " : ""));
	if ($_SESSION['iduser'] == 1) { echo $fecha_alta; print_r($antiguedad);}
	if ($_SESSION['iduser'] != 1) $tpl->printToScreen();
	die;
}

$sql = "SELECT fecha_alta FROM catalogo_trabajadores WHERE id = $_GET[id]";
$fecha_alta = $db->query($sql);

$sql = "SELECT nombre, ap_paterno, ap_materno, catalogo_puestos.descripcion AS puesto, catalogo_turnos.descripcion AS turno FROM catalogo_trabajadores LEFT JOIN catalogo_puestos USING (cod_puestos) LEFT JOIN catalogo_turnos USING (cod_turno)";
$sql .= " WHERE id = $_GET[id]";
$datos = $db->query($sql);

$antiguedad = antiguedad($fecha_alta[0]['fecha_alta']);

$tpl->newBlock("modificar");
$tpl->assign("nombre", "{$datos[0]['ap_paterno']} {$datos[0]['ap_materno']} {$datos[0]['nombre']}");
$tpl->assign("puesto", $datos[0]['puesto']);
$tpl->assign("turno", $datos[0]['turno']);
$tpl->assign("id", $_GET['id']);
$tpl->assign("i", $_GET['i']);
$tpl->assign("fecha_alta", $fecha_alta[0]['fecha_alta']);

// Años
for ($i = 0; $i <= 50; $i++) {
	$tpl->newBlock("anios");
	$tpl->assign("anios", $i > 0 ? $i : "");
	if ($antiguedad && $i == $antiguedad[0]) $tpl->assign("selected", "selected");
	else if (!$antiguedad && $i == 0) $tpl->assign("selected", "selected");
}

// Meses
for ($i = 0; $i <= 12; $i++) {
	$tpl->newBlock("meses");
	$tpl->assign("meses", $i > 0 ? $i : "");
	if ($antiguedad && $i == $antiguedad[1]) $tpl->assign("selected", "selected");
	else if (!$antiguedad && $i == 0) $tpl->assign("selected", "selected");
}

// Imprimir el resultado
$tpl->printToScreen();
?>