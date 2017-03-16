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

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_con_cia_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	function filter($value) {
		return $value > 0;
	}
	
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, $_GET['anio']));
	$fecha2 = $_GET['anio'] < date('Y') ? date('d/m/Y', mktime(0, 0, 0, 12, 31, $_GET['anio'])) : ($_GET['tipo'] == 1 ? date('d/m/Y', mktime(0, 0, 0, date('n'), 0, $_REQUEST['anio'])) : date('d/m/Y'));
	
	$condiciones = array();
	
	$condiciones[] = 'tipo_mov = TRUE';
	
	$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
	
	if (count(array_filter($_GET['num_cia'], 'filter')) > 0) {
		$condiciones[] = 'num_cia IN (' . implode(', ', array_filter($_GET['num_cia'], 'filter')) . ')';
	}
	
	if ($_GET['admin'] > 0) {
		$condiciones[] = 'idadministrador = ' . $_GET['admin'];
	}
	
	if (count(array_filter($_GET['codmp'], 'filter')) > 0) {
		$condiciones[] = 'codmp IN (' . implode(', ', array_filter($_GET['codmp'], 'filter')) . ')';
	}
	
	if (count(array_filter($_GET['mes'], 'filter')) > 0) {
		$condiciones[] = 'EXTRACT(MONTH FROM fecha) IN (' . implode(', ', array_filter($_GET['mes'], 'filter')) . ')';
		
//		foreach (array_filter($_GET['mes'], 'filter') as $mes) {
//			$condiciones[] = 'fecha BETWEEN \'' . date('d/m/Y', mktime(0, 0, 0, $mes, 1, $_GET['anio'])) . '\' AND \'' . date('d/m/Y', mktime(0, 0, 0, $mes + 1, 0, $_GET['anio'])) . '\'';
//		}
	}
	
	$sql = '
		SELECT
			num_cia,
			codmp,
			cmp.nombre,
			EXTRACT(month FROM fecha)
				AS mes,
			SUM(cantidad)
				AS consumo
		FROM
			mov_inv_real mov
			LEFT JOIN catalogo_companias cc
				USING (num_cia)
			LEFT JOIN catalogo_mat_primas cmp
				USING (codmp)
		WHERE
			' . implode(' AND ', $condiciones) . '
		GROUP BY
			num_cia,
			codmp,
			cmp.nombre,
			mes
		ORDER BY
			num_cia,
			nombre,
			mes
	';
	
//	$sql = "(SELECT num_cia, codmp, cmp.nombre, mes, consumo, num_orden FROM consumos_mensuales cm LEFT JOIN catalogo_mat_primas cmp USING (codmp) LEFT JOIN control_avio ca USING (num_cia, codmp) LEFT JOIN catalogo_companias cc USING (num_cia)";
//	$sql .= " WHERE anio = $_GET[anio] AND controlada = 'TRUE'";
//	if (count($cias) > 0) {
//		$sql .= ' AND num_cia IN (';
//		foreach ($cias as $i => $cia)
//			$sql .= $cia . ($i < count($cias) - 1 ? ', ' : ')');
//	}
//	$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '';
//	$sql .= " GROUP BY num_cia, controlada, codmp, cmp.nombre, mes, consumo, num_orden UNION ALL";
//	$sql .= " SELECT num_cia, codmp, cmp.nombre, mes, consumo, 1000 AS num_orden FROM consumos_mensuales cm LEFT JOIN catalogo_mat_primas cmp USING (codmp) LEFT JOIN catalogo_companias cc USING (num_cia)";
//	$sql .= " WHERE anio = $_GET[anio] AND controlada = 'FALSE'";
//	if (count($cias) > 0) {
//		$sql .= ' AND num_cia IN (';
//		foreach ($cias as $i => $cia)
//			$sql .= $cia . ($i < count($cias) - 1 ? ', ' : ')');
//	}
//	$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '';
//	$sql .= ') ORDER BY num_cia, nombre, mes';
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./fac_con_cia_v2.php?codigo_error=1");
		die;
	}
	
	/*$nombre = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$numfilas_x_hoja = 48;
	$mp_conversion = array(1, 3, 4);
	$numfilas = $numfilas_x_hoja;
	$num_meses = $_GET['anio'] < date("Y") ? 12 : date("n") - 1;
	$codmp = NULL;
	$data = '';
	if ($_GET['tipo'] == 2) {
		$data .= "\"$_GET[num_cia] {$nombre[0]['nombre']}\"\n";
		$data .= "\"CONSUMOS ANUALES POR COMPAÑIA DEL $_GET[anio]\"\n\"\"\n";
		$data .= "\"PRODUCTO\",\"ENERO\",\"FEBRERO\",\"MARZO\",\"ABRIL\",\"MAYO\",\"JUNIO\",\"JULIO\",\"AGOSTO\",\"SEPTIEMBRE\",\"OCTUBRE\",\"NOVIEMBRE\",\"DICIEMBRE\",\"TOTAL\",\"PROMEDIO\"\n";
	}*/
	$num_cia = NULL;
	$data = '';
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			if ($num_cia != NULL) {
				if ($_GET['tipo'] == 1) {
					$tpl->assign("total", number_format($total, 2, ".", ","));
					$tpl->assign("prom", number_format($total / $num_meses, 2, ".", ","));
				}
				else {
					if ($last_month < 12)
						for ($i = 0; $i < 12 - $last_month; $i++)
							$data .= "\"\",";
					$data .= '"' . number_format($total, 2, '.', ',') . '","' . number_format($total / $num_meses, 2, ".", ",") . "\"\n";
				}
				
				if ($_GET['tipo'] == 1)
					$tpl->assign("listado.salto", "<br style=\"page-break-after:always;\">");
				else
					$data .= "\n\"\"\n";
			}
			
			$num_cia = $reg['num_cia'];
			
			$nombre = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = $num_cia");
			$numfilas_x_hoja = 45;
			$mp_conversion = array(1, 3, 4);
			$numfilas = $numfilas_x_hoja;
			$num_meses = $_GET['anio'] < date("Y") ? 12 : date("n") - 1;
			$codmp = NULL;
			
			if ($_GET['tipo'] == 2) {
				$data .= "\"$num_cia {$nombre[0]['nombre']}\"\n";
				$data .= "\"CONSUMOS ANUALES POR COMPAÑIA DEL $_GET[anio]\"\n\"\"\n";
				$data .= "\"PRODUCTO\",\"ENERO\",\"FEBRERO\",\"MARZO\",\"ABRIL\",\"MAYO\",\"JUNIO\",\"JULIO\",\"AGOSTO\",\"SEPTIEMBRE\",\"OCTUBRE\",\"NOVIEMBRE\",\"DICIEMBRE\",\"TOTAL\",\"PROMEDIO\"\n";
			}
		}
		
		if ($codmp != $reg['codmp']) {
			if ($codmp != NULL) {
				if ($_GET['tipo'] == 1) {
					$tpl->assign("total", number_format($total, 2, ".", ","));
					$tpl->assign("prom", number_format($total / $num_meses, 2, ".", ","));
				}
				else {
					if ($last_month < 12)
						for ($i = 0; $i < 12 - $last_month; $i++)
							$data .= "\"\",";
					$data .= '"' . number_format($total, 2, '.', ',') . '","' . number_format($total / $num_meses, 2, ".", ",") . "\"\n";
				}
			}
			if ($numfilas == $numfilas_x_hoja) {
				if ($codmp != NULL) {
					if ($_GET['tipo'] == 1)
						$tpl->assign("listado.salto", "<br style=\"page-break-after:always;\">");
					else
						$data .= "\n\"\"\n";
				}
				
				if ($_GET['tipo'] == 1) {
					$tpl->newBlock("listado");
					$tpl->assign("anio", $_GET['anio']);
					$tpl->assign("num_cia", /*$_GET['num_cia']*/$num_cia);
					$tpl->assign("nombre", $nombre[0]['nombre']);
				}
				
				$numfilas = 0;
			}
			
			$codmp = $reg['codmp'];
			
			if ($_GET['tipo'] == 1) {
				$tpl->newBlock("fila");
				$tpl->assign("codmp", $codmp);
				$tpl->assign("nombre", strlen($reg['nombre']) > 17 ? substr($reg['nombre'], 0, 15) . '...' : $reg['nombre']);
			}
			else
				$data .= "\"$codmp $reg[nombre]\",";
			
			$last_month = 0;
			$total = 0;
			$numfilas++;
		}
		if ($_GET['tipo'] == 1)
			$tpl->assign($reg['mes'], number_format(in_array($reg['codmp'], $mp_conversion) ? ($reg['codmp'] == 1 ? $reg['consumo'] / 44 : $reg['consumo'] / 50) : $reg['consumo'], 2, ".", ","));
		else {
			if ($last_month < $reg['mes'] - 1)
				for ($i = 0; $i < $reg['mes'] - $last_month - 1; $i++)
					$data .= "\"\",";
			$data .= '"' . number_format(in_array($reg['codmp'], $mp_conversion) ? ($reg['codmp'] == 1 ? $reg['consumo'] / 44 : $reg['consumo'] / 50) : $reg['consumo'], 2, ".", ",") . '",';
			$last_month = $reg['mes'];
		}
		$total += in_array($reg['codmp'], $mp_conversion) ? ($reg['codmp'] == 1 ? $reg['consumo'] / 44 : $reg['consumo'] / 50) : $reg['consumo'];
	}
	if ($codmp != NULL) {
		if ($_GET['tipo'] == 1) {
			$tpl->assign("total", number_format($total, 2, ".", ","));
			$tpl->assign("prom", number_format($total / $num_meses, 2, ".", ","));
		}
		else {
			if ($last_month < 12)
				for ($i = 0; $i < 12 - $last_month; $i++)
					$data .= "\"\",";
			$data .= '"' . number_format($total, 2, '.', ',') . '","' . number_format($total / $num_meses, 2, ".", ",") . "\"\n";
		}
	}
	
	if ($_GET['tipo'] == 1)
		$tpl->printToScreen();
	else {
		header("Content-Type: application/download");
		header("Content-Disposition: attachment; filename=consumos.csv");
		echo $data;
	}
	die;
}

$tpl->newBlock("datos");
$tpl->assign("anio", date("Y"));

$result = $db->query('SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores ORDER BY admin');
foreach ($result as $r) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $r['id']);
	$tpl->assign('admin', $r['admin']);
}

if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>