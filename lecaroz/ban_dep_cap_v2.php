<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] == 1) die("Pantalla no disponible...");

$users = array(28, 29, 30, 31);

$descripcion_error[1] = "El catálogo de movimientos bancarios esta vacio";
$descripcion_error[2] = "No capturo ningun movimiento";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_dep_cap_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Función de comparacion para ordenar los datos
function cmp($a, $b) {
	// Descomponer fecha
	// ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{1,2})", $a['fecha'], $fecha_a);
	// ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{1,2})", $b['fecha'], $fecha_b);
	preg_match("/([0-9]{1,2})/([0-9]{1,2})/([0-9]{1,2})/", $a['fecha'], $fecha_a);
	preg_match("/([0-9]{1,2})/([0-9]{1,2})/([0-9]{1,2})/", $b['fecha'], $fecha_b);

	// Timestamp para comparacion
	$ts_a = mktime(0, 0, 0, $fecha_a[2], $fecha_a[1], $fecha_a[3]);
	$ts_b = mktime(0, 0, 0, $fecha_b[2], $fecha_b[1], $fecha_b[3]);

	// Si las compañías son iguales
	if ($a['num_cia'] == $b['num_cia']) {
		if ($ts_a == $ts_b) {
			if ($a['importe'] == $b['importe'])
				return 0;
			else
				return $a['importe'] < $b['importe'] ? -1 : 1;
		}
		else
			return $ts_a < $ts_b ? -1 : 1;
	}
	else
		return $a['num_cia'] < $b['num_cia'] ? -1 : 1;
}

if (isset($_POST['cuenta'])) {
	$sql = "";

	$tabla = $_POST['tipo_mov'] == "FALSE" ? "depositos" : "retiros";
	$catMovBan = $_POST['cuenta'] == 1 ? "catalogo_mov_bancos" : "catalogo_mov_santander";
	$codigos = $db->query("SELECT cod_mov, descripcion FROM $catMovBan WHERE tipo_mov = '$_POST[tipo_mov]' GROUP BY cod_mov, descripcion ORDER BY cod_mov");

	function buscar_mov($codigos, $cod_mov) {
		for ($i = 0; $i < count($codigos); $i++)
			if ($cod_mov == $codigos[$i]['cod_mov'])
				return $codigos[$i]['descripcion'];

		return FALSE;
	}

	function buscar_cia($cias, $num_cia) {
		for ($i = 0; $i < count($cias); $i++)
			if ($num_cia == $cias[$i]['num_cia'])
				return $cias[$i];

		return FALSE;
	}

	$index = 0;
	for ($i = 0; $i < count($_POST['num_cia']); $i++)
		if ($_POST['num_cia'][$i] > 0 && $_POST['fecha'][$i] != "" && $_POST['importe'][$i] > 0) {
			$data[$index]['num_cia'] = $_POST['num_cia'][$i];
			$data[$index]['cod_mov'] = $_POST['cod_mov'][$i];
			$data[$index]['fecha'] = $_POST['fecha'][$i];
			$data[$index]['fecha_mov'] = $_POST['fecha'][$i];
			$data[$index]['tipo_mov'] = $_POST['tipo_mov'];
			$data[$index]['importe'] = get_val($_POST['importe'][$i]);
			$data[$index]['concepto'] = $_POST['concepto'][$i] != "" ? strtoupper($_POST['concepto'][$i]) : buscar_mov($codigos, $_POST['cod_mov'][$i]);
			$data[$index]['fecha_cap'] = date("d/m/Y");
			$data[$index]['manual'] = "TRUE";
			$data[$index]['imprimir'] = "FALSE";
			$data[$index]['ficha'] = "FALSE";
			$data[$index]['cuenta'] = $_POST['cuenta'];
			$data[$index]['iduser'] = $_SESSION['iduser'];
			$data[$index]['timestamp'] = date("d/m/Y H:i:s");
			$data[$index]['tipo_con'] = "0";
			$index++;
		}

	if (isset($data)) {
		usort($data, "cmp");

		$sql .= $db->multiple_insert($tabla, $data);
		$sql .= $db->multiple_insert("estado_cuenta", $data);

		$tpl->newBlock("listado");
		$tpl->assign("mov", $_POST['tipo_mov'] == 1 ? "Dep&oacute;sitos" : "Cargos");
		$tpl->assign("dia", date("d"));
		$tpl->assign("mes", mes_escrito(date("n")));
		$tpl->assign("anio", date("Y"));

		$cuenta = $_POST['cuenta'] == 1 ? "clabe_cuenta" : "clabe_cuenta2";
		$cias = $db->query("SELECT num_cia, $cuenta, nombre FROM catalogo_companias ORDER BY num_cia");

		$total = 0;
		for ($i = 0; $i < count($data); $i++) {
			$tpl->newBlock("mov");
			$cia = buscar_cia($cias, $data[$i]['num_cia']);
			$tpl->assign("num_cia", $data[$i]['num_cia']);
			$tpl->assign("cuenta", $cia[$cuenta]);
			$tpl->assign("nombre_cia", $cia['nombre']);
			$tpl->assign("cod_mov", $data[$i]['cod_mov']);
			$tpl->assign("descripcion", buscar_mov($codigos, $data[$i]['cod_mov']));
			$tpl->assign("concepto", $data[$i]['concepto']);
			$tpl->assign("importe", number_format($data[$i]['importe'], 2, ".", ","));
			$tpl->assign("fecha", $data[$i]['fecha']);

			$total += $data[$i]['importe'];

			$sql .= "UPDATE saldos SET saldo_libros = saldo_libros " . ($_POST['tipo_mov'] == "FALSE" ? "+" : "-") . " {$data[$i]['importe']} WHERE num_cia = {$data[$i]['num_cia']} AND cuenta = $_POST[cuenta];\n";
		}
		$tpl->assign("listado.total", number_format($total, 2, ".", ","));

		$tpl->printToScreen();

		$db->query($sql);
	}
	else
		header("location: ./ban_dep_cap_v2.php?codigo_error=2");

	die;
}

if (isset($_GET['cuenta'])) {
	$tpl->newBlock("captura");
	$tpl->assign("cuenta", $_GET['cuenta']);
	$tpl->assign("tipo_mov", $_GET['tipo_mov']);
	$tpl->assign("mov", $_GET['tipo_mov'] == 1 ? "Dep&oacute;sitos" : "Cargos");
	$tpl->assign("banco", $_GET['cuenta'] == 1 ? "BANORTE" : "SANTANDER SERFIN");

	$catMovBan = $_GET['cuenta'] == 1 ? "catalogo_mov_bancos" : "catalogo_mov_santander";

	$cod = $db->query("SELECT cod_mov, descripcion FROM $catMovBan WHERE tipo_mov = '$_GET[tipo_mov]' AND cod_mov NOT IN (2) GROUP BY cod_mov, descripcion ORDER BY cod_mov");

	// Si no hay conceptos bancarios
	if (!$cod) {
		header("location: ./ban_dep_cap_v2.php?codigo_error=1");
		die;
	}

	for ($i = 0; $i < $_GET['num_mov']; $i++) {
		$tpl->newBlock("fila");
		$tpl->assign("i", $i);
		$tpl->assign("next", $i < $_GET['num_mov'] - 1 ? $i + 1 : 0);
		$tpl->assign("fecha", date("d/m/Y"));

		for ($j = 0; $j < count($cod); $j++) {
			$tpl->newBlock("cod_mov");
			$tpl->assign("cod_mov", $cod[$j]['cod_mov']);
			$tpl->assign("descripcion", $cod[$j]['descripcion']);
		}
	}

	if (!in_array($_SESSION['iduser'], $users))
		$sql = "SELECT num_cia, nombre_corto, clabe_cuenta, clabe_cuenta2 FROM catalogo_companias WHERE num_cia NOT IN (999) ORDER BY num_cia";
	else
		$sql = "SELECT num_cia, nombre_corto, clabe_cuenta, clabe_cuenta2 FROM catalogo_companias WHERE num_cia BETWEEN 900 AND 998 ORDER BY num_cia";
	$cia = $db->query($sql);
	for ($i = 0; $i < count($cia); $i++) {
		$tpl->newBlock("cia");
		$tpl->assign("num_cia", $cia[$i]['num_cia']);
		$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
		$tpl->assign('cuenta_banco', $cia[$i][$_GET['cuenta'] == 1 ? 'clabe_cuenta' : 'clabe_cuenta2']);
	}

	$tpl->printToScreen();
	die;
}

// Si no se ha cargado archivo, solicitarlo
$tpl->newBlock("datos");

$tpl->assign("num_mov", 10);

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>
