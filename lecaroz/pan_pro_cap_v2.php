<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

$descripcion_error[1] = "No hay resultados";

if (isset($_POST['num_cia'])) {
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/pan/pan_pro_cap_v2.tpl");
$tpl->prepare();

// Pedir datos iniciales
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	
	$sql = "SELECT num_cia FROM catalogo_companias LEFT JOIN catalogo_operadoras USING (idoperadora) WHERE (num_cia < 100 OR num_cia IN (702, 703))";
	$sql .= (!in_array($_SESSION['iduser'], array(1, 4)) ? " AND iduser = $_SESSION[iduser]" : "") . " ORDER BY num_cia";
	$cias = $db->query($sql);
	
	foreach ($cias as $cia) {
		$tpl->newBlock("cia");
		$tpl->assign("num_cia", $cia['num_cia']);
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

// Obtener la ultima fecha de captura
$lastDate = $db->query("SELECT cast(fecha_total + interval '1 day' as date) AS fecha FROM total_produccion WHERE numcia = $_GET[num_cia] ORDER BY fecha_total DESC LIMIT 1");

// Validar fecha de captura
if (!$lastDate && !isset($_GET['fecha'])) {
	$tpl->newBlock("fecha");
	$tpl->assign("num_cia", $_GET['num_cia']);
	$tpl->assign("fecha", date("d/m/Y"));
	$tpl->printToScreen();
	die;
}
else
	$fecha = $lastDate ? $lastDate[0]['fecha'] : $_GET['fecha'];

// Obtener datos de la compañía
$cia = $db->query("SELECT num_cia, nombre_corto, med_agua FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");

// Validar que se hayan capturado las mediciones de agua
if ($cia[0]['med_agua'] == "t" && !$db->query("SELECT fecha FROM medidor_agua WHERE num_cia = $_GET[num_cia] AND fecha >= CURRENT_DATE - interval '10 days' ORDER BY fecha DESC LIMIT 1")) {
	$tpl->newBlock("agua");
	$tpl->printToScreen();
	die;
}

$sql = "SELECT cod_turno, ct.descripcion AS turno, cod_producto AS cod_pro, cp.nombre, precio_raya AS p_raya, porc_raya AS porc, precio_venta AS p_venta FROM control_produccion";
$sql .= " LEFT JOIN catalogo_productos AS cp USING (cod_producto) LEFT JOIN catalogo_turnos AS ct USING (cod_turno) WHERE num_cia = $_GET[num_cia] ORDER BY cod_turno, num_orden";
$result = $db->query($sql);

if (!$result) {
	header("location: ./pan_pro_cap_v2.php?codigo_error=1");
	die;
}

$tpl->newBlock("captura");
$tpl->assign("num_cia", $cia[0]['num_cia']);
$tpl->assign("nombre", $cia[0]['nombre_corto']);
$tpl->assign("fecha", $fecha);

$cod_turno = NULL;
$turno = 0;
foreach ($result as $i => $reg) {
	if ($cod_turno != $reg['cod_turno']) {
		if ($cod_turno != NULL)
			$turno++;
		$cod_turno = $reg['cod_turno'];
		
		$tpl->newBlock("turno");
		$tpl->assign("turno", $reg['turno']);
		$tpl->assign("ini", $i);
	}
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("next", $i < count($result) - 1 ? $i + 1 : 0);
	$tpl->assign("turno.fin", $i);
	$tpl->assign("turno", $turno);
	$tpl->assign("cod_turno", $reg['cod_turno']);
	$tpl->assign("cod", $reg['cod_pro']);
	$tpl->assign("nombre", $reg['nombre']);
	$tpl->assign("p_raya", $reg['p_raya'] > 0 ? number_format($reg['p_raya'], 4, ".", ",") : ($reg['porc'] > 0 ? number_format($reg['porc'], 2, ".", ",") . "%" : ""));
	$tpl->assign("p_venta", $reg['p_venta'] > 0 ? number_format($reg['p_venta'], 2, ".", ",") : "");
}
$tpl->printToScreen();
?>