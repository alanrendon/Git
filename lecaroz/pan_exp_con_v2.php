<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_exp_con_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Buscar datos de compañía -------------------------------------------------
if (!isset($_GET['tipo'])) {
	$fecha1 = date("d/m/Y", mktime(0, 0, 0, date("d") > 1 ? date("n") : date("n") - 1, 1, date("Y")));
	$fecha2 = date("d/m/Y", mktime(0, 0, 0, date("n"), date("d") > 1 ? date("d") - 1 : 0, date("Y")));
	
	$tpl->newBlock($_SESSION['iduser'] == 3 ? "datos2" : "datos1");
	$tpl->assign("fecha1", $fecha1);
	$tpl->assign("fecha2", $fecha2);
	
	if ($_SESSION['iduser'] != 3) {
		$admins = $db->query('
			SELECT
				idadministrador
					AS value,
				nombre_administrador
					AS text
			FROM
				catalogo_administradores
			ORDER BY
				text
		');
		
		if ($admins) {
			foreach ($admins as $a) {
				$tpl->newBlock('admin');
				$tpl->assign('value', $a['value']);
				$tpl->assign('text', $a['text']);
			}
		}
		
		$ag = $db->query('SELECT idagven, nombre FROM catalogo_agentes_venta ORDER BY nombre');
		if ($ag)
			foreach ($ag as $a) {
				$tpl->newBlock('idagven');
				$tpl->assign('id', $a['idagven']);
				$tpl->assign('nombre', $a['nombre']);
			}
	}
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}

	$tpl->printToScreen();
	die();
}

// ---------------------------------- Listado por día -----------------------------------------------------
if ($_GET['tipo'] == 1 || ($_GET['tipo'] == 1 && $_SESSION['iduser'] == 3)) {
	// Obtener registros
	if ($_SESSION['iduser'] == 3) {
		$sql = "SELECT num_cia, nombre_corto, num_expendio, nombre_expendio, sum(pan_p_venta) AS pan_p_venta, sum(pan_p_expendio) AS pan_p_expendio, sum(abono) AS abono, sum(devolucion) AS devolucion,";
		$sql .= " avg(porc_ganancia) AS porc_ganancia, AVG(pan_p_expendio) AS promedio FROM mov_expendios LEFT JOIN catalogo_expendios USING (num_cia, num_expendio) LEFT JOIN catalogo_companias USING (num_cia) WHERE idagven = 1";
		$sql .= " AND fecha " . ($_GET['fecha2'] == "" ? "= '$_GET[fecha1]'" : "BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'");
		$sql .= " GROUP BY num_cia, nombre_corto, num_expendio, nombre_expendio ORDER BY num_cia, num_expendio";
	}
	else if ($_GET['idagven'] > 0) {
		$sql = "SELECT num_cia, nombre_corto, num_expendio, nombre_expendio, sum(pan_p_venta) AS pan_p_venta, sum(pan_p_expendio) AS pan_p_expendio, sum(abono) AS abono, sum(devolucion) AS devolucion,";
		$sql .= " avg(porc_ganancia) AS porc_ganancia, AVG(pan_p_expendio) AS promedio FROM mov_expendios LEFT JOIN catalogo_expendios USING (num_cia, num_expendio) LEFT JOIN catalogo_companias USING (num_cia) WHERE idagven = $_GET[idagven]";
		$sql .= " AND fecha " . ($_GET['fecha2'] == "" ? "= '$_GET[fecha1]'" : "BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'");
		$sql .= " GROUP BY num_cia, nombre_corto, num_expendio, nombre_expendio ORDER BY num_cia, num_expendio";
	}
	else {
		$sql = "SELECT num_expendio, nombre_expendio, sum(pan_p_venta) AS pan_p_venta, sum(pan_p_expendio) AS pan_p_expendio, sum(abono) AS abono, sum(devolucion) AS devolucion,";
		$sql .= " avg(porc_ganancia) AS porc_ganancia, AVG(pan_p_expendio) AS promedio FROM mov_expendios WHERE num_cia = $_GET[num_cia]";
		$sql .= " AND fecha " . ($_GET['fecha2'] == "" ? "= '$_GET[fecha1]'" : "BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]'");
		$sql .= " GROUP BY num_expendio, nombre_expendio ORDER BY num_expendio";
	}
	$result = $db->query($sql);
	
	// Si no hay resultados
	if (!$result) {
		header("location: ./pan_exp_con_v2.php?codigo_error=1");
		die;
	}
	
	// Crear bloque de consulta
	$tpl->newBlock($_GET['idagven'] > 0 ? 'agentes' : "saldos");
	
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_GET['fecha2'],$fecha);
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})",$_GET['fecha1'],$fecha1);
	
	// Asignar valores a encabezado
	if ($_GET['idagven'] <= 0) {
		$tpl->assign("num_cia",$_GET['num_cia']);			// Asignar número de compañía
		$nombre_cia = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
		$tpl->assign("nombre_cia",$nombre_cia[0]['nombre']);	// Asignar nombre de compañía
	}
	$tpl->assign("dia1",$fecha1[1]);						// Asignar día
	$tpl->assign("anio1",$fecha1[3]);						// Asignar año
	$tpl->assign("mes1",mes_escrito($fecha1[2]));						// Asignar mes
	$tpl->assign("dia2",$fecha[1]);						// Asignar día
	$tpl->assign("anio2",$fecha[3]);						// Asignar año
	$tpl->assign("mes2",mes_escrito($fecha[2]));						// Asignar mes

	$pan_p_venta = 0;
	$pan_p_expendio = 0;
	$abono = 0;
	$devolucion = 0;
	$rezago_anterior = 0;
	$rezago_actual = 0;
	$porc = 0;
	$diferencia = 0;
	
	$num_cia = NULL;
	for ($i = 0; $i < count($result); $i++) {
		if ($_GET['idagven'] > 0 && $num_cia != $result[$i]['num_cia']) {
			$num_cia = $result[$i]['num_cia'];
			
			$tpl->newBlock('cia_ag');
			$cia_pan_p_venta = 0;
			$cia_pan_p_expendio = 0;
			$cia_abono = 0;
			$cia_devolucion = 0;
			$cia_rezago_anterior = 0;
			$cia_rezago_actual = 0;
			$cia_porc = 0;
			$cia_diferencia = 0;
		}
		
		// Saldo inicial del expendio
		$sql = "SELECT rezago_anterior FROM mov_expendios WHERE num_cia = " . ($_GET['idagven'] > 0 ? $result[$i]['num_cia'] : $_GET['num_cia']) . " AND num_expendio = {$result[$i]['num_expendio']} AND nombre_expendio = '{$result[$i]['nombre_expendio']}'";
		$sql .= " AND fecha BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]' ORDER BY fecha LIMIT 1";
		$saldo_ini = $db->query($sql);
		
		// Saldo final del expendio
		$sql = "SELECT rezago FROM mov_expendios WHERE num_cia = " . ($_GET['idagven'] > 0 ? $result[$i]['num_cia'] : $_GET['num_cia']) . " AND num_expendio = {$result[$i]['num_expendio']} AND nombre_expendio = '{$result[$i]['nombre_expendio']}'";
		$sql .= " AND fecha BETWEEN '$_GET[fecha1]' AND '$_GET[fecha2]' ORDER BY fecha DESC LIMIT 1";
		$saldo_fin = $db->query($sql);
		
		// [24-Jul-2007] Calculo de días de atraso
		$dias_exceso = 2000;
		$saldo = $saldo_fin[0]['rezago'];
		$dias_atraso = 0;
		$sql = "SELECT pan_p_expendio FROM mov_expendios WHERE num_cia = " . ($_GET['idagven'] > 0 ? $result[$i]['num_cia'] : $_GET['num_cia']);
		$sql .= " AND num_expendio = {$result[$i]['num_expendio']} AND nombre_expendio = '{$result[$i]['nombre_expendio']}' AND fecha BETWEEN";
		$sql .= " cast('$_GET[fecha2]' as date) - interval '2000 days' AND '$_GET[fecha2]' ORDER BY fecha DESC";
		$arrastre = $db->query($sql);
		if ($arrastre)
			foreach ($arrastre as $reg) {
				$saldo -= /*$reg['pan_p_expendio']*/$result[$i]['promedio'];
				if ($saldo > 0)
					$dias_atraso++;
				else
					break;
			}
		
		if ($saldo_ini || $saldo_fin || $_GET['idagven'] > 0) {
			if (isset($_GET['aumento']) && $saldo_fin[0]['rezago'] - $saldo_ini[0]['rezago_anterior'] <= 0)
				continue;
			
			$tpl->newBlock($_GET['idagven'] > 0 ? "fila_ag" : "fila");
			if ($_GET['idagven'] > 0) {
				$tpl->assign('num_cia', $result[$i]['num_cia']);
				$tpl->assign('nombre', $result[$i]['nombre_corto']);
			}
			$tpl->assign("num_exp",$result[$i]['num_expendio']);
			$tpl->assign("nombre_exp",$result[$i]['nombre_expendio']);
			
			$ini = $saldo_ini ? $saldo_ini[0]['rezago_anterior'] : 0;
			$fin = $saldo_fin ? $saldo_fin[0]['rezago'] : 0;
			
			// Obtener saldo inicial
			$tpl->assign("saldo_anterior", $saldo_ini[0]['rezago_anterior'] > 0 ? number_format($saldo_ini[0]['rezago_anterior'], 2, ".", ",") : "&nbsp;");
			$tpl->assign("precio_venta",($result[$i]['pan_p_venta'] > 0)?number_format($result[$i]['pan_p_venta'],2,".",","):"&nbsp;");
			$tpl->assign("precio_exp",($result[$i]['pan_p_expendio'] > 0)?number_format($result[$i]['pan_p_expendio'],2,".",","):"&nbsp;");
			$tpl->assign("diferencia",(round($result[$i]['pan_p_venta']-$result[$i]['pan_p_expendio'],2) != 0)?number_format($result[$i]['pan_p_venta']-$result[$i]['pan_p_expendio'],2,".",","):"&nbsp;");
			$tpl->assign("porc",($result[$i]['porc_ganancia'] > 0)?number_format($result[$i]['porc_ganancia'],2,".",","):"&nbsp;");
			$tpl->assign("abono",($result[$i]['abono'] > 0)?number_format($result[$i]['abono'],2,".",","):"&nbsp;");
			$tpl->assign("devolucion",($result[$i]['devolucion'] > 0)?number_format($result[$i]['devolucion'],2,".",","):"&nbsp;");
			$tpl->assign("saldo_actual",($saldo_fin[0]['rezago'] > 0)?number_format($saldo_fin[0]['rezago'],2,".",","):"&nbsp;");
			$tpl->assign("dif_saldo", $saldo_fin[0]['rezago'] - $saldo_ini[0]['rezago_anterior'] != 0 ? "<span style=\"color:#" . ($saldo_fin[0]['rezago'] - $saldo_ini[0]['rezago_anterior'] < 0 ? "CC0000" : "0000CC") . "\">" . number_format($saldo_fin[0]['rezago'] - $saldo_ini[0]['rezago_anterior'], 2, ".", ",") . ($saldo_fin[0]['rezago'] - $saldo_ini[0]['rezago_anterior'] != 0 ? ($saldo_fin[0]['rezago'] - $saldo_ini[0]['rezago_anterior'] > 0 ? " SUBIO" : " BAJO") : "") . "</span>" : "&nbsp;");
			$tpl->assign("prom", $result[$i]['abono'] > 0 ? number_format($result[$i]['abono'] / $fecha[1], 2, ".", ",") : "&nbsp;");
			// [24-Jul-2007] Dias de rezago
			$tpl->assign('dias', $dias_atraso > 0 ? ($dias_atraso < 90 ? '<a href="#" onclick="detalleRezago(' . $num_cia . ',' . $result[$i]['num_expendio'] . ',\'' . $result[$i]['nombre_expendio'] . '\',\'' . $_REQUEST['fecha1'] . '\',\'' . $_REQUEST['fecha2'] . '\', ' . $dias_atraso . ')">' . $dias_atraso . '</a>' : '<a href="#" onclick="detalleRezago(' . $num_cia . ',' . $result[$i]['num_expendio'] . ',\'' . $result[$i]['nombre_expendio'] . '\',\'' . $_REQUEST['fecha1'] . '\',\'' . $_REQUEST['fecha2'] . '\', ' . $dias_atraso . ')"><span style="color:red;">EXCEDIDO<span> <span style="color:blue;">[' . $dias_atraso . ']</span></a>') : '&nbsp;');
			
			if ($_GET['idagven'] > 0) {
				$cia_pan_p_venta += $result[$i]['pan_p_venta'];
				$cia_pan_p_expendio += $result[$i]['pan_p_expendio'];
				$cia_abono += $result[$i]['abono'];
				$cia_devolucion += $result[$i]['devolucion'];
				$cia_rezago_anterior += $saldo_ini[0]['rezago_anterior'];
				$cia_rezago_actual += $saldo_fin[0]['rezago'];
				$cia_porc += $result[$i]['porc_ganancia'];
				$cia_diferencia += $result[$i]['pan_p_venta']-$result[$i]['pan_p_expendio'];
				
				$tpl->assign("cia_ag.saldo_anterior",number_format($cia_rezago_anterior,2,".",","));
				$tpl->assign("cia_ag.precio_venta",number_format($cia_pan_p_venta,2,".",","));
				$tpl->assign("cia_ag.precio_exp",number_format($cia_pan_p_expendio,2,".",","));
				$tpl->assign("cia_ag.diferencia",number_format($cia_diferencia,2,".",","));
				$tpl->assign("cia_ag.abono",number_format($cia_abono,2,".",","));
				$tpl->assign("cia_ag.devolucion",number_format($cia_devolucion,2,".",","));
				$tpl->assign("cia_ag.saldo_actual",number_format($cia_rezago_actual,2,".",","));
			}
			
			$pan_p_venta += $result[$i]['pan_p_venta'];
			$pan_p_expendio += $result[$i]['pan_p_expendio'];
			$abono += $result[$i]['abono'];
			$devolucion += $result[$i]['devolucion'];
			$rezago_anterior += $saldo_ini[0]['rezago_anterior'];
			$rezago_actual += $saldo_fin[0]['rezago'];
			$porc += $result[$i]['porc_ganancia'];
			$diferencia += $result[$i]['pan_p_venta']-$result[$i]['pan_p_expendio'];
		}
	}
	
	$tpl->assign(($_GET['idagven'] > 0 ? 'agentes' : 'saldos') . ".saldo_anterior",number_format($rezago_anterior,2,".",","));
	$tpl->assign(($_GET['idagven'] > 0 ? 'agentes' : 'saldos') . ".precio_venta",number_format($pan_p_venta,2,".",","));
	$tpl->assign(($_GET['idagven'] > 0 ? 'agentes' : 'saldos') . ".precio_exp",number_format($pan_p_expendio,2,".",","));
	$tpl->assign(($_GET['idagven'] > 0 ? 'agentes' : 'saldos') . ".diferencia",number_format($diferencia,2,".",","));
	$tpl->assign(($_GET['idagven'] > 0 ? 'agentes' : 'saldos') . ".abono",number_format($abono,2,".",","));
	$tpl->assign(($_GET['idagven'] > 0 ? 'agentes' : 'saldos') . ".devolucion",number_format($devolucion,2,".",","));
	$tpl->assign(($_GET['idagven'] > 0 ? 'agentes' : 'saldos') . ".saldo_actual",number_format($rezago_actual,2,".",","));

	// Imprimir el resultado
	$tpl->printToScreen();
}
// --------------------------------------------------------------------------------------------------------

// ---------------------------------- Listado acumulado -----------------------------------------------------
if ($_GET['tipo'] == 2) {
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha1'], $fecha);
	
	$fecha1 = $_GET['fecha1'];
	$fecha2 = $_GET['fecha2'];
	
	// Obtener registros
	$sql = "SELECT fecha, SUM(pan_p_venta) AS precio_venta, SUM(pan_p_expendio) AS precio_expendio, AVG(porc_ganancia) AS porc, SUM(abono) AS abono FROM mov_expendios";
	$sql .= " WHERE num_cia = $_GET[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY fecha ORDER BY fecha ASC";
	$result = $db->query($sql);
	
	// Si no hay resultados
	if (!$result) {
		header("location: ./pan_exp_con_v2.php?codigo_error=1");
		die;
	}
	
	// Crear bloque de consulta
	$tpl->newBlock("movimientos");
	
	// Asignar valores a encabezado
	$tpl->assign("num_cia", $_GET['num_cia']);			// Asignar número de compañía
	$nombre_cia = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$tpl->assign("nombre_cia", $nombre_cia[0]['nombre']);	// Asignar nombre de compañía
	$tpl->assign("dia", $fecha[1]);						// Asignar día
	$tpl->assign("anio", $fecha[3]);					// Asignar año
	$tpl->assign("mes", mes_escrito($fecha[2]));		// Asignar mes
	
	$precio_venta = 0;
	$precio_expendio = 0;
	$diferencia = 0;
	$porc = 0;
	$abono = 0;
	
	for ($i=0; $i<count($result); $i++) {
		$tpl->newBlock("dia");
		ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $result[$i]['fecha'],$dia);
		
		$tpl->assign("dia", (int)$dia[1]);
		$tpl->assign("precio_venta", number_format($result[$i]['precio_venta'], 2, ".", ","));
		$tpl->assign("precio_expendio", number_format($result[$i]['precio_expendio'], 2, ".", ","));
		$tpl->assign("diferencia", number_format($result[$i]['precio_venta'] - $result[$i]['precio_expendio'], 2, ".", ","));
		$tpl->assign("porc", number_format($result[$i]['porc'], 0, "", ""));
		$tpl->assign("abono", number_format($result[$i]['abono'], 2, ".", ","));
		
		$precio_venta += $result[$i]['precio_venta'];
		$precio_expendio += $result[$i]['precio_expendio'];
		$diferencia += $result[$i]['precio_venta'] - $result[$i]['precio_expendio'];
		$porc += $result[$i]['porc'];
		$abono += $result[$i]['abono'];
	}
	$tpl->gotoBlock("movimientos");
	// Totales
	$tpl->assign("precio_venta", number_format($precio_venta, 2, ".", ","));
	$tpl->assign("precio_expendio", number_format($precio_expendio, 2, ".", ","));
	$tpl->assign("diferencia", number_format($diferencia, 2, ".", ","));
	$tpl->assign("porc", number_format(($precio_venta - $precio_expendio) * 100 / $precio_venta, 2, ".", ""));
	$tpl->assign("abono", number_format($abono, 2, ".", ","));
	// Promedios
	$tpl->assign("prom_venta", number_format($precio_venta / $i, 2, ".", ","));
	$tpl->assign("prom_expendio", number_format($precio_expendio / $i, 2, ".", ","));
	$tpl->assign("prom_diferencia", number_format(($diferencia) / $i, 2, ".", ","));
	$tpl->assign("prom_porc", number_format(($precio_venta - $precio_expendio) * 100 / $precio_venta, 2, ".", ""));
	$tpl->assign("prom_abono", number_format($abono / $i, 2, ".", ","));
	
	$tpl->printToScreen();
}

if ($_GET['tipo'] == 3) {
	function toInt($value) {
		return intval($value, 10);
	}
	
	list($dia1, $mes1, $anio1) = array_map('toInt', explode('/', $_REQUEST['fecha1']));
	list($dia2, $mes2, $anio2) = array_map('toInt', explode('/', $_REQUEST['fecha2']));
	
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $mes1, $dia1, $anio1));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $mes2, $dia2, $anio2));
	
	$condiciones = array();
	
	$condiciones[] = 'num_cia <= 300';
	
	if (isset($_REQUEST['num_cia']) && $_REQUEST['num_cia'] > 0) {
		$condiciones[] = 'num_cia = ' . $_REQUEST['num_cia'];
	}
	
	if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
		$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
	}
	
	$sql = '
		SELECT
			*
		FROM
			(
				SELECT
					num_cia,
					nombre_corto
						AS nombre_cia,
					COALESCE((
						SELECT
							SUM(rezago_anterior)
						FROM
							mov_expendios
						WHERE
							num_cia = cc.num_cia
							AND fecha = \'' . $fecha1 . '\'
					), 0)
						AS rezago_ini,
					COALESCE((
						SELECT
							SUM(rezago)
						FROM
							mov_expendios
						WHERE
							num_cia = cc.num_cia
							AND fecha = \'' . $fecha2 . '\'
					), 0)
						AS rezago_fin
				FROM
					catalogo_companias cc
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia
			) result
		WHERE
			rezago_ini <> 0
			OR rezago_fin <> 0
	';
	
	$result = $db->query($sql);
	
	// Si no hay resultados
	if (!$result) {
		header("location: pan_exp_con_v2.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock('rezagos');
	
	$tpl->assign('dia1', $dia1);
	$tpl->assign('mes1', mes_escrito($mes1));
	$tpl->assign('anio1', $anio1);
	
	$tpl->assign('dia2', $dia2);
	$tpl->assign('mes2', mes_escrito($mes2));
	$tpl->assign('anio2', $anio2);
	
	$rezago_ini = 0;
	$rezago_fin = 0;
	$total_dif = 0;
	
	foreach ($result as $rec) {
		$tpl->newBlock('fila_rezago');
		
		$tpl->assign('num_cia', $rec['num_cia']);
		$tpl->assign('nombre_cia', $rec['nombre_cia']);
		$tpl->assign('rezago_ini', $rec['rezago_ini'] != 0 ? number_format($rec['rezago_ini'], 2) : '&nbsp;');
		$tpl->assign('rezago_fin', $rec['rezago_fin'] != 0 ? number_format($rec['rezago_fin'], 2) : '&nbsp;');
		
		$dif = $rec['rezago_ini'] != 0 ? $rec['rezago_fin'] - $rec['rezago_ini'] : 0;
		
		$tpl->assign('dif', $dif != 0 ? number_format($dif, 2) : '&nbsp;');
		$tpl->assign('color_dif', $dif > 0 ? 'C00' : '00C');
		
		$var = $rec['rezago_ini'] != 0 ? ($rec['rezago_fin'] * 100 / $rec['rezago_ini']) - 100 : 0;
		
		$tpl->assign('var', $var != 0 ? ($var > 0 ? 'SUBIO ' : 'BAJO ') . number_format(abs($var), 2) . '%' : '&nbsp;');
		
		$rezago_ini += $rec['rezago_ini'];
		$rezago_fin += $rec['rezago_fin'];
		
		$total_dif += $rezago_fin - $rezago_ini;
	}
	
	$tpl->assign('rezagos.rezago_ini', number_format($rezago_ini, 2));
	$tpl->assign('rezagos.rezago_fin', number_format($rezago_fin, 2));
	$tpl->assign('rezagos.dif', number_format($total_dif, 2));
	
	$tpl->printToScreen();
}
?>