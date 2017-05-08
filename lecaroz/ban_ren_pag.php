<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

//if ($_SESSION['iduser'] != 1) die(header('location: ./offline.htm'));

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_ren_pag.tpl");
$tpl->prepare();

if (isset($_GET['id'])) {
	$sql = "SELECT nombre_arrendatario, representante, contacto, telefono FROM catalogo_arrendatarios WHERE id = $_GET[id]";
	$result = $db->query($sql);
	
	$tpl->newBlock('detalle');
	foreach ($result[0] as $k => $v)
		$tpl->assign($k, trim($v) != '' ? trim($v) : '&nbsp;');
	
	die($tpl->printToScreen());
}

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

function buscar($array, $anio, $mes) {
	if (!$array) return FALSE;
	
	foreach ($array as $r)
		if ($r['anio'] == $anio && $r['mes'] == $mes)
			return TRUE;
	
	return FALSE;
}

function buscar_alt($array, $anio, $mes) {
	if (!$array) return FALSE;
	
	foreach ($array as $r)
		if ($r['anio'] == $anio && $r['mes'] == $mes) {
			$estado = NULL;
			switch ($r['tipo']) {
				case 0:
					$estado = 2;
				break;
				case 1:
					$estado = 1;
				break;
				case 2:
					$estado = 3;
				break;
			}
			return $estado;
		}
	
	return FALSE;
}

if (isset($_GET['anio'])) {
	$sql = "
		SELECT
			ca.id,
			ca.num_local,
			nombre_local,
			ca.num_cia,
			cc.nombre_corto,
			giro
		FROM
				catalogo_arrendatarios ca
			LEFT JOIN
				catalogo_locales cl
					ON
						(cl.id = ca.num_local)
			LEFT JOIN
				catalogo_companias cc
					ON
						(cc.num_cia = ca.num_cia)
		WHERE
				ca.status = 1
			AND
				ca.bloque = 2
	";
	$sql .= $_GET['local'] > 0 ? " AND ca.num_local = $_GET[local]" : '';
	$sql .= "
		ORDER BY
			ca.num_cia
	";
	$result = $db->query($sql);
	
	if (!$result) die(header('location: ./ban_ren_pag.php?codigo_error=1'));
	
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, /*10*/7, 1, $_GET['anio'] - 1));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, 12, /*31*/10, $_GET['anio']));
	
	$current_year = date('Y');
	$current_month = date('n');
	$current_day = date('d');
	
	$sql = "
		SELECT
			local,
			extract(month from fecha_renta)
				AS
					mes,
			extract(year from fecha_renta)
				AS
					anio,
			CASE
				WHEN fecha_con IS NOT NULL THEN
					't'
				ELSE
					'f'
			END
				AS
					status
		FROM
			estado_cuenta
		WHERE
				local
					IN
						(
							SELECT
								ca.id
							FROM
									catalogo_arrendatarios ca
								LEFT JOIN
									catalogo_locales cl
										ON
											(cl.id = ca.num_local)
								LEFT JOIN
									catalogo_companias cc
										ON
											(cc.num_cia = ca.num_cia)
							WHERE
									ca.status = 1
								AND
									ca.bloque = 2
						)
			AND
				cod_mov = 2
			AND
				fecha_renta BETWEEN '$fecha1' AND '$fecha2'
		ORDER BY
			fecha_renta
	";
	$ren = $db->query($sql);
	foreach ($ren as $r)
		$rentas[$r['local']][$r['anio']][$r['mes']] = $r['status'] == 't' ? 1 : 10;
	
	$sql = "
		SELECT
			local,
			anio,
			mes,
			tipo
		FROM
			estatus_locales
		WHERE
				local
					IN
						(
							SELECT
								ca.id
							FROM
									catalogo_arrendatarios ca
								LEFT JOIN
									catalogo_locales cl
										ON
											(cl.id = ca.num_local)
								LEFT JOIN
									catalogo_companias cc
										ON
											(cc.num_cia = ca.num_cia)
							WHERE
									ca.status = 1
								AND
									ca.bloque = 2
						)
			AND
				anio IN ($_GET[anio], $_GET[anio] - 1, $_GET[anio] - 2)
			AND
				(local, mes, anio)
					NOT IN
						(
							SELECT
								local,
								extract(month from fecha_renta)
									AS
										mes,
								extract(year from fecha_renta)
									AS
										anio
							FROM
								estado_cuenta
							WHERE
									local
										IN
											(
												SELECT
													ca.id
												FROM
														catalogo_arrendatarios ca
													LEFT JOIN
														catalogo_locales cl
															ON
																(cl.id = ca.num_local)
													LEFT JOIN
														catalogo_companias cc
															ON
																(cc.num_cia = ca.num_cia)
												WHERE
														ca.status = 1
													AND
														ca.bloque = 2
											)
								AND
									cod_mov = 2
								AND
									fecha_renta BETWEEN '$fecha1' AND '$fecha2'
						)
		ORDER BY
			local,
			anio,
			mes
	";
	$est = $db->query($sql);
	
	// [22-Oct-2008] Ordenar estados
	$estados = array();
	$ultimo_estado = array();
	foreach ($est as $e) {
		switch ($e['tipo']) {
			case 0:
				$estado = 2;
			break;
			case 1:
				$estado = 1;
			break;
			case 2:
				$estado = 0;
			break;
		}
		$estados[$e['local']][$e['anio']][$e['mes']] = $estado;
		$ultimo_estado[$e['local']] = $estado;
	}
	
	$max_filas = 45;
	$num_filas = $max_filas;
	foreach ($result as $reg) {
		if ($num_filas >= $max_filas) {
			$num_filas = 1;
			
			$tpl->newBlock('especial');
			$tpl->assign('anio', $_GET['anio']);
		}
		
		$months = array();
		$ok = TRUE;
		$pen = FALSE;
		$last = NULL;
		for ($y = $_GET['anio'] - 1; $y <= $_GET['anio']; $y++) {
			for ($m = $y == $_GET['anio'] - 1 ? /*10*/7 : 1; $m <= 12; $m++) {
				if (isset($rentas[$reg['id']][$y][$m])) {
					$months[$y][$m] = /*1*/$rentas[$reg['id']][$y][$m];
					$ok = TRUE;
					// Poner los demas meses como pendientes
					$last = 2;
				}
				else if (isset($estados[$reg['id']][$y][$m])) {
					$months[$y][$m] = $estados[$reg['id']][$y][$m];
					$ok = $estados[$reg['id']][$y][$m] == 1 ? TRUE : FALSE;
					$last = $r;
				}
				else if ($last == NULL && isset($ultimo_estado[$reg['id']])) {
					$months[$y][$m] = $ultimo_estado[$reg['id']];
					$ok = $ultimo_estado[$reg['id']] == 1 ? TRUE : FALSE;
					$last = $months[$y][$m];
					
					if ($last == 0 && ($y < $_GET['anio'] || ($y == $_GET['anio'] && $y < $current_year) || ($y == $current_year && ($m < $current_month || ($m == $current_month && $current_day > 15)))))
						$pen = TRUE;
				}
				else if (!$ok) {
					$months[$y][$m] = $last;
				}
				else {
					$months[$y][$m] = 0;
					if ($y < $_GET['anio'] || ($y == $_GET['anio'] && $y < $current_year) || ($y == $current_year && ($m < $current_month || ($m == $current_month && $current_day > 15))))
						$pen = TRUE;
				}
			}
		}
		
		if (isset($_GET['pen']) && !$pen)
			continue;
		
		//$num_cia = $reg['num_cia'];
		
		$tpl->newBlock('fila_esp');
		$tpl->assign('id', $reg['id']);
		$tpl->assign('num', $reg['num_cia']);
		$tpl->assign('local', $reg['nombre_corto']);
		$tpl->assign('arr', $reg['num_local']);
		$tpl->assign('nombre', $reg['nombre_local']);
		$tpl->assign('giro', trim($reg['giro']) != '' ? trim($reg['giro']) : '&nbsp;');
		$tpl->assign('ant', substr($_GET['anio'], 2, 2));
		
		foreach ($months as $y => $m)
			foreach ($m as $i => $s)
				$tpl->assign(($y < $_GET['anio'] ? 'mes_ant_' : 'mes') . $i, $s == 0 ? '&nbsp;' : ($s == 1 ? '<img src="./imagenes/negro.GIF" />' : ($s == 10 ? '<img src="./imagenes/azul.GIF" />' : '<span style="color:#C00; font-weight:bold;">BAJA</span>')));
		
		$num_filas++;
	}
	
	$tpl->newBlock('scripts');
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');
$tpl->assign('anio', date('Y'));

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>