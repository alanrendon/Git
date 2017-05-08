<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$users = array(28, 29, 30, 31);

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_efe_zap_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$descripcion_error[1] = "No hay resultados";

if (isset($_GET['num_cia'])) {
	$mes = $_GET['mes'];
	$anio = $_GET['anio'];
	$fecha1 = "01/$mes/$anio";
	$fecha2 = date("d/m/Y", mktime(0, 0, 0, $mes + 1, 0, $anio));
	// EFECTIVOS DESGLOSADOS
	if ($_GET['tipo'] == 1) {
		// Ventas desglosadas
		$sql = "SELECT num_cia, nombre, extract(day from fecha) AS dia, ez.venta AS venta, errores, tz.venta AS venta_total, tz.otros AS otros, gastos, efectivo, pares, clientes,";
		$sql .= " nota1, nota2, nota3, nota4 FROM total_zapaterias AS tz LEFT JOIN efectivos_zap AS ez USING (num_cia, fecha) LEFT JOIN catalogo_companias USING (num_cia) WHERE";
		$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia] AND" : "";
		$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY num_cia, dia";
		$result = $db->query($sql);
		
		if (!$result) {
			header("location: ./ban_efe_zap_con.php?codigo_error=1");
			die;
		}
		
		$tpl->newBlock("ventas_des");
		$tpl->assign("mes", mes_escrito($mes));
		$tpl->assign("anio", $anio);
		
		$num_cia = NULL;
		foreach ($result as $reg) {
			if ($num_cia != $reg['num_cia']) {
				$num_cia = $reg['num_cia'];
				
				$tpl->newBlock("ventas_cia");
				$tpl->assign("num_cia", $num_cia);
				$tpl->assign("nombre", $reg['nombre']);
				
				$total = array('venta' => 0,
								'errores' => 0,
								'venta_total' => 0,
								'otros' => 0,
								'gastos' => 0,
								'efectivo' => 0,
								'clientes' => 0,
								'pares' => 0);
			}
			$tpl->newBlock("ventas_cia_fila");
			$tpl->assign("dia", $reg['dia']);
			$tpl->assign("venta", $reg['venta'] != 0 ? number_format($reg['venta'], 2, ".", ",") : "");
			$tpl->assign("errores", $reg['errores'] != 0 ? number_format($reg['errores'], 2, ".", ",") : "");
			$tpl->assign("venta_total", $reg['venta_total'] != 0 ? number_format($reg['venta_total'], 2, ".", ",") : "");
			$tpl->assign("otros", $reg['otros'] != 0 ? number_format($reg['otros'], 2, ".", ",") : "");
			$tpl->assign("gastos", $reg['gastos'] != 0 ? number_format($reg['gastos'], 2, ".", ",") : "");
			$tpl->assign("efectivo", $reg['efectivo'] != 0 ? number_format($reg['efectivo'], 2, ".", ",") : "");
			$tpl->assign("color_efectivo", $reg['efectivo'] < 0 ? " color:#CC0000;" : "");
			$tpl->assign("nota1", $reg['nota1'] != 0 ? $reg['nota1'] : "");
			$tpl->assign("nota2", $reg['nota2'] != 0 ? $reg['nota2'] : "");
			$tpl->assign("nota3", $reg['nota3'] != 0 ? $reg['nota3'] : "");
			$tpl->assign("nota4", $reg['nota4'] != 0 ? $reg['nota4'] : "");
			$tpl->assign("clientes", $reg['clientes'] != 0 ? number_format($reg['clientes']) : "");
			$tpl->assign("pares", $reg['pares'] != 0 ? number_format($reg['pares']) : "");
			
			$total['venta'] += $reg['venta'];
			$total['errores'] += $reg['errores'];
			$total['venta_total'] += $reg['venta_total'];
			$total['otros'] += $reg['otros'];
			$total['gastos'] += $reg['gastos'];
			$total['efectivo'] += $reg['efectivo'];
			$total['clientes'] += $reg['clientes'];
			$total['pares'] += $reg['pares'];
			
			$tpl->assign("ventas_cia.venta", number_format($total['venta'], 2, ".", ","));
			$tpl->assign("ventas_cia.errores", number_format($total['errores'], 2, ".", ","));
			$tpl->assign("ventas_cia.venta_total", number_format($total['venta_total'], 2, ".", ","));
			$tpl->assign("ventas_cia.otros", number_format($total['otros'], 2, ".", ","));
			$tpl->assign("ventas_cia.gastos", number_format($total['gastos'], 2, ".", ","));
			$tpl->assign("ventas_cia.efectivo", number_format($total['efectivo'], 2, ".", ","));
			$tpl->assign("ventas_cia.clientes", number_format($total['clientes']));
			$tpl->assign("ventas_cia.pares", number_format($total['pares']));
		}
	}
	else {
		$sql = "SELECT num_cia, nombre_corto, sum(ez.venta) AS venta, sum(errores) AS errores, sum(tz.venta) AS venta_total, sum(tz.otros) AS otros, sum(gastos) AS gastos, sum(efectivo)";
		$sql .= " AS efectivo, sum(pares) AS pares, sum(clientes) AS clientes FROM total_zapaterias AS tz LEFT JOIN efectivos_zap AS ez USING (num_cia, fecha) LEFT JOIN";
		$sql .= " catalogo_companias USING (num_cia) WHERE fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY num_cia, nombre_corto ORDER BY num_cia";
		$result = $db->query($sql);
		
		if (!$result) {
			header("location: ./ban_efe_zap_con.php?codigo_error=1");
			die;
		}
		
		$tpl->newBlock("ventas_totales");
		$tpl->assign("mes", mes_escrito($mes));
		$tpl->assign("anio", $anio);
		
		$total = array('venta' => 0,
						'errores' => 0,
						'venta_total' => 0,
						'otros' => 0,
						'gastos' => 0,
						'efectivo' => 0,
						'clientes' => 0,
						'pares' => 0);
		
		foreach ($result as $reg) {
			$tpl->newBlock("fila_total");
			$tpl->assign("num_cia", $reg['num_cia']);
			$tpl->assign("nombre", $reg['nombre_corto']);
			$tpl->assign("venta", $reg['venta'] != 0 ? number_format($reg['venta'], 2, ".", ",") : "");
			$tpl->assign("errores", $reg['errores'] != 0 ? number_format($reg['errores'], 2, ".", ",") : "");
			$tpl->assign("venta_total", $reg['venta_total'] != 0 ? number_format($reg['venta_total'], 2, ".", ",") : "");
			$tpl->assign("otros", $reg['otros'] != 0 ? number_format($reg['otros'], 2, ".", ",") : "");
			$tpl->assign("gastos", $reg['gastos'] != 0 ? number_format($reg['gastos'], 2, ".", ",") : "");
			$tpl->assign("efectivo", $reg['efectivo'] != 0 ? number_format($reg['efectivo'], 2, ".", ",") : "");
			$tpl->assign("color_efectivo", $reg['efectivo'] < 0 ? " color:#CC0000;" : "");
			$tpl->assign("clientes", $reg['clientes'] != 0 ? number_format($reg['clientes']) : "");
			$tpl->assign("pares", $reg['pares'] != 0 ? number_format($reg['pares']) : "");
			
			$total['venta'] += $reg['venta'];
			$total['errores'] += $reg['errores'];
			$total['venta_total'] += $reg['venta_total'];
			$total['otros'] += $reg['otros'];
			$total['gastos'] += $reg['gastos'];
			$total['efectivo'] += $reg['efectivo'];
			$total['clientes'] += $reg['clientes'];
			$total['pares'] += $reg['pares'];
		}
		$tpl->assign("ventas_totales.venta", number_format($total['venta'], 2, ".", ","));
		$tpl->assign("ventas_totales.errores", number_format($total['errores'], 2, ".", ","));
		$tpl->assign("ventas_totales.venta_total", number_format($total['venta_total'], 2, ".", ","));
		$tpl->assign("ventas_totales.otros", number_format($total['otros'], 2, ".", ","));
		$tpl->assign("ventas_totales.gastos", number_format($total['gastos'], 2, ".", ","));
		$tpl->assign("ventas_totales.efectivo", number_format($total['efectivo'], 2, ".", ","));
		$tpl->assign("ventas_totales.clientes", number_format($total['clientes']));
		$tpl->assign("ventas_totales.pares", number_format($total['pares']));
	}
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");
$tpl->assign(date("n"), " selected");
$tpl->assign("anio", date("Y"));

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message",$descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
die;

?>