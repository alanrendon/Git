<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "Ya se han capturado las reservas";

if (isset($_POST['num_cia'])) {
	$num_cia = $_POST['num_cia'];
	$anio = $_POST['anio'];
	$cod_reserva = $_POST['cod_reserva'];
	
	$sql = "";
	foreach ($_POST['importe'] as $i => $imp) {
		$importe = floatval(str_replace(",", "", $imp));
		$mes = $i + 1;
		$fecha = "01/$mes/$anio";
		$sql .= "UPDATE reservas_cias SET importe = $importe WHERE num_cia = $num_cia AND fecha = '$fecha' AND cod_reserva = $cod_reserva;\n";
	}
	$db->query($sql);
	
	header("location: ./bal_mod_res.php");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_mod_res.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$sql = "SELECT extract(month from fecha) AS mes, importe FROM reservas_cias WHERE num_cia = $_GET[num_cia] AND anio = $_GET[anio] AND cod_reserva = $_GET[cod_reserva]";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./bal_mod_res.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("reservas");
	$tpl->assign("num_cia", $_GET['num_cia']);
	$tmp = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$tpl->assign("nombre_cia", $tmp[0]['nombre_corto']);
	$tpl->assign("anio", $_GET['anio']);
	$tpl->assign("cod_reserva", $_GET['cod_reserva']);
	$tmp = $db->query("SELECT descripcion FROM catalogo_reservas WHERE tipo_res = $_GET[cod_reserva]");
	$tpl->assign("nombre_reserva", $tmp[0]['descripcion']);
	
	$tmp = $db->query("SELECT mes FROM balances_" . ($_GET['num_cia'] <= 300 ? "pan" : "ros") . " WHERE num_cia = $_GET[num_cia] AND anio = $_GET[anio] ORDER BY mes DESC LIMIT 1");
	$mes = $tmp ? $tmp[0]['mes'] : date("n", mktime(0, 0, 0, date("n"), 0, date("Y")));
	
	$total = 0;
	$total_pagado = 0;
	foreach ($result as $reg) {
		$tpl->assign("checked" . $reg['mes'], $reg['mes'] != 12 ? ($reg['mes'] < $mes + 1 && $reg['importe'] > 0 ? " disabled" : ($reg['mes'] == $mes + 1 && $reg['importe'] > 0 ? " checked" : " checked disabled")) : " disabled");
		$tpl->assign("importe" . $reg['mes'], number_format($reg['importe'], 2, ".", ","));
		$total += $reg['mes'] <= $mes + 1 ? $reg['importe'] : 0;
	}
	
	if ($_GET['cod_reserva'] == 4) {
		$fecha1 = "01/01/$_GET[anio]";
		$fecha2 = "31/12/$_GET[anio]";
		$fecha1_ant = "01/01/" . ($_GET['anio'] - 1);
		$fecha2_ant = "31/12/" . ($_GET['anio'] - 1);
		
		if ($db->query("SELECT id FROM catalogo_filiales_imss WHERE num_cia = $_GET[num_cia]")) {
			$sql = '
				SELECT	
					anio,
					mes,
					importe
				FROM
					pagos_imss
				WHERE
						num_cia = ' . $_GET['num_cia'] . '
					AND
						(\'01/\' || mes || \'/\' || anio)::date
							BETWEEN
									\'' . $fecha1 . '\'
								AND
									\'' . $fecha2 . '\'::date + interval \'1 month\'
				ORDER BY
					anio,
					mes';
			
			/*$sql2 = '
				SELECT
					AVG(importe)
						AS importe
				FROM
					pagos_imss
				WHERE
						num_cia = ' . $_GET['num_cia'] . '
					AND
						(\'01/\' || mes || \'/\' || anio)::date
							BETWEEN
									\'' . $fecha1_ant . '\'
								AND
									\'' . $fecha2_ant . '\'::date + interval \'1 month\'
				ORDER BY
					anio,
					mes';*/
		}
		else {
			$sql = '
				SELECT
					extract(year from fecha)
						AS
							anio,
					extract(month from fecha)
						AS
							mes,
					sum(importe)
						AS
							importe
				FROM
					cheques
				WHERE
						num_cia = ' . $_GET['num_cia'] . '
					AND
						fecha
							BETWEEN
									\'' . $fecha1 . '\'
								AND
									\'' . $fecha2 . '\'::date + interval \'1 month\'
					AND
						codgastos = 141
					AND
						fecha_cancelacion IS NULL
					AND
						importe > 0
				GROUP BY
					extract(year from fecha),
					extract(month from fecha)
				ORDER BY
					extract(year from fecha),
					extract(month from fecha)';
			
			/*$sql2 = '
				SELECT
					AVG(importe)
						AS
							importe
				FROM
					cheques
				WHERE
						num_cia = ' . $_GET['num_cia'] . '
					AND
						fecha
							BETWEEN
									\'' . $fecha1_ant . '\'
								AND
									\'' . $fecha2_ant . '\'::date + interval \'1 month\'
					AND
						codgastos = 141
					AND
						fecha_cancelacion IS NULL
					AND
						importe > 0';*/
		}
		$result = $db->query($sql);
		//$result2 = $db->query($sql2);
		
		if ($result/* && !isset($_REQUEST['por_promedio'])*/) {
			$pagado = array();
			foreach ($result as $reg)
				$pagado[$reg['anio'] == $_GET['anio'] ? $reg['mes'] : 13] = $reg['importe'];
			
			for ($m = 1; $m <= $mes + 1; $m++) {
				$tpl->assign("pagado" . $m, isset($pagado[$m]) ? number_format($pagado[$m], 2, ".", ",") : "");
				$total_pagado += isset($pagado[$m]) ? $pagado[$m] : 0;
				$prom = $total_pagado / $m;
				$tpl->assign("promedio" . $m, number_format($prom, 2, ".", ","));
			}
			
			$tpl->assign("total_pagado", number_format($total_pagado, 2, ".", ","));
			
			/*if ($result2[0]['importe'] > 0) {
				$tpl->assign('promedio_ant', $result2[0]['importe']);
			} else {
				$tpl->assign('promedio_ant', '0');
			}*/
		} /*else if ($result && isset($_REQUEST['por_promedio'])) {
			$promedio_ant = 0;
			if ($result2[0]['importe'] > 0) {
				$tpl->assign('promedio_ant', $result2[0]['importe']);
				$promedio_ant = $result2[0]['importe'];
			} else {
				$tpl->assign('promedio_ant', '0');
			}
			
			for ($m = 1; $m <= $mes + 1; $m++) {
				if ($m == 1) {
					$prom = $promedio_ant;
				} else {
					$prom = 
				}
				
				$tpl->assign("pagado" . $m, isset($pagado[$m]) ? number_format($pagado[$m], 2, ".", ",") : "");
				$total_pagado += isset($pagado[$m]) ? $pagado[$m] : 0;
				$prom = $total_pagado / $m;
				$tpl->assign("promedio" . $m, number_format($prom, 2, ".", ","));
			}
			
			
			
		}*/
		
		
		
		$sql = '
			SELECT
				mes,
				emp_afi
			FROM
				balances_pan
			WHERE
					num_cia = ' . $_GET['num_cia'] . '
				AND
					anio = ' . $_GET['anio'] . '
			ORDER BY
				mes
		';
		$result = $db->query($sql);
		
		$tpl->newBlock('costo_emp');
		
		$sql = '
			SELECT
				anio,
				mes,
				sum(importe)
					AS
						importe
			FROM
				(
						SELECT
							anio,
							mes,
							importe
						FROM
								infonavit
									i
							LEFT JOIN
								catalogo_trabajadores
									ct
										ON
											(
												ct.id = i.id_emp
											)
						WHERE
								num_cia = ' . $_GET['num_cia'] . '
							AND
								anio = ' . $_GET['anio'] . '
					
					UNION
					
						SELECT
							anio,
							mes,
							importe
						FROM
							infonavit_pendientes
						WHERE
								num_cia = ' . $_GET['num_cia'] . '
							AND
								anio = ' . $_GET['anio'] . '
							AND
								status = 0
				)
					result
			GROUP BY
				anio,
				mes
			ORDER BY
				anio,
				mes
		';
		$inf = $db->query($sql);
		
		$infonavit = array();
		if ($inf) {
			foreach ($inf as $r) {
				$tpl->assign('infonavit' . $r['mes'], number_format($r['importe'], 2, '.', ','));
				
				$infonavit[$r['mes']] = $r['importe'];
			}
		}
		
		if ($result) {
			$emp = 0;
			$pag = 0;
			$inf = 0;
			$costo_emp = 0;
			foreach ($result as $r) {
				$tpl->assign('empleados' . $r['mes'], $r['emp_afi']);
				$emp += $r['emp_afi'];
				
				$inf += isset($infonavit[$r['mes']]) ? $infonavit[$r['mes']] : 0;
				
				$pag += isset($pagado[$r['mes'] + 1]) ? $pagado[$r['mes'] + 1] : 0;
				
				$tpl->assign('costo_emp' . $r['mes'], number_format($costo_emp, 2, '.', ','));
				
				if ($r['mes'] % 2 == 0) {
					if (isset($pagado[$r['mes'] + 1])) {
						$costo_emp = round(($pag - $inf) / $emp, 2);
						
						$tpl->assign('costo_emp' . ($r['mes'] - 1), number_format($costo_emp, 2, '.', ','));
						$tpl->assign('costo_emp' . $r['mes'], number_format($costo_emp, 2, '.', ','));
					}
					
					$emp = 0;
					$pag = 0;
					$inf = 0;
				}
			}
		}
		
		$sql = '
			SELECT
				mes,
				prima_riesgo_trabajo
					AS
						prima
			FROM
				balances_pan
			WHERE
					num_cia = ' . $_GET['num_cia'] . '
				AND
					anio = ' . $_GET['anio'] . '
			ORDER BY
				mes
		';
		$result = $db->query($sql);
		
		if ($result) {
			foreach ($result as $r) {
				$tpl->assign('prima' . $r['mes'], $r['prima'] > 0 ? number_format($r['prima'], 5, '.', ',') : '');
			}
		}
		
		$tpl->gotoBlock('reservas');
	} else if ($_GET['cod_reserva'] == 5) {
		$fecha1 = "01/01/$_GET[anio]";
		$fecha2 = "31/12/$_GET[anio]";

		$sql = '
			SELECT
				extract(year from fecha)
					AS anio,
				extract(month from fecha)
					AS mes,
				sum(importe)
					AS importe
			FROM
				cheques
			WHERE
				num_cia = ' . $_GET['num_cia'] . '
				AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'::date + interval \'1 month\'
				AND codgastos = 84
				AND fecha_cancelacion IS NULL
				AND importe > 0
			GROUP BY
				extract(year from fecha),
				extract(month from fecha)
			ORDER BY
				extract(year from fecha),
				extract(month from fecha)
		';

		$result = $db->query($sql);

		if ($result) {
			$pagado = array();

			foreach ($result as $reg) {
				$pagado[$reg['anio'] == $_GET['anio'] ? $reg['mes'] : 13] = $reg['importe'];
			}
			
			for ($m = 1; $m <= $mes + 1; $m++) {
				$tpl->assign("pagado" . $m, isset($pagado[$m]) ? number_format($pagado[$m], 2, ".", ",") : "");
				$total_pagado += isset($pagado[$m]) ? $pagado[$m] : 0;
				$prom = $total_pagado / $m;
				$tpl->assign("promedio" . $m, number_format($prom, 2, ".", ","));
			}
			
			$tpl->assign("total_pagado", number_format($total_pagado, 2, ".", ","));
		}
	}

	$tpl->assign("total_reserva", number_format($total, 2, ".", ","));
	$tpl->assign("gran_total", number_format($total - $total_pagado, 2, ".", ","));
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");
$tpl->assign("anio", date("Y"));

$cods = $db->query("SELECT tipo_res, descripcion FROM catalogo_reservas ORDER BY tipo_res");
foreach ($cods as $cod) {
	$tpl->newBlock("cod");
	$tpl->assign("cod", $cod['tipo_res']);
	$tpl->assign("nombre", $cod['descripcion']);
	if ($cod['tipo_res'] == 4)
		$tpl->assign('selected', ' selected');
}

if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>