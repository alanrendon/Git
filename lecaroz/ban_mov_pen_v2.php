<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$users = array(28, 29, 30, 31);

//if ($_SESSION['iduser'] != 1) die(header('location: offline.htm'));

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_mov_pen_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['list'])) {
	$cuenta = $_GET['cuenta'];
	$tabla_mov = $cuenta == 1 ? "mov_banorte" : "mov_santander";
	$clabe_cuenta = $cuenta == 1 ? "clabe_cuenta" : "clabe_cuenta2";
	$catalogo_mov = $cuenta == 1 ? "catalogo_mov_bancos" : "catalogo_mov_santander";
	
	$sql = "SELECT num_cia, $clabe_cuenta, catalogo_companias.nombre, fecha, importe, tipo_mov, num_documento, cod_mov, concepto";
	$sql .= " FROM $tabla_mov LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha_con IS NOT NULL AND imprimir = 'TRUE' AND num_cia BETWEEN ";
	$sql .= !in_array($_SESSION['iduser'], $users) ? "1 AND 899" : "900 AND 998";
	$sql .= " ORDER BY num_cia, fecha";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ban_mov_pen_v2.php");
		die;
	}
	
	// Quitar marca de impresión
	$db->query("UPDATE $tabla_mov SET imprimir = 'FALSE' WHERE imprimir = 'TRUE'");
	
	$cod_mov = $db->query("SELECT cod_mov, descripcion FROM $catalogo_mov GROUP BY cod_mov, descripcion ORDER BY cod_mov");
	function buscarCod($cod) {
		global $cod_mov;
		
		for ($i = 0; $i < count($cod_mov); $i++)
			if ($cod_mov[$i]['cod_mov'] == $cod)
				return $cod_mov[$i]['descripcion'];
	}
	
	$tpl->newBlock("listado");
	$tpl->assign("dia", date("d"));
	$tpl->assign("mes", mes_escrito(date("n")));
	$tpl->assign("anio", date("Y"));
	
	$num_cia = NULL;
	$total_abonos = 0;
	$total_cargos = 0;
	for ($i = 0; $i < count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			if ($num_cia != NULL) {
				$tpl->assign("cia_con.abonos", number_format($abonos, 2, ".", ","));
				$tpl->assign("cia_con.cargos", number_format($cargos, 2, ".", ","));
			}
			
			$num_cia = $result[$i]['num_cia'];
			$tpl->newBlock("cia_con");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("cuenta", $result[$i][$clabe_cuenta]);
			$tpl->assign("nombre_cia", $result[$i]['nombre']);
			
			$abonos = 0;
			$cargos = 0;
		}
		$tpl->newBlock("fila");
		$tpl->assign("fecha", $result[$i]['fecha']);
		$tpl->assign("abono", $result[$i]['tipo_mov'] == "f" ? number_format($result[$i]['importe'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("cargo", $result[$i]['tipo_mov'] == "t" ? number_format($result[$i]['importe'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("folio", $result[$i]['num_documento'] > 0 ? $result[$i]['num_documento'] : "&nbsp;");
		$tpl->assign("cod_mov", $result[$i]['cod_mov']);
		$tpl->assign("descripcion", buscarCod($result[$i]['cod_mov']));
		$tpl->assign("concepto", $result[$i]['concepto']);
		
		$abonos += $result[$i]['tipo_mov'] == "f" ? $result[$i]['importe'] : 0;
		$cargos += $result[$i]['tipo_mov'] == "t" ? $result[$i]['importe'] : 0;
		$total_abonos += $result[$i]['tipo_mov'] == "f" ? $result[$i]['importe'] : 0;
		$total_cargos += $result[$i]['tipo_mov'] == "t" ? $result[$i]['importe'] : 0;
	}
	if ($num_cia != NULL) {
		$tpl->assign("cia_con.abonos", number_format($abonos, 2, ".", ","));
		$tpl->assign("cia_con.cargos", number_format($cargos, 2, ".", ","));
	}
	$tpl->assign("listado.total_abonos", number_format($total_abonos, 2, ".", ","));
	$tpl->assign("listado.total_cargos", number_format($total_cargos, 2, ".", ","));
	
	$tpl->printToScreen();
	die;
}

if (isset($_GET['cuenta'])) {
	$cuenta = $_GET['cuenta'];
	$tabla_mov = $cuenta == 1 ? "mov_banorte" : "mov_santander";
	$banco = $cuenta == 1 ? "BANORTE" : "SANTANDER";
	$clabe_cuenta = $cuenta == 1 ? "clabe_cuenta" : "clabe_cuenta2";
	
	$sql = "SELECT id, num_cia, fecha, importe, tipo_mov, num_documento, cod_banco, concepto FROM $tabla_mov WHERE fecha_con IS NULL AND num_cia BETWEEN ";
	$sql .= !in_array($_SESSION['iduser'], $users) ? "1 AND 899" : "900 AND 998";
	$sql .= " ORDER BY num_cia, fecha";
	$result = $db->query($sql);
	
	if (!$result) {
		$tpl->newBlock("no_mov");
		$tpl->assign("cuenta", $cuenta);
		$tpl->printToScreen();
		die;
	}
	
	$tpl->newBlock("movimientos");
	$tpl->assign("cuenta", $cuenta);
	$tpl->assign("banco", $banco);
	
	$num_cia = NULL;
	$total_abonos = 0;
	$total_cargos = 0;
	for ($i = 0; $i < count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			if ($num_cia != NULL) {
				$tpl->assign("cia.abonos", number_format($abonos, 2, ".", ","));
				$tpl->assign("cia.cargos", number_format($cargos, 2, ".", ","));
			}
			
			$num_cia = $result[$i]['num_cia'];
			
			$cia = $db->query("SELECT nombre, $clabe_cuenta FROM catalogo_companias WHERE num_cia = $num_cia");
			
			$tpl->newBlock("cia");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre_cia", $cia[0]['nombre']);
			$tpl->assign("clabe_cuenta", $cia[0][$clabe_cuenta]);
			$tpl->assign("cuenta", $cuenta);
			
			$abonos = 0;
			$cargos = 0;
		}
		$tpl->newBlock("mov");
		$tpl->assign("id", $result[$i]['id']);
		$tpl->assign("tipo_mov", $result[$i]['tipo_mov']);
		$tpl->assign("fecha", $result[$i]['fecha']);
		$tpl->assign("abono", $result[$i]['tipo_mov'] == "f" ? number_format($result[$i]['importe'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("cargo", $result[$i]['tipo_mov'] == "t" ? number_format($result[$i]['importe'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("folio", $result[$i]['num_documento'] > 0 ? $result[$i]['num_documento'] : "&nbsp;");
		$tpl->assign("cod_banco", $result[$i]['cod_banco']);
		$tpl->assign("concepto", $result[$i]['concepto']);
		
		$abonos += $result[$i]['tipo_mov'] == "f" ? $result[$i]['importe'] : 0;
		$cargos += $result[$i]['tipo_mov'] == "t" ? $result[$i]['importe'] : 0;
		
		$total_abonos += $result[$i]['tipo_mov'] == "f" ? $result[$i]['importe'] : 0;
		$total_cargos += $result[$i]['tipo_mov'] == "t" ? $result[$i]['importe'] : 0;
	}
	if ($num_cia != NULL) {
		$tpl->assign("cia.abonos", number_format($abonos, 2, ".", ","));
		$tpl->assign("cia.cargos", number_format($cargos, 2, ".", ","));
	}
	$tpl->assign("movimientos.abonos", number_format($total_abonos, 2, ".", ","));
	$tpl->assign("movimientos.cargos", number_format($total_cargos, 2, ".", ","));
	
	$tpl->printToScreen();
	die;
}

// Si no se ha cargado archivo, solicitarlo
$tpl->newBlock("datos");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>