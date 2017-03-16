<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$users = array(28, 29, 30, 31);

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_con_man_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['accion']) && $_GET['accion'] == "finish") {
	$cuenta = $_GET['cuenta'];
	$clabe_cuenta = $cuenta == 1 ? "clabe_cuenta" : "clabe_cuenta2";
	$catalogo_mov = $cuenta == 1 ? "catalogo_mov_bancos" : "catalogo_mov_santander";
	
	// Obtener movimientos conciliados
	$sql = "SELECT num_cia, nombre, $clabe_cuenta, fecha, importe, estado_cuenta.tipo_mov, folio, cod_mov, descripcion, concepto FROM estado_cuenta LEFT JOIN catalogo_companias USING (num_cia)";
	$sql .= " LEFT JOIN $catalogo_mov USING (cod_mov) WHERE estado_cuenta.id IN (SELECT idesc FROM mov_con_ban) GROUP BY num_cia, nombre, $clabe_cuenta, fecha, importe, estado_cuenta.tipo_mov, folio, cod_mov, descripcion, concepto";
	$sql .= " ORDER BY num_cia, fecha";
	$result = $db->query($sql);
	
	// Vaciar tabla de temporales
	$db->query("TRUNCATE TABLE mov_con_ban");
	
	if (!$result) {
		header("location: ./ban_con_man_v2.php");
		die;
	}
	
	$tpl->newBlock("listado");
	$tpl->assign("dia", date("d"));
	$tpl->assign("mes", mes_escrito(date("n")));
	$tpl->assign("anio", date("Y"));
	
	$num_cia = NULL;
	$total_abonos = 0;
	$total_cargos = 0;
	foreach ($result as $i => $mov) {
		if ($num_cia != $mov['num_cia']) {
			$num_cia = $mov['num_cia'];
			
			$tpl->newBlock("cia_list");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre_cia", $mov['nombre']);
			$tpl->assign("cuenta", $mov[$clabe_cuenta]);
			$abonos = 0;
			$cargos = 0;
		}
		$tpl->newBlock("fila");
		$tpl->assign("fecha", $mov['fecha']);
		$tpl->assign("abono", $mov['tipo_mov'] == "f" ? number_format($mov['importe'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("cargo", $mov['tipo_mov'] == "t" ? number_format($mov['importe'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("folio", $mov['folio'] > 0 ? $mov['folio'] : "&nbsp;");
		$tpl->assign("cod_mov", $mov['cod_mov']);
		$tpl->assign("descripcion", $mov['descripcion']);
		$tpl->assign("concepto", $mov['concepto']);
		
		$abonos += $mov['tipo_mov'] == "f" ? $mov['importe'] : 0;
		$cargos += $mov['tipo_mov'] == "t" ? $mov['importe'] : 0;
		
		$total_abonos += $mov['tipo_mov'] == "f" ? $mov['importe'] : 0;
		$total_cargos += $mov['tipo_mov'] == "t" ? $mov['importe'] : 0;
		
		$tpl->assign("cia_list.abonos", number_format($abonos, 2, ".", ","));
		$tpl->assign("cia_list.cargos", number_format($cargos, 2, ".", ","));
	}
	$tpl->assign("listado.total_abonos", number_format($total_abonos, 2, ".", ","));
	$tpl->assign("listado.total_cargos", number_format($total_cargos, 2, ".", ","));
	
	$tpl->printToScreen();
	die;
}

if (empty($_GET['cuenta'])) {
	$tpl->newBlock("datos");
	$tpl->assign("fecha", date("d/m/Y", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"))));
	
	$sql = "(SELECT num_cia, cuenta FROM estado_cuenta WHERE fecha_con IS NULL AND cuenta = 1" . (in_array($_SESSION['iduser'], $users) ? " AND num_cia BETWEEN 900 AND 998" : "") . " ORDER BY num_cia LIMIT 1) UNION ";
	$sql .= "(SELECT num_cia, cuenta FROM estado_cuenta WHERE fecha_con IS NULL AND cuenta = 2" . (in_array($_SESSION['iduser'], $users) ? " AND num_cia BETWEEN 900 AND 998" : "") . " ORDER BY num_cia LIMIT 1)";
	$result = $db->query($sql);
	
	if ($result)
		foreach ($result as $value)
			$tpl->assign("num_cia$value[cuenta]", $value['num_cia']);
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
	}
	
	$tpl->printToScreen();
	die;
}

$cuenta = $_GET['cuenta'];
$num_cia = $_GET['num_cia' . $cuenta];
$cia_next = 0;
$banco = $cuenta == 1 ? "BANORTE" : "SANTANDER";
$cat_mov_ban = $cuenta == 1 ? "catalogo_mov_bancos" : "catalogo_mov_santander";
$clabe_cuenta = $cuenta == 1 ? "clabe_cuenta" : "clabe_cuenta2";

if ($_GET['num_cia' . $cuenta] == "") {
	header("location: ./ban_con_man_v2.php?codigo_error=1");
	die;
}

$tpl->newBlock("con_screen");

$sql = "SELECT nombre_corto, $clabe_cuenta FROM catalogo_companias WHERE num_cia = $num_cia";
$ciainfo = $db->query($sql);

$tpl->assign("cuenta", $cuenta);
$tpl->assign("num_cia", $num_cia);
$tpl->assign("nombre_cia", $ciainfo[0]['nombre_corto']);
$tpl->assign("banco", $banco);
$tpl->assign("clabe_cuenta", $ciainfo[0][$clabe_cuenta]);
$tpl->assign("fecha", $_GET['fecha']);

$sql = "SELECT num_cia, nombre_corto FROM estado_cuenta LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha_con IS NULL AND cuenta = $cuenta";
$sql .= in_array($_SESSION['iduser'], $users) ? " AND num_cia BETWEEN 900 AND 998" : "";
$sql .= " GROUP BY num_cia, nombre_corto ORDER BY num_cia";
$cias = $db->query($sql);

if ($cias) {
	foreach ($cias as $i => $cia) {
		$tpl->newBlock("cia");
		$tpl->assign("num_cia", $cia['num_cia']);
		$tpl->assign("nombre_cia", $cia['nombre_corto']);
		
		if ($num_cia == $cia['num_cia'] && isset($cias[$i + 1])) $cia_next = $i + 1;
	}
	$tpl->assign("con_screen.num_cia_next", $cias[$cia_next]['num_cia']);
	$tpl->assign("con_screen.nombre_cia_next", $cias[$cia_next]['nombre_corto']);
}

$sql = "SELECT estado_cuenta.id, fecha, cod_mov, descripcion, importe FROM estado_cuenta LEFT JOIN $cat_mov_ban USING (cod_mov) WHERE fecha_con IS NULL AND estado_cuenta.tipo_mov = 'FALSE'";
$sql .= " AND cuenta = $cuenta AND num_cia = $num_cia GROUP BY estado_cuenta.id, fecha, cod_mov, descripcion, importe ORDER BY fecha, importe";
$abonos = $db->query($sql);

if ($abonos) {
	foreach ($abonos as $abono) {
		$tpl->newBlock("abono");
		$tpl->assign("id", $abono['id']);
		$tpl->assign("fecha", $abono['fecha']);
		$tpl->assign("cod_mov", $abono['cod_mov']);
		$tpl->assign("descripcion", $abono['descripcion']);
		$tpl->assign("importe", number_format($abono['importe'], 2, ".", ","));
	}
}
else
	$tpl->newBlock("no_abonos");

$sql = "SELECT estado_cuenta.id, fecha, cod_mov, descripcion, folio, importe FROM estado_cuenta LEFT JOIN $cat_mov_ban USING (cod_mov) WHERE fecha_con IS NULL AND estado_cuenta.tipo_mov = 'TRUE'";
$sql .= " AND cuenta = $cuenta AND num_cia = $num_cia GROUP BY estado_cuenta.id, fecha, cod_mov, descripcion, folio, importe ORDER BY fecha, importe";
$cargos = $db->query($sql);

if ($cargos) {
	foreach ($cargos as $cargo) {
		$tpl->newBlock("cargo");
		$tpl->assign("id", $cargo['id']);
		$tpl->assign("fecha", $cargo['fecha']);
		$tpl->assign("cod_mov", $cargo['cod_mov']);
		$tpl->assign("descripcion", $cargo['descripcion']);
		$tpl->assign("folio", $cargo['folio']);
		$tpl->assign("importe", number_format($cargo['importe'], 2, ".", ","));
	}
}
else
	$tpl->newBlock("no_cargos");

$tpl->printToScreen();
?>