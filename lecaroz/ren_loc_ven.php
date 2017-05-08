<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ren/ren_loc_ven.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['arr'])) {
	$sql = "SELECT catalogo_arrendatarios.id, num_local, nombre_local, cod_arrendador, nombre, renta_con_recibo AS renta, fecha_inicio, fecha_final FROM catalogo_arrendatarios";
	$sql .= " LEFT JOIN catalogo_arrendadores USING (cod_arrendador) WHERE status = 1";
	$sql .= $_GET['criterio'] == 3 ? " AND fecha_final < CURRENT_DATE" . ($_GET['meses'] > 0 ? " + interval '$_GET[meses] months'" : "") : "";
	$sql .= $_GET['arr'] > 0 ? " AND cod_arrendador = $_GET[arr]" : "";
	$sql .= $_GET['local'] > 0 ? " AND num_local = $_GET[local]" : "";
	$sql .= $_GET['tipo'] > 0 ? " AND bloque = $_GET[tipo]" : "";
	$sql .= " ORDER BY fecha_inicio";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ren_loc_ven.php?codigo_error=1");
		die;
	}
	
	$dia = date("d");
	$mes = date("n");
	$anio = date("Y");
	
	$arr = array();
	$cont = 0;
	if ($_GET['criterio'] == 1) {
		foreach ($result as $reg) {
			$arr[$cont] = $reg;
			ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $reg['fecha_inicio'], $tmp);
			$fecha_ven = date("d/m/Y", mktime(0, 0, 0, $tmp[2], $tmp[1], $anio));
			$arr[$cont]['fecha_inicio'] = $fecha_ven;
			$cont++;
		}
	}
	else if ($_GET['criterio'] == 2) {
		$fecha1 = date("d/m/Y", mktime(0, 0, 0, $mes, 1, $anio - 1));
		$fecha2 = date("d/m/Y", mktime(0, 0, 0, $mes, 1, $anio));
		foreach ($result as $reg) {
			$r1 = $db->query("SELECT fecha, renta FROM recibos_rentas WHERE local = $reg[id] AND fecha BETWEEN '$fecha1' AND cast('$fecha1' as date) + interval '1 month' - interval '1 day' AND status = 1 ORDER BY fecha LIMIT 1");
			$r2 = $db->query("SELECT fecha, renta FROM recibos_rentas WHERE local = $reg[id] AND fecha BETWEEN '$fecha2' AND cast('$fecha2' as date) + interval '1 month' - interval '1 day' AND status = 1 ORDER BY fecha LIMIT 1");
			
			if ($r1 && $r2 && $r2[0]['renta'] <= $r1[0]['renta'])
				$arr[] = $reg;
		}
	}
	else if ($_GET['criterio'] == 3)
		$arr = $result;
	
	if (count($arr) == 0) {
		header("location: ./ren_loc_ven.php?codigo_error=1");
		die;
	}
	
	function cmp($a, $b) {
		// Descomponer fecha
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $a['fecha_inicio'], $fecha_a);
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $b['fecha_inicio'], $fecha_b);
		
		// Timestamp para comparacion
		$ts_a = mktime(0, 0, 0, $fecha_a[2], $fecha_a[1], $fecha_a[3]);
		$ts_b = mktime(0, 0, 0, $fecha_b[2], $fecha_b[1], $fecha_b[3]);
		
		// Si las compañías son iguales
		if ($ts_a == $ts_b)
			return 0;
		else
			return $ts_a < $ts_b ? -1 : 1;
	}
	
	usort($arr, "cmp");
	
	$tpl->newBlock("listado");
	switch ($_GET['criterio']) {
		case 1:
		case 2: $titulo = "Locales Vencidos" .($_GET['tipo'] > 0 ? ($_GET['tipo'] == 1 ? "<br>(Propios)" : "<br>(Ajenos)") : ""); break;
		case 3: $titulo = "Locales con Contrato Vencido" . ($_GET['tipo'] > 0 ? ($_GET['tipo'] == 1 ? "<br>(Propios)" : "<br>(Ajenos)") : ""); break;
	}
	$tpl->assign("titulo", $titulo);
	
	foreach ($arr as $a) {
		$tpl->newBlock("fila");
		$tpl->assign("num", $a['num_local']);
		$tpl->assign("nombre", $a['nombre_local']);
		//$tpl->assign("cod", $a['cod_arrendador']);
		$tpl->assign("arr", $a['nombre']);
		$tpl->assign("renta", number_format($a['renta'], 2, ".", ","));
		$tpl->assign("fecha_ven", /*$_GET['criterio'] != 3 ? */$a['fecha_final']/* : $a['fecha_inicio']*/);
	}
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	$tpl->printToScreen();
	die();
}

$tpl->printToScreen();
?>