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
$tpl->assignInclude("body","./plantillas/fac/fac_con_anu_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['codmp'])) {
	function filter($value) {
		return $value > 0;
	}

	$fecha1 = date('d/m/Y', mktime(0, 0, 0, 1, 1, $_GET['anio']));
	$fecha2 = $_GET['anio'] < date('Y') ? date('d/m/Y', mktime(0, 0, 0, 12, 31, $_GET['anio'])) : ($_GET['tipo'] == 1 ? date('d/m/Y', mktime(0, 0, 0, date('n'), 0, $_REQUEST['anio'])) : date('d/m/Y'));

	$condiciones = array();

	$condiciones[] = 'codmp IN (' . implode(', ', array_filter($_GET['codmp'], 'filter')) . ')';

	$condiciones[] = 'tipo_mov = TRUE';

	$condiciones[] = 'fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';

	if ($_GET['admin'] > 0) {
		$condiciones[] = 'idadministrador = ' . $_GET['admin'];
	}

	$sql = '
		SELECT
			codmp,
			cmp.nombre
				AS nombre_mp,
			num_cia,
			cc.nombre_corto,
			EXTRACT(month FROM fecha)
				AS mes,
			SUM(
				CASE
					WHEN codmp = 1 THEN
						cantidad / 44
					WHEN codmp IN (3, 4) THEN
						cantidad / 50
					ELSE
						cantidad
				END
			)
				AS consumo
		FROM
			mov_inv_real mov
			LEFT JOIN catalogo_mat_primas cmp
				USING (codmp)
			LEFT JOIN catalogo_companias cc
				USING (num_cia)
		WHERE
			' . implode(' AND ', $condiciones) . '
		GROUP BY
			codmp,
			nombre_mp,
			num_cia,
			nombre_corto,
			mes
		ORDER BY
			codmp,
			num_cia,
			mes
	';
//	$sql = "SELECT num_cia, nombre_corto, mes, consumo FROM consumos_mensuales LEFT JOIN catalogo_companias USING (num_cia) WHERE codmp = $_GET[codmp] AND anio = $_GET[anio]";
//	$sql .= $_GET['anio'] == date('Y') ? ($_GET['tipo'] == 1 ? ' AND mes < ' . date('n') : '') : '';
//	$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '';
//	$sql .= " ORDER BY num_cia, mes";
	$result = $db->query($sql);

	if (!$result) {
		header("location: ./fac_con_anu_v2.php?codigo_error=1");
		die;
	}

	//$nombre = $db->query("SELECT nombre FROM catalogo_mat_primas WHERE codmp = $_GET[codmp]");
	$numfilas_x_hoja = 48;
	$numfilas = $numfilas_x_hoja;
	$num_meses = $_GET['anio'] < date("Y") ? 12 : ($_GET['tipo'] == 1 ? date('n') - 1 : date('n'));

	$codmp = NULL;
	$num_cia = NULL;

	foreach ($result as $reg) {
		if ($codmp != $reg['codmp']) {
			if ($num_cia != NULL) {
				$tpl->assign("total", number_format($total, 2, ".", ","));
				$tpl->assign("prom", number_format($total / $num_meses, 2, ".", ","));
				$promedio_total += $total / $num_meses;

				$tpl->newBlock("totales");
				foreach ($total_mes as $m => $t)
					$tpl->assign($m, $t != 0 ? number_format($t, 2, ".", ",") : "");
				$tpl->assign("total", number_format(array_sum($total_mes), 2, ".", ","));
				$tpl->assign("prom", number_format($promedio_total, 2, ".", ","));

				$tpl->assign("listado.salto", "<br style=\"page-break-after:always;\">");
			}

			$codmp = $reg['codmp'];

			$tpl->newBlock("listado");
			$tpl->assign("anio", $_GET['anio']);
			$tpl->assign("comdp", $reg['codmp']);
			$tpl->assign("nombre", $reg['nombre_mp']);

			$total_mes = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0);
			$promedio_total = 0;
			$num_cia = NULL;

			$numfilas = 0;
		}

		if ($num_cia != $reg['num_cia']) {
			if ($num_cia != NULL) {
				$tpl->assign("total", number_format($total, 2, ".", ","));
				$tpl->assign("prom", number_format($total / $num_meses, 2, ".", ","));
				$promedio_total += $total / $num_meses;
			}

			if ($numfilas == $numfilas_x_hoja) {
				if ($num_cia != NULL)
					$tpl->assign("listado.salto", "<br style=\"page-break-after:always;\">");

				$tpl->newBlock("listado");
				$tpl->assign("anio", $_GET['anio']);
				$tpl->assign("comdp", $_GET['codmp']);
				$tpl->assign("nombre", $reg['nombre_mp']);

				$numfilas = 0;
			}

			$num_cia = $reg['num_cia'];

			$tpl->newBlock("fila");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre", $reg['nombre_corto']);

			$total = 0;
			$numfilas++;
		}

		$consumo = in_array($_GET['codmp'], array(1, 3, 4)) ? ($_GET['codmp'] == 1 ? $reg['consumo'] / 44 : $reg['consumo'] / 50) : $reg['consumo'];
		$tpl->assign($reg['mes'], number_format($consumo, 2, ".", ","));
		$total += $consumo;
		$total_mes[$reg['mes']] += $consumo;
	}
	if ($num_cia != NULL) {
		$tpl->assign("total", number_format($total, 2, ".", ","));
		$tpl->assign("prom", number_format($total / $num_meses, 2, ".", ","));
		$promedio_total += $total / $num_meses;
	}
	$tpl->newBlock("totales");
	foreach ($total_mes as $m => $t)
		$tpl->assign($m, $t != 0 ? number_format($t, 2, ".", ",") : "");
	$tpl->assign("total", number_format(array_sum($total_mes), 2, ".", ","));
	$tpl->assign("prom", number_format($promedio_total, 2, ".", ","));

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
