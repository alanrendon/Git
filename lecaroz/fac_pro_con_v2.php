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
$tpl->assignInclude("body","./plantillas/fac/fac_pro_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$anio_act = $_GET['anio'];
	$anio_ant = $_GET['anio'] - 1;
	
	$cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	
	$sql = "SELECT nombre, existencia, tipo_unidad_consumo.descripcion AS unidad, tipo, tipo_presentacion.descripcion AS presentacion, procpedautomat AS ped_aut, porcientoincremento";
	$sql .= " AS por_ped, entregasfinmes AS entregas FROM inventario_real LEFT JOIN catalogo_mat_primas USING (codmp) LEFT JOIN tipo_unidad_consumo ON (idunidad = unidadconsumo)";
	$sql .= " LEFT JOIN tipo_presentacion ON (idpresentacion = presentacion) WHERE num_cia = $_GET[num_cia] AND codmp = $_GET[codmp]";
	$mp = $db->query($sql);
	
	$sql = "SELECT consumo, mes, anio FROM consumos_mensuales WHERE num_cia = $_GET[num_cia] AND codmp = $_GET[codmp] AND anio IN ($anio_ant, $anio_act) ORDER BY anio, mes";
	$result = $db->query($sql);
	
	if (!$cia || !$mp || !$result) {
		header("location: ./fac_pro_con_v2.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("listado");
	$tpl->assign("num_cia", $_GET['num_cia']);
	$tpl->assign("nombre_cia", $cia[0]['nombre_corto']);
	$tpl->assign("codmp", $_GET['codmp']);
	$tpl->assign("nombre_mp", $mp[0]['nombre']);
	$tpl->assign("anio", $_GET['anio']);
	$tpl->assign("unidad", $mp[0]['unidad']);
	$tpl->assign("tipo", $mp[0]['tipo'] == 1 ? "MATERIA PRIMA" : "MATERIAL DE EMPAQUE");
	$tpl->assign("presentacion", $mp[0]['presentacion']);
	$tpl->assign("existencia", number_format($mp[0]['existencia'], 2, ".", ","));
	$tpl->assign("ped_aut", $mp[0]['ped_aut'] == "t" ? "SI" : "NO");
	$tpl->assign("por_ped", number_format($mp[0]['por_ped'], 2, ".", ","));
	$tpl->assign("num_entregas", $mp[0]['entregas']);
	$tpl->assign("anio_ant", $anio_ant);
	$tpl->assign("anio_act", $anio_act);
	
	$total_act = 0;
	$total_ant = 0;
	foreach ($result as $reg)
		if ($reg['anio'] == $anio_act) {
			$tpl->assign($reg['mes'] . "_act", $reg['consumo'] != 0 ? number_format($reg['consumo'], 2, ".", ",") : "");
			$total_act += $reg['consumo'];
			$mes_act = $reg['mes'];
		}
		else if ($reg['anio'] == $anio_ant) {
			$tpl->assign($reg['mes'] . "_ant", $reg['consumo'] != 0 ? number_format($reg['consumo'], 2, ".", ",") : "");
			$total_ant += $reg['consumo'];
			$mes_ant = $reg['mes'];
		}
	$tpl->assign("total_act", $total_act != 0 ? number_format($total_act, 2, ".", ",") : "");
	$tpl->assign("prom_act", $total_act != 0 ? number_format($total_act / $mes_act, 2, ".", ",") : "");
	$tpl->assign("total_ant", $total_act != 0 ? number_format($total_ant, 2, ".", ",") : "");
	$tpl->assign("prom_ant", $total_act != 0 ? number_format($total_ant / $mes_ant, 2, ".", ",") : "");
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");
$tpl->assign("anio", date("Y"));

//$result = $db->query('SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores ORDER BY admin');
//foreach ($result as $r) {
//	$tpl->newBlock('admin');
//	$tpl->assign('id', $r['id']);
//	$tpl->assign('admin', $r['admin']);
//}

if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>