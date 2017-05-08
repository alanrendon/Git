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
$tpl->assignInclude("body","./plantillas/bal/bal_ven_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$fecha1 = "01/01/$_GET[anio]";
	$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
	
	// SQL para obtener compañías con venta en puerta
	$sql = "SELECT num_cia, nombre_corto FROM total_panaderias LEFT JOIN catalogo_companias USING (num_cia) WHERE";
	$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' AND num_cia NOT IN (999) AND venta_puerta > 0";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : " AND num_cia < 100";
	$sql .= " GROUP BY num_cia, nombre_corto ORDER BY num_cia";
	$cia = $db->query($sql);
	
	if (!$cia) {
		header("location: ./bal_ven_con.php?codigo_error=1");
		die;
	}
	
	function buscarVenta($mes) {
		global $v_puerta;
		
		if (!$v_puerta)
			return FALSE;
		
		for ($i = 0; $i < count($v_puerta); $i++)
			if ($mes == $v_puerta[$i]['mes'])
				return $v_puerta[$i]['v_puerta'];
		
		return FALSE;
	}
	
	function buscarPastel($mes) {
		global $v_pastel;
		
		if (!$v_pastel)
			return FALSE;
		
		for ($i = 0; $i < count($v_pastel); $i++)
			if ($mes == $v_pastel[$i]['mes'])
				return $i;
		
		return FALSE;
	}
	
	function buscarKilosEntregados($mes) {
		global /*$k_pastel1, $k_pastel2*/$k_pastel_ent;
		
		if (/*!$k_pastel1 && !$k_pastel2*/!$k_pastel_ent)
			return 0;
		
		$kilos = 0;
		/*for ($i = 0; $i < count($k_pastel1); $i++)
			if ($mes == $k_pastel1[$i]['mes'])
				$kilos += $k_pastel1[$i]['kilos'];
		
		for ($i = 0; $i < count($k_pastel2); $i++)
			if ($mes == $k_pastel2[$i]['mes'])
				$kilos += $k_pastel2[$i]['kilos'];*/
		for ($i = 0; $i < count($k_pastel_ent); $i++)
			if ($mes == $k_pastel_ent[$i]['mes'])
				$kilos += $k_pastel_ent[$i]['kilos'];
		
		return $kilos;
	}
	
	function buscarKilosPedidos($mes) {
		global $k_pastel_ped;
		
		if (!$k_pastel_ped)
			return 0;
		
		$kilos = 0;
		for ($i = 0; $i < count($k_pastel_ped); $i++)
			if ($mes == $k_pastel_ped[$i]['mes'])
				$kilos += $k_pastel_ped[$i]['kilos'];
		
		return $kilos;
	}
	
	$numfilas_x_hoja = 11;
	$numfilas = 11;
	// Recorrer las compañías
	for ($i = 0; $i < count($cia); $i++) {
		if ($numfilas >= $numfilas_x_hoja) {
			$tpl->newBlock("listado");
			$tpl->assign("mes", mes_escrito($_GET['mes']));
			$tpl->assign("anio", $_GET['anio']);
			for ($j = 1; $j <= $_GET['mes']; $j++) {
				$tpl->newBlock("title_mes");
				$tpl->assign("mes", mes_escrito($j));
			}
			$tpl->newBlock("title_mes");
			$tpl->assign("mes", "Total");
			
			$numfilas = 0;
		}
		$tpl->newBlock("cia");
		$tpl->assign("num_cia", $cia[$i]['num_cia']);
		$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
		
		// Obtener la venta en puerta para la compañía en curso
		$sql = "SELECT extract(month FROM fecha) AS mes, sum(venta_puerta) AS v_puerta FROM total_panaderias LEFT JOIN catalogo_companias USING (num_cia) WHERE";
		$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2' AND num_cia = {$cia[$i]['num_cia']}";
		$sql .= " GROUP BY extract(month FROM fecha) ORDER BY extract(month FROM fecha)";
		$v_puerta = $db->query($sql);
		
		if ($v_puerta) {
			// Obtener la venta de pastel pedido para la compañía en curso
			$sql = "SELECT extract(month FROM fecha) AS mes, sum(cuenta) AS cuenta, sum(resta) AS resta, sum(base) AS base FROM venta_pastel WHERE";
			$sql .= " (estado != 2 OR estado IS NULL) AND fecha BETWEEN '$fecha1' AND '$fecha2' AND num_cia = {$cia[$i]['num_cia']}";
			$sql .= " GROUP BY extract(month FROM fecha) ORDER BY extract(month FROM fecha)";
			$v_pastel = $db->query($sql);
			
			// Obtener los kilos de pastel entregado para la compañía en curso (Notas pagadas en control 1)
			/*$sql = "SELECT extract(month FROM fecha_entrega) AS mes, sum(kilos) AS kilos FROM venta_pastel WHERE (estado != 2 OR estado IS NULL)";
			$sql .= " AND fecha_entrega BETWEEN '$fecha1' AND '$fecha2' AND num_cia = {$cia[$i]['num_cia']} AND kilos IS NOT NULL AND resta_pagar = 0";
			$sql .= " GROUP BY extract(month FROM fecha_entrega) ORDER BY extract(month FROM fecha_entrega)";
			$k_pastel1 = $db->query($sql);*/
			
			// Obtener los kilos de pastel entregado para la compañía en curso (Notas pagadas en control 2)
			/*$sql = "SELECT extract(month FROM fecha_entrega) AS mes, sum(kilos) AS kilos FROM venta_pastel WHERE (num_cia, num_remi, letra_folio) IN (";
			$sql .= "SELECT num_cia, num_remi, letra_folio FROM venta_pastel WHERE id IN (";
			$sql .= "SELECT id FROM venta_pastel WHERE (num_cia, num_remi, letra_folio) IN (";
			$sql .= "SELECT num_cia, num_remi, letra_folio FROM venta_pastel WHERE (estado != 2 OR estado IS NULL) AND";
			$sql .= " fecha_entrega BETWEEN '$fecha1' AND '$fecha2' AND num_cia = {$cia[$i]['num_cia']} AND kilos IS NOT NULL AND resta_pagar > 0";
			$sql .= ") AND resta IS NOT NULL)) AND resta_pagar > 0 GROUP BY extract(month FROM fecha_entrega) ORDER BY extract(month FROM fecha_entrega)";
			$k_pastel2 = $db->query($sql);*/
			
			// [8-Agosto-2008] Obtener los kilos de pastel entregado para la compañía en curso
			$sql = "SELECT extract(month FROM fecha_entrega) AS mes, sum(kilos) AS kilos FROM venta_pastel WHERE estado IS NOT NULL AND estado <> 2 AND fecha_entrega BETWEEN '$fecha1' AND '$fecha2' AND num_cia = {$cia[$i]['num_cia']} AND kilos IS NOT NULL GROUP BY mes ORDER BY mes";
			$k_pastel_ent = $db->query($sql);
			
			// [8-Agosto-2008] Obtener los kilos de pastel pedido para la compañía en curso
			$sql = "SELECT extract(month FROM fecha) AS mes, sum(kilos) AS kilos FROM venta_pastel WHERE estado IS NOT NULL AND estado <> 2 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND num_cia = {$cia[$i]['num_cia']} AND kilos IS NOT NULL GROUP BY mes ORDER BY mes";
			$k_pastel_ped = $db->query($sql);
			
			$total_venta = 0;
			$total_pastel = 0;
			$total_puerta = 0;
			$total_kilos_entregados = 0;
			$total_kilos_pedidos = 0;
			
			for ($j = 1; $j <= $_GET['mes']; $j++)
				if (($venta_puerta = buscarVenta($j)) !== FALSE) {
					$pastel = 0;
					$kilos_entregados = 0;
					$kilos_pedidos = 0;
					if (($index_pastel = buscarPastel($j)) !== FALSE) {
						$pastel = $v_pastel[$index_pastel]['cuenta'] + $v_pastel[$index_pastel]['resta'] - $v_pastel[$index_pastel]['base'];
						$kilos_entregados = buscarKilosEntregados($j);
						$kilos_pedidos = buscarKilosPedidos($j);
					}
					
					$venta = $venta_puerta - $pastel;
					
					$tpl->newBlock("venta");
					$tpl->assign("venta", number_format($venta, 2, ".", ","));
					$tpl->newBlock("pastel");
					$tpl->assign("pastel", $pastel > 0 ? number_format($pastel, 2, ".", ",") : "&nbsp;");
					$tpl->newBlock("vpuerta");
					$tpl->assign("vpuerta", number_format($venta_puerta, 2, ".", ","));
					$tpl->newBlock("kilos_entregados");
					$tpl->assign("kilos", $kilos_entregados > 0 ? number_format($kilos_entregados, 2, ".", ",") : "&nbsp;");
					$tpl->newBlock("kilos_pedidos");
					$tpl->assign("kilos", $kilos_pedidos > 0 ? number_format($kilos_pedidos, 2, ".", ",") : "&nbsp;");
					
					$total_venta += $venta;
					$total_pastel += $pastel;
					$total_puerta += $venta_puerta;
					$total_kilos_entregados += $kilos_entregados;
					$total_kilos_pedidos += $kilos_pedidos;
				}
				else {
					$tpl->newBlock("venta");
					$tpl->assign("venta", "&nbsp;");
					$tpl->newBlock("pastel");
					$tpl->assign("pastel", "&nbsp;");
					$tpl->newBlock("vpuerta");
					$tpl->assign("vpuerta", "&nbsp;");
					$tpl->newBlock("kilos_entregados");
					$tpl->assign("kilos", "&nbsp;");
					$tpl->newBlock("kilos_pedidos");
					$tpl->assign("kilos", "&nbsp;");
				}
			$tpl->newBlock("venta");
			$tpl->assign("venta", "<strong>" . number_format($total_venta, 2, ".", ",") . "</strong>");
			$tpl->newBlock("pastel");
			$tpl->assign("pastel", $total_pastel > 0 ? "<strong>" . number_format($total_pastel, 2, ".", ",") . "</strong>" : "&nbsp;");
			$tpl->newBlock("vpuerta");
			$tpl->assign("vpuerta", "<strong>" . number_format($total_puerta, 2, ".", ",") . "</strong>");
			$tpl->newBlock("kilos_entregados");
			$tpl->assign("kilos", $total_kilos_entregados > 0 ? "<strong>" . number_format($total_kilos_entregados, 2, ".", ",") . "</strong>" : "&nbsp;");
			$tpl->newBlock("kilos_pedidos");
			$tpl->assign("kilos", $total_kilos_pedidos > 0 ? "<strong>" . number_format($total_kilos_pedidos, 2, ".", ",") . "</strong>" : "&nbsp;");
			
			
			$numfilas++;
			if ($numfilas >= $numfilas_x_hoja)
				$tpl->newBlock("salto");
		}
	}
	
	$tpl->printToScreen();
	
	die;
}

$mes = date("m");
$anio = date("Y");

$tpl->newBlock("datos");
$tpl->assign(date("n", mktime(0, 0, 0, $mes, 0, $anio)), "selected");
$tpl->assign("anio", date("Y", mktime(0, 0, 0, $mes, 0, $anio)));

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>