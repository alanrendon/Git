<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn);

$users = array(28, 29, 30, 31);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Delaracion de variables -------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/ban/ban_con_tot.tpl" );
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	
	$tpl->assign(date("n"), "selected");
	$tpl->assign("anio", date("Y"));
	
	$sql = "SELECT cod_mov, descripcion FROM catalogo_mov_bancos GROUP BY cod_mov, descripcion ORDER BY cod_mov, descripcion";
	$result = $db->query($sql);
	$cods = '';
	foreach ($result as $reg) {
		if ($cods != '')
			$cods .= ', ';
		$cods .= "$reg[cod_mov], '$reg[descripcion]'";
	}
	$tpl->assign('ban', $cods);
	
	$sql = "SELECT cod_mov, descripcion FROM catalogo_mov_santander GROUP BY cod_mov, descripcion ORDER BY cod_mov, descripcion";
	$result = $db->query($sql);
	$cods = '';
	foreach ($result as $reg) {
		if ($cods != '')
			$cods .= ', ';
		$cods .= "$reg[cod_mov], '$reg[descripcion]'";
	}
	$tpl->assign('san', $cods);
	
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
	die;
}

$tpl->newBlock("listado");
$tpl->assign("titulo", $_GET['mes'] > 0 ? "al mes de " . mes_escrito($_GET['mes']) . "de $_GET[anio]" : "del a&ntilde;o $_GET[anio]");
$tpl->assign("mes", mes_escrito($_GET['mes']));
$tpl->assign("anio", $_GET['anio']);

$fecha1 = $_GET['mes'] > 0 ? "01/$_GET[mes]/$_GET[anio]" : "01/01/$_GET[anio]";
$fecha2 = $_GET['mes'] > 0 ? date("d/m/Y", mktime(0, 0, 0,$_GET['mes'] + 1, 0, $_GET['anio'])) : ($_GET['anio'] == date("Y") ? date("d/m/Y") : "31/12/$_GET[anio]");

$sql = "SELECT num_cia, nombre_corto, tipo_mov, cod_mov, sum(importe) AS total FROM estado_cuenta LEFT JOIN catalogo_companias USING (num_cia)";
$sql .= " WHERE fecha BETWEEN '$fecha1' AND '$fecha2'";
$sql .= in_array($_SESSION['iduser'], $users) ? " AND num_cia BETWEEN 900 AND 950" : " AND num_cia BETWEEN 1 AND 800";
$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
$sql .= $_GET['cod_mov'] > 0 ? " AND cod_mov = $_GET[cod_mov]" : "";
$sql .= $_GET['cuenta'] > 0 ? " AND cuenta = $_GET[cuenta]" : "";
$sql .= " GROUP BY num_cia, nombre_corto, tipo_mov, cod_mov ORDER BY num_cia, cod_mov";

$result = $db->query($sql);

$gran_total = 0;
if ($result) {
	$num_cia = NULL;
	for ($i = 0; $i < count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			$num_cia = $result[$i]['num_cia'];
			
			$tpl->newBlock("cia");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
			
			$total_abono = 0;
			$total_cargo = 0;
			$total_general = 0;
		}
		$tpl->newBlock("fila");
		$tpl->assign("num_cia", $num_cia);
		$tpl->assign("cod_mov", $result[$i]['cod_mov']);
		$tpl->assign("fecha1", $fecha1);
		$tpl->assign("fecha2", $fecha2);
		$cod_mov = $db->query("SELECT descripcion FROM catalogo_mov_bancos WHERE cod_mov = {$result[$i]['cod_mov']} LIMIT 1");
		if ($cod_mov[0]['descripcion'] == '')
			$cod_mov = $db->query("SELECT descripcion FROM catalogo_mov_santander WHERE cod_mov = {$result[$i]['cod_mov']} LIMIT 1");
		$tpl->assign("descripcion", $cod_mov[0]['descripcion']);
		$tpl->assign("total_abono", $result[$i]['tipo_mov'] == "f" ? number_format($result[$i]['total'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("total_cargo", $result[$i]['tipo_mov'] == "t" ? number_format($result[$i]['total'], 2, ".", ",") : "&nbsp;");
		
		$total_abono += $result[$i]['tipo_mov'] == "f" ? $result[$i]['total'] : 0;
		$total_cargo += $result[$i]['tipo_mov'] == "t" ? $result[$i]['total'] : 0;
		$total_general += $result[$i]['tipo_mov'] == "f" ? $result[$i]['total'] : -$result[$i]['total'];
		
		$gran_total += $result[$i]['tipo_mov'] == "f" ? $result[$i]['total'] : -$result[$i]['total'];;
		
		$tpl->assign("cia.total_abono", number_format($total_abono, 2, ".", ","));
		$tpl->assign("cia.total_cargo", number_format($total_cargo, 2, ".", ","));
		$tpl->assign("cia.total_general", "<font color=\"#" . ($total_general > 0 ? "0000FF" : "FF0000") . "\">" . number_format($total_general, 2, ".", ",") . "</font>");
	}
	$tpl->newBlock('gran_total');
	$tpl->assign('total', number_format($gran_total, 2, '.', ','));
}
else
	$tpl->newBlock("no_result");

$tpl->printToScreen();
$db->desconectar();
?>