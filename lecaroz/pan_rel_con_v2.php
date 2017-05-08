<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
include './includes/pcl.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_rel_con_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	if ($_GET['tipo'] == "mes") {
		$fecha1 = "01/$_GET[mes]/$_GET[anio]";
		$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
		
		$dias = date("d", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
		
		// Obtener compañias para la operadora
		if ($_SESSION['iduser'] == 1 || $_SESSION['iduser'] == 4) {
			$sql = "SELECT num_cia, nombre FROM catalogo_companias WHERE";
			$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia]" : " num_cia < 100";
			$sql .= " ORDER BY num_cia";
		}
		else {
			$sql = "SELECT num_cia, nombre FROM catalogo_operadoras JOIN catalogo_companias USING (idoperadora) WHERE";
			$sql .= $_GET['num_cia'] > 0 ? " iduser = $_SESSION[iduser] AND num_cia = $_GET[num_cia]" : " iduser = $_SESSION[iduser] AND (num_cia < 100 OR num_cia IN (702,703))";
			$sql .= " ORDER BY num_cia";
		}
		$cia = $db->query($sql);
		
		if (!$cia) {
			header("location: ./pan_rel_con_v2.php?codigo_error=1");
			die;
		}
		
		function buscarDia($dia) {
			global $k_pastel1, $k_pastel2, $k_pro;
			
			$result = array("k_ent" => 0, "k_pro" => 0, "importe" => 0, "num_fac" => 0);
			
			// Buscar en control 1
			if ($k_pastel1)
				for ($i = 0; $i < count($k_pastel1); $i++)
					if ($dia == $k_pastel1[$i]['dia']) {
						$result['k_ent'] += $k_pastel1[$i]['kilos'];
						$result['importe'] += $k_pastel1[$i]['total_factura'] - $k_pastel1[$i]['base'];
						$result['num_fac'] += $k_pastel1[$i]['num_fac'];
					}
			
			// Buscar en control 2
			if ($k_pastel2)
				for ($i = 0; $i < count($k_pastel2); $i++)
					if ($dia == $k_pastel2[$i]['dia']) {
						$result['k_ent'] += $k_pastel2[$i]['kilos'];
						$result['importe'] += $k_pastel2[$i]['total_factura'];
						$result['num_fac'] += $k_pastel2[$i]['num_fac'];
					}
			
			// Buscar en produccion
			if ($k_pro)
				for ($i = 0; $i < count($k_pro); $i++)
					if ($dia == $k_pro[$i]['dia'])
						$result['k_pro'] += $k_pro[$i]['kilos'];
			
			return $result;
		}
		
		$count = 0;
		for ($i = 0; $i < count($cia); $i++) {
			// Obtener los kilos de pastel pedido para la compañía en curso (Notas pagadas en control 1)
			$sql = "SELECT extract(day FROM fecha_entrega) AS dia, sum(kilos) AS kilos, sum(total_factura) AS total_factura, sum(base) AS base, count(num_remi) AS num_fac FROM venta_pastel WHERE (estado != 2 OR estado IS NULL)";
			$sql .= " AND fecha_entrega BETWEEN '$fecha1' AND '$fecha2' AND num_cia = {$cia[$i]['num_cia']} AND kilos IS NOT NULL AND resta_pagar = 0";
			$sql .= " GROUP BY extract(day FROM fecha_entrega) ORDER BY extract(day FROM fecha_entrega)";
			$k_pastel1 = $db->query($sql);
			
			// Obtener los kilos de pastel pedido para la compañía en curso (Notas pagadas en control 2)
			$sql = "SELECT extract(day FROM fecha_entrega) AS dia, sum(kilos) AS kilos, sum(total_factura) AS total_factura, count(num_remi) AS num_fac FROM venta_pastel WHERE (num_cia, num_remi, letra_folio) IN (";
			$sql .= "SELECT num_cia, num_remi, letra_folio FROM venta_pastel WHERE id IN (";
			$sql .= "SELECT id FROM venta_pastel WHERE (num_cia, num_remi, letra_folio) IN (";
			$sql .= "SELECT num_cia, num_remi, letra_folio FROM venta_pastel WHERE (estado != 2 OR estado IS NULL) AND";
			$sql .= " fecha_entrega BETWEEN '$fecha1' AND '$fecha2' AND num_cia = {$cia[$i]['num_cia']} AND kilos IS NOT NULL AND resta_pagar > 0";
			$sql .= ") AND resta IS NOT NULL)) AND resta_pagar > 0 GROUP BY extract(day FROM fecha_entrega) ORDER BY extract(day FROM fecha_entrega)";
			$k_pastel2 = $db->query($sql);
			
			// Obtener los kilos de pastel producidos para la compañía en curso (Notas pagadas en control 2)
			$sql = "SELECT extract(day FROM fecha) AS dia, sum(piezas) AS kilos FROM produccion WHERE";
			$sql .= " num_cia = {$cia[$i]['num_cia']} AND fecha BETWEEN '$fecha1' AND '$fecha2' AND cod_producto IN (211, 212, 215, 175, 375, 229, 230, 235, 234) AND piezas > 0";
			$sql .= " GROUP BY extract(day FROM fecha) ORDER BY extract(day FROM fecha)";
			$k_pro = $db->query($sql);
			
			if ($k_pastel1 || $k_pastel2 || $k_pro) {
				$tpl->newBlock("listado_mes");
				$tpl->assign("num_cia", $cia[$i]['num_cia']);
				$tpl->assign("nombre_cia", $cia[$i]['nombre']);
				$tpl->assign("mes", mes_escrito($_GET['mes']));
				$tpl->assign("anio", $_GET['anio']);
				
				$total_k_ent = 0;
				$total_k_pro = 0;
				$total_importe = 0;
				
				for ($j = 1; $j <= $dias; $j++) {
					$tpl->newBlock("dia");
					$tpl->assign("dia", $j);
					$dia = buscarDia($j);
					$tpl->assign("k_ent", $dia['k_ent'] > 0 ? number_format($dia['k_ent'], 2, ".", ",") : "&nbsp;");
					$tpl->assign("k_pro", $dia['k_pro'] > 0 ? number_format($dia['k_pro'], 2, ".", ",") : "&nbsp;");
					$tpl->assign("importe", $dia['importe'] > 0 ? number_format($dia['importe'], 2, ".", ",") : "&nbsp;");
					$tpl->assign("num_fac", $dia['num_fac'] > 0 ? $dia['num_fac'] : "&nbsp;");
					
					$total_k_ent += $dia['k_ent'];
					$total_k_pro += $dia['k_pro'];
					$total_importe += $dia['importe'];
				}
				$count++;
				
				$tpl->assign("listado_mes.k_ent", number_format($total_k_ent, 2, ".", ","));
				$tpl->assign("listado_mes.k_pro", number_format($total_k_pro, 2, ".", ","));
				$tpl->assign("listado_mes.importe", number_format($total_importe, 2, ".", ","));
				$tpl->assign("listado_mes.salto", "<br style=\"page-break-after:always;\">");
			}
		}
		
		if ($count == 0) {
			header("location: ./pan_rel_con_v2.php?codigo_error=1");
			die;
		}
		
		$tpl->printToScreen();
		die;
	}
	else {
		// Obtener compañias para la operadora
		if ($_SESSION['iduser'] == 1 || $_SESSION['iduser'] == 4) {
			$sql = "SELECT num_cia, nombre FROM catalogo_companias WHERE";
			$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia]" : " num_cia < 100";
			$sql .= " ORDER BY num_cia";
		}
		else {
			$sql = "SELECT num_cia, nombre FROM catalogo_operadoras JOIN catalogo_companias USING (idoperadora) WHERE";
			$sql .= $_GET['num_cia'] > 0 ? "iduser = $_SESSION[iduser] AND num_cia = $_GET[num_cia]" : " iduser = $_SESSION[iduser] AND (num_cia < 100 OR num_cia IN (702,703))";
			$sql .= " ORDER BY num_cia";
		}
		$cia = $db->query($sql);
		
		if (!$cia) {
			header("location: ./pan_rel_con_v2.php?codigo_error=1");
			die;
		}
		
		function cmp($a, $b) {
			if ($a['num_remi'] == $b['num_remi'])
				return 0;
			
			return ($a['num_remi'] < $b['num_remi']) ? -1 : 1;
		}
		
		$count = 0;
		for ($i = 0; $i < count($cia); $i++) {
			// Obtener facturas
			/*$sql = "((SELECT letra_folio, num_remi, kilos, total_factura, base FROM venta_pastel WHERE";
			$sql .= " (estado != 2 OR estado IS NULL) AND fecha_entrega = '$_GET[fecha]' AND num_cia = 41 AND kilos IS NOT NULL AND resta_pagar = 0)";
			$sql .= " UNION ";
			$sql .= "(SELECT letra_folio, num_remi, kilos, total_factura, NULL FROM venta_pastel WHERE";
			$sql .= " (num_cia, num_remi, letra_folio) IN (SELECT num_cia, num_remi, letra_folio FROM venta_pastel WHERE id IN (";
			$sql .= "SELECT id FROM venta_pastel WHERE (num_cia, num_remi, letra_folio) IN (";
			$sql .= "SELECT num_cia, num_remi, letra_folio FROM venta_pastel WHERE";
			$sql .= " (estado != 2 OR estado IS NULL) AND fecha_entrega = '$_GET[fecha]' AND num_cia = 41 AND kilos IS NOT NULL AND resta_pagar > 0)";
			$sql .= " AND resta IS NOT NULL)) AND resta_pagar > 0))";
			$sql .= " ORDER BY num_remi";*/
			$sql = "SELECT letra_folio, num_remi, kilos, total_factura, base FROM venta_pastel WHERE";
			$sql .= " (estado != 2 OR estado IS NULL) AND fecha_entrega = '$_GET[fecha]' AND num_cia = 41 AND kilos IS NOT NULL AND resta_pagar = 0 ORDER BY num_remi";
			$ctrl1 = $db->query($sql);
			
			$sql = "SELECT letra_folio, num_remi, kilos, total_factura, NULL AS base FROM venta_pastel WHERE";
			$sql .= " (num_cia, num_remi, letra_folio) IN (SELECT num_cia, num_remi, letra_folio FROM venta_pastel WHERE id IN (";
			$sql .= "SELECT id FROM venta_pastel WHERE (num_cia, num_remi, letra_folio) IN (";
			$sql .= "SELECT num_cia, num_remi, letra_folio FROM venta_pastel WHERE";
			$sql .= " (estado != 2 OR estado IS NULL) AND fecha_entrega = '$_GET[fecha]' AND num_cia = 41 AND kilos IS NOT NULL AND resta_pagar > 0)";
			$sql .= " AND resta IS NOT NULL)) AND resta_pagar > 0";
			$sql .= " ORDER BY num_remi";
			$ctrl2 = $db->query($sql);
			
			$result = array_merge((array)$ctrl1, (array)$ctrl2);
			
			$sql = "SELECT sum(piezas) AS kilos FROM produccion WHERE";
			$sql .= " num_cia = {$cia[$i]['num_cia']} AND fecha = '$_GET[fecha]' AND cod_producto IN (211, 212, 215, 175, 375, 229, 230, 235, 234) AND piezas > 0";
			$pro = $db->query($sql);
			
			usort($result, "cmp");
			
			if ($result) {
				ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha'], $fecha);
				
				$tpl->newBlock("listado_dia");
				$tpl->assign("num_cia", $cia[$i]['num_cia']);
				$tpl->assign("nombre_cia", $cia[$i]['nombre']);
				$tpl->assign("dia", $fecha[1]);
				$tpl->assign("mes", mes_escrito($fecha[2]));
				$tpl->assign("anio", $fecha[3]);
				$tpl->assign("k_pro", $pro[0]['kilos'] > 0 ? number_format($pro[0]['kilos'], 2, ".", ",") : "&nbsp;");
				
				$kilos = 0;
				$total = 0;
				for ($j = 0; $j < count($result); $j++) {
					$tpl->newBlock("fac");
					$tpl->assign("factura", ($result[$j]['letra_folio'] != "X" ? $result[$j]['letra_folio'] : "") . $result[$j]['num_remi']);
					$tpl->assign("kilos", number_format($result[$j]['kilos'], 2, ".", ","));
					$tpl->assign("importe", number_format($result[$j]['total_factura'] - $result[$j]['base'], 2, ".", ","));
					
					$kilos += $result[$j]['kilos'];
					$total += $result[$j]['total_factura'] - $result[$j]['base'];
				}
				$tpl->assign("listado_dia.kilos", number_format($kilos, 2, ".", ","));
				$tpl->assign("listado_dia.total", number_format($total, 2, ".", ","));
				$tpl->assign("listado_dia.salto", "<br style=\"page-break-after:always;\">");
				$count++;
			}
		}
		
		if ($count == 0) {
			header("location: ./pan_rel_con_v2.php?codigo_error=1");
			die;
		}
		
		$tpl->printToScreen();
		die;
	}
}

$tpl->newBlock("datos");
$tpl->assign("fecha", date("d/m/Y"));
$tpl->assign(date("n"), "selected");
$tpl->assign("anio", date("Y"));

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>