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
$tpl->assignInclude("body","./plantillas/fac/fac_pro_anu_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_pro'])) {
	$fecha1 = "01/01/$_GET[anio]";
	if ($_GET['anio'] == date('Y')) {
		if ($_REQUEST['periodo'] == 1)
		{
			$sql = '
				SELECT
					MAX(mes)
						AS mes
				FROM
					balances_pan
				WHERE
					anio = ' . $_GET['anio'] . '
			';
			
			$tmp = $db->query($sql);
			
			$fecha2 = date("d/m/Y", mktime(0, 0, 0, $tmp[0]['mes'] + 1, 0, $_GET['anio']));
		}
		else
		{
			$fecha2 = date('d/m/Y');
		}
	}
	else {
		$fecha2 = date("d/m/Y", mktime(0, 0, 0, 12, 31, $_GET['anio']));
	}
	
	if ($_GET['tipo'] == 1) {
		$sql = "
			SELECT
				num_cia,
				nombre_corto,
				extract(month from fecha)
					AS mes,
				sum(total)
					AS compras
			FROM
				facturas
				LEFT JOIN catalogo_companias
					USING (num_cia)
			WHERE
				facturas.num_proveedor = $_GET[num_pro]
				AND fecha BETWEEN '$fecha1' AND '$fecha2'
				" . ($_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '') . "
			GROUP BY
				num_cia,
				nombre_corto,
				mes
			ORDER BY
				num_cia,
				mes
		";
	}
	else if ($_GET['tipo'] == 2) {
		$sql = '
			SELECT
				num_cia,
				nombre_corto,
				EXTRACT(MONTH FROM fecha)
					AS mes,
				SUM(
					CASE
						WHEN codmp = 1 THEN
							contenido * cantidad / 44
						WHEN codmp IN (3, 4) THEN
							contenido * cantidad / 50
						ELSE
							contenido * cantidad
					END
				)
					AS compras
			FROM
				entrada_mp ent
				LEFT JOIN catalogo_companias cc
					USING (num_cia)
			WHERE
				ent.num_proveedor = ' . $_REQUEST['num_pro'] . '
				AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
				AND codmp = ' . $_REQUEST['codmp'] . '
				' . ($_REQUEST['admin'] > 0 ? ' AND idadministrador = ' . $_GET['admin'] : '') . '
			GROUP BY
				num_cia,
				nombre_corto,
				mes
			ORDER BY
				num_cia,
				mes
		';
	}
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./fac_pro_anu_v2.php?codigo_error=1");
		die;
	}
	
	$nombre = $db->query("SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = $_GET[num_pro]");
	$numfilas_x_hoja = 48;
	$numfilas = $numfilas_x_hoja;
	$num_meses = $_GET['anio'] < date("Y") ? 12 : date("n") - 1;
	$total_mes = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0);
	$num_cia = NULL;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			if ($num_cia != NULL) {
				$sql = "
					SELECT
						num_fact,
						fecha_cheque
					FROM
						facturas_pagadas
					WHERE
						num_proveedor = {$_REQUEST['num_pro']}
						AND EXTRACT(YEAR FROM fecha) = {$_REQUEST['anio']}
						AND num_cia = {$reg['num_cia']}
					ORDER BY
						fecha DESC
					LIMIT
						1
				";

				$ultima = $db->query($sql);

				if ($ultima)
				{
					$tpl->assign('fac', htmlentities("FACTURA: {$ultima[0]['num_fact']}<br />FECHA PAGO: {$ultima[0]['fecha_cheque']}"));
				}

				$tpl->assign("total", number_format($total, 2, ".", ","));
				$tpl->assign("prom", number_format($total / $num_meses, 2, ".", ","));
			}
			if ($numfilas == $numfilas_x_hoja) {
				if ($num_cia != NULL)
					$tpl->assign("listado.salto", "<br style=\"page-break-after:always;\">");
				
				$tpl->newBlock("listado");
				$tpl->assign("anio", $_GET['anio']);
				$tpl->assign("num_pro", $_GET['num_pro']);
				$tpl->assign("nombre", $nombre[0]['nombre']);
				
				$numfilas = 0;
			}
			
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock("fila");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre", $reg['nombre_corto']);
			
			$total = 0;
			$numfilas++;
		}
		$tpl->assign($reg['mes'], number_format($reg['compras'], 2, ".", ","));
		$total += $reg['compras'];
		$total_mes[$reg['mes']] += $reg['compras'];
	}
	if ($num_cia != NULL) {
		$sql = "
			SELECT
				num_fact,
				fecha_cheque
			FROM
				facturas_pagadas
			WHERE
				num_proveedor = {$_REQUEST['num_pro']}
				AND EXTRACT(YEAR FROM fecha) = {$_REQUEST['anio']}
				AND num_cia = {$reg['num_cia']}
			ORDER BY
				fecha DESC
			LIMIT
				1
		";

		$ultima = $db->query($sql);

		if ($ultima)
		{
			$tpl->assign('fac', htmlentities("FACTURA: {$ultima[0]['num_fact']}<br />FECHA PAGO: {$ultima[0]['fecha_cheque']}"));
		}

		$tpl->assign("total", number_format($total, 2, ".", ","));
		$tpl->assign("prom", number_format($total / $num_meses, 2, ".", ","));
	}
	$tpl->newBlock("totales");
	foreach ($total_mes as $m => $t) {
		$tpl->assign($m, $t != 0 ? number_format($t, 2, ".", ",") : "");
		$tpl->assign('anio', $_GET['anio']);
		$tpl->assign('num_pro', $_GET['num_pro']);
		$tpl->assign('codmp', $_GET['codmp']);
	}
	$tpl->assign("total", number_format(array_sum($total_mes), 2, ".", ","));
	
	$tpl->printToScreen();
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