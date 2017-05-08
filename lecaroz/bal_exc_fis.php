<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_exc_fis.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['tipo'])) {
	$fecha = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
	$sql = "SELECT num_cia, catalogo_companias.nombre AS nombre_cia, codmp, catalogo_mat_primas.nombre AS nombre, precio_unidad, existencia";
	$sql .= " FROM historico_inventario LEFT JOIN catalogo_mat_primas USING (codmp) LEFT JOIN catalogo_companias USING (num_cia) WHERE";
	$sql .= " fecha = '$fecha' AND existencia > 0";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : " AND num_cia < 300";
	$sql .= $_GET['codmp'] > 0 ? " AND codmp = $_GET[codmp]" : "";
	$sql .= " ORDER BY num_cia, controlada DESC, tipo, codmp";
	
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./bal_exc_fis.php?codigo_error=1");
		die;
	}
	
	if ($_GET['tipo'] == "listado") {
		$num_cia = NULL;
		$numfilas_x_hoja = 60;
		for ($i = 0; $i < count($result); $i++) {
			if ($num_cia != $result[$i]['num_cia']) {
				if ($num_cia != NULL) {
					$tpl->newBlock("total");
					$tpl->assign("total", number_format($total, 2, ".", ","));
					$tpl->assign("listado.salto", "<br style=\"page-break-after:always;\">");
				}
				
				$num_cia = $result[$i]['num_cia'];
				
				$tpl->newBlock("listado");
				$tpl->assign("num_cia", $num_cia);
				$tpl->assign("nombre_cia", $result[$i]['nombre_cia']);
				$tpl->assign("mes", mes_escrito($_GET['mes']));
				$tpl->assign("anio", $_GET['anio']);
				
				$numfilas = 0;
				$total = 0;
			}
			if ($numfilas >= $numfilas_x_hoja) {
				$tpl->assign("listado.salto", "<br style=\"page-break-after:always;\">");
				$tpl->newBlock("listado");
				$tpl->assign("num_cia", $num_cia);
				$tpl->assign("nombre_cia", $result[$i]['nombre_cia']);
				$tpl->assign("mes", mes_escrito($_GET['mes']));
				$tpl->assign("anio", $_GET['anio']);
				
				$numfilas = 0;
			}
			$tpl->newBlock("fila");
			$tpl->assign("codmp", $result[$i]['codmp']);
			$tpl->assign("nombre", $result[$i]['nombre']);
			$tpl->assign("costo", number_format($result[$i]['precio_unidad'], 4, ".", ","));
			$tpl->assign("existencia", number_format($result[$i]['existencia'], 4, ".", ","));
			$tpl->assign("total", number_format($result[$i]['precio_unidad'] * $result[$i]['existencia'], 2, ".", ","));
			
			$total += $result[$i]['precio_unidad'] * $result[$i]['existencia'];
			$numfilas++;
		}
		if ($num_cia != NULL) {
			$tpl->newBlock("total");
			$tpl->assign("total", number_format($total, 2, ".", ","));
		}
		
		$tpl->printToScreen();
	}
	else if ($_GET['tipo'] == "archivo") {
		$num_cia = NULL;
		$data = "";
		for ($i = 0; $i < count($result); $i++) {
			if ($num_cia != $result[$i]['num_cia']) {
				if ($num_cia != NULL)
					$data .= "\"\",\"\",\"\",\"COSTO INVENTARIO\",\"" . number_format($total, 2, ".", "") . "\"\n";
				
				$num_cia = $result[$i]['num_cia'];
				
				$data .= "\"$num_cia {$result[$i]['nombre_cia']}\"\n\n";
				$data .= "\"" . mes_escrito($_GET['mes'], TRUE) . "\",$_GET[anio]\n";
				$data .= "\"COD.\",\"NOMBRE\",\"COSTO UNITARIO\",\"EXISTENCIA FISICA\",\"COSTO TOTAL\"\n";
				
				$total = 0;
			}
			$data .= "\"{$result[$i]['codmp']}\",";
			$data .= "\"{$result[$i]['nombre']}\",";
			$data .= "\"". number_format($result[$i]['precio_unidad'], 4, ".", "") . "\",";
			$data .= "\"". number_format($result[$i]['existencia'], 4, ".", "") . "\",";
			$data .= "\"". number_format($result[$i]['precio_unidad'] * $result[$i]['existencia'], 2, ".", "") . "\"\n";
			
			$total += $result[$i]['precio_unidad'] * $result[$i]['existencia'];
		}
		if ($num_cia != NULL)
			$data .= "\"\",\"\",\"\",\"COSTO INVENTARIO\",\"" . number_format($total, 2, ".", "") . "\"\n";
		
		header("Content-Type: application/download");
		header("Content-Disposition: attachment; filename=Existencias" . mes_escrito($_GET['mes']) . "$_GET[anio].csv");
		echo $data;
	}
	die;
}

$tpl->newBlock("datos");

$tpl->assign(date("n", mktime(0, 0, 0, date("n"), 0, date("Y"))), "selected");
$tpl->assign("anio", date("Y", mktime(0, 0, 0, date("n"), 0, date("Y"))));

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>