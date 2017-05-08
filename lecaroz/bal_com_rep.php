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
$tpl->assignInclude("body","./plantillas/bal/bal_com_rep.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	if (!isset($_GET['des'])) {
		$fecha1 = "01/01/$_GET[anio]";
		$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
		
		$sql = "SELECT num_cia, nombre_corto, idadministrador AS admin FROM movimiento_gastos LEFT JOIN catalogo_gastos USING (codgastos) LEFT JOIN catalogo_companias USING (num_cia)";
		$sql .= " WHERE aplicacion_gasto = 'TRUE' AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
		$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : "";
		$sql .= " GROUP BY num_cia, nombre_corto, idadministrador";
		$sql .= $_GET['admin'] < 0 ? " ORDER BY idadministrador, num_cia" : " ORDER BY num_cia";
		$cias = $db->query($sql);
		
		if (!$cias) {
			header("location: ./bal_com_rep.php?codigo_error=1");
			die;
		}
		
		// [13-Ene-2009] Obtener listado de gastos
		$sql = "SELECT codgastos, descripcion FROM movimiento_gastos LEFT JOIN catalogo_gastos USING (codgastos) LEFT JOIN catalogo_companias USING (num_cia) WHERE aplicacion_gasto = 'TRUE' AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
		$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '';
		$sql .= ' GROUP BY codgastos, descripcion ORDER BY codgastos';
		$cod_gastos = $db->query($sql);
		
		if ($_GET['admin'] >= 0) {
			$tpl->newBlock("listado");
			$tpl->assign("mes", mes_escrito($_GET['mes']));
			$tpl->assign("anio", $_GET['anio']);
			for ($i = 1; $i <= $_GET['mes']; $i++) {
				$tpl->newBlock("tmes");
				$tpl->assign("mes", mes_escrito($i));
			}
			//$tpl->newBlock("tmes");
			//$tpl->assign("mes", "Total");
			$tpl->newBlock("tmes");
			$tpl->assign("mes", "Promedio");
		}
		
		function buscar($mes) {
			global $gastos;
			
			if (!$gastos)
				return FALSE;
			
			foreach ($gastos as $gasto)
				if ($mes == $gasto['mes'])
					return $gasto['importe'];
			
			return FALSE;
		}
		
		function buscarExp($mes) {
			global $exps;
			
			if (!$exps)
				return FALSE;
			
			foreach ($exps as $exp)
				if ($mes == $exp['mes'])
					return $exp;
			
			return FALSE;
		}
		
		function buscarPas($mes) {
			global $pasteles;
			
			if (!$pasteles)
				return FALSE;
			
			foreach ($pasteles as $pas)
				if ($mes == $pas['mes'])
					return $pas;
			
			return FALSE;
		}
		
		if ($_GET['admin'] >= 0) {
			$total_mes = array();
			for ($i = 1; $i <= $_GET['mes']; $i++)
				$total_mes[$i] = 0;
		}
		
		$admin = NULL;
		$cont = 0;
		foreach ($cias as $cia) {
			if ($_GET['admin'] < 0 && $admin != $cia['admin']) {
				if ($admin != NULL) {
					$tpl->newBlock("totales");
					foreach ($total_mes as $total) {
						$tpl->newBlock("total");
						$tpl->assign("total", number_format($total / $cont, 3, ".", ","));
					}
					$tpl->newBlock("total");
					$tpl->assign("total", number_format(array_sum($total_mes) / $_GET['mes'], 3, ".", ","));
					
					foreach ($codgastos as $reg) {
						$tpl->newBlock('gasto');
						$tpl->assign('cod', $reg['codgastos']);
						$tpl->assign('desc', $reg['descripcion']);
					}
					
					$tpl->assign("listado.salto", "<br style=\"page-break-after:always;\">");
				}
				
				$admin = $cia['admin'];
				
				$tpl->newBlock("listado");
				$tpl->assign("mes", mes_escrito($_GET['mes']));
				$tpl->assign("anio", $_GET['anio']);
				for ($i = 1; $i <= $_GET['mes']; $i++) {
					$tpl->newBlock("tmes");
					$tpl->assign("mes", mes_escrito($i));
				}
				//$tpl->newBlock("tmes");
				//$tpl->assign("mes", "Total");
				$tpl->newBlock("tmes");
				$tpl->assign("mes", "Promedio");
				
				$total_mes = array();
				for ($i = 1; $i <= $_GET['mes']; $i++)
					$total_mes[$i] = 0;
				
				$cont = 0;
			}
			
			$sql = "SELECT extract(month from fecha) AS mes, sum(importe) AS importe FROM movimiento_gastos LEFT JOIN catalogo_gastos USING (codgastos) WHERE aplicacion_gasto = 'TRUE'";
			$sql .= " AND fecha BETWEEN '$fecha1' AND '$fecha2' AND num_cia = $cia[num_cia] GROUP BY extract(month from fecha) ORDER BY extract(month from fecha)";
			$gastos = $db->query($sql);
			
			$sql = "SELECT extract(month from fecha) AS mes, sum(pan_p_venta) AS pan_venta, sum(pan_p_expendio) AS pan_exp, sum(devolucion) AS devolucion, sum(abono) AS abono FROM mov_expendios";
			$sql .= " WHERE num_cia = $cia[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY extract(month from fecha) ORDER BY extract(month from fecha)";
			$exps = $db->query($sql);
			
			if (!$exps)
				continue;
			
			// [09-Ene-2008] Obtener importe de facturas de pastel
			$sql = "SELECT extract(month from fecha) AS mes, sum(cuenta + CASE WHEN resta IS NOT NULL THEN resta ELSE 0 END - CASE WHEN dev_base IS NOT NULL THEN dev_base ELSE 0 END) AS pasteles FROM venta_pastel WHERE num_cia = $cia[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2' AND (cuenta > 0 OR resta > 0) AND estado < 2 GROUP BY mes ORDER BY mes";
			$pasteles = $db->query($sql);
			
			$tpl->newBlock("cia");
			$tpl->assign("num_cia", $cia['num_cia']);
			$tpl->assign("nombre", $cia['nombre_corto']);
			$cont++;
			
			$total = 0;
			
			for ($i = 1; $i <= $_GET['mes']; $i++) {
				$gasto = buscar($i);
				$exp = buscarExp($i);
				$pas = buscarPas($i);
				@$dato = ($gasto + ($exp['pan_venta']/* + $pas['pasteles']*/ - $exp['pan_exp']) + $exp['devolucion']) / ($exp['pan_venta'] + $pas['pasteles']);
				
				$total += $dato;
				$total_mes[$i] += $dato;
				$tpl->newBlock("mes");
				$tpl->assign("dato", $dato != 0 ? number_format($dato, 3, ".", ",") : "&nbsp;");
			}
			//$tpl->newBlock("mes");
			//$tpl->assign("dato", "<strong>" . number_format($total, 3, ".", ",") . "</strong>");
			$tpl->newBlock("mes");
			$tpl->assign("dato", "<strong>" . number_format($total / $_GET['mes'], 3, ".", ",") . "</strong>");
		}
		
		if ($_GET['admin'] >= 0) {
			$tpl->newBlock("totales");
			foreach ($total_mes as $total) {
				$tpl->newBlock("total");
				$tpl->assign("total", number_format($total / $cont, 3, ".", ","));
			}
			$tpl->newBlock("total");
			$tpl->assign("total", number_format(array_sum($total_mes) / $cont / $_GET['mes'], 3, ".", ","));
			
			foreach ($cod_gastos as $reg) {
				$tpl->newBlock('gasto');
				$tpl->assign('cod', $reg['codgastos']);
				$tpl->assign('desc', $reg['descripcion']);
			}
		}
	}
	else {
		$fecha1 = "01/01/$_GET[anio]";
		$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
		
		$sql = "SELECT num_cia, nombre_corto, idadministrador AS admin FROM movimiento_gastos LEFT JOIN catalogo_gastos USING (codgastos) LEFT JOIN catalogo_companias USING (num_cia)";
		$sql .= " WHERE aplicacion_gasto = 'TRUE' AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
		$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : "";
		$sql .= " GROUP BY num_cia, nombre_corto, idadministrador";
		$sql .= $_GET['admin'] < 0 ? " ORDER BY idadministrador, num_cia" : " ORDER BY num_cia";
		$cias = $db->query($sql);print_r($cias);
		
		if (!$cias) {
			header("location: ./bal_com_rep.php?codigo_error=1");
			die;
		}
		
		$num_cia = NULL;
		foreach ($cias as $cia) {
			$total_gastos = 0;
			
			$sql = "(SELECT extract(month from fecha) AS mes, codgastos, descripcion, sum(importe) AS importe FROM movimiento_gastos LEFT JOIN catalogo_gastos USING (codgastos)";
			$sql .= " WHERE aplicacion_gasto = 'TRUE' AND fecha BETWEEN '$fecha1' AND '$fecha2' AND num_cia = $cia[num_cia] GROUP BY extract(month from fecha), codgastos, descripcion)";
			$sql .= " ORDER BY codgastos, mes";
			$gastos = $db->query($sql);
			
			$sql = "SELECT extract(month from fecha) AS mes, sum(pan_p_venta) AS pan_venta, sum(pan_p_expendio) AS pan_exp, sum(devolucion) AS devolucion, sum(abono) AS abono FROM mov_expendios";
			$sql .= " WHERE num_cia = $cia[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY extract(month from fecha) ORDER BY extract(month from fecha)";
			$exps = $db->query($sql);
			
			// [09-Ene-2008] Obtener importe de facturas de pastel
			$sql = "SELECT extract(month from fecha) AS mes, sum(cuenta + CASE WHEN resta IS NOT NULL THEN resta ELSE 0 END - CASE WHEN dev_base IS NOT NULL THEN dev_base ELSE 0 END) AS pasteles FROM venta_pastel WHERE num_cia = $cia[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2' AND (cuenta > 0 OR resta > 0) AND estado < 2 GROUP BY mes ORDER BY mes";
			$pasteles = $db->query($sql);
			
			if ($exps) {
				$tpl->newBlock("desglose");
				$tpl->assign("num_cia", $cia['num_cia']);
				$tpl->assign("nombre", $cia['nombre_corto']);
				$tpl->assign("mes", mes_escrito($_GET['mes']));
				$tpl->assign("anio", $_GET['anio']);
				
				for ($i = 1; $i <= $_GET['mes']; $i++) {
					$tpl->newBlock("tmes_des");
					$tpl->assign("mes", mes_escrito($i));
					$tpl->newBlock("tdatos");
				}
				
				if ($gastos) {
					$ganancia = array();
					$total_gastos = array();
					$devolucion = array();
					$reparto = array();
					$porc = array();
					for ($i = 1; $i <= $_GET['mes']; $i++) {
						$ganancia[$i] = 0;
						$total_gastos[$i] = 0;
						$devolucion[$i] = 0;
						$reparto[$i] = 0;
						$porc[$i] = 0;
					}
					
					foreach ($exps as $exp) {
						$devolucion[$exp['mes']] = $exp['devolucion'];
						$reparto[$exp['mes']] = $exp['pan_venta'];
					}
					foreach ($gastos as $gasto)
						$total_gastos[$gasto['mes']] += $gasto['importe'] != 0 ? $gasto['importe'] : 0;
					foreach ($exps as $exp) {
						$ganancia[$exp['mes']] += $exp['pan_venta'] - $exp['pan_exp'];
						$total_gastos[$exp['mes']] += $ganancia[$exp['mes']] + $devolucion[$exp['mes']];
					}
					// [09-Ene-2008] Sumar lo abonado de pasteles a reparto
					if ($pasteles)
						foreach ($pasteles as $pas)
							$reparto[$pas['mes']] += $pas['pasteles'];
					
					$codgastos = NULL;
					foreach ($gastos as $gasto) {
						if ($codgastos != $gasto['codgastos']) {
							$codgastos = $gasto['codgastos'];
							
							$tpl->newBlock("gasto_des");
							$tpl->assign("codgastos", $codgastos);
							$tpl->assign("descripcion", $gasto['descripcion']);
							
							$mes_actual = 1;
						}
						if ($mes_actual < $gasto['mes']) {
							for ($j = $mes_actual; $j < $gasto['mes']; $j++) {
								$tpl->newBlock("dato");
								$tpl->assign("importe", "&nbsp;");
								$tpl->assign("porc", "&nbsp;");
								$mes_actual++;
							}
						}
						if ($gasto['importe'] != 0) {
							$tpl->newBlock("dato");
							$tpl->assign("importe", number_format($gasto['importe'], 2, ".", ","));
							$tpl->assign("porc", number_format($gasto['importe'] * 100 / $total_gastos[$gasto['mes']], 2, ".", ",") . "%");
							$mes_actual++;
						}
					}
					
					for ($i = 1; $i <= $_GET['mes']; $i++) {
						$tpl->newBlock("devolucion");
						$tpl->assign("devolucion", $devolucion[$i] != 0 ? number_format($devolucion[$i], 2, ".", ",") : "&nbsp;");
						$tpl->assign("porc_dev", $devolucion[$i] != 0 ? number_format($devolucion[$i] * 100 / $total_gastos[$i], 2, ".", ",") : "&nbsp;");
						$tpl->newBlock("ganancia");
						$tpl->assign("ganancia", $ganancia[$i] != 0 ? number_format($ganancia[$i], 2, ".", ",") : "&nbsp;");
						$tpl->assign("porc_gan", $ganancia[$i] != 0 ? number_format($ganancia[$i] * 100 / $total_gastos[$i], 2, ".", ",") : "&nbsp;");
						$tpl->newBlock("total_gastos");
						$tpl->assign("total_gastos", number_format($total_gastos[$i], 2, ".", ","));
						$tpl->newBlock("reparto");
						$tpl->assign("reparto", $reparto[$i] != 0 ? number_format($reparto[$i], 2, ".", ",") : "&nbsp;");
						@$porc = $total_gastos[$i] / $reparto[$i] * 100;
						$tpl->newBlock("porc");
						$tpl->assign("porc", number_format($porc, 1, ".", ","));
					}
				}
			}
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

$admin = $db->query("SELECT * FROM catalogo_administradores ORDER BY nombre_administrador");
for ($i = 0; $i < count($admin); $i++) {
	$tpl->newBlock("admin");
	$tpl->assign("id", $admin[$i]['idadministrador']);
	$tpl->assign("nombre", $admin[$i]['nombre_administrador']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>