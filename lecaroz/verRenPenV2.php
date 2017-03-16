<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

if (!in_array($_SESSION['iduser'], array(7, 25, 43))) die;

$sql = "SELECT ca.id, ca.num_local, nombre_local, ca.num_cia, cc.nombre_corto, giro, ca.cod_arrendador FROM catalogo_arrendatarios ca LEFT JOIN catalogo_locales cl ON (cl.id = ca.num_local) LEFT JOIN catalogo_companias cc ON (cc.num_cia = ca.num_cia) WHERE ca.status = 1 AND ca.bloque = 2 ORDER BY ca.num_cia";
$result = $db->query($sql);
	
if (!$result) die;

$anio = date('Y');
$fecha1 = date('d/m/Y', mktime(0, 0, 0, 10, 1, $anio - 1));
$fecha2 = date('d/m/Y', mktime(0, 0, 0, 11, 1, $anio));
$current_month = date('n');

$sql = "SELECT local, extract(month from fecha_renta) AS mes, extract(year from fecha_renta) AS anio FROM estado_cuenta WHERE local IN (SELECT ca.id FROM catalogo_arrendatarios ca LEFT JOIN catalogo_locales cl ON (cl.id = ca.num_local) LEFT JOIN catalogo_companias cc ON (cc.num_cia = ca.num_cia) WHERE ca.status = 1 AND ca.bloque = 2) AND cod_mov = 2 AND fecha_renta BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha_renta";
$ren = $db->query($sql);
foreach ($ren as $r)
	$rentas[$r['local']][$r['anio']][$r['mes']] = TRUE;

$sql = "SELECT local, anio, mes, tipo FROM estatus_locales WHERE local IN (SELECT ca.id FROM catalogo_arrendatarios ca LEFT JOIN catalogo_locales cl ON (cl.id = ca.num_local) LEFT JOIN catalogo_companias cc ON (cc.num_cia = ca.num_cia) WHERE ca.status = 1 AND ca.bloque = 2) AND anio IN ($anio, $anio - 1) AND (local, mes, anio) NOT IN (SELECT local, extract(month from fecha_renta) AS mes, extract(year from fecha_renta) AS anio FROM estado_cuenta WHERE local IN (SELECT ca.id FROM catalogo_arrendatarios ca LEFT JOIN catalogo_locales cl ON (cl.id = ca.num_local) LEFT JOIN catalogo_companias cc ON (cc.num_cia = ca.num_cia) WHERE ca.status = 1 AND ca.bloque = 2) AND cod_mov = 2 AND fecha_renta BETWEEN '$fecha1' AND '$fecha2')";
$est = $db->query($sql);

// [22-Oct-2008] Ordenar estados
$estados = array();
foreach ($est as $e)
	$estados[$e['local']][$e['anio']][$e['mes']] = $e['tipo'] == 0 ? 2 : 1;

$tpl = new TemplatePower( "./plantillas/verRenPenV2.tpl" );
$tpl->prepare();

$tpl->assign('anio', $anio);

$cont = 0;
foreach ($result as $reg) {
	$months = array();
	
	$ok = TRUE;
	$pen = FALSE;
	for ($y = $anio - 1; $y <= $anio; $y++) {
		for ($m = ($y == $anio - 1 ? 10 : 1); $m <= ($y == $anio - 1 ? 12 : 11); $m++)
			if (isset($rentas[$reg['id']][$y][$m])) {
				$months[$y][$m] = 1;
				$ok = TRUE;
			}
			else if (isset($estados[$reg['id']][$y][$m])) {
				$r = $estados[$reg['id']][$y][$m];
				$months[$y][$m] = $r;
				$ok = $r == 1 ? TRUE : FALSE;
			}
			else if (!$ok)
				$months[$y][$m] = 2;
			else {
				$months[$y][$m] = 0;
				if ($y < $anio || ($y == $anio && $m <= $current_month))
					$pen = TRUE;
			}
	}
	
	if (!$pen)
		continue;
	
	$tpl->newBlock('fila');
	$tpl->assign('num', $reg['num_cia']);
	$tpl->assign('cod', $reg['cod_arrendador']);
	$tpl->assign('local', $reg['nombre_corto']);
	$tpl->assign('arr', $reg['num_local']);
	$tpl->assign('nombre', $reg['nombre_local']);
	$tpl->assign('giro', trim($reg['giro']) != '' ? trim($reg['giro']) : '&nbsp;');
	$tpl->assign('ant', substr($anio, 2, 2));
	
	foreach ($months as $y => $m)
		foreach ($m as $i => $s)
			$tpl->assign(($y < $anio ? 'mes_ant_' : 'mes') . $i, $s == 0 ? '&nbsp;' : ($s == 1 ? '<img src="./imagenes/negro.GIF" />' : '<span style="color:#C00; font-weight:bold;">BAJA</span>'));
	
	$cont++;
}

if ($cont == 0) die;

$tpl->printToScreen();
//$tpl->getOutputContent();
?>