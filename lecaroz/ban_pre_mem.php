<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Delaracion de variables -------------------------------------------------
$numfilas = 10;	// Número de filas en la captura

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_pre_mem.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['idadministrador'])) {
	$tpl->newBlock("datos");
	
	$sql = "SELECT idadministrador AS id, nombre_administrador AS nombre FROM catalogo_administradores ORDER BY nombre_administrador";
	$id = $db->query($sql);
	
	foreach ($id as $ad) {
		$tpl->newBlock("id");
		$tpl->assign("id", $ad['id']);
		$tpl->assign("nombre", $ad['nombre']);
	}
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign("message", $descripcion_error[$_GET['codigo_error']]);
	}
	
	$tpl->printToScreen();
	die;
}

$dia = date("d");
$mes = date("n");
$anio = date("Y");
$current_ts = mktime(0, 0, 0, $mes, $dia, $anio);
$fecha = date("d/m/Y", mktime(0, 0, 0, $mes, $dia - $_GET['dias'], $anio));

$sql = "SELECT nombre_administrador, catalogo_trabajadores.num_cia, catalogo_companias.nombre AS nombre_cia, num_emp, catalogo_trabajadores.nombre AS nombre, ap_paterno, ap_materno,";
$sql .= " fecha, importe, (SELECT sum(importe) FROM prestamos WHERE id_empleado = catalogo_trabajadores.id AND tipo_mov = 'TRUE' AND pagado = 'FALSE') AS abonos, (SELECT fecha FROM";
$sql .= " prestamos WHERE id_empleado = catalogo_trabajadores.id AND tipo_mov = 'TRUE' AND pagado = 'FALSE' ORDER BY fecha DESC LIMIT 1) AS fecha_ultimo, (SELECT importe FROM prestamos";
$sql .= " WHERE id_empleado = catalogo_trabajadores.id AND tipo_mov = 'TRUE' AND pagado = 'FALSE' ORDER BY fecha DESC LIMIT 1) AS importe_ultimo FROM prestamos LEFT JOIN";
$sql .= " catalogo_trabajadores ON (catalogo_trabajadores.id = prestamos.id_empleado) LEFT JOIN catalogo_companias ON (catalogo_companias.num_cia = catalogo_trabajadores.num_cia)";
$sql .= " LEFT JOIN catalogo_administradores USING (idadministrador) WHERE tipo_mov = 'FALSE' AND pagado = 'FALSE' AND ((SELECT fecha FROM prestamos WHERE";
$sql .= " id_empleado = catalogo_trabajadores.id AND tipo_mov = 'TRUE' AND pagado = 'FALSE' ORDER BY fecha DESC LIMIT 1) <= '$fecha' OR (fecha <= '$fecha' AND";
$sql .= " (SELECT fecha FROM prestamos WHERE id_empleado = catalogo_trabajadores.id AND tipo_mov = 'TRUE' AND pagado = 'FALSE' ORDER BY fecha DESC LIMIT 1) IS NULL))";
$sql .= $_GET['idadministrador'] > 0 ? " AND idadministrador = $_GET[idadministrador]" : "";
$sql .= " ORDER BY nombre_administrador, num_cia, fecha";
$result = $db->query($sql);

if (!$result) {
	header("location: ./ban_pre_mem.php?codigo_error=1");
	die;
}

$num_cia = NULL;
for ($i=0; $i<count($result); $i++) {
	if ($num_cia != $result[$i]['num_cia']) {
		$num_cia = $result[$i]['num_cia'];
		
		$tpl->newBlock("memo");
		$tpl->assign("dia", date("d"));
		$tpl->assign("mes", mes_escrito(date("n"), TRUE));
		$tpl->assign("anio", date("Y"));
		$tpl->assign("num_cia", $num_cia);
		$tpl->assign("nombre_cia", $result[$i]['nombre_cia']);
		$tpl->assign("admin", $result[$i]['nombre_administrador']);
		$encargado = $db->query("SELECT nombre_fin FROM encargados WHERE num_cia = {$result[$i]['num_cia']} ORDER BY anio DESC, mes DESC LIMIT 1");
		$tpl->assign("encargado", strtoupper($encargado[0]['nombre_fin']));
		
		$saldo_total = 0;
		$tmp_dias = NULL;
	}
	$tpl->newBlock("fila");
	$tpl->assign("num_emp", $result[$i]['num_emp']);
	$tpl->assign("nombre", "{$result[$i]['nombre']} {$result[$i]['ap_paterno']} {$result[$i]['ap_materno']}");
	$tpl->assign("fecha", $result[$i]['fecha']);
	$tpl->assign("saldo", number_format($result[$i]['importe'] - $result[$i]['abonos'], 2, ".", ","));
	$tpl->assign("abonos", $result[$i]['abonos'] > 0 ? number_format($result[$i]['abonos'], 2, ".", ",") : "&nbsp;");
	$tpl->assign("fecha_ultimo", $result[$i]['fecha_ultimo'] != "" ? $result[$i]['fecha_ultimo'] : "&nbsp;");
	$tpl->assign("importe", $result[$i]['importe_ultimo'] > 0 ? number_format($result[$i]['importe_ultimo'], 2, ".", ",") : "&nbsp;");
	
	// MOD. 16/Mar/2006
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $result[$i]['fecha_ultimo'] ? $result[$i]['fecha_ultimo'] : $result[$i]['fecha'], $fecha_ultimo);
	
	$ultimo_ts = mktime(0, 0, 0, $fecha_ultimo[2], $fecha_ultimo[1], $fecha_ultimo[3]);
	
	$dias = round(($current_ts - $ultimo_ts) / 86400);
	if ($dias > $tmp_dias)
		$tmp_dias = $dias;
	
	$tpl->assign("dias", $dias);
	
	$saldo_total += $result[$i]['importe'] - $result[$i]['abonos'];
	
	$tpl->assign("memo.saldo_total", number_format($saldo_total, 2, ".", ","));
	$tpl->assign("memo.dias_retraso", $tmp_dias);
}

$tpl->printToScreen();
die;

?>