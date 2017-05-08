<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ros/ros_com_anu.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$fecha1 = "01/01/$_GET[anio]";
	$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['anio'] < date('Y') ? 12 : date("n"), $_GET['anio'] < date('Y') ? 31 : 0, $_GET['anio']));

	if ($_GET['tipo'] == 1) {
		$sql = "SELECT num_cia, nombre_corto, 1 AS codmp, extract(month from fecha_mov) AS mes, sum(cantidad) AS piezas FROM fact_rosticeria LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha_mov BETWEEN '$fecha1' AND '$fecha2'";
		$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
		$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '';
		$sql .= $_GET['num_pro'] > 0 ? " AND fact_rosticeria.num_proveedor = $_GET[num_pro]" : "";
		$sql .= $_GET['codmp'] > 0 ? " AND codmp = $_GET[codmp]" : " AND codmp IN (160, 600, 700, 573)";
		$sql .= " GROUP BY num_cia, nombre_corto, mes";
		$sql .= " UNION SELECT num_cia, nombre_corto, 1 AS codmp, extract(month from fecha) AS mes, sum(cantidad * contenido) AS piezas FROM entrada_mp LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha BETWEEN '$fecha1' AND '$fecha2'";
		$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
		$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '';
		$sql .= $_GET['num_pro'] > 0 ? " AND entrada_mp.num_proveedor = $_GET[num_pro]" : "";
		$sql .= $_GET['codmp'] > 0 ? " AND codmp = $_GET[codmp]" : " AND codmp IN (160, 600, 700, 573)";
		$sql .= " GROUP BY num_cia, nombre_corto, mes";
		$sql .= " UNION SELECT num_cia, nombre_corto, 1 AS codmp, extract(month from fecha_mov) AS mes, sum(cantidad) AS piezas FROM compra_directa LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha_mov BETWEEN '$fecha1' AND '$fecha2'";
		$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
		$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '';
		$sql .= $_GET['num_pro'] > 0 ? " AND compra_directa.num_proveedor = $_GET[num_pro]" : "";
		$sql .= $_GET['codmp'] > 0 ? " AND codmp = $_GET[codmp]" : " AND codmp IN (160, 600, 700, 573)";
		$sql .= " GROUP BY num_cia, nombre_corto, mes ORDER BY codmp, num_cia, mes";
	}
	else {
		$sql = "SELECT num_cia, nombre_corto, 1 AS codmp, extract(month from fecha) AS mes, sum(cantidad) AS piezas FROM mov_inv_real LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov = 'TRUE'";
		$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
		$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '';
		$sql .= $_GET['codmp'] > 0 ? " AND codmp = $_GET[codmp]" : " AND codmp IN (160, 600, 700, 573)";
		$sql .= " GROUP BY num_cia, nombre_corto, mes ORDER BY codmp, num_cia, mes";
	}
	$result = $db->query($sql);

	if (!$result) {
		header("location: ./ros_com_anu.php?codigo_error=1");
		die;
	}

	$codmp = NULL;
	foreach ($result as $reg) {
		if ($codmp != $reg['codmp']) {
			if ($codmp != NULL) {
				$tpl->gotoBlock("list");
				$meses = 0;
				foreach ($total_mes as $m => $t) {
					$tpl->assign($m, number_format($t));
					if ($t > 0) $meses++;
				}
				$tpl->assign("prom", number_format(round(array_sum($total_mes) / $meses)));
				$tpl->assign("total", number_format(array_sum($total_mes)));
				$tpl->assign("salto", "<br style=\"page-break-after:always;\">");
			}

			$codmp = $reg['codmp'];

			switch ($codmp) {
				case 160: $tamanio = "Normal"; break;
				case 600: $tamanio = "Chico"; break;
				case 700: $tamanio = "Grande"; break;
				default:  $tamanio = '';
			}

			$tpl->newBlock("list");
			$tpl->assign("anio", $_GET['anio']);
			$tpl->assign('tipo', $_GET['tipo'] == 1 ? 'Compras' : 'Ventas');
			$tpl->assign("tamanio", $tamanio);

			$num_cia = NULL;
			$total_mes = array(1 => 0,
								2 => 0,
								3 => 0,
								4 => 0,
								5 => 0,
								6 => 0,
								7 => 0,
								8 => 0,
								9 => 0,
								10 => 0,
								11 => 0,
								12 => 0);
		}
		if ($num_cia != $reg['num_cia']) {
			$num_cia = $reg['num_cia'];

			$tpl->newBlock("fila");
			$tpl->assign("num_cia", $reg['num_cia']);
			$tpl->assign("nombre", $reg['nombre_corto']);

			$total = 0;
		}
		$tpl->assign($reg['mes'], number_format($reg['piezas']));
		$total += $reg['piezas'];
		$total_mes[$reg['mes']] += $reg['piezas'];
		$tpl->assign("prom", number_format(round($total / $reg['mes'])));
		$tpl->assign("total", number_format(round($total)));
	}
	if ($codmp != NULL) {
		$tpl->gotoBlock("list");
		$meses = 0;
		foreach ($total_mes as $m => $t) {
			$tpl->assign($m, number_format($t));
			if ($t > 0) $meses++;
		}
		$tpl->assign("prom", number_format(round(array_sum($total_mes) / $meses)));
		$tpl->assign("total", number_format(array_sum($total_mes)));
	}
	$tpl->newBlock('back');
	$tpl->printToScreen();
	die;
}

$descripcion_error[1] = "No hay resultados";

$tpl->newBlock("datos");
$tpl->assign("anio", date("Y"));

$admins = $db->query('SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores WHERE idadministrador NOT IN (11, 12) ORDER BY admin');
foreach ($admins as $a) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $a['id']);
	$tpl->assign('admin', $a['admin']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message",$descripcion_error[$_GET['codigo_error']]);
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
die;

?>
