<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_res_nco.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$fecha1 = "01/01/$_GET[anio]";
	$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
	
	$sql = "SELECT num_cia, nombre_corto, sum(importe) AS reserva FROM reservas_cias LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha BETWEEN '$fecha1' AND '$fecha2'";
	$sql .= " AND cod_reserva = 4";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
	$sql .= " GROUP BY num_cia, nombre_corto ORDER BY num_cia";
	$reservas = $db->query($sql);
	
	if (!$reservas) {
		header("location: ./bal_res_sco.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("listado");
	$tpl->assign("mes", mes_escrito($_GET['mes']));
	$tpl->assign("anio", $_GET['anio']);
	
	foreach($reservas as $reserva) {
		$cias = $reserva['num_cia'] > 100 && $reserva['num_cia'] < 200 ? "$reserva[num_cia]," . ($reserva['num_cia'] + 100) : $reserva['num_cia'];
		// Obtener lo pagado en el año
		$sql = "SELECT sum(importe) FROM cheques WHERE num_cia IN ($cias) AND fecha BETWEEN '$fecha1' AND '$fecha2' AND codgastos = 141 AND importe > 0 AND fecha_cancelacion IS NULL";
		$tmp = $db->query($sql);
		$pagado = $tmp[0]['sum'] != 0 ? $tmp[0]['sum'] : 0;
		$dif = $reserva['reserva'] - $pagado;
		if ($dif < -5000 || $dif > 5000) {
			$tpl->newBlock("fila");
			$tpl->assign("num_cia", $reserva['num_cia']);
			$tpl->assign("nombre", $reserva['nombre_corto']);
			$tpl->assign("reserva", number_format($reserva['reserva'], 2, ".", ","));
			$tpl->assign("pagado", number_format($pagado, 2, ".", ","));
			$tpl->assign("dif", number_format($dif, 2, ".", ","));
		}
	}
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");
$tpl->assign(date("n"), " selected");
$tpl->assign("anio", date("Y"));

if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>