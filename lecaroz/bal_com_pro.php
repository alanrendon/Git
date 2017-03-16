<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";
$descripcion_error[2] = "No existe el gasto";
$descripcion_error[3] = "No existe la compañía";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/bal/bal_com_pro.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['anio'])) {
	$tpl->newBlock("datos");
	$tpl->assign("anio", date("Y"));
	
	$admins = $db->query("SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores ORDER BY nombre_administrador");
	foreach ($admins as $admin) {
		$tpl->newBlock("admin");
		$tpl->assign("id", $admin['id']);
		$tpl->assign("admin", $admin['admin']);
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

$fecha1 = "01/01/$_GET[anio]";
$fecha2 = "31/12/$_GET[anio]";

$nombre_turno = array(
1 => "Frances de D&iacute;a",
2 => "Frances de Noche",
3 => "Bizcochero",
4 => "Repostero",
8 => "Piconero",
9 => "Gelatinero"
);

$turnos = array();
if (isset($_GET['turno']))
	foreach ($_GET['turno'] as $turno)
		$turnos[] = $turno;

$sql = "SELECT numcia AS num_cia, nombre_corto" . (isset($_GET['idadmin']) && ($_GET['idadmin'] == -1 || $_GET['idadmin'] > 0) ? ", idadministrador" : "") . " FROM total_produccion";
$sql .= " LEFT JOIN catalogo_companias ON (num_cia = numcia) WHERE fecha_total BETWEEN '$fecha1' AND '$fecha2'";
if (isset($_GET['turno'])) {
	$sql .= " AND codturno IN (";
	foreach ($_GET['turno'] as $i => $turno)
		$sql .= $turno . ($i < count($_GET['turno']) - 1 ? ", " : ")");
}
if (isset($_GET['com'])) {
	$cias = array();
	foreach ($_GET['cias'] as $cia)
		if ($cia > 0)
			$cias[] = $cia;
	
	if (count($cias) > 0) {
		$sql .= " AND numcia IN (";
		foreach ($cias as $i => $cia)
			$sql .= $cia . ($i < count($cias) - 1 ? ", " : ")");
	}
}
else
	$sql .= $_GET['idadmin'] == "" ? ($_GET['num_cia'] > 0 ? " AND numcia = $_GET[num_cia]" : "") : ($_GET['idadmin'] > 0 ? " AND idadministrador = $_GET[idadmin]" : "");
if (isset($_GET['com']) || $_GET['idadmin'] == "")
	$sql .= " GROUP BY numcia, nombre_corto ORDER BY numcia";
else
	$sql .= " GROUP BY numcia, nombre_corto, idadministrador ORDER BY idadministrador, numcia";//echo "$sql";die;
$cia = $db->query($sql);

$num_meses = $_GET['anio'] < date("Y") ? 12 : date("n", mktime(0, 0, 0, date("n"), 0, $_GET['anio']));
for($z = 1; $z <= 12; $z++)
	$t[$z] = 0;

$grantotal = 0;
$promedio = 0;

function buscar($mes) {
	global $result;
	
	for ($i = 0; $i < count($result); $i++)
		if ($mes == $result[$i]['mes'])
			return $result[$i]['importe'];
	
	return 0;
}

if (isset($_GET['com']) || $_GET['idadmin'] == "") {
	$tpl->newBlock(!isset($_GET['por']) ? "listado" : "porcentajes");
	$tpl->assign("anio", $_GET['anio']);
	$turno = "";
	if (isset($_GET['turno']) && count($_GET['turno']) >= 1)
		foreach ($_GET['turno'] as $i => $tmp)
			$turno .= $nombre_turno[$tmp] . ($i < count($_GET['turno']) - 1 ? ", " : "");
	else
		$turno .= "Frances de D&iacute;a, Frances de Noche, Bizcochero, Repostero, Piconero, Gelatinero";
	$tpl->assign("turno", "$turno");
}

$idadmin = NULL;
for ($i = 0; $i < count($cia); $i++) {
	if (isset($_GET['idadmin']) && ($_GET['idadmin'] == -1 || $_GET['idadmin'] > 0) && $cia[$i]['idadministrador'] != $idadmin) {
		if ($idadmin != NULL) {
			$tpl->gotoBlock(!isset($_GET['por']) ? "listado" : "porcentajes");
			
			for($z=1;$z<=12;$z++){
				if($t[$z] > 0)
					$tpl->assign("t" . $z, number_format($t[$z], 0, '.', ','));
			}
			
			$tpl->assign("total", number_format($grantotal, 0, ".", ","));
			$tpl->assign("prom", number_format($promedio, 0, ".", ","));
		}
		
		$idadmin = $cia[$i]['idadministrador'];
		
		$tpl->newBlock(!isset($_GET['por']) ? "listado" : "porcentajes");
		$tpl->assign("anio", $_GET['anio']);
		$turno = "";
		if (isset($_GET['turno']) && count($_GET['turno']) >= 1)
			foreach ($_GET['turno'] as $k => $tmp)
				$turno .= $nombre_turno[$tmp] . ($k < count($_GET['turno']) - 1 ? ", " : "");
		else
			$turno .= "Frances de D&iacute;a, Frances de Noche, Bizcochero, Repostero, Piconero, Gelatinero";
		$tpl->assign("turno", "$turno");
		
		for($z = 1; $z <= 12; $z++)
			$t[$z] = 0;
		
		$grantotal = 0;
		$promedio = 0;
	}
	
	$sql = "SELECT extract(month FROM fecha_total) AS mes, " . (isset($_GET['por']) ? "sum(raya_pagada) / " : "") . "sum(total_produccion) AS importe FROM total_produccion";
	$sql .= " WHERE numcia = {$cia[$i]['num_cia']} AND fecha_total BETWEEN '$fecha1' AND '$fecha2'";
	if (isset($_GET['turno'])) {
		$sql .= " AND codturno IN (";
		foreach ($_GET['turno'] as $k => $turno)
			$sql .= $turno . ($k < count($_GET['turno']) - 1 ? ", " : ")");
	}
	$sql .= " GROUP BY extract(month FROM fecha_total) ORDER BY extract(month FROM fecha_total)";
	$result = $db->query($sql);

	$tpl->newBlock(!isset($_GET['por']) ? "fila" : "fila_por");
	$tpl->assign("num_cia", $cia[$i]['num_cia']);
	$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
	
	$total = 0;
	
	$presicion = !isset($_GET['por']) ? 0 : 4;
	
	for ($j = 1; $j <= $num_meses; $j++) {
		$pro = buscar($j);
		
		$total += $pro;
		
		$t[$j] += $pro;
		$tpl->assign($j, round($pro, $presicion) != 0 ? number_format($pro, $presicion, ".", ",") : "&nbsp;");
	}
	if (!isset($_GET['por']))
		$tpl->assign("total", number_format($total, $presicion, ".", ","));
	$tpl->assign("prom", number_format($total / $num_meses, $presicion, ".", ","));
	$grantotal += $total;
	$promedio += $total / $num_meses;
}

$tpl->gotoBlock(!isset($_GET['por']) ? "listado" : "porcentajes");

if (!isset($_GET['por'])) {
	for($z=1;$z<=12;$z++){
		if($t[$z] > 0)
			$tpl->assign("t" . $z, number_format($t[$z], $presicion, '.', ','));
	}
	$tpl->assign("total", number_format($grantotal, $presicion, ".", ","));
}
$tpl->assign("prom", number_format($promedio, $presicion, ".", ","));

$tpl->printToScreen();
//$db->desconectar();
?>